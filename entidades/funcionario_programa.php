<?php
	
	class funcionario_programa {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function funcionario_programa(){
			// n�o faz nada
		}


			/**
		  m�todo: set_Programa_Funcionario
		  prop�sito: herdar as novas permiss�es do pai.
		*/
		function set_Programa_Funcionario($post){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											FNC.idfuncionario
										FROM
											{$conf['db_name']}funcionario FNC
										WHERE
											 FNC.idcargo = ". $post['idcargo'];

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);
			while($list = $db->fetch_array($get_q))
			{
				  
					$post['idfuncionario'] = $list['idfuncionario'];
					
					$set_sql = "  INSERT INTO
		                  {$conf['db_name']}funcionario_programa
		                    (
												idfuncionario, 
												idprograma, 
												permissao_adicionar, 
												permissao_editar, 
												permissao_excluir, 
												permissao_listar  
												
												)
		                VALUES
		                    (
		                    " . $post['idfuncionario'] . ",  
												" . $post['idprograma'] . ",  
												'" . $post['permissao_adicionar'] . "',  
												'" . $post['permissao_editar'] . "',  
												'" . $post['permissao_excluir'] . "',  
												'" . $post['permissao_listar'] . "'   
												
												)";
				

					$db->query($set_sql);
			}

								
		}
		
		
		/**
		 * m�todo:deleta_Programa_Funcionario
		 * prop�sito: Excluir todos os registros que o funcionario herda de seu cargo. 
		 */
		
		function deleta_Programa_Funcionario($idcargo)
		{
			
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
			

				$delete_sql = "
						DELETE
							FPROG, CPROG
						FROM
							{$conf['db_name']}funcionario_programa FPROG
							INNER JOIN {$conf['db_name']}funcionario FUNC ON FPROG.idfuncionario = FUNC.idfuncionario
							INNER JOIN {$conf['db_name']}cargo_programa CPROG ON CPROG.idcargo = FUNC.idcargo
						WHERE
							 CPROG.idcargo = $idcargo";
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
		  m�todo: getById
		  prop�sito: busca informa��es
		*/
		function getById_funcionario($idfuncionario){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											FPROG.* , FNC.*
										FROM
											{$conf['db_name']}funcionario_programa FPROG
											INNER JOIN {$conf['db_name']}funcionario FNC ON FPROG.idfuncionario = FNC.idfuncionario
										WHERE
											 FPROG.idfuncionario = $idfuncionario ";

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
		  m�todo: delete
		  prop�sito: excluir registro
		*/
		function delete_programa($idfuncionario){
			
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
													{$conf['db_name']}funcionario_programa
												WHERE
													 idfuncionario = $idfuncionario ";
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
		  m�todo: getById
		  prop�sito: busca informa��es
		*/
		function getById($idfuncionario,$idprograma){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											FPROG.*
										FROM
											{$conf['db_name']}funcionario_programa FPROG
										WHERE
											 FPROG.idfuncionario = $idfuncionario AND  FPROG.idprograma = $idprograma ";

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
			
			if ($ordem == "") $ordem = "";
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											FPROG.*   , FNC.nome_funcionario , PROG.nome_programa
										FROM
           						{$conf['db_name']}funcionario_programa FPROG 
												 INNER JOIN {$conf['db_name']}funcionario FNC ON FPROG.idfuncionario=FNC.idfuncionario 
												 INNER JOIN {$conf['db_name']}programa PROG ON FPROG.idprograma=PROG.idprograma 
												
										$filtro
										GROUP BY FNC.idfuncionario
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
		                  {$conf['db_name']}funcionario_programa
		                    (
		                    
												idfuncionario, 
												idprograma, 
												permissao_adicionar, 
												permissao_editar, 
												permissao_excluir, 
												permissao_listar  
												
												)
		                VALUES
		                    (
		                    
		                    " . $info['idfuncionario'] . ",  
												" . $info['idprograma'] . ",  
												'" . $info['permissao_adicionar'] . "',  
												'" . $info['permissao_editar'] . "',  
												'" . $info['permissao_excluir'] . "',  
												'" . $info['permissao_listar'] . "'   
												
												)";
			
			//executa a query e testa se a consulta foi "boa"
			
			//print_r($set_sql);
			
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
		function update($idfuncionario,$idprograma, $info){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}funcionario_programa
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
			$update_sql .= " WHERE  idfuncionario = $idfuncionario AND  idprograma = $idprograma ";
			
			
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
		function delete($idfuncionario,$idprograma){
			
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
													{$conf['db_name']}funcionario_programa
												WHERE
													 idfuncionario = $idfuncionario AND  idprograma = $idprograma ";
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
		  m�todo: make_list
		  prop�sito: faz a listagem para colocar no select
		*/

		function make_list_select( $filtro = "", $ordem = "") {
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}funcionario_programa
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
		
		
		
		

	} // fim da classe
?>
