<?php

//inclusão de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/rotinas.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");
require_once("../common/lib/xajax/xajax.inc.php");

require_once("../entidades/fornecedor.php");
require_once("../entidades/endereco.php");
require_once("../entidades/ramo_atividade.php");
require_once("../entidades/bairro.php");
require_once("../entidades/estado.php");
require_once("../entidades/conta_fornecedor.php");

require_once("fornecedor_ajax.php");
require_once("conta_fornecedor_ajax.php");


// configurações anotionais
$conf['area'] = "Fornecedor"; // área


//configuração de estilo
$conf['style'] = "full";

// inicializa templating
$smarty = new Smarty;

// cria o objeto xajax
$xajax = new xajax();


// registra todas as funções que serão usadas

$xajax->registerFunction("Verifica_Campos_Fornecedor_AJAX");
$xajax->registerFunction("Insere_Conta_Bancaria_AJAX");
$xajax->registerFunction("Deleta_Conta_Bancaria_AJAX");
$xajax->registerFunction("Seleciona_Conta_Bancaria_AJAX");



// processa as funções
$xajax->processRequests();


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
			$fornecedor = new fornecedor();
			 
			$endereco = new endereco();
			$ramo_atividade = new ramo_atividade();
			$endereco = new endereco();
			$bairro = new bairro();
			$estado = new estado();
			$conta_fornecedor = new conta_fornecedor();


			// inicializa banco de dados
			$db = new db();

			//incializa classe para validação de formulário
			$form = new form();

			$list = $auth->check_priv($conf['priv']);
			$aux = $flags['action'];
			if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}


			switch($flags['action']) {

				// busca genérica
				case "busca_generica":

					if ( ($_POST['for_chk']) || ($_GET['rpp'] != "") ) {

						$flags['fez_busca'] = 1;

						if ($_POST['for_chk']) {
							$flags['busca'] = $_POST['busca'];
							$flags['rpp'] = $_POST['rpp'];
						}
						else {
							$flags['busca'] = $_GET['busca'];
							$flags['rpp'] = $_GET['rpp'];
						}

						if ($_GET['target'] == "full") $flags['rpp'] = 9999999;


						//obtém qual página da listagem deseja exibir
						$pg = intval(trim($_GET['pg']));

						//se não foi passada a página como parâmetro, faz página default igual à página 0
						if(!$pg) $pg = 0;

						//lista os registros
						$list = $fornecedor->Busca_Generica($pg, $flags['rpp'], $flags['busca'], "", "ac=busca_generica&busca=".$flags['busca']."&rpp=".$flags['rpp']);


						//pega os erros
						$err = $fornecedor->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

					}

					if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];

				break;



					// busca parametrizada
				case "busca_parametrizada":

					if ( ($_POST['for_chk']) || ($_GET['rpp'] != "") ) {

						$flags['fez_busca'] = 1;

						if ($_POST['for_chk']) {
							$flags['nome_fornecedor'] = $_POST['nome_fornecedor'];
							$flags['nome_contato_fornecedor'] = $_POST['nome_contato_fornecedor'];
							$flags['rpp'] = $_POST['rpp'];
						}
						else {
							$flags['nome_fornecedor'] = $_GET['nome_fornecedor'];
							$flags['nome_contato_fornecedor'] = $_GET['nome_contato_fornecedor'];
							$flags['rpp'] = $_GET['rpp'];
						}

						$parametros_get = "&nome_fornecedor=" . $flags['nome_fornecedor'] . "&nome_contato_fornecedor=" . $flags['nome_contato_fornecedor'];


						$filtro_where = "";
						if ($flags['nome_fornecedor'] != "") $filtro_where .= " UPPER(FORN.nome_fornecedor) LIKE UPPER('%" . $flags['nome_fornecedor'] . "%') AND ";
						if ($flags['nome_contato_fornecedor'] != "") $filtro_where .= " UPPER(FORN.nome_contato_fornecedor) LIKE UPPER('%" . $flags['nome_contato_fornecedor'] . "%') AND ";
						$filtro_where = substr($filtro_where,0,strlen($filtro_where)-4);


						if ($_GET['target'] == "full") $flags['rpp'] = 9999999;


						//obtém qual página da listagem deseja exibir
						$pg = intval(trim($_GET['pg']));

						//se não foi passada a página como parâmetro, faz página default igual à página 0
						if(!$pg) $pg = 0;

						//lista os registros
						$list = $fornecedor->Busca_Parametrizada($pg, $flags['rpp'], $filtro_where, "", "ac=busca_parametrizada$parametros_get&rpp=".$flags['rpp']);

						//pega os erros
						$err = $fornecedor->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

					}

					if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];

				break;



					// ação: adicionar <<<<<<<<<<
				case "adicionar":

					if($_POST['for_chk']) {
						 
						 
						 
						$form->chk_empty($_POST['tipo_fornecedor'], 1, 'Tipo do fornecedor');
						$form->chk_empty($_POST['nome_fornecedor'], 1, 'Nome do fornecedor');
						$form->chk_empty($_POST['idramo_atividade'], 1, 'Ramo de atividade');
							

						$err = $form->err;

						if(count($err) == 0) {

							$_POST['telefone_fornecedor'] 	 = $form->FormataTelefoneParaInserir($_POST['telefone_fornecedor_ddd'], $_POST['telefone_fornecedor']);
							$_POST['fax_fornecedor'] 		 = $form->FormataTelefoneParaInserir($_POST['fax_fornecedor_ddd'], $_POST['fax_fornecedor']);
							$_POST['telefone_representante'] = $form->FormataTelefoneParaInserir($_POST['telefone_representante_ddd'], $_POST['telefone_representante']);
							$_POST['celular_representante']  = $form->FormataTelefoneParaInserir($_POST['celular_representante_ddd'], $_POST['celular_representante']);


							$_POST['idbairro'] = $_POST['fornecedor_idbairro'];
							$_POST['idcidade'] = $_POST['fornecedor_idcidade'];
							$_POST['idestado'] = $_POST['fornecedor_idestado'];

							// Grava o registro do endereço no Banco de Dados
							$_POST['idendereco_fornecedor'] = $endereco->InsereEndereco($_POST, "fornecedor");

							$_POST['idbairro'] = $_POST['representante_fornecedor_idbairro'];
							$_POST['idcidade'] = $_POST['representante_fornecedor_idcidade'];
							$_POST['idestado'] = $_POST['representante_fornecedor_idestado'];

							// Grava o registro do endereço no Banco de Dados
							$_POST['idendereco_representante_fornecedor'] = $endereco->InsereEndereco($_POST, "representante_fornecedor");

	       
							//grava o registro no banco de dados
							$idfornecedor = $fornecedor->set($_POST);

							// grava as contas bancarias da filial
							$conta_fornecedor->GravaContasBancariasFornecedor($_POST, $idfornecedor);


							//obtém os erros que ocorreram no cadastro
							$err = $fornecedor->err;

							//se não ocorreram erros
							if(count($err) == 0) {
								$flags['sucesso'] = $conf['inserir'];

								//limpa o $flags.action para que seja exibida a listagem
								$flags['action'] = "listar";

								//lista
								$list = $fornecedor->make_list(0, $conf['rppg']);

								//pega os erros
								$err = $fornecedor->err;

								//envia a listagem para o template
								$smarty->assign("list", $list);

							}

						}

					}


					$list_ramo_atividade = $ramo_atividade->make_list_select();
					$smarty->assign("list_ramo_atividade",$list_ramo_atividade);

					$list_bairro_fornecedor = $bairro->make_list_select();
					$smarty->assign("list_bairro_fornecedor",$list_bairro_fornecedor);

					$list_estado_fornecedor = $estado->make_list_select();
					$smarty->assign("list_estado_fornecedor",$list_estado_fornecedor);

					$list_bairro_representante_fornecedor = $bairro->make_list_select();
					$smarty->assign("list_bairro_representante_fornecedor",$list_bairro_representante_fornecedor);

					$list_estado_representante_fornecedor = $estado->make_list_select();
					$smarty->assign("list_estado_representante_fornecedor",$list_estado_representante_fornecedor);



				break;


					//listagem dos registros
				case "listar":

					//obtém qual página da listagem deseja exibir
					$pg = intval(trim($_GET['pg']));

					//se não foi passada a página como parâmetro, faz página default igual à página 0
					if(!$pg) $pg = 0;

					//lista os registros
					$list = $fornecedor->make_list($pg, $conf['rppg']);

					//pega os erros
					$err = $fornecedor->err;

					//passa a listagem para o template
					$smarty->assign("list", $list);

				break;


					// ação: editar <<<<<<<<<<
				case "editar":

					if($_POST['for_chk']) {
							
							

						$info = $_POST;
							
						$info['idfornecedor'] = $_GET['idfornecedor'];
							
							
						$form->chk_empty($_POST['littipo_fornecedor'], 1, 'Tipo do fornecedor');
						$form->chk_empty($_POST['litnome_fornecedor'], 1, 'Nome do fornecedor');
						$form->chk_empty($_POST['numidramo_atividade'], 1, 'Ramo de atividade');
							
							
						$err = $form->err;

						if(count($err) == 0) {





							$_POST['littelefone_fornecedor'] = $form->FormataTelefoneParaInserir($_POST['telefone_fornecedor_ddd'], $_POST['littelefone_fornecedor']);
							$_POST['litfax_fornecedor'] = $form->FormataTelefoneParaInserir($_POST['fax_fornecedor_ddd'], $_POST['litfax_fornecedor']);
							$_POST['littelefone_representante'] = $form->FormataTelefoneParaInserir($_POST['telefone_representante_ddd'], $_POST['littelefone_representante']);
							$_POST['litcelular_representante'] = $form->FormataTelefoneParaInserir($_POST['celular_representante_ddd'], $_POST['litcelular_representante']);



							// atualiza os dados do endereço
							$endereco->AtualizaEndereco($_POST['idendereco_fornecedor'], $_POST, "fornecedor");

							// atualiza os dados do endereço
							$endereco->AtualizaEndereco($_POST['idendereco_representante_fornecedor'], $_POST, "representante_fornecedor");

							$fornecedor->update($_GET['idfornecedor'], $_POST);

							// grava as contas bancarias da filial
							$conta_fornecedor->GravaContasBancariasFornecedor($_POST, $_GET['idfornecedor']);


							//obtém erros
							$err = $fornecedor->err;

							//se não ocorreram erros
							if(count($err) == 0) {
								$flags['sucesso'] = $conf['alterar'];

								//limpa o $flags.action para que seja exibida a listagem
								$flags['action'] = "listar";

								//lista
								$list = $fornecedor->make_list(0, $conf['rppg']);

								//pega os erros
								$err = $fornecedor->err;

								//envia a listagem para o template
								$smarty->assign("list", $list);

							}

						}

					}
					else {

						//busca detalhes
						$info = $fornecedor->getById($_GET['idfornecedor']);

						//tratamento das informações para fazer o UPDATE
						$info['littipo_fornecedor'] = $info['tipo_fornecedor'];
						$info['litnome_fornecedor'] = $info['nome_fornecedor'];
						$info['litcpf_cnpj'] = $info['cpf_cnpj'];
						$info['litinscricao_estadual_fornecedor'] = $info['inscricao_estadual_fornecedor'];
						$info['litnome_contato_fornecedor'] = $info['nome_contato_fornecedor'];
						$info['numidendereco_fornecedor'] = $info['idendereco_fornecedor'];
						$info['littelefone_fornecedor'] = $info['telefone_fornecedor'];
						$info['litfax_fornecedor'] = $info['fax_fornecedor'];
						$info['litemail_fornecedor'] = $info['email_fornecedor'];
						$info['litsite_fornecedor'] = $info['site_fornecedor'];
						$info['numidramo_atividade'] = $info['idramo_atividade'];
						$info['litnome_representante'] = $info['nome_representante'];
						$info['numidendereco_representante_fornecedor'] = $info['idendereco_representante_fornecedor'];
						$info['littelefone_representante'] = $info['telefone_representante'];
						$info['litcelular_representante'] = $info['celular_representante'];
						$info['litemail_representante'] = $info['email_representante'];
							
						// Busca os dados do endereço
						$dados_endereco = $endereco->BuscaDadosEndereco($info['idendereco_fornecedor'], $info, "fornecedor");
							
						$info['fornecedor_idestado_Nome'] = $dados_endereco['nome_estado'];
						$info['fornecedor_idestado_NomeTemp'] = $dados_endereco['nome_estado'];

						$info['fornecedor_idcidade_Nome'] = $dados_endereco['nome_cidade'];
						$info['fornecedor_idcidade_NomeTemp'] = $dados_endereco['nome_cidade'];

						$info['fornecedor_idbairro_Nome'] = $dados_endereco['nome_bairro'];
						$info['fornecedor_idbairro_NomeTemp'] = $dados_endereco['nome_bairro'];
							
						// Busca os dados do endereço
						$dados_endereco = $endereco->BuscaDadosEndereco($info['idendereco_representante_fornecedor'], $info, "representante_fornecedor");

						$info['representante_fornecedor_idestado_Nome'] = $dados_endereco['nome_estado'];
						$info['representante_fornecedor_idestado_NomeTemp'] = $dados_endereco['nome_estado'];

						$info['representante_fornecedor_idcidade_Nome'] = $dados_endereco['nome_cidade'];
						$info['representante_fornecedor_idcidade_NomeTemp'] = $dados_endereco['nome_cidade'];

						$info['representante_fornecedor_idbairro_Nome'] = $dados_endereco['nome_bairro'];
						$info['representante_fornecedor_idbairro_NomeTemp'] = $dados_endereco['nome_bairro'];
							
							
						if ( strlen($info['telefone_fornecedor']) == 10 ) {
							$info['littelefone_fornecedor'] = substr($info['telefone_fornecedor'],2,4) . "-" . substr($info['telefone_fornecedor'],6);
							$info['telefone_fornecedor_ddd'] = substr($info['telefone_fornecedor'],0,2);
						}
							
						if ( strlen($info['fax_fornecedor']) == 10 ) {
							$info['litfax_fornecedor'] = substr($info['fax_fornecedor'],2,4) . "-" . substr($info['fax_fornecedor'],6);
							$info['fax_fornecedor_ddd'] = substr($info['fax_fornecedor'],0,2);
						}
							
						if ( strlen($info['telefone_representante']) == 10 ) {
							$info['littelefone_representante'] = substr($info['telefone_representante'],2,4) . "-" . substr($info['telefone_representante'],6);
							$info['telefone_representante_ddd'] = substr($info['telefone_representante'],0,2);
						}
							
						if ( strlen($info['celular_representante']) == 10 ) {
							$info['litcelular_representante'] = substr($info['celular_representante'],2,4) . "-" . substr($info['celular_representante'],6);
							$info['celular_representante_ddd'] = substr($info['celular_representante'],0,2);
						}
							
							
							
						//obtém os erros
						$err = $fornecedor->err;
					}


					$list_ramo_atividade = $ramo_atividade->make_list_select();
					$smarty->assign("list_ramo_atividade",$list_ramo_atividade);

					$list_bairro_fornecedor = $bairro->make_list_select();
					$smarty->assign("list_bairro_fornecedor",$list_bairro_fornecedor);

					$list_estado_fornecedor = $estado->make_list_select();
					$smarty->assign("list_estado_fornecedor",$list_estado_fornecedor);

					$list_bairro_representante_fornecedor = $bairro->make_list_select();
					$smarty->assign("list_bairro_representante_fornecedor",$list_bairro_representante_fornecedor);

					$list_estado_representante_fornecedor = $estado->make_list_select();
					$smarty->assign("list_estado_representante_fornecedor",$list_estado_representante_fornecedor);





					//            Solução para o problema de caracteres especiais como " ' < >
					//						foreach ($info as $k=>$v) {
					//						$info[$k] = htmlspecialchars($v, ENT_QUOTES, 'UTF-8');}

					//passa os dados para o template
					$smarty->assign("info", $info);

				break;



					// deleta um registro do sistema
				case "excluir":

					//verifica se foi pedido a deleção
					if($_POST['for_chk']){

						// busca o codigo do endereço
						$info = $fornecedor->getById($_GET['idfornecedor']);

						// deleta o registro
						$fornecedor->delete($_GET['idfornecedor']);

						// deleta o endereço do banco de dados
						$endereco->delete($info['idendereco_fornecedor']);

						// deleta o endereço do banco de dados
						$endereco->delete($info['idendereco_representante_fornecedor']);

						//obtém erros
						$err = $fornecedor->err;

						//se não ocorreram erros
						if(count($err) == 0){
							$flags['sucesso'] = $conf['excluir'];



						}

						//limpa o $flags.action para que seja exibida a listagem
						$flags['action'] = "listar";

						//lista registros
						$list = $fornecedor->make_list(0, $conf['rppg']);

						//pega os erros
						$err = $fornecedor->err;

						//envia a listagem para o template
						$smarty->assign("list", $list);

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
// $smarty->default_modifiers = array('escape');
// $smarty->default_modifiers = array('escape:"html"');
//$smarty->default_modifiers = array('escape:"htmlall"');

$list_permissao = $auth->check_priv($conf['priv']);
$smarty->assign("list_permissao",$list_permissao);

if ($_GET['target'] == "full") $smarty->display("adm_relatorio_fornecedor.tpl");
else $smarty->display("adm_fornecedor.tpl");


?>

