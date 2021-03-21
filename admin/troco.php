<?php

  //inclus�o de bibliotecas
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

  // configura��es anotionais
  $conf['area'] = "Troco"; // �rea


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

  // cria o objeto xajax
	$xajax = new xajax();


	// registra todas as fun��es que ser�o usadas
	$xajax->registerFunction("Verifica_Campos_Troco_AJAX");
	$xajax->registerFunction("Verifica_Campos_Busca_Rapida_AJAX");

	// processa as fun��es
	$xajax->processRequests();


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
	  		$troco = new troco();	  		
				$filial = new filial();
				$funcionario = new funcionario();
	  		
	  											
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

						// busca o dia atual
						$flags['data_criacao'] = date('d/m/Y');
						
						// busca os dados da filial
						$info_filial = $filial->getById($_SESSION['idfilial_usuario']);
						$smarty->assign("info_filial", $info_filial);

						//busca os funcionarios da filial
						$list_funcionarios = $funcionario->Seleciona_Funcionarios_Da_Filial($_SESSION['idfilial_usuario']);
						$smarty->assign("list_funcionarios",$list_funcionarios);

						
						// verifica se o troco de hoje j� foi cadastrado
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


								//obt�m os erros que ocorreram no cadastro
								$err = $troco->err;

								//se n�o ocorreram erros
								if(count($err) == 0) {

									// redireciona a p�gina para evitar o problema do reload	
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
          
          
          // a��o: editar <<<<<<<<<<
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
								
								

								//obt�m erros
								$err = $troco->err;

								//se n�o ocorreram erros
								if(count($err) == 0) {

									// redireciona a p�gina para evitar o problema do reload	
									$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=alterar'>"; 
									echo $redirecionar; 
									exit;

								}

							}

						}
						else {

							//busca detalhes
							$info = $troco->getById($_GET['idtroco']);

							//tratamento das informa��es para fazer o UPDATE
							$info['numvalor_troco'] = $info['valor_troco']; 
							
							// s� � poss�vel alterar o troco do dia atual
							if ($info['dia'] != date('d/m/Y')) $flags['proibe'] = 1;
							
							
							//obt�m os erros
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

	// Forma Array de intru��es de preenchimento
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

