<?php

/**
 * Classe responsável pela montagem do conteúdo do arquivo de retorno do banco Itaú
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/entidades/conta_filial.php';
require_once dirname(dirname(__FILE__)) . '/util.inc.php';
require_once dirname(dirname(__FILE__)) . '/form.inc.php';


class RetornoBradesco{
	
	private $codigo_identificador;

	private $conteudo_retorno;
	
	private $contas_pagas;
	
	private $descricoes_movimentos;

	private $codigo_banco;
	
	public function __construct($filial, $conteudo_retorno, $cedente){
		
		$this->conta_filial = new conta_filial();
		
		$this->conteudo_retorno = $conteudo_retorno;

		$this->codigo_banco = '237';		
		
		$this->defineInformacoesEmpresa($filial, $cedente);
		
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
		
		$codigo_identificador = substr($this->conteudo_retorno[0],26,20);

		if(intval($codigo_identificador) != $this->codigo_identificador){
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
				$nosso_numero = substr($this->conteudo_retorno[$i],70,12);

				/// busca ID do movimento que está incluído no nosso número
				$id_movimento = intval(substr($nosso_numero,0,11));

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

				/// tipo de movimento (exemplo: liquidação)
				$movimento = substr($this->conteudo_retorno[$i],108,2);
				$this->contas_pagas[$id_movimento]['movimento'] = $this->descricoes_movimentos[$movimento];

				/// Registra forma de pagamento

				/// exemplo: boca do caixa, internet
				$tipo_liquidacao = substr($this->conteudo_retorno[$i],301,3);

				/// exemplo: dinheiro, cheque
				/// O campo forma de pagamento está em "Motivos para os códigos de ocorrência"
				/// O campo possui tamanho 10 porque possui até 5 motivos. Apenas o primeiro será utilizado
				$forma_pagamento = substr($this->conteudo_retorno[$i],318,2);
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
					'02' => 'Entrada Confirmada',
					'03' => 'Entrada Rejeitada',
					'06' => 'Liquidação Normal',
					'09' => 'Baixado Automat. via Arquivo',
					'10' => 'Baixado conforme instruções da Agência',
					'11' => 'Em Ser -Arquivo de Títulos pendentes',
					'12' => 'Abatimento Concedido',
					'13' => 'Abatimento Cancelado',
					'14' => 'Vencimento Alterado',
					'15' => 'Liquidação em Cartório',
					'16' => 'Título Pago em Cheque',
					'17' => 'Liquidação após baixa ou título não registrado',
					'18' => 'Acerto de Depositária',
					'19' => 'Confirmação Receb. Inst. de Protesto',
					'20' => 'Confirmação Recebimento Instrução Sustação de Protesto',
					'21' => 'Acerto do Controle do Participante',
					'22' => 'Título Com Pagamento Cancelado',
					'23' => 'Entrada do Título em Cartório (sem motivo)',
					'24' => 'Entrada rejeitada por CEP Irregular',
					'25' => 'Confirmação Receb.Inst.de Protesto Falimentar',
					'27' => 'Baixa Rejeitada',
					'28' => 'Débito de tarifas/custas',
					'29' => 'Ocorrências do Pagador',
					'30' => 'Alteração de Outros Dados Rejeitados',
					'32' => 'Instrução Rejeitada',
					'33' => 'Confirmação Pedido Alteração Outros Dados',
					'34' => 'Retirado de Cartório e Manutenção Carteira',
					'35' => 'Desagendamento do débito automático',
					'40' => 'Estorno de pagamento',
					'55' => 'Sustado judicial',
					'68' => 'Acerto dos dados do rateio de Crédito',
					'69' => 'Cancelamento dos dados do rateio',
					'73' => 'Confirmação Receb. Pedido de Negativação',
					'74' => 'Confir Pedido de Excl de Negat'
					);
		
		return $descricoes_movimentos;
	}
	

	
	/**
	 * Retorna descrições das formas de pagamento
	 */
	private function retornaDescricaoFormaPagamento($tipo_liquidacao, $forma_pagamento){

		$descricoes_tipos_liquidacao = array(
											'01' => 'CICS (AT00)',											
											'07' => 'TERM. GER. CBCA PF8',
											'10' => 'TERM. GER. CBCA SENHAS',
											'74' => 'Boca do Caixa',
											'75' => 'Retaguarda',
											'76' => 'Subcentro',

											'02' => 'BDN Multi Saque',
											'24' => 'TERM. Multi Função',
											'27' => 'PAG Contas',

											'14' => 'Internet',
											'35' => 'NET Empresa',
											'52' => 'SHOP Credit',
											'73' => 'PAG FOR',

											'13' => 'Fone Fácil',
											'67' => 'DEB Automático',
											'77' => 'Cartão de Crédito',	
											'78' => 'Compensação Eletrônica',
											'82' => 'Bradesco Expresso'
										);
		
		$descricoes_forma_pagamento = array(
											'00' => 'Dinheiro',
											'15' => 'Cheque',
											'42' => 'Rateio não efetuado'
										);
		
		return $descricoes_tipos_liquidacao[$tipo_liquidacao] . '-' . $descricoes_forma_pagamento[$forma_pagamento];
		
	}
	
	/**
	 * Busca no banco de dados as informações relacionadas ao nome, cnpj e 
	 * conta bancária da empresa
	 * @param integer $filial - ID da filial relacionada às informações
	 * @param string $cedente - Nome do cedente
	 */
	private function defineInformacoesEmpresa($filial, $cedente){
		
		$dados_empresa = $this->conta_filial->buscaContaPorBanco($this->codigo_banco,$filial, $cedente);

		$this->codigo_identificador = $dados_empresa['identificador'];
	}
}