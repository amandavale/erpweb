<?php
	
	class orcamento_produto {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function orcamento_produto(){
			// n�o faz nada
		}

   /**
      m�todo: make_list_consulta_venda_produto
      prop�sito: faz a listagem da movimenta��o mensal da entrada de mercadoria.
    */
    
    function make_list_consulta_venda_produto( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
      
      
      
      // vari�veis globais
      global $form;
      global $conf;
      global $db;
      global $falha;
      //---------------------
     
      $list_sql = " SELECT ORC.*, FUN.nome_funcionario, CLI.nome_cliente,ORCPRD.*,PRD.descricao_produto,PRD.idproduto, UNV.sigla_unidade_venda
                    FROM
                     {$conf['db_name']}orcamento ORC
                    INNER JOIN  {$conf['db_name']}funcionario FUN ON ORC.idfuncionario = FUN.idfuncionario
                    LEFT OUTER JOIN  {$conf['db_name']}cliente CLI ON ORC.idcliente = CLI.idcliente
                    INNER JOIN  {$conf['db_name']}orcamento_produto ORCPRD ON ORC.idorcamento = ORCPRD.idorcamento
                    INNER JOIN  {$conf['db_name']}produto PRD ON PRD.idproduto = ORCPRD.idproduto
                    INNER JOIN  {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda = UNV.idunidade_venda
                         
                        
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
          
          
          
          $list['valor_total'] = number_format($list['qtd_produto']*$list['preco_unitario_produto'],2,",","");
          if ($list['qtd'] != "") $list['qtd'] = number_format($list['qtd'],2,",",""); 
          if ($list['qtd_produto'] != "") $list['qtd_produto'] = number_format($list['qtd_produto'],2,",",""); 
          if ($list['qtd_reserva'] != "") $list['qtd_reserva'] = number_format($list['qtd_reserva'],2,",",""); 
          if ($list['valorUnit'] != "") $list['valorUnit'] = number_format($list['valorUnit'],2,",",""); 
          if ($list['preco_unitario_produto'] != "") $list['preco_unitario_produto'] = number_format($list['preco_unitario_produto'],2,",",""); 
          if ($list['datahoraCriacaoNF'] != "") $list['datahoraCriacaoNF'] = $form->FormataDataHoraParaExibir($list['datahoraCriacaoNF']);
          if ($list['datahoraCriacao'] != "") $list['datahoraCriacao'] = $form->FormataDataHoraParaExibir($list['datahoraCriacao']);
          if ($list['tipoOrcamento'] == 'SD') $list['tipoOrcamento'] = "S�rie D";
          if ($list['tipoOrcamento'] == 'ECF') $list['tipoOrcamento'] = "Cupom Fiscal";
          if ($list['tipoOrcamento'] == 'NF') $list['tipoOrcamento'] = "Nota Fiscal";
          
          
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
		function getById($idproduto,$idorcamento){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											PO.*
										FROM
											{$conf['db_name']}orcamento_produto PO
										WHERE
											 PO.idproduto = $idproduto AND  PO.idorcamento = $idorcamento ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				
				
				if ($get['qtd_produto'] != "") $get['qtd_produto'] = number_format($get['qtd_produto'],2,",","");
				if ($get['preco_unitario_produto'] != "") $get['preco_unitario_produto'] = number_format($get['preco_unitario_produto'],2,",","");
				if ($get['desconto_produto'] != "") $get['desconto_produto'] = number_format($get['desconto_produto'],2,",","");

				
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
			
			if ($ordem == "") $ordem = " ORDER BY PO.idorcamento ASC";
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											PO.*   , PRD.descricao_produto, PRD.codigo_produto , ORCT.idorcamento, UNPRD.sigla_unidade_venda
										FROM
           						{$conf['db_name']}orcamento_produto PO 
												 INNER JOIN {$conf['db_name']}produto PRD ON PO.idproduto=PRD.idproduto 
												 INNER JOIN {$conf['db_name']}orcamento ORCT ON PO.idorcamento=ORCT.idorcamento
												 INNER JOIN {$conf['db_name']}unidade_venda UNPRD ON UNPRD.idunidade_venda=PRD.idunidade_venda
												
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
					
					
					
					if ($list['qtd_produto'] != "") $list['qtd_produto'] = number_format($list['qtd_produto'],2,",","");
					if ($list['preco_unitario_produto'] != "") $list['preco_unitario_produto'] = number_format($list['preco_unitario_produto'],2,",","");
					if ($list['desconto_produto'] != "") $list['desconto_produto'] = number_format($list['desconto_produto'],2,",","");
					
					
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
		                  {$conf['db_name']}orcamento_produto
		                    (
		                    
												idproduto, 
												idorcamento, 
												qtd_produto,
												preco_unitario_produto,
												desconto_produto,
												aliquota_icms_produto,
												cst_produto
												
												)
		                VALUES
		                    (
		                    
		                    " . $info['idproduto'] . ",  
												" . $info['idorcamento'] . ",  
												" . $info['qtd_produto'] . ",
												" . $info['preco_unitario_produto'] . ",
												" . $info['desconto_produto'] . ",
												" . $info['aliquota_icms_produto'] . ",
												'" . $info['cst_produto'] . "'

												
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
		function update($idproduto,$idorcamento, $info){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}orcamento_produto
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
			$update_sql .= " WHERE  idproduto = $idproduto AND  idorcamento = $idorcamento ";
			
			
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
		function delete($idproduto,$idorcamento){
			
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
													{$conf['db_name']}orcamento_produto
												WHERE
													 idproduto = $idproduto AND  idorcamento = $idorcamento ";
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
				$this->err = "Este registro n�o pode ser exclu�do, pois existem registros relacionados a ele.";
			}	

		}	

		
		/**
		  m�todo: make_list
		  prop�sito: faz a listagem para colocar no select
		*/

		function make_list_select( $filtro = "", $ordem = " ORDER BY idorcamento ASC") {
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}orcamento_produto
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
