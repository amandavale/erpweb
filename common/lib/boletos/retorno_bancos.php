<?php

require_once dirname(__FILE__) . '/retorno_caixa_economica.php';
require_once dirname(__FILE__) . '/retorno_bradesco.php';
require_once dirname(__FILE__) . '/retorno_itau.php';
require_once dirname(__FILE__) . '/retorno_sicoob.php';

/**
 * Classe responsável por manipular objetos que são referentes à interpretação
 * do arquivo de retorno de cada banco
 * @author amanda
 *
 */

	class RetornoBancos{
		
		/// armazena o objeto relacionado ao banco selecionado 
		/// para gerar arquivo de remessa
		private $banco;
		
		private $arquivo_validado;
		
		public function __construct($banco, $filial, $conteudo_retorno){
			
			switch($banco){
					
				case 'BR':
					$this->banco = new RetornoBradesco($filial, $conteudo_retorno, 'mila center');
					break;

				case 'BT':
					$this->banco = new RetornoBradesco($filial, $conteudo_retorno, 'sos prestadora');
					break;
						
				case 'CE':
					$this->banco = new RetornoCaixaEconomica($filial, $conteudo_retorno);
					break;

				case 'I':
					$this->banco = new RetornoItau($filial, $conteudo_retorno);
					break;

				case 'S':
					$this->banco = new RetornoSicoob($filial, $conteudo_retorno);
					break;
						
			}


		}
		
		/**
		 * Faz interpretação do arquivo de retorno e retorna true
		 * se o arquivo for válido e false se for inválido
		 */
		public function interpretaConteudoRetorno(){
			
			if($this->banco->interpretaConteudoRetorno()){
				$this->arquivo_validado = true;
			}
			else{
				$this->arquivo_validado = false;
			}
			
			return $this->arquivo_validado;
		}

		/**
		 * Retorna array com informações sobre as contas que foram pagas 
		 */
		public function obtemContasPagas(){

			return $this->banco->obtemContasPagas();
		}
		
	}