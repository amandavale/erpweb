<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/parametros.php");
	
	

  // configurações anotionais
  $conf['area'] = "Parâmetros"; // área
  //$conf['priv'] = array($conf['pri_adm'],$conf['pri_cliente']); // privilégios requeridos

  //configuração de estilo
  $conf['style'] = "full";

  // inicializa templating
  $smarty = new Smarty;

  // configura diretórios
  $smarty->template_dir = "../common/tpl";
  $smarty->compile_dir =   "../common/tpl_c";

  // seta configuraï¿½ï¿½es
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
      // verifica requisiçao de login
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

    // conteúdo
    if($auth->check_user()) {
      // verifica privilégios
      if(!$auth->check_priv($conf['priv'])) {
        $err = $auth->err;
      }
      else {
        // libera conteúdo
        $flags['okay'] = 1;

				//inicializa classe
	  		$parametros = new parametros();
	  		
	  		
	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para validação de formulários
        $form = new form();
        
				

        switch($flags['action']) {

          // ação: adicionar <<<<<<<<<<
          case "adicionar":
          
          	if(!$auth->check_priv($conf['pri_adm'])) {
       			$err = $auth->err;
       			$flags['okay'] = 0;	
      		}
      		else{

	            if($_POST['for_chk']) {
	            
					$form->chk_empty($_POST['nome_parametro'], 1, 'Nome do Parâmetro'); 
					$form->chk_empty($_POST['valor_parametro'], 1, 'Valor do Parâmetro'); 		
	
	             	$err = $form->err;
	
					if(count($err) == 0) {

	       				//grava o registro no banco de dados
						$parametros->set($_POST);
						
						//obtêm os erros que ocorreram no cadastro
						$err = $parametros->err;
						
						//se não ocorreram erros
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
          			
					  //obtêm qual página da listagem deseja exibir
					  $pg = intval(trim($_GET['pg']));

					  //se não foi passada a página como parâmetro, faz página default igual página 0
					  if(!$pg) $pg = 0;

					  //lista os registros
						$list = $parametros->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $parametros->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          break;
          
          
          // ação: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idparametros'] = $_GET['idparametros']; 
							
							
							$form->chk_empty($_POST['litnome_parametro'], 1, 'Nome do Parâmetro'); 
							$form->chk_empty($_POST['litvalor_parametro'], 1, 'Valor do Parâmetro'); 
							
							
							$err = $form->err;

		          if(count($err) == 0) {

								
								
								
								
		          	
								


								$parametros->update($_GET['idparametros'], $_POST);
								
								

								//obtêm erros
								$err = $parametros->err;

								//se não ocorreram erros
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

							//tratamento das informações para fazer o UPDATE
							$info['litdescricao_parametro'] = $info['descricao_parametro'];
							$info['litnome_parametro'] = $info['nome_parametro']; 
							$info['litvalor_parametro'] = $info['valor_parametro']; 
							
							
							
							
							//obtêm os erros
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

					  	//obtêm erros
							$err = $parametros->err;

							//se não ocorreram erros
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

