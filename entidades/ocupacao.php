<?php
	
	class ocupacao {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function ocupacao(){
			// n�o faz nada
		}


		


		/**
		  m�todo: getById
		  prop�sito: busca informa��es
		*/
		function getById($idapartamento,$idcliente){

			// vari�veis globais
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
		  m�todo: make_list
		  prop�sito: faz a listagem
		*/
		
		function make_list( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
			
			
			if ($ordem == "") $ordem = " ORDER BY OCUP.dataInicial DESC";
			
			// vari�veis globais
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

			//manda fazer a pagina��o
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

			if($list_q){
				
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){
					
					//insere um �ndice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);
					
					if ($list['dataInicial'] != '0000-00-00') $list['dataInicial'] = $form->FormataDataParaExibir($list['dataInicial']); 
					else $list['dataInicial'] = "";
					if ($list['dataFinal'] != '0000-00-00') $list['dataFinal'] = $form->FormataDataParaExibir($list['dataFinal']); 
					else $list['dataFinal'] = "";
					
					
					
					if ($list['tipo'] == 'P') $list['tipo'] = "Propriet�rio"; 
					else if ($list['tipo'] == 'I') $list['tipo'] = "Inquilino"; 
					if ($list['ativa'] == 'S') $list['ativa'] = "<span class='req'>Ocupa��o Ativa</span>";
					else if ($list['ativa'] == 'N') $list['ativa'] = "N�o Ativa";
					if ($list['sindico'] == 'S') $list['sindico'] = "Sim"; 
					else if ($list['sindico'] == 'N') $list['sindico'] = "N�o"; 
					
						
					
					
					
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
				
		    	if($info['ativa'] == 'S'){ //Se ativar a ocupa��o, seta todas as outras ocupa�oes pra 'N'
		    
			        $desativa_sql = "UPDATE ocupacao
									 SET ativa = 'N'
									 WHERE idapartamento = ".$info['idapartamento']."
									 AND idcliente <> " . $info['idcliente'];

					if(!$db->query($desativa_sql)){
						$this->err = $falha['inserir'];
						return(0);
					}
				}
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
		function update($idapartamento,$idcliente, $info){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			/*
			if($info['ativa'] = 'S'){ //Se ativar a ocupa��o, seta todas as outras ocupa�oes pra 'N'
				$db->query("UPDATE ocupacao SET ativa = 'N' WHERE idocupacao <> $codigo");
			}
			*/
			//inicializa a query
			$update_sql = "	UPDATE
								{$conf['db_name']}ocupacao
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
			$update_sql .= " WHERE  idapartamento = $idapartamento AND  idcliente = $idcliente ";
			
			//envia a query para o banco
			$update_q = $db->query($update_sql);
			
			if($update_q){

				if($info['litativa'] == 'S'){ //Se ativar a ocupa��o, seta todas as outras ocupa�oes pra 'N'

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
		  m�todo: delete
		  prop�sito: excluir registro
		*/
		function delete($idapartamento,$idcliente){
			
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
				$this->err = "Este registro n�o pode ser exclu�do, pois existem registros relacionadas a ele.";
			}	

		}	

		
		/**
		  m�todo: make_list
		  prop�sito: faz a listagem para colocar no select
		*/

		function make_list_select( $filtro = "", $ordem = " ORDER BY idapartamento ASC") {
			
			// vari�veis globais
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
		  m�todo: Verifica_Ocupacao_Duplicada
		  prop�sito: Verificar se existe apartamento duplicado
		*/
		Function Verifica_Ocupacao_Duplicada($idcliente, $idapartamento, $editar=''){

			// vari�veis globais
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
		// vari�veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $apartamento;
		//---------------------
			
		//Faz a consulta buscando pelo numero de ocupa��es ativas
		$sql = "SELECT * FROM {$conf['db_name']}ocupacao OCUP
				WHERE ativa = 'S'";
		$sql_q = $db->query($sql);

		//testa a consulta
		if($sql_q){
			
			//Nenhuma ocupa��o ativa, logo o apto est� vazio
			if ( $db->num_rows($sql_q) == 0 ){
			    $situacao = 'V';
			}
			else{

    			if($_GET['ac'] != 'excluir'){
					//Verifica se a ocupa��o est� sendo ativa por Inquilino ou Propriet�rio
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
