<?php
	
	class transicao_status {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function transicao_status(){
			// não faz nada
		}


		


		/**
		  método: getById
		  propósito: busca informações
		*/
		function getById($idtransicao_status){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											TST.*
										FROM
											{$conf['db_name']}transicao_status TST
										WHERE
											 TST.idtransicao_status = $idtransicao_status ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				if ($get['data_programacao'] != '0000-00-00') $get['data_programacao'] = $form->FormataDataParaExibir($get['data_programacao']); 
				else $get['data_programacao'] = "";
					
				if ($get['data_hora_transicao'] != '0000-00-00 00:00:00') { 
					$array = split(" ",$get['data_hora_transicao']); 
					$get['data_hora_transicao_D'] = $form->FormataDataParaExibir($array[0]); 
					$get['data_hora_transicao_H'] = $array[1]; 
				} 
				
				
				
				//retorna o vetor associativo com os dados
				return $get;
			}
			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}
				
		}
		

		function getLastStatus($idordem_servico){

			// variáveis globais
			global $form, $conf, $db, $falha;

			$sql = "SELECT
						TS . * , SOP.idstatus_os, PS.campo_complementar, PS.nome_programacao, PS.campo_complementar, SO.nome_status_os
					FROM
						{$conf['db_name']}transicao_status TS
						LEFT JOIN status_os_programacao SOP ON (SOP.idstatus_os_programacao = TS.idstatus)
						LEFT JOIN programacao_status PS ON(SOP.idprogramacao_status = PS.idprogramacao_status)
						LEFT JOIN status_os SO ON ( SOP.idstatus_os = SO.idstatus_os )
					WHERE
						TS.idordem_servico = $idordem_servico
		
					ORDER BY TS.data_hora_transicao
					DESC LIMIT 1";

			$sql_q = $db->query($sql);

			if($sql_q){
				$status =  $db->fetch_array($sql_q) ;
				$status['data_programacao'] = $form->FormataDataParaExibir($status['data_programacao']);
				return $status;
			}
			else{
				$this->err = $falha['listar'];
				return(0);
			}

		}

		
		
		function getListStatus($idordem_servico){
			
				// variáveis globais
				global $form, $conf, $db, $falha;
				
				$sql = "SELECT TS.*, PS.nome_programacao, SOS.nome_status_os, PS.campo_complementar, FNC.nome_funcionario 
						FROM
							{$conf['db_name']}transicao_status TS
							LEFT JOIN status_os_programacao SOP ON (SOP.idstatus_os_programacao = TS.idstatus)
							LEFT JOIN status_os SOS ON(SOS.idstatus_os = SOP.idstatus_os)
							LEFT JOIN programacao_status PS ON(SOP.idprogramacao_status = PS.idprogramacao_status)
							JOIN funcionario FNC USING(idfuncionario)
							
						WHERE
							TS.idordem_servico = $idordem_servico
							 
						ORDER BY TS.data_hora_transicao DESC ";
	
				$sql_q = $db->query($sql);

				if($sql_q){		
					
					while($list = $db->fetch_array($sql_q)){
						$list['index']++;
						$list['data_hora_transicao'] = $form->FormataDataHoraParaExibir($list['data_hora_transicao']);
						
					
						if($list['campo_complementar'] == 'data_programacao'){ 

							$list['complementar'] = ($list['data_programacao'] != '0000-00-00') ? $form->FormataDataParaExibir($list['data_programacao']) : '';
						}
						else{
							$list['complementar'] = $list['motivo_programacao'];
						}
					
						$list_status[] = $list; 
					}
					
					return $list_status;
				}
				else{
					$this->err = $falha['listar'];
					return(0);
				}
				
		}
		
		/**
		  método: make_list
		  propósito: faz a listagem
		*/
		
		function make_list( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
			
			if ($ordem == "") $ordem = " ORDER BY TST.data_programacao DESC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
                                                TST.*   , FNC.nome_funcionario , SOP.idstatus_os_programacao , ORS.descricao_ordem
                                        FROM
                                            {$conf['db_name']}transicao_status TST 
                                            INNER JOIN {$conf['db_name']}funcionario FNC ON TST.idfuncionario=FNC.idfuncionario 
                                            LEFT  JOIN {$conf['db_name']}status_os_programacao SOP ON TST.idstatus=SOP.idstatus_os_programacao 
                                            LEFT  JOIN {$conf['db_name']}ordem_servico ORS ON TST.idordem_servico=ORS.idordem_servico 

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
					
					if ($list['data_programacao'] != '0000-00-00') $list['data_programacao'] = $form->FormataDataParaExibir($list['data_programacao']); 
					else $list['data_programacao'] = "";
					
					if ($list['data_hora_transicao'] != '0000-00-00 00:00:00') { 
						$array = split(" ",$list['data_hora_transicao']); 
						$list['data_hora_transicao'] = $form->FormataDataParaExibir($array[0]) . " " . $array[1]; 
					} 
					else $list['data_hora_transicao'] = "";
				
					
					
					
					
          $list_return[] = $list;
          
          $cont++;
				}
				
				return $list_return;
					
			}	
			else{
				$this->err = $falha['listar'] . $db->error();
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
		                  {$conf['db_name']}transicao_status
		                    (
		                    
												idfuncionario, 
												idstatus, 
												idordem_servico, 
												data_hora_transicao, 
												observacao_transicao, 
												data_programacao, 
												motivo_programacao  
												
												)
		                VALUES
		                    (
		                    
		                    " . $info['idfuncionario'] . ",  
												" . $info['idstatus'] . ",  
												" . $info['idordem_servico'] . ",  
												'" . $info['data_hora_transicao'] . "',  
												'" . $info['observacao_transicao'] . "',  
												'" . $info['data_programacao'] . "',  
												'" . $info['motivo_programacao'] . "'   
												
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
		function update($idtransicao_status, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}transicao_status
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
			$update_sql .= " WHERE  idtransicao_status = $idtransicao_status ";
			
			
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
		function delete($idtransicao_status){
			
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
													{$conf['db_name']}transicao_status
												WHERE
													 idtransicao_status = $idtransicao_status ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY data_programacao DESC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}transicao_status
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
