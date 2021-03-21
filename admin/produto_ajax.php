<?php

  //inclus?o de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  

  require_once("../entidades/aliquota_icms.php");  
	require_once("../entidades/parametro.php");
	require_once("../entidades/parametros.php");
	require_once("../entidades/fornecedor.php");
	require_once("../entidades/produto.php");
	require_once("../entidades/produto_referencia.php");
	require_once("../entidades/produto_filial.php");

  // inicializa templating
  $smarty = new Smarty;

  // configura diret?rios
  $smarty->template_dir = "../common/tpl";
  $smarty->compile_dir =   "../common/tpl_c";

  // seta configura??es
  $smarty->assign("conf", $conf);

  // a??o selecionada
  $flags['action'] = $_GET['ac'];


  // inicializa autentica??o
  $auth = new auth();

	//inicializa classe

	$fornecedor         = new fornecedor();
	$produto            = new produto();
	$produto_referencia = new produto_referencia();
	$produto_filial     = new produto_filial();
	$aliquota_icms      = new aliquota_icms();
	$parametros         = new parametros();							

  // inicializa banco de dados
  $db = new db();

  //incializa classe para valida??o de formul?rio
  $form = new form();
        
				

  switch($flags['action']) {


		// busca os fornecedores de acordo com a busca
		case "busca_fornecedor":

			$fornecedor->Filtra_Fornecedor_AJAX($_GET['typing']);

		break;
		
		// busca os produtos de acordo com a busca
		case "busca_produto":
		
			$produto->Filtra_Produto_AJAX($_GET['typing'], $_GET['campoID'], $_GET['idproduto'], $_GET['mostraDetalhes']);

		break;

		
		// busca os produtos de acordo com a busca
		case "busca_produto_os":
			
			$produto->Filtra_Produto_OS_AJAX($_GET['typing'], $_GET['campoID'], $_GET['idproduto']);
			
		break;
		
		// busca os produtos e os Encartelamentos de acordo com a busca
		case "busca_produto_encartelamento":

			$produto->Filtra_Produto_Encartelamento_AJAX($_GET['typing'], $_GET['campoID'], $_GET['tipoPreco']);

		break;
		
		// busca os produtos e os Encartelamentos para a transferência
		case "busca_produto_encartelamento_transferencia":

			$produto->Filtra_Produto_Encartelamento_Transferencia_AJAX($_GET['typing'], $_GET['campoID']);

		break;

  }
  

  	
  // seta erros
  $smarty->assign("err", $err);

  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);

  
  
  
  
  
  /*
   Fun??o: Atribui_Preco_AJAX
  atribui o pre?o de uma filial para todas as outras
  */
  function Cadastro_Rapido_Produto_AJAX ($post) {

  	// vari?veis globais
  	global $form, $conf, $db, $falha, $err, $produto, $produto_filial;
  	//---------------------

  	$filial = new filial();
  	// cria o objeto xajaxResponse
  	$objResponse = new xajaxResponse();

  	$form->chk_empty($post['descricao_produto'], 1, 'Descrição do Material / Serviço');
  	
  	$err = $form->err;
  	
  	//Recupera os erros de validação do formulário
	$err = $form->err;
	
	if(count($err)){
		$mensagem = implode("\n", $err);
	}
	else{
		
		
		$idproduto = $produto->set(array(
					'descricao_produto'       => $db->escape(utf8_decode($post['descricao_produto'])),
					'idsecao'			      => (int)$post['idsecao'],
					'idunidade_venda'		  => (int)$post['idunidade_venda'],
					'produto_comercializado ' => '1'
		));
		
		if($idproduto){
			$produto_filial->set(array(
						'idproduto' => $idproduto,
						'idfilial'  => $_SESSION['idfilial_usuario'],
						'qtd_produto' 			=> '0',
						'preco_balcao_produto' 	=> '0',
						'preco_oferta_produto' 	=> '0',
						'preco_atacado_produto' => '0',
						'preco_custo_produto' 	=> '0',						
						'produto_em_oferta' 	=> '0',
						'preco_telemarketing_produto' => '0'
			));	
			
		}
		
		
		if(!count($produto->err) && !count($produto_filial->err)){
			
						
			//Seta a mensagem de sucesso
			$mensagem = "Registro inserido com sucesso!";
			
			//Limpa os campos do formulário
			$objResponse->addClear('descricao_produto', 'value');
			
			//Seleciona o produto recém-cadastrado
			$objResponse->addAssign('idproduto', 'value', $idproduto );
			$objResponse->addAssign('idproduto_NomeTemp', 'value',$post['descricao_produto']);
			$objResponse->addAssign('idproduto_Nome', 'value', $post['descricao_produto']);
			$objResponse->addAssign('idproduto_Flag', 'className', 'selecionou');
			
			//Fecha a Lightbox
			$objResponse->addScript('fecharLightbox(produto_conteudo)');
		}
		else{
			$mensagem = "Falha ao cadastrar o Material / Serviço";
		}
		
		
		
	}
  	
	//Exibe a(s) mensagem(s) para o usuário
	$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
	
  	return $objResponse->getXML();

  }

  
  
  
  

	/*
	Fun??o: Mostra_Detalhes_Produto_AJAX
	Mostra os detalhes do produto quando ele for selecionado.
	*/
	function Mostra_Detalhes_Produto_AJAX ($idproduto) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $produto;
		global $produto_filial;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		//busca detalhes
		$info = $produto->getById($idproduto);

		$objResponse->addAssign("for", "action", $conf['addr']."/admin/produto.php?ac=editar&idproduto=$idproduto");

		$objResponse->addAssign("departamento", "innerHTML", utf8_encode($info['nome_departamento']));
		$objResponse->addAssign("secao", "innerHTML", utf8_encode($info['nome_secao']));
		$objResponse->addAssign("descricao_produto", "innerHTML", utf8_encode($info['descricao_produto']));
		$objResponse->addAssign("localizacao_produto", "innerHTML", utf8_encode($info['localizacao_produto']));
		$objResponse->addAssign("aplicacao_produto", "innerHTML", utf8_encode($info['aplicacao_produto']));
		$objResponse->addAssign("percentual_max_desconto_produto", "innerHTML", utf8_encode($info['percentual_max_desconto_produto']));
		$objResponse->addAssign("referencia_produto", "innerHTML", utf8_encode($info['referencia_produto']));
		$objResponse->addAssign("observacao_produto", "innerHTML", utf8_encode($info['observacao_produto']));
		$objResponse->addAssign("data_cadastro_produto", "innerHTML", utf8_encode($info['data_cadastro_produto']));
		$objResponse->addAssign("peso_bruto_produto", "innerHTML", utf8_encode($info['peso_bruto_produto']));
		$objResponse->addAssign("peso_liquido_produto", "innerHTML", utf8_encode($info['peso_liquido_produto']));
		$objResponse->addAssign("unidade_venda", "innerHTML", utf8_encode($info['nome_unidade_venda'] . " (" . $info['sigla_unidade_venda'] . ")"));
		$objResponse->addAssign("qtd_unitaria_embalagem_compra_produto", "innerHTML", utf8_encode($info['qtd_unitaria_embalagem_compra_produto']));
		$objResponse->addAssign("qtd_unitaria_embalagem_venda_produto", "innerHTML", utf8_encode($info['qtd_unitaria_embalagem_venda_produto']));
		$objResponse->addAssign("comissao_interno_produto", "innerHTML", utf8_encode($info['comissao_interno_produto']));
		$objResponse->addAssign("comissao_externo_produto", "innerHTML", utf8_encode($info['comissao_externo_produto']));
		$objResponse->addAssign("comissao_representante_produto", "innerHTML", utf8_encode($info['comissao_representante_produto']));
		$objResponse->addAssign("comissao_operador_telemarketing_produto", "innerHTML", utf8_encode($info['comissao_operador_telemarketing_produto']));

		$objResponse->addAssign("icms_produto", "innerHTML", utf8_encode($info['icms_produto']));
		$objResponse->addAssign("ipi_produto", "innerHTML", utf8_encode($info['ipi_produto']));


		if ($info['situacao_tributaria_produto'] == "I") $info['situacao_tributaria_produto'] = "(I) Isento";
		else if ($info['situacao_tributaria_produto'] == "N") $info['situacao_tributaria_produto'] = "(N) Não Tributado";
		else if ($info['situacao_tributaria_produto'] == "F") $info['situacao_tributaria_produto'] = "(F) Substituição Tributária";
		else if ($info['situacao_tributaria_produto'] == "T") $info['situacao_tributaria_produto'] = "(T) Tributado pelo ICMS";
		else if ($info['situacao_tributaria_produto'] == "S") $info['situacao_tributaria_produto'] = "(S) Tributado pelo ISSQN";
		$objResponse->addAssign("situacao_tributaria_produto", "innerHTML", utf8_encode($info['situacao_tributaria_produto']));


		if ($info['produto_comercializado'] == '0') $info['produto_comercializado'] = "Não";
		else $info['produto_comercializado'] = "Sim";

		$objResponse->addAssign("produto_comercializado", "innerHTML", utf8_encode($info['produto_comercializado']));

		$objResponse->addAssign("idproduto_label", "innerHTML", utf8_encode($info['idproduto']));
		$objResponse->addAssign("codigo_produto", "innerHTML", utf8_encode($info['codigo_produto']));
		$objResponse->addAssign("cst_produto", "innerHTML", utf8_encode($info['cst_produto']));

		// limpa a tabela de fornecedor
		$tabela_fornecedor = utf8_encode("<table width='100%' cellpadding='5'>
														<tr>
															<th align='center' width='35%'>Nome</th>
															<th align='center' width='10%'>Endereço</th>
															<th align='center' width='15%'>Telefone</th>
														</tr>
													</table>");
		$objResponse->addAssign("div_fornecedor", "innerHTML", $tabela_fornecedor);


		// limpa a tabela de referencia
		$tabela_referencia = utf8_encode("<table width='100%' cellpadding='5'>
														<tr>
															<th align='Left' width='15%'>Código</th>
															<th align='Left' width='35%'>Produto</th>
															<th align='center' width='10%'>Unidade</th>
														</tr>
													</table>");
		$objResponse->addAssign("div_referencia", "innerHTML", $tabela_referencia);


		//seleciona as filiais
		$list_filial = $produto_filial->make_list_filial(0,999999," WHERE PRD.idproduto = $idproduto ");

		$tabela_precos_estoque = "";

		for ($i=0; $i<count($list_filial); $i++) {

			if ($list_filial[$i]['produto_em_oferta'] == '0') $list_filial[$i]['produto_em_oferta'] = "Não";
			else $list_filial[$i]['produto_em_oferta'] = "Sim";

			$tabela_precos_estoque .= "
					<table width='95%' align='center'>
					<tr bgcolor='#F7F7F7'>
						<td colspan='9' align='center'>Preços / Estoque do Produto - " . $list_filial[$i]['nome_filial'] . "</td>
	        </tr>

	        <tr>
	          <td class='row' height='1' bgcolor='#999999' colspan='9'></td>
	        </tr>

					<tr>
						<td colspan='9' align='center' width='50%'>Estoque : " . $list_filial[$i]['qtd_produto'] . " " . $info['nome_unidade_venda'] . " (" . $info['sigla_unidade_venda'] . ") </td>
					</tr>

					<tr>
						<td align='right'>Produto em Oferta: </td>
						<td>" . $list_filial[$i]['produto_em_oferta'] . "</td>
					</tr>

					<tr>
						<td align='right' width='50%'>Preço de balcão (R$):</td>
						<td>" . $list_filial[$i]['preco_balcao_produto'] . "</td>
					</tr>

					<tr>
						<td align='right'>Preço de oferta (R$):</td>
						<td>" . $list_filial[$i]['preco_oferta_produto'] . "</td>
					</tr>

					<tr>
						<td align='right'>Preço de atacado (R$):</td>
						<td>" . $list_filial[$i]['preco_atacado_produto'] . "</td>
					</tr>

					<tr>
						<td align='right'>Preço de telemarketing (R$):</td>
						<td>" . $list_filial[$i]['preco_telemarketing_produto'] . "</td>
					</tr>

					<tr>
						<td align='right'>Preço de custo (R$):</td>
						<td>" . $list_filial[$i]['preco_custo_produto'] . "</td>
					</tr>

				</table> ";

		} // fim do for




		$objResponse->addAssign("precos_estoque_filiais", "innerHTML", utf8_encode($tabela_precos_estoque));


		$objResponse->addAssign("dados_produto", "style.display", "block"); // mostra tabs
		//$objResponse->addAssign("dados_produto", "style.display", "none"); // esconde tabs

		// Inicialmente, preenche todos os fornecedores que fazem parte da filial
		$objResponse->loadXML( Seleciona_Fornecedor_AJAX($idproduto) );

		// Inicialmente, preenche todos referências do produto
		$objResponse->loadXML( Seleciona_Referencia_AJAX($idproduto) );

		// Inicialmente, busca os encartelamentos do produto
		$objResponse->loadXML( Seleciona_Encartelamentos_AJAX($idproduto) );


    return $objResponse->getXML();

	}

	/*
	Fun??o: Atribui_Preco_AJAX
	atribui o pre?o de uma filial para todas as outras
	*/
	function Atribui_Preco_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $banco;
		//---------------------

		$filial = new filial();
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		for ($i=1; $i<=count($filial->make_list(0, 9999, $filtro)); $i++)
		{
		if ($_GET['ac'] == "editar"){
			
			$objResponse->addAssign("numpreco_balcao_produto_".$i, "value", $post['numpreco_balcao_produto_1']);
			$objResponse->addAssign("numpreco_oferta_produto_".$i, "value", $post['numpreco_oferta_produto_1']);
			$objResponse->addAssign("numpreco_atacado_produto_".$i, "value", $post['numpreco_atacado_produto_1']);
			$objResponse->addAssign("numpreco_telemarketing_produto_".$i, "value", $post['numpreco_telemarketing_produto_1']);
			$objResponse->addAssign("numpreco_custo_produto_".$i, "value", $post['numpreco_custo_produto_1']);
		}
		else {
			$objResponse->addAssign("preco_balcao_produto_".$i, "value", $post['preco_balcao_produto_1']);
			$objResponse->addAssign("preco_oferta_produto_".$i, "value", $post['preco_oferta_produto_1']);
			$objResponse->addAssign("preco_atacado_produto_".$i, "value", $post['preco_atacado_produto_1']);
			$objResponse->addAssign("preco_telemarketing_produto_".$i, "value", $post['preco_telemarketing_produto_1']);
			$objResponse->addAssign("preco_custo_produto_".$i, "value", $post['preco_custo_produto_1']);
		}	
			

		}	

    return $objResponse->getXML();

}

  	/*
	Fun??o: Insere_Referencia_AJAX
	Insere uma referencia de produto dinamicamente na tabela html
	*/
	function Insere_Referencia_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $produto_referencia;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		// codigo do funcionario
		$codigoReferencia = $post['idproduto_referencia'];

		$form->chk_empty($post['idproduto_referencia'], 1, 'Produto');


		$err = $form->err;


		// se houveram erros, mostra-os
    if(count($err) != 0) {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
    }
    // se nao houveram erros, verifica se ele j? nao existe na tabela
		else if (Verifica_Referencia_Existe_AJAX ($post) == false) {
			
   		// incrementa 1 na quantidade de produtos
			$total_produtos = intval($post['total_produtos']) + 1;
			$objResponse->addAssign("total_produtos", "value", $total_produtos);

			// busca os dados do produto
			$info_referencia = $produto_referencia->retornaReferencia($codigoReferencia);


			// nome da tabela criada
      $nome_tabela = "tabela_referencia_" . $codigoReferencia;

			// se for listar, nao poe a opção de excluir
			if ( ($_GET['ac'] == "listar") || ($_GET['ac'] == "") ) {
				// tabela de referências
				$tabela = utf8_encode("
							<table width='100%' cellpadding='5' id='$nome_tabela'>
								<tr>
									<td class='tb_bord_baixo' align='left' width='15%'>&nbsp;{$info_referencia['codigo_produto']}</td>
									<td class='tb_bord_baixo' align='left' width='35%'>
										<input type='hidden' name='codigo_referencia_$total_produtos' id='codigo_referencia_$total_produtos' value='$codigoReferencia' />
	
										{$info_referencia['descricao_produto']}
									</td>
									<td class='tb_bord_baixo' align='center' width='10%'>&nbsp;{$info_referencia['sigla_unidade_venda']}</td>
								</tr>
							</table>
						");

			}
			else {

				// tabela de referências
				$tabela = utf8_encode("
							<table width='100%' cellpadding='5' id='$nome_tabela'>
								<tr>
									<td class='tb_bord_baixo' align='left' width='15%'>&nbsp;{$info_referencia['codigo_produto']}</td>
									<td class='tb_bord_baixo' align='left' width='35%'>
										<input type='hidden' name='codigo_referencia_$total_produtos' id='codigo_referencia_$total_produtos' value='$codigoReferencia' />
	
										{$info_referencia['descricao_produto']}
									</td>
									<td class='tb_bord_baixo' align='center' width='10%'>&nbsp;{$info_referencia['sigla_unidade_venda']}</td>
									<td class='tb_bord_baixo' align='center' width='5%'>
										<a href='javascript:;' onclick=" . '"' . "xajax_Deleta_Referencia_AJAX(" . "'" . $codigoReferencia . "'" . ");" . '"' . "><img src='../common/img/delete.gif'></a>
									</td>
								</tr>
							</table>
						");
			}

			// adiciona a tabela
			$objResponse->addAppend("div_referencia", "innerHTML", $tabela);

			$objResponse->addClear("idproduto_referencia", "value");
			$objResponse->addClear("idproduto_referencia_Nome", "value");
			$objResponse->addClear("idproduto_referencia_NomeTemp", "value");
			$objResponse->addAssign("idproduto_referencia_Flag", "className", "nao_selecionou");

		}
		else {
			$objResponse->addAlert(utf8_encode("Este Produto já está na lista!"));
		}

		// retorna o resultado XML
    return $objResponse->getXML();

  }



	/*
	Fun??o: Verifica_Referencia_Existe_AJAX
	Verifica se uma referencia ja existe na tabela html
	*/
	function Verifica_Referencia_Existe_AJAX ($post) {

		for ($i=1; $i<=intval($post['total_produtos']); $i++) {
			if ( $post["codigo_referencia_$i"] == $post['idproduto_referencia'] ) return true;
		}

		return false;
  }



	/*
	Fun??o: Deleta_Referencia_AJAX
	Deleta uma referencia dinamicamente na tabela html
	*/
	function Deleta_Referencia_AJAX ($codigoReferencia) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;

		global $produto;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		// nome da tabela criada
    $nome_tabela = "tabela_referencia_" . $codigoReferencia;

		// busca os dados do funcion?rio
		$info_produto = $produto->getById($codigoReferencia);

		// verifica se vai remover: Pula 1 linha se clicar no cancelar
		$objResponse->addConfirmCommands(1, utf8_encode("Deseja excluir o produto '{$info_produto['descricao_produto']}' da tabela de referência ?"));

		// remove a tabela
		$objResponse->addRemove($nome_tabela);

		// retorna o resultado XML
    return $objResponse->getXML();

  }


	/*
	Fun??o: Seleciona_Referencia_AJAX
 	Seleciona as referencias do produto e colocam eles dinamicamente na tabela html
	*/
	function Seleciona_Referencia_AJAX ($codigoProduto) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;

		global $produto_referencia;
		global $produto;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

			$list_sql = "	SELECT
											PRD.*, UNV.* , PRDREF.*
										FROM
           						{$conf['db_name']}produto PRD
           							INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda=UNV.idunidade_venda
           							INNER JOIN {$conf['db_name']}produto_referencia PRDREF ON PRDREF.idproduto_vendido = PRD.idproduto

										WHERE
											  PRDREF.idproduto_vendido = $codigoProduto
											  AND PRD.produto_comercializado = '1'
										ORDER BY
											PRD.descricao_produto ASC ";
											



		//manda fazer a pagina??o
		$list_q = $db->query($list_sql);


		if($list_q){

			//busca os registros no banco de dados e monta o vetor de retorno
			$cont = 0;

			while($list = $db->fetch_array($list_q)){
				$post['idproduto_referencia'] = $list['idproduto_referencia'];
				$post['total_produtos'] = $cont;

				// acrescenta o XML que foi retornado no objeto atual
				$objResponse->loadXML( Insere_Referencia_AJAX($post) );

        $cont++;
			} // fim do while

		}

		// retorna o resultado XML
    return $objResponse->getXML();

	}



	/*
	Fun??o: Seleciona_Encartelamentos_AJAX
 	Seleciona os encartelamentos do produto e colocam eles dinamicamente na tabela html
	*/
	function Seleciona_Encartelamentos_AJAX ($codigoProduto) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		$list_sql = "	SELECT
										ENCTPRD.*, ENCT.descricao_encartelamento
									FROM
										{$conf['db_name']}encartelamento_produto ENCTPRD
											INNER JOIN {$conf['db_name']}encartelamento ENCT ON ENCTPRD.idencartelamento = ENCT.idencartelamento
									WHERE
											ENCTPRD.idproduto = $codigoProduto ";
										

		//manda fazer a pagina??o
		$list_q = $db->query($list_sql);


		if($list_q){

			// monta a tabela de encartelamentos 
			$tabela_encartelamentos = "<table width='100%' cellpadding='5'>";
					
			//busca os registros no banco de dados e monta o vetor de retorno
			while($list = $db->fetch_array($list_q)){

				$tabela_encartelamentos .= "
						<tr>
		          <td>
								<a class='link_geral' href='{$conf['addr']}/admin/encartelamento.php?ac=editar&idencartelamento={$list['idencartelamento']}'>
	            		{$list['descricao_encartelamento']}
								</a>
							</td>
		        </tr>
				";

			} // fim do while
	
			$tabela_encartelamentos .= "</table>"; 
			$tabela_encartelamentos = utf8_encode($tabela_encartelamentos);
			$objResponse->addAssign("div_encartelamentos", "innerHTML", $tabela_encartelamentos);

		}

		// retorna o resultado XML
    return $objResponse->getXML();

	}



	/*
	Fun??o: Verifica_Campos_Produto_AJAX
	Verifica se os campos do produto foram preenchidos
	*/
	function Verifica_Campos_Produto_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $produto;
		global $aliquota_icms;
		//---------------------
  
    $existe_produto = 0;
    
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		if ($_GET['ac'] == "editar") { 
			$form->chk_empty($post['iddepartamento'], 1, 'Departamento');
			$form->chk_empty($post['idsecao'], 1, 'Seção');
			$form->chk_empty($post['litdescricao_produto'], 1, 'Descrição do produto');
			$form->chk_empty($post['litlocalizacao_produto'], 1, 'Localização do produto');
			$form->chk_empty($post['numidunidade_venda'], 1, 'Unidade de venda');
			$form->chk_empty($post['litsituacao_tributaria_produto'], 1, 'Situação tributária');

			$form->chk_empty($post['numpercentual_max_desconto_produto'], 1, 'Percentual máximo de desconto');
			$form->chk_moeda($post['numpercentual_max_desconto_produto'], 1, 'Percentual máximo de desconto');		

			$form->chk_empty($post['numcomissao_interno_produto'], 1, 'Comissão Interno');
			$form->chk_moeda($post['numcomissao_interno_produto'], 1, 'Comissão Interno');

			$form->chk_empty($post['numcomissao_externo_produto'], 1, 'Comissão Externo');
			$form->chk_moeda($post['numcomissao_externo_produto'], 1, 'Comissão Externo');

			$form->chk_empty($post['numcomissao_representante_produto'], 1, 'Comissão Representante');
			$form->chk_moeda($post['numcomissao_representante_produto'], 1, 'Comissão Representante');

			$form->chk_empty($post['numcomissao_operador_telemarketing_produto'], 1, 'Comissão do Operador de telemarketing');
			$form->chk_moeda($post['numcomissao_operador_telemarketing_produto'], 1, 'Comissão do Operador de telemarketing');

			// se for tributado, tem que digitar a alíquota
			if ( ($post['litsituacao_tributaria_produto'] == "T") || ($post['litsituacao_tributaria_produto'] == "S") )
				$form->chk_moeda($post['numicms_produto'], 0, 'ICMS do produto');
			else if ($post['numicms_produto'] != "") {
				$form->err[] = "Para esta Situação Tributária, não se deve preencher a alíquota de ICMS.";
			}

			// verifica se colocou uma aliquota de icms válida
			if ($post['numicms_produto'] != "") {
				$info_icms = $aliquota_icms->getByICMS($post['numicms_produto']);
				if ($info_icms['idaliquota_icms'] == "") $form->err[] = "Esta alíquota de ICMS não está cadastrada na tabela de alíquotas válidas!";
			}

			$form->chk_empty($post['litproduto_comercializado'], 1, 'Produto Comercializado');

      $err = $form->err;

			// verifica se existe algum fornecedor ligado ao produto
			for ($i=1; $i<=$post['total_fornecedores']; $i++) {
				if ( isset( $post["codigo_fornecedor_$i"] ) ) {
					$existe_produto = 1;
					break;
				}
			}
			if ($existe_produto==0) $err[] = "Adicione um ou mais fornecedores ao produto.";
			//------------------------------------------------------

			// verifica se os preços foram preenchidos corretamente
			$count=1;          
			while (isset($post["filial_$count"])) {

				if ( 
					!$form->chk_moeda($post["numpreco_balcao_produto_$count"], 0) ||
					!$form->chk_moeda($post["numpreco_oferta_produto_$count"], 0) ||
					!$form->chk_moeda($post["numpreco_atacado_produto_$count"], 0) ||
					!$form->chk_moeda($post["numpreco_telemarketing_produto_$count"], 0) ||
					!$form->chk_moeda($post["numpreco_custo_produto_$count"], 0)
				) {
					$err[] = "Verifique se todos os preços de todas as filiais foram preenchidos corretamente e possuem valor diferente de 0,00 !";
					break;
				}

				$count++;
			}
			//------------------------------------------------------


		}
		// Adicionar
		else {
			$form->chk_empty($post['iddepartamento'], 1, 'Departamento');
			$form->chk_empty($post['idsecao'], 1, 'Seção');
			$form->chk_empty($post['descricao_produto'], 1, 'Descrição do produto');
			$form->chk_empty($post['localizacao_produto'], 1, 'Localização do produto');
			$form->chk_empty($post['idunidade_venda'], 1, 'Unidade de venda');
			$form->chk_empty($post['situacao_tributaria_produto'], 1, 'Situação tributária');

			$form->chk_empty($post['percentual_max_desconto_produto'], 1, 'Percentual máximo de desconto');
			$form->chk_moeda($post['percentual_max_desconto_produto'], 1, 'Percentual máximo de desconto');		

			$form->chk_empty($post['comissao_interno_produto'], 1, 'Comissão Interno');
			$form->chk_moeda($post['comissao_interno_produto'], 1, 'Comissão Interno');

			$form->chk_empty($post['comissao_externo_produto'], 1, 'Comissão Externo');
			$form->chk_moeda($post['comissao_externo_produto'], 1, 'Comissão Externo');

			$form->chk_empty($post['comissao_representante_produto'], 1, 'Comissão Representante');
			$form->chk_moeda($post['comissao_representante_produto'], 1, 'Comissão Representante');

			$form->chk_empty($post['comissao_operador_telemarketing_produto'], 1, 'Comissão do Operador de telemarketing');
			$form->chk_moeda($post['comissao_operador_telemarketing_produto'], 1, 'Comissão do Operador de telemarketing');

			if ($post['data_cadastro_produto'] != "") $form->chk_IsDate($post['data_cadastro_produto'],'A data de cadastro ');

			// se for tributado, tem que digitar a alíquota
			if ( ($post['situacao_tributaria_produto'] == "T") || ($post['situacao_tributaria_produto'] == "S") )
				$form->chk_moeda($post['icms_produto'], 0, 'ICMS do produto');
			else if ($post['icms_produto'] != "") {
				$form->err[] = "Para esta Situação Tributária, não se deve preencher a alíquota de ICMS.";
			}

			// verifica se colocou uma aliquota de icms válida
			if ($post['icms_produto'] != "") {
				$info_icms = $aliquota_icms->getByICMS($post['icms_produto']);
				if ($info_icms['idaliquota_icms'] == "") $form->err[] = "Esta alíquota de ICMS não está cadastrada na tabela de alíquotas válidas!";
			}

			$form->chk_empty($post['produto_comercializado'], 1, 'Produto Comercializado');

			$err = $form->err;
			
			// verifica se existe algum fornecedor ligado ao produto
			$existe_produto = 0; 
			for ($i=1; $i<=$post['total_fornecedores']; $i++) {
				if ( isset( $post["codigo_fornecedor_$i"] ) ) {
					$existe_produto = 1;
					break;
				}
			}
			if ($existe_produto==0) $err[] = "Adicione um ou mais fornecedores ao produto.";
			//------------------------------------------------------------------------------------

			// verifica se os preços foram preenchidos corretamente
			$count=1;					
			while (isset($post["filial_$count"])) {

				if ( 
					!$form->chk_moeda($post["preco_balcao_produto_$count"], 0) ||
					!$form->chk_moeda($post["preco_oferta_produto_$count"], 0) ||
					!$form->chk_moeda($post["preco_atacado_produto_$count"], 0) ||
					!$form->chk_moeda($post["preco_telemarketing_produto_$count"], 0) ||
					!$form->chk_moeda($post["preco_custo_produto_$count"], 0)
				) {
					$err[] = "Verifique se todos os preços de todas as filiais foram preenchidos corretamente e possuem valor diferente de 0,00 !";
					break;
				}

				$count++;
			} // while
			//------------------------------------------------------------------------------------


		}


		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {
    	if($post['litproduto_comercializado'] == '0') {
    		// verifica se o usuário quer alterar os dados
				$objResponse->addConfirmCommands(1, utf8_encode("Deseja alterar a situação do produto '{$post['litdescricao_produto']}' para não comercializado ? Se sim, o produto será excluído de seus respectivos encartelamentos, referências e não será mais listado."));
    		$objResponse->addScript("document.getElementById('for_produto').submit();");
    	}
      else {
    		// verifica se o usuário quer alterar os dados
				$objResponse->addConfirmCommands(1, utf8_encode("Deseja gravar os dados do Produto ?"));				
      	$objResponse->addScript("document.getElementById('for_produto').submit();");
			}
    }
    // houve erros, logo mostra-os
    else {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
		}

		// retorna o resultado XML
    return $objResponse->getXML();
  }

	/*
	Fun??o: Verifica_Campos_Atualizar_Produto_AJAX
	Verifica se os campos do produto foram preenchidos
	*/
	function Verifica_Campos_Atualizar_Produto_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $produto;
		//---------------------

    $existe_produto = 0;
    
		$parametro = new parametro();
		
		// busca o valor padrao para a validade do orçamento e o máximo de itens
		$list_parametro = $parametro->make_list(0, $conf['rppg']);
		if ( count ($list_parametro) > 0 ) {
			$post['porcentagem_maxima'] = str_replace(",",".",$list_parametro[0]['porcentagem_maxima']);
		}
		else {
			$post['porcentagem_maxima'] = 0;
			
		}

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

	
					$form->chk_empty($post['iddepartamento'], 1, 'Departamento');
					$form->chk_empty($post['idsecao'], 1, 'Seção');
					$form->chk_empty($post['tipo_atribuicao'], 1, 'Tipo de Atribuição');
					$form->chk_empty($post['porcentagem'], 1, 'Porcentagem');

          $err = $form->err;
          $post['porcentagem'] = str_replace(",",".",$post['porcentagem']);
					if($post['porcentagem'] > $post['porcentagem_maxima']) $err[] = "A porcentagem escolhida ultrapassa a porcentagem padrão de " . number_format($post['porcentagem_maxima'],2,",","") ."%!";

		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {
    	    if($post['tipo_atribuicao'] == 'R'){$tipo = "REDUZIR";}
    	    else{$tipo = "AUMENTAR";}
    			$objResponse->addConfirmCommands(1, utf8_encode("Deseja ".$tipo." os preços em " . number_format($post['porcentagem'],2,",","") . "% ?"));
    			$objResponse->addScript("document.getElementById('for_produto').submit();");
    }
    // houve erros, logo mostra-os
    else {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
		}

		// retorna o resultado XML
    return $objResponse->getXML();
  }


?>
