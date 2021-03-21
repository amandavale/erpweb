<?php

/**
 * Classe responsável pela montagem do conteúdo do arquivo de pré-crítica
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/entidades/conta_filial.php';
require_once dirname(dirname(__FILE__)) . '/util.inc.php';
require_once dirname(dirname(__FILE__)) . '/form.inc.php';


class PreCriticaCaixaEconomica{


	private $conteudo_pre_critica;
	
	private $resultado;
	
	private $descricoes_movimentos;

	/** Informações sobre a empresa */

	private $codigo_identificador;
	
	/** Objetos de entidade */

	private $conta_filial;

	
	public function __construct($filial, $conteudo_pre_critica){

		$this->conta_filial = new conta_filial();

		$this->defineInformacoesEmpresa($filial);

		$this->conteudo_pre_critica = $conteudo_pre_critica;
		
		$this->descricoes_movimentos = $this->retornaDescricaoMovimento();
	}
	

	/**
	 * Retorna array com informações das contas que foram pagas
	 */
	public function obtemResultado(){

		return $this->resultado;
	}
	
	
	/**
	 * Interpreta o conteúdo do arquivo de retorno.
	 * Se o conteúdo for inválido retorna false
	 * Caso contrário faz leitura do arquivo armazenando informações
	 * das contas pagas 
	 */
	public function interpretaConteudoPreCritica(){
		
		if(!$this->validaArquivo()){
			return false;
		}
		else{
			
			$this->processaMovimentos();

			return true;			
		}
	}
	
	/**
	 * Verifica se informações fornecidas no arquivo são válidas
	 * Retorna false se informações forem inválidas e true se forem válidas
	 */
	private function validaArquivo(){
		
		/**
		 * verifica código identificador da empresa
		 */
		
		$codigo_identificador = substr($this->conteudo_pre_critica[0],30,6);

		if($codigo_identificador != $this->codigo_identificador){
			return false;
		}
		
		return true;
	}
	
	/**
	 * Lê o conteúdo do arquivo e armazena no array $this->resultado
	 * o resultado do processamento
	 */
	private function processaMovimentos(){

		/// verifica a mensagem de retorno correspondente ao processamento
		$mensagem = substr($this->conteudo_pre_critica[0], 100,289);

		if(strstr($mensagem, 'REMESSA PROCESSADA') === false){

			/** Se não houver a mensagem REMESSA PROCESSADA significa que houve algum movimento rejeitado */

			/// número de boletos pagos: tamanho do conteúdo de retorno menos 2, que são
			/// header e trailler do arquivo
			$numero_boletos = (sizeof($this->conteudo_pre_critica) - 2);

			for($i=1; $i <= $numero_boletos; $i++){

				/// Array que registra mensagens de erro no processamento
				$mensagem = array();

				/// Verifica código do primeiro erro
				$erro1 = substr($this->conteudo_pre_critica[$i],29,2);

				/// Verifica código do segundo erro
				$erro2 = substr($this->conteudo_pre_critica[$i],73,2);

				$codigosErros = array($erro1, $erro2);

				foreach($codigosErros as $erro){

					$erro = intval($erro);

					if($erro){

						if($erro > 22){

							$movimento = substr($this->conteudo_pre_critica[$i],116,10);
							$movimento = intval($movimento);

							$this->resultado[$movimento]['movimento'] = $movimento;
							$this->resultado[$movimento]['mensagem'][] = $this->descricoes_movimentos[$erro];

						}
						else{
							/// se o código do erro for menor que 22, o erro é referente ao cabeçalho da remessa,
							/// que invalida todos os movimentos.
							/// Os erros desse tipo devem ser analisados antes de os movimentos serem enviados novamente.
							$this->resultado[0] =  array('movimento' => 0, 
														'mensagem' => array($this->descricoes_movimentos[$erro] . "\n" .
																		'Entre em contato com a equipe técnica.'));
							break 2;
						}
					}
				}
			}
		}
		else{
			$this->resultado[0] = array('movimento' => 0, 
										'mensagem' => array('Todos os movimentos foram processados com sucesso.'));
		}
	}
	

	/**
	 * Retorna descrições dos movimentos de retorno
	 */
	private function retornaDescricaoMovimento(){

		$descricoes_movimentos = array(
					'01' => 'Remessa sem registro tipo 0',
					'02' => 'Identificação inválida da Empresa na CAIXA',
					'03' => 'Número Inválido da Remessa',
					'04' => 'Beneficiário não pertence a Cobrança Eletrônica',
					'05' => 'Código da Remessa Inválido',
					'06' => 'Literal da Remessa Inválido',
					'07' => 'Código de Serviço Inválido',
					'08' => 'Literal de Serviço Inválido',
					'09' => 'Código do Banco Inválido',
					'10' => 'Nome do Banco Inválido',
					'11' => 'Data de gravação Inválida',
					'12' => 'Número de Remessa já Processada',
					'13' => 'Tipo de registro esperado Inválido',
					'14' => 'Tipo de Ocorrência Inválido',
					'15' => 'Literal Remessa Inválida para fase de Testes',
					'16' => 'Identificação da empresa no Registro tipo 0 difere da identificação no Registro Tipo 1',
					'17' => 'Identificação na CAIXA inválida (Nosso Número)',
					'18' => 'Código da Carteira inválido',
					'19' => 'Número seqüencial do Registro Inválido',
					'20' => 'Tipo de Inscrição da empresa Inválido',
					'21' => 'Número de Inscrição da empresa Inválido',
					'22' => 'Literal REM.TST válida somente para a fase de Testes',
					'23' => 'Taxa de Comissão de Permanência Inválida',
					'24' => 'Nosso Número inválido para Cobrança Registrada emissão Beneficiário (14)',
					'25' => 'Dígito do Nosso Número não confere',
					'26' => 'Data de vencimento inválida',
					'27' => 'Valor do título inválido',
					'28' => 'Espécie de título Inválida',
					'29' => 'Código de Aceite Inválido',
					'30' => 'Data de emissão do título inválida',
					'31' => 'Instrução de Cobrança 1 Inválida',
					'32' => 'Instrução de Cobrança 2 Inválida',
					'33' => 'Instrução de Cobrança 3 Inválida',
					'34' => 'Valor de Juros Inválido',
					'35' => 'Data do Desconto Inválida',
					'36' => 'Valor do Desconto Inválido',
					'37' => 'Valor do IOF Inválido',
					'38' => 'Valor do Abatimento Inválido',
					'39' => 'Tipo de Inscrição do Pagador Inválido',
					'40' => 'Número de Inscrição do Pagador Inválido',
					'41' => 'Número de Inscrição do Pagador obrigatório',
					'42' => 'Nome do Pagador obrigatório',
					'43' => 'Endereço do Pagador obrigatório',
					'44' => 'CEP do Pagador Inválido',
					'45' => 'Cidade do Pagador obrigatório',
					'46' => 'Estado do Pagador obrigatório',
					'47' => 'Data da multa inválida',
					'48' => 'Valor da multa inválido'
					);
		
		return $descricoes_movimentos;
	}
	

	/**
	 * Busca no banco de dados as informações relacionadas ao nome, cnpj e 
	 * conta bancária da empresa
	 * @param integer $filial - ID da filial relacionada às informações
	 */
	private function defineInformacoesEmpresa($filial){
		
		$dados_empresa = $this->conta_filial->buscaContaPorBanco(104,$filial);

		/// Obtém código identificador da empresa no banco
		$this->codigo_identificador = $dados_empresa['identificador'];
	}

	
}