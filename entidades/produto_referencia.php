<?php
	
	class produto_referencia {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function produto_referencia(){
			// n�o faz nada
		}

		/**
		  m�todo: delete_produto_referencia
		  prop�sito: excluir o produto de tdas suas referencias
		*/
		function delete_produto_referencia($idproduto){

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
													{$conf['db_name']}produto_referencia
												WHERE
													 idproduto_vendido = $idproduto
													 OR idproduto_referencia = $idproduto";
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
		  m�todo: BuscaProdutosReferencia
		  prop�sito: BuscaProdutosReferencia
		*/

		function BuscaProdutosReferencia( $idproduto_vendido, $tipoPreco ){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			// verifica qual o pre�o vai ser utilizado
			if ($tipoPreco == "B") $campo_preco = 'preco_balcao_produto';
			else if ($tipoPreco == "O") $campo_preco = 'preco_oferta_produto';
			else if ($tipoPreco == "A") $campo_preco = 'preco_atacado_produto';
			else if ($tipoPreco == "T") $campo_preco = 'preco_telemarketing_produto';


			$list_sql = "	SELECT
											PRDREF.*,
											PRD.descricao_produto,
											UNV.sigla_unidade_venda,
											PRDFL.{$campo_preco} as  preco
										FROM
           						{$conf['db_name']}produto_referencia PRDREF
												 INNER JOIN {$conf['db_name']}produto PRD ON PRDREF.idproduto_referencia = PRD.idproduto
												 INNER JOIN {$conf['db_name']}produto_filial PRDFL ON PRDFL.idproduto = PRD.idproduto
												 INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda = UNV.idunidade_venda
										WHERE
											PRDREF.idproduto_vendido = $idproduto_vendido
											  AND
											PRDFL.idfilial = {$_SESSION['idfilial_usuario']}
										
										ORDER BY
											PRD.descricao_produto ASC
									";


			//manda fazer a pagina��o
			$list_q = $db->query($list_sql);

			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					//insere um �ndice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);

					$list['preco'] = number_format($list['preco'],2,",","");


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
		  m�todo: GravaReferencia
		  prop�sito: GravaReferencia
		*/

		function GravaReferencia ($post, $idproduto){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// deleta os fornecedores do Produto
			$this->DeletaReferencia($idproduto);

			$info['idproduto_vendido'] = $idproduto;

			// percorre os fornecedores
			for ($i=1; $i<=intval($post['total_produtos']); $i++) {
				if (isset($post["codigo_referencia_$i"])) {
					$info['idproduto_referencia'] = $post["codigo_referencia_$i"];
					$this->set($info);
				}

			}

			return(1);

		}



		/**
		  m�todo: DeletaReferencia
		  prop�sito: DeletaReferencia
		*/

		function DeletaReferencia ($idproduto){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// deleta todos as referencias de um determinado produto
			$delete_sql = "	DELETE FROM
												{$conf['db_name']}produto_referencia
											WHERE
												 idproduto_vendido = $idproduto ";
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
		  m�todo: getById
		  prop�sito: busca informa��es
		*/
		function RetornaReferencia($idproduto){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$get_sql =		"	SELECT
											PRD.*, UNV.*
										FROM
           						{$conf['db_name']}produto PRD
           							INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda=UNV.idunidade_venda

										WHERE
											  PRD.idproduto = $idproduto
											  AND PRD.produto_comercializado = '1'
										ORDER BY
											PRD.descricao_produto ASC ";
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
		  m�todo: getById
		  prop�sito: busca informa��es
		*/
		function getById($idproduto_vendido,$idproduto_referencia){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											PRDREF.*
										FROM
											{$conf['db_name']}produto_referencia PRDREF
										WHERE
											 PRDREF.idproduto_vendido = $idproduto_vendido AND  PRDREF.idproduto_referencia = $idproduto_referencia ";

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

			if ($ordem == "") $ordem = " ORDER BY PRD.descricao_produto ASC";
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											PRDREF.*   , PRD.descricao_produto
										FROM
           						{$conf['db_name']}produto_referencia PRDREF 
												 INNER JOIN {$conf['db_name']}produto PRD ON PRDREF.idproduto_referencia=PRD.idproduto
												
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
		                  {$conf['db_name']}produto_referencia
		                    (
		                    
												idproduto_vendido, 
												idproduto_referencia  
												
												)
		                VALUES
		                    (
		                    
		                    " . $info['idproduto_vendido'] . ",  
												" . $info['idproduto_referencia'] . "   
												
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
		function update($idproduto_vendido,$idproduto_referencia, $info){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}produto_referencia
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
			$update_sql .= " WHERE  idproduto_vendido = $idproduto_vendido AND  idproduto_referencia = $idproduto_referencia ";
			
			
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
		function delete($idproduto_vendido,$idproduto_referencia){
			
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
													{$conf['db_name']}produto_referencia
												WHERE
													 idproduto_vendido = $idproduto_vendido AND  idproduto_referencia = $idproduto_referencia ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY idproduto_vendido ASC") {
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}produto_referencia
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
