<?php
	
	class sintegra {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function sintegra(){
			// não faz nada
		}


		
		/**
		  método: getByCodigo
		  propósito: busca informações
		*/
		function getByCodigo($codigo_cfop){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											CFOP.*
										FROM
											{$conf['db_name']}cfop CFOP
										WHERE
											 CFOP.codigo = '$codigo_cfop' ";


			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				
				
				
				
				//retorna o vetor associativo com os dados
				return $get;
			}
			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}
				
		}


		/**
		  método: getById
		  propósito: busca informações
		*/
		function getById($idcfop){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											CFOP.*
										FROM
											{$conf['db_name']}cfop CFOP
										WHERE
											 CFOP.idcfop = $idcfop ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				$get['descricao_curta'] = substr($get['descricao'],0,25);
				
				
				
				//retorna o vetor associativo com os dados
				return $get;
			}
			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}
				
		}

		/**
		  método: make_list_saida_50
		  propósito: faz a listagem dos registros de saida do tipo 50
		*/
		
		function make_list_saida_50( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
			
			//if ($ordem == "") $ordem = " ORDER BY CFOP.codigo ASC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											O.*, C.*, CF.*, CJ.*, E.*,ES.*, CFOP.*, ORCPRD.*, sum(ORCPRD.preco_unitario_produto) as preco_total  
										FROM
           						{$conf['db_name']}orcamento O
											INNER JOIN {$conf['db_name']}cliente C ON O.idcliente = C.idcliente
											LEFT OUTER JOIN {$conf['db_name']}cliente_fisico CF ON C.idcliente = CF.idcliente
											LEFT OUTER JOIN {$conf['db_name']}cliente_juridico CJ ON C.idcliente = CJ.idcliente
                      LEFT OUTER JOIN {$conf['db_name']}endereco E ON C.idendereco_cliente = E.idendereco
                      LEFT OUTER JOIN {$conf['db_name']}estado ES ON E.idestado = ES.idestado 
                      LEFT OUTER JOIN {$conf['db_name']}cfop CFOP ON O.idcfop = CFOP.idcfop 
                      INNER JOIN {$conf['db_name']}orcamento_produto ORCPRD ON O.idorcamento = ORCPRD.idorcamento

										
										$filtro
										$ordem
                    GROUP BY O.idorcamento, ORCPRD.aliquota_icms_produto";



			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

			if($list_q){
				
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){
					
					//insere um índice na listagem
					$list50['index'] = $cont+1 + ($pg*$rppg);
					$list50['tipo'] = "50";
					if($list['cpf_cliente'] == NULL) 
          {
            $list50['cnpj_cpf'] = $this->formataCamposSintegra($this->FormataCNPJParaSintegra($list['cnpj_cliente']), "N", 14); 
            $list50['inscricao_estadual'] = $this->formataCamposSintegra($this->FormataInscricaoSintegra($list['inscricao_estadual_cliente']), "X", 14);
          }
          else
          {
            $list50['cnpj_cpf'] = $this->formataCamposSintegra($this->FormataCPFParaSintegra($list['cpf_cliente']), "N", 14);
            $list50['inscricao_estadual'] = $this->formataCamposSintegra("ISENTO", "X", 14);
          }
					$list50['data_emissao'] = $this->formataCamposSintegra($this->FormataDataSintegra($list['datahoraCriacaoNF'], 's'), "N", 8);
					$list50['UF'] = $this->formataCamposSintegra($list['sigla_estado'], "X", 2);
          $list50['modelo'] = $this->formataCamposSintegra($list['modeloNota'], "N", 2);
          $list50['serie'] = $this->formataCamposSintegra($list['serieNota'], "X", 3);
          $list50['numero'] = $this->formataCamposSintegra($list['numeroNota'], "N", 6);
					$list50['CFOP'] = $this->formataCamposSintegra($list['codigo'], "N", 4);
					$list50['emitente'] = "P";
					$list50['valor_total'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list['valor_total_nota']), "N", 13);
					$list50['base_calc_icms'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list['base_calc_icms']), "N", 13);          
          $list50['valor_icms'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list['preco_total'] * $list['aliquota_icms_produto']), "N", 13);
          $list50['aliquota_icms'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list['aliquota_icms_produto']), "N", 4);
          if($list['idmotivo_cancelamento'] == NULL) $list50['situacao'] = "N"; else $list50['situacao'] = "S";
          
          //faltam os campos 14-15 , irei seta-los com 0,00
          $list50['isentas'] = $this->formataCamposSintegra("000", "N", 13);
          $list50['outras'] = $this->formataCamposSintegra("000", "N", 13);

          $list_return[] = $list50;
          
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
      método: make_list_entrada_50
      propósito: faz a listagem dos registros de entrada do tipo 50
    */
    
    function make_list_entrada_50( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
      
      //if ($ordem == "") $ordem = " ORDER BY CFOP.codigo ASC";
      
      // variáveis globais
      global $form;
      global $conf;
      global $db;
      global $falha;
      //---------------------
      
      $list_sql = " SELECT
                      PED.*, FORN.*, E.*, EST.*, CFOP.*, PEDPRD.*, sum(PEDPRD.valorUnit) as preco_total   
                    FROM
                      {$conf['db_name']}pedido PED
                      LEFT OUTER JOIN {$conf['db_name']}fornecedor FORN ON PED.idfornecedor = FORN.idfornecedor
                      LEFT OUTER JOIN {$conf['db_name']}endereco E ON FORN.idendereco_fornecedor = E.idendereco
                      INNER JOIN {$conf['db_name']}estado EST ON E.idestado = EST.idestado
                      INNER JOIN {$conf['db_name']}pedido_produto PEDPRD ON PED.idpedido = PEDPRD.idpedido
                      INNER JOIN {$conf['db_name']}cfop CFOP ON PED.idcfop = CFOP.idcfop

                    
                    $filtro
                    $ordem
                    GROUP BY PED.idpedido, PEDPRD.aliquota_icms_produto";


      //manda fazer a paginação
      $list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

      if($list_q){
        
        //busca os registros no banco de dados e monta o vetor de retorno
        $list_return = array();
        $cont = 0;
        while($list = $db->fetch_array($list_q)){
          
          //insere um índice na listagem
          $list50['index'] = $cont+1 + ($pg*$rppg);
          $list50['tipo'] = "50";
          if(strlen($list['cpf_cnpj']) > 14) $list['cpf_cnpj'] = $this->FormataCNPJParaSintegra($list['cpf_cnpj']); else $list['cpf_cnpj'] = $this->FormataCPFParaSintegra($list['cpf_cnpj']);
          $list50['cnpj_cpf'] = $this->formataCamposSintegra($list['cpf_cnpj'], "N", 14);
          if($list['inscricao_estadual_fornecedor'] == NULL) $list50['inscricao_estadual'] = $this->formataCamposSintegra("ISENTO", "X", 14); else $list50['inscricao_estadual'] = $this->formataCamposSintegra($this->FormataInscricaoSintegra($list['inscricao_estadual_fornecedor']), "X", 14);
          $list50['data_emissao'] = $this->formataCamposSintegra($this->FormataDataSintegra($list['data_emissao_nota']), "N", 8);
          $list50['UF'] = $this->formataCamposSintegra($list['sigla_estado'], "N", 2);
          $list50['modelo'] = $this->formataCamposSintegra($list['modelo_nota'], "N", 2);
          $list50['serie'] = $this->formataCamposSintegra($list['serie_nota'], "X", 3);
          $list50['numero'] = $this->formataCamposSintegra($list['numero_nota'], "N", 6);
          $list50['CFOP'] = $this->formataCamposSintegra($list['codigo'], "N", 4);
          $list50['emitente'] = "T";
          $list50['valor_total'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list['valor_total']), "N", 13);
          $list50['base_calc_icms'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list['base_calc_icms']), "N", 13);          
          $list50['valor_icms'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($form->formataTelefoneParaExibir($list['preco_total'] * $list['aliquota_icms_produto'])), "N", 13);
          $list50['aliquota_icms'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list['aliquota_icms_produto']), "N", 4);
          if($list['idmotivo_cancelamento'] == NULL) $list50['situacao'] = "N"; else $list50['situacao'] = "S";
          
          //faltam os campos 14-15 , irei seta-los com 0,00
          $list50['isentas'] = $this->formataCamposSintegra("000", "N", 13);
          $list50['outras'] = $this->formataCamposSintegra("000", "N", 13);

          $list_return[] = $list50;
          
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
      método: make_list_saida_54
      propósito: faz a listagem dos registros de saida do tipo 54
    */

    function make_list_saida_54( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
      
     // if ($ordem == "") $ordem = " ORDER BY CFOP.codigo ASC";
      
      // variáveis globais
      global $form;
      global $conf;
      global $db;
      global $falha;
      //---------------------
      
      $list_sql = " SELECT
                      O.*, C.*, CF.*, CJ.*, E.*, CFOP.*, ORCPRD.*, PRD.codigo_produto 
                    FROM
                      {$conf['db_name']}orcamento O
                      INNER JOIN {$conf['db_name']}cliente C ON O.idcliente = C.idcliente
                      LEFT OUTER JOIN {$conf['db_name']}cliente_fisico CF ON C.idcliente = CF.idcliente
                      LEFT OUTER JOIN {$conf['db_name']}cliente_juridico CJ ON C.idcliente = CJ.idcliente
                      LEFT OUTER JOIN {$conf['db_name']}endereco E ON C.idendereco_cliente = E.idendereco
                      LEFT OUTER JOIN {$conf['db_name']}estado ES ON E.idestado = ES.idestado 
                      LEFT OUTER JOIN {$conf['db_name']}cfop CFOP ON O.idcfop = CFOP.idcfop 
                      INNER JOIN {$conf['db_name']}orcamento_produto ORCPRD ON O.idorcamento = ORCPRD.idorcamento
                      INNER JOIN {$conf['db_name']}produto PRD ON ORCPRD.idproduto = PRD.idproduto
                        
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
          $list54['index'] = $cont+1 + ($pg*$rppg);
          $list54['tipo'] = "54";
          if($list['cpf_cliente'] == NULL) $list54['cnpj_cpf'] = $this->formataCamposSintegra($this->FormataCNPJParaSintegra($list['cnpj_cliente']), "N", 14); else $list54['cnpj_cpf'] = $this->formataCamposSintegra($this->FormataCPFParaSintegra($list['cpf_cliente']), "N", 14);
          $list54['modelo'] = $this->formataCamposSintegra($list['modeloNota'], "N", 2);
          $list54['serie'] = $this->formataCamposSintegra($list['serieNota'], "X", 3);
          $list54['numero'] = $this->formataCamposSintegra($list['numeroNota'], "N", 6);
          $list54['CFOP'] = $this->formataCamposSintegra($list['codigo'], "N", 4);
          $list54['cst'] = $this->formataCamposSintegra($list['cst_produto'], "X", 3);
          $list54['ordem'] = $this->formataCamposSintegra($list['ordem_produto'], "N", 3);
          $list54['codigo_produto'] = $this->formataCamposSintegra($list['idproduto'], "X", 14);
          $list54['quantidade'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list['qtd_produto'], 's'), "N", 11);
          $list54['valor_bruto'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($form->FormataMoedaParaExibir($list['qtd_produto']*$list['preco_unitario_produto'])), "N", 12);
          $list54['valor_desconto'] = $this->formataCamposSintegra($this->formataMoedaParaExibir((100*$list54['valor_bruto'])/$list['desconto_produto']), "N", 12);
          $list54['base_calculo_icms'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list54['valor_bruto']), "N", 12);
          $list54['base_calculo_icms_tributaria'] = $this->formataCamposSintegra("000", "N", 12);    
          $list54['ipi'] = $this->formataCamposSintegra("000", "N", 12);
          $list54['aliquota_icms'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list['aliquota_icms_produto']), "N", 4);

          $list_return[] = $list54;
          
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
      método: make_list_entrada_54
      propósito: faz a listagem dos registros de entrada do tipo 54
    */

    function make_list_entrada_54( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
      
     // if ($ordem == "") $ordem = " ORDER BY CFOP.codigo ASC";
      
      // variáveis globais
      global $form;
      global $conf;
      global $db;
      global $falha;
      //---------------------
      
      $list_sql = " SELECT
                      PED.*, FORN.*, E.*, EST.*, CFOP.*, PEDPRD.*, PRD.codigo_produto 
                    FROM
                      {$conf['db_name']}pedido PED
                      LEFT OUTER JOIN {$conf['db_name']}fornecedor FORN ON PED.idfornecedor = FORN.idfornecedor
                      LEFT OUTER JOIN {$conf['db_name']}endereco E ON FORN.idendereco_fornecedor = E.idendereco
                      INNER JOIN {$conf['db_name']}estado EST ON E.idestado = EST.idestado
                      INNER JOIN {$conf['db_name']}pedido_produto PEDPRD ON PED.idpedido = PEDPRD.idpedido
                      INNER JOIN {$conf['db_name']}produto PRD ON PEDPRD.idproduto = PRD.idproduto
                      INNER JOIN {$conf['db_name']}cfop CFOP ON PED.idcfop = CFOP.idcfop
                        
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
          $list54['index'] = $cont+1 + ($pg*$rppg);
          $list54['tipo'] = "54";
          if(strlen($list['cpf_cnpj']) > 14) {$list['cpf_cnpj'] = $this->FormataCNPJParaSintegra($list['cpf_cnpj']);} else {$list['cpf_cnpj'] = $this->FormataCPFParaSintegra($list['cpf_cnpj']);}
          $list54['cnpj_cpf'] = $this->formataCamposSintegra($list['cpf_cnpj'], "N", 14);
          $list54['modelo'] = $this->formataCamposSintegra($list['modelo_nota'], "N", 2);
          $list54['serie'] = $this->formataCamposSintegra($list['serie_nota'], "X", 3);
          $list54['numero'] = $this->formataCamposSintegra($list['numero_nota'], "N", 6);
          $list54['CFOP'] = $this->formataCamposSintegra($list['codigo'], "N", 4);
          $list54['cst'] = $this->formataCamposSintegra($list['cst_produto'], "X", 3);
          $list54['ordem'] = $this->formataCamposSintegra($list['ordem_produto'], "N", 3);
          $list54['codigo_produto'] = $this->formataCamposSintegra($list['idproduto'], "X", 14);
          $list54['quantidade'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list['qtd'], 's'), "N", 11);
          $list54['valor_bruto'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($form->FormataMoedaParaExibir($list['qtd']*$list['valorUnit'])), "N", 12);
          $list54['valor_desconto'] = $this->formataCamposSintegra("000", "N", 12);
          $list54['base_calculo_icms'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list54['valor_bruto']), "N", 12);
          $list54['base_calculo_icms_tributaria'] = $this->formataCamposSintegra("000", "N", 12);    
          $list54['ipi'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list['ipi_produto']), "N", 12);
          $list54['aliquota_icms'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list['aliquota_icms_produto']), "N", 4);



          $list_return[] = $list54;
          
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
      método: make_list_registro_61
      propósito: faz a listagem dos registros do tipo 61 ( Serie D)
    */

    function make_list_registro_61( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
      
     // if ($ordem == "") $ordem = " ORDER BY CFOP.codigo ASC";
      
      // variáveis globais
      global $form;
      global $conf;
      global $db;
      global $falha;
      //---------------------

      
      $list_sql = " SELECT
                      O.*, C.*, CF.*, CJ.*, E.*, CFOP.*, ORCPRD.*, PRD.codigo_produto 
                    FROM
                      {$conf['db_name']}orcamento O
                      INNER JOIN {$conf['db_name']}cliente C ON O.idcliente = C.idcliente
                      LEFT OUTER JOIN {$conf['db_name']}cliente_fisico CF ON C.idcliente = CF.idcliente
                      LEFT OUTER JOIN {$conf['db_name']}cliente_juridico CJ ON C.idcliente = CJ.idcliente
                      LEFT OUTER JOIN {$conf['db_name']}endereco E ON C.idendereco_cliente = E.idendereco
                      LEFT OUTER JOIN {$conf['db_name']}estado ES ON E.idestado = ES.idestado 
                      LEFT OUTER JOIN {$conf['db_name']}cfop CFOP ON O.idcfop = CFOP.idcfop 
                      INNER JOIN {$conf['db_name']}orcamento_produto ORCPRD ON O.idorcamento = ORCPRD.idorcamento
                      INNER JOIN {$conf['db_name']}produto PRD ON ORCPRD.idproduto = PRD.idproduto
                        
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
          $list61['index'] = $cont+1 + ($pg*$rppg);
          $list61['tipo'] = "54";
          $list61['branco1'] = "              ";
          $list61['branco2'] = "              ";
          $list61['data_emissao'] = $this->formataCamposSintegra($this->formataDataSintegra($list["datahoraCriacao"],1),"N",8);
          $list61['modelo'] = $this->formataCamposSintegra($list['modeloNota'], "N", 2);
          $list61['serie'] = $this->formataCamposSintegra($list['serieNota'], "X", 3);
          $list61['subserie'] = $this->formataCamposSintegra($list['numeroNota'], "N", 6);
          $list61['numero_inicial'] = $this->formataCamposSintegra($list['numero_inicial'], "N", 3);
          $list61['numero_final'] = $this->formataCamposSintegra($list['numero_final'], "X", 14);
          $list61['valor_bruto'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($form->FormataMoedaParaExibir($list['qtd_produto']*$list['preco_unitario_produto'])), "N", 12);
          $list61['valor_desconto'] = $this->formataCamposSintegra($this->formataMoedaParaExibir((100*$list61['valor_bruto'])/$list['desconto_produto']), "N", 12);
          $list61['base_calculo_icms'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list61['valor_bruto']), "N", 12);
          $list61['aliquota_icms'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list['aliquota_icms_produto']), "N", 4);

          $list_return[] = $list54; 
          
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
      método: make_list_saida_75
      propósito: faz a listagem dos registros de saida do tipo 75
    */


    function make_list_saida_75( $datas, $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
      
     // if ($ordem == "") $ordem = " ORDER BY CFOP.codigo ASC";
      
      // variáveis globais
      global $form;
      global $conf;
      global $db;
      global $falha;
      //---------------------
      
      $list_sql = " SELECT
                      PED.*, FORN.*, E.*, EST.*, CFOP.*, PEDPRD.*, PRD.*,UNV.*
                    FROM
                      {$conf['db_name']}pedido PED
                      LEFT OUTER JOIN {$conf['db_name']}fornecedor FORN ON PED.idfornecedor = FORN.idfornecedor
                      LEFT OUTER JOIN {$conf['db_name']}endereco E ON FORN.idendereco_fornecedor = E.idendereco
                      INNER JOIN {$conf['db_name']}estado EST ON E.idestado = EST.idestado
                      INNER JOIN {$conf['db_name']}pedido_produto PEDPRD ON PED.idpedido = PEDPRD.idpedido
                      INNER JOIN {$conf['db_name']}produto PRD ON PEDPRD.idproduto = PRD.idproduto
                      INNER JOIN {$conf['db_name']}cfop CFOP ON PED.idcfop = CFOP.idcfop
                      INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda = UNV.idunidade_venda
                        
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
          $list75['index'] = $cont+1 + ($pg*$rppg);
          $list75['tipo'] = "75";
          $list75['data_inicial'] = $this->formataCamposSintegra($this->FormataDataSintegra($datas['data_1']), "N", 8);
          $list75['data_final'] = $this->formataCamposSintegra($this->FormataDataSintegra($datas['data_2']), "N", 8);
          $list75['codigo_produto'] = $this->formataCamposSintegra($list['idproduto'], "X", 14);
          $list75['cncm'] = $this->formataCamposSintegra("", "X", 8);
          $list75['descricao'] = $this->formataCamposSintegra($list['descricao_produto'], "X", 53);
          $list75['unidade_venda'] = $this->formataCamposSintegra($list['sigla_unidade_venda'], "X", 6);
          $list75['aliquota_ipi'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list['ipi_produto']), "N", 5);
          $list75['aliquota_icms'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list['icms_produto']), "N", 4);
          $list75['reducao_base_calculo_icms'] = $this->formataCamposSintegra("000", "N", 5);
          $list75['base_calculo_icms_subs_tributaria'] = $this->formataCamposSintegra("000", "N", 13);    



          $list_return[] = $list75;
          
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
      método: make_list_entrada_75
      propósito: faz a listagem dos registros de entrada do tipo 75
    */


    function make_list_entrada_75( $datas, $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
      
     // if ($ordem == "") $ordem = " ORDER BY CFOP.codigo ASC";
      
      // variáveis globais
      global $form;
      global $conf;
      global $db;
      global $falha;
      //---------------------
      
      $list_sql = " SELECT
                      O.*, C.*, CF.*, CJ.*, E.*, CFOP.*, ORCPRD.*, PRD.*,UNV.*
                    FROM
                      {$conf['db_name']}orcamento O
                      INNER JOIN {$conf['db_name']}cliente C ON O.idcliente = C.idcliente
                      LEFT OUTER JOIN {$conf['db_name']}cliente_fisico CF ON C.idcliente = CF.idcliente
                      LEFT OUTER JOIN {$conf['db_name']}cliente_juridico CJ ON C.idcliente = CJ.idcliente
                      LEFT OUTER JOIN {$conf['db_name']}endereco E ON C.idendereco_cliente = E.idendereco
                      LEFT OUTER JOIN {$conf['db_name']}estado ES ON E.idestado = ES.idestado 
                      LEFT OUTER JOIN {$conf['db_name']}cfop CFOP ON O.idcfop = CFOP.idcfop 
                      INNER JOIN {$conf['db_name']}orcamento_produto ORCPRD ON O.idorcamento = ORCPRD.idorcamento
                      INNER JOIN {$conf['db_name']}produto PRD ON ORCPRD.idproduto = PRD.idproduto
                      INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda = UNV.idunidade_venda
                        
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
          $list75['index'] = $cont+1 + ($pg*$rppg);
          $list75['tipo'] = "75";
          $list75['data_inicial'] = $this->formataCamposSintegra($this->FormataDataSintegra($datas['data_1']), "N", 8);
          $list75['data_final'] = $this->formataCamposSintegra($this->FormataDataSintegra($datas['data_2']), "N", 8);
          $list75['codigo_produto'] = $this->formataCamposSintegra($list['idproduto'], "X", 14);
          $list75['cncm'] = $this->formataCamposSintegra("", "X", 8);
          $list75['descricao'] = $this->formataCamposSintegra($list['descricao_produto'], "X", 53);
          $list75['unidade_venda'] = $this->formataCamposSintegra($list['sigla_unidade_venda'], "X", 6);
          $list75['aliquota_ipi'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list['ipi_produto']), "N", 5);
          $list75['aliquota_icms'] = $this->formataCamposSintegra($this->formataMoedaParaExibir($list['icms_produto']), "N", 4);
          $list75['reducao_base_calculo_icms'] = $this->formataCamposSintegra("000", "N", 5);
          $list75['base_calculo_icms_subs_tributaria'] = $this->formataCamposSintegra("000", "N", 13);    



          $list_return[] = $list75;
          
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
      método: armazenaRegistros60ECF
      propósito: extrair e armazenar os registros do tipo 60 contido nos arquivos txt da ECF .
    */

    function armazenaRegistrosECF($arquivo){
    
    $i = 0;
    $a = 0;    
    
    $registro = array();
    
    while(!feof($arquivo))
    {
      $linha = fgets($arquivo);
      
      if(substr($linha,0,2) == "60")
      {
        $registro["60_$i"] = $linha;
        $i++;
      }
      
      if(substr($linha,0,2) == "75")
      {
        $registro["75_$a"] = $linha;
        $a++;
      }

    }
    
    return $registro;
    
    
    }


    /**
      método: comparaRegistro75
      propósito: compara e armazenar os registros únicos do tipo 75  .
    */

    function comparaRegistro75($array1, $array2){
    
      //pega todos os registros que não existe no segundo array
      $aux = array_diff($array1,$array2);
      
      //pega todos os registros que não existe no primeiro array
      $aux2 = array_diff($array2,$array1);
      
      //pega todos os registros que são comuns a ambos arrays
      $aux3 = array_intersect($array1,$array2);
    
      $registros = array_merge($aux,$aux2,$aux3);
    
      $registro = array_unique($registros);
    
      return $registro;
    
    }



    /**
      método: formataCamposSintegra
      propósito: formatar o tamanho do campo com os caracteres determinados.
    */

    function formataCamposSintegra($campo, $tipo, $tamanho){

      if ($tipo == 'N')
          $tipo = "0";
      else $tipo = ' ';
    
      if(strlen($campo) >  $tamanho){

          $campo = substr($campo, 0 , $tamanho);
          
        }
      else{

          while(strlen($campo) < $tamanho)
          {
            if($tipo == '0') $campo = $tipo . $campo;
            else $campo = $campo . $tipo;
          }
        }
        

    return $campo;


    }

    /**
      método: FormataCNPJParaSintegra
      propósito: retirar os caracteres que diferem de números.
    */

    function FormataCNPJParaSintegra($cnpj) {

     $cnpj = substr($cnpj,0,2).substr($cnpj,3,3).substr($cnpj,7,3).substr($cnpj,11,4).substr($cnpj,16,2);

     return $cnpj;

    }

    /**
      método: FormataCPFParaSintegra
      propósito: retirar os caracteres que diferem de números.
    */

    function FormataCPFParaSintegra($cpf) { 

     $cpf = substr($cpf,0,3).substr($cpf,4,3).substr($cpf,8,3).substr($cpf,12,2);

     return $cpf;

    }

    /**
      método: FormataDataSintegra
      propósito: retirar os caracteres que diferem de números.
    */

    function FormataDataSintegra($data, $hora="") {


     if($hora != "")
      {
        $horas = explode(" ",$data);
        $data = $horas[0];
      }

     $data = substr($data,0,4).substr($data,5,2).substr($data,8,2) ;

     return $data;

    }

    /**
      método: FormataCEPSintegra
      propósito: retirar os caracteres que diferem de números.
    */

    function FormataCEPSintegra($data) {

     $cep = explode("-",$data);
  
     $data = $cep[0].$cep[1];

     $cep2 = explode(".",$data);

     $data = $cep2[0].$cep2[1];

     return $data;

    }

    /**
      método: FormataInscricaoSintegra
      propósito: retirar os caracteres que diferem de números.
    */

    function FormataInscricaoSintegra($data) {

     $ie = explode(".",$data);
     $i=0;
    while($ie["$i"])
{  
     $data2 = $data2.$ie["$i"];
     $i++;
}

     return $data2;

    }



    /**
      método: formataMoedaParaExibir
      propósito: retirar os caracteres que diferem de números.
    */

    function formataMoedaParaExibir($data , $qtd="") {

     $data2 = explode(".",$data);
  
     $data = $data2[0].$data2[1];

     $data3 = explode(",",$data);
  
     $data = $data3[0].$data3[1];

     if($qtd != "") $data = $data . "0";

     return $data;

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
		                  {$conf['db_name']}cfop
		                    (
		                    
												codigo, 
												descricao  
												
												)
		                VALUES
		                    (
		                    
		                    '" . $info['codigo'] . "',  
												'" . $info['descricao'] . "'   
												
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
		function update($idcfop, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}cfop
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
			$update_sql .= " WHERE  idcfop = $idcfop ";
			
			
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
		function delete($idcfop){
			
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
								{$conf['db_name']}orcamento
							WHERE 
								 idcfop = $idcfop ";
			$verifica_q = $db->query($sql);
			$n0 = $db->num_rows($verifica_q);
			
			
			//---------------------
			

			// verifica se pode excluir
			if (1 && $n0==0) {

				

				$delete_sql = "	DELETE FROM
													{$conf['db_name']}cfop
												WHERE
													 idcfop = $idcfop ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY codigo ASC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}cfop
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

					$list_return["descricao_cfop"][$cont] = $list['descricao'] . " - " . $list['codigo'];

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
