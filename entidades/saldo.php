<?php

class saldo {

	var $err;

	/**
	 * construtor da classe
	 */
	function saldo(){
		// não faz nada
	}


	/**
	 * Busca saldo de um cliente na data passada como parâmetro
	 * @param integer $idcliente - ID do cliente
	 * @param string $data - Data para a qual o saldo será pesquisado
	 */
	function getById($idcliente, $data=''){

		// variáveis globais
		global $form, $conf, $db, $falha;
		//---------------------
			
		if(!empty($data)){
			$get_sql = "SELECT * FROM saldo WHERE idcliente = $idcliente AND data = '$data' ";
		}else{
			//Se data estiver vazio, pega a última disponível
			$get_sql = "SELECT *  FROM `saldo` WHERE idcliente = $idcliente ORDER BY data DESC LIMIT 1";
		}
		

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
	 método: make_list
	 propósito: faz a listagem
		*/

	function make_list( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
			
		if ($ordem == "") $ordem = " ORDER BY EST.nome_estado ASC, CID.nome_cidade ASC, BAR.nome_bairro ASC ";
			
		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------
			
		$list_sql = "	SELECT
							BAR.*   , CID.nome_cidade , EST.nome_estado
						FROM
							{$conf['db_name']}bairro BAR
						 INNER JOIN {$conf['db_name']}cidade CID ON BAR.idcidade=CID.idcidade 
						 INNER JOIN {$conf['db_name']}estado EST ON CID.idestado=EST.idestado
						
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
						{$conf['db_name']}saldo
		                (
		                    
							idcliente, 
							data,
							saldo  
						
						)
		                VALUES(
		                    
		                    " . $info['idcliente'] . ",  
							'" . $info['data'] . "',
							" . $info['saldo'] . "
							   
												
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
	 3) campos literais deverão ter o prefixo lit e campos numéricos deverão ter o prefixo nu
		*/
	function update($idbairro, $info){
			
		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------
			
		//inicializa a query
		$update_sql = "	UPDATE {$conf['db_name']}saldo SET ";

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
		$update_sql .= " WHERE  idbairro = $idbairro ";
			
			
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
	function delete($idcliente, $idsaldo){
			
		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------
			


		$delete_sql = "	DELETE FROM
		{$conf['db_name']}saldo
						WHERE
							 idcliente = $idcliente
							 AND data = '$data' ";
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

	function make_list_select( $filtro = "", $ordem = " ORDER BY idcidade ASC") {
			
		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------
			

		$list_sql = "	SELECT * FROM {$conf['db_name']}saldo
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


	function atualizaSaldo($data, $idcliente, $dataInicio = '2016-01-01'){

		// variáveis globais
		global $form, $conf, $db, $falha;
		
		//Inicializa as variáveis de somatório
		$somaCreditos = $somaDebitos = $saldoAnterior = 0.00;
		

		$sql_q = " SELECT  
						saldo, data 
				   FROM  
				   		{$conf['db_name']}saldo 
				   WHERE 
				   	(idcliente = $idcliente AND data = '$data') OR 
				   	data < '$data' AND data > '$dataInicio' LIMIT 1 ";

// echo "\n"; var_dump($sql_q); echo "\n";	

		$sql_res = $db->query($sql_q);
		$info = $db->fetch_all($sql_res);

		
		if($db->num_rows($sql_res) == 0){
 			//echo 'Nao há saldo a ser calculado';

			return true; //Nao hà saldo a ser calculado
			
		}
		elseif($info[0]['data'] != $data ){

			//Faz a chamada recursiva 
			$date = new DateTime($data);
			$date->modify('-1 day');
			$novaData = $date->format('Y-m-d');	

			$this->atualizaSaldo($novaData, $idcliente, $dataInicio);

			//Soma os débitos do cliente
			$somaDebt_sql = "SELECT sum(valor_movimento) as valor FROM {$conf['db_name']}movimento 
								WHERE
									( data_baixa BETWEEN '$data 00:00:00' AND '$data 23:59:59' ) AND
									idcliente_origem = $idcliente AND
									baixado = '1' 
								GROUP BY idcliente_origem";
			$somaDebt_res = $db->query($somaDebt_sql);
			if($db->num_rows($somaDebt_res) > 0) $somaDebitos = $db->result($somaDebt_res,0,'valor');

			
			//Soma os créditos do cliente
			$somaCredt_sql = "SELECT sum(valor_movimento) as valor FROM {$conf['db_name']}movimento 
							  WHERE
								( data_baixa BETWEEN '$data 00:00:00' AND '$data 23:59:59' ) AND
									idcliente_destino = $idcliente AND
									baixado = '1' 
								GROUP BY idcliente_destino";
			$somaCredt_res = $db->query($somaCredt_sql);
			if($db->num_rows($somaCredt_res) > 0) $somaCreditos = $db->result($somaCredt_res,0,'valor');


 
			
			//Recupera o saldo do dia anterior			
			$saldoAnterior = $this->getById($idcliente, $novaData);

			
			$somaMovimentos = $somaCreditos - $somaDebitos;
			$novoSaldo = $saldoAnterior['saldo'] + $somaMovimentos;

// echo "\nSaldo do dia $novaData : {$saldoAnterior['saldo']} - Soma dos movimentos do dia $data: $somaMovimentos - Saldo final do dia $data: $novoSaldo";
// flush();
/*echo "\n";
var_dump($somaDebt_sql);
echo "\n";
var_dump($somaCredt_sql);
*/
      /*
			if($novoSaldo != 0){			
			
				$sql_res = $db->query("SELECT saldo FROM {$conf['db_name']}saldo WHERE idcliente = $idcliente AND data = '$data'");
				if($db->num_rows($sql_res)) 
					$sqlNovoSaldo = "UPDATE {$conf['db_name']}saldo SET saldo = $novoSaldo WHERE idcliente = $idcliente AND data = '$data'";
				else
					$sqlNovoSaldo = "INSERT INTO {$conf['db_name']}saldo(idcliente, data, saldo) VALUES ($idcliente, '$data', $novoSaldo )"; 
			}
      */
	
	     	$sql_res = $db->query("SELECT saldo FROM {$conf['db_name']}saldo WHERE idcliente = $idcliente AND data = '$data'");
	     	
			if($db->num_rows($sql_res)) 
				$sqlNovoSaldo = "UPDATE {$conf['db_name']}saldo SET saldo = $novoSaldo WHERE idcliente = $idcliente AND data = '$data'";
			else
				$sqlNovoSaldo = "INSERT INTO {$conf['db_name']}saldo(idcliente, data, saldo) VALUES ($idcliente, '$data', $novoSaldo )";
	
			if($db->query($sqlNovoSaldo)){
				return true;			
			}
			else{
				$this->err = $falha['inserir'];
				return false;
			}	
		}		
	}


	/**
	 * Gera relatório de saldos dos clientes
	 * @param integer $idCliente - ID do cliente cujo saldo deve ser buscado. Caso seja null todos os clientes são buscados
	 * @param string $tipoCliente - Tipo de cliente que deve ser pesquisado: 
	 *								T (todos), C (condomínios), CA (condomínios adm financeira)
	 * @return array $dadosRelatorio - array contendo as chaves:
	 *									'data': Data do saldo mais recente, referência no relatório
	 *									'saldosClientes': Array contendo ID, nome do cliente saldo
	 *									'saldoTotal': soma de todos os saldos encontrados
	 **/
	public function geraRelatorio( $idCliente, $tipoCliente){

		require_once dirname(__FILE__) . '/cliente_condominio.php';

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;

		$cliente_condominio = new cliente_condominio();
		//---------------------

		/// Soma de todos os saldos encontrados
		$saldoTotal = 0.0;

		if($idCliente){
			/// Se o cliente for informado, busca 
			$filtro .= " saldo.idCliente = $idCliente ";
			$orderby = ' data DESC ';
			$limit = ' LIMIT 1 ';
		}
		else{

			/// Obtém data mais recente de cálculo de saldo
			$hoje = date('Y-m-d');

			$get_sql = "SELECT max(data) as data FROM saldo WHERE data <= '$hoje' ";
			$get_q = $db->query($get_sql);
			$get = $db->fetch_array($get_q);
			$data = $get['data'];

			$filtro = " data = '$data' ";
			$orderby = ' cliente.nome_cliente ';
			$limit = '';
		}

		/// Verifica se precisa filtrar por tipo de cliente e incluir na cláusula where
		switch($tipoCliente){
			case 'C':
				$idsCondominios = $cliente_condominio->buscaIdsCondominios(false);
				$filtro .= ' AND saldo.idcliente IN (' . implode(',',$idsCondominios) . ')';

			break;
			case 'CA':
				$idsCondominios = $cliente_condominio->buscaIdsCondominios(true);
				$filtro .= ' AND saldo.idcliente IN (' . implode(',',$idsCondominios) . ')';
			break;
		}

		$list_sql = "SELECT saldo.*, cliente.nome_cliente FROM saldo 
					LEFT JOIN cliente ON (saldo.idcliente = cliente.idcliente)
					WHERE $filtro 
					ORDER BY $orderby
					$limit";


		$list_q = $db->query($list_sql);

		if($list_q){

		 	//busca os registros no banco de dados e monta o vetor de retorno
		 	$list_return = array();
		 	$cont = 0;
		 	while($list = $db->fetch_array($list_q)){

		 		//insere um índice na listagem
		 		$list['index'] = $cont+1;
		 		$saldoTotal += $list['saldo'];
		 		$list['saldo'] = $form->FormataMoedaParaExibirPontuacao($list['saldo']);
		 		$list_return[] = $list;
		 		$cont++;

		 	}

			$data = $form->FormataDataHoraParaExibir($list_return[0]['data']);

			$saldoTotal = $form->FormataMoedaParaExibirPontuacao($saldoTotal);

			$dadosRelatorio = array('data' => $data, 'saldosClientes' => $list_return, 'saldoTotal' => $saldoTotal);

		 	return $dadosRelatorio;
		}
		else{
		 	$this->err = $falha['listar'];
		 	return(0);
		}
	}


} // fim da classe
?>
