<?php

/** ESTE ARQUIVO PRECISA TER CODIFICAÇÃO 1252 (ANSI LATINO 1) **/
/**
 * Classe responsável pela montagem do conteúdo do arquivo de remessa do banco Sicoob
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/entidades/conta_filial.php';
require_once dirname(dirname(__FILE__)) . '/util.inc.php';


class RemessaSicoob{
	
	/// objeto da entidade conta_filial
	private $conta_filial;
	
	private $nome_empresa;
	
	private $cnpj_empresa;
	
	private $conta;
	
	private $agencia;

	private $digito_agencia;	
	
	private $digito_conta;
	
	private $carteira;

	private $codigo_banco = 756;

	/// Código identificador da empresa no banco
	private $identificador;

	private $movimentos_remessa;

	/// Registra o valor máximo de nosso número registrado na remessa
	private $nosso_numero;

	/// Array contendo associações de nosso número a cada ID de movimento
	private $nosso_numero_movimentos;
	
	public function __construct($filial, $movimentos_remessa, $sequencia, $nossoNumero){
		
		$this->conta_filial = new conta_filial();
		
		$this->movimentos_remessa = $movimentos_remessa;
		
		$this->defineInformacoesEmpresa($filial);

		$this->sequencia = $sequencia;

		$this->nosso_numero = $nossoNumero;

		$this->nosso_numero_movimentos = array();
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
			
			/// Formata nosso número e define dígito verificador
			$this->nosso_numero++;
			$nosso_numero_dv = $this->defineNossoNumero($this->nosso_numero);

			$detalhe .= $this->montaDetalhe($movimento, $numero_sequencial, $nosso_numero_dv);

			/// Registra nosso número ao movimento
			$this->nosso_numero_movimentos[$movimento['idmovimento']] = $nosso_numero_dv;

			/// Incrementa o número sequencial após o uso
			$numero_sequencial++;

			next($this->movimentos_remessa);
		}

		/// linha final do arquivo
		$trailer = $this->montaTrailer($numero_sequencial);

		return (
					array('conteudo' => $cabecalho . $detalhe . $trailer, 
						'nosso_numero' => $this->nosso_numero, 
						'nosso_numero_movimentos' => $this->nosso_numero_movimentos
					)
				);
	}


	/**
	 * Retorna nome que deve receber o arquivo de remessa
	 * @param integer $codigo - Código atribuído à remessa
	 * @return string $nome - Nome do arquivo
	 */
	public function retornaNomeArquivoRemessa($codigo){
		return 'S' . str_pad($codigo,6,'0',STR_PAD_LEFT);
	}


	/**
	 * Busca no banco de dados as informações relacionadas ao nome, cnpj e 
	 * conta bancária da empresa
	 * @param integer $filial - ID da filial relacionada às informações
	 */
	private function defineInformacoesEmpresa($filial){

		$dados_empresa = $this->conta_filial->buscaContaPorBanco($this->codigo_banco, $filial);

		$this->nome_empresa = substr($dados_empresa['conta_cedente'],0,30);
		$this->cnpj_empresa = str_replace(array('.','-','/'), '',$dados_empresa['conta_cnpj']);
		
		$this->agencia = str_pad($dados_empresa['agencia_filial'],4,'0',STR_PAD_LEFT);
		$this->digito_agencia = $dados_empresa['agencia_dig_filial'];
		$this->conta = str_pad($dados_empresa['conta_filial'],8,'0',STR_PAD_LEFT);
		$this->digito_conta = $dados_empresa['conta_dig_filial'];
		$this->carteira = str_pad($dados_empresa['carteira'],2,'0',STR_PAD_LEFT);
		
		$identificador = explode('-',$dados_empresa['identificador']);
		$this->identificador = str_pad($identificador[0],8,'0',STR_PAD_LEFT);
		$this->digito_identificador = str_pad($identificador[1],1,'0',STR_PAD_LEFT);		
	}
	
	
	/**
	 * Formata dados do movimento
	 * @param array $movimento - Array contendo dados das contas a receber
	 */
	private function formataDadosMovimento($movimento){

		/// recebe a data no formato dd/mm/aaaa e altera para o formado ddmmaa

		$data = str_replace('/','',$movimento['data_vencimento']);
		$movimento['data_vencimento'] = substr($data,0,-4) . substr($data,-2);

		$data = str_replace('/','',$movimento['data_multa']);
		$movimento['data_multa'] = substr($data,0,-4) . substr($data,-2);
		
		$data = str_replace('/','',$movimento['data_desconto']);
		$movimento['data_desconto'] = substr($data,0,-4) . substr($data,-2);


		/// valor da multa precisa ser porcentagem
		$movimento['valor_multa'] = str_replace('.', '', $movimento['valor_multa']);
		$movimento['valor_multa'] = str_replace(',', '.', $movimento['valor_multa']);

		$movimento['valor_boleto'] = str_replace('.', '', $movimento['valor_boleto']);
		$movimento['valor_boleto'] = str_replace(',', '.', $movimento['valor_boleto']);

		$movimento['valor_multa'] = $movimento['valor_multa'] * 100 / $movimento['valor_boleto'];
		$movimento['valor_multa'] = number_format($movimento['valor_multa'],2);
		$movimento['valor_multa'] = str_replace(array('.',','),"",$movimento['valor_multa']);

		/// retira do valor caracteres que não são numerais
		$movimento['valor_boleto'] = str_replace(array('.',','),"",$movimento['valor_boleto']);


		$movimento['valor_desconto'] = str_replace(array('.',','),"",$movimento['valor_desconto']);

		/// O valor de juros informado é por mês
		$movimento['valor_juros'] = $movimento['valor_juros'] * 30;
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
		
		$movimento['cliente'] = $this->trataInformacao($movimento['cliente'],40);

		$movimento['logradouro'] = $this->trataInformacao($movimento['logradouro'],37);
		
		$movimento['bairro'] = $this->trataInformacao($movimento['bairro'],15);
		
		$movimento['cidade'] = $this->trataInformacao($movimento['cidade'],15);

		$movimento['cep'] = str_replace(array('.','-'), "", $movimento['cep']);

		$movimento['cep'] = $this->trataInformacao($movimento['cep'],8,'0');


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
		$conteudo .= str_pad('COBRANÇA',15);
		
		/// agência mantenedora da conta				027 030     9(04)
		$conteudo .= $this->agencia;
		
		/// Complemento de registro 					031 031     9(01)
		$conteudo .= $this->digito_agencia;

		/// Código do cliente/beneficiário				032 039     9(08)
		$conteudo .= $this->identificador;

		/// Dígito de auto conferência da conta			040 040     9(01)
		$conteudo .= $this->digito_identificador;

		/// Convênio líder: brancos						041 046     X(06)
		$conteudo .=  str_pad("",6);
									
		/// Nome da empresa, máximo de 30 caracteres 	047 076     X(30)
		$conteudo .= $this->nome_empresa;

		///	Identificação do banco 		   				077 094     X(18)
		$identificacao_banco = '756BANCOOBCED     ';
		//$conteudo .= str_pad($identificacao_banco,18,' ',STR_PAD_RIGHT);
		$conteudo .= $identificacao_banco;

		/// data da geração do arquivo      			095 100     9(06)
		$conteudo .= date("dmy");

		/// número sequencial de remessa 			 	101 107     9(07)
		$conteudo .= str_pad($this->sequencia,7,'0',STR_PAD_LEFT);

		/// complemento de registro       				108 394     X(287)
		$conteudo .= str_pad("",287);

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
	 * @param integer $nosso_numero - Nosso número do registro que será incluído
	 */
	private function montaDetalhe($movimento, $numero_sequencial, $nosso_numero){
		
		/**
		 *  NOME DO CAMPO	SIGNIFICADO					POSICAO		PICTURE
		 */
		
		/// tipo registro ID registro transação  		001 001         9(01)
		$conteudo = '1';
		
		/// tipo de inscrição da empresa  				002 003         9(02)
		/// CNPJ: 02
		$conteudo .= '02';
		        
		/// CNPJ da empresa								004 017         9(14)
		$conteudo .= $this->cnpj_empresa;
		
		/// agência mantenedora da conta    			018 021         9(04)
		$conteudo .= $this->agencia;

		/// dígito da agência mantenedora da conta 		022 022         X(01)
		$conteudo .= $this->digito_agencia;

		/// conta da empresa 			    			023 030         9(08)
		$conteudo .= $this->conta;

		/// dígito verifica da conta da empresa			031 031         9(01)
		$conteudo .= $this->digito_conta;

		/// Convênio de cobrança do beneficiário: 
		/// Zeros 	 			    					032 037         9(06)
		$conteudo .= '000000'; 

		/// Controle do participante: brancos    		038 062         X(25)
		$conteudo .= str_pad('',25);

		/// nosso número / ID do título no banco		063 074         9(12)
		$conteudo .= $nosso_numero;

		/// número da parcela							075 076	        9(02)
		$conteudo .=  '01';

		/// Zeros										077 078	        9(02)
		$conteudo .=  '00';

		/// Brancos										079 081	        X(03)
		$conteudo .= str_pad('',3);

		/// Indicativo de mensagem ou sacador/avalista	082 082	        X(01)
		$conteudo .=  ' ';

		/// Prefixo do título: Brancos					083 085	        X(03)
		$conteudo .= str_pad('',3);

		/// Variação da carteira: Zeros					086 088	        9(03)
		$conteudo .=  '000';

		/// Conta caução								089 089	        9(01)
		$conteudo .=  '0';

		/// Número do contrato garantia					090 094         9(05)
		$conteudo .= str_pad('0',5,'0');

		/// DV do contrato								095 095	        9(01)
		$conteudo .=  '0';

		/// Número do borderô							096 101	        9(06)
		$conteudo .= str_pad('0',6,'0');

		/// Complemento de registro			    		102 105         X(04)
		$conteudo .= str_pad('',4);

		/// Tipo de emissão (1)Cooperativa, (2)Cliente  106 106         9(01)
		$conteudo .= '2';

		/// Carteira									107 108         9(02)
		$conteudo .= $this->carteira;		

		/// código de ocorrência (remessa)				109 110         9(02)
		$conteudo .= '01';		
		
		// número do documento de cobrança				111 120			X(10)
		$conteudo .= str_pad($movimento['idmovimento'],10,'0',STR_PAD_LEFT);
		
		// vencimento do título no formato ddmmaa       121 126         9(06)
		$conteudo .= $movimento['data_vencimento'];
		
		// valor do título								127 139         9(11)V9(2)
		$conteudo .= str_pad($movimento['valor_boleto'],13,'0',STR_PAD_LEFT); 
		
		// código do banco								140	142			9(03)
		$conteudo .= $this->codigo_banco;

		/// Prefixo da Cooperativa 						143 146			9(04)
		$conteudo .= $this->agencia;

		/// Dígito verificador do Prefixo 		 		147 147         9(01)
		$conteudo .= str_pad($this->digito_agencia,1," ");

		/// Espécie do título							148 149         X(02)
		/// (Duplicata de serviço)
		$conteudo .= '12';

		// aceite (A=aceite,N=não aceite)     			150 150         9(01)
		$conteudo .= '1'; 
		
		// data da emissão do título					151 156         9(06)
		$conteudo .= date('dmy');

		// instrução 1									157 158         9(02)
		$conteudo .= '00';
		
		// instrução 2									159 160         9(02)
		$conteudo .= '00';
		
		// juros por mês								161 166			9(02)V9(04)
		$conteudo .= str_pad($movimento['valor_juros'],6,'0',STR_PAD_LEFT);

		// multa										167 172			9(02)V9(04)
		$conteudo .= str_pad($movimento['valor_multa'],6,'0',STR_PAD_LEFT);

		// Tipo de distribuição							173 173         X(01)
		$conteudo .= '2';
		
		// data limite para concessão de desconto		174 179         9(06)
		$conteudo .= $movimento['data_desconto'];
		
		// valor do desconto							180 192         9(11)V9(02)
		$conteudo .= str_pad($movimento['valor_desconto'],13,'0',STR_PAD_LEFT);
		
		// valor do IOF recolhido para notas seguro		193 205         9(11)V9(02)
		$conteudo .= '9000000000000';
		
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
		$conteudo .= $movimento['cliente'];
		
		// logradouro: rua, número e complemento		275 311         X(37)
		$conteudo .= $movimento['logradouro'];

		// bairro										312 326         X(15)
		$conteudo .= $movimento['bairro'];

		// cep											327 334         9(08)
		$conteudo .= $movimento['cep'];
		
		// cidade										335 349         X(15)
		$conteudo .= $movimento['cidade'];
		
		// estado										350 351         X(02)
		$conteudo .= str_pad(substr($movimento['uf'],0,2),2);

		// sacador ou avalista							352 391         X(40)
		$conteudo .= str_pad('',40);

		// Número de dias para protesto 				392 393         9(02)
		$conteudo .= '00';

		// complemento de registro 						394 394         X(01)
		$conteudo .= str_pad('',1);

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
	 * Formata nosso número e inclui dígito verificador
	 * @param string $nosso_numero - Número a ser formatado
	 * @return string - Retorna nosso número formatado, concatenado com dígito verificador
	 */
	private function defineNossoNumero($nosso_numero){

		$nosso_numero_dv = $this->calculaVerificadorNossoNumero($nosso_numero);

		$nosso_numero = str_pad($nosso_numero,11,'0',STR_PAD_LEFT);



		return $nosso_numero . $nosso_numero_dv;
	}

	/**
	 * Calcula dígito de autoconferência do nosso número.
	 * O dígito é calculado com aplicação do módulo 11 na base que é a constante 3791 (com o número de trás pra frente)
	 * @param string $nosso_numero - String contendo o nosso número
	 * @return integer $digito - Retorna o dígito verificador
	 */
	private function calculaVerificadorNossoNumero($nosso_numero){

		$multiplicador = array(3,7,9,1);

		$numero = 	$this->agencia . 
					str_pad($this->identificador . $this->digito_identificador,10,'0',STR_PAD_LEFT) .
					str_pad($nosso_numero,7,'0',STR_PAD_LEFT);


	    $soma = 0;
	    $base = 3;
	    $fator = 0;

	    /* Separacao dos números */
	    for ($i = strlen($numero); $i > 0; $i--) {

	        // pega cada numero isoladamente
	        //$digito = substr($numero,$i-1,1);
	        $digito = $numero[$i-1];

	        // Efetua multiplicação do numero pelo falor
	        $parcial = $digito * $multiplicador[$fator];

	        // Soma dos digitos
	        $soma += $parcial;

	        if ($fator == $base) {
	            // restaura fator de multiplicação para 2 
	            $fator = -1;
	        }

	        $fator++;
	    }

	    $resto = $soma % 11;

	    $digito = 11 - $resto;

	    if(in_array($digito, array(10,11))) {
	    	$digito = 0;
	    }
	    return $digito;
	}
}