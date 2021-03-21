<?php

/**
 * Classe responsável pela montagem do conteúdo do arquivo de remessa do banco Itaú
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/entidades/conta_filial.php';
require_once dirname(dirname(__FILE__)) . '/util.inc.php';


class RemessaItau{
	
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

	private $prefixo_nosso_numero;
	
	private $movimentos_remessa;
	
	public function __construct($filial, $movimentos_remessa, $sequencia, $nossoNumero){
		
		$this->conta_filial = new conta_filial();
		
		$this->movimentos_remessa = $movimentos_remessa;
		
		$this->defineInformacoesEmpresa($filial);

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
		return 'I' . str_pad($codigo,6,'0',STR_PAD_LEFT);
	}


	/**
	 * Busca no banco de dados as informações relacionadas ao nome, cnpj e 
	 * conta bancária da empresa
	 * @param integer $filial - ID da filial relacionada às informações
	 */
	private function defineInformacoesEmpresa($filial){
		
		$dados_empresa = $this->conta_filial->buscaContaPorBanco(341, $filial);

		$this->nome_empresa = substr($dados_empresa['conta_cedente'],0,30);
		$this->cnpj_empresa = str_replace(array('.','-','/'), '',$dados_empresa['conta_cnpj']);
		
		$this->agencia = $dados_empresa['agencia_filial'];
		$this->conta = $dados_empresa['conta_filial']; 
		$this->digito_conta = $dados_empresa['conta_dig_filial'];
		$this->carteira = str_pad($dados_empresa['carteira'],2,'0',STR_PAD_LEFT);
		$this->identificador = $dados_empresa['identificador'];
		$this->prefixo_nosso_numero = $dados_empresa['prefixo_nosso_numero'];
		$this->numero_banco = $dados_empresa[''];
		
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

		$movimento['nosso_numero'] = str_pad($movimento['idmovimento'], 8, '0', STR_PAD_LEFT);

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
		
		/// agência mantenedora da conta				027 030     9(04)
		$conteudo .= $this->agencia;
		
		/// Complemento de registro 					031 032     9(02)
		$conteudo .= '00';

		/// Conta corrente da EMPRESA 					033 037     9(05)
		$conteudo .= $this->conta;

		/// Dígito de auto conferência da conta			038 038     9(01)
		$conteudo .= $this->digito_conta;

		/// brancos 									039 046     X(08)
		$conteudo .=  str_pad("",8);
									
		/// nome da empresa, máximo de 30 caracteres 	047 076     X(30)
		$conteudo .= $this->nome_empresa;

		///	código do banco 		   					077 079     9(03)
		$conteudo .= '341';
		
		/// nome do banco 		  						080 094     X(15)
		$conteudo .= str_pad('BANCO ITAU SA',15);

		/// data da geração do arquivo      			095 100     9(06)
		$conteudo .= date("dmy");
									
		/// complemento de registro       				101 394     X(294)
		$conteudo .= str_pad("",294);

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
		
		/// tipo de inscrição da empresa  				002 003         9(02)
		/// CNPJ: 02
		$conteudo .= '02';
		        
		/// CNPJ da empresa								004 017         9(14)
		$conteudo .= $this->cnpj_empresa;
		
		/// agência mantenedora da conta    			018 021         9(04)
		$conteudo .= $this->agencia;

		/// Complemento de registro    					022 023         9(02)
		$conteudo .= '00';

		/// conta da empresa 			    			024 028         9(05)
		$conteudo .= $this->conta;

		/// dígito verifica da conta da empresa			029 029         9(01)
		$conteudo .= $this->digito_conta;

		/// Brancos 			    					030 033         9(04)
		$conteudo .= '0000'; 

		/// Cód. da instrução/alegação a ser cancelada 	034 037         9(04)
		$conteudo .= '0000';

		/// USO DA EMPRESA					    		038 062         X(25)
		/// Campo para uso livre da empresa
		/// o mesmo valor será enviado no arquivo de retorno
		/// não aparece no boleto
		$conteudo .= str_pad('',25);

		/// nosso número / ID do título no banco		063 070         9(08)
		$conteudo .= $movimento['nosso_numero'];

		/// Quantidade de moeda variável				071 083         9(08)V9(5)
		$conteudo .= str_pad('0',13,'0');

		/// número da carteira							084 086	        9(03)
		$conteudo .=  $this->carteira;

		/// uso do banco 					    		087 107         X(21)
		$conteudo .= str_pad('',21);

		/// código da carteira 							108 108         X(01)
		$conteudo .= 'I';

		/// código de ocorrência (remessa)				109 110         9(02)
		$conteudo .= '01';		
		
		// número do documento de cobrança				111 120			X(10)
		$conteudo .= str_pad($movimento['idmovimento'],10,'0',STR_PAD_LEFT);
		
		// vencimento do título no formato ddmmaa       121 126         9(06)
		$conteudo .= $movimento['data_vencimento'];
		
		// valor do título								127 139         9(11)V9(2)
		$conteudo .= str_pad($movimento['valor_boleto'],13,'0',STR_PAD_LEFT); 
		
		// código do banco								140	142			9(03)
		$conteudo .= '341';  

		/// agência encarregada da cobrança				143 147			9(05)
		$conteudo .= '00000';

		// espécie do título							148 149         X(02)
		$conteudo .= '08';

		// aceite (A=aceite,N=não aceite)     			150 150         X(01)
		$conteudo .= 'A'; 
		
		// data da emissão do título					151 156         9(06)
		$conteudo .= date('dmy');

		// instrução 1									157 158         X(02)
		$conteudo .= '05';
		
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
		
		// nome do cliente								235 264         X(30)
		$conteudo .= str_pad($movimento['cliente'],30,' ');

		// complemento de registro 						265 274         X(10)
		$conteudo .= str_pad('',10);
		
		// logradouro: rua, número e complemento		275 314         X(40)
		$conteudo .= str_pad($movimento['logradouro'],40,' ');

		// bairro										315 326         X(12)
		$conteudo .= $movimento['bairro'];

		// cep											327 334         9(08)
		$conteudo .= $movimento['cep'];
		
		// cidade										335 349         X(15)
		$conteudo .= $movimento['cidade'];
		
		// estado										350 351         X(02)
		$conteudo .= str_pad(substr($movimento['uf'],0,2),2);

		// sacador ou avalista							352 381         X(30)
		$conteudo .= str_pad('',30);

		// coplemento de registro						382 385         X(04)
		$conteudo .= str_pad('',4);

		/// Definição da data para pagamento de multa 	386 391			9(06)
		$conteudo .= str_pad($movimento['data_multa'],6,'0');

		// Número de dias de prazo da instrução 1		392 393         9(02)
		$conteudo .= '00';

		// complemento de registro 						394 394         9(01)
		$conteudo .= ' ';

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
}