<?php

	//inclus�o de bibliotecas
	require_once("../common/lib/conf.inc.php");
	require_once("../common/lib/db.inc.php");
	require_once("../common/lib/auth.inc.php");
	require_once("../common/lib/form.inc.php");
	require_once("../common/lib/rotinas.inc.php");
	require_once("../common/lib/Smarty/Smarty.class.php");
	require_once("../common/lib/xajax/xajax.inc.php");

	require_once("../entidades/apartamento.php");
	require_once("../entidades/cliente_condominio.php");
	require_once("../entidades/cliente.php");
	require_once("../entidades/demonstrativo_apartamento.php");	
  	require_once("../entidades/movimento.php");
	
	require_once("cliente_ajax.php");
	require_once("apartamento_ajax.php");

  	// configura��es anotionais
  	$conf['area'] = "Apartamentos"; // �rea


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
  
  	//inicializa classe
	$apartamento = new apartamento();
	$cliente_condominio = new cliente_condominio();
	$cliente = new cliente();
	$demonstrativo_apartamento = new demonstrativo_apartamento();
  	$movimento = new movimento();

  	// cria o objeto xajax
	$xajax = new xajax();

	// registra todas as fun��es que ser�o usadas
	$xajax->registerFunction("Verifica_Campos_Apartamento_AJAX");
	$xajax->registerFunction("Gerar_Boletos_Condominos_AJAX");
	$xajax->registerFunction("chk_Condominio_Gerado_AJAX");     
	$xajax->registerFunction("Ver_Boletos_Condominos_AJAX");


	
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
      		elseif( (!$apartamento->chk_condominio_selecionado()) && ($flags['action'] != 'selecionar_condominio') ){
      			$err = $apartamento->err;
      		}
	  		else{
        		// libera conte�do
        		$flags['okay'] = 1;
											
        		// inicializa banco de dados
        		$db = new db();

        		//incializa classe para valida��o de formul�rio
        		$form = new form();
        
    			$list = $auth->check_priv($conf['priv']);
    			$aux = $flags['action'];
    			if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {
    				$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";
	    		}


    		    switch($flags['action']) {

					// busca gen�rica
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

						  	//obt�m qual p�gina da listagem deseja exibir
						  	$pg = intval(trim($_GET['pg']));

						  	//se n�o foi passada a p�gina como par�metro, faz p�gina default igual � p�gina 0
						  	if(!$pg) $pg = 0;

						  	//lista os registros
							$list = $apartamento->Busca_Generica($pg, $flags['rpp'], $flags['busca'], "", "ac=busca_generica&busca=".$flags['busca']."&rpp=".$flags['rpp']);

							//pega os erros
							$err = $apartamento->err;

							//passa a listagem para o template
							$smarty->assign("list", $list);

						}

						if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];

          				break;


					// busca parametrizada
          			case "busca_parametrizada":

            			if ( ($_POST['for_chk']) || ($_GET['rpp'] != "") ) {

            				$flags['fez_busca'] = 1;

							if ($_POST['for_chk']) {
								$flags['cliente'] = $_POST['cliente'];
								$flags['ramo_atividade'] = $_POST['ramo_atividade'];
								$flags['cnpj_cliente'] = $_POST['cnpj_cliente'];
								$flags['email_cliente'] = $_POST['email_cliente'];
								$flags['rpp'] = $_POST['rpp'];
							}
							else {
								$flags['cliente'] = $_GET['cliente'];
								$flags['ramo_atividade'] = $_GET['ramo_atividade'];
								$flags['cnpj_cliente'] = $_GET['cnpj_cliente'];
								$flags['email_cliente'] = $_GET['email_cliente'];
								$flags['rpp'] = $_GET['rpp'];
							}

							$parametros_get = "&cliente=" . $flags['cliente'] . "&ramo_atividade=" . $flags['ramo_atividade'] . "&cnpj_cliente=" . $flags['cnpj_cliente'] . "&email_cliente=" . $flags['email_cliente'];


							$filtro_where = "";
							if ($flags['cliente'] != "") $filtro_where .= " UPPER(CLI.nome_cliente) LIKE UPPER('%" . $flags['cliente'] . "%') AND ";
							if ($flags['ramo_atividade'] != "") $filtro_where .= " ( (UPPER(RAT.descricao_atividade) LIKE UPPER('%" . $flags['ramo_atividade'] . "%'))) AND ";
							if ($flags['cnpj_cliente'] != "") $filtro_where .= " ( (UPPER(JCLI.cnpj_cliente) LIKE UPPER('%" . $flags['cnpj_cliente'] . "%')) ) AND ";
							if ($flags['email_cliente'] != "") $filtro_where .= " UPPER(CLI.email_cliente) LIKE UPPER('%" . $flags['email_cliente'] . "%') AND ";

							$filtro_where = substr($filtro_where,0,strlen($filtro_where)-4);


							if ($_GET['target'] == "full") $flags['rpp'] = 9999999;


						  	//obt�m qual p�gina da listagem deseja exibir
						  	$pg = intval(trim($_GET['pg']));

						  	//se n�o foi passada a p�gina como par�metro, faz p�gina default igual � p�gina 0
						  	if(!$pg) $pg = 0;

						  	//lista os registros
							$list = $apartamento->Busca_Parametrizada($pg, $flags['rpp'], $filtro_where, "", "ac=busca_parametrizada$parametros_get&rpp=".$flags['rpp']);

							//pega os erros
							$err = $apartamento->err;

							//passa a listagem para o template
							$smarty->assign("list", $list);

						}

						if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];

          				break;


          			// a��o: adicionar <<<<<<<<<<
          			case "adicionar":

			            if($_POST['for_chk']) {
      				
		      				$form->chk_empty($_POST['apto'], 1, 'N�mero');
      						$form->chk_empty($_POST['situacao'], 1, 'Situa��o');
      				
				
						    $err = $form->err;

	         				if(count($err) == 0) {
    
		    					$_POST['apto'] = trim($_POST['apto']);
		    					$_POST['observacao'] = nl2br($_POST['observacao']);
				          		$_POST['custoFixo'] = str_replace(",",".",$_POST['custoFixo']);
		    					$_POST['fundoReserva'] = str_replace(",",".",$_POST['fundoReserva']);
		    					$_POST['fracaoIdeal'] = str_replace(",",".",$_POST['fracaoIdeal']);
		    					$_POST['idcliente'] = $_SESSION['idcliente'];
    
		    					if ($_POST['custoFixo'] == "") $_POST['custoFixo'] = "NULL";
		    					if ($_POST['fundoReserva'] == "") $_POST['fundoReserva'] = "NULL";
		    					if ($_POST['fracaoIdeal'] == "") $_POST['fracaoIdeal'] = "NULL";
		    					
		    					if ( ($_POST['custoFixo'] != "NULL") && ($_POST['fracaoIdeal'] != "NULL") ){
		    						$err[] = 'Os campos "Fra��o Ideal" e "Custo Fixo" possuem valores. Deve-se escolher apenas um destes campos para ser preenchido.';
		    					}	
    					
    					
		    					//Se n�o houver propriet�rio selecionado, cadastra um novo com base nos dados informados
		    					if( empty($_POST['idproprietario']) && $_POST['situacao'] != 'V' && trim($_POST['nome_proprietario']) != '' ){
			    					
			  						$novo_cliente['nome_cliente'] = trim($_POST['nome_proprietario']);
			  						$novo_cliente['tel_residencial_cliente'] =  $form->FormataTelefoneParaInserir($_POST['tel_residencial_proprietario_ddd'], $_POST['tel_residencial_proprietario']);
			  						$novo_cliente['celular_cliente'] =  $form->FormataTelefoneParaInserir($_POST['celular_proprietario_ddd'], $_POST['celular_proprietario']);
			  						$novo_cliente['telefone_cliente'] =  $form->FormataTelefoneParaInserir($_POST['telefone_proprietario_ddd'], $_POST['telefone_proprietario']);
			  						$novo_cliente['cpf_cliente'] = $form->FormataCPFParaInserir($_POST['cpf_proprietario']);						
			  						
			  						$novo_cliente['idcliente'] = $cliente->set($novo_cliente);
			  						$cliente_fisico->set($novo_cliente);
			  						
			  						$_POST['idproprietario'] = $novo_cliente['idcliente'];
			  						
			  						//Limpa a vari�vel auxiliar
			  						$novo_cliente = NULL;
								}
					
					
								//Se n�o houver morador selecionado, cadastra um novo com base nos dados informados
								if( empty($_POST['idmorador']) && $_POST['situacao'] != 'V' && trim($_POST['nome_morador']) != ''){
						
									$novo_cliente['nome_cliente'] = trim($_POST['nome_morador']);
									$novo_cliente['tel_residencial_cliente'] =  $form->FormataTelefoneParaInserir($_POST['tel_residencial_morador_ddd'], $_POST['tel_residencial_morador']);
									$novo_cliente['celular_cliente'] =  $form->FormataTelefoneParaInserir($_POST['celular_morador_ddd'], $_POST['celular_morador']);
									$novo_cliente['telefone_cliente'] =  $form->FormataTelefoneParaInserir($_POST['telefone_morador_ddd'], $_POST['telefone_morador']);
									$novo_cliente['cpf_cliente'] = $form->FormataCPFParaInserir($_POST['cpf_morador']);						
									$novo_cliente['idcliente'] = $cliente->set($novo_cliente);
						            $cliente_fisico->set($novo_cliente);
							
									$_POST['idmorador'] = $novo_cliente['idcliente'];
							
									//Limpa a vari�vel auxiliar
									$novo_cliente = NULL;
							
								}//------------------------------------------------------
						
						
								//grava o registro no banco de dados
								$idapartamento = $apartamento->set($_POST);

								//obt�m os erros que ocorreram no cadastro
								$err = $apartamento->err;

								//se n�o ocorreram erros
								if(count($err) == 0) {

									if(isset($_POST['demonstrativo']) && $_POST['demonstrativo']){

										$demonstrativo_apartamento->set(
											array('demonstrativo' => $_POST['demonstrativo'], 
													'idapartamento' => $idapartamento));
									}

									$flags['sucesso'] = $conf['inserir'];

									//Envia para a tela de edi��o
									header('location:'.$conf['addr'].'/admin/apartamento.php?ac=listar&sucesso='.$conf['inserir']);

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
					  $filtro = 'WHERE APTO.idcliente = '. $_SESSION['idcliente'];
					  $list = $apartamento->make_list($pg, $conf['rppg'],$filtro);
						

						//pega os erros
						$err = $apartamento->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

					break;
      
                    case "gerar_boleto":
          
						//obt�m qual p�gina da listagem deseja exibir
						$pg = intval(trim($_GET['pg']));

					  	//se n�o foi passada a p�gina como par�metro, faz p�gina default igual � p�gina 0
					  	if(!$pg) $pg = 0;
						
					  
					  	//lista os registros
					  	$filtro = 'WHERE APTO.idcliente = '. $_SESSION['idcliente'];
					  	$list = $apartamento->make_list($pg, 1000,$filtro);
					  
					  	foreach($list as $k=>$apto){		  	
					 		$list[$k]['gerou_cond_mes'] = $apartamento->chk_taxa_condominio_gerada($apto['idapartamento'], date('m'),date('Y'));
					  	}
					  
					  	//Calcula a data de vencimento sugerida
					  	$dados_condominio = $cliente_condominio->getById($_SESSION['idcliente']);
        			  	if(!empty($dados_condominio['sugestaoVencimento'])){
					  		$dados_condominio['sugestaoVencimento'].= date('/m/Y');
					  	}
					  
					  	/// M�s e ano sugeridos para refer�ncia � gera��o
					  	$dados_condominio['sugestaoGeracao'] = date('m/Y');
					  
					  	//pega os erros
					  	$err = $apartamento->err;

					  	//passa a listagem para o template
					  	$smarty->assign("list", $list);
					  	$smarty->assign("dados_condominio", $dados_condominio);


					break;
          
          
			case "selecionar_condominio":

				//recupera os dados do cliente
				if ($_GET['idcliente']){
					$dados = $cliente->getById($_GET['idcliente']);

					//Grava na sess�o
					$_SESSION['idcliente'] = $dados['idcliente'];
					$_SESSION['nome_cliente'] = $dados['nome_cliente'];

					//Envia para a p�gina listar
					header('location:'.$conf['addr'].'/admin/apartamento.php?ac=listar');
				}
				
			break;
          
          
          // a��o: editar <<<<<<<<<<
			case "editar":

				if($_POST['for_chk']) {
					
					
					$info = $_POST;

					$info['idapartamento'] = $_GET['idapartamento'];

					$form->chk_empty($_POST['litapto'], 1, 'N�mero');
					$form->chk_empty($_POST['litsituacao'], 1, 'Situa��o');

					$err = $form->err;

          			if(count($err) == 0) {

						$_POST['litapto'] = trim($_POST['litapto']);
						$_POST['litobservacao'] = nl2br($_POST['litobservacao']);
						$_POST['numcustoFixo'] = str_replace(",",".",$_POST['numcustoFixo']);
						$_POST['numfracaoIdeal'] = str_replace(",",".",$_POST['numfracaoIdeal']);
						$_POST['numfundoReserva'] = str_replace(",",".",$_POST['numfundoReserva']);

						if ($_POST['numcustoFixo'] == "") $_POST['numcustoFixo'] = "NULL";
						if ($_POST['numfracaoIdeal'] == "") $_POST['numfracaoIdeal'] = "NULL";
						if ($_POST['numfundoReserva'] == "") $_POST['numfundoReserva'] = "NULL";
						
						$_POST['idcliente'] = $_SESSION['idcliente'];
						
						
						//Se n�o houver propriet�rio selecionado, cadastra um novo com base nos dados informados
						if( empty($_POST['idproprietario']) && $_POST['litsituacao'] != 'V' ){
						
							$novo_cliente['nome_cliente'] = trim($_POST['nome_proprietario']);
							$novo_cliente['tel_residencial_cliente'] =  $form->FormataTelefoneParaInserir($_POST['tel_residencial_proprietario_ddd'], $_POST['tel_residencial_proprietario']);
							$novo_cliente['celular_cliente'] =  $form->FormataTelefoneParaInserir($_POST['celular_proprietario_ddd'], $_POST['celular_proprietario']);
							$novo_cliente['telefone_cliente'] =  $form->FormataTelefoneParaInserir($_POST['telefone_proprietario_ddd'], $_POST['telefone_proprietario']);
							$novo_cliente['cpf_cliente'] = $form->FormataCPFParaInserir($_POST['cpf_proprietario']);						
							
							$novo_cliente['idcliente'] = $cliente->set($novo_cliente);
							$cliente_fisico->set($novo_cliente);
							
							$_POST['numidproprietario'] = $novo_cliente['idcliente'];
							
							//Limpa a vari�vel auxiliar
							$novo_cliente = NULL;
						}
						else{
							$_POST['numidproprietario'] = $_POST['idproprietario'];
						}
						//------------------------------------------------------
						
						
						//Se n�o houver morador selecionado, cadastra um novo com base nos dados informados
						if( empty($_POST['idmorador']) && $_POST['litsituacao'] != 'V' && trim($_POST['nome_morador']) != ''){
						
							$novo_cliente['nome_cliente'] = trim($_POST['nome_morador']);
							$novo_cliente['tel_residencial_cliente'] =  $form->FormataTelefoneParaInserir($_POST['tel_residencial_morador_ddd'], $_POST['tel_residencial_morador']);
							$novo_cliente['celular_cliente'] =  $form->FormataTelefoneParaInserir($_POST['celular_morador_ddd'], $_POST['celular_morador']);
							$novo_cliente['telefone_cliente'] =  $form->FormataTelefoneParaInserir($_POST['telefone_morador_ddd'], $_POST['telefone_morador']);
							$novo_cliente['cpf_cliente'] = $form->FormataCPFParaInserir($_POST['cpf_morador']);						
							
							$novo_cliente['idcliente'] = $cliente->set($novo_cliente);
							$cliente_fisico->set($novo_cliente);
							
							$_POST['numidmorador'] = $novo_cliente['idcliente'];
							
							//Limpa a vari�vel auxiliar
							$novo_cliente = NULL;
							
						}else{
							$_POST['numidmorador'] = $_POST['idmorador'];
						}
						//------------------------------------------------------

						
						
						$apartamento->update($_GET['idapartamento'], $_POST);

						//obt�m erros
						$err = $apartamento->err;

						//se n�o ocorreram erros
						if(count($err) == 0) {

							if(isset($_POST['demonstrativo']) && $_POST['demonstrativo']){

								$demonstrativo_apartamento->set(
									array('demonstrativo' => $_POST['demonstrativo'], 
											'idapartamento' => $_GET['idapartamento']));
							}

							$flags['sucesso'] = $conf['alterar'];

							//limpa o $flags.action para que seja exibida a listagem
							$flags['action'] = "listar";

						  	//lista
							$filtro = 'WHERE APTO.idcliente = '. $_SESSION['idcliente'];
							$list = $apartamento->make_list(0, $conf['rppg'], $filtro);

							//pega os erros
							$err = $apartamento->err;

							//envia a listagem para o template
							$smarty->assign("list", $list);

						}

					}

				}
				else {

					//busca detalhes
					$info = $apartamento->getById($_GET['idapartamento']);

					//tratamento das informa��es para fazer o UPDATE
					
					if(!empty($info['idproprietario'])) $info['proprietario'] = $cliente->getById($info['idproprietario']);
					if(!empty($info['idmorador'])) $info['morador'] = $cliente->getById($info['idmorador']);
					$info['numidcliente'] = $info['idcliente'];
					$info['litapto'] = $info['apto'];
					$info['litsituacao'] = $info['situacao'];
					$info['numfracaoIdeal'] = str_replace('.',',',$info['fracaoIdeal']);
					$info['numcustoFixo'] = $info['custoFixo'];
					$info['numfundoReserva'] = $info['fundoReserva'];
					$info['litobservacao'] = strip_tags($info['observacao']);


					$demonstrativo = $demonstrativo_apartamento->getByApartamento($_GET['idapartamento']);
					$info['demonstrativo'] = $demonstrativo['demonstrativo'];

					//obt�m os erros
					$err = $apartamento->err;
				}
			
				if(!empty($info['idmorador'])){
					$info['morador'] = $cliente->getById($info['idmorador']);
				}
				
				if(!empty($info['idproprietario'])){
					$info['proprietario'] = $cliente->getById($info['idproprietario']);
				}				
				

    			$list_cliente_condominio = $cliente_condominio->make_list_select();
				$smarty->assign("list_cliente_condominio",$list_cliente_condominio);

				//passa os dados para o template
				$smarty->assign("info", $info);

			break;

          
          
          // deleta um registro do sistema
	        case "excluir":

				//verifica se foi pedido a dele��o
				if($_POST['for_chk']){


				// deleta o registro
				$apartamento->delete($_GET['idapartamento']);

				//obt�m erros
				$err = $apartamento->err;

				//se n�o ocorreram erros
				if(count($err) == 0){
					$flags['sucesso'] = $conf['excluir'];
				}

					//limpa o $flags.action para que seja exibida a listagem
					$flags['action'] = "listar";

					//lista registros
					$filtro = 'WHERE APTO.idcliente = '. $_SESSION['idcliente'];
					$list = $apartamento->make_list(0, $conf['rppg'],$filtro);

					//pega os erros
					$err = $apartamento->err;

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
  
	
	$smarty->display("adm_apartamento.tpl");
	
?>
