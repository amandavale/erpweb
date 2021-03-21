<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  require_once("../common/lib/xajax/xajax.inc.php");

  require_once("../entidades/cheque.php");
	require_once("../entidades/banco.php"); 
	
	require_once("cheque_ajax.php");


  // configurações anotionais
  $conf['area'] = "Cadastro de Cheques"; // área


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
	$xajax->registerFunction("Verifica_Campos_Cheque_AJAX");
	$xajax->registerFunction("Verifica_Campos_Busca_Rapida_Cheque_AJAX");


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
	  		$cheque = new cheque();
	  		
	  		$banco = new banco(); 
				
	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para validação de formulário
        $form = new form();
        

				$list = $auth->check_priv($conf['priv']);
				$aux = $flags['action'];
				if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}
				

        switch($flags['action']) {



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
							$list = $cheque->Busca_Generica($pg, $flags['rpp'], $flags['busca'], "", "ac=busca_generica&busca=".$flags['busca']."&rpp=".$flags['rpp']);

							//pega os erros
							$err = $cheque->err;

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
								$flags['banco'] = $_POST['banco'];
								$flags['agencia'] = $_POST['agencia'];
								$flags['conta'] = $_POST['conta'];
								$flags['numero_cheque'] = $_POST['numero_cheque'];
								$flags['data_cheque'] = $_POST['data_cheque'];
								$flags['titular_conta'] = $_POST['titular_conta'];
								$flags['rpp'] = $_POST['rpp'];
							}
							else {
								$flags['banco'] = $_GET['banco'];
								$flags['agencia'] = $_GET['agencia'];
								$flags['conta'] = $_GET['conta'];
								$flags['numero_cheque'] = $_GET['numero_cheque'];
								$flags['data_cheque'] = $_GET['data_cheque'];
								$flags['titular_conta'] = $_GET['titular_conta'];
								$flags['rpp'] = $_GET['rpp'];
							}

							$parametros_get = "&banco=" . $flags['banco'] . "&agencia=" . $flags['agencia'] . "&conta=" . $flags['conta'] . "&numero_cheque=" . $flags['numero_cheque'] . "&data_cheque=" . $flags['data_cheque'] . "&titular_conta=" . $flags['titular_conta'];


							$filtro_where = "";
							if ($flags['banco'] != "") $filtro_where .= " UPPER(BNC.nome_banco) LIKE UPPER('%" . $flags['banco'] . "%') AND ";
							if ($flags['agencia'] != "") $filtro_where .= " ( (UPPER(CHQ.agencia) LIKE UPPER('%" . $flags['agencia'] . "%'))) AND ";
							if ($flags['conta'] != "") $filtro_where .= " ( (UPPER(CHQ.conta) LIKE UPPER('%" . $flags['conta'] . "%')) ) AND ";
							if ($flags['numero_cheque'] != "") $filtro_where .= " UPPER(CHQ.numero_cheque) LIKE UPPER('%" . $flags['numero_cheque'] . "%') AND ";
							if ($flags['data_cheque'] != "") $filtro_where .= " ( (UPPER(CHQ.data_cheque) LIKE UPPER('%" . $form->FormataDataParaInserir($flags['data_cheque']) . "%')) ) AND ";
							if ($flags['titular_conta'] != "") $filtro_where .= " ( (UPPER(CHQ.titular_conta) LIKE UPPER('%" . $flags['titular_conta'] . "%')) ) AND ";

							$filtro_where = substr($filtro_where,0,strlen($filtro_where)-4);


							if ($_GET['target'] == "full") $flags['rpp'] = 9999999;


						  //obtém qual página da listagem deseja exibir
						  $pg = intval(trim($_GET['pg']));

						  //se não foi passada a página como parâmetro, faz página default igual à página 0
						  if(!$pg) $pg = 0;

						  //lista os registros
							$list = $cheque->Busca_Parametrizada($pg, $flags['rpp'], $filtro_where, "", "ac=busca_parametrizada$parametros_get&rpp=".$flags['rpp']);

							//pega os erros
							$err = $cheque->err;

							//passa a listagem para o template
							$smarty->assign("list", $list);

						}

						if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];

          break;




          // ação: adicionar <<<<<<<<<<
          case "adicionar":

            if($_POST['for_chk']) {
            	
            	
            	
							$form->chk_empty($_POST['idbanco'], 1, 'Banco'); 
							$form->chk_empty($_POST['agencia'], 1, 'Agência'); 
							$form->chk_empty($_POST['conta'], 1, 'Conta'); 
							

              $err = $form->err;

              if(count($err) == 0) {

								$_POST['observacao'] = nl2br($_POST['observacao']); 
							
	              $_POST['valor'] = str_replace(",",".",$_POST['valor']); 
								
	              $_POST['data_cheque'] = $form->FormataDataParaInserir($_POST['data_cheque']); 
								$_POST['bom_para'] = $form->FormataDataParaInserir($_POST['bom_para']); 
								
	              
								
								if ($_POST['valor'] == "") $_POST['valor'] = "NULL"; 
								

	              
								//grava o registro no banco de dados
								$idcheque = $cheque->set($_POST);


								//obtém os erros que ocorreram no cadastro
								$err = $cheque->err;

								//se não ocorreram erros
								if(count($err) == 0) {

									// redireciona a página para evitar o problema do reload	
									$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=inserir&idcheque=$idcheque'>"; 
									echo $redirecionar; 
									exit;



								}
								
              }
              
            }
            						

          break;


					//listagem dos registros
          case "listar":

						if (isset($_GET['sucesso'])) { 
							$flags['sucesso'] = $conf["{$_GET['sucesso']}"];

							$_POST['idcheque'] = $_GET['idcheque'];
							if ($_GET['sucesso'] == "inserir") $_POST['idcheque_inserido'] = $_GET['idcheque'];
							if ($_GET['sucesso'] != "excluir") $flags['buscar_dados_cheque'] = 1;
						}

          break;
          
          
          // ação: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idcheque'] = $_GET['idcheque']; 
							
							
							$form->chk_empty($_POST['numidbanco'], 1, 'Banco'); 
							$form->chk_empty($_POST['litagencia'], 1, 'Agência'); 
							$form->chk_empty($_POST['litconta'], 1, 'Conta'); 
							
							
							$err = $form->err;

		          if(count($err) == 0) {

								$_POST['litobservacao'] = nl2br($_POST['litobservacao']); 
							
								$_POST['numvalor'] = str_replace(",",".",$_POST['numvalor']); 
								
								$_POST['litdata_cheque'] = $form->FormataDataParaInserir($_POST['litdata_cheque']); 
								$_POST['litbom_para'] = $form->FormataDataParaInserir($_POST['litbom_para']); 
								
								
		          	
								if ($_POST['numvalor'] == "") $_POST['numvalor'] = "NULL"; 
								


								$cheque->update($_GET['idcheque'], $_POST);
								
								

								//obtém erros
								$err = $cheque->err;

								//se não ocorreram erros
								if(count($err) == 0) {

									$idcheque =	$_GET['idcheque'];

									// redireciona a página para evitar o problema do reload	
									$redirecionar = "<meta HTTP-EQUIV='refresh' CONTENT='1; URL={$conf['nome_programa']}.php?ac=listar&sucesso=alterar&idcheque=$idcheque'>"; 
									echo $redirecionar; 
									exit;


								}

							}

						}
						else {

							//busca detalhes
							$info = $cheque->getById($_GET['idcheque']);

							//tratamento das informações para fazer o UPDATE
							$info['numidbanco'] = $info['idbanco']; 
							$info['litagencia'] = $info['agencia']; 
							$info['litagencia_dig'] = $info['agencia_dig']; 
							$info['litconta'] = $info['conta']; 
							$info['litconta_dig'] = $info['conta_dig']; 
							$info['litnumero_cheque'] = $info['numero_cheque']; 
							$info['litdata_cheque'] = $info['data_cheque']; 
							$info['litbom_para'] = $info['bom_para']; 
							$info['littitular_conta'] = $info['titular_conta']; 
							$info['numvalor'] = $info['valor']; 
							$info['litobservacao'] = strip_tags($info['observacao']); 
							
							$info['idbanco_Nome'] = $info['nome_banco'];
							$info['idbanco_NomeTemp'] = $info['nome_banco'];							
							
							
							//obtém os erros
							$err = $cheque->err;
						}
	
		
						// busca os dados de origem do cheque (contas a receber)
						$list_contas_receber = $cheque->Busca_Origem_Cheque($_GET['idcheque']);
						$smarty->assign("list_contas_receber", $list_contas_receber);

						// busca os dados de destino do cheque (contas a pagar)
						$list_contas_pagar = $cheque->Busca_Destino_Cheque($_GET['idcheque']);
						$smarty->assign("list_contas_pagar", $list_contas_pagar);




						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a deleção
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$cheque->delete($_GET['idcheque']);

					  	//obtém erros
							$err = $cheque->err;

							//se não ocorreram erros
							if(count($err) == 0){

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

	
	$smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));

  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);
  
	$list_permissao = $auth->check_priv($conf['priv']);
	$smarty->assign("list_permissao",$list_permissao);

	if ($_GET['target'] == "full")  $smarty->display("adm_relatorio_cheque.tpl");
  else $smarty->display("adm_cheque.tpl");
?>

