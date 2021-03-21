<?php

class cliente_condominio {

	var $err;

	/**
	 * construtor da classe
	 */
	Function cliente_condominio() {
		// não faz nada
	}

	/**
	 * método: Filtra_Cliente_AJAX
	 * propósito: Filtra_Cliente_AJAX
	 */
	function Filtra_Cliente_AJAX($filtro, $campoID, $mostraDetalhes) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------
		// verifica qual a pagina atual
		if (!isset($_GET["page"]))
			$pg = 0;
		else
			$pg = $_GET["page"];

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
		CLI.*, CCLI.cnpj, EDR.*, BAR.*, CID.*, EST.*
		FROM
		{$conf['db_name']}cliente CLI
		INNER JOIN {$conf['db_name']}cliente_condominio CCLI ON CLI.idcliente=CCLI.idcliente
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
		UPPER(CCLI.cnpj) LIKE UPPER('%{$filtro}%')
		)

		AND CLI.cliente_bloqueado <> '1'
			
		ORDER BY
		CLI.nome_cliente ASC ";


		//manda fazer a paginação
		$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);


		if ($list_q) {

			// testa se retornou algum registro
			if ($db->num_rows($list_q) > 0) {
				?>
<table width="100%" cellpadding="5" cellspacing="2">
	<tr onselect="" class="cabecalho">
		<td width="10%" class="cabecalho_negrito"><?php echo ('C&oacute;digo'); ?></td>
		<td width="60%" class="cabecalho_negrito"><?php echo utf8_encode('Cliente'); ?>
		</td>
		<td class="cabecalho_negrito" align="center"><?php echo utf8_encode('CPF/CNPJ'); ?>
		</td>
	</tr>
	<?php
	$filtro = htmlentities($filtro);
	$cont = 0;
	while ($list = $db->fetch_array($list_q)) {

		//insere um índice na listagem
		$list['index'] = $cont + 1;
		if ($mostraDetalhes == 1) {

			if ($list['nome_cidade'] != '')
				$cidade_uf = $list['nome_cidade'] . " / " . $list['sigla_estado'];
			else
				$cidade_uf = 'Não Informado';

			if ($list['telefone_cliente'] != '')
				$telefone = $form->FormataTelefoneParaExibir($list['telefone_cliente']);
			else
				$telefone = 'N&atilde;o Informado';

			$list['info_cliente'] = "
			<table width=95% align=center>
			<tr>
			<th align=center>Cliente</th>
			<th align=center>Cidade</th>
			<th align=center>Telefone</th>
			</tr>
			<tr>
			<td align=center><a class=menu_item href=" . $conf['addr'] . "/admin/cliente_condominio.php?ac=editar&idcliente=" . $list['idcliente'] . ">" . htmlentities($list['nome_cliente']) . "</a></td>
			<td align=center><a class=menu_item href=" . $conf['addr'] . "/admin/cliente_condominio.php?ac=editar&idcliente=" . $list['idcliente'] . ">" . htmlentities($cidade_uf) . "</a></td>
			<td align=center><a class=menu_item href=" . $conf['addr'] . "/admin/cliente_condominio.php?ac=editar&idcliente=" . $list['idcliente'] . ">" . $telefone . "</a></td>
			</tr>

			</table>";

			$list['info_cliente'] = ereg_replace("(\r\n|\n|\r)", "", $list['info_cliente']);
		}

		if ($list['cliente_bloqueado'] == "1")
			$list['cliente_bloqueado'] = "SIM";
		else
			$list['cliente_bloqueado'] = "NÃO";

		if (strlen($filtro) > 1)
			$list['nome_cliente'] = htmlentities($list['nome_cliente']);

		// coloca em negrito a string que foi encontrada na palavra
		$list['idcliente_negrito'] = preg_replace("'$filtro'i", "<span class='substring_negrito'>\\0</span>", $list['idcliente']);
		$list['nome_cliente_negrito'] = preg_replace("'$filtro'i", "<span class='substring_negrito'>\\0</span>", $list['nome_cliente']);
		$list['cpf_cnpj_negrito'] = preg_replace("'$filtro'i", "<span class='substring_negrito'>\\0</span>", $list['cnpj']);
		?>
	<tr
		onselect="
                                        this.text.value = '<?php echo utf8_encode($list['nome_cliente']); ?>';
                                        $('<?php echo $campoNomeTemp; ?>').value = '<?php echo utf8_encode($list['nome_cliente']); ?>';
                                        $('<?php echo $campoID; ?>').value = '<?php echo utf8_encode($list['idcliente']); ?>';
                                        $('<?php echo $campoFlag; ?>').className = 'selecionou';

                    <?php if ($mostraDetalhes == 1)  ?>
                                                                                $('dados_cliente').innerHTML = '<?php echo utf8_encode($list['info_cliente']); ?>';
                                                                                
                            ">
		<td class="tb_bord_baixo"><?php echo ($list['idcliente_negrito']); ?>
		</td>
		<td class="tb_bord_baixo"><?php echo utf8_encode($list['nome_cliente_negrito']); ?>
		</td>
		<td class="tb_bord_baixo" align="center">&nbsp;<?php echo utf8_encode($list['cpf_cnpj_negrito']); ?>
		</td>
	</tr>
	<?php
	$cont++;
	}

	// verifica a paginação
	$paginacao = "";
	if ($pg > 0)
		$paginacao .= "<a href='?page=" . ($pg - 1) . "' style='float:left' class='page_up'>" . utf8_encode('Anterior') . "</a>";
	$paginacao .= "<a href='?page=" . ($pg + 1) . "' style='float:right'  class='page_down'>" . utf8_encode('Pr&oacute;ximo') . "</a>";
			}
			// Nenhum registro foi encontrado
			else {
				?>
	<table width="100%" cellpadding="5" cellspacing="2">
		<tr onselect="" class="cabecalho">
			<td width="70%" class="cabecalho_negrito"><?php echo utf8_encode($conf['listar']); ?>
			</td>
		</tr>
		<?php
		// verifica a paginação
		$paginacao = "";
		if ($pg > 0)
			$paginacao .= "<a href='?page=" . ($pg - 1) . "' style='float:left' class='page_up'>" . utf8_encode('Anterior') . "</a>";
			}
		}
		else {
			?>
		<table width="100%" cellpadding="5" cellspacing="2">
			<tr onselect="" class="cabecalho">
				<td width="70%" class="cabecalho_negrito"><?php echo utf8_encode($falha['listar']); ?>
				</td>
			</tr>
			<?php
		}

		// Encerra a tabela e coloca a paginação
		echo "</table>";
		if ($paginacao != "")
			echo $paginacao;
	}

	/**
	 método: Verifica_CNPJ_Duplicado
	 propósito: Verifica_CNPJ_Duplicado
	 */
	Function Verifica_CNPJ_Duplicado($CNPJ, $idcliente = "") {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------

		$filtro = "";

		if ($idcliente != "")
			$filtro = " AND CCLI.idcliente <> $idcliente ";

		$get_sql = "	SELECT
		CCLI.*
		FROM
		{$conf['db_name']}cliente_condominio CCLI
		WHERE
		CCLI.cnpj = '$CNPJ'

		$filtro

		";

		//executa a query no banco de dados
		$get_q = $db->query($get_sql);

		//testa se a consulta foi bem sucedida
		if ($get_q) { //foi bem sucedida
			if ($db->num_rows($get_q) == 0)
				return (false);
			else
				return (true);
		}
		else { //deu erro no banco de dados
			$this->err = $falha['listar'];
			return(0);
		}
	}

	/**
	 * método: AtualizaCliente
	 * propósito: AtualizaCliente
	 */
	Function AtualizaClienteCondominio($idcliente, $post) {

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
		unset($post['litcelular_contato']);
		unset($post['litdata_nascimento_contato']);
		unset($post['littipo_cliente']);
		unset($post['numcondominio_caixa_Nome']);
		unset($post['numcondominio_caixa_NomeTemp']);

		$this->update($idcliente, $post);

		return(1);
	}

	/**
	 * método: getById
	 * propósito: busca informações
	 */
	Function getById($idcliente) {

		// variáveis globais
		global $form, $conf, $db, $falha, $parametros;
		//---------------------
		
		if(!$parametros){
			
			require_once dirname(__FILE__) . '/parametros.php';
			$parametros = new parametros();
		}
			
		

		$get_sql = "SELECT
						CLI.nome_cliente as nome_condominio, 
						CLI.email_cliente as email_condominio,
						CCLI.*
					FROM
						{$conf['db_name']}cliente_condominio CCLI
					JOIN {$conf['db_name']}cliente CLI USING(idcliente)
					WHERE
						CCLI.idcliente = $idcliente ";

		//executa a query no banco de dados
		$get_q = $db->query($get_sql);

		//testa se a consulta foi bem sucedida
		if ($get_q) { //foi bem sucedida
			$get = $db->fetch_array($get_q);

			$param = $parametros->getAll();

			$get['taxa_condominio'] = $form->formataMoedaParaExibir($get['taxa_condominio']);
			$get['sugestaoReserva'] = $form->formataMoedaParaExibir($get['sugestaoReserva']);
			$get['valAdm']          = $form->formataMoedaParaExibir($get['valAdm']);
			$get['valFaxina']       = $form->formataMoedaParaExibir($get['valFaxina']);
			$get['valVigia']        = $form->formataMoedaParaExibir($get['valVigia']);

			//Se não hover valores definidos para boletos, recupera o padrão
			if($get['multa_boleto']      == '') $get['multa_boleto']      = $param['multa_boleto'];
			if($get['juros_boleto']      == '') $get['juros_boleto']      = $param['juros_boleto'];
			if($get['desconto_boleto']   == '') $get['desconto_boleto']   = $param['desconto_boleto'];
			if($get['instrucoes_boleto'] == '') $get['instrucoes_boleto'] = $param['instrucoes_boleto'];
			//--------------------------------------------------------------------------------------//

			$get['multa_boleto'] = str_replace(',','.', $get['multa_boleto']);
			$get['juros_boleto'] = str_replace(',','.', $get['juros_boleto']);
			$get['desconto_boleto'] = str_replace(',','.', $get['desconto_boleto']);

			$get['multa_boleto']    = $form->formataMoedaParaExibir($get['multa_boleto']);
			$get['juros_boleto']    = $form->formataMoedaParaExibir($get['juros_boleto']);
			$get['desconto_boleto'] = $form->formataMoedaParaExibir($get['desconto_boleto']);


			//retorna o vetor associativo com os dados
			return $get;
		} else { //deu erro no banco de dados
			$this->err = $falha['listar'];
			return(0);
		}
	}

	/**
	 método: make_list
	 propósito: faz a listagem
	 */
	function make_list($pg, $rppg, $filtro = "", $ordem = "", $url = "") {

		if ($ordem == "") $ordem = " ORDER BY CCLI.idcliente ASC";

		// variáveis globais
		global $form, $conf, $db, $falha, $parametros;
		//---------------------


		($filtro != "") ? ($filtro .= ' AND ') : ($filtro = ' WHERE ');

		$list_sql = "	SELECT
		CCLI.*   , CLI.nome_cliente, CLI.telefone_cliente,CLI.email_cliente
		FROM
		{$conf['db_name']}cliente_condominio CCLI
		INNER JOIN {$conf['db_name']}cliente CLI ON CCLI.idcliente=CLI.idcliente

		$filtro
		CLI.cliente_bloqueado <> '1'

		$ordem";

		//manda fazer a paginação
		$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

		if ($list_q) {

			//busca os registros no banco de dados e monta o vetor de retorno
			$list_return = array();
			$cont = 0;


			$param = $parametros->getAll();
			while ($list = $db->fetch_array($list_q)) {

				//insere um índice na listagem
				$list['index'] = $cont + 1 + ($pg * $rppg);


				//Se não hover valores definidos para boletos, recupera o padrão
				if($get['multa_boleto']      == '') $get['multa_boleto']      = $param['multa_boleto'];
				if($get['juros_boleto']      == '') $get['juros_boleto']      = $param['juros_boleto'];
				if($get['desconto_boleto']   == '') $get['desconto_boleto']   = $param['desconto_boleto'];
				if($get['instrucoes_boleto'] == '') $get['instrucoes_boleto'] = $param['instrucoes_boleto'];
				//--------------------------------------------------------------------------------------//

				//Formata os dados
				$list['sugestaoReserva'] = $form->formataMoedaParaExibir($list['sugestaoReserva']);
				$list['valAdm'] = $form->formataMoedaParaExibir($list['valAdm']);
				$list['valFaxina'] = $form->formataMoedaParaExibir($list['valFaxina']);
				$list['valVigia'] = $form->formataMoedaParaExibir($list['valVigia']);
				$list['telefone_cliente'] = $form->FormataTelefoneParaExibir($list['telefone_cliente']);

				$list_return[] = $list;
				$cont++;
			}

			return $list_return;
		} else {
			$this->err = $falha['listar'];
			return(0);
		}
	}

	function get_apartamentos($list, $ordem = null, $url = null) {

		global $apartamento, $conf, $db;

		foreach ($list as $k => $condominio) {

			$list[$k]['apartamentos'] = $apartamento->make_list(false, 99999, $filtro = " WHERE APTO.idcliente = " . $condominio['idcliente'], $ordem, $url);
		}

		return($list);
	}

	/**
	 método: set
	 propósito: inclui novo registro
	 */
	Function set($info) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------

		$camposNumericos= array('sugestaoReserva', 'valAdm', 'valFaxina', 'valVigia', 'idbanco',
								 'agencia', 'agenciaDigito', 'conta', 'contaDigito', 'numeroContrato',
								 'taxa_condominio', 'multa_boleto', 'juros_boleto', 'desconto_boleto', 'condominio_caixa');
		
		foreach($camposNumericos as $campo){
			$info[$campo] = empty($info[$campo]) ? 'NULL' : $info[$campo];
		}


		$set_sql = "  INSERT INTO
		{$conf['db_name']}cliente_condominio
		(
		idcliente,
		cnpj,
		admFinanceira,
		sugestaoReserva,
		valAdm,
		valFaxina,
		valVigia, emissaoPropria,
		idbanco,
		agencia,
		agenciaDigito,
		conta,
		contaDigito,
		numeroContrato,
		taxa_condominio,
		sugestaoVencimento,
		multa_boleto,
		juros_boleto,
		desconto_boleto,
		instrucoes_boleto,
		condominio_caixa
		)
		VALUES
		(

		" . $info['idcliente'] . ",
		'" . $info['cnpj'] . "',
		'" . $info['admFinanceira'] . "',
		" . $info['sugestaoReserva'] . ",
		" . $info['valAdm'] . ",
		" . $info['valFaxina'] . ",
		" . $info['valVigia'] . ",
		'" . $info['emissaoPropria'] . "',
		" . $info['idbanco'] . ",
		" . $info['agencia'] . ",
		" . $info['agenciaDigito'] . ",
		" . $info['conta'] . ",
		" . $info['contaDigito'] . ",
		" . $info['numeroContrato'] . ",
		" . $info['taxa_condominio'] . ",
		'" . $info['sugestaoVencimento'] . "',
		" . $info['multa_boleto'] . ",
		" . $info['juros_boleto'] . ",
		" . $info['desconto_boleto'] . ",
		'" . $db->escape($info['instrucoes_boleto']) . "',
		" . $info['condominio_caixa'] . "

		)";

		
		//executa a query e testa se a consulta foi "boa"
		if ($db->query($set_sql)) {
			//retorna o código inserido
			$codigo = $db->insert_id();


			return($codigo);
		} else {
			$this->err = $falha['inserir'];
			return(0);
		}
	}

	/**
	 * método: update
	 * propósito: atualiza os dados
	 * 	
	 * 1) o vetor $info deve conter todos os campos tabela a serem atualizados
	 * 2) a variável $id deve conter o código do usuário cujos dados serão atualizados
	 * 3) campos literais deverão ter o prefixo lit e campos numéricos deverão ter o prefixo nu
	 */
	Function update($idcliente, $info) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------
		//inicializa a query
		$update_sql = "	UPDATE
		{$conf['db_name']}cliente_condominio
		SET ";

		//varre o formulário e monta a consulta;
		$cont_validos = 0;
		foreach ($info as $campo => $valor) {

			$tipo_campo = substr($campo, 0, 3);
			$nome_campo = substr($campo, 3, strlen($campo) - 3);

			if (($tipo_campo == "lit") || ($tipo_campo == "num")) {

				$usu_validos["$campo"] = $valor;
				$cont_validos++;
			}
		}

		$cont = 0;
		foreach ($usu_validos as $campo => $valor) {

			$tipo_campo = substr($campo, 0, 3);
			$nome_campo = substr($campo, 3, strlen($campo) - 3);

			if ($tipo_campo == "lit"){
				$valor = $db->escape($valor);
				$update_sql .= "$nome_campo = '$valor'";

			}elseif ($tipo_campo == "num") {

				if (empty($valor)) $valor = 'NULL';
				$update_sql .= "$nome_campo = $valor";
			}

			$cont++;

			//testa se é o último
			if ($cont != $cont_validos) {
				$update_sql .= ", ";
			}
		}


		//completa o sql com a restrição
		$update_sql .= " WHERE  idcliente = $idcliente ";

		//envia a query para o banco
		$update_q = $db->query($update_sql);


		if ($update_q)
			return(1);
		else
			$this->err = $falha['alterar'];
	}

	/**
	 * método: delete
	 * propósito: excluir registro
	 */
	Function delete($idcliente) {

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
			{$conf['db_name']}cliente_condominio
			WHERE
			idcliente = $idcliente ";
			$delete_q = $db->query($delete_sql);

			if ($delete_q) {
				return(1);
			} else {
				$this->err = $falha['excluir'];
				return(0);
			}
		} else {
			$this->err = "Este registro não pode ser excluído, pois existem registros relacionados a ele.";
		}
	}

	/**
	 método: make_list
	 propósito: faz a listagem para colocar no select
	 */
	function make_list_select($filtro = "", $ordem = " ORDER BY idcliente ASC") {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------

		($filtro != "") ? ($filtro .= ' AND ') : ($filtro = ' WHERE ');

		$list_sql = "	SELECT
		*
		FROM
		{$conf['db_name']}cliente_juridico
		$filtro
		CLI.cliente_bloqueado <> '1'
		$ordem";

		$list_q = $db->query($list_sql);
		if ($list_q) {

			//busca os registros no banco de dados e monta o vetor de retorno
			$list_return = array();
			$cont = 0;
			while ($list = $db->fetch_array($list_q)) {

				foreach ($list as $campo => $value) {
					$list_return["$campo"][$cont] = $value;
				}

				$cont++;
			}

			return $list_return;
		} else {
			$this->err = $falha['listar'];
			return(0);
		}
	}

	
	/**
	 método: make_list
	 propósito: faz a listagem para colocar no select
	 */
	function make_list_select_cliente_condominio($filtro = "", $ordem = " ORDER BY idcliente ASC") {
	
		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------
	
		($filtro != "") ? ($filtro .= ' AND ') : ($filtro = ' WHERE ');
	
		$list_sql = "	SELECT cliente_condominio.*, cliente.nome_cliente
						FROM
							{$conf['db_name']}cliente_condominio
						LEFT JOIN {$conf['db_name']}cliente ON (cliente_condominio.idcliente = cliente.idcliente)
						$filtro
							cliente.cliente_bloqueado <> '1'
						$ordem";
	
		$list_q = $db->query($list_sql);
		if ($list_q) {
	
			//busca os registros no banco de dados e monta o vetor de retorno
			$list_return = array();
			$cont = 0;
			while ($list = $db->fetch_array($list_q)) {
		
				foreach ($list as $campo => $value) {
					$list_return["$campo"][$cont] = $value;
				}
		
				$cont++;
			}
		
			return $list_return;
		} else {
			$this->err = $falha['listar'];
			return(0);
		}
	}
	
	
	function Busca_Generica($pg, $rppg, $busca = "", $ordem = "", $url = "") {

		if ($ordem == "")
			$ordem = " ORDER BY CLI.nome_cliente ";



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

		AND CLI.cliente_bloqueado <> '1'

		$ordem ";


		//manda fazer a paginação
		$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

		if ($list_q) {

			//busca os registros no banco de dados e monta o vetor de retorno
			$list_return = array();
			$cont = 0;
			while ($list = $db->fetch_array($list_q)) {

				//insere um índice na listagem
				$list['index'] = $cont + 1 + ($pg * $rppg);

				$list_return[] = $list;

				$cont++;
			}

			return $list_return;
		} else {
			$this->err = $falha['listar'];
			return(0);
		}
	}

	/**
	 método: Busca_Parametrizada
	 propósito: Busca_Parametrizada
	 */
	function Busca_Parametrizada($pg, $rppg, $filtro_where = "", $ordem = "", $url = "") {

		if ($ordem == "")
			$ordem = " ORDER BY CLI.nome_cliente ";

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------


		if ($filtro_where != "")
			$filtro_where = " WHERE ( " . $filtro_where . " ) AND  CLI.cliente_bloqueado <> '1' ";
		else
			$filtro_where = "WHERE  CLI.cliente_bloqueado <> '1'";


		$list_sql = "	SELECT
		CLI.*, RAT.descricao_atividade, CCLI.*, EDR.*, BAR.*, CID.*, EST.*
		FROM
		{$conf['db_name']}cliente CLI
		LEFT JOIN 		{$conf['db_name']}ramo_atividade RAT ON CLI.idramo_atividade = RAT.idramo_atividade
		INNER JOIN 		{$conf['db_name']}cliente_condominio CCLI ON CLI.idcliente = CCLI.idcliente
		LEFT OUTER JOIN {$conf['db_name']}endereco EDR ON CLI.idendereco_cliente=EDR.idendereco
		LEFT OUTER JOIN {$conf['db_name']}bairro BAR ON EDR.idbairro=BAR.idbairro
		LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade=CID.idcidade
		LEFT OUTER JOIN {$conf['db_name']}estado EST ON EDR.idestado=EST.idestado
			
		$filtro_where
		$ordem ";


		//manda fazer a paginação
		$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

		if ($list_q) {

			//busca os registros no banco de dados e monta o vetor de retorno
			$list_return = array();
			$cont = 0;

			while ($list = $db->fetch_array($list_q)) {

				//insere um índice na listagem
				$list['index'] = $cont + 1 + ($pg * $rppg);


				if ($list['logradouro'] != '')
					$list['endereco_string'] = $list['logradouro'] . ', ' . $list['numero'];
				if ($list['complemento'] != '')
					$list['endereco_string'] .= ' ' . $list['complemento'];
				if ($list['nome_bairro'] != '')
					$list['endereco_string'] .= ' - Bairro ' . $list['nome_bairro'];
				if ($list['nome_cidade'] != '')
					$list['endereco_string'] .= ' - ' . $list['nome_cidade'] . ' / ' . $list['sigla_estado'];

				$list['valAdm'] = $form->FormataMoedaParaExibir($list['valAdm']);
				$list['valFaxina'] = $form->FormataMoedaParaExibir($list['valFaxina']);
				$list['valVigia'] = $form->FormataMoedaParaExibir($list['valVigia']);

				$list_return[] = $list;
				$cont++;
			}

			return $list_return;
		}
		else {
			$this->err = $falha['listar'];
			return(0);
		}
	}

	function getCondominiosCliente($idcliente) {

		// variáveis globais
		global $form, $conf, $db, $falha;


		$list_sql = "SELECT
		COND.idcliente, COND.nome_cliente
		FROM cliente COND
		JOIN cliente_condominio CLI_COND ON (COND.idcliente = CLI_COND.idcliente)
		JOIN apartamento APTO ON (CLI_COND.idcliente = APTO.idcliente)
		WHERE
		APTO.idmorador = $idcliente OR APTO.idproprietario = $idcliente
		GROUP BY
		COND.idcliente";

		//manda fazer a paginação
		$list_q = $db->query($list_sql);

		if ($list_q)
			return $db->fetch_all($list_q);
		else {
			$this->err = $falha['listar'];
			return(0);
		}
	}


	/**
	 * Busca caixas-proprietários associados a um cliente
	 * @param integer $idcliente - ID do cliente para o qual os caixas
	 *					devem ser buscados
	 * @return array $caixas_proprietarios - Array contendo IDs dos caixas
	 */
	function buscaCaixaProprietario($idcliente){

		global $db;

		if(!$db){
			require_once dirname(dirname(__FILE__)) . '/common/lib/db.inc.php';
			$db = new db();
		}

		$consulta = 'SELECT CC.idcliente, nome_cliente ' . 
					"FROM {$conf['db_name']}cliente_condominio CC " .
					"LEFT JOIN {$conf['db_name']}cliente C ON (CC.idcliente = C.idcliente) " .
					"WHERE CC.condominio_caixa = " . $idcliente;

		$list_q = $db->query($consulta);

		/// array com IDs dos caixas proprietários
		$caixas_proprietarios = array();

		if ($list_q) {

			while ($list = $db->fetch_array($list_q)) {

				$caixas_proprietarios[] = array('idcliente' => $list['idcliente'], 'nome_cliente' => $list['nome_cliente']);
			}
		}

		return $caixas_proprietarios;

	}

	/**
	 * Retorna array contendo IDs dos clientes que são condomínio
	 * @param boolean $somente_adm_financeira - indica se deve retornar somente condomínios de administração
	 * 			financeira
	 * @return array $condominios - Array tendo como valores os IDs dos condomínios
	 */ 
	public function buscaIdsCondominios($somente_adm_financeira = false){


		global $db;

		if(!$db){
			require_once dirname(dirname(__FILE__)) . '/common/lib/db.inc.php';
			$db = new db();
		}

		/// array que será retornado com IDs dos condomínios
		$condominios = array();

		$where = '';
		if($somente_adm_financeira){
			$where = " WHERE  admFinanceira = 'S'";
		}

		$consulta = 'SELECT idcliente ' . 
					"FROM {$conf['db_name']}cliente_condominio " .
					$where;

		$list_q = $db->query($consulta);

		if ($list_q) {

			while ($list = $db->fetch_array($list_q)) {


				$condominios[] = $list['idcliente'];
			}
		}

		return $condominios;

	}

}

// fim da classe
?>
