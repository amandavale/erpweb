<?php

  //inclusão de bibliotecas
    require_once("../common/lib/conf.inc.php");
    require_once("../common/lib/db.inc.php");
    require_once("../common/lib/auth.inc.php");
    require_once("../common/lib/form.inc.php");
    require_once("../common/lib/rotinas.inc.php");
    require_once("../common/lib/Smarty/Smarty.class.php");
    require_once("../common/lib/xajax/xajax.inc.php");

    require_once("../entidades/orcamento.php");
    require_once("../entidades/orcamento_produto.php");
    require_once("../entidades/motivo_cancelamento.php");
    require_once("../entidades/funcionario.php");
    require_once("../entidades/cliente.php");
    require_once("../entidades/filial.php"); 
    require_once("../entidades/cfop.php"); 
    require_once("../entidades/parametro.php");
    require_once("../entidades/endereco.php");
    require_once("../entidades/transportador.php");
    require_once("../entidades/produto.php");
    require_once("../entidades/conta_receber.php");
    require_once("../entidades/produto_filial.php");
    require_once("../entidades/modo_recebimento.php");
    require_once("../entidades/movimento.php");


    require_once("orcamento_ajax.php");
    require_once("cfop_ajax.php");


  // inicializa autenticação
  $auth = new auth();

  // ação selecionada
  $flags['action'] = $_GET['ac'];

	// se mandou emitir uma NF, busca qual tipo sera emitido do post
	if (isset($_POST['chk_emitir_nf'])) $_GET['tipo'] = $_POST['emitir_tipo_orcamento'];
	else if ($_POST['emitir_tipo_orcamento_redireciona'] != "") $_GET['tipo'] = $_POST['emitir_tipo_orcamento_redireciona'];
	
  // configurações anotionais
	if ( ($_GET['tipo'] != "") && ($_SESSION['tipo_orcamento'] != $_GET['tipo']) ) $_SESSION['tipo_orcamento'] = $_GET['tipo'];

  if ($_SESSION['tipo_orcamento'] == "O") {
		$conf['area'] = "Orçamento"; // área
   	if ($flags['action'] == "") $flags['action'] = "listar";
	}
  else if ($_SESSION['tipo_orcamento'] == "ECF") {
		$conf['area'] = "Cupom Fiscal"; // área
		if ($flags['action'] == "") $flags['action'] = "listarNF";
	}
  else if ($_SESSION['tipo_orcamento'] == "NF") {
		$conf['area'] = "Nota Fiscal"; // área
		if ($flags['action'] == "") $flags['action'] = "listarNF";
	}
  else if ($_SESSION['tipo_orcamento'] == "SD") {
		$conf['area'] = "Série D"; // área
		if ($flags['action'] == "") $flags['action'] = "listarNF";
	}



  //configuração de estilo
  $conf['style'] = "full";

  // inicializa templating
  $smarty = new Smarty;

  // configura diretórios
  $smarty->template_dir = "../common/tpl";
  $smarty->compile_dir =   "../common/tpl_c";

  // seta configurações
  $smarty->assign("conf", $conf);


  	// cria o objeto xajax
	$xajax = new xajax();

	// registra todas as funções que serão usadas
	$xajax->registerFunction("Insere_Produto_Encartelamento_AJAX");
	$xajax->registerFunction("Calcula_Total_AJAX");
	$xajax->registerFunction("Deleta_Produto_Encartelamento_AJAX");
	$xajax->registerFunction("Verifica_Campos_Orcamento_AJAX");
	$xajax->registerFunction("Verifica_Campos_Busca_Rapida_AJAX");
	$xajax->registerFunction("Seleciona_Produtos_AJAX");
	$xajax->registerFunction("Verifica_Cancelamento_Orcamento_AJAX");
	$xajax->registerFunction("ReImpressao_Fiscal_AJAX");
	$xajax->registerFunction("Calcula_Valor_Financiado_AJAX");
	$xajax->registerFunction("Insere_Conta_Receber_A_Vista_AJAX");
	$xajax->registerFunction("Insere_Conta_Receber_A_Prazo_AJAX");
	$xajax->registerFunction("Insere_Referencia_AJAX");
	$xajax->registerFunction("Seleciona_Contas_Receber_AJAX");
	$xajax->registerFunction("Gerar_Emissao_Fiscal_AJAX");
	$xajax->registerFunction("Busca_Descricao_CFOP_AJAX");
	$xajax->registerFunction("Deleta_Modo_Recebimento_A_Vista_AJAX");
	$xajax->registerFunction("Verifica_Campos_Busca_Rapida_Nota_AJAX");
	$xajax->registerFunction("Verifica_Serie_ECF_AJAX");
	$xajax->registerFunction("Define_Status_Impressao_ECF_AJAX"); 
	$xajax->registerFunction("Inicia_Processo_TEF_AJAX");
  


	// processa as funções
	$xajax->processRequests();



  // verifica requisição de logout
  if($flags['action'] == "logout") {
    $auth->logout();
  }
  else {

    // inicializa vetor de erros
    $err = array();

    // verifica sessão
    if(!$auth->check_user()) {
      // verifica requisição de login
      if($_POST['usr_chk']) {
        // verifica login
        if(!$auth->login($_POST['usr_log'], $_POST['usr_sen'])) {
          $err = $auth->err;
        }
      }
      else {
        $err = $auth->err;
      }
    }

    // conteúdo
    if($auth->check_user()) {
      // verifica privilégios
      if(!$auth->check_priv($conf['priv'])) {
        $err = $auth->err;
      }
      else {
        // libera conteúdo
        $flags['okay'] = 1;

		//inicializa classe
  		$orcamento = new orcamento();
	  		
  		$orcamento_produto = new orcamento_produto();
  		$motivo_cancelamento = new motivo_cancelamento();
		$funcionario = new funcionario();
		$cliente = new cliente();
		$filial = new filial(); 
		$cfop = new cfop();
		$parametro = new parametro();
		$endereco = new endereco();
		$transportador = new transportador();
		$produto = new produto();
		$conta_receber = new conta_receber();
		$produto_filial = new produto_filial();
		$modo_recebimento = new modo_recebimento();
		$movimento = new movimento();

        // inicializa banco de dados
   	    $db = new db();

        //incializa classe para validação de formulário
        $form = new form();
        
        $list = $auth->check_priv($conf['priv']);
		$aux = $flags['action'];
		if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}


        switch($flags['action']) {
        	
        	// ação: adicionar <<<<<<<<<<
          	case "adicionar":

				// busca o dia e hora atual
				$flags['data_criacao'] = date('d/m/Y H:i:s');
				
				// busca os dados da filial
				$info_filial = $filial->getById($_SESSION['idfilial_usuario']);
				$smarty->assign("info_filial", $info_filial);
				
				//busca os funcionarios da filial
				$list_funcionarios = $funcionario->Seleciona_Funcionarios_Da_Filial($_SESSION['idfilial_usuario'], "V");
				$smarty->assign("list_funcionarios",$list_funcionarios);
				
				// busca os modos de recebimento a vista
				//$list_modo_recebimento_a_vista = $modo_recebimento->make_list_select(" WHERE a_vista = '1' ");
				//$smarty->assign("list_modo_recebimento_a_vista",$list_modo_recebimento_a_vista);
				
				// busca os modos de recebimento a prazo
				//$list_modo_recebimento_a_prazo = $modo_recebimento->make_list_select(" WHERE a_prazo = '1' ");
				//$smarty->assign("list_modo_recebimento_a_prazo",$list_modo_recebimento_a_prazo);


	            if($_POST['for_chk']) {
	            	
					//$form->chk_empty($_POST['idfuncionario'], 1, 'Funcionário Responsável');

              		$err = $form->err;

					if(count($err) == 0) {
						
						$_POST['idfuncionario'] = $_SESSION['usr_cod']; 
              	
		            	$_POST['idfilial'] = $_SESSION['idfilial_usuario'];
		            	$_POST['idmotivo_cancelamento'] = "NULL";
		            	$_POST['idUltfuncionario'] = $_POST['idfuncionario'];
		            	$_POST['idfuncionarioNF'] = "NULL";
	
						if ($_POST['idcliente'] == "") $_POST['idcliente'] = "NULL";
	
						$_POST['idcfop'] = "NULL";
						$_POST['idtransportador'] = "NULL";
						$_POST['numeroNota'] = "NULL";
						$_POST['datahoraCriacao'] = date('Y-m-d H:i:s');
						$_POST['datahoraUltEmissao'] = date('Y-m-d H:i:s');
						$_POST['tipoOrcamento'] = "O";
						
						if ($_POST['validade'] == "") $_POST['validade'] = "NULL";
	
						$_POST['infoClienteProv'] = nl2br($_POST['infoClienteProv']);
	
						$_POST['desconto'] = str_replace(",",".",$_POST['desconto']); 
	      				if ($_POST['desconto'] == "") $_POST['desconto'] = "NULL";
	
						$_POST['frete'] = $form->FormataMoedaParaInserir($_POST['frete']); 
	            		if ($_POST['frete'] == "") $_POST['frete'] = "NULL";
	
						$_POST['outras_despesas'] = $form->FormataMoedaParaInserir($_POST['outras_despesas']); 
	      				if ($_POST['outras_despesas'] == "") $_POST['outras_despesas'] = "NULL";
	
						//$_POST['jurosParcelamento'] = $form->FormataMoedaParaInserir($_POST['juros_parcelamento']); 
	      				//if ($_POST['jurosParcelamento'] == "") $_POST['jurosParcelamento'] = "NULL";
	      				$_POST['jurosParcelamento'] = "NULL";
	
						$_POST['numero_parcelas_cr'] = $_POST['quantidade_de_parcelas'];
						if ($_POST['numero_parcelas_cr'] == "") $_POST['numero_parcelas_cr'] = "NULL";
	
						$_POST['dias_entre_parcelas_cr'] = $_POST['dias_entre_parcelas'];
						if ($_POST['dias_entre_parcelas_cr'] == "") $_POST['dias_entre_parcelas_cr'] = "NULL";
	
						$_POST['data_parcela_1_cr'] = $_POST['data_parcela1'];
						$_POST['data_parcela_1_cr'] = $form->FormataDataParaInserir($_POST['data_parcela_1_cr']);
						
						//$_POST['sigla_modo_recebimento_prazo_cr'] = $_POST['modo_recebimento_a_prazo'];
						$_POST['sigla_modo_recebimento_prazo_cr'] = "NULL";
	
	
						$_POST['valor_seguro'] = "NULL";
						$_POST['valor_total_ipi'] = "NULL";
						$_POST['base_calculo_icms'] = "NULL";
						$_POST['valor_icms'] = "NULL";
						$_POST['base_calc_icms_sub'] = "NULL";
						$_POST['valor_icms_sub'] = "NULL";
	
	
						//grava o registro no banco de dados
						$idorcamento = $orcamento->set($_POST);
						$flags['idorcamento'] = $idorcamento;
	
						//grava os itens do orcamento
						$info_item['idorcamento'] = $idorcamento;
						
						for ($i=1; $i<=$_POST['total_produtos']; $i++) {
							
							if (isset($_POST["idproduto_".$i])) {
								
								if ($_POST["desconto_produto_".$i] == "") $_POST["desconto_produto_".$i] = "0,00";
	
								$info_item['idproduto'] = $_POST["idproduto_".$i];
								$info_item['preco_unitario_produto'] = str_replace(",",".",$_POST["preco_unitario_produto_".$i]);
								$info_item['qtd_produto'] = str_replace(",",".",$_POST["qtd_produto_".$i]);
								$info_item['desconto_produto'] = str_replace(",",".",$_POST["desconto_produto_".$i]);
								$info_item['aliquota_icms_produto'] = "NULL";
								$info_item['cst_produto'] = '';
	
								$orcamento_produto->set($info_item);
								
							}
							
						}
	
	
					/**
						// grava as contas a receber
						// Como eh um orçamento, grava todas as contas a receber pendentes
						$_GET['idorcamento'] = $idorcamento;
			            //$conta_receber->Grava_Simulacao_Contas_Receber();
			         */
						
						//obtém os erros que ocorreram no cadastro
						$err = $orcamento->err;

						/// grava movimentações para as parcelas
						if(!$movimento->geraMovimentoOrcamento($idorcamento, $_POST)){
							$err[] = 'Houve um erro ao gerar a movimentação.';
						}

						//se não ocorreram erros
						if(count($err) == 0) {
							
							$chk_imprimir_orcamento = $_POST['chk_imprimir_orcamento'];

							// redireciona para a Nota fiscal
							if (isset($_POST['chk_emitir_nf'])) {

								// redireciona a página para evitar o problema do reload	
								$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=editarNF&sucesso=inserir&idorcamento=$idorcamento&chk_imprimir_orcamento=$chk_imprimir_orcamento'>"; 
								echo $redirecionar; 
								exit;

							}
							// redireciona para a Listagem de Orçamentos
							else {

								// redireciona a página para evitar o problema do reload	
								$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=inserir&idorcamento=$idorcamento&chk_imprimir_orcamento=$chk_imprimir_orcamento'>"; 
								echo $redirecionar; 
								exit;

							}
						}
					}
              
            	}
            	else {
            	
            		// busca o valor padrao para a validade do orçamento e o máximo de itens
					$list_parametro = $parametro->make_list(0, $conf['rppg']);
					if ( count ($list_parametro) > 0 ) {
						$_POST['validade'] = $list_parametro[0]['validadeOrcamento'];
						$_POST['maximoItensOrcamento'] = $list_parametro[0]['maximoItensOrcamento'];
						$_POST['descontoMaximoOrcamento'] = str_replace(",",".",$list_parametro[0]['descontoMaximoOrcamento']);
						$_POST['jurosPadraoParcelamento'] = number_format($list_parametro[0]['jurosPadraoParcelamento'],2,",","");
					}
					else {
						$_POST['validade'] = "";
						$_POST['maximoItensOrcamento'] = 0;
						$_POST['descontoMaximoOrcamento'] = 0;
						$_POST['jurosPadraoParcelamento'] = "0,00";
					}	
					$_POST['dias_entre_parcelas'] = "30";
					$_POST['quantidade_de_parcelas'] = "1";
					$_POST['data_parcela1'] = date("d/m/Y");
					//------------------------------------------------
				}
            

			break;


			//listagem dos registros
			case "listar":
		
				if (isset($_GET['sucesso'])) $flags['sucesso'] = $conf["{$_GET['sucesso']}"];

				if (isset($_GET['idorcamento'])) {
					$_POST['idorcamento'] = $_GET['idorcamento'];
					$flags['buscar_dados_orcamento'] = 1;												
				}

			break;

			//listagem dos registros
			case "listarNF":

				if (isset($_GET['sucesso'])) $flags['sucesso'] = $conf["{$_GET['sucesso']}"];

				if (isset($_GET['numero_nota'])) {
					$_POST['numero_nota'] = $_GET['numero_nota'];
					$flags['buscar_dados_nota'] = 1;		
				}

          	break;

          
			//ação: editar <<<<<<<<<<
			case "editar":

				// se for o estado inicial, busca os dados primeiro
				if ($_GET['inicio'] == 1) $_POST['for_chk'] = 0;

				if($_POST['for_chk']) {
					
					$info = $_POST;
							
					$info['idorcamento'] = intval($_GET['idorcamento']); 
					$flags['idorcamento'] = $info['idorcamento'];
							
					//$form->chk_empty($_POST['idUltfuncionario'], 1, 'Funcionário Responsável');

					$err = $form->err;

					if(count($err) == 0) {
						
						$_POST['idUltfuncionario'] = $_SESSION['usr_cod']; 

	              		$_POST['littipoPreco'] = $_POST['tipoPreco'];
						$_POST['numidUltfuncionario'] = $_POST['idUltfuncionario'];
						$_POST['litdatahoraUltEmissao'] = date('Y-m-d H:i:s');
						$_POST['litinfoClienteProv'] = nl2br($_POST['infoClienteProv']);
						$_POST['litnomeClienteProv'] = $_POST['nomeClienteProv'];
						$_POST['numvalor_total_nota'] = $_POST['valor_total_nota'];
						$_POST['numvalor_total_produtos'] = $_POST['valor_total_produtos'];
						
						$_POST['numvalidade'] = $_POST['validade'];
						if ($_POST['numvalidade'] == "") $_POST['numvalidade'] = "NULL";
						
						$_POST['numdesconto'] = str_replace(",",".",$_POST['desconto']);
						if ($_POST['numdesconto'] == "") $_POST['numdesconto'] = "NULL";

						$_POST['numfrete'] = str_replace(",",".",$_POST['frete']);
						if ($_POST['numfrete'] == "") $_POST['numfrete'] = "NULL";

						$_POST['numoutras_despesas'] = str_replace(",",".",$_POST['outras_despesas']);
						if ($_POST['numoutras_despesas'] == "") $_POST['numoutras_despesas'] = "NULL";

						$_POST['numjurosParcelamento'] = $form->FormataMoedaParaInserir($_POST['juros_parcelamento']); 
           				if ($_POST['numjurosParcelamento'] == "") $_POST['numjurosParcelamento'] = "NULL";

						$_POST['numnumero_parcelas_cr'] = $_POST['quantidade_de_parcelas'];
						if ($_POST['numnumero_parcelas_cr'] == "") $_POST['numnumero_parcelas_cr'] = "NULL";

						$_POST['litdias_entre_parcelas_cr'] = $_POST['dias_entre_parcelas'];
						if ($_POST['litdias_entre_parcelas_cr'] == "") $_POST['litdias_entre_parcelas_cr'] = "NULL";

						$_POST['litdata_parcela_1_cr'] = $_POST['data_parcela1'];
						$_POST['litdata_parcela_1_cr'] = $form->FormataDataParaInserir($_POST['litdata_parcela_1_cr']);
								
						$_POST['litsigla_modo_recebimento_prazo_cr'] = $_POST['modo_recebimento_a_prazo'];

						$_POST['numidcliente'] = $_POST['idcliente'];
						if ($_POST['numidcliente'] == "") $_POST['numidcliente'] = "NULL";
								
						$_POST['numidmotivo_cancelamento'] = $_POST['idmotivo_cancelamento'];
						if ($_POST['numidmotivo_cancelamento'] == "") $_POST['numidmotivo_cancelamento'] = "NULL";

						$orcamento->update($info['idorcamento'], $_POST);

						// deleta os itens do orçamento
						$orcamento->Deleta_Itens_Orcamento($info['idorcamento']);

						//grava os itens do orcamento
						$info_item['idorcamento'] = $info['idorcamento'];

						for ($i=1; $i<=$_POST['total_produtos']; $i++) {

							if (isset($_POST["idproduto_".$i])) {

								if ($_POST["desconto_produto_".$i] == "") $_POST["desconto_produto_".$i] = "0,00";

								$info_item['idproduto'] = $_POST["idproduto_".$i];
								$info_item['preco_unitario_produto'] = str_replace(",",".",$_POST["preco_unitario_produto_".$i]);
								$info_item['qtd_produto'] = str_replace(",",".",$_POST["qtd_produto_".$i]);
								$info_item['desconto_produto'] = str_replace(",",".",$_POST["desconto_produto_".$i]);
								$info_item['aliquota_icms_produto'] = "NULL";
								$info_item['cst_produto'] = '';

								$orcamento_produto->set($info_item);
							}
						}


						if($info['idorcamento'] > 0){
							// apaga movimentos associados ao orçamento
							$movimento->apagaMovimentosOrcamento($info['idorcamento']);
						}
						
						// apenas se não solicitou o cancelamento do orçamento é que grava-se as contas a receber
						if ($_POST['numidmotivo_cancelamento'] == "NULL") {
							$movimento->geraMovimentoOrcamento($info['idorcamento'],$_POST);
						}
			

						//obtém erros
						$err = $orcamento->err;

						//se não ocorreram erros
						if(count($err) == 0) {
							
							$idorcamento = $_GET['idorcamento'];

							// redireciona para a Nota fiscal
							if (isset($_POST['chk_emitir_nf'])) {

								// redireciona a página para evitar o problema do reload	
								$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=editarNF&sucesso=inserir&idorcamento=$idorcamento'>"; 
								echo $redirecionar; 
								exit;
							}
							else {

								$chk_imprimir_orcamento = $_POST['chk_imprimir_orcamento'];

								// redireciona a página para evitar o problema do reload	
								$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=alterar&idorcamento=$idorcamento&chk_imprimir_orcamento=$chk_imprimir_orcamento'>"; 
								echo $redirecionar; 
								exit;
							}
						}
					}
				}
				else {

					//busca detalhes
					$info = $orcamento->getById($_GET['idorcamento']);

					// tratamento das informações para fazer o UPDATE
					$info['numidUltfuncionario'] = $info['idUltfuncionario']; 
					$info['numidcliente'] = $info['idcliente'];
					$info['littipoPreco'] = $info['tipoPreco'];
					$info['numvalidade'] = $info['validade'];
					$info['numdesconto'] = $info['desconto'];
					$info['numfrete'] = $info['frete']; 
					$info['numoutras_despesas'] = $info['outras_despesas'];

					if ($_GET['imprimir'] == 1) {
						if ($info['numdesconto'] == "") $info['numdesconto'] = "0,00";
						if ($info['numfrete'] == "") $info['numfrete'] = "0,00";
						if ($info['numoutras_despesas'] == "") $info['numoutras_despesas'] = "0,00";
					}

					$info['juros_parcelamento'] = $form->FormataMoedaParaExibir($info['jurosParcelamento']);
					$info['quantidade_de_parcelas'] = $info['numero_parcelas_cr'];
					$info['modo_recebimento_a_prazo'] = $info['sigla_modo_recebimento_prazo_cr'];
					$info['dias_entre_parcelas'] = $info['dias_entre_parcelas_cr'];
					$info['data_parcela1'] = $form->FormataDataParaExibir($info['data_parcela_1_cr']);

					$info['litnomeClienteProv'] = $info['nomeClienteProv'];
					$info['litinfoClienteProv'] = strip_tags($info['infoClienteProv']);
					$info['numidmotivo_cancelamento'] = $info['idmotivo_cancelamento'];

					// se selecionou o cliente, busca os dados dele
					if ($info['idcliente'] != "") $info_cliente = $cliente->BuscaDadosCliente($info['idcliente']);
					else $flags['nao_selecionou'] = 1;

					$smarty->assign("info_cliente", $info_cliente);
					//---------------------------------------

					//busca o nome do cliente
					$info['idcliente_Nome'] = $info_cliente['nome_cliente'];
					$info['idcliente_NomeTemp'] = $info_cliente['nome_cliente'];
							
					//obtém os erros
					$err = $orcamento->err;
							
					// busca os dados da filial
					$info_filial = $filial->getById($info['idfilial']);
					$info_filial['telefone_filial'] = $form->FormataTelefoneParaExibir($info_filial['telefone_filial']);
					$info_filial['fax_filial'] = $form->FormataTelefoneParaExibir($info_filial['fax_filial']);
					$smarty->assign("info_filial", $info_filial);

					// busca o endereço da filial
					$info_endereco_filial = $endereco->getById($info_filial['idendereco_filial']);
					$smarty->assign("info_endereco_filial", $info_endereco_filial);

					// busca os dados do funcionario
					$info_funcionario_criou = $funcionario->getById($info['idfuncionario']);
					$smarty->assign("info_funcionario_criou", $info_funcionario_criou);

					$info_funcionario_alterou = $funcionario->getById($info['idUltfuncionario']);
					$smarty->assign("info_funcionario_alterou", $info_funcionario_alterou);

					// busca o motivo do cancelamento, caso tenha sido cancelado
					$info_motivo_cancelamento = $motivo_cancelamento->getById($info['idmotivo_cancelamento']);
					$smarty->assign("info_motivo_cancelamento", $info_motivo_cancelamento);

					// busca os motivos de cancelamento
					$list_motivo_cancelamento = $motivo_cancelamento->make_list_select();
					$smarty->assign("list_motivo_cancelamento",$list_motivo_cancelamento);

					//busca os funcionarios da filial
					$list_funcionarios = $funcionario->Seleciona_Funcionarios_Da_Filial($_SESSION['idfilial_usuario'], "V");
					$smarty->assign("list_funcionarios",$list_funcionarios);

				}

				// busca os modos de recebimento a vista
				$list_modo_recebimento_a_vista = $modo_recebimento->make_list_select(" WHERE a_vista = '1' ");
				$smarty->assign("list_modo_recebimento_a_vista",$list_modo_recebimento_a_vista);

				// busca os modos de recebimento a prazo
				$list_modo_recebimento_a_prazo = $modo_recebimento->make_list_select(" WHERE a_prazo = '1' ");
				$smarty->assign("list_modo_recebimento_a_prazo",$list_modo_recebimento_a_prazo);

   				// busca o valor padrao para a validade do orçamento e o máximo de itens
				$list_parametro = $parametro->make_list(0, $conf['rppg']);
				if ( count ($list_parametro) > 0 ) {
					$_POST['maximoItensOrcamento'] = $list_parametro[0]['maximoItensOrcamento'];
					$_POST['descontoMaximoOrcamento'] = str_replace(",",".",$list_parametro[0]['descontoMaximoOrcamento']);
				}
				else {
					$_POST['maximoItensOrcamento'] = 0;
					$_POST['descontoMaximoOrcamento'] = 0;
				}
				//------------------------------------------------

				//passa os dados para o template
				$smarty->assign("info", $info);

			break;




          // ação: editarNF <<<<<<<<<<
					case "editarNF":
					
					  // se for o estado inicial, busca os dados primeiro
					  if ($_GET['inicio'] == 1) $_POST['for_chk'] = 0;
					
           

						if($_POST['for_chk']) {


							$info = $_POST;

              
							$err = $form->err;

			          		if(count($err) == 0) {

			                if($_POST['semestoque'] == 1)$_POST['litimpressao_ecf_em_andamento'] = "0";
			                else unset($_POST["numnumeroNota"]);

								// Cancelar a Emissão fiscal
								if ($_POST['cancelar_emissao_fiscal'] == "1") {
									// Se for cancelar a nota, deleta-se as contas a receber e volta o estoque ao normal
									$_POST['numidUltfuncionario'] = $_POST['idUltfuncionario'];
									$_POST['litdatahoraUltEmissao'] = date('Y-m-d H:i:s');

									$_POST['numidmotivo_cancelamento'] = $_POST['idmotivo_cancelamento'];
									if ($_POST['numidmotivo_cancelamento'] == "") $_POST['numidmotivo_cancelamento'] = "NULL";

									// cancela a emissão fiscal
									$orcamento->update($_GET['idorcamento'], $_POST);

									// deleta todas as contas a receber geradas anteriormente
									$conta_receber->Deleta_Contas_Receber($_GET['idorcamento']);
		
                 				 //verifica se este cancelamento é provido de uma queda de energia da ECF;
                  				  if($_POST['semestoque'] == 0){
									// volta o estoque ao normal
									for ($i=1; $i<=$_POST['total_produtos']; $i++) {
										if (isset($_POST["idproduto_".$i])) {
											// faz a Alta no estoque deste produto
											$idproduto = $_POST["idproduto_".$i];
											$qtd_produto = str_replace(",",".",$_POST["qtd_produto_".$i]);

											$produto_filial->Dar_Alta_Estoque($_SESSION['idfilial_usuario'], $idproduto, $qtd_produto);
										} // fim do if
									} // fim do for
                 				  }
								}
								// Atualização normal
								else if (! isset ($_GET['nao_atualizar'])) {
       
									$_POST['littipoOrcamento'] = $_POST['tipoOrcamento'];
		              				$_POST['littipoPreco'] = $_POST['tipoPreco'];
									$_POST['numidfuncionarioNF'] = $_POST['idfuncionarioNF'];
                  					$_POST['litimpressao_ecf_em_andamento'] = "0";
                  
									if($_POST['tipoOrcamento'] == 'ECF') {
										$_POST['litserie_ecf'] = $_POST['serie_ecf'];
										$_POST['litdatahoraCriacaoNF'] = $form->FormataDataHoraECFParaInserir($_POST['data_ecf'], $_POST['hora_ecf']);
									}
                  else { 
										$_POST['litdatahoraCriacaoNF'] = date('Y-m-d H:i:s');
									}

									// busca o código do CFOP
									$info_cfop = $cfop->getByCodigo($_POST['codigo_cfop']);
									$_POST['numidcfop'] = $info_cfop['idcfop'];
									if ($_POST['numidcfop'] == "") $_POST['numidcfop'] = "NULL";

									$_POST['litmodeloNota'] = $_POST['modeloNota'];
									$_POST['litserieNota'] = $_POST['serieNota'];

									$_POST['litinfoClienteProv'] = nl2br($_POST['infoClienteProv']);
									$_POST['litnomeClienteProv'] = $_POST['nomeClienteProv'];

									$_POST['numvalor_total_nota'] = $_POST['valor_total_nota'];
									$_POST['numvalor_total_produtos'] = $_POST['valor_total_produtos'];

									$_POST['numfrete'] = str_replace(",",".",$_POST['frete']);
									if ($_POST['numfrete'] == "") $_POST['numfrete'] = "NULL";
	
									$_POST['numoutras_despesas'] = str_replace(",",".",$_POST['outras_despesas']);
									if ($_POST['numoutras_despesas'] == "") $_POST['numoutras_despesas'] = "NULL";

									$_POST['numbase_calculo_icms'] = str_replace(",",".",$_POST['base_calculo_icms']);
									if ($_POST['numbase_calculo_icms'] == "") $_POST['numbase_calculo_icms'] = "NULL";

									$_POST['numvalor_icms'] = str_replace(",",".",$_POST['valor_icms']);
									if ($_POST['numvalor_icms'] == "") $_POST['numvalor_icms'] = "NULL";

									$_POST['numbase_calc_icms_sub'] = str_replace(",",".",$_POST['base_calc_icms_sub']);
									if ($_POST['numbase_calc_icms_sub'] == "") $_POST['numbase_calc_icms_sub'] = "NULL";

									$_POST['numvalor_icms_sub'] = str_replace(",",".",$_POST['valor_icms_sub']);
									if ($_POST['numvalor_icms_sub'] == "") $_POST['numvalor_icms_sub'] = "NULL";

									$_POST['numvalor_seguro'] = str_replace(",",".",$_POST['valor_seguro']);
									if ($_POST['numvalor_seguro'] == "") $_POST['numvalor_seguro'] = "NULL";

									$_POST['numvalor_total_ipi'] = str_replace(",",".",$_POST['valor_total_ipi']);
									if ($_POST['numvalor_total_ipi'] == "") $_POST['numvalor_total_ipi'] = "NULL";


									$_POST['numjurosParcelamento'] = $form->FormataMoedaParaInserir($_POST['juros_parcelamento']); 
									if ($_POST['numjurosParcelamento'] == "") $_POST['numjurosParcelamento'] = "NULL";
	
									$_POST['numnumero_parcelas_cr'] = $_POST['quantidade_de_parcelas'];
									if ($_POST['numnumero_parcelas_cr'] == "") $_POST['numnumero_parcelas_cr'] = "NULL";
	
									$_POST['litdias_entre_parcelas_cr'] = $_POST['dias_entre_parcelas'];
									if ($_POST['litdias_entre_parcelas_cr'] == "") $_POST['litdias_entre_parcelas_cr'] = "NULL";
	
									$_POST['litdata_parcela_1_cr'] = $_POST['data_parcela1'];
									$_POST['litdata_parcela_1_cr'] = $form->FormataDataParaInserir($_POST['litdata_parcela_1_cr']);
									
									$_POST['litsigla_modo_recebimento_prazo_cr'] = $_POST['modo_recebimento_a_prazo'];


									$_POST['numdesconto'] = str_replace(",",".",$_POST['desconto']);
									if ($_POST['numdesconto'] == "") $_POST['numdesconto'] = "NULL";

									$_POST['numidcliente'] = $_POST['idcliente'];
									if ($_POST['numidcliente'] == "") $_POST['numidcliente'] = "NULL";

									$_POST['numidtransportador'] = $_POST['idtransportador'];
									if ($_POST['numidtransportador'] == "") $_POST['numidtransportador'] = "NULL";

									$_POST['litobs'] = nl2br($_POST['litobs']);
									$_POST['litdados_adicionais'] = nl2br($_POST['litdados_adicionais']);

									// se for uma NF, busca qual será o numero da Nota Fiscal
									if ($_POST['tipoOrcamento'] == "NF") {
										$_POST['numnumeroNota'] = $filial->Busca_Numero_Nota_Fiscal($_POST['idfilial']);
									}
									// se for uma SD, apenas grava ela
									else if ($_POST['tipoOrcamento'] == "SD") {
										$_POST['numnumeroNota'] = $_POST['numeroNota'];
										unset($_POST['numeroNota']);
									}
									// se for uma ECF, o numero da nota o número COO gerado pela ECF
									else if ($_POST['tipoOrcamento'] == "ECF") {
										$_POST['numnumeroNota'] = $_POST['ordemECF'];
									}
									
									
									$orcamento->update($_GET['idorcamento'], $_POST);


									// deleta os itens do orçamento
									$orcamento->Deleta_Itens_Orcamento($_GET['idorcamento']);


									//grava os itens do orcamento
									$info_item['idorcamento'] = $_GET['idorcamento'];

									// valor da comissao total do vendedor
									$comissao_total_vendedor = 0;
									
									if ($_POST['tipoVendedor'] == "I") $campo_comissao = "comissao_interno_produto";
									else if ($_POST['tipoVendedor'] == "E") $campo_comissao = "comissao_externo_produto";
									else if ($_POST['tipoVendedor'] == "R") $campo_comissao = "comissao_representante_produto";
									else if ($_POST['tipoVendedor'] == "T") $campo_comissao = "comissao_operador_telemarketing_produto";

									for ($i=1; $i<=$_POST['total_produtos']; $i++) {

										if (isset($_POST["idproduto_".$i])) {

											if ($_POST["desconto_produto_".$i] == "") $_POST["desconto_produto_".$i] = "0,00";

											$info_item['idproduto'] = $_POST["idproduto_".$i];
											$info_item['preco_unitario_produto'] = str_replace(",",".",$_POST["preco_unitario_produto_".$i]);
											$info_item['qtd_produto'] = str_replace(",",".",$_POST["qtd_produto_".$i]);
											$info_item['desconto_produto'] = str_replace(",",".",$_POST["desconto_produto_".$i]);

											if ($_POST["icms_produto_".$i] == "") $info_item['aliquota_icms_produto'] = "NULL"; // isento de icms
											else $info_item['aliquota_icms_produto'] = $form->FormataMoedaParaInserir($_POST["icms_produto_".$i]); // não é isento

											$info_item['cst_produto'] = $_POST["cst_produto_".$i];											

											$orcamento_produto->set($info_item);
											

											// da baixa no estoque, apenas se não teve problema com o tef
											if ( ! ( ($_POST['usaTEF'] != "0") && ($_POST['finalizaTEF'] == "-1") ) ) {
												// faz a baixa no estoque deste produto
												$produto_filial->Dar_Baixa_Estoque($_SESSION['idfilial_usuario'], $info_item['idproduto'], $info_item['qtd_produto']);
											}

									
											// calcula o valor total da comissão do vendedor
											$info_produto = $produto->getById($info_item['idproduto']);

											$preco_produto_final = $info_item['preco_unitario_produto'] * (1 - ($info_item['desconto_produto']/100));
											$preco_produto_final = number_format($preco_produto_final,2,",","");
											$preco_produto_final = str_replace(",",".",$preco_produto_final);
											
											$total_produto_final = $preco_produto_final * $info_item['qtd_produto'];
											
											$comissao_produto = str_replace(",",".",$info_produto["$campo_comissao"]);
											$comissao_total_vendedor += $total_produto_final * ($comissao_produto / 100);
											//-------------------------------------------------
											
										} // fim do if

									} // fim do for
									

									// atualiza o valor da comissao do vendedor.
									// A base de cálculo da comissão é o total dos produtos menos o desconto que foi dado na compra
									$valor_base_calc_comissao = $_POST['numvalor_total_produtos'] - $_POST['numdesconto'];

									$comissao_total_vendedor = ($comissao_total_vendedor * $valor_base_calc_comissao) / $_POST['numvalor_total_produtos'];

									// arredonda a comissão do vendedor para 2 casas decimais
									$comissao_total_vendedor = number_format($comissao_total_vendedor,2,",","");
									$comissao_total_vendedor = str_replace(",",".",$comissao_total_vendedor);

									// calculo do fator de correção da comissão do vendedor
									$frete = number_format($_POST['numfrete'],2,",","");
									$outras_despesas = number_format($_POST['numoutras_despesas'],2,",","");
									$valor_seguro = number_format($_POST['numvalor_seguro'],2,",","");
									$frete = str_replace(",",".",$frete);
									$outras_despesas = str_replace(",",".",$outras_despesas);
									$valor_seguro = str_replace(",",".",$valor_seguro);
									$valor_total_nota = str_replace(",",".",$_POST['valor_total_nota']);

									$fator_correcao_comissao_vendedor = ($frete + $outras_despesas + $valor_seguro) / $valor_total_nota;
									$fator_correcao_comissao_vendedor = (100 - ($fator_correcao_comissao_vendedor * 100));
									$fator_correcao_comissao_vendedor = number_format($fator_correcao_comissao_vendedor,2,",","");
									$fator_correcao_comissao_vendedor = str_replace(",",".",$fator_correcao_comissao_vendedor);

									$info_comissao['numvalor_comissao_vendedor'] = $comissao_total_vendedor;
									$info_comissao['numvalor_base_calc_comissao'] = $valor_base_calc_comissao;
									$info_comissao['numfator_correcao_comissao_vendedor'] = $fator_correcao_comissao_vendedor;

									$orcamento->update($_GET['idorcamento'], $info_comissao);
									//----------------------------------------------
									
									// deleta todas as contas a receber geradas anteriormente
									$conta_receber->Deleta_Contas_Receber($_GET['idorcamento']);

		             				 // grava as contas a receber
									$_POST['idfuncionario'] = $_POST['idfuncionarioNF'];

									// grava as contas a receber, apenas se não teve problema com o tef
									if ( ! ( ($_POST['usaTEF'] != "0") && ($_POST['finalizaTEF'] == "-1") ) ) {	
			              				$conta_receber->Grava_Contas_Receber();
									}
									
								}


								//obtém erros
								$err = $orcamento->err;

								//se não ocorreram erros
								if(count($err) == 0) {

									$chk_imprimir_emissao_nf = $_POST['chk_imprimir_emissao_nf'];
									$idorcamento = $_GET['idorcamento'];

									if ($_POST['tipoOrcamento'] == "ECF") $numero_nota = $idorcamento; // se for ECF, busca o código da venda
									else $numero_nota = $_POST['numnumeroNota']; // se for SD ou NF, busca pelo número da nota

									// redireciona a página para evitar o problema do reload	
									$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listarNF&sucesso=alterar&numero_nota=$numero_nota&chk_imprimir_emissao_nf=$chk_imprimir_emissao_nf&idorcamento=$idorcamento'>"; 
									echo $redirecionar; 
									exit;

								}

							}

						}
						else {
							// Recupera os dado da Nota Fiscal
							$orcamento->Recupera_Dados_Da_NF();
						}
						

            if(isset($_GET['semestoque']))$info['semestoque'] = 1; else $info['semestoque'] = 0;
						//passa os dados para o template
						$smarty->assign("info", $info);

					break;



          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a deleção
					  if($_POST['for_chk']){
					  	

							// deleta todas as contas a receber geradas anteriormente
							$conta_receber->Deleta_Contas_Receber($_GET['idorcamento']);


							// deleta o registro
					  	$orcamento->delete($_GET['idorcamento']);

					  	//obtém erros
							$err = $orcamento->err;

							//se não ocorreram erros
							if(count($err) == 0){

								// redireciona a página para evitar o problema do reload	
								$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=excluir'>"; 
								echo $redirecionar; 
								exit;

							}


							//pega os erros
							$err = $orcamento->err;

							//envia a listagem para o template
							$smarty->assign("list", $list);

						}

	        break;

          
          
          
          

	      }
	      
      }
      
  	}
  	
    // seta erros
    $smarty->assign("err", $err);
    
	}
	
	// Forma Array de intruções de preenchimento
	$intrucoes_preenchimento = array();
	if ($flags['action'] == "adicionar" || $flags['action'] == "editar" || $flags['action'] == "editarNF") {
		$intrucoes_preenchimento[] = "Os campos em <span class=req>vermelho</span> s&atilde;o obrigat&oacute;rios.";
	}
	else if ($flags['action'] == "busca_generica" || $flags['action'] == "busca_parametrizada" || $flags['action'] == "busca_genericaNF" || $flags['action'] == "busca_parametrizadaNF") {
		$intrucoes_preenchimento[] = "Preencha os campos para realizar a busca.";
	}

	// Formata a mensagem para ser exibida
	$flags['intrucoes_preenchimento'] = $form->FormataMensagemAjuda($intrucoes_preenchimento);


	$smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));

  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);

  $list_permissao = $auth->check_priv($conf['priv']);
	$smarty->assign("list_permissao",$list_permissao);
  

  if ( ($flags['action'] == "editar") && ($_GET['imprimir'] == 1) ) $smarty->display("adm_orcamento_impressao.tpl");
  else $smarty->display("adm_orcamento.tpl");

?>

