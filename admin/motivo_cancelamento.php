<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/motivo_cancelamento.php");
	
	

  // configurações anotionais
  $conf['area'] = "Motivos de Cancelamento"; // área


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
	  		$motivo_cancelamento = new motivo_cancelamento();
	  		
	  		
	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para validação de formulário
        $form = new form();
        
				$list = $auth->check_priv($conf['priv']);
				$aux = $flags['action'];
				if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}


        switch($flags['action']) {

					// busca genérica
          case "busca_generica":

            if ( ($_POST['for_chk']) || ($_GET['rpp'] != "") ) {

            	$flags['fez_busca'] = 1;

							if ($_POST['for_chk']) {
								$flags['busca'] = $_POST['busca'];
								$flags['rpp'] = $_POST['rpp'];
							}
							else {
								$flags['busca'] = $_GET['busca'];
								$flags['rpp'] = $_GET['rpp'];
							}

							if ($_GET['target'] == "full") $flags['rpp'] = 9999999;


						  //obtém qual página da listagem deseja exibir
						  $pg = intval(trim($_GET['pg']));

						  //se não foi passada a página como parâmetro, faz página default igual à página 0
						  if(!$pg) $pg = 0;

						  //lista os registros
							$list = $motivo_cancelamento->Busca_Generica($pg, $flags['rpp'], $flags['busca'], "", "ac=busca_generica&busca=".$flags['busca']."&rpp=".$flags['rpp']);

							//pega os erros
							$err = $motivo_cancelamento->err;

							//passa a listagem para o template
							$smarty->assign("list", $list);

						}

						if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];

          break;

					// busca parametrizada
          case "busca_parametrizada":

            if ( ($_POST['for_chk']) || ($_GET['rpp'] != "") ) {

            	$flags['fez_busca'] = 1;

							if ($_POST['for_chk']) {
								$flags['descricao'] = $_POST['descricao'];
								$flags['tipo'] = $_POST['tipo'];
								$flags['rpp'] = $_POST['rpp'];
							}
							else {
								$flags['descricao'] = $_GET['descricao'];
								$flags['tipo'] = $_GET['tipo'];
								$flags['rpp'] = $_GET['rpp'];
							}

							$parametros_get = "&descricao=" . $flags['descricao'] . "&tipo=" . $flags['tipo'];


							$filtro_where = "";
							if ($flags['descricao'] != "") $filtro_where .= " UPPER(MOTC.descricao) LIKE UPPER('%" . $flags['descricao'] . "%') AND ";
							if ($flags['tipo'] != "") $filtro_where .= " UPPER(MOTC.tipo) = UPPER('" . $flags['tipo'] . "') AND ";
							$filtro_where = substr($filtro_where,0,strlen($filtro_where)-4);


							if ($_GET['target'] == "full") $flags['rpp'] = 9999999;


						  //obtém qual página da listagem deseja exibir
						  $pg = intval(trim($_GET['pg']));

						  //se não foi passada a página como parâmetro, faz página default igual à página 0
						  if(!$pg) $pg = 0;

						  //lista os registros
							$list = $motivo_cancelamento->Busca_Parametrizada($pg, $flags['rpp'], $filtro_where, "", "ac=busca_parametrizada$parametros_get&rpp=".$flags['rpp']);

							//pega os erros
							$err = $motivo_cancelamento->err;

							//passa a listagem para o template
							$smarty->assign("list", $list);

						}

						if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];

          break;


          // ação: adicionar <<<<<<<<<<
          case "adicionar":

            if($_POST['for_chk']) {
            	
            	
            	
							$form->chk_empty($_POST['descricao'], 1, 'Descrição'); 
							$form->chk_empty($_POST['tipo'], 1, 'Tipo'); 
							

              $err = $form->err;

              if(count($err) == 0) {

								
	              
	              
	              
								
								

	              
								//grava o registro no banco de dados
								$motivo_cancelamento->set($_POST);


								//obtém os erros que ocorreram no cadastro
								$err = $motivo_cancelamento->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['inserir'];

									//limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $motivo_cancelamento->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $motivo_cancelamento->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}
								
              }
              
            }
            
            

          break;


					//listagem dos registros
          case "listar":
          
					  //obtém qual página da listagem deseja exibir
					  $pg = intval(trim($_GET['pg']));

					  //se não foi passada a página como parâmetro, faz página default igual à página 0
					  if(!$pg) $pg = 0;

					  //lista os registros
						$list = $motivo_cancelamento->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $motivo_cancelamento->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          break;
          
          
          // ação: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idmotivo_cancelamento'] = $_GET['idmotivo_cancelamento']; 
							
							
							$form->chk_empty($_POST['litdescricao'], 1, 'Descrição'); 
							$form->chk_empty($_POST['littipo'], 1, 'Tipo'); 
							
							
							$err = $form->err;

		          if(count($err) == 0) {

								
								
								
								
		          	
								


								$motivo_cancelamento->update($_GET['idmotivo_cancelamento'], $_POST);
								
								

								//obtém erros
								$err = $motivo_cancelamento->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['alterar'];

								  //limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $motivo_cancelamento->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $motivo_cancelamento->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}

							}

						}
						else {

							//busca detalhes
							$info = $motivo_cancelamento->getById($_GET['idmotivo_cancelamento']);

							//tratamento das informações para fazer o UPDATE
							$info['litdescricao'] = $info['descricao']; 
							$info['littipo'] = $info['tipo']; 
							
							
							
							
							//obtém os erros
							$err = $motivo_cancelamento->err;
						}

            
            
            
            

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a deleção
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$motivo_cancelamento->delete($_GET['idmotivo_cancelamento']);

					  	//obtém erros
							$err = $motivo_cancelamento->err;

							//se não ocorreram erros
							if(count($err) == 0){
								$flags['sucesso'] = $conf['excluir'];
								
								
								
							}

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista registros
							$list = $motivo_cancelamento->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $motivo_cancelamento->err;

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
  
	if ($_GET['target'] == "full") $smarty->display("adm_relatorio_motivo_cancelamento.tpl");
  else $smarty->display("adm_motivo_cancelamento.tpl");
?>

