<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  require_once("../common/lib/xajax/xajax.inc.php");
  
  require_once("../entidades/conta_pagar.php");
	require_once("../entidades/filial.php"); 
	require_once("../entidades/funcionario.php");
	require_once("../entidades/conta_pagar_cheque.php");
	require_once("../entidades/movimento.php");

	require_once("conta_pagar_ajax.php");	
	require_once("cheque_ajax.php");	


  // configurações anotionais
  $conf['area'] = "Contas a pagar"; // área

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
  if ($flags['action'] == "") $flags['action'] = "busca_parametrizada";

  // inicializa autenticação
  $auth = new auth();

  // cria o objeto xajax
  $xajax = new xajax();


	// registra todas as funções que serão usadas
	$xajax->registerFunction("Verifica_Campos_Conta_Pagar_AJAX");
	$xajax->registerFunction("Insere_Cheque_AJAX");
	$xajax->registerFunction("Deleta_Cheque_AJAX");
	$xajax->registerFunction("Seleciona_Cheques_Conta_Pagar_AJAX");



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
  		$conta_pagar = new conta_pagar();
  		
			$filial = new filial();
			$movimento = new movimento();
			$funcionario = new funcionario();
			$conta_pagar_cheque = new conta_pagar_cheque();
				

	  											
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
		  
		  /*   29/05/2009 - A busca genérica foi desabilitada por não haver necessidade de uso,
		  					as buscas serão realizadas no modo de busca_parametrizada.			*/
		  
		  header('location:'.$conf['addr'].'/admin/conta_pagar.php?ac=busca_parametrizada'); //Redireciona para a Busca Parametrizada.
		  
		  
			/*
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
							$list = $conta_pagar->Busca_Generica($pg, $flags['rpp'], $flags['busca'], "", "ac=busca_generica&busca=".$flags['busca']."&rpp=".$flags['rpp']);

							//pega os erros
							$err = $conta_pagar->err;

							//passa a listagem para o template
							$smarty->assign("list", $list);

						}

						if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];

						// busca os dados da filial
						$info_filial = $filial->getById($_SESSION['idfilial_usuario']);
						$smarty->assign("info_filial", $info_filial);
			*/	

          break;


					// busca parametrizada
          case "busca_parametrizada":

						if (isset($_GET['sucesso'])) $flags['sucesso'] = $conf["{$_GET['sucesso']}"];

            if ( ($_POST['for_chk']) || ($_GET['rpp'] != "") ) {

            	$flags['fez_busca'] = 1;

							if ($_POST['for_chk']) {
								$flags['descricao_conta'] = $_POST['descricao_conta'];
								$flags['idpedido'] = $_POST['idpedido'];
								$flags['data_vencimento_de'] = $_POST['data_vencimento_de'];
								$flags['data_vencimento_ate'] = $_POST['data_vencimento_ate'];
								$flags['data_pagamento_de'] = $_POST['data_pagamento_de'];
								$flags['data_pagamento_ate'] = $_POST['data_pagamento_ate'];
								$flags['status_conta'] = $_POST['status_conta'];
								$flags['rpp'] = $_POST['rpp'];
							}
							else {
								$flags['descricao_conta'] = $_GET['descricao_conta'];
								$flags['idpedido'] = $_GET['idpedido'];
								$flags['data_vencimento_de'] = $_GET['data_vencimento_de'];
								$flags['data_vencimento_ate'] = $_GET['data_vencimento_ate'];
								$flags['data_pagamento_de'] = $_GET['data_pagamento_de'];
								$flags['data_pagamento_ate'] = $_GET['data_pagamento_ate'];
								$flags['status_conta'] = $_GET['status_conta'];
								$flags['rpp'] = $_GET['rpp'];
							}

							$parametros_get = "&descricao_conta=" . $flags['descricao_conta'] . "&idpedido=" . $flags['idpedido'] . "&data_vencimento_de=" . $flags['data_vencimento_de'] . "&data_vencimento_ate=" . $flags['data_vencimento_ate'] . "&data_pagamento_de=" . $flags['data_pagamento_de'] . "&data_pagamento_ate=" . $flags['data_pagamento_ate'] . "&status_conta=" . $flags['status_conta'];


							$filtro_where = "";
							if ($flags['descricao_conta'] != "") $filtro_where .= " ( UPPER(CTA_PAG.descricao_conta) LIKE UPPER('%" . $flags['descricao_conta'] . "%')  ) AND ";
							if ($flags['idpedido'] != "") $filtro_where .= " ( (UPPER(CTA_PAG.idpedido) LIKE UPPER('%" . $flags['idpedido'] . "%'))) AND ";

							// verifica as datas de vencimento							
							$data_vencimento_de = $form->FormataDataParaInserir($flags['data_vencimento_de']);
							$data_vencimento_ate = $form->FormataDataParaInserir($flags['data_vencimento_ate']);

							if ($flags['data_vencimento_de'] != "") $filtro_where .= " ( CTA_PAG.data_vencimento >= '$data_vencimento_de' ) AND ";
							if ($flags['data_vencimento_ate'] != "") $filtro_where .= " ( CTA_PAG.data_vencimento <= '$data_vencimento_ate' ) AND ";
							//----------------------------------------	

							// verifica as datas de pagamento							
							$data_pagamento_de = $form->FormataDataParaInserir($flags['data_pagamento_de']);
							$data_pagamento_ate = $form->FormataDataParaInserir($flags['data_pagamento_ate']);

							if ($flags['data_pagamento_de'] != "") $filtro_where .= " ( CTA_PAG.data_pagamento >= '$data_pagamento_de' ) AND ";
							if ($flags['data_pagamento_ate'] != "") $filtro_where .= " ( CTA_PAG.data_pagamento <= '$data_pagamento_ate' ) AND ";
							//----------------------------------------	

							if ($flags['status_conta'] != "") $filtro_where .= " ( (UPPER(CTA_PAG.status_conta) LIKE UPPER('%" . $flags['status_conta'] . "%')) ) AND ";


							$filtro_where = substr($filtro_where,0,strlen($filtro_where)-4);


							if ($_GET['target'] == "full") $flags['rpp'] = 9999999;


						  //obtém qual página da listagem deseja exibir
						  $pg = intval(trim($_GET['pg']));

						  //se não foi passada a página como parâmetro, faz página default igual à página 0
						  if(!$pg) $pg = 0;

						  //lista os registros
							$list = $conta_pagar->Busca_Parametrizada($pg, $flags['rpp'], $filtro_where, "", "ac=busca_parametrizada$parametros_get&rpp=".$flags['rpp']);

							//pega os erros
							$err = $conta_pagar->err;

							//passa a listagem para o template
							$smarty->assign("list", $list);

						}

						if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];

						// busca os dados da filial
						$info_filial = $filial->getById($_SESSION['idfilial_usuario']);
						$smarty->assign("info_filial", $info_filial);


          break;




          // ação: adicionar <<<<<<<<<<
          case "adicionar":

						// busca o dia e hora atual
						$flags['data_criacao'] = date('d/m/Y H:i:s');
						
						// busca os dados da filial
						$info_filial = $filial->getById($_SESSION['idfilial_usuario']);
						$smarty->assign("info_filial", $info_filial);

						//busca os funcionarios da filial
						$list_funcionarios = $funcionario->Seleciona_Funcionarios_Da_Filial($_SESSION['idfilial_usuario']);
						$smarty->assign("list_funcionarios",$list_funcionarios);
						


            			if($_POST['for_chk']) {
            	
            	
            	
							$form->chk_empty($_POST['valor_conta'], 1, 'Valor da conta (R$)'); 
							$form->chk_empty($_POST['valor_pago'], 1, 'Valor pago (R$)');
							$form->chk_IsDate($_POST['data_vencimento'], "Data de vencimento"); 
							$form->chk_empty($_POST['status_conta'], 1, 'Status da conta'); 


				            $err = $form->err;

				            if(count($err) == 0) {

								$_POST['observacao'] = nl2br($_POST['observacao']); 
	              				$_POST['valor_conta'] = str_replace(",",".",$_POST['valor_conta']); 
								$_POST['valor_pago'] = str_replace(",",".",$_POST['valor_pago']); 
								$_POST['juros_conta'] = str_replace(",",".",$_POST['juros_conta']); 
								$_POST['multa_conta'] = str_replace(",",".",$_POST['multa_conta']); 
								$_POST['valor_saiu_caixa'] = str_replace(",",".",$_POST['valor_saiu_caixa']); 
	              				$_POST['data_vencimento'] = $form->FormataDataParaInserir($_POST['data_vencimento']); 
								$_POST['data_pagamento'] = $form->FormataDataParaInserir($_POST['data_pagamento']); 
								
	              
								
								if ($_POST['valor_conta'] == "") $_POST['valor_conta'] = "NULL"; 
								if ($_POST['valor_pago'] == "") $_POST['valor_pago'] = "NULL"; 
								if ($_POST['juros_conta'] == "") $_POST['juros_conta'] = "NULL"; 
								if ($_POST['multa_conta'] == "") $_POST['multa_conta'] = "NULL"; 
								if ($_POST['valor_saiu_caixa'] == "") $_POST['valor_saiu_caixa'] = "NULL"; 
								

								$_POST['idfilial'] = $_SESSION['idfilial_usuario'];
								$_POST['data_cadastro'] = date('Y-m-d H:i:s');

								//if ($_POST['idconta_pagar_tipo'] == "") $_POST['idconta_pagar_tipo'] = "NULL";
								if ($_POST['idpedido'] == "") $_POST['idpedido'] = "NULL";
								
								$_POST['idUltFuncionario'] = $_POST['idfuncionario'];
								$_POST['data_ult_alteracao'] = $_POST['data_cadastro'];

	              				//Inicia a transação do banco de dados
								$db->query('begin');
				  
								//grava o registro no banco de dados
								$idconta_pagar = $conta_pagar->set($_POST);
								
								//obtém os erros que ocorreram no cadastro
								$err = $conta_pagar->err;
								
																			
								//Desfaz as alterações em caso de erro
								if(count($err) > 0 ){
								
									$db->query('rollback');	
								}
								else{
									
									
	
									// grava os cheques utilizados para pagar a conta
									if(!$conta_pagar_cheque->GravaChequesContaPagar($_POST, $idconta_pagar)){
										//Desfaz as alterações em caso de erro
										$db->query('rollback');
										$err[] = "Houve uma falha ao registrar os cheques no sistema. Por favor, entre em contato com os autores.";			
									}
	
	
									
											
																					
									//Trata as informações para o registro de movimentação financeira						
									$novo_movimento['idconta_pagar'] = $idconta_pagar;
									$novo_movimento['idplano_debito'] = $_POST['idplano_debito'];
									$novo_movimento['idplano_credito'] = $_POST['idplano_credito'];
									//$novo_movimento['idconta_filial'] = $_POST[''];
									//$novo_movimento['idmovimento_origem'] = $_POST[''];
									$novo_movimento['idfilial'] = $_SESSION['idfilial_usuario'];
									$novo_movimento['descricao_movimento'] = $_POST['descricao_conta'];
									//$novo_movimento['controle_movimento'] = $_POST[''];
									$novo_movimento['valor_movimento'] = $_POST['valor_conta'];
									$novo_movimento['data_cadastro'] = date("Y-m-d H:i:s");
									$novo_movimento['data_movimento'] = date("Y-m-d");
									//$novo_movimento['data_baixa'] = $_POST[''];
									$_POST['status_conta'] == 'P' ? $novo_movimento['baixado'] = 1 : $novo_movimento['baixado'] = 0;
									
									 
									$novo_movimento['data_vencimento'] = $_POST['data_vencimento'];
									$novo_movimento['observacao'] = $_POST['observacao'];
									$novo_movimento['valor_juros'] = $_POST['juros_conta'];
									$novo_movimento['valor_multa'] = $_POST['multa_conta'];
									//$novo_movimento['gerar_fatura'] = $_POST[''];
									
						
									$movimento->set($novo_movimento);
									$err = $movimento->err;
								
								
									if(count($err) == 0) {
									
										//Faz o commit da transação
										$db->query('commit');
									
										// redireciona a página para evitar o problema do reload	
										$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=busca_parametrizada&sucesso=inserir'>"; 
										echo $redirecionar; 
										exit;
									}
									else{
										//Desfaz as alterações em caso de erro
										$db->query('rollback');
									}
								
								
								}
								
								
								
							}
							
              
            			}// fim do if($_POST['for_chk'])
            
            			
						$smarty->assign("err",$err);

						

          break;

          
          
          // ação: editar <<<<<<<<<<
					case "editar":

						// busca o dia e hora atual
						$flags['data_alteracao'] = date('d/m/Y H:i:s');
						
						// busca os dados da filial
						$info_filial = $filial->getById($_SESSION['idfilial_usuario']);
						$smarty->assign("info_filial", $info_filial);

						//busca os funcionarios da filial
						$list_funcionarios = $funcionario->Seleciona_Funcionarios_Da_Filial($_SESSION['idfilial_usuario']);
						$smarty->assign("list_funcionarios",$list_funcionarios);



						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idconta_pagar'] = $_GET['idconta_pagar']; 
							
							
							$form->chk_empty($_POST['numvalor_conta'], 1, 'Valor da conta (R$)'); 
							$form->chk_IsDate($_POST['litdata_vencimento'], "Data de vencimento"); 
							$form->chk_empty($_POST['litstatus_conta'], 1, 'Status da conta'); 
							
							
							$err = $form->err;

							if(count($err) == 0) {


								$_POST['litobservacao'] = nl2br($_POST['litobservacao']); 
							
								$_POST['numvalor_conta'] = str_replace(",",".",$_POST['numvalor_conta']); 
								$_POST['numvalor_pago'] = str_replace(",",".",$_POST['numvalor_pago']); 
								$_POST['numjuros_conta'] = str_replace(",",".",$_POST['numjuros_conta']); 
								$_POST['nummulta_conta'] = str_replace(",",".",$_POST['nummulta_conta']); 
								$_POST['numvalor_saiu_caixa'] = str_replace(",",".",$_POST['numvalor_saiu_caixa']); 
								
								$_POST['litdata_vencimento'] = $form->FormataDataParaInserir($_POST['litdata_vencimento']); 
								$_POST['litdata_pagamento'] = $form->FormataDataParaInserir($_POST['litdata_pagamento']); 
								
								
		          	
								if ($_POST['numvalor_conta'] == "") $_POST['numvalor_conta'] = "NULL"; 
								if ($_POST['numvalor_pago'] == "") $_POST['numvalor_pago'] = "NULL"; 
								if ($_POST['numjuros_conta'] == "") $_POST['numjuros_conta'] = "NULL"; 
								if ($_POST['nummulta_conta'] == "") $_POST['nummulta_conta'] = "NULL"; 
								if ($_POST['numvalor_saiu_caixa'] == "") $_POST['numvalor_saiu_caixa'] = "NULL"; 
								

								//if ($_POST['numidconta_pagar_tipo'] == "") $_POST['numidconta_pagar_tipo'] = "NULL";

								$_POST['numidUltFuncionario'] = $_POST['idUltFuncionario'];
								$_POST['litdata_ult_alteracao'] = date('Y-m-d H:i:s');

								
								//Pega o id da conta a pagar
								$idconta_pagar = intval($_GET['idconta_pagar']);
								
								
								//Inicia a transação
								$db->query('begin');
								
								
								//Faz o update
								$conta_pagar->update($idconta_pagar, $_POST);
								
								//obtém erros
								$err = $conta_pagar->err;
								
								// grava os cheques utilizados para pagar a conta
								$conta_pagar_cheque->GravaChequesContaPagar($_POST, $idconta_pagar);
								
								
								
								
								//---------Trata as informações para o registro de movimentação financeira ------------------\\
								
								if ($_POST['litstatus_conta'] == 'P'){ //Só faz a movimentação quando for a baixa do pagamento
								
									$filtro_movimento = 'WHERE MOVIM.idconta_pagar = '.$idconta_pagar;
									$movimento_origem = $movimento->make_list(0,1,$filtro_movimento);
											
									$novo_movimento['idconta_pagar'] = $idconta_pagar;
									$novo_movimento['idplano_debito'] = $_POST['idplano_debito'];
									$novo_movimento['idplano_credito'] = $_POST['idplano_credito'];
									
									$novo_movimento['idmovimento_origem'] = $movimento_origem[0]['idmovimento'];;
									$novo_movimento['idfilial'] = $_SESSION['idfilial_usuario'];
									
									
									$novo_movimento['data_cadastro'] = date("Y-m-d H:i:s");
									$novo_movimento['data_movimento'] = date("Y-m-d");
									
									$novo_movimento['data_vencimento'] = $_POST['litdata_vencimento'];
									$novo_movimento['observacao'] = $_POST['litobservacao'];
									$novo_movimento['valor_juros'] = $_POST['numjuros_conta'];
									$novo_movimento['valor_multa'] = $_POST['nummulta_conta'];
								
								
									$novo_movimento['valor_movimento'] = $_POST['numvalor_pago'];
									$novo_movimento['descricao_movimento'] = $_POST['litdescricao_conta'] . ' (baixa)';
									$novo_movimento['baixado'] = 1 ;
									$novo_movimento['data_baixa'] = date('Y-m-d H:i:s');
									
									if(!$movimento->set($novo_movimento))
									$err[] = "Houve uma falha ao registrar a movimentação financeira. Por favor, entre em contato com os autores.";
								}
								//----------------------------------------------------------------------------------\\
								

									
	
								//se não ocorreram erros
								if(count($err) == 0) {
								
									//Faz o commit das consultas
									$db->query('commit');

									// redireciona a página para evitar o problema do reload	
									$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=busca_parametrizada&sucesso=alterar'>"; 
									echo $redirecionar; 
									exit;

								}
								else{
								
									//Desfaz as alterações do banco
									$db->query('rollback');
								}

							}

						}
						else {

							//busca detalhes
							$info = $conta_pagar->getById($_GET['idconta_pagar']);

							//tratamento das informações para fazer o UPDATE
							//$info['numidconta_pagar_tipo'] = $info['idconta_pagar_tipo'];
							$info['litdescricao_conta'] = $info['descricao_conta']; 
							$info['numvalor_conta'] = $info['valor_conta']; 
							$info['numvalor_pago'] = $info['valor_pago']; 
							$info['numjuros_conta'] = $info['juros_conta']; 
							$info['nummulta_conta'] = $info['multa_conta']; 
							$info['litdata_vencimento'] = $info['data_vencimento']; 
							$info['litdata_pagamento'] = $info['data_pagamento']; 
							$info['litstatus_conta'] = $info['status_conta']; 
							$info['litobservacao'] = strip_tags($info['observacao']); 
							$info['litsaiu_do_caixa'] = $info['saiu_do_caixa']; 
							$info['numvalor_saiu_caixa'] = $info['valor_saiu_caixa']; 
							
							// busca os dados do funcionario
							$info_funcionario_criou = $funcionario->getById($info['idfuncionario']);
							$smarty->assign("info_funcionario_criou", $info_funcionario_criou);

							$info_funcionario_alterou = $funcionario->getById($info['idUltFuncionario']);
							$smarty->assign("info_funcionario_alterou", $info_funcionario_alterou);
							
							
							
							//obtém os erros
							$err = $conta_pagar->err;
						}

            			//$list_conta_pagar_tipo = $conta_pagar_tipo->make_list_select();
						//$smarty->assign("list_conta_pagar_tipo",$list_conta_pagar_tipo);

						
            
            
            

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a deleção
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$conta_pagar->delete($_GET['idconta_pagar']);

					  	//obtém erros
							$err = $conta_pagar->err;

							//se não ocorreram erros
							if(count($err) == 0){
								
								// redireciona a página para evitar o problema do reload	
								$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=busca_parametrizada&sucesso=excluir'>"; 
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

	if ($_GET['target'] == "full")  $smarty->display("adm_relatorio_conta_pagar.tpl");
  else $smarty->display("adm_conta_pagar.tpl");
?>

