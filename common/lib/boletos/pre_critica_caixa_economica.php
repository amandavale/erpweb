<?php

/**
 * Classe respons�vel pela montagem do conte�do do arquivo de pr�-cr�tica
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/entidades/conta_filial.php';
require_once dirname(dirname(__FILE__)) . '/util.inc.php';
require_once dirname(dirname(__FILE__)) . '/form.inc.php';


class PreCriticaCaixaEconomica{


	private $conteudo_pre_critica;
	
	private $resultado;
	
	private $descricoes_movimentos;

	/** Informa��es sobre a empresa */

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
	 * Retorna array com informa��es das contas que foram pagas
	 */
	public function obtemResultado(){

		return $this->resultado;
	}
	
	
	/**
	 * Interpreta o conte�do do arquivo de retorno.
	 * Se o conte�do for inv�lido retorna false
	 * Caso contr�rio faz leitura do arquivo armazenando informa��es
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
	 * Verifica se informa��es fornecidas no arquivo s�o v�lidas
	 * Retorna false se informa��es forem inv�lidas e true se forem v�lidas
	 */
	private function validaArquivo(){
		
		/**
		 * verifica c�digo identificador da empresa
		 */
		
		$codigo_identificador = substr($this->conteudo_pre_critica[0],30,6);

		if($codigo_identificador != $this->codigo_identificador){
			return false;
		}
		
		return true;
	}
	
	/**
	 * L� o conte�do do arquivo e armazena no array $this->resultado
	 * o resultado do processamento
	 */
	private function processaMovimentos(){

		/// verifica a mensagem de retorno correspondente ao processamento
		$mensagem = substr($this->conteudo_pre_critica[0], 100,289);

		if(strstr($mensagem, 'REMESSA PROCESSADA') === false){

			/** Se n�o houver a mensagem REMESSA PROCESSADA significa que houve algum movimento rejeitado */

			/// n�mero de boletos pagos: tamanho do conte�do de retorno menos 2, que s�o
			/// header e trailler do arquivo
			$numero_boletos = (sizeof($this->conteudo_pre_critica) - 2);

			for($i=1; $i <= $numero_boletos; $i++){

				/// Array que registra mensagens de erro no processamento
				$mensagem = array();

				/// Verifica c�digo do primeiro erro
				$erro1 = substr($this->conteudo_pre_critica[$i],29,2);

				/// Verifica c�digo do segundo erro
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
							/// se o c�digo do erro for menor que 22, o erro � referente ao cabe�alho da remessa,
							/// que invalida todos os movimentos.
							/// Os erros desse tipo devem ser analisados antes de os movimentos serem enviados novamente.
							$this->resultado[0] =  array('movimento' => 0, 
														'mensagem' => array($this->descricoes_movimentos[$erro] . "\n" .
																		'Entre em contato com a equipe t�cnica.'));
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
	 * Retorna descri��es dos movimentos de retorno
	 */
	private function retornaDescricaoMovimento(){

		$descricoes_movimentos = array(
					'01' => 'Remessa sem registro tipo 0',
					'02' => 'Identifica��o inv�lida da Empresa na CAIXA',
					'03' => 'N�mero Inv�lido da Remessa',
					'04' => 'Benefici�rio n�o pertence a Cobran�a Eletr�nica',
					'05' => 'C�digo da Remessa Inv�lido',
					'06' => 'Literal da Remessa Inv�lido',
					'07' => 'C�digo de Servi�o Inv�lido',
					'08' => 'Literal de Servi�o Inv�lido',
					'09' => 'C�digo do Banco Inv�lido',
					'10' => 'Nome do Banco Inv�lido',
					'11' => 'Data de grava��o Inv�lida',
					'12' => 'N�mero de Remessa j� Processada',
					'13' => 'Tipo de registro esperado Inv�lido',
					'14' => 'Tipo de Ocorr�ncia Inv�lido',
					'15' => 'Literal Remessa Inv�lida para fase de Testes',
					'16' => 'Identifica��o da empresa no Registro tipo 0 difere da identifica��o no Registro Tipo 1',
					'17' => 'Identifica��o na CAIXA inv�lida (Nosso N�mero)',
					'18' => 'C�digo da Carteira inv�lido',
					'19' => 'N�mero seq�encial do Registro Inv�lido',
					'20' => 'Tipo de Inscri��o da empresa Inv�lido',
					'21' => 'N�mero de Inscri��o da empresa Inv�lido',
					'22' => 'Literal REM.TST v�lida somente para a fase de Testes',
					'23' => 'Taxa de Comiss�o de Perman�ncia Inv�lida',
					'24' => 'Nosso N�mero inv�lido para Cobran�a Registrada emiss�o Benefici�rio (14)',
					'25' => 'D�gito do Nosso N�mero n�o confere',
					'26' => 'Data de vencimento inv�lida',
					'27' => 'Valor do t�tulo inv�lido',
					'28' => 'Esp�cie de t�tulo Inv�lida',
					'29' => 'C�digo de Aceite Inv�lido',
					'30' => 'Data de emiss�o do t�tulo inv�lida',
					'31' => 'Instru��o de Cobran�a 1 Inv�lida',
					'32' => 'Instru��o de Cobran�a 2 Inv�lida',
					'33' => 'Instru��o de Cobran�a 3 Inv�lida',
					'34' => 'Valor de Juros Inv�lido',
					'35' => 'Data do Desconto Inv�lida',
					'36' => 'Valor do Desconto Inv�lido',
					'37' => 'Valor do IOF Inv�lido',
					'38' => 'Valor do Abatimento Inv�lido',
					'39' => 'Tipo de Inscri��o do Pagador Inv�lido',
					'40' => 'N�mero de Inscri��o do Pagador Inv�lido',
					'41' => 'N�mero de Inscri��o do Pagador obrigat�rio',
					'42' => 'Nome do Pagador obrigat�rio',
					'43' => 'Endere�o do Pagador obrigat�rio',
					'44' => 'CEP do Pagador Inv�lido',
					'45' => 'Cidade do Pagador obrigat�rio',
					'46' => 'Estado do Pagador obrigat�rio',
					'47' => 'Data da multa inv�lida',
					'48' => 'Valor da multa inv�lido'
					);
		
		return $descricoes_movimentos;
	}
	

	/**
	 * Busca no banco de dados as informa��es relacionadas ao nome, cnpj e 
	 * conta banc�ria da empresa
	 * @param integer $filial - ID da filial relacionada �s informa��es
	 */
	private function defineInformacoesEmpresa($filial){
		
		$dados_empresa = $this->conta_filial->buscaContaPorBanco(104,$filial);

		/// Obt�m c�digo identificador da empresa no banco
		$this->codigo_identificador = $dados_empresa['identificador'];
	}

	
}