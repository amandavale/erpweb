<?php

/*
	O modulo "entrada" também utiliza esta entidade para manipulação de dados.
	Pedido e Entrada é a mesma tabela.

*/

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  require_once("../common/lib/xajax/xajax.inc.php");
  
  require_once("../entidades/pedido.php");
  require_once("../entidades/pedido_produto.php");
	require_once("../entidades/funcionario.php"); 
	require_once("../entidades/motivo_cancelamento.php"); 
	require_once("../entidades/fornecedor.php"); 
	require_once("../entidades/filial.php"); 
	
	require_once("pedido_ajax.php");
	

  // configurações anotionais
  $conf['area'] = "Pedidos de Compras"; // área

  //configuração de estilo
  $conf['style'] = "full";

  // inicializa templating
  $smarty = new Smarty;
  
    // cria o objeto xajax
	$xajax = new xajax();
	
	// registra todas as funções que serão usadas
	$xajax->registerFunction("Verifica_Campos_Pedido_AJAX");
	$xajax->registerFunction("Insere_Produto_Encartelamento_AJAX");
	$xajax->registerFunction("Deleta_Produto_Encartelamento_AJAX");
	$xajax->registerFunction("Seleciona_Produtos_Pedidos");

	// processa as funções
	$xajax->processRequests();
	
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
	  		$pedido = new pedido();
	  		
	  		$funcionario = new funcionario(); 
				$motivo_cancelamento = new motivo_cancelamento(); 
				$fornecedor = new fornecedor(); 
				$filial = new filial(); 
				$pedido_produto = new pedido_produto(); 
				
	  											
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

						//busca os funcionarios da filial
						$list_funcionarios = $funcionario->Seleciona_Funcionarios_Da_Filial($_SESSION['idfilial_usuario']);
						$smarty->assign("list_funcionarios",$list_funcionarios);



            if($_POST['for_chk']) {
            	
            	
            	
							$_POST['idfilial'] = $_SESSION['idfilial_usuario'];
							$_POST['status_pedido'] = 'P';

              $err = $form->err;

              if(count($err) == 0) {

								$_POST['obs'] = nl2br($_POST['obs']); 
							
	              $_POST['ipi'] = str_replace(",",".",$_POST['ipi']); 
								$_POST['frete'] = str_replace(",",".",$_POST['frete']); 
								$_POST['valor_nota'] = str_replace(",",".",$_POST['valor_nota']); 
								$_POST['valor_total_produtos'] = str_replace(",",".",$_POST['valor_total_produtos']); 
								$_POST['desconto'] = str_replace(",",".",$_POST['desconto']); 
								$_POST['base_calculo_icms'] = str_replace(",",".",$_POST['base_calculo_icms']); 
								$_POST['icms'] = str_replace(",",".",$_POST['icms']); 
								$_POST['seguro'] = str_replace(",",".",$_POST['seguro']); 
								$_POST['outras_despesas'] = str_replace(",",".",$_POST['outras_despesas']); 
								$_POST['base_calculo_icms_substituicao'] = str_replace(",",".",$_POST['base_calculo_icms_substituicao']); 
								$_POST['icms_substituicao'] = str_replace(",",".",$_POST['icms_substituicao']); 
								
	              $_POST['data_entrada'] = $form->FormataDataParaInserir($_POST['data_entrada']); 
								$_POST['data_emissao_nota'] = $form->FormataDataParaInserir($_POST['data_emissao_nota']); 
								$_POST['previsao_entrega'] = $form->FormataDataParaInserir($_POST['previsao_entrega']); 


								$_POST['idUltFuncionario'] = $_POST['idfuncionario'];
								$_POST['dataHoraCriacao'] = date('Y-m-d H:i:s');
								$_POST['dataHoraUltAlteracao'] = $_POST['dataHoraCriacao'];
	              

								if ($_POST['idcfop'] == "") $_POST['idcfop'] = "NULL"; 
								if ($_POST['idmotivo_cancelamento'] == "") $_POST['idmotivo_cancelamento'] = "NULL"; 
								if ($_POST['ipi'] == "") $_POST['ipi'] = "NULL"; 
								if ($_POST['frete'] == "") $_POST['frete'] = "NULL"; 
								if ($_POST['valor_nota'] == "") $_POST['valor_nota'] = "NULL"; 
								if ($_POST['valor_total_produtos'] == "") $_POST['valor_total_produtos'] = "NULL"; 
								if ($_POST['desconto'] == "") $_POST['desconto'] = "NULL"; 
								if ($_POST['base_calculo_icms'] == "") $_POST['base_calculo_icms'] = "NULL"; 
								if ($_POST['icms'] == "") $_POST['icms'] = "NULL"; 
								if ($_POST['seguro'] == "") $_POST['seguro'] = "NULL"; 
								if ($_POST['outras_despesas'] == "") $_POST['outras_despesas'] = "NULL"; 
								if ($_POST['base_calculo_icms_substituicao'] == "") $_POST['base_calculo_icms_substituicao'] = "NULL"; 
								if ($_POST['icms_substituicao'] == "") $_POST['icms_substituicao'] = "NULL"; 
								if ($_POST['dataHoraCriacao'] == "") $_POST['dataHoraCriacao'] = "NULL"; 
								if ($_POST['dataHoraUltAlteracao'] == "") $_POST['dataHoraUltAlteracao'] = "NULL"; 
								


														
																

             	
              	//grava o registro no banco de dados
								$codigo = $pedido->set($_POST);
								
								$_POST['idpedido'] = $codigo;
								
								$ordem_produto = 1;
								for ($count=1; $count<=$_POST['total_produtos']; $count++) {
                
									if (isset($_POST["tabela_produto_$count"]))
									{
	                  $_POST["qtd"] = str_replace(",",".",$_POST["qtd_produto_$count"]);
	                  $_POST["idproduto"] = $_POST["idproduto_$count"];
	                  $_POST["valorUnit"] = "NULL";
	                  $_POST["preco_custo_antigo"] = "NULL";
	                  $_POST["aliquota_icms_produto"] = "NULL";
	                  $_POST["ipi_produto"] = "NULL";
										$_POST["ordem_produto"] = $ordem_produto;
										$ordem_produto++;		                  								
	                  
	                  $pedido_produto->set($_POST);
									//grava o registro no banco de dados
									
									}
								}

								//obtém os erros que ocorreram no cadastro
								$err = $pedido->err;

								//se não ocorreram erros
								if(count($err) == 0) {

									// redireciona a página para evitar o problema do reload	
									$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=inserir'>"; 
									echo $redirecionar; 
									exit;

								}
								
              }
              
            }
            
            $data = date("d/m/Y");
            $smarty->assign("data", $data);
            
            $filial = $_SESSION['nomefilial_usuario'];
            $smarty->assign("filial", $filial);
            

						

          break;


					//listagem dos registros
          case "listar":

						if (isset($_GET['sucesso'])) $flags['sucesso'] = $conf["{$_GET['sucesso']}"];
          
					  //obtém qual página da listagem deseja exibir
					  $pg = intval(trim($_GET['pg']));

					  //se não foi passada a página como parâmetro, faz página default igual à página 0
					  if(!$pg) $pg = 0;

					  //lista os registros
						$list = $pedido->make_list($pg, $conf['rppg'],"WHERE PED.status_pedido = 'P'","ORDER BY PED.previsao_entrega DESC");

						//pega os erros
						$err = $pedido->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          break;
          
          
          // ação: editar <<<<<<<<<<
					case "editar":

						//busca os funcionarios da filial
						$list_funcionarios = $funcionario->Seleciona_Funcionarios_Da_Filial($_SESSION['idfilial_usuario']);
						$smarty->assign("list_funcionarios",$list_funcionarios);


						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idpedido'] = $_GET['idpedido']; 
							
														
							$_POST['numidfilial'] = $_SESSION['idfilial_usuario'];
							$_POST['litstatus_pedido'] = 'P';							
							
							$err = $form->err;

		          if(count($err) == 0) {

								$_POST['litobs'] = nl2br($_POST['litobs']); 
							
								$_POST['numipi'] = str_replace(",",".",$_POST['numipi']); 
								$_POST['numfrete'] = str_replace(",",".",$_POST['numfrete']); 
								$_POST['numvalor_nota'] = str_replace(",",".",$_POST['numvalor_nota']); 
								$_POST['numdesconto'] = str_replace(",",".",$_POST['numdesconto']); 
								$_POST['numbase_calculo_icms'] = str_replace(",",".",$_POST['numbase_calculo_icms']); 
								$_POST['numicms'] = str_replace(",",".",$_POST['numicms']); 
								$_POST['numseguro'] = str_replace(",",".",$_POST['numseguro']); 
								$_POST['numoutras_despesas'] = str_replace(",",".",$_POST['numoutras_despesas']); 
								$_POST['numbase_calculo_icms_substituicao'] = str_replace(",",".",$_POST['numbase_calculo_icms_substituicao']); 
								$_POST['numicms_substituicao'] = str_replace(",",".",$_POST['numicms_substituicao']); 
								
								$_POST['litdata_entrada'] = $form->FormataDataParaInserir($_POST['litdata_entrada']); 
								$_POST['litdata_emissao_nota'] = $form->FormataDataParaInserir($_POST['litdata_emissao_nota']); 
								$_POST['litprevisao_entrega'] = $form->FormataDataParaInserir($_POST['litprevisao_entrega']); 
								
								
								$_POST['numidUltFuncionario'] = $_POST['idfuncionario'];
								$_POST['litdataHoraUltAlteracao'] = date('Y-m-d H:i:s');
		          	
								if ($_POST['numipi'] == "") $_POST['numipi'] = "NULL"; 
								if ($_POST['numfrete'] == "") $_POST['numfrete'] = "NULL"; 
								if ($_POST['numvalor_nota'] == "") $_POST['numvalor_nota'] = "NULL"; 
								if ($_POST['numdesconto'] == "") $_POST['numdesconto'] = "NULL"; 
								if ($_POST['numbase_calculo_icms'] == "") $_POST['numbase_calculo_icms'] = "NULL"; 
								if ($_POST['numicms'] == "") $_POST['numicms'] = "NULL"; 
								if ($_POST['numseguro'] == "") $_POST['numseguro'] = "NULL"; 
								if ($_POST['numoutras_despesas'] == "") $_POST['numoutras_despesas'] = "NULL"; 
								if ($_POST['numbase_calculo_icms_substituicao'] == "") $_POST['numbase_calculo_icms_substituicao'] = "NULL"; 
								if ($_POST['numicms_substituicao'] == "") $_POST['numicms_substituicao'] = "NULL"; 
								


								$pedido->update($_GET['idpedido'], $_POST);
								
								$pedido_produto->delete_produto($_GET['idpedido']);

								$_POST['idpedido'] = $_GET['idpedido'];

								$ordem_produto = 1;              									
								for ($count=1; $count<=$_POST['total_produtos']; $count++) {
                
									if (isset($_POST["tabela_produto_$count"]))
									{
	                  $_POST["qtd"] = str_replace(",",".",$_POST["qtd_produto_$count"]);
	                  $_POST["idproduto"] = $_POST["idproduto_$count"];
	                  $_POST["valorUnit"] = "NULL";
	                  $_POST["preco_custo_antigo"] = "NULL";	
	                  $_POST["aliquota_icms_produto"] = "NULL";
	                  $_POST["ipi_produto"] = "NULL";                  
										$_POST["ordem_produto"] = $ordem_produto;
										$ordem_produto++;			                  								
	                  
	                  $pedido_produto->set($_POST);
									//grava o registro no banco de dados
									
									}
								}
								
								

								//obtém erros
								$err = $pedido->err;

								//se não ocorreram erros
								if(count($err) == 0) {

									if($_POST['gerar'] == 1) {
										$aux = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL=entrada.php?ac=gerar_pedido&idpedido=".$_GET["idpedido"]."'>"; 
										echo $aux ; 
										exit;
									}
									else {
										// redireciona a página para evitar o problema do reload	
										$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=alterar'>"; 
										echo $redirecionar; 
										exit;
									}

								}

							}

						}
						else {

							//busca detalhes
							$info = $pedido->getById($_GET['idpedido']);

							//tratamento das informações para fazer o UPDATE
							$info['numidUltFuncionario'] = $info['idUltFuncionario']; 
							$info['numidfuncionario'] = $info['idfuncionario']; 
							$info['numidmotivo_cancelamento'] = $info['idmotivo_cancelamento']; 
							$info['numidfilial'] = $info['idfilial']; 
							$info['litobs'] = strip_tags($info['obs']); 
							$info['numipi'] = $info['ipi']; 
							$info['numfrete'] = $info['frete']; 
							$info['numvalor_nota'] = $info['valor_nota']; 
							$info['litdata_entrada'] = $info['data_entrada']; 
							$info['litnumero_nota'] = $info['numero_nota']; 
							$info['litserie_nota'] = $info['serie_nota']; 
							$info['litdata_emissao_nota'] = $info['data_emissao_nota']; 
							$info['numdesconto'] = $info['desconto']; 
							$info['numbase_calculo_icms'] = $info['base_calculo_icms']; 
							$info['numicms'] = $info['icms']; 
							$info['numseguro'] = $info['seguro']; 
							$info['numoutras_despesas'] = $info['outras_despesas']; 
							$info['numbase_calculo_icms_substituicao'] = $info['base_calculo_icms_substituicao']; 
							$info['numicms_substituicao'] = $info['icms_substituicao'];
							$info['litprevisao_entrega'] = $info['previsao_entrega'];
							 
 							$info['numidfornecedor'] = $info['idfornecedor']; 
							$info['idfornecedor_NomeTemp'] = $info['nome_fornecedor']; 
							$info['idfornecedor_Nome'] = $info['nome_fornecedor']; 

							// busca informações do fornecedor
							$info_fornecedor = $fornecedor->RetornaFornecedor($info['idfornecedor']);
							$smarty->assign("info_fornecedor", $info_fornecedor);
							
							
							
							//obtém os erros
							$err = $pedido->err;
						}

						// busca os dados do funcionario
						$info_funcionario_criou = $funcionario->getById($info['idfuncionario']);
						$smarty->assign("info_funcionario_criou", $info_funcionario_criou);

						$info_funcionario_alterou = $funcionario->getById($info['idUltFuncionario']);
						$smarty->assign("info_funcionario_alterou", $info_funcionario_alterou);
						


						$info['filial_nome'] = $_SESSION['nomefilial_usuario'];
            
            
            
            

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a deleção
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	
							$pedido_produto->delete_produto($_GET['idpedido']);

							$pedido->delete($_GET['idpedido']);

					  	//obtém erros
							$err = $pedido->err;

							//se não ocorreram erros
							if(count($err) == 0) {
								
								// redireciona a página para evitar o problema do reload	
								$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=excluir'>"; 
								echo $redirecionar; 
								exit;								
								
							}

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
	if ($flags['action'] == "adicionar" || $flags['action'] == "editar" ) {
		$intrucoes_preenchimento[] = "Os campos em <span class=req>vermelho</span> s&atilde;o obrigat&oacute;rios.";
	}
	else if ($flags['action'] == "busca_generica" || $flags['action'] == "busca_parametrizada") {
		$intrucoes_preenchimento[] = "Preencha os campos para realizar a busca.";
	}

	// Formata a mensagem para ser exibida
	$flags['intrucoes_preenchimento'] = $form->FormataMensagemAjuda($intrucoes_preenchimento);

  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);
  
  $smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));
  
  $list_permissao = $auth->check_priv($conf['priv']);
	$smarty->assign("list_permissao",$list_permissao);

  $smarty->display("adm_pedido.tpl");
?>

