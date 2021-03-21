<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");

	require_once("../entidades/funcionario.php");
  require_once("../entidades/produto.php");
	require_once("../entidades/pedido.php");
	require_once("../entidades/transferencia_filial.php");
  require_once("../entidades/produto_filial.php");
  require_once("../entidades/encartelamento_produto.php");



  // inicializa templating
  $smarty = new Smarty;

  // ação selecionada
  $flags['action'] = $_GET['ac'];

  // inicializa autenticação
  $auth = new auth();

	//inicializa classe
	$funcionario = new funcionario();
	$pedido = new pedido();
	$transferencia_filial = new transferencia_filial();
	$produto = new produto();
	$produto_filial = new produto_filial();
	$encartelamento_produto = new encartelamento_produto();

  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();
        
				

  switch($flags['action']) {



	}
  
   
  
  
  
  


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

					}

				}
				
				
				
				
               
				$total = number_format($total,2,",","");
        
				$objResponse->addAssign("Sub", "innerHTML", $total);
				
        $total = str_replace(",",".",$total);
        
        // desconto do produto
        $total = $total - (str_replace(",",".",$post["desconto"])/100 * $total);
        
        $objResponse->addAssign("preco_total", "value", $total); 
        
        $total = number_format($total,2,",","");
				          
		  	$objResponse->addAssign("SubTotal", "innerHTML", $total);


		}

		// retorna o resultado XML
    return $objResponse->getXML();

	}
	
	

		/*	Função: Atualiza_Total_AJAX
	Re-Calcula o total do orçamento
	*/
	function Atualiza_Total_AJAX ($post) {

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
		
		for ($i=1; $i<=$post['total_produtos']; $i++) {

				if ( isset( $post["idproduto_$i"] ) ) {
					
						
							
						// busca os dados do encartelamento
						$info_produto = $encartelamento_produto->retornaProduto($post["idproduto_$i"], $_SESSION['idfilial_usuario']);

			      if ($post['tipoPreco'] == "B") {$info_produto['preco'] = $info_produto['preco_balcao_produto'];}
			      else if ($post['tipoPreco'] == "O") {$info_produto['preco'] = $info_produto['preco_oferta_produto'];}
			      else if ($post['tipoPreco'] == "A") {$info_produto['preco'] = $info_produto['preco_atacado_produto'];}
			      else if ($post['tipoPreco'] == "T") {$info_produto['preco'] = $info_produto['preco_telemarketing_produto'];}
			      else if ($post['tipoPreco'] == "C") {$info_produto['preco'] = $info_produto['preco_custo_produto'];}

						$info_produto['qtd_produto'] = str_replace(",",".",$post["qtd_produto_$i"]);
						
			      $info_produto['total'] = $info_produto['qtd_produto'] * $info_produto['preco'];

						$info_produto['preco'] = number_format($info_produto['preco'],2,",","");

						$total = $info_produto['total'] + $total;

						$objResponse->addAssign("preco_total_$i", "value", $info_produto['total']);
						
						$info_produto['total'] = number_format($info_produto['total'],2,",","");
						
						
						
						$objResponse->addAssign("valor_$i", "innerHTML", $info_produto['preco']);
						$objResponse->addAssign("valor_final_$i", "innerHTML", $info_produto['total']);
						

				}

		}
				$total = number_format($total,2,",","");
				
    		$objResponse->addAssign("Sub", "innerHTML", $total);
    		
    		$total = str_replace(",",".",$total);
    		
    		$total = $total - (str_replace(",",".",$post["desconto"])/100 * $total);
    		
    		$total = number_format($total,2,",","");
    		
		  	$objResponse->addAssign("SubTotal", "innerHTML", $total);
		  	
		  	//$objResponse->loadXML( Calcula_Total_AJAX());
		  	
		  // retorna o resultado XML
    	return $objResponse->getXML();
}
	
	

	/*
	Função: Verifica_Campos_Pedido_AJAX
	Verifica se os campos da tranferencia de estoque foram preenchidos
	*/

	function Verifica_Campos_Pedido_AJAX ($post , $gerar="") {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $transferencia_filial;
		global $funcionario;
		global $pedido;

		//---------------------


		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		if($_GET['ac'] == "adicionar")
		{
			$form->chk_empty($post['idfornecedor'], 1, 'o fornecedor');
			
			$err = $form->err;
				
			for ($i=1; $i<=$post['total_produtos']; $i++) {
			
				if ( isset( $post["idproduto_$i"] ) ) {
	
					$existe_produto = 1;
					break;
				}
			}
		
			if ($existe_produto==0) $err[]="É preciso fazer no mínimo o pedido de um produto!";

			// verifica se a senha do funcionario está correta
			$info_funcionario = $funcionario->getById($post['idfuncionario']);
			if ($info_funcionario['senha_funcionario'] != md5($post['senha_funcionario'])) $err[] = "A senha digitada está incorreta !";

				
		}
		else
		{
	
			$err = $form->err;
				
			for ($i=1; $i<=$post['total_produtos']; $i++) {
			
				if ( isset( $post["idproduto_$i"] ) ) {
	
					$existe_produto = 1;
					break;
				}
			}
		
			if ($existe_produto==0) $err[]="É preciso fazer no mínimo o pedido de um produto!";	

			// verifica se a senha do funcionario está correta
			$info_funcionario = $funcionario->getById($post['idfuncionario']);
			if ($info_funcionario['senha_funcionario'] != md5($post['senha_funcionario'])) $err[] = "A senha digitada está incorreta !";

			$list = $pedido->getById($_GET['idpedido']);
			
			if($list['status_pedido'] == 'E') $err[] = "Já foi gerado uma entrada para este pedido.";							

		}	

		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {
		
		if($gerar != "")
		{
				$objResponse->addConfirmCommands(2, utf8_encode("Gerar a entrada?"));
				$objResponse->addAssign("gerar","value",1);
				$objResponse->addScript("document.getElementById('for').submit();");			
		}
    else
		{
  		// verifica se confirma está transação.
			$objResponse->addConfirmCommands(1, utf8_encode("Confirma o pedido?"));
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
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
		
        
		$form->chk_empty($post['idproduto'], 1, 'Produto');
		$form->chk_moeda($post['qtd_produto'], 0, 'Quantidade');
		//if ($post['qtd_produto'] == "0,00") $form->err[] = "A quantidade não pode ser 0!";


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

							$mensagem = "Não será possível inserir este produto. Ele já está contido no pedido ! Caso esteja inserindo um Encartelamento, verifique se algum produto dele já está contido neste pedido !";
							$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));

							// retorna o resultado XML
			    		return $objResponse->getXML();
						}
					} // fim do for
					//---------------------------------------
				}
			} // fim do for

			
			// incremente 1 na quantidade de total_produtos
			$total_produtos = intval($post['total_produtos']);

			// percorre os produtos e vai adicionando-os na tabela
			for ($i=0; $i<count($array_produtos); $i++) {
				
				
    
    		if($post['idproduto_Tipo'] =="E") 
    		{
    			$info_produto_filial = $produto_filial->getByIdFilial($array_produtos[$i]['idproduto'],$_SESSION['idfilial_usuario']);
    			
    		}
    		else 
    		{
    			$info_produto_filial = $produto_filial->getByIdFilial($post['idproduto'],$_SESSION['idfilial_usuario']);
  
    		}

				// nome da tabela criada
				$total_produtos++;
	      $nome_tabela = "tabela_produto_" . $total_produtos;

	      
	      // busca os dados do encartelamento
				$info_produto = $encartelamento_produto->retornaProduto($post['idproduto'], $_SESSION['idfilial_usuario']);
				
	      
      // tabela de funcioanrio
				$tabela = utf8_encode("
							<table width='100%' cellpadding='5' id='$nome_tabela'>
								<tr>
									<td class='tb_bord_baixo' align='left'>
									  <input type='hidden' name='$nome_tabela' id='$nome_tabela' value='" . $total_produtos . "'/>
										<input type='hidden' name='idproduto_$total_produtos' id='idproduto_$total_produtos' value='" . $array_produtos[$i]['idproduto'] . "' />
										<input type='hidden' name='descricao_produto_$total_produtos' id='descricao_produto_$total_produtos' value='" . $array_produtos[$i]['descricao_produto'] . "' />
										<input type='hidden' name='qtd_produto_$total_produtos' id='qtd_produto_$total_produtos' value='" . $array_produtos[$i]['qtd_produto'] . "' />
									</td>

									<td width='10%' align='left' id='codigo_produto_$total_produtos' class='tb_bord_baixo'>" . $array_produtos[$i]['idproduto'] . "</td>
									<td width='60%' align='left' id='descricao_produto_$total_produtos' class='tb_bord_baixo'>" . $array_produtos[$i]['descricao_produto'] . "</td>

         					<td width='10%' align='center' id='sigla_unidade_venda_$total_produtos' class='tb_bord_baixo'>" . $array_produtos[$i]['sigla_unidade_venda'] . "</td>
									
									<td class='tb_bord_baixo' align='center' width='10%' id='qtd_produto_$total_produtos' >" . $array_produtos[$i]['qtd_produto'] . "</td>

									<td class='tb_bord_baixo' align='center' width='10%'>
										<a href='javascript:;' onclick=" . '"' . "xajax_Deleta_Produto_Encartelamento_AJAX(" . "'" . $total_produtos . "', '" . $array_produtos[$i]['idproduto'] . "'" . ");" . '"' . "><img src='../common/img/delete.gif'></a>
									</td>

								</tr>
							</table>
						");
				

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
		$objResponse->addConfirmCommands(1, utf8_encode("Deseja retirar o produto '" . $info_produto['descricao_produto'] . "' deste pedido ?"));

		// remove a tabela
		$objResponse->addRemove($nome_tabela);

		$objResponse->loadXML( Calcula_Total_AJAX () );
		
		// retorna o resultado XML
    return $objResponse->getXML();

  }
  
  
  	/*
	Função: Seleciona_Fornecedor_AJAX
 	Seleciona os fornecedores do produto e colocam eles dinamicamente na tabela html
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
											";


		//manda fazer a paginação
		$list_q = $db->query($list_sql);

		
		if($list_q){

			//busca os registros no banco de dados e monta o vetor de retorno
			$cont = 0;

			while($list = $db->fetch_array($list_q)){
				
				//$objResponse->addAlert("teste". $cont);
    		$post['idproduto'] = $list['idproduto'];
				$post['total_produtos'] = $cont;
				$post['qtd_produto'] = number_format($list['qtd'],2,",","");
				$post['idproduto_Tipo'] = "P";
				
				// acrescenta o XML que foi retornado no objeto atual
				$objResponse->loadXML( Insere_Produto_Encartelamento_AJAX($post) );
				
        
				$cont++;
			} // fim do while
			

		}
		

		// retorna o resultado XML
    return $objResponse->getXML();

	}




?>
