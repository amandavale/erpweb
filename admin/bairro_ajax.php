<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
	require_once("../entidades/bairro.php");

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


  // libera conteúdo
  $flags['okay'] = 1;

  //inicializa classe
  $bairro = new bairro();

  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();
        
				

  switch($flags['action']) {


		// busca os estados de acordo com a busca
		case "busca_bairro":

			$bairro->Filtra_Bairro_AJAX($_GET['typing'], $_GET['idcidade'], $_GET['campoID'], $_GET['mostraDetalhes'],$_GET['inserirBairro']);

		break;

  }
  

  
  function Cadastro_Rapido_Bairro_AJAX($idcidade,$nome_bairro, $campoID) {
  
  	// variáveis globais
  	global $form, $conf, $db, $falha, $bairro;
  	//---------------------
  
  	// cria o objeto xajaxResponse
  	$objResponse = new xajaxResponse();
  	
  	$idbairro = $bairro->set(array(
	  				'idcidade'    => (int)$idcidade,
	  				'nome_bairro' => $db->escape(utf8_decode($nome_bairro))  	
	  			));
  
  	if($idbairro){
  		
  		$objResponse->addAlert(utf8_decode($conf['inserir']));
  	
  		$objResponse->addAssign($campoID, 'value', $idbairro);
  		$objResponse->addAssign($campoID."_Nome", 'value', utf8_decode($nome_bairro));
  		$objResponse->addAssign($campoID."_NomeTemp", 'value', utf8_decode($nome_bairro));
  		$objResponse->addAssign($campoID."_Flag", 'className', 'selecionou');
  	
  	}
  	else{
  		$objResponse->addAlert(utf8_decode($falha['inserir']));
  	}
  	
  	
  	// retorna o resultado XML
  	return $objResponse->getXML();
  
  }
  
  	
  // seta erros
  $smarty->assign("err", $err);

  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);

?>
