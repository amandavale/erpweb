<?php
	
	class cheque {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function cheque(){
			// não faz nada
		}




		/**
		  método: Busca_Destino_Cheque
		  spropósito: Busca o destino do cheque, ou seja, quais contas a pagar ele está relacionado
		*/
		
		function Busca_Destino_Cheque ($idcheque) {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------


			//busca os registros no banco de dados e monta o vetor de retorno
			$list_return = array();			
			

			$list_sql = "	SELECT
											CTA_PAG.*   , CPAGT.descricao_conta_pagar
										FROM
           						{$conf['db_name']}conta_pagar CTA_PAG 
												 LEFT OUTER JOIN {$conf['db_name']}conta_pagar_tipo CPAGT ON CTA_PAG.idconta_pagar_tipo=CPAGT.idconta_pagar_tipo
												 INNER JOIN {$conf['db_name']}conta_pagar_cheque CPCHQ ON CTA_PAG.idconta_pagar=CPCHQ.idconta_pagar		
												 INNER JOIN {$conf['db_name']}cheque CHQ ON CPCHQ.idcheque=CHQ.idcheque

										WHERE 								
											CHQ.idcheque = $idcheque

										ORDER BY 
											CTA_PAG.data_vencimento DESC ";
			

			//manda fazer a paginação
			$list_q = $db->query($list_sql);

			// percorre os registros de contas a receber, por venda	
			$cont = 0;
			while($list = $db->fetch_array($list_q)){

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
		




		/**
		  método: Busca_Origem_Cheque
		  spropósito: Busca a origem do cheque, ou seja, quais contas a receber ele está relacionado
		*/
		
		function Busca_Origem_Cheque ($idcheque) {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------


			//busca os registros no banco de dados e monta o vetor de retorno
			$list_return = array();			
			

			// busca as contas a receber de acordo com o registro de vendas
			$list_sql = "	SELECT
											CTA_REC.*, CLI.nome_cliente, FNC_CR.nome_funcionario,
											MREC.sigla_modo_recebimento, MREC.descricao as descricao_modo_recebimento,
											ORCT.valor_total_nota, ORCT.valor_comissao_vendedor, ORCT.valor_base_calc_comissao, ORCT.fator_correcao_comissao_vendedor

										FROM
											{$conf['db_name']}conta_receber CTA_REC 
												INNER JOIN {$conf['db_name']}modo_recebimento MREC ON CTA_REC.sigla_modo_recebimento=MREC.sigla_modo_recebimento 
												INNER JOIN {$conf['db_name']}orcamento ORCT ON CTA_REC.idorcamento = ORCT.idorcamento
												LEFT OUTER JOIN {$conf['db_name']}cliente CLI ON ORCT.idcliente=CLI.idcliente
												INNER JOIN {$conf['db_name']}funcionario FNC_CR ON ORCT.idfuncionario=FNC_CR.idfuncionario											
												INNER JOIN {$conf['db_name']}conta_receber_cheque CRCHQ ON CTA_REC.idconta_receber=CRCHQ.idconta_receber		
												INNER JOIN {$conf['db_name']}cheque CHQ ON CRCHQ.idcheque=CHQ.idcheque


										WHERE 
											
											CHQ.idcheque = $idcheque

										ORDER BY 
											CTA_REC.idorcamento ASC, CTA_REC.idconta_receber ASC
									";				



			//manda fazer a paginação
			$list_q = $db->query($list_sql);

			// percorre os registros de contas a receber, por venda	
			$cont = 0;
			while($list = $db->fetch_array($list_q)){

				if ($list['data_vencimento'] != '0000-00-00') $list['data_vencimento'] = $form->FormataDataParaExibir($list['data_vencimento']); 
				else $list['data_vencimento'] = "";

				if ($list['data_recebimento'] != '0000-00-00') $list['data_recebimento'] = $form->FormataDataParaExibir($list['data_recebimento']); 
				else $list['data_recebimento'] = "";

				if ($list['data_baixa'] != '0000-00-00') $list['data_baixa'] = $form->FormataDataParaExibir($list['data_baixa']); 
				else $list['data_baixa'] = "";

				if ($list['data_cadastro'] != '0000-00-00 00:00:00') { 
					$array = split(" ",$list['data_cadastro']); 
					$list['datahoraCadastro_D'] = $form->FormataDataParaExibir($array[0]); 
					$list['datahoraCadastro_H'] = $array[1]; 
				} 

				if ($list['status_conta'] == 'RE') $list['status_conta'] = "Recebido"; 
				else if ($list['status_conta'] == 'RP') $list['status_conta'] = "Rec. Parcial"; 
				else if ($list['status_conta'] == 'NA') $list['status_conta'] = "Não Recebido"; 
				else if ($list['status_conta'] == 'NE') $list['status_conta'] = "Negociado"; 

				if ($list['baixa_conta'] == '0') $list['baixa_conta'] = "Não"; 
				else if ($list['baixa_conta'] == '1') $list['baixa_conta'] = "Sim"; 

				
				$list['valor_total_conta'] = $list['valor_basico_conta'] + $list['valor_juros_parcela'];
				if ($list['valor_total_conta'] != "") $list['valor_total_conta'] = number_format($list['valor_total_conta'],2,",",""); 


				if ($list['valor_basico_conta'] != "") $list['valor_basico_conta'] = number_format($list['valor_basico_conta'],2,",",""); 
				if ($list['valor_juros_parcela'] != "") $list['valor_juros_parcela'] = number_format($list['valor_juros_parcela'],2,",",""); 
				if ($list['valor_juros_atraso'] != "") $list['valor_juros_atraso'] = number_format($list['valor_juros_atraso'],2,",",""); 
				if ($list['valor_multa'] != "") $list['valor_multa'] = number_format($list['valor_multa'],2,",",""); 
				if ($list['valor_recebido'] != "") $list['valor_recebido'] = number_format($list['valor_recebido'],2,",",""); 


        $list_return[] = $list;
				
				$cont++;
			
			}

			return $list_return;


		}	
		



		


		/**
		  método: getById
		  propósito: busca informações
		*/
		function getById($idcheque){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											CHQ.* , BNC.nome_banco
										FROM
											{$conf['db_name']}cheque CHQ
													INNER JOIN {$conf['db_name']}banco BNC ON CHQ.idbanco=BNC.idbanco 
										WHERE
											 CHQ.idcheque = $idcheque ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				if ($get['data_cheque'] != '0000-00-00') $get['data_cheque'] = $form->FormataDataParaExibir($get['data_cheque']); 
				else $get['data_cheque'] = "";
					if ($get['bom_para'] != '0000-00-00') $get['bom_para'] = $form->FormataDataParaExibir($get['bom_para']); 
				else $get['bom_para'] = "";
					
				
				if ($get['valor'] != "") $get['valor'] = number_format($get['valor'],2,",",""); 
					
				
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
			
			if ($ordem == "") $ordem = " ORDER BY CHQ.idbanco ASC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											CHQ.*   , BNC.nome_banco
										FROM
           						{$conf['db_name']}cheque CHQ 
												 INNER JOIN {$conf['db_name']}banco BNC ON CHQ.idbanco=BNC.idbanco 
												
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
					
					if ($list['data_cheque'] != '0000-00-00') $list['data_cheque'] = $form->FormataDataParaExibir($list['data_cheque']); 
					else $list['data_cheque'] = "";
					if ($list['bom_para'] != '0000-00-00') $list['bom_para'] = $form->FormataDataParaExibir($list['bom_para']); 
					else $list['bom_para'] = "";
					
					
					if ($list['valor'] != "") $list['valor'] = number_format($list['valor'],2,",",""); 
					
					
					
					
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
		                  {$conf['db_name']}cheque
		                    (
		                    
												idbanco, 
												agencia, 
												agencia_dig, 
												conta, 
												conta_dig, 
												numero_cheque, 
												data_cheque, 
												bom_para, 
												titular_conta, 
												valor, 
												observacao  
												
												)
		                VALUES
		                    (
		                    
		                    " . $info['idbanco'] . ",  
												'" . $info['agencia'] . "',  
												'" . $info['agencia_dig'] . "',  
												'" . $info['conta'] . "',  
												'" . $info['conta_dig'] . "',  
												'" . $info['numero_cheque'] . "',  
												'" . $info['data_cheque'] . "',  
												'" . $info['bom_para'] . "',  
												'" . $info['titular_conta'] . "',  
												" . $info['valor'] . ",  
												'" . $info['observacao'] . "'   
												
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
		function update($idcheque, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}cheque
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
			$update_sql .= " WHERE  idcheque = $idcheque ";
			
			
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
		function delete($idcheque){
			
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
													{$conf['db_name']}cheque
												WHERE
													 idcheque = $idcheque ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY idbanco ASC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}cheque
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

			if ($ordem == "") $ordem = " ORDER BY CHQ.idcheque ";

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
															

			$list_sql = "	SELECT
											CHQ.*   , BNC.nome_banco
										FROM
           						{$conf['db_name']}cheque CHQ 
												 INNER JOIN {$conf['db_name']}banco BNC ON CHQ.idbanco=BNC.idbanco 
           					WHERE
											(
											UPPER(BNC.nome_banco) LIKE UPPER('%{$busca}%') OR
           					 	UPPER(CHQ.agencia) LIKE UPPER('%{$busca}%') OR
           					 	UPPER(CHQ.conta) LIKE UPPER('%{$busca}%') OR
											UPPER(CHQ.titular_conta) LIKE UPPER('%{$busca}%') OR
											UPPER(CHQ.valor) LIKE UPPER('%{$busca}%') OR
           					 	UPPER(CHQ.numero_cheque) LIKE UPPER('%{$busca}%')
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

					if ($list['data_cheque'] != '0000-00-00') $list['data_cheque'] = $form->FormataDataParaExibir($list['data_cheque']); 
					else $list['data_cheque'] = "";
					if ($list['bom_para'] != '0000-00-00') $list['bom_para'] = $form->FormataDataParaExibir($list['bom_para']); 
					else $list['bom_para'] = "";
					
					
					if ($list['valor'] != "") $list['valor'] = number_format($list['valor'],2,",",""); 
					
					

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

			if ($ordem == "") $ordem = " ORDER BY CHQ.idcheque ";

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------


			if ($filtro_where != "")
				$filtro_where = " WHERE ( " . $filtro_where . " ) ";



			$list_sql = "	SELECT
											CHQ.*   , BNC.nome_banco
										FROM
           						{$conf['db_name']}cheque CHQ 
												 INNER JOIN {$conf['db_name']}banco BNC ON CHQ.idbanco=BNC.idbanco 

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

					if ($list['data_cheque'] != '0000-00-00') $list['data_cheque'] = $form->FormataDataParaExibir($list['data_cheque']); 
					else $list['data_cheque'] = "";
					if ($list['bom_para'] != '0000-00-00') $list['bom_para'] = $form->FormataDataParaExibir($list['bom_para']); 
					else $list['bom_para'] = "";
					
					
					if ($list['valor'] != "") $list['valor'] = number_format($list['valor'],2,",",""); 
					
					

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
