<?php

//inclusão de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/rotinas.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");
require_once("../common/lib/xajax/xajax.inc.php");

require_once("../entidades/filial.php");
require_once("../entidades/movimento.php");
require_once("../entidades/saldo.php");
require_once("../entidades/cliente.php");
require_once("../entidades/plano.php");
require_once("../entidades/parametros.php");

require_once("movimento_ajax.php");


// configurações anotionais

// if($_GET['ac'] == "balancete") $conf['area'] = 'Relatório de Balancete';
// elseif($_GET['ac'] == "razonete") $conf['area'] = 'Relatório de Razonete';
// elseif($_GET['ac'] == "demonstrativo") $conf['area'] = 'Demonstrativo';
// else $conf['area'] = "Relatório de Caixa"; // área

switch ($_GET['ac']) {
	case 'balancete':
		$conf['area'] = 'Relatório de Balancete';
		break;
	case 'razonete':
		$conf['area'] = 'Relatório de Razonete';
		break;
	case 'demonstrativo':
		$conf['area'] = 'Demonstrativo';
		break;
	case 'clientes_inadimplentes':
		$conf['area'] = 'Relatório de Clientes Inadimplentes';
		break;
	case 'saldo':
		$conf['area'] = 'Relatório de Saldo';
		break;
	
	default:
		$conf['area'] = "Relatório de Caixa";
		break;
}


//configuração de estilo
$conf['style'] = "full";

// inicializa templating
$smarty = new Smarty;

// configura diretórios
$smarty->template_dir = "../common/tpl";
$smarty->compile_dir =   "../common/tpl_c";

// seta configurações
$smarty->assign("conf", $conf);

// ação selecionada
$flags['action'] = $_GET['ac'];
if ($flags['action'] == "") $flags['action'] = "listar";

// inicializa autenticação
$auth = new auth();

// cria o objeto xajax
$xajax = new xajax();


// registra todas as funções que serão usadas
$xajax->registerFunction("Verifica_Campos_Busca_Caixa_AJAX");
$xajax->registerFunction("Atualiza_Saldo_Cliente_AJAX");

// processa as funções
$xajax->processRequests();


// verifica requisição de logout
if($flags['action'] == "logout") {
	$auth->logout();
}
else {

	// inicializa vetor de erros
	$err = array();

	// verifica sessão
	if(!$auth->check_user()) {
		// verifica requisição de login
		if($_POST['usr_chk']) {
			// verifica login
			if(!$auth->login($_POST['usr_log'], $_POST['usr_sen'])) {
				$err = $auth->err;
			}
		}
		else {
			$err = $auth->err;
		}
	}

	// conteúdo
	if($auth->check_user()) {
		// verifica privilégios
		if(!$auth->check_priv($conf['priv'])) {
			$err = $auth->err;
		}
		else {
			// libera conteúdo
			$flags['okay'] = 1;

			//inicializa classe
			$filial = new filial();
			$movimento = new movimento();
			$saldo = new saldo();
			$cliente = new cliente();
			$plano = new plano();
			$ObjParametros = new parametros();
				
			// inicializa banco de dados
			$db = new db();

			//incializa classe para validação de formulário
			$form = new form();

			$list = $auth->check_priv($conf['priv']);
			$aux = $flags['action'];
			if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {
				$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";
			}

			//Busca os parâmetros do banco
			$parametro = $ObjParametros->getAll();
			$smarty->assign("parametro", $parametro);

			switch($flags['action']) {



				//listagem dos registros
				case "listar":

					// busca os dados da filial
					$info_filial = $filial->getById($_SESSION['idfilial_usuario']);
					$smarty->assign("info_filial", $info_filial);

					// busca o dia e hora atual
					$flags['data_hora_atual'] = date('d/m/Y H:i:s');

						
					// busca os dados do relatório de recebimentos do vendedor
					if($_POST['for_chk']) {

						if( isset($_GET['target']) && $_GET['target'] == 'full'){
								
							$idcliente = (int)$_POST['idcliente_relatorio'];
							$data1 = $form->formataDataParaInserir($_POST['data_baixa_de_relatorio']);
							$data2 = $form->formataDataParaInserir($_POST['data_baixa_ate_relatorio']);
						}
						else{
								
							$idcliente = (int)$_POST['idcliente'];
							$data1 = $form->formataDataParaInserir($_POST['data_baixa_de']);
							$data2 = $form->formataDataParaInserir($_POST['data_baixa_ate']);
						}
							
							
						// lista as contas a receber das comissões do vendedor
						$list_caixa = $movimento->make_list_caixa($idcliente, $data1, $data2);

						$dados_cliente = $cliente->getById($idcliente);

						//passa a listagem para o template
						$smarty->assign("list_caixa", $list_caixa);
						$smarty->assign("dados_cliente", $dados_cliente);


					}


					break;


				case "demonstrativo":

					// busca os dados da filial
					$info_filial = $filial->getById($_SESSION['idfilial_usuario']);
					$smarty->assign("info_filial", $info_filial);

					// busca o dia e hora atual
					$flags['data_hora_atual'] = date('d/m/Y H:i:s');

					// Monta a listagem de anos e meses
					$listMesAno = $form->make_list_mesAno();
					$smarty->assign("listMesAno", $listMesAno);
						
					// busca os dados do relatório de recebimentos do vendedor
					if($_POST['for_chk']) {

						//Verifica se vai exibir como relatório e trata os campos para tal
						if( isset($_GET['target']) && $_GET['target'] == 'full'){
								
							$idcliente = (int)$_POST['idcliente_relatorio'];
								
							//Usar a função formataDataParaInserir aqui já evita SQL Injection
							$data1 = $form->formataDataParaInserir($_POST['data_baixa_de_relatorio']);
							$data2 = $form->formataDataParaInserir($_POST['data_baixa_ate_relatorio']);
								
						}
						else{ // Se for POST normal, prepara os campos


							$idcliente = (int)$_POST['idcliente'];
								
							if($parametro['rel_SeletorData'] == 'select'){ //formata data vinda de campos select (mm/aaaa)
									
								$data1 =  $_POST['anoBaixa'].'-'.$_POST['mesBaixa'].'-01';
								$data2 =  $_POST['anoBaixa'].'-'.$_POST['mesBaixa'].'-'.$form->get_LastDayMonth($_POST['mesBaixa'],$_POST['anoBaixa']);

							}
							else{ //Formata data vindo do Datepicker (dd/mm/aaaa)
									
								$data1 = $form->formataDataParaInserir($_POST['data_baixa_de']);
								$data2 = $form->formataDataParaInserir($_POST['data_baixa_ate']);
							}
								
							//Armazena dados em outro campo para exibir o relatório
							$_POST['data_baixa_de_relatorio'] = $form->formataDataParaExibir($data1);
							$_POST['data_baixa_ate_relatorio'] =  $form->formataDataParaExibir($data2);

						}



						//Salva a mensagem para ser exibida na impressao do demonstrativo
						if($_POST['salvar_msg'] == '1'){
							$movimento->salva_msg_demonstrativo($idcliente, date('Y-m-d'), addslashes($_POST['mensagem']));
						}

						// lista as contas a receber das comissões do vendedor
						$list= $movimento->make_list_demonstrativo($idcliente, $data1, $data2,true);


						// Recupera os dados do cliente
						$dados_cliente = $cliente->getById($idcliente);

						// Pega a última mensagem cadastrada para o demonstrativo
						$mensagem = $movimento->getLast_msg_demonstrativo($idcliente,$_POST['dataMsg']);


						// Faz a lista das mensagens de demosntrativos salvas nos últimos meses
						$mensagens = $movimento->list_msg_demonstrativo($idcliente);

						 
						//passa a listagem para o template
						$smarty->assign("list", $list);
						$smarty->assign("dados_cliente", $dados_cliente);
						$smarty->assign("mensagem", $mensagem);
						$smarty->assign("mensagens", $mensagens);


					}
					else{

						//Pega a data do mês corrente para deixar pré-selecionado no filtro de busca
						$_POST['mesBaixa'] = date('m');
						$_POST['anoBaixa'] = date('Y');

					}


					break;

				case "razonete":


					// busca os dados da filial
					$info_filial = $filial->getById($_SESSION['idfilial_usuario']);
					$smarty->assign("info_filial", $info_filial);

					// busca o dia e hora atual
					$flags['data_hora_atual'] = date('d/m/Y H:i:s');

						
					// busca os dados do relatório de recebimentos do vendedor
					if($_POST['for_chk']) {

						if( isset($_GET['target']) && $_GET['target'] == 'full'){
								
							$idcliente = (int)$_POST['idcliente_relatorio'];
							$data1 = $form->formataDataParaInserir($_POST['data_baixa_de_relatorio']);
							$data2 = $form->formataDataParaInserir($_POST['data_baixa_ate_relatorio']);
						}
						else{
								
							$idcliente = (int)$_POST['idcliente'];
							$data1 = $form->formataDataParaInserir($_POST['data_baixa_de']);
							$data2 = $form->formataDataParaInserir($_POST['data_baixa_ate']);
						}


						//Prepara o filtro de planos
						if($_POST['idplano_ini'] != '' && $_POST['idplano_fim'] != ''){

							$aux = explode('-', $_POST['idplano_ini_Nome']);
							$NumPlano['inicio'] = trim($aux[0]);

							$aux = explode('-', $_POST['idplano_fim_Nome']);
							$NumPlano['fim'] = trim($aux[0]);
						}
						else $NumPlano = null;


						// lista as contas a receber das comissões do vendedor
						$list = $movimento->make_list_caixa($idcliente, $data1, $data2,true, $NumPlano);
						$dados_cliente = $cliente->getById($idcliente);

						//passa a listagem para o template
						$smarty->assign("list", $list);
						$smarty->assign("dados_cliente", $dados_cliente);

					}
					;
						
						
					break;

				case "balancete" :
						
					// busca os dados da filial
					$info_filial = $filial->getById($_SESSION['idfilial_usuario']);
					$smarty->assign("info_filial", $info_filial);

					// busca o dia e hora atual
					$flags['data_hora_atual'] = date('d/m/Y H:i:s');

						
					// Prepara o filtro de busca
					if($_POST['for_chk']) {


						//Define o complemento do nome do campo para o caso da Tela de Impressão
						$complemento = (isset($_GET['target']) && $_GET['target'] == 'full') ? '_relatorio' : null ;

						//Formata os campos e prepara os filtros
						$info['idcliente'] = (int)$_POST['idcliente'.$complemento];
						$info['data_baixa1'] = $form->formataDataParaInserir($_POST['data_baixa_de'.$complemento]);
						$info['data_baixa2'] = $form->formataDataParaInserir($_POST['data_baixa_ate'.$complemento]);


						//Prepara o filtro de planos, caso tenham sido preenchidos
						if($_POST['idplano_ini'] != '' && $_POST['idplano_fim'] != ''){

							//Formata para passar somente o número dos planos
							$aux = explode('-', $_POST['idplano_ini_Nome']);
							$info['plano']['inicio'] = trim($aux[0]);

							$aux = explode('-', $_POST['idplano_fim_Nome']);
							$info['plano']['fim'] = trim($aux[0]);
						}


						// Faz a listagem das contas
						$list = $movimento->make_list_balancete($info);

						$dados_cliente = $cliente->getById($info['idcliente']);

						//passa a listagem para o template
						$smarty->assign("list", $list);
						$smarty->assign("dados_cliente", $dados_cliente);

					}

					break;


        			case "clientes_inadimplentes":

        				if(isset($_POST['for_chk'])){

        					/**
        					 * Se não houver o campo idcliente significa que o botão 
        					 * de impressão do relatório foi acionado.
        					 * Nesse caso os dados de pesquisa são buscados na sessão
        					 */
        					if(!isset($_POST['idcliente'])){

        						if(isset($_SESSION['busca_clientes_inadimplentes']) && 
        							$_SESSION['busca_clientes_inadimplentes']){

        							$_POST = $_SESSION['busca_clientes_inadimplentes'];

        						}
        					}
        					else{

        						/**
        						 * Se houver existir o campo idcliente significa que o botão
        						 * Buscar foi acionado. Nesse caso armazena os dados de pesquisa
        						 * na sessão para o caso de o usuário acionar o botão de tela 
        						 * de impressão
        						 */
        						$_SESSION['busca_clientes_inadimplentes'] = $_POST;

        					}


	        				$clientes_inadimplentes = $movimento->buscaClientesInadimplentes($_POST);

	                        /// registra total dos valores
	                        $valor_total = $clientes_inadimplentes['total'];
	                        unset($clientes_inadimplentes['total']);

	                        $smarty->assign('total',$valor_total);
	        				$smarty->assign('clientes', $clientes_inadimplentes);

	        				if($_POST['data_vencimento_ate'] || $_POST['data_vencimento_de']){

	        					if($_POST['data_vencimento_ate'] && $_POST['data_vencimento_de']){
	        						$descricao_relatorio = $_POST['data_vencimento_de'] . ' a ' .
	        												$_POST['data_vencimento_ate'];
	        					}
	        					else{

		        					if($_POST['data_vencimento_ate']){
	        							$descricao_relatorio = 'Vencimentos at&eacute; ' .
	        												$_POST['data_vencimento_ate'];
	        						}
	        						elseif($_POST['data_vencimento_de']){
	        							$descricao_relatorio = 'Vencimentos a partir de ' .
	        												$_POST['data_vencimento_de'];
	        						}
	        					}
	        				}
	        				else{
	        					$descricao_relatorio = date('d/m/Y');
	        				}
	   				        
	   				        $smarty->assign('descricao_relatorio',$descricao_relatorio);	

	   				    }
   				        
        			break;


        			case "saldo":

        				if(isset($_POST['for_chk'])){

        					/**
        					 * Se não houver o campo idcliente significa que o botão 
        					 * de impressão do relatório foi acionado.
        					 * Nesse caso os dados de pesquisa são buscados na sessão
        					 */
        					if(!isset($_POST['idcliente'])){

        						if(isset($_SESSION['relatorio_saldo_clientes']) && 
        							$_SESSION['relatorio_saldo_clientes']){

        							$_POST = $_SESSION['relatorio_saldo_clientes'];

        						}
        					}
        					else{

        						/**
        						 * Se existir o campo idcliente significa que o botão
        						 * Buscar foi acionado. Nesse caso armazena os dados de pesquisa
        						 * na sessão para o caso de o usuário acionar o botão de tela 
        						 * de impressão
        						 */
        						$_SESSION['relatorio_saldo_clientes'] = $_POST;

        					}

							$idCliente = null;
							if(isset($_POST['idcliente'])){
								$idCliente = $_POST['idcliente'];
							}
							$tipoCliente = 'T';
							if(isset($_POST['tipo_cliente'])){
								$tipoCliente = $_POST['tipo_cliente'];
							}

	        				$saldoClientes = $saldo->geraRelatorio($idCliente, $tipoCliente);
	        				$smarty->assign('saldo', $saldoClientes['saldosClientes']);

	        				$smarty->assign('saldoTotal', $saldoClientes['saldoTotal']);

	        				$descricao_relatorio = "Data de referência: " . $saldoClientes['data'];
	        				$smarty->assign('descricao_relatorio', $descricao_relatorio);
	        			}


	        		break;


			}

		}

	}

	// seta erros
	$smarty->assign("err", $err);

}

// Forma Array de intruções de preenchimento
$intrucoes_preenchimento = array();
if ($flags['action'] == "adicionar" || $flags['action'] == "editar" ) {
	$intrucoes_preenchimento[] = "Os campos em <span class=req>vermelho</span> s&atilde;o obrigat&oacute;rios.";
}
else if ($flags['action'] == "busca_generica" || $flags['action'] == "busca_parametrizada") {
	$intrucoes_preenchimento[] = "Preencha os campos para realizar a busca.";
}

// Formata a mensagem para ser exibida
$flags['intrucoes_preenchimento'] = $form->FormataMensagemAjuda($intrucoes_preenchimento);


$smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));

$smarty->assign("form", $form);
$smarty->assign("flags", $flags);

$list_permissao = $auth->check_priv($conf['priv']);
$smarty->assign("list_permissao",$list_permissao);

if ($_GET['target'] == "full"){
	$smarty->display("adm_relatorio_caixa_impressao.tpl");
}
else{
	$smarty->display("adm_relatorio_caixa.tpl");	
}

?>