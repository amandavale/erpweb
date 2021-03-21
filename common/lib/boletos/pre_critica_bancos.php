<?php

require_once dirname(__FILE__) . '/pre_critica_caixa_economica.php';

/**
 * Classe respons�vel por manipular objetos que s�o referentes � interpreta��o
 * do arquivo de pr�-cr�tica de cada banco
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
		 * Faz interpreta��o do arquivo de retorno e retorna true
		 * se o arquivo for v�lido e false se for inv�lido
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
		 * Retorna array com informa��es sobre a an�lise do arquivo
		 */
		public function obtemResultado(){

			return $this->banco->obtemResultado();
		}
		
	}