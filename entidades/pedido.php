<?php


/*
	O modulo "entrada" também utiliza esta entidade para manipulação de dados.
	Pedido e Entrada é a mesma tabela.

*/
	
	class pedido {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function pedido(){
			// não faz nada
		}


		


		/**
		  método: getById
		  propósito: busca informações
		*/
		function getById($idpedido){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											PED.*,FNR.nome_fornecedor,  FL.nome_filial,FNC.nome_funcionario, MC.descricao, CFOP.codigo as codigo_cfop
										FROM
											{$conf['db_name']}pedido PED
											INNER JOIN {$conf['db_name']}fornecedor FNR ON FNR.idfornecedor = PED.idfornecedor
											INNER JOIN {$conf['db_name']}filial FL ON FL.idfilial = PED.idfilial
											INNER JOIN {$conf['db_name']}funcionario FNC ON FNC.idfuncionario = PED.idfuncionario
											LEFT OUTER JOIN {$conf['db_name']}motivo_cancelamento MC ON MC.idmotivo_cancelamento = PED.idmotivo_cancelamento
											LEFT OUTER JOIN {$conf['db_name']}cfop CFOP ON CFOP.idcfop = PED.idcfop
	
										WHERE
											 PED.idpedido = $idpedido ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				if ($get['data_entrada'] != '0000-00-00') $get['data_entrada'] = $form->FormataDataParaExibir($get['data_entrada']); 
				else $get['data_entrada'] = "";
				if ($get['data_emissao_nota'] != '0000-00-00') $get['data_emissao_nota'] = $form->FormataDataParaExibir($get['data_emissao_nota']); 
				else $get['data_emissao_nota'] = "";
				if ($get['previsao_entrega'] != '0000-00-00') $get['previsao_entrega'] = $form->FormataDataParaExibir($get['previsao_entrega']); 
				else $get['previsao_entrega'] = "";
					
				
				if ($get['ipi'] != "") $get['ipi'] = number_format($get['ipi'],2,",",""); 
					if ($get['frete'] != "") $get['frete'] = number_format($get['frete'],2,",",""); 
					if ($get['valor_nota'] != "") $get['valor_nota'] = number_format($get['valor_nota'],2,",",""); 
					if ($get['desconto'] != "") $get['desconto'] = number_format($get['desconto'],2,",",""); else $get['desconto'] = "0,00";
					if ($get['base_calculo_icms'] != "") $get['base_calculo_icms'] = number_format($get['base_calculo_icms'],2,",",""); 
					if ($get['icms'] != "") $get['icms'] = number_format($get['icms'],2,",",""); 
					if ($get['seguro'] != "") $get['seguro'] = number_format($get['seguro'],2,",",""); 
					if ($get['outras_despesas'] != "") $get['outras_despesas'] = number_format($get['outras_despesas'],2,",",""); 
					if ($get['base_calculo_icms_substituicao'] != "") $get['base_calculo_icms_substituicao'] = number_format($get['base_calculo_icms_substituicao'],2,",",""); 
					if ($get['icms_substituicao'] != "") $get['icms_substituicao'] = number_format($get['icms_substituicao'],2,",",""); 
					
				
				if ($get['dataHoraCriacao'] != '0000-00-00 00:00:00') { 
					$array = split(" ",$get['dataHoraCriacao']); 
					$get['datahoraCriacao_D'] = $form->FormataDataParaExibir($array[0]); 
					$get['datahoraCriacao_H'] = $array[1]; 
				} 
				if ($get['dataHoraUltAlteracao'] != '0000-00-00 00:00:00') { 
					$array = split(" ",$get['dataHoraUltAlteracao']); 
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
			
			if ($ordem == "") $ordem = " ORDER BY PED.idfilial ASC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											PED.*   , FNC.idfuncionario , FORN.nome_fornecedor , FLI.nome_filial
										FROM
           						{$conf['db_name']}pedido PED 
												 INNER JOIN {$conf['db_name']}funcionario FNC ON PED.idUltFuncionario=FNC.idfuncionario 
												 INNER JOIN {$conf['db_name']}fornecedor FORN ON PED.idfornecedor=FORN.idfornecedor 
												 INNER JOIN {$conf['db_name']}filial FLI ON PED.idfilial=FLI.idfilial 
												
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
					
					if ($list['data_entrada'] != '0000-00-00') $list['data_entrada'] = $form->FormataDataParaExibir($list['data_entrada']); 
					else $list['data_entrada'] = "";
					if ($list['data_emissao_nota'] != '0000-00-00') $list['data_emissao_nota'] = $form->FormataDataParaExibir($list['data_emissao_nota']); 
					else $list['data_emissao_nota'] = "";
					
					if ($list['previsao_entrega'] != '0000-00-00') $list['previsao_entrega'] = $form->FormataDataParaExibir($list['previsao_entrega']); 
					else $list['previsao_entrega'] = "";
					
					if ($list['ipi'] != "") $list['ipi'] = number_format($list['ipi'],2,",",""); 
					if ($list['frete'] != "") $list['frete'] = number_format($list['frete'],2,",",""); 
					if ($list['valor_nota'] != "") $list['valor_nota'] = number_format($list['valor_nota'],2,",",""); 
					if ($list['desconto'] != "") $list['desconto'] = number_format($list['desconto'],2,",",""); 
					if ($list['base_calculo_icms'] != "") $list['base_calculo_icms'] = number_format($list['base_calculo_icms'],2,",",""); 
					if ($list['icms'] != "") $list['icms'] = number_format($list['icms'],2,",",""); 
					if ($list['seguro'] != "") $list['seguro'] = number_format($list['seguro'],2,",",""); 
					if ($list['outras_despesas'] != "") $list['outras_despesas'] = number_format($list['outras_despesas'],2,",",""); 
					if ($list['base_calculo_icms_substituicao'] != "") $list['base_calculo_icms_substituicao'] = number_format($list['base_calculo_icms_substituicao'],2,",",""); 
					if ($list['icms_substituicao'] != "") $list['icms_substituicao'] = number_format($list['icms_substituicao'],2,",",""); 
					
					if ($list['status_pedido'] == 'P') $list['status_pedido'] = "Pedido"; 
					else if ($list['status_pedido'] == 'E') $list['status_pedido'] = "Entrada"; 
					
					
					
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
		                  {$conf['db_name']}pedido
		                    (
		                    
												idcfop,
												idUltFuncionario, 
												idfuncionario, 
												idmotivo_cancelamento, 
												idfornecedor, 
												idfilial, 
												obs, 
												ipi, 
												frete, 
												valor_nota, 
												data_entrada, 
												numero_nota, 
												serie_nota, 
												data_emissao_nota, 
												desconto, 
												base_calculo_icms, 
												icms, 
												seguro, 
												outras_despesas, 
												base_calculo_icms_substituicao, 
												icms_substituicao, 
												status_pedido,
												previsao_entrega,
												dataHoraCriacao,
												dataHoraUltAlteracao,
												modelo_nota,
												valor_total_produtos	  
												
												)
		                VALUES
		                    (
		                    
												" . $info['idcfop'] . ",  
		                    " . $info['idUltFuncionario'] . ",  
												" . $info['idfuncionario'] . ",  
												" . $info['idmotivo_cancelamento'] . ",  
												" . $info['idfornecedor'] . ",  
												" . $info['idfilial'] . ",  
												'" . $info['obs'] . "',  
												" . $info['ipi'] . ",  
												" . $info['frete'] . ",  
												" . $info['valor_nota'] . ",  
												'" . $info['data_entrada'] . "',  
												'" . $info['numero_nota'] . "',  
												'" . $info['serie_nota'] . "',  
												'" . $info['data_emissao_nota'] . "',  
												" . $info['desconto'] . ",  
												" . $info['base_calculo_icms'] . ",  
												" . $info['icms'] . ",  
												" . $info['seguro'] . ",  
												" . $info['outras_despesas'] . ",  
												" . $info['base_calculo_icms_substituicao'] . ",  
												" . $info['icms_substituicao'] . ",  
												'" . $info['status_pedido'] . "',   
												'" . $info['previsao_entrega'] . "',
												'" . $info['dataHoraCriacao'] . "',
												'" . $info['dataHoraUltAlteracao'] . "',
												'" . $info['modelo_nota'] . "',
												" . $info['valor_total_produtos'] . "
												
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
		function update($idpedido, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}pedido
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
			$update_sql .= " WHERE  idpedido = $idpedido ";
			
		
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
		function delete($idpedido){
			
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
								{$conf['db_name']}pedido_produto
							WHERE 
								 idpedido = $idpedido
							";
		
			$verifica_q = $db->query($sql);
			$n0 = $db->num_rows($verifica_q);
			
			
			//---------------------
			

			// verifica se pode excluir
			if (1 && $n0==0) {

				

				$delete_sql = "	DELETE FROM
													{$conf['db_name']}pedido
												WHERE
													 idpedido = $idpedido
												AND  status_pedido = 'P' ";

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
											{$conf['db_name']}pedido
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
