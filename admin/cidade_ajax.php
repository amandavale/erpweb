<?php

  //inclus�o de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
	require_once("../entidades/cidade.php");

  // inicializa templating
  $smarty = new Smarty;

  // configura diret�rios
  $smarty->template_dir = "../common/tpl";
  $smarty->compile_dir =   "../common/tpl_c";

  // seta configura��es
  $smarty->assign("conf", $conf);

  // a��o selecionada
  $flags['action'] = $_GET['ac'];


  // inicializa autentica��o
  $auth = new auth();

	//inicializa classe
	$cidade = new cidade();

  // inicializa banco de dados
  $db = new db();

  //incializa classe para valida��o de formul�rio
  $form = new form();
        
				

  switch($flags['action']) {


		// busca os estados de acordo com a busca
		case "busca_cidade":

			$cidade->Filtra_Cidade_AJAX($_GET['typing'], $_GET['idestado'], $_GET['campoID'], $_GET['mostraDetalhes']);

		break;

  }
  

  	
  // seta erros
  $smarty->assign("err", $err);

  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);

?>
