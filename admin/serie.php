<?php

  //inclus?o de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  require_once("../common/lib/xajax/xajax.inc.php");

	require_once("../entidades/serie.php");   

	require_once("serie_ajax.php");

  // configura??es anotionais
  $conf['area'] = "S?rie"; // ?rea


  //configura??o de estilo
  $conf['style'] = "full";

  // inicializa templating
  $smarty = new Smarty;

  // configura diret?rios
  $smarty->template_dir = "../common/tpl";
  $smarty->compile_dir =   "../common/tpl_c";

  // seta configura??es
  $smarty->assign("conf", $conf);

  // a??o selecionada
  $flags['action'] = $_GET['ac'];
  if ($flags['action'] == "") $flags['action'] = "adicionar";

  // inicializa autentica??o
  $auth = new auth();

  // cria o objeto xajax
	$xajax = new xajax();

	// registra todas as fun??es que ser?o usadas
	$xajax->registerFunction("Verifica_Campos_Serie_AJAX");

	// processa as fun??es
	$xajax->processRequests();


  // verifica requisi??o de logout
  if($flags['action'] == "logout") {
    $auth->logout();
  }
  else {

    // inicializa vetor de erros
    $err = array();

    // verifica sess?o
    if(!$auth->check_user()) {
      // verifica requisi??o de login
      if($_POST['usr_chk']) {
        // verifica login
        if(!$auth->login($_POST['usr_log'], $_POST['usr_sen'])) {
          $err = $auth->err;
        }
      }
      else {
        $err = $auth->err;
      }
    }

    // conte?do
    if($auth->check_user()) {
      // verifica privil?gios
      if(!$auth->check_priv($conf['priv'])) {
        $err = $auth->err;
      }
      else {
        // libera conte?do
        $flags['okay'] = 1;


				//inicializa classe
	  		$serie = new serie();

        // inicializa banco de dados
        $db = new db();

        //incializa classe para valida??o de formul?rio
        $form = new form();
        
				$list = $auth->check_priv($conf['priv']);
				$aux = $flags['action'];
				if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}
				
				
        switch($flags['action']) {


					//listagem dos registros
          case "adicionar":

            if($_POST['for_chk']) { 

							if ($_POST['serie_ecf'] != "") {

								$_POST['serie_crip_ecf'] = md5($_POST['serie_ecf']);
	
								$serie->set($_POST);
	
								//obt?m os erros que ocorreram no cadastro
								$err = $serie->err;
	
								$flags['sucesso'] = $conf["inserir"];

							}
							else {
								$err = "Falha de comunica??o com a Impressora. Verifique se ela est? ligada!";
							}


						}						


          break;
          
          

	      }
	      
      }
      
  	}
  	
    // seta erros
    $smarty->assign("err", $err);
    
	}



	// Forma Array de intru??es de preenchimento
	$intrucoes_preenchimento = array();
	if ($flags['action'] == "adicionar" || $flags['action'] == "editar" ) {
		$intrucoes_preenchimento[] = "Os campos em <span class=req>vermelho</span> s&atilde;o obrigat&oacute;rios.";
	}
	else if ($flags['action'] == "busca_generica" || $flags['action'] == "busca_parametrizada") {
		$intrucoes_preenchimento[] = "Preencha os campos para realizar a busca.";
	}

	// Formata a mensagem para ser exibida
	$flags['intrucoes_preenchimento'] = $form->FormataMensagemAjuda($intrucoes_preenchimento);

	
	$smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));

  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);
  
	$list_permissao = $auth->check_priv($conf['priv']);
	$smarty->assign("list_permissao",$list_permissao);

  $smarty->display("adm_serie.tpl");

?>