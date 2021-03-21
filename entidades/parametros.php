<?php
	
	class parametros {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function parametros(){
			// n�o faz nada
		}

		/**
		  m�todo: getById
		  prop�sito: busca informa��es
		*/
		
		function getById($idparametros){
	
			// vari�veis globais
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
			m�todo: make_list
			prop�sito: faz a listagem. Caso este m�todo seja utilizado para armazenar o retorno na sess�o do usu�rio deve ser passado o argumento $sessao com valor 1.
		*/
			
		function make_list( $pg, $rppg, $filtro = "", $ordem = "", $url = "", $sessao = "0"){
		
			if ($ordem == "") $ordem = " ORDER BY PARAM.descricao_parametro ASC";
	
			// vari�veis globais
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
	
			//manda fazer a pagina��o
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);
	
			if($list_q){ //busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){
					//insere um �ndice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);
					if ($sessao == 1){	//se o m�todo for utilizado para armazenar na sess�o
						$nome_parametro = $list['nome_parametro'];
		          		$list_return[$nome_parametro] = $list;	//os �ndices de $_SESSION['parametros'] ser�o os nomes dos par�metros, facilitando sua localiza��o
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
			if($db->query($set_sql)){ //retorna o c�digo inserido
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
		function update($idparametros, $info){
	
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
	
			//inicializa a query
			$update_sql = "	UPDATE
													{$conf['db_name']}parametros
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
				$update_sql .= " WHERE  idparametros = $idparametros ";
				
				
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
		function delete($idparametros){
		
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
				$this->err = "Este registro n�o pode ser exclu�do, pois existem registros relacionadas a ele.";
			}
		}	
		
		/**
			m�todo: make_list_select
			prop�sito: faz a listagem para colocar no select
		*/
	
		function make_list_select( $filtro = "", $ordem = " ORDER BY descricao_parametro ASC") {
				
			// vari�veis globais
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
		 * m�todo: getParam
		 * prop�sito: buscar o valor do par�metro na base de dados
		 */
		function getParam($nomeParametro){
			
			// vari�veis globais
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
			
			// vari�veis globais
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
