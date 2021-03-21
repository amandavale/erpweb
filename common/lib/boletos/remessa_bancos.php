<?php

$currentDir = dirname(__FILE__);
require_once  $currentDir . '/remessa_caixa.php';
require_once  $currentDir . '/remessa_itau.php';
require_once  $currentDir . '/remessa_bradesco.php';
require_once  $currentDir . '/remessa_sicoob.php';


/**
 * Classe responsável por manipular objetos que são referentes à montagem do conteúdo
 * do arquivo de remessa de cada banco
 * @author amanda
 *
 */

	class RemessaBancos{
		
		/// armazena o objeto relacionado ao banco selecionado 
		/// para gerar arquivo de remessa
		private $banco;
		
		public function __construct($modo_recebimento, $filial, $movimentos_remessa, $sequencia, $nossoNumero){
			
			switch($modo_recebimento){
				
				case 'CE':
					$this->banco = new RemessaCaixaEconomicaFederal($filial, $movimentos_remessa, $sequencia, $nossoNumero);
				break;

				case 'I':
					$this->banco = new RemessaItau($filial, $movimentos_remessa, $sequencia, $nossoNumero);
				break;

				case 'IE':
					$this->banco = new RemessaItau($filial, $movimentos_remessa, $sequencia, $nossoNumero,'estrela da mata');
				break;				

				case 'BR':
					/** Remessa do banco Bradesco para todos o condoínio Mila Center **/
					$this->banco = new RemessaBradesco($filial, $movimentos_remessa, $sequencia, $nossoNumero, 'mila center');
				break;

				case 'BT':
					/** Remessa do banco Bradesco para todos os clientes **/
					$this->banco = new RemessaBradesco($filial, $movimentos_remessa, $sequencia, $nossoNumero, 'sos prestadora');
				break;

				case 'S':
					$this->banco = new RemessaSicoob($filial, $movimentos_remessa, $sequencia, $nossoNumero);
				break;
				
			}
		}

		public function retornaConteudoArquivoRemessa(){
			return $this->banco->retornaConteudoArquivoRemessa();
		}

		public function retornaNomeArquivoRemessa($codigo){
			return $this->banco->retornaNomeArquivoRemessa($codigo);
		}
		
	}