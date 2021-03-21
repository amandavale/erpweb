<?php

  // inclus�o de bibliotecas
  require_once("common/lib/conf.inc.php");
  require_once("common/lib/db.inc.php");
  require_once("common/lib/auth.inc.php");
  require_once("common/lib/form.inc.php");
  require_once("common/lib/Smarty/Smarty.class.php");

  // configura��es adicionais



  $conf['area'] = "Login"; // �rea



  // inicializa templating
  $smarty = new Smarty;

  // configura diret�rios
  $smarty->template_dir = "common/tpl";
  $smarty->compile_dir = "common/tpl_c";

  // seta configura��es
  $smarty->assign("conf", $conf);

  // a��o selecionada
  $flags['action'] = $_GET['ac'];
  if ($flags['action'] == "") $flags['action'] = "listar";

  // inicializa autentica��o
  $auth = new auth();

  // verifica requisi��o de logout
  if($flags['action'] == "logout") {
    $auth->logout();
  }
  else {
    // inicializa vetor de erros
    $err = array();

    // verifica sess�o
    if(!$auth->check_user()) {
      // verifica requisi��o de login
      if($_POST['usr_chk']) {
        // verifica login
        if(!$auth->login($_POST['usr_log'], $_POST['usr_sen'])) {
          $err = $auth->err;
        }
      }
      else {
				//$err = $auth->err;
			}

    }

    // seta erros
    $smarty->assign("err", $err);
  }

  // libera conte�do
  $flags['okay'] = 1;







  // seta flags de conte�do
  $smarty->assign("flags", $flags);

  // mostra output
  $smarty->display("pub_index.tpl");

?>
