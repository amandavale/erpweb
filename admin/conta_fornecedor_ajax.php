<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");

	require_once("../entidades/conta_fornecedor.php");
	require_once("../entidades/banco.php");


  // inicializa templating
  $smarty = new Smarty;

  // ação selecionada
  $flags['action'] = $_GET['ac'];

  // inicializa autenticação
  $auth = new auth();

	//inicializa classe
	$conta_filial = new conta_fornecedor();
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
		$form->chk_empty($post['agencia_fornecedor'], 1, 'Agência');
		$form->chk_empty($post['conta_fornecedor'], 1, 'Conta');


		$err = $form->err;

		// se houveram erros, mostra-os
    if(count($err) != 0) {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
    }
    // se nao houveram erros, insere na tabela html
		else {
			
			// incremente 1 na quantidade de total_contas_bancarias
			$total_contas_bancarias = intval($post['total_contas_bancarias']) + 1;
			$objResponse->addAssign("total_contas_bancarias", "value", $total_contas_bancarias);

			// busca os dados do banco
			$info_banco = $banco->getById($post['idbanco']);

			// nome da tabela criada
      $nome_tabela = "tabela_conta_bancaria_" . $total_contas_bancarias;

			// conta principal ?
			if ($post['principal_fornecedor'] == "1") $principal = "Sim";
			else $principal = "Não";
      
      // tabela de funcioanrio
			$tabela = utf8_encode("
						<table width='100%' cellpadding='5' id='$nome_tabela'>
							<tr>
								<td class='tb_bord_baixo' align='left' width='35%'>
									<input type='hidden' name='idbanco_$total_contas_bancarias' id='idbanco_$total_contas_bancarias' value='{$post['idbanco']}' />
									<input type='hidden' name='agencia_fornecedor_$total_contas_bancarias' id='agencia_fornecedor_$total_contas_bancarias' value='{$post['agencia_fornecedor']}' />
									<input type='hidden' name='agencia_dig_fornecedor_$total_contas_bancarias' id='agencia_dig_fornecedor_$total_contas_bancarias' value='{$post['agencia_dig_fornecedor']}' />
									<input type='hidden' name='conta_fornecedor_$total_contas_bancarias' id='conta_fornecedor_$total_contas_bancarias' value='{$post['conta_fornecedor']}' />
									<input type='hidden' name='conta_dig_fornecedor_$total_contas_bancarias' id='conta_dig_fornecedor_$total_contas_bancarias' value='{$post['conta_dig_fornecedor']}' />
									<input type='hidden' name='principal_fornecedor_$total_contas_bancarias' id='principal_fornecedor_$total_contas_bancarias' value='{$post['principal_fornecedor']}' />

									{$info_banco['nome_banco']} - {$info_banco['codigo_banco']}
								</td>
								<td class='tb_bord_baixo' align='center' width='15%'>{$post['agencia_fornecedor']}-{$post['agencia_dig_fornecedor']}</td>
								<td class='tb_bord_baixo' align='center' width='15%'>{$post['conta_fornecedor']}-{$post['conta_dig_fornecedor']}</td>
								<td class='tb_bord_baixo' align='center' width='10%'>$principal</td>
								<td class='tb_bord_baixo' align='center' width='5%'>
									<a href='javascript:;' onclick=" . '"' . "xajax_Deleta_Conta_Bancaria_AJAX(" . "'" . $total_contas_bancarias . "'" . ");" . '"' . "><img src='../common/img/delete.gif'></a>
								</td>
							</tr>
						</table>
					");

			// adiciona a tabela
			$objResponse->addAppend("div_contas_bancarias", "innerHTML", $tabela);
			
			// limpa os campos inseridos
			$objResponse->addClear("idbanco", "value");
			$objResponse->addClear("idbanco_Nome", "value");
			$objResponse->addClear("idbanco_NomeTemp", "value");
			$objResponse->addAssign("idbanco_Flag", "className", "nao_selecionou");

			$objResponse->addClear("agencia_fornecedor", "value");
			$objResponse->addClear("agencia_dig_fornecedor", "value");
			$objResponse->addClear("conta_fornecedor", "value");
			$objResponse->addClear("conta_dig_fornecedor", "value");
			
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
		$objResponse->addConfirmCommands(1, utf8_encode("Deseja excluir esta conta bancária do fornecedor ?"));

		// remove a tabela
		$objResponse->addRemove($nome_tabela);

		// retorna o resultado XML
    return $objResponse->getXML();

  }


	/*
	Função: Seleciona_Conta_Bancaria_AJAX
 	Seleciona as contas bancárias da filial e coloca eles dinamicamente na tabela html
	*/
	function Seleciona_Conta_Bancaria_AJAX ($codigoFornecedor) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------
		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		$list_sql = "	SELECT
										CFOR.*
									FROM
		     						{$conf['db_name']}conta_fornecedor CFOR
									WHERE
										CFOR.idfornecedor = $codigoFornecedor
									ORDER BY
										CFOR.principal_fornecedor DESC
								";

		//manda fazer a paginação
		$list_q = $db->query($list_sql);

		
		if($list_q){

			//busca os registros no banco de dados e monta o vetor de retorno
			$cont = 0;

			while($list = $db->fetch_array($list_q)){
				$list['total_contas_bancarias'] = $cont;

				// acrescenta o XML que foi retornado no objeto atual
				$objResponse->loadXML( Insere_Conta_Bancaria_AJAX($list) );

        $cont++;
			} // fim do while

		}

		// retorna o resultado XML
    return $objResponse->getXML();

	}



  


?>
