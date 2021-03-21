<?php

	//inclusão de bibliotecas
	require_once("../common/lib/conf.inc.php");
	require_once("../common/lib/db.inc.php");
	require_once("../common/lib/auth.inc.php");
	require_once("../common/lib/form.inc.php");
	require_once("../common/lib/Smarty/Smarty.class.php");

	require_once '../entidades/filial.php';
	require_once '../entidades/movimento.php';

	// configurações anotionais
  	$conf['area'] = "Retorno"; // área

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
  	if ($flags['action'] == "") $flags['action'] = 'adicionar';

  	$form = new form();
  	
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

        		$filial = new filial();
        		$movimento = new movimento();
        		
        		// inicializa banco de dados
		        $db = new db();

        		//incializa classe para validação de formulário
        		$form = new form();
        
				$list = $auth->check_priv($conf['priv']);
				$aux = $flags['action'];

				if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {
					
					$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".
							$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; 
					$flags['action'] = "";
				}
				
        		switch($flags['action']) {

	          		// ação: adicionar
          			case "adicionar":

			      		$lista_bancos = array(
			      				'id_banco' => array('CE','BR','BT','I', 'S'),
			      				'nome_banco' => array('Caixa Econ&ocirc;mica Federal','Bradesco Mila Center', 'Bradesco SOS Prestadora', 'Ita&uacute;', 'Sicoob')
			      		);
			      		$smarty->assign('lista_bancos', $lista_bancos);

					
						if(isset($_POST['Adicionar'])){

							/**
							 * Verificação de dados fornecidos no formulário
							 */

							$form->validateUpload($_FILES['arquivo_retorno']);

							$form->chk_empty($_POST['banco'], true, "Banco");
							
							$err = $form->err;
							
							if(count($err) == 0){
								
								/// coloca o conteúdo do arquivo em um array
								$conteudo_arquivo = file($_FILES['arquivo_retorno']['tmp_name']);
								
								$contas_pagas = $movimento->interpretaRetorno($_POST['banco'],
																				$_SESSION['idfilial_usuario'],
																				  $conteudo_arquivo);

								if($contas_pagas){
									
									if($contas_pagas['erro']){
										$err[] = $erro_validacao['arquivo_retorno'];
									}
									else{

										/// faz pesquisa para verificar que as contas enviadas no arquivo de retorno 
										/// existem no banco de dados e estão associadas à filial 
										$contas_pagas = $movimento->validaContasPagas($contas_pagas['contas_pagas'], 
																							$_SESSION['idfilial_usuario']);

										$smarty->assign('contas_pagas',$contas_pagas);

										/// armazena os dados na sessão para não precisar fazer consulta novamente
										$_SESSION['movimentos_retorno'] = $contas_pagas;
										$_SESSION['tipo_arquivo_retorno'] = $_POST['banco'];
										
										switch($_POST['banco']){
											case 'BR':
												$smarty->assign('nome_banco','Bradesco');
												break;
											case 'CE':
												$smarty->assign('nome_banco','Caixa Econ&ocirc;mica Federal');
												break;
											case 'I':
												$smarty->assign('nome_banco','Ita&uacute;');
												break;
											case 'S':
												$smarty->assign('nome_banco','Sicoob');
												break;


										}
										
										/// registra modo de recebimento
										$smarty->assign('banco', $_POST['banco']);

									}
								}
								else{
									$err[] = $conf['listar_conteudo_retorno'];
								}
								
								unset($_POST['filial']);
							}

						}
						elseif(isset($_POST['Confirmar'])){

							/**
							 * verifica se o array $_POST['array_movimentos'] foi fornecido
							 * se esse array não existir significa que nenhum movimento
							 * foi selecionado para baixa
							 */
							if(isset($_POST['array_movimentos'])){

								/// array com contas a receber que foram mostradas na tela 
								$contas_pagas = $_SESSION['movimentos_retorno'];
	
								/// array com IDs das contas a receber que o usuário selecionou na tela
								/// para registrar pagamento
								$contas_incluir_pagamento = array_keys($_POST['array_movimentos']);
								
								/// array com IDs de todas as contas a receber que foram mostradas na tela
								$contas_pagas_todas = array_keys($contas_pagas);
	
								/// verifica se há alguma conta a receber que o usuário não incluiu no formulário
								$contas_nao_incluir = array_diff($contas_pagas_todas, $contas_incluir_pagamento);
								
								/// Retira do array com todas as contas a receber as contas
								/// que não foram selecionadas no formulário
								while(key($contas_nao_incluir) !== null){
									
									$id_conta = current($contas_nao_incluir);
									
									unset($contas_pagas[$id_conta]);
									
									next($contas_nao_incluir);
								}
								
								if(!empty($contas_pagas)){
									
									$pagamento_registrado = $movimento->registraPagamento($contas_pagas);
									
									if($pagamento_registrado){
										$flags['sucesso'] = $conf['contas_pagas_registradas'];
									}
									else{
										$err = $movimento->err;
									}
										
									unset($_SESSION['contas_receber_retorno']);
									unset($_SESSION['tipo_arquivo_retorno']);
								}
							}
							else{
								$err[] = $erro_validacao['nenhum_movimento_selecionado'];
								$smarty->assign('contas_pagas',$_SESSION['contas_receber_retorno']);
							}
						}
						elseif(isset($_POST['Cancelar'])){
						
							/// retira da sessão os dados de contas a receber que foram encontrados
							/// anteriormente
							unset($_SESSION['movimentos_retorno']);
							unset($_SESSION['tipo_arquivo_retorno']);
						}
						
					break;

          			
				}
      		}
      		

      		// Forma Array de intruções de preenchimento
      		$intrucoes_preenchimento = array();
      		if ($flags['action'] == "adicionar") {
      			$intrucoes_preenchimento[] = "Os campos em <span class=req>vermelho</span> s&atilde;o obrigat&oacute;rios.";
      		}
      		
      		// Formata a mensagem para ser exibida
      		$flags['intrucoes_preenchimento'] = $form->FormataMensagemAjuda($intrucoes_preenchimento);
      		
      		/// define dados da filial logada
      		if(!isset($_SESSION['idfilial_usuario']) || !isset($_SESSION['nomefilial_usuario'])){
      			// busca os dados da filial
      			$info_filial = $filial->getById($_SESSION['idfilial_usuario']);
      		}
      		else{
      			$info_filial = array('idfilial' => $_SESSION['idfilial_usuario'],
      					'nome_filial' => $_SESSION['nomefilial_usuario']);
      		}
      		
      		$smarty->assign("info_filial", $info_filial);

      		$list_permissao = $auth->check_priv($conf['priv']);
      		$smarty->assign("list_permissao",$list_permissao);

      		$smarty->assign("form", $form);
      		$smarty->assign("flags", $flags);
      		
  		}
  	
    	// seta erros
    	$smarty->assign("err", $err);
	}

  	$smarty->display("adm_retorno.tpl");
  	
?>
