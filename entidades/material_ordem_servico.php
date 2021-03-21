<?php

class material_ordem_servico {

	var $err;


	/**
	 método: getById
	 propósito: busca informações
		*/
	function getById($idmaterial){

		// variáveis globais
		global $form, $conf, $db, $falha;
		//---------------------
			
		$get_sql = "	SELECT
							MOS.*
						FROM
							{$conf['db_name']}material_ordem_servico MOS
						WHERE
							MOS.idmaterial = $idmaterial ";

		//executa a query no banco de dados
		$get_q = $db->query($get_sql);

		//testa se a consulta foi bem sucedida
		if($get_q){ //foi bem sucedida

			$get = $db->fetch_array($get_q);

			if ($get['qtd_material']   != "") $get['qtd_material']   = number_format($get['qtd_material'],2,",","");
			if ($get['valor_unitario'] != "") $get['valor_unitario'] = number_format($get['valor_unitario'],2,",","");
			
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
			
		if ($ordem == "") $ordem = " ORDER BY MOS.idordem_servico ASC";
			
		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------
			
		$list_sql = "	SELECT
		MOS.*   , ORS.descricao_ordem , FORN.nome_fornecedor , UVD.sigla_unidade_venda
		FROM
		{$conf['db_name']}material_ordem_servico MOS
		INNER JOIN {$conf['db_name']}ordem_servico ORS ON MOS.idordem_servico=ORS.idordem_servico
		INNER JOIN {$conf['db_name']}fornecedor FORN ON MOS.idfornecedor=FORN.idfornecedor
		INNER JOIN {$conf['db_name']}unidade_venda UVD ON MOS.idunidade_venda=UVD.idunidade_venda

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
					
					
					
				if ($list['qtd_material'] != "") $list['qtd_material'] = number_format($list['qtd_material'],2,",","");
				if ($list['valor_unitario'] != "") $list['valor_unitario'] = number_format($list['valor_unitario'],2,",","");
					
					


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



	function make_list_os($idordem_servico){
			
		global $form, $conf, $db, $falha;
		//---------------------


		$set_sql = "  SELECT MOS.*, PRD.descricao_produto, FRN.nome_fornecedor, UVN.sigla_unidade_venda
		FROM {$conf['db_name']}material_ordem_servico MOS
		INNER JOIN produto       PRD USING(idproduto)
		INNER JOIN unidade_venda UVN USING(idunidade_venda)
		INNER JOIN fornecedor    FRN USING(idfornecedor)
		WHERE
		idordem_servico = $idordem_servico";
			
		if($list_q = $db->query($set_sql)){

			//busca os registros no banco de dados e monta o vetor de retorno
			$list_return = array();
			$valor_total_os = 0.00;
			$cont = 0;
			while($list = $db->fetch_array($list_q)){

				$valor_total_os += ($list['qtd_produto'] * $list['valor_unitario']);
				
				$list['valor_total'] = $form->FormataMoedaParaExibir($list['qtd_produto'] * $list['valor_unitario']);
				$list['qtd_produto'] = $form->FormataMoedaParaExibir($list['qtd_produto']);
				$list['valor_unitario'] = $form->FormataMoedaParaExibir($list['valor_unitario']);

				$list_return[] = $list;
				 
				$cont++;
			}

			return array($list_return, $form->FormataMoedaParaExibir($valor_total_os));
				
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
		{$conf['db_name']}material_ordem_servico
		(

		idordem_servico,
		idfornecedor,
		numero_nf,
		idproduto,
		qtd_produto,
		valor_unitario

		)
		VALUES
		(

		" . $info['idordem_servico'] . ",
		" . $info['idfornecedor'] . ",
	   '" . $info['numero_nf'] . "',
		" . $info['idproduto'] . ",
		" . $info['qtd_produto'] . ",
		" . $info['valor_unitario'] . "
			
		)";

		//executa a query e testa se a consulta foi "boa"
		if($db->query($set_sql)){
			//retorna o código inserido
			$codigo = $db->insert_id($set_sql);

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
	function update($idmaterial, $info){
			
		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------
			
		//inicializa a query
		$update_sql = "	UPDATE
		{$conf['db_name']}material_ordem_servico
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
		$update_sql .= " WHERE  idmaterial = $idmaterial ";
			
			
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
	function delete($idmaterial){
			
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
			{$conf['db_name']}material_ordem_servico
			WHERE
			idmaterial = $idmaterial ";
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
	
	
	function atualizaMateriaisOs($idordem_servico, $list_produtos){
		// variáveis globais
		global $form, $conf, $db, $falha;
		$err = false;
		
		if($this->deleteAll_OS($idordem_servico)){

			if(!count($list_produtos)) return true;

			$valor_total = 0.00;
			foreach($list_produtos as $idproduto => $dadosProduto){
		
				$valor_unitario = $form->FormataMoedaParaInserir($dadosProduto['valor_unitario']);
				$qtd_produto	= $form->FormataMoedaParaInserir($dadosProduto['qtd_produto']);
				$valor_total   += ($valor_unitario * $qtd_produto);
				
				$this->set(array(	'idordem_servico'=> $idordem_servico,
									'idfornecedor' 	 => ( empty($dadosProduto['idfornecedor']) ? 'NULL' : (int)$dadosProduto['idfornecedor']),
								  	'numero_nf' 	 => $db->escape($dadosProduto['numero_nf']),
								  	'idproduto' 	 => (int)$idproduto,
									'qtd_produto' 	 => $qtd_produto,
									'valor_unitario' => $valor_unitario
				));

				
				if(count($this->err)){
					$err = true;
					break;
				}
			}
				
		}
		else{

			$err = true;
		}
		
		
		if(!$err){
			
			return $valor_total;
		}
		else{
			
			$this->err = 'Falha ao atualizar a lista de materiais.';
			return false;
		}
		
		
	}
	
	
	function deleteAll_OS($idordem_servico){
			
		// variáveis globais
		global $form, $conf, $db, $falha;

		$delete_sql = "	DELETE FROM
							{$conf['db_name']}material_ordem_servico
						WHERE
							idordem_servico = $idordem_servico ";
							
		$delete_q = $db->query($delete_sql);

		if($delete_q){
			return(1);
		}
		else{
			$this->err = $falha['excluir'];
			return(0);
		}

	
	}


	/**
	 método: make_list
	 propósito: faz a listagem para colocar no select
		*/

	function make_list_select( $filtro = "", $ordem = " ORDER BY idordem_servico ASC") {
			
		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------
			

		$list_sql = "	SELECT
		*
		FROM
		{$conf['db_name']}material_ordem_servico
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
