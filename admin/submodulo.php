<?php

  //inclus?o de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/submodulo.php");
	require_once("../entidades/modulo.php"); 
	
	

  // configura??es anotionais
  $conf['area'] = "Sub-M?dulos"; // ?rea


  //configura??o de estilo
  $conf['style'] = "full";

  // inicializa templating
  $smarty = new Smarty;

  // configura diret?rios
  $smarty->template_dir = "../common/tpl";
  $smarty->compile_dir =   "../common/tpl_c";

  // seta configura??es
  $smarty->assign("conf", $conf);

  // a??o selecionada
  $flags['action'] = $_GET['ac'];
  if ($flags['action'] == "") $flags['action'] = "listar";

  // inicializa autentica??o
  $auth = new auth();
  
  //incializa classe para valida??o de formul?rio
  $form = new form();

  // verifica requisi??o de logout
  if($flags['action'] == "logout") {
    $auth->logout();
  }
  else {

    // inicializa vetor de erros
    $err = array();

    // verifica sess?o
    if(!$auth->check_user()) {
      // verifica requisi??o de login
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

    // conte?do
    if($auth->check_user()) {
      // verifica privil?gios
      if(!$auth->check_priv($conf['priv'])) {
        $err = $auth->err;
      }
      else {
        // libera conte?do
        $flags['okay'] = 1;

				//inicializa classe
	  		$submodulo = new submodulo();
	  		
	  		$modulo = new modulo(); 
				
	  											
        // inicializa banco de dados
        $db = new db();


        $list = $auth->check_priv($conf['priv']);
				$aux = $flags['action'];
				if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}

				

        switch($flags['action']) {

          // a??o: adicionar <<<<<<<<<<
          case "adicionar":

            if($_POST['for_chk']) {
            	
            	
            	
							$form->chk_empty($_POST['idmodulo'], 1, 'M?dulo'); 
							$form->chk_empty($_POST['nome_submodulo'], 1, 'Nome do Sub-M?dulo'); 
							$form->chk_empty($_POST['descricao_submodulo'], 1, 'Descri??o  do Sub-M?dulo'); 
							$form->chk_empty($_POST['submodulo_final'], 1, 'Sub-M?dulo Final ?'); 
							$form->chk_empty($_POST['ordem_submodulo'], 1, 'Ordem do Sub-M?dulo'); 
							$form->chk_empty($_POST['largura_menu_programa'], 1, 'Largura do Menu Programa'); 
							

              $err = $form->err;

              if(count($err) == 0) {

								
	              
	              
	              
								
								if ($_POST['ordem_submodulo'] == "") $_POST['ordem_submodulo'] = "NULL"; 
								if ($_POST['largura_menu_programa'] == "") $_POST['largura_menu_programa'] = "NULL"; 
								

	              
								//grava o registro no banco de dados
								$submodulo->set($_POST);


								//obt?m os erros que ocorreram no cadastro
								$err = $submodulo->err;

								//se n?o ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['inserir'];

									//limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $submodulo->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $submodulo->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}
								
              }
              
            }
            
            $list_modulo = $modulo->make_list_select();
						$smarty->assign("list_modulo",$list_modulo);

						

          break;


					//listagem dos registros
          case "listar":
          
					  //obt?m qual p?gina da listagem deseja exibir
					  $pg = intval(trim($_GET['pg']));

					  //se n?o foi passada a p?gina como par?metro, faz p?gina default igual ? p?gina 0
					  if(!$pg) $pg = 0;

					  //lista os registros
						$list = $submodulo->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $submodulo->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          break;
          
          
          // a??o: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idsubmodulo'] = $_GET['idsubmodulo']; 
							
							
							$form->chk_empty($_POST['numidmodulo'], 1, 'M?dulo'); 
							$form->chk_empty($_POST['litnome_submodulo'], 1, 'Nome do Sub-M?dulo'); 
							$form->chk_empty($_POST['litdescricao_submodulo'], 1, 'Descri??o  do Sub-M?dulo'); 
							$form->chk_empty($_POST['litsubmodulo_final'], 1, 'Sub-M?dulo Final ?'); 
							$form->chk_empty($_POST['numordem_submodulo'], 1, 'Ordem do Sub-M?dulo'); 
							$form->chk_empty($_POST['numlargura_menu_programa'], 1, 'Largura do Menu Programa'); 
							
							
							$err = $form->err;

		          if(count($err) == 0) {

								
								
								
								
		          	
								if ($_POST['numordem_submodulo'] == "") $_POST['numordem_submodulo'] = "NULL"; 
								if ($_POST['numlargura_menu_programa'] == "") $_POST['numlargura_menu_programa'] = "NULL"; 
								


								$submodulo->update($_GET['idsubmodulo'], $_POST);
								
								

								//obt?m erros
								$err = $submodulo->err;

								//se n?o ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['alterar'];

								  //limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $submodulo->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $submodulo->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}

							}

						}
						else {

							//busca detalhes
							$info = $submodulo->getById($_GET['idsubmodulo']);

							//tratamento das informa??es para fazer o UPDATE
							$info['numidmodulo'] = $info['idmodulo']; 
							$info['litnome_submodulo'] = $info['nome_submodulo']; 
							$info['litdescricao_submodulo'] = $info['descricao_submodulo']; 
							$info['litsubmodulo_final'] = $info['submodulo_final']; 
							$info['numordem_submodulo'] = $info['ordem_submodulo']; 
							$info['numlargura_menu_programa'] = $info['largura_menu_programa']; 
							
							
							
							
							//obt?m os erros
							$err = $submodulo->err;
						}

            $list_modulo = $modulo->make_list_select();
						$smarty->assign("list_modulo",$list_modulo);

						
            
            
            

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a dele??o
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$submodulo->delete($_GET['idsubmodulo']);

					  	//obt?m erros
							$err = $submodulo->err;

							//se n?o ocorreram erros
							if(count($err) == 0){
								$flags['sucesso'] = $conf['excluir'];
								
								
								
							}

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista registros
							$list = $submodulo->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $submodulo->err;

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

	
		// Forma Array de intru??es de preenchimento
	$intrucoes_preenchimento = array();
	if ($flags['action'] == "adicionar" || $flags['action'] == "editar" ) {
		$intrucoes_preenchimento[] = "Os campos em <span class=req>vermelho</span> s&atilde;o obrigat&oacute;rios.";
	}


	// Formata a mensagem para ser exibida
	$flags['intrucoes_preenchimento'] = $form->FormataMensagemAjuda($intrucoes_preenchimento);

	
  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);

  $list_permissao = $auth->check_priv($conf['priv']);
	$smarty->assign("list_permissao",$list_permissao);

  $smarty->display("adm_submodulo.tpl");
?>

