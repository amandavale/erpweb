<?php

//inclusão de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/rotinas.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");
require_once("../common/lib/xajax/xajax.inc.php");
require_once("../entidades/plano.php");
require_once("plano_ajax.php");

// configurações anotionais
$conf['area'] = "Planos de Contas"; // área

//configuração de estilo
$conf['style'] = "full";

// inicializa templating
$smarty = new Smarty;


// cria o objeto xajax
$xajax = new xajax();

// registra todas as funções que serão usadas
$xajax->registerFunction("Verifica_Campos_Planos_AJAX");

//Processa as funcções
$xajax->processRequests();



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
			$plano = new plano();

			// inicializa banco de dados
			$db = new db();

			//incializa classe para validação de formulário
			$form = new form();

			switch($flags['action']) {

				// ação: adicionar <<<<<<<<<<
				case "adicionar":

					if($_POST['for_chk']) {

						$form->chk_empty($_POST['numero'], 1, 'Código');
						$form->chk_empty($_POST['nome'], 1, 'Nome');
						$form->chk_empty($_POST['tipo'], 1, 'Tipo');


						$err = $form->err;

						if(count($err) == 0) {

							$_POST['descricao'] = nl2br($_POST['descricao']);


							$plano_set['idplano_Nome'] = $_POST['idplano'];
							$plano_set['codHidden'] = $_POST['hid_pai_numero'] . $_POST['numero'];
							$plano_set['nome'] = $_POST['nome'];
							$plano_set['tipo'] = $_POST['tipo'];
							$plano_set['descricao'] = $_POST['descricao'];
							
							
							$plano->set($plano_set);


							//obtém os erros que ocorreram no cadastro
							$err = $plano->err;


							//se não ocorreram erros
							if(count($err) == 0) {
								$flags['sucesso'] = $conf['inserir'];

								//limpa o $flags.action para que seja exibida a listagem
								$flags['action'] = "listar";
									
								//lista
								$list = $plano->make_list(0, $conf['rppg']);
								
								//pega os erros
								$err = $plano->err;

								//envia a listagem para o template
								$smarty->assign("list", $list);

							}

						}

					}

					$list_plano = $plano->make_list_select();
					$smarty->assign("list_plano",$list_plano);


				break;


					//listagem dos registros
				case "listar":

					//obtém qual página da listagem deseja exibir
					$pg = intval(trim($_GET['pg']));

					//se não foi passada a página como parâmetro, faz página default igual à página 0
					if(!$pg) $pg = 0;

					if($_GET['target'] == 'full') $conf['rppg'] = 9999;
						
					//lista os registros
					$list = $plano->make_list($pg, $conf['rppg']);

					//pega os erros
					$err = $plano->err;

					//passa a listagem para o template
					$smarty->assign("list", $list);

				break;

					// ação: editar <<<<<<<<<<
				case "editar":

					if($_POST['for_chk']) {

						$info = $_POST;

						$info['idplano'] = $_GET['idplano'];


						if(count($err) == 0) {

							$_POST['litdescricao'] = nl2br($_POST['litdescricao']);

							if ($_POST['litnumero'] == "") $_POST['litnumero'] = "NULL";
							else $_POST['litnumero'] = $_POST['hid_pai_numero'].$_POST['litnumero'];

							$_POST['numidpai'] = $_POST['idplano'];

							$plano->update($_GET['idplano'], $_POST);

							//obtém erros
							$err = $plano->err;

							//se não ocorreram erros
							if(count($err) == 0) {
								$flags['sucesso'] = $conf['alterar'];

								//limpa o $flags.action para que seja exibida a listagem
								$flags['action'] = "listar";

								//lista
								$list = $plano->make_list(0, $conf['rppg']);

								//pega os erros
								$err = $plano->err;

								//envia a listagem para o template
								$smarty->assign("list", $list);

							}

						}

					}
					else {

						//busca detalhes
						$info = $plano->getById($_GET['idplano']);
						$info_pai = $plano->getById($info['idpai']);
							
							

						//tratamento das informações para fazer o UPDATE
						if ($info_pai > 0){

							$info['numidpai'] = $info['idpai'];
							$info['pai_nome'] = $info_pai['nome'];
							$info['pai_numero'] = $info_pai['numero'].'.';
							$info['pai_numero_js'] = $info_pai['numero'];
						}
							
							
							
						$explode_numeros = explode('.',$info['numero']);
						$info['litnumero'] = end($explode_numeros);
							
						$info['litnome'] = $info['nome'];
						$info['littipo'] = $info['tipo'];
						$info['litdescricao'] = strip_tags($info['descricao']);



						//obtém os erros
						$err = $plano->err;
					}

					$list_plano = $plano->make_list_select();
					$smarty->assign("list_plano",$list_plano);


					//passa os dados para o template
					$smarty->assign("info", $info);

					break;

					// deleta um registro do sistema
				case "excluir":

					//verifica se foi pedido a deleção
					if($_POST['for_chk']){

						// deleta o registro
						$plano->delete($_GET['idplano']);

						//obtém erros
						$err = $plano->err;

						//se não ocorreram erros
						if(count($err) == 0){
							$flags['sucesso'] = $conf['excluir'];

						}

						//limpa o $flags.action para que seja exibida a listagem
						$flags['action'] = "listar";

						//lista registros
						$list = $plano->make_list(0, $conf['rppg']);

						//pega os erros
						$err = $plano->err;

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
$smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));
$smarty->assign("form", $form);
$smarty->assign("flags", $flags);


$arquivoTpl =  $_GET['target'] == "full" ? "adm_relatorio_plano.tpl" : "adm_plano.tpl" ;
$smarty->display($arquivoTpl);


?>

