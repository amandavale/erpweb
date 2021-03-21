<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  require_once("../common/lib/xajax/xajax.inc.php");
  
  require_once("../entidades/troco.php");
	require_once("../entidades/filial.php"); 
	require_once("../entidades/funcionario.php");

	
	require_once("troco_ajax.php");		

  // configurações anotionais
  $conf['area'] = "Troco"; // área


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

  // cria o objeto xajax
	$xajax = new xajax();


	// registra todas as funções que serão usadas
	$xajax->registerFunction("Verifica_Campos_Troco_AJAX");
	$xajax->registerFunction("Verifica_Campos_Busca_Rapida_AJAX");

	// processa as funções
	$xajax->processRequests();


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
	  		$troco = new troco();	  		
				$filial = new filial();
				$funcionario = new funcionario();
	  		
	  											
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

						// busca o dia atual
						$flags['data_criacao'] = date('d/m/Y');
						
						// busca os dados da filial
						$info_filial = $filial->getById($_SESSION['idfilial_usuario']);
						$smarty->assign("info_filial", $info_filial);

						//busca os funcionarios da filial
						$list_funcionarios = $funcionario->Seleciona_Funcionarios_Da_Filial($_SESSION['idfilial_usuario']);
						$smarty->assign("list_funcionarios",$list_funcionarios);

						
						// verifica se o troco de hoje já foi cadastrado
						$data_atual = date('Y-m-d');
						$info_troco =	$troco->RecuperaTroco($data_atual, $_SESSION['idfilial_usuario']);

						if ($info_troco['idtroco'] != "") $flags['proibe'] = 1;
						//--------------------------------------------------------


            if($_POST['for_chk']) {
            	
            	
            	
							$form->chk_empty($_POST['valor_troco'], 1, 'Valor do troco (R$)'); 
							

              $err = $form->err;

              if(count($err) == 0) {

								
	              $_POST['valor_troco'] = str_replace(",",".",$_POST['valor_troco']); 
								
								if ($_POST['valor_troco'] == "") $_POST['valor_troco'] = "NULL"; 
								

								$_POST['dia'] = date('Y-m-d');

	              
								//grava o registro no banco de dados
								$troco->set($_POST);


								//obtém os erros que ocorreram no cadastro
								$err = $troco->err;

								//se não ocorreram erros
								if(count($err) == 0) {

									// redireciona a página para evitar o problema do reload	
									$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=inserir'>"; 
									echo $redirecionar; 
									exit;


								}
								
              }
              
            }
            
            

          break;


					//listagem dos registros
          case "listar":

						if (isset($_GET['sucesso'])) $flags['sucesso'] = $conf["{$_GET['sucesso']}"];						

						// busca os dados da filial
						$info_filial = $filial->getById($_SESSION['idfilial_usuario']);
						$smarty->assign("info_filial", $info_filial);

          break;
          
          
          // ação: editar <<<<<<<<<<
					case "editar":

						// busca os dados da filial
						$info_filial = $filial->getById($_SESSION['idfilial_usuario']);
						$smarty->assign("info_filial", $info_filial);

						//busca os funcionarios da filial
						$list_funcionarios = $funcionario->Seleciona_Funcionarios_Da_Filial($_SESSION['idfilial_usuario']);
						$smarty->assign("list_funcionarios",$list_funcionarios);


						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idtroco'] = $_GET['idtroco']; 
							
							
							$form->chk_empty($_POST['numvalor_troco'], 1, 'Valor do troco (R$)'); 
							
							
							$err = $form->err;

		          if(count($err) == 0) {

								
								$_POST['numvalor_troco'] = str_replace(",",".",$_POST['numvalor_troco']); 

								if ($_POST['numvalor_troco'] == "") $_POST['numvalor_troco'] = "NULL"; 

								$_POST['numidfuncionario'] = $_POST['idfuncionario'];


								$troco->update($_GET['idtroco'], $_POST);
								
								

								//obtém erros
								$err = $troco->err;

								//se não ocorreram erros
								if(count($err) == 0) {

									// redireciona a página para evitar o problema do reload	
									$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=alterar'>"; 
									echo $redirecionar; 
									exit;

								}

							}

						}
						else {

							//busca detalhes
							$info = $troco->getById($_GET['idtroco']);

							//tratamento das informações para fazer o UPDATE
							$info['numvalor_troco'] = $info['valor_troco']; 
							
							// só é possível alterar o troco do dia atual
							if ($info['dia'] != date('d/m/Y')) $flags['proibe'] = 1;
							
							
							//obtém os erros
							$err = $troco->err;
						}

            
            
            
            

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

           

	      }
	      
      }
      
  	}
  	
    // seta erros
    $smarty->assign("err", $err);
    
	}

	// Forma Array de intruções de preenchimento
	$intrucoes_preenchimento = array();
	if ($flags['action'] == "adicionar" || $flags['action'] == "editar" ) {
		$intrucoes_preenchimento[] = "Os campos em <span class=req>vermelho</span> s&atilde;o obrigat&oacute;rios.";
	}
	else if ($flags['action'] == "busca_generica" || $flags['action'] == "busca_parametrizada") {
		$intrucoes_preenchimento[] = "Preencha os campos para realizar a busca.";
	}

	// Formata a mensagem para ser exibida
	$flags['intrucoes_preenchimento'] = $form->FormataMensagemAjuda($intrucoes_preenchimento);

	
	$smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));

  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);
  
	$list_permissao = $auth->check_priv($conf['priv']);
	$smarty->assign("list_permissao",$list_permissao);

  $smarty->display("adm_troco.tpl");
?>

