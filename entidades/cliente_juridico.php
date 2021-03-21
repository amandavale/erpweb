<?php
	
class cliente_juridico {
	
	var $err;
	
	/**
	 * construtor da classe
	 */
	Function cliente_juridico(){
		// não faz nada
	}
	
	
	/**
	 método: Filtra_Cliente_AJAX
	 propósito: Filtra_Cliente_AJAX
	 */
	
	function Filtra_Cliente_AJAX ( $filtro, $campoID, $mostraDetalhes ) {
		
		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------
		
		// verifica qual a pagina atual
		if (!isset($_GET["page"])) $pg = 0;
		else $pg = $_GET["page"];
		
		// maximo numero de registros listados
		$rppg = $conf['rppg_auto_completar'];
		
		// volta o filtro para a codificação original
		$filtro = utf8_decode($filtro);
		$campoID = utf8_decode($campoID);
		$mostraDetalhes = utf8_decode($mostraDetalhes);
		
		// campos de controle
		$campoNomeTemp = $campoID . "_NomeTemp";
		$campoFlag = $campoID . "_Flag";
		
		$list_sql = "	SELECT
			CLI.*, JCLI.cnpj_cliente, EDR.*, BAR.*, CID.*, EST.*
			FROM
			{$conf['db_name']}cliente CLI
			INNER JOIN {$conf['db_name']}cliente_juridico JCLI ON CLI.idcliente=JCLI.idcliente
			LEFT OUTER JOIN {$conf['db_name']}endereco EDR ON CLI.idendereco_cliente=EDR.idendereco
			LEFT OUTER JOIN {$conf['db_name']}bairro BAR ON EDR.idbairro=BAR.idbairro
			LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade=CID.idcidade
			LEFT OUTER JOIN {$conf['db_name']}estado EST ON EDR.idestado=EST.idestado
			
			WHERE
				(
					UPPER(CLI.idcliente) LIKE UPPER('%{$filtro}%')
					OR
					UPPER(CLI.nome_cliente) LIKE UPPER('%{$filtro}%')
					OR
					UPPER(JCLI.cnpj_cliente) LIKE UPPER('%{$filtro}%')
				)
				
				AND CLI.cliente_bloqueado <> '1'
				
			ORDER BY
			CLI.nome_cliente ASC ";
		
		
		//manda fazer a paginação
		$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);
		
		
		if($list_q){
			
			// testa se retornou algum registro
			if ($db->num_rows($list_q) > 0) {
				
				?>
					<table width="100%" cellpadding="5" cellspacing="2">
						<tr onselect="" class="cabecalho">
							<td width="10%" class="cabecalho_negrito"><?php echo utf8_encode('Código'); ?></td>
							<td width="60%" class="cabecalho_negrito"><?php echo utf8_encode('Cliente'); ?></td>
							<td class="cabecalho_negrito" align="center"><?php echo utf8_encode('CPF/CNPJ'); ?></td>
						</tr>
					<?php
					
			$filtro = htmlentities($filtro);
			$cont = 0;
			while($list = $db->fetch_array($list_q)){
				
				//insere um índice na listagem
				$list['index'] = $cont+1;
				if ($mostraDetalhes == 1) {
					$list['info_cliente'] = "
						<table width=95% align=center>
						<tr>
						<th align=center>Cliente</th>
						<th align=center>Cidade</th>
						<th align=center>Telefone</th>
						</tr>
						<tr>
						<td><a class=menu_item href=" . $conf['addr'] . "/admin/cliente_juridico.php?ac=editar&idcliente=" . $list['idcliente'] . ">" . htmlentities($list['nome_cliente']) . "</a></td>
						<td><a class=menu_item href=" . $conf['addr'] . "/admin/cliente_juridico.php?ac=editar&idcliente=" . $list['idcliente'] . ">" . htmlentities($list['nome_cidade']) . " / " . $list['sigla_estado'] . "</a></td>
						<td><a class=menu_item href=" . $conf['addr'] . "/admin/cliente_juridico.php?ac=editar&idcliente=" . $list['idcliente'] . ">" . $list['telefone_cliente'] . "</a></td>
						</tr>
						
						</table>";
					
					$list['info_cliente'] = ereg_replace("(\r\n|\n|\r)", "", $list['info_cliente']);
				}
	
	
				if ($list['cpf_cliente'] != "") $list['cpf_cnpj'] = $list['cpf_cliente'];
				else $list['cpf_cnpj'] = $list['cnpj_cliente'];
				
				if ($list['cliente_bloqueado'] == "1") $list['cliente_bloqueado'] = "SIM";
				else $list['cliente_bloqueado'] = "NÃO";
				
				if (strlen($filtro) > 1 )$list['nome_cliente'] = htmlentities($list['nome_cliente']);
				
				// coloca em negrito a string que foi encontrada na palavra
				$list['idcliente_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['idcliente']);
				$list['nome_cliente_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['nome_cliente']);
				$list['cpf_cnpj_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['cpf_cnpj']);
				
				?>
						<tr onselect="
							this.text.value = '<?php echo utf8_encode($list['nome_cliente']); ?>';
							$('<?php echo $campoNomeTemp; ?>').value = '<?php echo utf8_encode($list['nome_cliente']); ?>';
							$('<?php echo $campoID; ?>').value = '<?php echo utf8_encode($list['idcliente']); ?>';
							$('<?php echo $campoFlag; ?>').className = 'selecionou';

							<?php if ($mostraDetalhes == 1) ?>
							$('dados_cliente').innerHTML = '<?php echo utf8_encode($list['info_cliente']); ?>';

						">
							<td class="tb_bord_baixo"><?php echo ($list['idcliente_negrito']); ?></td>
							<td class="tb_bord_baixo"><?php echo utf8_encode($list['nome_cliente_negrito']); ?></td>
							<td class="tb_bord_baixo" align="center">&nbsp;<?php echo utf8_encode($list['cpf_cnpj_negrito']); ?></td>
						</tr>
						<?php
				
				$cont++;
				}
				
				// verifica a paginação
				$paginacao = "";
				if ($pg > 0) $paginacao .= "<a href='?page=" . ($pg - 1) . "' style='float:left' class='page_up'>" . utf8_encode('Anterior') . "</a>";
				$paginacao .= "<a href='?page=" . ($pg + 1) .  "' style='float:right'  class='page_down'>" . utf8_encode('Pr&oacute;ximo') . "</a>";
				
				}
				// Nenhum registro foi encontrado
				else {
					?>
					<table width="100%" cellpadding="5" cellspacing="2">
						<tr onselect="" class="cabecalho">
							<td width="70%" class="cabecalho_negrito"><?php echo utf8_encode($conf['listar']); ?></td>
						</tr>
					<?php

					// verifica a paginação
					$paginacao = "";
					if ($pg > 0) $paginacao .= "<a href='?page=" . ($pg - 1) . "' style='float:left' class='page_up'>" . utf8_encode('Anterior') . "</a>";
					}
					
					}
					else{
								?>
						<table width="100%" cellpadding="5" cellspacing="2">
							<tr onselect="" class="cabecalho">
								<td width="70%" class="cabecalho_negrito"><?php echo utf8_encode($falha['listar']); ?></td>
							</tr>
						<?php
					}
					
					

					// Encerra a tabela e coloca a paginação
					echo "</table>";
					if ($paginacao != "") echo $paginacao;
					
					}



/**
 método: Verifica_CNPJ_Duplicado
 propósito: Verifica_CNPJ_Duplicado
 */
Function Verifica_CNPJ_Duplicado($CNPJ, $idcliente = ""){
	
	// variáveis globais
	global $form;
	global $conf;
	global $db;
	global $falha;
	//---------------------
	
	$filtro = "";
	
	if ($idcliente != "") $filtro = " AND JCLI.idcliente <> $idcliente ";
	
	$get_sql = "	SELECT
		JCLI.*
		FROM
		{$conf['db_name']}cliente_juridico JCLI
		WHERE
		JCLI.cnpj_cliente = '$CNPJ'
		
		$filtro
		
		";
	
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
 método: AtualizaCliente
 propósito: AtualizaCliente
 */
Function AtualizaClienteJuridico($idcliente, $post){
	
	// variáveis globais
	global $form;
	global $conf;
	global $db;
	global $falha;
	//---------------------
	
	// retira os campos que nao estao na tabela clientes
	unset($post['litnome_cliente']);
	unset($post['numidramo_atividade']);
	unset($post['littelefone_cliente']);
	unset($post['litfax_cliente']);
	unset($post['litemail_cliente']);
	unset($post['litsite_cliente']);
	unset($post['litcliente_bloqueado']);
	unset($post['numidmotivo_bloqueio']);
	unset($post['litdata_bloqueio_cliente']);
	unset($post['numidendereco_cliente']);
	unset($post['litmesmo_endereco']);
	unset($post['numidendereco_cobranca']);
	unset($post['littelefone_cobranca']);
	unset($post['litobservacao_cliente']);
	unset($post['numvalor_contrato_cliente']);
	unset($post['litdata_cadastro_cliente']);
	unset($post['litconsumidor_final']);
	unset($post['litvencimento_boleto_cliente']);
	unset($post['littipo_cliente']);
	unset($post['litsenha_cliente']);
        
	$this->update($idcliente, $post);
	
	return(1);
	
}



/**
 método: getById
 propósito: busca informações
 */
Function getById($idcliente){
	
	// variáveis globais
	global $form;
	global $conf;
	global $db;
	global $falha;
	//---------------------
	
	$get_sql = "	SELECT
		JCLI.*
		FROM
		{$conf['db_name']}cliente_juridico JCLI
		WHERE
		JCLI.idcliente = $idcliente ";
	
	//executa a query no banco de dados
	$get_q = $db->query($get_sql);
	
	//testa se a consulta foi bem sucedida
	if($get_q){ //foi bem sucedida
		
		$get = $db->fetch_array($get_q);
		
		if ($get['data_nascimento_contato'] != '0000-00-00') $get['data_nascimento_contato'] = $form->FormataDataParaExibir($get['data_nascimento_contato']); 
		else $get['data_nascimento_contato'] = "";
		
		
		//Flag para indicar que o clietne é Pessoa jurídica
                $get['pessoa_juridica'] = true; 
		
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

Function make_list( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
	
	if ($ordem == "") $ordem = " ORDER BY JCLI.idcliente ASC";
	
	// variáveis globais
	global $form;
	global $conf;
	global $db;
	global $falha;
	//---------------------
	
	($filtro != "") ? ($filtro .= ' AND ') : ($filtro = ' WHERE ') ;
	
	$list_sql = "	SELECT
		JCLI.*   , CLI.nome_cliente, CLI.telefone_cliente,CLI.email_cliente
		FROM
		{$conf['db_name']}cliente_juridico JCLI 
		INNER JOIN {$conf['db_name']}cliente CLI ON JCLI.idcliente=CLI.idcliente 
		
		$filtro
		
		CLI.cliente_bloqueado <> '1'	
		
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
			
			if ($list['data_nascimento_contato'] != '0000-00-00') $list['data_nascimento_contato'] = $form->FormataDataParaExibir($list['data_nascimento_contato']); 
			else $list['data_nascimento_contato'] = "";
			
			
			$list['telefone_cliente'] = $form->FormataTelefoneParaExibir($list['telefone_cliente']);
			
			
			$list['celular_contato'] = $form->FormataTelefoneParaExibir($list['celular_contato']); 
			
			
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

Function set($info){
	
	// variáveis globais
	global $form;
	global $conf;
	global $db;
	global $falha;
	//---------------------
	
	
	$set_sql = "  INSERT INTO
		{$conf['db_name']}cliente_juridico
		(
		
		idcliente, 
		cnpj_cliente, 
		inscricao_estadual_cliente, 
		nome_fantasia, 
		nome_contato, 
		data_nascimento_contato, 
		celular_contato  
		
		)
		VALUES
		(
		
		" . $info['idcliente'] . ",  
		'" . $info['cnpj_cliente'] . "',  
		'" . $info['inscricao_estadual_cliente'] . "',  
		'" . $info['nome_fantasia'] . "',  
		'" . $info['nome_contato'] . "',  
		'" . $info['data_nascimento_contato'] . "',  
		'" . $info['celular_contato'] . "'   
		
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
Function update($idcliente, $info){
	
	// variáveis globais
	global $form;
	global $conf;
	global $db;
	global $falha;
	//---------------------
	
	//inicializa a query
	$update_sql = "	UPDATE
		{$conf['db_name']}cliente_juridico
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
	$update_sql .= " WHERE  idcliente = $idcliente ";
	
	
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
Function delete($idcliente){
	
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
			{$conf['db_name']}cliente_juridico
			WHERE
			idcliente = $idcliente ";
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

function make_list_select( $filtro = "", $ordem = " ORDER BY idcliente ASC") {
	
	// variáveis globais
	global $form;
	global $conf;
	global $db;
	global $falha;
	//---------------------
	
	
		$list_sql = "	SELECT
		*
		FROM
		{$conf['db_name']}cliente_juridico
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

function Busca_Generica ( $pg, $rppg, $busca = "", $ordem = "", $url = ""){
	
	if ($ordem == "") $ordem = " ORDER BY CLI.nome_cliente ";
	
	// variáveis globais
	global $form;
	global $conf;
	global $db;
	global $falha;
	//---------------------
	
	$list_sql = "	SELECT
		CLI.*, RAM.descricao_atividade, JCLI.*
		FROM
		{$conf['db_name']}cliente CLI
		INNER JOIN {$conf['db_name']}ramo_atividade RAM ON CLI.idramo_atividade=RAM.idramo_atividade
		INNER JOIN {$conf['db_name']}cliente_juridico JCLI ON CLI.idcliente=JCLI.idcliente
		WHERE
		(
		UPPER(RAM.descricao_atividade) LIKE UPPER('%{$busca}%') OR
		UPPER(CLI.nome_cliente) LIKE UPPER('%{$busca}%') OR
		UPPER(JCLI.cnpj_cliente) LIKE UPPER('%{$busca}%') OR
		UPPER(CLI.email_cliente) LIKE UPPER('%{$busca}%')
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
	
	if ($ordem == "") $ordem = " ORDER BY CLI.nome_cliente ";
	
	// variáveis globais
	global $form, $conf, $db, $falha;
	//---------------------
	
	
	if ($filtro_where != "") $filtro_where = " WHERE ( " . $filtro_where . " ) AND  CLI.cliente_bloqueado <> '1' ";
	else $filtro_where = "WHERE  CLI.cliente_bloqueado <> '1'";

			
	$list_sql = "	SELECT
		CLI.*, RAT.descricao_atividade, JCLI.*,
		EDR.*, BAR.nome_bairro, CID.nome_cidade, EST.sigla_estado
		
		FROM
		{$conf['db_name']}cliente CLI
		LEFT JOIN {$conf['db_name']}ramo_atividade RAT ON CLI.idramo_atividade=RAT.idramo_atividade
		INNER JOIN {$conf['db_name']}cliente_juridico JCLI ON CLI.idcliente=JCLI.idcliente
		
		LEFT  JOIN {$conf['db_name']}endereco EDR ON CLI.idendereco_cliente=EDR.idendereco
		LEFT  JOIN {$conf['db_name']}bairro BAR ON EDR.idbairro=BAR.idbairro
		LEFT  JOIN {$conf['db_name']}cidade CID ON EDR.idcidade=CID.idcidade
		LEFT  JOIN {$conf['db_name']}estado EST ON EDR.idestado=EST.idestado
		
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
			
			$list['endereco'] = null;
			if(!empty($list['numero'])) $list['logradouro'] .= ', '.$list['numero'];
			if(!empty($list['nome_bairro'])) $list['endereco'] .= $list['logradouro'];
			if(!empty($list['nome_bairro'])) $list['endereco'] .= ' <br> '.$list['nome_bairro'];
			if(!empty($list['nome_cidade'])) $list['endereco'] .= ' - '.$list['nome_cidade'] . '/'. $list['sigla_estado'] ;
	
			
			$list['telefone_cliente'] = $form->FormataTelefoneParaExibir($list['telefone_cliente']);
			$list['celular_contato'] = $form->FormataTelefoneParaExibir($list['celular_contato']);
			$list['valor_contrato_cliente'] = $form->FormataMoedaParaExibir($list['valor_contrato_cliente']);
			
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
