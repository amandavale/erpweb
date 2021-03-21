<?php

/**
 * Classe respons�vel pela montagem do conte�do do arquivo de retorno do banco Ita�
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
	 * Retorna descri��es dos movimentos de retorno
	 */
	private function retornaDescricaoMovimento(){

		$descricoes_movimentos = array(
					'02' => 'Entrada confirmada',
					'03' => 'Entrada rejeitada',
					'04' => 'Altera��o de dados � nova entrada ou altera��o/exclus�o de dados acatada',
					'05' => 'Altera��o de dados � baixa',
					'06' => 'Liquida��o normal',
					'07' => 'Liquida��o parcial � cobran�a inteligente (b2b)',
					'08' => 'Liquida��o em cart�rio',
					'09' => 'Baixa simples',
					'10' => 'Baixa por ter sido liquidado',
					'11' => 'Em ser (s� no retorno mensal)',
					'12' => 'Abatimento concedido',
					'13' => 'Abatimento cancelado',
					'14' => 'Vencimento alterado',
					'15' => 'Baixas rejeitadas',
					'16' => 'Instru��es rejeitadas',
					'17' => 'Altera��o/exclus�o de dados rejeitados',
					'18' => 'Cobran�a contratual � instru��es/altera��es rejeitadas/pendentes',
					'19' => 'Confirma recebimento de instru��o de protesto',
					'20' => 'Confirma recebimento de instru��o de susta��o de protesto /tarifa',
					'21' => 'Confirma recebimento de instru��o de n�o protestar',
					'23' => 'T�tulo enviado a cart�rio/tarifa',
					'24' => 'Instru��o de protesto rejeitada / sustada / pendente',
					'25' => 'Alega��es do pagador',
					'26' => 'Tarifa de aviso de cobran�a',
					'27' => 'Tarifa de extrato posi��o (b40x)',
					'28' => 'Tarifa de rela��o das liquida��es',
					'29' => 'Tarifa de manuten��o de t�tulos vencidos',
					'30' => 'D�bito mensal de tarifas (para entradas e baixas)',
					'32' => 'Baixa por ter sido protestado',
					'33' => 'Custas de protesto',
					'34' => 'Custas de susta��o',
					'35' => 'Custas de cart�rio distribuidor',
					'36' => 'Custas de edital',
					'37' => 'Tarifa de emiss�o de boleto/tarifa de envio de duplicata',
					'38' => 'Tarifa de instru��o',
					'39' => 'Tarifa de ocorr�ncias',
					'40' => 'Tarifa mensal de emiss�o de boleto/tarifa mensal de envio de duplicata',
					'41' => 'D�bito mensal de tarifas � extrato de posi��o (b4ep/b4ox)',
					'42' => 'D�bito mensal de tarifas � outras instru��es',
					'43' => 'D�bito mensal de tarifas � manuten��o de t�tulos vencidos',
					'44' => 'D�bito mensal de tarifas � outras ocorr�ncias',
					'45' => 'D�bito mensal de tarifas � protesto',
					'46' => 'D�bito mensal de tarifas � susta��o de protesto',
					'47' => 'Baixa com transfer�ncia para desconto',
					'48' => 'Custas de susta��o judicial',
					'51' => 'Tarifa mensal ref a entradas bancos correspondentes na carteira',
					'52' => 'Tarifa mensal baixas na carteira',
					'53' => 'Tarifa mensal baixas em bancos correspondentes na carteira',
					'54' => 'Tarifa mensal de liquida��es na carteira',
					'55' => 'Tarifa mensal de liquida��es em bancos correspondentes na carteira',
					'56' => 'Custas de irregularidade',
					'57' => 'Instru��o cancelada',
					'59' => 'Baixa por cr�dito em c/c atrav�s do sispag',
					'60' => 'Entrada rejeitada carn�',
					'61' => 'Tarifa emiss�o aviso de movimenta��o de t�tulos (2154)',
					'62' => 'D�bito mensal de tarifa � aviso de movimenta��o de t�tulos (2154)',
					'63' => 'T�tulo sustado judicialmente',
					'64' => 'Entrada confirmada com rateio de cr�dito',
					'65' => 'Pagamento com cheque � aguardando compensa��o',
					'69' => 'Cheque devolvido',
					'71' => 'Entrada registrada, aguardando avalia��o',
					'72' => 'Baixa por cr�dito em c/c atrav�s do sispag sem t�tulo correspondente',
					'73' => 'Confirma��o de entrada na cobran�a simples � entrada n�o aceita na cobran�a contratual',
					'74' => 'Instru��o de negativa��o expressa rejeitada',
					'75' => 'Confirma��o de recebimento de instru��o de entrada em negativa��o expressa',
					'76' => 'Cheque compensado',
					'77' => 'Confirma��o de recebimento de instru��o de exclus�o de entrada em negativa��o expressa',
					'78' => 'Confirma��o de recebimento de instru��o de cancelamento de negativa��o expressa',
					'79' => 'Negativa��o expressa informacional (nota 20 � tabela 12)',
					'80' => 'Confirma��o de entrada em negativa��o expressa � tarifa',
					'82' => 'Confirma��o do cancelamento de negativa��o expressa � tarifa',
					'83' => 'Confirma��o de exclus�o de entrada em negativa��o expressa por liquida��o � tarifa',
					'85' => 'Tarifa por boleto (at� 03 envios) cobran�a ativa eletr�nica',
					'86' => 'Tarifa email cobran�a ativa eletr�nica',
					'87' => 'Tarifa sms cobran�a ativa eletr�nica',
					'88' => 'Tarifa mensal por boleto (at� 03 envios) cobran�a ativa eletr�nica',
					'89' => 'Tarifa mensal email cobran�a ativa eletr�nica',
					'90' => 'Tarifa mensal sms cobran�a ativa eletr�nica',
					'91' => 'Tarifa mensal de exclus�o de entrada de negativa��o expressa',
					'92' => 'Tarifa mensal de cancelamento de negativa��o expressa',
					'93' => 'Tarifa mensal de exclus�o de negativa��o expressa por liquida��o');
		
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
		
		$dados_empresa = $this->conta_filial->buscaContaPorBanco(341,$filial);

		$this->nome_empresa = substr($dados_empresa['nome_filial'],0,30);
		$this->cnpj_empresa = str_replace(array('.','-','/'), '',$dados_empresa['cnpj_filial']);
		
		$this->agencia = $dados_empresa['agencia_filial'];
		$this->digito_agencia = $dados_empresa['agencia_dig_filial'];
		$this->conta = str_pad($dados_empresa['conta_filial'],8,'0',STR_PAD_LEFT); 
		$this->digito_conta = $dados_empresa['conta_dig_filial'];
	}
}