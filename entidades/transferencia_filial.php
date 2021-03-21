<?php
	
	class transferencia_filial {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function transferencia_filial(){
			// n�o faz nada
		}


		
		
		/**
		  m�todo: Busca_Parametrizada
		  prop�sito: Busca_Parametrizada
		*/

		function Busca_Parametrizada ( $pg, $rppg, $filtro_where = "", $ordem = "", $url = ""){

			if ($ordem == "") $ordem = " ORDER BY TRFL.data_transferencia DESC";

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			
			if ($filtro_where != "") $filtro_where = " WHERE ( " . $filtro_where . " ) ";


			$list_sql = "		SELECT
											TRFL.*   , FNC.nome_funcionario ,FLIR.nome_filial, FLIR.idfilial ,FLIR.idfilial as idfilial_destinataria ,FLID.nome_filial as filial_destinataria
										FROM
           						{$conf['db_name']}transferencia_filial TRFL 
												 INNER JOIN {$conf['db_name']}funcionario FNC ON TRFL.idfuncionario=FNC.idfuncionario 
												 INNER JOIN {$conf['db_name']}filial FLIR ON TRFL.idfilial_remetente=FLIR.idfilial
												 INNER JOIN {$conf['db_name']}filial FLID ON TRFL.idfilial_destinataria=FLID.idfilial

           					$filtro_where

										$ordem ";
			
			
			
			//manda fazer a pagina��o
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);
			
			
			
			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					//insere um �ndice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);

					if ($list['data_transferencia'] != '0000-00-00') $list['data_transferencia'] = $form->FormataDataHoraParaExibir($list['data_transferencia']);
					else $list['data_transferencia'] = "";

					if ($list['qtd_transferida'] != "") $list['qtd_transferida'] = number_format($list['qtd_transferida'],2,",","");

					if ($list['preco_total'] != "") $list['preco_total'] = number_format($list['preco_total'],2,",","");


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
		  m�todo: getById
		  prop�sito: busca informa��es
		*/
		function getById($idtransferencia_filial){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											TRFL.*   , FNC.nome_funcionario , PRD.descricao_produto ,PRD.codigo_produto, FLIR.nome_filial ,FLID.nome_filial as nome_filial_destinataria, TRFLPRD.qtd_transferida
										FROM
           						{$conf['db_name']}transferencia_filial TRFL 
												 INNER JOIN {$conf['db_name']}funcionario FNC ON TRFL.idfuncionario=FNC.idfuncionario 
												 INNER JOIN {$conf['db_name']}transferencia_filial_produto TRFLPRD ON TRFL.idtransferencia_filial=TRFLPRD.idtransferencia_filial
												 INNER JOIN {$conf['db_name']}produto PRD ON TRFLPRD.idproduto=PRD.idproduto 
												 INNER JOIN {$conf['db_name']}filial FLIR ON TRFL.idfilial_remetente=FLIR.idfilial
												 INNER JOIN {$conf['db_name']}filial FLID ON TRFL.idfilial_destinataria=FLID.idfilial
										WHERE
											 TRFL.idtransferencia_filial = $idtransferencia_filial ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				
				
				if ($get['qtd_transferida'] != "") $get['qtd_transferida'] = number_format($get['qtd_transferida'],2,",",""); 
					
				
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
			
			if ($ordem == "") $ordem = " ORDER BY TRFL.data_transferencia DESC";
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											TRFL.*   , FNC.nome_funcionario , PRD.descricao_produto ,PRD.codigo_produto, FLIR.nome_filial ,FLID.nome_filial as filial_destinataria, TRFLPRD.qtd_transferida
										FROM
           						{$conf['db_name']}transferencia_filial TRFL 
												 INNER JOIN {$conf['db_name']}funcionario FNC ON TRFL.idfuncionario=FNC.idfuncionario 
												 INNER JOIN {$conf['db_name']}transferencia_filial_produto TRFLPRD ON TRFL.idtransferencia_filial=TRFLPRD.idtransferencia_filial
												 INNER JOIN {$conf['db_name']}produto PRD ON TRFLPRD.idproduto=PRD.idproduto 
												 INNER JOIN {$conf['db_name']}filial FLIR ON TRFL.idfilial_remetente=FLIR.idfilial
												 INNER JOIN {$conf['db_name']}filial FLID ON TRFL.idfilial_destinataria=FLID.idfilial
												
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
					
					
					
					if ($list['qtd_transferida'] != "") $list['qtd_transferida'] = number_format($list['qtd_transferida'],2,",",""); 
					
					
					
					
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
		                  {$conf['db_name']}transferencia_filial
		                    (
		                    
												idfuncionario, 
												idfilial_remetente, 
												idfilial_destinataria, 
												data_transferencia,
												tipo_preco,
												desconto,
												preco_total,
												observacoes  
												
												)
		                VALUES
		                    (
		                    
		                    " . $info['idfuncionario'] . ",  
												" . $info['idfilial_remetente'] . ",  
												" . $info['idfilial_destinataria'] . ",  
												'" . $info['data_transferencia'] . "',  
												'" . $info['tipoPreco'] . "',  
												" . $info['desconto'] . " ,  
												" . $info['preco_total'] . ",
												'" . $info['observacoes'] . "'  
												
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
		function update($idtransferencia_filial, $info){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}transferencia_filial
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
			$update_sql .= " WHERE  idtransferencia_filial = $idtransferencia_filial ";
			
			
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
		function delete($idtransferencia_filial){
			
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
													{$conf['db_name']}transferencia_filial
												WHERE
													 idtransferencia_filial = $idtransferencia_filial ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY idfuncionario ASC") {
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}transferencia_filial
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
