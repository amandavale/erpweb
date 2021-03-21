<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");

	require_once("../entidades/cheque.php");
	require_once("../entidades/conta_receber.php");

  // inicializa templating
  $smarty = new Smarty;

  // ação selecionada
  $flags['action'] = $_GET['ac'];

  // inicializa autenticação
  $auth = new auth();

	//inicializa classe
	$cheque = new cheque();

	$conta_receber = new conta_receber();


  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();
        

	/*
	Fun??o: Verifica_Campos_Cheque_AJAX
	Verifica se os campos do cheque foram preenchidos
	*/
	function Verifica_Campos_Cheque_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		//---------------------

		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
		
		if ($_GET['ac'] == "editar") {
			$form->chk_empty($post['numidbanco'], 1, 'Banco'); 
			$form->chk_empty($post['litagencia'], 1, 'Agência'); 
			$form->chk_empty($post['litconta'], 1, 'Conta'); 
			$form->chk_empty($post['litnumero_cheque'], 1, 'Nº do cheque'); 
			$form->chk_IsDate($post['litdata_cheque'], "Data de cheque");
			$form->chk_empty($post['numvalor'], 1, 'Valor (R$)');
		}
		else {
			$form->chk_empty($post['idbanco'], 1, 'Banco'); 
			$form->chk_empty($post['agencia'], 1, 'Agência'); 
			$form->chk_empty($post['conta'], 1, 'Conta'); 
			$form->chk_empty($post['numero_cheque'], 1, 'Nº do cheque'); 
			$form->chk_IsDate($post['data_cheque'], "Data de cheque");
			$form->chk_empty($post['valor'], 1, 'Valor (R$)');
		}

		$err = $form->err;


		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {
    	
    	$objResponse->addScript("document.getElementById('for_cheque').submit();");

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
	Fun??o: Verifica_Campos_Busca_Rapida_Cheque_AJAX
	Verifica se os campos da busca rápida do cheque
	*/
	function Verifica_Campos_Busca_Rapida_Cheque_AJAX ($post, $idcheque_inserido = "") {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $cheque;
		//---------------------

		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
		
		$form->chk_empty($post['idcheque'], 1, 'Código do cheque'); 

		$err = $form->err;


		// se nao houveram erros, exibe os dados do cheque
    if(count($err) == 0) {
    	
			//busca detalhes do cheque
			$info = $cheque->getById($post['idcheque']);

			// se o cheque não existir, mostra msg de erro
			if ($info['idcheque'] == "") {
	
				$tabela = "
					<table width='100%'  border='0' cellpadding='2' cellspacing='3' bgcolor='#FDF5E6' class='tb4cantos'>
						<tr><td><b>Código do Cheque inexistente !</b></td></tr>
					</table>
				";	

			}
			// se o cheque existir, mostra os dados
			else {
	
				$tabela = "
					<table border='0' width='100%' align='center'>
						<tr>
							<td align='right' width='40%'><b>CÓDIGO DO CHEQUE:</b></td>
							<td><b>{$info['idcheque']}</b></td>
						</tr>
		
						<tr>
							<td align='right'>Banco:</td>
							<td>{$info['nome_banco']}</td>
						</tr>
			
						<tr>
							<td align='right'>Agência:</td>
							<td>{$info['agencia']}-{$info['agencia_dig']}</td>
						</tr>
						
						<tr>
							<td align='right'>Conta:</td>
							<td>{$info['conta']}-{$info['conta_dig']}</td>
						</tr>
						
						<tr>
							<td align='right'>Nº do cheque:</td>
							<td>{$info['numero_cheque']}</td>
						</tr>
						
						<tr>
							<td align='right'>Data do cheque:</td>
							<td>{$info['data_cheque']}</td>
						</tr>
						
						<tr>
							<td align='right'>Data (Bom para):</td>
							<td>{$info['bom_para']}</td>
						</tr>
						
						<tr>
							<td align='right'>Titular da conta:</td>
							<td>{$info['titular_conta']}</td>
						</tr>
						
						<tr>
							<td align='right'>Valor do cheque (R$):</td>
							<td>{$info['valor']}</td>
						</tr>
						
						<tr>
							<td align='right'>Observação:</td>
							<td>{$info['observacao']}</td>
						</tr>

						<tr>
							<td align='center' colspan='2'>&bull;<a class='link_geral' href='{$conf['addr']}/admin/cheque.php?ac=editar&idcheque={$info['idcheque']}'>Editar dados deste Cheque</a></td>
						</tr>

					</table>
				";

			}

			$tabela = utf8_encode(html_entity_decode($tabela));

			$objResponse->addAssign("dados_cheque", "innerHTML", "$tabela");

			if ($idcheque_inserido != "") {
				$mensagem = "O Código do Cheque inserido é $idcheque_inserido. Anote este número no verso do cheque!";
				$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
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
	Função: Insere_Cheque_AJAX
	Insere um Cheque dinamicamente na tabela html
	*/
	function Insere_Cheque_AJAX ($post) {
		
		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;
		
		global $cheque;
		global $conta_receber;
		//---------------------
		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		$form->chk_empty($post['idcheque'], 1, 'Código do cheque');

		$err = $form->err;

		// busca os dados do cheque
		$info_cheque = $cheque->getById($post['idcheque']);
		if ($info_cheque['idcheque'] == "") $err[] = "Código do Cheque inexistente !";

		// verifica se o cheque já não está na lista
		for ($i=1; $i<=intval($post['total_cheques']); $i++) {
			if ($post["idcheque_$i"] == $post['idcheque']) {
				$err[] = "O cheque com Código igual a " . $post['idcheque'] . " já está na lista! Digite outro Código de cheque!";
				break;
			}
		}


		// se houveram erros, mostra-os
    if(count($err) != 0) {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
    }
    // se nao houveram erros, insere na tabela html
		else {
			
			// incremente 1 na quantidade de total_cheques
			$total_cheques = intval($post['total_cheques']) + 1;
			$objResponse->addAssign("total_cheques", "value", $total_cheques);

			// nome da tabela criada
      $nome_tabela = "tabela_cheque_" . $total_cheques;

			// se a conta a receber já foi baixada, não deixa excluir mais o cheque da lista
			$celula_delete = "";

			$info_conta_receber =	$conta_receber->getById($post['codigoContaReceber']);

			if ( ($post['baixa_conta_receber'] != "1") && ($info_conta_receber['status_conta'] != 'NE') ) {
				$celula_delete = "
						<tr>
							<td class='tb_bord_baixo' align='right'>Excluir este cheque da lista:</td>
							<td class='tb_bord_baixo'>
								<a href='javascript:;' onclick=" . '"' . "xajax_Deleta_Cheque_AJAX(" . "'" . $total_cheques . "'" . ");" . '"' . "><img src='../common/img/delete.gif'></a>
							</td>
						</tr>
				";
			}

      
      // tabela de cheques
			$tabela = utf8_encode("
						<tr>
							<td align='right' width='40%'><b>CÓDIGO DO CHEQUE:</b></td>
							<td><b>{$info_cheque['idcheque']}</b></td>
							<input type='hidden' name='idcheque_$total_cheques' id='idcheque_$total_cheques' value='{$info_cheque['idcheque']}'>
						</tr>
		
						<tr>
							<td align='right'>Banco:</td>
							<td>{$info_cheque['nome_banco']}</td>
						</tr>
			
						<tr>
							<td align='right'>Agência:</td>
							<td>{$info_cheque['agencia']}-{$info_cheque['agencia_dig']}</td>
						</tr>
						
						<tr>
							<td align='right'>Conta:</td>
							<td>{$info_cheque['conta']}-{$info_cheque['conta_dig']}</td>
						</tr>
						
						<tr>
							<td align='right'>Nº do cheque:</td>
							<td>{$info_cheque['numero_cheque']}</td>
						</tr>
						
						<tr>
							<td align='right'>Data do cheque:</td>
							<td>{$info_cheque['data_cheque']}</td>
						</tr>
						
						<tr>
							<td align='right'>Data (Bom para):</td>
							<td>{$info_cheque['bom_para']}</td>
						</tr>
						
						<tr>
							<td align='right'>Titular da conta:</td>
							<td>{$info_cheque['titular_conta']}</td>
						</tr>
						
						<tr>
							<td align='right'>Valor do cheque (R$):</td>
							<td>{$info_cheque['valor']}</td>
						</tr>
						
						<tr>
							<td align='right'>Observação:</td>
							<td>{$info_cheque['observacao']}</td>
						</tr>

						$celula_delete
					");

			$objResponse->addCreate("div_cheques", "table", "$nome_tabela");
			$objResponse->addAssign("$nome_tabela", "innerHTML", "$tabela");
			$objResponse->addAssign("$nome_tabela", 'width', "100%");
			$objResponse->addAssign("$nome_tabela", 'cellpadding', "5");


			
			// limpa os campos inseridos
			$objResponse->addClear("idcheque", "value");

			
		}
		
		// retorna o resultado XML
    return $objResponse->getXML();
    
  }
  
  

	/*
	Função: Deleta_Deleta_Cheque_AJAX
	Deleta um cheque dinamicamente na tabela html
	*/
	function Deleta_Cheque_AJAX ($codigoTabela) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		// nome da tabela criada
    $nome_tabela = "tabela_cheque_" . $codigoTabela;

		// verifica se vai remover: Pula 1 linha se clicar no cancelar
		$objResponse->addConfirmCommands(1, utf8_encode("Deseja excluir este Cheque da lista ?"));

		// remove a tabela
		$objResponse->addRemove($nome_tabela);

		// retorna o resultado XML
    return $objResponse->getXML();

  }


	/*
	Função: Seleciona_Cheques_Conta_Pagar_AJAX
 	Seleciona os cheques da conta a pagar e coloca eles dinamicamente na tabela html
	*/
	function Seleciona_Cheques_Conta_Pagar_AJAX ($codigoContaPagar) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		$list_sql = "	SELECT
										CPCHQ.*
									FROM
		     						{$conf['db_name']}conta_pagar_cheque CPCHQ
									WHERE
										CPCHQ.idconta_pagar = $codigoContaPagar
									ORDER BY
										CPCHQ.idcheque ASC
								";

		//manda fazer a paginação
		$list_q = $db->query($list_sql);

		
		if($list_q){

			//busca os registros no banco de dados e monta o vetor de retorno
			$cont = 0;

			while($list = $db->fetch_array($list_q)){
				$list['total_cheques'] = $cont;

				// acrescenta o XML que foi retornado no objeto atual
				$objResponse->loadXML( Insere_Cheque_AJAX($list) );

        $cont++;
			} // fim do while

		}

		// retorna o resultado XML
    return $objResponse->getXML();

	}



	/*
	Função: Seleciona_Cheques_Conta_Receber_AJAX
 	Seleciona os cheques da conta a receber e coloca eles dinamicamente na tabela html
	*/
	function Seleciona_Cheques_Conta_Receber_AJAX ($codigoContaReceber, $baixa_conta) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		$list_sql = "	SELECT
										CRCHQ.*
									FROM
		     						{$conf['db_name']}conta_receber_cheque CRCHQ
									WHERE
										CRCHQ.idconta_receber = $codigoContaReceber
									ORDER BY
										CRCHQ.idcheque ASC
								";

		//manda fazer a paginação
		$list_q = $db->query($list_sql);

		
		if($list_q){

			//busca os registros no banco de dados e monta o vetor de retorno
			$cont = 0;

			while($list = $db->fetch_array($list_q)){
				$list['total_cheques'] = $cont;
				$list['baixa_conta_receber'] = $baixa_conta;
				$list['codigoContaReceber'] = $codigoContaReceber; 


				// acrescenta o XML que foi retornado no objeto atual
				$objResponse->loadXML( Insere_Cheque_AJAX($list) );

        $cont++;
			} // fim do while

		}

		// retorna o resultado XML
    return $objResponse->getXML();

	}


  


?>
