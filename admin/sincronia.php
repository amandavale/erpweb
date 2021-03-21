<?php

  //inclus�o de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/sincronia.php");





  // configura��es anotionais
  $conf['area'] = "Sincronia com o Banco de Dados Central"; // �rea
  

  //configura��o de estilo
  $conf['style'] = "full";

  // inicializa templating
  $smarty = new Smarty;

  // configura diret�rios
  $smarty->template_dir = "../common/tpl";
  $smarty->compile_dir =   "../common/tpl_c";



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
        $err = $auth->err;
      }
    }

    // conte�do
    if($auth->check_user()) {
      // verifica privil�gios
      if(!$auth->check_priv($conf['priv'])) {
        $err = $auth->err;
      }
      else {

        // libera conte�do
        $flags['okay'] = 1;
        
        // inicializa banco de dados
         $db = new db();

        //incializa classe para valida��o de formul�rio
        $form = new form();


        $list = $auth->check_priv($conf['priv']);
        $aux = $flags['action'];
        if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}

       


        switch($flags['action']) {

         case "listar":       
                       
            
            

            if($_POST['for_chk']) {
            
             $sincronia = new sincronia();
            
              // Script para determinar o proxy                          
              // Define a context for HTTP. 
              
              if($conf["proxy"] == 1)
              {
                $aContext = array(
                    'http' => array(
                        'proxy' => $conf["endereco_proxy"], // This needs to be the server and the port of the NTLM Authentication Proxy Server.
                        'request_fulluri' => True,
                        ),
                    );
                    
                $cxContext = stream_context_create($aContext);
                
                
                // Now all file stream functions can use this context.
                
                $conteudo = file_get_contents($conf["endereco_servidor"], False, $cxContext);
              }
              else
              {
              
                $conteudo = file_get_contents($conf["endereco_servidor"]);
              
              }
              
              $aux = explode('\n',$conteudo);
              $ip = $aux[0];
              
              $status_slave = $sincronia->showSlaveStatus();
              $smarty->assign("status_slave", $status_slave);

              $status_master = $sincronia->showMasterStatus();
              $smarty->assign("status_master", $status_master);
              
             //var_dump($sincronia->showSlaveStatus()); 
                       
              //verifica a situa��o atual da sincroniza��o
              $aux1 = $db->query("stop slave");
              if($aux1) 
                $aux2 = $db->query("start slave");
                       
              $aux = $sincronia->stopSlave();
              
              if($aux == 1)
                {
                  $sincronia->changeMaster($ip);
                  $sincronia->query("reset slave");  
                  $sincronia->startSlave();
                }
              else
              {
                $err[] = "N�o foi possivel mudar o mestre.";
              }
      
             // $status = $db
             // while(){
              
              
              //} 
              
              
              break;
      
              

           }
        }

			}
		}
	}

  
  
  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);

  $list_permissao = $auth->check_priv($conf['priv']);
  $smarty->assign("list_permissao",$list_permissao);
  
  $smarty->display("adm_sincronia.tpl");


?>