<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  

	require_once("../entidades/fornecedor.php");
	require_once("../entidades/produto.php");
	require_once("../entidades/endereco.php");

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

  $fornecedor = new fornecedor();
  $produto    = new produto();
  $endereco   = new endereco();
										

  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();
        
				

  switch($flags['action']) {


		// busca os fornecedores de acordo com a busca
		case "busca_fornecedor":			
			$fornecedor->Filtra_Fornecedor_AJAX($_GET['typing'], $_GET['campoID'], $_GET['mostraDetalhes'], $_GET['resumido'],$_GET['inserirEndereco']);
		break;
		

  }
  

  	
  // seta erros
  $smarty->assign("err", $err);

  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);
  
  function Cadastro_Rapido_Fornecedor_AJAX($post){
  	
  	// variáveis globais
  	global $form, $conf, $db, $falha, $err, $fornecedor, $endereco;
  	//---------------------
  	
  	// cria o objeto xajaxResponse
  	$objResponse = new xajaxResponse();
  

  	$form->chk_empty($post['tipo_fornecedor'], 1, 'Tipo de Fornecedor');
  	$form->chk_empty($post['nome_fornecedor'], 1, 'Nome do Fornecedor');
  	
  	$err = $form->err;
  	
  	
  	// se nao houveram erros, da o submit no form
  	if(count($err) == 0) {

  		$post['nome_fornecedor']		= $db->escape(utf8_decode($post['nome_fornecedor']));
  		$post['fornecedor_logradouro']	= $db->escape(utf8_decode($post['fornecedor_logradouro']));
  		$post['fornecedor_complemento'] = $db->escape(utf8_decode($post['fornecedor_complemento']));
  			
	  	$post['telefone_fornecedor'] 	 = $form->FormataTelefoneParaInserir($post['telefone_fornecedor_ddd'], $post['telefone_fornecedor']);
		$post['fax_fornecedor'] 		 = $form->FormataTelefoneParaInserir($post['fax_fornecedor_ddd'], $post['fax_fornecedor']);
		$post['telefone_representante']  = $form->FormataTelefoneParaInserir($post['telefone_representante_ddd'], $post['telefone_representante']);
		$post['celular_representante']   = $form->FormataTelefoneParaInserir($post['celular_representante_ddd'], $post['celular_representante']);
	
	
		$post['idbairro'] = (int)$post['fornecedor_idbairro'];
		$post['idcidade'] = (int)$post['fornecedor_idcidade'];
		$post['idestado'] = (int)$post['fornecedor_idestado'];
		$post['idramo_atividade'] = (int)$post['idramo_atividade'];
		$post['idendereco_representante_fornecedor'] = 'NULL';
		
		// Grava o registro do endereço no Banco de Dados
		$post['idendereco_fornecedor'] = $endereco->InsereEndereco($post, "fornecedor");
	  		 
		//grava o registro no banco de dados
		$idfornecedor = $fornecedor->set($post);
		$err= $fornecedor->err;
		
		if(!count($err)){
			
			//Seta a mensagem de sucesso
			$objResponse->addAlert(utf8_encode("Fornecedor inserido com sucesso!"));
			
			//Limpa os campos do formulário
			$campos = array('nome_fornecedor','cpf_cnpj', 'nome_contato_fornecedor', 'telefone_fornecedor_ddd','telefone_fornecedor',
							'email_fornecedor', 'fornecedor_logradouro', 'fornecedor_numero', 'fornecedor_complemento', 'fornecedor_idcidade_Nome',
							'fornecedor_idestado_Nome', 'fornecedor_idbairro_Nome', 'fornecedor_cep');
			
			foreach($campos as $campo){ $objResponse->addClear($campo, 'value'); }
			
			//Limpa as flags dos campos de endereço que foram selecionados.
			$objResponse->addAssign('fornecedor_idestado_Flag', 'className', 'nao_selecionou');
			$objResponse->addAssign('fornecedor_idcidade_Flag', 'className', 'nao_selecionou');
			$objResponse->addAssign('fornecedor_idbairro_Flag', 'className', 'nao_selecionou');
			
			if(!empty($post['idendereco_fornecedor'])){
				
				$dados_endereco = $endereco->getById($post['idendereco_fornecedor']);
				$strEndereco    = $endereco->formataStringEndereco($dados_endereco);
				
				$objResponse->addAssign('endereco_fornecedor', 'value', utf8_encode($strEndereco));
				
			}
			
			
			//Seleciona o fornecedor recém-cadastrado
			$objResponse->addAssign('idfornecedor', 'value', $idfornecedor );
			$objResponse->addAssign('idfornecedor_NomeTemp', 'value', utf8_encode($post['nome_fornecedor']));
			$objResponse->addAssign('idfornecedor_Nome', 'value', utf8_encode($post['nome_fornecedor']));
			$objResponse->addAssign('idfornecedor_Flag', 'className', 'selecionou');
			
			//Fecha a Lightbox
			$objResponse->addScript('fecharLightbox(fornecedor_conteudo)');
		}
	
  	}
  	
  	// houve erros, logo mostra-os
  	if(count($err) > 0){
  		

  		$mensagem = implode("\n",$err);
  		$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
  	}
  	 
  	
  	// retorna o resultado XML
  	return $objResponse->getXML();
  	
  }
  
  	/*
	Função: Insere_Fornecedor_AJAX
	Insere um funcionario dinamicamente na tabela html
	*/
	function Insere_Fornecedor_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $fornecedor;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		// codigo do funcionario
		$codigoFornecedor = $post['idfornecedor'];

		$form->chk_empty($post['idfornecedor'], 1, 'Fornecedor');

		$err = $form->err;

		// se houveram erros, mostra-os
    if(count($err) != 0) {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
    }
    // se nao houveram erros, verifica se ele já nao existe na tabela
		else if (Verifica_Fornecedor_Existe_AJAX ($post) == false) {

			// incrementa 1 na quantidade de funcionarios
			$total_fornecedor = intval($post['total_fornecedores']) + 1;
			$objResponse->addAssign("total_fornecedores", "value", $total_fornecedor);

			// busca os dados do funcionário
			$info_fornecedor = $fornecedor->retornaFornecedor($codigoFornecedor);
			
			$info_fornecedor['telefone_fornecedor'] = $form->FormataTelefoneParaExibir($info_fornecedor['telefone_fornecedor']);

			if ($list['nome_cidade'] != ""){$list['nome_cidade'] = ($list['nome_cidade'])."/".($list['sigla_estado']);}

			// nome da tabela criada
      $nome_tabela = "tabela_fornecedor_" . $codigoFornecedor;

			// se for listar, nao poe a opção de excluir
			if ( ($_GET['ac'] == "listar") || ($_GET['ac'] == "") ) {

				// tabela de fornecedor
				$tabela = utf8_encode("
							<table width='100%' cellpadding='5' id='$nome_tabela'>
								<tr>
									<td class='tb_bord_baixo' align='left' width='35%'>
										<input type='hidden' name='codigo_fornecedor_$total_fornecedor' id='codigo_fornecedor_$total_fornecedor' value='$codigoFornecedor' />
	
										{$info_fornecedor['nome_fornecedor']}
									</td>
									<td class='tb_bord_baixo' align='center' width='10%'>&nbsp;{$info_fornecedor['nome_cidade']}</td>
									<td class='tb_bord_baixo' align='center' width='15%'>&nbsp;{$info_fornecedor['telefone_fornecedor']}</td>
								</tr>
							</table>
						");
			}
			else {

				// tabela de fornecedor
				$tabela = utf8_encode("
							<table width='100%' cellpadding='5' id='$nome_tabela'>
								<tr>
									<td class='tb_bord_baixo' align='left' width='35%'>
										<input type='hidden' name='codigo_fornecedor_$total_fornecedor' id='codigo_fornecedor_$total_fornecedor' value='$codigoFornecedor' />
	
										{$info_fornecedor['nome_fornecedor']}
									</td>
									<td class='tb_bord_baixo' align='center' width='10%'>&nbsp;{$info_fornecedor['nome_cidade']}</td>
									<td class='tb_bord_baixo' align='center' width='15%'>&nbsp;{$info_fornecedor['telefone_fornecedor']}</td>
									<td class='tb_bord_baixo' align='center' width='5%'>
										<a href='javascript:;' onclick=" . '"' . "xajax_Deleta_Fornecedor_AJAX(" . "'" . $codigoFornecedor . "'" . ");" . '"' . "><img src='../common/img/delete.gif'></a>
									</td>
								</tr>
							</table>
						");

			}

			// adiciona a tabela
			$objResponse->addAppend("div_fornecedor", "innerHTML", $tabela);
			
			$objResponse->addClear("idfornecedor", "value");
			$objResponse->addClear("idfornecedor_Nome", "value");
			$objResponse->addClear("idfornecedor_NomeTemp", "value");
			$objResponse->addAssign("idfornecedor_Flag", "className", "nao_selecionou");

		}
		else {
			$objResponse->addAlert(utf8_encode("Este Fornecedor já está na lista!"));
		}

		// retorna o resultado XML
    return $objResponse->getXML();

  }



	/*
	Função: Verifica_Fornecedor_Existe_AJAX
	Verifica se um fornecedor ja existe na tabela html
	*/
	function Verifica_Fornecedor_Existe_AJAX ($post) {

		for ($i=1; $i<=intval($post['total_fornecedores']); $i++) {
			if ( $post["codigo_fornecedor_$i"] == $post['idfornecedor'] ) return true;
		}

		return false;
  }



	/*
	Função: Deleta_Fornecedor_AJAX
	Deleta um fornecedor dinamicamente na tabela html
	*/
	function Deleta_Fornecedor_AJAX ($codigoFornecedor) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;

		global $fornecedor;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		// nome da tabela criada
    $nome_tabela = "tabela_fornecedor_" . $codigoFornecedor;

		// busca os dados do funcionário
		$info_fornecedor = $fornecedor->getById($codigoFornecedor);

		// verifica se vai remover: Pula 1 linha se clicar no cancelar
		$objResponse->addConfirmCommands(1, utf8_encode("Deseja excluir o fornecedor '{$info_fornecedor['nome_fornecedor']}' deste produto ?"));

		// remove a tabela
		$objResponse->addRemove($nome_tabela);

		// retorna o resultado XML
    return $objResponse->getXML();

  }


	/*
	Função: Seleciona_Fornecedor_AJAX
 	Seleciona os fornecedores do produto e colocam eles dinamicamente na tabela html
	*/
	function Seleciona_Fornecedor_AJAX ($codigoProduto) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;

		global $fornecedor;
		//---------------------
		
		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

			$list_sql = "	SELECT
											FORN.*,EDR.*,EST.*,CID.*
										FROM
           						{$conf['db_name']}fornecedor FORN
           						INNER JOIN {$conf['db_name']}endereco EDR	ON FORN.idendereco_fornecedor = EDR.idendereco
								 			LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade = CID.idcidade
								 			LEFT OUTER JOIN {$conf['db_name']}estado EST ON CID.idestado = EST.idestado
								 			INNER JOIN {$conf['db_name']}produto_fornecedor PRDFOR ON PRDFOR.idfornecedor = FORN.idfornecedor
											INNER JOIN {$conf['db_name']}produto PRD ON PRD.idproduto = PRDFOR.idproduto
										WHERE
											PRD.idproduto = $codigoProduto
										ORDER BY
											FORN.nome_fornecedor ASC
											";


		//manda fazer a paginação
		$list_q = $db->query($list_sql);

		
		if($list_q){

			//busca os registros no banco de dados e monta o vetor de retorno
			$cont = 0;

			while($list = $db->fetch_array($list_q)){
    		$post['idfornecedor'] = $list['idfornecedor'];
				$post['total_fornecedores'] = $cont;
				
				// acrescenta o XML que foi retornado no objeto atual
				$objResponse->loadXML( Insere_Fornecedor_AJAX($post) );

        $cont++;
			} // fim do while

		}

		// retorna o resultado XML
    return $objResponse->getXML();

	}
	
		/*
	Fun??o: Verifica_Campos_Fornecedor_AJAX
	Verifica se os campos do produto foram preenchidos
	*/
	function Verifica_Campos_Fornecedor_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $produto;
		//---------------------

    $existe_produto = 0;

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		if ($_GET['ac'] == "editar") {
					$form->chk_empty($post['littipo_fornecedor'], 1, 'Tipo de Fornecedor');
					$form->chk_empty($post['litnome_fornecedor'], 1, 'Nome do Fornecedor');
					$form->chk_empty($post['numidramo_atividade'], 1, 'Ramo de Atividade');
	
          $err = $form->err;


		}
		else {
					$form->chk_empty($post['tipo_fornecedor'], 1, 'Tipo de Fornecedor');
					$form->chk_empty($post['nome_fornecedor'], 1, 'Nome do Fornecedor');
					$form->chk_empty($post['idramo_atividade'], 1, 'Ramo de Atividade');
					
          $err = $form->err;

		}


		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {

      	$objResponse->addScript("document.getElementById('for').submit();");

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
