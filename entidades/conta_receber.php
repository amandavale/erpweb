<?php
	
	class conta_receber {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function conta_receber(){
			// não faz nada
		}



		/**
		  método: Busca_Contas_Receber_Caixa_Diario
		  propósito: busca as contas a receber para fazer o relatório de caixa diário
		*/
		
		function Busca_Contas_Receber_Caixa_Diario (){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------


			//busca os registros no banco de dados e monta o vetor de retorno
			$list_return = array();			
			

			// data atual
			$data_atual = date('Y-m-d');

			// busca as contas a receber de acordo com o registro de vendas
			$list_sql = "	SELECT
											CTA_REC.*, CLI.nome_cliente, FNC_CR.nome_funcionario,
											MREC.sigla_modo_recebimento, MREC.descricao as descricao_modo_recebimento,
											ORCT.valor_total_nota, ORCT.valor_comissao_vendedor, ORCT.valor_base_calc_comissao, ORCT.fator_correcao_comissao_vendedor

										FROM
											{$conf['db_name']}conta_receber CTA_REC 
												INNER JOIN {$conf['db_name']}modo_recebimento MREC ON CTA_REC.sigla_modo_recebimento=MREC.sigla_modo_recebimento 
												INNER JOIN {$conf['db_name']}orcamento ORCT ON CTA_REC.idorcamento = ORCT.idorcamento
												LEFT OUTER JOIN {$conf['db_name']}cliente CLI ON ORCT.idcliente=CLI.idcliente
												INNER JOIN {$conf['db_name']}funcionario FNC_CR ON ORCT.idfuncionario=FNC_CR.idfuncionario											


										WHERE 
											
											ORCT.idfilial = " . $_SESSION['idfilial_usuario'] . "
												AND
											ORCT.tipoOrcamento <> 'O'
												AND
											
												( 
													( (CTA_REC.data_recebimento = '$data_atual') AND (CTA_REC.sigla_modo_recebimento != '{$conf['sigla_modo_cheque']}' ) )
														OR
													( (CTA_REC.data_cadastro LIKE '%$data_atual%') AND (CTA_REC.sigla_modo_recebimento = '{$conf['sigla_modo_cheque']}' ) )
												)												

												AND

											(CTA_REC.status_conta = 'RP' OR CTA_REC.status_conta = 'RE')


										ORDER BY 
											CTA_REC.idorcamento ASC, CTA_REC.idconta_receber ASC
									";				



			//manda fazer a paginação
			$list_q = $db->query($list_sql);

			// percorre os registros de contas a receber, por venda	
			$cont = 0;
			while($list = $db->fetch_array($list_q)){

				if ($list['data_vencimento'] != '0000-00-00') $list['data_vencimento'] = $form->FormataDataParaExibir($list['data_vencimento']); 
				else $list['data_vencimento'] = "";

				if ($list['data_recebimento'] != '0000-00-00') $list['data_recebimento'] = $form->FormataDataParaExibir($list['data_recebimento']); 
				else $list['data_recebimento'] = "";

				if ($list['data_baixa'] != '0000-00-00') $list['data_baixa'] = $form->FormataDataParaExibir($list['data_baixa']); 
				else $list['data_baixa'] = "";

				if ($list['data_cadastro'] != '0000-00-00 00:00:00') { 
					$array = split(" ",$list['data_cadastro']); 
					$list['datahoraCadastro_D'] = $form->FormataDataParaExibir($array[0]); 
					$list['datahoraCadastro_H'] = $array[1]; 
				} 

				if ($list['status_conta'] == 'RE') $list['status_conta'] = "Recebido"; 
				else if ($list['status_conta'] == 'RP') $list['status_conta'] = "Rec. Parcial"; 
				else if ($list['status_conta'] == 'NA') $list['status_conta'] = "Não Recebido"; 
				else if ($list['status_conta'] == 'NE') $list['status_conta'] = "Negociado"; 

				if ($list['baixa_conta'] == '0') $list['baixa_conta'] = "Não"; 
				else if ($list['baixa_conta'] == '1') $list['baixa_conta'] = "Sim"; 

				
				$list['valor_total_conta'] = $list['valor_basico_conta'] + $list['valor_juros_parcela'];
				if ($list['valor_total_conta'] != "") $list['valor_total_conta'] = number_format($list['valor_total_conta'],2,",",""); 


				if ($list['valor_basico_conta'] != "") $list['valor_basico_conta'] = number_format($list['valor_basico_conta'],2,",",""); 
				if ($list['valor_juros_parcela'] != "") $list['valor_juros_parcela'] = number_format($list['valor_juros_parcela'],2,",",""); 
				if ($list['valor_juros_atraso'] != "") $list['valor_juros_atraso'] = number_format($list['valor_juros_atraso'],2,",",""); 
				if ($list['valor_multa'] != "") $list['valor_multa'] = number_format($list['valor_multa'],2,",",""); 
				if ($list['valor_recebido'] != "") $list['valor_recebido'] = number_format($list['valor_recebido'],2,",",""); 


        $list_return[] = $list;
				
				$cont++;
			
			}

			return $list_return;


		}	
		




		/**
		  método: Busca_Contas_Receber_Comissao_Vendedor
		  propósito: busca as contas a receber para fazer o relatório de comissão do vendedor
		*/
		
		function Busca_Contas_Receber_Comissao_Vendedor ($post){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------


			//busca os registros no banco de dados e monta o vetor de retorno
			$list_return = array();			
			

			// prepara o filtro de datas
			$filtro_datas = "";				
			$filtro_datas .= " AND CTA_REC.data_baixa >= '" . $form->FormatadataParaInserir($post['data_recebimento_de']) . "' ";
			$filtro_datas .= " AND CTA_REC.data_baixa <= '" . $form->FormatadataParaInserir($post['data_recebimento_ate']) . "' ";


			// busca as contas a receber de acordo com o registro de vendas
			$list_sql = "	SELECT
											CTA_REC.*, CLI.nome_cliente,
											MREC.sigla_modo_recebimento, MREC.descricao as descricao_modo_recebimento,
											ORCT.valor_total_nota, ORCT.valor_comissao_vendedor, ORCT.valor_base_calc_comissao, ORCT.fator_correcao_comissao_vendedor

										FROM
											{$conf['db_name']}conta_receber CTA_REC 
												INNER JOIN {$conf['db_name']}modo_recebimento MREC ON CTA_REC.sigla_modo_recebimento=MREC.sigla_modo_recebimento 
												INNER JOIN {$conf['db_name']}orcamento ORCT ON CTA_REC.idorcamento = ORCT.idorcamento
												LEFT OUTER JOIN {$conf['db_name']}cliente CLI ON ORCT.idcliente=CLI.idcliente
												INNER JOIN {$conf['db_name']}funcionario FNC_CR ON ORCT.idfuncionario=FNC_CR.idfuncionario											


										WHERE 
											
											ORCT.idfilial = " . $_SESSION['idfilial_usuario'] . "
												AND
											ORCT.tipoOrcamento <> 'O'
												AND
											FNC_CR.idfuncionario = " . $post['idFuncionario'] . "
											
											$filtro_datas
											
												AND

											CTA_REC.baixa_conta = '1' AND (CTA_REC.status_conta = 'RP' OR CTA_REC.status_conta = 'RE')


										ORDER BY 
											CTA_REC.data_baixa ASC, CTA_REC.idorcamento ASC
									";				



			//manda fazer a paginação
			$list_q = $db->query($list_sql);

			// percorre os registros de contas a receber, por venda	
			$cont = 0;
			while($list = $db->fetch_array($list_q)){

				if ($list['data_vencimento'] != '0000-00-00') $list['data_vencimento'] = $form->FormataDataParaExibir($list['data_vencimento']); 
				else $list['data_vencimento'] = "";

				if ($list['data_recebimento'] != '0000-00-00') $list['data_recebimento'] = $form->FormataDataParaExibir($list['data_recebimento']); 
				else $list['data_recebimento'] = "";

				if ($list['data_baixa'] != '0000-00-00') $list['data_baixa'] = $form->FormataDataParaExibir($list['data_baixa']); 
				else $list['data_baixa'] = "";

				// calcula o valor recebido líquido
				$valor_recebido_liquido = $list['valor_recebido'] - ($list['valor_multa'] + $list['valor_juros_atraso'] + $list['valor_juros_parcela']);
				
				// calcula o valor da comissão do vendedor
				$list['porcentagem_comissao_vendedor'] = ($list['valor_comissao_vendedor'] * 100) / $list['valor_base_calc_comissao'];

				// valor sem correção
				$valor_comissao_sem_correcao = ($valor_recebido_liquido * $list['porcentagem_comissao_vendedor']) / 100;

				// valor com correção
				$valor_comissao_com_correcao = ($valor_comissao_sem_correcao * $list['fator_correcao_comissao_vendedor']) / 100;
				

				if ($list['valor_basico_conta'] != "") $list['valor_basico_conta'] = number_format($list['valor_basico_conta'],2,",",""); 
				if ($list['valor_juros_parcela'] != "") $list['valor_juros_parcela'] = number_format($list['valor_juros_parcela'],2,",",""); 
				if ($list['valor_juros_atraso'] != "") $list['valor_juros_atraso'] = number_format($list['valor_juros_atraso'],2,",",""); 
				if ($list['valor_multa'] != "") $list['valor_multa'] = number_format($list['valor_multa'],2,",",""); 
				if ($list['valor_recebido'] != "") $list['valor_recebido'] = number_format($list['valor_recebido'],2,",",""); 

				$list['porcentagem_comissao_vendedor'] = number_format($list['porcentagem_comissao_vendedor'],2,",",""); 
				$list['fator_correcao_comissao_vendedor'] = number_format($list['fator_correcao_comissao_vendedor'],2,",",""); 
				$list['valor_comissao_com_correcao'] = number_format($valor_comissao_com_correcao,2,",",""); 


        $list_return[] = $list;
				
				$cont++;
			
			}

			return $list_return;


		}	
		




		/**
		  método: Negocia_Contas_Receber
		  propósito: Marca como negociada as contas a receber selecionadas
		*/
		
		function Negocia_Contas_Receber ($post){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$contas_negociadas = "";

			// lista as contas a receber de acordo com o registro de vendas
			$list_contas_receber = $this->Busca_Contas_Receber_Pela_Venda($post['idvenda']);
	
			$info_negociacao['litstatus_conta'] = "NE"; // conta negociada

			// percorre as contas a receber para saber se ela foi selecionada para ser negociada
			for ($i=0; $i<count($list_contas_receber); $i++) {
		
				$campo = "id_cr_" . $list_contas_receber[$i]['idconta_receber'];
				if ( isset($post[$campo]) ) {
					// a conta foi selecionada para ser negociada
					$this->update($list_contas_receber[$i]['idconta_receber'], $info_negociacao);

					$contas_negociadas .= $list_contas_receber[$i]['numero_seq_conta'] . ", ";
				} // fim do if  
	
			}
			// fim do for

			if ($contas_negociadas != "") {
				$contas_negociadas = substr($contas_negociadas, 0, strlen($contas_negociadas)-2);
			}

			return $contas_negociadas;

		}




		/**
		  método: BuscaProximoNumeroSequencial
		  propósito: busca o próximo numero sequencial das contas a receber
		*/
		function BuscaProximoNumeroSequencial($idorcamento){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT 
												max(CTA_REC.numero_seq_conta) as maior_sequencial
											
										FROM
											{$conf['db_name']}conta_receber CTA_REC

										WHERE
											 CTA_REC.idorcamento = $idorcamento ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				$get['proximo_sequencial'] = $get['maior_sequencial'] + 1;

				//retorna o vetor associativo com os dados
				return $get;
			}
			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}
				
		}


		/**
		  método: Busca_Vendas_Por_Periodo
		  propósito: faz a listagem de vendas
		*/
		
		function Busca_Vendas_Por_Periodo ($post, $idorcamento = ""){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			
			//$filtro_cartao = "AND (CTA_REC.sigla_modo_recebimento <> 'CC' AND CTA_REC.sigla_modo_recebimento <> 'CD')";
			
			
			// busca a venda específica
			if ( ($post == "") && ($idorcamento != "") ) {

				
				
				
				$list_sql = "	SELECT
									Distinct (ORCT.idorcamento), ORCT.*, CLI.nome_cliente, FNC_CR.nome_funcionario as funcionario_criou_orcamento,
									JCLI.cnpj_cliente, FCLI.cpf_cliente,EST.nome_estado, CID.nome_cidade, BAR.nome_bairro, EDR.logradouro,
									EDR.numero, EDR.complemento, EDR.cep
									
								FROM
									{$conf['db_name']}conta_receber CTA_REC 
										INNER JOIN {$conf['db_name']}orcamento ORCT ON CTA_REC.idorcamento = ORCT.idorcamento

										LEFT OUTER JOIN {$conf['db_name']}cliente CLI ON ORCT.idcliente=CLI.idcliente
										LEFT OUTER JOIN {$conf['db_name']}cliente_fisico FCLI ON FCLI.idcliente=CLI.idcliente
										LEFT OUTER JOIN {$conf['db_name']}cliente_juridico JCLI ON JCLI.idcliente=CLI.idcliente

										LEFT OUTER JOIN {$conf['db_name']}endereco EDR ON EDR.idendereco = CLI.idendereco_cliente
										LEFT OUTER JOIN {$conf['db_name']}estado EST ON EST.idestado = EDR.idestado
										LEFT OUTER JOIN {$conf['db_name']}cidade CID ON CID.idcidade = EDR.idcidade
										LEFT OUTER JOIN {$conf['db_name']}bairro BAR ON BAR.idbairro = EDR.idbairro
										
										
										INNER JOIN {$conf['db_name']}funcionario FNC_CR ON ORCT.idfuncionario=FNC_CR.idfuncionario

								WHERE 
									ORCT.idorcamento = " . $idorcamento . "
									$filtro_cartao

								ORDER BY 
									ORCT.idorcamento DESC
							";

			}
			// faz a busca de vendas normal
			else { 

				// prepara o filtro de datas
				$filtro_datas = "";				
				if ($post['data_vencimento_de'] != "") $filtro_datas .= " AND CTA_REC.data_vencimento >= '" . $form->FormatadataParaInserir($post['data_vencimento_de']) . "' ";
				if ($post['data_vencimento_ate'] != "") $filtro_datas .= " AND CTA_REC.data_vencimento <= '" . $form->FormatadataParaInserir($post['data_vencimento_ate']) . "' ";
	
				// prepara o filtro de cliente
				$filtro_cliente = "";
				if ($post['idcliente'] != "") $filtro_cliente = " ORCT.idcliente = {$post['idcliente']} AND ";


				// prepara o filtro da baixa das contas 
				$fitro_baixa = "";
		 		if ($_GET['ac'] == "baixa_conta_receber") $fitro_baixa = " AND CTA_REC.baixa_conta = '0' AND  CTA_REC.status_conta != 'NE' ";


				// prepara o filtro da negociação das contas 
				$fitro_negociacao = "";
		 		if ($_GET['ac'] == "negociar_conta_receber") $fitro_negociacao = " AND CTA_REC.baixa_conta = '0' AND  CTA_REC.status_conta != 'NE' ";

				//Prepara o filtro para o tipo de conta a receber
				$filtro_tipo = NULL;
				if(!empty($post['tipo_conta'])) $filtro_tipo = " AND CTA_REC.tipo_conta = '".$post['tipo_conta']."' ";


							$list_sql = "	SELECT
												Distinct (ORCT.idorcamento), ORCT.*, CLI.nome_cliente, FNC_CR.nome_funcionario as funcionario_criou_orcamento,
												JCLI.cnpj_cliente, FCLI.cpf_cliente
											FROM
												{$conf['db_name']}conta_receber CTA_REC 
													INNER JOIN {$conf['db_name']}orcamento ORCT ON CTA_REC.idorcamento = ORCT.idorcamento
													LEFT OUTER JOIN {$conf['db_name']}cliente CLI ON ORCT.idcliente=CLI.idcliente
             										LEFT OUTER JOIN {$conf['db_name']}cliente_fisico FCLI ON FCLI.idcliente=CLI.idcliente
													LEFT OUTER JOIN {$conf['db_name']}cliente_juridico JCLI ON JCLI.idcliente=CLI.idcliente
													INNER JOIN {$conf['db_name']}funcionario FNC_CR ON ORCT.idfuncionario=FNC_CR.idfuncionario
													
											WHERE 

												$filtro_cliente

												ORCT.idfilial = " . $_SESSION['idfilial_usuario'] . "
													AND
												ORCT.tipoOrcamento <> 'O'
		
												$filtro_datas		
	
												$fitro_baixa

												$fitro_negociacao
												
												$filtro_cartao
												
												$filtro_tipo
												
											ORDER BY 
												ORCT.idorcamento DESC
										";
										
										




			}

			//manda fazer a paginação
			$list_q = $db->query($list_sql);

			if($list_q){
				
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					if ($list['tipoOrcamento'] == "NF") $list['tipoOrcamentoDescricao'] = "Nota Fiscal";
					else if ($list['tipoOrcamento'] == "SD") $list['tipoOrcamentoDescricao'] = "Série D";
					else if ($list['tipoOrcamento'] == "ECF") $list['tipoOrcamentoDescricao'] = "Cupom Fiscal";

					if ($list['datahoraCriacao'] != '0000-00-00 00:00:00') {
						$array = split(" ",$list['datahoraCriacao']);
						$list['datahoraCriacao'] = $form->FormataDataParaExibir($array[0]) . " " . $array[1];
					}
					else $list['datahoraCriacao'] = "";
					


					if( !empty($list['cnpj_cliente']) ){
						$list['cpf_cnpj'] = $list['cnpj_cliente'];
					}
					elseif( !empty($list['cpf_cliente']) ){
	          			$list['cpf_cnpj'] = $list['cpf_cliente'];
					}
					
					$list['endereco_cliente'] = $list['logradouro'] . ', ' . $list['numero'] . ' - ' . $list['complemento'] . "\n" .
												$list['nome_bairro'] . "\n" .
												$list['nome_cidade'] . ' / ' . $list['nome_estado'] . "\n" .
												$list['cep'];
									

					if( !empty($list['cpf_cliente']) )	unset($list['cpf_cliente']);
					if( !empty($list['cnpj_cliente']) )	unset($list['cnpj_cliente']);
					if( !empty($list['logradouro']) )	unset($list['logradouro']);
					if( !empty($list['numero']) )		unset($list['numero']);
					if( !empty($list['complemento']) )	unset($list['complemento']);
					if( !empty($list['nome_bairro']) )	unset($list['nome_bairro']);
					if( !empty($list['nome_cidade']) )	unset($list['nome_cidade']);
					if( !empty($list['nome_estado']) )	unset($list['nome_estado']);
					if( !empty($list['cep']) )			unset($list['cep']);

	          		$cont++;

					$list_return[] = $list;
				}

				return $list_return;
					
			}	
			else{
				$this->err = $falha['listar'];
				return(0);
			}
		}	
		


		/**
		  método: Busca_Contas_Receber
		  propósito: faz a listagem de contas a receber por registro de venda
		*/
		
				function Busca_Contas_Receber ($list_vendas, $contas_sem_baixa=0){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------


			//busca os registros no banco de dados e monta o vetor de retorno
			$list_return = array();			
			

			// prepara o filtro de datas
			$filtro_datas = "";				
			if ($_POST['data_vencimento_de'] != "") $filtro_datas .= " AND CTA_REC.data_vencimento >= '" . $form->FormatadataParaInserir($_POST['data_vencimento_de']) . "' ";
			if ($_POST['data_vencimento_ate'] != "") $filtro_datas .= " AND CTA_REC.data_vencimento <= '" . $form->FormatadataParaInserir($_POST['data_vencimento_ate']) . "' ";


			// prepara o filtro da baixa das contas 
			$filtro_baixa = NULL;
			if ( ($_GET['ac'] == "baixa_conta_receber") || ($contas_sem_baixa==1)) $filtro_baixa = " AND CTA_REC.baixa_conta = '0'  AND  CTA_REC.status_conta != 'NE' ";

			//$filtro_cartao = "AND (CTA_REC.sigla_modo_recebimento <> 'CC' AND CTA_REC.sigla_modo_recebimento <> 'CD')";
			
			
			// percorre o registro de vendas
			for ($i=0; $i<count($list_vendas); $i++) {
						
				//Prepara filtro do orçamento	
				if(!empty($list_vendas[$i]['idorcamento']))
					$filtro_orcamento = 'CTA_REC.idorcamento = ' . $list_vendas[$i]['idorcamento'];
				else
					$filtro_orcamento = "CTA_REC.idconta_receber = ". $list_vendas[$i]['idconta_receber']; //Para contas avulsas


				// busca as contas a receber de acordo com o registro de vendas
				$list_sql = "	SELECT
												CTA_REC.*   , MREC.sigla_modo_recebimento, MREC.descricao as descricao_modo_recebimento
											FROM
												{$conf['db_name']}conta_receber CTA_REC 
												INNER JOIN {$conf['db_name']}modo_recebimento MREC ON CTA_REC.sigla_modo_recebimento=MREC.sigla_modo_recebimento 
												
											WHERE 
												
												$filtro_orcamento

												$filtro_datas

												$fitro_baixa
												
												$filtro_cartao
	
											ORDER BY 
												CTA_REC.data_vencimento ASC, CTA_REC.numero_seq_conta ASC
										";				


				//manda fazer a paginação
				$list_q = $db->query($list_sql);

				// percorre os registros de contas a receber, por venda	
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					if ($list['data_vencimento'] != '0000-00-00') $list['data_vencimento'] = $form->FormataDataParaExibir($list['data_vencimento']); 
					else $list['data_vencimento'] = "";

					if ($list['data_recebimento'] != '0000-00-00') $list['data_recebimento'] = $form->FormataDataParaExibir($list['data_recebimento']); 
					else $list['data_recebimento'] = "";

					if ($list['data_baixa'] != '0000-00-00') $list['data_baixa'] = $form->FormataDataParaExibir($list['data_baixa']); 
					else $list['data_baixa'] = "";

					if ($list['status_conta'] == 'RE') $list['status_conta'] = "Recebido"; 
					else if ($list['status_conta'] == 'RP') $list['status_conta'] = "Rec. Parcial"; 
					else if ($list['status_conta'] == 'NA') $list['status_conta'] = "Não Recebido"; 
					else if ($list['status_conta'] == 'NE') $list['status_conta'] = "Negociado"; 


					$list['baixa_conta_bk'] = $list['baixa_conta'];

					if ($list['baixa_conta'] == '0') $list['baixa_conta'] = "Não"; 
					else if ($list['baixa_conta'] == '1') $list['baixa_conta'] = "Sim"; 

					
					$list['valor_total_conta'] = $list['valor_basico_conta'] + $list['valor_juros_parcela'];
					if ($list['valor_total_conta'] != "") $list['valor_total_conta'] = number_format($list['valor_total_conta'],2,",",""); 

					if ($list['valor_basico_conta'] != "") $list['valor_basico_conta'] = number_format($list['valor_basico_conta'],2,",",""); 
					if ($list['valor_juros_parcela'] != "") $list['valor_juros_parcela'] = number_format($list['valor_juros_parcela'],2,",",""); 
					if ($list['valor_juros_atraso'] != "") $list['valor_juros_atraso'] = number_format($list['valor_juros_atraso'],2,",",""); 
					if ($list['valor_multa'] != "") $list['valor_multa'] = number_format($list['valor_multa'],2,",",""); 
					if ($list['valor_recebido'] != "") $list['valor_recebido'] = number_format($list['valor_recebido'],2,",",""); 
					

					$list_return[$i][$cont]['idconta_receber'] = $list['idconta_receber'];
					$list_return[$i][$cont]['numero_seq_conta'] = $list['numero_seq_conta'];
					$list_return[$i][$cont]['descricao_modo_recebimento'] = $list['descricao_modo_recebimento'];
					$list_return[$i][$cont]['descricao_conta'] = $list['descricao_conta'];
					$list_return[$i][$cont]['valor_total_conta'] = $list['valor_total_conta'];	
					$list_return[$i][$cont]['valor_juros_atraso'] = $list['valor_juros_atraso'];	
					$list_return[$i][$cont]['valor_multa'] = $list['valor_multa'];					
					$list_return[$i][$cont]['valor_recebido'] = $list['valor_recebido'];
					$list_return[$i][$cont]['data_vencimento'] = $list['data_vencimento'];
					$list_return[$i][$cont]['data_recebimento'] = $list['data_recebimento'];
					$list_return[$i][$cont]['data_baixa'] = $list['data_baixa'];
					$list_return[$i][$cont]['status_conta'] = $list['status_conta'];
					$list_return[$i][$cont]['baixa_conta'] = $list['baixa_conta'];
					$list_return[$i][$cont]['baixa_conta_bk'] = $list['baixa_conta_bk'];
					
					$cont++;
				
				}


			} // fim do for


			return $list_return;


		}	
		



		/**
		  método: Busca_Contas_Receber_Pela_Venda
		  propósito: faz a listagem de contas a receber por registro de venda
		*/
		
		function Busca_Contas_Receber_Pela_Venda ($idvenda){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------


			//busca os registros no banco de dados e monta o vetor de retorno
			$list_return = array();			
			

			// busca as contas a receber de acordo com o registro de vendas
			$list_sql = "	SELECT
											CTA_REC.*   , MREC.sigla_modo_recebimento, MREC.descricao as descricao_modo_recebimento
										FROM
											{$conf['db_name']}conta_receber CTA_REC 
												INNER JOIN {$conf['db_name']}modo_recebimento MREC ON CTA_REC.sigla_modo_recebimento=MREC.sigla_modo_recebimento 
											
										WHERE 
											CTA_REC.idorcamento = " . $idvenda . "

										ORDER BY 
											CTA_REC.data_vencimento ASC, CTA_REC.numero_seq_conta ASC
									";				


			//manda fazer a paginação
			$list_q = $db->query($list_sql);

			// percorre os registros de contas a receber, por venda	
			while($list = $db->fetch_array($list_q)){

				if ($list['data_vencimento'] != '0000-00-00') $list['data_vencimento'] = $form->FormataDataParaExibir($list['data_vencimento']); 
				else $list['data_vencimento'] = "";

				if ($list['data_recebimento'] != '0000-00-00') $list['data_recebimento'] = $form->FormataDataParaExibir($list['data_recebimento']); 
				else $list['data_recebimento'] = "";

				if ($list['data_baixa'] != '0000-00-00') $list['data_baixa'] = $form->FormataDataParaExibir($list['data_baixa']); 
				else $list['data_baixa'] = "";


				// pode negociar a conta quando ela não teve baixa e não foi negociada
				if ( ($list['baixa_conta'] == '0') && ($list['status_conta'] != 'NE') ) $list['negociar'] = "1";
				// não pode negociar
				else $list['negociar'] = "0"; 				



				if ($list['status_conta'] == 'RE') $list['status_conta'] = "Recebido"; 
				else if ($list['status_conta'] == 'RP') $list['status_conta'] = "Rec. Parcial"; 
				else if ($list['status_conta'] == 'NA') $list['status_conta'] = "Não Recebido"; 
				else if ($list['status_conta'] == 'NE') $list['status_conta'] = "Negociado"; 


				if ($list['baixa_conta'] == '0') $list['baixa_conta'] = "Não"; 
				else if ($list['baixa_conta'] == '1') $list['baixa_conta'] = "Sim"; 

				
				$list['valor_total_conta'] = $list['valor_basico_conta'] + $list['valor_juros_parcela'];
				if ($list['valor_total_conta'] != "") $list['valor_total_conta'] = number_format($list['valor_total_conta'],2,",",""); 

				if ($list['valor_basico_conta'] != "") $list['valor_basico_conta'] = number_format($list['valor_basico_conta'],2,",",""); 
				if ($list['valor_juros_parcela'] != "") $list['valor_juros_parcela'] = number_format($list['valor_juros_parcela'],2,",",""); 
				if ($list['valor_juros_atraso'] != "") $list['valor_juros_atraso'] = number_format($list['valor_juros_atraso'],2,",",""); 
				if ($list['valor_multa'] != "") $list['valor_multa'] = number_format($list['valor_multa'],2,",",""); 
				if ($list['valor_recebido'] != "") $list['valor_recebido'] = number_format($list['valor_recebido'],2,",",""); 
				
        $list_return[] = $list;
			
			}

			return $list_return;

		}	
		


		/**
		  método: Deleta_Contas_Receber
		  propósito: Deleta_Contas_Receber
		*/
		function Deleta_Contas_Receber ($idorcamento) {

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$delete_sql = "	DELETE FROM
												{$conf['db_name']}conta_receber
											WHERE
												 idorcamento = $idorcamento ";
			$delete_q = $db->query($delete_sql);

			if($delete_q){
				return(1);
			}
			else{
				$this->err = $falha['excluir'];
				return(0);
			}

		}



		/**
		  método: Grava_Simulacao_Contas_Receber
		  propósito: Grava_Simulacao_Contas_Receber
		*/
		function Grava_Simulacao_Contas_Receber () {

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$numero_sequencial_conta = 0;

			// Grava as contas a receber a VISTA
			for ($i=1; $i<=$_POST['total_contas_receber_a_vista']; $i++) {

				if (isset($_POST["sigla_".$i])) {

					$info_conta['sigla_modo_recebimento'] = $_POST["sigla_".$i];
					$info_conta['idfilial'] = $_SESSION['idfilial_usuario'];
					$info_conta['idorcamento'] = $_GET['idorcamento'];
					$info_conta['idfuncionario'] = $_POST['idfuncionario'];
					$info_conta['idUltFuncionario'] = $_POST['idfuncionario'];

					$numero_sequencial_conta++;
					$info_conta['numero_seq_conta'] =  $numero_sequencial_conta;

					$info_conta['descricao_conta'] =  "Pagamento a vista $numero_sequencial_conta";

					$valor_basico_conta = str_replace(",",".",$_POST["valor_a_vista_".$i]);
					$info_conta['valor_basico_conta'] = $valor_basico_conta;
					$info_conta['valor_juros_parcela'] = "0.00";
					$info_conta['valor_juros_atraso'] = "0.00";
					$info_conta['valor_multa'] = "0.00";
					$info_conta['valor_recebido'] = "0.00";

					$info_conta['data_cadastro'] = date('Y-m-d H:i:s');
					$info_conta['data_vencimento'] = date('Y-m-d');
					$info_conta['data_recebimento'] = "NULL";
					$info_conta['data_baixa'] = "NULL";
					$info_conta['data_ult_alteracao'] = date('Y-m-d H:i:s');
					$info_conta['status_conta'] = "NULL";
					$info_conta['baixa_conta'] = "NULL";
					$info_conta['tipo_conta'] = "E"; // entrada
					$info_conta['observacao'] = "";

					$this->set($info_conta);

				} // fim do if

			} // fim do for


			// Grava as contas a receber a PRAZO
			for ($i=1; $i<=$_POST['total_contas_receber_a_prazo']; $i++) {

				if (isset($_POST["CR_descricao_".$i])) {

					$info_conta['sigla_modo_recebimento'] = $_POST['modo_recebimento_a_prazo'];
					$info_conta['idfilial'] = $_SESSION['idfilial_usuario'];
					$info_conta['idorcamento'] = $_GET['idorcamento'];
					$info_conta['idfuncionario'] = $_POST['idfuncionario'];
					$info_conta['idUltFuncionario'] = $_POST['idfuncionario'];

					$numero_sequencial_conta++;
					$info_conta['numero_seq_conta'] =  $numero_sequencial_conta;

					$info_conta['descricao_conta'] =  $_POST["CR_descricao_".$i];

					$valor_basico_conta = str_replace(",",".",$_POST["CR_valor_basico_".$i]);
					$valor_conta = str_replace(",",".",$_POST["CR_valor_".$i]);

					$info_conta['valor_basico_conta'] = $valor_basico_conta;

					$valor_juros_parcela = $valor_conta - $valor_basico_conta;
					$info_conta['valor_juros_parcela'] = $valor_juros_parcela;

					$info_conta['valor_juros_atraso'] = "0.00";
					$info_conta['valor_multa'] = "0.00";
					$info_conta['valor_recebido'] = "0.00";

					$info_conta['data_cadastro'] = date('Y-m-d H:i:s');
					$info_conta['data_vencimento'] = $form->FormatadataParaInserir($_POST["CR_data_".$i]);
					$info_conta['data_recebimento'] = "NULL";
					$info_conta['data_baixa'] = "NULL";
					$info_conta['data_ult_alteracao'] = date('Y-m-d H:i:s');

					$info_conta['status_conta'] = "NULL";
					$info_conta['baixa_conta'] = "NULL";
					$info_conta['tipo_conta'] = "P"; // parcelas
					$info_conta['observacao'] = "";

					$this->set($info_conta);

				} // fim do if

			} // fim do for


		}



		/**
		  método: Grava_Contas_Receber
		  propósito: Grava_Contas_Receber

			se $contas_negociadas for 1, o valor basico da conta deve ser recalculado, 
			pois indica que foi uma negociação de contas a receber
		*/
		function Grava_Contas_Receber ($contas_negociadas = 0) {

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;

			global $modo_recebimento;
			global $movimento;
			
			//---------------------

			
			// inicia a transação
			$db->query('begin');
					

			// calcula o total final da venda
			$total_final_da_venda = $_POST['valor_total_a_vista'] + $_POST['valor_total_a_prazo'];

			$numero_sequencial_conta = 0;

			// Grava as contas a receber a VISTA
			for ($i=1; $i<=$_POST['total_contas_receber_a_vista']; $i++) {

				if (isset($_POST["sigla_".$i])) {

					// busca os dados do modo de recebimento
					$info_modo_recebimento = $modo_recebimento->getById($_POST["sigla_".$i]);
					
					// se for baixa automatica
					if ($info_modo_recebimento['baixa_automatica'] == '1') {
						$dias_baixa_automatica_vista	= $info_modo_recebimento['dias_baixa_automatica_vista'];					

						$data_baixa = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+$dias_baixa_automatica_vista, date("Y")) );
						$data_recebimento =	$data_baixa;
						$baixa_conta = "1";
						$status_conta = "RE"; // recebido
						$valor_recebido = str_replace(",",".",$_POST["valor_a_vista_".$i]);
					}
					// não é baixa automática
					else {

						// se for pago com cheque
						if ($_POST["sigla_".$i] == $conf['sigla_modo_cheque']) {
							$data_recebimento = date('Y-m-d');
							$status_conta = "RE"; //recebido
							$valor_recebido = str_replace(",",".",$_POST["valor_a_vista_".$i]);
						}
						else {
							$data_recebimento = "NULL";
							$status_conta = "NA"; // não recebido
							$valor_recebido = "0.00";
						}

						$data_baixa = "NULL";
						$baixa_conta = "0";

					}	
					//------------------------------------------------------------------------------------


					$info_conta['sigla_modo_recebimento'] = $_POST["sigla_".$i];
					$info_conta['idfilial'] = $_SESSION['idfilial_usuario'];
					$info_conta['idorcamento'] = $_GET['idorcamento'];
					$info_conta['idfuncionario'] = $_POST['idfuncionario'];
					$info_conta['idUltFuncionario'] = $_POST['idfuncionario'];

					// processo normal
					if ($contas_negociadas == 0) {
						$numero_sequencial_conta++;
						$info_conta['numero_seq_conta'] =  $numero_sequencial_conta;

						$info_conta['descricao_conta'] =  "Venda {$_GET['idorcamento']} - Conta $numero_sequencial_conta (Vista)";

						$valor_basico_conta = str_replace(",",".",$_POST["valor_a_vista_".$i]);

						$info_conta['valor_juros_parcela'] = "0.00";
					}
					// negociação de contas a receber
					else {
						$info_sequencial = $this->BuscaProximoNumeroSequencial($_GET['idorcamento']);
						$info_conta['numero_seq_conta'] = $info_sequencial['proximo_sequencial'];

						$info_conta['descricao_conta'] =  "Venda {$_GET['idorcamento']} - Conta " . $info_conta['numero_seq_conta'] . " (Negociação: $contas_negociadas)";

						$valor_da_conta = str_replace(",",".",$_POST["valor_a_vista_".$i]);
						$valor_basico_conta = ($_POST['valor_basico_total'] * $valor_da_conta) / $total_final_da_venda;
						$valor_basico_conta = $form->FormataMoedaParaExibir($valor_basico_conta);
						$valor_basico_conta = $form->FormataMoedaParaInserir($valor_basico_conta);

						$valor_juros_parcela = $valor_da_conta - $valor_basico_conta;
						$info_conta['valor_juros_parcela'] = $valor_juros_parcela;
					}
					
					$info_conta['valor_basico_conta'] = $valor_basico_conta;
					$info_conta['valor_juros_atraso'] = "0.00";
					$info_conta['valor_multa'] = "0.00";
					$info_conta['valor_recebido'] = $valor_recebido;

					$info_conta['data_cadastro'] = date('Y-m-d H:i:s');
					$info_conta['data_vencimento'] = date('Y-m-d');
					$info_conta['data_recebimento'] = $data_recebimento;
					$info_conta['data_baixa'] = $data_baixa;
					$info_conta['data_ult_alteracao'] = date('Y-m-d H:i:s');

					$info_conta['status_conta'] = $status_conta;
					$info_conta['baixa_conta'] = $baixa_conta;
					$info_conta['tipo_conta'] = "E"; // entrada
					$info_conta['observacao'] = "";



					$idconta_receber = $this->set($info_conta);
					$err = $this->err;
					
					if(count($err) > 0){
						$db->query('rollback');
						$this->err = "Houve uma falha ao registrar as contas a receber. Por favor, entre em contato com os autores.";
						return(0);
					}									
					else{
					
						//Trata as informações para o registro de movimentação financeira						
						$novo_movimento['idconta_receber'] = $idconta_receber;
						$novo_movimento['idfilial'] = $_SESSION['idfilial_usuario'];
						$novo_movimento['descricao_movimento'] = $info_conta['descricao_conta'];
						$novo_movimento['valor_movimento'] = $valor_basico_conta;
						$novo_movimento['data_cadastro'] = date("Y-m-d H:i:s");
						$novo_movimento['data_movimento'] = date("Y-m-d");				
						$novo_movimento['data_vencimento'] = date('Y-m-d');
						$novo_movimento['valor_juros'] = $info_conta['valor_juros_parcela'];
						$novo_movimento['valor_multa'] = '0.00';
						$novo_movimento['idplano_debito'] = $_POST['idplano_debito_vista'];
						$novo_movimento['idplano_credito'] = $_POST['idplano_credito_vista'];
						
						if($baixa_conta == "1"){
							$novo_movimento['baixado'] = $baixa_conta;
							$novo_movimento['data_baixa'] = $data_baixa  . date("H:i:s");	//Deixa a data_baixa no formato datetime
						}
						else{
							$novo_movimento['baixado'] = '0';
						}											
						
						$movimento->set($novo_movimento);
						$err = $movimento->err;
						
						if(count($err) > 0){
							$db->query('rollback');
							$this->err = "Houve uma falha ao registrar a movimentação financeira. Por favor, entre em contato com os autores.";
							return(0);
						}
					}

				} // fim do if

			} // fim do for


			// ##################################################################################################


			// Grava as contas a receber a PRAZO
			for ($i=1; $i<=$_POST['total_contas_receber_a_prazo']; $i++) {

				if (isset($_POST["CR_descricao_".$i])) {

					// busca os dados do modo de recebimento
					$info_modo_recebimento = $modo_recebimento->getById($_POST['modo_recebimento_a_prazo']);
					
					// se for baixa automatica
					if ($info_modo_recebimento['baixa_automatica'] == '1') {
						$dias_baixa_automatica_prazo	= $info_modo_recebimento['dias_baixa_automatica_prazo'];					

						$array_data = split("/",$_POST["CR_data_".$i]);
						$dia = $array_data[0];
						$mes = $array_data[1];
						$ano = $array_data[2];

						$data_baixa = date("Y-m-d", mktime(0, 0, 0, $mes, $dia+$dias_baixa_automatica_prazo, $ano) );
						$data_recebimento =	$data_baixa;
						$baixa_conta = "1";
						$status_conta = "RE"; // recebido
						$valor_recebido = str_replace(",",".",$_POST["CR_valor_".$i]);
					}
					// não é baixa automática
					else {

						// se for pago com cheque
						if ($_POST['modo_recebimento_a_prazo'] == $conf['sigla_modo_cheque']) {
							$data_recebimento = $form->FormatadataParaInserir($_POST["CR_data_".$i]);
							$status_conta = "RE"; //recebido
							$valor_recebido = str_replace(",",".",$_POST["CR_valor_".$i]);
						}
						else {
							$data_recebimento = "NULL";
							$status_conta = "NA"; // não recebido
							$valor_recebido = "0.00";
						}

						$data_baixa = "NULL";
						$baixa_conta = "0";

					}	
					//------------------------------------------------------------------------------------



					$info_conta['sigla_modo_recebimento'] = $_POST['modo_recebimento_a_prazo'];
					$info_conta['idfilial'] = $_SESSION['idfilial_usuario'];
					$info_conta['idorcamento'] = $_GET['idorcamento'];
					$info_conta['idfuncionario'] = $_POST['idfuncionario'];
					$info_conta['idUltFuncionario'] = $_POST['idfuncionario'];

					$valor_conta = str_replace(",",".",$_POST["CR_valor_".$i]);

					// processo normal
					if ($contas_negociadas == 0) {
						$numero_sequencial_conta++;
						$info_conta['numero_seq_conta'] =  $numero_sequencial_conta;

						$info_conta['descricao_conta'] =  "Venda {$_GET['idorcamento']} - Conta $numero_sequencial_conta (Parcela)";

						$valor_basico_conta = str_replace(",",".",$_POST["CR_valor_basico_".$i]);
					}
					// negociação de contas a receber
					else {
						$info_sequencial = $this->BuscaProximoNumeroSequencial($_GET['idorcamento']);
						$info_conta['numero_seq_conta'] = $info_sequencial['proximo_sequencial'];

						$info_conta['descricao_conta'] =  "Venda {$_GET['idorcamento']} - Conta " . $info_conta['numero_seq_conta'] . " (Negociação: $contas_negociadas)";

						$valor_da_conta = $valor_conta;
						$valor_basico_conta = ($_POST['valor_basico_total'] * $valor_da_conta) / $total_final_da_venda;
						$valor_basico_conta = $form->FormataMoedaParaExibir($valor_basico_conta);
						$valor_basico_conta = $form->FormataMoedaParaInserir($valor_basico_conta);
					}

					$info_conta['valor_basico_conta'] = $valor_basico_conta;

					$valor_juros_parcela = $valor_conta - $valor_basico_conta;
					$info_conta['valor_juros_parcela'] = $valor_juros_parcela;

					$info_conta['valor_juros_atraso'] = "0.00";
					$info_conta['valor_multa'] = "0.00";
					$info_conta['valor_recebido'] = $valor_recebido;

					$info_conta['data_cadastro'] = date('Y-m-d H:i:s');
					$info_conta['data_vencimento'] = $form->FormatadataParaInserir($_POST["CR_data_".$i]);
					$info_conta['data_recebimento'] = $data_recebimento;
					$info_conta['data_baixa'] = $data_baixa;
					$info_conta['data_ult_alteracao'] = date('Y-m-d H:i:s');

					$info_conta['status_conta'] = $status_conta;
					$info_conta['baixa_conta'] = $baixa_conta;
					$info_conta['tipo_conta'] = "P"; // parcelas
					$info_conta['observacao'] = "";

					$idconta_receber = $this->set($info_conta);
					
					if(count($err) > 0){
						$db->query('rollback');
						$this->err = "Houve uma falha ao registrar as contas a receber. Por favor, entre em contato com os autores.";
						return(0);
					}
					else{
											
						//Trata as informações para o registro de movimentação financeira						
	
						$novo_movimento['idconta_receber'] = $idconta_receber;
						$novo_movimento['idfilial'] = $_SESSION['idfilial_usuario'];
						$novo_movimento['descricao_movimento'] = $info_conta['descricao_conta'];
						$novo_movimento['valor_movimento'] = $valor_conta;
						$novo_movimento['data_cadastro'] = date("Y-m-d H:i:s");
						$novo_movimento['data_movimento'] = date("Y-m-d");
						$novo_movimento['baixado'] = $baixa_conta;
						$novo_movimento['data_vencimento'] = $info_conta['data_vencimento'];
						$novo_movimento['valor_juros'] = '0.00';
						$novo_movimento['valor_multa'] = '0.00';
						$novo_movimento['idplano_debito'] = $_POST['idplano_debito_prazo'];
						$novo_movimento['idplano_credito'] = $_POST['idplano_credito_prazo'];
												
						$movimento->set($novo_movimento);
						$err = $movimento->err;
						if(count($err) > 0){
							$db->query('rollback');
							$this->err = "Houve uma falha ao registrar a movimentação financeira. Por favor, entre em contato com os autores.";
							return(0);
						}
						
					}
					

				} // fim do if

			} // fim do for
			
			//Se todos os registros ocorrem sem erros, faz o commit para o banco de dados.
			if(count($err) == 0){ 
				$db->query('commit');
				return(1);
			}


		}

		


		/**
		  método: getById
		  propósito: busca informações
		*/
		function getById($idconta_receber){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											CTA_REC.*  , MREC.sigla_modo_recebimento, MREC.descricao as descricao_modo_recebimento
										FROM
											{$conf['db_name']}conta_receber CTA_REC
													INNER JOIN {$conf['db_name']}modo_recebimento MREC ON CTA_REC.sigla_modo_recebimento=MREC.sigla_modo_recebimento

										WHERE
											 CTA_REC.idconta_receber = $idconta_receber ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				if ($get['data_vencimento'] != '0000-00-00') $get['data_vencimento'] = $form->FormataDataParaExibir($get['data_vencimento']); 
				else $get['data_vencimento'] = "";
					if ($get['data_recebimento'] != '0000-00-00') $get['data_recebimento'] = $form->FormataDataParaExibir($get['data_recebimento']); 
				else $get['data_recebimento'] = "";
					if ($get['data_baixa'] != '0000-00-00') $get['data_baixa'] = $form->FormataDataParaExibir($get['data_baixa']); 
				else $get['data_baixa'] = "";
					
				// calcula o valor da conta a receber
				$get['valor_conta_receber'] = $get['valor_basico_conta'] + $get['valor_juros_parcela'];
				if ($get['valor_conta_receber'] != "") $get['valor_conta_receber'] = number_format($get['valor_conta_receber'],2,",",""); 				


				if ($get['valor_basico_conta'] != "") $get['valor_basico_conta'] = number_format($get['valor_basico_conta'],2,",",""); 
					if ($get['valor_juros_parcela'] != "") $get['valor_juros_parcela'] = number_format($get['valor_juros_parcela'],2,",",""); 
					if ($get['valor_juros_atraso'] != "") $get['valor_juros_atraso'] = number_format($get['valor_juros_atraso'],2,",",""); 
					if ($get['valor_multa'] != "") $get['valor_multa'] = number_format($get['valor_multa'],2,",",""); 
					if ($get['valor_recebido'] != "") $get['valor_recebido'] = number_format($get['valor_recebido'],2,",",""); 
					
				
				if ($get['data_cadastro'] != '0000-00-00 00:00:00') { 
					$array = split(" ",$get['data_cadastro']); 
					$get['datahoraCriacao_D'] = $form->FormataDataParaExibir($array[0]); 
					$get['datahoraCriacao_H'] = $array[1]; 
				} 
				if ($get['data_ult_alteracao'] != '0000-00-00 00:00:00') { 
					$array = split(" ",$get['data_ult_alteracao']); 
					$get['datahoraUltAlteracao_D'] = $form->FormataDataParaExibir($array[0]); 
					$get['datahoraUltAlteracao_H'] = $array[1]; 
				} 

				if ($get['status_conta'] == "RE") $get['status_conta_descricao'] = "Recebido";
				else if ($get['status_conta'] == "RP") $get['status_conta_descricao'] = "Recebido Parcialmente";
				else if ($get['status_conta'] == "NA") $get['status_conta_descricao'] = "Não Recebido";
				else if ($get['status_conta'] == "NE") $get['status_conta_descricao'] = "Negociado";

				//retorna o vetor associativo com os dados
				return $get;
			}
			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}
				
		}

		/**
		  método: make_list
		  propósito: faz a listagem
		*/
		
		function make_list( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
			
			if ($ordem == "") $ordem = " ORDER BY CTA_REC.data_vencimento ASC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
										CTA_REC.*   , MREC.sigla_modo_recebimento
										FROM
           										 {$conf['db_name']}conta_receber CTA_REC 
												 INNER JOIN {$conf['db_name']}modo_recebimento MREC ON CTA_REC.sigla_modo_recebimento=MREC.sigla_modo_recebimento 
												
										$filtro
										$ordem";
			
			
			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

			if($list_q){
				
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){
					
					//insere um índice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);
					
					if ($list['data_vencimento'] != '0000-00-00') $list['data_vencimento'] = $form->FormataDataParaExibir($list['data_vencimento']); 
					else $list['data_vencimento'] = "";
					if ($list['data_recebimento'] != '0000-00-00') $list['data_recebimento'] = $form->FormataDataParaExibir($list['data_recebimento']); 
					else $list['data_recebimento'] = "";
					if ($list['data_baixa'] != '0000-00-00') $list['data_baixa'] = $form->FormataDataParaExibir($list['data_baixa']); 
					else $list['data_baixa'] = "";
					
					
					if ($list['valor_basico_conta'] != "") $list['valor_basico_conta'] = number_format($list['valor_basico_conta'],2,",",""); 
					if ($list['valor_juros_parcela'] != "") $list['valor_juros_parcela'] = number_format($list['valor_juros_parcela'],2,",",""); 
					if ($list['valor_juros_atraso'] != "") $list['valor_juros_atraso'] = number_format($list['valor_juros_atraso'],2,",",""); 
					if ($list['valor_multa'] != "") $list['valor_multa'] = number_format($list['valor_multa'],2,",",""); 
					if ($list['valor_recebido'] != "") $list['valor_recebido'] = number_format($list['valor_recebido'],2,",",""); 
					
					if ($list['status_conta'] == 'RE') $list['status_conta'] = "Recebido"; 
					else if ($list['status_conta'] == 'RP') $list['status_conta'] = "Recebido Parcialmente"; 
					else if ($list['status_conta'] == 'NA') $list['status_conta'] = "Não Recebido"; 
					else if ($list['status_conta'] == 'NE') $list['status_conta'] = "Negociado"; 
					if ($list['baixa_conta'] == '0') $list['baixa_conta'] = "Não"; 
					else if ($list['baixa_conta'] == '1') $list['baixa_conta'] = "Sim"; 
					
					
					
			        $list_return[] = $list;
			          
			        $cont++;
				}
				
				return $list_return;
					
			}	
			else{
				$this->err = $falha['listar'];
				return(0);
			}
		}	
		

		/**
			método: set
		  propósito: inclui novo registro
		*/

		function set($info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			
			
			
			
			$set_sql = "  INSERT INTO
		                  {$conf['db_name']}conta_receber
		                    (
												sigla_modo_recebimento, 
												idfilial,
												idorcamento,
												idfuncionario,
												idUltFuncionario,
												numero_seq_conta, 
												descricao_conta, 
												valor_basico_conta, 
												valor_juros_parcela, 
												valor_juros_atraso, 
												valor_multa, 
												valor_recebido, 
												data_cadastro,
												data_vencimento, 
												data_recebimento, 
												data_baixa, 
												data_ult_alteracao,
												status_conta, 
												tipo_conta,
												baixa_conta, 
												observacao 
												
												)
		                VALUES
		                    (
		                    
							
		                    					
		                    					'" . $info['sigla_modo_recebimento'] . "',  
												" . $info['idfilial'] . ",
 												" . $info['idorcamento'] . ",    
												" . $info['idfuncionario'] . ",  
												" . $info['idUltFuncionario'] . ",  
												" . $info['numero_seq_conta'] . ",  
												'" . $info['descricao_conta'] . "',  
												" . $info['valor_basico_conta'] . ",  
												" . $info['valor_juros_parcela'] . ",  
												" . $info['valor_juros_atraso'] . ",  
												" . $info['valor_multa'] . ",  
												" . $info['valor_recebido'] . ",  
												'" . $info['data_cadastro'] . "',
												'" . $info['data_vencimento'] . "',  
												'" . $info['data_recebimento'] . "',  
												'" . $info['data_baixa'] . "',
												'" . $info['data_ult_alteracao'] . "',    
												'" . $info['status_conta'] . "',  
												'" . $info['tipo_conta'] . "',  
												'" . $info['baixa_conta'] . "',  
												'" . $info['observacao'] . "'   
												
												)";


			//executa a query e testa se a consulta foi "boa"
			if($db->query($set_sql)){
				//retorna o código inserido
				$codigo = $db->insert_id();
				
				
				
				return($codigo);
			}
			else{
				$this->err = $falha['inserir'];
				return(0);
			}
		}
		
		/**
		  método: update
		  propósito: atualiza os dados
		  
		  1) o vetor $info deve conter todos os campos tabela a serem atualizados
			2) a variável $id deve conter o código do usuário cujos dados serão atualizados
			3) campos literais deverão ter o prefixo lit e campos numéricos deverão ter o prefixo num
		*/
		function update($idconta_receber, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}conta_receber
											SET ";

   		//varre o formulário e monta a consulta;
			$cont_validos = 0;
			foreach($info as $campo => $valor){

				$tipo_campo = substr($campo, 0, 3);
				$nome_campo = substr($campo, 3, strlen($campo) - 3);
					
				if(($tipo_campo == "lit") || ($tipo_campo == "num")){
					
					$usu_validos["$campo"] = $valor;
					$cont_validos++;
					
				}
					
			}
			
			$cont = 0;
			foreach($usu_validos as $campo => $valor){
			
				$tipo_campo = substr($campo, 0, 3);
				$nome_campo = substr($campo, 3, strlen($campo) - 3);
				
				if($tipo_campo == "lit")
					$update_sql .= "$nome_campo = '$valor'";
				elseif($tipo_campo == "num")
					$update_sql .= "$nome_campo = $valor";
					
				$cont++;
				
				//testa se é o último
				if($cont != $cont_validos){
					$update_sql .= ", ";
				}
				
			}
			

			//completa o sql com a restrição
			$update_sql .= " WHERE  idconta_receber = $idconta_receber ";
			

			//envia a query para o banco
			$update_q = $db->query($update_sql);
			
			if($update_q)
			  return(1);
			else
			  $this->err = $falha['alterar'];
		}	
		
		/**
		  método: delete
		  propósito: excluir registro
		*/
		function delete($idconta_receber){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			// conjunto de dependências geradas
			
			//---------------------
			

			// verifica se pode excluir
			if (1) {

				

				$delete_sql = "	DELETE FROM
													{$conf['db_name']}conta_receber
												WHERE
													 idconta_receber = $idconta_receber ";
				$delete_q = $db->query($delete_sql);

				if($delete_q){
					return(1);
				}
				else{
					$this->err = $falha['excluir'];
					return(0);
				}
				
			}
			else {
				$this->err = "Este registro não pode ser excluído, pois existem registros relacionadas a ele.";
			}	

		}	

		
		/**
		  método: make_list
		  propósito: faz a listagem para colocar no select
		*/

		function make_list_select( $filtro = "", $ordem = " ORDER BY data_vencimento ASC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}conta_receber
										$filtro
										$ordem";

			$list_q = $db->query($list_sql);
			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					foreach($list as $campo => $value){
						$list_return["$campo"][$cont] = $value;
					}

          $cont++;
				}

				return $list_return;

			}
			else{
				$this->err = $falha['listar'];
				return(0);
			}
			
		}
		
		
		
		
		
		/**
		  método: conta_receber_cliente
		  propósito: Busca o total de contas a receber do cliente
		*/
		function busca_credito_cliente($idcliente){

			global $cliente;
			global $form;
			
			//pega os dados do cliente
			$dados_cliente= $cliente->getById($idcliente);
			
			
			// Busca os registros de compra do cliente
			$list_orcamento = $this->Busca_Vendas_Por_Periodo($dados_cliente);


			// lista as contas a receber de acordo com o registro de compreas
			$list_contas_receber = $this->Busca_Contas_Receber($list_orcamento,1);
			
			$err = $this->err;

			if(!$err){
				//Soma o total de contas a receber em aberto
				foreach($list_contas_receber as $conta_array){

					foreach($conta_array as $conta){

							$total_conta += $form->formataMoedaParaInserir($conta['valor_total_conta']);
					}

				}



				$credito['limite'] = $form->formataMoedaParaExibir($dados_cliente['limite_credito_cliente']);
				$credito['disponivel'] = $form->formataMoedaParaExibir($credito['limite'] - $total_conta);

				return $credito;
			}
			else{
				
				return false;
				
			}	
			



		}
		
		
		
		
		

	} // fim da classe
?>
