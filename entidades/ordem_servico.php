<?php
	
	class ordem_servico {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function ordem_servico(){
			// não faz nada
		}


		


		/**
		  método: getById
		  propósito: busca informações
		*/
		function getById($idordem_servico){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											ORS.*, CLI.nome_cliente as idcliente_Nome, SLC.nome_cliente as idsolicitante_Nome,
											TIPSER.nome_servico
										FROM
											{$conf['db_name']}ordem_servico ORS
											INNER JOIN {$conf['db_name']}cliente CLI ON ORS.idcliente = CLI.idcliente 
								 			INNER JOIN {$conf['db_name']}cliente SLC ON SLC.idcliente = ORS.idsolicitante
								 			JOIN  {$conf['db_name']}tipo_servico TIPSER USING(idtipo_servico)
										WHERE
											 ORS.idordem_servico = $idordem_servico ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				if ($get['previsao_servico'] != '0000-00-00') $get['previsao_servico'] = $form->FormataDataParaExibir($get['previsao_servico']); 
				else $get['previsao_servico'] = "";
				

				$get['num_ordem_servico'] = str_pad($get['idordem_servico'],7,'0',STR_PAD_LEFT); 
				
				
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
		
		function make_list( $pg, $rppg, $filtro = "", $ordem = "", $url = "",$idstatus = null){
			
			if ($ordem == "") $ordem = " ORDER BY ORS.previsao_servico ASC";
			
			// variáveis globais
			global $form, $conf, $db, $falha, $transicao_status;
			//---------------------
			
			$list_sql = "	SELECT DISTINCT
								ORS.*   , CLI.nome_cliente , SLC.nome_cliente as nome_solicitante , TSR.nome_servico
							FROM
							{$conf['db_name']}ordem_servico ORS 
								 INNER JOIN {$conf['db_name']}transicao_status TRA ON ORS.idordem_servico = TRA.idordem_servico
								 INNER JOIN {$conf['db_name']}cliente CLI ON ORS.idcliente = CLI.idcliente 
								 INNER JOIN {$conf['db_name']}cliente SLC ON SLC.idcliente = ORS.idsolicitante 
								 INNER JOIN {$conf['db_name']}tipo_servico TSR ON ORS.idtipo_servico=TSR.idtipo_servico 
												
							$filtro
							$ordem";

			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

			if($list_q){
				
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){
					
					$status = $transicao_status->getLastStatus($list['idordem_servico']);
					if(!is_null($idstatus) && $status['idstatus'] != $idstatus) continue;
					
					//insere um índice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);
					
					$list['num_ordem_servico'] =  str_pad($list['idordem_servico'],7,'0',STR_PAD_LEFT);
					
					if ($list['previsao_servico'] != '0000-00-00') $list['previsao_servico'] = $form->FormataDataParaExibir($list['previsao_servico']); 
					else $list['previsao_servico'] = "";
					
					
					
					//Monta a string com o status atual
					$list['ultimo_status'] = $status['nome_status_os'];
					if(!empty($status['nome_programacao'])){
						
						$list['ultimo_status'] .= ' - '. $status['nome_programacao'];
						
						if($status['campo_complementar'] == 'data_programacao'){
							$list['ultimo_status'] .= $status['data_programacao'];
						}
					}//---------------------------------------------------------

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
		                  {$conf['db_name']}ordem_servico
		                    (
		                    
												descricao_ordem, 
												previsao_servico, 
												idcliente, 
												idsolicitante, 
												idtipo_servico, 
												observacao_ordem,
												endereco_cliente,
												endereco_fornecedor,
												idfornecedor
												
												)
		                VALUES
		                    (
		                    
		                    '" . $info['descricao_ordem'] . "',  
												'" . $info['previsao_servico'] . "',  
												" . $info['idcliente'] . ",  
												" . $info['idsolicitante'] . ",  
												" . $info['idtipo_servico'] . ",  
												'" . $info['observacao_ordem'] . "',
												'" . $info['endereco_cliente'] . "',
												'" . $info['endereco_fornecedor'] . "',
												'" . $info['idfornecedor'] . "'  
												
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
		function update($idordem_servico, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
								{$conf['db_name']}ordem_servico
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
			$update_sql .= " WHERE  idordem_servico = $idordem_servico ";

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
		function delete($idordem_servico){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			// Apaga o conjunto de dependências geradas
			if ($db->query("DELETE  FROM {$conf['db_name']}transicao_status WHERE idordem_servico = $idordem_servico") &&
				$db->query("DELETE FROM  {$conf['db_name']}material_ordem_servico WHERE idordem_servico = $idordem_servico ") &&
				$db->query("DELETE FROM {$conf['db_name']}ordem_servico WHERE idordem_servico = $idordem_servico ")
			) {	
				return(1);
			}
			else {
				$this->err = $falha['excluir'];
				return(0);
			}	

		}	

		
		/**
		  método: make_list
		  propósito: faz a listagem para colocar no select
		*/

		function make_list_select( $filtro = "", $ordem = " ORDER BY previsao_servico DESC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}ordem_servico
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
