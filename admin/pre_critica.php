<?php

	//inclusão de bibliotecas
	require_once("../common/lib/conf.inc.php");
	require_once("../common/lib/db.inc.php");
	require_once("../common/lib/auth.inc.php");
	require_once("../common/lib/form.inc.php");
	require_once("../common/lib/Smarty/Smarty.class.php");

	require_once '../entidades/movimento.php';

	// configurações anotionais
  	$conf['area'] = "Pr&eacute-cr&iacute;tica"; // área

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
			      				'id_banco' => array('CE'),
			      				'nome_banco' => array('Caixa Econ&ocirc;mica Federal')
			      		);
			      		$smarty->assign('lista_bancos', $lista_bancos);

					
						if(isset($_POST['Adicionar'])){

							/**
							 * Verificação de dados fornecidos no formulário
							 */

							$form->validateUpload($_FILES['arquivo_pre_critica']);

							$form->chk_empty($_POST['banco'], true, "Banco");
							
							$err = $form->err;
							
							if(count($err) == 0){
								
								/// coloca o conteúdo do arquivo em um array
								$conteudo_arquivo = file($_FILES['arquivo_pre_critica']['tmp_name']);
								
								$analise_pre_critica = $movimento->interpretaPreCritica($_POST['banco'],
																				$_SESSION['idfilial_usuario'],
																				  $conteudo_arquivo);

								if($analise_pre_critica){

									if($analise_pre_critica['erro']){
										$err[] = $erro_validacao['arquivo_pre_critica'];
									}
									else{

										if(!empty($analise_pre_critica['resultado'])){

											/// Se houver chave com ID zero significa que houve uma mensagem para 
											/// todos os movimentos e nesse caso apenas a mensagem é mostrada, sem
											/// a tabela para selcionar movimentos que devem ser reassociados
											if(isset($analise_pre_critica['resultado'][0]['movimento'])){

												$smarty->assign('mostra_movimentos', false);
											}
											else{
												$smarty->assign('mostra_movimentos', true);
											}

											$smarty->assign('resultado',$analise_pre_critica['resultado']);

											/// armazena os dados na sessão para não precisar fazer consulta novamente
											$_SESSION['movimentos_pre_critica'] = $analise_pre_critica;
											$_SESSION['tipo_arquivo_pre_critica'] = $_POST['banco'];
											
											switch($_POST['banco']){
												case 'CE':
													$smarty->assign('nome_banco','Caixa Econ&ocirc;mica Federal');
													break;
											}
											
											/// registra modo de recebimento
											$smarty->assign('banco', $_POST['banco']);
										}
										else{
											$err[] = $conf['listar_movimento_remessa'];
										}
									}
								}
								else{
									$err[] = $falha['listar_conteudo_pre_critica'];
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
							if(isset($_POST['array_movimentos']) && !empty($_POST['array_movimentos'])){

								/// array com movimentos que foram mostrados na tela
								// $analise_pre_critica = $_SESSION['movimentos_pre_critica'];
								// $analise_pre_critica = array_keys($analise_pre_critica['resultado']);
	
								/// array com IDs dos movimentos que o usuário selecionou na tela
								/// para desassociar da remessa
								$movimentos_desassociar = array_keys($_POST['array_movimentos']);
								
								if(!empty($movimentos_desassociar)){

									$movimentos_desassociados = $movimento->desassociaRemessa($movimentos_desassociar);

									if($movimentos_desassociados){
										$flags['sucesso'] = $conf['analise_pre_critica_desassociada'];
									}
									else{
										$err = $movimento->err;
									}
										
									unset($_SESSION['movimentos_pre_critica']);
									unset($_SESSION['tipo_arquivo_pre_critica']);
								}
							}
							else{
								$err[] = $erro_validacao['nenhum_movimento_selecionado'];
								$smarty->assign('analise_pre_critica',$_SESSION['movimentos_pre_critica']);
							}
						}
						elseif(isset($_POST['Cancelar'])){
						
							/// retira da sessão os dados dos movimentos que foram encontrados
							/// anteriormente
							unset($_SESSION['movimentos_pre_critica']);
							unset($_SESSION['tipo_arquivo_pre_critica']);
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

  	$smarty->display("adm_pre_critica.tpl");
  	
?>
