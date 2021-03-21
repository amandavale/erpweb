<?php

	//inclus�o de bibliotecas
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

  	// configura��es anotionais
  	$conf['area'] = "Filial"; // �rea
 

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
	$xajax->registerFunction("Insere_Funcionario_AJAX");
	$xajax->registerFunction("Deleta_Funcionario_AJAX");
	$xajax->registerFunction("Seleciona_Funcionario_AJAX");

	$xajax->registerFunction("Insere_Conta_Bancaria_AJAX");
	$xajax->registerFunction("Deleta_Conta_Bancaria_AJAX");
	$xajax->registerFunction("Seleciona_Conta_Bancaria_AJAX");

	$xajax->registerFunction("Verifica_Campos_Filial_AJAX");
	

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
	  			$filial = new filial();
	  		
	  			$endereco = new endereco(); 
	  			$bairro = new bairro();
				$estado = new estado();
				$filial_funcionario = new filial_funcionario();
				$conta_filial = new conta_filial();
				$produto_filial = new produto_filial();
	  											
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
            	
            	
							$form->chk_empty($_POST['nome_filial'], 1, 'Nome da filial'); 
							$form->chk_cnpj($_POST['cnpj_filial'], 1);
							

              				$err = $form->err;

              				if(count($err) == 0) {

								$_POST['observacao_filial'] = nl2br($_POST['observacao_filial']); 
							
	              				$_POST['cnpj_filial'] = $form->FormataCNPJParaInserir($_POST['cnpj_filial']);
	              
	              
								$_POST['telefone_filial'] = $form->FormataTelefoneParaInserir($_POST['telefone_filial_ddd'], $_POST['telefone_filial']); 
								$_POST['fax_filial'] = $form->FormataTelefoneParaInserir($_POST['fax_filial_ddd'], $_POST['fax_filial']); 
								
								if ($_POST['prox_numero_nf_filial'] == "") $_POST['prox_numero_nf_filial'] = "NULL"; 
								

								// Grava o registro do endere�o no Banco de Dados
								$_POST['idendereco_filial'] = $endereco->InsereEndereco($_POST, "filial");

	              
								//grava o registro no banco de dados
								$idfilial = $filial->set($_POST);
								
								// grava os funcion�rios da filial
								$filial_funcionario->GravaFuncionario($_POST, $idfilial);

								// grava as contas bancarias da filial
								$conta_filial->GravaContasBancariasFilial($_POST, $idfilial);


								//obt�m os erros que ocorreram no cadastro
								$err = $filial->err;

								//se n�o ocorreram erros
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
          
						//obt�m qual p�gina da listagem deseja exibir
					  	$pg = intval(trim($_GET['pg']));

					  	//se n�o foi passada a p�gina como par�metro, faz p�gina default igual � p�gina 0
					  	if(!$pg) $pg = 0;

					  	//lista os registros
						$list = $filial->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $filial->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          				break;
          
          
          			// a��o: editar <<<<<<<<<<
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
								

								// atualiza os dados do endere�o
								$endereco->AtualizaEndereco($_POST['idendereco_filial'], $_POST, "filial");


								$filial->update($_GET['idfilial'], $_POST);
								
 								// grava os funcionarios da filial
								$filial_funcionario->GravaFuncionario($_POST, $_GET['idfilial']);

								// grava as contas bancarias da filial
								$conta_filial->GravaContasBancariasFilial($_POST, $_GET['idfilial']);


								//obt�m erros
								$err = $filial->err;

								//se n�o ocorreram erros
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

							//tratamento das informa��es para fazer o UPDATE
							$info['litnome_filial'] = $info['nome_filial']; 
							$info['litcnpj_filial'] = $info['cnpj_filial']; 
							$info['litinscricao_estadual_filial'] = $info['inscricao_estadual_filial']; 
							$info['numidendereco_filial'] = $info['idendereco_filial']; 
							$info['littelefone_filial'] = $info['telefone_filial']; 
							$info['litfax_filial'] = $info['fax_filial']; 
							$info['litemail_filial'] = $info['email_filial']; 
							$info['litsite_filial'] = $info['site_filial']; 
							$info['litobservacao_filial'] = strip_tags($info['observacao_filial']); 
							

							// Busca os dados do endere�o
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
							

							//obt�m os erros
							$err = $filial->err;
						}



						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a dele��o
					  if($_POST['for_chk']){
					  	
							// busca o codigo do endere�o
					  	$info = $filial->getById($_GET['idfilial']);
					  	
							// deleta o registro
					  	$filial->delete($_GET['idfilial']);
					  	

					  	// deleta o endere�o do banco de dados
					  	$endereco->delete($info['idendereco_filial']);

					  	//obt�m erros
							$err = $filial->err;

							//se n�o ocorreram erros
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


	// Forma Array de intru��es de preenchimento
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
