<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
	require_once("../entidades/programa.php");

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
	$programa = new programa();

  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();
        
				

  switch($flags['action']) {


  }
  
  function Seleciona_SubModulo($post,$id=""){
  
  	global $programa;
  	global $smarty;
  	
  	$objResponse = new xajaxResponse();
  	
  	$list_submodulo = $programa->Seleciona_Submodulo($post['idmodulo']);
	
	$cont =0;
  	while(isset($list_submodulo[$cont]['idsubmodulo'])){		

  		if($list_submodulo[$cont]['idsubmodulo'] == $id) $aux = "selected"; else $aux = "";
		$aux2 = $aux2 . utf8_encode("<option value='".$list_submodulo[$cont]['idsubmodulo']."' $aux>" . $list_submodulo[$cont]['nome_submodulo']."</option>");    
  		
		$cont++;
  	}
    
    
    $objResponse->addAssign("idsubmodulo","innerHTML","$aux2");
    
  	// retorna o resultado XML
    return $objResponse->getXML();
  }

  	
  // seta erros
  $smarty->assign("err", $err);

  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);

?>
