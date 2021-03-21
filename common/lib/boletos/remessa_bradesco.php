<?php

/**
 * Classe responsável pela montagem do conteúdo do arquivo de remessa do banco Itaú
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/entidades/conta_filial.php';
require_once dirname(dirname(__FILE__)) . '/util.inc.php';


class RemessaBradesco{
	
	/// objeto da entidade conta_filial
	private $conta_filial;
	
	private $nome_empresa;
	
	private $cnpj_empresa;
	
	private $conta;
	
	private $agencia;
	
	private $digito_conta;
	
	private $carteira;

	private $codigo_banco;

	/// Código identificador da empresa no banco
	private $identificador;

	private $conferencia_nosso_numero;
	
	private $movimentos_remessa;

	public function __construct($filial, $movimentos_remessa, $sequencia, $nossoNumero, $cedente){

		$this->codigo_banco = '237';
		
		$this->conta_filial = new conta_filial();
		
		$this->movimentos_remessa = $movimentos_remessa;
		
		$this->defineInformacoesEmpresa($filial, $cedente);

		$this->sequencia = $sequencia;
	}
	
	/**
	 * Faz montagem e retorna conteúdo do arquivo de remessa
	 */
	public function retornaConteudoArquivoRemessa(){
		
		/** montagem do cabeçalho */
		$cabecalho = $this->montaCabecalho();
		
		/** montagem do detalhe: informações dos movimentos */
		
		$detalhe = '';
		
		/// número sequencial para cada movimento
		/// começa a contar do 2 porque o 1 é o cabeçalho 
		$numero_sequencial = 2;
		
		while(key($this->movimentos_remessa) !== null){
			
			$movimento = current($this->movimentos_remessa);
			$movimento = $this->formataDadosMovimento($movimento);
			
			$detalhe .= $this->montaDetalhe($movimento, $numero_sequencial);
			
			$numero_sequencial++;
			next($this->movimentos_remessa);
		}
		
		/// linha final do arquivo
		$trailer = $this->montaTrailer($numero_sequencial);

		return (
					array('conteudo' => $cabecalho . $detalhe . $trailer)
				);

	}

	/**
	 * Retorna nome que deve receber o arquivo de remessa
	 * @param integer $codigo - Código atribuído à remessa
	 * @return string $nome - Nome do arquivo
	 */
	public function retornaNomeArquivoRemessa($codigo){

		if($codigo >= 100){
			$variavel = substr($codigo,-2);
		}
		else{
			$variavel = str_pad($codigo,2,'0',STR_PAD_LEFT);
		}
		
		return 'CB' . date('dm') . $variavel . '.REM';

	}
	

	/**
	 * Busca no banco de dados as informações relacionadas ao nome, cnpj e 
	 * conta bancária da empresa
	 * @param integer $filial - ID da filial relacionada às informações
	 * @param string $cedente - Cedente 
	 */
	private function defineInformacoesEmpresa($filial, $cedente){
		
		if(!isset($cedente)){
			$cedente = '';
		}
		$dados_empresa = $this->conta_filial->buscaContaPorBanco($this->codigo_banco, $filial, $cedente);

		$this->nome_empresa = str_pad(substr($dados_empresa['conta_cedente'],0,30),30,' ');
		$this->cnpj_empresa = str_replace(array('.','-','/'), '',$dados_empresa['conta_cnpj']);
		
		$this->agencia = str_pad($dados_empresa['agencia_filial'],5,'0',STR_PAD_LEFT);
		$this->conta = str_pad($dados_empresa['conta_filial'],7,'0',STR_PAD_LEFT);
		$this->digito_conta = $dados_empresa['conta_dig_filial'];
		$this->carteira = str_pad($dados_empresa['carteira'],3,'0',STR_PAD_LEFT);
		$this->identificador = $dados_empresa['identificador'];

	}
	
	
	/**
	 * Formata dados do movimento
	 * @param array $movimento - Array contendo dados das contas a receber
	 */
	private function formataDadosMovimento($movimento){

		/// recebe a data no formato dd/mm/aaaa e altera para o formado ddmmaa

		$data = str_replace('/','',$movimento['data_vencimento']);
		$movimento['data_vencimento'] = substr($data,0,-4) . substr($data,-2);

		/// Registra se há cobrança de multa
		if($movimento['valor_multa'] > 0){
			$movimento['tem_multa'] = '2';

			$movimento['valor_multa'] = str_replace('.', '', $movimento['valor_multa']);
			$movimento['valor_multa'] = str_replace(',', '.', $movimento['valor_multa']);

			$movimento['valor_boleto'] = str_replace('.', '', $movimento['valor_boleto']);
			$movimento['valor_boleto'] = str_replace(',', '.', $movimento['valor_boleto']);

			$percentual = number_format($movimento['valor_multa'] * 100 / $movimento['valor_boleto'],2);

			if($percentual == 100){
				$percentual = 99.99;	
			}			
			$percentual = str_replace(array(',','.'),'',$percentual);
			$movimento['percentual_multa'] =  str_pad($percentual,4,'0',STR_PAD_LEFT);
		}
		else{
			$movimento['tem_multa'] = '0';
			$movimento['percentual_multa'] = '0000';
		}

		$data = str_replace('/','',$movimento['data_multa']);
		$movimento['data_multa'] = substr($data,0,-4) . substr($data,-2);
		
		$data = str_replace('/','',$movimento['data_desconto']);
		$movimento['data_desconto'] = substr($data,0,-4) . substr($data,-2);

		/// retira do valor caracteres que não são numerais

		$movimento['valor_boleto'] = str_replace(array('.',','),"",$movimento['valor_boleto']);
		$movimento['valor_multa'] = str_replace(array('.',','),"",$movimento['valor_multa']);
		$movimento['valor_desconto'] = str_replace(array('.',','),"",$movimento['valor_desconto']);
		$movimento['valor_juros'] = str_replace(array('.',','),"",$movimento['valor_juros']);

		$movimento['inscricao_cliente'] = str_replace(array('.','-','/'),"",$movimento['inscricao_cliente']);

		/// Registra tipo de inscrição: cpf ou cnpj
		if(strlen($movimento['inscricao_cliente']) == 11){
			$movimento['tipo_inscricao'] = 'CPF';
		}
		elseif(strlen($movimento['inscricao_cliente']) == 14){
			$movimento['tipo_inscricao'] = 'CNPJ';
		}

		$movimento['inscricao_cliente'] = str_pad($movimento['inscricao_cliente'],14,'0',STR_PAD_LEFT);

		/**
		 * garantindo que o nome do cliente e o endereço possuem o tamanho necessário
		 * e não possuem acentos
		 */ 
		
		$movimento['cliente'] = $this->trataInformacao($movimento['cliente'],30);

		$movimento['logradouro'] = $this->trataInformacao($movimento['logradouro'],40);
		
		$movimento['bairro'] = $this->trataInformacao($movimento['bairro'],12);
		
		$movimento['cidade'] = $this->trataInformacao($movimento['cidade'],15);

		$movimento['cep'] = str_replace(array('.','-'), "", $movimento['cep']);

		$movimento['cep'] = $this->trataInformacao($movimento['cep'],8,'0');

		$movimento['nosso_numero'] = str_pad($movimento['idmovimento'], 11, '0', STR_PAD_LEFT);

		$movimento['digito_verificador_nosso_numero'] = $this->calculaVerificadorNossoNumero($movimento['nosso_numero']);

		return $movimento;		
	}


	
	/**
	 * Formata informações que devem ser incluídas no arquivo com tamanhos específicos
	 * @param string $texto - Informação que deve ser formatada
	 * @param integer $tamanho - Tamanho da informação
	 * @param string $string_completar - String que deve ser utilizada para completar a informação
	 * 						para ficar com o tamanho correto
	 */
	private function trataInformacao($texto,$tamanho, $string_completar = ' '){
		
		$texto_formatado = substituiCaracteresEspeciais($texto,true);

		/// retira espaços do início e do fim da string
		if(strlen($texto_formatado) > 0){
			$texto_formatado = trim($texto_formatado);
		}
		
		/// se a string for maior que o permitido, corta os últimos caracteres para ficar
		/// com o tamanho permitido
		if(strlen($texto_formatado) > $tamanho){
			$texto_formatado = substr($texto_formatado,0,$tamanho);
		}
		
		$texto_formatado = str_pad($texto_formatado,$tamanho,$string_completar);
		
		return $texto_formatado;
	}

	

	/**
	 * Monta cabecalho do arquivo de remessa
	 */
	private function montaCabecalho(){
		
		/**
		 * CAMPO	    								POSIÇÃO		FORMATO: X - Alfanumérico
		 * 											   NO ARQUIVO			 9 - Númerico
		 * 																	(xx) - número de caracteres				
		 */															
		
		///	tipo de registro    						001 001    	9(01)
		$conteudo = '0';

		/// tipo operacao remessa 						002 002 	9(01)
		$conteudo .= '1';
		
		/// literal remessa								003 009     X(07)
		//$conteudo .= 'REM.TST';
		$conteudo .= 'REMESSA';

		/// código do serviço							010 011     9(02)
		$conteudo .= '01';
		
		/// literal cobranca 							012 026		X(15)
		/// o tamanho do campo é 15, completa o resto com espaços
		$conteudo .= str_pad('COBRANCA',15);
		
		/// Código da empresa, fornecido				027 046     9(20)
		/// pelo Bradesco
		$conteudo .= str_pad($this->identificador,20, '0',STR_PAD_LEFT);

		/// nome da empresa, máximo de 30 caracteres 	047 076     X(30)
		$conteudo .= $this->nome_empresa;

		///	código do banco 		   					077 079     9(03)
		$conteudo .= $this->codigo_banco;

		/// nome do banco 		  						080 094     X(15)
		$conteudo .= str_pad('BRADESCO',15);

		/// data da geração do arquivo      			095 100     9(06)
		$conteudo .= date("dmy");

		/// branco 					       				101 108     X(08)
		$conteudo .= str_pad("",8);

		/// identificação do sistema       				109 110     X(02)
		$conteudo .= "MX";

		/// número sequencial de remessa 			 	111 117     9(07)
		$conteudo .= str_pad($this->sequencia,7,'0',STR_PAD_LEFT);

		/// branco 					       				118 394     X(277)
		$conteudo .= str_pad("",277);		

		/// número sequencial do registro no arquivo 	395 400     9(06)
		$conteudo .= str_pad('1',6,'0',STR_PAD_LEFT);
		
		/// quebra de linha, necessária para o arquivo funcionar
		$conteudo .= chr(13).chr(10);

		return $conteudo;
		
	}
	

	/**
	 * Monta linha com dados da conta a receber
	 * @param array $movimento - Array contendo dados da conta a receber
	 * @param integer $numero_sequencial - Número da linha dentro do arquivo
	 */
	private function montaDetalhe($movimento, $numero_sequencial){
		
		/**
		 *  NOME DO CAMPO	SIGNIFICADO					POSICAO		PICTURE
		 */
		
		/// tipo registro ID registro transação  		001 001         9(01)
		$conteudo = '1';


		/// Posições 002 a 020: preenchimento			002 020			9(19)
		/// somente se houver opção de pagamento com débito automático
		$conteudo .= str_pad('',19,'0');

		/// identificação da empresa 	    			021 021         9(01)
		$conteudo .= '0';

		/// número da carteira							022 024	        9(03)
		$conteudo .= $this->carteira;

		/// código da agência							025 029			9(05)
		$conteudo .= $this->agencia;

		/// conta da empresa 			    			030 036         9(07)
		$conteudo .= $this->conta;

		/// dígito verifica da conta da empresa			037 037         9(01)
		$conteudo .= $this->digito_conta;

		/// USO DA EMPRESA					    		038 062         X(25)
		/// Campo para uso livre da empresa
		/// o mesmo valor será enviado no arquivo de retorno
		/// não aparece no boleto
		$conteudo .= str_pad('',25);

		/// código do banco para débito					063 065         9(03)
		/// zeros se não tiver opção de débito automático 
		$conteudo .= '000';

		/// campo de multa 								066 066			9(01)
		$conteudo .= $movimento['tem_multa'];

		/// percentual do valor da multa				067 070			9(04)
		$conteudo .= $movimento['percentual_multa'];

		/// nosso número / ID do título no banco		071 081         9(10)
		$conteudo .= $movimento['nosso_numero'];

		/// dígito de auto conferência do nosso número 	082	082			9(01)
		$conteudo .= $movimento['digito_verificador_nosso_numero'];

		/// Desconto Bonificação por dia 				083 092			9(10)
		$conteudo .= str_pad('0',10,'0');

		/// Condição para Emissão da Papeleta de  		093 093			9(01)
		/// Cobrança.
		/// 2: cliente emite e banco somente processa
		$conteudo .= '2';

		/// Ident.  se  emite  Boleto    para  Débito  	094 094			X(01)
		/// Automático
		/// Quando os dados de débito automático estiverem incorretos,
		/// a cobrança é registrada e boleto emitido
		$conteudo .= ' ';

		/// brancos 									095 104			9(10)
		$conteudo .= str_pad('',10);

		/// indicadores de rateio de crédito 			105 105			9(01)
		$conteudo .= str_pad('',1);

		/// endereçamento para aviso do débito 			106 106			9(01)
		/// automático em conta corrente
		$conteudo .= '1';

		/// brancos 									107 108			9(02)
		$conteudo .= str_pad('',2);

		/// código de ocorrência (remessa)				109 110         9(02)
		$conteudo .= '01';		
		
		// número do documento de cobrança				111 120			X(10)
		$conteudo .= str_pad($movimento['idmovimento'],10,'0',STR_PAD_LEFT);
		
		// vencimento do título no formato ddmmaa       121 126         9(06)
		$conteudo .= $movimento['data_vencimento'];
		
		// valor do título								127 139         9(11)V9(2)
		$conteudo .= str_pad($movimento['valor_boleto'],13,'0',STR_PAD_LEFT); 
		
		/// banco encarregado da cobrança				140	142			9(03)
		/// preencher com zeros
		$conteudo .= '000';  

		/// agência encarregada da cobrança				143 147			9(05)
		$conteudo .= '00000';

		// espécie do título							148 149         X(02)
		$conteudo .= '12';

		// Identificação 				     			150 150         X(01)
		$conteudo .= 'N'; 
		
		// data da emissão do título					151 156         9(06)
		$conteudo .= date('dmy');

		// instrução 1									157 158         X(02)
		$conteudo .= '00';
		
		// instrução 2									159 160         X(02)
		$conteudo .= '00';
		
		// juros de 1 dia								161 173			9(11)V9(02)
		$conteudo .= str_pad($movimento['valor_juros'],13,'0',STR_PAD_LEFT);
		
		// data limite para concessão de desconto		174 179         9(06)
		$conteudo .= $movimento['data_desconto'];
		
		// valor do desconto							180 192         9(11)V9(02)
		$conteudo .= str_pad($movimento['valor_desconto'],13,'0',STR_PAD_LEFT);
		
		// valor do IOF recolhido para notas seguro		193 205         9(11)V9(02)
		$conteudo .= str_pad('',13,'0');
		
		// valor do abatimento a ser concedido			206 218         9(11)V9(02)
		$conteudo .= str_pad('',13,'0');
		
		if($movimento['tipo_inscricao'] == 'CNPJ'){
			
			/// pessoa jurídica
			
			// código do tipo de cliente 01=CPF 02=CNPJ 219 220         9(02)
			$conteudo .= '02'; 
		}
		else{
			
			/// pessoa física
			
			// código do tipo de cliente 01=CPF 02=CNPJ 219 220         9(02)			
			$conteudo .= '01'; 
		}

		// número de inscrição: CNPJ					221 234         9(14)
		$conteudo .= $movimento['inscricao_cliente'];
		
		// nome do cliente								235 274         X(40)
		$conteudo .= str_pad($movimento['cliente'],40,' ');
		
		// logradouro: rua, número e complemento		275 314         X(40)
		$conteudo .= str_pad($movimento['logradouro'],40,' ');

		// primeira mensagem 							315 326         X(12)
		$conteudo .= str_pad('',12,' ');

		// cep											327 334         9(08)
		$conteudo .= $movimento['cep'];
		
		/// segunda mensagem 							335 394         X(60)
		$conteudo .= str_pad('',60,' ');
		
		// número sequencial do registro no arquivo  	395 400         9(06)
		$conteudo .= str_pad($numero_sequencial,6,'0',STR_PAD_LEFT); 

		// quebra de linha, necessária para o arquivo ser validado
		$conteudo .= chr(13).chr(10);

		return $conteudo;
	}
	

	/**
	 * Monta linha final do arquivo
	 * @param integer $numero_sequencial - número sequencial da linha
	 * @return string
	 */
	private function montaTrailer($numero_sequencial){
	
		/**
		 *  NOME DO CAMPO	SIGNIFICADO					POSICAO		PICTURE
		 */
	
		/// Identificação do registro de trailer  		001 001         9(01)
		$conteudo = '9';
	
		// complemento de registro						002 394         X(393)
		$conteudo .= str_pad('',393);

		// número sequencial do registro no arquivo  	395 400         9(06)
		$conteudo .= str_pad($numero_sequencial,6,'0',STR_PAD_LEFT);
		
		// quebra de linha, necessária para o arquivo ser validado
		$conteudo .= chr(13).chr(10);
		
		
		return $conteudo;
	}


	/**
	 * Calcula dígito de autoconferência do nosso número.
	 * O dígito é calculado com aplicação do módulo 11 na base 7 no número que é a 
	 * concatenação da carteira com o nosso número
	 * @param string $nosso_numero - String contendo o nosso número
	 * @return integer $digito - Retorna o dígito verificador
	 */
	private function calculaVerificadorNossoNumero($nosso_numero){

		$numero = $this->carteira . str_pad($nosso_numero,11,'0',STR_PAD_LEFT);

	    $soma = 0;
	    $fator = 2;
	    $base = 7;

	    /* Separacao dos números */
	    for ($i = strlen($numero); $i > 0; $i--) {

	        // pega cada numero isoladamente
	        //$digito = substr($numero,$i-1,1);
	        $digito = $numero[$i-1];

	        // Efetua multiplicação do numero pelo falor
	        $parcial = $digito * $fator;

	        // Soma dos digitos
	        $soma += $parcial;

	        if ($fator == $base) {
	            // restaura fator de multiplicação para 2 
	            $fator = 1;
	        }

	        $fator++;
	    }

	    $resto = $soma % 11;

	    $digito = 11 - $resto;

	    if($digito == 10){
	    	$digito = 'P';
	    }
	    elseif($digito == 11){
	    	$digito = 0;
	    }
	    return $digito;
	}
}