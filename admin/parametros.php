<?php

  //inclus�o de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/parametros.php");
	
	

  // configura��es anotionais
  $conf['area'] = "Par�metros"; // �rea
  //$conf['priv'] = array($conf['pri_adm'],$conf['pri_cliente']); // privil�gios requeridos

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
      // verifica requisi�ao de login
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
	  		$parametros = new parametros();
	  		
	  		
	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para valida��o de formul�rios
        $form = new form();
        
				

        switch($flags['action']) {

          // a��o: adicionar <<<<<<<<<<
          case "adicionar":
          
          	if(!$auth->check_priv($conf['pri_adm'])) {
       			$err = $auth->err;
       			$flags['okay'] = 0;	
      		}
      		else{

	            if($_POST['for_chk']) {
	            
					$form->chk_empty($_POST['nome_parametro'], 1, 'Nome do Par�metro'); 
					$form->chk_empty($_POST['valor_parametro'], 1, 'Valor do Par�metro'); 		
	
	             	$err = $form->err;
	
					if(count($err) == 0) {

	       				//grava o registro no banco de dados
						$parametros->set($_POST);
						
						//obt�m os erros que ocorreram no cadastro
						$err = $parametros->err;
						
						//se n�o ocorreram erros
						if(count($err) == 0) {
							$flags['sucesso'] = $conf['inserir'];

						//limpa o $flags.action para que seja exibida a listagem
						$flags['action'] = "listar";

						//lista
						$list = $parametros->make_list(0, $conf['rppg']);

						//pega os erros
						$err = $parametros->err;
						
						//envia a listagem para o template
						$smarty->assign("list", $list);
					}
				}
			}
	     } 

          break;


					//listagem dos registros
          case "listar":    			
          			
					  //obt�m qual p�gina da listagem deseja exibir
					  $pg = intval(trim($_GET['pg']));

					  //se n�o foi passada a p�gina como par�metro, faz p�gina default igual p�gina 0
					  if(!$pg) $pg = 0;

					  //lista os registros
						$list = $parametros->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $parametros->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          break;
          
          
          // a��o: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idparametros'] = $_GET['idparametros']; 
							
							
							$form->chk_empty($_POST['litnome_parametro'], 1, 'Nome do Par�metro'); 
							$form->chk_empty($_POST['litvalor_parametro'], 1, 'Valor do Par�metro'); 
							
							
							$err = $form->err;

		          if(count($err) == 0) {

								
								
								
								
		          	
								


								$parametros->update($_GET['idparametros'], $_POST);
								
								

								//obt�m erros
								$err = $parametros->err;

								//se n�o ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['alterar'];

								  //limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $parametros->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $parametros->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}

							}

						}
						else {

							//busca detalhes
							$info = $parametros->getById($_GET['idparametros']);

							//tratamento das informa��es para fazer o UPDATE
							$info['litdescricao_parametro'] = $info['descricao_parametro'];
							$info['litnome_parametro'] = $info['nome_parametro']; 
							$info['litvalor_parametro'] = $info['valor_parametro']; 
							
							
							
							
							//obt�m os erros
							$err = $parametros->err;
						}

            
            
            
            

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a deletado
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$parametros->delete($_GET['idparametros']);

					  	//obt�m erros
							$err = $parametros->err;

							//se n�o ocorreram erros
							if(count($err) == 0){
								$flags['sucesso'] = $conf['excluir'];
								
								
								
							}

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista registros
							$list = $parametros->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $parametros->err;

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

  $smarty->display("adm_parametros.tpl");
?>

