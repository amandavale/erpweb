<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/conta_funcionario.php");
	require_once("../entidades/funcionario.php"); 
	require_once("../entidades/banco.php"); 
	
	

  // configurações anotionais
  $conf['area'] = "Contas Bancárias do Funcionario"; // área


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
	  		$conta_funcionario = new conta_funcionario();
	  		
	  		$funcionario = new funcionario(); 
				$banco = new banco(); 
				
	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para validação de formulário
        $form = new form();
        
				$list = $auth->check_priv($conf['priv']);
				$aux = $flags['action'];
				if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}


        switch($flags['action']) {

          // ação: adicionar <<<<<<<<<<
          case "adicionar":

            if($_POST['for_chk']) {
            	
            	
            	
							$form->chk_empty($_POST['idfuncionario'], 1, 'Funcionario'); 
							$form->chk_empty($_POST['idbanco'], 1, 'Banco'); 
							$form->chk_empty($_POST['agencia_funcionario'], 1, 'Agência'); 
							$form->chk_empty($_POST['conta_funcionario'], 1, 'Conta'); 
							

              $err = $form->err;

              if(count($err) == 0) {

								
								//grava o registro no banco de dados
								$conta_funcionario->set($_POST);


								//obtém os erros que ocorreram no cadastro
								$err = $conta_funcionario->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['inserir'];

									//limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $conta_funcionario->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $conta_funcionario->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}
								
              }
              
            }
            
            $list_funcionario = $funcionario->make_list_select();
						$smarty->assign("list_funcionario",$list_funcionario);

						$list_banco = $banco->make_list_select();
						$smarty->assign("list_banco",$list_banco);

						

          break;


					//listagem dos registros
          case "listar":
          
					  //obtém qual página da listagem deseja exibir
					  $pg = intval(trim($_GET['pg']));

					  //se não foi passada a página como parâmetro, faz página default igual à página 0
					  if(!$pg) $pg = 0;

					  //lista os registros
						$list = $conta_funcionario->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $conta_funcionario->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          break;
          
          
          // ação: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idconta_funcionario'] = $_GET['idconta_funcionario']; 
							
							
							$form->chk_empty($_POST['numidfuncionario'], 1, 'Funcionário'); 
							$form->chk_empty($_POST['numidbanco'], 1, 'Banco'); 
							$form->chk_empty($_POST['litagencia_funcionario'], 1, 'Agência'); 
							$form->chk_empty($_POST['litconta_funcionario'], 1, 'Conta'); 
							
							
							$err = $form->err;

		          if(count($err) == 0) {

								
								
								
								
		          	
								


								$conta_funcionario->update($_GET['idconta_funcionario'], $_POST);
								
								

								//obtém erros
								$err = $conta_funcionario->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['alterar'];

								  //limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $conta_funcionario->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $conta_funcionario->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}

							}

						}
						else {

							//busca detalhes
							$info = $conta_funcionario->getById($_GET['idconta_funcionario']);

							//tratamento das informações para fazer o UPDATE
							$info['numidfuncionario'] = $info['idfuncionario']; 
							$info['numidbanco'] = $info['idbanco']; 
							$info['litagencia_funcionario'] = $info['agencia_funcionario']; 
							$info['litagencia_dig_funcionario'] = $info['agencia_dig_funcionario']; 
							$info['litconta_funcionario'] = $info['conta_funcionario']; 
							$info['litconta_dig_funcionario'] = $info['conta_dig_funcionario']; 
							$info['litprincipal_funcionario'] = $info['principal_funcionario']; 
							
							
							
							
							//obtém os erros
							$err = $conta_funcionario->err;
						}

            $list_funcionario = $funcionario->make_list_select();
						$smarty->assign("list_funcionario",$list_funcionario);

						$list_banco = $banco->make_list_select();
						$smarty->assign("list_banco",$list_banco);

						
            
            
            

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a deleção
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$conta_funcionario->delete($_GET['idconta_funcionario']);

					  	//obtém erros
							$err = $conta_funcionario->err;

							//se não ocorreram erros
							if(count($err) == 0){
								$flags['sucesso'] = $conf['excluir'];
								
								
								
							}

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista registros
							$list = $conta_funcionario->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $conta_funcionario->err;

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
  
  $smarty->display("adm_conta_funcionario.tpl");
?>

