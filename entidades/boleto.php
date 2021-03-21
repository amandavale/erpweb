<?php

	require_once dirname(__FILE__) . '/movimento.php';
	require_once dirname(__FILE__) . '/filial.php';
	require_once dirname(__FILE__) . '/cliente.php';
	require_once dirname(__FILE__) . '/cliente_juridico.php';
	require_once dirname(__FILE__) . '/apartamento.php';
	
	class boleto {

		var $err;
		
		function boleto(){
			// construtor da classe
		}
                
                
                
		function SetDadosCedente($idmovimento){
            
            global $movimento, $cliente, $filial, $form, $conf, $db, $parametros;
            
            
            $info                 = $movimento->getById($idmovimento);
            $info_cliente         = $cliente->BuscaDadosCliente($info['idcliente_origem']);
            $info_filial          = $filial->BuscaDadosFilial($parametros->getParam('idfilial_boleto'));
            $info_cliente_destino = $cliente->BuscaDadosCliente($info['idcliente_destino']);
          

            // Dados de cobrança
            $dias_de_prazo_para_pagamento = 5;
            $taxa_boleto = empty($info['taxa_boleto']) ? 0.00 : $form->FormataMoedaParaInserir($info['taxa_boleto']);
            $valor_movimento = $form->FormataMoedaParaInserir($info['valor_movimento']);
            $dadosboleto["valor_boleto"] = $form->FormataMoedaParaExibir($valor_movimento + $taxa_boleto);

            
            
            $dadosboleto["nosso_numero"] = $idmovimento;
            $dadosboleto["numero_documento"] = str_pad($idmovimento, 5, '0', STR_PAD_LEFT); // Num do pedido ou do documento
            
            // Dados do Cedente
            $dadosboleto["identificacao"] = strtoupper($info_filial['nome_filial']);
            $dadosboleto["cpf_cnpj"]      = $info_filial['cnpj_filial'];
            $dadosboleto["endereco"]      = $info_filial['endereco']['linha1'];
            $dadosboleto["cidade_uf"]     = $info_filial['nome_cidade'].' / '.$info_filial['sigla_estado']; 
            $dadosboleto["cedente"]	  = strtoupper($info_filial['nome_filial']);
            
            $dadosboleto["data_vencimento"]    = $info['data_vencimento'];  //$data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
            $dadosboleto["data_documento"]     = date("d/m/Y");             // Data de emissão do Boleto
            $dadosboleto["data_processamento"] = date("d/m/Y");             // Data de processamento do boleto (opcional)	
            
            
            $endereco  = "{$info_cliente_destino['logradouro']},   {$info_cliente_destino['numero']} - apto: {$info['apto']}<br />";
            $endereco .= "{$info_cliente_destino['nome_bairro']} - {$info_cliente_destino['nome_cidade']} - {$info_cliente_destino['sigla_estado']}<br />";
            $endereco .=  "CEP: " . $info_cliente_destino['cep'];

            // DADOS DO CLIENTE
            $dadosboleto["sacado"] = $info_cliente['nome_cliente'] . ' ('.strtoupper($_SESSION['condominio']['nome_condominio']).')<br />'  . $endereco ;				

            //Endereço do cliente
            if ($info_cliente['logradouro'] != '')  $info_cliente['endereco1']  = $dadosboleto["logradouro"].', '.$info_cliente['numero'];
            if ($info_cliente['complemento'] != '') $info_cliente['endereco1'] .= $info_cliente['complemento'].' - ';			
            if ($info_cliente['nome_bairro'] != '') $info_cliente['endereco1'] .= $info_cliente['nome_bairro'];
            if ($info_cliente['nome_cidade'] != '') $info_cliente['endereco2'] .= $info_cliente['nome_cidade'].' - '.$info_cliente['sigla_estado'];			
            if ($info_cliente['cep'] != '')         $info_cliente['endereco2'] .= ' - '.$info_cliente['cep'];

            return $dadosboleto;
        }


		/**
		 * Método responsável pela geração de boleto do bradesco
		 * @param array $dadosboleto - Dados do boleto a ser gerado
		 * @param string $cedente - Nome do cedente, caso seja necessário incluir na pesquisa por contas
		 */
		function bradesco($dadosboleto, $condominio = false, $descricao_condominio = array(), $descricao_caixa = '', $cedente = ''){

			switch($cedente){
				case 'bradescomila':
					$cedente = 'mila center';
					$dadosboleto['logo'] = 'bradesco';
				break;
				case 'bradescosos':
					$cedente = 'sos prestadora';
					$dadosboleto['logo'] = 'sos';
				break;
			}

			$dadosboleto = $this->retornaDadosBoleto($dadosboleto, 237,$cedente);

			include_once "../boletos/include/funcoes_boleto.php"; 
			$dadosboleto = startBoleto($dadosboleto, 237,$condominio);
			include "../boletos/include/layout_bradesco.php";
		}

		/**
		 * Método responsável pela geração de boleto da caixa econômica
		 * para clientes que não são condomínio
		 * @param array $dadosboleto - Dados do boleto a ser gerado
		 */
		function caixa($dadosboleto, $condominio = false, $descricao_condominio = array(), $descricao_caixa = ''){

			$dadosboleto = $this->retornaDadosBoletoCaixa($dadosboleto);
			
			$data_venc = $dadosboleto["data_vencimento"];
			
			include_once "../boletos/include/funcoes_cef.php"; 
			$dadosboleto = startBoleto($dadosboleto);

			include "../boletos/include/layout_boleto.php";
			
		}
		

		/**
		 * Monta array com dados de um boleto da Caixa
		 * @param array $info - Array contendo dados do boleto já encontrados
		 */
		function retornaDadosBoletoCaixa($dadosboleto){

			require_once dirname(__FILE__) . '/conta_filial.php';

			global $form, $conf, $db, $falha, $filial;

			$conta_filial = new conta_filial();

			/// Busca no banco de dados as informações sobre a conta do bradesco
			$dados_conta = $conta_filial->buscaContaPorBanco(104,$_SESSION['idfilial_usuario']);

			// INFORMACOES PARA O CLIENTE
			$dadosboleto["demonstrativo1"] = $info['descricao_movimento'];
			$dadosboleto["demonstrativo2"] = '';
			$dadosboleto["demonstrativo3"] = "";
			
				
			// DADOS DA SUA CONTA - CEF
			$dadosboleto["agencia"]  = $dados_conta['agencia_filial']; // Num da agencia, sem digito
			$dadosboleto["conta"] 	 = ""; 	// Num da conta, sem digito
			$dadosboleto["conta_dv"] = ""; 	// Digito do Num da conta
			$dadosboleto['codigo_beneficiario'] = $dados_conta['identificador'];
				
			// DADOS PERSONALIZADOS - CEF
			$dadosboleto["conta_cedente"] 	 = "87000000192"; // ContaCedente do Cliente, sem digito (Somente Números)
			$dadosboleto["conta_cedente_dv"] = "5"; // Digito da ContaCedente do Cliente

			// Código da Carteira: pode ser SR (Sem Registro) ou CR (Com Registro) - (Confirmar com gerente qual usar)			
			// if(!$dados_conta['carteira']){

			// 	$dadosboleto["carteira"]         = "SR";
			// }
			// else{
				$dadosboleto["carteira"]         = "CR";
			// }
				
			$dadosboleto["inicio_nosso_numero"] = $dados_conta['prefixo_nosso_numero'];  // Número informado pelo gerente da conta, cobrança sem registro
			
			// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
			$dadosboleto["quantidade"] = "";
			$dadosboleto["valor_unitario"] = "";
			$dadosboleto["aceite"] = "";
			$dadosboleto["especie"] = "R$";
			$dadosboleto["especie_doc"] = "";

			$dadosboleto['local_pagamento'] = 'PAGAR PREFERENCIALMENTE NAS CASAS LOTÉRICAS ATÉ O VALOR LIMITE';
			$dadosboleto['logo'] = "../boletos/imagens/logocaixa.jpg";
				
			return $dadosboleto;
		}		


		/**
		 * Método responsável pela geração de boleto do sicoob
		 * @param array $dadosboleto - Dados do boleto a ser gerado
		 */
		function sicoob($dadosboleto, $condominio = false, $descricao_condominio = array(), $descricao_caixa = ''){

			$dadosboleto = $this->retornaDadosBoleto($dadosboleto, 756);
			include_once "../boletos/include/funcoes_boleto.php"; 
			$dadosboleto = startBoleto($dadosboleto, 756, $condominio);
			include "../boletos/include/layout_sicoob.php";			
		}

		/**
		 * Método responsável pela geração de boleto do itaú
		 * para clientes que não são condomínio
		 * @param array $dadosboleto - Dados do boleto a ser gerado
		 */
		function itau($dadosboleto, $condominio = false, $descricao_condominio = array(), $descricao_caixa = '', $cedente = ''){

			switch($cedente){
				case 'itauestrela':
					$cedente = 'estrela da mata';
					$dadosboleto['logo'] = 'itau';
				break;
				case 'itau':
					$cedente = 'sos prestadora';
					$dadosboleto['logo'] = 'sos';
				break;
			}

			$dadosboleto = $this->retornaDadosBoleto($dadosboleto, 341, $cedente);
			
			$data_venc = $dadosboleto["data_vencimento"];
			
			//$condominio = false;
			include_once "../boletos/include/funcoes_boleto.php"; 
			$dadosboleto = startBoleto($dadosboleto,341, $condominio);
			$dadosboleto['nosso_numero'] = $dadosboleto['carteira_descricao'] . '/' . 
											$dadosboleto['nosso_numero'] . '-' .
											$dadosboleto['dac_nosso_numero'];
			
			$dadosboleto['local_pagamento'] = "ATÉ O VENCIMENTO, PAGUE EM QUALQUER BANCO OU CORRESPONDENTE NÃO BANCÁRIO. " .
				"\nAPÓS O VENCIMENTO, ACESSE ITAU.COM.BR/BOLETOS E PAGUE EM QUALQUER BANCO OU CORRESPONDENTE NÃO BANCÁRIO.";
			$dadosboleto['label_cedente'] = 'Benefici&aacute;rio';
			$dadosboleto['label_dados_cedente'] = 'Nome do Benefici&aacute;rio / CNPJ / CPF / Endere&ccedil;o';
			$dadosboleto['label_sacado'] = 'Pagador';

			include "../boletos/include/layout_itau.php";
		}


		/**
		 * Retorna dos dados de boleto passados como parâmetro, além das informações
		 * do banco cujo código é também passado por parâmetro
		 * @param array $dadosboleto - Dados do boleto
		 * @param integer $banco - Dados do banco
		 * @param string $cedente - Nome do cedente, caso seja necessário incluir na pesquisa por contas
		 * @return array $dadosboleto
		 */
		private function retornaDadosBoleto($dadosboleto, $banco, $cedente = ''){

			require_once dirname(__FILE__) . '/conta_filial.php';
			require_once dirname(__FILE__) . '/filial.php';
			require_once dirname(__FILE__) . '/cliente_condominio.php';

			$conta_filial = new conta_filial();
			$filial = new filial();
			$cliente_condominio = new cliente_condominio();

			/// Busca no banco de dados as informações sobre a conta do bradesco
			$dados_conta = $conta_filial->buscaContaPorBanco($banco,$_SESSION['idfilial_usuario'], $cedente);

			$info_filial = $filial->BuscaDadosFilial($_SESSION['idfilial_usuario']);

			/// Se o cnpj da conta for diferente do cnpj da filial na qual o usuário está logado,
			/// o endereço da conta deve ser buscado entre os condomínios, por cnpj
			if($dados_conta['conta_cnpj'] != $info_filial['cnpj_filial']){
				$endereco = $cliente_condominio->Busca_Parametrizada(0, 1, "CCLI.cnpj = '" . $dados_conta['conta_cnpj'] . "'");
				$endereco = $endereco[0];

				$dadosboleto['endereco'] = $endereco['logradouro'] . ', ' .	$endereco['numero'] . ' - Bairro ' . $endereco['nome_bairro'];
				$dadosboleto['cidade_uf'] = $endereco['nome_cidade'] . ' / ' . $endereco['sigla_estado'];
				$dadosboleto['cep'] = $endereco['cep'];
			}

			if($dados_conta['conta_cedente']){
				$dadosboleto['identificacao'] = $dadosboleto['cedente'] = strtoupper($dados_conta['conta_cedente']);
			}

			if($dados_conta['conta_cnpj']){
				$dadosboleto['cpf_cnpj'] = $dados_conta['conta_cnpj'];
			}

			// DADOS DA SUA CONTA - CEF
			$dadosboleto["agencia"]  = $dados_conta['agencia_filial'];
			$dadosboleto["agencia_dv"]  = $dados_conta['agencia_dig_filial'];
			$dadosboleto["conta"] 	 = $dados_conta['conta_filial'];
			$dadosboleto["conta_dv"] = $dados_conta['conta_dig_filial'];
				
			$dadosboleto['codigo_cliente'] = $dados_conta['agencia_filial'] . 
											($dados_conta['agencia_dig_filial']? '-' . $dados_conta['agencia_dig_filial']:'') .
											'/' . $dados_conta['conta_filial'] . '-' . $dados_conta['conta_dig_filial'];

			$dadosboleto["agencia_codigo"] = $dadosboleto['codigo_cliente'];

			$dadosboleto["carteira_descricao"] = $dados_conta['carteira'];

			$dadosboleto["inicio_nosso_numero"] = $dados_conta['carteira'];

			$dadosboleto["especie"] = 'R$';

			$dadosboleto['identificador'] = $dados_conta['identificador'];

			return $dadosboleto;

		}

		
		
		function santander($dadosboleto){
			
			
			// DADOS PERSONALIZADOS - SANTANDER BANESPA
			$dadosboleto["codigo_cliente"] = "3301010"; // Código do Cliente (PSK) (Somente 7 digitos)
			$dadosboleto["ponto_venda"] = "3251"; // Ponto de Venda = Agencia
			$dadosboleto["carteira"] = "102";  // Cobrança Simples - SEM Registro
			$dadosboleto["carteira_descricao"] = "COBRANÇA SIMPLES - CSR";  // Descrição da Carteira
			
			
			
			// INFORMACOES PARA O CLIENTE
			$dadosboleto["demonstrativo1"] = "";
			$dadosboleto["demonstrativo2"] = "";
			$dadosboleto["demonstrativo3"] = "";
						
			// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
			$dadosboleto["quantidade"] = "";
			$dadosboleto["valor_unitario"] = "";
			$dadosboleto["aceite"] = "";		
			$dadosboleto["especie"] = "R$";
			$dadosboleto["especie_doc"] = "";
						
			include_once "../boletos/include/funcoes_santander_banespa.php";
			$dadosboleto = startBoleto($dadosboleto);
			include "../boletos/include/layout_santander_banespa.php";			
			
		}


		/**
		 * Gera informações para cobrança de uma movimentação. As informações são relacionadas
		 * a boletos e arquivos de remessa
		 * @param integer $idmovimento - ID do movimento para o qual será gerada a cobrança
		 */
		public function geraInformacoesCobranca($idmovimento){

			global $form, $cliente, $movimento, $filial, $cliente_juridico, $apartamento;

			if(!$movimento){
				$movimento = new movimento();
			}
			if(!$cliente){
				$cliente = new cliente();
			}
			if(!$filial){
				$filial = new filial();
			}

			if(!$cliente_juridico){
				$cliente_juridico = new cliente_juridico();
			}

			if(!$apartamento){
				$apartamento = new apartamento();
			}

			$info                 = $movimento->getById($idmovimento);
			$info_cliente         = $cliente->BuscaDadosCliente($info['idcliente_origem'], true);
            $info_filial          = $filial->BuscaDadosFilial($_SESSION['idfilial_usuario']);
			$info_cliente_destino = $cliente->BuscaDadosCliente($info['idcliente_destino']);

			// Dados de cobrança
			$dias_de_prazo_para_pagamento = 5;
			$taxa_boleto = empty($info['taxa_boleto']) ? 0.00 : $form->FormataMoedaParaInserir($info['taxa_boleto']);
			$valor_movimento = $form->FormataMoedaParaInserir($info['valor_movimento']);
                            //$data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias  OU  informe data: "13/04/2006"  OU  informe "" se Contra Apresentacao;	
			$dadosboleto["valor_boleto"] = $form->FormataMoedaParaExibir($valor_movimento + $taxa_boleto);

			$dadosboleto['idmovimento'] = $idmovimento;
			$dadosboleto['descricao_movimento'] = $info['descricao_movimento'];
							
			// Dados do Cedente
			$dadosboleto["identificacao"] = strtoupper($info_filial['nome_filial']);
			$dadosboleto["cpf_cnpj"]      = $info_filial['cnpj_filial'];
			$dadosboleto["endereco"]      = $info_filial['endereco']['linha1'];
			$dadosboleto["cidade_uf"]     = $info_filial['nome_cidade'].' / '.$info_filial['sigla_estado']; 
			$dadosboleto["cedente"]	      = strtoupper($info_filial['nome_filial']);

			$dadosboleto["data_vencimento"] = $info['data_vencimento']; //$data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
			$dadosboleto["data_documento"] = date("d/m/Y");             // Data de emissão do Boleto
			$dadosboleto["data_processamento"] = date("d/m/Y");         // Data de processamento do boleto (opcional)

				
			$nomeCondominio = "";
							
			//Verifica se é um cliente jurídico				
			$juridico = $cliente_juridico->getById($info_cliente['idcliente']);

			//if(isset($apto)){ //Taxa de Condomínio - Usa o endereço do condomínio + apartamento como endereço do sacado
			if($info['idapartamento']){

				//Pega o número do apartamento para o caso de condomínio
				$apto = $apartamento->getById($info['idapartamento']);
				$apto = $apto['apto'];
					
				$nomeCondominio = "<b>( " . strtoupper($info_cliente_destino['nome_cliente']) . " )</b>";
				
				$endereco = $info_cliente_destino['logradouro'] . ", " . $info_cliente_destino['numero'] . " - apto: $apto - ";
				$endereco .=  $info_cliente_destino['nome_bairro'] . " - " . $info_cliente_destino['nome_cidade'] . " - " . $info_cliente_destino['sigla_estado'] . "<br />";
				$endereco .=  "CEP: " . $info_cliente_destino['cep'];
				
				$dadosboleto['logradouro'] = $info_cliente_destino['logradouro'] . " " . $info_cliente_destino['numero'] . " apto: $apto";
				$dadosboleto['bairro'] = $info_cliente_destino['nome_bairro'];
				$dadosboleto['cidade'] = $info_cliente_destino['nome_cidade'];
				$dadosboleto['uf'] = $info_cliente_destino['sigla_estado'];
				$dadosboleto['cep'] = $info_cliente_destino['cep'];

				/// Nome do cliente que será mostrado na tela apenas para informação
				$dadosboleto['cliente_label'] = $info_cliente_destino['nome_cliente'];

				$dadosboleto['apartamento'] = $apto . ' - ' . $info_cliente['nome_cliente'];
			}
			elseif(isset($juridico ['idcliente'])){
				
				$endereco = $info_cliente['logradouro'] . ", " . $info_cliente['numero'] . ' - ' . $info_cliente['complemento'].' ';
				$endereco .=  $info_cliente['nome_bairro'] . " - " . $info_cliente['nome_cidade'] . " - " . $info_cliente['sigla_estado'] . "<br />";
				$endereco .=  "CEP: " . $info_cliente['cep'];
				
				$dadosboleto['logradouro'] = $info_cliente['logradouro'] . " " . $info_cliente['numero'];
				$dadosboleto['bairro'] = $info_cliente['nome_bairro'];
				$dadosboleto['cidade'] = $info_cliente['nome_cidade'];
				$dadosboleto['uf'] = $info_cliente['sigla_estado'];
				$dadosboleto['cep'] = $info_cliente['cep'];

				/// Nome do cliente que será mostrado na tela apenas para informação
				$dadosboleto['cliente_label'] = $info_cliente['nome_cliente'];

				$dadosboleto['apartamento'] = '';

			}
			else{ // Clientes Avulsos
				
				$endereco = $info_cliente['logradouro'] . ", " . $info_cliente['numero'] . " - " .  $info_cliente['complemento'] . " - ";
				$endereco .=  $info_cliente['nome_bairro'] . " - " . $info_cliente['nome_cidade'] . " - " . $info_cliente['sigla_estado'] . "<br />";
				$endereco .=  "CEP: " . $info_cliente['cep'];

				$dadosboleto['logradouro'] = $info_cliente['logradouro'] . " " . $info_cliente['numero'];
				$dadosboleto['bairro'] = $info_cliente['nome_bairro'];
				$dadosboleto['cidade'] = $info_cliente['nome_cidade'];
				$dadosboleto['uf'] = $info_cliente['sigla_estado'];
				$dadosboleto['cep'] = $info_cliente['cep'];
				
				/// Nome do cliente que será mostrado na tela apenas para informação
				$dadosboleto['cliente_label'] = $info_cliente['nome_cliente'];

				$dadosboleto['apartamento'] = '';
			}
			
			// DADOS DO CLIENTE
			$cpf_cnpj = '';
			if(isset($info_cliente['cpf_cliente'])){
				$cpf_cnpj = 'CPF: ' . $info_cliente['cpf_cliente'];
				$dadosboleto['inscricao_cliente'] = $info_cliente['cpf_cliente'];
			}
			elseif(isset($info_cliente['cnpj_cliente'])){
				$cpf_cnpj = 'CNPJ: ' . $info_cliente['cnpj_cliente'];
				$dadosboleto['inscricao_cliente'] = $info_cliente['cnpj_cliente'];
			}

			$dadosboleto["sacado"]	  = $info_cliente['nome_cliente'] . "<br />" . $cpf_cnpj . " $nomeCondominio<br />"  . $endereco ;				

			//Endereço do cliente
			if ($info_cliente['logradouro'] != '')  $info_cliente['endereco1']  = $dadosboleto["logradouro"].', '.$info_cliente['numero'];
			if ($info_cliente['complemento'] != '') $info_cliente['endereco1'] .= $info_cliente['complemento'].' - ';			
			if ($info_cliente['nome_bairro'] != '') $info_cliente['endereco1'] .= $info_cliente['nome_bairro'];
			if ($info_cliente['nome_cidade'] != '') $info_cliente['endereco2'] .= $info_cliente['nome_cidade'].' - '.$info_cliente['sigla_estado'];			
			if ($info_cliente['cep'] != '')         $info_cliente['endereco2'] .= ' - '.$info_cliente['cep'];
			
			$dadosboleto["numero_documento"] = str_pad($idmovimento,5,'0',STR_PAD_LEFT);	// Num do pedido ou do documento
			
			//Dados do Boleto
			$dadosboleto["nosso_numero"] = $idmovimento;

			/// Registra nome do cliente apenas, sem formatação de condomínio, para arquivo de remessa
			$dadosboleto['cliente'] = $info_cliente['nome_cliente'];

			/// Busca informações de cobrança de multa, juros e descontos definidos para o condomínio
			$dadosboleto['valor_desconto'] = ($info['desconto']?$info['desconto']:0);
			$dadosboleto['valor_juros'] = ($info['valor_juros']?$info['valor_juros']:0);
			$dadosboleto['valor_multa'] = ($info['valor_multa']?$info['valor_multa']:0);

			$dadosboleto['data_desconto'] = $dadosboleto['data_vencimento'];

			/// Define data para cobrança de multa como um dia após a data de vencimento
			$vencimento = explode('/', $dadosboleto["data_vencimento"]);
			$dadosboleto['data_multa'] = date('d/m/Y',mktime(0,0,0,$vencimento[1], $vencimento[0]+1, $vencimento[2]));

			return $dadosboleto;
		}
	} // fim da classe
?>
