<?php

  //inclus�o de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/entrega_produto.php");
	require_once("../entidades/entrega.php"); 
	require_once("../entidades/produto.php"); 
	
	

  // configura��es anotionais
  $conf['area'] = "Entrega-Produto"; // �rea


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
	  		$entrega_produto = new entrega_produto();
	  		
	  		$entrega = new entrega(); 
				$produto = new produto(); 
				
	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para valida��o de formul�rio
        $form = new form();
        
				

        switch($flags['action']) {

          // a��o: adicionar <<<<<<<<<<
          case "adicionar":

            if($_POST['for_chk']) {
            	
            	
            	
							$form->chk_empty($_POST['identrega'], 1, 'Entrega'); 
							$form->chk_empty($_POST['idproduto'], 1, 'Produto'); 
							$form->chk_empty($_POST['qtd'], 1, 'Quantidade'); 
							

              $err = $form->err;

              if(count($err) == 0) {

								
	              
	              
	              
								
								

	              
								//grava o registro no banco de dados
								$entrega_produto->set($_POST);


								//obt�m os erros que ocorreram no cadastro
								$err = $entrega_produto->err;

								//se n�o ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['inserir'];

									//limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $entrega_produto->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $entrega_produto->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}
								
              }
              
            }
            
            $list_entrega = $entrega->make_list_select();
						$smarty->assign("list_entrega",$list_entrega);

						$list_produto = $produto->make_list_select();
						$smarty->assign("list_produto",$list_produto);

						

          break;


					//listagem dos registros
          case "listar":
          
					  //obt�m qual p�gina da listagem deseja exibir
					  $pg = intval(trim($_GET['pg']));

					  //se n�o foi passada a p�gina como par�metro, faz p�gina default igual � p�gina 0
					  if(!$pg) $pg = 0;

					  //lista os registros
						$list = $entrega_produto->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $entrega_produto->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          break;
          
          
          // a��o: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['identrega'] = $_GET['identrega']; 
							$info['idproduto'] = $_GET['idproduto']; 
							
							
							$form->chk_empty($_POST['numidentrega'], 1, 'Entrega'); 
							$form->chk_empty($_POST['numidproduto'], 1, 'Produto'); 
							$form->chk_empty($_POST['numqtd'], 1, 'Quantidade'); 
							
							
							$err = $form->err;

		          if(count($err) == 0) {

								
								
								
								
		          	
								


								$entrega_produto->update($_GET['identrega'],$_GET['idproduto'], $_POST);
								
								

								//obt�m erros
								$err = $entrega_produto->err;

								//se n�o ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['alterar'];

								  //limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $entrega_produto->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $entrega_produto->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}

							}

						}
						else {

							//busca detalhes
							$info = $entrega_produto->getById($_GET['identrega'],$_GET['idproduto']);

							//tratamento das informa��es para fazer o UPDATE
							$info['numidentrega'] = $info['identrega']; 
							$info['numidproduto'] = $info['idproduto']; 
							$info['numqtd'] = $info['qtd']; 
							
							
							
							
							//obt�m os erros
							$err = $entrega_produto->err;
						}

            $list_entrega = $entrega->make_list_select();
						$smarty->assign("list_entrega",$list_entrega);

						$list_produto = $produto->make_list_select();
						$smarty->assign("list_produto",$list_produto);

						
            
            
            

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a dele��o
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$entrega_produto->delete($_GET['identrega'],$_GET['idproduto']);

					  	//obt�m erros
							$err = $entrega_produto->err;

							//se n�o ocorreram erros
							if(count($err) == 0){
								$flags['sucesso'] = $conf['excluir'];
								
								
								
							}

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista registros
							$list = $entrega_produto->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $entrega_produto->err;

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

  $smarty->display("adm_entrega_produto.tpl");
?>

