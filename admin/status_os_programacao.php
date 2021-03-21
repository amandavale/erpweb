<?php

  //inclus�o de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/status_os_programacao.php");
	require_once("../entidades/status_os.php"); 
	require_once("../entidades/programacao_status.php"); 
	
	

  // configura��es anotionais
  $conf['area'] = "Status e Programa��o da OS"; // �rea
  

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

				//inicializa classe
	  		$status_os_programacao = new status_os_programacao();
	  		
	  		$status_os = new status_os(); 
				$programacao_status = new programacao_status(); 
				
	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para valida��o de formul�rio
        $form = new form();
        
				

        switch($flags['action']) {

          // a��o: adicionar <<<<<<<<<<
          case "adicionar":

            if($_POST['for_chk']) {
            	
            	
            	
							$form->chk_empty($_POST['idstatus_os'], 1, 'Status'); 
							

              $err = $form->err;

              if(count($err) == 0) {

								
	              
	              
	              
								
								

	              
								//grava o registro no banco de dados
								$status_os_programacao->set($_POST);


								//obt�m os erros que ocorreram no cadastro
								$err = $status_os_programacao->err;

								//se n�o ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['inserir'];

									//limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $status_os_programacao->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $status_os_programacao->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}
								
              }
              
            }
            
            $list_status_os = $status_os->make_list_select();
						$smarty->assign("list_status_os",$list_status_os);

						$list_programacao_status = $programacao_status->make_list_select();
						$smarty->assign("list_programacao_status",$list_programacao_status);

						

          break;


					//listagem dos registros
          case "listar":
          
					  //obt�m qual p�gina da listagem deseja exibir
					  $pg = intval(trim($_GET['pg']));

					  //se n�o foi passada a p�gina como par�metro, faz p�gina default igual � p�gina 0
					  if(!$pg) $pg = 0;

					  //lista os registros
						$list = $status_os_programacao->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $status_os_programacao->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          break;
          
          
          // a��o: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idstatus_os_programacao'] = $_GET['idstatus_os_programacao']; 
							
							
							$form->chk_empty($_POST['numidstatus_os'], 1, 'Status'); 
							
							
							$err = $form->err;

		          if(count($err) == 0) {

								
								
								
								
		          	
								


								$status_os_programacao->update($_GET['idstatus_os_programacao'], $_POST);
								
								

								//obt�m erros
								$err = $status_os_programacao->err;

								//se n�o ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['alterar'];

								  //limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $status_os_programacao->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $status_os_programacao->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}

							}

						}
						else {

							//busca detalhes
							$info = $status_os_programacao->getById($_GET['idstatus_os_programacao']);

							//tratamento das informa��es para fazer o UPDATE
							$info['numidstatus_os'] = $info['idstatus_os']; 
							$info['numidprogramacao_status'] = $info['idprogramacao_status']; 
							
							
							
							
							//obt�m os erros
							$err = $status_os_programacao->err;
						}

            $list_status_os = $status_os->make_list_select();
						$smarty->assign("list_status_os",$list_status_os);

						$list_programacao_status = $programacao_status->make_list_select();
						$smarty->assign("list_programacao_status",$list_programacao_status);

						
            
            
            

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a dele��o
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$status_os_programacao->delete($_GET['idstatus_os_programacao']);

					  	//obt�m erros
							$err = $status_os_programacao->err;

							//se n�o ocorreram erros
							if(count($err) == 0){
								$flags['sucesso'] = $conf['excluir'];
								
								
								
							}

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista registros
							$list = $status_os_programacao->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $status_os_programacao->err;

							//envia a listagem para o template
							$smarty->assign("list", $list);

						}

	        break;

          
          
          
          

	      }
	      
      }
      
  	}
  	
    // seta erros
    $smarty->assign("err", $err);
    
	}

  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);

  $smarty->display("adm_status_os_programacao.tpl");
?>

