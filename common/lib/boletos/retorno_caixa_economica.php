<?php

/**
 * Classe respons�vel pela montagem do conte�do do arquivo de retorno do banco Ita�
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/entidades/conta_filial.php';
require_once dirname(dirname(__FILE__)) . '/util.inc.php';
require_once dirname(dirname(__FILE__)) . '/form.inc.php';


class RetornoCaixaEconomica{
	
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
	 * Retorna array com informa��es das contas que foram pagas
	 */
	public function obtemContasPagas(){

		return $this->contas_pagas;
	}
	
	
	/**
	 * Interpreta o conte�do do arquivo de retorno.
	 * Se o conte�do for inv�lido retorna false
	 * Caso contr�rio faz leitura do arquivo armazenando informa��es
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
	 * Verifica se informa��es fornecidas no arquivo s�o v�lidas
	 * Retorna false se informa��es forem inv�lidas e true se forem v�lidas
	 */
	private function validaArquivo(){
		
		/**
		 * Verifica informa��es do cabe�alho que � a primeira chave do array
		 */
		
		/// verifica c�digo de retorno do arquivo
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
		 * verifica dados da empresa: ag�ncia e conta
		 */
		
		// $codigo_identificador = substr($this->conteudo_retorno[0],26,16);

		// $agencia = substr($codigo_identificador,0,4);

		$agencia = substr($this->conteudo_retorno[0],26,4);
		if($agencia != $this->agencia){
			return false;
		}
		
		// $conta = substr($codigo_identificador,7,8);
		// if($conta != $this->conta){
		// 	return false;
		// }
		
		// $digito_conta = substr($codigo_identificador,15,1);
		// if($digito_conta != $this->digito_conta){
		// 	return false;
		// }
		
		return true;
	}
	
	/**
	 * L� o conte�do do arquivo e armazena no array $this->contas_pagas
	 * os dados das contas pagas que foram encontradas
	 */
	private function processaContasPagas(){
		
		$form = new form();
		
		/// n�mero de boletos pagos: tamanho do conte�do de retorno menos 2, que s�o
		/// header e trailer do arquivo
		$numero_boletos = (sizeof($this->conteudo_retorno) - 2);	
		
		for($i=1; $i <= $numero_boletos; $i++){

			$codigo_movimento = substr($this->conteudo_retorno[$i],108,2);

			/// Verifica se o movimento � de entrada confirmada e s� inclui se n�o for desse tipo.
			/// A entrada confirmada s� informa que o boleto foi registrado e n�o cont�m informa��es 
			/// relevantes � baixa
			if($codigo_movimento != '01'){

				/// nosso n�mero, n�mero gerado pelo banco, n�mero �nico para cada conta
				$nosso_numero = substr($this->conteudo_retorno[$i],58,15);

				/// busca ID do movimento que est� inclu�do no nosso n�mero
				$id_movimento = intval(substr($nosso_numero,2,13));

				$this->contas_pagas[$id_movimento]['movimento'] = $this->descricoes_movimentos[$codigo_movimento];
				
				$this->contas_pagas[$id_movimento]['nosso_numero'] = $nosso_numero;

				/// valor do boleto
				$this->contas_pagas[$id_movimento]['valor'] = substr($this->conteudo_retorno[$i],152,13);
				/// divide por 100 para formatar com casas decimais
				$this->contas_pagas[$id_movimento]['valor'] = number_format(($this->contas_pagas[$id_movimento]['valor']/100),2);
				
				/// valor do boleto que foi pago
				$this->contas_pagas[$id_movimento]['valor_pago'] = substr($this->conteudo_retorno[$i],253,13);

				/// divide por 100 para formatar com casas decimais
				$this->contas_pagas[$id_movimento]['valor_pago'] = 
										number_format(($this->contas_pagas[$id_movimento]['valor_pago']/100),2,'.','');

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
				
				$tipo_liquidacao = substr($this->conteudo_retorno[$i],188,3);
				$forma_pagamento = substr($this->conteudo_retorno[$i],191,1);
				$this->contas_pagas[$id_movimento]['forma_pagamento'] = 
													$this->retornaDescricaoFormaPagamento($tipo_liquidacao, $forma_pagamento);
			}
		}


	}
	

	/**
	 * Retorna descri��es dos movimentos de retorno
	 */
	private function retornaDescricaoMovimento(){

		$descricoes_movimentos = array(
					'01' => 'Entrada confirmada',
					'02' => 'Baixa confirmada',
					'03' => 'Abatimento concedido',
					'04' => 'Abatimento cancelado',
					'05' => 'Vencimento alterado',
					'06' => 'Uso da empresa alterado',
					'07' => 'Prazo de protesto alterado',
					'08' => 'Prazo de devolu��o alterado',
					'09' => 'Altera��o confirmada',
					'10' => 'Altera��o com Reemiss�o de Bloqueto Confirmada',
					'11' => 'Altera��o da Op��o de Protesto para Devolu��o',
					'12' => 'Altera��o da Op��o de Devolu��o para protesto',
					'20' => 'Em ser',
					'21' => 'Liquida��o',
					'22' => 'Liquida��o em Cart�rio',
					'23' => 'Baixa por Devolu��o',
					'24' => 'Baixa por Franco Pagamento',
					'25' => 'Baixa por Franco Protesto',
					'26' => 'T�tulo enviado para Cart�rio',
					'27' => 'Susta��o de Protesto',
					'28' => 'Estorno de Protesto',
					'29' => 'Estorno de Susta��o de Protesto',
					'30' => 'Altera��o de T�tulo',
					'31' => 'Tarifa sobre T�tulo Vencido',
					'32' => 'Outras Tarifas de Altera��o',
					'33' => 'Estorno de Baixa/Liquida��o',
					'34' => 'Transfer�ncia de Carteira/Entrada',
					'35' => 'Transfer�ncia de Carteira/Baixa',
					'99' => 'Rejei��o do T�tulo');
		
		return $descricoes_movimentos;
	}
	

	
	/**
	 * Retorna descri��es das formas de pagamento
	 */
	private function retornaDescricaoFormaPagamento($tipo_liquidacao, $forma_pagamento){

		$descricoes_tipos_liquidacao = array(
											'620' => 'Correspondentes Banc�rios',
											'639' => 'Outros Canais',
											'647' => 'Lot�ricos',
											'655' => 'Guich� CAIXA',
											'663' => 'Compensa��o'
										);
		
		$descricoes_forma_pagamento = array(
											'1' => 'Dinheiro',
											'2' => 'Cheque'
										);
		
		return $descricoes_tipos_liquidacao[$tipo_liquidacao] . '-' . $descricoes_forma_pagamento[$forma_pagamento];
		
	}
	
	/**
	 * Busca no banco de dados as informa��es relacionadas ao nome, cnpj e 
	 * conta banc�ria da empresa
	 * @param integer $filial - ID da filial relacionada �s informa��es
	 */
	private function defineInformacoesEmpresa($filial){
		
		$dados_empresa = $this->conta_filial->buscaContaPorBanco(104,$filial);

		$this->nome_empresa = substr($dados_empresa['nome_filial'],0,30);
		$this->cnpj_empresa = str_replace(array('.','-','/'), '',$dados_empresa['cnpj_filial']);
		
		$this->agencia = $dados_empresa['agencia_filial'];
		$this->digito_agencia = $dados_empresa['agencia_dig_filial'];
		$this->conta = str_pad($dados_empresa['conta_filial'],8,'0',STR_PAD_LEFT); 
		$this->digito_conta = $dados_empresa['conta_dig_filial'];
		
		
	}
}