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
	Fun??o: Verifica_Campos_Comando_ECF_AJAX
	Verifica se os campos para executar comando na ECF
	*/
	function Verifica_Campos_Comando_ECF_AJAX ($post) {

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
			$form->err[] = "Para executar um comando na ECF, é necessário usar o navegador Internet Explorer!";
		}				

		if ($post['serie_ecf'] == "") {
			$form->err[] = "Para executar um comando na ECF, use o computador ligado na impressora de ECF!";
		}

		// Se for LMF por data, verifica os campos
		if ($_GET['comando'] == "lmf_data") {
			$form->chk_IsDate($post['data_vencimento_de'], 'Data Inicial');
			$form->chk_IsDate($post['data_vencimento_ate'], 'Data Final');
			if ($form->data1_maior($post['data_vencimento_de'], $post['data_vencimento_ate'])) $form->err[] = "A data inicial tem que ser menor que a data final !";
		}
		// Se for LMF por reducao, verifica os campos
		else if ($_GET['comando'] == "lmf_reducao") {
			$form->chk_empty($post['reducao_inicial'], 1, 'Redução Inicial');
			$form->chk_empty($post['reducao_final'], 1, 'Redução Final');
		}
    else if ($_GET['comando'] == "suprimento") {
      $form->chk_moeda($post['valor_suprimento'], 0, 'Valor do Registro de Suprimento de Caixa');
    }  
    else if ($_GET['comando'] == "sangria") {
      $form->chk_moeda($post['valor_sangria'], 0, 'Valor do Registro de Retirada de Caixa');
    } 

		$err = $form->err;


		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {
    	
			if ($_GET['comando'] == "sintegra") {
    		$objResponse->addScriptCall("Gera_Relatorio_Sintegra");
			}
			else if ($_GET['comando'] == "leiturax") {
    		$objResponse->addScriptCall("Gera_LeituraX");
			}
			else if ($_GET['comando'] == "lmf_data") {
    		$objResponse->addScriptCall("Gera_LMF_Data");
			}
			else if ($_GET['comando'] == "lmf_reducao") {
    		$objResponse->addScriptCall("Gera_LMF_Reducao");
			}
      else if ($_GET['comando'] == "suprimento") {
        $objResponse->addScriptCall("Suprimento");
      }   
      else if ($_GET['comando'] == "sangria") {
        $objResponse->addScriptCall("Sangria");
      }   
      else if ($_GET['comando'] == "reducao_z") {
        $objResponse->addScriptCall("ReducaoZ");
      } 


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
