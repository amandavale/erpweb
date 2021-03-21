<?php
	
	class filial_funcionario {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function filial_funcionario(){
			// não faz nada
		}


		/**
		  método: delete_funcionario_filial
		  propósito: excluir o funcionario de todas as suas filiais
		*/
		function delete_funcionario_filial($idfuncionario){

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
													{$conf['db_name']}filial_funcionario
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
				$this->err = "Este registro não pode ser excluído, pois existem registros relacionados a ele.";
			}

		}




		/**
		  método: GravaFuncionario
		  propósito: GravaFuncionario
		  
		  
		*/

		function GravaFuncionario ($post, $idfilial, $DeletaFilial = true){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// deleta os funcionarios da filial
			if($DeletaFilial) $this->DeletaFuncionario($idfilial);

			$info['idfilial'] = $idfilial;

			// percorre os funcionarios
			for ($i=1; $i<=intval($post['total_funcionarios']); $i++) {
				if (isset($post["codigo_funcionario_$i"])) {
					$info['idfuncionario'] = $post["codigo_funcionario_$i"];
					$this->set($info);
				}

			}

			return(1);

		}


		
		/**
		  método: DeletaFuncionario
		  propósito: DeletaFuncionario
		*/

		function DeletaFuncionario ($idfilial){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// deleta todos os funcionarios de uma determinada filial
			$delete_sql = "	DELETE FROM
												{$conf['db_name']}filial_funcionario
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
		  método: GravaFilial
		  propósito: GravaFilial
		*/

		function GravaFilial ($post, $idfuncionario){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// deleta as filiais do funcionario
			$this->DeletaFilial($idfuncionario);

			$info['idfuncionario'] = $idfuncionario;

			// seleciona as filiais
			$list_filial = $this->SelecionaFilial($post);

			// percorre as filiais
			for ($i=0; $i<count($list_filial); $i++) {
				
				$atributo =	'filial_' . $list_filial[$i]['idfilial'];
				if ($post[$atributo]) {
					$info['idfilial'] = $list_filial[$i]['idfilial'];
					$this->set($info);
				}
				
			}	

			return(1);

		}



		/**
		  método: DeletaFilial
		  propósito: DeletaFilial
		*/

		function DeletaFilial ($idfuncionario){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// deleta todas as filiais de um determinado funcionario
			$delete_sql = "	DELETE FROM
												{$conf['db_name']}filial_funcionario
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



		
		/**
		  método: SelecionaFilial
		  propósito: SelecionaFilial
		*/

		function SelecionaFilial ($post, $idfuncionario = ""){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT
											FLI.*
										FROM
           						{$conf['db_name']}filial FLI
           						INNER JOIN {$conf['db_name']}filial_funcionario FLFNC ON FLFNC.idfilial=FLI.idfilial
										WHERE
										  FLFNC.idfuncionario = $idfuncionario
										ORDER BY FLI.nome_filial ASC
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
						$info_registro = $this->getById($list['idfilial'],$idfuncionario);
						if ($info_registro['idfilial'] != "") $list['selecionado'] = 1;

					}	
					else {
						$atributo =	'filial_' . $list['idfilial'];
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
		function getById($idfilial,$idfuncionario){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											FLFNC.*
										FROM
											{$conf['db_name']}filial_funcionario FLFNC
										WHERE
											 FLFNC.idfilial = $idfilial AND  FLFNC.idfuncionario = $idfuncionario ";

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
			
			if ($ordem == "") $ordem = " ORDER BY FLFNC.idfilial ASC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											FLFNC.*   , FLI.nome_filial , FNC.nome_funcionario
										FROM
           						{$conf['db_name']}filial_funcionario FLFNC 
												 INNER JOIN {$conf['db_name']}filial FLI ON FLFNC.idfilial=FLI.idfilial 
												 INNER JOIN {$conf['db_name']}funcionario FNC ON FLFNC.idfuncionario=FNC.idfuncionario 
												
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
		                  {$conf['db_name']}filial_funcionario
		                    (
		                    
												idfilial, 
												idfuncionario  
												
												)
		                VALUES
		                    (
		                    
		                    " . $info['idfilial'] . ",  
												" . $info['idfuncionario'] . "   
												
												)";
			
			//executa a query e testa se a consulta foi "boa"
			if($db->query($set_sql)){
				//retorna o código inserido
				$codigo = $db->insert_id();
				
				return((string)$codigo);
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
		function update($idfilial,$idfuncionario, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}filial_funcionario
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
			$update_sql .= " WHERE  idfilial = $idfilial AND  idfuncionario = $idfuncionario ";
			
			
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
		function delete($idfilial,$idfuncionario){
			
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
													{$conf['db_name']}filial_funcionario
												WHERE
													 idfilial = $idfilial AND  idfuncionario = $idfuncionario ";
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
					 						*
										FROM
											{$conf['db_name']}filial_funcionario
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
