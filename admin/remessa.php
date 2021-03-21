<?php

	//inclusão de bibliotecas
	require_once("../common/lib/conf.inc.php");
	require_once("../common/lib/db.inc.php");
	require_once("../common/lib/auth.inc.php");
	require_once("../common/lib/form.inc.php");
	require_once("../common/lib/rotinas.inc.php");
	require_once("../common/lib/Smarty/Smarty.class.php");
	require_once("../common/lib/xajax/xajax.inc.php");

	require_once '../entidades/arquivo_remessa.php';
	require_once '../entidades/movimento.php';

	// configurações anotionais
  	$conf['area'] = "Remessa"; // área

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

        		$arquivo_remessa = new arquivo_remessa();
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

						if(isset($_POST['Adicionar'])){
							
							/**
							 * Verificação de dados fornecidos no formulário
							 */
							
							$form->chk_empty($_POST['modo_recebimento'], true, "Banco");
							
							$err = $form->err;
							 
							if($_POST['data_movimento_de'] && $_POST['data_movimento_ate']){
								
								/// verifica se data de venda "de" não é posterior à data de venda "até"
								
								$verifica_data = $form->verificaSeDataNaoPosterior($_POST['data_movimento_de'], 
																					$_POST['data_movimento_ate']);
								
								if(!$verifica_data){
									$err[] = sprintf($erro_validacao['data_nao_posterior'], 
													'de gera&ccedil;&atilde;o "De"', 
													'de gera&ccedil;&atilde;o "Até"');
								}
							}
							
							if($_POST['data_vencimento_de'] && $_POST['data_vencimento_ate']){
								
								/// verifica se data de vencimento "de" não é posterior à data de vencimento "até"
								
								$verifica_data = $form->verificaSeDataNaoPosterior($_POST['data_vencimento_de'], 
																	$_POST['data_vencimento_ate']);
								if(!$verifica_data){
									$err[] = sprintf($erro_validacao['data_nao_posterior'], 
													'de vencimento "De"', 
													'de vencimento "Até"');
								}
							}
				
							if(count($err) == 0){
								
								/// busca movimentos que devem ser enviados para o banco selecionado na tela
								$resultado = $movimento->buscaMovimentosRemessa($_POST);

								if($resultado['movimentos']){

									$smarty->assign('movimentos_remessa',$resultado['movimentos']);

									/// armazena os dados na sessão para não precisar fazer consulta novamente
									$_SESSION['movimentos_remessa'] = $resultado['movimentos'];
									$_SESSION['tipo_arquivo_remessa'] = $_POST['modo_recebimento'];
									
									switch($_POST['modo_recebimento']){
										case 'CE':
											$smarty->assign('nome_banco','Caixa Econ&ocirc;mica Federal');
											break;
										case 'I':
											$smarty->assign('nome_banco','Ita&uacute;');											
											break;
										case 'IE':
											$smarty->assign('nome_banco','Ita&uacute; Estrela da Mata');
											break;											
										case 'BR':
											$smarty->assign('nome_banco','Bradesco Mila Center');
											break;
										case 'BT':
											$smarty->assign('nome_banco','Bradesco SOS Prestadora');
											break;
										case 'S':
											$smarty->assign('nome_banco','Sicoob');
											break;

									}
								}

								if($resultado['erro_endereco']){

									$erro_endereco = '<p>Os seguintes movimentos n&atilde;o possuem endere&ccedil;o completo e ' . 
													'n&atilde;o podem ser inclu&iacute;dos na remessa:</p>';

									foreach($resultado['erro_endereco'] as $erro){
										$erro_endereco .= '<br />' . $erro;
									}

									$smarty->assign('erro_endereco', $erro_endereco);

								}

								if(!$resultado['movimentos'] && !$resultado['erro_endereco']){
									$err[] = $conf['listar_movimento_remessa'];
								}
								
								unset($_POST['filial']);
							}
						}
						elseif(isset($_POST['Confirmar'])){

							/**
							 * verifica se o array $_POST['movimento'] foi fornecido
							 * se esse array não existir significa que nenhum movimento
							 * foi selecionado para geração do arquivo de remessa
							 */
							if(isset($_POST['movimento'])){

								/// array com movimentos que foram encontrados na pesquisa 
								$movimentos_remessa = $_SESSION['movimentos_remessa'];
	
								/// array com IDs dos movimentos que o usuário selecionou na tela
								/// para gerar o arquivo
								$movimentos_incluir_arquivo = array_keys($_POST['movimento']);

								/// array com IDs de todas as contas a receber
								$movimentos_todos = array_keys($movimentos_remessa);

								/// verifica se há alguma conta a receber que o usuário não incluiu no formulário
								$movimentos_nao_incluir = array_diff($movimentos_todos, $movimentos_incluir_arquivo);
	
								/// Retira do array com todos os movimentos os
								/// que não foram selecionados no formulário
								while(key($movimentos_nao_incluir) !== null){
									
									$idmovimento = current($movimentos_nao_incluir);
									
									unset($movimentos_remessa[$idmovimento]);
									
									next($movimentos_nao_incluir);
								}

								$arquivo_gerado = $arquivo_remessa->geraArquivoRemessa($_SESSION['tipo_arquivo_remessa'], 
																 			 	$_SESSION['idfilial_usuario'], 
															 					$movimentos_remessa);

								if($arquivo_gerado){
									
									$flags['sucesso'] = $conf['gerar_arquivo_remessa'];
									$flags['action'] = "listar";
								}
								else{
									$err = $arquivo_remessa->err;	
								}
									
								unset($_SESSION['movimentos_remessa']);
								unset($_SESSION['tipo_arquivo_remessa']);
							}
							else{
								$err[] = $erro_validacao['nenhum_movimento_selecionado'];
								$smarty->assign('movimentos_remessa',$_SESSION['movimentos_remessa']);
							}
						}
						elseif(isset($_POST['Cancelar'])){
						
							/// retira da sessão os dados de contas a receber que foram encontrados
							/// anteriormente
							unset($_SESSION['movimentos_remessa']);
							unset($_SESSION['tipo_arquivo_remessa']);
						}

					break;


					//listagem dos registros
          			case "listar":
          				listar();						
          			break;
          			
          			case 'detalhar_arquivo_remessa':

          				$_GET['idarquivo_remessa'] = intval($_GET['idarquivo_remessa']);

          				if($_GET['idarquivo_remessa']){
          					$movimentos_remessa = $movimento->buscaConteudoArquivoRemessa($_GET['idarquivo_remessa']);
          					$smarty->assign('movimentos_remessa',$movimentos_remessa);
          				}
          				
          			break;
          			
          			case 'baixar_arquivo_remessa':
          				
          				if($_GET['idarquivo_remessa']){
          					$dados_arquivo = $arquivo_remessa->getById($_GET['idarquivo_remessa']);

          					if(strpos($dados_arquivo['nome_arquivo'],'.') === false){
          						$arquivo = $dados_arquivo['nome_arquivo'].'.txt';
          					}
          					else{
          						$arquivo = $dados_arquivo['nome_arquivo'];
          					}

          					//Envia as informações do header para o browser forçando o download
          					header('Content-Type: application/force-download; Charset=UTF-8');
          					header('Content-Disposition: attachment; filename='.$arquivo );
          					echo $dados_arquivo['conteudo'];
          					
          					exit;
          					 
          				}
          			break;

          			case 'desfazer_remessa':

          				/**
          				 * Desassocia movimentos de um arquivo de remessa e apaga o registro do arquivo
          				 */

          				if(isset($_GET['idarquivo_remessa'])){
							$_GET['idarquivo_remessa'] = intval($_GET['idarquivo_remessa']);
						}
						else{
							$_GET['idarquivo_remessa'] = 0;
						}

						if($_GET['idarquivo_remessa']){

							if($arquivo_remessa->desfazRemessa($_GET['idarquivo_remessa'])){
								$flags['sucesso'] = sprintf($conf['desfazer_remessa'],$_GET['nome_remessa']);;
							}
							else{
								$err = $arquivo_remessa->err;
							}
						}

						listar();

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
      		
      		$lista_bancos_remessa = array(
      				'modo_recebimento' => array('CE','BR','BT', 'I', 'IE', 'S'),
      				'nome_banco' => array('Caixa Econ&ocirc;mica Federal','Bradesco Mila Center', 'Bradesco SOS Prestadora',  'Ita&uacute;', 'Ita&uacute; Estrela da Mata', 'Sicoob')
      		);
      		$smarty->assign('lista_bancos_remessa', $lista_bancos_remessa);

      		$list_permissao = $auth->check_priv($conf['priv']);
      		$smarty->assign("list_permissao",$list_permissao);

      		$smarty->assign("form", $form);
      		$smarty->assign("flags", $flags);
  		}
  	
    	// seta erros
    	$smarty->assign("err", $err);
	}

  	$smarty->display("adm_remessa.tpl");


function listar(){

	global $flags, $err, $form, $smarty, $arquivo_remessa;

	$flags['action'] = 'listar';

	if(isset($_POST['Limpar'])){
		unset($_SESSION['busca_remessa']);
		unset($_POST);
	}

	if(isset($_POST['Buscar'])){
		$_SESSION['busca_remessa'] = $_POST;
	}
	elseif(isset($_SESSION['busca_remessa'])){
		$_POST = $_SESSION['busca_remessa'];
	}
	
	if(isset($_POST['Buscar'])){
			
		/**
		 * Verificação de dados fornecidos no formulário
		 */
			
		if($_POST['data_geracao_de'] && $_POST['data_geracao_ate']){

			/// verifica se data de geração "de" não é posterior à data de venda "até"

			$verifica_data = $form->verificaSeDataNaoPosterior($_POST['data_geracao_de'],
																$_POST['data_geracao_ate']);

			if(!$verifica_data){
				$err[] = sprintf($erro_validacao['data_nao_posterior'],
						'de gera&ccedil;&atilde;o "De"',
						'de gera&ccedil;&atilde;o "Até"');
			}
		}
			
		if(count($err) == 0){
			$dados_arquivos = $arquivo_remessa->pesquisaArquivosRemessa($_POST);
			
			if(is_array($dados_arquivos)){
				$smarty->assign('dados_arquivos',$dados_arquivos);
			}
			else{
				$err = $arquivo_remessa->err;
			}
		}
	}

}
  	
?>