<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/aliquota_icms.php");
	

  // configurações anotionais
  $conf['area'] = "Alíquotas de ICMS"; // área


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
	  		$aliquota_icms = new aliquota_icms();

	  											
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
            	
            	
            	
							$form->chk_empty($_POST['valor_icms'], 1, 'Valor da alíquota ICMS (%)'); 

							if ($_POST['valor_icms'] == "0,00") $form->err[] = "O Valor da alíquota ICMS não pode ser 0.";
							

              $err = $form->err;

              if(count($err) == 0) {

								
	              $_POST['valor_icms'] = str_replace(",",".",$_POST['valor_icms']); 
								
	              
	              
								
								if ($_POST['valor_icms'] == "") $_POST['valor_icms'] = "NULL"; 
								

	              
								//grava o registro no banco de dados
								$aliquota_icms->set($_POST);


								//obtém os erros que ocorreram no cadastro
								$err = $aliquota_icms->err;

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

					  //obtém qual página da listagem deseja exibir
					  $pg = intval(trim($_GET['pg']));

					  //se não foi passada a página como parâmetro, faz página default igual à página 0
					  if(!$pg) $pg = 0;

					  //lista os registros
						$list = $aliquota_icms->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $aliquota_icms->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          break;
          
          
          // ação: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idaliquota_icms'] = $_GET['idaliquota_icms']; 
							
							
							$form->chk_empty($_POST['numvalor_icms'], 1, 'Valor da alí­quota ICMS (%)'); 
							
							if ($_POST['numvalor_icms'] == "0,00") $form->err[] = "O Valor da alíquota ICMS não pode ser 0.";

							
							$err = $form->err;

		          if(count($err) == 0) {

								
								$_POST['numvalor_icms'] = str_replace(",",".",$_POST['numvalor_icms']); 
								
								
								
		          	
								if ($_POST['numvalor_icms'] == "") $_POST['numvalor_icms'] = "NULL"; 
								


								$aliquota_icms->update($_GET['idaliquota_icms'], $_POST);
								
								

								//obtém erros
								$err = $aliquota_icms->err;

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
							$info = $aliquota_icms->getById($_GET['idaliquota_icms']);

							//tratamento das informações para fazer o UPDATE
							$info['numvalor_icms'] = $info['valor_icms']; 
							
							
							
							
							//obtém os erros
							$err = $aliquota_icms->err;
						}

            
            
            
            

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a deleção
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$aliquota_icms->delete($_GET['idaliquota_icms']);

					  	//obtém erros
							$err = $aliquota_icms->err;

							//se não ocorreram erros
							if(count($err) == 0){
								
								// redireciona a página para evitar o problema do reload	
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

