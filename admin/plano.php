<?php

//inclus�o de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/rotinas.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");
require_once("../common/lib/xajax/xajax.inc.php");
require_once("../entidades/plano.php");
require_once("plano_ajax.php");

// configura��es anotionais
$conf['area'] = "Planos de Contas"; // �rea

//configura��o de estilo
$conf['style'] = "full";

// inicializa templating
$smarty = new Smarty;


// cria o objeto xajax
$xajax = new xajax();

// registra todas as fun��es que ser�o usadas
$xajax->registerFunction("Verifica_Campos_Planos_AJAX");

//Processa as func��es
$xajax->processRequests();



// inicializa templating
$smarty = new Smarty;

// configura diret�rios
$smarty->template_dir = "../common/tpl";
$smarty->compile_dir =   "../common/tpl_c";

// seta configura��es
$smarty->assign("conf", $conf);

// a��o selecionada
$flags['action'] = $_GET['ac'];
if ($flags['action'] == "") $flags['action'] = "listar";

// inicializa autentica��o
$auth = new auth();

// verifica requisi��o de logout
if($flags['action'] == "logout") {
	$auth->logout();
}
else {

	// inicializa vetor de erros
	$err = array();

	// verifica sess�o
	if(!$auth->check_user()) {
		// verifica requisi��o de login
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

	// conte�do
	if($auth->check_user()) {
		// verifica privil�gios
		if(!$auth->check_priv($conf['priv'])) {
			$err = $auth->err;
		}
		else {
			// libera conte�do
			$flags['okay'] = 1;

			//inicializa classe
			$plano = new plano();

			// inicializa banco de dados
			$db = new db();

			//incializa classe para valida��o de formul�rio
			$form = new form();

			switch($flags['action']) {

				// a��o: adicionar <<<<<<<<<<
				case "adicionar":

					if($_POST['for_chk']) {

						$form->chk_empty($_POST['numero'], 1, 'C�digo');
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


							//obt�m os erros que ocorreram no cadastro
							$err = $plano->err;


							//se n�o ocorreram erros
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

					//obt�m qual p�gina da listagem deseja exibir
					$pg = intval(trim($_GET['pg']));

					//se n�o foi passada a p�gina como par�metro, faz p�gina default igual � p�gina 0
					if(!$pg) $pg = 0;

					if($_GET['target'] == 'full') $conf['rppg'] = 9999;
						
					//lista os registros
					$list = $plano->make_list($pg, $conf['rppg']);

					//pega os erros
					$err = $plano->err;

					//passa a listagem para o template
					$smarty->assign("list", $list);

				break;

					// a��o: editar <<<<<<<<<<
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

							//obt�m erros
							$err = $plano->err;

							//se n�o ocorreram erros
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
							
							

						//tratamento das informa��es para fazer o UPDATE
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



						//obt�m os erros
						$err = $plano->err;
					}

					$list_plano = $plano->make_list_select();
					$smarty->assign("list_plano",$list_plano);


					//passa os dados para o template
					$smarty->assign("info", $info);

					break;

					// deleta um registro do sistema
				case "excluir":

					//verifica se foi pedido a dele��o
					if($_POST['for_chk']){

						// deleta o registro
						$plano->delete($_GET['idplano']);

						//obt�m erros
						$err = $plano->err;

						//se n�o ocorreram erros
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

