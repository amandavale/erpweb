<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");

	require_once("../entidades/conta_receber.php");
	require_once("../entidades/funcionario.php");
	require_once("../entidades/modo_recebimento.php"); 
	require_once("../entidades/serie.php");

  // inicializa templating
  $smarty = new Smarty;

  // ação selecionada
  $flags['action'] = $_GET['ac'];

  // inicializa autenticação
  $auth = new auth();

	//inicializa classe
	$conta_receber = new conta_receber();
	$funcionario = new funcionario();
	$modo_recebimento = new modo_recebimento();
	$serie = new serie();

  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();
        


	/*
	Fun??o: Calcula_Total_CR_AJAX
	Calcula o valor final da conta a receber
	*/
	function Calcula_Total_CR_AJAX ($post = "") {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		//---------------------

		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();


		// se nao tiver nada no post, da um submit no form para pegar ele
		if ($post == "") {
			$objResponse->addScript("xajax_Calcula_Total_CR_AJAX(xajax.getFormValues('for_conta_receber'));");
		}
		else {

			$valor_conta_receber = $form->FormataMoedaParaInserir($post['valor_conta_receber']);
			$valor_juros_atraso = $form->FormataMoedaParaInserir($post['numvalor_juros_atraso']);
			$valor_multa = $form->FormataMoedaParaInserir($post['numvalor_multa']);

			$valor_total = $valor_conta_receber + $valor_juros_atraso + $valor_multa;
			$valor_total = $form->FormataMoedaParaExibir($valor_total);

			$objResponse->addAssign("total_final_cr", "innerHTML", $valor_total);			
			$objResponse->addAssign("numvalor_recebido", "value", $valor_total);
		}


		// retorna o resultado XML
    return $objResponse->getXML();
  }


	/*
	Função: Verifica_Campos_Busca_Rapida_Conta_Receber_AJAX
	Verifica se os campos da busca rápida do contas a receber foram preenchidos
	*/
	function Verifica_Campos_Busca_Rapida_Conta_Receber_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		//---------------------

		
		// cria o objeto xajaxResponse
    	$objResponse = new xajaxResponse();

		// se nao for baixa de conta a receber, obriga a preencher apenas o cliente
 		if ($_GET['ac'] != "baixa_conta_receber") {
			$form->chk_empty($post['idcliente'], 0, 'Cliente'); 
	
			if ($post['data_vencimento_de'] != "") $form->chk_IsDate($post['data_vencimento_de'], "Data de vencimento inicial");
			if ($post['data_vencimento_ate'] != "") $form->chk_IsDate($post['data_vencimento_ate'], "Data de vencimento final");
		}
		// se for baixa de contas a receber, obriga o preenchimento das datas
		else {
			$form->chk_IsDate($post['data_vencimento_de'], "Data de vencimento inicial");
			$form->chk_IsDate($post['data_vencimento_ate'], "Data de vencimento final");
		}



		$err = $form->err;


		// se nao houveram erros, da o submit no form
    	if(count($err) == 0) {
    	
    	$objResponse->addScript("document.getElementById('for_conta_receber').submit();");

		}
	    // houve erros, logo mostra-os
	    else {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
		}

		// retorna o resultado XML
    	return $objResponse->getXML();
  }



	/*
	Fun??o: Verifica_Campos_Conta_Receber_AJAX
	Verifica se os campos da conta a receber foram preenchidos
	*/
	function Verifica_Campos_Conta_Receber_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $conta_receber;
		global $funcionario;
		global $modo_recebimento;
		global $serie;
		//---------------------

		
		// cria o objeto xajaxResponse
    	$objResponse = new xajaxResponse();
		
		// busca os dados originais da conta a receber
		
		if($_GET['ac'] != "adicionar")
		$info_conta_receber = $conta_receber->getById($post['idconta_receber']);
		
		$status_conta = "";

		if ($_GET['ac'] == "editar") {

			$form->chk_empty($post['idUltFuncionario'], 1, 'Funcionário');


			// se for modo cheque
			if ($info_conta_receber['sigla_modo_recebimento'] == $conf['sigla_modo_cheque']) {
				// não faz nada
				
			}

			// se preencheu o valor recebido ou a data de recebimento ou marcou que quer dar baixa, verifica os dados
			else if ( (($post['numvalor_recebido'] != "0,00") && ($post['numvalor_recebido'] != "")) || ($post['litdata_recebimento'] != "") || ($post['litbaixa_conta'] == "1") ) {

				

				$form->chk_empty($post['numvalor_recebido'], 1, 'Valor recebido (R$)');
				$form->chk_IsDate($post['litdata_recebimento'], "Data do recebimento"); 
				
				if($post['litbaixa_conta'] == "1" && empty($post['idplano_debito'])  && empty($post['idplano_credito']) ){ //Dados obrigatórios para o registro da movimentação fiananceira, caso a baixa esteja sendo feita.
				
					$form->err[] = "As contas de Crédito e Débito não estão preenchidas. Informe pelo menos uma delas.";
				}

				$valor_conta_receber = $form->FormataMoedaParaInserir($post['valor_conta_receber']);
				$valor_juros_atraso = $form->FormataMoedaParaInserir($post['numvalor_juros_atraso']);
				$valor_multa = $form->FormataMoedaParaInserir($post['numvalor_multa']);
				$valor_recebido = $form->FormataMoedaParaInserir($post['numvalor_recebido']);

				// se não for o tipo CARTEIRA, o valor recebido tem que ser o valor da conta + o valor dos juros + valor da multa
				if ($info_conta_receber['sigla_modo_recebimento'] != $conf['sigla_modo_carteira']) {
					$status_conta = "RE";
					if ($post['litbaixa_conta'] == "0") $form->err[] = "Marque que esta conta foi baixada!";
					
					if ($form->Mat_Decimal($valor_recebido,2) != $form->Mat_Decimal($valor_conta_receber + $valor_juros_atraso + $valor_multa,2)) {
						$form->err[] = "O Valor Recebido tem que ser igual ao Valor da Conta + Valor dos Juros + Valor da Multa !";
					}
				}
				// se for o tipo CARTEIRA
				else {

					// verifica se escolheu alguma forma de recebimento válida
					if ( ($post['litsigla_modo_recebimento'] == $conf['sigla_modo_carteira']) || ($post['litsigla_modo_recebimento'] == "") ) {
						$form->err[] = "Escolha algum modo de recebimento válido, que NÃO seja CARTEIRA!";
			
					}
					else {

						// verifica se o valor recebido é maior que 0
						if ($valor_recebido <= "0.00") {
							$form->err[] = "O Valor Recebido tem que ser maior do que R$ 0,00 !";
						}
						

						// verifica se o valor recebido vai amortizar um valor maior que 0. Se nao for amortizar, nao deixa executar a operação.
						$valor_juros_parcela = $form->FormataMoedaParaInserir($info_conta_receber['valor_juros_parcela']);
						if ( ($valor_recebido - ($valor_juros_parcela + $valor_juros_atraso + $valor_multa)) <= 0 ) {
							$form->err[] = "O Valor Recebido não está amortizando nenhum valor! Digite um valor maior para a operação poder ser executada!";
						}


						// busca os dados atuais da conta a receber
						$info_conta_receber_atual = $modo_recebimento->getById($post['litsigla_modo_recebimento']);

						// verifica se selecionou um modo com baixa automática
						if ( ($info_conta_receber_atual['baixa_automatica'] == "1") && ($post['litbaixa_conta'] == "0") ) {
							$form->err[] = "O modo de recebimento escolhido possui baixa automática! Logo, marque esta conta a receber como baixada!";
						}

						$mensagem_baixa_parcial = "";
						if ($valor_recebido < ($valor_conta_receber + $valor_juros_atraso + $valor_multa)) {
							$status_conta = "RP";
							$valor_complementar =	 ($valor_conta_receber + $valor_juros_atraso + $valor_multa) - $valor_recebido;
							$valor_complementar = $form->FormataMoedaParaExibir($valor_complementar);

							$mensagem_baixa_parcial = "O Valor Recebido está menor do que o valor Total da Conta! Será gerado uma Conta a Receber complementar no valor de R$ $valor_complementar. Deseja prosseguir ?";
						}
						
					    
						// verifica se o valor da conta não ultrapassa o valor total dela
						else if ($valor_recebido > ($valor_conta_receber + $valor_juros_atraso + $valor_multa)) {
							$form->err[] = "O Valor Recebido não pode ser maior do que o valor Total da conta a receber!";
						}
						else {
							$status_conta = "RE";
						}


					}




				}



			}



/*
			if ($post['litbaixa_conta'] == "1") {

		      // busca os dados da serie
		      $info_serie = $serie->getById(md5($post['serie_ecf']));

		      // se a ecf não está cadastrada na tabela auxiliar, não deixa imprimir!
		      if ($info_serie['serie_crip_ecf'] == "") {
		        $form->err[] = "A impressora de ECF não está ligada ou não está configurada para trabalhar neste computador." .$post['serie_ecf'];
		      }

			  
		    }

*/		      
			$err = $form->err;



			// verifica se a senha do funcionario está correta
			$info_funcionario = $funcionario->getById($post['idUltFuncionario']);
			if ($info_funcionario['senha_funcionario'] != md5($post['senha_funcionario'])) $err[] = "A senha digitada está incorreta !";


		}		
		elseif($_GET['ac'] == 'adicionar'){
			
			$form->chk_empty($post['idfuncionario'], 1, 'o funcionário');
			$form->chk_empty($post['descricao_conta'], 1, 'a descrição da conta');
			$form->chk_empty($post['sigla_modo_recebimento'], 1, 'o modo de recebimento');
			$form->chk_moeda($post['valor_basico_conta'],0,'"Valor da conta a receber"');
			$form->chk_IsDate($post['data_vencimento'],'A data do vencimento') ;
			
			if ( empty($post['idplano_debito'])  && empty($post['idplano_credito']) ){
				$form->err[] = "As contas de Crédito e Débito não estão preenchidas. Informe pelo menos uma delas.";
			}
			
			
		}

		$err = $form->err;

  		// verifica se a senha do funcionario está correta
		if (!empty($post['idfuncionario'])){
		
			$info_funcionario = $funcionario->getById($post['idfuncionario']);
			if ($info_funcionario['senha_funcionario'] != md5($post['senha_funcionario'])) $err[] = "A senha digitada está incorreta !";
		}
		
  		

		// se nao houveram erros, da o submit no form
    	if(count($err) == 0) {

			if ($mensagem_baixa_parcial != ""){
				
				$objResponse->addConfirmCommands(2, utf8_encode($mensagem_baixa_parcial));
						$objResponse->addAssign("status_conta", "value", $status_conta);
			    		$objResponse->addScript("document.getElementById('for_conta_receber').submit();");
			
			}
			else if ($post['litbaixa_conta'] == "1"){

				$objResponse->addScriptCall("Retorna_Data_Hora_Conta_Receber");
				
				// verifica se vai cancelar: Pula 2 linhas se clicar no cancelar
				$objResponse->addConfirmCommands(3, utf8_encode("Tem certeza que deseja DAR BAIXA nesta conta a receber ? NÃO será possível desfazer esta operação!"));
					$objResponse->addScriptCall("RecebimentoNaoFiscal('{$conf['totalizador_venda_parcelada']}', '{$post['numvalor_recebido']}', '{$post['modo_recebimento']}')");
					$objResponse->addAssign("status_conta", "value", $status_conta);
		    		$objResponse->addScript("document.getElementById('for_conta_receber').submit();");
	    		
			}
			else{
				// verifica se vai cancelar: Pula 2 linhas se clicar no cancelar
				$objResponse->addConfirmCommands(2, utf8_encode("Tem certeza que deseja salvar os dados da conta a receber ?"));
					$objResponse->addAssign("status_conta", "value", $status_conta);
		    		$objResponse->addScript("document.getElementById('for_conta_receber').submit();");
			}
			
			
		}
	    // houve erros, logo mostra-os
	    else {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
		}

		// retorna o resultado XML
    return $objResponse->getXML();
  }


	/*
	Fun??o: Calcular_Juros_Atraso_AJAX
	Calcula os juros de atraso da conta a receber
	*/
	function Calcular_Juros_Atraso_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		//---------------------

		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		$form->chk_IsDate($post['litdata_recebimento'], "Data de recebimento");
		$form->chk_empty($post['juros_atraso'], 1, 'Juros de atraso (% a.d.)');

		$data1 = $form->FormataDataParaInserir($post['data_vencimento']);
		$data2 = $form->FormataDataParaInserir($post['litdata_recebimento']);

		if ($data2 < $data1) $form->err[] = "Para calcular os Juros de Atraso, a data de recebimento tem que ser maior do que a data de vencimento!";

		$err = $form->err;


		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {
    	
			// calcula a diferença de dias
			$array = $form->date_diff($data1, $data2);
			$dias_de_atraso = $array["d"];

			// Calcula o FV
			$juros_diarios = $form->FormataMoedaParaInserir($post['juros_atraso']) / 100;
			$valor_conta_receber = $form->FormataMoedaParaInserir($post['valor_conta_receber']); 
			$fv = $form->MatFin_Calcula_FV($juros_diarios, $dias_de_atraso, $valor_conta_receber);

			// calcula os juros de atraso (FV - valor_conta)
			$juros_de_atraso = $fv - $valor_conta_receber;
			$juros_de_atraso = $form->FormataMoedaParaExibir($juros_de_atraso);

			$objResponse->addAssign("numvalor_juros_atraso", "value", $juros_de_atraso);
	
			$objResponse->addScript("xajax_Calcula_Total_CR_AJAX(xajax.getFormValues('for_conta_receber'));");

		}
    // houve erros, logo mostra-os
    else {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
		}

		// retorna o resultado XML
    return $objResponse->getXML();
  }



	/*
	Fun??o: Calcular_Divida_Atual_AJAX
	Calcula a dívida atual da negociação
	*/
	function Calcular_Divida_Atual_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $conta_receber;
		//---------------------

		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

    $objResponse2 = new xajaxResponse();


		$form->chk_empty($post['juros_atraso'], 1, 'Juros de atraso (% a.d.)');
		$form->chk_empty($post['juros_desconto'], 1, 'Juros de desconto (% a.d.)');

		$err = $form->err;

		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {
    	
	
			// lista as contas a receber de acordo com o registro de vendas
			$list_contas_receber = $conta_receber->Busca_Contas_Receber_Pela_Venda($post['idvenda']);
	
			$valor_divida_atual = 0;
			$valor_negociado = 0;
			$exite_conta_selecionada = 0;		
			$valor_basico_total = 0;	

			// percorre as contas a receber para saber se ela foi selecionada para ser negociada
			for ($i=0; $i<count($list_contas_receber); $i++) {
		
				$campo = "id_cr_" . $list_contas_receber[$i]['idconta_receber'];
				if ( isset($post[$campo]) ) {
					// a conta foi selecionada para ser negociada
					$exite_conta_selecionada = 1;	

					// atualiza o valor básico total das contas
					$valor_basico_total += $form->FormataMoedaParaInserir($list_contas_receber[$i]['valor_basico_conta']);	

					$data_vencimento = $form->FormataDataParaInserir($list_contas_receber[$i]['data_vencimento']);		
					$data_atual = date('Y-m-d');
	
					// conta venceu, aplica o juros de atraso
					if ($data_vencimento < $data_atual) {
	
						// calcula a diferença de dias
						$array = $form->date_diff($data_vencimento, $data_atual);
						$dias_de_atraso = $array["d"];
			
						// Calcula o FV
						$juros_diarios = $form->FormataMoedaParaInserir($post['juros_atraso']) / 100;
						$valor_conta_receber = $form->FormataMoedaParaInserir($list_contas_receber[$i]['valor_total_conta']); 
						$fv = $form->MatFin_Calcula_FV($juros_diarios, $dias_de_atraso, $valor_conta_receber);
						$valor_corrigido = $fv;
	
	
						// calcula os juros de atraso (FV - valor_conta)
						$juros_de_atraso = $fv - $valor_conta_receber;
						$juros = $form->FormataMoedaParaExibir($juros_de_atraso);
	
					}
					// conta não venceu, aplica o juros de desconto
					else {
	
						// calcula a diferença de dias
						$array = $form->date_diff($data_atual, $data_vencimento);
						$dias_de_atraso = $array["d"];
			
						// Calcula o PV
						$juros_diarios = $form->FormataMoedaParaInserir($post['juros_desconto']) / 100;
						$valor_conta_receber = $form->FormataMoedaParaInserir($list_contas_receber[$i]['valor_total_conta']); 
						$pv = $form->MatFin_Calcula_PV($juros_diarios, $dias_de_atraso, $valor_conta_receber);
						$valor_corrigido = $pv;
	
						// se o pv ficou menor do que o valor básico da conta, não deixa efetuar a operação
						$valor_basico_conta = $form->FormataMoedaParaInserir($list_contas_receber[$i]['valor_basico_conta']);
						if ($pv < $valor_basico_conta) {
							$mensagem = "O Juros de desconto (% a.d.) está muito alto, pois ele está afetando o valor básico da conta. Lembre que ele é um juros Diário. Reduza este juros!";
							$objResponse2->addAlert(utf8_encode(html_entity_decode($mensagem)));

							// retorna o resultado XML
							return $objResponse2->getXML();
						}
						//--------------------------------------------------------------------------------

						// calcula os juros de atraso (PV - valor_conta)
						$juros_de_desconto = $pv - $valor_conta_receber;
						$juros = $form->FormataMoedaParaExibir($juros_de_desconto);
	
					}
	
					$valor_negociado += $valor_conta_receber;
					$valor_divida_atual += $valor_corrigido;
	
					$valor_corrigido = $form->FormataMoedaParaExibir($valor_corrigido);
	
					$campo = "juros_cr_" . $list_contas_receber[$i]['idconta_receber'];
					$objResponse->addAssign("$campo", "innerHTML", $juros);		
	
					$campo = "valor_cr_" . $list_contas_receber[$i]['idconta_receber'];
					$objResponse->addAssign("$campo", "innerHTML", $valor_corrigido);		
				
	
				} // fim do if  
	
			}
			// fim do for
					

			if ($exite_conta_selecionada == 0) {
				$mensagem = "Não existe nenhuma Conta a Receber selecionada para ser negociada!";
				$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
			}
			else {

				// deleta todas as contas a receber ja geradas
				$objResponse->loadXML( Limpa_Divida_Atual_AJAX($post) );
				//-------------------------------------------------------

				$objResponse->addAssign("valor_total_financiar", "value", $valor_divida_atual);
				$objResponse->addAssign("valor_total_nota", "value", $valor_divida_atual);
				$objResponse->addAssign("valor_basico_total", "value", $valor_basico_total);


				$valor_divida_atual = $form->FormataMoedaParaExibir($valor_divida_atual);
				$objResponse->addAssign("valor_divida_atual", "innerHTML", $valor_divida_atual);
				$objResponse->addAssign("TotalNota2", "innerHTML", $valor_divida_atual);
				$objResponse->addAssign("TotalFinanciar", "innerHTML", $valor_divida_atual);


				$valor_negociado = $form->FormataMoedaParaExibir($valor_negociado);
				$objResponse->addAssign("valor_negociado", "innerHTML", $valor_negociado);		
			}

		}
    // houve erros, logo mostra-os
    else {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
		}



		// retorna o resultado XML
    return $objResponse->getXML();
  }


	/*
	Fun??o: Limpa_Divida_Atual_AJAX
	Limpa os dados da dívida atual da negociação
	*/
	function Limpa_Divida_Atual_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		//---------------------

		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		$objResponse->addAssign("valor_total_nota", "value", "0.00");
		$objResponse->addAssign("valor_total_financiar", "value", "0.00");
		$objResponse->addAssign("valor_basico_total", "value", "0.00");
		
		$objResponse->addAssign("valor_divida_atual", "innerHTML", "");
		$objResponse->addAssign("valor_negociado", "innerHTML", "");

		// atualiza os dados da conta a receber
		$objResponse->addAssign("TotalNota2", "innerHTML", "0,00");
		$objResponse->addAssign("TotalVista", "innerHTML", "0,00");
		$objResponse->addAssign("TotalPrazo", "innerHTML", "0,00");
		$objResponse->addAssign("TotalFinal", "innerHTML", "0,00");
		$objResponse->addAssign("TotalFinanciar", "innerHTML", "0,00");

		$objResponse->addAssign("valor_total_a_vista", "value", "0.00");
		$objResponse->addAssign("valor_total_a_prazo", "value", "0.00");
		
		// deleta todas as contas a receber ja geradas
		$objResponse->loadXML( Deleta_Contas_Receber_AJAX( intval($post['total_contas_receber_a_vista']), intval($post['total_contas_receber_a_prazo']) ) );
		//-------------------------------------------------------


		// retorna o resultado XML
    return $objResponse->getXML();
  }


	/*
	Fun??o: Verifica_Campos_Negociacao_AJAX
	Verifica se os campos da negociação da conta a receber foram preenchidos
	*/
	function Verifica_Campos_Negociacao_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
    	global $serie;
		global $funcionario;
		//---------------------

		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();


		$form->chk_empty($post['idUltFuncionario'], 1, 'Funcionário');
		
		if($post['total_contas_receber_a_vista'] > 0){
			
			$chk_plano = $form->chk_empty($post['idplano_debito_vista'], 1, 'Conta de Débito (à vista) ');				   
			$chk_plano += $form->chk_empty($post['idplano_credito_vista'], 1, 'Conta de Crédito (à vista) ');
			
			//Mostra a tab para o usuário caso ele deixe de preencher algum dos campos acima
			if($chk_plano < 2) $objResponse->addScriptCall("Processa_Tabs(1, 'tab_')");	
			$chk_plano = 0;
		}
		
		if($post['total_contas_receber_a_prazo'] > 0){
			$chk_plano = $form->chk_empty($post['idplano_debito_prazo'], 1, 'Conta de Débito (a prazo) ');
			$chk_plano += $form->chk_empty($post['idplano_credito_prazo'], 1, 'Conta de Crédito (a prazo) ');
			
			//Mostra a tab para o usuário caso ele deixe de preencher algum dos campos acima
			if($chk_plano < 2) $objResponse->addScriptCall("Processa_Tabs(1, 'tab_')");
			$chk_plano = 0;
		}

		$err = $form->err;

		// verifica se a senha do funcionario está correta
		$info_funcionario = $funcionario->getById($post['idUltFuncionario']);
		if ($info_funcionario['senha_funcionario'] != md5($post['senha_funcionario'])) $err[] = "A senha digitada está incorreta !";

		
		// se for gerar uma emissão fiscal, verifica se as contas a receber foram geradas
		$post["valor_total_nota"] = round($post["valor_total_nota"],2);
		if ( ($post["valor_total_a_vista"] + $post["valor_total_a_prazo"]) < $post["valor_total_nota"]) {
			$err[] = "É necessário gerar o restante das Contas a Receber!";
		}
		
		// se a ecf não está cadastrada na tabela auxiliar, não deixa imprimir!
		// busca os dados da serie
		$info_serie = $serie->getById(md5($post['serie_ecf']));
		
       if ($info_serie['serie_crip_ecf'] == "") {
       		$err[] = "A impressora de ECF não está ligada ou não está configurada para trabalhar neste computador.";
       }
	   
		// se nao houveram erros, da o submit no form
    	if(count($err) == 0) {

			$pergunta = "Tem certeza que deseja EXECUTAR esta negociação de contas a receber ? NÃO será possível desfazer esta operação!";
			
			$valor_total_a_vista = $form->formataMoedaParaExibir($post['valor_total_a_vista']);
			$objResponse->addScriptCall("Retorna_Data_Hora");
			
			// verifica se vai cancelar: Pula 1 linha se clicar no cancelar
			$objResponse->addConfirmCommands(2, utf8_encode($pergunta));
				$objResponse->addScriptCall("RecebimentoNaoFiscal('{$conf['totalizador_venda_parcelada']}', '$valor_total_a_vista', '{$post['modo_recebimento']}')");
    			$objResponse->addScript("document.getElementById('for_orcamento').submit();");

		}
    	// houve erros, logo mostra-os
    	else {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
		}

		// retorna o resultado XML
    return $objResponse->getXML();
  }



	/*
	Fun??o: Verifica_Campos_Busca_Rapida_Comissao_Vendedor_AJAX
	Verifica se os campos da busca rápida do relatório de comissão do vendedor
	*/
	function Verifica_Campos_Busca_Rapida_Comissao_Vendedor_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		//---------------------

		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		$form->chk_empty($post['idFuncionario'], 1, 'Vendedor'); 

		$form->chk_IsDate($post['data_recebimento_de'], "Data de recebimento inicial");
		$form->chk_IsDate($post['data_recebimento_ate'], "Data de recebimento final");

		if ($form->data1_maior($post['data_recebimento_de'], $post['data_recebimento_ate'])) $form->err[] = "A data final tem que ser maior que a data inicial !";


		$err = $form->err;


		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {
    	
    	$objResponse->addScript("document.getElementById('for_comissao_vendedor').submit();");

		}
    // houve erros, logo mostra-os
    else {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
		}

		// retorna o resultado XML
    return $objResponse->getXML();
  }




?>
