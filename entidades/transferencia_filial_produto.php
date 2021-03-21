<?php


class transferencia_filial_produto {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function transferencia_filial_produto(){
			// nгo faz nada
		}
    
    
    
    
    /**
      mйtodo: make_list_movimentacao_saida
      propуsito: faz a listagem da movimentaзгo mensal da entrada de mercadoria.
    */
    
    function make_list_movimentacao_saida( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
      
      
      // variбveis globais
      global $form;
      global $conf;
      global $db;
      global $falha;
      //---------------------
      
      $list_sql = " SELECT TRNFLPRD.* , PRD.descricao_produto, FL.nome_filial AS filial_remetente , TRNFL.*, FLD.nome_filial as filial_destinataria, UNV.sigla_unidade_venda
                    FROM
                    {$conf['db_name']}transferencia_filial_produto TRNFLPRD 
                    INNER JOIN {$conf['db_name']}produto PRD ON TRNFLPRD.idproduto = PRD.idproduto
                    INNER JOIN {$conf['db_name']}transferencia_filial TRNFL ON TRNFLPRD.idtransferencia_filial=TRNFL.idtransferencia_filial
                    INNER JOIN {$conf['db_name']}filial FL ON TRNFL.idfilial_remetente = FL.idfilial
                    INNER JOIN {$conf['db_name']}filial FLD ON TRNFL.idfilial_destinataria=FLD.idfilial
                    INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda=UNV.idunidade_venda
                        
                    $filtro 
                    $ordem";


      //manda fazer a paginaзгo
      $list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

      if($list_q){
        
        //busca os registros no banco de dados e monta o vetor de retorno
        $list_return = array();
        $cont = 0;
        while($list = $db->fetch_array($list_q)){
          
          //insere um нndice na listagem
          $list['index'] = $cont+1 + ($pg*$rppg);
          
          
          $list["preco_total"] =number_format( ($list["qtd_transferida"]*$list["preco_unitario_praticado"]),2,",","");
          if ($list['qtd_transferida'] != "") $list['qtd_transferida'] = number_format($list['qtd_transferida'],2,",",""); 
          if ($list['preco_unitario_praticado'] != "") $list['preco_unitario_praticado'] = number_format($list['preco_unitario_praticado'],2,",",""); 
          if ($list['data_transferencia'] != "") $list['data_transferencia'] = $form->FormataDataHoraParaExibir($list['data_transferencia']);
          
          
          
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
			mйtodo: set
		  propуsito: inclui novo registro
		*/

		function set($info){
			
			// variбveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			
			$set_sql = "  INSERT INTO
		                  {$conf['db_name']}transferencia_filial_produto
		                    (
		                    
												idtransferencia_filial, 
												idproduto, 
												qtd_transferida, 
												preco_unitario_praticado
												
												)
		                VALUES
		                    (
		                    
		                    " . $info['idtransferencia_filial'] . ",  
												" . $info['idproduto'] . ",  
												" . $info['qtd_transferida'] . ",  
												" . $info['preco_unitario_praticado'] . " 
												
												)";
			
			
			
			//executa a query e testa se a consulta foi "boa"
			if($db->query($set_sql)){
				//retorna o cуdigo inserido
				$codigo = $db->insert_id();
				
				
				
				return($codigo);
			}
			else{
				$this->err = $falha['inserir'];
				return(0);
			}
		}

}
?>