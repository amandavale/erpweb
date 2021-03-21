<?php

// inclus�o de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");

require_once("../entidades/filial.php");
require_once("../entidades/funcionario.php");
require_once("../entidades/cargo_programa.php");


// configura��es adicionais
$conf['area'] = "Balancete"; // �ea


// inicializa templating
$smarty = new Smarty;

// configura diret�rios
$smarty->template_dir = "../common/tpl";
$smarty->compile_dir = "../common/tpl_c";

// seta configura��es
$smarty->assign("conf", $conf);

// a��o selecionada
$flags['action'] = $_GET['ac'];
if ($flags['action'] == "") $flags['action'] = "listar";

// inicializa autentica��o
$auth = new auth();

// inicializa banco de dados
$db = new db();

//incializa classe para valida��o de formul�rio
$form = new form();

$filial = new filial();
$funcionario = new funcionario();
$cargo_programa = new cargo_programa();

$list_filial = $filial->make_list_select();
$smarty->assign("list_filial", $list_filial);






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
			//$err = $auth->err;
		}
		
	}
	
	// contedo
	if($auth->check_user()) {
		// verifica privil�ios
		
		// libera contedo
		$flags['okay'] = 1;
		
		// monta o menu do usu�rio
		if ($_SESSION['menu_usuario'] == "")
			$_SESSION['menu_usuario'] = $auth->Monta_Menu_Usuario($_SESSION['usr_cod']);
		
		
	}
	
	
	// seta erros
	$smarty->assign("err", $err);
}


// identificador para a opera��o de TEF
$flags['identificacao'] = rand(0,999999999);


// seta flags de contedo
$smarty->assign("flags", $flags);

$list_permissao = $auth->check_priv($conf['priv']);
$smarty->assign("list_permissao",$list_permissao);

// mostra output
$smarty->display("cond_balancete.tpl");

?>