<?php

	//inclus�o de bibliotecas
	require_once("../common/lib/conf.inc.php");
	require_once("../common/lib/db.inc.php");
	require_once("../common/lib/auth.inc.php");
	require_once("../common/lib/form.inc.php");
	require_once("../common/lib/Smarty/Smarty.class.php");

	require_once("../entidades/ocupacao.php");
	
	// inicializa templating
	$smarty = new Smarty;

	// a��o selecionada
	$flags['action'] = $_GET['ac'];

	// inicializa autentica��o
	$auth = new auth();

	//inicializa classe
	$ocupacao = new ocupacao();

	// inicializa banco de dados
	$db = new db();

	//incializa classe para valida��o de formul�rio
	$form = new form();




	function Verifica_Campos_Ocupacao_AJAX($post) {

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			global $err;

			global $ocupacao;
			//---------------------


			// cria o objeto xajaxResponse
	    	$objResponse = new xajaxResponse();


			if ($_GET['ac'] == "editar") {

				$form->chk_empty($post['idcliente'], 1, 'Cliente');
				$form->chk_empty($post['littipo'], 1, 'Tipo');
				$form->chk_IsDate($post['litdataInicial'], "Data Inicial");
				if ($post['litdataFinal'] != ''){
					$form->chk_IsDate($post['litdataFinal'], "Data Final");
					
				}

				$err = $form->err;

				if($post['idcliente'] != $_GET['idcliente']){
					if ($ocupacao->Verifica_Ocupacao_Duplicada($post['idcliente'],$_SESSION['idapartamento']) ){
						$err[] = 'J� existe um registro de ocupa��o deste cliente. Por favor, selecione outro.';
					}
				}
				  
			}
			else {

				$form->chk_empty($post['idcliente'], 1, 'Cliente');
				$form->chk_empty($post['tipo'], 1, 'Tipo');
				$form->chk_IsDate($post['dataInicial'], "Data Inicial");
				if ($post['dataFinal'] != '') $form->chk_IsDate($post['dataFinal'], "Data Final");

				$err = $form->err;

    			if ($ocupacao->Verifica_Ocupacao_Duplicada($post['idcliente'],$_SESSION['idapartamento'])){
				$err[] = 'J� existe um registro de ocupa��o deste cliente. Por favor, selecione outro.';
			}
				
			}

			//Verifica se o usu�rio selecionou um apartamento
			if (! $_SESSION['idapartamento']) $err[] = 'Voc� deve selecionar um apartamento';
			


			//Verifica se o usu�rio selecionou um condom�nio
			if (!$_SESSION['idcliente']){
				$err[] = 'Ainda n�o foi selecionado um condom�nio. Por favor, selecione um na op��o "<a href="'.$conf['addr'].'/admin/apartamento.php?ac=selecionar_condominio">Selecionar Condom�nio"';
			}
			
				
			// se nao houveram erros, da o submit no form
		    if(count($err) == 0) {
		    	$objResponse->addScript("document.getElementById('for_ocupacao').submit();");
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
