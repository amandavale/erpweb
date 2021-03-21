<?php
	
	class modo_recebimento {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function modo_recebimento(){
			// não faz nada
		}


		


		/**
		  método: getById
		  propósito: busca informações
		*/
		function getById($sigla_modo_recebimento){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											MREC.*
										FROM
											{$conf['db_name']}modo_recebimento MREC
										WHERE
											 MREC.sigla_modo_recebimento = '$sigla_modo_recebimento' ";

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
			
			if ($ordem == "") $ordem = " ORDER BY MREC.sigla_modo_recebimento ASC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											MREC.*  
										FROM
           						{$conf['db_name']}modo_recebimento MREC 
												
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
					
					
					
					
					if ($list['a_vista'] == '0') $list['a_vista'] = "Não"; 
					else if ($list['a_vista'] == '1') $list['a_vista'] = "Sim"; 
					if ($list['a_prazo'] == '0') $list['a_prazo'] = "Não"; 
					else if ($list['a_prazo'] == '1') $list['a_prazo'] = "Sim"; 
					if ($list['baixa_automatica'] == '0') $list['baixa_automatica'] = "Não"; 
					else if ($list['baixa_automatica'] == '1') $list['baixa_automatica'] = "Sim"; 
					
					
					
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
		                  {$conf['db_name']}modo_recebimento
		                    (
		                    
												sigla_modo_recebimento, 
												descricao, 
												a_vista, 
												a_prazo, 
												baixa_automatica, 
												dias_baixa_automatica_vista,  
												dias_baixa_automatica_prazo
												
												)
		                VALUES
		                    (
		                    
		                    '" . $info['sigla_modo_recebimento'] . "',  
												'" . $info['descricao'] . "',  
												'" . $info['a_vista'] . "',  
												'" . $info['a_prazo'] . "',  
												'" . $info['baixa_automatica'] . "',  
												" . $info['dias_baixa_automatica_vista'] . ",
												" . $info['dias_baixa_automatica_prazo'] . "   
												
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
		function update($sigla_modo_recebimento, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}modo_recebimento
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
			$update_sql .= " WHERE  sigla_modo_recebimento = '$sigla_modo_recebimento' ";
			
			
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
		function delete($sigla_modo_recebimento){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			// conjunto de dependências geradas
			$sql = "SELECT 
								 * 
							FROM 
								{$conf['db_name']}conta_receber
							WHERE 
								 sigla_modo_recebimento = '$sigla_modo_recebimento' ";
			$verifica_q = $db->query($sql);
			$n0 = $db->num_rows($verifica_q);
			
			
			//---------------------
			

			// verifica se pode excluir
			if (1 && $n0==0) {

				

				$delete_sql = "	DELETE FROM
													{$conf['db_name']}modo_recebimento
												WHERE
													 sigla_modo_recebimento = '$sigla_modo_recebimento' ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY sigla_modo_recebimento ASC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}modo_recebimento
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
