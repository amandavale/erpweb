<?php
	
	class ocupacao {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function ocupacao(){
			// não faz nada
		}


		


		/**
		  método: getById
		  propósito: busca informações
		*/
		function getById($idapartamento,$idcliente){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
								OCUP.*
							FROM
								{$conf['db_name']}ocupacao OCUP
							WHERE
							OCUP.idapartamento = $idapartamento AND  OCUP.idcliente = $idcliente ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				if ($get['dataInicial'] != '0000-00-00') $get['dataInicial'] = $form->FormataDataParaExibir($get['dataInicial']); 
				else $get['dataInicial'] = "";

				if ($get['dataFinal'] != '0000-00-00') $get['dataFinal'] = $form->FormataDataParaExibir($get['dataFinal']); 
				else $get['dataFinal'] = "";
					
				
				
				
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
			
			
			if ($ordem == "") $ordem = " ORDER BY OCUP.dataInicial DESC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
								OCUP.*   , APTO.apto , CLI.nome_cliente
							FROM
           						{$conf['db_name']}ocupacao OCUP

							INNER JOIN {$conf['db_name']}apartamento APTO ON OCUP.idapartamento=APTO.idapartamento
							INNER JOIN {$conf['db_name']}cliente CLI ON OCUP.idcliente=CLI.idcliente

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
					
					if ($list['dataInicial'] != '0000-00-00') $list['dataInicial'] = $form->FormataDataParaExibir($list['dataInicial']); 
					else $list['dataInicial'] = "";
					if ($list['dataFinal'] != '0000-00-00') $list['dataFinal'] = $form->FormataDataParaExibir($list['dataFinal']); 
					else $list['dataFinal'] = "";
					
					
					
					if ($list['tipo'] == 'P') $list['tipo'] = "Proprietário"; 
					else if ($list['tipo'] == 'I') $list['tipo'] = "Inquilino"; 
					if ($list['ativa'] == 'S') $list['ativa'] = "<span class='req'>Ocupação Ativa</span>";
					else if ($list['ativa'] == 'N') $list['ativa'] = "Não Ativa";
					if ($list['sindico'] == 'S') $list['sindico'] = "Sim"; 
					else if ($list['sindico'] == 'N') $list['sindico'] = "Não"; 
					
						
					
					
					
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
		                  {$conf['db_name']}ocupacao
		                    (
		                    
							idapartamento, 
							idcliente, 
							tipo, 
							dataInicial, 
							dataFinal, 
							ativa, 
							sindico, 
							observacao  
							
							)
		                VALUES
		                    (
		                    
		                    " . $info['idapartamento'] . ",  
							" . $info['idcliente'] . ",  
							'" . $info['tipo'] . "',  
							'" . $info['dataInicial'] . "',  
							'" . $info['dataFinal'] . "',  
							'" . $info['ativa'] . "',  
							'" . $info['sindico'] . "',  
							'" . $info['observacao'] . "'
							
							)";
			
	
			//executa a query e testa se a consulta foi "boa"
			if($db->query($set_sql)){
				
		    	if($info['ativa'] == 'S'){ //Se ativar a ocupação, seta todas as outras ocupaçoes pra 'N'
		    
			        $desativa_sql = "UPDATE ocupacao
									 SET ativa = 'N'
									 WHERE idapartamento = ".$info['idapartamento']."
									 AND idcliente <> " . $info['idcliente'];

					if(!$db->query($desativa_sql)){
						$this->err = $falha['inserir'];
						return(0);
					}
				}
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
		function update($idapartamento,$idcliente, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			/*
			if($info['ativa'] = 'S'){ //Se ativar a ocupação, seta todas as outras ocupaçoes pra 'N'
				$db->query("UPDATE ocupacao SET ativa = 'N' WHERE idocupacao <> $codigo");
			}
			*/
			//inicializa a query
			$update_sql = "	UPDATE
								{$conf['db_name']}ocupacao
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
			$update_sql .= " WHERE  idapartamento = $idapartamento AND  idcliente = $idcliente ";
			
			//envia a query para o banco
			$update_q = $db->query($update_sql);
			
			if($update_q){

				if($info['litativa'] == 'S'){ //Se ativar a ocupação, seta todas as outras ocupaçoes pra 'N'

			        $desativa_sql = "UPDATE ocupacao
									 SET ativa = 'N'
									 WHERE (idapartamento = $idapartamento
									 AND idcliente <> $idcliente)";
					
					if(!$db->query($desativa_sql)){
						$this->err = $falha['inserir'];
						return(0);
					}
					
					
				}

			  	return(1);
			  
			  
			}
			else{
			  $this->err = $falha['alterar'];
			}
			
		}	
		
		/**
		  método: delete
		  propósito: excluir registro
		*/
		function delete($idapartamento,$idcliente){
			
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
									{$conf['db_name']}ocupacao
								WHERE
									 idapartamento = $idapartamento AND  idcliente = $idcliente ";

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

		function make_list_select( $filtro = "", $ordem = " ORDER BY idapartamento ASC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}ocupacao
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
		  método: Verifica_Ocupacao_Duplicada
		  propósito: Verificar se existe apartamento duplicado
		*/
		Function Verifica_Ocupacao_Duplicada($idcliente, $idapartamento, $editar=''){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

		
			
			$get_sql = "	SELECT
								OCUP.idapartamento
							FROM
								{$conf['db_name']}ocupacao OCUP
							WHERE
								 OCUP.idapartamento =$idapartamento
								 AND OCUP.idcliente=$idcliente 
						";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida
   
				if ($db->num_rows($get_q) == 0)	return (false);
				else return (true);

			}
			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}

		}
		
		
		
		
	function altera_Situacao_Apto($idapartamento, $idcliente){
		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $apartamento;
		//---------------------
			
		//Faz a consulta buscando pelo numero de ocupações ativas
		$sql = "SELECT * FROM {$conf['db_name']}ocupacao OCUP
				WHERE ativa = 'S'";
		$sql_q = $db->query($sql);

		//testa a consulta
		if($sql_q){
			
			//Nenhuma ocupação ativa, logo o apto está vazio
			if ( $db->num_rows($sql_q) == 0 ){
			    $situacao = 'V';
			}
			else{

    			if($_GET['ac'] != 'excluir'){
					//Verifica se a ocupação está sendo ativa por Inquilino ou Proprietário
					$dados_ocupacao = $this->getById($idapartamento, $idcliente);
	    			$situacao = $dados_ocupacao['tipo'];
	    		}
			}
			
			

			if ($situacao){
				
				//Atualiza na tabela de apartamentos
				$set_sql = "UPDATE
					{$conf['db_name']}apartamento
				SET situacao = '$situacao'
				WHERE idapartamento = $idapartamento";

				if($db->query($set_sql)){
					return true;
				}
				else{
					$this->error = $falha['atualizar'];
					return false;
				}
			}
			else{
				return true;
			}	

		}
		else{
			$this->error = 'Falha ao verificar o atual morador do apartamento. Por favor, entre em contato com os autores';
			return false;
		}

	}
	
	
	
		

	} // fim da classe
?>
