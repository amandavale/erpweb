<?php

	require_once '../common/lib/boletos/remessa_bancos.php';
	
	class arquivo_remessa {

		var $err;
		
		/**
    	 * construtor da classe
  		*/
		function arquivo_remessa(){

		}
		

		/**
		 * método: getById
		 * propósito: busca informações
		 */
		function getById($idarquivo_remessa){
		
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
				
			$get_sql = 'SELECT * ' .
						'FROM ' .
							" {$conf['db_name']}arquivo_remessa " .
						' WHERE ' .
							' idarquivo_remessa = ' . $idarquivo_remessa;
						
			//executa a query no banco de dados
			$get_q = $db->query($get_sql);
		
			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida
		
				$get = $db->fetch_array($get_q);
		
				//retorna o vetor associativo com os dados
				return $get;
			}
			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}
		}
		
		/**
		 * Realiza geração do arquivo de remessa para filial e banco passados como parâmetro
		 * @param string $modo_recebimento - Sigla do modo de recebimento
		 * @param integer $filial - ID da filial
		 * @param array $movimentos_remessa - Array com dados que serão incluídos no arquivo
		 * @return boolean
		 */
		function geraArquivoRemessa($modo_recebimento, $filial, $movimentos_remessa){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			/// Indica se houve sucesso na transação
			$success = true;

			/// Obtém próximo número de sequência para incluir no arquivo de remessa
			$query = "select max(sequencia) as maxId from arquivo_remessa where sigla_modo_recebimento = '$modo_recebimento'" ;
			$res = $db->query($query);
			$max = mysql_fetch_array($res);
			$sequencia = $max['maxId'] + 1;

			/// Busca valor máximo do nosso número já gerado para a remessa
			$query = "SELECT max(nosso_numero) as nosso_numero FROM {$conf['db_name']}arquivo_remessa " .
					"WHERE sigla_modo_recebimento = '$modo_recebimento'";
			$res = $db->query($query);
			$max = mysql_fetch_array($res);
			$nossoNumero = ($max['nosso_numero']?$max['nosso_numero']:0);


			/// define conteúdo do arquivo de remessa
			$remessa_bancos = new RemessaBancos($modo_recebimento, $filial, $movimentos_remessa, $sequencia, $nossoNumero);
			
			$dados_arquivo = $remessa_bancos->retornaConteudoArquivoRemessa();

			/// obtém maior valor de nosso número gerado nos registros da remessa
			if(isset($dados_arquivo['nosso_numero'])){
				$nosso_numero = $dados_arquivo['nosso_numero'];
			}
			else{
				$nosso_numero = 0;
			}

			$query = "INSERT INTO {$conf['db_name']}arquivo_remessa " .
						'(nome_arquivo,conteudo,sigla_modo_recebimento,sequencia, nosso_numero) ' .
						'VALUES ( "' .
							time() . '", "' .
							$dados_arquivo['conteudo'] . '", "' .
							$modo_recebimento . '", ' .
							$sequencia . ', ' .
							$nosso_numero . 
							')';

			$db->start_transaction();

			// realiza inserção
			if($db->query($query)){
				
				//retorna o código inserido
				$codigo = $db->insert_id();

				$nome_arquivo = $remessa_bancos->retornaNomeArquivoRemessa($codigo);
				
				/**
				 * Atualiza nome do arquivo de remessa para possuir como nome
				 * o ID do registro
				 */
				$query = "UPDATE {$conf['db_name']}arquivo_remessa " .
									 'SET nome_arquivo = "' . $nome_arquivo . '" ' .
									 ' WHERE idarquivo_remessa = ' . $codigo;
				
				if($db->query($query)){

					/**
					 * Atualiza contas a receber com o ID do arquivo inserido
					 */

					if(isset($dados_arquivo['nosso_numero_movimentos'])){

						foreach ($dados_arquivo['nosso_numero_movimentos'] as $id_movimento => $nosso_numero) {

							$query = "UPDATE {$conf['db_name']}movimento " .
											 'SET idarquivo_remessa = ' . $codigo . ', ' .
											 "nosso_numero = " . $nosso_numero . 
											 ' WHERE idmovimento  = ' . $id_movimento;

							if(!$db->query($query)){
								$this->err = $falha['associacao_movimento_arquivo'];
								$success = false;
								break;
							}
						}
					}
					else{
					
						$ids_movimentos = array_keys($movimentos_remessa);
						
						$query = "UPDATE {$conf['db_name']}movimento " .
											 'SET idarquivo_remessa = ' . $codigo .
											 ' WHERE idmovimento IN (' . implode(',', $ids_movimentos) . ')';

						if(!$db->query($query)){
							$this->err = $falha['associacao_movimento_arquivo'];
							$success = false;
						}
					}
				}
				else{
					$this->err = $falha['inserir'];
					$success = false;
				}
			}
			else{
				$this->err = $falha['inserir'];
				$success = false;
			}

			if($success){
				$db->commit();
				return true;
			}
			else{
				$db->rollback();
				return false;
			}

		}
		

		/**
		 * Realiza pesquisa de arquivos de remessa de acordo com as condições de busca
		 * do array $dados_pesquisa
		 * @param array $dados_pesquisa
		 * @return array $dados_arquivos - Retorna um array com os dados dos arquivos encontrados
		 */
		public function pesquisaArquivosRemessa($dados_pesquisa){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//obtém qual página da listagem deseja exibir
			$pg = intval(trim($_GET['pg']));
			
			//se não foi passada a página como parâmetro, faz página default igual à página 0
			if(!$pg) $pg = 0;
			
				
			$condicoes_busca = array();
			
			if($dados_pesquisa['modo_recebimento']){
				$condicoes_busca[] = 'AR.sigla_modo_recebimento = "' . $dados_pesquisa['modo_recebimento'] . '"'; 
			}
			
			if($dados_pesquisa['data_geracao_de']){
				$data_geracao = $form->FormataDataParaInserir($dados_pesquisa['data_geracao_de']) . ' 00:00:00';
				$condicoes_busca[] = 'AR.timestamp >= "' . $data_geracao . '"';
			}
				
			if($dados_pesquisa['data_geracao_ate']){
				$data_geracao = $form->FormataDataParaInserir($dados_pesquisa['data_geracao_ate']) . ' 23:59:59';
				$condicoes_busca[] = 'AR.timestamp <= "' . $data_geracao . '"';
			}
				
			if(isset($dados_pesquisa['idcliente']) && $dados_pesquisa['idcliente']){
				
				$consulta = 'SELECT distinct(AR.idarquivo_remessa), AR.nome_arquivo, AR.timestamp, B.nome_banco ' .
						"FROM {$conf['db_name']}arquivo_remessa AR " .
						"LEFT JOIN {$conf['db_name']}banco B ON (MR.idbanco = B.idbanco) " .
						"LEFT JOIN {$conf['db_name']}movimento ON (AR.idarquivo_remessa = movimento.idarquivo_remessa) ";
						
				$condicoes_busca[] = 'O.idcliente = ' . $dados_pesquisa['idcliente'];
			}
			else{
				
				$consulta = 'SELECT distinct(AR.idarquivo_remessa), AR.nome_arquivo, AR.timestamp ' .
						"FROM {$conf['db_name']}arquivo_remessa AR " .
/*						"LEFT JOIN {$conf['db_name']}banco B ON (MR.idbanco = B.idbanco) " .*/
						"LEFT JOIN {$conf['db_name']}movimento ON (AR.idarquivo_remessa = movimento.idarquivo_remessa) ";
			}

			if(!empty($condicoes_busca)){
				$consulta .= ' WHERE ';
				$consulta .= implode(' AND ',$condicoes_busca);
			}

			$consulta .= ' ORDER BY AR.timestamp DESC';			
			
			$resultado = $form->paginacao_completa($consulta, $pg, $conf['rppg'], '');
				
			//testa se a consulta foi bem sucedida
			if($resultado){ //foi bem sucedida
			
				$indice = 0;
				
				while($lista = $db->fetch_array($resultado)){
				
					$lista['index'] = $indice;
					$lista['timestamp'] = $form->FormataDataHoraParaExibir($lista['timestamp']);
					$dados_arquivos[] = $lista;

					$indice++;
				}
				
				return $dados_arquivos;
			}
			else{
				$this->err = $falha['listar'];
				return(0);
			}	
		}


		/**
		 * Exclui um registro de arquivo de remessa
		 * @param integer $idarquivo_remessa - ID do arquivo de remessa que deve ser excluído
		 * @return boolean - Retorna true em caso de sucesso e false em caso de falha
		 */
		function desfazRemessa($idarquivo_remessa){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$db->start_transaction();

	        $update_sql = "UPDATE {$conf['db_name']}movimento SET idarquivo_remessa = NULL, nosso_numero = NULL " .
        					" WHERE idarquivo_remessa = $idarquivo_remessa";

    	    //envia a query para o banco
        	$update_q = $db->query($update_sql);

        	if(!$update_q){
        		$db->rollback();
        		$this->err = 'Houve um erro ao desassociar os movimentos.';
        		return(0);
        	}
        	else{

				$delete_sql = "	DELETE FROM
									{$conf['db_name']}arquivo_remessa
								WHERE
									idarquivo_remessa = $idarquivo_remessa";
				$delete_q = $db->query($delete_sql);

				if(!$delete_q){
	        		$db->rollback();
	        		$this->err = 'Houve um erro ao excluir o arquivo.';
	        		return(0);
				}
				else{
					$db->commit();
					return(1);
				}
        	}
		}
		
	} // fim da classe
?>
