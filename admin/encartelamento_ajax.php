<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  

	require_once("../entidades/produto.php");
	require_once("../entidades/encartelamento.php");
	require_once("../entidades/encartelamento_produto.php");

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

	$produto = new produto();
	$encartelamento_produto = new encartelamento_produto();
										

  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();
        
				

  switch($flags['action']) {


		// busca os produtos de acordo com a busca
		case "busca_produto":

			$produto->Filtra_Produto_AJAX($_GET['typing']);

		break;

  }
  

  	
  // seta erros
  $smarty->assign("err", $err);

  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);

  	/*
	Fun??o: Insere_Produto_AJAX
	Insere uma referencia de produto dinamicamente na tabela html
	*/
	function Insere_Produto_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $encartelamento_produto;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		// codigo do funcionario
		$codigoProduto = $post['idproduto'];

		$form->chk_empty($post['idproduto'], 1, 'Produto');
		$form->chk_moeda($post['qtd_produto'], 0, "Quantidade");

		$err = $form->err;
				

		// se houveram erros, mostra-os
    if(count($err) != 0) {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
    }
    // se nao houveram erros, verifica se ele já nao existe na tabela
		else if (Verifica_Produto_Existe_AJAX ($post) == false) {

			// incrementa 1 na quantidade de produtos
			$total_produtos = intval($post['total_produtos']) + 1;
			$objResponse->addAssign("total_produtos", "value", $total_produtos);

			// busca os dados do encartelamento
			$info_produto = $encartelamento_produto->RetornaProduto($codigoProduto, $post['idfilial']);
			
      if ($post['tipoPreco'] == "B") {$info_produto['preco'] = $info_produto['preco_balcao_produto'];}
      else if ($post['tipoPreco'] == "O") {$info_produto['preco'] = $info_produto['preco_oferta_produto'];}
      else if ($post['tipoPreco'] == "A") {$info_produto['preco'] = $info_produto['preco_atacado_produto'];}
      else if ($post['tipoPreco'] == "T") {$info_produto['preco'] = $info_produto['preco_telemarketing_produto'];}
      
			$info_produto['qtd_produto'] = str_replace(",",".",$post['qtd_produto']);
      
      $info_produto['total'] = $info_produto['qtd_produto'] * $info_produto['preco'];
      
			$info_produto['preco'] = number_format($info_produto['preco'],2,",","");
			$info_produto['valor_final'] = number_format($info_produto['total'],2,",","");
      
			// nome da tabela criada
      $nome_tabela = "tabela_produto_" . $codigoProduto;

      // tabela de fornecedor
			$tabela = utf8_encode("
						<table width='100%' cellpadding='5' id='$nome_tabela'>
							<tr>
								<td class='tb_bord_baixo' align='center' width='10%'>{$info_produto['codigo_produto']}</td>
								<td class='tb_bord_baixo' align='left' width='25%'>
         				<input type='hidden' name='codigo_produto_$total_produtos' id='codigo_produto_$total_produtos' value='$codigoProduto' />
								<input type='hidden' name='qtd_produto_$total_produtos' id='qtd_produto_$total_produtos' value='{$post['qtd_produto']}' />
         				<input type='hidden' name='preco_total_$total_produtos' id='preco_total_$total_produtos' value='{$info_produto['valor_final']}' />


									{$info_produto['descricao_produto']}
								</td>
								<td class='tb_bord_baixo' align='center' width='10%'>&nbsp;{$info_produto['sigla_unidade_venda']}</td>
								<td class='tb_bord_baixo' align='right' width='10%'>&nbsp;{$post['qtd_produto']}</td>
								<td name='valor_$total_produtos' id='valor_$total_produtos' class='tb_bord_baixo' align='right' width='15%'>&nbsp;{$info_produto['preco']}</td>
								<td name='valor_final_$total_produtos' id='valor_final_$total_produtos' class='tb_bord_baixo' align='right' width='10%'>&nbsp;{$info_produto['valor_final']}</td>
								<td class='tb_bord_baixo' align='center' width='5%'>
								<a href='javascript:;' onclick=" . '"' . "xajax_Deleta_Produto_AJAX(" . "'" . $codigoProduto . "'" . ");" . '"' . "><img src='../common/img/delete.gif'></a>
								</td>
							</tr>
						</table>
					");


			// adiciona a tabela
			$objResponse->addAppend("div_encartelamento", "innerHTML", $tabela);

			$objResponse->addClear("idproduto", "value");
			$objResponse->addClear("idproduto_Nome", "value");
			$objResponse->addClear("idproduto_NomeTemp", "value");
			$objResponse->addClear("qtd_produto", "value");
			$objResponse->addAssign("idproduto_Flag", "className", "nao_selecionou");

			// calcula o total
			$objResponse->loadXML( Calcula_Total_AJAX () );


		}
		else {
			$objResponse->addAlert(utf8_encode("Este Produto já está na lista!"));
		}

		// retorna o resultado XML
    return $objResponse->getXML();

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
			$objResponse->addScript("xajax_Calcula_Total_AJAX(xajax.getFormValues('for_encartelamento'));");
		}
		else {
			  
				$total = 0;

				for ($i=1; $i<=$post['total_produtos']; $i++) {

					if ( isset( $post["codigo_produto_$i"] ) ) {
						
            $post["preco_total_$i"] = str_replace(",",".",$post["preco_total_$i"]);
						$total = $post["preco_total_$i"] + $total;

					}

				}



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

				if ( isset( $post["codigo_produto_$i"] ) ) {

						// busca os dados do encartelamento
						$info_produto = $encartelamento_produto->RetornaProduto($post["codigo_produto_$i"], $post['idfilial']);

			      if ($post['tipoPreco'] == "B") {$info_produto['preco'] = $info_produto['preco_balcao_produto'];}
			      else if ($post['tipoPreco'] == "O") {$info_produto['preco'] = $info_produto['preco_oferta_produto'];}
			      else if ($post['tipoPreco'] == "A") {$info_produto['preco'] = $info_produto['preco_atacado_produto'];}
			      else if ($post['tipoPreco'] == "T") {$info_produto['preco'] = $info_produto['preco_telemarketing_produto'];}

						$info_produto['qtd_produto'] = str_replace(",",".",$post["qtd_produto_$i"]);
						
			      $info_produto['total'] = $info_produto['qtd_produto'] * $info_produto['preco'];

						$info_produto['preco'] = number_format($info_produto['preco'],2,",","");

						$total = $info_produto['total'] + $total;

						$info_produto['total'] = number_format($info_produto['total'],2,",","");
						
						$objResponse->addAssign("valor_$i", "innerHTML", $info_produto['preco']);
						$objResponse->addAssign("valor_final_$i", "innerHTML", $info_produto['total']);

				}

		}
		
    		$total = number_format($total,2,",","");
		  	$objResponse->addAssign("SubTotal", "innerHTML", $total);
		  	
		  // retorna o resultado XML
    	return $objResponse->getXML();
}

	/*
	Fun??o: Verifica_Produto_Existe_AJAX
	Verifica se uma referencia ja existe na tabela html
	*/
	function Verifica_Produto_Existe_AJAX ($post) {

		for ($i=1; $i<=intval($post['total_produtos']); $i++) {
			if ( $post["codigo_produto_$i"] == $post['idproduto'] ) return true;
		}

		return false;
  }



	/*
	Fun??o: Deleta_Produto_AJAX
	Deleta uma referencia dinamicamente na tabela html
	*/
	function Deleta_Produto_AJAX ($codigoProduto) {

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
    $nome_tabela = "tabela_produto_" . $codigoProduto;

		// busca os dados do funcion?rio
		$info_produto = $produto->getById($codigoProduto);

		// verifica se vai remover: Pula 1 linha se clicar no cancelar
		$objResponse->addConfirmCommands(1, utf8_encode("Deseja excluir o produto '{$info_produto['descricao_produto']}' deste encartelamento ?"));

		// remove a tabela
		$objResponse->addRemove($nome_tabela);

		// retorna o resultado XML
		$objResponse->loadXML( Calcula_Total_AJAX () );
    return $objResponse->getXML();

  }


	/*
	Fun??o: Seleciona_Produto_AJAX
 	Seleciona as referencias do produto e colocam eles dinamicamente na tabela html
	*/
	function Seleciona_Produto_AJAX ($codigoEncartelamento, $post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------


		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

    $idfilial = $post['idfilial'];
    
		$list_sql = "	SELECT
										PRD.*, UNV.*,PRDFL.*,ENCTPRD.*
									FROM
										{$conf['db_name']}produto PRD
											INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda=UNV.idunidade_venda
											INNER JOIN {$conf['db_name']}produto_filial PRDFL ON PRDFL.idproduto=PRD.idproduto
											INNER JOIN {$conf['db_name']}encartelamento_produto ENCTPRD ON ENCTPRD.idproduto=PRD.idproduto

									WHERE
											PRDFL.idfilial = $idfilial
											AND ENCTPRD.idencartelamento = $codigoEncartelamento
											
									ORDER BY
										PRD.descricao_produto ASC ";


		//manda fazer a pagina??o
		$list_q = $db->query($list_sql);

		
		if($list_q){

			//busca os registros no banco de dados e monta o vetor de retorno
			$cont = 0;

			while($list = $db->fetch_array($list_q)){
				$post['idproduto'] = $list['idproduto'];
				$post['qtd_produto'] = number_format($list['qtd'],2,",","");
				$post['total_produtos'] = $cont;

				// acrescenta o XML que foi retornado no objeto atual
				$objResponse->loadXML( Insere_Produto_AJAX($post) );

        $cont++;
			} // fim do while

		}

		// retorna o resultado XML
    return $objResponse->getXML();

	}


	/*
	Fun??o: Verifica_Campos_Encartelamento_AJAX
	Verifica se os campos do produto foram preenchidos
	*/
	function Verifica_Campos_Encartelamento_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
	
		// editar
		if ($_GET['ac'] == "editar") {
			$form->chk_empty($post['litdescricao_encartelamento'], 1, 'Descrição do Encartelamento');	
			$err = $form->err;   
		}
		// adicionar
		else {
			$form->chk_empty($post['descricao_encartelamento'], 1, 'Descrição do Encartelamento');
			$err = $form->err;
		}

		// verifica se foi inserido algum produto
		$existe_produto = 0;
		for ($i=1; $i<=$post['total_produtos']; $i++) {
			if ( isset( $post["codigo_produto_$i"] ) ) {
				$existe_produto = 1;
				break;
			}
		}
		if ($existe_produto == 0) $err[] = "Adicione um ou mais produtos ao encartelamento.";
		//-----------------------------------------------------------		

		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {
			// verifica se o usuário quer alterar os dados
			$objResponse->addConfirmCommands(1, utf8_encode("Deseja gravar os dados do Encartelamento ?"));	
    	$objResponse->addScript("document.getElementById('for_encartelamento').submit();");
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
