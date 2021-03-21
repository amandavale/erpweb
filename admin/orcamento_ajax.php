<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/orcamento.php");
	require_once("../entidades/produto.php");
	require_once("../entidades/funcionario.php");
	require_once("../entidades/produto_referencia.php");
	require_once("../entidades/parametro.php");
	require_once("../entidades/cliente.php");
	//require_once("../entidades/conta_receber.php");
	require_once("../entidades/cfop.php");
	require_once("../entidades/aliquota_icms.php");
	require_once("../entidades/modo_recebimento.php");
	require_once("../entidades/serie.php");
	
	require_once("../entidades/movimento.php");	


  // inicializa templating
  $smarty = new Smarty;

  // configura diretórios
  $smarty->template_dir = "../common/tpl";
  $smarty->compile_dir =   "../common/tpl_c";

  // seta configurações
  $smarty->assign("conf", $conf);

  // ação selecionada
  $flags['action'] = $_GET['ac'];


  // inicializa autenticação
  $auth = new auth();

	//inicializa classe
	$orcamento = new orcamento();
	$produto = new produto();
	$funcionario = new funcionario();
	$produto_referencia = new produto_referencia();
	$parametro = new parametro();
	$cliente = new cliente();
	//$conta_receber = new conta_receber();
	$cfop = new cfop();
	$aliquota_icms = new aliquota_icms();
	$modo_recebimento = new modo_recebimento();
	$serie = new serie();
	
	$movimento = new movimento();

  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();


	/*
	Função: Insere_Produto_Encartelamento_AJAX
	Insere um produto ou um encartelamento dinamicamente na tabela html
	*/
	function Insere_Produto_Encartelamento_AJAX ($post, $action="") {
		
		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		global $flags;
		
		global $produto;
		global $produto_referencia;
		global $aliquota_icms;
		//---------------------

		// cria o objeto xajaxResponse
    	$objResponse = new xajaxResponse();

		$form->chk_empty($post['idproduto'], 1, 'Produto');
		$form->chk_moeda($post['qtd_produto'], 0, "Quantidade");

		$err = $form->err;

		// se houveram erros, mostra-os
    	if(count($err) != 0) {
    		
    		
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
    	}
   		// se nao houveram erros, insere na tabela html
		else {

			// Se for um produto, busca os dados dele
			if ($post['idproduto_Tipo'] == "P") {
				$array_produtos = $produto->Busca_Dados_Produto_AJAX($post['idproduto'], $post['qtd_produto'], "A");
			}
			// se for um encartelamento, busca os produtos dele
			else if ($post['idproduto_Tipo'] == "E") {
				$array_produtos = $produto->Busca_Dados_Encartelamento_AJAX($post['idproduto'], $post['qtd_produto']);
			}

			// verifica se pode inserir mais produtos no orçamento
			$total_produtos_validos = 0;
			for ($i=1; $i<=$post['total_produtos']; $i++) {
				if ( isset( $post["idproduto_$i"] ) ) {
					$total_produtos_validos++;

					for ($j=0; $j<count($array_produtos); $j++) {
						// verifica se o item ja nao está no orçamento
						if ($post["idproduto_$i"] == $array_produtos[$j]['idproduto']) {

							$mensagem = "Não será possível inserir este produto. Ele já contido neste orçamento ! Caso esteja inserindo um Encartelamento, verifique se algum produto dele já está contido neste orçamento !";
							$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));

							// retorna o resultado XML
			    		return $objResponse->getXML();
						}
					} // fim do for
					//---------------------------------------
				}
			} // fim do for


			if ($total_produtos_validos + count($array_produtos) > $post['maximoItensOrcamento']) {
				$mensagem = "Não será possível inserir este produto. O número máximo de produtos permitido por orçamento é " . $post['maximoItensOrcamento'] . " !";
				$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
				
				// retorna o resultado XML
    		return $objResponse->getXML();
			}	
			//---------------------------------------------------


			// incremente 1 na quantidade de total_produtos
			$total_produtos = intval($post['total_produtos']);

			// percorre os produtos e vai adicionando-os na tabela
			for ($i=0; $i<count($array_produtos); $i++) {

				// nome da tabela criada
				$total_produtos++;
	      		$nome_tabela = "tabela_produto_" . $total_produtos;

				// operacao de editar
				if ($post['operacao'] == "editar") {
					$array_produtos[$i]['desconto_produto'] = $post['desconto_produto'];
				}	
				// operacao de adicionar
				else {
					// se o desconto do produto for vazio, coloca 0,00
					if ($array_produtos[$i]['desconto_produto'] == "") $array_produtos[$i]['desconto_produto'] = "0,00";
				}

				// busca os produtos de referencia
				$list_produtos_referencia = $produto_referencia->BuscaProdutosReferencia($array_produtos[$i]['idproduto'], $post['tipoPreco']);

				if (count($list_produtos_referencia) > 0) {
					// Formata a mensagem para ser exibida
					$produtos_de_referencia = $form->FormataMensagemAjuda($list_produtos_referencia, 2);

		  		$imagem_produto_referencia = "<img class='lightbulb' src='../common/img/lampada.png' width='16' height='16' border='0' align='middle' onmouseover=" . '"' . 'pmaTooltip(' . "'" . $produtos_de_referencia . "'" . '); return false;" onmouseout=' . '"' . "swapTooltip(" . "'default'" . '); return false;" />';
				}
				else $imagem_produto_referencia = "";
				//---------------------------------------------


				// verifica qual o preço vai ser utilizado
				if ($post['tipoPreco'] == "B") $preco = $array_produtos[$i]['preco_balcao_produto'];
				else if ($post['tipoPreco'] == "O") $preco = $array_produtos[$i]['preco_oferta_produto'];
				else if ($post['tipoPreco'] == "A") $preco = $array_produtos[$i]['preco_atacado_produto'];
				else if ($post['tipoPreco'] == "T") $preco = $array_produtos[$i]['preco_telemarketing_produto'];

				$informacoes_do_produto = "Peso Bruto (kg): " . number_format($array_produtos[$i]['peso_bruto_produto'],2,",","") . "<br>";
				$informacoes_do_produto .= "Peso Líquido (kg): " . number_format($array_produtos[$i]['peso_liquido_produto'],2,",","") . "<br>";
				$informacoes_do_produto .= "Estoque Atual (" . $array_produtos[$i]['sigla_unidade_venda'] . "): " . number_format($array_produtos[$i]['qtd_produto_estoque'],2,",","") . " * <br>";
				$informacoes_do_produto .= "* Baseado nesta filial";

				$imagem_falta_estoque = "";
				if ($array_produtos[$i]['qtd_produto_estoque'] <= 0) $imagem_falta_estoque = "<img src='" . $conf['addr'] . "/common/img/exclamacao.png' />";


				// verifica qual é a ação verdadeira
				$acao = $_GET['ac'];
				if ($action != "") $acao = $action;

				if ($acao != "editarNF") {

					// tabela de produtos
					$tabela = utf8_encode("
								<tr>
									<td class='tb_bord_baixo' align='left' width='5%'>
										<input type='hidden' name='idproduto_$total_produtos' id='idproduto_$total_produtos' value='" . $array_produtos[$i]['idproduto'] . "' />
										<input type='hidden' name='descricao_produto_$total_produtos' id='descricao_produto_$total_produtos' value='" . $array_produtos[$i]['descricao_produto'] . "' />
										<input type='hidden' name='maximo_desconto_produto_$total_produtos' id='maximo_desconto_produto_$total_produtos' value='" . $array_produtos[$i]['percentual_max_desconto_produto'] . "' />
										<input type='hidden' name='preco_unitario_produto_$total_produtos' id='preco_unitario_produto_$total_produtos' value='" . $preco . "' />

										<input type='hidden' name='peso_bruto_produto_$total_produtos' id='peso_bruto_produto_$total_produtos' value='" . $array_produtos[$i]['peso_bruto_produto'] . "' />
										<input type='hidden' name='peso_liquido_produto_$total_produtos' id='peso_liquido_produto_$total_produtos' value='" . $array_produtos[$i]['peso_liquido_produto'] . "' />

										<input type='hidden' name='preco_balcao_produto_$total_produtos' id='preco_balcao_produto_$total_produtos' value='" . $array_produtos[$i]['preco_balcao_produto'] . "' />
										<input type='hidden' name='preco_oferta_produto_$total_produtos' id='preco_oferta_produto_$total_produtos' value='" . $array_produtos[$i]['preco_oferta_produto'] . "' />
										<input type='hidden' name='preco_atacado_produto_$total_produtos' id='preco_atacado_produto_$total_produtos' value='" . $array_produtos[$i]['preco_atacado_produto'] . "' />
										<input type='hidden' name='preco_telemarketing_produto_$total_produtos' id='preco_telemarketing_produto_$total_produtos' value='" . $array_produtos[$i]['preco_telemarketing_produto'] . "' />

										" . $imagem_produto_referencia . " " . $array_produtos[$i]['idproduto'] . "
									</td>


									<td class='tb_bord_baixo' align='left' width='35%'>
										<a href='#' class='link_referencia' onmouseover=" . '"' . "pmaTooltip(" . "'" . $informacoes_do_produto . "'" . "); return false;" . '"' . " onmouseout=" . '"' . "swapTooltip(" . "'default'" . "); return false;" . '"' . " />
										" . $imagem_falta_estoque . "&nbsp;" . $array_produtos[$i]['descricao_produto'] . "
										</a>
									</td>

									<td id='sigla_unidade_venda_$total_produtos' class='tb_bord_baixo' align='center' width='5%'>" . $array_produtos[$i]['sigla_unidade_venda'] . "</td>

									<td class='tb_bord_baixo' align='center' width='10%'>
										<input class='short' type='text' name='qtd_produto_$total_produtos' id='qtd_produto_$total_produtos' value='" . $array_produtos[$i]['qtd_produto'] . "' maxlength='10' onkeydown=" . '"' . "FormataValor('" . "qtd_produto_$total_produtos" . "')" . '"' . " onkeyup=" . '"' . "FormataValor('" . "qtd_produto_$total_produtos" . "')" . '"' . " onblur=" . '"' . "xajax_Calcula_Total_AJAX();" . '"' . " />
									</td>
									
									<td id='preco_produto_$total_produtos' class='tb_bord_baixo' align='center' width='10%'>" . number_format($preco,2,",","") . "</td>
									
									<td class='tb_bord_baixo' align='center' width='10%'>
										<input class='tiny' type='text' name='desconto_produto_$total_produtos' id='desconto_produto_$total_produtos' value='" . $array_produtos[$i]['desconto_produto'] . "' maxlength='10' onkeydown=" . '"' . "FormataValor('" . "desconto_produto_$total_produtos" . "')" . '"' . " onkeyup=" . '"' . "FormataValor('" . "desconto_produto_$total_produtos" . "')" . '"' . " onblur=" . '"' . "xajax_Calcula_Total_AJAX();" . '"' . " />									
									</td>
									
									<td id='preco_produto_final_$total_produtos' class='tb_bord_baixo' align='center' width='10%'>" . number_format($preco,2,",","") . "</td>
									<td id='total_produto_final_$total_produtos' class='tb_bord_baixo' align='center' width='10%'>0,00</td>

									<td class='tb_bord_baixo' align='center' width='5%'>
										<a href='javascript:;' onclick=" . '"' . "xajax_Deleta_Produto_Encartelamento_AJAX(" . "'" . $total_produtos . "', '" . $array_produtos[$i]['idproduto'] . "'" . ");" . '"' . "><img src='../common/img/delete.gif'></a>
									</td>
								</tr>
						");
					
					}
					// é a ediação de uma NF (inclui o CST e o ICMS)
					else {

						// busca as aliquotas de ICMS, sendo que a aliquota padrão do produto já vem selecionada.
						//$lista_aliquotas_icms = $aliquota_icms->BuscaAliquotasICMS($array_produtos[$i]['icms_produto']);
            
            if($post['tipoOrcamento'] == "ECF" ) {$edicao = "readonly style='background: #E7E7E7' ";} else $edicao = "";
            

						// tabela de produtos
						$tabela = utf8_encode("
								<tr>
									<td class='tb_bord_baixo' align='left' width='5%'>
										<input type='hidden' name='idproduto_$total_produtos' id='idproduto_$total_produtos' value='" . $array_produtos[$i]['idproduto'] . "' />
										<input type='hidden' name='descricao_produto_$total_produtos' id='descricao_produto_$total_produtos' value='" . $array_produtos[$i]['descricao_produto'] . "' />
										<input type='hidden' name='maximo_desconto_produto_$total_produtos' id='maximo_desconto_produto_$total_produtos' value='" . $array_produtos[$i]['percentual_max_desconto_produto'] . "' />
										<input type='hidden' name='preco_unitario_produto_$total_produtos' id='preco_unitario_produto_$total_produtos' value='" . $preco . "' />

										<input type='hidden' name='peso_bruto_produto_$total_produtos' id='peso_bruto_produto_$total_produtos' value='" . $array_produtos[$i]['peso_bruto_produto'] . "' />
										<input type='hidden' name='peso_liquido_produto_$total_produtos' id='peso_liquido_produto_$total_produtos' value='" . $array_produtos[$i]['peso_liquido_produto'] . "' />

										<input type='hidden' name='preco_balcao_produto_$total_produtos' id='preco_balcao_produto_$total_produtos' value='" . $array_produtos[$i]['preco_balcao_produto'] . "' />
										<input type='hidden' name='preco_oferta_produto_$total_produtos' id='preco_oferta_produto_$total_produtos' value='" . $array_produtos[$i]['preco_oferta_produto'] . "' />
										<input type='hidden' name='preco_atacado_produto_$total_produtos' id='preco_atacado_produto_$total_produtos' value='" . $array_produtos[$i]['preco_atacado_produto'] . "' />
										<input type='hidden' name='preco_telemarketing_produto_$total_produtos' id='preco_telemarketing_produto_$total_produtos' value='" . $array_produtos[$i]['preco_telemarketing_produto'] . "' />

										" . $imagem_produto_referencia . " " . $array_produtos[$i]['idproduto'] . "
									</td>


									<td class='tb_bord_baixo' align='left' width='20%'>
										<a href='#' class='link_referencia' onmouseover=" . '"' . "pmaTooltip(" . "'" . $informacoes_do_produto . "'" . "); return false;" . '"' . " onmouseout=" . '"' . "swapTooltip(" . "'default'" . "); return false;" . '"' . " />
										" . $imagem_falta_estoque . "&nbsp;" . $array_produtos[$i]['descricao_produto'] . "
										</a>
									</td>

									<td id='sigla_unidade_venda_$total_produtos' class='tb_bord_baixo' align='center' width='5%'>" . $array_produtos[$i]['sigla_unidade_venda'] . "</td>

									<td class='tb_bord_baixo' align='center' width='10%'>
										<input class='short' type='text' name='qtd_produto_$total_produtos' id='qtd_produto_$total_produtos' value='" . $array_produtos[$i]['qtd_produto'] . "' maxlength='10' onkeydown=" . '"' . "FormataValor('" . "qtd_produto_$total_produtos" . "')" . '"' . " onkeyup=" . '"' . "FormataValor('" . "qtd_produto_$total_produtos" . "')" . '"' . " onblur=" . '"' . "xajax_Calcula_Total_AJAX();" . '"' . " />
									</td>
									
									<td id='preco_produto_$total_produtos' class='tb_bord_baixo' align='center' width='10%'>" . number_format($preco,2,",","") . "</td>
									
									<td class='tb_bord_baixo' align='center' width='10%'>
									 	<input $edicao class='tiny' type='text' name='desconto_produto_$total_produtos' id='desconto_produto_$total_produtos' value='" . $array_produtos[$i]['desconto_produto'] . "' maxlength='10' onkeydown=" . '"' . "FormataValor('" . "desconto_produto_$total_produtos" . "')" . '"' . " onkeyup=" . '"' . "FormataValor('" . "desconto_produto_$total_produtos" . "')" . '"' . " onblur=" . '"' . "xajax_Calcula_Total_AJAX();" . '"' . " />
										<input type='hidden' name='desconto_produto_ecf_$total_produtos' id='desconto_produto_ecf_$total_produtos' value='0' />
									</td>
									
									<td id='preco_produto_final_$total_produtos' class='tb_bord_baixo' align='center' width='10%'>" . number_format($preco,2,",","") . "</td>
									<td id='total_produto_final_$total_produtos' class='tb_bord_baixo' align='center' width='10%'>0,00</td>

									<td class='tb_bord_baixo' align='center' width='4%'>
										" . $array_produtos[$i]['cst_produto'] . "
										<input type='hidden' name='cst_produto_$total_produtos' id='cst_produto_$total_produtos' value='" . $array_produtos[$i]['cst_produto'] . "' />
										<input type='hidden' name='icms_produto_formatado_$total_produtos' id='icms_produto_formatado_$total_produtos' value='" . $array_produtos[$i]['icms_produto_formatado'] . "' />
									</td>

									<td class='tb_bord_baixo' align='center' width='3%'>
										" . $array_produtos[$i]['situacao_tributaria_produto'] . "
									</td>

									<td class='tb_bord_baixo' align='center' width='8%'>
										" . number_format($array_produtos[$i]['icms_produto'],2,",","") . "
										<input type='hidden' name='icms_produto_$total_produtos' id='icms_produto_$total_produtos' value='" . $array_produtos[$i]['icms_produto'] . "' />										
									</td>

									<td class='tb_bord_baixo' align='center' width='5%'>
										<a href='javascript:;' onclick=" . '"' . "xajax_Deleta_Produto_Encartelamento_AJAX(" . "'" . $total_produtos . "', '" . $array_produtos[$i]['idproduto'] . "'" . ");" . '"' . "><img src='../common/img/delete.gif'></a>
									</td>
								</tr>
						");

				}

        //conserta bug do addAppend no mozilla  
        // usa o addCreate no mozilla  
        if($_SESSION['browser_usuario']) {
          // adiciona a tabela
          $objResponse->addCreate("div_produtos", "table", "$nome_tabela");
          $objResponse->addAssign("$nome_tabela", "innerHTML", "$tabela");
          $objResponse->addAssign("$nome_tabela", 'width', "100%");
          $objResponse->addAssign("$nome_tabela", 'cellpadding', "5");
        }
				// usa o addAppend no IE
        else {
          $tabela = '<table id="'.$nome_tabela.'" width="100%" cellpadding="5">'.$tabela.'</table>';
          $objResponse->addAppend("div_produtos", "innerHTML", $tabela);
        }
      

			} // fim do for

			$objResponse->addAssign("total_produtos", "value", $total_produtos);


			// limpa os campos inseridos
			$objResponse->addClear("idproduto", "value");
			$objResponse->addClear("idproduto_Nome", "value");
			$objResponse->addClear("idproduto_NomeTemp", "value");
			$objResponse->addClear("idproduto_Tipo", "value");
			$objResponse->addAssign("idproduto_Flag", "className", "nao_selecionou");

			$objResponse->addClear("qtd_produto", "value");


			if ($post['operacao'] != "editar") {
				// calcula o total
				$objResponse->loadXML( Calcula_Total_AJAX () );
			}

		}

		// retorna o resultado XML
    return $objResponse->getXML();

  }


	/*
	Função: Insere_Produto_Encartelamento_IMPRESSAO_AJAX
	Insere um produto ou um encartelamento dinamicamente na tabela html para a IMPRESSAO DO ORÇAMENTO
	*/
	function Insere_Produto_Encartelamento_IMPRESSAO_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $produto;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		$form->chk_empty($post['idproduto'], 1, 'Produto');
		$form->chk_empty($post['qtd_produto'], 1, 'Quantidade');


		$err = $form->err;

		// se houveram erros, mostra-os
    if(count($err) != 0) {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
    }
    // se nao houveram erros, insere na tabela html
		else {

			// Se for um produto, busca os dados dele
			if ($post['idproduto_Tipo'] == "P") {
				$array_produtos = $produto->Busca_Dados_Produto_AJAX($post['idproduto'], $post['qtd_produto'], "A");
			}
			// se for um encartelamento, busca os produtos dele
			else if ($post['idproduto_Tipo'] == "E") {
				$array_produtos = $produto->Busca_Dados_Encartelamento_AJAX($post['idproduto'], $post['qtd_produto']);
			}

			// verifica se pode inserir mais produtos no orçamento
			$total_produtos_validos = 0;
			for ($i=1; $i<=$post['total_produtos']; $i++) {
				if ( isset( $post["idproduto_$i"] ) ) {
					$total_produtos_validos++;

					for ($j=0; $j<count($array_produtos); $j++) {
						// verifica se o item ja nao está no orçamento
						if ($post["idproduto_$i"] == $array_produtos[$j]['idproduto']) {
							
							$mensagem = "Não será possível inserir este produto. Ele já está neste orçamento !";
							$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));

							// retorna o resultado XML
			    		return $objResponse->getXML();
						}
					} // fim do for
					//---------------------------------------
				}
			} // fim do for

			if ($total_produtos_validos + count($array_produtos) > $post['maximoItensOrcamento']) {
				$mensagem = "Não será possível inserir este produto. O número máximo de produtos permitido por orçamento é " . $post['maximoItensOrcamento'] . " !";
				$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));

				// retorna o resultado XML
    		return $objResponse->getXML();
			}
			//---------------------------------------------------


			// incremente 1 na quantidade de total_produtos
			$total_produtos = intval($post['total_produtos']);

			// percorre os produtos e vai adicionando-os na tabela
			for ($i=0; $i<count($array_produtos); $i++) {

				// nome da tabela criada
				$total_produtos++;
	      $nome_tabela = "tabela_produto_" . $total_produtos;

				// operacao de editar
				if ($post['operacao'] == "editar") {
					$array_produtos[$i]['desconto_produto'] = $post['desconto_produto'];
				}
				// operacao de adicionar
				else {
					// se o desconto do produto for vazio, coloca 0,00
					if ($array_produtos[$i]['desconto_produto'] == "") $array_produtos[$i]['desconto_produto'] = "0,00";
				}

				// verifica qual o preço vai ser utilizado
				// usa o preço do produto da época da compra
				if (isset($post['preco_unitario_produto'])) {
					$preco = $post['preco_unitario_produto'];
					
					$array_produtos[$i]['preco_balcao_produto'] = $preco;
					$array_produtos[$i]['preco_oferta_produto'] = $preco;
					$array_produtos[$i]['preco_atacado_produto'] = $preco;
					$array_produtos[$i]['preco_telemarketing_produto'] = $preco;
				}
				// usa o preço do produto atualizado
				else {
					if ($post['tipoPreco'] == "B") $preco = $array_produtos[$i]['preco_balcao_produto'];
					else if ($post['tipoPreco'] == "O") $preco = $array_produtos[$i]['preco_oferta_produto'];
					else if ($post['tipoPreco'] == "A") $preco = $array_produtos[$i]['preco_atacado_produto'];
					else if ($post['tipoPreco'] == "T") $preco = $array_produtos[$i]['preco_telemarketing_produto'];
				}

				if ($_GET['ac'] != "editarNF") {

					// tabela de produtos
					$tabela = utf8_encode("
								<table width='100%' cellpadding='5' id='$nome_tabela'>
									<tr>
										<td align='left' width='10%'>
											<input type='hidden' name='idproduto_$total_produtos' id='idproduto_$total_produtos' value='" . $array_produtos[$i]['idproduto'] . "' />
											<input type='hidden' name='descricao_produto_$total_produtos' id='descricao_produto_$total_produtos' value='" . $array_produtos[$i]['descricao_produto'] . "' />
											<input type='hidden' name='maximo_desconto_produto_$total_produtos' id='maximo_desconto_produto_$total_produtos' value='" . $array_produtos[$i]['percentual_max_desconto_produto'] . "' />
											<input type='hidden' name='preco_unitario_produto_$total_produtos' id='preco_unitario_produto_$total_produtos' value='" . $preco . "' />
	
											<input type='hidden' name='preco_balcao_produto_$total_produtos' id='preco_balcao_produto_$total_produtos' value='" . $array_produtos[$i]['preco_balcao_produto'] . "' />
											<input type='hidden' name='preco_oferta_produto_$total_produtos' id='preco_oferta_produto_$total_produtos' value='" . $array_produtos[$i]['preco_oferta_produto'] . "' />
											<input type='hidden' name='preco_atacado_produto_$total_produtos' id='preco_atacado_produto_$total_produtos' value='" . $array_produtos[$i]['preco_atacado_produto'] . "' />
											<input type='hidden' name='preco_telemarketing_produto_$total_produtos' id='preco_telemarketing_produto_$total_produtos' value='" . $array_produtos[$i]['preco_telemarketing_produto'] . "' />
	
											" . $array_produtos[$i]['idproduto'] . "
										</td>
	
										<td align='left' width='30%'>" . $array_produtos[$i]['descricao_produto'] . "</td>
										<td id='sigla_unidade_venda_$total_produtos' align='center' width='5%'>" . $array_produtos[$i]['sigla_unidade_venda'] . "</td>
	
										<td align='center' width='10%'>
											<input type='hidden' name='qtd_produto_$total_produtos' id='qtd_produto_$total_produtos' value='" . $array_produtos[$i]['qtd_produto'] . "'  />
											" . $array_produtos[$i]['qtd_produto'] . "
										</td>
	
										<td id='preco_produto_$total_produtos' align='center' width='10%'>" . number_format($preco,2,",","") . "</td>
	
										<td align='center' width='10%'>
										<input type='hidden' name='desconto_produto_$total_produtos' id='desconto_produto_$total_produtos' value='" . $array_produtos[$i]['desconto_produto'] . "' />
										" . $array_produtos[$i]['desconto_produto'] . "
										</td>
	
										<td id='preco_produto_final_$total_produtos' align='center' width='10%'>" . number_format($preco,2,",","") . "</td>
										<td id='total_produto_final_$total_produtos' align='center' width='10%'>0,00</td>
	
									</tr>
								</table>
							");

					}
					// é a ediação de uma NF (inclui o CST e o ICMS)
					else {

						// tabela de produtos
						$tabela = utf8_encode("
								<table width='100%' cellpadding='5' id='$nome_tabela'>
									<tr>
										<td align='left' width='5%'>
											<input type='hidden' name='idproduto_$total_produtos' id='idproduto_$total_produtos' value='" . $array_produtos[$i]['idproduto'] . "' />
											<input type='hidden' name='descricao_produto_$total_produtos' id='descricao_produto_$total_produtos' value='" . $array_produtos[$i]['descricao_produto'] . "' />
											<input type='hidden' name='maximo_desconto_produto_$total_produtos' id='maximo_desconto_produto_$total_produtos' value='" . $array_produtos[$i]['percentual_max_desconto_produto'] . "' />
											<input type='hidden' name='preco_unitario_produto_$total_produtos' id='preco_unitario_produto_$total_produtos' value='" . $preco . "' />
	
											<input type='hidden' name='preco_balcao_produto_$total_produtos' id='preco_balcao_produto_$total_produtos' value='" . $array_produtos[$i]['preco_balcao_produto'] . "' />
											<input type='hidden' name='preco_oferta_produto_$total_produtos' id='preco_oferta_produto_$total_produtos' value='" . $array_produtos[$i]['preco_oferta_produto'] . "' />
											<input type='hidden' name='preco_atacado_produto_$total_produtos' id='preco_atacado_produto_$total_produtos' value='" . $array_produtos[$i]['preco_atacado_produto'] . "' />
											<input type='hidden' name='preco_telemarketing_produto_$total_produtos' id='preco_telemarketing_produto_$total_produtos' value='" . $array_produtos[$i]['preco_telemarketing_produto'] . "' />
	
											" . $array_produtos[$i]['idproduto'] . "
										</td>
	
										<td align='left' width='20%'>" . $array_produtos[$i]['descricao_produto'] . "</td>
										<td id='sigla_unidade_venda_$total_produtos' align='center' width='5%'>" . $array_produtos[$i]['sigla_unidade_venda'] . "</td>
	
										<td align='center' width='10%'>
											<input type='hidden' name='qtd_produto_$total_produtos' id='qtd_produto_$total_produtos' value='" . $array_produtos[$i]['qtd_produto'] . "'  />
											" . $array_produtos[$i]['qtd_produto'] . "
										</td>
	
										<td id='preco_produto_$total_produtos' align='center' width='10%'>" . number_format($preco,2,",","") . "</td>
	
										<td align='center' width='10%'>
										<input type='hidden' name='desconto_produto_$total_produtos' id='desconto_produto_$total_produtos' value='" . $array_produtos[$i]['desconto_produto'] . "' />
										" . $array_produtos[$i]['desconto_produto'] . "
										</td>
	
										<td id='preco_produto_final_$total_produtos' align='center' width='10%'>" . number_format($preco,2,",","") . "</td>
										<td id='total_produto_final_$total_produtos' align='center' width='10%'>0,00</td>

										<td id='cst_produto_$total_produtos' align='center' width='7%'>" . $post['cst_produto'] . "</td>

										<td align='center' width='3%'>
											" . $array_produtos[$i]['situacao_tributaria_produto'] . "
										</td>

										<td id='icms_produto_$total_produtos' align='right' width='10%'>" . $post['aliquota_icms_produto'] . "</td>
	
									</tr>
								</table>
							");

					}				




				// adiciona a tabela
				$objResponse->addAppend("div_produtos", "innerHTML", $tabela);

			} // fim do for

			$objResponse->addAssign("total_produtos", "value", $total_produtos);


			// limpa os campos inseridos
			$objResponse->addClear("idproduto", "value");
			$objResponse->addClear("idproduto_Nome", "value");
			$objResponse->addClear("idproduto_NomeTemp", "value");
			$objResponse->addClear("idproduto_Tipo", "value");
			$objResponse->addAssign("idproduto_Flag", "className", "nao_selecionou");

			$objResponse->addClear("qtd_produto", "value");


			if ($post['operacao'] != "editar") {
				// calcula o total
				$objResponse->loadXML( Calcula_Total_AJAX () );
			}

		}

		// retorna o resultado XML
    return $objResponse->getXML();

  }


	/*
	Função: Calcula_Total_AJAX
	Calcula o total do orçamento
	*/
	function Calcula_Total_AJAX ($post = "") {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		// se nao tiver nada no post, da um submit no form para pegar ele
		if ($post == "") {
			$objResponse->addScript("xajax_Calcula_Total_AJAX(xajax.getFormValues('for_orcamento'));");
		}
		else {

			$sub_total_produtos = 0;
			$valor_icms_produtos = 0;
			
			$peso_bruto_total = 0;
			$peso_liquido_total = 0;

			// verifica qual o preço vai ser utilizado
			if ($post['tipoPreco'] == "B") $campoPreco = 'preco_balcao_produto';
			else if ($post['tipoPreco'] == "O") $campoPreco = 'preco_oferta_produto';
			else if ($post['tipoPreco'] == "A") $campoPreco = 'preco_atacado_produto';
			else if ($post['tipoPreco'] == "T") $campoPreco = 'preco_telemarketing_produto';


			// percorre os produtos e atualiza os preços
			for ($i=1; $i<=$post['total_produtos']; $i++) {
				
				if ( isset( $post["idproduto_$i"] ) ) {
				
					// verifica qual preço será utilizado
					$post["preco_unitario_produto_$i"] = $post[$campoPreco . "_" . $i];
					$objResponse->addAssign("preco_unitario_produto_$i", "value", $post["preco_unitario_produto_$i"]);
					
     			$preco = number_format($post["preco_unitario_produto_$i"],2,",","");
     			$objResponse->addAssign("preco_produto_$i", "innerHTML", $preco);
     			//--------------------------------------------------------------------------

					// desconto do produto
          $desconto_produto = str_replace(",",".",$post["desconto_produto_$i"]);
          
          // se deu um desconto no preço do produto maior do que o permitido, da uma mensagem e volta o desconto para 0,00
          if ($desconto_produto > $post["maximo_desconto_produto_$i"]) {
          	$post["maximo_desconto_produto_$i"] = number_format($post["maximo_desconto_produto_$i"],2,",","");
          	$mensagem = "O máximo percentual de desconto para o produto '" . $post["descricao_produto_$i"] . "' é " . $post["maximo_desconto_produto_$i"] . "% ! O desconto será voltado para 0,00% .";
						$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
          	
          	// volta o desconto para 0,00
          	$post["desconto_produto_$i"] = "0,00";
          	$desconto_produto = str_replace(",",".",$post["desconto_produto_$i"]);
          	$objResponse->addAssign("desconto_produto_$i", "value", $post["desconto_produto_$i"]);
					}	
          
          // preço final do produto
					$preco_produto_final = $post["preco_unitario_produto_$i"] * (1 - ($desconto_produto/100));
					$preco_produto_final = number_format($preco_produto_final,2,",","");
					$objResponse->addAssign("preco_produto_final_$i", "innerHTML", $preco_produto_final);
					
					// total a ser pago pelo produto
					$preco_produto_final = str_replace(",",".",$preco_produto_final);
					$quantidade_produto = str_replace(",",".",$post["qtd_produto_$i"]);
					$total_produto_final = $preco_produto_final * $quantidade_produto;

					// faz o truncamento para 2 casas decimais, ou seja, não arredonda (as maquinas de ECF não arredondam)
					$total_produto_final = number_format($total_produto_final,5,",","");
					$total_produto_final = substr($total_produto_final, 0, strlen($total_produto_final)-3);


					//$total_produto_final = number_format($total_produto_final,2,",","");
					$objResponse->addAssign("total_produto_final_$i", "innerHTML", $total_produto_final);

					// atualiza o subtotal
					$total_produto_final = str_replace(",",".",$total_produto_final);
					$sub_total_produtos += $total_produto_final;

					// calcula o valor do ICMS
					$icms = $total_produto_final * ($post["icms_produto_$i"]/100);
					$valor_icms_produtos += $icms;

					// atualiza os pesos
					$peso_bruto_total += $post["peso_bruto_produto_$i"] * $quantidade_produto;
					$peso_liquido_total += $post["peso_liquido_produto_$i"] * $quantidade_produto;


				}	
				
			} // fim do for
      

	
			
			// atualiza os pesos liquido e bruto
			$peso_bruto_total = number_format($peso_bruto_total,2,",","");
			$peso_liquido_total = number_format($peso_liquido_total,2,",","");
			$objResponse->addAssign("Peso_Bruto_Total", "innerHTML", $peso_bruto_total);
			$objResponse->addAssign("Peso_Liquido_Total", "innerHTML", $peso_liquido_total);
			//--------------------------------------------
			

			// Valor dor ICMS
			$valor_icms_produtos = $form->FormataMoedaParaExibir($valor_icms_produtos);
			$objResponse->addAssign("valor_icms", "value", $valor_icms_produtos);

			// desconto geral
      $desconto = str_replace(",",".",$post["desconto"]);

			// frete
      $frete = $form->FormataMoedaParaInserir($post["frete"]);

			// outras despesas
			$outras_despesas = $form->FormataMoedaParaInserir($post["outras_despesas"]);

			// valor do seguro
			$valor_seguro = $form->FormataMoedaParaInserir($post["valor_seguro"]);

			// calcula Total
      $total = $sub_total_produtos - $desconto + $frete + $outras_despesas + $valor_seguro;
			$objResponse->addAssign("valor_total_produtos", "value", $sub_total_produtos);

			// Mostra o Total dos Produtos e o Total da Nota
			$sub_total_produtos = number_format($sub_total_produtos,2,",","");
			$objResponse->addAssign("TotalProdutos", "innerHTML", $sub_total_produtos);
			$objResponse->addAssign("valor_total_nota", "value", $total);
			$objResponse->addAssign("valor_total_financiar", "value", $total);
			
			$post['valor_total_nota'] = $total;
			$total = number_format($total,2,",","");
			$objResponse->addAssign("TotalNota", "innerHTML", $total);
			$objResponse->addAssign("TotalFinanciar", "innerHTML", $total);

			// atualiza os dados da conta a receber
			$objResponse->addAssign("TotalNota2", "innerHTML", $total);
			$objResponse->addAssign("TotalVista", "innerHTML", "0,00");
			$objResponse->addAssign("TotalPrazo", "innerHTML", "0,00");
			$objResponse->addAssign("TotalFinal", "innerHTML", "0,00");

			$objResponse->addAssign("valor_total_a_vista", "value", "0.00");
			$objResponse->addAssign("valor_total_a_prazo", "value", "0.00");
			
			// deleta todas as contas a receber ja geradas
			$objResponse->loadXML( Deleta_Contas_Receber_AJAX( intval($post['total_contas_receber_a_vista']), intval($post['total_contas_receber_a_prazo']) ) );
			//-------------------------------------------------------
      if($post["tipoOrcamento"] == "ECF")
      {
      
        $objResponse->addAssign("base_calculo_icms","style.background","#E7E7E7");        
        $objResponse->addAssign("base_calculo_icms","readOnly","true");
        
        
        $objResponse->addAssign("valor_icms","style.background","#E7E7E7");        
        $objResponse->addAssign("valor_icms","readOnly","true");
        
        
        $objResponse->addAssign("base_calc_icms_sub","style.background","#E7E7E7");        
        $objResponse->addAssign("base_calc_icms_sub","readOnly","true");
        
        
        $objResponse->addAssign("valor_icms_sub","style.background","#E7E7E7");        
        $objResponse->addAssign("valor_icms_sub","readOnly","true");
        
        $objResponse->addAssign("valor_total_ipi","style.background","#E7E7E7");        
        $objResponse->addAssign("valor_total_ipi","readOnly","true");
      
      }

			// se for ECF, calcula o troco
			//if ($post['tipoOrcamento'] == "ECF") {
			//	$objResponse->loadXML( Calcula_Troco_AJAX ($post) );
			//}

		}

		// retorna o resultado XML
    return $objResponse->getXML();

	}


	/*
	Função: Deleta_Produto_Encartelamento_AJAX
	Deleta um produto dinamicamente na tabela html
	*/
	function Deleta_Produto_Encartelamento_AJAX ($codigoTabela, $idproduto) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		
		global $produto;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		// nome da tabela criada
    $nome_tabela = "tabela_produto_" . $codigoTabela;

		// busca os dados do produto
		$info_produto = $produto->getById($idproduto);

		// verifica se vai remover: Pula 1 linha se clicar no cancelar
		$objResponse->addConfirmCommands(1, utf8_encode("Deseja retirar o produto '" . $info_produto['descricao_produto'] . "' deste orçamento ?"));

		// remove a tabela
		$objResponse->addRemove($nome_tabela);

		// calcula o total
		$objResponse->loadXML( Calcula_Total_AJAX () );

		// retorna o resultado XML
    return $objResponse->getXML();

  }


	/*
	Função: Deleta_Modo_Recebimento_A_Vista_AJAX
	Deleta um modo de recebimento da tabela html
	*/
	function Deleta_Modo_Recebimento_A_Vista_AJAX ($codigoTabela, $sigla_modo_recebimento) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		
		global $modo_recebimento;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		// nome da tabela criada
    $nome_tabela = "tabela_contas_receber_a_vista_" . $codigoTabela;

		// busca os dados do modo de recebimento
		$info_modo_recebimento = $modo_recebimento->getById($sigla_modo_recebimento);

		// verifica se vai remover: Pula 1 linha se clicar no cancelar
		$objResponse->addConfirmCommands(1, utf8_encode("Deseja retirar o modo de Recebimento '" . $info_modo_recebimento['descricao'] . "' das contas a receber ?"));

		// remove a tabela
		$objResponse->addRemove($nome_tabela);

		// calcula o total a ser financiado novamente
		$objResponse->addScript("xajax_Calcula_Valor_Financiado_AJAX(xajax.getFormValues('for_orcamento'));");

		// retorna o resultado XML
    return $objResponse->getXML();

  }



	/*
	Função: Seleciona_Produtos_AJAX
 	Seleciona os produtos do orçamento e colocam eles dinamicamente na tabela html
	*/
	function Seleciona_Produtos_AJAX ($idorcamento, $action="",$tipo="") {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		
		global $orcamento;
		global $parametro;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

  	// busca o valor padrao para a validade do orçamento e o máximo de itens
		$list_parametro = $parametro->make_list(0, $conf['rppg']);
		if ( count ($list_parametro) > 0 ) {
			$post['maximoItensOrcamento'] = $list_parametro[0]['maximoItensOrcamento'];
			$post['descontoMaximoOrcamento'] = str_replace(",",".",$list_parametro[0]['descontoMaximoOrcamento']);
		}
		else {
			$post['maximoItensOrcamento'] = 0;
			$post['descontoMaximoOrcamento'] = 0;
		}
		//------------------------------------------------


		// busca os dados do orçamento
		$info_orcamento = $orcamento->getById($idorcamento);

		// busca os produtos do orçamento
		$list_produtos = $orcamento->Seleciona_Produtos_Do_Orcamento($idorcamento);


		$cont = 0;
		$post['operacao'] = "editar";
		$post['idproduto_Tipo'] = "P";

		for ($i=0; $i<count($list_produtos); $i++) {

			$post['idproduto'] = $list_produtos[$i]['idproduto'];
			$post['qtd_produto'] = $list_produtos[$i]['qtd_produto'];
			$post['desconto_produto'] = $list_produtos[$i]['desconto_produto'];
			$post['tipoPreco'] = $list_produtos[$i]['tipoPreco'];
			$post['aliquota_icms_produto'] = $list_produtos[$i]['aliquota_icms_produto'];
			$post['cst_produto'] = $list_produtos[$i]['cst_produto_orcamento'];
      if($tipo == "ECF") $post['tipoOrcamento'] = "ECF";
			
			// se ja for uma NF ou for orçamento cancelado, busca o preço que foi vendido (preço da época)
			// se for um orçamento valido, deixa buscar os preços atualizados
	 		if ( ($info_orcamento['tipoOrcamento'] != "O") || ($info_orcamento['idmotivo_cancelamento'] != "") )
				$post['preco_unitario_produto'] = $list_produtos[$i]['preco_unitario_produto'];
			
			$post['total_produtos'] = $cont;

			// acrescenta o XML que foi retornado no objeto atual
			/*
	 		if ( $_GET['imprimirNF'] == 1 ) {
				//echo "a";
	 			$objResponse->loadXML( Insere_Produto_Encartelamento_IMPRESSAO_NF_AJAX ($post) );
			}
			*/
			if ( ($_GET['imprimir'] == 1) || ($info_orcamento['tipoOrcamento'] != "O") ) {
				//echo "b";
				$objResponse->loadXML( Insere_Produto_Encartelamento_IMPRESSAO_AJAX ($post) );
			}
			else {
				//echo "c";
				$objResponse->loadXML( Insere_Produto_Encartelamento_AJAX ($post, $action) );
			}

      $cont++;
      
		} // fim do for


		if ( $_GET['imprimirNF'] != 1) {
			// calcula o total
			$objResponse->loadXML( Calcula_Total_AJAX () );				
		}

		// retorna o resultado XML
    return $objResponse->getXML();

	}


  	

	/*
	Função: Verifica_Campos_Orcamento_AJAX
	Verifica se os campos do orçamento foram preenchidos
	*/
	function Verifica_Campos_Orcamento_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		
		global $funcionario;
		global $orcamento;
		global $cliente;
		global $parametro;
		global $cfop;
	  	global $modo_recebimento;
    	global $serie;
		//---------------------

		// cria o objeto xajaxResponse
    	$objResponse = new xajaxResponse();

		// se nao for orçamento, vai pro editar NF
		if ( (isset($post['tipoOrcamento'])) && ($post['tipoOrcamento'] != "O") ) $_GET['ac'] = "editarNF";
		
		if ($_GET['ac'] == "editar") {
			//$form->chk_empty($post['idUltfuncionario'], 1, 'Vendedor');
			//$post['idfuncionario'] = $post['idUltfuncionario'];

			// se quiser emitir uma ECF, verifica se está no browser IE
			if ( (isset($post['chk_emitir_nf'])) && ($post['emitir_tipo_orcamento'] == "ECF") && ($_SESSION['browser_usuario'] == "1") ) {
				$form->err[] = "Para fazer uma emissão de Cupom Fiscal, é necessário usar o navegador Internet Explorer!";
			}			

			// se quiser emitir uma ECF, verifica se está sendo praticado o preço de balcão
			if ( (isset($post['chk_emitir_nf'])) && ($post['emitir_tipo_orcamento'] == "ECF") && ($post['tipoPreco'] != "B") ) {
				$form->err[] = "Para fazer uma emissão de Cupom Fiscal, é necessário usar o preço de Balcão!";
			}		

		}
		else if ($_GET['ac'] == "adicionar") {
			//$form->chk_empty($post['idfuncionario'], 1, 'Vendedor');

			// se quiser emitir uma ECF, verifica se está no browser IE
			if ( (isset($post['chk_emitir_nf'])) && ($post['emitir_tipo_orcamento'] == "ECF") && ($_SESSION['browser_usuario'] == "1") ) {
				$form->err[] = "Para fazer uma emissão de Cupom Fiscal, é necessário usar o navegador Internet Explorer!";
			}

			// se quiser emitir uma ECF, verifica se está sendo praticado o preço de balcão
			if ( (isset($post['chk_emitir_nf'])) && ($post['emitir_tipo_orcamento'] == "ECF") && ($post['tipoPreco'] != "B") ) {
				$form->err[] = "Para fazer uma emissão de Cupom Fiscal, é necessário usar o preço de Balcão!";
			}	

		}
		else if ($_GET['ac'] == "editarNF") {
			$form->chk_empty($post['idfuncionarioNF'], 1, 'Funcionário');
			$post['idfuncionario'] = $post['idfuncionarioNF'];
			
			// se nao for ECF, verifica se preencheu o CFOP, se selecionou um cliente valido, e se digitou o modelo e serie da nota
			if ($post['tipoOrcamento'] != "ECF") {
				// verifica se o CFOP existe
				$info_cfop = $cfop->getByCodigo($post['codigo_cfop']);
				if($info_cfop['descricao'] == "") $form->err[] = "O código do CFOP está inválido !";

				// verifica se o cliente foi preenchido
				$form->chk_empty($post['idcliente'], 1, 'Cliente');

				// verifica o modelo e série da nota fiscal
				$form->chk_empty($post['modeloNota'], 1, 'Modelo da Nota');
				$form->chk_empty($post['serieNota'], 1, 'Série da Nota');
			}
			// se for ECF
			else {

				// verifica se o Acréscimo ou o Desconto é menor do que o Total dos SubProdutos
				$acrescimo_desconto = $form->FormataMoedaParaInserir($post['acrescimo_desconto']);
				if ($acrescimo_desconto >= $post['valor_total_produtos']) $form->err[] = "O valor do Acréscimo / Desconto tem que ser menor do que o valor Total de Produtos!";

				// verifica se existe alguma conta a receber com baixa não automática e verifica se preencheu o cliente
				if ($post['idcliente'] == "") { // se não preencheu o cliente, verifica se existe contas a receber sem baixa automática
					$existe_conta_sem_baixa_automatica = 0;

					// percorre as contas a receber a VISTA
					for ($i=1; $i<=$post['total_contas_receber_a_vista']; $i++) {
						if (isset($post["sigla_".$i])) {
							$info_modo_rec = $modo_recebimento->GetById($post["sigla_".$i]);
							if ($info_modo_rec['baixa_automatica'] == '0') {
								$existe_conta_sem_baixa_automatica = 1; 
								break;
							}
						}
					} // for

					if ($existe_conta_sem_baixa_automatica == 0) {
						// percorre as contas a receber a PRAZO
						for ($i=1; $i<=$post['total_contas_receber_a_prazo']; $i++) {
							if (isset($post["CR_descricao_".$i])) {
								$info_modo_rec = $modo_recebimento->GetById($post['modo_recebimento_a_prazo']);
								if ($info_modo_rec['baixa_automatica'] == '0') {
									$existe_conta_sem_baixa_automatica = 1; 
									break;
								}
							}
						} // for
					}

					if ($existe_conta_sem_baixa_automatica == 1) {
						$form->err[] = "Existe contas a receber nesta venda que NÃO possuem baixa automática. Logo, é necessário informar o Cliente!";
					}
				} // if cliente


				// ************************************************
				// Verifica se vai ser usado o modo TEF
				$siglas_tef = "";
				$valor_tef = 0;

				// percorre as contas a receber a VISTA
				for ($i=1; $i<=$post['total_contas_receber_a_vista']; $i++) {
					if (isset($post["sigla_".$i])) {
						if ( 
								 ($post["sigla_".$i] == $conf['sigla_cartao_credito']) ||
								 ($post["sigla_".$i] == $conf['sigla_cartao_debito'])	||
								 ($post["sigla_".$i] == $conf['sigla_modo_cheque'])	||
								 ( ($post["sigla_".$i] == $conf['sigla_modo_cheque']) && ($post['tef_caminho'] != "") )
							) 
						{
							$siglas_tef .= $post["sigla_".$i];
							$valor_tef = $post["valor_a_vista_".$i];
						}
					}
				} // for



				if ( strlen($siglas_tef) > 2 ) {

					$objResponse->addAssign("usaTEF", "value", "0");
					$objResponse->addAssign("valorTEF", "value", "0");	
					$objResponse->addAssign("valorTEF_bk", "value", "0");
					$objResponse->addAssign("modo_recebimento_tef", "value", "");
					$form->err[] = "Não é permitido pagamento com múltiplos cartões.";
				}
				else if ( strlen($siglas_tef) == 2 ) {

					$form->chk_empty($post['tef_caminho'], 1, 'TEF');
					
					//Verifica se a forma de pagamento é por Cheque
					if ($siglas_tef == $conf['sigla_modo_cheque']){
						$objResponse->addAssign("usaTEF", "value", "2"); // chama o módulo de cheque
						$objResponse->addAssign("modo_recebimento_tef", "value", "CONSULTA CHEQUE"); // modo de recebimento do tef (Consulta Cheque)
					}
					else{//Forma de pagamento por Cartão
						$objResponse->addAssign("usaTEF", "value", "1"); // chama o módulo de cartão
						$objResponse->addAssign("modo_recebimento_tef", "value", "TEF"); // modo de recebimento do tef
					}

					$valor_tef_bk = $valor_tef;
					$valor_tef_bk = str_replace(",",".",$valor_tef_bk);
					$valor_tef_bk = number_format($valor_tef_bk,2,",","");
					$objResponse->addAssign("valorTEF_bk", "value", $valor_tef_bk);  // valor do tef formatado com vírgula

					$valor_tef = str_replace(",","",$valor_tef);
					$valor_tef = str_replace(".","",$valor_tef);
					$objResponse->addAssign("valorTEF", "value", $valor_tef); // valor do tef sem vírgula nem ponto	

					//$info_modo_rec_tef = $modo_recebimento->GetById($siglas_tef);
					//$descricao_tef = $info_modo_rec_tef['descricao'];
					//$descricao_tef = substr($descricao_tef,0,15);
					//$objResponse->addAssign("modo_recebimento_tef", "value", utf8_encode($descricao_tef)); // modo de recebimento do tef
				}
				else {

					$objResponse->addAssign("usaTEF", "value", "0");
					$objResponse->addAssign("valorTEF", "value", "0");	
					$objResponse->addAssign("valorTEF_bk", "value", "0");
					$objResponse->addAssign("modo_recebimento_tef", "value", "");
				}							
				// ************************************************

			} // if ecf
			

			// se for SD, verifica se preencheu o numero da nota e se ela já não existe
			if ($post['tipoOrcamento'] == "SD") {
				$form->chk_empty($post['numeroNota'], 1, 'Número do Documento Fiscal');
				
				if ( $orcamento->Verifica_Numero_Documento_Fiscal_Existente($_SESSION['idfilial_usuario'],$post['numeroNota'],"SD") )
					$form->err[] = "Esse Nº de Documento Fiscal já foi utilizado em uma Série D. Por favor, digite outro Nº de documento fiscal !";
			}
		}
		
		// verifica se o cliente está bloqueado
		if ($post['idcliente'] != "") {
			$info_cliente = $cliente->getById($post['idcliente']);
			if ($info_cliente['cliente_bloqueado'] == "1") $form->err[] = "Este cliente está bloqueado, logo não será possível fechar uma venda para ele !";
		}	

		
		// verifica o campos moeda
		$form->chk_moeda($post['desconto'], 1, "Desconto");
		$form->chk_moeda($post['frete'], 1, "Frete");
		$form->chk_moeda($post['outras_despesas'], 1, "Outras despesas");

 		if ($_GET['ac'] == "editarNF") {
			// verifica o campos moeda
			$form->chk_moeda($post['valor_seguro'], 1, "Valor do Seguro");
			$form->chk_moeda($post['base_calculo_icms'], 1, "Base de cálculo do ICMS");
			$form->chk_moeda($post['valor_icms'], 1, "Valor do ICMS");
			$form->chk_moeda($post['base_calc_icms_sub'], 1, "Base de cálculo ICMS Substituição");
			$form->chk_moeda($post['valor_icms_sub'], 1, "Valor do ICMS Substituição");
			$form->chk_moeda($post['valor_total_ipi'], 1, "Valor Total do IPI");
		}

		
		$err = $form->err;


		// verifica se a senha do funcionario está correta
		//$info_funcionario = $funcionario->getById($post['idfuncionario']);
		//if ($info_funcionario['senha_funcionario'] != md5($post['senha_funcionario'])) $err[] = "A senha digitada está incorreta !";


		// verifica se está violando o desconto global maximo permitido
		$retorno_desconto = Verifica_Violacao_Desconto_Maximo_AJAX ($post);
		if ($retorno_desconto != false ) {
			$descontoMaximoOrcamento = number_format($post['descontoMaximoOrcamento'],2,",","");
			$retorno_desconto = number_format($retorno_desconto,2,",","");
			$err[] = "O máximo de desconto permitido em um Orçamento / Emissão Fiscal  é $descontoMaximoOrcamento %. Neste Orçamento / Emissão Fiscal, o desconto geral é de $retorno_desconto %!";
		}
		//--------------------------------------------------

		// verifica se existe algum produto e se ha algum produto com quantidade 0
		$existe_produto = 0;
		$existe_produto_sem_qtd = 0;
		$existe_produto_desconto_errado = 0;
		$existe_produto_com_desconto = 0;
		for ($i=1; $i<=$post['total_produtos']; $i++) {
			if ( isset( $post["idproduto_$i"] ) ) {
				$existe_produto = 1;
				if (!($form->chk_moeda($post["qtd_produto_$i"], 0))) {
        	$nome_produto_sem_qtd = $post["descricao_produto_$i"];
        	$existe_produto_sem_qtd = 1;
        	break;
				}
				if (!($form->chk_moeda($post["desconto_produto_$i"], 1))) {
        	$nome_produto_desconto_errado = $post["descricao_produto_$i"];
        	$existe_produto_desconto_errado = 1;
        	break;
				}
				// se for ECF, não pode ter desconto por produto	
				if (($post["desconto_produto_$i"] != "0,00") && ($post['emitir_tipo_orcamento'] == "ECF")) {
        	$nome_produto_desconto_errado = $post["descricao_produto_$i"];
        	$existe_produto_com_desconto = 1;
        	break;
				}
			}
		}
		if ($existe_produto == 0) $err[] = "É necessário pelo menos 1 produto para cadastrar o orçamento !";
		if ($existe_produto_sem_qtd == 1) $err[] = "Coloque uma quantidade válida para o produto '" . $nome_produto_sem_qtd . "' !";
		if ($existe_produto_desconto_errado == 1) $err[] = "Coloque um desconto válido para o produto '" . $nome_produto_desconto_errado . "' !";
		if ($existe_produto_com_desconto == 1) $err[] = "Para emitir um cupom fiscal, não é possível dar desconto por produto. Informe apenas o desconto global. Coloque o desconto 0,00 para o produto '" . $nome_produto_desconto_errado . "' !";
		//--------------------------------------------------


		// se for gerar uma emissão fiscal, verifica se as contas a receber foram geradas
		if ($post['tipoOrcamento'] != "O") {
			
			if ($post["valor_total_a_vista"] + $post["valor_total_a_prazo"] < $post["valor_total_nota"]) {
				$err[] = "É necessário gerar o restante das Contas a Receber!";
			}
		}

		// verifica se mandou emitir algum tipo de NF
		if ( isset($post['chk_emitir_nf']) && ($post['emitir_tipo_orcamento'] == "")) $err[] = "Preencha o que será Emitido !";
    
    if ( ($post['tipoOrcamento'] == "ECF") && (isset($post['chk_imprimir_emissao_nf'])) ) {
      // busca os dados da serie
      $info_serie = $serie->getById(md5($post['serie_ecf_final']));
  
      // se a ecf não está cadastrada na tabela auxiliar, redireciona para a tela de orçamento
      if ($info_serie['serie_crip_ecf'] == "") {
        $err[] = "A impressora de ECF não está ligada ou não está configurada para trabalhar neste computador.";
      }
    }
    
		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {

			if ($_GET['ac'] == "editarNF") {
				// verifica se vai cancelar: Pula 16 linhas se clicar no cancelar
				$objResponse->addConfirmCommands(16, utf8_encode("ATENÇÃO! Após a gravação, não será possível fazer alterações! Confirma a gravação dos dados ?"));

	    	if ( ($post['tipoOrcamento'] == "ECF") && (isset($post['chk_imprimir_emissao_nf'])) ) {
          
					// se não iniciou a impressao da ecf, começa a imprimir do início
					if ($post['iniciou_impressao_ecf'] == "0") {

  					// define o status da impressão para em andamento
	          $objResponse->loadXML(Define_Status_Impressao_ECF_AJAX($post["idorcamento"],1,"",$post["serie_ecf_final"]));     

						// desabilita os tabs de 0 a 2 para o usuário não mexer mais no total da compra, já que o cupom será fechado
	          $objResponse->addAssign("a_tab_0", "onclick", "");
						$objResponse->addAssign("a_tab_1", "onclick", "");
						$objResponse->addAssign("a_tab_2", "onclick", "");

						$objResponse->addAssign("iniciou_impressao_ecf", "value", "1");
						$objResponse->addScriptCall("Imprime_Cupom_Fiscal");

					}
					// se já tinha iniciado a impressão, retoma a partir do pagamento
					else {
						$objResponse->addScriptCall("Verifica_TEF_ECF");
					}

				}
				// é nota fiscal ou série D
				elseif (isset($post['chk_imprimir_emissao_nf']) || $post['tipoOrcamento'] == "SD") {
					$objResponse->addScript("document.getElementById('for_orcamento').submit();");
				}

	    	
			}
			else {
				// verifica se vai cancelar: Pula 1 linha se clicar no cancelar
				$objResponse->addConfirmCommands(1, utf8_encode("Confirma a gravação dos dados deste Orçamento ?"));

				$objResponse->addScript("document.getElementById('for_orcamento').submit();");
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
	Função: Inicia_Processo_TEF_AJAX
	Inicia o processo da TEF
	*/
	function Inicia_Processo_TEF_AJAX ($idorcamento, $cupom) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		// define o status da impressão para concluído
		$objResponse->loadXML(Define_Status_Impressao_ECF_AJAX($idorcamento,0,$cupom,""));     

		// terminou a impressão da ECF
		$objResponse->addAssign("terminou_impressao_ecf", "value", "1");

		// verifica se vai imprimir o TEF
		$objResponse->addScriptCall("Imprime_TEF_ECF");

		// retorna o resultado XML
    return $objResponse->getXML();
  }




	/*
	Função: Verifica_Cancelamento_Orcamento_AJAX
	Verifica se orçamento pode ser cancelado
	*/
	function Verifica_Cancelamento_Orcamento_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $funcionario;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		$form->chk_empty($post['idUltfuncionario'], 1, 'Vendedor / Funcionário');
		$form->chk_empty($post['idmotivo_cancelamento'], 1, 'Motivo do cancelamento');

		$err = $form->err;

		// verifica se a senha do funcionario está correta
		$info_funcionario = $funcionario->getById($post['idUltfuncionario']);
		if ($info_funcionario['senha_funcionario'] != md5($post['senha_funcionario'])) $err[] = "A senha digitada está incorreta !";

		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {
    	
			// verifica se vai cancelar: Pula 3 linhas se clicar no cancelar
			$objResponse->addConfirmCommands(3, utf8_encode("ATENÇÃO! Após cancelar um Orçamento / Emissão Fiscal, não será possível fazer alterações nele! Confirma o cancelamento ?"));

			if ($_GET['ac'] == "editarNF") $objResponse->addAssign("cancelar_emissao_fiscal", "value", "1");

			if ($post['tipoOrcamento'] != "ECF") $objResponse->addScript("document.getElementById('for_orcamento').submit();");
			else $objResponse->addScriptCall("Verifica_Cancelamento_Cupom"); 
  

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
	Função: Verifica_Violacao_Desconto_Maximo_AJAX
	Verifica se o desconto maximo permitido para o orçamento foi violado
	*/
	function Verifica_Violacao_Desconto_Maximo_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		//---------------------

		$desconto_nota = $form->FormataMoedaParaInserir($post['desconto']); 

		$preco_total_sem_desconto = 0;
		$preco_total_com_desconto = $post['valor_total_produtos'] - $desconto_nota;

		// percorre os produtos
		for ($i=1; $i<=$post['total_produtos']; $i++) {

			if ( isset( $post["idproduto_$i"] ) ) {

				$quantidade_produto = str_replace(",",".",$post["qtd_produto_$i"]);

				// preço sem desconto
				$preco_total_sem_desconto += $post["preco_unitario_produto_$i"] * $quantidade_produto;

			}
			
		}

		// calcula quantos % foi o desconto global dado
		$desconto = $preco_total_sem_desconto - $preco_total_com_desconto;
		$percentual_desconto = ($desconto / $preco_total_sem_desconto) * 100;
		$percentual_desconto = number_format($percentual_desconto,2,",","");
		$percentual_desconto = str_replace(",",".",$percentual_desconto);
		

		if ($percentual_desconto > $post['descontoMaximoOrcamento']) return ($percentual_desconto);
		else return false;

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

		global $orcamento;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		$form->chk_empty($post['idorcamento'], 1, 'Código do Orçamento');

		$err = $form->err;

		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {

			// verifica se o orçamento existe
			$filtro = " WHERE ORCT.idorcamento = " . $post['idorcamento'] . " 
									AND ORCT.idfilial = " . $_SESSION['idfilial_usuario'] . "
								";
			$info_orcamento = $orcamento->make_list(0, 999999, $filtro);

			// O código do orçamento não existe para a filial logada
			if ( count($info_orcamento) == 0 ) {

				$tabela = "
					<table width='100%'  border='0' cellpadding='2' cellspacing='3' bgcolor='#FDF5E6' class='tb4cantos'>
						<tr><td><b>Código do Orçamento inexistente para esta filial !</b></td></tr>
					</table>
				";	
			
			}
			// Mostra os dados do orçamento
			else {

				if ($info_orcamento[0]['idmotivo_cancelamento'] != '') $cancelado = "(CANCELADO)";
				else  $cancelado = "";

				$tabela = "
			
					<table width='95%' align='center'>
						<tr>
							<th align='center'>Cód. Orçamento</th>
							<th align='center'>Data/Hora (1)</th>
							<th align='center'>Funcionário (2)</th>
							<th align='center'>Cliente</th>
							<th align='center'>* Valor (R$)</th>
						</tr>
						
						<tr>
							<td align='center'><a class='menu_item' href = '{$conf['addr']}/admin/orcamento.php?ac=editar&idorcamento={$info_orcamento[0]['idorcamento']}'><b>{$info_orcamento[0]['idorcamento_formatado']} $cancelado</b></a></td>
							<td><a class='menu_item' href = '{$conf['addr']}/admin/orcamento.php?ac=editar&idorcamento={$info_orcamento[0]['idorcamento']}'>{$info_orcamento[0]['datahoraCriacao']}</a></td>
							<td><a class='menu_item' href = '{$conf['addr']}/admin/orcamento.php?ac=editar&idorcamento={$info_orcamento[0]['idorcamento']}'>{$info_orcamento[0]['funcionario_criou_orcamento']}</a></td>
							<td><a class='menu_item' href = '{$conf['addr']}/admin/orcamento.php?ac=editar&idorcamento={$info_orcamento[0]['idorcamento']}'>{$info_orcamento[0]['cliente_descricao']}</a></td>
							<td align='right'><a class='menu_item' href = '{$conf['addr']}/admin/orcamento.php?ac=editar&idorcamento={$info_orcamento[0]['idorcamento']}'>{$info_orcamento[0]['valor_total_nota']}</a></td>
						</tr>				

						<tr><td>&nbsp;</td></tr>
		
						<tr>
							<td colspan='9'>Legenda:</td>
						</tr>
						<tr>
							<td colspan='9'>(1) Data/Hora de criação do orçamento.</td>
						</tr>
						<tr>
							<td colspan='9'>(2) Funcionário que criou o orçamento.</td>
						</tr>
						<tr>
							<td colspan='9'>* Sujeito a alterações, caso os preços dos produtos tenham sido alterados.</td>
						</tr>
					</table>
				";	

			}

			$tabela = utf8_encode(html_entity_decode($tabela));

			$objResponse->addAssign("dados_orcamento", "innerHTML", "$tabela");



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
	Função: Verifica_Campos_Busca_Rapida_Nota_AJAX
	Verifica se o número da nota foi preenchido na busca rápida
	*/
	function Verifica_Campos_Busca_Rapida_Nota_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $orcamento;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		$form->chk_empty($post['numero_nota'], 1, 'Nº da nota');

		$err = $form->err;

		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {

			$cabecalho_ecf = "";
			$celula_ecf = "";

			// Série D ou NF
 			if ($post['tipoOrcamento'] != "ECF") {
				$filtro = " WHERE ORCT.numeroNota = " . $post['numero_nota'] . " 
										AND ORCT.idfilial = " . $_SESSION['idfilial_usuario'] . "
										AND ORCT.tipoOrcamento = '" . $post['tipoOrcamento'] . "' ";
			}
			// ECF, busca pelo código do orçamento
			else {
				$filtro = " WHERE ORCT.idorcamento = " . $post['numero_nota'] . " 
										AND ORCT.idfilial = " . $_SESSION['idfilial_usuario'] . "
										AND ORCT.tipoOrcamento = '" . $post['tipoOrcamento'] . "' ";
			}

			// verifica se o orçamento existe
			$info_orcamento = $orcamento->make_list(0, 999999, $filtro);

			// O código do orçamento não existe para a filial logada
			if ( count($info_orcamento) == 0 ) {

				$tabela = "
					<table width='100%'  border='0' cellpadding='2' cellspacing='3' bgcolor='#FDF5E6' class='tb4cantos'>
						<tr><td><b>Nota / Venda inexistente para esta filial !</b></td></tr>
					</table>
				";	
			
			}
			// Mostra os dados do orçamento
			else {

				if ($info_orcamento[0]['idmotivo_cancelamento'] != '') $cancelado = "(CANCELADO)";
				else  $cancelado = "";


				// ECF
				if ($post['tipoOrcamento'] == "ECF") {	
          $info_emissao = $orcamento->verifica_falha_emissao_ECF();
          if($info_emissao[0]['idorcamento'] == $info_orcamento[0]['idorcamento'])$semestoque = "&semestoque=1"; else $semestoque = "";    
					$cabecalho_ecf = "<th align='center'>Equipamento</th>";
					$celula_ecf = "<td align='center'><a class='menu_item' href = '{$conf['addr']}/admin/orcamento.php?ac=editarNF&idorcamento={$info_orcamento[0]['idorcamento']}{$semestoque}'>{$info_orcamento[0]['serie_ecf']}</a></td>";

          
				}


				$tabela = "
			
					<table width='95%' align='center'>
						<tr>
							<th align='center'>Nº da Nota</th>
							<th align='center'>Nº da Venda</th>
							$cabecalho_ecf
							<th align='center'>Data/Hora (1)</th>
							<th align='center'>Funcionário (2)</th>
							<th align='center'>Cliente</th>
							<th align='center'>Valor (R$)</th>
						</tr>
						
						<tr>
							<td align='center'><a class='menu_item' href = '{$conf['addr']}/admin/orcamento.php?ac=editarNF&idorcamento={$info_orcamento[0]['idorcamento']}{$semestoque}'><b>{$info_orcamento[0]['numeroNotaFormatado']} $cancelado</b></a></td>
							<td align='center'><a class='menu_item' href = '{$conf['addr']}/admin/orcamento.php?ac=editarNF&idorcamento={$info_orcamento[0]['idorcamento']}{$semestoque}'><b>{$info_orcamento[0]['idorcamento_formatado']}</b></a></td>
							$celula_ecf
							<td><a class='menu_item' href = '{$conf['addr']}/admin/orcamento.php?ac=editarNF&idorcamento={$info_orcamento[0]['idorcamento']}{$semestoque}'>{$info_orcamento[0]['datahoraCriacaoNF']}</a></td>
							<td><a class='menu_item' href = '{$conf['addr']}/admin/orcamento.php?ac=editarNF&idorcamento={$info_orcamento[0]['idorcamento']}{$semestoque}'>{$info_orcamento[0]['funcionario_emitiu_NF']}</a></td>
							<td><a class='menu_item' href = '{$conf['addr']}/admin/orcamento.php?ac=editarNF&idorcamento={$info_orcamento[0]['idorcamento']}{$semestoque}'>{$info_orcamento[0]['cliente_descricao']}</a></td>
							<td align='right'><a class='menu_item' href = '{$conf['addr']}/admin/orcamento.php?ac=editarNF&idorcamento={$info_orcamento[0]['idorcamento']}{$semestoque}'>{$info_orcamento[0]['valor_total_nota']}</a></td>
						</tr>				

						<tr><td>&nbsp;</td></tr>
		
						<tr>
							<td colspan='9'>Legenda:</td>
						</tr>
						<tr>
							<td colspan='9'>(1) Data/Hora da emissão fiscal.</td>
						</tr>
						<tr>
							<td colspan='9'>(2) Funcionário responsável pela emissão fiscal.</td>
						</tr>
					</table>
				";	

			}

			$tabela = utf8_encode(html_entity_decode($tabela));

			$objResponse->addAssign("dados_nota", "innerHTML", "$tabela");



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
	Função: Insere_Referencia_AJAX
	Insere o produto de referencia no auto-complete para agilizar
	*/
	function Insere_Referencia_AJAX ($idproduto) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $produto;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		// busca os dados do produto
		$info_produto = $produto->getById($idproduto);

		$objResponse->addAssign("idproduto", "value", $info_produto['idproduto']);
		$objResponse->addAssign("idproduto_Nome", "value", $info_produto['descricao_produto']);
		$objResponse->addAssign("idproduto_NomeTemp", "value", $info_produto['descricao_produto']);
		$objResponse->addAssign("idproduto_Tipo", "value", "P");
		$objResponse->addAssign("idproduto_Flag", "className", "selecionou");

		// retorna o resultado XML
    return $objResponse->getXML();


	}



	/*
	Função: ReImpressao_Fiscal_AJAX
	Auxilia na re-impressao de uma emissao fiscal
	*/
	function ReImpressao_Fiscal_AJAX ($post, $visualizar = 0) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
    
		// verifica se vai cancelar: Pula 6 linhas se clicar no cancelar
		$objResponse->addConfirmCommands(6, utf8_encode("Tem certeza que deseja imprimir ?"));

		$objResponse->addAssign("chk_imprimir_emissao_nf", "value", "1");
		$objResponse->addAssign("for_orcamento", "action", "{$conf['addr']}/admin/nota_fiscal_impressao.php?idorcamento={$post['idorcamento']}&visualizar=$visualizar");
		$objResponse->addAssign("for_orcamento", "target", "_blank");
		$objResponse->addScript("document.getElementById('for_orcamento').submit();");
		// volta com a URL antiga
		$objResponse->addAssign("for_orcamento", "action", "{$conf['addr']}/admin/orcamento.php?ac=editarNF&idorcamento={$post['idorcamento']}}&nao_atualizar=1");
		$objResponse->addAssign("for_orcamento", "target", "");			

  	
		// retorna o resultado XML
    return $objResponse->getXML();


	}


	/**
	 * Função: Calcula_Valor_Financiado_AJAX
	 * Calcula o valor a ser financiado
  	 * @param integer $parcela_editada - índice da parcela que acabou de ser editada
	 */
	function Calcula_Valor_Financiado_AJAX ($post = "", $parcela_editada = 1) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		//---------------------

		// cria o objeto xajaxResponse
    	$objResponse = new xajaxResponse();
    
		if ($post == "") {
			$objResponse->addScript("xajax_Calcula_Valor_Financiado_AJAX(xajax.getFormValues('for_orcamento'));");
		}
		else {
	
				// calcula o total financiado	
				$total_financiado = floatval($post['valor_total_nota']);
				$total_financiado = $form->FormataMoedaParaInserir($total_financiado);
				$objResponse->addAssign("valor_total_financiar", "value", $total_financiado);
				$objResponse->addAssign("TotalFinanciar", "innerHTML", $form->FormataMoedaParaExibir($total_financiado));

				
				/**
				 * Verifica valores das parcelas e faz correção automática se a soma de parcelas
				 * não for igual ao valor da nota
				 */
				
				/// armazena como valores do array os nomes das chaves de $post
				/// para buscar as parcelas
				$chaves_post = array_keys($post);
				
				/// busca as chaves do array $post que se referem a valores de parcelas
				$array_parcelas = preg_grep("/^(CR_valor_)(\d+)$/", $chaves_post);
				
				/// ordena os índices do array, começando de 0
				$array_parcelas = array_values($array_parcelas);
				
				$numero_parcelas = sizeof($array_parcelas);
				 
				/// força o array a começar do índice 1 para ficar de acordo com a ordem das parcelas
				array_unshift($array_parcelas,'');
				 
				$soma_parcelas = 0;
				$indice_parcela = 1;
				
				/// formata parcelas com duas casas decimais
				foreach($array_parcelas as $indice => $chave){
					if($chave){
						$post[$chave] = sprintf("%.2f",str_replace(',','.',$post[$chave]));
					}
				}
				
				/// percorre as parcelas e calcula a soma das parcelas que já foram preenchidas
				while($indice_parcela <= $parcela_editada){
					$soma_parcelas += $post[$array_parcelas[$indice_parcela]];
					$indice_parcela++;
				}
				

				/// se a parcela editada não foi a última e a soma das parcelas ultrapassar o valor da nota mostra mensagem de erro
				if(($parcela_editada != $numero_parcelas) && ($soma_parcelas >= $total_financiado)){
					$objResponse->addAlert(utf8_encode(html_entity_decode("A soma das parcelas não pode ser maior que o valor total!")));
				
					// retorna o resultado XML
					return $objResponse->getXML();
				}
				else{
				
					/// valor que está faltando para completar o valor da nota fiscal
					$valor_faltando = ($total_financiado - $soma_parcelas);
				
					/// número de parcelas que estão após a parcela que acabou de ser preenchida
					$numero_parcelas_faltando = $numero_parcelas - $parcela_editada;
				
					if($numero_parcelas_faltando > 0){
						/// valor faltando dividido pelo número que parcelas que faltam ser preenchidas
						$novo_valor_parcelas = sprintf("%.2f",$valor_faltando / $numero_parcelas_faltando);

						/// atribui o valor que está faltando igualmente para as parcelas seguintes, exceto a última
						/// porque na última são acertados os centavos que podem ter se perdido na divisão de parcelas
						for($i = $indice_parcela; $i<= ($numero_parcelas-1); $i++){
								
							/// ajusta o valor no formulário
							$objResponse->addAssign('CR_valor_' . $i, "value", $form->FormataMoedaParaExibir($novo_valor_parcelas));
								
							/// ajusta o valor no array $post para ajustar valor total
							$post['CR_valor_' . $i] = $form->FormataMoedaParaExibir($novo_valor_parcelas);
								
							$soma_parcelas += $novo_valor_parcelas;
								
						}
						
					}
					else{
				
						/// se o número de parcelas faltando for igual a zero significa que a última parcela foi editada
						/// nesse caso a própria parcela será editada para ajustar valor
				
						$indice_parcela--;
						
						$soma_parcelas = floatval($total_financiado) - $post[$array_parcelas[$indice_parcela]];
					}
					 

					/// na última parcela acerta os centavos faltantes
					$valor_ultima_parcela = floatval($total_financiado) - $soma_parcelas;
					 
				
					/// ajusta o valor no formulário
					$objResponse->addAssign('CR_valor_' . $numero_parcelas, "value", $form->FormataMoedaParaExibir($valor_ultima_parcela));
					 
					/// ajusta o valor no array $post para ajustar valor total
					$post['CR_valor_' . $numero_parcelas] = $form->FormataMoedaParaExibir($valor_ultima_parcela);
					
					$objResponse->addAssign("valor_total_a_prazo", "value", $total_financiado);
						
				}
				
				
				//$objResponse->addAssign("valor_total_a_prazo", "value", $soma_total_prazo);
//				$objResponse->addAssign("TotalPrazo", "innerHTML", $form->FormataMoedaParaExibir($total_financiado));
				
				// mostra o total final
				$objResponse->addAssign("TotalFinal", "innerHTML", $form->FormataMoedaParaExibir($total_financiado));
				
				
				// mostra o total a vista
				$objResponse->addAssign("valor_total_a_vista", "value", $total_pag_vista);
//				$objResponse->addAssign("TotalVista", "innerHTML", $form->FormataMoedaParaExibir($total_pag_vista));
				
				
				
				// calcula o Acréscimo / Desconto, usado na ECF
				$acrescimo_desconto = $total_pag_vista - $post['valor_total_produtos'];
				if ($acrescimo_desconto > 0) {
					$tipo_acrescimo_desconto = "A"; // acrescimo
				}
				else if ($acrescimo_desconto < 0) {
					$tipo_acrescimo_desconto = "D"; // desconto
					$acrescimo_desconto = -1 * $acrescimo_desconto;
				}
				else {
					$tipo_acrescimo_desconto = "D"; // desconto
					$acrescimo_desconto = "0.00";
				}
	
				$acrescimo_desconto = $form->FormataMoedaParaExibir($acrescimo_desconto);
				$objResponse->addAssign("acrescimo_desconto", "value", $acrescimo_desconto);
				$objResponse->addAssign("tipo_acrescimo_desconto", "value", $tipo_acrescimo_desconto);
				//------------------------------------------------------------------------------
				
				
				// deleta todas as contas a receber a prazo ja geradas
				//$objResponse->loadXML( Deleta_Contas_Receber_AJAX( "", intval($post['total_contas_receber_a_prazo']) ) );
			

		}

		// retorna o resultado XML
	    return $objResponse->getXML();


	}




	/*
	Função: Insere_Conta_Receber_A_Vista_AJAX
	Insere as contas a receber A VISTA dinamicamente na tabela html
	*/
	function Insere_Conta_Receber_A_Vista_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $modo_recebimento;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		$form->chk_empty($post['modo_recebimento_a_vista'], 1, 'Modo de recebimento');
		$form->chk_empty($post['valor_recebimento_a_vista'], 1, 'Valor (R$)');


		$valor_recebimento_a_vista = $form->FormataMoedaParaInserir($post['valor_recebimento_a_vista']);
		if ( $valor_recebimento_a_vista <= 0 ) $form->err[] = "O Valor (R$) deve ser maior do que 0 !";

		// verifica se o modo de recebimento já está na tabela
		for ($i=1; $i<=$post['total_contas_receber_a_vista']; $i++) {
			if ( isset( $post["sigla_$i"] ) ) {
				if ($post["sigla_$i"] == $post['modo_recebimento_a_vista']) {
					$mensagem = "Este modo de recebimento já está na tabela! Escolha outro modo de recebimento.";
					$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));

					// retorna o resultado XML
					return $objResponse->getXML();
				}
			}
		} // fim do for

		// verifica se o total pago a vista supera o valor total da nota
	  $valor_pag_vista = $form->FormataMoedaParaInserir($post['valor_recebimento_a_vista']);

		if ( ($valor_pag_vista + $post['valor_total_a_vista']) > $post['valor_total_nota'] ) {
			$form->err[] = "O 'Total pago a vista (R$)' não pode ser maior que o valor 'Total (R$)' !";
		}
		//------------------------------------------------------------------------------------------


		$err = $form->err;

		// se houveram erros, mostra-os
    if(count($err) != 0) {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
    }
    // se nao houveram erros, insere na tabela html
		else {

			// incremente 1 na quantidade de total_contas_receber a vista
			$total_contas_receber_a_vista = intval($post['total_contas_receber_a_vista']);


			// nome da tabela criada
			$total_contas_receber_a_vista++;
			$nome_tabela = "tabela_contas_receber_a_vista_" . $total_contas_receber_a_vista;


			// busca os dados do modo de recebimento
			$info_modo_recebimento = $modo_recebimento->getById($post['modo_recebimento_a_vista']);


			// tabela de conta a receber
			$tabela = utf8_encode("
						<table width='100%' cellpadding='5' id='$nome_tabela'>
							<tr>
								<td class='tb_bord_baixo' align='left' width='40%' id='descricao_vista_$total_contas_receber_a_vista'>									
									" . $info_modo_recebimento['descricao'] . "
								</td>

								<td class='tb_bord_baixo' align='center' width='40%' id='valor_vista_$total_contas_receber_a_vista'>									
									" . $post['valor_recebimento_a_vista'] . "
								</td>

								<td class='tb_bord_baixo' align='center' width='20%'>
									<input type='hidden' name='sigla_$total_contas_receber_a_vista' id='sigla_$total_contas_receber_a_vista' value='" . $post['modo_recebimento_a_vista'] . "' />
									<input type='hidden' name='valor_a_vista_$total_contas_receber_a_vista' id='valor_a_vista_$total_contas_receber_a_vista' value='" . $post['valor_recebimento_a_vista'] . "' />

									<a href='javascript:;' onclick=" . '"' . "xajax_Deleta_Modo_Recebimento_A_Vista_AJAX(" . "'" . $total_contas_receber_a_vista . "', '" . $post['modo_recebimento_a_vista'] . "'" . ");" . '"' . "><img src='../common/img/delete.gif'></a>
								</td>
							</tr>
						</table>
					");


			// adiciona a tabela
			$objResponse->addAppend("div_contas_receber_a_vista", "innerHTML", $tabela);


			$objResponse->addAssign("modo_recebimento_a_vista", "value", "");
			$objResponse->addAssign("valor_recebimento_a_vista", "value", "");
				
			$objResponse->addAssign("total_contas_receber_a_vista", "value", $total_contas_receber_a_vista);


			// Calcula o valor a ser financiado
			$objResponse->loadXML( Calcula_Valor_Financiado_AJAX() );
			

		}


		// retorna o resultado XML
    return $objResponse->getXML();

  }




	/*
	Função: Insere_Conta_Receber_A_Prazo_AJAX
	Insere as contas a receber A PRAZO dinamicamente na tabela html
	*/
	function Insere_Conta_Receber_A_Prazo_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $modo_recebimento;
		
		global $movimento;
		//---------------------

		// cria o objeto xajaxResponse
    	$objResponse = new xajaxResponse();

		$form->chk_empty($post['quantidade_de_parcelas'], 1, 'Quantidade de parcelas');
		$form->chk_empty($post['dias_entre_parcelas'], 1, 'Dias entre as parcelas');
		$form->chk_IsDate($post['data_parcela1'], 'Data da parcela 1');

		// verificar se a data da primeira parcela eh maior do que a data atual
		$data1 = date("d/m/Y");
		$data2 = $post['data_parcela1'];
		if ($form->data1_maior($data1, $data2)) $form->err[] = "A data da parcela 1 tem que ser maior que a data atual !";

		// verificar se o numero de parcelas eh maior que 0
		$quantidade_de_parcelas = $form->FormataMoedaParaInserir($post['quantidade_de_parcelas']);
		if ($quantidade_de_parcelas <= 0) $form->err[] = "A Quantidade de parcelas tem que ser maior que 0 !";

		// verifica se o dias entre as parecelas é menor que 0
		$dias_entre_parcelas = $form->FormataMoedaParaInserir($post['dias_entre_parcelas']);
		if ($dias_entre_parcelas <= 0) $form->err[] = "O número de dias entre parcelas tem que ser maior que 0 !";

		$err = $form->err;

		// se houveram erros, mostra-os
	    if(count($err) != 0) {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
	    }
	    // se nao houve erros, insere na tabela html
		else {

			// deleta todas as contas a receber a Prazo ja geradas
			$objResponse->loadXML( Deleta_Contas_Receber_AJAX("", intval($post['total_contas_receber_a_prazo']) ) );

			// depois calcula quantos dias de carencia foram dados
			$data1 = date("Y-m-d");
			$data2 = $form->FormataDataParaInserir( $post['data_parcela1'] );
		  	$array = $form->date_diff($data1, $data2);
			$dias_de_carencia = $array["d"];

			/**
			 * Código adaptado para gerar movimentos ao invés de contas a receber.
			 * O tratamento de juros foi retirado da tela e será considerado sempre zero
			 */
			
			// primeiro calcula os juros diarios
			//			$juros_diarios = $form->MatFin_Calcula_Taxa_Diaria($post['juros_parcelamento']);
			//$juros_diarios = 0.0;
				
			// Calcula o PV apos a carencia. O meu pv vai ser o fv depois da carencia
			//$pv = $form->MatFin_Calcula_FV($juros_diarios, $dias_de_carencia, $post['valor_total_financiar']);

			// calcula qual é a taxa do periodo escolhido
			//$taxa_periodo = $form->MatFin_Calcula_Juros_Do_Periodo($juros_diarios, $post['dias_entre_parcelas']);

			// calcula o pmt
			//$pmt = $form->MatFin_Calcula_PMT($taxa_periodo, $post['quantidade_de_parcelas'], $pv, 0, "BGN", 2);
			
			$pmt = $post['valor_total_financiar'] / $post['quantidade_de_parcelas'];
			$pmt = number_format($pmt,2,",","");

			// calcula o pmt basico, sem juros
			//$pmt_basico = $form->MatFin_Calcula_PMT(0, $post['quantidade_de_parcelas'], $post['valor_total_financiar'], 0, "BGN", 2);
			//$pmt_basico = number_format($pmt_basico,2,",","");


			// incremente 1 na quantidade de total_contas_receber_a_prazo
			$total_contas_receber_a_prazo = intval($post['total_contas_receber_a_prazo']);

			// pega os dados da parcela 1
			$array_temp = split("/", $post['data_parcela1']);
			$dia = $array_temp[0];
			$mes = $array_temp[1];
			$ano = $array_temp[2];

			// busca a descrição do modo de recebimento a prazo
			$info_modo_recebimento = $modo_recebimento->GetById($post['modo_recebimento_a_prazo']);
			$modo_recebimento_descricao = $info_modo_recebimento['descricao'];
			
			
			/// se não existir o array parcelas significa que o orçamento ainda está sendo gerado
			/// nesse caso busca os valores padrão das contas de débito e crédito para associar aos movimentos
			if(!isset($post['parcelas'])){
				$dados_movimento_padrao = $movimento->buscaDadosPadraoMovimento($post['idcliente']); 
			}

			// percorre as contas e vai adicionando-os na tabela
			for ($i=0; $i < intval($post['quantidade_de_parcelas']); $i++) {
				
				if(isset($post['parcelas'][$i])){
				
					$pmt_basico = $post['parcelas'][$i]['valor_basico'];
					$pmt = $post['parcelas'][$i]['valor_basico'];
				
					$data_parcela = $form->FormataDataHoraParaExibir($post['parcelas'][$i]['data_vencimento']);
				}
				else{
				
					//Soma separadamente os centavos e o real para deixar os centavos somados na ultima parcela (Ticket #179)
					$pmt_real = intval($pmt . '.00');
					$pmt = number_format(intval($pmt), 2, ".", "");
					 
					$pmt_basico_real = intval($pmt . '.00');
					$pmt_basico = number_format(intval($pmt), 2, ".", "");
				
					//Se for a última parcela, soma os centavos
					if ($i == (intval($post['quantidade_de_parcelas']) - 1)) {
						$pmt = $pmt_basico = ($post['valor_total_financiar'] - $soma_parcelas);
					}
					else{
						/// enquanto não chegar na última parcela vai somando os valores das parcelas
						/// para que a última seja o total menos a soma das parcelas, para que não fique com
						/// diferença nos centavos
						$soma_parcelas += $pmt;
					}
				
					//Formata as parcelas
					$pmt = $form->FormataMoedaParaExibir($pmt);
					$pmt_basico = $form->FormataMoedaParaExibir($pmt_basico);
					 
					$data_parcela = date("d/m/Y", mktime(0, 0, 0, $mes, $dia + ($i * $post['dias_entre_parcelas']), $ano));
					
					$post['parcelas'][$i]['idplano_debito'] = $dados_movimento_padrao['id_conta_debito'];
					$post['parcelas'][$i]['idplano_debito_descricao'] = $dados_movimento_padrao['descricao_conta_debito'];
					$post['parcelas'][$i]['idplano_credito'] = $dados_movimento_padrao['id_conta_credito'];
					$post['parcelas'][$i]['idplano_credito_descricao'] = $dados_movimento_padrao['descricao_conta_credito'];
						
				}
				

				// nome da tabela criada
				$total_contas_receber_a_prazo++;
				$nome_tabela = "tabela_contas_receber_a_prazo_" . $total_contas_receber_a_prazo;

				// contador da parcela
				$contador = $i+1;

           		if ($conf['nome_programa'] == 'orcamento') {
           			
           			if(!$post['parcelas'][$i]['idplano_debito']){
           				$post['parcelas'][$i]['idplano_debito'] = '';
           			}
           			
            		$tabela = utf8_encode(
           				"<input type='hidden' name='tem_parcelas_conta_receber' value='1' />" .
						"<table width='100%' cellpadding='5' id='$nome_tabela'>
							<tr>
								<td class='tb_bord_baixo' align='left' width='20%'>
									<input type='hidden' name='CR_descricao_$contador' id='CR_descricao_$contador' value='Parcela " . $contador . "' />
									<input type='hidden' name='CR_valor_basico_$contador' id='CR_valor_basico_$contador' value='$pmt_basico' />" .             			
	            					'Parcela ' . $contador .
								"</td> " .
            					
		                        "<td class='tb_bord_baixo' align='center' width='25%'>".
	                	            "<input type='hidden' name='CR_idplano_debito_$contador' id='idplano_debito_$contador' value=" . $post['parcelas'][$i]['idplano_debito'] . " />
	                    	        <input type='hidden' name='idplano_debito_{$contador}_NomeTemp' id='idplano_debito_{$contador}_NomeTemp' value='" . $post['parcelas'][$i]['idplano_debito_descricao'] . "' />
	                        	    <input class='long' type='text' name='idplano_debito_{$contador}_Nome' id='idplano_debito_{$contador}_Nome' value='" . $post['parcelas'][$i]['idplano_debito_descricao'] . "' " .
	                            	' onKeyUp=\'javascript:
	                                           VerificaMudancaCampo("idplano_debito_' . $contador . '");\'> ' .
	                            	"<span class='nao_selecionou' id='idplano_debito_{$contador}_Flag'>
	                                	&nbsp;&nbsp;&nbsp;
	                            	</span> " .
		                        "</td>
            				
		                        <td class='tb_bord_baixo' align='center' width='25%'>
		                            <input type='hidden' name='CR_idplano_credito_$contador' id='idplano_credito_$contador' value=" . $post['parcelas'][$i]['idplano_credito'] . " />
		                            <input type='hidden' name='idplano_credito_{$contador}_NomeTemp' id='idplano_credito_{$contador}_NomeTemp' value='" . $post['parcelas'][$i]['idplano_credito_descricao'] . "' />
		                            <input class='long' type='text' name='idplano_credito_{$contador}_Nome' id='idplano_credito_{$contador}_Nome' value='" . $post['parcelas'][$i]['idplano_credito_descricao'] ."'
		                                   onKeyUp='javascript:
		                                           VerificaMudancaCampo(\'idplano_credito_$contador\');'/>
		                            <span class='nao_selecionou' id='idplano_credito_{$contador}_Flag'>
		                                &nbsp;&nbsp;&nbsp;
		                            </span>
		                        </td> " . 
		            				
		            				            					
								"<td class='tb_bord_baixo' align='center' width='15%' id='CR_data_prazo_$contador'>					
									<input type='text' name='CR_data_$contador' id='CR_data_$contador' value='$data_parcela' 
									 class='short' onkeyup=\"mask('CR_data_$contador', 'data')\" onkeydown=\"mask('CR_data_$contador', 'data')\" />
								</td>
	
								<td class='tb_bord_baixo' align='center' width='15%' id='CR_valor_prazo_$contador'>
									<input type='text' class='short' name='CR_valor_$contador' id='CR_valor_$contador' value='$pmt'  
										onkeydown=\"FormataValor('CR_valor_$contador')\" onkeyup=\"FormataValor('CR_valor_$contador')\" onblur=\"xajax_Calcula_Valor_Financiado_AJAX(xajax.getFormValues('for_orcamento')," . $contador . ");\" />
								</td>
							</tr>
						</table>
					");
            		
            		$objResponse->addAssign("idplano_debito_$contador", "value", $post['parcelas'][$i]['idplano_debito']);
            		$objResponse->addAssign("idplano_debito_{$contador}_NomeTemp", "value", $post['parcelas'][$i]['idplano_debito_descricao']);
            		$objResponse->addAssign("idplano_debito_{$contador}_Nome", "value", $post['parcelas'][$i]['idplano_debito_descricao']);
            		
           		}				

				// adiciona a tabela
				$objResponse->addAppend("div_contas_receber_a_prazo", "innerHTML", $tabela);
				

			} // fim do for

			// inclui chamadas de autocomplete das contas
			for ($i=1; $i <= intval($post['quantidade_de_parcelas']); $i++) {
		
				$objResponse->AddScript('new CAPXOUS.AutoComplete("idplano_debito_' . $i . '_Nome", function() {
				return "plano_ajax.php?ac=busca_plano&typing=" + this.text.value + "&idplano=" + document.getElementById(\'idplano_debito_' . $i . '\').value + "&campoID=idplano_debito_' . $i . '&tipo=D";},
				{minChars: 2 }); VerificaMudancaCampo("idplano_debito_' . $i . '");');
			
				$objResponse->AddScript('new CAPXOUS.AutoComplete("idplano_credito_' . $i . '_Nome", function() {
				return "plano_ajax.php?ac=busca_plano&typing=" + this.text.value + "&idplano=" + document.getElementById(\'idplano_credito_' . $i . '\').value + "&campoID=idplano_credito_' . $i . '&tipo=R";},
				{minChars: 2 }); VerificaMudancaCampo("idplano_credito_' . $i . '");');
		
			}
					
			
			// total de pagamento a prazo
			$pmt = str_replace(",",".",$pmt);
			$total_pag_prazo = $post['quantidade_de_parcelas'] * $pmt;
//			$objResponse->addAssign("valor_total_a_prazo", "value", $total_pag_prazo);
//			$objResponse->addAssign("TotalPrazo", "innerHTML", $form->FormataMoedaParaExibir($total_pag_prazo));		


			// total geral final
			$total_geral_final = $total_pag_prazo + $total_pag_vista;
			$total_geral_final = number_format($total_geral_final,2,",","");
			$objResponse->addAssign("TotalFinal", "innerHTML", $total_geral_final);

			$objResponse->addAssign("total_contas_receber_a_prazo", "value", $total_contas_receber_a_prazo);


			// calcula o Acréscimo / Desconto, usado na ECF
/*			
			$acrescimo_desconto = $total_pag_vista + $total_pag_prazo - $post['valor_total_produtos'];
			if ($acrescimo_desconto > 0) {
				$tipo_acrescimo_desconto = "A"; // acrescimo
			}
			else if ($acrescimo_desconto < 0) {
				$tipo_acrescimo_desconto = "D"; // desconto
				$acrescimo_desconto = -1 * $acrescimo_desconto;
			}
			else {
				$tipo_acrescimo_desconto = "D"; // desconto
				$acrescimo_desconto = "0.00";
			}

			$acrescimo_desconto = $form->FormataMoedaParaExibir($acrescimo_desconto);
			$objResponse->addAssign("acrescimo_desconto", "value", $acrescimo_desconto);
			$objResponse->addAssign("tipo_acrescimo_desconto", "value", $tipo_acrescimo_desconto);
*/			
			//------------------------------------------------------------------------------
		

		}

		// retorna o resultado XML
    	return $objResponse->getXML();

  	}



	/*
	Função: Deleta_Contas_Receber_AJAX
	Deleta todas as contas a receber geradas
	*/
	function Deleta_Contas_Receber_AJAX ($total_contas_receber_a_vista = "", $total_contas_receber_a_prazo = "") {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		// deleta as contas a receber a vista
		if ($total_contas_receber_a_vista != "") {
			for ($i=1; $i<=$total_contas_receber_a_vista; $i++) {
				// nome da tabela criada
				$nome_tabela = "tabela_contas_receber_a_vista_" . $i;
	
				// remove a tabela
				$objResponse->addRemove($nome_tabela);
			}
		}

		// deleta as contas a receber a prazo
		if ($total_contas_receber_a_prazo != "") {
			for ($i=1; $i<=$total_contas_receber_a_prazo; $i++) {
				// nome da tabela criada
				$nome_tabela = "tabela_contas_receber_a_prazo_" . $i;
	
				// remove a tabela
				$objResponse->addRemove($nome_tabela);
			}
		}



		// retorna o resultado XML
    return $objResponse->getXML();

  }




	/*
	Função: Seleciona_Contas_Receber_AJAX
 	Seleciona as contas a receber e colocam eles dinamicamente na tabela html
	*/
	function Seleciona_Contas_Receber_AJAX ($idorcamento, $post = "", $insere_parcelas = "") {
		
		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;

		global $orcamento;
		global $movimento;
		
		//---------------------

		// cria o objeto xajaxResponse
    	$objResponse = new xajaxResponse();

		$lista_movimentos = $movimento->pesquisaMovimento(" WHERE idorcamento = $idorcamento");

		$form->chk_empty($post['quantidade_de_parcelas'], 1, 'Quantidade de parcelas');
		$form->chk_empty($post['dias_entre_parcelas'], 1, 'Dias entre as parcelas');
		//$form->chk_empty($post['juros_parcelamento'], 1, 'Juros Parcelamento');
		$form->chk_IsDate($post['data_parcela1'], 'Data da parcela 1');
		//$form->chk_empty($post['modo_recebimento_a_prazo'], 1, 'Modo de recebimento a prazo');

		$err = $form->err;		
		
		$numero_movimentos = sizeof($lista_movimentos);

		if ( $numero_movimentos > 0 && (count($err) == 0) ) {
			
			$post['parcelas'] = array();
			for($i=0; $i<$numero_movimentos; $i++){
			
				$post['parcelas'][$i]['valor_basico'] = $lista_movimentos[$i]['valor_movimento'];
				$post['parcelas'][$i]['data_vencimento'] = $lista_movimentos[$i]['data_vencimento'];
				$post['parcelas'][$i]['idplano_debito'] = $lista_movimentos[$i]['idplano_debito'];
				$post['parcelas'][$i]['idplano_debito_descricao'] = utf8_encode($lista_movimentos[$i]['idplano_debito_descricao']);
				$post['parcelas'][$i]['idplano_credito'] = $lista_movimentos[$i]['idplano_credito'];
				$post['parcelas'][$i]['idplano_credito_descricao'] = utf8_encode($lista_movimentos[$i]['idplano_credito_descricao']);
				
			}

			// lista as contas a receber a PRAZO
			$objResponse->loadXML( Insere_Conta_Receber_A_Prazo_AJAX ($post) );
		}

		// retorna o resultado XML
	    return $objResponse->getXML();

	}



	/*
	Função: Gerar_Emissao_Fiscal_AJAX
 	Redireciona para a tela de emissão fiscal
	*/
	function Gerar_Emissao_Fiscal_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();


		$form->chk_empty($post['emitir_tipo_orcamento_redireciona'], 1, 'Tipo da Emissão Fiscal');

		// se quiser emitir uma ECF, verifica se está no browser IE
		if ( ($post['emitir_tipo_orcamento_redireciona'] == "ECF") && ($_SESSION['browser_usuario'] == "1") ) {
			$form->err[] = "Para fazer uma emissão de Cupom Fiscal, é necessário usar o navegador Internet Explorer!";
		}		

		// se quiser emitir uma ECF, verifica se está sendo praticado o preço de balcão
		if ( ($post['emitir_tipo_orcamento_redireciona'] == "ECF") && ($post['tipoPreco'] != "B") ) {
			$form->err[] = "Para fazer uma emissão de Cupom Fiscal, é necessário usar o preço de Balcão!";
		}	

		$err = $form->err;

		// se houveram erros, mostra-os
    if(count($err) != 0) {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
    }
    // se nao houveram erros, redireciona para a emissão fiscal
		else {

			$objResponse->addAssign("for_orcamento", "action", $post['url_redirecionamento']);
			$objResponse->addScript("document.getElementById('for_orcamento').submit();");

		}

		// retorna o resultado XML
    return $objResponse->getXML();

	}




	/*
	Função: Verifica_Serie_ECF_AJAX
	Verifica se a série da ECF está na tabela auxiliar
	Também verifica se já foi feito alguma redução Z no dia
	*/
	function Verifica_Serie_ECF_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $serie;
    global $orcamento;  
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		// verifica se já foi feito a redução Z hoje
		if ($post['reducaoZ_efetuada'] == "1") {
			$mensagem = "Não será possível operar com a ECF, pois já foi feito uma redução Z hoje.";
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));

			if($post['idorcamento'])
			{
				$objResponse->addAssign("for_orcamento", "action", "{$conf['addr']}/admin/orcamento.php?ac=listar&idorcamento={$post['idorcamento']}");
				$objResponse->addScript("document.getElementById('for_orcamento').submit();");  
			}
			else
			{
				$objResponse->addAssign("for_ecf", "action", "{$conf['addr']}/admin/index.php");	
				$objResponse->addScript("document.getElementById('for_ecf').submit();");  
			}		

			// retorna o resultado XML
			return $objResponse->getXML();
		}
		// executa as outras verificações
		else {		
	
			// busca os dados da serie
			$info_serie = $serie->getById(md5($post['serie_ecf']));
	
			// se a ecf não está cadastrada na tabela auxiliar, redireciona para a tela de orçamento
			if ($info_serie['serie_crip_ecf'] == "") {
	
				$mensagem = "A impressora de ECF não está ligada ou não está configurada para trabalhar neste computador.";
				$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
	
				if($post['idorcamento'])
				{
					$objResponse->addAssign("for_orcamento", "action", "{$conf['addr']}/admin/orcamento.php?ac=listar&idorcamento={$post['idorcamento']}");
					$objResponse->addScript("document.getElementById('for_orcamento').submit();");  
				}
				else
				{
					$objResponse->addAssign("for_ecf", "action", "{$conf['addr']}/admin/index.php");	
					$objResponse->addScript("document.getElementById('for_ecf').submit();");  
				}
				
			}
			// a impressora está no arquivo auxiliar
			else
			{
				$andamento = $orcamento->verifica_falha_emissao_ECF();
				
				// verifica se a impressora que deu queda de energia é a mesma que está ligada na máquina
				if($post["serie_ecf"] == $andamento[0]["serie_ecf"])
				{     
	
					$mensagem = "Existe um cupom fiscal em aberto no sistema. É preciso efetuar o cancelamento deste antes de prosseguir com as demais operações relacionadas a ECF.";
					$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
	
					$objResponse->addAssign("for_chk","value",0);
	
					if($post["idorcamento"])
					{ 
						$objResponse->addAssign("for_orcamento", "action", "{$conf['addr']}/admin/orcamento.php?ac=editarNF&tipo=ECF&idorcamento=".$andamento[0]["idorcamento"]."&semestoque=1");
						$objResponse->addScript("document.getElementById('for_orcamento').submit();");
					}
					else
					{ 
						$objResponse->addAssign("for_ecf", "action", "{$conf['addr']}/admin/orcamento.php?ac=editarNF&tipo=ECF&idorcamento=".$andamento[0]["idorcamento"]."&semestoque=1");
						$objResponse->addScript("document.getElementById('for_ecf').submit();");
					}
	
				}
	
			}  

			// retorna o resultado XML
			return $objResponse->getXML();

		}

	}

  /*
  Função: Define_Status_Impressao_ECF_AJAX
  Define o status de impressão para "em andamento"
  */

  function Define_Status_Impressao_ECF_AJAX ($idorcamento, $status, $cupom = "", $serie = "", $tipo = "ECF") {

    // variáveis globais
    global $form;
    global $conf;
    global $db;
    global $falha;
    global $err;

    global $orcamento;
    //---------------------

    // cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
    

    $info["litimpressao_ecf_em_andamento"] = $status;
		$info["littipoOrcamento"] = $tipo;    
		if ($cupom != "") $info["numnumeroNota"] = $cupom;
    if ($serie != "") $info["litserie_ecf"] = $serie;
    
    
    
    $info_orcamento = $orcamento->update($idorcamento, $info);
    
    // retorna o resultado XML
    return $objResponse->getXML();

  }


?>
