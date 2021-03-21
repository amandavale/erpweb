<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");

	require_once("../entidades/funcionario.php");
  require_once("../entidades/produto.php");
	require_once("../entidades/transferencia_filial.php");
  require_once("../entidades/produto_filial.php");
  require_once("../entidades/encartelamento_produto.php");
	require_once("../entidades/pedido.php");
	require_once("../entidades/pedido_produto.php");
	require_once("../entidades/parametro.php");
	require_once("../entidades/aliquota_icms.php");
	require_once("../entidades/cfop.php");


  // inicializa templating
  $smarty = new Smarty;

  // ação selecionada
  $flags['action'] = $_GET['ac'];

  // inicializa autenticação
  $auth = new auth();

	//inicializa classe
	$funcionario = new funcionario();
	$transferencia_filial = new transferencia_filial();
	$produto = new produto();
	$produto_filial = new produto_filial();
	$pedido = new pedido();
	$pedido_produto = new pedido_produto();
	$encartelamento_produto = new encartelamento_produto();
	$parametro = new parametro();
	$aliquota_icms = new aliquota_icms();
	$cfop = new cfop();



  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();
        



	/*	Função: Calcula_Total_AJAX
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
			
			$objResponse->addScript("xajax_Calcula_Total_AJAX(xajax.getFormValues('for'));");
		}
		else {
			

						 
				$total = 0;

				for ($i=1; $i<=$post['total_produtos']; $i++) {

					if ( isset( $post["idproduto_$i"] ) ) {
						
            $post["preco_total_$i"] = str_replace(",",".",$post["preco_total_$i"]);
						$total = $post["preco_total_$i"] + $total;

						// calcula o valor do ICMS
						$icms = $post["preco_total_$i"] * ($post["icms_produto_$i"]/100);
						$valor_icms_produtos += $icms;

					}

				}
			
              

				$objResponse->addAssign("valor_total_produtos", "value", $total);

				$total = number_format($total,2,",","");
        
				$objResponse->addAssign("Sub", "innerHTML", $total);

        $total = str_replace(",",".",$total);

				// Valor dor ICMS
				$valor_icms_produtos = $form->FormataMoedaParaExibir($valor_icms_produtos);
				if($_GET['ac'] == "gerar_pedido")	$objResponse->addAssign("numicms", "value", $valor_icms_produtos);
				else $objResponse->addAssign("icms", "value", $valor_icms_produtos);
 				
        
        // desconto do produto

				if($_GET['ac'] != "gerar_pedido")
        $total = $total - str_replace(",",".",$post["desconto"]) + str_replace(",",".",$post["outras_despesas"]) + str_replace(",",".",$post["seguro"]) + str_replace(",",".",$post["frete"]);
        else
        $total = $total - str_replace(",",".",$post["desconto"]) + str_replace(",",".",$post["numoutras_despesas"]) + str_replace(",",".",$post["numseguro"]) + str_replace(",",".",$post["numfrete"]);


       

				$objResponse->addAssign("valor_total", "value", str_replace(",",".",$total));	

				$total = number_format($total,2,",","");
			
				          
		  	$objResponse->addAssign("SubTotal", "innerHTML", $total);


		}

		// retorna o resultado XML
    return $objResponse->getXML();

	}
	
	

		/*	Função: Atualiza_Total_AJAX
	Re-Calcula o total do orçamento
	*/
	function Atualiza_Total_AJAX ($post = "") {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $encartelamento_produto;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
    
    // se nao tiver nada no post, da um submit no form para pegar ele
		if ($post == "") {
			
			$objResponse->addScript("xajax_Atualiza_Total_AJAX(xajax.getFormValues('for'));");
		}
		else {
		
		for ($i=1; $i<=$post['total_produtos']; $i++) {

				if ( isset( $post["idproduto_$i"] ) ) {
					
					
						$info_produto['qtd_produto'] = str_replace(",",".",$post["qtd_produto_$i"]);
						$info_produto['preco'] = str_replace(",",".",$post["preco_custo_$i"]);
						
			      $info_produto['total'] = $info_produto['qtd_produto'] * $info_produto['preco'];

						$info_produto['preco'] = number_format($info_produto['preco'],2,",","");

						$total = $info_produto['total'] + $total;

						$objResponse->addAssign("preco_total_$i", "value", $info_produto['total']);
						
						$info_produto['total'] = number_format($info_produto['total'],2,",","");
						
						$objResponse->addAssign("preco_$i", "innerHTML", $info_produto['total']);
						

				}

		}

				$objResponse->addAssign("valor_total_produtos", "value", $total);

				$total = number_format($total,2,",","");
				
    		$objResponse->addAssign("Sub", "innerHTML", $total);
    		
    		$total = str_replace(",",".",$total);
			
				$total = $total - str_replace(",",".",$post['desconto']);    		

    		$total = number_format($total,2,",","");
    		
		  	$objResponse->addAssign("SubTotal", "innerHTML", $total);
		  	
		  	$objResponse->loadXML( Calcula_Total_AJAX());
		}		  	
		  // retorna o resultado XML
    	return $objResponse->getXML();

	}
	
	

	/*
	Função: Verifica_Campos_Transferencia_AJAX
	Verifica se os campos da tranferencia de estoque foram preenchidos
	*/

	function Verifica_Campos_Entrada_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $transferencia_filial;
		global $funcionario;		
		global $pedido;
		global $parametro;
		global $cfop;
		//---------------------


			// cria o objeto xajaxResponse
	    $objResponse = new xajaxResponse();
			    
			
			$existe_produto = 0;

	    if($_GET['ac'] == "editar")

	    {
	    
				$form->chk_empty($post['idUltFuncionario'], 1, 'o funcionário');
				$form->chk_empty($post['senha_funcionario'], 1, 'a senha do funcionário');
				$form->chk_empty($post['idmotivo_cancelamento'], 1, 'o motivo do cancelamento');
				
				$err = $form->err;
				
				// verifica se a senha do cliente confere
				$info_funcionario = $funcionario->getById($post['idUltFuncionario']);
				$senha = md5($post['senha_funcionario']);
				if($post['senha_funcionario'] != "")
					if($info_funcionario['senha_funcionario'] != $senha) $err[] = "A senha digitada está incorreta.";
	
				$info_parametro = $parametro->getById(1);
	
				$data = explode("/",$post['data']);
	
				$data_1 = mktime(0, 0, 0, $data[1], $data[0], $data[2]);			
				$data_limite = mktime(0, 0, 0, date("m"), date("d") - $info_parametro['limite_cancelamento'], date("Y"));
	
				if( $data_limite > $data_1) $err[] = "O periodo máximo para cancelamento desta entrada expirou.";
	
	   	}
			if($_GET['ac'] == "adicionar")
			{
				
							$form->chk_empty($post['idfuncionario'], 1, 'o funcionário');
							$form->chk_empty($post['senha_funcionario'], 1, 'a senha do funcionário');
							$form->chk_empty($post['idfornecedor'], 1, 'o fornecedor');
							$form->chk_empty($post['valor_nota'], 1, 'o valor');
							$form->chk_empty($post['numero_nota'], 1, 'o número da nota');
							$form->chk_empty($post['codigo_cfop'], 1, 'o CFOP');
							$form->chk_empty($post['modelo_nota'], 1, 'o Modelo da Nota de Entrada');
							$form->chk_empty($post['serie_nota'], 1, 'a Série da Nota de Entrada');
	

							$form->chk_IsDate($post['data_entrada'], 'A data da entrada');
							$form->chk_IsDate($post['data_emissao_nota'], 'A data de emissão da nota');
	
							// verifica se o CFOP existe
							$info_cfop = $cfop->getByCodigo($post['codigo_cfop']);
							if($info_cfop['descricao'] == "") $form->err[] = "O código do CFOP está inválido !";

							
							$err = $form->err;
							


							if($form->FormataDataParaInserir($post['data_entrada']) > date("Y-m-d")) $err[] = "A data da entrada tem que ser menor ou igual a data atual.";
	
							if($form->FormataDataParaInserir($post['data_emissao_nota']) > date("Y-m-d")) $err[] = "A data de emissão tem que ser menor ou igual a data atual.";
							
							// verifica se a senha do cliente confere
							$info_funcionario = $funcionario->getById($post['idfuncionario']);
							$senha = md5($post['senha_funcionario']);
							if($post['senha_funcionario'] != "")
								if($info_funcionario['senha_funcionario'] != $senha) $err[] = "A senha digitada está incorreta.";
				
							
	
							for ($i=1; $i<=$post['total_produtos']; $i++) {
							
								if ( isset( $post["idproduto_$i"] ) ) {
					
									$existe_produto = 1;
									break;
								}
							}
						
						if ($existe_produto==0) $err[]="É preciso fazer no mínimo uma entrada de produto!";

						if(str_replace(",",".",$post["valor_nota"]) != str_replace(",",".",number_format($post['valor_total'],2,",",""))) $err[]="O valor declarado da nota difere do valor final na tabela de produtos!";
			}
			if($_GET['ac'] == "gerar_pedido") 
			{		
						$form->chk_empty($post['idfuncionario'], 1, 'o funcionário');
						$form->chk_empty($post['senha_funcionario'], 1, 'a senha do funcionário');
						$form->chk_empty($post['idfornecedor'], 1, 'o fornecedor');
						$form->chk_empty($post['numvalor_nota'], 1, 'o valor');
						$form->chk_empty($post['litnumero_nota'], 1, 'o número da nota');
						$form->chk_empty($post['codigo_cfop'], 1, 'o CFOP');
						$form->chk_empty($post['litmodelo_nota'], 1, 'o Modelo da Nota de Entrada');
						$form->chk_empty($post['litserie_nota'], 1, 'a Série da Nota de Entrada');

						$form->chk_IsDate($post['litdata_entrada'], 'A data da entrada');
						$form->chk_IsDate($post['litdata_emissao_nota'], 'A data de emissão da nota');
						
						// verifica se o CFOP existe
						$info_cfop = $cfop->getByCodigo($post['codigo_cfop']);
						if($info_cfop['descricao'] == "") $form->err[] = "O código do CFOP está inválido !";


						$err = $form->err;
						
						$list = $pedido->getById($_GET['idpedido']);
						
						if($list['status_pedido'] == 'E') $err[] = "Já foi gerado uma entrada para este pedido.";
	
						if($form->FormataDataParaInserir($post['litdata_entrada']) > date("Y-m-d")) $err[] = "A data de entrada não pode ser maior do que a data atual.";
						
						if($form->FormataDataParaInserir($post['litdata_emissao_nota']) > date("Y-m-d")) $err[] = "A data de emissão não pode ser maior do que a data atual.";
						
						// verifica se a senha do cliente confere
						$info_funcionario = $funcionario->getById($post['idfuncionario']);
						$senha = md5($post['senha_funcionario']);
						if($post['senha_funcionario'] != "")
							if($info_funcionario['senha_funcionario'] != $senha) $err[] = "A senha digitada está incorreta.";
							
						$erro_preco = 0;
	
						$erro_qtd = 0;

						for ($i=1; $i<=$post['total_produtos']; $i++) {
						
							if ( isset( $post["idproduto_$i"] ) ) {
									
								if($post["preco_custo_$i"] == "" || $post["preco_custo_$i"] == "0,00" ) $erro_preco = 1;
								if($post["qtd_produto_$i"] == "" || $post["qtd_produto_$i"] == "0,00" ) $erro_qtd = 1;

								$existe_produto = 1;
							}
						}
					
					if ($erro_preco==1) $err[]="Existe um ou mais produtos com preços nulo, ou não determinados.";
	
					if ($erro_qtd==1) $err[]="Existe um ou mais produtos com quantidades nula, ou não determinadas.";
	
					if ($existe_produto==0) $err[]="É preciso fazer no mínimo uma transferência!";

					
					if(str_replace(",",".",$post["numvalor_nota"]) != str_replace(",",".",number_format($post['valor_total'],2,",",""))) $err[]="O valor declarado da nota difere do valor final na tabela de produtos!";
	
			}
			


		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {

    	$objResponse->addAssign("idfilial_remetente","value",$_SESSION['idfilial_usuario']);
		 	$objResponse->addAssign("data_transferencia","value",date("Y-m-d H:i:s"));

  		// verifica se confirma está transação.
  		if($_GET['ac'] == "editar"){
  			$objResponse->addConfirmCommands(1, utf8_encode("Confirmar o cancelamento da entrada de produtos?"));
    		$objResponse->addScript("document.getElementById('for').submit();");
  		}
  		else{
				$objResponse->addConfirmCommands(1, utf8_encode("Confirmar a entrada de produtos?"));
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
	Função: Insere_Produto_Encartelamento_AJAX
	Insere um produto ou um encartelamento dinamicamente na tabela html
	*/
	function Insere_Produto_Encartelamento_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $produto;
		global $produto_filial;
		global $encartelamento_produto;
		global $aliquota_icms;
		global $pedido_produto;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
		
        
		$form->chk_empty($post['idproduto'], 1, 'Produto');
		$form->chk_moeda($post['qtd_produto'], 0, 'Quantidade');

		// se não for recuperação de produtos vindos do "gerar_entrada", verifica o valor
		if ($post['inicio'] != "1")	{
			$form->chk_moeda($post['preco_custo_unitario'], 0, 'Preço Unitário');
		}

		$err = $form->err;
		
    
		// se houveram erros, mostra-os
    if(count($err) != 0) {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
    }
    // se nao houveram erros, insere na tabela html
		else {


			$array_produtos = $produto->Busca_Dados_Produto_AJAX($post['idproduto'], $post['qtd_produto'], "A");

						
			// verifica se pode inserir mais produtos no orçamento
			$total_produtos_validos = 0;
			for ($i=1; $i<=$post['total_produtos']; $i++) {
				if ( isset( $post["idproduto_$i"] ) ) {
					$total_produtos_validos++;

						// verifica se o item ja nao está no orçamento
						if ($post["idproduto_$i"] == $array_produtos[0]['idproduto']) {

							$mensagem = "Não será possível inserir este produto. Ele já está contido no pedido !";
							$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));

							// retorna o resultado XML
			    		return $objResponse->getXML();
						}

					//---------------------------------------
				}
			} // fim do for

				
			// incremente 1 na quantidade de total_produtos
			$total_produtos = intval($post['total_produtos']);
			$total_produtos++;

			// nome da tabela criada
			$nome_tabela = "tabela_produto_" . $total_produtos;

			// busca os dados do encartelamento
			$info_produto = $encartelamento_produto->retornaProduto($post['idproduto'], $_SESSION['idfilial_usuario']);
			
			$preco_total = str_replace(",",".",$post["preco_custo_unitario"]) * str_replace(",",".",$post["qtd_produto"]);
			
			$preco_total = number_format($preco_total,2,",","");


	     if($_GET['ac'] == "adicionar") {

      	// tabela de produtos
				$tabela = utf8_encode("
							
								<tr>
									<td width='0%' class='tb_bord_baixo' align='left'>
									  <input type='hidden' name='$nome_tabela' id='$nome_tabela' value='" . $total_produtos . "'/>
										<input type='hidden' name='idproduto_$total_produtos' id='idproduto_$total_produtos' value='" . $array_produtos[0]['idproduto'] . "' />
										<input type='hidden' name='descricao_produto_$total_produtos' id='descricao_produto_$total_produtos' value='" . $array_produtos[0]['descricao_produto'] . "' />
										<input type='hidden' name='qtd_produto_$total_produtos' id='qtd_produto_$total_produtos' value='" . $array_produtos[0]['qtd_produto'] . "' />
										<input type='hidden' name='preco_total_$total_produtos' id='preco_total_$total_produtos' value='" . $preco_total . "' />
										<input type='hidden' name='preco_custo_$total_produtos' id='preco_custo_$total_produtos' value='" . $post['preco_custo_unitario'] . "' />
									</td>
									<td width='5%' align='left' id='codigo_produto_$total_produtos' class='tb_bord_baixo'>" . $array_produtos[0]['idproduto'] . "</td>

									<td width='30%' align='left' id='descricao_produto_$total_produtos' class='tb_bord_baixo'>" . $array_produtos[0]['descricao_produto'] . "</td>

         					<td width='7%' align='center' id='sigla_unidade_venda_$total_produtos' class='tb_bord_baixo'>" . $array_produtos[0]['sigla_unidade_venda'] . "</td>
									
									<td width='8%' align='center' id='qtd_produto_$total_produtos' class='tb_bord_baixo'>" . $array_produtos[0]['qtd_produto'] . "</td>
									
									<td width='10%' align='center' id='preco_unitario_$total_produtos' class='tb_bord_baixo'>" . $post['preco_custo_unitario'] . "</td>
					
									<td width='10%' align='center' id='preco_$total_produtos' class='tb_bord_baixo'>" . $preco_total . "</td>
									
									<td class='tb_bord_baixo' align='center' width='5%'>
										<input class='tiny' type='text' name='cst_produto_$total_produtos' id='cst_produto_$total_produtos' value='" . $array_produtos[0]['cst_produto'] . "' maxlength='3' />
									</td>

									<td class='tb_bord_baixo' align='center' width='10%'>
										<input class='tiny' type='text' name='icms_produto_$total_produtos' id='icms_produto_$total_produtos' value='' maxlength='5' onkeydown=\"FormataValor('icms_produto_$total_produtos')\" onkeyup=\"FormataValor('icms_produto_$total_produtos')\" onblur=" . '"' . "xajax_Calcula_Total_AJAX();" . '"' . " />
									</td>

									<td class='tb_bord_baixo' align='center' width='10%'>
										<input class='tiny' type='text' name='ipi_produto_$total_produtos' id='ipi_produto_$total_produtos' value='' maxlength='5' onkeydown=\"FormataValor('ipi_produto_$total_produtos')\" onkeyup=\"FormataValor('ipi_produto_$total_produtos')\" />
									</td>

									<td class='tb_bord_baixo' align='center' width='5%'>
										<a href='javascript:;' onclick=" . '"' . "xajax_Deleta_Produto_Encartelamento_AJAX(" . "'" . $total_produtos . "', '" . $array_produtos[0]['idproduto'] . "'" . ");" . '"' . "><img src='../common/img/delete.gif'></a>
									</td>
								</tr>
							
						");
	     	}
	     	else if($_GET['ac'] == "editar") {
	
					$info_pedido_produto = $pedido_produto->getById($array_produtos[0]['idproduto'],$_GET['idpedido']);
	
					if($info_pedido_produto['cst_produto'] == NULL) $info_pedido_produto['cst_produto'] = "&nbsp;";
					
					if($info_pedido_produto['aliquota_icms_produto'] == NULL) $info_pedido_produto['aliquota_icms_produto'] = "0,00";
					else $info_pedido_produto['aliquota_icms_produto'] = number_format($info_pedido_produto['aliquota_icms_produto'],2,",","");
	
					if($info_pedido_produto['ipi_produto'] == NULL) $info_pedido_produto['ipi_produto'] = "0,00";
					else $info_pedido_produto['ipi_produto'] = number_format($info_pedido_produto['ipi_produto'],2,",","");
	
					// tabela de produtos
					$tabela = utf8_encode("
							
								<tr>
									<td class='tb_bord_baixo' align='left'>
									  <input type='hidden' name='$nome_tabela' id='$nome_tabela' value='" . $total_produtos . "'/>
										<input type='hidden' name='idproduto_$total_produtos' id='idproduto_$total_produtos' value='" . $array_produtos[0]['idproduto'] . "' />
										<input type='hidden' name='descricao_produto_$total_produtos' id='descricao_produto_$total_produtos' value='" . $array_produtos[0]['descricao_produto'] . "' />
										<input type='hidden' name='qtd_produto_$total_produtos' id='qtd_produto_$total_produtos' value='" . $array_produtos[0]['qtd_produto'] . "' />
										<input type='hidden' name='preco_total_$total_produtos' id='preco_total_$total_produtos' value='" . $preco_total . "' />
										<input type='hidden' name='preco_custo_$total_produtos' id='preco_custo_$total_produtos' value='" . $post['preco_custo_unitario'] . "' />
										<input type='hidden' name='icms_produto_$total_produtos' id='icms_produto_$total_produtos' value='" . $info_pedido_produto['aliquota_icms_produto'] . "' />
									</td>

									<td width='5%' align='left' id='codigo_produto_$total_produtos' class='tb_bord_baixo'>" . $array_produtos[0]['idproduto'] . "</td>

									<td width='30%' align='left' id='descricao_produto_$total_produtos' class='tb_bord_baixo'>" . $array_produtos[0]['descricao_produto'] . "</td>

         					<td width='10%' align='center' id='sigla_unidade_venda_$total_produtos' class='tb_bord_baixo'>" . $array_produtos[0]['sigla_unidade_venda'] . "</td>
									
									<td width='10%' align='center' id='qtd_produto_$total_produtos' class='tb_bord_baixo'>" . $array_produtos[0]['qtd_produto'] . "</td>
									
									<td width='10%' align='center' id='preco_unitario_$total_produtos' class='tb_bord_baixo'>" . $post['preco_custo_unitario'] . "</td>
									
									<td width='10%' align='center' id='preco_$total_produtos' class='tb_bord_baixo'>" . $preco_total . "</td>
									
									<td width='10%' align='center' class='tb_bord_baixo'>". $info_pedido_produto['cst_produto'] ."</td>
									
									<td width='10%' align='center' class='tb_bord_baixo'>". $info_pedido_produto['aliquota_icms_produto'] ."</td>

									<td width='5%' align='center' class='tb_bord_baixo'>". $info_pedido_produto['ipi_produto'] ."</td>

								</tr>
							
						");
	    	}
	     	else if($_GET['ac'] == "gerar_pedido") {
					
					// tabela de produtos
					$tabela = utf8_encode("
							
								<tr>
									<td class='tb_bord_baixo' align='left'>
									  <input type='hidden' name='$nome_tabela' id='$nome_tabela' value='" . $total_produtos . "'/>
										<input type='hidden' name='idproduto_$total_produtos' id='idproduto_$total_produtos' value='" . $array_produtos[0]['idproduto'] . "' />
										<input type='hidden' name='descricao_produto_$total_produtos' id='descricao_produto_$total_produtos' value='" . $array_produtos[0]['descricao_produto'] . "' />
										<input type='hidden' name='preco_total_$total_produtos' id='preco_total_$total_produtos' value='" . $preco_total . "' />
										<input type='hidden' name='qtd_produto_$total_produtos' id='qtd_produto_$total_produtos' value='" . $array_produtos[0]['qtd_produto'] . "' />
									</td>

									<td width='5%' align='left' id='codigo_produto_$total_produtos' class='tb_bord_baixo'>" . $array_produtos[0]['idproduto'] . "</td>
									<td width='30%' align='left' id='descricao_produto_$total_produtos' class='tb_bord_baixo'>" . $array_produtos[0]['descricao_produto'] . "</td>

         					<td width='7%' align='center' id='sigla_unidade_venda_$total_produtos' class='tb_bord_baixo'>" . $array_produtos[0]['sigla_unidade_venda'] . "</td>
									
									<td width='8%' class='tb_bord_baixo' align='center' id='qtd_produto_label_$total_produtos'>" . $array_produtos[0]['qtd_produto'] . "</td>
									
									<td class='tb_bord_baixo' align='center' width='10%'>
											<input class='shorty' type='text' name='preco_custo_$total_produtos' id='preco_custo_$total_produtos' value='" . $post['preco_custo_unitario'] . "' maxlength='10' onkeydown=\"FormataValor('preco_custo_$total_produtos')\" onkeyup=\"FormataValor('preco_custo_$total_produtos')\" onblur='xajax_Atualiza_Total_AJAX();'/>
									</td>
									
									<td class='tb_bord_baixo' align='center' width='10%' id='preco_$total_produtos' >" . $preco_total . "</td>
									
									<td class='tb_bord_baixo' align='center' width='5%'>
										<input class='tiny' type='text' name='cst_produto_$total_produtos' id='cst_produto_$total_produtos' value='" . $array_produtos[0]['cst_produto'] . "' maxlength='3' />
									</td>									

									<td class='tb_bord_baixo' align='center' width='10%'>
										<input class='tiny' type='text' name='icms_produto_$total_produtos' id='icms_produto_$total_produtos' value='' maxlength='5' onkeydown=\"FormataValor('icms_produto_$total_produtos')\" onkeyup=\"FormataValor('icms_produto_$total_produtos')\" onblur=" . '"' . "xajax_Calcula_Total_AJAX();" . '"' . " />
									</td>

									<td class='tb_bord_baixo' align='center' width='10%'>
										<input class='tiny' type='text' name='ipi_produto_$total_produtos' id='ipi_produto_$total_produtos' value='' maxlength='5' onkeydown=\"FormataValor('ipi_produto_$total_produtos')\" onkeyup=\"FormataValor('ipi_produto_$total_produtos')\" />
									</td>
								
									<td class='tb_bord_baixo' align='center' width='5%'>
										<a href='javascript:;' onclick=" . '"' . "xajax_Deleta_Produto_Encartelamento_AJAX(" . "'" . $total_produtos . "', '" . $array_produtos[0]['idproduto'] . "'" . ");" . '"' . "><img src='../common/img/delete.gif'></a>
									</td>
								</tr>
							
						");
	     		}
	   
	  
      //conserta bug do addAppend no mozilla  
      if($_SESSION['browser_usuario'])
      {
        // adiciona a tabela
        $objResponse->addCreate("div_produtos", "table", "$nome_tabela");
        $objResponse->addAssign("$nome_tabela", "innerHTML", "$tabela");
        $objResponse->addAssign("$nome_tabela", 'width', "100%");
        $objResponse->addAssign("$nome_tabela", 'cellpadding', "5");
      }
      else
      {
        $tabela = '<table id="'.$nome_tabela.'" width="100%" cellpadding="5">'.$tabela.'</table>';
        $objResponse->addAppend("div_produtos", "innerHTML", $tabela);
      }
				

			$objResponse->addAssign("total_produtos", "value", $total_produtos);
    

			// limpa os campos inseridos
			$objResponse->addClear("idproduto", "value");
			$objResponse->addClear("preco_custo_unitario", "value");
			$objResponse->addClear("idproduto_Nome", "value");
			$objResponse->addClear("idproduto_NomeTemp", "value");
			$objResponse->addClear("idproduto_Tipo", "value");
			$objResponse->addAssign("idproduto_Flag", "className", "nao_selecionou");

			$objResponse->addClear("qtd_produto", "value");
			
			$objResponse->loadXML( Calcula_Total_AJAX());
			
			
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
		$objResponse->addConfirmCommands(1, utf8_encode("Deseja retirar o produto '" . $info_produto['descricao_produto'] . "' desta entrada de mercadorias ?"));

		// remove a tabela
		$objResponse->addRemove($nome_tabela);

		$objResponse->loadXML( Calcula_Total_AJAX () );
		
		// retorna o resultado XML
    return $objResponse->getXML();

  }
  
  
  /*
	Função: Seleciona_Produtos_Pedidos
 	Seleciona os produtos e colocam eles dinamicamente na tabela html
	*/
	function Seleciona_Produtos_Pedidos ($codigoPedido) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;

		//---------------------
		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

			$list_sql = "	SELECT
											PRD.*,PEDPRD.*
										FROM
           						{$conf['db_name']}pedido_produto PEDPRD
           						INNER JOIN {$conf['db_name']}produto PRD ON PEDPRD.idproduto=PRD.idproduto 
										WHERE
											PEDPRD.idpedido = $codigoPedido
										ORDER BY 
											PEDPRD.ordem_produto ASC	

									";


		//manda fazer a paginação
		$list_q = $db->query($list_sql);

		
		if($list_q){

			//busca os registros no banco de dados e monta o vetor de retorno
			$cont = 0;

			while($list = $db->fetch_array($list_q)){

				$post['inicio'] = "1";				
				$post['idproduto'] = $list['idproduto'];
				$post['total_produtos'] = $cont;
				$post['qtd_produto'] = number_format($list['qtd'],2,",","");
				$post['idproduto_Tipo'] = "P";
				if($list['valorUnit'] != "") $post['preco_custo_unitario'] = number_format($list['valorUnit'],2,",","");
				
				// acrescenta o XML que foi retornado no objeto atual
				$objResponse->loadXML( Insere_Produto_Encartelamento_AJAX($post) );
				
        
				$cont++;
			} // fim do while
			

		}
		

		// retorna o resultado XML
    return $objResponse->getXML();

	}


		/*
	Função: Insere_Contas_Pagar_AJAX
	Insere um produto ou um encartelamento dinamicamente na tabela html
	*/
	function Insere_Contas_Pagar_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $produto;
		global $produto_filial;
		global $encartelamento_produto;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
		
		
       
		$form->chk_moeda($post['valor_conta'], 0, 'Valor conta');
		$form->chk_IsDate($post['data_vencimento'],'A data de vencimento');
		


		$err = $form->err;
		
    
		// se houveram erros, mostra-os
    if(count($err) != 0) {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
    }
    // se nao houveram erros, insere na tabela html
		else {

		
			// incremente 1 na quantidade de total_produtos
			$total_contas = intval($post['total_contas']);

				// nome da tabela criada
				$total_contas++;
	      $nome_tabela = "tabela_contas_" . $total_contas;

		
	    if($_GET['ac'] == "editar")
			{
				$tabela = utf8_encode("
							<table width='100%' cellpadding='5' id='$nome_tabela'>
								<tr>
									<td class='tb_bord_baixo' align='left'>
									  <input type='hidden' name='$nome_tabela' id='$nome_tabela' value='" . $total_contas . "'/>
										<input type='hidden' name='data_$total_contas' id='data_$total_contas' value='" . $post['data_vencimento'] . "' />
										<input type='hidden' name='valor_conta_$total_contas' id='valor_conta_$total_contas' value='" . $post['valor_conta'] . "' />
									</td>
									
									<td class='tb_bord_baixo' align='center' width='10%
										 id='parcela_$total_contas' >" . $total_contas . "</td>
									</td>
									<td class='tb_bord_baixo' align='center' width='45%
										 id='data_conta_$total_contas' >" . $post['data_vencimento'] . "</td>
									</td>
									<td class='tb_bord_baixo' align='center' width='45%
										 id='preco_$total_contas' >" . $post['valor_conta'] . "</td>
									</td>

								</tr>
							</table>
						");
			}
			else
			{  
      // tabela de funcioanrio
				$tabela = utf8_encode("
							<table width='100%' cellpadding='5' id='$nome_tabela'>
								<tr>
									<td class='tb_bord_baixo' align='left'>
									  <input type='hidden' name='$nome_tabela' id='$nome_tabela' value='" . $total_contas . "'/>
										<input type='hidden' name='data_$total_contas' id='data_$total_contas' value='" . $post['data_vencimento'] . "' />
										<input type='hidden' name='valor_conta_$total_contas' id='valor_conta_$total_contas' value='" . $post['valor_conta'] . "' />
									</td>
									
									<td class='tb_bord_baixo' align='center' width='10%
										 id='parcela_$total_contas' >" . $total_contas . "</td>
									</td>
									<td class='tb_bord_baixo' align='center' width='35%
										 id='data_conta_$total_contas' >" . $post['data_vencimento'] . "</td>
									</td>
									<td class='tb_bord_baixo' align='center' width='40%
										 id='preco_$total_contas' >" . $post['valor_conta'] . "</td>
									</td>
								
									<td class='tb_bord_baixo' align='center' width='15%'>
										<a href='javascript:;' onclick=" . '"' . "xajax_Deleta_Contas_Pagar_AJAX(" . "'" . $total_contas . "'".");" . '"' . "><img src='../common/img/delete.gif'></a>
									</td>
								</tr>
							</table>
						");
				
					}
          
          
          
			// adiciona a tabela
      $objResponse->addAppend("div_contas", "innerHTML", $tabela);



			$objResponse->addAssign("total_contas", "value", $total_contas);
    

			// limpa os campos inseridos
			$objResponse->addClear("data_vencimento", "value");
			$objResponse->addClear("valor_conta", "value");

			
			$objResponse->loadXML( Calcula_Total_Contas_AJAX());
			
			
		}

		// retorna o resultado XML
    return $objResponse->getXML();

  }
	
	
  	/*
	Função: Deleta_Produto_Encartelamento_AJAX
	Deleta um produto dinamicamente na tabela html
	*/
	function Deleta_Contas_Pagar_AJAX ($codigoTabela) {

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
    $nome_tabela = "tabela_contas_" . $codigoTabela;

		// verifica se vai remover: Pula 1 linha se clicar no cancelar
		$objResponse->addConfirmCommands(1, utf8_encode("Deseja realmente retirar esta conta ?"));

		// remove a tabela
		$objResponse->addRemove($nome_tabela);

				$objResponse->loadXML( Calcula_Total_Contas_AJAX() );
		
		// retorna o resultado XML
    return $objResponse->getXML();

  }
  
	
  	/*	Função: Calcula_Total_AJAX
	Calcula o total do orçamento
	*/
	function Calcula_Total_Contas_AJAX ($post = "") {

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
			
			$objResponse->addScript("xajax_Calcula_Total_Contas_AJAX(xajax.getFormValues('for'));");
		}
		else {
			
			
			  
				$total = 0;

				for ($i=1; $i<=$post['total_contas']; $i++) {

					if ( isset( $post["tabela_contas_$i"] ) ) {
						
            $post["valor_conta_$i"] = str_replace(",",".",$post["valor_conta_$i"]);
						$total = $post["valor_conta_$i"] + $total;

					}

				}
				
				
				
				
               
				$total = number_format($total,2,",","");
        
				$objResponse->addAssign("total_contas_pagar", "innerHTML", $total);

		}

		// retorna o resultado XML
    return $objResponse->getXML();

	}

  	/*
	Função: Seleciona_Conta_Pedido
 	Seleciona as contas a pagar e colocam eles dinamicamente na tabela html
	*/
	function Seleciona_Conta_Pedido ($codigoPedido) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;

		//---------------------
		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

			$list_sql = "	SELECT
											CTP.*
										FROM
           						{$conf['db_name']}conta_pagar CTP
											WHERE
											 CTP.idpedido = $codigoPedido
											";


		//manda fazer a paginação
		$list_q = $db->query($list_sql);

		
		if($list_q){

			//busca os registros no banco de dados e monta o vetor de retorno
			$cont = 0;

			while($list = $db->fetch_array($list_q)){
				
				$post['valor_conta'] = number_format($list['valor_conta'],2,",","");
				$post['total_contas'] = $cont;
				$post['data_vencimento'] = $form->FormataDataParaExibir($list['data_vencimento']);
				$total = $total + $list['valor_conta'];				

				// acrescenta o XML que foi retornado no objeto atual
				$objResponse->loadXML( Insere_Contas_Pagar_AJAX($post) );
				
        
				$cont++;
			} // fim do while
			
			$objResponse->addAssign("total_contas_pagar", "innerHTML", $total);

		}
		
		

		// retorna o resultado XML
    return $objResponse->getXML();

	}


  	/*
	Função: Seleciona_Fornecedor_AJAX
 	Seleciona os fornecedores do produto e colocam eles dinamicamente na tabela html
	*/
	function Seleciona_Info_Fornecedor ($codigoPedido) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;

		//---------------------
		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

			$list_sql = "	SELECT
											PED.*,FORN.*,EDR.*,EST.*,CID.*
										FROM
           						{$conf['db_name']}pedido PED
											INNER JOIN {$conf['db_name']}fornecedor FORN ON PED.idfornecedor = FORN.idfornecedor
           						INNER JOIN {$conf['db_name']}endereco EDR	ON FORN.idendereco_fornecedor = EDR.idendereco
								 			LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade = CID.idcidade
								 			LEFT OUTER JOIN {$conf['db_name']}estado EST ON EDR.idestado = EST.idestado
										WHERE
											PED.idpedido = $codigoPedido
									";


		//manda fazer a paginação
		$list_q = $db->query($list_sql);

		
		if($list_q){
		
			while($list = $db->fetch_array($list_q)){

			$list['telefone_fornecedor'] = $form->FormataTelefoneParaExibir($list['telefone_fornecedor']);

			$info_fornecedor = "
					<table width=95% align=center>
								<tr>
									<td align=center><a target=_blank class=menu_item href=" . $conf['addr'] . "/admin/fornecedor.php?ac=editar&idfornecedor=" . $list['idfornecedor'] . ">Fornecedor: " . $list['nome_fornecedor'] . "</a></td>
									<td align=center><a target=_blank class=menu_item href=" . $conf['addr'] . "/admin/fornecedor.php?ac=editar&idfornecedor=" . $list['idfornecedor'] . ">Cidade: " . $list['nome_cidade'] . " / " . $list['sigla_estado'] . "</a></td>
									<td align=center><a target=_blank class=menu_item href=" . $conf['addr'] . "/admin/fornecedor.php?ac=editar&idfornecedor=" . $list['idfornecedor'] . ">Telefone: " . $list['telefone_fornecedor'] . "</a></td>
								</tr>
					</table>";

			$info_fornecedor = ereg_replace("(\r\n|\n|\r)", "", $info_fornecedor);

			}

			$objResponse->addAssign("dados_fornecedor", "innerHTML", $info_fornecedor);
		}
		
		

		// retorna o resultado XML
    return $objResponse->getXML();

	}


  
  

?>
