<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  require_once("../common/lib/xajax/xajax.inc.php");
  
  require_once("../entidades/conta_receber.php");
	require_once("../entidades/modo_recebimento.php"); 
	require_once("../entidades/funcionario.php");
	require_once("../entidades/filial.php"); 
	require_once("../entidades/orcamento.php"); 
	require_once("../entidades/conta_receber_cheque.php");
	require_once("../entidades/parametro.php");
	require_once("../entidades/plano.php");
	require_once("../entidades/movimento.php");


	require_once("conta_receber_ajax.php");	
	require_once("cheque_ajax.php");
	require_once("orcamento_ajax.php");	


  // configurações anotionais
  $conf['area'] = "Contas a receber"; // área


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
	$xajax->registerFunction("Verifica_Campos_Busca_Rapida_Conta_Receber_AJAX");
	$xajax->registerFunction("Insere_Cheque_AJAX");
	$xajax->registerFunction("Deleta_Cheque_AJAX");
	$xajax->registerFunction("Seleciona_Cheques_Conta_Receber_AJAX");
	$xajax->registerFunction("Verifica_Campos_Conta_Receber_AJAX");
	$xajax->registerFunction("Calcular_Juros_Atraso_AJAX");
	$xajax->registerFunction("Calcula_Total_CR_AJAX");
	$xajax->registerFunction("Calcular_Divida_Atual_AJAX");
	$xajax->registerFunction("Limpa_Divida_Atual_AJAX");
	$xajax->registerFunction("Verifica_Campos_Negociacao_AJAX");


	// funções do orçamento_ajax.php
	$xajax->registerFunction("Insere_Conta_Receber_A_Vista_AJAX");
	$xajax->registerFunction("Insere_Conta_Receber_A_Prazo_AJAX");
	$xajax->registerFunction("Deleta_Modo_Recebimento_A_Vista_AJAX");
	$xajax->registerFunction("Calcula_Valor_Financiado_AJAX");


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
	  		$conta_receber = new conta_receber();
	  		
	  		$modo_recebimento = new modo_recebimento(); 
				$filial = new filial();
				$funcionario = new funcionario();
				$orcamento = new orcamento();
				$conta_receber_cheque = new conta_receber_cheque();
				$parametro = new parametro();
				$plano = new plano();
				$movimento = new movimento();
	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para validação de formulário
        $form = new form();
        
        
				$list = $auth->check_priv($conf['priv']);
				$aux = $flags['action'];
				if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}
								

        switch($flags['action']) {


		 // localizar contas a receber para dar baixa
          case "baixa_conta_receber":

				if($_POST['for_chk']) {
					
					
					if ($_POST['tipo_conta'] == 'A'){ 
							
						//----------------Listas as contas avulsas-------------------------------
						$filtro =  "WHERE CTA_REC.tipo_conta = 'A' 
						AND CTA_REC.data_vencimento >= '" . $form->FormatadataParaInserir($_POST['data_vencimento_de']) . "' 
						AND CTA_REC.data_vencimento <= '" . $form->FormatadataParaInserir($_POST['data_vencimento_ate']) . "' 
						AND CTA_REC.baixa_conta = '0' AND  CTA_REC.status_conta != 'NE'";						
						
						$list_vendas = $conta_receber->make_list(0, 9999, $filtro);				
														
					}
					else{
						// lista os registro de venda do perído e cliente selecionados
						$list_vendas = $conta_receber->Busca_Vendas_Por_Periodo($_POST);
		
					}
													
			
					// lista as contas a receber de acordo com o registro de vendas
					$list_contas_receber = $conta_receber->Busca_Contas_Receber($list_vendas);
					
					

					
					
					
												
					//passa a listagem para o template
					$smarty->assign("list_vendas", $list_vendas);
					$smarty->assign("list_contas_receber", $list_contas_receber);

				}

          break;



					// Negociar contas a receber
          case "negociar_conta_receber":

						if($_POST['for_chk']) {

							// lista os registro de venda do perído e cliente selecionados
							$list_vendas = $conta_receber->Busca_Vendas_Por_Periodo($_POST);

							// lista as contas a receber de acordo com o registro de vendas
							$list_contas_receber = $conta_receber->Busca_Contas_Receber($list_vendas);


							//passa a listagem para o template
							$smarty->assign("list_vendas", $list_vendas);
							$smarty->assign("list_contas_receber", $list_contas_receber);
	
						}

          break;



					// Efetua negociação
          case "efetua_negociacao":

						// busca o dia e hora atual
						$flags['data_atual'] = date('d/m/Y');

						// busca os dados da filial
						$info_filial = $filial->getById($_SESSION['idfilial_usuario']);
						$smarty->assign("info_filial", $info_filial);

						//busca os funcionarios da filial
						$list_funcionarios = $funcionario->Seleciona_Funcionarios_Da_Filial($_SESSION['idfilial_usuario']);
						$smarty->assign("list_funcionarios",$list_funcionarios);

						
						// requisitou a execução da negociação
						if($_POST['for_chk']) {

							$_GET['idorcamento'] = $_POST['idvenda'];
							$_POST['idfuncionario'] = $_POST['idUltFuncionario'];

							// atualiza as contas marcadas como negociadas
							$contas_negociadas = $conta_receber->Negocia_Contas_Receber($_POST);

							// gera as novas contas a receber			
							$conta_receber->Grava_Contas_Receber($contas_negociadas);

							//obtém erros
							$err = $conta_receber->err;

							//se não ocorreram erros
							if(count($err) == 0) {

								// redireciona a página para evitar o problema do reload	
								$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=alterar'>"; 
								echo $redirecionar; 
								exit;

							}


						}
						// se já estiver escolhido qual é a venda, mostra a tela da negociação
						else if(isset($_GET['idvenda'])) {

							// lista os registro de venda do perído e cliente selecionados
							$list_vendas = $conta_receber->Busca_Vendas_Por_Periodo("", $_GET['idvenda']);

							// lista as contas a receber de acordo com o registro de vendas
							$list_contas_receber = $conta_receber->Busca_Contas_Receber_Pela_Venda($_GET['idvenda']);


							//passa a listagem para o template
							$smarty->assign("list_vendas", $list_vendas);
							$smarty->assign("list_contas_receber", $list_contas_receber);

	
							// busca o valor padrao para a validade do orçamento e o máximo de itens
							$list_parametro = $parametro->make_list(0, $conf['rppg']);
							if ( count ($list_parametro) > 0 ) {
								$info['juros_parcelamento'] = number_format($list_parametro[0]['jurosPadraoParcelamento'],2,",","");
								$info['juros_atraso'] = $list_parametro[0]['jurosPadraoAtraso'];
								$info['juros_desconto'] = $list_parametro[0]['jurosPadraoDesconto'];
							}
							else {
								$info['juros_parcelamento'] = "0,00";
								$info['juros_atraso'] = "0,00";
								$info['juros_desconto'] = "0,00";	
							}
							$info['dias_entre_parcelas'] = "30";
							$info['data_parcela1'] = date("d/m/Y", mktime(0, 0, 0, date("m"), date("d")+30, date("Y")) );
							//------------------------------------------------


							// busca os modos de recebimento a vista
							$list_modo_recebimento_a_vista = $modo_recebimento->make_list_select(" WHERE a_vista = '1' AND (sigla_modo_recebimento <> 'CC' AND sigla_modo_recebimento <> 'CD')");
							$smarty->assign("list_modo_recebimento_a_vista",$list_modo_recebimento_a_vista);
				
							// busca os modos de recebimento a prazo
							$list_modo_recebimento_a_prazo = $modo_recebimento->make_list_select(" WHERE a_prazo = '1' ");
							$smarty->assign("list_modo_recebimento_a_prazo",$list_modo_recebimento_a_prazo);

							//passa os dados para o template
							$smarty->assign("info", $info);

						}




          break;



					//listagem dos registros
          case "listar":
          
				if (isset($_GET['sucesso'])) $flags['sucesso'] = $conf["{$_GET['sucesso']}"];

				if($_POST['for_chk']) {

					//Inicializa os arrays de contas a receber
					$list_vendas = array();
					$list_avulsa = array();
		
						
					if ( (empty($_POST['tipo_conta']) || $_POST['tipo_conta'] == 'A') && (empty($_POST['idcliente'])) ){ 
					
						//----------------Listas as contas avulsas-------------------------------
						$filtro =  "WHERE CTA_REC.tipo_conta = 'A' ";
						
						if(!empty($_POST['data_vencimento_de']))
							$filtro .=	" AND CTA_REC.data_vencimento >= '" . $form->FormatadataParaInserir($_POST['data_vencimento_de']) . "'"; 
						if(!empty($_POST['data_vencimento_de']))
							$filtro .=	" AND CTA_REC.data_vencimento <= '" . $form->FormatadataParaInserir($_POST['data_vencimento_ate']) . "'"; 
									
						$list_avulsa = $conta_receber->make_list(0, 9999, $filtro);				
														
					}
					
					// lista os registro de venda do perído e cliente selecionados
					if($_POST['tipo_conta'] != 'A')
					$list_vendas = $conta_receber->Busca_Vendas_Por_Periodo($_POST);
		
					

					//Junta as contas avulsas com as contas geradas por orçamento
					$list_total = array_merge($list_avulsa, $list_vendas);



					// lista as contas a receber de acordo com o registro
					$list_contas_receber = $conta_receber->Busca_Contas_Receber($list_total);
			
					
					//passa a listagem para o template
					$smarty->assign("list_vendas", $list_total);
					$smarty->assign("list_contas_receber", $list_contas_receber);
					
				}

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
							$info['idconta_receber'] = intval($_GET['idconta_receber']); 
																												
							$err = $form->err;

		          			if(count($err) == 0) {

								$_POST['litobservacao'] = nl2br($_POST['litobservacao']); 

								// se for dar a baixa, informa a data da baixa
								if ($_POST['litbaixa_conta'] == "1") $_POST['litdata_baixa'] = date('Y-m-d'); 

								$_POST['numidUltFuncionario'] = $_POST['idUltFuncionario'];
								$_POST['litdata_ult_alteracao'] = date('Y-m-d H:i:s');
							
								// só atualiza as informações abaixo se não for cheque								
								if ($_POST['sigla_modo_recebimento_original'] != $conf['sigla_modo_cheque']) {

									$_POST['numvalor_juros_atraso'] = str_replace(",",".",$_POST['numvalor_juros_atraso']); 
									$_POST['numvalor_multa'] = str_replace(",",".",$_POST['numvalor_multa']); 
									$_POST['numvalor_recebido'] = str_replace(",",".",$_POST['numvalor_recebido']); 
									$_POST['litdata_recebimento'] = $form->FormataDataParaInserir($_POST['litdata_recebimento']); 
									
									
									
									if ($_POST['numvalor_juros_atraso'] == "") $_POST['numvalor_juros_atraso'] = "NULL"; 
									if ($_POST['numvalor_multa'] == "") $_POST['numvalor_multa'] = "NULL"; 
									if ($_POST['numvalor_recebido'] == "") $_POST['numvalor_recebido'] = "NULL"; 
									
									
									if ($_POST['status_conta'] != "") $_POST['litstatus_conta'] = $_POST['status_conta'];	
								
								}	
								else {
									unset($_POST['numvalor_juros_atraso']); 
									unset($_POST['numvalor_multa']);
								}						

								
								
								
								
								$conta_receber->update($info['idconta_receber'], $_POST);
								
								
								// grava os cheques utilizados para pagar a conta a receber			
								if ( ($_POST['sigla_modo_recebimento_original'] == $conf['sigla_modo_cheque']) || ($_POST['litsigla_modo_recebimento'] == $conf['sigla_modo_cheque']) ) {
									$conta_receber_cheque->GravaChequesContaReceber($_POST, $_GET['idconta_receber']);
								}									
								

								//---------VERIFICA SE TEM  QUE GERAR UMA CONTA COMPLEMENTAR-----------------------\\
								if ($_POST['sigla_modo_recebimento_original'] == $conf['sigla_modo_carteira']) {

									$valor_conta_receber = $form->FormataMoedaParaInserir($_POST['valor_conta_receber']);
									$valor_juros_atraso = $_POST['numvalor_juros_atraso'];
									$valor_multa = $_POST['numvalor_multa'];
									$valor_recebido = $_POST['numvalor_recebido'];

									
									
									// gera a conta complementar, pois pagou um valor inferior ao valor da conta
									if ( ($valor_recebido < ($valor_conta_receber + $valor_juros_atraso + $valor_multa)) &&
												($valor_recebido != "") && ($valor_recebido != "0,00")  )  {

										// busca as informações 	
										$info_conta_receber = $conta_receber->GetById($_GET['idconta_receber']);

										$valor_complementar =	 ($valor_conta_receber + $valor_juros_atraso + $valor_multa) - $valor_recebido;
									
										$info_conta_receber_complementar['sigla_modo_recebimento'] = $conf['sigla_modo_carteira'];
										$info_conta_receber_complementar['idfilial'] = $_SESSION['idfilial_usuario'];
										$info_conta_receber_complementar['idorcamento'] = $info_conta_receber['idorcamento'];

										$info_conta_receber_complementar['idfuncionario'] = $_POST['idUltFuncionario'];
										$info_conta_receber_complementar['idUltFuncionario'] = $_POST['idUltFuncionario'];
					
										$info_prox_sequencial = $conta_receber->BuscaProximoNumeroSequencial($info_conta_receber['idorcamento']);
										$info_conta_receber_complementar['numero_seq_conta'] = $info_prox_sequencial['proximo_sequencial'];
					
										$info_conta_receber_complementar['descricao_conta'] =  "Venda {$info_conta_receber['idorcamento']} - Conta {$info_prox_sequencial['proximo_sequencial']} (P. Comp. Ref: {$info_conta_receber['numero_seq_conta']})";
					
										$info_conta_receber_complementar['valor_basico_conta'] = $valor_complementar;
										$info_conta_receber_complementar['valor_juros_parcela'] = "0.00";
										$info_conta_receber_complementar['valor_juros_atraso'] = "0.00";
										$info_conta_receber_complementar['valor_multa'] = "0.00";
										$info_conta_receber_complementar['valor_recebido'] = "0.00";
					
										$info_conta_receber_complementar['data_cadastro'] = date('Y-m-d H:i:s');
										$info_conta_receber_complementar['data_vencimento'] = $form->FormataDataParaInserir($info_conta_receber['data_vencimento']);
										$info_conta_receber_complementar['data_recebimento'] = "NULL";
										$info_conta_receber_complementar['data_baixa'] = "NULL";
										$info_conta_receber_complementar['data_ult_alteracao'] = date('Y-m-d H:i:s');
															
										$info_conta_receber_complementar['status_conta'] = "NA";
										$info_conta_receber_complementar['baixa_conta'] = "0";
										$info_conta_receber_complementar['tipo_conta'] = "P"; // parcela
										$info_conta_receber_complementar['observacao'] = "";
					
										$conta_receber->set($info_conta_receber_complementar);
										
										
										//se não ocorreram erros
										if(count($err) == 0) {
		
											// redireciona a página para evitar o problema do reload	
											$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=alterar'>"; 
											echo $redirecionar; 
											exit;
		
										}

									}



								}
								
								//----------------------------------------------------------------------------------\\


								//obtém erros
								$err = $conta_receber->err;

																				
								//---------Trata as informações para o registro de movimentação financeira ------------------\\
								
								if ($_POST['litbaixa_conta'] == '1'){ //Só faz a movimentação quando for a baixa do pagamento
								
									$filtro_movimento = 'WHERE MOVIM.idconta_receber = '.$info['idconta_receber'];
									$movimento_origem = $movimento->make_list(0,1,$filtro_movimento);
											
									$novo_movimento['idconta_receber'] = $idconta_receber;
									$novo_movimento['idplano_debito'] = $_POST['idplano_debito'];
									$novo_movimento['idplano_credito'] = $_POST['idplano_credito'];
									
									$novo_movimento['idmovimento_origem'] = $movimento_origem[0]['idmovimento'];;
									$novo_movimento['idfilial'] = $_SESSION['idfilial_usuario'];
									
									
									$novo_movimento['data_cadastro'] = date("Y-m-d H:i:s");
									$novo_movimento['data_movimento'] = date("Y-m-d");
									
									$novo_movimento['data_vencimento'] = $form->FormataDataParaInserir($_POST['data_vencimento']);
									$novo_movimento['observacao'] = $_POST['litobservacao'];
									$novo_movimento['valor_juros'] = $_POST['numvalor_juros_atraso'];
									$novo_movimento['valor_multa'] = $_POST['numvalor_multa'];
								
								
									$novo_movimento['valor_movimento'] = $_POST['numvalor_recebido'];
									$novo_movimento['descricao_movimento'] = $_POST['descricao_conta'] . ' (baixa)';
									$novo_movimento['baixado'] = '1' ;
									$novo_movimento['data_baixa'] = date('Y-m-d H:i:s');
									
									if(!$movimento->set($novo_movimento))
									$err[] = "Houve uma falha ao registrar a movimentação financeira. Por favor, entre em contato com os autores.";
								}
								//----------------------------------------------------------------------------------\\										
										
										
								//se não ocorreram erros
								if(count($err) == 0) {

									// redireciona a página para evitar o problema do reload	
									$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=alterar'>"; 
									echo $redirecionar; 
									exit;

								}


							}

						}																																																																																																																													
				else {

							//busca detalhes
							$info = $conta_receber->getById(intval($_GET['idconta_receber']));


							// busca informações da venda caso não seje uma conta avulsa
							if(!empty($info['idorcamento'])){
											
								$list_vendas = $conta_receber->Busca_Vendas_Por_Periodo("", $info['idorcamento']);	
								$smarty->assign("list_vendas", $list_vendas);
							}

							//tratamento das informações para fazer o UPDATE
							
							//Recupera o plano de contas
							$filtro_movimento = 'WHERE MOVIM.idconta_receber = '.$info['idconta_receber'];
							$movimento_origem = $movimento->make_list(0,1,$filtro_movimento);
							if(count($movimento_origem) > 0){
								$info['plano_debito'] =  $plano->getById($movimento_origem[0]['idplano_debito']);
								$info['plano_credito'] =  $plano->getById($movimento_origem[0]['idplano_credito']);	
							}
							//--------------------

							$info['litsigla_modo_recebimento'] = $info['sigla_modo_recebimento']; 
							$info['numnumero_seq_conta'] = $info['numero_seq_conta']; 
							$info['litdescricao_conta'] = $info['descricao_conta']; 
							$info['numvalor_basico_conta'] = $info['valor_basico_conta']; 
							$info['numvalor_juros_parcela'] = $info['valor_juros_parcela']; 
							$info['numvalor_juros_atraso'] = $info['valor_juros_atraso']; 
							$info['numvalor_multa'] = $info['valor_multa']; 
							$info['numvalor_recebido'] = $info['valor_recebido']; 
							$info['litdata_vencimento'] = $info['data_vencimento']; 
							$info['litdata_recebimento'] = $info['data_recebimento']; 
							$info['litdata_baixa'] = $info['data_baixa']; 
							$info['litstatus_conta'] = $info['status_conta']; 
							$info['litbaixa_conta'] = $info['baixa_conta']; 
							$info['litobservacao'] = strip_tags($info['observacao']); 


							// busca os dados do funcionario
							$info_funcionario_criou = $funcionario->getById($info['idfuncionario']);
							$smarty->assign("info_funcionario_criou", $info_funcionario_criou);

							$info_funcionario_alterou = $funcionario->getById($info['idUltFuncionario']);
							$smarty->assign("info_funcionario_alterou", $info_funcionario_alterou);
							
														
							
							
								//obtém erros
								$err = $conta_receber->err;


							}																																							
		
					$list_modo_recebimento = $modo_recebimento->make_list_select();
					$smarty->assign("list_modo_recebimento",$list_modo_recebimento);
		
					// busca o valor padrao os juros de atraso
					$list_parametro = $parametro->make_list(0, $conf['rppg']);
					if ( count ($list_parametro) > 0 ) {
						$info['juros_atraso'] = number_format($list_parametro[0]['jurosPadraoAtraso'],2,",","");
					}
					else {
						$info['juros_atraso'] = "0,00";
					}							
		
		
		
		
					//passa os dados para o template
					$smarty->assign("info", $info);
		
			  break;
			  
			  
			  case "adicionar":
			  
			  
			  	if($_POST['for_chk']) {
				
	
					//$err = $form->err;
	
					if(count($err) == 0){
						
						$_POST['observacao'] = nl2br($_POST['observacao']); 
						
						$_POST['numero_seq_conta'] = 1 ; 
						$_POST['idUltFuncionario'] = $_POST['idfuncionario'];
						$_POST['data_cadastro'] = date("Y-m-d");
						$_POST['data_ult_alteracao'] = date("Y-m-d H:i:s");
						$_POST['status_conta'] = 'NA' ; 
						$_POST['tipo_conta'] = 'A' ; 
						$_POST['baixa_conta'] = '0' ; 
						
						$_POST['idorcamento'] = 'NULL';
						$_POST['valor_juros_parcela'] = "NULL"; 
						$_POST['valor_juros_atraso'] = "NULL"; 
						$_POST['valor_multa'] = "NULL"; 
						$_POST['valor_recebido'] = "NULL"; 
						$_POST['data_recebimento'] = "NULL"; 
						
						
						if ( empty($_POST['data_vencimento']) ) $_POST['data_vencimento'] = "NULL"; 
						else $_POST['data_vencimento'] = $form->FormataDataParaInserir($_POST['data_vencimento']); 
						
						
						if ($_POST['sigla_modo_recebimento'] == "") $_POST['sigla_modo_recebimento'] = "NULL"; 
						if ($_POST['valor_basico_conta'] == "") $_POST['valor_basico_conta'] = "NULL";
						else  $_POST['valor_basico_conta'] = $form->FormataMoedaParaInserir($_POST['valor_basico_conta']); 
			
			
						//grava o registro no banco de dados
						$idconta_receber = $conta_receber->set($_POST);
						
						
						//---------Trata as informações para o registro de movimentação financeira ------------------\\
											
						$novo_movimento['idconta_receber'] = $idconta_receber;
						$novo_movimento['idplano_debito'] = $_POST['idplano_debito'];
						$novo_movimento['idplano_credito'] = $_POST['idplano_credito'];
						$novo_movimento['idfilial'] = $_SESSION['idfilial_usuario'];
						$novo_movimento['descricao_movimento'] = $_POST['descricao_conta'];
						$novo_movimento['data_cadastro'] = date("Y-m-d H:i:s");
						$novo_movimento['data_movimento'] = date("Y-m-d");
						$novo_movimento['data_vencimento'] = $_POST['data_vencimento'];
						$novo_movimento['observacao'] = $_POST['observacao'];
						$novo_movimento['valor_movimento'] = $_POST['valor_basico_conta'];
						$novo_movimento['baixado'] = '0' ;
						
						
						if(!$movimento->set($novo_movimento))
						$err[] = "Houve uma falha ao registrar a movimentação financeira. Por favor, entre em contato com os autores.";
						//--------------------------------------------------------------------------------------------\\
						
						//obtém os erros que ocorreram no cadastro
						$err = $conta_receber->err;
						
						
						//se não ocorreram erros
						if(count($err) == 0) {

							// redireciona a página para evitar o problema do reload	
							$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=adicionar'>"; 
							echo $redirecionar; 
							exit;

						}
		
					
				}

			}
		  	


			$list_modo_recebimento = $modo_recebimento->make_list_select();
			$smarty->assign("list_modo_recebimento", $list_modo_recebimento);
			
			$info_filial = $filial->getById($_SESSION['idfilial_usuario']);
			$smarty->assign("info_filial", $info_filial);
			
			//busca os funcionarios da filial
			$list_funcionarios = $funcionario->Seleciona_Funcionarios_Da_Filial($_SESSION['idfilial_usuario']);
			$smarty->assign("list_funcionarios",$list_funcionarios);
		  
		  
		  break;
    
          

	      }
	      
      }
      
  	}
  	
    // seta erros
    $smarty->assign("err", $err);
    
	}


	// Forma Array de intruções de preenchimento
	$intrucoes_preenchimento = array();
	if ($flags['action'] == "adicionar" || $flags['action'] == "editar" || $flags['action'] == "baixa_conta_receber" || $flags['action'] == "negociar_conta_receber" ) {
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


  $smarty->display("adm_conta_receber.tpl");
?>