<?php

  //inclus�o de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");

	require_once("../entidades/filial.php");


  // inicializa templating
  $smarty = new Smarty;

  // a��o selecionada
  $flags['action'] = $_GET['ac'];

  // inicializa autentica��o
  $auth = new auth();


  // libera conte�do
  $flags['okay'] = 1;

	//inicializa classe
	$filial = new filial();

  // inicializa banco de dados
  $db = new db();

  //incializa classe para valida��o de formul�rio
  $form = new form();
        
				



	/*
	Fun��o: Verifica_Campos_Filial_AJAX
	Verifica se os campos da filial foram preenchidos
	*/
	function Verifica_Campos_Filial_AJAX ($post) {

		// vari�veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		
		global $filial;
		//---------------------


		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		if ($_GET['ac'] == "editar") {
			$form->chk_empty($post['litnome_filial'], 1, 'Nome da filial');
			$form->chk_cnpj($post['litcnpj_filial'], 1);

			$err = $form->err;

	    // verifica se o CNPJ do cliente est� duplicado
	    $post['litcnpj_filial'] = $form->FormataCNPJParaInserir($post['litcnpj_filial']);
	    if ($filial->Verifica_CNPJ_Duplicado($post['litcnpj_filial'], $_GET['idfilial'])) $err[] = "Este CNPJ j� existe e n�o pode ser duplicado!";
		}
		else {
			$form->chk_empty($post['nome_filial'], 1, 'Nome da filial');
			$form->chk_cnpj($post['cnpj_filial'], 1);

			$err = $form->err;

      // verifica se o CNPJ da filial est� duplicado
      $post['cnpj_filial'] = $form->FormataCNPJParaInserir($post['cnpj_filial']);
			if ($filial->Verifica_CNPJ_Duplicado($post['cnpj_filial'])) $err[] = "Este CNPJ j� existe e n�o pode ser duplicado!";
		}


		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {
    	$objResponse->addScript("document.getElementById('for_filial').submit();");
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
