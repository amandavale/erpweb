<?php
	
	class conta_fornecedor {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function conta_fornecedor(){
			// n�o faz nada
		}


		/**
		  m�todo: DeletaContasBancariasFornecedor
		  prop�sito: DeletaContasBancariasFornecedor
		*/

		function DeletaContasBancariasFornecedor ($idfornecedor){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// deleta todos os funcionarios de uma determinada filial
			$delete_sql = "	DELETE FROM
												{$conf['db_name']}conta_fornecedor
											WHERE
												 idfornecedor = $idfornecedor ";
			$delete_q = $db->query($delete_sql);

			if($delete_q){
				return(1);
			}
			else{
				$this->err = $falha['excluir'];
				return(0);
			}

		}


		/**
		  m�todo: GravaContasBancariasFornecedor
		  prop�sito: GravaContasBancariasFornecedor
		*/

		function GravaContasBancariasFornecedor ($post, $idfornecedor){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// deleta os funcionarios da filial
			$this->DeletaContasBancariasFornecedor($idfornecedor);

			$info['idfornecedor'] = $idfornecedor;

			// percorre os funcionarios
			for ($i=1; $i<=intval($post['total_contas_bancarias']); $i++) {
				if (isset($post["idbanco_$i"])) {
					$info['idbanco'] = $post["idbanco_$i"];
					$info['agencia_fornecedor'] = $post["agencia_fornecedor_$i"];
					$info['agencia_dig_fornecedor'] = $post["agencia_dig_fornecedor_$i"];
					$info['conta_fornecedor'] = $post["conta_fornecedor_$i"];
					$info['conta_dig_fornecedor'] = $post["conta_dig_fornecedor_$i"];
					$info['principal_fornecedor'] = $post["principal_fornecedor_$i"];

					$this->set($info);
				}

			}

			return(1);

		}


		/**
		  m�todo: getById
		  prop�sito: busca informa��es
		*/
		function getById($idconta_fornecedor){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											CFOR.*
										FROM
											{$conf['db_name']}conta_fornecedor CFOR
										WHERE
											 CFOR.idconta_fornecedor = $idconta_fornecedor ";

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
			
			if ($ordem == "") $ordem = " ORDER BY CFOR.idfornecedor ASC";
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											CFOR.*   , FORN.nome_fornecedor , BNC.nome_banco
										FROM
           						{$conf['db_name']}conta_fornecedor CFOR 
												 INNER JOIN {$conf['db_name']}fornecedor FORN ON CFOR.idfornecedor=FORN.idfornecedor 
												 INNER JOIN {$conf['db_name']}banco BNC ON CFOR.idbanco=BNC.idbanco 
												
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
					
					
					
					
					if ($list['principal_fornecedor'] == '0') $list['principal_fornecedor'] = "N�o"; 
					else if ($list['principal_fornecedor'] == '1') $list['principal_fornecedor'] = "Sim"; 
					
					
					
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
		                  {$conf['db_name']}conta_fornecedor
		                    (
		                    
												idfornecedor, 
												idbanco, 
												agencia_fornecedor, 
												agencia_dig_fornecedor, 
												conta_fornecedor, 
												conta_dig_fornecedor, 
												principal_fornecedor  
												
												)
		                VALUES
		                    (
		                    
		                    " . $info['idfornecedor'] . ",  
												" . $info['idbanco'] . ",  
												'" . $info['agencia_fornecedor'] . "',  
												'" . $info['agencia_dig_fornecedor'] . "',  
												'" . $info['conta_fornecedor'] . "',  
												'" . $info['conta_dig_fornecedor'] . "',  
												'" . $info['principal_fornecedor'] . "'   
												
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
		function update($idconta_fornecedor, $info){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}conta_fornecedor
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
			$update_sql .= " WHERE  idconta_fornecedor = $idconta_fornecedor ";
			
			
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
		function delete($idconta_fornecedor){
			
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
													{$conf['db_name']}conta_fornecedor
												WHERE
													 idconta_fornecedor = $idconta_fornecedor ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY idfornecedor ASC") {
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}conta_fornecedor
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
