<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  require_once("../common/lib/xajax/xajax.inc.php");
  
  require_once("../entidades/transportador.php");
	require_once("../entidades/endereco.php"); 
	require_once("../entidades/bairro.php");
	require_once("../entidades/estado.php");

	require_once("transportador_ajax.php");	
	

  // configurações anotionais
  $conf['area'] = "Transportador"; // área


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
	$xajax->registerFunction("Verifica_Campos_Transportador_AJAX");


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
	  		$transportador = new transportador();
	  		
	  		$endereco = new endereco(); 
	  		$bairro = new bairro();
				$estado = new estado();

	  											
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
							$list = $transportador->Busca_Generica($pg, $flags['rpp'], $flags['busca'], "", "ac=busca_generica&busca=".$flags['busca']."&rpp=".$flags['rpp']);

							//pega os erros
							$err = $transportador->err;

							//passa a listagem para o template
							$smarty->assign("list", $list);

						}

						if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];

          break;

        	
        	
        	

          // ação: adicionar <<<<<<<<<<
          case "adicionar":

            if($_POST['for_chk']) {
            	
            	
            	
							$form->chk_empty($_POST['tipo_transportador'], 1, 'Tipo do transportador'); 
							$form->chk_empty($_POST['nome_transportador'], 1, 'Nome do transportador'); 
							

              $err = $form->err;

              if(count($err) == 0) {

								$_POST['observacao_transportador'] = nl2br($_POST['observacao_transportador']); 
							

								$_POST['telefone_transportador'] = $form->FormataTelefoneParaInserir($_POST['telefone_transportador_ddd'], $_POST['telefone_transportador']); 
								$_POST['fax_transportador'] = $form->FormataTelefoneParaInserir($_POST['fax_transportador_ddd'], $_POST['fax_transportador']); 
								

								// Grava o registro do endereço no Banco de Dados
								$_POST['idendereco_transportador'] = $endereco->InsereEndereco($_POST, "transportador");

								//grava o registro no banco de dados
								$transportador->set($_POST);


								//obtém os erros que ocorreram no cadastro
								$err = $transportador->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['inserir'];

									//limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $transportador->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $transportador->err;

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
						$list = $transportador->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $transportador->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          break;
          
          
          // ação: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idtransportador'] = $_GET['idtransportador']; 
							
							
							$form->chk_empty($_POST['littipo_transportador'], 1, 'Tipo do transportador'); 
							$form->chk_empty($_POST['litnome_transportador'], 1, 'Nome do transportador'); 
							
							
							$err = $form->err;

		          if(count($err) == 0) {

								$_POST['litobservacao_transportador'] = nl2br($_POST['litobservacao_transportador']); 
							
								
								
								
		          	$_POST['littelefone_transportador'] = $form->FormataTelefoneParaInserir($_POST['telefone_transportador_ddd'], $_POST['littelefone_transportador']); 
								$_POST['litfax_transportador'] = $form->FormataTelefoneParaInserir($_POST['fax_transportador_ddd'], $_POST['litfax_transportador']); 
								
								
								// atualiza os dados do endereço
								$endereco->AtualizaEndereco($_POST['idendereco_transportador'], $_POST, "transportador");

								$transportador->update($_GET['idtransportador'], $_POST);
								
								

								//obtém erros
								$err = $transportador->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['alterar'];

								  //limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $transportador->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $transportador->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}

							}

						}
						else {

							//busca detalhes
							$info = $transportador->getById($_GET['idtransportador']);

							//tratamento das informações para fazer o UPDATE
							$info['littipo_transportador'] = $info['tipo_transportador']; 
							$info['litnome_transportador'] = $info['nome_transportador']; 
							$info['litcpf_cnpj'] = $info['cpf_cnpj']; 
							$info['litinstricao_estadual_transportador'] = $info['instricao_estadual_transportador']; 
							$info['numidendereco_transportador'] = $info['idendereco_transportador']; 
							$info['littelefone_transportador'] = $info['telefone_transportador']; 
							$info['litfax_transportador'] = $info['fax_transportador']; 
							$info['litemail_transportador'] = $info['email_transportador']; 
							$info['litsite_transportador'] = $info['site_transportador']; 
							$info['litobservacao_transportador'] = strip_tags($info['observacao_transportador']); 
							
							
							// Busca os dados do endereço
							$dados_endereco = $endereco->BuscaDadosEndereco($info['idendereco_transportador'], $info, "transportador");

							$info['transportador_idestado_Nome'] = $dados_endereco['nome_estado'];
							$info['transportador_idestado_NomeTemp'] = $dados_endereco['nome_estado'];

							$info['transportador_idcidade_Nome'] = $dados_endereco['nome_cidade'];
							$info['transportador_idcidade_NomeTemp'] = $dados_endereco['nome_cidade'];

							$info['transportador_idbairro_Nome'] = $dados_endereco['nome_bairro'];
							$info['transportador_idbairro_NomeTemp'] = $dados_endereco['nome_bairro'];
							

							if ( strlen($info['telefone_transportador']) == 10 ) { 
								$info['littelefone_transportador'] = substr($info['telefone_transportador'],2,4) . "-" . substr($info['telefone_transportador'],6); 
								$info['telefone_transportador_ddd'] = substr($info['telefone_transportador'],0,2); 
							} 
							
							if ( strlen($info['fax_transportador']) == 10 ) { 
								$info['litfax_transportador'] = substr($info['fax_transportador'],2,4) . "-" . substr($info['fax_transportador'],6); 
								$info['fax_transportador_ddd'] = substr($info['fax_transportador'],0,2); 
							} 
							
							
							
							//obtém os erros
							$err = $transportador->err;
						}


            

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a deleção
					  if($_POST['for_chk']){
					  	
					  	// busca o codigo do endereço
					  	$info = $transportador->getById($_GET['idtransportador']);

							// deleta o registro
					  	$transportador->delete($_GET['idtransportador']);

					  	// deleta o endereço do banco de dados
					  	$endereco->delete($info['idendereco_transportador']);


					  	//obtém erros
							$err = $transportador->err;

							//se não ocorreram erros
							if(count($err) == 0){
								$flags['sucesso'] = $conf['excluir'];
								
								
								
							}

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista registros
							$list = $transportador->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $transportador->err;

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
  	
	if ($_GET['target'] == "full") $smarty->display("adm_relatorio_transportador.tpl");
  else $smarty->display("adm_transportador.tpl");
	
  
?>

