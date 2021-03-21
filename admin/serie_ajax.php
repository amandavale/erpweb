<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");


  // inicializa templating
  $smarty = new Smarty;

  // ação selecionada
  $flags['action'] = $_GET['ac'];

  // inicializa autenticação
  $auth = new auth();


  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();
        

	/*
	Fun??o: Verifica_Campos_Serie_AJAX
	Verifica se os campos da serie da ECF
	*/
	function Verifica_Campos_Serie_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		//---------------------

		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		// se quiser usar a ECF, verifica se está no browser IE
		if ($_SESSION['browser_usuario'] == "1") {
			$form->err[] = "Para gerar gerar a Série, é necessário usar o navegador Internet Explorer!";
		}				


		$err = $form->err;


		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {
    	
			$objResponse->addScriptCall("RecuperaNumeroSerie");
			$objResponse->addScript("document.getElementById('for_ecf').submit();");			

		}
    // houve erros, logo mostra-os
    else {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
		}

		// retorna o resultado XML
    return $objResponse->getXML();
  }



?>
