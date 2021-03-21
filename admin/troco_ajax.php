<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");

	require_once("../entidades/troco.php");
	require_once("../entidades/funcionario.php");

  // inicializa templating
  $smarty = new Smarty;

  // ação selecionada
  $flags['action'] = $_GET['ac'];

  // inicializa autenticação
  $auth = new auth();

	//inicializa classe
	$troco = new troco();
	$funcionario = new funcionario();


  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();
        

	/*
	Fun??o: Verifica_Campos_Troco_AJAX
	Verifica se os campos do troco foram preenchidos
	*/
	function Verifica_Campos_Troco_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $funcionario;
		//---------------------

		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
		
		if ($_GET['ac'] == "editar") {
			$form->chk_empty($post['numvalor_troco'], 1, 'Valor do troco (R$)');
			$form->chk_empty($post['idfuncionario'], 1, 'Funcionário');

		}
		else {
			$form->chk_empty($post['valor_troco'], 1, 'Valor do troco (R$)');
			$form->chk_empty($post['idfuncionario'], 1, 'Funcionário');

		}

		$err = $form->err;

		// verifica se a senha do funcionario está correta
		$info_funcionario = $funcionario->getById($post['idfuncionario']);
		if ($info_funcionario['senha_funcionario'] != md5($post['senha_funcionario'])) $err[] = "A senha digitada está incorreta !";



		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {
    	
    	$objResponse->addScript("document.getElementById('for_troco').submit();");

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
	Fun??o: Verifica_Campos_Busca_Rapida_AJAX
	Verifica se os campos da busca rápida do troco
	*/
	function Verifica_Campos_Busca_Rapida_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $troco;
		//---------------------

		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
		
		$form->chk_IsDate($post['data_troco'], "Data do troco");

		$err = $form->err;


		// se nao houveram erros, exibe os dados do troco
    if(count($err) == 0) {
    	
			$dia = $form->FormataDataParaInserir($post['data_troco']);
	
			//busca detalhes do troco
			$info = $troco->RecuperaTroco($dia, $post['idfilial']);


			// se o troco não existir, mostra msg de erro
			if ($info['idtroco'] == "") {
	
				$tabela = "
					<table width='100%'  border='0' cellpadding='2' cellspacing='3' bgcolor='#FDF5E6' class='tb4cantos'>
						<tr><td><b>O troco do dia " . $post['data_troco'] . " não foi cadastrado!</b></td></tr>
					</table>
				";	

			}
			// se o cheque existir, mostra os dados
			else {
	
				// mostra o link de editar troco, apenas se for o dia atual
				if ($dia == date('Y-m-d')) {
					$mostrar_link = "
							<tr>
								<td align='center' colspan='2'>&bull;<a class='link_geral' href='{$conf['addr']}/admin/troco.php?ac=editar&idtroco={$info['idtroco']}'>Editar dados do Troco</a></td>
							</tr> ";
				}
				else $mostrar_link = "";

				$tabela = "
					<table border='0' width='100%' align='center'>
		
						<tr>
							<td align='right'>Dia:</td>
							<td>{$info['dia']}</td>
						</tr>
			
						<tr>
							<td align='right'>Valor do troco (R$):</td>
							<td>{$info['valor_troco']}</td>
						</tr>
						
						<tr>
							<td align='right'>Funcionário responsável:</td>
							<td>{$info['nome_funcionario']}</td>
						</tr>

						$mostrar_link

					</table>
				";

			}

			$tabela = utf8_encode(html_entity_decode($tabela));

			$objResponse->addAssign("dados_troco", "innerHTML", "$tabela");


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
