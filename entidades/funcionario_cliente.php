<?php
	
	class funcionario_cliente {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function funcionario_cliente(){
			// não faz nada
		}


		


		/**
		  método: getById
		  propósito: busca informações
		*/
		function getById($idcliente,$idfuncionario){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											FUN_CLI.*
										FROM
											{$conf['db_name']}funcionario_cliente FUN_CLI
										WHERE
											 FUN_CLI.idcliente = $idcliente AND  FUN_CLI.idfuncionario = $idfuncionario ";

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
		  propósito: faz a listagem
		*/
		
		function make_list( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
			
			if ($ordem == "") $ordem = "";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											FUN_CLI.*  
										FROM
           						{$conf['db_name']}funcionario_cliente FUN_CLI 
												
										$filtro
										$ordem";

			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

			if($list_q){
				
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){
					
					//insere um índice na listagem
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
		                  {$conf['db_name']}funcionario_cliente
		                    (
		                    
												
												)
		                VALUES
		                    (
		                    
		                    
												)";
			
			//executa a query e testa se a consulta foi "boa"
			if($db->query($set_sql)){
				//retorna o código inserido
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
		function update($idcliente,$idfuncionario, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}funcionario_cliente
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
			$update_sql .= " WHERE  idcliente = $idcliente AND  idfuncionario = $idfuncionario ";
			
			
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
		function delete($idcliente,$idfuncionario){
			
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
													{$conf['db_name']}funcionario_cliente
												WHERE
													 idcliente = $idcliente AND  idfuncionario = $idfuncionario ";
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
		  método: make_list
		  propósito: faz a listagem para colocar no select
		*/

		function make_list_select( $filtro = "", $ordem = "") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}funcionario_cliente
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
		
			
			
		/**
		 * Método que relaciona uma lista de funcionários a um cliente
		 * @param integer $idcliente ID do cliente
		 * @param array $funcionarios IDs dos funcionários
		 * @return bool 
		 */
		function relacionaFuncionarioACliente($idcliente, $funcionarios) {
			// variáveis globais
			global $conf;
			global $db;
			global $falha;
			
			// faz verificações preliminares
			if (!($idcliente > 0) || !is_array($funcionarios) || empty($funcionarios)) {
				$this->err = $falha['inserir'];
				return false;
			}
	
			$idcliente = (int)$idcliente;
			
			/* REMOVE AS RELAÇÕES ANTERIORES */
			$sql = "DELETE FROM {$conf['db_name']}funcionario_cliente
						WHERE idcliente = $idcliente";
			$db->query($sql);
			
			/* INSERE AS NOVAS RELAÇÕES */
			$sql = "
					INSERT INTO 
						{$conf['db_name']}funcionario_cliente 
							(idcliente, idfuncionario)
					VALUES (%s)";
			
			$sql_set = array();
			foreach ($funcionarios as $idfuncionario) {
				$sql_set[] = $idcliente . ', ' . (intval($idfuncionario));
			}
			
			$sql = sprintf($sql, implode('), (', $sql_set));
			
			// Insere os registros
			if($db->query($sql)){
				return true;
			}
			else{
				$this->err = $falha['inserir'];
				return false;
			}
		}
		
		
		
		/**
		 * Método que relaciona uma lista de clientes a um funcionário
		 * @param integer $idfuncionario ID do funcionário
		 * @param array $clientes IDs dos clientes
		 * @return bool 
		 */
		function relacionaClienteAFuncionario($idfuncionario, $clientes) {
			// variáveis globais
			global $conf;
			global $db;
			global $falha;
			
			// faz verificações preliminares
			if (!($idfuncionario > 0) || !is_array($clientes) || empty($clientes)) {
				$this->err = $falha['inserir'];
				return false;
			}
	
			$idfuncionario = (int)$idfuncionario;
			
			/* REMOVE AS RELAÇÕES ANTERIORES */
			$sql = "DELETE FROM {$conf['db_name']}funcionario_cliente
						WHERE idfuncionario = $idfuncionario";
			$db->query($sql);
			
			/* INSERE AS NOVAS RELAÇÕES */
			$sql = "
					INSERT INTO 
						{$conf['db_name']}funcionario_cliente 
							(idfuncionario, idcliente)
					VALUES (%s)";
			
			$sql_set = array();
			foreach ($clientes as $idcliente) {
				$sql_set[] = $idfuncionario . ', ' . (intval($idcliente));
			}
			
			$sql = sprintf($sql, implode('), (', $sql_set));
			
			// Insere os registros
			if($db->query($sql)){
				return true;
			}
			else{
				$this->err = $falha['inserir'];
				return false;
			}
		}
		
		
		
		

	} // fim da classe
?>
