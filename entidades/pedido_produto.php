<?php
	
	class pedido_produto {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function pedido_produto(){
			// n�o faz nada
		}
    
    

    /**
      m�todo: make_list_movimentacao_entrada
      prop�sito: faz a listagem da movimenta��o mensal da entrada de mercadoria.
    */
    
    function make_list_movimentacao_entrada( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
      
      if ($ordem == "") $ordem = " ORDER BY PEDPROD.ordem_produto ASC";
      
      // vari�veis globais
      global $form;
      global $conf;
      global $db;
      global $falha;
      //---------------------
      
      $list_sql = " SELECT
                      PEDPROD.*, PRD.descricao_produto, PED.*, FORN.nome_fornecedor, UNV.sigla_unidade_venda
                    FROM
                      {$conf['db_name']}pedido_produto PEDPROD 
                         INNER JOIN {$conf['db_name']}produto PRD ON PEDPROD.idproduto=PRD.idproduto 
                         INNER JOIN {$conf['db_name']}pedido PED ON PEDPROD.idpedido=PED.idpedido 
                         INNER JOIN {$conf['db_name']}fornecedor FORN ON PED.idfornecedor=FORN.idfornecedor 
                         INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda=UNV.idunidade_venda
                         
                        
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
          
          
          $list["preco_total"] =number_format( ($list["qtd"]*$list["valorUnit"]),2,",","");
          if ($list['qtd'] != "") $list['qtd'] = number_format($list['qtd'],2,",",""); 
          if ($list['qtd_reserva'] != "") $list['qtd_reserva'] = number_format($list['qtd_reserva'],2,",",""); 
          if ($list['valorUnit'] != "") $list['valorUnit'] = number_format($list['valorUnit'],2,",",""); 
          if ($list['data_entrada'] != "") $list['data_entrada'] = $form->formataDataParaExibir($list['data_entrada']);
          
          
          
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
		  m�todo: delete_produto
		  prop�sito: excluir registro de acorod com o pedido
		*/
		function delete_produto($idpedido){
			
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
													{$conf['db_name']}pedido_produto
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
				$this->err = "Este registro n�o pode ser exclu�do, pois existem registros relacionadas a ele.";
			}	

		}	




		


		/**
		  m�todo: getById
		  prop�sito: busca informa��es
		*/
		function getById($idproduto,$idpedido){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											PEDPROD.*
										FROM
											{$conf['db_name']}pedido_produto PEDPROD
										WHERE
											 PEDPROD.idproduto = $idproduto AND  PEDPROD.idpedido = $idpedido ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				
				
				if ($get['qtd'] != "") $get['qtd'] = number_format($get['qtd'],2,",",""); 
					if ($get['qtd_reserva'] != "") $get['qtd_reserva'] = number_format($get['qtd_reserva'],2,",",""); 
					if ($get['valorUnit'] != "") $get['valorUnit'] = number_format($get['valorUnit'],2,",",""); 
					
				
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
			
			if ($ordem == "") $ordem = " ORDER BY PEDPROD.ordem_produto ASC";
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											PEDPROD.*   , PRD.descricao_produto , PED.idpedido
										FROM
           						{$conf['db_name']}pedido_produto PEDPROD 
												 INNER JOIN {$conf['db_name']}produto PRD ON PEDPROD.idproduto=PRD.idproduto 
												 INNER JOIN {$conf['db_name']}pedido PED ON PEDPROD.idpedido=PED.idpedido 
												
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
					
					
					
					if ($list['qtd'] != "") $list['qtd'] = number_format($list['qtd'],2,",",""); 
					if ($list['qtd_reserva'] != "") $list['qtd_reserva'] = number_format($list['qtd_reserva'],2,",",""); 
					if ($list['valorUnit'] != "") $list['valorUnit'] = number_format($list['valorUnit'],2,",",""); 
					
					
					
					
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
		                  {$conf['db_name']}pedido_produto
		                    (
		                    
												idproduto, 
												idpedido, 
												qtd, 
												valorUnit,
												preco_custo_antigo,
												aliquota_icms_produto,
												ipi_produto,
												cst_produto,
												ordem_produto
												
												)
		                VALUES
		                    (
		                    
		                    " . $info['idproduto'] . ",  
												" . $info['idpedido'] . ",  
												" . $info['qtd'] . ",  
												" . $info['valorUnit'] . ",
												" . $info['preco_custo_antigo'] . ",   
												" . $info['aliquota_icms_produto'] . " ,  
												" . $info['ipi_produto'] . ",   
												'" . $info['cst_produto'] . "',   
												" . $info['ordem_produto'] . " 

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
		function update($idproduto,$idpedido, $info){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}pedido_produto
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
			$update_sql .= " WHERE  idproduto = $idproduto AND  idpedido = $idpedido ";
			
			
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
		function delete($idproduto,$idpedido){
			
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
													{$conf['db_name']}pedido_produto
												WHERE
													 idproduto = $idproduto AND  idpedido = $idpedido ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY ordem_produto ASC") {
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}pedido_produto
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
