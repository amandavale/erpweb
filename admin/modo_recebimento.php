<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/modo_recebimento.php");
	
	

  // configurações anotionais
  $conf['area'] = "Modos de Recebimento"; // área


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
	  		$modo_recebimento = new modo_recebimento();
	  		
	  		
	  											
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
            	
            	
            	
							$form->chk_empty($_POST['sigla_modo_recebimento'], 1, 'Sigla do Modo de recebimento'); 
							$form->chk_empty($_POST['descricao'], 1, 'Descrição do Modo de recebimento'); 
							$form->chk_empty($_POST['a_vista'], 1, 'Modo a vista ?'); 
							$form->chk_empty($_POST['a_prazo'], 1, 'Modo a prazo ?'); 
							$form->chk_empty($_POST['baixa_automatica'], 1, 'Baixa automática ?'); 
							

              $err = $form->err;

              if(count($err) == 0) {

								
	              
	              
	              
								
								if ($_POST['dias_baixa_automatica_vista'] == "") $_POST['dias_baixa_automatica_vista'] = "NULL"; 
								if ($_POST['dias_baixa_automatica_prazo'] == "") $_POST['dias_baixa_automatica_prazo'] = "NULL"; 
								

	              
								//grava o registro no banco de dados
								$modo_recebimento->set($_POST);


								//obtém os erros que ocorreram no cadastro
								$err = $modo_recebimento->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['inserir'];

									//limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $modo_recebimento->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $modo_recebimento->err;

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
						$list = $modo_recebimento->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $modo_recebimento->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          break;
          
          
          // ação: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['sigla_modo_recebimento'] = $_GET['sigla_modo_recebimento']; 
							
							
							$form->chk_empty($_POST['litsigla_modo_recebimento'], 1, 'Sigla do Modo de recebimento'); 
							$form->chk_empty($_POST['litdescricao'], 1, 'Descrição do Modo de recebimento'); 
							$form->chk_empty($_POST['lita_vista'], 1, 'Modo a vista ?'); 
							$form->chk_empty($_POST['lita_prazo'], 1, 'Modo a prazo ?'); 
							$form->chk_empty($_POST['litbaixa_automatica'], 1, 'Baixa automática ?'); 
							
							
							$err = $form->err;

		          if(count($err) == 0) {

								
								
								
								
		          	
								if ($_POST['numdias_baixa_automatica_vista'] == "") $_POST['numdias_baixa_automatica_vista'] = "NULL"; 
								if ($_POST['numdias_baixa_automatica_prazo'] == "") $_POST['numdias_baixa_automatica_prazo'] = "NULL"; 
								


								$modo_recebimento->update($_GET['sigla_modo_recebimento'], $_POST);
								
								

								//obtém erros
								$err = $modo_recebimento->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['alterar'];

								  //limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $modo_recebimento->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $modo_recebimento->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}

							}

						}
						else {

							//busca detalhes
							$info = $modo_recebimento->getById($_GET['sigla_modo_recebimento']);

							//tratamento das informações para fazer o UPDATE
							$info['litsigla_modo_recebimento'] = $info['sigla_modo_recebimento']; 
							$info['litdescricao'] = $info['descricao']; 
							$info['lita_vista'] = $info['a_vista']; 
							$info['lita_prazo'] = $info['a_prazo']; 
							$info['litbaixa_automatica'] = $info['baixa_automatica']; 
							$info['numdias_baixa_automatica_vista'] = $info['dias_baixa_automatica_vista']; 
							$info['numdias_baixa_automatica_prazo'] = $info['dias_baixa_automatica_prazo']; 
							
							
							
							//obtém os erros
							$err = $modo_recebimento->err;
						}

            
            
            
            

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a deleção
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$modo_recebimento->delete($_GET['sigla_modo_recebimento']);

					  	//obtém erros
							$err = $modo_recebimento->err;

							//se não ocorreram erros
							if(count($err) == 0){
								$flags['sucesso'] = $conf['excluir'];
								
								
								
							}

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista registros
							$list = $modo_recebimento->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $modo_recebimento->err;

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

	// Forma Array de intruções de preenchimento
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
 
  
  $smarty->display("adm_modo_recebimento.tpl");
?>

