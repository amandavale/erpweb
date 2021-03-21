<?php

	//inclusão de bibliotecas
  	require_once("../common/lib/conf.inc.php");
  	require_once("../common/lib/db.inc.php");
  	require_once("../common/lib/auth.inc.php");
  	require_once("../common/lib/form.inc.php");
  	require_once("../common/lib/rotinas.inc.php");
  	require_once("../common/lib/Smarty/Smarty.class.php");
  	require_once("../common/lib/xajax/xajax.inc.php");
  
  	require_once("../entidades/filial.php");
	require_once("../entidades/endereco.php"); 
	require_once("../entidades/bairro.php");
	require_once("../entidades/estado.php");
	require_once("../entidades/filial_funcionario.php");
	require_once("../entidades/conta_filial.php");
  	require_once("../entidades/produto_filial.php");

	require_once("funcionario_ajax.php");
	require_once("conta_filial_ajax.php");
	require_once("filial_ajax.php");

  	// configurações anotionais
  	$conf['area'] = "Filial"; // área
 

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
	$xajax->registerFunction("Insere_Funcionario_AJAX");
	$xajax->registerFunction("Deleta_Funcionario_AJAX");
	$xajax->registerFunction("Seleciona_Funcionario_AJAX");

	$xajax->registerFunction("Insere_Conta_Bancaria_AJAX");
	$xajax->registerFunction("Deleta_Conta_Bancaria_AJAX");
	$xajax->registerFunction("Seleciona_Conta_Bancaria_AJAX");

	$xajax->registerFunction("Verifica_Campos_Filial_AJAX");
	

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
	  			$filial = new filial();
	  		
	  			$endereco = new endereco(); 
	  			$bairro = new bairro();
				$estado = new estado();
				$filial_funcionario = new filial_funcionario();
				$conta_filial = new conta_filial();
				$produto_filial = new produto_filial();
	  											
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
            	
            	
							$form->chk_empty($_POST['nome_filial'], 1, 'Nome da filial'); 
							$form->chk_cnpj($_POST['cnpj_filial'], 1);
							

              				$err = $form->err;

              				if(count($err) == 0) {

								$_POST['observacao_filial'] = nl2br($_POST['observacao_filial']); 
							
	              				$_POST['cnpj_filial'] = $form->FormataCNPJParaInserir($_POST['cnpj_filial']);
	              
	              
								$_POST['telefone_filial'] = $form->FormataTelefoneParaInserir($_POST['telefone_filial_ddd'], $_POST['telefone_filial']); 
								$_POST['fax_filial'] = $form->FormataTelefoneParaInserir($_POST['fax_filial_ddd'], $_POST['fax_filial']); 
								
								if ($_POST['prox_numero_nf_filial'] == "") $_POST['prox_numero_nf_filial'] = "NULL"; 
								

								// Grava o registro do endereço no Banco de Dados
								$_POST['idendereco_filial'] = $endereco->InsereEndereco($_POST, "filial");

	              
								//grava o registro no banco de dados
								$idfilial = $filial->set($_POST);
								
								// grava os funcionários da filial
								$filial_funcionario->GravaFuncionario($_POST, $idfilial);

								// grava as contas bancarias da filial
								$conta_filial->GravaContasBancariasFilial($_POST, $idfilial);


								//obtém os erros que ocorreram no cadastro
								$err = $filial->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									
									$produto_filial->set_Nova_Filial($idfilial);
									$flags['sucesso'] = $conf['inserir'];

									//limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $filial->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $filial->err;

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
						$list = $filial->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $filial->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          				break;
          
          
          			// ação: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							$info = $_POST;
							
							$info['idfilial'] = $_GET['idfilial']; 
							
							
							$form->chk_empty($_POST['litnome_filial'], 1, 'Nome da filial'); 
							$form->chk_cnpj($_POST['litcnpj_filial'], 1);
							
							$err = $form->err;

		          			if(count($err) == 0) {

								$_POST['litobservacao_filial'] = nl2br($_POST['litobservacao_filial']); 
							
								
								$_POST['litcnpj_filial'] = $form->FormataCNPJParaInserir($_POST['litcnpj_filial']);
								
		          				$_POST['littelefone_filial'] = $form->FormataTelefoneParaInserir($_POST['telefone_filial_ddd'], $_POST['littelefone_filial']); 
								$_POST['litfax_filial'] = $form->FormataTelefoneParaInserir($_POST['fax_filial_ddd'], $_POST['litfax_filial']); 
								

								// atualiza os dados do endereço
								$endereco->AtualizaEndereco($_POST['idendereco_filial'], $_POST, "filial");


								$filial->update($_GET['idfilial'], $_POST);
								
 								// grava os funcionarios da filial
								$filial_funcionario->GravaFuncionario($_POST, $_GET['idfilial']);

								// grava as contas bancarias da filial
								$conta_filial->GravaContasBancariasFilial($_POST, $_GET['idfilial']);


								//obtém erros
								$err = $filial->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['alterar'];

								  //limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $filial->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $filial->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}

							}

						}
						else {

							//busca detalhes
							$info = $filial->getById($_GET['idfilial']);

							//tratamento das informações para fazer o UPDATE
							$info['litnome_filial'] = $info['nome_filial']; 
							$info['litcnpj_filial'] = $info['cnpj_filial']; 
							$info['litinscricao_estadual_filial'] = $info['inscricao_estadual_filial']; 
							$info['numidendereco_filial'] = $info['idendereco_filial']; 
							$info['littelefone_filial'] = $info['telefone_filial']; 
							$info['litfax_filial'] = $info['fax_filial']; 
							$info['litemail_filial'] = $info['email_filial']; 
							$info['litsite_filial'] = $info['site_filial']; 
							$info['litobservacao_filial'] = strip_tags($info['observacao_filial']); 
							

							// Busca os dados do endereço
							$dados_endereco = $endereco->BuscaDadosEndereco($info['idendereco_filial'], $info, "filial");

							$info['filial_idestado_Nome'] = $dados_endereco['nome_estado'];
							$info['filial_idestado_NomeTemp'] = $dados_endereco['nome_estado'];

							$info['filial_idcidade_Nome'] = $dados_endereco['nome_cidade'];
							$info['filial_idcidade_NomeTemp'] = $dados_endereco['nome_cidade'];

							$info['filial_idbairro_Nome'] = $dados_endereco['nome_bairro'];
							$info['filial_idbairro_NomeTemp'] = $dados_endereco['nome_bairro'];


							if ( strlen($info['telefone_filial']) == 10 ) { 
								$info['littelefone_filial'] = substr($info['telefone_filial'],2,4) . "-" . substr($info['telefone_filial'],6); 
								$info['telefone_filial_ddd'] = substr($info['telefone_filial'],0,2); 
							} 
							
							if ( strlen($info['fax_filial']) == 10 ) { 
								$info['litfax_filial'] = substr($info['fax_filial'],2,4) . "-" . substr($info['fax_filial'],6); 
								$info['fax_filial_ddd'] = substr($info['fax_filial'],0,2); 
							} 
							

							//obtém os erros
							$err = $filial->err;
						}



						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a deleção
					  if($_POST['for_chk']){
					  	
							// busca o codigo do endereço
					  	$info = $filial->getById($_GET['idfilial']);
					  	
							// deleta o registro
					  	$filial->delete($_GET['idfilial']);
					  	

					  	// deleta o endereço do banco de dados
					  	$endereco->delete($info['idendereco_filial']);

					  	//obtém erros
							$err = $filial->err;

							//se não ocorreram erros
							if(count($err) == 0){
								$flags['sucesso'] = $conf['excluir'];
								
								
								
							}

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista registros
							$list = $filial->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $filial->err;

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
	$intrucoes_preenchimento[] = "Os campos em <span class=req>vermelho</span> s&atilde;o obrigat&oacute;rios.";

	// Formata a mensagem para ser exibida
	$flags['intrucoes_preenchimento'] = $form->FormataMensagemAjuda($intrucoes_preenchimento);



	$smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));

  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);

  $list_permissao = $auth->check_priv($conf['priv']);
	$smarty->assign("list_permissao",$list_permissao);
  
  $smarty->display("adm_filial.tpl");
  

?>
