<?php

	//inclus�o de bibliotecas
	require_once("../common/lib/conf.inc.php");
	require_once("../common/lib/db.inc.php");
	require_once("../common/lib/auth.inc.php");
	require_once("../common/lib/form.inc.php");
	require_once("../common/lib/rotinas.inc.php");
	require_once("../common/lib/Smarty/Smarty.class.php");
	require_once("../common/lib/xajax/xajax.inc.php");

	require_once("../entidades/comunicado.php");
	require_once("../entidades/comunicado_condominio.php");
	require_once("../entidades/cliente_condominio.php");
	
	// configura��es anotionais
	$conf['area'] = "Comunicados"; // �rea
	
	//configura��o de estilo
	$conf['style'] = "full";
	
	$form = new form();
	
	// inicializa autentica��o
	$auth = new auth();
	

	// inicializa templating
	$smarty = new Smarty;

	// configura diret�rios
	$smarty->template_dir = "../common/tpl";
	$smarty->compile_dir = "../common/tpl_c";

	// seta configura��es
	$smarty->assign("conf", $conf);

	// a��o selecionada
	$flags['action'] = $_GET['ac'];
	if ($flags['action'] == "")
    	$flags['action'] = "listar";


	// cria o objeto xajax
	$xajax = new xajax();
	
	// verifica requisi��o de logout
	if ($flags['action'] == "logout") {
    	$auth->logout();
	} else {

    	// inicializa vetor de erros
    	$err = array();

	    // verifica sess�o
    	if (!$auth->check_user()) {
        	// verifica requisi��o de login
        	if ($_POST['usr_chk']) {
            	// verifica login
            	if (!$auth->login($_POST['usr_log'], $_POST['usr_sen'])) {
                	$err = $auth->err;
            	}
        	} else {
            	$err = $auth->err;
        	}
    	}

    	// conte�do
    	if ($auth->check_user()) {
    		
        	// verifica privil�gios
        	if (!$auth->check_priv($conf['priv'])) {
        		
            	$err = $auth->err;
        	} else {
        		
            	// libera conte�do
            	$flags['okay'] = 1;

	            //inicializa classe
    	        $comunicado = new comunicado();
    	        $comunicado_condominio = new comunicado_condominio();
    	        $cliente_condominio = new cliente_condominio();

        	    // inicializa banco de dados
            	$db = new db();

	            //incializa classe para valida��o de formul�rio
    	        $form = new form();

        	    $list = $auth->check_priv($conf['priv']);
            	$aux = $flags['action'];
            	if ($list[$aux] != '1' && $_SESSION['usr_cargo'] != "") {
                	$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"" . $conf['addr'] . "/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial.";
                	$flags['action'] = "";
            	}


            	switch ($flags['action']) {

	                // a��o: adicionar <<<<<<<<<<
    	            case "adicionar":
    	            	
	                    if ($_POST['for_chk']) {
	                    	
	                    	$_POST['titulo'] = trim($_POST['titulo']);
	                    	$_POST['descricao'] = trim($_POST['descricao']);
	
	                        $form->chk_empty($_POST['notificar_email'], '1', 'Notificar condom�nios por email?');
	                        
	                        if(!isset($_POST['condominios'])){
	                        	$form->err[] = 'Selecione pelo menos um condom�nio.';
	                        }
	                        
	                        $err = $form->err;
	
	                        if (count($err) == 0) {
	                        	
	                            //grava o registro no banco de dados
	                            $id_comunicado = $comunicado->set($_POST);
	                            
	                            //obt�m os erros que ocorreram no cadastro
	                            $err = $comunicado->err;
	
	                            //se n�o ocorreram erros
	                            if (count($err) == 0) {
	                            	
	                            	$comunicado->incluiArquivosComunicado($id_comunicado, $_FILES);
	                            	
	                            	if($comunicado->err){
	                            		$err += $comunicado->err;
	                            	}
	                            	
	                            	if(!$comunicado_condominio->associaComunicadoCondominios($id_comunicado, $_POST['condominios'])){
	                            		$err[] = $falha['associar_comunicado'];
	                            	}
	                            	else{
	                            		if($_POST['notificar_email'] == 'S'){
	                            			$comunicado_condominio->enviaEmailCondominios($_POST['titulo'], date('d/m/Y'), $_POST['condominios']);
	                            		}
	                            	}
	                            	
	                                $flags['sucesso'] = $conf['inserir'];
	
	                                //limpa o $flags.action para que seja exibida a listagem
	                                $flags['action'] = "listar";
	                            }
	                        }
	                    }
	
	                    
	                    if(!isset($_POST['notificar_email'])){
	                    	$_POST['notificar_email'] = 'S';
	                    }
	                    
	
	                  	/// monta lista de condom�nios para mostrar no select  
	                    $condominios = $cliente_condominio->make_list_select_cliente_condominio();
	                    $smarty->assign('condominios',$condominios);

                    break;
                    
                    
    	            case 'editar':
    	            	
    	            	if($_POST['for_chk']) {
    	            		
    	            		$info = $_POST;
    	            		
    	            		$_POST['littitulo'] = trim($_POST['littitulo']);
    	            		$_POST['litdescricao'] = trim($_POST['litdescricao']);
    	            		
    	            		if(!isset($_POST['condominios'])){
    	            			$form->err[] = 'Selecione pelo menos um condom�nio.';
    	            		}
    	            	
    	            		if(count($err) == 0) {
    	            	
    	            			$_POST['litdescricao'] = nl2br($_POST['litdescricao']);
    	            	
    	            			$comunicado->update($_GET['idcomunicado'], $_POST);
    	            	
    	            			//obt�m erros
    	            			$err = $plano->err;
    	            	
    	            			//se n�o ocorreram erros
    	            			if(count($err) == 0) {
    	            				
    	            				$comunicado->incluiArquivosComunicado($_GET['idcomunicado'], $_FILES);
    	            				
    	            				if($comunicado->err){
    	            					$err += $comunicado->err;
    	            				}
    	            				
    	            				if(!$comunicado_condominio->associaComunicadoCondominios($_GET['idcomunicado'], $_POST['condominios'], true)){
    	            					$err[] = $falha['associar_comunicado'];
    	            				}
    	            				 
    	            				$flags['sucesso'] = $conf['alterar'];
    	            	
    	            				//limpa o $flags.action para que seja exibida a listagem
    	            				$flags['action'] = "listar";
    	            			}
    	            		}
    	            	}
    	            	else {
    	            	
    	            		// busca dados do comunicado
    	            		$info = $comunicado->getById($_GET['idcomunicado']);
    	            		$info['littitulo'] = $info['titulo'];
    	            		$info['litdescricao'] = strip_tags($info['descricao']);
    	            	
    	            		//obt�m os erros
    	            		$err = $plano->err;
    	            	}
    	            	
    	            	//passa os dados para o template
    	            	$smarty->assign("info", $info);
    	            	
    	            	
	                  	/// monta lista de condom�nios para mostrar no select  
	                    $condominios = $cliente_condominio->make_list_select_cliente_condominio();
	                    $smarty->assign('condominios',$condominios);

	                    
	                    /// armazena condom�nios associados para mostrar selecionados no select
	                    if(!isset($_POST['condominios'])){
	                    	$condominios_associados = $comunicado_condominio->buscaCondominiosAssociados($_GET['idcomunicado']);
	                    }
	                    else{
	                    	$condominios_associados = $_POST['condominios']; 
	                    }
	                    
	                    $smarty->assign('condominios_associados',$condominios_associados);
	                    
	                    
	                    /// busca arquivos associados ao comunicado
	                    $arquivos_comunicado = $comunicado->buscaArquivosComunicado($_GET['idcomunicado']);
	                    $smarty->assign('arquivos_comunicado',$arquivos_comunicado);
    	            	    	            	
    	            break;


                	//listagem dos registros
                	case "listar":
                		
                		//obt�m qual p�gina da listagem deseja exibir
                		$pg = intval(trim($_GET['pg']));
                			
                		//se n�o foi passada a p�gina como par�metro, faz p�gina default igual � p�gina 0
                		if(!$pg) $pg = 0;
                		
                		
                		/// verifica se o formul�rio foi submetido ou se h� pesquisa na sess�o
                		if(!isset($_POST['Pesquisar']) && !isset($_POST['Limpar']) && isset($_SESSION['pesquisa_comunicados'])){
                			$_POST = $_SESSION['pesquisa_comunicados'];
                		}

						if(isset($_POST['Limpar'])){
							
							/// bot�o Limpar pesquisa acionado
							unset($_POST);
							unset($_SESSION['pesquisa_comunicados']);
						}
							
                		if(isset($_POST['Pesquisar']) && isset($_POST['idcliente'])){
                			
                			/// bot�o Pesquisar acionado
                			
                			$_SESSION['pesquisa_comunicados'] = $_POST;
                			
                			$comunicados_associados = $comunicado_condominio->buscaComunicadosCondominioPaginado($_POST['idcliente'],$pg, $conf['rppg']);
                			
                			if($comunicados_associados){
                				$smarty->assign('comunicados',$comunicados_associados);
                			}
                			else{
                				$smarty->assign('nenhum_resultado',true);
                			}
                		}
                		
                	break;


	                // deleta um registro do sistema
    	            case "excluir":

	                    //verifica se foi pedido a dele��o
	                    if ($_POST['for_chk']) {
	
	                        // deleta o registro
	                        $comunicado->delete($_GET['idcomunicado']);
	
	                        //obt�m erros
	                        $err = $comunicado->err;
	
	                        //se n�o ocorreram erros
	                        if (count($err) == 0) {
	                            $flags['sucesso'] = $conf['excluir'];
	                        }
	
	                        //limpa o $flags.action para que seja exibida a listagem
	                        $flags['action'] = "listar";
	
	                    }

                    break;
                    
                    
    	            case 'visualizar_anexo':

    	            	/**
    	            	 * Mostra tela para download do arquivo
    	            	 */
    	            	
    	            	$arquivo = $conf['path'] . '/common/comunicados/' . $_GET['id_comunicado'] . '_' . $_GET['arquivo'];
    	            	
    	            	$finfo = finfo_open(FILEINFO_MIME_TYPE);
    	            	$tipo_arquivo = finfo_file($finfo,$arquivo);
    	            	
    	            	header('Content-type: ' . $tipo_arquivo);
    	            	header('Content-Disposition: attachment; filename="' . $_GET['arquivo'] . '"');
    	            	readfile($arquivo);
    	            	
    	            	exit();
    	            	
    	            break;
	            }
    	    }
    	}

	    // seta erros
    	$smarty->assign("err", $err);
    	
    	$list_permissao = $auth->check_priv($conf['priv']);
    	$smarty->assign("list_permissao",$list_permissao);
    	 
	}

	$smarty->assign("form", $form);
	$smarty->assign("flags", $flags);

	$smarty->display("adm_comunicado.tpl");
?>
