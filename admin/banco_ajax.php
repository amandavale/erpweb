<?php

  //inclus�o de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");

	require_once("../entidades/banco.php");


  // inicializa templating
  $smarty = new Smarty;

  // a��o selecionada
  $flags['action'] = $_GET['ac'];

  // inicializa autentica��o
  $auth = new auth();


  // libera conte�do
  $flags['okay'] = 1;

	//inicializa classe
	$banco = new banco();

  // inicializa banco de dados
  $db = new db();

  //incializa classe para valida��o de formul�rio
  $form = new form();
        
				

  switch($flags['action']) {


		// busca os bancos de acordo com a busca
		case "busca_banco":

			$banco->Filtra_Banco_AJAX($_GET['typing'], $_GET['campoID']);

		break;

	}


?>
