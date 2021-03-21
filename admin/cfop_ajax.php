<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
	require_once("../entidades/cfop.php");


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
	$cfop = new cfop();


  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();


	/*
	Função: Busca_Descricao_CFOP_AJAX
 	Busca a Descrição do CFOP
	*/
	function Busca_Descricao_CFOP_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;

		global $cfop;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		// busca o CFOP
		$info_cfop = $cfop->getByCodigo($post['codigo_cfop']);
		

		// se houveram erros, mostra-os
    if($info_cfop['descricao'] == "") {
			$mensagem = "O código do CFOP está inválido !";
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
			$objResponse->addAssign("descricao_cfop", "innerHTML", "");
    }
    // se nao houveram erros, mostra a descrição
		else {

			$objResponse->addAssign("descricao_cfop", "innerHTML", utf8_encode($info_cfop['descricao']));

		}			


		// retorna o resultado XML
    return $objResponse->getXML();

	}




?>
