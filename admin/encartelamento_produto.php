<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/encartelamento_produto.php");
	require_once("../entidades/encartelamento.php"); 
	require_once("../entidades/produto.php"); 
	
	

  // configurações anotionais
  $conf['area'] = "Encartelamento de Produtos"; // área


  //configuração de estilo
  $conf['style'] = "full";

  // inicializa templating
  $smarty = new Smarty;

  // configura diretórios
  $smarty->template_dir = "../common/tpl";
  $smarty->compile_dir =   "../common/tpl_c";

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
	  		$encartelamento_produto = new encartelamento_produto();
	  		
	  		$encartelamento = new encartelamento(); 
				$produto = new produto(); 
				
	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para validação de formulário
        $form = new form();
        
				$list = $auth->check_priv($conf['priv']);
				$aux = $flags['action'];
				if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}


        switch($flags['action']) {



          // ação: cadastrar <<<<<<<<<<
          case "cadastrar":

	        	//busca detalhes do produto
						$info_produto = $produto->getById($_GET['idproduto']);

						//passa os dados para o template
						$smarty->assign("info_produto", $info_produto);


						if($_POST['for_chk']) {

							$list_encartelamento = $encartelamento_produto->SelecionaEncartelamento($_POST);
							$smarty->assign("list_encartelamento",$list_encartelamento);
							
							$list_encartelamento_produto = $encartelamento_produto->chkEncartelamento($info_produto['idproduto']);
			 				$smarty->assign("list_encartelamento_produto",$list_encartelamento_produto);
			 				
							$err = $form->err;


						}
						else {

							//busca os encartelamentos
							$list_encartelamento = $encartelamento_produto->SelecionaEncartelamento("",$_GET['idproduto']);
							$smarty->assign("list_encartelamento",$list_encartelamento);
							echo $info_produto["idproduto"];
              $list_encartelamento_produto = $encartelamento_produto->chkEncartelamento($info_produto['idproduto']);
			 				$smarty->assign("list_encartelamento_produto", $list_encartelamento_produto);
              
						}



       		break;
       		
       		
       
          // ação: adicionar <<<<<<<<<<
          case "adicionar":

            if($_POST['for_chk']) {
            	
            	
            	
							$form->chk_empty($_POST['idencartelamento'], 1, 'Encartelamento'); 
							$form->chk_empty($_POST['idproduto'], 1, 'Produto'); 
							

              $err = $form->err;

              if(count($err) == 0) {

								
	              
	              
	              
								
								

	              
								//grava o registro no banco de dados
								$encartelamento_produto->set($_POST);


								//obtém os erros que ocorreram no cadastro
								$err = $encartelamento_produto->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['inserir'];

									//limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $encartelamento_produto->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $encartelamento_produto->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}
								
              }
              
            }
            
            $list_encartelamento = $encartelamento->make_list_select();
						$smarty->assign("list_encartelamento",$list_encartelamento);

						$list_produto = $produto->make_list_select();
						$smarty->assign("list_produto",$list_produto);

						

          break;


					//listagem dos registros
          case "listar":
          
					  //obtém qual página da listagem deseja exibir
					  $pg = intval(trim($_GET['pg']));

					  //se não foi passada a página como parâmetro, faz página default igual à página 0
					  if(!$pg) $pg = 0;

					  //lista os registros
						$list = $encartelamento_produto->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $encartelamento_produto->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          break;
          
          
          // ação: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idencartelamento'] = $_GET['idencartelamento']; 
							$info['idproduto'] = $_GET['idproduto']; 
							
							
							$form->chk_empty($_POST['numidencartelamento'], 1, 'Encartelamento'); 
							$form->chk_empty($_POST['numidproduto'], 1, 'Produto'); 
							
							
							$err = $form->err;

		          if(count($err) == 0) {

								
								
								
								
		          	
								


								$encartelamento_produto->update($_GET['idencartelamento'],$_GET['idproduto'], $_POST);
								
								

								//obtém erros
								$err = $encartelamento_produto->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['alterar'];

								  //limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $encartelamento_produto->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $encartelamento_produto->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}

							}

						}
						else {

							//busca detalhes
							$info = $encartelamento_produto->getById($_GET['idencartelamento'],$_GET['idproduto']);

							//tratamento das informações para fazer o UPDATE
							$info['numidencartelamento'] = $info['idencartelamento']; 
							$info['numidproduto'] = $info['idproduto']; 
							
							
							
							
							//obtém os erros
							$err = $encartelamento_produto->err;
						}

            $list_encartelamento = $encartelamento->make_list_select();
						$smarty->assign("list_encartelamento",$list_encartelamento);

						$list_produto = $produto->make_list_select();
						$smarty->assign("list_produto",$list_produto);

						
            
            
            

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a deleção
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$encartelamento_produto->delete($_GET['idencartelamento'],$_GET['idproduto']);

					  	//obtém erros
							$err = $encartelamento_produto->err;

							//se não ocorreram erros
							if(count($err) == 0){
								$flags['sucesso'] = $conf['excluir'];
								
								
								
							}

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista registros
							$list = $encartelamento_produto->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $encartelamento_produto->err;

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

  $list_permissao = $auth->check_priv($conf['priv']);
	$smarty->assign("list_permissao",$list_permissao);
  
  $smarty->display("adm_encartelamento_produto.tpl");
?>

