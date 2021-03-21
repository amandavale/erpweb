<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
	require_once("../entidades/transportador.php");

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
	$transportador = new transportador();

  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();
        
				

  switch($flags['action']) {


		// busca os transportadores de acordo com a busca
		case "busca_transportador":

			$transportador->Filtra_Transportador_AJAX($_GET['typing'], $_GET['campoID'], $_GET['mostraDetalhes']);

		break;

  }
  

	/*
	Fun??o: Verifica_Campos_Transportador_AJAX
	Verifica se os campos do transportador foram preenchidos
	*/
	function Verifica_Campos_Transportador_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $transportador;
		//---------------------

		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
		
		if ($_GET['ac'] == "editar") {		
			$form->chk_empty($post['littipo_transportador'], 1, 'Tipo do transportador'); 
			$form->chk_empty($post['litnome_transportador'], 1, 'Nome do transportador'); 

			$err = $form->err;
		}
		else {
			$form->chk_empty($post['tipo_transportador'], 1, 'Tipo do transportador'); 
			$form->chk_empty($post['nome_transportador'], 1, 'Nome do transportador'); 

			$err = $form->err;
		}



		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {
    	
    	$objResponse->addScript("document.getElementById('for_transportador').submit();");

		}
    // houve erros, logo mostra-os
    else {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
		}

		// retorna o resultado XML
    return $objResponse->getXML();
  }



  	
  // seta erros
  $smarty->assign("err", $err);

  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);

?>
