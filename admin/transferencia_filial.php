<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  require_once("../common/lib/xajax/xajax.inc.php");
  
  require_once("../entidades/transferencia_filial.php");
  require_once("../entidades/transferencia_filial_produto.php");
	require_once("../entidades/funcionario.php"); 
	require_once("../entidades/produto.php"); 
	require_once("../entidades/filial.php"); 
	require_once("../entidades/produto_filial.php");

	require_once("transferencia_filial_ajax.php");

	


  // configurações anotionais
  $conf['area'] = "Transfêrencia entre filiais."; // área


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
	$xajax->registerFunction("Verifica_Campos_Transferencia_AJAX");
	$xajax->registerFunction("Insere_Produto_Encartelamento_AJAX");
	$xajax->registerFunction("Deleta_Produto_Encartelamento_AJAX");
	$xajax->registerFunction("Calcula_Total_AJAX");
	$xajax->registerFunction("Atualiza_Total_AJAX");
	$xajax->registerFunction("Seleciona_Produtos_Transferidos");


	// processa as funções
	$xajax->processRequests();
	
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
	  		$transferencia_filial = new transferencia_filial();
	  		$transferencia_filial_produto = new transferencia_filial_produto();
	  		
	  		$funcionario = new funcionario(); 
				$produto = new produto(); 
				$filial = new filial(); 
				$produto_filial = new produto_filial();

				
	  											
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

            if($_POST['for_chk']) {
            	
            	
            	
							$form->chk_empty($_POST['idfuncionario'], 1, 'Funcionário'); 

							

              $err = $form->err;

              if(count($err) == 0) {

								if($_POST["desconto"] == "") $_POST["desconto"] = 0;
								else $_POST["desconto"] = str_replace(",",".",$_POST["desconto"]);

								$_POST['observacoes'] = nl2br($_POST['observacoes']);
              	
              					//grava o registro no banco de dados
								$codigo = $transferencia_filial->set($_POST);
								
								$_POST['idtransferencia_filial'] = $codigo;
              	
								
								
								for ($count=1; $count<=$_POST['total_produtos']; $count++) {
                
									if (isset($_POST["tabela_produto_$count"])){
										$_POST["qtd_transferida"] = str_replace(",",".",$_POST["qtd_produto_$count"]);
										$_POST["idproduto"] = $_POST["idproduto_$count"];
										$_POST['preco_unitario_praticado'] = str_replace(",",".",$_POST["preco_unitario_praticado_$count"]);
										
										
										$transferencia_filial_produto->set($_POST);
										
										$produto_filial->Dar_Alta_Estoque($_POST['idfilial_destinataria'],$_POST['idproduto'],$_POST['qtd_transferida']);
										$produto_filial->Dar_Baixa_Estoque($_POST['idfilial_remetente'],$_POST['idproduto'],$_POST['qtd_transferida']);
	
									}
								
								}


								//obtém os erros que ocorreram no cadastro
								$err = $transferencia_filial->err;

								//se não ocorreram erros
								if(count($err) == 0) {

									// redireciona a página para evitar o problema do reload	
									$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=inserir'>"; 
									echo $redirecionar; 
									exit;

								}
								
              }
              
            }
            $filtro_filial = "WHERE nome_filial != '" . $_SESSION['nomefilial_usuario'] ."'";
            
            
            $list_funcionario = $funcionario->Seleciona_Funcionarios_Da_Filial($_SESSION['idfilial_usuario']);
						$smarty->assign("list_funcionario",$list_funcionario);

						$smarty->assign("nome_filial",$_SESSION['nomefilial_usuario']);
						
						$list_filial = $filial->make_list_select($filtro_filial);
						$smarty->assign("list_filial",$list_filial);
						
						$smarty->assign("data",$form->FormataDataHoraParaExibir(date("Y-m-d H:i:s")));


						

          break;


					//listagem dos registros
          case "listar":
          	
						if (isset($_GET['sucesso'])) $flags['sucesso'] = $conf["{$_GET['sucesso']}"];          	


            if ( ($_POST['for_chk']) || ($_GET['rpp'] != "") ) {

            	$flags['fez_busca'] = 1;
            		if( $_POST['data_transferencia'] != "") $_POST['data_transferencia'] = $form->FormataDataHoraParaInserir($_POST['data_transferencia']);

							if ($_POST['for_chk']) {
								$flags['idfilial_remetente'] = $_POST['idfilial_remetente'];
								$flags['idfilial_destinataria'] = $_POST['idfilial_destinataria'];
								$flags['data_transferencia'] = $_POST['data_transferencia'];
								$flags['nome_funcionario'] = $_POST['nome_funcionario'];
								$flags['rpp'] = $_POST['rpp'];

							}
							else {
								$flags['idfilial_remetente'] = $_GET['idfilial_remetente'];
								$flags['idfilial_destinataria'] = $_GET['idfilial_destinataria'];
								$flags['data_transferencia'] = $_GET['data_transferencia'];
								$flags['nome_funcionario'] = $_GET['nome_funcionario'];
								$flags['rpp'] = $_GET['rpp'];

							}
							
							
							
							$parametros_get = "&idfilial_remetente=" . $flags['idfilial_remetente'] . "&idfilial_destinataria=" . $flags['idfilial_destinataria'] . "&data_transferencia=" . $flags['data_transferencia'] . "&nome_funcionario=" . $flags['nome_funcionario'] ;


							$filtro_where = "";
							if ($flags['idfilial_remetente'] != "") $filtro_where .= " FLIR.idfilial =" . $flags['idfilial_remetente'] . " AND ";
							if ($flags['idfilial_destinataria'] != "") $filtro_where .= "idfilial_destinataria = " . $flags['idfilial_destinataria'] . " AND ";
							if ($flags['nome_funcionario'] != "") $filtro_where .= " UPPER(FNC.nome_funcionario) LIKE UPPER('%" . $flags['nome_funcionario'] . "%') AND ";
							if ($flags['data_transferencia'] != "") $filtro_where .= " UPPER(TRFL.data_transferencia) LIKE UPPER('%" . $flags['data_transferencia'] . "%') AND ";
              				$filtro_where = substr($filtro_where,0,strlen($filtro_where)-4);

							if ($_GET['target'] == "full") $flags['rpp'] = 9999999;

							if($flags['data_transferencia'] !="")$flags['data_transferencia'] = $form->FormataDataHoraParaExibir($flags['data_transferencia']);
						  //obtém qual página da listagem deseja exibir
						  $pg = intval(trim($_GET['pg']));

						  //se não foi passada a página como parâmetro, faz página default igual à página 0
						  if(!$pg) $pg = 0;

						  //lista os registros
							$list = $transferencia_filial->Busca_Parametrizada($pg, $flags['rpp'], $filtro_where, "", "ac=listar".$parametros_get."&rpp=".$flags['rpp']);

							//pega os erros
							$err = $transferencia_filial->err;

							//passa a listagem para o template
							$smarty->assign("list", $list);
							
							

            }
            
            if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];
            
            
            $list_filial = $filial->make_list_select();
						$smarty->assign("list_filial",$list_filial);
												

          break;



          
          // ação: editar <<<<<<<<<<
					case "editar":



							//busca detalhes
							$info = $transferencia_filial->getById($_GET['idtransferencia_filial']);
							
							$info['data_transferencia'] = $form->FormataDataHoraParaExibir($info['data_transferencia']);
							
							if ($info['tipo_preco'] == 'A') $info['tipo_preco'] = "Atacado"; 
							elseif ($info['tipo_preco'] == 'B') $info['tipo_preco'] = "Balcão"; 
							elseif ($info['tipo_preco'] == 'O') $info['tipo_preco'] = "Oferta"; 
							elseif ($info['tipo_preco'] == 'T') $info['tipo_preco'] = "Telemarketing";
							else  $info['tipo_preco'] = "Custo";
							
							$info['preco_2'] = (100*$info['preco_total']) / (100-$info['desconto']);
							
							
							$info['preco_total'] = number_format($info['preco_total'],2,",","");
							$info['preco_2'] = number_format($info['preco_2'],2,",","");
							$info['desconto'] = number_format($info['desconto'],2,",","");
							
							//obtém os erros
							$err = $transferencia_filial->err;
							
							          

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":
					/*
						//verifica se foi pedido a deleção
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$transferencia_filial->delete($_GET['idtransferencia_filial']);

					  	//obtém erros
							$err = $transferencia_filial->err;

							//se não ocorreram erros
							if(count($err) == 0){
								$flags['sucesso'] = $conf['excluir'];
								
								
								
							}

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista registros
							$list = $transferencia_filial->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $transferencia_filial->err;

							//envia a listagem para o template
							$smarty->assign("list", $list);

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
  
  $smarty->display("adm_transferencia_filial.tpl");
?>

