<?php

  //inclus�o de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/aliquota_icms.php");
	

  // configura��es anotionais
  $conf['area'] = "Al�quotas de ICMS"; // �rea


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
	  		$aliquota_icms = new aliquota_icms();

	  											
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
            	
            	
            	
							$form->chk_empty($_POST['valor_icms'], 1, 'Valor da al�quota ICMS (%)'); 

							if ($_POST['valor_icms'] == "0,00") $form->err[] = "O Valor da al�quota ICMS n�o pode ser 0.";
							

              $err = $form->err;

              if(count($err) == 0) {

								
	              $_POST['valor_icms'] = str_replace(",",".",$_POST['valor_icms']); 
								
	              
	              
								
								if ($_POST['valor_icms'] == "") $_POST['valor_icms'] = "NULL"; 
								

	              
								//grava o registro no banco de dados
								$aliquota_icms->set($_POST);


								//obt�m os erros que ocorreram no cadastro
								$err = $aliquota_icms->err;

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

					  //obt�m qual p�gina da listagem deseja exibir
					  $pg = intval(trim($_GET['pg']));

					  //se n�o foi passada a p�gina como par�metro, faz p�gina default igual � p�gina 0
					  if(!$pg) $pg = 0;

					  //lista os registros
						$list = $aliquota_icms->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $aliquota_icms->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          break;
          
          
          // a��o: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idaliquota_icms'] = $_GET['idaliquota_icms']; 
							
							
							$form->chk_empty($_POST['numvalor_icms'], 1, 'Valor da al�quota ICMS (%)'); 
							
							if ($_POST['numvalor_icms'] == "0,00") $form->err[] = "O Valor da al�quota ICMS n�o pode ser 0.";

							
							$err = $form->err;

		          if(count($err) == 0) {

								
								$_POST['numvalor_icms'] = str_replace(",",".",$_POST['numvalor_icms']); 
								
								
								
		          	
								if ($_POST['numvalor_icms'] == "") $_POST['numvalor_icms'] = "NULL"; 
								


								$aliquota_icms->update($_GET['idaliquota_icms'], $_POST);
								
								

								//obt�m erros
								$err = $aliquota_icms->err;

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
							$info = $aliquota_icms->getById($_GET['idaliquota_icms']);

							//tratamento das informa��es para fazer o UPDATE
							$info['numvalor_icms'] = $info['valor_icms']; 
							
							
							
							
							//obt�m os erros
							$err = $aliquota_icms->err;
						}

            
            
            
            

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a dele��o
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$aliquota_icms->delete($_GET['idaliquota_icms']);

					  	//obt�m erros
							$err = $aliquota_icms->err;

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

  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);

  $list_permissao = $auth->check_priv($conf['priv']);
	$smarty->assign("list_permissao",$list_permissao);

  $smarty->display("adm_aliquota_icms.tpl");
?>

