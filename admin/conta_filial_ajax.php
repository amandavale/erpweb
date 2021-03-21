<?php

  	//inclusão de bibliotecas
	require_once("../common/lib/conf.inc.php");
	require_once("../common/lib/db.inc.php");
	require_once("../common/lib/auth.inc.php");
	require_once("../common/lib/form.inc.php");
	require_once("../common/lib/Smarty/Smarty.class.php");

	require_once("../entidades/conta_filial.php");
	require_once("../entidades/banco.php");


  	//inicializa templating
	$smarty = new Smarty;

  	// ação selecionada
	$flags['action'] = $_GET['ac'];

  	//inicializa autenticação
	$auth = new auth();

	//inicializa classe
	$conta_filial = new conta_filial();
	$banco = new banco();

	// inicializa banco de dados
	$db = new db();

	//incializa classe para validação de formulário
	$form = new form();
        
				

	/*
	Função: Insere_Conta_Bancaria_AJAX
	Insere uma Conta Bancaria dinamicamente na tabela html
	*/
	function Insere_Conta_Bancaria_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		
		global $banco;
		//---------------------
		
		// cria o objeto xajaxResponse
    	$objResponse = new xajaxResponse();

		$form->chk_empty($post['idbanco'], 1, 'Banco');
		$form->chk_empty($post['agencia_filial'], 1, 'Agência');
		$form->chk_empty($post['conta_filial'], 1, 'Conta');


		$err = $form->err;

		// se houveram erros, mostra-os
	    if(count($err) != 0) {
				$mensagem = implode("\n",$err);
				$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
	    }
    	// se nao houve erros, insere na tabela html
		else {
			
			// incremente 1 na quantidade de total_contas_bancarias
			$total_contas_bancarias = intval($post['total_contas_bancarias']) + 1;
			$objResponse->addAssign("total_contas_bancarias", "value", $total_contas_bancarias);

			// busca os dados do banco
			$info_banco = $banco->getById($post['idbanco']);

			// nome da tabela criada
      		$nome_tabela = "tabela_conta_bancaria_" . $total_contas_bancarias;

			// conta principal ?
			if ($post['principal_filial'] == "1") $principal = "Sim";
			else $principal = "Não";
      
      		if(!$post['conta_cedente'] && $post['nome_filial']){
      			$post['conta_cedente'] = $post['nome_filial'];
      		}

      		if(!$post['conta_cnpj'] && $post['cnpj_filial']){
      			$post['conta_cnpj'] = $post['cnpj_filial'];
      		}

			$post['conta_cedente'] = utf8_decode($post['conta_cedente']);

      		// tabela de contas da filial
			$tabela = utf8_encode("
							<tr>
								<td class='tb_bord_baixo' align='left' width='20%'>
									<input type='hidden' name='idbanco_$total_contas_bancarias' id='idbanco_$total_contas_bancarias' value='{$post['idbanco']}' />
									<input type='hidden' name='agencia_filial_$total_contas_bancarias' id='agencia_filial_$total_contas_bancarias' value='{$post['agencia_filial']}' />
									<input type='hidden' name='agencia_dig_filial_$total_contas_bancarias' id='agencia_dig_filial_$total_contas_bancarias' value='{$post['agencia_dig_filial']}' />
									<input type='hidden' name='conta_filial_$total_contas_bancarias' id='conta_filial_$total_contas_bancarias' value='{$post['conta_filial']}' />
									<input type='hidden' name='conta_cedente_$total_contas_bancarias' id='conta_cedente_$total_contas_bancarias' value='{$post['conta_cedente']}' />
									<input type='hidden' name='conta_cnpj_$total_contas_bancarias' id='conta_cnpj_$total_contas_bancarias' value='{$post['conta_cnpj']}' />
									<input type='hidden' name='conta_dig_filial_$total_contas_bancarias' id='conta_dig_filial_$total_contas_bancarias' value='{$post['conta_dig_filial']}' />
									<input type='hidden' name='principal_filial_$total_contas_bancarias' id='principal_filial_$total_contas_bancarias' value='{$post['principal_filial']}' />
									<input type='hidden' name='carteira_$total_contas_bancarias' id='carteira_$total_contas_bancarias' value='{$post['carteira']}' />
									<input type='hidden' name='identificador_$total_contas_bancarias' id='identificador_$total_contas_bancarias' value='{$post['identificador']}' />
									<input type='hidden' name='prefixo_nosso_numero_$total_contas_bancarias' id='prefixo_nosso_numero_$total_contas_bancarias' value='{$post['prefixo_nosso_numero']}' />

									{$info_banco['nome_banco']} - {$info_banco['codigo_banco']}
								</td>
								<td class='tb_bord_baixo' align='center' width='20%'>{$post['conta_cedente']}</td>
								<td class='tb_bord_baixo' align='center' width='15%'>{$post['conta_cnpj']}</td>
								<td class='tb_bord_baixo' align='center' width='5%'>{$post['agencia_filial']}-{$post['agencia_dig_filial']}</td>
								<td class='tb_bord_baixo' align='center' width='10%'>{$post['conta_filial']}-{$post['conta_dig_filial']}</td>
								<td class='tb_bord_baixo' align='center' width='5%'>{$post['carteira']}</td>
								<td class='tb_bord_baixo' align='left' width='5%'>{$post['identificador']}</td>
								<td class='tb_bord_baixo' align='left' width='10%'>{$post['prefixo_nosso_numero']}</td>
								<td class='tb_bord_baixo' align='center' width='5%'>$principal</td>
								<td class='tb_bord_baixo' align='center' width='5%'>
									<a href='javascript:;' onclick=" . '"' . "xajax_Deleta_Conta_Bancaria_AJAX(" . "'" . $total_contas_bancarias . "'" . ");" . '"' . "><img src='../common/img/delete.gif'></a>
								</td>
							</tr>
					");

			$objResponse->addCreate("div_contas_bancarias", "table", "$nome_tabela");
			$objResponse->addAssign("$nome_tabela", "innerHTML", "$tabela");
			$objResponse->addAssign("$nome_tabela", 'width', "100%");
			$objResponse->addAssign("$nome_tabela", 'cellpadding', "5");


			
			// limpa os campos inseridos
			$objResponse->addClear("idbanco", "value");
			$objResponse->addClear("idbanco_Nome", "value");
			$objResponse->addClear("idbanco_NomeTemp", "value");
			$objResponse->addAssign("idbanco_Flag", "className", "nao_selecionou");

			$objResponse->addClear("agencia_filial", "value");
			$objResponse->addClear("agencia_dig_filial", "value");
			$objResponse->addClear("conta_filial", "value");
			$objResponse->addClear("conta_dig_filial", "value");
			$objResponse->addClear("conta_cedente", "value");
			$objResponse->addClear("conta_cnpj", "value");
			$objResponse->addClear("identificador", "value");
			$objResponse->addClear("prefixo_nosso_numero", "value");
			$objResponse->addClear("carteira", "value");


		}
		
		// retorna o resultado XML
    	return $objResponse->getXML();
    
  }
  
  

	/*
	Função: Deleta_Conta_Bancaria_AJAX
	Deleta uma conta bancária dinamicamente na tabela html
	*/
	function Deleta_Conta_Bancaria_AJAX ($codigoTabela) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------

		// cria o objeto xajaxResponse
    	$objResponse = new xajaxResponse();

		// nome da tabela criada
    	$nome_tabela = "tabela_conta_bancaria_" . $codigoTabela;

		// verifica se vai remover: Pula 1 linha se clicar no cancelar
		$objResponse->addConfirmCommands(1, utf8_encode("Deseja excluir esta conta bancária da filial ?"));

		// remove a tabela
		$objResponse->addRemove($nome_tabela);

		// retorna o resultado XML
   		return $objResponse->getXML();

  }


	/*
	Função: Seleciona_Conta_Bancaria_AJAX
 	Seleciona as contas bancárias da filial e coloca eles dinamicamente na tabela html
	*/
	function Seleciona_Conta_Bancaria_AJAX ($codigoFilial) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------
		
		// cria o objeto xajaxResponse
    	$objResponse = new xajaxResponse();

		$list_sql = "	SELECT
								*
							FROM
     						{$conf['db_name']}conta_filial CFLI
     						LEFT JOIN {$conf['db_name']}filial ON (CFLI.idfilial = filial.idfilial)
							WHERE
								CFLI.idfilial = $codigoFilial
							ORDER BY
								CFLI.principal_filial DESC
						";

		//manda fazer a paginação
		$list_q = $db->query($list_sql);

		
		if($list_q){

			//busca os registros no banco de dados e monta o vetor de retorno
			$cont = 0;

			while($list = $db->fetch_array($list_q)){

				$list['total_contas_bancarias'] = $cont;


				/**
				 * O trecho a seguir foi acrescentado devido a um problema de codificação na função
				 * Insere_Conta_Bancaria_AJAX.
				 * O nome que chega do formulário no cadastro precisa da função utf8_decode antes de ser inserido
				 * no banco de dados.
				 * O nome que chega do banco de dados e é enviado para a função para ser mostrado na tela não
				 * precisa de utf8_decode.
				 * Isso está causando conflito e erro na hora de registrar os dados.
				 * O trecho a seguir transforma as strings com encode para que o decode possa ser aplicado na função
				 * Insere_Conta_Bancaria_AJAX.
				 */
				$list['conta_cedente'] = utf8_encode($list['conta_cedente']);
				$list['nome_filial'] = utf8_encode($list['nome_filial']);

				// acrescenta o XML que foi retornado no objeto atual
				$objResponse->loadXML( Insere_Conta_Bancaria_AJAX($list) );

        		$cont++;
			} // fim do while

		}

		// retorna o resultado XML
    return $objResponse->getXML();

	}



  


?>
