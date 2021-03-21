<?php
	
	class parametro {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function parametro(){
			// não faz nada
		}


		


		/**
		  método: getById
		  propósito: busca informações
		*/
		function getById($idparametro){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											PA.*
										FROM
											{$conf['db_name']}parametro PA
										WHERE
											 PA.idparametro = $idparametro ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				if ($get['descontoMaximoOrcamento'] != "") $get['descontoMaximoOrcamento'] = number_format($get['descontoMaximoOrcamento'],2,",","");
				if ($get['limiteCreditoPadrao'] != "") $get['limiteCreditoPadrao'] = number_format($get['limiteCreditoPadrao'],2,",","");
				if ($get['jurosPadraoParcelamento'] != "") $get['jurosPadraoParcelamento'] = number_format($get['jurosPadraoParcelamento'],2,",","");
				if ($get['jurosPadraoAtraso'] != "") $get['jurosPadraoAtraso'] = number_format($get['jurosPadraoAtraso'],2,",","");
				if ($get['jurosPadraoDesconto'] != "") $get['jurosPadraoDesconto'] = number_format($get['jurosPadraoDesconto'],2,",","");
				if ($get['porcentagem_maxima'] != "") $get['porcentagem_maxima'] = number_format($get['porcentagem_maxima'],2,",","");
				


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
			
			if ($ordem == "") $ordem = " ORDER BY PA.idparametro ASC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											PA.*
										FROM
           						{$conf['db_name']}parametro PA
												
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
					
					if ($list['descontoMaximoOrcamento'] != "") $list['descontoMaximoOrcamento'] = number_format($list['descontoMaximoOrcamento'],2,",","");
					if ($list['limiteCreditoPadrao'] != "") $list['limiteCreditoPadrao'] = number_format($list['limiteCreditoPadrao'],2,",","");
					if ($list['jurosPadraoParcelamento'] != "") $list['jurosPadraoParcelamento'] = number_format($list['jurosPadraoParcelamento'],2,",","");
					if ($list['jurosPadraoAtraso'] != "") $list['jurosPadraoAtraso'] = number_format($list['jurosPadraoAtraso'],2,",","");
					if ($list['jurosPadraoDesconto'] != "") $list['jurosPadraoDesconto'] = number_format($list['jurosPadraoDesconto'],2,",","");
					if ($list['porcentagem_maxima'] != "") $list['porcentagem_maxima'] = number_format($list['porcentagem_maxima'],2,",","");
					

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
		                  {$conf['db_name']}parametro
		                    (
		                    
												validadeOrcamento,
												maximoItensOrcamento,
											  descontoMaximoOrcamento,
											  limiteCreditoPadrao,
											  jurosPadraoParcelamento,
											  jurosPadraoAtraso,
												jurosPadraoDesconto,
											  porcentagem_maxima,
												limite_cancelamento,
												modeloPadraoNota,
												seriePadraoNota

												)
		                VALUES
		                    (
		                    
												" . $info['validadeOrcamento'] . ",
												" . $info['maximoItensOrcamento'] . ",
												" . $info['descontoMaximoOrcamento'] . ",
												" . $info['limiteCreditoPadrao'] . ",
												" . $info['jurosPadraoParcelamento'] . ",
												" . $info['jurosPadraoAtraso'] . ",
												" . $info['jurosPadraoDesconto'] . ",
												" . $info['porcentagem_maxima'] . ",
												" . $info['limite_cancelamento'] . ",
												'" . $info['modeloPadraoNota'] . "',
												'" . $info['seriePadraoNota'] . "'

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
		function update($idparametro, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}parametro
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
			$update_sql .= " WHERE  idparametro = $idparametro ";


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
		function delete($idparametro){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			// verifica se pode excluir
			if (1) {

				

				$delete_sql = "	DELETE FROM
													{$conf['db_name']}parametro
												WHERE
													 idparametro = $idparametro ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY descricaoparametro ASC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}parametro
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
