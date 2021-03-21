<?php

  //inclus�o de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  require_once("../common/lib/xajax/xajax.inc.php");
  
  require_once("../entidades/encartelamento.php");
  require_once("../entidades/filial.php");
  require_once("../entidades/encartelamento_produto.php");
  
  require_once("encartelamento_ajax.php");
	



  // configura��es anotionais
  $conf['area'] = "Encartelamento"; // �rea
  
  //configura��o de estilo
  $conf['style'] = "full";

  // inicializa templating
  $smarty = new Smarty;

  // configura diret�rios
  $smarty->template_dir = "../common/tpl";
  $smarty->compile_dir =   "../common/tpl_c";

  // seta configura��es
  $smarty->assign("conf", $conf);
  
  // cria o objeto xajax
	$xajax = new xajax();
	
 
	// registra todas as fun��es que ser�o usadas 
	$xajax->registerFunction("Insere_Produto_AJAX");
	$xajax->registerFunction("Deleta_Produto_AJAX");
	$xajax->registerFunction("Seleciona_Produto_AJAX");
	$xajax->registerFunction("Calcula_Total_AJAX");
	$xajax->registerFunction("Atualiza_Total_AJAX");
	$xajax->registerFunction("Verifica_Campos_Encartelamento_AJAX");
	
	// processa as fun��es
	$xajax->processRequests();

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
	  		$encartelamento = new encartelamento();
	  		$encartelamento_produto = new encartelamento_produto();
	  		$filial = new filial();
	  		
	  		
	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para valida��o de formul�rio
        $form = new form();
        
        $list = $auth->check_priv($conf['priv']);
				$aux = $flags['action'];
				if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}

				

        switch($flags['action']) {

					// busca gen�rica
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


						  //obt�m qual p�gina da listagem deseja exibir
						  $pg = intval(trim($_GET['pg']));

						  //se n�o foi passada a p�gina como par�metro, faz p�gina default igual � p�gina 0
						  if(!$pg) $pg = 0;

						  //lista os registros
							$list = $encartelamento->Busca_Generica($pg, $flags['rpp'], $flags['busca'], "", "ac=busca_generica&busca=".$flags['busca']."&rpp=".$flags['rpp']);

							//pega os erros
							$err = $produto->err;

							//passa a listagem para o template
							$smarty->assign("list", $list);

						}

						if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];

          break;




          // a��o: adicionar <<<<<<<<<<
          case "adicionar":


						if($_POST['for_chk']) {
      	      	
							$form->chk_empty($_POST['descricao_encartelamento'], 1, 'Descri��o');

              $err = $form->err;

              if(count($err) == 0) {


								//grava o registro no banco de dados
								$idencartelamento = $encartelamento->set($_POST);
								$flags['idencartelamento'] = $idencartelamento;

								//grava os itens do encartelamento
								$encartelamento_produto->GravaEncartelamento($_POST, $idencartelamento);


								//obt�m os erros que ocorreram no cadastro
								$err = $encartelamento->err;

								//se n�o ocorreram erros
								if(count($err) == 0) {

									// redireciona a p�gina para evitar o problema do reload	
									$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=inserir'>"; 
									echo $redirecionar; 
									exit;		

								}


              }


            }

          	//passa a listagem de filiais para o template
            $list_filial = $filial->make_list_select();
						$smarty->assign("list_filial",$list_filial);



					//listagem dos registros
          case "listar":

						if (isset($_GET['sucesso'])) $flags['sucesso'] = $conf["{$_GET['sucesso']}"];
          
					  //obt�m qual p�gina da listagem deseja exibir
					  $pg = intval(trim($_GET['pg']));

					  //se n�o foi passada a p�gina como par�metro, faz p�gina default igual � p�gina 0
					  if(!$pg) $pg = 0;

					  //lista os registros
						$list = $encartelamento->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $encartelamento->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);
						
          break;
          
          
         	// a��o: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							$info = $_POST;
							
							$info['idencartelamento'] = $_GET['idencartelamento']; 
							
							
							$form->chk_empty($_POST['litdescricao_encartelamento'], 1, 'Descri��o do encartelamento'); 
							
							
							$err = $form->err;

		          if(count($err) == 0) {

								

        				// retira o numero_itens do array de atualiza��o
								$numero_itens = $_POST['numero_itens'];
								
								unset($_POST['numero_itens']);

								$encartelamento->update($_GET['idencartelamento'], $_POST);


								$flags['idencartelamento'] = $_GET['idencartelamento'];


								//grava os itens do encartelamento
								$encartelamento_produto->GravaEncartelamento($_POST, $_GET['idencartelamento']);



								//obt�m erros
								$err = $encartelamento->err;

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
							$info = $encartelamento->getById($_GET['idencartelamento']);

							//tratamento das informa��es para fazer o UPDATE
							$info['litdescricao_encartelamento'] = $info['descricao_encartelamento']; 
							
							
							
							//obt�m os erros
							$err = $encartelamento->err;
						}

            //passa a listagem de filiais para o template
            $list_filial = $filial->make_list_select();
						$smarty->assign("list_filial",$list_filial);

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

 

          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a dele��o
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$encartelamento->delete($_GET['idencartelamento']);

					  	//obt�m erros
							$err = $encartelamento->err;

							//se n�o ocorreram erros
							if(count($err) == 0){

								// redireciona a p�gina para evitar o problema do reload	
								$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=excluir'>"; 
								echo $redirecionar; 
								exit;								
								
							}

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
  
	if ($_GET['target'] == "full") $smarty->display("adm_relatorio_encartelamento.tpl");
  else $smarty->display("adm_encartelamento.tpl");

?>

