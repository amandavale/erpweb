<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/ordem_servico.php");
  require_once("../entidades/produto.php");
  require_once("../entidades/status_os_programacao.php");
  require_once("../entidades/programacao_status.php");
  
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



  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();

  $produto = new produto();
  
  /* Exemplo de função XAJAX
   
  
	function <NomeDaFuncao>_AJAX ($post) {
	
		// variáveis globais
		global $form, $conf, $db, $falha;
		//---------------------
	
		// cria o objeto xajaxResponse
		$objResponse = new xajaxResponse();
	
		// retorna o resultado XML
		return $objResponse->getXML();
	
	}


   */
  
  
  
  
  
  function Verifica_Campos_OS_AJAX ($post) {
  
  	// variáveis globais
  	global $form, $conf, $db, $falha;
  	//---------------------
  
  	// cria o objeto xajaxResponse
  	$objResponse = new xajaxResponse();
  	
  	$prefix = (isset($_GET['ac']) && $_GET['ac'] == 'editar') ? 'num' : '';
  	
  	$form->chk_empty($post['descricao_ordem'], 1, 'Descrição');
  	$form->chk_empty($post['idtipo_servico'], 1, 'Tipo de Serviço');
  	$form->chk_empty($post['previsao_servico'], 1, 'Previsão');
  	$form->chk_empty($post[$prefix.'idcliente'], 1, 'Cliente');
  	$form->chk_empty($post[$prefix.'idsolicitante'], 1, 'Solicitante');
  	
  	
  	
  	$err = $form->err;
  	 
  	if(count($err)){
  		$objResponse->addAlert(utf8_encode(html_entity_decode(implode($err,"\n"))));
  	}else{
  		$objResponse->addScript("document.getElementById('for_ordem_servico').submit();");
  	}
  	
  	
  	// retorna o resultado XML
  	return $objResponse->getXML();
  
  }
  
  function Set_Campo_Status_AJAX($idstatus_os) {
  
  	// variáveis globais
  	global $form, $conf, $db, $falha;
  	
  
	$status_os_programacao = new status_os_programacao();
  	$objResponse = new xajaxResponse();
  
  	$prog_status = $status_os_programacao->getProgramacao($idstatus_os);

  	$objResponse->addClear('div_status_ordem_servico','innerHTML');
  	
  	if(count($prog_status) && !empty($idstatus_os)){
  		
  		$selectBox = '<select name="programacao_status" id="programacao_status" onchange="xajax_Set_Campo_Programacao_AJAX(this.value);" >';
  		foreach($prog_status as $status){
  			$selectBox .= '<option value="'.$status['idprogramacao_status'].'">'. htmlentities($status['nome_programacao']).'</option>'."\n";
  		}
  		$selectBox .= '</select> <div id="div_campo_complementar" style="display:inline;"></div>';

  		$objResponse->addAppend('div_status_ordem_servico', 'innerHTML', $selectBox);

  		$objResponse->addScript('xajax_Set_Campo_Programacao_AJAX('.$prog_status[0]['idprogramacao_status'].');');
  		
  	}
  	
  	// retorna o resultado XML
  	return $objResponse->getXML();
  
  }
  
  
  
  
  function Set_Campo_Programacao_AJAX($idprogramacao_status) {
  
  	// variáveis globais
  	global $form, $conf, $db, $falha, $parametros;
  
  	
  	$programacao_status = new programacao_status();
  	$objResponse = new xajaxResponse();
  	 
  
  	$ProgStatus = $programacao_status->getById($idprogramacao_status);
  
  	$objResponse->addClear('div_campo_complementar','innerHTML');
  	 
  	if($ProgStatus['campo_complementar'] == 'data_programacao'){
  
  		$campoComplementar = <<<HTML
		<input class="short" type="text" name="data_programacao" id="data_programacao" value="" maxlength='10' onkeydown="mask('data_programacao', 'data')" onkeyup="mask('data_programacao', 'data')" />
	    <img src="{$conf['addr']}/common/img/calendar.png" id="img_data_programacao" style="cursor: pointer;" />
HTML;

  		$objResponse->addAppend('div_campo_complementar', 'innerHTML', $campoComplementar);
  		
  		$objResponse->addScript('Calendar.setup(
									{
										inputField : "data_programacao", // ID of the input field
										ifFormat : "%d/%m/%Y", // the date format
										button : "img_data_programacao", // ID of the button
										align  : "cR"  // alinhamento
									}
								);
			  				');
  		
  	}
  	elseif($ProgStatus['campo_complementar'] == 'motivo_programacao'){
  		$objResponse->addAppend('div_campo_complementar', 'innerHTML', 'Motivo: <input type="text" name="motivo_programacao" id="motivo_programacao" class="long" />');
  	}

  	$objResponse->addScript("xajax_Set_Campo_Movmentacao_AJAX(xajax.getFormValues('for_ordem_servico'));");
  	
  	
  	// retorna o resultado XML
  	return $objResponse->getXML();
  
  }
  
  
  function Set_Campo_Movmentacao_AJAX ($post) {
  
  	// variáveis globais
  	global $form, $conf, $db, $falha,$parametros, $status_os_programacao, $parametros;
  	
  	$status_os_programacao = new status_os_programacao();
  	$programacao_status = new programacao_status();
  	//---------------------
  
  	// cria o objeto xajaxResponse
  	$objResponse = new xajaxResponse();
  	
  	$idstatus_os_programacao = $status_os_programacao->getIdStatusProg($post['status_ordem_servico'],$post['programacao_status']);
  	$idstatus_final_os = $parametros->getParam('idstatus_final_os');
  	
  	
  	if($idstatus_os_programacao == $idstatus_final_os){ 
	  	$checkbox = '<input type="checkbox" id="gerar_movimento" name="gerar_movimento" checked="checked" /> <span for="gerar_movimentacao">Gerar Movimentação Financeira</span>'; 
	  	$objResponse->addAppend('div_campo_complementar', 'innerHTML', utf8_encode($checkbox));
  	}
  	
  	
  	// retorna o resultado XML
  	return $objResponse->getXML();
  
  }
  
  
  
  
  
  	/*
	Função: Busca_Descricao_CFOP_AJAX
 	Busca a Descrição do CFOP
	*/
	function Insere_Material_OS_AJAX ($post) {

		// variáveis globais
		global $form, $conf, $db, $falha, $produto;
		//---------------------

		// cria o objeto xajaxResponse
    	$objResponse = new xajaxResponse();

    	
    	//Validações dos campos
    	$form->chk_empty($post['idproduto'], 1, 'Material');
    	$form->chk_empty($post['qtd_produto'], 1, 'Quantidade');
    	$form->chk_empty($post['idfornecedor'], 1, 'Fornecedor');
    	$form->chk_empty($post['valor_unitario'], 1, 'Valor Unitário');
    	    		
    	$err = $form->err;
    	
    	if(count($err)){
    		$objResponse->addAlert(utf8_encode(html_entity_decode(implode($err,"\n"))));
    		return $objResponse->getXML();
    	}
    	    	
    	
    	$qtd_produto    = $form->FormataMoedaParaInserir($post['qtd_produto']);
    	$valor_unitario = $form->FormataMoedaParaInserir($post['valor_unitario']);
    	$valor_total    = $form->FormataMoedaParaExibir(($qtd_produto * $valor_unitario));
    	
    	$post['idproduto'] = (int)$post['idproduto'];
    	$dados_produto = $produto->getById($post['idproduto']);
    	
    		
    	if( isset($post['material'][$post['idproduto']])) {
	    	$objResponse->addAlert(utf8_encode("Este material já se encontra inserido"));
	    	return $objResponse->getXML();
    	}
    		
    	$linha = <<<HTML
					<tr id="linha_produto_{$post['idproduto']}">
						<input type="hidden" name="material[{$post['idproduto']}][qtd_produto]"    value="{$post['qtd_produto']}" />
						<input type="hidden" name="material[{$post['idproduto']}][valor_unitario]" value="{$post['valor_unitario']}" />
						<input type="hidden" name="material[{$post['idproduto']}][idfornecedor]"   value="{$post['idfornecedor']}" />
						<input type="hidden" name="material[{$post['idproduto']}][numero_nf]"   value="{$post['numero_nf']}" />
						
						<td>{$post['numero_nf']}</td>
						<td>{$post['idfornecedor_Nome']}</td>
						<td>{$post['idproduto_Nome']}</td>
						<td>{$dados_produto['sigla_unidade_venda']}</td>
						<td align="right">{$post['qtd_produto']}</td>
						<td align="right">{$post['valor_unitario']}</td>
						<td align="right">$valor_total</td>
						<td align="center"><img onclick="xajax_Remove_Material_OS_AJAX({$post['idproduto']});" style="cursor:pointer;cursor:hand;" src="{$conf['addr']}/common/img/delete.gif" /></td>
					</tr>
					  	
HTML;

    	
    	$objResponse->addAppend('tbl_materiais', 'innerHTML', $linha);
    	
    	$objResponse->addClear('idproduto_NomeTemp', 'value');
    	$objResponse->addClear('idproduto_Nome', 'value');
    	$objResponse->addClear('valor_unitario', 'value');
    	$objResponse->addClear('qtd_produto', 'value');
    	
    	$objResponse->addScript("VerificaMudancaCampo('idproduto')");
    	$objResponse->addAssign('idproduto_Flag', 'className', 'nao_selecionou');
    	
    	$objResponse->addScript("xajax_Calcula_Total_OS_AJAX(xajax.getFormValues('for_ordem_servico'));");
    	
		// retorna o resultado XML
    	return $objResponse->getXML();

	}
	
	
	function Calcula_Total_OS_AJAX($post){
		
		// variáveis globais
		global $form, $conf, $db, $falha, $produto;
		//---------------------
		
		// cria o objeto xajaxResponse
		$objResponse = new xajaxResponse();
		
		
		$valor_total_os = 0.00;
		
		foreach($post['material'] as $idproduto => $produto){
			
			$qtd_produto    = $form->FormataMoedaParaInserir($produto['qtd_produto']);
			$valor_unitario = $form->FormataMoedaParaInserir($produto['valor_unitario']);
			
			$valor_total_os += ($qtd_produto * $valor_unitario);
			
		}
		
		
		$objResponse->addAssign('valor_total_os', 'innerHTML', number_format($valor_total_os,2,',','.'));
		
		// retorna o resultado XML
		return $objResponse->getXML();
	}

	
	/*
	 Função: Remove_Material_OS_AJAX
	
	*/
	function Remove_Material_OS_AJAX ($idproduto) {
	
		// variáveis globais
		global $form, $conf, $db, $falha;
		//---------------------
	
		// cria o objeto xajaxResponse
		$objResponse = new xajaxResponse();
	
		$objResponse->addRemove('linha_produto_'.$idproduto);		

		$objResponse->addScript("xajax_Calcula_Total_OS_AJAX(xajax.getFormValues('for_ordem_servico'));");
		
		// retorna o resultado XML
		return $objResponse->getXML();
	
	}


?>
