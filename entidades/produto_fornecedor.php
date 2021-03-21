<?php
	
	class produto_fornecedor {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function produto_fornecedor(){
			// não faz nada
		}


		/**
		  método: GravaFornecedor
		  propósito: GravaFornecedor
		*/

		function GravaFornecedor ($post, $idproduto){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// deleta os fornecedores do Produto
			$this->DeletaFornecedor($idproduto);

			$info['idproduto'] = $idproduto;

			// percorre os fornecedores
			for ($i=1; $i<=intval($post['total_fornecedores']); $i++) {
				if (isset($post["codigo_fornecedor_$i"])) {
					$info['idfornecedor'] = $post["codigo_fornecedor_$i"];
					$this->set($info);
				}

			}

			return(1);

		}




		/**
		  método: DeletaFornecedor
		  propósito: DeletaFornecedor
		*/

		function DeletaFornecedor ($idproduto){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// deleta todos os fornecedors de um determinado produto
			$delete_sql = "	DELETE FROM
												{$conf['db_name']}produto_fornecedor
											WHERE
												 idproduto = $idproduto ";
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
		  método: SelecionaFornecedor
		  propósito: SelecionaFornecedor
		*/

		function SelecionaFornecedor ($post, $idproduto = ""){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT
											FORN.*
										FROM
           						{$conf['db_name']}fornecedor FORN
										ORDER BY FORN.nome_fornecedor ASC
									";

			//manda fazer a paginação
			$list_q = $db->query($list_sql);

			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					if ($post == "") {

						// verifica se o funcionario esta relacionado com a filial
						$info_registro = $this->getById($idproduto, $list['idfornecedor']);
						if ($info_registro['idfornecedor'] != "") $list['selecionado'] = 1;
					}
					else {
						$atributo =	'fornecedor_' . $list['idfornecedor'];
						if ($post[$atributo]) $list['selecionado'] = 1;
					}

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
		  método: getById
		  propósito: busca informações
		*/
		function getById($idproduto,$idfornecedor){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											PRDFOR.*
										FROM
											{$conf['db_name']}produto_fornecedor PRDFOR
										WHERE
											 PRDFOR.idproduto = $idproduto AND  PRDFOR.idfornecedor = $idfornecedor ";

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
			
			if ($ordem == "") $ordem = " ORDER BY PRDFOR.idproduto ASC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											PRDFOR.*   , PRD.descricao_produto , FORN.nome_fornecedor
										FROM
           						{$conf['db_name']}produto_fornecedor PRDFOR 
												 INNER JOIN {$conf['db_name']}produto PRD ON PRDFOR.idproduto=PRD.idproduto 
												 INNER JOIN {$conf['db_name']}fornecedor FORN ON PRDFOR.idfornecedor=FORN.idfornecedor 
												
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
		                  {$conf['db_name']}produto_fornecedor
		                    (
		                    
												idproduto, 
												idfornecedor  
												
												)
		                VALUES
		                    (
		                    
		                    " . $info['idproduto'] . ",  
												" . $info['idfornecedor'] . "   
												
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
		function update($idproduto,$idfornecedor, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}produto_fornecedor
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
			$update_sql .= " WHERE  idproduto = $idproduto AND  idfornecedor = $idfornecedor ";
			
			
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
		function delete($idproduto,$idfornecedor){
			
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
													{$conf['db_name']}produto_fornecedor
												WHERE
													 idproduto = $idproduto AND  idfornecedor = $idfornecedor ";
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
				$this->err = "Este registro não pode ser excluído, pois existem registros relacionados a ele.";
			}	

		}	

		
		/**
		  método: make_list
		  propósito: faz a listagem para colocar no select
		*/

		function make_list_select( $filtro = "", $ordem = " ORDER BY idproduto ASC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}produto_fornecedor
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
