<?php
	
	class parametros {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function parametros(){
			// não faz nada
		}

		/**
		  método: getById
		  propósito: busca informações
		*/
		
		function getById($idparametros){
	
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
	
			$get_sql = "	SELECT
												PARAM.*
											FROM
												{$conf['db_name']}parametros PARAM
											WHERE
												 PARAM.idparametros = $idparametros ";
	
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
			método: make_list
			propósito: faz a listagem. Caso este método seja utilizado para armazenar o retorno na sessão do usuário deve ser passado o argumento $sessao com valor 1.
		*/
			
		function make_list( $pg, $rppg, $filtro = "", $ordem = "", $url = "", $sessao = "0"){
		
			if ($ordem == "") $ordem = " ORDER BY PARAM.descricao_parametro ASC";
	
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
												PARAM.*  
											FROM
	           						{$conf['db_name']}parametros PARAM 
													
											$filtro
											$ordem";
	
			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);
	
			if($list_q){ //busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){
					//insere um índice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);
					if ($sessao == 1){	//se o método for utilizado para armazenar na sessão
						$nome_parametro = $list['nome_parametro'];
		          		$list_return[$nome_parametro] = $list;	//os índices de $_SESSION['parametros'] serão os nomes dos parâmetros, facilitando sua localização
					}
					else{
						$list_return[] = $list;
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
	
		/**
			método: set
			propósito: inclui novo registro
		*/
	
		function set($info){
	
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
	
			$set_sql = "  INSERT INTO
			                  {$conf['db_name']}parametros
			                    (
			                    
													descricao_parametro,
													nome_parametro, 
													valor_parametro  
													
													)
			                VALUES
			                    (
			                    '" . $info['descricao_parametro'] . "',
			                    '" . $info['nome_parametro'] . "',  
								'" . $info['valor_parametro'] . "'   
													
													)";
				
			//executa a query e testa se a consulta foi "boa"
			if($db->query($set_sql)){ //retorna o código inserido
				$codigo = $db->insert_id();
				return($codigo);
			}
			else{
				$this->err = $falha['inserir'];
				return(0);
			}
		}
			
		/**
		 	método: update
			propósito: atualiza os dados
			1) o vetor $info deve conter todos os campos tabela a serem atualizados
			2) a variável $id deve conter o código do usuário cujos dados serão atualizados
			3) campos literais deverão ter o prefixo lit e campos numéricos deverão ter o prefixo num
		*/
		function update($idparametros, $info){
	
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
	
			//inicializa a query
			$update_sql = "	UPDATE
													{$conf['db_name']}parametros
												SET ";
	
	   		//varre o formulário e monta a consulta;
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
					
					//testa se é o último
					if($cont != $cont_validos){
						$update_sql .= ", ";
					}
					
				}
				
	
				//completa o sql com a restrição
				$update_sql .= " WHERE  idparametros = $idparametros ";
				
				
				//envia a query para o banco
				$update_q = $db->query($update_sql);
				
				if($update_q)
				  return(1);
				else
				  $this->err = $falha['alterar'];
			}	
			
		/**
			método: delete
			propósito: excluir registro
		*/
		function delete($idparametros){
		
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
	
			// conjunto de dependências geradas
			//---------------------
	
			// verifica se pode excluir
			if (1) {
				$delete_sql = "	DELETE FROM
														{$conf['db_name']}parametros
													WHERE
														 idparametros = $idparametros ";
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
				$this->err = "Este registro não pode ser excluído, pois existem registros relacionadas a ele.";
			}
		}	
		
		/**
			método: make_list_select
			propósito: faz a listagem para colocar no select
		*/
	
		function make_list_select( $filtro = "", $ordem = " ORDER BY descricao_parametro ASC") {
				
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
	
			$list_sql = "	SELECT
						 						*
											FROM
												{$conf['db_name']}parametros
											$filtro
											$ordem";
	
			$list_q = $db->query($list_sql);
			if($list_q){ //busca os registros no banco de dados e monta o vetor de retorno
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
		
		/**
		 * método: getParam
		 * propósito: buscar o valor do parâmetro na base de dados
		 */
		function getParam($nomeParametro){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$valor_param_sql = "SELECT valor_parametro FROM {$conf['db_name']}parametros WHERE nome_parametro = '$nomeParametro'";
			
			//executa a query e testa se a consulta foi bem sucedida
			if($valor_param = $db->query($valor_param_sql)){ //retorna o valor do parametro
				
				$getparam = $db->fetch_array($valor_param);
				return($getparam['valor_parametro']);
			}
			else{
				$this->err = $falha['listar'];
				return(0);
			}
			
		}
		
		
		//Retorna um array com todos os parametros
		function getAll(){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$valor_param_sql = "SELECT nome_parametro, valor_parametro FROM {$conf['db_name']}parametros";
			
			//executa a query e testa se a consulta foi bem sucedida
			if($valor_param = $db->query($valor_param_sql)){ //retorna o valor do parametro
				
				while($list = $db->fetch_array($valor_param)){
					$ArrParam[$list['nome_parametro']] = $list['valor_parametro'];
				}
				return($ArrParam);
			}
			else{
				$this->err = $falha['listar'];
				return(0);
			}
			
		}
	

	} // fim da classe
?>
