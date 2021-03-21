<?php

/**
 * Classe responsável pela montagem do conteúdo do arquivo de retorno do banco Itaú
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/entidades/conta_filial.php';
require_once dirname(dirname(__FILE__)) . '/util.inc.php';
require_once dirname(dirname(__FILE__)) . '/form.inc.php';


class RetornoSicoob{
	
	private $nome_empresa;

	private $cnpj_empresa;

	private $agencia;

	private $identificador;

	private $identificador_digito;

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

		/**
		 * Verifica código do cliente
		 */
		$identificador = substr($this->conteudo_retorno[0],32,7);
		if($identificador != $this->identificador){
			return false;
		}

		/**
		 * Verifica dígito verificador do código do cliente
		 */
		$identificador_digito = substr($this->conteudo_retorno[0],39,1);
		if($identificador_digito != $this->identificador_digito){
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
				$id_movimento = intval(substr($this->conteudo_retorno[$i],116,10));

				$this->contas_pagas[$id_movimento]['movimento'] = $this->descricoes_movimentos[$codigo_movimento];
				
				$this->contas_pagas[$id_movimento]['nosso_numero'] = substr($this->conteudo_retorno[$i],62,11);

				/// valor do boleto
				$this->contas_pagas[$id_movimento]['valor'] = substr($this->conteudo_retorno[$i],152,13);
				/// divide por 100 para formatar com casas decimais
				$this->contas_pagas[$id_movimento]['valor'] = number_format(($this->contas_pagas[$id_movimento]['valor']/100),2);
				
				/// valor do boleto que foi pago
				$this->contas_pagas[$id_movimento]['valor_pago'] = substr($this->conteudo_retorno[$i],253,13);

				$tarifa = substr($this->conteudo_retorno[$i],181,7);

				/// divide por 100 para formatar com casas decimais
				$this->contas_pagas[$id_movimento]['valor_pago'] = 
										number_format((($this->contas_pagas[$id_movimento]['valor_pago'] )/100),2,'.','');

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
				$this->contas_pagas[$id_movimento]['tarifa_boleto'] = number_format(($tarifa/100),2);
				$this->contas_pagas[$id_movimento]['tarifa_boleto_exibir'] = 
										$form->FormataMoedaParaExibir($this->contas_pagas[$id_movimento]['tarifa_boleto']);
				
				/// data do pagamento da conta
				/// formata data que vem do arquivo para mostrar na tela
				$pagamento = str_split(substr($this->conteudo_retorno[$i],110,6),2);
				$this->contas_pagas[$id_movimento]['pagamento'] = 	$pagamento[0] . '/' . 
																	$pagamento[1] . '/' . 
																	substr(date('Y'),0,2) . $pagamento[2];
				
				/// Registra forma de pagamento
				$this->contas_pagas[$id_movimento]['forma_pagamento'] = '';
			}
		}
	}
	

	/**
	 * Retorna descrições dos movimentos de retorno
	 */
	private function retornaDescricaoMovimento(){

		$descricoes_movimentos = array(
					'02' => 'Entrada confirmada',
					'05' => 'Liquidação em registro',
					'06' => 'Liquidação normal',
					'10' => 'Baixa por ter sido liquidado',
					'11' => 'Em ser (só no retorno mensal)',
					'14' => 'Vencimento alterado',
					'15' => 'Liquidação em cartório',
					'23' => 'Encaminhado a protesto',
					'27' => 'Confirmação de alteração de dados',
					'48' => 'Confirmação de instrução de transferência de carteira');
		
		return $descricoes_movimentos;
	}
	
	
	/**
	 * Busca no banco de dados as informações relacionadas ao nome, cnpj e 
	 * conta bancária da empresa
	 * @param integer $filial - ID da filial relacionada às informações
	 */
	private function defineInformacoesEmpresa($filial){
		
		$dados_empresa = $this->conta_filial->buscaContaPorBanco(756,$filial);

		$this->nome_empresa = substr($dados_empresa['nome_filial'],0,30);
		$this->cnpj_empresa = str_replace(array('.','-','/'), '',$dados_empresa['cnpj_filial']);
		
		$this->agencia = $dados_empresa['agencia_filial'];
		$identificador = explode('-',$dados_empresa['identificador']);
		$this->identificador = str_pad($identificador[0],8,'0',STR_PAD_LEFT); 
		$this->identificador_digito = $identificador[1];
	}
}