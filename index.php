<?php

  // inclusão de bibliotecas
  require_once("common/lib/conf.inc.php");
  require_once("common/lib/db.inc.php");
  require_once("common/lib/auth.inc.php");
  require_once("common/lib/form.inc.php");
  require_once("common/lib/Smarty/Smarty.class.php");

  // configurações adicionais



  $conf['area'] = "Login"; // área



  // inicializa templating
  $smarty = new Smarty;

  // configura diretórios
  $smarty->template_dir = "common/tpl";
  $smarty->compile_dir = "common/tpl_c";

  // seta configurações
  $smarty->assign("conf", $conf);

  // ação selecionada
  $flags['action'] = $_GET['ac'];
  if ($flags['action'] == "") $flags['action'] = "listar";

  // inicializa autenticação
  $auth = new auth();

  // verifica requisição de logout
  if($flags['action'] == "logout") {
    $auth->logout();
  }
  else {
    // inicializa vetor de erros
    $err = array();

    // verifica sessão
    if(!$auth->check_user()) {
      // verifica requisição de login
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

  // libera conteúdo
  $flags['okay'] = 1;







  // seta flags de conteúdo
  $smarty->assign("flags", $flags);

  // mostra output
  $smarty->display("pub_index.tpl");

?>
