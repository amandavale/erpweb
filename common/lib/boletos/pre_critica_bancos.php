<?php

require_once dirname(__FILE__) . '/pre_critica_caixa_economica.php';

/**
 * Classe responsável por manipular objetos que são referentes à interpretação
 * do arquivo de pré-crítica de cada banco
 * @author amanda
 *
 */

	class PreCriticaBancos{
		
		/// armazena o objeto relacionado ao banco selecionado 
		private $banco;
		
		private $arquivo_validado;
		
		public function __construct($banco, $filial, $conteudo_retorno){
			
			switch($banco){
					
				case 'CE':
					$this->banco = new PreCriticaCaixaEconomica($filial, $conteudo_retorno);
					break;
						
			}


		}
		
		/**
		 * Faz interpretação do arquivo de retorno e retorna true
		 * se o arquivo for válido e false se for inválido
		 */
		public function interpretaConteudoPreCritica(){
			
			if($this->banco->interpretaConteudoPreCritica()){
				$this->arquivo_validado = true;
			}
			else{
				$this->arquivo_validado = false;
			}
			
			return $this->arquivo_validado;
		}

		/**
		 * Retorna array com informações sobre a análise do arquivo
		 */
		public function obtemResultado(){

			return $this->banco->obtemResultado();
		}
		
	}