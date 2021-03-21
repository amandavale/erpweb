<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");

	require_once("../entidades/conta_pagar.php");
	require_once("../entidades/funcionario.php");
	require_once("../entidades/cheque.php");


  // inicializa templating
  $smarty = new Smarty;

  // ação selecionada
  $flags['action'] = $_GET['ac'];

  // inicializa autenticação
  $auth = new auth();

	//inicializa classe
	$conta_pagar = new conta_pagar();
	$funcionario = new funcionario();
	$cheque = new cheque();

  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();
        

	/*
	Fun??o: Verifica_Campos_Conta_Pagar_AJAX
	Verifica se os campos do contas a pagar foram preenchidos
	*/
	function Verifica_Campos_Conta_Pagar_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $funcionario;
		//---------------------

		
		// cria o objeto xajaxResponse
    	$objResponse = new xajaxResponse();
	
		
		if ( empty($post['idplano_debito'])  && empty($post['idplano_credito']) ){
			$form->err[] = "As contas de Crédito e Débito não estão preenchidas. Informe pelo menos uma delas.";
		}

		
		if ($_GET['ac'] == "editar") {

			$form->chk_empty($post['numvalor_conta'], 1, 'Valor da conta (R$)'); 
			$form->chk_IsDate($post['litdata_vencimento'], "Data de vencimento"); 
			$form->chk_empty($post['litstatus_conta'], 1, 'Status da conta'); 
						
			if ($post['litstatus_conta'] == "P") {
				$form->chk_empty($post['numvalor_pago'], 1, 'Valor pago (R$)'); 
				$form->chk_IsDate($post['litdata_pagamento'], "Data do pagamento"); 
			}			

			if ($post['litsaiu_do_caixa'] == "1") {
				$form->chk_empty($post['numvalor_saiu_caixa'], 1, 'Valor que saiu do caixa (R$)'); 
			}

			$err = $form->err;

			//if ( ($post['numidconta_pagar_tipo'] == "") && ($post['litdescricao_conta'] == "") ) $err[] = "Selecione o Tipo da Conta a pagar ou então digite a descrição da conta!";
			if ( $post['litdescricao_conta'] == "" ) $err[] = "Selecione o Tipo da Conta a pagar ou então digite a descrição da conta!";

			// verifica se a senha do funcionario está correta
			$info_funcionario = $funcionario->getById($post['idUltFuncionario']);
			if ($info_funcionario['senha_funcionario'] != md5($post['senha_funcionario'])) $err[] = "A senha digitada está incorreta !";


		}
		else {
			$form->chk_empty($post['valor_conta'], 1, 'Valor da conta (R$)'); 
			$form->chk_IsDate($post['data_vencimento'], "Data de vencimento"); 
			$form->chk_empty($post['status_conta'], 1, 'Status da conta'); 
			
			if ($post['status_conta'] == "P") {
				$form->chk_empty($post['valor_pago'], 1, 'Valor pago (R$)'); 
				$form->chk_IsDate($post['data_pagamento'], "Data do pagamento"); 
			}

			if ($post['saiu_do_caixa'] == "1") {
				$form->chk_empty($post['valor_saiu_caixa'], 1, 'Valor que saiu do caixa (R$)'); 
			}

			$err = $form->err;

			//if ( ($post['idconta_pagar_tipo'] == "") && ($post['descricao_conta'] == "") ) $err[] = "Selecione o Tipo da Conta a pagar ou então digite a descrição da conta!";
			if ( $post['descricao_conta'] == "" )  $err[] = "Selecione o Tipo da Conta a pagar ou então digite a descrição da conta!";
			
			// verifica se a senha do funcionario está correta
			$info_funcionario = $funcionario->getById($post['idfuncionario']);
			if ($info_funcionario['senha_funcionario'] != md5($post['senha_funcionario'])) $err[] = "A senha digitada está incorreta !";
		}



		// se nao houveram erros, da o submit no form
    	if(count($err) == 0) {
    	
    	$objResponse->addScript("document.getElementById('for_conta_pagar').submit();");

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
