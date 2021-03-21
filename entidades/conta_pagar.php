<?php
	
	class conta_pagar {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function conta_pagar(){
			// não faz nada
		}


		/**
		  método: BuscaSaidasDoDia
		  propósito: busca todas as saídas do dia, para colocar no caixa diário
		*/
		
		function BuscaSaidasDoDia($dia, $idfilial){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											CTA_PAG.*  
										FROM
           						{$conf['db_name']}conta_pagar CTA_PAG 
												 
										WHERE
												CTA_PAG.idfilial = $idfilial
													AND
												CTA_PAG.data_pagamento = '$dia'
													AND
												CTA_PAG.saiu_do_caixa = '1'	
	
										ORDER BY 
												CTA_PAG.data_vencimento DESC

									";

			//manda fazer a paginação
			$list_q = $db->query($list_sql);

			if($list_q){
				
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){
					
					//insere um índice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);
					
					if ($list['data_vencimento'] != '0000-00-00') $list['data_vencimento'] = $form->FormataDataParaExibir($list['data_vencimento']); 
					else $list['data_vencimento'] = "";
					if ($list['data_pagamento'] != '0000-00-00') $list['data_pagamento'] = $form->FormataDataParaExibir($list['data_pagamento']); 
					else $list['data_pagamento'] = "";
					
					
					if ($list['valor_conta'] != "") $list['valor_conta'] = number_format($list['valor_conta'],2,",",""); 
					if ($list['valor_pago'] != "") $list['valor_pago'] = number_format($list['valor_pago'],2,",",""); 
					if ($list['juros_conta'] != "") $list['juros_conta'] = number_format($list['juros_conta'],2,",",""); 
					if ($list['multa_conta'] != "") $list['multa_conta'] = number_format($list['multa_conta'],2,",",""); 
					if ($list['valor_saiu_caixa'] != "") $list['valor_saiu_caixa'] = number_format($list['valor_saiu_caixa'],2,",",""); 
					
					if ($list['status_conta'] == 'P') $list['status_conta'] = "Pago"; 
					else if ($list['status_conta'] == 'N') $list['status_conta'] = "Não pago"; 
					if ($list['saiu_do_caixa'] == '0') $list['saiu_do_caixa'] = "Não"; 
					else if ($list['saiu_do_caixa'] == '1') $list['saiu_do_caixa'] = "Sim"; 
					
					
					
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
		  método: delete_conta_pedido
		  propósito: excluir registro
		*/
		function delete_conta_pedido($idpedido){
			
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
													{$conf['db_name']}conta_pagar
												WHERE
													 idpedido = $idpedido ";
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
		  método: getById
		  propósito: busca informações
		*/
		function getById($idconta_pagar){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											CTA_PAG.*
										FROM
											{$conf['db_name']}conta_pagar CTA_PAG
										WHERE
											 CTA_PAG.idconta_pagar = $idconta_pagar ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				if ($get['data_vencimento'] != '0000-00-00') $get['data_vencimento'] = $form->FormataDataParaExibir($get['data_vencimento']); 
				else $get['data_vencimento'] = "";
					if ($get['data_pagamento'] != '0000-00-00') $get['data_pagamento'] = $form->FormataDataParaExibir($get['data_pagamento']); 
				else $get['data_pagamento'] = "";
					
				
				if ($get['valor_conta'] != "") $get['valor_conta'] = number_format($get['valor_conta'],2,",",""); 
					if ($get['valor_pago'] != "") $get['valor_pago'] = number_format($get['valor_pago'],2,",",""); 
					if ($get['juros_conta'] != "") $get['juros_conta'] = number_format($get['juros_conta'],2,",",""); 
					if ($get['multa_conta'] != "") $get['multa_conta'] = number_format($get['multa_conta'],2,",",""); 
					if ($get['valor_saiu_caixa'] != "") $get['valor_saiu_caixa'] = number_format($get['valor_saiu_caixa'],2,",",""); 
					

				if ($get['data_cadastro'] != '0000-00-00 00:00:00') { 
					$array = split(" ",$get['data_cadastro']); 
					$get['datahoraCriacao_D'] = $form->FormataDataParaExibir($array[0]); 
					$get['datahoraCriacao_H'] = $array[1]; 
				} 
				if ($get['data_ult_alteracao'] != '0000-00-00 00:00:00') { 
					$array = split(" ",$get['data_ult_alteracao']); 
					$get['datahoraUltAlteracao_D'] = $form->FormataDataParaExibir($array[0]); 
					$get['datahoraUltAlteracao_H'] = $array[1]; 
				} 

				
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
			
			if ($ordem == "") $ordem = " ORDER BY CTA_PAG.data_vencimento ASC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											CTA_PAG.* 
										FROM
           						{$conf['db_name']}conta_pagar CTA_PAG 
												 
												
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
					
					if ($list['data_vencimento'] != '0000-00-00') $list['data_vencimento'] = $form->FormataDataParaExibir($list['data_vencimento']); 
					else $list['data_vencimento'] = "";
					if ($list['data_pagamento'] != '0000-00-00') $list['data_pagamento'] = $form->FormataDataParaExibir($list['data_pagamento']); 
					else $list['data_pagamento'] = "";
					
					
					if ($list['valor_conta'] != "") $list['valor_conta'] = number_format($list['valor_conta'],2,",",""); 
					if ($list['valor_pago'] != "") $list['valor_pago'] = number_format($list['valor_pago'],2,",",""); 
					if ($list['juros_conta'] != "") $list['juros_conta'] = number_format($list['juros_conta'],2,",",""); 
					if ($list['multa_conta'] != "") $list['multa_conta'] = number_format($list['multa_conta'],2,",",""); 
					if ($list['valor_saiu_caixa'] != "") $list['valor_saiu_caixa'] = number_format($list['valor_saiu_caixa'],2,",",""); 
					
					if ($list['status_conta'] == 'P') $list['status_conta'] = "Pago"; 
					else if ($list['status_conta'] == 'N') $list['status_conta'] = "Não pago"; 
					if ($list['saiu_do_caixa'] == '0') $list['saiu_do_caixa'] = "Não"; 
					else if ($list['saiu_do_caixa'] == '1') $list['saiu_do_caixa'] = "Sim"; 
					
					
					
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
		                  {$conf['db_name']}conta_pagar
		                    (
		                    
												idfilial,
												idpedido,
												idfuncionario,
												idUltFuncionario,
												descricao_conta, 
												valor_conta, 
												valor_pago, 
												juros_conta, 
												multa_conta, 
												data_cadastro,
												data_vencimento, 
												data_pagamento, 
												data_ult_alteracao,
												status_conta, 
												observacao, 
												saiu_do_caixa, 
												valor_saiu_caixa  
												
												)
		                VALUES
		                    (
		                    
		                    " . $info['idfilial'] . ",  
		                    " . $info['idpedido'] . ",
  		                  " . $info['idfuncionario'] . ",
  		                  " . $info['idUltFuncionario'] . ",
												'" . $info['descricao_conta'] . "',  
												" . $info['valor_conta'] . ",  
												" . $info['valor_pago'] . ",  
												" . $info['juros_conta'] . ",  
												" . $info['multa_conta'] . ",  
												'" . $info['data_cadastro'] . "',  
												'" . $info['data_vencimento'] . "',  
												'" . $info['data_pagamento'] . "',  
												'" . $info['data_ult_alteracao'] . "',  
												'" . $info['status_conta'] . "',  
												'" . $info['observacao'] . "',  
												'" . $info['saiu_do_caixa'] . "',  
												" . $info['valor_saiu_caixa'] . "   
												
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
		function update($idconta_pagar, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}conta_pagar
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
			$update_sql .= " WHERE  idconta_pagar = $idconta_pagar ";
			
			
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
		function delete($idconta_pagar){
			
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
													{$conf['db_name']}conta_pagar
												WHERE
													 idconta_pagar = $idconta_pagar ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY data_vencimento ASC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}conta_pagar
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
		  método: Busca_Generica
		  propósito: Busca_Generica
		*/

		
		function Busca_Generica ( $pg, $rppg, $busca = "", $ordem = "", $url = ""){

												
			if ($ordem == "") $ordem = " ORDER BY CTA_PAG.data_vencimento DESC ";

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
															

			$list_sql = "	SELECT
											CTA_PAG.*
										FROM
           						{$conf['db_name']}conta_pagar CTA_PAG 
												 
           					WHERE
											CTA_PAG.idfilial = " . $_SESSION['idfilial_usuario'] . " AND
											(
												UPPER(CTA_PAG.descricao_conta) LIKE UPPER('%{$busca}%') OR
           					 					UPPER(CTA_PAG.idpedido) LIKE UPPER('%{$busca}%')
											)
										$ordem ";


			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					//insere um índice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);

					if ($list['data_vencimento'] != '0000-00-00') $list['data_vencimento'] = $form->FormataDataParaExibir($list['data_vencimento']); 
					else $list['data_vencimento'] = "";
					if ($list['data_pagamento'] != '0000-00-00') $list['data_pagamento'] = $form->FormataDataParaExibir($list['data_pagamento']); 
					else $list['data_pagamento'] = "";
					
					
					if ($list['valor_conta'] != "") $list['valor_conta'] = number_format($list['valor_conta'],2,",",""); 
					if ($list['valor_pago'] != "") $list['valor_pago'] = number_format($list['valor_pago'],2,",",""); 
					if ($list['juros_conta'] != "") $list['juros_conta'] = number_format($list['juros_conta'],2,",",""); 
					if ($list['multa_conta'] != "") $list['multa_conta'] = number_format($list['multa_conta'],2,",",""); 
					if ($list['valor_saiu_caixa'] != "") $list['valor_saiu_caixa'] = number_format($list['valor_saiu_caixa'],2,",",""); 
					
					if ($list['status_conta'] == 'P') $list['status_conta'] = "Pago"; 
					else if ($list['status_conta'] == 'N') $list['status_conta'] = "Não pago"; 
					if ($list['saiu_do_caixa'] == '0') $list['saiu_do_caixa'] = "Não"; 
					else if ($list['saiu_do_caixa'] == '1') $list['saiu_do_caixa'] = "Sim"; 
					
					
					

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
		  método: Busca_Parametrizada
		  propósito: Busca_Parametrizada
		*/

		function Busca_Parametrizada ( $pg, $rppg, $filtro_where = "", $ordem = "", $url = ""){

			if ($ordem == "") $ordem = " ORDER BY CTA_PAG.data_vencimento DESC ";

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------


			$filtro = " WHERE CTA_PAG.idfilial = " . $_SESSION['idfilial_usuario'];
			
			if ($filtro_where != "")
				$filtro_where = $filtro . " AND ( " . $filtro_where . " ) ";
			else
				$filtro_where = $filtro;

			$list_sql = "	SELECT
											CTA_PAG.* 
										FROM
           						{$conf['db_name']}conta_pagar CTA_PAG 
												 

										$filtro_where

										$ordem ";


			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					//insere um índice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);

					if ($list['data_vencimento'] != '0000-00-00') $list['data_vencimento'] = $form->FormataDataParaExibir($list['data_vencimento']); 
					else $list['data_vencimento'] = "";
					if ($list['data_pagamento'] != '0000-00-00') $list['data_pagamento'] = $form->FormataDataParaExibir($list['data_pagamento']); 
					else $list['data_pagamento'] = "";
					
					
					if ($list['valor_conta'] != "") $list['valor_conta'] = number_format($list['valor_conta'],2,",",""); 
					if ($list['valor_pago'] != "") $list['valor_pago'] = number_format($list['valor_pago'],2,",",""); 
					if ($list['juros_conta'] != "") $list['juros_conta'] = number_format($list['juros_conta'],2,",",""); 
					if ($list['multa_conta'] != "") $list['multa_conta'] = number_format($list['multa_conta'],2,",",""); 
					if ($list['valor_saiu_caixa'] != "") $list['valor_saiu_caixa'] = number_format($list['valor_saiu_caixa'],2,",",""); 
					
					if ($list['status_conta'] == 'P') $list['status_conta'] = "Pago"; 
					else if ($list['status_conta'] == 'N') $list['status_conta'] = "Não pago"; 
					if ($list['saiu_do_caixa'] == '0') $list['saiu_do_caixa'] = "Não"; 
					else if ($list['saiu_do_caixa'] == '1') $list['saiu_do_caixa'] = "Sim"; 
					
					

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


		
				
		
		

	} // fim da classe
?>
