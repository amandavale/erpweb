<?php
	
	class conta_filial {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function conta_filial(){
			// não faz nada
		}



		/**
		  método: GravaContasBancariasFilial
		  propósito: GravaContasBancariasFilial
		*/

		function GravaContasBancariasFilial ($post, $idfilial){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// deleta os funcionarios da filial
			$this->DeletaContasBancariasFilial($idfilial);

			$info['idfilial'] = $idfilial;

			// percorre os funcionarios
			for ($i=1; $i<=intval($post['total_contas_bancarias']); $i++) {

				if (isset($post["idbanco_$i"])) {

					$info['idbanco'] = $post["idbanco_$i"];
					$info['agencia_filial'] = $post["agencia_filial_$i"];
					$info['agencia_dig_filial'] = $post["agencia_dig_filial_$i"];
					$info['conta_filial'] = $post["conta_filial_$i"];
					$info['conta_dig_filial'] = $post["conta_dig_filial_$i"];
					$info['principal_filial'] = $post["principal_filial_$i"];
					$info['conta_cedente'] = ($post["conta_cedente_$i"]);
					$info['conta_cnpj'] = $post["conta_cnpj_$i"];
					$info['carteira'] = $post["carteira_$i"];
					$info['identificador'] = $post["identificador_$i"];
					$info['prefixo_nosso_numero'] = $post["prefixo_nosso_numero_$i"];

					$this->set($info);
				}

			}

			return(1);

		}



		/**
		  método: DeletaContasBancariasFilial
		  propósito: DeletaContasBancariasFilial
		*/

		function DeletaContasBancariasFilial ($idfilial){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// deleta todos os funcionarios de uma determinada filial
			$delete_sql = "	DELETE FROM
												{$conf['db_name']}conta_filial
											WHERE
												 idfilial = $idfilial ";
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
		  método: getById
		  propósito: busca informações
		*/
		function getById($idconta_filial){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											CFLI.*, BNC.nome_banco
										FROM
											{$conf['db_name']}conta_filial CFLI
												INNER JOIN {$conf['db_name']}banco BNC ON CFLI.idbanco=BNC.idbanco
											
										WHERE
											 CFLI.idconta_filial = $idconta_filial ";

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
		 * Busca dados de uma conta de acordo com o banco e filial passados como parâmetro
		 * @param integer $codigo_banco - Código do banco definido pela febraban
		 * @param integer $codigo_filial - Código da filial cuja conta deve ser buscada
		 * @param string $cedente - Nome do cedente, se necessário incluir na busca
		 */
		function buscaContaPorBanco($codigo_banco, $id_filial, $cedente = ''){
		
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
				
			$get_sql = "	SELECT
								conta_filial.*
							FROM
							{$conf['db_name']}banco
							INNER JOIN {$conf['db_name']}conta_filial ON conta_filial.idbanco = banco.idbanco
									
							WHERE
								conta_filial.idfilial = $id_filial AND codigo_banco = $codigo_banco";
			if($cedente){
				$get_sql .= ' AND conta_filial.conta_cedente LIKE "%' . $cedente . '%"';
			}

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
			
			if ($ordem == "") $ordem = " ORDER BY CFLI.idfilial ASC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											CFLI.*   , FLI.nome_filial , BNC.nome_banco
										FROM
           						{$conf['db_name']}conta_filial CFLI 
												 INNER JOIN {$conf['db_name']}filial FLI ON CFLI.idfilial=FLI.idfilial 
												 INNER JOIN {$conf['db_name']}banco BNC ON CFLI.idbanco=BNC.idbanco 
												
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
					
					
					
					
					if ($list['principal_filial'] == '0') $list['principal_filial'] = "Não"; 
					else if ($list['principal_filial'] == '1') $list['principal_filial'] = "Sim"; 
					
					
					
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
			
			if(!isset($info['prefixo_nosso_numero']) || !$info['prefixo_nosso_numero']){
				$info['prefixo_nosso_numero'] = 0;
			}

			if(!isset($info['identificador']) || !$info['identificador']){
				$info['identificador'] = 0;
			}
			
			$set_sql = "  INSERT INTO
		                  {$conf['db_name']}conta_filial
		                    (
		                    
												idfilial, 
												idbanco, 
												agencia_filial, 
												agencia_dig_filial, 
												conta_filial, 
												conta_dig_filial, 
												principal_filial,
												conta_cedente,
												conta_cnpj,
												carteira,
												identificador,
												prefixo_nosso_numero
												
												)
		                VALUES
		                    (
		                    
		                    " . $info['idfilial'] . ",  
												" . $info['idbanco'] . ",  
												'" . $info['agencia_filial'] . "',  
												'" . $info['agencia_dig_filial'] . "',  
												'" . $info['conta_filial'] . "',  
												'" . $info['conta_dig_filial'] . "',  
												'" . $info['principal_filial'] . "',   
												'" . $info['conta_cedente'] . "',
												'" . $info['conta_cnpj'] . "',
												'" . $info['carteira'] . "',
												" . $info['identificador'] . ",
												" . $info['prefixo_nosso_numero'] . "
												
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
		function update($idconta_filial, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}conta_filial
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
			$update_sql .= " WHERE  idconta_filial = $idconta_filial ";
			
			
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
		function delete($idconta_filial){
			
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
													{$conf['db_name']}conta_filial
												WHERE
													 idconta_filial = $idconta_filial ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY idfilial ASC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
								CFLI.*   , BNC.nome_banco
					 		FROM
           						{$conf['db_name']}conta_filial CFLI
												 INNER JOIN {$conf['db_name']}filial FLI ON CFLI.idfilial=FLI.idfilial
												 INNER JOIN {$conf['db_name']}banco BNC ON CFLI.idbanco=BNC.idbanco
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
					
					$list_return["conta_filial_descricao"][$cont] = "Banco: " . $list['nome_banco'] . " Ag: " . $list['agencia_filial'] . $list['agencia_dig_filial']. " Conta: " . $list['conta_filial'] . $list['conta_dig_filial'];

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
