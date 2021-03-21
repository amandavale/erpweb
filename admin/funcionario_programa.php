<?php

  //inclus�o de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  require_once("../common/lib/xajax/xajax.inc.php");
  
  require_once("../entidades/funcionario_programa.php");
	require_once("../entidades/funcionario.php"); 
	require_once("../entidades/programa.php"); 
	
	require_once("funcionario_programa_ajax.php");
	require_once("funcionario_ajax.php");
	

  // configura��es anotionais
  $conf['area'] = "Cadastro de rela��es entre Programas e Funcion�rios"; // �rea


  //configura��o de estilo
  $conf['style'] = "full";

  // inicializa templating
  $smarty = new Smarty;
  
  
  // cria o objeto xajax
	$xajax = new xajax();
	//$xajax->debugOn();
 
	
	// registra todas as fun��es que ser�o usadas
	$xajax->registerFunction("Insere_Programa_AJAX");
	$xajax->registerFunction("Verifica_Campos_Programa_AJAX");
	$xajax->registerFunction("Verifica_Programa_Existe_AJAX");
	$xajax->registerFunction("Deleta_Programa_AJAX");
	$xajax->registerFunction("Seleciona_Programa_AJAX");
	$xajax->registerFunction("Seleciona_Herdeiro");
	$xajax->registerFunction("Manipula_Div");
	$xajax->registerFunction("Mostra_Div");
	$xajax->registerFunction("Esconde_Div");

  
	// processa as fun��es
	$xajax->processRequests();

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
	  		$funcionario_programa = new funcionario_programa();
	  		
	  		$funcionario = new funcionario(); 
				$programa = new programa(); 
				
	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para valida��o de formul�rio
        $form = new form();
        
				$list = $auth->check_priv($conf['priv']);
				$aux = $flags['action'];
				if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}


        switch($flags['action']) {

          // a��o: adicionar <<<<<<<<<<
          case "adicionar":

            if($_POST['for_chk']) {

              $err = $form->err;

              if(count($err) == 0) {

							$_POST['idfuncionario'] = $_POST['idfuncionario_programa'];
              	
              $funcionario_programa->delete_programa($_POST['idfuncionario']);	
              		
							$count = 0;
							
							while (isset($_POST["programa_$count"]))
							{	
								
								$codigoPrograma = $_POST["programa_$count"];
								
								if ($_POST["permissao_adicionar_programa_$codigoPrograma"]) $_POST['permissao_adicionar'] = "1";
								else $_POST['permissao_adicionar'] = "0";
								if ($_POST["permissao_editar_programa_$codigoPrograma"]) $_POST['permissao_editar'] = "1";
								else $_POST['permissao_editar'] = "0";
								if ($_POST["permissao_excluir_programa_$codigoPrograma"]) $_POST['permissao_excluir'] = "1";
								else $_POST['permissao_excluir'] = "0";
								if ($_POST["permissao_listar_programa_$codigoPrograma"]) $_POST['permissao_listar'] = "1";
								else $_POST['permissao_listar'] = "0";
								
								
								$_POST['idprograma'] = $_POST["programa_$count"];
								//grava o registro no banco de dados
								
								if($_POST['permissao_adicionar'] == '1' || $_POST['permissao_editar'] == '1' || $_POST['permissao_excluir'] == '1' || $_POST['permissao_listar'] == '1')

									$funcionario_programa->set($_POST);
								
								$count++;

							}

	              

								//obt�m os erros que ocorreram no cadastro
								$err = $funcionario_programa->err;

								//se n�o ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['inserir'];

									//limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "";


								}
								
              }
              
            }
            
            $list_funcionario = $funcionario->make_list_select();
						$smarty->assign("list_funcionario",$list_funcionario);

						$list_programa = $programa->make_list_select();
						$smarty->assign("list_programa",$list_programa);

						

          break;


					//listagem dos registros
          case "listar":
          
					  //obt�m qual p�gina da listagem deseja exibir
					 // $pg = intval(trim($_GET['pg']));

					  //se n�o foi passada a p�gina como par�metro, faz p�gina default igual � p�gina 0
					  //if(!$pg) $pg = 0;

					  //lista os registros
						//$list = $funcionario_programa->make_list($pg, $conf['rppg']);

						//pega os erros
						//$err = $funcionario_programa->err;

						//passa a listagem para o template
						//$smarty->assign("list", $list);
						
						
						$flags['action'] = "adicionar";

          break;
          
          
          // a��o: editar <<<<<<<<<<
					case "editar":
					
						$flags['action'] = "adicionar";

          	break;
          	
          	
						if($_POST['for_chk']) {
							
							
							$err = $form->err;

		          if(count($err) == 0) {

		          	
		          	$_POST['idfuncionario'] = $_GET['idfuncionario'];
	              	
	              $funcionario_programa->delete_programa($_POST['idfuncionario']);	
	              		
								$count = 0;
								
								while (isset($_POST["programa_$count"]))
								{	
									
								$codigoPrograma = $_POST["programa_$count"];
								
								if ($_POST["permissao_adicionar_programa_$codigoPrograma"]) $_POST['permissao_adicionar'] = "1";
								else $_POST['permissao_adicionar'] = "0";
								if ($_POST["permissao_editar_programa_$codigoPrograma"]) $_POST['permissao_editar'] = "1";
								else $_POST['permissao_editar'] = "0";
								if ($_POST["permissao_excluir_programa_$codigoPrograma"]) $_POST['permissao_excluir'] = "1";
								else $_POST['permissao_excluir'] = "0";
								if ($_POST["permissao_listar_programa_$codigoPrograma"]) $_POST['permissao_listar'] = "1";
								else $_POST['permissao_listar'] = "0";
									
									
									$_POST['idprograma'] = $_POST["programa_$count"];
									//grava o registro no banco de dados
									
								if($_POST['permissao_adicionar'] == '1' || $_POST['permissao_editar'] == '1' || $_POST['permissao_excluir'] == '1' || $_POST['permissao_listar'] == '1')

									$funcionario_programa->set($_POST);
								
									
									$count++;
	
								}
								

								//obt�m erros
								$err = $funcionario_programa->err;

								//se n�o ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['alterar'];

								  //limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "";



								}

							}

						}
						else {

							//busca detalhes
							$info = $funcionario_programa->getById_funcionario($_GET['idfuncionario'],$_GET['idprograma']);

							//tratamento das informa��es para fazer o UPDATE
							$info['numidfuncionario'] = $info['idfuncionario']; 
							$info['numidprograma'] = $info['idprograma']; 
							$info['litpermissao_adicionar'] = $info['permissao_adicionar']; 
							$info['litpermissao_editar'] = $info['permissao_editar']; 
							$info['litpermissao_excluir'] = $info['permissao_excluir']; 
							$info['litpermissao_listar'] = $info['permissao_listar']; 
							
							
							
							
							//obt�m os erros
							$err = $funcionario_programa->err;
						}

            $list_funcionario = $funcionario->make_list_select();
						$smarty->assign("list_funcionario",$list_funcionario);

						$list_programa = $programa->make_list_select();
						$smarty->assign("list_programa",$list_programa);

						
            
            
            

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a dele��o
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$funcionario_programa->delete($_GET['idfuncionario'],$_GET['idprograma']);

					  	//obt�m erros
							$err = $funcionario_programa->err;

							//se n�o ocorreram erros
							if(count($err) == 0){
								$flags['sucesso'] = $conf['excluir'];
								
								
								
							}

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista registros
							$list = $funcionario_programa->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $funcionario_programa->err;

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
	
	// Forma Array de intru��es de preenchimento
	$intrucoes_preenchimento = array();
	if ($flags['action'] == "adicionar" || $flags['action'] == "editar" ) {
		$intrucoes_preenchimento[] = "Os campos em <span class=req>vermelho</span> s&atilde;o obrigat&oacute;rios.";
	}


	// Formata a mensagem para ser exibida
	$flags['intrucoes_preenchimento'] = $form->FormataMensagemAjuda($intrucoes_preenchimento);
	
	$smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));
	
  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);
  
	$list_permissao = $auth->check_priv($conf['priv']);
	$smarty->assign("list_permissao",$list_permissao);
  
  
  $smarty->display("adm_funcionario_programa.tpl");
?>

