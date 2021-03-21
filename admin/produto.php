<?php


  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  require_once("../common/lib/xajax/xajax.inc.php");

  
  
  require_once("../entidades/produto.php");
	require_once("../entidades/secao.php"); 
	require_once("../entidades/unidade_venda.php"); 
	require_once("../entidades/departamento.php");
	require_once("../entidades/produto_fornecedor.php");
	require_once("../entidades/produto_filial.php");
	require_once("../entidades/produto_referencia.php");
	require_once("../entidades/filial.php");
	require_once("../entidades/encartelamento_produto.php");

	
	
	require_once("fornecedor_ajax.php");
	require_once("produto_ajax.php");


  // configurações anotionais
  $conf['area'] = "Produto"; // área


  //configuração de estilo
  $conf['style'] = "full";

  // inicializa templating
  $smarty = new Smarty;

  // configura diretórios
  $smarty->template_dir = "../common/tpl";
  $smarty->compile_dir =   "../common/tpl_c";

  // seta configurações
  $smarty->assign("conf", $conf);

  // ação selecionada
  $flags['action'] = $_GET['ac'];
  if ($flags['action'] == "") $flags['action'] = "listar";

  // inicializa autenticação
  $auth = new auth();
  
    // cria o objeto xajax
	$xajax = new xajax();
 
	
	// registra todas as funções que serão usadas
	$xajax->registerFunction("Insere_Fornecedor_AJAX");
	$xajax->registerFunction("Deleta_Fornecedor_AJAX");
	$xajax->registerFunction("Seleciona_Fornecedor_AJAX");
	$xajax->registerFunction("Atribui_Preco_AJAX");
	$xajax->registerFunction("Insere_Referencia_AJAX");
	$xajax->registerFunction("Deleta_Referencia_AJAX");
	$xajax->registerFunction("Seleciona_Referencia_AJAX");
	$xajax->registerFunction("Verifica_Campos_Produto_AJAX");
	$xajax->registerFunction("Verifica_Campos_Atualizar_Produto_AJAX");
	$xajax->registerFunction("Mostra_Detalhes_Produto_AJAX");
	$xajax->registerFunction("Seleciona_Encartelamentos_AJAX");


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
	  		$produto = new produto();
	  		
	  		$secao = new secao(); 
				$unidade_venda = new unidade_venda(); 
				$departamento = new departamento();
				$produto_fornecedor = new produto_fornecedor();
				$produto_filial = new produto_filial();
				$produto_referencia = new produto_referencia();
				$filial = new filial();
				$encartelamento_produto = new encartelamento_produto();
	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para validação de formulário
        $form = new form();
        
				$list = $auth->check_priv($conf['priv']);
				$aux = $flags['action'];
				if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}


        switch($flags['action']) {

          // ação: adicionar <<<<<<<<<<
          case "atualizar_preco":

            if($_POST['for_chk']) {


            	$form->chk_empty($_POST['iddepartamento'], 1, 'Departamento');
							$form->chk_empty($_POST['idsecao'], 1, 'Seção');

              $err = $form->err;

              if(count($err) == 0) {


                $produto->Atualiza_Preco_Sessao($_POST['idsecao'],$_POST['porcentagem'],$_POST['tipo_atribuicao']);
	

								//obtém os erros que ocorreram no cadastro
								$err = $produto->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['inserir'];

									//limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $produto->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $produto->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}

              }
              // houve erro
              else {

							}

            }


          break;





					// busca genérica
          case "busca_generica":

            if ( ($_POST['for_chk']) || ($_GET['rpp'] != "") ) {

            	$flags['fez_busca'] = 1;

							if ($_POST['for_chk']) {
								$flags['busca'] = $_POST['busca'];
								$flags['rpp'] = $_POST['rpp'];
							}
							else {
								$flags['busca'] = $_GET['busca'];
								$flags['rpp'] = $_GET['rpp'];
							}

							if ($_GET['target'] == "full") $flags['rpp'] = 9999999;


						  //obtém qual página da listagem deseja exibir
						  $pg = intval(trim($_GET['pg']));

						  //se não foi passada a página como parâmetro, faz página default igual à página 0
						  if(!$pg) $pg = 0;

						  //lista os registros
							$list = $produto->Busca_Generica($pg, $flags['rpp'], $flags['busca'], "", "ac=busca_generica&busca=".$flags['busca']."&rpp=".$flags['rpp']);

							//pega os erros
							$err = $produto->err;

							//passa a listagem para o template
							$smarty->assign("list", $list);

						}

						if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];

          break;



					// busca parametrizada
          case "busca_parametrizada":

            if ( ($_POST['for_chk']) || ($_GET['rpp'] != "") ) {

            	$flags['fez_busca'] = 1;
								
							if ($_POST['for_chk']) {
								$flags['descricao_produto'] = $_POST['descricao_produto'];
								$flags['departamento_produto'] = $_POST['departamento_produto'];
								$flags['secao_produto'] = $_POST['secao_produto'];
								$flags['localizacao_produto'] = $_POST['localizacao_produto'];
								$flags['referencia_produto'] = $_POST['referencia_produto'];
								$flags['produto_comercializado'] = $_POST['produto_comercializado'];
								$flags['rpp'] = $_POST['rpp'];

							}
							else {
								$flags['descricao_produto'] = $_GET['descricao_produto'];
								$flags['departamento_produto'] = $_GET['departamento_produto'];
								$flags['secao_produto'] = $_GET['secao_produto'];
								$flags['localizacao_produto'] = $_GET['localizacao_produto'];
								$flags['referencia_produto'] = $_GET['referencia_produto'];
								$flags['produto_comercializado'] = $_GET['produto_comercializado'];
								$flags['rpp'] = $_GET['rpp'];

							}

							$parametros_get = "&descricao_produto=" . $flags['descricao_produto'] . "&departamento_produto=" . $flags['departamento_produto'] . "&secao_produto=" . $flags['secao_produto'] . "&localizacao_produto=" . $flags['localizacao_produto'] . "&referencia_produto=" . $flags['referencia_produto'] . "&produto_comercializado=" . $flags['produto_comercializado'];


							$filtro_where = "";
							if ($flags['descricao_produto'] != "") $filtro_where .= " UPPER(PRD.descricao_produto) LIKE UPPER('%" . $flags['descricao_produto'] . "%') AND ";
							if ($flags['departamento_produto'] != "") $filtro_where .= " UPPER(D.nome_departamento) LIKE UPPER('%" . $flags['departamento_produto'] . "%') AND ";
							if ($flags['secao_produto'] != "") $filtro_where .= " UPPER(S.nome_secao) LIKE UPPER('%" . $flags['secao_produto'] . "%') AND ";
							if ($flags['localizacao_produto'] != "") $filtro_where .= " UPPER(PRD.localizacao_produto) LIKE UPPER('%" . $flags['localizacao_produto'] . "%') AND ";
							if ($flags['referencia_produto'] != "") $filtro_where .= " UPPER(PRD.referencia_produto) LIKE UPPER('%" . $flags['referencia_produto'] . "%') AND ";
              if (!isset($flags['produto_comercializado'])) $filtro_where .= " PRD.produto_comercializado = '1' AND ";
							$filtro_where = substr($filtro_where,0,strlen($filtro_where)-4);
							if (isset($flags['produto_comercializado'])) $flags['produto_comercializado'] = 1;

							if ($_GET['target'] == "full") $flags['rpp'] = 9999999;


						  //obtém qual página da listagem deseja exibir
						  $pg = intval(trim($_GET['pg']));

						  //se não foi passada a página como parâmetro, faz página default igual à página 0
						  if(!$pg) $pg = 0;

						  //lista os registros
							$list = $produto->Busca_Parametrizada($pg, $flags['rpp'], $filtro_where, "", "ac=busca_parametrizada$parametros_get&rpp=".$flags['rpp']);

							//pega os erros
							$err = $produto->err;

							//passa a listagem para o template
							$smarty->assign("list", $list);

						}

						if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];

          break;




          // ação: adicionar <<<<<<<<<<
          case "adicionar":
			
            if($_POST['for_chk']) {
            	
            	
            	$form->chk_empty($_POST['iddepartamento'], 1, 'Departamento');
							$form->chk_empty($_POST['idsecao'], 1, 'Seção'); 
							
							$form->chk_empty($_POST['descricao_produto'], 1, 'Descrição do produto'); 
							$form->chk_empty($_POST['localizacao_produto'], 1, 'Localização do produto'); 
							$form->chk_empty($_POST['idunidade_venda'], 1, 'Unidade de venda'); 

							$count=1;
              while (isset($_POST["filial_$count"]))
              {
								$form->chk_empty($_POST["preco_balcao_produto_$count"], 1, 'Preço de balcão (R$)');
								$form->chk_empty($_POST["preco_oferta_produto_$count"], 1, 'Preço de oferta (R$)');
								$form->chk_empty($_POST["preco_atacado_produto_$count"], 1, 'Preço de atacado (R$)');
								$form->chk_empty($_POST["preco_telemarketing_produto_$count"], 1, 'Preço de telemarketing (R$)');
								$form->chk_empty($_POST["preco_custo_produto_$count"], 1, 'Preço de Custo (R$)');
								$count++;
							}
              $form->chk_empty($_POST['produto_comercializado'], 1, 'Produto Comercializado');

              $err = $form->err;

              if(count($err) == 0) {

								// retira as aspas duplas " do nome do produto 								
                $_POST['descricao_produto'] = str_replace('\"','',$_POST['descricao_produto']);

								$_POST['percentual_max_desconto_produto'] = str_replace(",",".",$_POST['percentual_max_desconto_produto']);
								$_POST['peso_bruto_produto'] = str_replace(",",".",$_POST['peso_bruto_produto']); 
								$_POST['peso_liquido_produto'] = str_replace(",",".",$_POST['peso_liquido_produto']); 
								$_POST['qtd_unitaria_embalagem_compra_produto'] = str_replace(",",".",$_POST['qtd_unitaria_embalagem_compra_produto']); 
								$_POST['qtd_unitaria_embalagem_venda_produto'] = str_replace(",",".",$_POST['qtd_unitaria_embalagem_venda_produto']); 
								$_POST['comissao_interno_produto'] = str_replace(",",".",$_POST['comissao_interno_produto']);
								$_POST['comissao_externo_produto'] = str_replace(",",".",$_POST['comissao_externo_produto']);
								$_POST['comissao_representante_produto'] = str_replace(",",".",$_POST['comissao_representante_produto']);
								$_POST['comissao_operador_telemarketing_produto'] = str_replace(",",".",$_POST['comissao_operador_telemarketing_produto']);
								$_POST['icms_produto'] = str_replace(",",".",$_POST['icms_produto']);
								$_POST['ipi_produto'] = str_replace(",",".",$_POST['ipi_produto']);

	              $_POST['data_cadastro_produto'] = $form->FormataDataParaInserir($_POST['data_cadastro_produto']); 
								
								if ($_POST['percentual_max_desconto_produto'] == "") $_POST['percentual_max_desconto_produto'] = "NULL";
								if ($_POST['peso_bruto_produto'] == "") $_POST['peso_bruto_produto'] = "NULL"; 
								if ($_POST['peso_liquido_produto'] == "") $_POST['peso_liquido_produto'] = "NULL"; 
								if ($_POST['qtd_unitaria_embalagem_compra_produto'] == "") $_POST['qtd_unitaria_embalagem_compra_produto'] = "NULL"; 
								if ($_POST['qtd_unitaria_embalagem_venda_produto'] == "") $_POST['qtd_unitaria_embalagem_venda_produto'] = "NULL"; 
								if ($_POST['comissao_interno_produto'] == "") $_POST['comissao_interno_produto'] = "NULL";
								if ($_POST['comissao_externo_produto'] == "") $_POST['comissao_externo_produto'] = "NULL";
								if ($_POST['comissao_representante_produto'] == "") $_POST['comissao_representante_produto'] = "NULL";
								if ($_POST['comissao_operador_telemarketing_produto'] == "") $_POST['comissao_operador_telemarketing_produto'] = "NULL";
								if ($_POST['icms_produto'] == "") $_POST['icms_produto'] = "NULL";
								if ($_POST['ipi_produto'] == "") $_POST['ipi_produto'] = "NULL";

	              
								//grava o registro no banco de dados
								$idproduto = $produto->set($_POST);
								
								$_POST['idproduto'] = $idproduto;
								
								$count = 1;
								while (isset($_POST["filial_$count"]))
								{
	 								$_POST["preco_balcao_produto_$count"] = str_replace(",",".",$_POST["preco_balcao_produto_$count"]);
									$_POST["preco_oferta_produto_$count"] = str_replace(",",".",$_POST["preco_oferta_produto_$count"]);
									$_POST["preco_atacado_produto_$count"] = str_replace(",",".",$_POST["preco_atacado_produto_$count"]);
									$_POST["preco_telemarketing_produto_$count"] = str_replace(",",".",$_POST["preco_telemarketing_produto_$count"]);
									$_POST["preco_custo_produto_$count"] = str_replace(",",".",$_POST["preco_custo_produto_$count"]);
                  $_POST["qtd_produto_$count"] = str_replace(",",".",$_POST["qtd_produto_$count"]);

								  if ($_POST["preco_balcao_produto_$count"] == "") $_POST["preco_balcao_produto_$count"] = "NULL";
									if ($_POST["preco_oferta_produto_$count"] == "") $_POST["preco_oferta_produto_$count"] = "NULL";
									if ($_POST["preco_atacado_produto_$count"] == "") $_POST["preco_atacado_produto_$count"] = "NULL";
									if ($_POST["preco_telemarketing_produto_$count"] == "") $_POST["preco_telemarketing_produto_$count"] = "NULL";
									if ($_POST["preco_custo_produto_$count"] == "") $_POST["preco_custo_produto_$count"] = "NULL";
									if ($_POST["qtd_produto_$count"] == "") $_POST["qtd_produto_$count"] = "0.00";
									

									$_POST['preco_balcao_produto'] = $_POST["preco_balcao_produto_$count"];
									$_POST['preco_oferta_produto'] = $_POST["preco_oferta_produto_$count"];
									$_POST['preco_atacado_produto'] = $_POST["preco_atacado_produto_$count"];
									$_POST['preco_telemarketing_produto'] = $_POST["preco_telemarketing_produto_$count"];
									$_POST['preco_custo_produto'] = $_POST["preco_custo_produto_$count"];
									$_POST['qtd_produto'] = $_POST["qtd_produto_$count"];
									$_POST['produto_em_oferta'] = $_POST["produto_em_oferta_$count"];

									$_POST['idfilial'] = $_POST["filial_$count"];

									$teste = $produto_filial->set($_POST);
									$count++;
								}	
								

                // grava os itens da tabela de fornecedores
								$produto_fornecedor->GravaFornecedor($_POST,$idproduto);

							 	// grava os itens da tabela de produtos
								$produto_referencia->GravaReferencia($_POST,$idproduto);


								//obtém os erros que ocorreram no cadastro
								$err = $produto->err;

								//se não ocorreram erros
								if(count($err) == 0) {

									// redireciona a página para evitar o problema do reload	
									$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=inserir&buscar_dados_produto=1&idproduto=$idproduto'>"; 
									echo $redirecionar; 
									exit;

								}
								
              }
              
            }
            
 
						$list_unidade_venda = $unidade_venda->make_list_select();
						$smarty->assign("list_unidade_venda",$list_unidade_venda);
						
						$filtro = "WHERE 1";
						$list_filial = $filial->make_list(0, 9999, $filtro);
						$numero_filial = count($list_filial);
						$smarty->assign("numero_filial",$numero_filial);
						$smarty->assign("list_filial",$list_filial);
						

          break;


					//listagem dos registros
          case "listar":

						if (isset($_GET['sucesso'])) $flags['sucesso'] = $conf["{$_GET['sucesso']}"];
          
						if (isset($_GET['buscar_dados_produto'])) {
							$flags['buscar_dados_produto'] = 1;
							$flags['idproduto'] = $_GET['idproduto'];
						}

          break;
          
          
          // ação: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info  = $_POST;
							
							$info['idproduto'] = $_GET['idproduto']; 
							
							$form->chk_empty($_POST['iddepartamento'], 1, 'Departamento');
							$form->chk_empty($_POST['idsecao'], 1, 'Seção');
							$form->chk_empty($_POST['litdescricao_produto'], 1, 'Descrição do produto'); 
							$form->chk_empty($_POST['litlocalizacao_produto'], 1, 'Localização do produto'); 
							$form->chk_empty($_POST['numidunidade_venda'], 1, 'Unidade de venda'); 

              $count=1;
              while (isset($_POST["filial_$count"]))
              {
								$form->chk_empty($_POST["numpreco_balcao_produto_$count"], 1, 'Preço de balcão (R$)');
								$form->chk_empty($_POST["numpreco_oferta_produto_$count"], 1, 'Preço de oferta (R$)');
								$form->chk_empty($_POST["numpreco_atacado_produto_$count"], 1, 'Preço de atacado (R$)');
								$form->chk_empty($_POST["numpreco_telemarketing_produto_$count"], 1, 'Preço de telemarketing (R$)');
								$form->chk_empty($_POST["numpreco_custo_produto_$count"], 1, 'Preço de Custo (R$)');

								$count++;
							}
							
							$err = $form->err;

							$_POST['numidsecao'] = $_POST['idsecao'];


		          if(count($err) == 0) {
		          	
                $_POST['litdescricao_produto'] = str_replace('\"', '',$_POST['litdescricao_produto']);
                $_POST['numpercentual_max_desconto_produto'] = str_replace(",",".",$_POST['numpercentual_max_desconto_produto']);
								$_POST['numpeso_bruto_produto'] = str_replace(",",".",$_POST['numpeso_bruto_produto']); 
								$_POST['numpeso_liquido_produto'] = str_replace(",",".",$_POST['numpeso_liquido_produto']); 
								$_POST['numqtd_unitaria_embalagem_compra_produto'] = str_replace(",",".",$_POST['numqtd_unitaria_embalagem_compra_produto']); 
								$_POST['numqtd_unitaria_embalagem_venda_produto'] = str_replace(",",".",$_POST['numqtd_unitaria_embalagem_venda_produto']); 
								$_POST['numcomissao_interno_produto'] = str_replace(",",".",$_POST['numcomissao_interno_produto']);
								$_POST['numcomissao_externo_produto'] = str_replace(",",".",$_POST['numcomissao_externo_produto']);
								$_POST['numcomissao_representante_produto'] = str_replace(",",".",$_POST['numcomissao_representante_produto']);
								$_POST['numcomissao_operador_telemarketing_produto'] = str_replace(",",".",$_POST['numcomissao_operador_telemarketing_produto']);
								$_POST['numicms_produto'] = str_replace(",",".",$_POST['numicms_produto']);
								$_POST['numipi_produto'] = str_replace(",",".",$_POST['numipi_produto']);



								$_POST['litdata_cadastro_produto'] = $form->FormataDataParaInserir($_POST['litdata_cadastro_produto']); 
								
								
		          	
								if ($_POST['numpercentual_max_desconto_produto'] == "") $_POST['numpercentual_max_desconto_produto'] = "NULL";
								if ($_POST['numpeso_bruto_produto'] == "") $_POST['numpeso_bruto_produto'] = "NULL"; 
								if ($_POST['numpeso_liquido_produto'] == "") $_POST['numpeso_liquido_produto'] = "NULL"; 
								if ($_POST['numqtd_unitaria_embalagem_compra_produto'] == "") $_POST['numqtd_unitaria_embalagem_compra_produto'] = "NULL"; 
								if ($_POST['numqtd_unitaria_embalagem_venda_produto'] == "") $_POST['numqtd_unitaria_embalagem_venda_produto'] = "NULL"; 
								if ($_POST['numcomissao_interno_produto'] == "") $_POST['numcomissao_interno_produto'] = "NULL";
								if ($_POST['numcomissao_externo_produto'] == "") $_POST['numcomissao_externo_produto'] = "NULL";
								if ($_POST['numcomissao_representante_produto'] == "") $_POST['numcomissao_representante_produto'] = "NULL";
								if ($_POST['numcomissao_operador_telemarketing_produto'] == "") $_POST['numcomissao_operador_telemarketing_produto'] = "NULL";
								if ($_POST['numicms_produto'] == "") $_POST['numicms_produto'] = "NULL";
								if ($_POST['numipi_produto'] == "") $_POST['numipi_produto'] = "NULL";




								//retira os preços do POST e monta o array de atualização dos preços da filial


								$count = 1;
								while (isset($_POST["filial_$count"]))
								{
	 								$_POST["numpreco_balcao_produto_$count"] = str_replace(",",".",$_POST["numpreco_balcao_produto_$count"]);
									$_POST["numpreco_oferta_produto_$count"] = str_replace(",",".",$_POST["numpreco_oferta_produto_$count"]);
									$_POST["numpreco_atacado_produto_$count"] = str_replace(",",".",$_POST["numpreco_atacado_produto_$count"]);
									$_POST["numpreco_telemarketing_produto_$count"] = str_replace(",",".",$_POST["numpreco_telemarketing_produto_$count"]);
									$_POST["numpreco_custo_produto_$count"] = str_replace(",",".",$_POST["numpreco_custo_produto_$count"]);
                  

								  if ($_POST["numpreco_balcao_produto_$count"] == "") $_POST["numpreco_balcao_produto_$count"] = "NULL";
									if ($_POST["numpreco_oferta_produto_$count"] == "") $_POST["numpreco_oferta_produto_$count"] = "NULL";
									if ($_POST["numpreco_atacado_produto_$count"] == "") $_POST["numpreco_atacado_produto_$count"] = "NULL";
									if ($_POST["numpreco_telemarketing_produto_$count"] == "") $_POST["numpreco_telemarketing_produto_$count"] = "NULL";
									if ($_POST["numpreco_custo_produto_$count"] == "") $_POST["numpreco_custo_produto_$count"] = "NULL";
									

									$list_filial['numpreco_balcao_produto'] = $_POST["numpreco_balcao_produto_$count"];
									$list_filial['numpreco_oferta_produto'] = $_POST["numpreco_oferta_produto_$count"];
									$list_filial['numpreco_atacado_produto'] = $_POST["numpreco_atacado_produto_$count"];
									$list_filial['numpreco_telemarketing_produto'] = $_POST["numpreco_telemarketing_produto_$count"];
									$list_filial['numpreco_custo_produto'] = $_POST["numpreco_custo_produto_$count"];
									$list_filial['litproduto_em_oferta'] = $_POST["litproduto_em_oferta_$count"];
									

									$list_filial['idfilial'] = $_POST["filial_$count"];

									$produto_filial->update($_GET['idproduto'], $list_filial['idfilial'], $list_filial);

									unset($_POST["numpreco_balcao_produto_$count"]);
									unset($_POST["numpreco_oferta_produto_$count"]);
									unset($_POST["numpreco_atacado_produto_$count"]);
									unset($_POST["numpreco_telemarketing_produto_$count"]);
									unset($_POST["numpreco_custo_produto_$count"]);
									unset($_POST["litproduto_em_oferta_$count"]);
									

									$count++;
								}


								$produto->update($_GET['idproduto'], $_POST);
								
                //grava os itens da tabela de fornecedores
								$produto_fornecedor->GravaFornecedor($_POST,$_GET['idproduto']);

					  	 	//grava os itens da tabela de produtos
								$produto_referencia->GravaReferencia($_POST,$_GET['idproduto']);
									

								//obtém erros
								$err = $produto->err;
								

								//se não ocorreram erros
								if(count($err) == 0) {

									if ($_POST['litproduto_comercializado'] ==0) {
										$encartelamento_produto->delete_produto_encartelamento($_GET['idproduto']);
										$produto_referencia->delete_produto_referencia($_GET['idproduto']);
									}

									$idproduto = $_GET['idproduto'];

									// redireciona a página para evitar o problema do reload	
									$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=alterar&buscar_dados_produto=1&idproduto=$idproduto'>"; 
									echo $redirecionar; 
									exit;


								}

							}

						}
						else {

							//busca detalhes
							$info = $produto->getById($_GET['idproduto']);

							//tratamento das informações para fazer o UPDATE
							$info['idproduto'] = $_GET['idproduto'];
							$info['idsecao'] = $info['idsecao'];
							$info['idsecao_NomeTemp'] = $info['nome_secao'];
							$info['idsecao_Nome'] = $info['nome_secao'];
							$info['iddepartamento'] = $info['iddepartamento'];
							$info['iddepartamento_NomeTemp'] = $info['nome_departamento'];
							$info['iddepartamento_Nome'] = $info['nome_departamento'];
							$info['litdescricao_produto'] = $info['descricao_produto']; 
							$info['litlocalizacao_produto'] = $info['localizacao_produto']; 
							$info['litaplicacao_produto'] = $info['aplicacao_produto']; 
							$info['numpercentual_max_desconto_produto'] = $info['percentual_max_desconto_produto'];
							$info['litreferencia_produto'] = $info['referencia_produto']; 
							$info['litobservacao_produto'] = $info['observacao_produto']; 
							$info['litdata_cadastro_produto'] = $info['data_cadastro_produto']; 
							$info['numpeso_bruto_produto'] = $info['peso_bruto_produto']; 
							$info['numpeso_liquido_produto'] = $info['peso_liquido_produto']; 
							$info['numidunidade_venda'] = $info['idunidade_venda']; 
							$info['numqtd_unitaria_embalagem_compra_produto'] = $info['qtd_unitaria_embalagem_compra_produto']; 
							$info['numqtd_unitaria_embalagem_venda_produto'] = $info['qtd_unitaria_embalagem_venda_produto']; 
							$info['numcomissao_interno_produto'] = $info['comissao_interno_produto'];
							$info['numcomissao_externo_produto'] = $info['comissao_externo_produto'];
							$info['numcomissao_representante_produto'] = $info['comissao_representante_produto'];
							$info['numcomissao_operador_telemarketing_produto'] = $info['comissao_operador_telemarketing_produto'];
              $info['litproduto_comercializado'] = $info['produto_comercializado'];
              $info['litcodigo_produto'] = $info['codigo_produto'];
              $info['litcst_produto'] = $info['cst_produto'];
              $info['numicms_produto'] = $info['icms_produto'];
              $info['numipi_produto'] = $info['ipi_produto'];
							$info['litsituacao_tributaria_produto'] = $info['situacao_tributaria_produto'];		

							//obtém os erros
							$err = $produto->err;
						}

   
						$list_unidade_venda = $unidade_venda->make_list_select();
						$smarty->assign("list_unidade_venda",$list_unidade_venda);

						

						//seleciona as filiais
						$list_filial = $produto_filial->make_list_filial(0,999999,"WHERE PRD.idproduto ='{$_GET['idproduto']}'");
						$smarty->assign("list_filial",$list_filial);


						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":
						/*  Não é possível excluir um produto. Apenas passar ele para não comercializável.
						//verifica se foi pedido a deleção
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$produto->delete($_GET['idproduto']);

					  	//obtém erros
							$err = $produto->err;

							//se não ocorreram erros
							if(count($err) == 0){
								
								// redireciona a página para evitar o problema do reload	
								$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=excluir'>"; 
								echo $redirecionar; 
								exit;								
								
							}

						}
						*/
	        break;

          
          
          
          

	      }
	      
      }
      
  	}
  	
    // seta erros
    $smarty->assign("err", $err);
    
	}

	// Forma Array de intruções de preenchimento
	$intrucoes_preenchimento = array();
	if ($flags['action'] == "adicionar" || $flags['action'] == "editar" ) {
		$intrucoes_preenchimento[] = "Os campos em <span class=req>vermelho</span> s&atilde;o obrigat&oacute;rios.";
	}
	else if ($flags['action'] == "busca_generica" || $flags['action'] == "busca_parametrizada") {
		$intrucoes_preenchimento[] = "Preencha os campos para realizar a busca.";
	}

	// Formata a mensagem para ser exibida
	$flags['intrucoes_preenchimento'] = $form->FormataMensagemAjuda($intrucoes_preenchimento);



	$smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));

  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);


  $list_permissao = $auth->check_priv($conf['priv']);
	$smarty->assign("list_permissao",$list_permissao);
  
  
	if ($_GET['target'] == "full") $smarty->display("adm_relatorio_produto.tpl");
  else $smarty->display("adm_produto.tpl");

?>

