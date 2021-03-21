<?php

//inclusão de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/rotinas.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");
require_once("../common/lib/xajax/xajax.inc.php");

require_once("../entidades/movimento.php");
require_once("../entidades/plano.php");
require_once("../entidades/conta_filial.php");
require_once("../entidades/movimento.php");
require_once("../entidades/filial.php");
require_once("../entidades/cliente.php");
require_once("../entidades/cliente_juridico.php");
require_once("../entidades/cliente_condominio.php");
require_once("../entidades/parametros.php");
require_once("../entidades/boleto.php");
require_once("../entidades/funcionario.php");
require_once("../entidades/endereco.php");
require_once("../entidades/apartamento.php");
require_once("../entidades/boleto_instrucao.php");

require_once("movimento_ajax.php");



// configurações anotionais
$conf['area'] = "Movimenta&ccedil;&otilde;es Financeiras"; // área


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
$xajax->registerFunction("Verifica_Campos_Movimento_AJAX");
$xajax->registerFunction("Verifica_Campos_Baixa_AJAX");
$xajax->registerFunction("Emite_Boleto_Ajax");


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
			$movimento = new movimento();
			$plano = new plano();
			$conta_filial = new conta_filial();
			$movimento = new movimento();
			$filial = new filial();
			$cliente = new cliente();
			$cliente_juridico = new cliente_juridico();
                        $cliente_condominio = new cliente_condominio();
			$parametros = new parametros();
			$boleto = new boleto();
			$funcionario = new funcionario();
			$endereco = new endereco();
			$boleto_instrucao = new boleto_instrucao();
			$apartamento = new apartamento();
			// inicializa banco de dados
			$db = new db();

			//incializa classe para validação de formulário
			$form = new form();


			$list = $auth->check_priv($conf['priv']);
			$aux = $flags['action'];
			if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}

			switch($flags['action']) {

				// ação: adicionar <<<<<<<<<<
				case "adicionar":

					if($_POST['for_chk']) {
						 
						$form->chk_empty($_POST['idconta_filial'], 0, 'Conta para Geração Boleto'); 
						$form->chk_empty($_POST['descricao_movimento'], 1, 'Descrição'); 
						$form->chk_empty($_POST['valor_movimento'], 1, 'Valor');
						$form->chk_IsDate($_POST['data_movimento'], "Data de Ocorrência do Movimento"); 
						$form->chk_IsDate($_POST['data_vencimento'], "Data de Vencimento");
							
						$err = $form->err;

						if(count($err) == 0) {

                            $_POST['descricao_movimento'] = addslashes($_POST['descricao_movimento']);
							$_POST['observacao'] = nl2br($_POST['observacao']);
							$_POST['valor_movimento'] = str_replace(",",".",$_POST['valor_movimento']);
							$_POST['valor_juros'] = str_replace(",",".",$_POST['valor_juros']);
							$_POST['valor_multa'] = str_replace(",",".",$_POST['valor_multa']);
							$_POST['desconto'] = str_replace(",",".",$_POST['desconto']);
							$_POST['taxa_boleto'] = str_replace(",",".",$_POST['taxa_boleto']);
							$_POST['data_movimento'] = $form->FormataDataParaInserir($_POST['data_movimento']);
							$_POST['data_vencimento'] = $form->FormataDataParaInserir($_POST['data_vencimento']);
							$_POST['data_cadastro'] = date("Y-m-d H:i:s");
							$_POST['data_baixa_D'] = $form->FormataDataParaInserir($_POST['data_baixa_D']);
							$_POST['data_baixa'] = $_POST['data_baixa_D'] . " " . $_POST['data_baixa_H'];
							$_POST['idfilial'] = $_SESSION['idfilial_usuario'];  //Insere a filial em que o usuário está logado

							if($_POST['baixado']){
								$_POST['tipo_baixa'] = 'M';
							}

							//grava o registro no banco de dados
							$idmovimento = $movimento->set($_POST);

							//obtém os erros que ocorreram no cadastro
							$err = $movimento->err;
								
							//se não ocorreram erros
							if(count($err) == 0) {
								
								if( isset($_POST['gerar_recibo'])){
									echo "<script language='javascript'>window.open('".$conf['addr']."/admin/movimento.php?ac=editar&idmovimento=".$idmovimento."&imprimir=1');</script>";								 								
								}
								
								if( isset($_POST['gerar_fatura']) && $_POST['gerar_fatura'] == '1'){
									echo "<script language='javascript'>window.open('".$conf['addr']."/admin/movimento.php?ac=gerar_boleto&banco=".$_POST['banco']."&idmovimento=".$idmovimento."');</script>";
								}
								
								$flags['sucesso'] = $conf['inserir'];
									
								//limpa o $flags.action para que seja exibida a listagem
								$flags['action'] = "listar";
									
								//lista
								$filtro = 'WHERE FLI.idfilial = '.$_SESSION['idfilial_usuario'];
								$list = $movimento->make_list(0, $conf['rppg'],$filtro);
									
								//pega os erros
								$err = $movimento->err;
									
								//envia a listagem para o template
								$smarty->assign("list", $list);
	
							}

						}

					}
					else{
				 
						$_POST['data_movimento'] = date("d/m/Y");
						$_POST['data_vencimento'] = date("d/m/Y");
						$_POST['taxa_boleto'] = $parametros->getParam('taxa_boleto');

						$juros = $parametros->getParam('juros_boleto_avulso');
						$multa = $parametros->getParam('multa_boleto_avulso');

						$juros = str_replace(',','.',$juros);
						$multa = str_replace(',','.',$multa);

						$smarty->assign('juros', $form->FormataMoedaParaExibir($juros));
						$smarty->assign('multa', $form->FormataMoedaParaExibir($multa));
					}

					
				break;


				//listagem dos registros
				case "listar":

		      		$lista_bancos = array(
		      				'id_banco' => array('sicoob','bradescomila','bradescosos', 'caixa','itau','itauestrela', 'santander'),
		      				'nome_banco' => array('Sicoob','Bradesco Mila Center', 'Bradesco SOS Prestadora', 'Caixa Econ&ocirc;mica Federal', 'Ita&uacute;', 'Ita&uacute; Estrela da Mata', 'Santander')
		      		);
		      		$smarty->assign('lista_bancos', $lista_bancos);


					//obtém qual página da listagem deseja exibir
					if(isset($_GET['pg'])){
						$pg = intval(trim($_GET['pg']));
						$_POST = $_GET;	
					}

					//se não foi passada a página como parâmetro, faz página default igual à página 0
					if(!$pg) $pg = 0;
					
					$rppg = isset($_POST['rppg']) ? (int)$_POST['rppg'] : $conf['rppg'];												
		
					//lista os registros
					if(count($_POST)){

						//Seleciona os parâmetros adicionais do POST e concatena no filtro
						$busca = $movimento->cria_filtro_movimento($_POST, true);
						$filtro .= $busca['filtro'];


						//Faz a listagem dos movimentos com base no filtros setados
						$list = $movimento->make_list($pg, $rppg, $filtro, "", $busca['parametros_get']);


						//Grava a listagem na sessão para a impressão em massa de boletos            
						$_SESSION['pagina_boletos'] = $list;
						$_SESSION['movimentos_get'] = $busca['parametros_get'];

						//pega os erros
						$err = $movimento->err;  			

						//passa a listagem para o template
						$smarty->assign("list", $list);
						$smarty->assign("soma_movimento", $soma_movimento);

				    }


					//Mensagem de sucesso ao alterar ou excluir item
					if(isset($_GET['sucesso'])) $flags['sucesso'] = $_GET['sucesso'];

					//Parametros de busca        	
					$smarty->assign("parBusca", $busca['parametros_get']);
					
					//Instruções para geração de boletos
					$smarty->assign("boleto_instrucoes",  $parametros->getParam('boleto_instrucoes') );
					
					
					if(isset($_GET['boletos']) && $_GET['boletos'] == '1'){
						
						$instrucoes = $boleto_instrucao->getInstrucoes();
						$smarty->assign("instrucoes", $instrucoes);
						
					}
					


				break;


				case "baixar_movimentos":


					
					//obtém qual página da listagem deseja exibir
					if(isset($_GET['pg'])){
						$pg = intval(trim($_GET['pg']));
						$_POST = $_GET;	
					}
					
					//se não foi passada a página como parâmetro, faz página default igual à página 0
					if(!$pg) $pg = 0; 
					
					$rppg = isset($_POST['rppg']) ? (int)$_POST['rppg'] : $conf['rppg'];
					


					if( isset($_POST) && !empty($_POST) ) {

						if(count($_POST['baixar'])){
							$data_baixa = $form->FormataDataParaInserir($_POST['data_baixa_D']) . ' ' . $_POST['data_baixa_H'];
							
							$sql_q = array();
							
							
							foreach ($_POST['baixar'] as $idMovimento){

								$desconto = $form->FormataMoedaParaInserir($_POST['desconto_' . $idMovimento]);
								$juros = $form->FormataMoedaParaInserir($_POST['juros_' . $idMovimento]);
								$multa = $form->FormataMoedaParaInserir($_POST['multa_' . $idMovimento]);
								
								$sql_q[] = "UPDATE 
													movimento 
												SET 
													baixado = '1', 
													data_baixa = '$data_baixa',
													desconto = $desconto,
													valor_multa = $multa,
													valor_juros = $juros,
													valor_movimento = (valor_movimento + $juros + $multa - $desconto),
													tipo_baixa = 'M'
												WHERE 
													idmovimento = $idMovimento";
								
								
							}
							
							
							foreach($sql_q as $q){
								
								if ($db->query($q)) $flags['sucesso'] = $conf['alterar'];
								else $movimento->err = $falha['alterar'];								
							}
							
						}
						else{
					
							$_POST['baixado'] = '0';	

							$busca = $movimento->cria_filtro_movimento($_POST, true);
							$filtro = $busca['filtro'];


							$list = $movimento->make_list($pg, $rppg, $filtro, "", 'ac=baixar_movimentos'.$busca['parametros_get']);
							$err = $movimento->err;
						
							//passa a listagem para o template
							$smarty->assign("list", $list);
							$smarty->assign("parBusca", $busca['parametros_get']);
						}

					}
					
					
				break;
				
				// ação: editar <<<<<<<<<< 
				case "editar":

				  	if($_POST['for_chk']) {
		
				  		//Recupera as informações de um possível POST anterior para que as informações do formulário não sejam perdidas
				  		$info = $_POST;
				  		$info['idmovimento'] = $_GET['idmovimento'];
		
				  			
				  		//Verifica se os campos obrigatórios estão preenchidos		
				  		$form->chk_empty($_POST['litdescricao_movimento'], 1, 'Descrição'); 
				  		$form->chk_empty($_POST['numvalor_movimento'], 1, 'Valor');
				  		$form->chk_IsDate($_POST['litdata_movimento'], 'Data de Ocorrência do Movimento'); 
				  		$form->chk_IsDate($_POST['litdata_vencimento'], 'Data de Vencimento');
				  			
				  			
				  		$err = $form->err;
		
				  		if(count($err) == 0) {
		
				  			$_POST['litobservacao'] 	 	= nl2br($_POST['litobservacao']);
				  			$_POST['numvalor_movimento'] 	= str_replace(",",".",$_POST['numvalor_movimento']);
				  			$_POST['numvalor_juros']		= str_replace(",",".",$_POST['numvalor_juros']);
				  			$_POST['numvalor_multa']		= str_replace(",",".",$_POST['numvalor_multa']);
				  			$_POST['numdesconto']			= str_replace(",",".",$_POST['numdesconto']);
				  			$_POST['numtaxa_boleto']		= str_replace(",",".",$_POST['numtaxa_boleto']);
				  			$_POST['litdata_movimento']	= $form->FormataDataParaInserir($_POST['litdata_movimento']);
				  			$_POST['litdata_vencimento']	= $form->FormataDataParaInserir($_POST['litdata_vencimento']);
				  			$_POST['numidcliente_origem']	= intval($_POST['idcliente_origem']);
				  			$_POST['numidcliente_destino']= intval($_POST['idcliente_destino']);
				  			$_POST['numidplano_credito'] 	= intval($_POST['idplano_credito']);
				  			$_POST['numidplano_debito'] 	= intval($_POST['idplano_debito']);
				  			$_POST['numidconta_filial'] 	= intval($_POST['idconta_filial']);
				  			$_POST['litnegociacaoObservacoes'] 	 	= nl2br($_POST['litnegociacaoObservacoes']);
				  			
				  			if($_POST['litbaixado'] == '1'){
				  				$_POST['data_baixa_D']	= $form->FormataDataParaInserir($_POST['data_baixa_D']);
				  				$_POST['litdata_baixa'] = $_POST['data_baixa_D'] . " " . $_POST['data_baixa_H'];
				  				$_POST['littipo_baixa'] = 'M';
				  			}
				  			else $_POST['litdata_baixa'] = null;

				  			$movimento->update(intval($_GET['idmovimento']), $_POST);
		
		
				  			//obtém erros
				  			$err = $movimento->err;
		
				  			//se não ocorreram erros
				  			if(count($err) == 0) {
								//Volta para a tela de listagem com os filtros que foram setados anteriormente
								$location = $conf['addr'].'/admin/movimento.php?ac=listar&pg='.$_GET['pg'].$_SESSION['movimentos_get'].'&sucesso='.$conf['alterar'];								
								header("Location: $location");

				  			}
		
				  		}
				  			
				  	}
				  	else {
		
				  		//busca detalhes
				  		$info = $movimento->getById($_GET['idmovimento']);

				  		//tratamento das informações para fazer o UPDATE
				  		if(!empty($info['idcliente_destino'])) 	$info['cliente_destino']  = $cliente->BuscaDadosCliente($info['idcliente_destino']);
				  		if(!empty($info['idcliente_origem'])) 	$info['cliente_origem']   = $cliente->BuscaDadosCliente($info['idcliente_origem']);
				  		if(!empty($info['idplano_debito'])) 	$info['plano_debito'] 	  = $plano->getById($info['idplano_debito']);
				  		if(!empty($info['idplano_credito'])) 	$info['plano_credito'] 	  = $plano->getById($info['idplano_credito']);
				  		if(!empty($info['idconta_filial'])) 	$info['conta_filial'] 	  = $conta_filial->getById($info['idconta_filial']);
				  		if(!empty($info['idmovimento_origem'])) $info['movimento_origem'] = $movimento->getById($info['idmovimento_origem']);
				  		
				  		$info['funcionario'] = $funcionario->getById($_SESSION['usr_cod']);
				  		$info['funcionario']['endereco'] = $endereco->BuscaDadosEndereco($info['funcionario']['idendereco_funcionario']);
				  		
				  		$info['litdescricao_movimento'] = $info['descricao_movimento'];
				  		$info['litcontrole_movimento'] = $info['controle_movimento'];
				  		$info['numvalor_movimento'] = $info['valor_movimento'];
				  		$info['litdata_cadastro'] = $info['data_cadastro'];
				  		$info['litdata_movimento'] = $info['data_movimento'];
				  		$info['litdata_baixa'] = $info['data_baixa'];
				  		$info['litbaixado'] = $info['baixado'];
				  		$info['litdata_vencimento'] = $info['data_vencimento'];
				  		$info['litobservacao'] = strip_tags($info['observacao']);
				  		$info['numvalor_juros'] = $info['valor_juros'];
				  		$info['numvalor_multa'] = $info['valor_multa'];
				  		$info['litgerar_fatura'] = $info['gerar_fatura'];
				  		$info['num_movimento'] = STR_PAD($info['idmovimento'],5,'0',STR_PAD_LEFT);
				  		$info['litnegociacaoObservacoes'] = strip_tags($info['negociacaoObservacoes']);

				  		/// se o campo de negociação não estiver preenchido, preenche com 0 (não)
				  		if(!$info['negociacao']){
				  			$info['negociacao'] = '0';
				  		}
				  		$info['litnegociacao'] = $info['negociacao'];

				  		//Informação para impressão de recibo
				  		if($_GET['imprimir'] == 1){
				  			
				  			$info['cliente_recibo']	= ($_GET['cliente_recibo'] == 'destino') ? ($info['cliente_destino']) : ($info['cliente_origem']);


							/** Busca os dados da Filial */
							$filial = new Filial();

							$info['filial'] = $filial->BuscaDadosFilial($_SESSION['idfilial_usuario']);
							$info['filial']['endereco']['linha1'] = ucwords(strtolower($info['filial']['endereco']['linha1'])); 
							$info['filial']['nome_cidade'] 		  = ucwords(strtolower($info['filial']['nome_cidade']));						 
				  			
				  		}
				  		
				  		//obtém os erros
				  		$err = $movimento->err;

				  		//Verifica se o usuário está tentando acessar a movimentação de outra filial
				  		if ($info['idfilial'] != $_SESSION['idfilial_usuario']){
				  			$err[] = "Esta movimentação não corresponde a filial em que você está logado.";
				  			$flags['okay'] = 0;
				  		}
				  			
				  	}

				  	
				  	
				  	
				  	$list_plano = $plano->make_list_select();
				  	$smarty->assign("list_plano",$list_plano);
		
				  	$list_plano = $plano->make_list_select();
				  	$smarty->assign("list_plano",$list_plano);
		
				  	$list_conta_filial = $conta_filial->make_list_select();
				  	$smarty->assign("list_conta_filial",$list_conta_filial);
		
				  	$list_filial = $filial->make_list_select();
				  	$smarty->assign("list_filial",$list_filial);
		
		
		
				  	//passa os dados para o template
				  	$smarty->assign("info", $info);
		
			break;
		  		
			case "gerar_boleto":

				$idmovimento = (int)$_GET['idmovimento'];
				$info                 = $movimento->getById($idmovimento);
				$info_cliente         = $cliente->BuscaDadosCliente($info['idcliente_origem']);
                $info_filial          = $filial->BuscaDadosFilial($_SESSION['idfilial_usuario']);
				$info_cliente_destino = $cliente->BuscaDadosCliente($info['idcliente_destino']);

				/// verifica conta de crédito
				/// o modelo do boleto é definido de acordo com a conta de crédito
				/// para condomínios o modelo é diferente dos demais
				$plano_contas_condominio = $parametros->getParam('idplanoBoletoCond');

				if($plano_contas_condominio == $info['idplano_credito']){
					
					$condominio = true;
					
					/**
					 * gera relatórios demonstrativos do condomínio e do caixa proprietário
					 * para mostrar no boleto
					 */
					
					$data_movimento = explode('/',$info['data_movimento']);
					
					/// define datas de início e fim do relatório de acordo com a data do movimento
					$data_inicio =  $data_movimento[2] . '-' . $data_movimento[1] . '-01';
					$data_fim =  $data_movimento[2] . '-' . 
								$data_movimento[1] . '-' . 
								$form->get_LastDayMonth($data_movimento[1],$data_movimento[2]);

					$mes_relatorios = '01/' . $data_movimento[1] . '/' . $data_movimento[2] . 
										' a ' .
										$form->get_LastDayMonth($data_movimento[1],$data_movimento[2]) . '/' . 
										$data_movimento[1] . '/' . $data_movimento[2];
						
					

					if($info_cliente_destino['idcliente']){
						/// busca relatório demonstrativo do cliente						
						$demonstrativo = $movimento->criaRelatorioCondominio($info_cliente_destino['idcliente'], $data_inicio, $data_fim);
					}
					else{
						$demonstrativo = array();
					}

					if($info_cliente_destino['idcliente']){
						/// busca relatório dos caixas-proprietário
						$caixa_proprietario = $movimento->criaRelatorioCaixaProprietario($info_cliente_destino['idcliente'], $data_inicio, $data_fim);
					}
					else{
						$caixa_proprietario = array();
					}
					
				}
				else{
					$condominio = false;
					$mes_relatorios = '';
					$demonstrativo = array();
					$caixa_proprietario = array();					
				}

				// Dados de cobrança
				$dias_de_prazo_para_pagamento = 5;
				$taxa_boleto = empty($info['taxa_boleto']) ? 0.00 : $form->FormataMoedaParaInserir($info['taxa_boleto']);
				$valor_movimento = $form->FormataMoedaParaInserir($info['valor_movimento']);
                                //$data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias  OU  informe data: "13/04/2006"  OU  informe "" se Contra Apresentacao;	
				$dadosboleto["valor_boleto"] = $form->FormataMoedaParaExibir($valor_movimento + $taxa_boleto);
								
				// Dados do Cedente
				$dadosboleto["identificacao"] = strtoupper($info_filial['nome_filial']);
				$dadosboleto["cpf_cnpj"]      = $info_filial['cnpj_filial'];
				$dadosboleto["endereco"]      = $info_filial['endereco']['linha1'];
				$dadosboleto["cidade_uf"]     = $info_filial['nome_cidade'].' / '.$info_filial['sigla_estado']; 
				$dadosboleto['cep']			  = $info_filial['cep'];
				$dadosboleto["cedente"]	      = strtoupper($info_filial['nome_filial']);

				$dadosboleto["data_vencimento"] = $info['data_vencimento']; //$data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
				$dadosboleto["data_documento"] = date("d/m/Y");             // Data de emissão do Boleto
				$dadosboleto["data_processamento"] = date("d/m/Y");         // Data de processamento do boleto (opcional)

					
				$nomeCondominio = "";
								
				//Verifica se é um cliente jurídico				
				$juridico = $cliente_juridico->getById($info_cliente['idcliente']);
				
				//if(isset($apto)){ //Taxa de Condomínio - Usa o endereço do condomínio + apartamento como endereço do sacado
				if($info['idapartamento']){

					//Pega o número do apartamento para o caso de condomínio
					$apto = $apartamento->getById($info['idapartamento']);
					$apto = $apto['apto'];
						
					$nomeCondominio = "<b>( " . strtoupper($info_cliente_destino['nome_cliente']) . " )</b>";
					
					$endereco = $info_cliente_destino['logradouro'] . ", " . $info_cliente_destino['numero'] . " - apto: $apto - ";
					$endereco .=  $info_cliente_destino['nome_bairro'] . " - " . $info_cliente_destino['nome_cidade'] . " - " . $info_cliente_destino['sigla_estado'] . "<br />";
					$endereco .=  "CEP: " . $info_cliente_destino['cep'];
					
					$dadosboleto['nome_cliente'] = $info_cliente['nome_cliente']. " " .$nomeCondominio . " - Apartamento: " . $apto;					
					
				}
				elseif(isset($juridico ['idcliente'])){
					
					$endereco = $info_cliente['logradouro'] . ", " . $info_cliente['numero'] . ' - ' . $info_cliente['complemento'].' ';
					$endereco .=  $info_cliente['nome_bairro'] . " - " . $info_cliente['nome_cidade'] . " - " . $info_cliente['sigla_estado'] . "<br />";
					$endereco .=  "CEP: " . $info_cliente['cep'];
					
					$dadosboleto['nome_cliente'] = $info_cliente['nome_cliente']. " " .$nomeCondominio;			
			
				}
				else{ // Clientes Avulsos
					
					$endereco = $info_cliente['logradouro'] . ", " . $info_cliente['numero'] . " - " .  $info_cliente['complemento'] . " - ";
					$endereco .=  $info_cliente['nome_bairro'] . " - " . $info_cliente['nome_cidade'] . " - " . $info_cliente['sigla_estado'] . "<br />";
					$endereco .=  "CEP: " . $info_cliente['cep'];

					$dadosboleto['nome_cliente'] = $info_cliente['nome_cliente']. " " .$nomeCondominio;
					
				}
				
				// DADOS DO CLIENTE
				$cpf_cnpj = '';
				if(isset($info_cliente['cpf_cliente'])){
					$cpf_cnpj = 'CPF: ' . $info_cliente['cpf_cliente'];
				}
				elseif(isset($info_cliente['cnpj_cliente'])){
					$cpf_cnpj = 'CNPJ: ' . $info_cliente['cnpj_cliente'];
				}

				$dadosboleto["sacado"]	  = $info_cliente['nome_cliente'] . "<br />" . $cpf_cnpj . " $nomeCondominio<br />"  . $endereco ;				


				//Endereço do cliente
				if ($info_cliente['logradouro'] != '')  $info_cliente['endereco1']  = $dadosboleto["logradouro"].', '.$info_cliente['numero'];
				if ($info_cliente['complemento'] != '') $info_cliente['endereco1'] .= $info_cliente['complemento'].' - ';			
				if ($info_cliente['nome_bairro'] != '') $info_cliente['endereco1'] .= $info_cliente['nome_bairro'];
				if ($info_cliente['nome_cidade'] != '') $info_cliente['endereco2'] .= $info_cliente['nome_cidade'].' - '.$info_cliente['sigla_estado'];			
				if ($info_cliente['cep'] != '')         $info_cliente['endereco2'] .= ' - '.$info_cliente['cep'];
				
				
				
				$instrucoes = explode("\n",$_POST['instrucoes']);
				$dadosboleto["instrucoes1"] = $instrucoes[0];
				$dadosboleto["instrucoes2"] = $instrucoes[1];
				$dadosboleto["instrucoes3"] = $instrucoes[2];
				$dadosboleto["instrucoes4"] = $instrucoes[3];
				$dadosboleto["numero_documento"] = str_pad($idmovimento,5,'0',STR_PAD_LEFT);	// Num do pedido ou do documento
				
				$dadosboleto['mes_relatorios'] = $mes_relatorios;

				//Dados do Boleto
				$dadosboleto["nosso_numero"] = $idmovimento;  // Nosso numero sem o DV - REGRA: Máximo de 8 caracteres!
				$dadosboleto["nosso_numero_boleto"] = $info['nosso_numero'];


				switch ($_GET['banco']) {

					case 'bradescomila':

						if($info['idapartamento']){
						
							require_once dirname(dirname(__FILE__)) . '/entidades/demonstrativo_apartamento.php';

							$demonstrativo_apartamento = new demonstrativo_apartamento();

							$demonstrativo = $demonstrativo_apartamento->getByApartamento($info['idapartamento']);

							$dadosboleto['demonstrativo1'] = $demonstrativo['demonstrativo'];

						}

						if(isset($_POST['demonstrativo']) && $_POST['demonstrativo']){

							/// Retira quebras de linha do início e do fim do demonstrativo e deixa apenas uma
							#$_POST['demonstrativo'] = trim($_POST['demonstrativo'],"<p>");
							$_POST['demonstrativo'] = str_replace(array("<p>","</p>"),array("<br />",""),$_POST['demonstrativo']);

							$dadosboleto['demonstrativo2'] = $_POST['demonstrativo'];									
						}
						
						$boleto->bradesco($dadosboleto,false,array(),'',$_GET['banco']);


						break;


					case 'itauestrela':

						if($info['idapartamento']){
						
							require_once dirname(dirname(__FILE__)) . '/entidades/demonstrativo_apartamento.php';

							$demonstrativo_apartamento = new demonstrativo_apartamento();

							$demonstrativo = $demonstrativo_apartamento->getByApartamento($info['idapartamento']);

							$dadosboleto['demonstrativo1'] = $demonstrativo['demonstrativo'];

						}

						if(isset($_POST['demonstrativo']) && $_POST['demonstrativo']){

							/// Retira quebras de linha do início e do fim do demonstrativo e deixa apenas uma
							#$_POST['demonstrativo'] = trim($_POST['demonstrativo'],"<p>");
							$_POST['demonstrativo'] = str_replace(array("<p>","</p>"),array("<br />",""),$_POST['demonstrativo']);

							$dadosboleto['demonstrativo2'] = $_POST['demonstrativo'];									
						}
						
						$boleto->itau($dadosboleto,false,array(),'',$_GET['banco']);


						break;

					case 'caixa':
					case 'itau':
					case 'sicoob':
					case 'bradescosos':

						$banco = $_GET['banco'];
	  	
						if($banco == 'bradescosos'){
							$banco = 'bradesco';
						}

						//$metodoCondominio = $banco . 'Condominio';					

						if($condominio){
							$boleto->$banco($dadosboleto, true, $demonstrativo, $caixa_proprietario, $_GET['banco']);
						}
						else{

							if(isset($_POST['demonstrativo']) && $_POST['demonstrativo']){

                                /// Retira quebras de linha do início e do fim do demonstrativo e deixa apenas uma
           		                $_POST['demonstrativo'] = str_replace(array("<p>","</p>"),array("<br />",""),$_POST['demonstrativo']);

								$dadosboleto['demonstrativo2'] = $_POST['demonstrativo'];
							}

							$boleto->$banco($dadosboleto, false,'','',$_GET['banco']);
						}

						break;

					case 'santander':

						$boleto->santander($dadosboleto);

						break;

					
					default:

						$err[] = 'Não foi possivel encontrar as informações do banco.';

						break;
				}
				
				exit;
			
		  		
		  	break;

			case "gerar_boleto_full":

				$list = $_SESSION['pagina_boletos'];

				/// armazena informação da conta de crédito de boletos de condomínio
				/// para gerar boleto com relatório se for o caso
				$plano_contas_condominio = $parametros->getParam('idplanoBoletoCond');

					
				foreach($list as $l){
					
					if($l['idmovimento']){
  									
	  					$info = $movimento->getById($l['idmovimento']);			
	  					$info_cliente = $cliente->BuscaDadosCliente($info['idcliente_origem']);
	  					$info_cliente_destino = $cliente->BuscaDadosCliente($info['idcliente_destino']);
	  					
	  					$info_filial = $filial->BuscaDadosFilial($_SESSION['idfilial_usuario']);


						/// verifica conta de crédito
						/// o modelo do boleto é definido de acordo com a conta de crédito
						/// para condomínios o modelo é diferente dos demais


						if($plano_contas_condominio == $info['idplano_credito']){
							
							$condominio = true;
							
							/**
							 * gera relatórios demonstrativos do condomínio e do caixa proprietário
							 * para mostrar no boleto
							 */
							
							$data_movimento = explode('/',$info['data_movimento']);
							
							/// define datas de início e fim do relatório de acordo com a data do movimento
							$data_inicio =  $data_movimento[2] . '-' . $data_movimento[1] . '-01';
							$data_fim =  $data_movimento[2] . '-' . 
										$data_movimento[1] . '-' . 
										$form->get_LastDayMonth($data_movimento[1],$data_movimento[2]);
							
							$mes_relatorios = '01/' . $data_movimento[1] . '/' . $data_movimento[2] .
												' a ' .
												$form->get_LastDayMonth($data_movimento[1],$data_movimento[2]) . '/' .
												$data_movimento[1] . '/' . $data_movimento[2];
							

							/// busca relatório demonstrativo do cliente						
							$demonstrativo = $movimento->criaRelatorioCondominio($info_cliente_destino['idcliente'], $data_inicio, $data_fim);

							/// busca relatório dos caixas-proprietário
							$caixa_proprietario = $movimento->criaRelatorioCaixaProprietario($info_cliente_destino['idcliente'], $data_inicio, $data_fim);
						}
						else{
							$condominio = false;
							$demonstrativo = array();
							$caixa_proprietario = array();
							$mes_relatorios = '';
						}  					
	  	
	  		
	  					// Dados de cobrança
	  					$dias_de_prazo_para_pagamento = 5;
	  					$taxa_boleto = empty($info['taxa_boleto']) ? 0.00 : $form->FormataMoedaParaInserir($info['taxa_boleto']);
	  					$valor_movimento = $form->FormataMoedaParaInserir($info['valor_movimento']);
			            
	                    //$data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias  OU  informe data: "13/04/2006"  OU  informe "" se Contra Apresentacao;          
	                    $dadosboleto["valor_boleto"] = $form->FormataMoedaParaExibir($valor_movimento + $taxa_boleto);
	                    $taxa_boleto = empty($info['taxa_boleto']) ? 0.00 : $form->FormataMoedaParaInserir($info['taxa_boleto']);

	  								          				
	  					// Dados da Filial
	  					$dadosboleto["identificacao"] = strtoupper($info_filial['nome_filial']);
	  					$dadosboleto["cpf_cnpj"] 	  = $info_filial['cnpj_filial'];
	  					$dadosboleto["endereco"] 	  = $info_filial['endereco']['linha1'];
	  					$dadosboleto["cidade_uf"]	  = $info_filial['nome_cidade'].' / '.$info_filial['sigla_estado']; 
	  					$dadosboleto['cep']			  = $info_filial['cep'];
	  					$dadosboleto["cedente"]		  = strtoupper($info_filial['nome_filial']);
	  	
	  					$dadosboleto["data_vencimento"] = $info['data_vencimento']; //$data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
	  					$dadosboleto["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto
	  					$dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)											
	  						
	  					$nomeCondominio = "";
	  					
	  					//Verifica se é um cliente jurídico				
						$juridico = $cliente_juridico->getById($info_cliente['idcliente']);
						
						//if(isset($apto)){ //Taxa de Condomínio - Usa o endereço do condomínio + apartamento como endereço do sacado
						if($info['idapartamento']){

							$apto = $apartamento->getById($info['idapartamento']);
							$apto = $apto['apto'];

							$nomeCondominio = "<b>( " . strtoupper($info_cliente_destino['nome_cliente']) . " )</b>";
						
							$endereco = $info_cliente_destino['logradouro'] . ", " . $info_cliente_destino['numero'] . " - apto: $apto - ";
							$endereco .=  $info_cliente_destino['nome_bairro'] . " - " . $info_cliente_destino['nome_cidade'] . " - " . $info_cliente_destino['sigla_estado'] . "<br />";
							$endereco .=  "CEP: " . $info_cliente_destino['cep'];
							
							$dadosboleto['nome_cliente'] = $info_cliente['nome_cliente']. " " .$nomeCondominio . " - Apartamento: " . $apto;						
						
						}
						elseif(isset($juridico ['idcliente'])){
							
							$endereco = $info_cliente['logradouro'] . ", " . $info_cliente['numero'] . ' - ' . $info_cliente['complemento'].' ';
							$endereco .=  $info_cliente['nome_bairro'] . " - " . $info_cliente['nome_cidade'] . " - " . $info_cliente['sigla_estado'] . "<br />";
							$endereco .=  "CEP: " . $info_cliente['cep'];
							
							$dadosboleto['nome_cliente'] = $info_cliente['nome_cliente']. " " .$nomeCondominio;
							
						}
						else{ // Clientes Físicos

							$endereco = $info_cliente['logradouro'] . ", " . $info_cliente['numero'] . " - " .  $info_cliente['complemento'] . "<br />";
							$endereco .=  $info_cliente['nome_bairro'] . " - " . $info_cliente['nome_cidade'] . " - " . $info_cliente['sigla_estado'] . "<br />";
							$endereco .=  "CEP: " . $info_cliente['cep'];						
							
							$dadosboleto['nome_cliente'] = $info_cliente['nome_cliente']. " " .$nomeCondominio;
						
						}
	  					
	  					// DADOS DO CLIENTE

						$cpf_cnpj = '';
						if(isset($info_cliente['cpf_cliente'])){
							$cpf_cnpj = 'CPF: ' . $info_cliente['cpf_cliente'];
						}
						elseif(isset($info_cliente['cnpj_cliente'])){
							$cpf_cnpj = 'CNPJ: ' . $info_cliente['cnpj_cliente'];
						}

						$dadosboleto["sacado"]	  = $info_cliente['nome_cliente'] . " $nomeCondominio - " . $cpf_cnpj . "<br />"  . $endereco ;

	  					//Endereço do cliente
	  					if ($info_cliente['logradouro'] != '')  $info_cliente['endereco1']  = $dadosboleto["logradouro"].', '.$info_cliente['numero'];
	  					if ($info_cliente['complemento'] != '') $info_cliente['endereco1'] .= $info_cliente['complemento'].' - ';			
	  					if ($info_cliente['nome_bairro'] != '') $info_cliente['endereco1'] .= $info_cliente['nome_bairro'];
	  					if ($info_cliente['nome_cidade'] != '') $info_cliente['endereco2'] .= $info_cliente['nome_cidade'].' - '.$info_cliente['sigla_estado'];			
	  					if ($info_cliente['cep'] != '') $info_cliente['endereco2'] .= ' - '.$info_cliente['cep'];									
	  					
	  					//$instrucoes = explode("\n",$parametros->getParam('boleto_instrucoes'));
	  					$instrucoes = explode("\n",$_POST['instrucoes']);
	  					$dadosboleto["instrucoes1"] = $instrucoes[0];
	  					$dadosboleto["instrucoes2"] = $instrucoes[1];
	  					$dadosboleto["instrucoes3"] = $instrucoes[2];
	  					$dadosboleto["instrucoes4"] = $instrucoes[3];
	  					$dadosboleto["numero_documento"] = str_pad($l['idmovimento'],5,'0',STR_PAD_LEFT);	// Num do pedido ou do documento
	  					
	  					$dadosboleto['mes_relatorios'] = $mes_relatorios;

						$dadosboleto["nosso_numero_boleto"] = $info['nosso_numero'];
						$dadosboleto["nosso_numero"] = $info['idmovimento'];

	  					switch($_GET['banco']){

							case 'bradescomila':

								if($info['idapartamento']){
								
									require_once dirname(dirname(__FILE__)) . '/entidades/demonstrativo_apartamento.php';

									$demonstrativo_apartamento = new demonstrativo_apartamento();

									$demonstrativo = $demonstrativo_apartamento->getByApartamento($info['idapartamento']);

									$dadosboleto['demonstrativo1'] = $demonstrativo['demonstrativo'];

								}

								if(isset($_POST['demonstrativo']) && $_POST['demonstrativo']){
	
                                    /// Retira quebras de linha do início e do fim do demonstrativo e deixa apenas uma
	                                #$_POST['demonstrativo'] = trim($_POST['demonstrativo'],"<p>");
               		                $_POST['demonstrativo'] = str_replace(array("<p>","</p>"),array("<br />",""),$_POST['demonstrativo']);

									$dadosboleto['demonstrativo2'] = $_POST['demonstrativo'];									
								}

								$boleto->bradesco($dadosboleto,false,array(),'',$_GET['banco']);

								break;

							case 'itauestrela':

								if($info['idapartamento']){
								
									require_once dirname(dirname(__FILE__)) . '/entidades/demonstrativo_apartamento.php';

									$demonstrativo_apartamento = new demonstrativo_apartamento();

									$demonstrativo = $demonstrativo_apartamento->getByApartamento($info['idapartamento']);

									$dadosboleto['demonstrativo1'] = $demonstrativo['demonstrativo'];

								}

								if(isset($_POST['demonstrativo']) && $_POST['demonstrativo']){
	
                                    /// Retira quebras de linha do início e do fim do demonstrativo e deixa apenas uma
	                                #$_POST['demonstrativo'] = trim($_POST['demonstrativo'],"<p>");
               		                $_POST['demonstrativo'] = str_replace(array("<p>","</p>"),array("<br />",""),$_POST['demonstrativo']);

									$dadosboleto['demonstrativo2'] = $_POST['demonstrativo'];									
								}

								$boleto->itau($dadosboleto,false,array(),'',$_GET['banco']);

								break;

	  										
	  						case 'caixa':
	  						case 'itau':
	  						case 'sicoob':
	  						case 'bradescosos':
	  	
	  							$banco = $_GET['banco'];
								if($banco == 'bradescosos'){
									$banco = 'bradesco';
								}


								if($condominio){							
									$boleto->$banco($dadosboleto, true, $demonstrativo, $caixa_proprietario, $_GET['banco']);
								}
								else{

									if(isset($_POST['demonstrativo']) && $_POST['demonstrativo']){
		
	                                    /// Retira quebras de linha do início e do fim do demonstrativo e deixa apenas uma
		                                #$_POST['demonstrativo'] = trim($_POST['demonstrativo'],"<p>");
	               		                $_POST['demonstrativo'] = str_replace(array("<p>","</p>"),array("<br />",""),$_POST['demonstrativo']);

										$dadosboleto['demonstrativo2'] = $_POST['demonstrativo'];
									}

									$boleto->$banco($dadosboleto,false,array(),'',$_GET['banco']);
								}  						

								break;
	  					
	  						case 'santander':
	  						
	  							$boleto->santander($dadosboleto);
	  						
							default:  						
	  							$err[] = 'Não foi possivel encontrar as informações do banco.';

	  					}
          
		          	}			
				
        		}//foreach
				
				exit;
			
		  		
		  	break;
		  	
		  	
		  	
		  	// deleta um registro do sistema
			case "excluir":

			  	//verifica se foi pedido a deleção
			  	if($_POST['for_chk']){
			  		 
			  		// deleta o registro
			  		$movimento->delete($_GET['idmovimento']);
	
			  		//obtém erros
			  		$err = $movimento->err;
	
			  		//se não ocorreram erros
			  		if(count($err) == 0){

						//Volta para a tela de listagem com os filtros que foram setados anteriormente
						header('Location: '.$conf['addr'].'/admin/movimento.php?ac=listar&pg='.$_GET['pg'].$_SESSION['movimentos_get'].'&sucesso='.$conf['excluir']);
			  		}
	
			  	}

		  	break;

			}
	   
		}

		// configura variáveis comuns à inserção e à alteração
		if ($flags['action'] == "adicionar" || $flags['action'] == "editar" ) {

			$intrucoes_preenchimento[] = "Os campos em <span class=req>vermelho</span> s&atilde;o obrigat&oacute;rios.";
		}

		$flags['intrucoes_preenchimento'] = $form->FormataMensagemAjuda($intrucoes_preenchimento); // Formata a mensagem para ser exibida

		$data_corrente = array('data' => date("d/m/Y"), 'hora' => date("H:i:s"));
		$smarty->assign("data_corrente", $data_corrente);

	}
	 
	// seta erros
	$smarty->assign("err", $err);

}



//Passa as funções do ajax para o tpl
$smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));


//Passa a lista de permissões para o tpl
$list_permissao = $auth->check_priv($conf['priv']);
$smarty->assign("list_permissao",$list_permissao);

$smarty->assign("form", $form);
$smarty->assign("flags", $flags);



if ( ($flags['action'] == "editar") && ($_GET['imprimir'] == 1) ) $smarty->display("recibo_impressao.tpl");
elseif(($flags['action'] == "listar") && ($_GET['target'] == 'full') ) $smarty->display("adm_relatorio_movimento.tpl");
else $smarty->display("adm_movimento.tpl");
	



?>
