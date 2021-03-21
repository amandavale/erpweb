<?php
	
	class orcamento {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function orcamento(){
			// não faz nada
		}

   /**
      método: verifica_falha_emissao_ECF
      propósito: verifica se houve alguma falha na emissao de ECF, em caso positivo devolve um vetor contendo as informações da venda
                 para um futuro cancelamento.
      
    */
    
    function verifica_falha_emissao_ECF(){

      // variáveis globais
      global $form;
      global $conf;
      global $db;
      global $falha;
      //---------------------
     
      $list_sql = " SELECT 
											ORC.*
                    FROM
                     {$conf['db_name']}orcamento ORC
                    WHERE
                    	ORC.impressao_ecf_em_andamento = '1' AND
                    	ORC.tipoOrcamento = 'ECF' AND
                    	ORC.idfilial = ".$_SESSION['idfilial_usuario']."                         
                    
                    ";

      //manda fazer a paginação
      $list_q = $db->query($list_sql);

      if($list_q){
        
        //busca os registros no banco de dados e monta o vetor de retorno
        $list_return = array();
        $cont = 0;
        while($list = $db->fetch_array($list_q)){
          
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
      método: make_list_consulta_vendas
      propósito: faz a listagem da movimentação mensal da entrada de mercadoria.
    */
    
    function make_list_consulta_vendas( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
      
      
      
      // variáveis globais
      global $form;
      global $conf;
      global $db;
      global $falha;
      //---------------------
     
      $list_sql = " SELECT ORC.*, FUN.nome_funcionario, CLI.nome_cliente
                    FROM
                     {$conf['db_name']}orcamento ORC
                    INNER JOIN  {$conf['db_name']}funcionario FUN ON ORC.idfuncionario = FUN.idfuncionario
                    LEFT OUTER JOIN  {$conf['db_name']}cliente CLI ON ORC.idcliente = CLI.idcliente
                         
                        
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
          
          
          
          if ($list['qtd'] != "") $list['qtd'] = number_format($list['qtd'],2,",",""); 
          if ($list['qtd_reserva'] != "") $list['qtd_reserva'] = number_format($list['qtd_reserva'],2,",",""); 
          if ($list['valorUnit'] != "") $list['valorUnit'] = number_format($list['valorUnit'],2,",",""); 
          if ($list['valor_total_nota'] != "") $list['valor_total_nota'] = number_format($list['valor_total_nota'],2,",",""); 
          if ($list['datahoraCriacaoNF'] != "") $list['datahoraCriacaoNF'] = $form->FormataDataHoraParaExibir($list['datahoraCriacaoNF']);
          if ($list['datahoraCriacao'] != "") $list['datahoraCriacao'] = $form->FormataDataHoraParaExibir($list['datahoraCriacao']);
          if ($list['tipoOrcamento'] == 'SD') $list['tipoOrcamento'] = "Série D";
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
		  método: getById
		  propósito: busca informações
		*/
		function buscaDadosFiscais($codigo,$tipo){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											ORCT.*,FL.*,FNC.nome_funcionario
										FROM
											{$conf['db_name']}orcamento ORCT
											INNER JOIN {$conf['db_name']}filial FL ON ORCT.idfilial = FL.idfilial
											INNER JOIN {$conf['db_name']}funcionario FNC ON ORCT.idfuncionario = FNC.idfuncionario
										WHERE
											 ORCT.tipoOrcamento = '".$tipo."'
													AND ORCT.numeronota = $codigo 
													AND ORCT.idmotivo_cancelamento is NULL
													AND ORCT.idfilial = " . $_SESSION['idfilial_usuario'];

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida
				if($db->num_rows($get_q) != 0)
				{
					$get = $db->fetch_array($get_q);
					
					
					if ($get['datahoraCriacao'] != '0000-00-00 00:00:00') { 
						$array = split(" ",$get['datahoraCriacao']); 
						$get['datahoraCriacao_D'] = $form->FormataDataParaExibir($array[0]); 
						$get['datahoraCriacao_H'] = $array[1]; 
					} 
					if ($get['datahoraUltEmissao'] != '0000-00-00 00:00:00') { 
						$array = split(" ",$get['datahoraUltEmissao']); 
						$get['datahoraUltEmissao_D'] = $form->FormataDataParaExibir($array[0]); 
						$get['datahoraUltEmissao_H'] = $array[1]; 
					} 
					if ($get['datahoraCriacaoNF'] != '0000-00-00 00:00:00') { 
						$array = split(" ",$get['datahoraCriacaoNF']); 
						$get['datahoraCriacaoNF_D'] = $form->FormataDataParaExibir($array[0]); 
						$get['datahoraCriacaoNF_H'] = $array[1]; 
					} 
					
					if ($get['desconto'] != "") $get['desconto'] = number_format($get['desconto'],2,",",""); 
	
						
					
					//retorna o vetor associativo com os dados
					return $get;
				}
			
			else{
					return(0);
					}
			}
			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}
				
		}

		/**
		  método: Recupera_Dados_Da_NF
		  propósito: Recupera_Dados_Da_NF
		*/
		function Recupera_Dados_Da_NF () {

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			global $smarty;
			global $flags;
			
			global $err;
			global $info;
			
			global $orcamento;
			global $funcionario;
			global $cliente;
			global $cfop;
			global $filial;
			global $motivo_cancelamento;
			global $parametro;
			global $transportador;
			global $modo_recebimento;
			//---------------------


			//busca detalhes
			$info = $orcamento->getById($_GET['idorcamento']);


			//tratamento das informações para fazer o UPDATE
			$info['numidUltfuncionario'] = $info['idUltfuncionario'];
			$info['numidcliente'] = $info['idcliente'];
			$info['littipoPreco'] = $info['tipoPreco'];
			$info['numvalidade'] = $info['validade'];
			$info['numdesconto'] = $info['desconto'];
			$info['numfrete'] = $info['frete'];
			$info['numoutras_despesas'] = $info['outras_despesas'];

			$info['numbase_calculo_icms'] = $info['base_calculo_icms'];
			$info['numvalor_icms'] = $info['valor_icms'];
			$info['numbase_calc_icms_sub'] = $info['base_calc_icms_sub'];
			$info['numvalor_icms_sub'] = $info['valor_icms_sub'];
			$info['numvalor_seguro'] = $info['valor_seguro'];
			$info['numvalor_total_ipi'] = $info['valor_total_ipi'];
			$info['numvalor_total_produtos'] = $info['valor_total_produtos'];
			$info['numvalor_total_nota'] = $info['valor_total_nota'];

			$info['litnomeClienteProv'] = $info['nomeClienteProv'];
			$info['litinfoClienteProv'] = $info['infoClienteProv'];

			$info['numidcfop'] = $info['idcfop'];
			$info['littipoOrcamento'] = $info['tipoOrcamento'];
			$info['numnumeroNota'] = $info['numeroNota'];
			$info['litobs'] = strip_tags($info['obs']);
			$info['litdados_adicionais'] = strip_tags($info['dados_adicionais']);

			if ($info['transportador_frete_por_conta'] == "E") {
				$info['transportador_frete_por_conta_cod'] = "1";
				$info['transportador_frete_por_conta_descricao'] = "Emitente";
			}
			else if ($info['transportador_frete_por_conta'] == "D") {
				$info['transportador_frete_por_conta_cod'] = "2";
				$info['transportador_frete_por_conta_descricao'] = "Destinatário";
			}


			//obtém os erros
			$err = $orcamento->err;

			// busca os dados da filial
			$info_filial = $filial->getById($info['idfilial']);
			$smarty->assign("info_filial", $info_filial);

			// busca os dados do funcionario que criou o Orçamento
			$info_funcionario_criou = $funcionario->getById($info['idfuncionario']);
			$smarty->assign("info_funcionario_criou", $info_funcionario_criou);

			// Funcionário alterou por ultimo o Orçamento
			$info_funcionario_alterou = $funcionario->getById($info['idUltfuncionario']);
			$smarty->assign("info_funcionario_alterou", $info_funcionario_alterou);

			if ($info['idfuncionarioNF'] != "") $info_funcionario_criouNF = $funcionario->getById($info['idfuncionarioNF']);
			else $info_funcionario_criouNF = $funcionario->getById($_SESSION['usr_cod']);
			$smarty->assign("info_funcionario_criouNF", $info_funcionario_criouNF);

			// se selecionou o cliente, busca os dados dele
			if ($info['idcliente'] != "")	{
				$info_cliente = $cliente->getById($info['idcliente']);
				$info_dados_cliente = $cliente->BuscaDadosCliente($info['idcliente']);
			}
			else $flags['nao_selecionou'] = 1;
			
			//busca o nome do cliente
			$info['idcliente_Nome'] = $info_cliente['nome_cliente'];
			$info['idcliente_NomeTemp'] = $info_cliente['nome_cliente'];


			$smarty->assign("info_cliente", $info_cliente);
			$smarty->assign("info_dados_cliente", $info_dados_cliente);
			//---------------------------------------

			// busca os dados do transportador
			$info_transportador = $transportador->BuscaDadosTransportador($info['idtransportador']);
			$smarty->assign("info_transportador", $info_transportador);
			//---------------------------------------



			// busca o motivo do cancelamento, caso tenha sido cancelado
			$info_motivo_cancelamento = $motivo_cancelamento->getById($info['idmotivo_cancelamento']);
			$smarty->assign("info_motivo_cancelamento", $info_motivo_cancelamento);

			// busca o CFOP
			$info_cfop = $cfop->getById($info['idcfop']);
			$smarty->assign("info_cfop", $info_cfop);

			// busca o dia e hora atual
			if ($info['datahoraCriacaoNF'] != '0000-00-00 00:00:00') $flags['data_criacaoNF'] = $info['datahoraCriacaoNF_D'] . " " . $info['datahoraCriacaoNF_H'];
			else $flags['data_criacaoNF'] = date('d/m/Y H:i:s');

			//busca os funcionarios da filial
			$list_funcionarios = $funcionario->Seleciona_Funcionarios_Da_Filial($_SESSION['idfilial_usuario'], "T", "A");
			$smarty->assign("list_funcionarios",$list_funcionarios);

			// busca Motico de cancelamento
			$list_motivo_cancelamento = $motivo_cancelamento->make_list_select();
			$smarty->assign("list_motivo_cancelamento",$list_motivo_cancelamento);

    	// busca o valor padrao para a validade do orçamento e o máximo de itens
			$list_parametro = $parametro->make_list(0, $conf['rppg']);
			if ( count ($list_parametro) > 0 ) {
				$_POST['maximoItensOrcamento'] = $list_parametro[0]['maximoItensOrcamento'];
				$_POST['descontoMaximoOrcamento'] = str_replace(",",".",$list_parametro[0]['descontoMaximoOrcamento']);
				$_POST['jurosPadraoParcelamento'] = number_format($list_parametro[0]['jurosPadraoParcelamento'],2,",","");
				$_POST['modeloNota'] = $list_parametro[0]['modeloPadraoNota'];
				$_POST['serieNota'] = $list_parametro[0]['seriePadraoNota'];
			}
			else {
				$_POST['maximoItensOrcamento'] = 0;
				$_POST['descontoMaximoOrcamento'] = 0;
				$_POST['jurosPadraoParcelamento'] = "0,00";
			}
			$_POST['dias_entre_parcelas'] = "30";

			$_POST['data_parcela1'] = date("d/m/Y", mktime(0, 0, 0, date("m"), date("d")+30, date("Y")) );
			//------------------------------------------------


			$info['juros_parcelamento'] = $form->FormataMoedaParaExibir($info['jurosParcelamento']);
			$info['quantidade_de_parcelas'] = $info['numero_parcelas_cr'];
			$info['modo_recebimento_a_prazo'] = $info['sigla_modo_recebimento_prazo_cr'];
			$info['dias_entre_parcelas'] = $info['dias_entre_parcelas_cr'];
			$info['data_parcela1'] = $form->FormataDataParaExibir($info['data_parcela_1_cr']);


			// busca os modos de recebimento a vista
			$list_modo_recebimento_a_vista = $modo_recebimento->make_list_select(" WHERE a_vista = '1' ");
			$smarty->assign("list_modo_recebimento_a_vista",$list_modo_recebimento_a_vista);

			// busca os modos de recebimento a prazo
			$list_modo_recebimento_a_prazo = $modo_recebimento->make_list_select(" WHERE a_prazo = '1' ");
			$smarty->assign("list_modo_recebimento_a_prazo",$list_modo_recebimento_a_prazo);


		}




		/**
		  método: Verifica_Numero_Documento_Fiscal_Existente
		  propósito: Verifica_Numero_Documento_Fiscal_Existente
		*/
		function Verifica_Numero_Documento_Fiscal_Existente ($idfilial, $numeroNota, $tipoEmissao="SD"){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$get_sql = "	SELECT
											ORCT.*
										FROM
											{$conf['db_name']}orcamento ORCT
										WHERE
											 ORCT.idfilial = $idfilial
											 	AND
											 ORCT.tipoOrcamento	= '$tipoEmissao'
											 	AND
											 ORCT.numeroNota = $numeroNota ";


			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				if ($db->num_rows($get_q) == 0) return (false);
				else return (true);

			}
			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}

		}



		/**
		  método: Deleta_Itens_Orcamento
		  propósito: Deleta_Itens_Orcamento
		*/
		function Deleta_Itens_Orcamento ($idorcamento){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// deleta os produtos
			$delete_produtos_sql = "	DELETE FROM
																	{$conf['db_name']}orcamento_produto
																WHERE
																	 idorcamento = $idorcamento ";
			$delete_produtos_q = $db->query($delete_produtos_sql);
			//---------------------


			if($delete_produtos_q){
				return(1);
			}
			else{
				$this->err = $falha['excluir'];
				return(0);
			}


		}



		/**
		  método: Seleciona_Produtos_Do_Orcamento
		  propósito: Seleciona_Produtos_Do_Orcamento
		*/

		function Seleciona_Produtos_Do_Orcamento($idorcamento){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			
			global $flags;
			//---------------------

			$list_sql = "	SELECT
											PO.*, PRD.*, ORCT.tipoPreco, PO.cst_produto as cst_produto_orcamento
										FROM
           						{$conf['db_name']}orcamento_produto PO
												 INNER JOIN {$conf['db_name']}produto PRD ON PO.idproduto=PRD.idproduto
													INNER JOIN {$conf['db_name']}orcamento ORCT ON PO.idorcamento=ORCT.idorcamento

										WHERE
											PO.idorcamento = $idorcamento

										ORDER BY
												PRD.descricao_produto ASC ";

			//manda fazer a paginação
			$list_q = $db->query($list_sql);

			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					//insere um índice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);


					if ($list['qtd_produto'] != "") $list['qtd_produto'] = number_format($list['qtd_produto'],2,",","");
					if ($list['desconto_produto'] != "") $list['desconto_produto'] = number_format($list['desconto_produto'],2,",","");
					if ($list['aliquota_icms_produto'] != "") $list['aliquota_icms_produto'] = number_format($list['aliquota_icms_produto'],2,",","");

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
		  método: getById
		  propósito: busca informações
		*/
		function getById($idorcamento){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											ORCT.*,FL.*,FNC.*
										FROM
											{$conf['db_name']}orcamento ORCT
											INNER JOIN {$conf['db_name']}filial FL ON ORCT.idfilial = FL.idfilial
											INNER JOIN {$conf['db_name']}funcionario FNC ON ORCT.idfuncionario = FNC.idfuncionario
										WHERE
											 ORCT.idorcamento = $idorcamento ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				
				if ($get['datahoraCriacao'] != '0000-00-00 00:00:00') { 
					$array = split(" ",$get['datahoraCriacao']); 
					$get['datahoraCriacao_D'] = $form->FormataDataParaExibir($array[0]); 
					$get['datahoraCriacao_H'] = $array[1]; 
				} 
				if ($get['datahoraUltEmissao'] != '0000-00-00 00:00:00') { 
					$array = split(" ",$get['datahoraUltEmissao']); 
					$get['datahoraUltEmissao_D'] = $form->FormataDataParaExibir($array[0]); 
					$get['datahoraUltEmissao_H'] = $array[1]; 
				} 
				if ($get['datahoraCriacaoNF'] != '0000-00-00 00:00:00') { 
					$array = split(" ",$get['datahoraCriacaoNF']); 
					$get['datahoraCriacaoNF_D'] = $form->FormataDataParaExibir($array[0]); 
					$get['datahoraCriacaoNF_H'] = $array[1]; 
				} 
				
				if ($get['desconto'] != "") $get['desconto'] = $form->FormataMoedaParaExibir($get['desconto']);
				if ($get['frete'] != "") $get['frete'] = $form->FormataMoedaParaExibir($get['frete']);
				if ($get['outras_despesas'] != "") $get['outras_despesas'] = $form->FormataMoedaParaExibir($get['outras_despesas']);
				
				if ($get['base_calculo_icms'] != "") $get['base_calculo_icms'] = $form->FormataMoedaParaExibir($get['base_calculo_icms']);
				if ($get['valor_icms'] != "") $get['valor_icms'] = $form->FormataMoedaParaExibir($get['valor_icms']);
				if ($get['base_calc_icms_sub'] != "") $get['base_calc_icms_sub'] = $form->FormataMoedaParaExibir($get['base_calc_icms_sub']);
				if ($get['valor_icms_sub'] != "") $get['valor_icms_sub'] = $form->FormataMoedaParaExibir($get['valor_icms_sub']);
				if ($get['valor_seguro'] != "") $get['valor_seguro'] = $form->FormataMoedaParaExibir($get['valor_seguro']);
				if ($get['valor_total_ipi'] != "") $get['valor_total_ipi'] = $form->FormataMoedaParaExibir($get['valor_total_ipi']);
				if ($get['valor_total_produtos'] != "") $get['valor_total_produtos'] = $form->FormataMoedaParaExibir($get['valor_total_produtos']);
				if ($get['valor_total_nota'] != "") $get['valor_total_nota'] = $form->FormataMoedaParaExibir($get['valor_total_nota']);

				// formata o numero do orçamento
				$idorcamento_formatado = "0000000" . $idorcamento;
				$get['idorcamento_formatado'] = substr($idorcamento_formatado,strlen($idorcamento_formatado)-7,7);
				//---------------------------------------------------------------------------

				// formata o numero da nota fiscal
				$numeroDaNota = "000000" . $get['numeroNota'];
				$get['numeroNotaFormatado'] = substr($numeroDaNota,strlen($numeroDaNota)-6,6);
				//---------------------------------------------------------------------------


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
			
			if ($ordem == "") $ordem = " ORDER BY ORCT.datahoraCriacao DESC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT
											ORCT.*   , MOTC.descricao , FNC_NF.nome_funcionario as funcionario_emitiu_NF , FNC_ULT.nome_funcionario as funcionario_ultima_alteracao , FNC_CR.nome_funcionario as funcionario_criou_orcamento,  CLI.nome_cliente , FLI.nome_filial , CFOP.descricao as descricao_cfop, CFOP.codigo as codigo_cfop
										FROM
           						{$conf['db_name']}orcamento ORCT
												 LEFT OUTER JOIN {$conf['db_name']}motivo_cancelamento MOTC ON ORCT.idmotivo_cancelamento=MOTC.idmotivo_cancelamento
												 LEFT OUTER JOIN {$conf['db_name']}funcionario FNC_NF ON ORCT.idfuncionarioNF=FNC_NF.idfuncionario
												 INNER JOIN {$conf['db_name']}funcionario FNC_ULT ON ORCT.idUltfuncionario=FNC_ULT.idfuncionario
												 INNER JOIN {$conf['db_name']}funcionario FNC_CR ON ORCT.idfuncionario=FNC_CR.idfuncionario
												 LEFT OUTER JOIN {$conf['db_name']}cliente CLI ON ORCT.idcliente=CLI.idcliente
												 INNER JOIN {$conf['db_name']}filial FLI ON ORCT.idfilial=FLI.idfilial
												 LEFT OUTER JOIN {$conf['db_name']}cfop CFOP ON ORCT.idcfop=CFOP.idcfop

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
					
					if ($list['idcliente'] == "") $list['cliente_descricao'] = $list['nomeClienteProv'];
					else $list['cliente_descricao'] = $list['nome_cliente'];

					
					if ($list['datahoraCriacao'] != '0000-00-00 00:00:00') { 
						$array = split(" ",$list['datahoraCriacao']); 
						$list['datahoraCriacao'] = $form->FormataDataParaExibir($array[0]) . " " . $array[1]; 
					} 
					else $list['datahoraCriacao'] = "";

					if ($list['datahoraUltEmissao'] != '0000-00-00 00:00:00') {
						$array = split(" ",$list['datahoraUltEmissao']); 
						$list['datahoraUltEmissao'] = $form->FormataDataParaExibir($array[0]) . " " . $array[1]; 
					} 
					else $list['datahoraUltEmissao'] = "";

					if ($list['datahoraCriacaoNF'] != '0000-00-00 00:00:00') {
						$array = split(" ",$list['datahoraCriacaoNF']); 
						$list['datahoraCriacaoNF'] = $form->FormataDataParaExibir($array[0]) . " " . $array[1]; 
					} 
					else $list['datahoraCriacaoNF'] = "";
				
					if ($list['desconto'] != "") $list['desconto'] = number_format($list['desconto'],2,",",""); 
					
					if ($list['tipoPreco'] == 'B') $list['tipoPreco'] = "Balcão"; 
					else if ($list['tipoPreco'] == 'O') $list['tipoPreco'] = "Oferta"; 
					else if ($list['tipoPreco'] == 'A') $list['tipoPreco'] = "Atacado"; 
					else if ($list['tipoPreco'] == 'T') $list['tipoPreco'] = "Telemarketing"; 

					if ($list['tipoOrcamento'] == 'O') $list['tipoOrcamento'] = "Orçamento"; 
					else if ($list['tipoOrcamento'] == 'NF') $list['tipoOrcamento'] = "Notal Fiscal"; 
					else if ($list['tipoOrcamento'] == 'SD') $list['tipoOrcamento'] = "Série D"; 
					else if ($list['tipoOrcamento'] == 'ECF') $list['tipoOrcamento'] = "Cupom Fiscal"; 

					if ($list['valor_total_nota'] != "") $list['valor_total_nota'] = $form->FormataMoedaParaExibir($list['valor_total_nota']);
					
					// formata o numero do orçamento
					$idorcamento_formatado = "0000000" . $list['idorcamento'];
					$list['idorcamento_formatado'] = substr($idorcamento_formatado,strlen($idorcamento_formatado)-7,7);
					//---------------------------------------------------------------------------

					// formata o numero da nota fiscal
					$numeroDaNota = "000000" . $list['numeroNota'];
					$list['numeroNotaFormatado'] = substr($numeroDaNota,strlen($numeroDaNota)-6,6);
					//---------------------------------------------------------------------------


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
		                  {$conf['db_name']}orcamento
		                    (
		                    
											  idfilial,
											  idmotivo_cancelamento,
											  idfuncionario,
											  idUltfuncionario,
											  idfuncionarioNF,
											  idcliente,
											  idcfop,
											  idtransportador,
												tipoPreco,
											  tipoOrcamento,
											  numeroNota,
											  datahoraCriacao,
											  datahoraUltEmissao,
											  datahoraCriacaoNF,
											  validade,
											  desconto,
												nomeClienteProv,
											  infoClienteProv,
												valor_total_nota,
											  obs,
											  imprimiuNotaFiscal,
											  jurosParcelamento,
											  transportador_placa_veiculo,
											  transportador_frete_por_conta,
											  transportador_quantidade,
											  transportador_especie,
											  transportador_marca,
											  transportador_numero,
											  transportador_peso_bruto,
											  transportador_peso_liquido,
											  dados_adicionais,
												frete,
												outras_despesas,
												valor_seguro,
												valor_total_ipi,
												base_calculo_icms,
												valor_icms,
												base_calc_icms_sub,
												valor_icms_sub,
												valor_total_produtos,
												serieNota,
												modeloNota,
												numero_parcelas_cr,
												dias_entre_parcelas_cr,
												data_parcela_1_cr,
												sigla_modo_recebimento_prazo_cr

												)
		                VALUES
		                    (
		                    
											  " . $info['idfilial'] . ",
											  " . $info['idmotivo_cancelamento'] . ",
											  " . $info['idfuncionario'] . ",
											  " . $info['idUltfuncionario'] . ",
											  " . $info['idfuncionarioNF'] . ",
											  " . $info['idcliente'] . ",
											  " . $info['idcfop'] . ",
											  " . $info['idtransportador'] . ",
												'" . $info['tipoPreco'] . "',
											  '" . $info['tipoOrcamento'] . "',
											  " . $info['numeroNota'] . ",
											  '" . $info['datahoraCriacao'] . "',
											  '" . $info['datahoraUltEmissao'] . "',
											  '" . $info['datahoraCriacaoNF'] . "',
											  " . $info['validade'] . ",
											  " . $info['desconto'] . ",
												'" . $info['nomeClienteProv'] . "',
											  '" . $info['infoClienteProv'] . "',
												" . $info['valor_total_nota'] . ",
											  '" . $info['obs'] . "',
											  '" . $info['imprimiuNotaFiscal'] . "',
											  " . $info['jurosParcelamento'] . ",
											  '" . $info['transportador_placa_veiculo'] . "',
											  '" . $info['transportador_frete_por_conta'] . "',
											  '" . $info['transportador_quantidade'] . "',
											  '" . $info['transportador_especie'] . "',
											  '" . $info['transportador_marca'] . "',
											  '" . $info['transportador_numero'] . "',
											  '" . $info['transportador_peso_bruto'] . "',
											  '" . $info['transportador_peso_liquido'] . "',
											  '" . $info['dados_adicionais'] . "',
												" . $info['frete'] . ",
												" . $info['outras_despesas'] . ",
											  " . $info['valor_seguro'] . ",
											  " . $info['valor_total_ipi'] . ",
											  " . $info['base_calculo_icms'] . ",
											  " . $info['valor_icms'] . ",
												" . $info['base_calc_icms_sub'] . ",
												" . $info['valor_icms_sub'] . ",
											  " . $info['valor_total_produtos'] . ",
												'" . $info['serieNota'] . "',
												'" . $info['modeloNota'] . "',
												" . $info['numero_parcelas_cr'] . ",
												" . $info['dias_entre_parcelas_cr'] . ",
												'" . $info['data_parcela_1_cr'] . "',													
												'" . $info['sigla_modo_recebimento_prazo_cr'] . "'

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
		function update($idorcamento, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}orcamento
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
			$update_sql .= " WHERE  idorcamento = $idorcamento ";


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
		function delete($idorcamento){
			
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
													{$conf['db_name']}orcamento
												WHERE
													 idorcamento = $idorcamento ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY datahoraCriacao DESC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}orcamento
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
