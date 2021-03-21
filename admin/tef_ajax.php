<?php

  //inclus�o de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");


  // inicializa templating
  $smarty = new Smarty;

  // a��o selecionada
  $flags['action'] = $_GET['ac'];

  // inicializa autentica��o
  $auth = new auth();


  // inicializa banco de dados
  $db = new db();

  //incializa classe para valida��o de formul�rio
  $form = new form();
        

	/*
	Fun??o: Verifica_Campos_Comando_TEF_AJAX
	Verifica se os campos para executar comando de TEF
	*/
	function Verifica_Campos_Comando_TEF_AJAX ($comando, $post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		//---------------------

		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		// se quiser usar TEF, verifica se est� no browser IE
		if ($_SESSION['browser_usuario'] == "1") {
			$form->err[] = "Para fazer uma opera��o de Administra��o TEF, � necess�rio usar o navegador Internet Explorer!";
		}				

		$form->chk_empty($post['tef_caminho'], 1, 'TEF');

		if ($comando == "CHQ") {
			$form->chk_moeda($post['valor_tef'], 0, "Valor");
		}

		$err = $form->err;


		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {

			// cria o n�mero da identifica��o	
			$identificacao = rand(1, 999999);
			$objResponse->addAssign("identificacao", "value", $identificacao);    	

			// formata o valor do tef
			$valor_tef = $post['valor_tef'];   	
			$valor_tef = str_replace(",","",$valor_tef);
			$valor_tef = str_replace(".","",$valor_tef);
			$objResponse->addAssign("valorTEF_bk", "value", $valor_tef); // valor do tef sem v�rgula nem ponto	

			// informa qual � o comando
			$objResponse->addAssign("comando", "value", $comando);    				

    		$objResponse->addScriptCall("Modulo_Adm_TEF");

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
