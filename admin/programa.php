<?php

//inclusão de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/rotinas.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");
require_once("../common/lib/xajax/xajax.inc.php");

require_once("../entidades/programa.php");
require_once("../entidades/submodulo.php");
require_once("../entidades/modulo.php");

require_once("programa_ajax.php");



// configurações anotionais
$conf['area'] = "Programas"; // área


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


// cria o objeto xajax
$xajax = new xajax();


// registra todas as funções que serão usadas
$xajax->registerFunction("Seleciona_Submodulo");

// processa as funções
$xajax->processRequests();

// inicializa autenticação
$auth = new auth();

//incializa classe para validação de formulário
$form = new form();

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
			$programa = new programa();
			 
			$submodulo = new submodulo();
			 
			$modulo = new modulo();


			// inicializa banco de dados
			$db = new db();


			$list = $auth->check_priv($conf['priv']);
			$aux = $flags['action'];
			if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}



			switch($flags['action']) {

				// ação: adicionar <<<<<<<<<<
				case "adicionar":

					if($_POST['for_chk']) {
						 


						$form->chk_empty($_POST['idsubmodulo'], 1, 'Sub-Módulo'); 
						$form->chk_empty($_POST['nome_programa'], 1, 'Nome do programa');
						$form->chk_empty($_POST['descricao_programa'], 1, 'Descrição do programa'); 
						$form->chk_empty($_POST['nome_arquivo'], 1, 'Nome do arquivo (sem .PHP)');
						$form->chk_empty($_POST['define_adicionar'], 1, 'Define o Adicionar ?');
						$form->chk_empty($_POST['define_editar'], 1, 'Define o Editar ?');
						$form->chk_empty($_POST['define_excluir'], 1, 'Define o Excluir ?');
						$form->chk_empty($_POST['define_listar'], 1, 'Define o Listar ?');
						$form->chk_empty($_POST['ordem_programa'], 1, 'Ordem do programa');
							

						$err = $form->err;

						if(count($err) == 0) {


	       
	       
	       

							if ($_POST['ordem_programa'] == "") $_POST['ordem_programa'] = "NULL";


	       
							//grava o registro no banco de dados
							$programa->set($_POST);


							//obtém os erros que ocorreram no cadastro
							$err = $programa->err;

							//se não ocorreram erros
							if(count($err) == 0) {
								$flags['sucesso'] = $conf['inserir'];

								//limpa o $flags.action para que seja exibida a listagem
								$flags['action'] = "listar";

								//lista
								$list = $programa->make_list(0, $conf['rppg']);

								//pega os erros
								$err = $programa->err;

								//envia a listagem para o template
								$smarty->assign("list", $list);

							}

						}

					}

					$list_modulo = $modulo->make_list_select();
					$smarty->assign("list_modulo",$list_modulo);



				break;


					//listagem dos registros
				case "listar":

					//obtém qual página da listagem deseja exibir
					$pg = intval(trim($_GET['pg']));

					//se não foi passada a página como parâmetro, faz página default igual à página 0
					if(!$pg) $pg = 0;

					//lista os registros
					$list = $programa->make_list($pg, $conf['rppg']);

					//pega os erros
					$err = $programa->err;

					//passa a listagem para o template
					$smarty->assign("list", $list);

					break;


					// ação: editar <<<<<<<<<<
				case "editar":

					if($_POST['for_chk']) {
							
						$_POST['numidsubmodulo'] = $_POST['idsubmodulo'];

						$info = $_POST;
							
						$info['idprograma'] = $_GET['idprograma'];
							
							
						$form->chk_empty($_POST['numidsubmodulo'], 1, 'Sub-Módulo'); 
						$form->chk_empty($_POST['litnome_programa'], 1, 'Nome do programa');
						$form->chk_empty($_POST['litdescricao_programa'], 1, 'Descrição do programa'); 
						$form->chk_empty($_POST['litnome_arquivo'], 1, 'Nome do arquivo (sem .PHP)');
						$form->chk_empty($_POST['litdefine_adicionar'], 1, 'Define o Adicionar ?');
						$form->chk_empty($_POST['litdefine_editar'], 1, 'Define o Editar ?');
						$form->chk_empty($_POST['litdefine_excluir'], 1, 'Define o Excluir ?');
						$form->chk_empty($_POST['litdefine_listar'], 1, 'Define o Listar ?');
						$form->chk_empty($_POST['numordem_programa'], 1, 'Ordem do programa');
							
							
						$err = $form->err;

						if(count($err) == 0) {

							 
							if ($_POST['numordem_programa'] == "") $_POST['numordem_programa'] = "NULL";



							$programa->update($_GET['idprograma'], $_POST);



							//obtém erros
							$err = $programa->err;

							//se não ocorreram erros
							if(count($err) == 0) {
								$flags['sucesso'] = $conf['alterar'];

								//limpa o $flags.action para que seja exibida a listagem
								$flags['action'] = "listar";

								//lista
								$list = $programa->make_list(0, $conf['rppg']);

								//pega os erros
								$err = $programa->err;

								//envia a listagem para o template
								$smarty->assign("list", $list);

							}

						}

					}
					else {

						//busca detalhes
						$info = $programa->getById($_GET['idprograma']);

						//tratamento das informações para fazer o UPDATE
						$info['numidsubmodulo'] = $info['idsubmodulo'];
						$info['litnome_programa'] = $info['nome_programa'];
						$info['litdescricao_programa'] = $info['descricao_programa'];
						$info['litnome_arquivo'] = $info['nome_arquivo'];
						$info['litparametros_arquivo'] = $info['parametros_arquivo'];
						$info['litdefine_adicionar'] = $info['define_adicionar'];
						$info['litdefine_editar'] = $info['define_editar'];
						$info['litdefine_excluir'] = $info['define_excluir'];
						$info['litdefine_listar'] = $info['define_listar'];
						$info['numordem_programa'] = $info['ordem_programa'];
							
							
							
							
						//obtém os erros
						$err = $programa->err;
					}

					$list_modulo = $modulo->make_list_select();
					$smarty->assign("list_modulo",$list_modulo);






					//passa os dados para o template
					$smarty->assign("info", $info);

				break;



					// deleta um registro do sistema
				case "excluir":

					//verifica se foi pedido a deleção
					if($_POST['for_chk']){

						// deleta o registro
						$programa->delete($_GET['idprograma']);

						//obtém erros
						$err = $programa->err;

						//se não ocorreram erros
						if(count($err) == 0){
							$flags['sucesso'] = $conf['excluir'];



						}

						//limpa o $flags.action para que seja exibida a listagem
						$flags['action'] = "listar";

						//lista registros
						$list = $programa->make_list(0, $conf['rppg']);

						//pega os erros
						$err = $programa->err;

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


// Formata a mensagem para ser exibida
$flags['intrucoes_preenchimento'] = $form->FormataMensagemAjuda($intrucoes_preenchimento);


$smarty->assign("form", $form);
$smarty->assign("flags", $flags);

$smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));


$list_permissao = $auth->check_priv($conf['priv']);
$smarty->assign("list_permissao",$list_permissao);

$smarty->display("adm_programa.tpl");
?>

