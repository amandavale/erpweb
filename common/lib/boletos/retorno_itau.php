<?php

/**
 * Classe responsável pela montagem do conteúdo do arquivo de retorno do banco Itaú
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/entidades/conta_filial.php';
require_once dirname(dirname(__FILE__)) . '/util.inc.php';
require_once dirname(dirname(__FILE__)) . '/form.inc.php';


class RetornoItau{
	
	private $nome_empresa;
	
	private $cnpj_empresa;
	
	private $conta;
	
	private $agencia;
	
	private $digito_agencia;
	
	private $digito_conta;
	
	private $conteudo_retorno;
	
	private $contas_pagas;
	
	private $descricoes_movimentos;
	
	
	public function __construct($filial, $conteudo_retorno){
		
		$this->conta_filial = new conta_filial();
		
		$this->conteudo_retorno = $conteudo_retorno;
		
		$this->defineInformacoesEmpresa($filial);
		
		$this->descricoes_movimentos = $this->retornaDescricaoMovimento();
	}
	

	/**
	 * Retorna array com informações das contas que foram pagas
	 */
	public function obtemContasPagas(){

		return $this->contas_pagas;
	}
	
	
	/**
	 * Interpreta o conteúdo do arquivo de retorno.
	 * Se o conteúdo for inválido retorna false
	 * Caso contrário faz leitura do arquivo armazenando informações
	 * das contas pagas 
	 */
	public function interpretaConteudoRetorno(){
		
		if(!$this->validaArquivo()){
			return false;
		}
		else{
			
			$this->processaContasPagas();

			return true;			
		}
	}
	
	/**
	 * Verifica se informações fornecidas no arquivo são válidas
	 * Retorna false se informações forem inválidas e true se forem válidas
	 */
	private function validaArquivo(){
		
		/**
		 * Verifica informações do cabeçalho que é a primeira chave do array
		 */
		
		/// verifica código de retorno do arquivo
		/// o valor deve ser 2
		$codigo_retorno = substr($this->conteudo_retorno[0],1,1);
		if($codigo_retorno != '2'){
			return false;
		}
		
		/// verifica literal de retorno do arquivo
		/// o valor deve ser RETORNO
		$literal_retorno = substr($this->conteudo_retorno[0],2,7);
		if($literal_retorno != 'RETORNO'){
			return false;
		}
		
		/**
		 * verifica dados da empresa: agência e conta
		 */
		
		$agencia = substr($this->conteudo_retorno[0],26,4);
		if($agencia != $this->agencia){
			return false;
		}


		$conta = substr($this->conteudo_retorno[0],32,5);
		if($conta != $this->conta){
			return false;
		}

		$dac_conta = substr($this->conteudo_retorno[0],37,1);
		if($dac_conta != $this->digito_conta){
			return false;
		}
		
		return true;
	}
	
	/**
	 * Lê o conteúdo do arquivo e armazena no array $this->contas_pagas
	 * os dados das contas pagas que foram encontradas
	 */
	private function processaContasPagas(){
		
		$form = new form();
		
		/// número de boletos pagos: tamanho do conteúdo de retorno menos 2, que são
		/// header e trailer do arquivo
		$numero_boletos = (sizeof($this->conteudo_retorno) - 2);	
		
		for($i=1; $i <= $numero_boletos; $i++){

			$codigo_movimento = substr($this->conteudo_retorno[$i],108,2);

			/// Verifica se o movimento é de entrada confirmada e só inclui se não for desse tipo.
			/// A entrada confirmada só informa que o boleto foi registrado e não contém informações 
			/// relevantes à baixa
			if($codigo_movimento != '02'){

				/// nosso número, número gerado pelo banco, número único para cada conta
				$id_movimento = intval(substr($this->conteudo_retorno[$i],126,8));

				$this->contas_pagas[$id_movimento]['movimento'] = $this->descricoes_movimentos[$codigo_movimento];
				
				$this->contas_pagas[$id_movimento]['nosso_numero'] = $id_movimento;

				/// valor do boleto
				$this->contas_pagas[$id_movimento]['valor'] = substr($this->conteudo_retorno[$i],152,13);
				/// divide por 100 para formatar com casas decimais
				$this->contas_pagas[$id_movimento]['valor'] = number_format(($this->contas_pagas[$id_movimento]['valor']/100),2);
				
				/// valor do boleto que foi pago
				$this->contas_pagas[$id_movimento]['valor_pago'] = substr($this->conteudo_retorno[$i],253,13);

				$tarifa = substr($this->conteudo_retorno[$i],175,13);

				/// divide por 100 para formatar com casas decimais
				$this->contas_pagas[$id_movimento]['valor_pago'] = 
										number_format((($this->contas_pagas[$id_movimento]['valor_pago'] + $tarifa)/100),2,'.','');

				$this->contas_pagas[$id_movimento]['valor_pago_exibir'] =
										$form->FormataMoedaParaExibir($this->contas_pagas[$id_movimento]['valor_pago']);

				/// juros
				$this->contas_pagas[$id_movimento]['juros'] = substr($this->conteudo_retorno[$i],266,13);
				/// divide por 100 para formatar com casas decimais
				$this->contas_pagas[$id_movimento]['juros'] = 
										number_format(($this->contas_pagas[$id_movimento]['juros']/100),2);
				$this->contas_pagas[$id_movimento]['juros_exibir'] =
										$form->FormataMoedaParaExibir($this->contas_pagas[$id_movimento]['juros']);
					
				
				/// multa
				$this->contas_pagas[$id_movimento]['multa'] = substr($this->conteudo_retorno[$i],279,13);
				/// divide por 100 para formatar com casas decimais
				$this->contas_pagas[$id_movimento]['multa'] = 
										number_format(($this->contas_pagas[$id_movimento]['multa']/100),2);
				$this->contas_pagas[$id_movimento]['multa_exibir'] =
										$form->FormataMoedaParaExibir($this->contas_pagas[$id_movimento]['multa']);
					

				/// desconto
				$this->contas_pagas[$id_movimento]['desconto'] = substr($this->conteudo_retorno[$i],240,13);
				/// divide por 100 para formatar com casas decimais
				$this->contas_pagas[$id_movimento]['desconto'] = 
										number_format(($this->contas_pagas[$id_movimento]['desconto']/100),2);
				$this->contas_pagas[$id_movimento]['desconto_exibir'] =
										$form->FormataMoedaParaExibir($this->contas_pagas[$id_movimento]['desconto']);
					
				
				/// tarifa do boleto
				$this->contas_pagas[$id_movimento]['tarifa_boleto'] = substr($this->conteudo_retorno[$i],175,13);
				/// divide por 100 para formatar com casas decimais
				$this->contas_pagas[$id_movimento]['tarifa_boleto'] = 
										number_format(($this->contas_pagas[$id_movimento]['tarifa_boleto']/100),2);
				$this->contas_pagas[$id_movimento]['tarifa_boleto_exibir'] = 
										$form->FormataMoedaParaExibir($this->contas_pagas[$id_movimento]['tarifa_boleto']);
				
				/// data do pagamento da conta
				/// formata data que vem do arquivo para mostrar na tela
				$pagamento = str_split(substr($this->conteudo_retorno[$i],110,6),2);
				$this->contas_pagas[$id_movimento]['pagamento'] = 	$pagamento[0] . '/' . 
																	$pagamento[1] . '/' . 
																	substr(date('Y'),0,2) . $pagamento[2];
				
				/// Registra forma de pagamento
				
				$tipo_liquidacao = '-';
				$forma_pagamento = '-';
				$this->contas_pagas[$id_movimento]['forma_pagamento'] = 
													$this->retornaDescricaoFormaPagamento($tipo_liquidacao, $forma_pagamento);
			}
		}
	}
	

	/**
	 * Retorna descrições dos movimentos de retorno
	 */
	private function retornaDescricaoMovimento(){

		$descricoes_movimentos = array(
					'02' => 'Entrada confirmada',
					'03' => 'Entrada rejeitada',
					'04' => 'Alteração de dados – nova entrada ou alteração/exclusão de dados acatada',
					'05' => 'Alteração de dados – baixa',
					'06' => 'Liquidação normal',
					'07' => 'Liquidação parcial – cobrança inteligente (b2b)',
					'08' => 'Liquidação em cartório',
					'09' => 'Baixa simples',
					'10' => 'Baixa por ter sido liquidado',
					'11' => 'Em ser (só no retorno mensal)',
					'12' => 'Abatimento concedido',
					'13' => 'Abatimento cancelado',
					'14' => 'Vencimento alterado',
					'15' => 'Baixas rejeitadas',
					'16' => 'Instruções rejeitadas',
					'17' => 'Alteração/exclusão de dados rejeitados',
					'18' => 'Cobrança contratual – instruções/alterações rejeitadas/pendentes',
					'19' => 'Confirma recebimento de instrução de protesto',
					'20' => 'Confirma recebimento de instrução de sustação de protesto /tarifa',
					'21' => 'Confirma recebimento de instrução de não protestar',
					'23' => 'Título enviado a cartório/tarifa',
					'24' => 'Instrução de protesto rejeitada / sustada / pendente',
					'25' => 'Alegações do pagador',
					'26' => 'Tarifa de aviso de cobrança',
					'27' => 'Tarifa de extrato posição (b40x)',
					'28' => 'Tarifa de relação das liquidações',
					'29' => 'Tarifa de manutenção de títulos vencidos',
					'30' => 'Débito mensal de tarifas (para entradas e baixas)',
					'32' => 'Baixa por ter sido protestado',
					'33' => 'Custas de protesto',
					'34' => 'Custas de sustação',
					'35' => 'Custas de cartório distribuidor',
					'36' => 'Custas de edital',
					'37' => 'Tarifa de emissão de boleto/tarifa de envio de duplicata',
					'38' => 'Tarifa de instrução',
					'39' => 'Tarifa de ocorrências',
					'40' => 'Tarifa mensal de emissão de boleto/tarifa mensal de envio de duplicata',
					'41' => 'Débito mensal de tarifas – extrato de posição (b4ep/b4ox)',
					'42' => 'Débito mensal de tarifas – outras instruções',
					'43' => 'Débito mensal de tarifas – manutenção de títulos vencidos',
					'44' => 'Débito mensal de tarifas – outras ocorrências',
					'45' => 'Débito mensal de tarifas – protesto',
					'46' => 'Débito mensal de tarifas – sustação de protesto',
					'47' => 'Baixa com transferência para desconto',
					'48' => 'Custas de sustação judicial',
					'51' => 'Tarifa mensal ref a entradas bancos correspondentes na carteira',
					'52' => 'Tarifa mensal baixas na carteira',
					'53' => 'Tarifa mensal baixas em bancos correspondentes na carteira',
					'54' => 'Tarifa mensal de liquidações na carteira',
					'55' => 'Tarifa mensal de liquidações em bancos correspondentes na carteira',
					'56' => 'Custas de irregularidade',
					'57' => 'Instrução cancelada',
					'59' => 'Baixa por crédito em c/c através do sispag',
					'60' => 'Entrada rejeitada carnê',
					'61' => 'Tarifa emissão aviso de movimentação de títulos (2154)',
					'62' => 'Débito mensal de tarifa – aviso de movimentação de títulos (2154)',
					'63' => 'Título sustado judicialmente',
					'64' => 'Entrada confirmada com rateio de crédito',
					'65' => 'Pagamento com cheque – aguardando compensação',
					'69' => 'Cheque devolvido',
					'71' => 'Entrada registrada, aguardando avaliação',
					'72' => 'Baixa por crédito em c/c através do sispag sem título correspondente',
					'73' => 'Confirmação de entrada na cobrança simples – entrada não aceita na cobrança contratual',
					'74' => 'Instrução de negativação expressa rejeitada',
					'75' => 'Confirmação de recebimento de instrução de entrada em negativação expressa',
					'76' => 'Cheque compensado',
					'77' => 'Confirmação de recebimento de instrução de exclusão de entrada em negativação expressa',
					'78' => 'Confirmação de recebimento de instrução de cancelamento de negativação expressa',
					'79' => 'Negativação expressa informacional (nota 20 – tabela 12)',
					'80' => 'Confirmação de entrada em negativação expressa – tarifa',
					'82' => 'Confirmação do cancelamento de negativação expressa – tarifa',
					'83' => 'Confirmação de exclusão de entrada em negativação expressa por liquidação – tarifa',
					'85' => 'Tarifa por boleto (até 03 envios) cobrança ativa eletrônica',
					'86' => 'Tarifa email cobrança ativa eletrônica',
					'87' => 'Tarifa sms cobrança ativa eletrônica',
					'88' => 'Tarifa mensal por boleto (até 03 envios) cobrança ativa eletrônica',
					'89' => 'Tarifa mensal email cobrança ativa eletrônica',
					'90' => 'Tarifa mensal sms cobrança ativa eletrônica',
					'91' => 'Tarifa mensal de exclusão de entrada de negativação expressa',
					'92' => 'Tarifa mensal de cancelamento de negativação expressa',
					'93' => 'Tarifa mensal de exclusão de negativação expressa por liquidação');
		
		return $descricoes_movimentos;
	}
	

	
	/**
	 * Retorna descrições das formas de pagamento
	 */
	private function retornaDescricaoFormaPagamento($tipo_liquidacao, $forma_pagamento){

		$descricoes_tipos_liquidacao = array(
											'620' => 'Correspondentes Bancários',
											'639' => 'Outros Canais',
											'647' => 'Lotéricos',
											'655' => 'Guichê CAIXA',
											'663' => 'Compensação'
										);
		
		$descricoes_forma_pagamento = array(
											'1' => 'Dinheiro',
											'2' => 'Cheque'
										);
		
		return $descricoes_tipos_liquidacao[$tipo_liquidacao] . '-' . $descricoes_forma_pagamento[$forma_pagamento];
		
	}
	
	/**
	 * Busca no banco de dados as informações relacionadas ao nome, cnpj e 
	 * conta bancária da empresa
	 * @param integer $filial - ID da filial relacionada às informações
	 */
	private function defineInformacoesEmpresa($filial){
		
		$dados_empresa = $this->conta_filial->buscaContaPorBanco(341,$filial);

		$this->nome_empresa = substr($dados_empresa['nome_filial'],0,30);
		$this->cnpj_empresa = str_replace(array('.','-','/'), '',$dados_empresa['cnpj_filial']);
		
		$this->agencia = $dados_empresa['agencia_filial'];
		$this->digito_agencia = $dados_empresa['agencia_dig_filial'];
		$this->conta = str_pad($dados_empresa['conta_filial'],8,'0',STR_PAD_LEFT); 
		$this->digito_conta = $dados_empresa['conta_dig_filial'];
	}
}