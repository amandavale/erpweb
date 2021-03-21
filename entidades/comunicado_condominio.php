<?php

	class comunicado_condominio {

		var $err;

		/**
	    * construtor da classe
	  	*/
		Function comunicado_condominio(){
			// n�o faz nada
		}

		
		
		/**
		 * Associa um comunicado a condom�nios
		 * @param integer $id_comunicado - ID do condom�nio que ser� associado
		 * @param array $condominios - Array contendo IDs dos condom�nios que ser�o associados
		 * @param boolean $alteracao - Indica se a opera��o � de altera��o (true) ou n�o (false)
		 * @return boolean
		 */
		function associaComunicadoCondominios($id_comunicado, $condominios, $alteracao = false){
			
			global $conf;
			global $db;
			
			$sucesso_insercao = true;
			
			/// se a opera��o for de altera��o apaga os registros que j� existem 
			/// para depois inserir novos
			if($alteracao){
				
				$query = 'DELETE FROM ' . $conf['db_name'] . 'comunicado_condominio WHERE idcomunicado = ' . $id_comunicado;
				if(!$db->query($query)){
					$sucesso_insercao = false;
				} 
			}
			
			if($sucesso_insercao){
				
				$numero_condominios = sizeof($condominios);
				
				for($i=0; $i < $numero_condominios; $i++){
					
					$info = array('idcomunicado' => $id_comunicado, 'idcondominio' => $condominios[$i]);
					
					if(!$this->set($info)){
						$sucesso_insercao = false;
					}
				}
			}
			
			return $sucesso_insercao;
		}
		
		
		
		/**
		 * Busca todos os condom�nios que est�o associados a um comunicado
		 * @param integer $id_comunicado
		 */
		function buscaCondominiosAssociados($id_comunicado){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = " 	SELECT idcondominio
							FROM
								{$conf['db_name']}comunicado_condominio
							WHERE idcomunicado = $id_comunicado";
			
			//manda fazer a pagina��o
			$list_q = $db->query($list_sql);
			
			if($list_q){
			
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();

				while($list = $db->fetch_array($list_q)){
			
					$list_return[] = $list['idcondominio'];
			
				}
			
				return $list_return;
			
			}
			else{
				$this->err = $falha['listar'];
				return(0);
			}
		}
		

		/**
		 * Busca dados dos comunicados aos quais o condom�nio est� associado
		 * @param integer $id_condominio - ID do condom�nio
		 */
		public function buscaComunicadosCondominio($id_condominio){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			require_once dirname(__FILE__) . '/comunicado.php';
			$comunicado = new comunicado();
			
			$list_sql = ' 	SELECT comunicado.* ' .
						'	FROM ' .
						"		{$conf['db_name']}comunicado_condominio " .
						"	LEFT JOIN {$conf['db_name']}comunicado ON (comunicado_condominio.idcomunicado = comunicado.idcomunicado) " .
						"	WHERE comunicado_condominio.idcondominio = $id_condominio " .  
						'	ORDER BY comunicado.criacao DESC';
				
			//manda fazer a pagina��o
			$list_q = $db->query($list_sql);
				
			if($list_q){
			
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
		
				while($list = $db->fetch_array($list_q)){

					$list['criacao'] = $form->FormataDataHoraParaExibir($list['criacao']);
					
					$anexos = $comunicado->buscaArquivosComunicado($list['idcomunicado']);
					
					$list['anexos'] = implode('<br />',$anexos);
						
					$list_return[] = $list;
				}
				
				return $list_return;					
			}
			else{
				$this->err = $falha['listar'];
				return(0);
			}
		}
		

		
		/**
		 * Busca dados dos comunicados aos quais o condom�nio est� associado
		 * Busca resultados com pagina��o
		 * @param integer $id_condominio - ID do condom�nio
		 */
		public function buscaComunicadosCondominioPaginado($id_condominio, $pg, $rppg){
		
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
				
			require_once dirname(__FILE__) . '/comunicado.php';
			$comunicado = new comunicado();
		
			$list_sql = ' 	SELECT comunicado.* ' .
					'	FROM ' .
					"		{$conf['db_name']}comunicado_condominio " .
					"	LEFT JOIN {$conf['db_name']}comunicado ON (comunicado_condominio.idcomunicado = comunicado.idcomunicado) " .
					"	WHERE comunicado_condominio.idcondominio = $id_condominio " .
					'	ORDER BY comunicado.criacao DESC';
		
			//manda fazer a pagina��o
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, '');

			$cont = 0;
				
			if($list_q){
		
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
					
				while($list = $db->fetch_array($list_q)){
					
					//insere um �ndice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);
		
					$list['criacao'] = $form->FormataDataHoraParaExibir($list['criacao']);
		
					$anexos = $comunicado->buscaArquivosComunicado($list['idcomunicado']);
		
					$list['anexos'] = implode('<br />',$anexos);
		
					$list_return[] = $list;
				}
				
				$cont++;
					
				return $list_return;
			}
			else{
				$this->err = $falha['listar'];
				return(0);
			}
		}
		
		
		
		/**
			m�todo: set
		  prop�sito: inclui novo registro
		*/

		Function set($info){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$set_sql = "  INSERT INTO
		                  {$conf['db_name']}comunicado_condominio
		                  (
		                  	idcomunicado,
		                  	idcondominio
						)
		                VALUES
		                    (
							" . $info['idcomunicado'] . ", 
							" . $info['idcondominio'] . "
						)";

		                  

			//executa a query e testa se a consulta foi "boa"
			if($db->query($set_sql)){
				return true;
			}
			else{
				$this->err = $falha['inserir'];
				return(0);
			}
		}
		
		
		/**
		 * Envia emails para os condom�nios informando que h� um comunicado dispon�vel
		 * @param string $titulo_comunicado - T�tulo do comunicado
		 * @param string $data_criacao - Data de cria��o do comunicado
		 * @param array $condominios - Array com condom�nios para os quais ser� enviado email
		 */
		function enviaEmailCondominios($titulo_comunicado, $data_criacao, $condominios){

			global $conf;
			global $cliente_condominio;
			global $db;
			
			$header = "From: SOS Prestadora <sos@sosprestadora.com.br>\r\n";
			$header .= "Reply-to: SOS Prestadora <sos@sosprestadora.com.br>\r\n";
			
			$numero_condominios = sizeof($condominios);
			
			
			$list_sql = 
					'	(SELECT nome_cliente, email_cliente, cliente.idcliente ' .
					'	FROM ' .
					"		{$conf['db_name']}apartamento " .
					' 	LEFT JOIN cliente ON (apartamento.idmorador = cliente.idcliente) ' .
					'	WHERE apartamento.idcliente IN (' . implode(',',$condominios) . ') AND email_cliente <> "") ' .
					'	UNION ' .
					'	(SELECT nome_cliente, email_cliente, cliente.idcliente ' .
					'	FROM ' .
					"		{$conf['db_name']}apartamento " .
					'	LEFT JOIN cliente ON (apartamento.idproprietario = cliente.idcliente) ' .
					'	WHERE apartamento.idcliente IN (' . implode(',',$condominios) . ') AND email_cliente <> "") ';

			$list_q = $db->query($list_sql);
								
			if($list_q){
			
				$dados_clientes = array();
				
				$emails_enviados = array();
			
				/// monta array com IDs de todos os clientes que devem receber email
				while($list = $db->fetch_array($list_q)){
					
					if(!isset($emails_enviados[$list['idcliente']])){

						$mensagem = sprintf($conf['texto_email_comunicado_disponivel'],
								$list['nome_cliente'],
								$titulo_comunicado,
								$data_criacao);

						mail($list['email_cliente'], 'Comunicado dispon�vel', $mensagem, $header);
						
						/// registra email enviado ao cliente para n�o enviar mais de uma vez
						$emails_enviados[$list['idcliente']] = true;
					}
						
				}
			}
								
		}


	} // fim da classe
?>
