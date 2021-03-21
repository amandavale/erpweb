<?php

/**
 * Classe respons�vel pela montagem do conte�do do arquivo de retorno do banco Ita�
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
		
		$codigo_identificador = substr($this->conteudo_retorno[0],26,20);

		if(intval($codigo_identificador) != $this->codigo_identificador){
			return false;
		}
		
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
			if($codigo_movimento != '02'){

				/// nosso n�mero, n�mero gerado pelo banco, n�mero �nico para cada conta
				$nosso_numero = substr($this->conteudo_retorno[$i],70,12);

				/// busca ID do movimento que est� inclu�do no nosso n�mero
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

				/// tipo de movimento (exemplo: liquida��o)
				$movimento = substr($this->conteudo_retorno[$i],108,2);
				$this->contas_pagas[$id_movimento]['movimento'] = $this->descricoes_movimentos[$movimento];

				/// Registra forma de pagamento

				/// exemplo: boca do caixa, internet
				$tipo_liquidacao = substr($this->conteudo_retorno[$i],301,3);

				/// exemplo: dinheiro, cheque
				/// O campo forma de pagamento est� em "Motivos para os c�digos de ocorr�ncia"
				/// O campo possui tamanho 10 porque possui at� 5 motivos. Apenas o primeiro ser� utilizado
				$forma_pagamento = substr($this->conteudo_retorno[$i],318,2);
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
					'02' => 'Entrada Confirmada',
					'03' => 'Entrada Rejeitada',
					'06' => 'Liquida��o Normal',
					'09' => 'Baixado Automat. via Arquivo',
					'10' => 'Baixado conforme instru��es da Ag�ncia',
					'11' => 'Em Ser -Arquivo de T�tulos pendentes',
					'12' => 'Abatimento Concedido',
					'13' => 'Abatimento Cancelado',
					'14' => 'Vencimento Alterado',
					'15' => 'Liquida��o em Cart�rio',
					'16' => 'T�tulo Pago em Cheque',
					'17' => 'Liquida��o ap�s baixa ou t�tulo n�o registrado',
					'18' => 'Acerto de Deposit�ria',
					'19' => 'Confirma��o Receb. Inst. de Protesto',
					'20' => 'Confirma��o Recebimento Instru��o Susta��o de Protesto',
					'21' => 'Acerto do Controle do Participante',
					'22' => 'T�tulo Com Pagamento Cancelado',
					'23' => 'Entrada do T�tulo em Cart�rio (sem motivo)',
					'24' => 'Entrada rejeitada por CEP Irregular',
					'25' => 'Confirma��o Receb.Inst.de Protesto Falimentar',
					'27' => 'Baixa Rejeitada',
					'28' => 'D�bito de tarifas/custas',
					'29' => 'Ocorr�ncias do Pagador',
					'30' => 'Altera��o de Outros Dados Rejeitados',
					'32' => 'Instru��o Rejeitada',
					'33' => 'Confirma��o Pedido Altera��o Outros Dados',
					'34' => 'Retirado de Cart�rio e Manuten��o Carteira',
					'35' => 'Desagendamento do d�bito autom�tico',
					'40' => 'Estorno de pagamento',
					'55' => 'Sustado judicial',
					'68' => 'Acerto dos dados do rateio de Cr�dito',
					'69' => 'Cancelamento dos dados do rateio',
					'73' => 'Confirma��o Receb. Pedido de Negativa��o',
					'74' => 'Confir Pedido de Excl de Negat'
					);
		
		return $descricoes_movimentos;
	}
	

	
	/**
	 * Retorna descri��es das formas de pagamento
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
											'24' => 'TERM. Multi Fun��o',
											'27' => 'PAG Contas',

											'14' => 'Internet',
											'35' => 'NET Empresa',
											'52' => 'SHOP Credit',
											'73' => 'PAG FOR',

											'13' => 'Fone F�cil',
											'67' => 'DEB Autom�tico',
											'77' => 'Cart�o de Cr�dito',	
											'78' => 'Compensa��o Eletr�nica',
											'82' => 'Bradesco Expresso'
										);
		
		$descricoes_forma_pagamento = array(
											'00' => 'Dinheiro',
											'15' => 'Cheque',
											'42' => 'Rateio n�o efetuado'
										);
		
		return $descricoes_tipos_liquidacao[$tipo_liquidacao] . '-' . $descricoes_forma_pagamento[$forma_pagamento];
		
	}
	
	/**
	 * Busca no banco de dados as informa��es relacionadas ao nome, cnpj e 
	 * conta banc�ria da empresa
	 * @param integer $filial - ID da filial relacionada �s informa��es
	 * @param string $cedente - Nome do cedente
	 */
	private function defineInformacoesEmpresa($filial, $cedente){
		
		$dados_empresa = $this->conta_filial->buscaContaPorBanco($this->codigo_banco,$filial, $cedente);

		$this->codigo_identificador = $dados_empresa['identificador'];
	}
}