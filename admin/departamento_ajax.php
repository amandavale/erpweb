<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
	require_once("../entidades/departamento.php");

  // inicializa templating
  $smarty = new Smarty;

  // configura diretórios
  $smarty->template_dir = "../common/tpl";
  $smarty->compile_dir =   "../common/tpl_c";

  // seta configurações
  $smarty->assign("conf", $conf);

  // ação selecionada
  $flags['action'] = $_GET['ac'];


  // inicializa autenticação
  $auth = new auth();

	//inicializa classe
	$departamento = new departamento();

  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();
        
				

  switch($flags['action']) {


		// busca os estados de acordo com a busca
		case "busca_departamento":

			$departamento->Filtra_Departamento_AJAX($_GET['typing'], $_GET['campoID']);

		break;

  }
  

  	
  // seta erros
  $smarty->assign("err", $err);

  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);

?>
