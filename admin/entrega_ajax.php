<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");

	require_once("../entidades/funcionario.php");	
	require_once("../entidades/orcamento.php");
	require_once("../entidades/orcamento_produto.php");
	require_once("../entidades/entrega_produto.php");
	require_once("../entidades/entrega.php");
	require_once("../entidades/cliente.php");
	require_once("../entidades/filial.php");
	require_once("../entidades/cfop.php");
	require_once("../entidades/motivo_cancelamento.php");


  // inicializa templating
  $smarty = new Smarty;

  // ação selecionada
  $flags['action'] = $_GET['ac'];

  // inicializa autenticação
  $auth = new auth();

	//inicializa classe
	$funcionario = new funcionario();	
	$orcamento = new orcamento();
	$orcamento_produto = new orcamento_produto();
	$entrega_produto = new entrega_produto();
	$entrega = new entrega();
	$cliente = new cliente();
	$filial = new filial();
	$cfop = new cfop();
	$motivo_cancelamento = new motivo_cancelamento();


  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();
        
				

  switch($flags['action']) {



	}

	/*
	Função: Busca_Rapida_AJAX
	Verifica se o codigo do orçamento foi preenchido na busca rápida
	*/
	function Busca_Rapida_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		global $smarty;

		global $orcamento_produto;
		global $entrega_produto;
		global $orcamento;
		global $funcionario;
		global $cliente;
		global $cfop;
		global $filial;
		global $motivo_cancelamento;
		global $entrega;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		if($post['tipo'] == 'NF')
		$form->chk_empty($post['nota'], 1, 'Insira o código da Nota Fiscal');
		else
		$form->chk_empty($post['nota'], 1, 'Insira o código da Série D');

		$err = $form->err;

		
    if(count($err) == 0) {

		$info_orcamento = $orcamento->buscaDadosFiscais($post['nota'],$post['tipo']);
		
			if(!$info_orcamento)
			{
				$objResponse->addAssign("info_orcamento","innerHTML",utf8_encode("Esta nota não existe para a filial ".$_SESSION['nomefilial_usuario'].", favor conferir o numero digitado."));
				$objResponse->addClear("info_produto", "innerHTML");
				$objResponse->addClear("info_nota", "innerHTML");
			}
			else{

			//busca detalhes
			$info = $orcamento->getById($info_orcamento['idorcamento']);

			//tratamento das informações para fazer o UPDATE
			$info['numidUltfuncionario'] = $info['idUltfuncionario'];
			$info['numidcliente'] = $info['idcliente'];
			$info['littipoPreco'] = $info['tipoPreco'];
			$info['numvalidade'] = $info['validade'];
			$info['numdesconto'] = $info['desconto'];
			$info['numfrete'] = $info['frete'];
			$info['numoutras_despesas'] = $info['outras_despesas'];
			$info['litnomeClienteProv'] = $info['nomeClienteProv'];
			$info['litinfoClienteProv'] = $info['infoClienteProv'];

			$info['numidcfop'] = $info['idcfop'];
			$info['littipoOrcamento'] = $info['tipoOrcamento'];
			$info['numnumeroNota'] = $info['numeroNota'];
			$info['litobs'] = strip_tags($info['obs']);
			$info['litdados_adicionais'] = strip_tags($info['dados_adicionais']);


			//---------------------------------------------------------------------------

			// formata o numero da nota fiscal
			$numeroDaNota = "000000" . $info['numeroNota'];
			$info['numeroNotaFormatado'] = substr($numeroDaNota,strlen($numeroDaNota)-6,6);
			//---------------------------------------------------------------------------


			//obtém os erros
			$err = $orcamento->err;

			// busca os dados da filial
			$info_filial = $filial->getById($info['idfilial']);

			$info_cfop = $cfop->getById($info['idcfop']);

			// busca os dados do funcionario que criou o Orçamento
			$info_funcionario_criou = $funcionario->getById($info['idfuncionario']);

			// Funcionário alterou por ultimo o Orçamento
			$info_funcionario_alterou = $funcionario->getById($info['idUltfuncionario']);

			if ($info['idfuncionarioNF'] != "") $info_funcionario_criouNF = $funcionario->getById($info['idfuncionarioNF']);
			else $info_funcionario_criouNF = $funcionario->getById($_SESSION['usr_cod']);

			// se selecionou o cliente, busca os dados dele
			if ($info['idcliente'] != "")	{
				$info_cliente = $cliente->getById($info['idcliente']);
				$info_dados_cliente = $cliente->BuscaDadosCliente($info['idcliente']);
			}
			else $flags['nao_selecionou'] = 1;
			
			//busca o nome do cliente
			$info['idcliente_Nome'] = $info_cliente['nome_cliente'];
			$info['idcliente_NomeTemp'] = $info_cliente['nome_cliente'];

			//---------------------------------------


			// busca o motivo do cancelamento, caso tenha sido cancelado
			$info_motivo_cancelamento = $motivo_cancelamento->getById($info['idmotivo_cancelamento']);

			// busca o CFOP
			$info_cfop = $cfop->getById($info['idcfop']);

			// busca o dia e hora atual
			if ($info['datahoraCriacaoNF'] != '0000-00-00 00:00:00') $flags['data_criacaoNF'] = $info['datahoraCriacaoNF_D'] . " " . $info['datahoraCriacaoNF_H'];
			else $flags['data_criacaoNF'] = date('d/m/Y H:i:s');

			//busca os funcionarios da filial
			$list_funcionarios = $funcionario->Seleciona_Funcionarios_Da_Filial($_SESSION['idfilial_usuario'], "T", "A");

			// busca Motico de cancelamento
			$list_motivo_cancelamento = $motivo_cancelamento->make_list_select();

			//------------------------------------------------


			$tabela_nota = utf8_encode("

			<table width='95%' align='center'>
				<tr>
						<td colspan='2' align='center'>
							<table class='tb4cantos' width='100%'>
			
								<tr bgcolor='#F7F7F7'>
									<td colspan='9' align='center'>Dados Gerais: <b>".$conf['area']."</b></td>
								</tr>
			
								<tr>
								<td width='50%'>
									<table>
										<tr>
										<td align='left'>
										Filial:
										".$info_filial['nome_filial']."
										</td>
									</tr>
			
									<tr>
										<td align='left'>
											CFOP:
											".$info_cfop['codigo']."
										</td>
									</tr>
			
									<tr>
										<td align='left'>
											<b>
											Número do Documento Fiscal:
											".$info['numeroNotaFormatado']."
										</b>
										</td>
									</tr>

									<tr>
										<td align='left' colspan='2'>
										Modelo da Nota:
										".$info['modeloNota']."
										</td>
									</tr>
			
								<tr>
									<td align='left' colspan='2'>
										Série da Nota:
										".$info['serieNota']."
									</td>
								</tr>
							</table>
						</td>
	
						<td align='right'>
								Observações:
								".$info['obs']."
						</td>
					</tr>
			
				</table>

							<table class='tb4cantos' width='100%' cellspacing='0'>
			
								<tr>
									<td align='center' width='35%'></td>
									<td class='tb4cantos' align='center' width='35%'>Vendedor / Funcionário</td>
									<td class='tb4cantos' align='center' width='30%'>Data / Hora</td>
								</tr>
			
								<tr>
									<td class='tb4cantos'>Criação da Emissão Fiscal</td>
									<td align='center' class='tb4cantos'>".$info_funcionario_criouNF['nome_funcionario']."</td>
									<td align='center' class='tb4cantos'>".$flags['data_criacaoNF']."</td>
								</tr>

								<tr>
									<td class='tb4cantos'>Criação do Orçamento</td>
									<td align='center' class='tb4cantos'>".$info_funcionario_criou['nome_funcionario']."</td>
									<td align='center' class='tb4cantos'>".$info['datahoraCriacao_D']." ".$info['datahoraCriacao_H']."</td>
								</tr>
			
								<tr>
									<td class='tb4cantos'>Última alteração dos dados</td>
									<td align='center' class='tb4cantos'>".$info_funcionario_alterou['nome_funcionario']."</td>
									<td align='center' class='tb4cantos'>".$info['datahoraUltEmissao_D']." ".$info['datahoraUltEmissao_H']."</td>
								</tr>
			
							</table>
			
						</td>
					</tr>

					<tr><td>&nbsp;</td></tr>
			
					<tr>
						<td colspan='2' align='center'>
							<table class='tb4cantos' width='100%'>
			
								<tr bgcolor='#F7F7F7'>
									<td colspan='9' align='center'>Dados do <b>CLIENTE</b></td>
								</tr>
			
								<input type='hidden' name='dados_cliente_linha_1' id='dados_cliente_linha_1' value='".$info_dados_cliente['dados_cliente_linha_1']."'>
								<input type='hidden' name='dados_cliente_linha_2' id='dados_cliente_linha_2' value='".$info_dados_cliente['dados_cliente_linha_2']."'>
								<input type='hidden' name='dados_cliente_linha_3' id='dados_cliente_linha_3' value='".$info_dados_cliente['dados_cliente_linha_3']."'>
								<input type='hidden' name='dados_cliente_linha_4' id='dados_cliente_linha_4' value='".$info_dados_cliente['dados_cliente_linha_4']."'>
								<input type='hidden' name='dados_cliente_linha_5' id='dados_cliente_linha_5' value='".$info_dados_cliente['dados_cliente_linha_5']."'>
			
									<tr>
										<td>
											Cliente: ".$info_dados_cliente['nome_cliente']."
										</td>
									</tr>
									<tr>
										<td>
											Endereço: ".$info_dados_cliente['logradouro']." &nbsp;&nbsp;&nbsp; ".$info_dados_cliente['numero']." &nbsp;&nbsp;&nbsp;
											Bairro: ".$info_dados_cliente['nome_bairro']." &nbsp;&nbsp;&nbsp;
											Cidade: ".$info_dados_cliente['nome_cidade']." &nbsp;&nbsp;&nbsp;
											Estado: ".$info_dados_cliente['sigla_estado']." &nbsp;&nbsp;&nbsp;
											CEP: ".$info_dados_cliente['cep']."
										</td>
									</tr>
									<tr>
										<td>
											Telefone: ".$info_dados_cliente['telefone_cliente']." &nbsp;&nbsp;&nbsp;
											Fax: ".$info_dados_cliente['fax_cliente']." &nbsp;&nbsp;&nbsp;
											Insc. Est.: ".$info_dados_cliente['inscricao_estadual_cliente']." &nbsp;&nbsp;&nbsp;
											CPF/CNPJ: ".$info_dados_cliente['cpf_cnpj']."
										</td>
									</tr>
								
			
							</table>
						</td>
					</tr>
				</table>
					");
	
			$objResponse->addAssign("info_nota","innerHTML",$tabela_nota);




			$info_produtos = array();
	
			$info_produtos = $orcamento_produto->make_list(0,99999,"WHERE PO.idorcamento =".$info_orcamento['idorcamento']);
	
			$tabela_produtos = utf8_encode("
									<table align='center' width='95%'  class='tb4cantos' id='tabela_orcamento'>
										<tr>
											<td>
												<table align='center' width='100%' cellpadding='5  colspan='5'  id='tabela_orcamento'>
													<tr bgcolor='#F7F7F7'>
														<th width='10%' align='center'>Código</th>
														<th width='35%' align='center'>Produto</th>
														<th width='10%' align='center'>Unidade</th>
														<th width='10%' align='center'>Qtd. Comprada</th>
														<th width='10%' align='center'>Qtd. Entregue</th>
														<th width='10%' align='center'>Qtd. Programada</th>
														<th width='10%' align='center'>Qtd. Disponível</th>
													</tr>
												</table>");

				for ($i=0; $i<count($info_produtos); $i++) {
	
				$qtd_entregue = $entrega_produto->ContaQuantidadeEntregue($info_orcamento['idorcamento'],$info_produtos[$i]['idproduto']);

				$qtd_programada = $entrega_produto->ContaQuantidadeProgramada($info_orcamento['idorcamento'],$info_produtos[$i]['idproduto']);			
	
				$nome_tabela = "tabela_" .$i;

				$tabela_produtos = $tabela_produtos . utf8_encode("
							<tr>
								<td>
									<table width='100%' cellpadding='2' id='$nome_tabela' colspan='5'>
										<input type='hidden' name='$nome_tabela' id='$nome_tabela' value='" . $i . "'/>
										<input type='hidden' name='idproduto_$i' id='idproduto_$i' value='" . $info_produtos[$i]['idproduto'] . "'/>
										<input type='hidden' name='qtd_$i' id='qtd_$i' value='".(str_replace(",",".", $info_produtos[$i]['qtd_produto']) - str_replace(",",".", $qtd_programada['soma']))."'/>
										<tr>
											<td class='tb_bord_baixo' align='left' width='10%'>	".$info_produtos[$i]['codigo_produto']."	</td>

											<td class='tb_bord_baixo' align='left' width='35%'>	".$info_produtos[$i]['descricao_produto']."	</td>

											<td class='tb_bord_baixo' align='center' width='10%'	id='sigla_unidade_venda_$i' >" . $info_produtos[$i]['sigla_unidade_venda'] . "</td>
	
											<td width='10%' id='codigo_produto_$i' class='tb_bord_baixo' align='right'>" . $info_produtos[$i]['qtd_produto'] . "</td>
	
											<td width='10%' id='qtd_entregue_$i' class='tb_bord_baixo' align='right' >" . $qtd_entregue['soma']. "</td>

											<td width='10%' id='qtd_programada_$i' class='tb_bord_baixo' align='right' >" . number_format(((str_replace(",",".",$qtd_programada['soma']) - str_replace(",",".",$qtd_entregue['soma']))),2,",",""). "</td>				
		
											<td width='10%' id='qtd_disponivel_$i' class='tb_bord_baixo' align='right'>" .number_format((str_replace(",",".", $info_produtos[$i]['qtd_produto']) - str_replace(",",".", $qtd_programada['soma'])),2,",",""). "</td>
											
										</tr>
									</table>
								</td>	
							</tr>
								");
				}

			
			$tabela_produtos = $tabela_produtos . utf8_encode("	
																													</td>
																												</tr>
																										</table>");
	
			$objResponse->addAssign("info_orcamento","innerHTML",$tabela_produtos);
			
			//lista os registros
			$list = $entrega->make_list(0,99999,"WHERE ENT.idorcamento =".$info_orcamento['idorcamento']);
			
			$tabela_lista = 

			utf8_encode("<br>
		
			<table width='95%' align='center'>
			
				<tr>
					<th align='center'>No</th>
					<th align='center'>Transportador</th>
					<th align='center'>Programada para:</th>
					<th align='center'>Entrega Realizada?</th>
					<th align='center'>Realizada em:</th>
				</tr>");
		
			for ($i=0; $i<count($list); $i++) {

			 if($list[$i]['realizada'] == "Não") $list[$i]['dataRealizada'] = "---";
 			 if($list[$i]['idmotivo_cancelamento'] != NULL) 
			 {
				$list[$i]['dataRealizada'] = "<font color='red'> CANCELADO </font>";
				$list[$i]['realizada'] = "<font color='red'> CANCELADO </font>";
				}
				$tabela_lista = $tabela_lista . utf8_encode("

	        <tr  bgcolor = "." >
	        	
						<td align='center'><a class='menu_item' href = ".$_SERVER['PHP_SELF']."?ac=editar&identrega=".$list[$i]['identrega'].">".$list[$i]['identrega']."</a></td>
						<td><a class='menu_item' href = ".$_SERVER['PHP_SELF']."?ac=editar&identrega=".$list[$i]['identrega'].">".$list[$i]['nome_transportador']."</a></td>
						<td align='center'><a class='menu_item' href = ".$_SERVER['PHP_SELF']."?ac=editar&identrega=".$list[$i]['identrega'].">".$list[$i]['dataMarcada']."</a></td>
						<td align='center'><a class='menu_item' href = ".$_SERVER['PHP_SELF']."?ac=editar&identrega=".$list[$i]['identrega'].">".$list[$i]['realizada']."</a></td>
						<td align='center'><a class='menu_item' href = ".$_SERVER['PHP_SELF']."?ac=editar&identrega=".$list[$i]['identrega'].">".$list[$i]['dataRealizada']."</a></td>
	        </tr>
	        
	        <tr>
	          <td class='row' height='1' bgcolor='#999999' colspan='9'></td>
	        </tr>
	      
				");
      
				}
			$tabela_lista = $tabela_lista . utf8_encode("</table>
      
     ");

			$objResponse->addAssign("info_produto","innerHTML",$tabela_lista);

			}
		}

		// retorna o resultado XML
    return $objResponse->getXML();
	 }




	/*
	Função: Insere_Produto_Encartelamento_AJAX
	Insere um produto ou um encartelamento dinamicamente na tabela html
	*/
	function ReduzQuantidade ($i,$post="") {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
	
		// se nao tiver nada no post, da um submit no form para pegar ele
		if ($post == "") {
			
			$objResponse->addScript("xajax_ReduzQuantidade($i,xajax.getFormValues('for'));");
		}
		else {
	
		
		$qtd_disponivel = str_replace(",",".", $post["qtd_$i"]) - str_replace(",",".",$post["qtd_a_entregar_$i"]);

		if($qtd_disponivel < 0)
		{
				
			$objResponse->addAssign("qtd_disponivel_$i","innerHTML","<font color='red'>".number_format($qtd_disponivel,2,",",""))."</font>";
			
			
		}
		else
		{	
		$objResponse->addAssign("qtd_disponivel_$i","innerHTML",number_format($qtd_disponivel,2,",",""));
		}
		}

		// retorna o resultado XML
    return $objResponse->getXML();

}


	/*
	Função: Verifica_Campos_Busca_Rapida_AJAX
	Verifica se o codigo do orçamento foi preenchido na busca rápida
	*/
	function Verifica_Campos_Busca_Rapida_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;


		global $orcamento_produto;
		global $entrega_produto;
		global $orcamento;
		global $funcionario;
		global $cliente;
		global $cfop;
		global $filial;
		global $motivo_cancelamento;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		if($post['tipo'] == 'NF')
		$form->chk_empty($post['nota'], 1, 'Insira o código da Nota Fiscal');
		else
		$form->chk_empty($post['nota'], 1, 'Insira o código da Série D');

		$err = $form->err;


    if(count($err) == 0) {

		$info_orcamento = $orcamento->buscaDadosFiscais($post['nota'],$post['tipo']);
		if(!$info_orcamento)
		{
			$objResponse->addAssign("info_orcamento","innerHTML",utf8_encode("Esta nota não existe para a filial ".$_SESSION['nomefilial_usuario']." ou foi cancelada, favor conferir o numero digitado."));
			$objResponse->addAssign("produtos","innerHTML",utf8_encode("Por favor, selecione uma nota válida."));		
			$objResponse->addAssign("valido","value","0");	
		}
		else{
			
			$objResponse->addAssign("valido","value","1");	
			$objResponse->addAssign("idorcamento","value",$info_orcamento['idorcamento']);
			//busca detalhes
			$info = $orcamento->getById($info_orcamento['idorcamento']);

			//tratamento das informações para fazer o UPDATE
			$info['numidUltfuncionario'] = $info['idUltfuncionario'];
			$info['numidcliente'] = $info['idcliente'];
			$info['littipoPreco'] = $info['tipoPreco'];
			$info['numvalidade'] = $info['validade'];
			$info['numdesconto'] = $info['desconto'];
			$info['numfrete'] = $info['frete'];
			$info['numoutras_despesas'] = $info['outras_despesas'];
			$info['litnomeClienteProv'] = $info['nomeClienteProv'];
			$info['litinfoClienteProv'] = $info['infoClienteProv'];

			$info['numidcfop'] = $info['idcfop'];
			$info['littipoOrcamento'] = $info['tipoOrcamento'];
			$info['numnumeroNota'] = $info['numeroNota'];
			$info['litobs'] = strip_tags($info['obs']);
			$info['litdados_adicionais'] = strip_tags($info['dados_adicionais']);


			//---------------------------------------------------------------------------

			// formata o numero da nota fiscal
			$numeroDaNota = "000000" . $info['numeroNota'];
			$info['numeroNotaFormatado'] = substr($numeroDaNota,strlen($numeroDaNota)-6,6);
			//---------------------------------------------------------------------------


			//obtém os erros
			$err = $orcamento->err;

			// busca os dados da filial
			$info_filial = $filial->getById($info['idfilial']);

			$info_cfop = $cfop->getById($info['idcfop']);

			// busca os dados do funcionario que criou o Orçamento
			$info_funcionario_criou = $funcionario->getById($info['idfuncionario']);

			// Funcionário alterou por ultimo o Orçamento
			$info_funcionario_alterou = $funcionario->getById($info['idUltfuncionario']);

			if ($info['idfuncionarioNF'] != "") $info_funcionario_criouNF = $funcionario->getById($info['idfuncionarioNF']);
			else $info_funcionario_criouNF = $funcionario->getById($_SESSION['usr_cod']);

			// se selecionou o cliente, busca os dados dele
			if ($info['idcliente'] != "")	{
				$info_cliente = $cliente->getById($info['idcliente']);
				$info_dados_cliente = $cliente->BuscaDadosCliente($info['idcliente']);
			}
			else $flags['nao_selecionou'] = 1;
			
			//busca o nome do cliente
			$info['idcliente_Nome'] = $info_cliente['nome_cliente'];
			$info['idcliente_NomeTemp'] = $info_cliente['nome_cliente'];

			//---------------------------------------


			// busca o motivo do cancelamento, caso tenha sido cancelado
			$info_motivo_cancelamento = $motivo_cancelamento->getById($info['idmotivo_cancelamento']);

			// busca o CFOP
			$info_cfop = $cfop->getById($info['idcfop']);

			// busca o dia e hora atual
			if ($info['datahoraCriacaoNF'] != '0000-00-00 00:00:00') $flags['data_criacaoNF'] = $info['datahoraCriacaoNF_D'] . " " . $info['datahoraCriacaoNF_H'];
			else $flags['data_criacaoNF'] = date('d/m/Y H:i:s');

			//busca os funcionarios da filial
			$list_funcionarios = $funcionario->Seleciona_Funcionarios_Da_Filial($_SESSION['idfilial_usuario'], "T", "A");

			// busca Motico de cancelamento
			$list_motivo_cancelamento = $motivo_cancelamento->make_list_select();

			//------------------------------------------------


			$tabela_orcamento = utf8_encode("

			<table width='95%' align='center'>
				<tr>
						<td colspan='2' align='center'>
							<table class='tb4cantos' width='100%'>
			
								<tr bgcolor='#F7F7F7'>
									<td colspan='9' align='center'>Dados Gerais: <b>".$conf['area']."</b></td>
								</tr>
			
								<tr>
								<td width='50%'>
									<table>
										<tr>
										<td align='left'>
										Filial:
										".$info_filial['nome_filial']."
										</td>
									</tr>
			
									<tr>
										<td align='left'>
											CFOP:
											".$info_cfop['codigo']."
										</td>
									</tr>
			
									<tr>
										<td align='left'>
											<b>
											Número do Documento Fiscal:
											".$info['numeroNotaFormatado']."
										</b>
										</td>
									</tr>

									<tr>
										<td align='left' colspan='2'>
										Modelo da Nota:
										".$info['modeloNota']."
										</td>
									</tr>
			
								<tr>
									<td align='left' colspan='2'>
										Série da Nota:
										".$info['serieNota']."
									</td>
								</tr>
							</table>
						</td>
	
						<td align='right'>
								Observações:
								".$info['obs']."
						</td>
					</tr>
			
				</table>

							<table class='tb4cantos' width='100%' cellspacing='0'>
			
								<tr>
									<td align='center' width='35%'></td>
									<td class='tb4cantos' align='center' width='35%'>Vendedor / Funcionário</td>
									<td class='tb4cantos' align='center' width='30%'>Data / Hora</td>
								</tr>
			
								<tr>
									<td class='tb4cantos'>Criação da Emissão Fiscal</td>
									<td align='center' class='tb4cantos'>".$info_funcionario_criouNF['nome_funcionario']."</td>
									<td align='center' class='tb4cantos'>".$flags['data_criacaoNF']."</td>
								</tr>

								<tr>
									<td class='tb4cantos'>Criação do Orçamento</td>
									<td align='center' class='tb4cantos'>".$info_funcionario_criou['nome_funcionario']."</td>
									<td align='center' class='tb4cantos'>".$info['datahoraCriacao_D']." ".$info['datahoraCriacao_H']."</td>
								</tr>
			
								<tr>
									<td class='tb4cantos'>Última alteração dos dados</td>
									<td align='center' class='tb4cantos'>".$info_funcionario_alterou['nome_funcionario']."</td>
									<td align='center' class='tb4cantos'>".$info['datahoraUltEmissao_D']." ".$info['datahoraUltEmissao_H']."</td>
								</tr>
			
							</table>
			
						</td>
					</tr>

					<tr><td>&nbsp;</td></tr>
			
					<tr>
						<td colspan='2' align='center'>
							<table class='tb4cantos' width='100%'>
			
								<tr bgcolor='#F7F7F7'>
									<td colspan='9' align='center'>Dados do <b>CLIENTE</b></td>
								</tr>
			
								<input type='hidden' name='dados_cliente_linha_1' id='dados_cliente_linha_1' value='".$info_dados_cliente['dados_cliente_linha_1']."'>
								<input type='hidden' name='dados_cliente_linha_2' id='dados_cliente_linha_2' value='".$info_dados_cliente['dados_cliente_linha_2']."'>
								<input type='hidden' name='dados_cliente_linha_3' id='dados_cliente_linha_3' value='".$info_dados_cliente['dados_cliente_linha_3']."'>
								<input type='hidden' name='dados_cliente_linha_4' id='dados_cliente_linha_4' value='".$info_dados_cliente['dados_cliente_linha_4']."'>
								<input type='hidden' name='dados_cliente_linha_5' id='dados_cliente_linha_5' value='".$info_dados_cliente['dados_cliente_linha_5']."'>
			
									<tr>
										<td>
											Cliente: ".$info_dados_cliente['nome_cliente']."
										</td>
									</tr>
									<tr>
										<td>
											Endereço: ".$info_dados_cliente['logradouro']." &nbsp;&nbsp;&nbsp; ".$info_dados_cliente['numero']." &nbsp;&nbsp;&nbsp;
											Bairro: ".$info_dados_cliente['nome_bairro']." &nbsp;&nbsp;&nbsp;
											Cidade: ".$info_dados_cliente['nome_cidade']." &nbsp;&nbsp;&nbsp;
											Estado: ".$info_dados_cliente['sigla_estado']." &nbsp;&nbsp;&nbsp;
											CEP: ".$info_dados_cliente['cep']."
										</td>
									</tr>
									<tr>
										<td>
											Telefone: ".$info_dados_cliente['telefone_cliente']." &nbsp;&nbsp;&nbsp;
											Fax: ".$info_dados_cliente['fax_cliente']." &nbsp;&nbsp;&nbsp;
											Insc. Est.: ".$info_dados_cliente['inscricao_estadual_cliente']." &nbsp;&nbsp;&nbsp;
											CPF/CNPJ: ".$info_dados_cliente['cpf_cnpj']."
										</td>
									</tr>
								
			
							</table>
						</td>
					</tr>
				</table>
					");
	
			$objResponse->addAssign("info_orcamento","innerHTML",$tabela_orcamento);
	
			
			$info_produtos = array();
	
			$info_produtos = $orcamento_produto->make_list(0,99999,"WHERE PO.idorcamento =".$info_orcamento['idorcamento']);

			if($_GET['ac'] == 'editar')
			{		
				if($post['realizada'] == 'S')
					{			
					$tabela_produtos = utf8_encode("
											<table align='center' width='100%'  class='tb4cantos' id='tabela_orcamento'>
												<tr>
													<td>
														<table align='center' width='100%' cellpadding='5  colspan='5'  id='tabela_orcamento'>
															<tr bgcolor='#F7F7F7'>
																<th width='7%' align='center'>Código</th>
																<th width='30%' align='center'>Produto</th>
																<th width='8%' align='center'>Unidade</th>
																<th width='10%' align='center'>Qtd. Comprada</th>
																<th width='10%' align='center'>Qtd. Total Entregue</th>
																<th width='10%' align='center'>Qtd. Total Programada</th>
																<th width='10%' align='center'>Qtd. Disponível</th>
																<th width='15%' align='center'>Qtd. Entregue</th>
															</tr>
														</table>");
			
					for ($i=0; $i<count($info_produtos); $i++) {
			
						$qtd_entregue = $entrega_produto->ContaQuantidadeEntregue($info_orcamento['idorcamento'],$info_produtos[$i]['idproduto']);

						$qtd_programada = $entrega_produto->ContaQuantidadeProgramada($info_orcamento['idorcamento'],$info_produtos[$i]['idproduto']);			

						$nome_tabela = "tabela_" .$i;
		
						$qtd_entrega_produto = $entrega_produto->getById($post['identrega'],$info_produtos[$i]['idproduto']);
					
						$tabela_produtos = $tabela_produtos . utf8_encode("
									<tr>
										<td>
											<table width='100%' cellpadding='2' id='$nome_tabela' colspan='5'>
												<input type='hidden' name='$nome_tabela' id='$nome_tabela' value='" . $i . "'/>
												<input type='hidden' name='idproduto_$i' id='idproduto_$i' value='" . $info_produtos[$i]['idproduto'] . "'/>
												<input type='hidden' name='qtd_$i' id='qtd_$i' value='".($info_produtos[$i]['qtd_produto'] - $qtd_programada['soma'] + $qtd_entrega_produto['qtd']). "'/>
												<tr>
													<td class='tb_bord_baixo' align='center' width='7%'>	".$info_produtos[$i]['codigo_produto']."	</td>

													<td class='tb_bord_baixo' align='left' width='30%'>	".$info_produtos[$i]['descricao_produto']."	</td>
			
													<td class='tb_bord_baixo' align='center' width='8%'	id='sigla_unidade_venda_$i' >" . $info_produtos[$i]['sigla_unidade_venda'] . "</td>

													<td width='10%' id='codigo_produto_$i' class='tb_bord_baixo' align='right'>" . $info_produtos[$i]['qtd_produto'] . "</td>
			
													<td width='10%' id='qtd_entregue_$i' class='tb_bord_baixo' align='right' >" . number_format((str_replace(",",".",$qtd_entregue['soma'])),2,",",""). "</td>

													<td width='10%' id='qtd_programada_$i' class='tb_bord_baixo' align='right' >" . number_format(((str_replace(",",".",$qtd_programada['soma']) - str_replace(",",".",$qtd_entregue['soma']))),2,",",""). "</td>				

													<td width='10%' id='qtd_disponivel_$i' class='tb_bord_baixo' align='right'>" .number_format((str_replace(",",".",$info_produtos[$i]['qtd_produto']) - str_replace(",",".",$qtd_programada['soma'] )),2,",",""). "</td>
																										
													<td width='15%' align='center' class='tb_bord_baixo' align='center'> 
														<input readonly class='tiny' type='text' name='qtd_a_entregar_$i' id='qtd_a_entregar_$i' maxlength='10' value='".number_format($qtd_entrega_produto['qtd'],2,",","")."' onkeydown='FormataValor(".'"'."qtd_a_entregar_$i".'"'.");xajax_ReduzQuantidade($i);' onkeyup='FormataValor(".'"'."qtd_a_entregar_$i".'"'.");xajax_ReduzQuantidade($i);'/>
													</td>
													
												</tr>
											</table>
										</td>	
									</tr>
										");
						}
						}
					else
					{
								$tabela_produtos = utf8_encode("
											<table align='center' width='100%'  class='tb4cantos' id='tabela_orcamento'>
												<tr>
													<td>
														<table align='center' width='100%' cellpadding='5  colspan='5'  id='tabela_orcamento'>
															<tr bgcolor='#F7F7F7'>
																<th width='7%' align='center'>Código</th>
																<th width='30%' align='center'>Produto</th>
																<th width='8%' align='center'>Unidade</th>
																<th width='10%' align='center'>Qtd. Comprada</th>
																<th width='10%' align='center'>Qtd. Entregue</th>
																<th width='10%' align='center'>Qtd. Programada</th>
																<th width='10%' align='center'>Qtd. Disponível</th>
																<th width='15%' align='center'>Qtd. A entregar</th>
															</tr>
														</table>");
			
					for ($i=0; $i<count($info_produtos); $i++) {
			
						$qtd_entregue = $entrega_produto->ContaQuantidadeEntregue($info_orcamento['idorcamento'],$info_produtos[$i]['idproduto']);

						$qtd_programada = $entrega_produto->ContaQuantidadeProgramada($info_orcamento['idorcamento'],$info_produtos[$i]['idproduto']);			
			
						$nome_tabela = "tabela_" .$i;
		
						$qtd_entrega_produto = $entrega_produto->getById($post['identrega'],$info_produtos[$i]['idproduto']);

						$tabela_produtos = $tabela_produtos . utf8_encode("
									<tr>
										<td>
											<table width='100%' cellpadding='2' id='$nome_tabela' colspan='5'>
												<input type='hidden' name='$nome_tabela' id='$nome_tabela' value='" . $i . "'/>
												<input type='hidden' name='idproduto_$i' id='idproduto_$i' value='" . $info_produtos[$i]['idproduto'] . "'/>
												<input type='hidden' name='qtd_$i' id='qtd_$i' value='".(str_replace(",",".",$info_produtos[$i]['qtd_produto']) -str_replace(",",".",$qtd_programada['soma']) + $qtd_entrega_produto['qtd']). "'/>
												<input type='hidden' name='qtd_editar_$i' id='qtd_editar_$i' value='".($qtd_programada['soma'] - $qtd_entrega_produto['qtd']). "'/>
												<tr>
													<td class='tb_bord_baixo' align='center' width='7%'>	".$info_produtos[$i]['codigo_produto']."	</td>		
												
													<td class='tb_bord_baixo' align='left' width='30%'>	".$info_produtos[$i]['descricao_produto']."	</td>
			
													<td class='tb_bord_baixo' align='center' width='8%'	id='sigla_unidade_venda_$i' >" . $info_produtos[$i]['sigla_unidade_venda'] . "</td>

													<td width='10%' id='codigo_produto_$i' class='tb_bord_baixo' align='right'>" . $info_produtos[$i]['qtd_produto'] . "</td>
			
													<td width='10%' id='qtd_entregue_$i' class='tb_bord_baixo' align='right' >" . number_format((str_replace(",",".",$qtd_entregue['soma'])),2,",",""). "</td>

													<td width='10%' id='qtd_programada_$i' class='tb_bord_baixo' align='right' >" . number_format((str_replace(",",".",$qtd_programada['soma']) - 	str_replace(",",".",$qtd_entrega_produto['qtd']) - str_replace(",",".",$qtd_entregue['soma'])),2,",",""). "</td>				

													<td width='10%' id='qtd_disponivel_$i' class='tb_bord_baixo' align='right'>" .number_format((str_replace(",",".",$info_produtos[$i]['qtd_produto']) -  str_replace(",",".",$qtd_programada['soma']) ),2,",",""). "</td>
																										
													<td width='15%' align='center' class='tb_bord_baixo' align='center'> 
														<input class='tiny' type='text' name='qtd_a_entregar_$i' id='qtd_a_entregar_$i' maxlength='10' value='".number_format($qtd_entrega_produto['qtd'],2,",","")."' onkeydown='FormataValor(".'"'."qtd_a_entregar_$i".'"'.");xajax_ReduzQuantidade($i);' onkeyup='FormataValor(".'"'."qtd_a_entregar_$i".'"'.");xajax_ReduzQuantidade($i);'/>
													</td>
													
												</tr>
											</table>
										</td>	
									</tr>
										");
						}
					}
			}
			else
			{		

			$tabela_produtos = utf8_encode("
					<table align='center' width='100%'  class='tb4cantos' id='tabela_orcamento'>
						<tr>
							<td>
								<table align='center' width='100%' cellpadding='5  colspan='5'  id='tabela_orcamento'>
									<tr bgcolor='#F7F7F7'>
										<th width='7%' align='center'>Código</th>
										<th width='30%' align='center'>Produto</th>
										<th width='8%' align='center'>Unidade</th>
										<th width='10%' align='center'>Qtd. Comprada</th>
										<th width='10%' align='center'>Qtd. Entregue</th>
										<th width='10%' align='center'>Qtd. Programada</th>
										<th width='10%' align='center'>Qtd. Disponível</th>
										<th width='15%' align='center'>Qtd. A entregar</th>
									</tr>
								</table>");



				for ($i=0; $i<count($info_produtos); $i++) {
	
				$qtd_entregue = $entrega_produto->ContaQuantidadeEntregue($info_orcamento['idorcamento'],$info_produtos[$i]['idproduto']);

				$qtd_programada = $entrega_produto->ContaQuantidadeProgramada($info_orcamento['idorcamento'],$info_produtos[$i]['idproduto']);			
	
				$nome_tabela = "tabela_" .$i;

				$tabela_produtos = $tabela_produtos . utf8_encode("
							<tr>
								<td>
									<table width='100%' cellpadding='2' id='$nome_tabela' colspan='5'>
										<input type='hidden' name='$nome_tabela' id='$nome_tabela' value='" . $i . "'/>
										<input type='hidden' name='idproduto_$i' id='idproduto_$i' value='" . $info_produtos[$i]['idproduto'] . "'/>
										<input type='hidden' name='qtd_$i' id='qtd_$i' value='".(str_replace(",",".",$info_produtos[$i]['qtd_produto']) -  str_replace(",",".", $qtd_programada['soma']))."'/>

										<tr>
											<td class='tb_bord_baixo' align='center' width='7%'>	".$info_produtos[$i]['codigo_produto']."	</td>

											<td class='tb_bord_baixo' align='left' width='30%'>	".$info_produtos[$i]['descricao_produto']."	</td>
	
											<td class='tb_bord_baixo' align='center' width='8%'	id='sigla_unidade_venda_$i' >" . $info_produtos[$i]['sigla_unidade_venda'] . "</td>

											<td width='10%' id='codigo_produto_$i' class='tb_bord_baixo' align='right'>" . $info_produtos[$i]['qtd_produto'] . "</td>
	
											<td width='10%' id='qtd_entregue_$i' class='tb_bord_baixo' align='right' >" . $qtd_entregue['soma']. "</td>

											<td width='10%' id='qtd_programada_$i' class='tb_bord_baixo' align='right' >" . number_format(( $qtd_programada['soma'] - $qtd_entregue['soma']) ,2,",",""). "</td>		

											<td width='10%' id='qtd_disponivel_$i' class='tb_bord_baixo' align='right'>" .number_format((str_replace(",",".", $info_produtos[$i]['qtd_produto']) - str_replace(",",".", $qtd_programada['soma'])),2,",",""). "</td>
											
											<td width='15%' align='center' class='tb_bord_baixo' align='center'> 
												<input class='tiny' type='text' name='qtd_a_entregar_$i' id='qtd_a_entregar_$i' maxlength='10' value='' onkeydown='FormataValor(".'"'."qtd_a_entregar_$i".'"'.");xajax_ReduzQuantidade($i);' onkeyup='FormataValor(".'"'."qtd_a_entregar_$i".'"'.");xajax_ReduzQuantidade($i);'/>
											</td>
											
										</tr>
									</table>
								</td>	
							</tr>
								");
				}

			}
			$tabela_produtos = $tabela_produtos . utf8_encode("	
																													</td>
																												</tr>
																										</table>");
	
			$objResponse->addAssign("produtos","innerHTML",$tabela_produtos);
	

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
	Função: Verifica_Campos_Entrega_AJAX
	Verifica se os campos da tranferencia de estoque foram preenchidos
	*/

	function Verifica_Campos_Entrega_AJAX ($post , $gerar="") {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $orcamento_produto;
		global $funcionario;

		//---------------------


		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		if($_GET['ac'] == "adicionar")
		{
			$form->chk_isDate($post['dataMarcada'], 'A data programada');
			$form->chk_empty($post['realizada'], 1, 'o status da entrega');
			$form->chk_empty($post['idfuncionario'], 1, 'o funcionário');
			$form->chk_empty($post['senha_funcionario'], 1, 'a senha do funcionário');

			if($post['realizada'] == "S")$form->chk_isDate($post['dataRealizada'], 'A data realizada');
			

			$err = $form->err;
	  
			// verifica se a senha do cliente confere
			$info_funcionario = $funcionario->getById($post['idfuncionario']);
			$senha = md5($post['senha_funcionario']);
	    if($post['senha_funcionario'] != "")
				if($info_funcionario['senha_funcionario'] != $senha) $err[] = "A senha digitada está incorreta.";

			
			if($post['valido'] == 0)
			{ $err[]= "Escolha uma nota válida!";}
			else{			
			$info = $orcamento_produto->make_list(0,99999,"WHERE PO.idorcamento =".$post['idorcamento']);
			
			for ($i=0; $i<=count($info); $i++) {
		
						
				if (str_replace(",",".", $post["qtd_a_entregar_$i"]) > 0 ) {
					
						if(($post["qtd_$i"] - str_replace(",",".", $post["qtd_a_entregar_$i"])) < 0) $estoque = 1;
					
					$existe_produto = 1;
					
				}
			}
			
			if ($estoque==1) $err[]="Um ou mais produtos excedem a quantidade disponível para entrega!";
			if ($existe_produto==0) $err[]="É preciso fazer no mínimo uma entrega de produto!";
			
			
			}
			
			
			
				
		}
		else
		{
			if($post['confirmada'] == 1){

			$form->chk_empty($post['numidUltFuncionario'], 1, 'o funcionário');
			$form->chk_empty($post['senha_funcionario'], 1, 'a senha do funcionário');
			$form->chk_empty($post['numidmotivo_cancelamento'],1, 'o motivo do cancelamento');

			$err = $form->err;

			// verifica se a senha do cliente confere
			$info_funcionario = $funcionario->getById($post['numidUltFuncionario']);
			$senha = md5($post['senha_funcionario']);
	    if($post['senha_funcionario'] != "")
				if($info_funcionario['senha_funcionario'] != $senha) $err[] = "A senha digitada está incorreta.";

			}

			else{
			$form->chk_isDate($post['litdataMarcada'],'A data programada');
			$form->chk_empty($post['litrealizada'], 1, 'o status da entrega');
			$form->chk_empty($post['numidUltFuncionario'], 1, 'o funcionário');
			$form->chk_empty($post['senha_funcionario'], 1, 'a senha do funcionário');

			if($post['litrealizada'] == "S")$form->chk_isDate($post['litdataRealizada'], 'A data realizada');
			
		 		
			$err = $form->err;
	    
			// verifica se a senha do cliente confere
			$info_funcionario = $funcionario->getById($post['numidUltFuncionario']);
			$senha = md5($post['senha_funcionario']);
	    if($post['senha_funcionario'] != "")
				if($info_funcionario['senha_funcionario'] != $senha) $err[] = "A senha digitada está incorreta.";

			
			if($post['valido'] == 0)
			{ $err[]= "Escolha uma nota válida!"; }
			else{			
			$info = $orcamento_produto->make_list(0,99999,"WHERE PO.idorcamento =".$post['idorcamento']);
			if ($err[0] == NULL) unset($err[0]);
			for ($i=0; $i<=count($info); $i++) {
		
						
				if (str_replace(",",".", $post["qtd_a_entregar_$i"]) > 0 ) {
					
						if((str_replace(",",".",$post["qtd_$i"]) - str_replace(",",".", $post["qtd_a_entregar_$i"])) < 0) $estoque = 1;
					
					$existe_produto = 1;
					break;
				}
			}
			
			if ($estoque==1) $err[]="Um ou mais produtos excedem a quantidade disponível para entrega!";
			if ($existe_produto==0) $err[]="É preciso fazer no mínimo uma entrega de produto!";
			
			
			}
			}
		}	

	

		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {
		
		if($post['confirmada'] == 1)
		{
			$objResponse->addConfirmCommands(1, utf8_encode("Confirma o cancelamento?"));
    	$objResponse->addScript("document.getElementById('for').submit();");
		}
		else{
			// verifica se confirma está transação.
			$objResponse->addConfirmCommands(1, utf8_encode("Confirma a entrega?"));
    	$objResponse->addScript("document.getElementById('for').submit();");
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
	Função: Seleciona_Produtos_Orcamento
 	Seleciona os fornecedores do produto e colocam eles dinamicamente na tabela html
	*/
	function Seleciona_Produtos_Orcamento ($identrega,$realizada='N') {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;

		//---------------------
		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

			$list_sql = "	SELECT
											ENT.identrega, ORC.tipoOrcamento, ORC.numeroNota
										FROM
											{$conf['db_name']}entrega ENT
           						INNER JOIN {$conf['db_name']}orcamento ORC ON ENT.idorcamento=ORC.idorcamento
										WHERE
											 ENT.identrega = $identrega
											";

		//manda fazer a paginação
		$list_q = $db->query($list_sql);

		if($list_q){

			//busca os registros no banco de dados e monta o vetor de retorno
			$cont = 0;

			$list = $db->fetch_array($list_q);
				
				if($realizada == 'S') $post['realizada'] = "S";
    		$post['tipo'] = $list['tipoOrcamento'];
				$post['nota'] = $list['numeroNota'];
				$post['identrega'] = $list['identrega'];
				// acrescenta o XML que foi retornado no objeto atual
				$objResponse->loadXML( Verifica_Campos_Busca_Rapida_AJAX($post) );

		}
		

		// retorna o resultado XML
    return $objResponse->getXML();

	}



  	/*
	Função: Seleciona_Info_Transportador
 	Seleciona os fornecedores do produto e colocam eles dinamicamente na tabela html
	*/
	function Seleciona_Info_Transportador ($codigoTransportador) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;

		//---------------------
		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
		

			$list_sql = "	SELECT
											TRP.*, EDR.*, BAR.*, CID.*, EST.*
										FROM
											{$conf['db_name']}transportador TRP
												 LEFT OUTER JOIN {$conf['db_name']}endereco EDR ON TRP.idendereco_transportador=EDR.idendereco
												 LEFT OUTER JOIN {$conf['db_name']}bairro BAR ON EDR.idbairro=BAR.idbairro
												 LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade=CID.idcidade
												 LEFT OUTER JOIN {$conf['db_name']}estado EST ON EDR.idestado=EST.idestado
										WHERE
											 TRP.idtransportador = $codigoTransportador
										ORDER BY
											TRP.nome_transportador ASC ";


		//manda fazer a paginação
		$list_q = $db->query($list_sql);

		
		if($list_q){
		
			$list = $db->fetch_array($list_q);

			$info_transportador = utf8_encode("
					<table class=tb4cantos width=40%>
						<tr>
							<td>Endereço: " . $list['logradouro'] . " " . $list['numero'] . " " . $list['complemento'] . "</td>
							<td> CEP: " .  $list['cep'] . "</td>
						</tr>
						<tr>
							<td>Bairro: " . $list['nome_bairro'] . " " . $list['nome_cidade'] . " " . $list['sigla_estado'] . "</td>
						</tr>
						<tr>
							<td>Telefone: " . $list['telefone_transportador'] . "</td>
						</tr>
						<tr>
							<td>CPF/CNPJ: " . $list['cpf_cnpj'] . "</td>
						</tr>
					</table>");

			$info_transportador = ereg_replace("(\r\n|\n|\r)", "", $info_transportador);

				
			
			$objResponse->addAssign("dados_transportador", "innerHTML", $info_transportador);
		}
		
		

		// retorna o resultado XML
    return $objResponse->getXML();

	}


  	/*
	Função: Seleciona_Produtos_Impressao_AJAX
 	Seleciona os produtos da entrega e colocam eles dinamicamente na tabela html para impressao.
	*/
	function Seleciona_Produtos_Impressao_AJAX ($identrega) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;

		//---------------------
		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
		

			$list_sql = "	SELECT
											PRD.codigo_produto,PRD.descricao_produto, UNV.sigla_unidade_venda, ENTPRD.qtd
										FROM
											{$conf['db_name']}entrega_produto ENTPRD
												 INNER JOIN {$conf['db_name']}produto PRD ON ENTPRD.idproduto=PRD.idproduto
												 INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda=UNV.idunidade_venda
										WHERE
											 ENTPRD.identrega = $identrega
										ORDER BY
											PRD.descricao_produto ASC ";


		//manda fazer a paginação
		$list_q = $db->query($list_sql);

		
		if($list_q){
		
			while($list = $db->fetch_array($list_q))
			{
			if($list['qtd'] > 0){
			$nome_tabela = "produto_".$i;
			$info_produto = utf8_encode("
							<table width='100%' cellpadding='2' id='$nome_tabela' colspan='5'>
									<tr>
										<td  align='center' width='10%'>	".$list['codigo_produto']."	</td>

										<td  align='left' width='30%'>	".$list['descricao_produto']."	</td>

										<td  align='center' width='5%'	id='sigla_unidade_venda_$i' >" . $list['sigla_unidade_venda'] . "</td>

										<td width='10%' id='codigo_produto_$i' align='center'>" . number_format($list['qtd'],2,",","") . "</td>

									</tr>
								</table>");

			$objResponse->addAppend("info_produto", "innerHTML", $info_produto);
			$i++;}
			}
		}
		
		

		// retorna o resultado XML
    return $objResponse->getXML();

	}


?>
