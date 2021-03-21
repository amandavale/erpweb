<?php
	
	class endereco {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function endereco(){
			// n�o faz nada
		}


		/**
		  m�todo: AtualizaEndereco
		  prop�sito: AtualizaEndereco
		*/
		function AtualizaEndereco ($idendereco, $post, $label){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

   			$info['litlogradouro'] = $post[$label."_logradouro"];
			$info['litnumero'] = $post[$label."_numero"];
			$info['litcomplemento'] = $post[$label."_complemento"];
			$info['numidbairro'] = $post[$label."_idbairro"];
			$info['numidcidade'] = $post[$label."_idcidade"];
			$info['numidestado'] = $post[$label."_idestado"];
			$info['litcep'] = $post[$label."_cep"];

			if ($info['numidbairro'] == "") $info['numidbairro'] = "NULL";
			if ($info['numidcidade'] == "") $info['numidcidade'] = "NULL";
			if ($info['numidestado'] == "") $info['numidestado'] = "NULL";

			//atualiza o registro no banco de dados
			$this->update($idendereco, $info);

			return(1);

		}




		/**
		  m�todo: BuscaDadosEndereco
		  prop�sito: BuscaDadosEndereco
		*/
		function BuscaDadosEndereco ($idendereco, &$info, $label){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			// busca os dados no banco de dados
			$dados_endereco = $this->getById($idendereco);

			$info[$label."_logradouro"] = $dados_endereco['logradouro'];
			$info[$label."_numero"] = $dados_endereco['numero'];
			$info[$label."_complemento"] = $dados_endereco['complemento'];
			$info[$label."_idbairro"] = $dados_endereco['idbairro'];
			$info[$label."_idcidade"] = $dados_endereco['idcidade'];
			$info[$label."_idestado"] = $dados_endereco['idestado'];
			$info[$label."_cep"] = $dados_endereco['cep'];


			return($dados_endereco);

		}



		/**
		  m�todo: InsereEndereco
		  prop�sito: InsereEndereco
		*/
		function InsereEndereco ($post, $label){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
   			$info['logradouro'] = $post[$label."_logradouro"];
			$info['numero'] = $post[$label."_numero"];
			$info['complemento'] = $post[$label."_complemento"];
			$info['idbairro'] = $post[$label."_idbairro"];
			$info['idcidade'] = $post[$label."_idcidade"];
			$info['idestado'] = $post[$label."_idestado"];
			$info['cep'] = $post[$label."_cep"];
			
			if ($info['idbairro'] == "") $info['idbairro'] = "NULL";
			if ($info['idcidade'] == "") $info['idcidade'] = "NULL";
			if ($info['idestado'] == "") $info['idestado'] = "NULL";

			//grava o registro no banco de dados
			$idendereco =	$this->set($info);
								
			return($idendereco);

		}


		


		/**
		  m�todo: getById
		  prop�sito: busca informa��es
		*/
		function getById($idendereco){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											EDR.* , BAR.nome_bairro , CID.nome_cidade, EST.*
										FROM
											{$conf['db_name']}endereco EDR
												 LEFT OUTER JOIN {$conf['db_name']}bairro BAR ON EDR.idbairro=BAR.idbairro
												 LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade=CID.idcidade
												 LEFT OUTER JOIN {$conf['db_name']}estado EST ON EDR.idestado=EST.idestado

										WHERE
											 EDR.idendereco = $idendereco ";

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
		  m�todo: make_list
		  prop�sito: faz a listagem
		*/
		
		function make_list( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
			
			if ($ordem == "") $ordem = " ORDER BY EDR.logradouro ASC";
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											EDR.*   , BAR.nome_bairro , CID.nome_cidade, EST.nome_estado
										FROM
           						{$conf['db_name']}endereco EDR 
												 LEFT OUTER JOIN {$conf['db_name']}bairro BAR ON EDR.idbairro=BAR.idbairro
												 LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade=CID.idcidade
												 LEFT OUTER JOIN {$conf['db_name']}estado EST ON EDR.idestado=EST.idestado
												
										$filtro
										$ordem";

			//manda fazer a pagina��o
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

			if($list_q){
				
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){
					
					//insere um �ndice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);
					
					
					
					
					
					
					
          $list_return[] = $list;
          
          $cont++;
				}
				
				return $list_return;
					
			}	
			else{
				$this->err = $falha['listar'];
				return(0);
			}
		}	
		


		/**
		 * Busca endere�o de um cliente
		 * @param integer $idcliente - ID do cliente cujo endere�o deve ser buscado
		 * @param string $tipo_endereco - Tipo de endere�o: cliente ou cobranca
		 * @param array $list_return - Array contendo os dados do endere�o encontrado
		 */
		function buscaEnderecoPorCliente($idcliente, $tipo_endereco = 'cliente'){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			/// Define se a busca ser� feita pelo endere�o principal ou pelo endere�o de cobran�a
			if($tipo_endereco == 'cobranca'){
				$tipo = 'idendereco_cobranca';
			}
			else{
				$tipo = 'idendereco_cliente';
			}
			
			$list_sql = "	SELECT
								EDR.logradouro, EDR.numero, EDR.complemento, EDR.cep, BAR.nome_bairro , CID.nome_cidade, EST.nome_estado
							FROM
          						{$conf['db_name']}cliente CLI
          							LEFT OUTER JOIN {$conf['db_name']}endereco EDR  ON CLI.$tipo = EDR.idendereco
									 LEFT OUTER JOIN {$conf['db_name']}bairro BAR ON EDR.idbairro=BAR.idbairro
									 LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade=CID.idcidade
									 LEFT OUTER JOIN {$conf['db_name']}estado EST ON EDR.idestado=EST.idestado
							WHERE CLI.idcliente = $idcliente";

var_dump($list_sql);

			//manda fazer a pagina��o
			$list_q = $db->query($list_sql);

			if($list_q){
				
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				while($list = $db->fetch_array($list_q)){
					
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
			m�todo: set
		  prop�sito: inclui novo registro
		*/

		function set($info){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			
			$set_sql = "  INSERT INTO
		                  {$conf['db_name']}endereco
		                    (
		                    
												logradouro, 
												numero, 
												complemento, 
												idbairro, 
												idcidade,
												idestado, 
												cep  
												
												)
		                VALUES
		                    (
		                    
		                    '" . $info['logradouro'] . "',  
												'" . $info['numero'] . "',  
												'" . $info['complemento'] . "',  
												" . $info['idbairro'] . ",  
												" . $info['idcidade'] . ",
												" . $info['idestado'] . ",  
												'" . $info['cep'] . "'   
												
												)";

			//executa a query e testa se a consulta foi "boa"
			if($db->query($set_sql)){
				//retorna o c�digo inserido
				$codigo = $db->insert_id();
				
				
				
				return($codigo);
			}
			else{
				$this->err = $falha['inserir'];
				return(0);
			}
		}
		
		/**
		  m�todo: update
		  prop�sito: atualiza os dados
		  
		  1) o vetor $info deve conter todos os campos tabela a serem atualizados
			2) a vari�vel $id deve conter o c�digo do usu�rio cujos dados ser�o atualizados
			3) campos literais dever�o ter o prefixo lit e campos num�ricos dever�o ter o prefixo num
		*/
		function update($idendereco, $info){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}endereco
											SET ";

   		//varre o formul�rio e monta a consulta;
			$cont_validos = 0;
			foreach($info as $campo => $valor){

				$tipo_campo = substr($campo, 0, 3);
				$nome_campo = substr($campo, 3, strlen($campo) - 3);
					
				if(($tipo_campo == "lit") || ($tipo_campo == "num")){
					
					$usu_validos["$campo"] = $valor;
					$cont_validos++;
					
				}
					
			}
			
			$cont = 0;
			foreach($usu_validos as $campo => $valor){
			
				$tipo_campo = substr($campo, 0, 3);
				$nome_campo = substr($campo, 3, strlen($campo) - 3);
				
				if($tipo_campo == "lit")
					$update_sql .= "$nome_campo = '$valor'";
				elseif($tipo_campo == "num")
					$update_sql .= "$nome_campo = $valor";
					
				$cont++;
				
				//testa se � o �ltimo
				if($cont != $cont_validos){
					$update_sql .= ", ";
				}
				
			}
			

			//completa o sql com a restri��o
			$update_sql .= " WHERE  idendereco = $idendereco ";
			
   			//envia a query para o banco
			$update_q = $db->query($update_sql);
			
			if($update_q)
			  return(1);
			else
			  $this->err = $falha['alterar'];
		}	
		
		/**
		  m�todo: delete
		  prop�sito: excluir registro
		*/
		function delete($idendereco){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			// conjunto de depend�ncias geradas
			
			//---------------------
			

			// verifica se pode excluir
			if (1) {

				

				$delete_sql = "	DELETE FROM
													{$conf['db_name']}endereco
												WHERE
													 idendereco = $idendereco ";
				$delete_q = $db->query($delete_sql);

				if($delete_q){
					return(1);
				}
				else{
					$this->err = $falha['excluir'];
					return(0);
				}
				
			}
			else {
				$this->err = "Este registro n�o pode ser exclu�do, pois existem registros relacionados a ele.";
			}	

		}	

		
		/**
		  m�todo: make_list
		  prop�sito: faz a listagem para colocar no select
		*/

		function make_list_select( $filtro = "", $ordem = " ORDER BY logradouro ASC") {
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}endereco
										$filtro
										$ordem";

			$list_q = $db->query($list_sql);
			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					foreach($list as $campo => $value){
						$list_return["$campo"][$cont] = $value;
					}

          $cont++;
				}

				return $list_return;

			}
			else{
				$this->err = $falha['listar'];
				return(0);
			}
			
		}
		
		

		
		function formataStringEndereco($dados_endereco){


			$strEndereco = '';
			
			if (!empty($dados_endereco['logradouro']))
				$strEndereco .= $dados_endereco['logradouro'];
			if (!empty($dados_endereco['numero']))
				$strEndereco .= ', ' . $dados_endereco['numero'];
			if (!empty($dados_endereco['complemento']))
				$strEndereco .= ' - ' . $dados_endereco['complemento'];
			if (!empty($dados_endereco['nome_bairro']))
				$strEndereco .= ' Bairro ' . $dados_endereco['nome_bairro'];
			if (!empty($dados_endereco['nome_cidade']))
				$strEndereco .= ' - ' . $dados_endereco['nome_cidade'];
			if (!empty($dados_endereco['sigla_estado']))
				$strEndereco .= '/' . $dados_endereco['sigla_estado'];
			if (!empty($dados_endereco['cep']))
				$strEndereco .= " - CEP: " . $dados_endereco['cep'];
			
			return $strEndereco;
			
		}
		
		
		

	} // fim da classe
?>