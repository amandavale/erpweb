<?php

  //inclus�o de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  require_once("../common/lib/xajax/xajax.inc.php");
  
  require_once("../entidades/entrega.php");
  require_once("../entidades/entrega_produto.php");
	require_once("../entidades/funcionario.php"); 
	require_once("../entidades/funcionario.php"); 
	require_once("../entidades/motivo_cancelamento.php"); 
	require_once("../entidades/transportador.php"); 
	require_once("../entidades/orcamento.php"); 
	require_once("../entidades/orcamento_produto.php"); 
	require_once("../entidades/endereco.php");
	require_once("../entidades/filial.php");
	require_once("../entidades/cliente.php");
	
	require_once("entrega_ajax.php");	

  // configura��es anotionais
  $conf['area'] = "Entrega de Mercadoria p/ Cliente"; // �rea


  //configura��o de estilo
  $conf['style'] = "full";

  // inicializa templating
  $smarty = new Smarty;

  // cria o objeto xajax
	$xajax = new xajax();
	
	// registra todas as fun��es que ser�o usadas
	$xajax->registerFunction("Verifica_Campos_Entrega_AJAX");
	$xajax->registerFunction("Verifica_Campos_Busca_Rapida_AJAX");
	$xajax->registerFunction("ReduzQuantidade");
	$xajax->registerFunction("Seleciona_Produtos_Orcamento");
	$xajax->registerFunction("Busca_Rapida_AJAX");
	$xajax->registerFunction("Seleciona_Info_Transportador");
	$xajax->registerFunction("Seleciona_Produtos_Impressao_AJAX");

	// processa as fun��es
	$xajax->processRequests();

  // configura diret�rios
  $smarty->template_dir = "../common/tpl";
  $smarty->compile_dir =   "../common/tpl_c";

  // seta configura��es
  $smarty->assign("conf", $conf);

  // a��o selecionada
  $flags['action'] = $_GET['ac'];
  if ($flags['action'] == "") $flags['action'] = "listar";

  // inicializa autentica��o
  $auth = new auth();

  // verifica requisi��o de logout
  if($flags['action'] == "logout") {
    $auth->logout();
  }
  else {

    // inicializa vetor de erros
    $err = array();

    // verifica sess�o
    if(!$auth->check_user()) {
      // verifica requisi��o de login
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

    // conte�do
    if($auth->check_user()) {
      // verifica privil�gios
      if(!$auth->check_priv($conf['priv'])) {
        $err = $auth->err;
      }
      else {
        // libera conte�do
        $flags['okay'] = 1;

				//inicializa classe
	  		$entrega = new entrega();
	  		
	  		$funcionario = new funcionario(); 
				$funcionario = new funcionario(); 
				$motivo_cancelamento = new motivo_cancelamento(); 
				$transportador = new transportador(); 
				$orcamento = new orcamento(); 
				$orcamento_produto = new orcamento_produto(); 
				$entrega_produto = new entrega_produto();
				$endereco = new endereco();				
				$filial = new filial();		
				$cliente = new cliente();		
	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para valida��o de formul�rio
        $form = new form();

        $list = $auth->check_priv($conf['priv']);
				$aux = $flags['action'];
				if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}


        switch($flags['action']) {

          // a��o: adicionar <<<<<<<<<<
          case "adicionar":

            if($_POST['for_chk']) {
            	
              $err = $form->err;

              if(count($err) == 0) {

								$_POST['obs'] = nl2br($_POST['obs']); 


								$_POST['datahoraUltAlteracao'] = date("Y-m-d H:i:s");
	              $_POST['datahoraCriacao'] = date("Y-m-d H:i:s");
	              $_POST['dataMarcada'] = $form->FormataDataParaInserir($_POST['dataMarcada']); 
								$_POST['dataRealizada'] = $form->FormataDataParaInserir($_POST['dataRealizada']); 
								$_POST['idUltFuncionario'] = $_POST['idfuncionario'];
								if($_POST['idtransportador'] == "") $_POST['idtransportador'] = 'NULL';
	              
								//grava o registro no banco de dados
								$_POST['identrega'] = $entrega->set($_POST);

								$info_produtos = $orcamento_produto->make_list(0,99999,"WHERE PO.idorcamento =".$_POST['idorcamento']);

								for ($i=0; $i<count($info_produtos); $i++) {
								
								$_POST['idproduto'] = $_POST["idproduto_$i"];
								$_POST['qtd'] = str_replace(",",".",$_POST["qtd_a_entregar_$i"]);

								$entrega_produto->set($_POST);

								}


								//obt�m os erros que ocorreram no cadastro
								$err = $entrega->err;

								//se n�o ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['inserir'];

									//limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $entrega->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $entrega->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}
								
              }
              
            }
            
            $list_funcionario = $funcionario->Seleciona_Funcionarios_Da_Filial($_SESSION['idfilial_usuario']);
						$smarty->assign("list_funcionario",$list_funcionario);


						$list_orcamento = $orcamento->make_list_select();
						$smarty->assign("list_orcamento",$list_orcamento);

						

          break;


					//listagem dos registros
          case "listar":
          
					  //obt�m qual p�gina da listagem deseja exibir
					  $pg = intval(trim($_GET['pg']));

					  //se n�o foi passada a p�gina como par�metro, faz p�gina default igual � p�gina 0
					  if(!$pg) $pg = 0;

						//pega os erros
						$err = $entrega->err;

          break;
          
          
          // a��o: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {

							$info = $_POST;
							
							$info['identrega'] = $_GET['identrega']; 
						
							
							$err = $form->err;

		          if(count($err) == 0) {


								$_POST['litobs'] = nl2br($_POST['litobs']); 
							
								
								$_POST['litdataMarcada'] = $form->FormataDataParaInserir($_POST['litdataMarcada']); 
								$_POST['litdataRealizada'] = $form->FormataDataParaInserir($_POST['litdataRealizada']); 
								
								$_POST['litdatahoraUltAlteracao'] = date("Y-m-d H:i:s");  	
							
								$info_produtos = $entrega_produto->SelecionaProdutosOrcamento($_GET['identrega']);

								
								$_POST['numidtransportador'] = $_POST['idtransportador'];								
								if($_POST['numidtransportador'] == "") $_POST['numidtransportador'] = "NULL";

								$entrega->update($_GET['identrega'], $_POST);
				
								$entrega_produto->deleteProdutos($_GET['identrega']);

								$_POST['identrega'] = $_GET['identrega'];

								for ($i=0; $i<$info_produtos; $i++) {

								$_POST['idproduto'] = $_POST["idproduto_$i"];
								$_POST['qtd'] = str_replace(",",".",$_POST["qtd_a_entregar_$i"]);

								$entrega_produto->set($_POST);
								
								}

								//obt�m erros
								$err = $entrega->err;

								//se n�o ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['alterar'];

								  //limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $entrega->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $entrega->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}

							}

						}
						else {

							//busca detalhes
			
							$info = $entrega->getById($_GET['identrega']);

							//tratamento das informa��es para fazer o UPDATE
							$info['numidUltFuncionario'] = $info['idUltFuncionario']; 
							$info['numidfuncionario'] = $info['idfuncionario']; 
							$info['numidmotivo_cancelamento'] = $info['idmotivo_cancelamento']; 
							$info['idtransportador'] = $info['idtransportador']; 
							$info['numidorcamento'] = $info['idorcamento']; 
							$info['litdataMarcada'] = $info['dataMarcada']; 
							$info['litrealizada'] = $info['realizada']; 
							$info['litdataRealizada'] = $info['dataRealizada']; 
							$info['litobs'] = strip_tags($info['obs']); 
							$info['litplaca'] = $info['placa']; 
							$info['litdatahoraCriacao'] = $info['datahoraCriacao']; 
							$info['litdatahoraUltAlteracao'] = $info['datahoraUltAlteracao']; 
							$info['idtransportador_Nome'] = $info['nome_transportador']; 
							$info['idtransportador_NomeTemp'] = $info['nome_transportador']; 
							$info['datahoraCriacao'] = $form->FormataDataHoraParaExibir($info['datahoraCriacao']);
							$info['datahoraUltAlteracao'] = $form->FormataDataHoraParaExibir($info['datahoraUltAlteracao']);

							//obt�m os erros
							$err = $entrega->err;
						}

						// busca os dados da filial
						$info_filial = $filial->getById($_SESSION['idfilial_usuario']);
						$info_filial['telefone_filial'] = $form->FormataTelefoneParaExibir($info_filial['telefone_filial']);
						$info_filial['fax_filial'] = $form->FormataTelefoneParaExibir($info_filial['fax_filial']);
						$smarty->assign("info_filial", $info_filial);

						// se selecionou o cliente, busca os dados dele
						$info_cliente = $cliente->BuscaDadosCliente($info['idcliente']);

						$info_nota = $orcamento->getById($info['idorcamento']);
						$smarty->assign("info_nota", $info_nota);
						
						$info['data_impresao'] = date("d/m/Y");

						$smarty->assign("info_cliente", $info_cliente);

						// busca o endere�o da filial
						$info_endereco_filial = $endereco->getById($info_filial['idendereco_filial']);
						$smarty->assign("info_endereco_filial", $info_endereco_filial);

						$info_transportador = $transportador->getByID($info['idtransportador']);
						$smarty->assign("info_transportador",$info_transportador);

            $list_funcionario = $funcionario->Seleciona_Funcionarios_Da_Filial($_SESSION['idfilial_usuario']);
						$smarty->assign("list_funcionario",$list_funcionario);

						$list_cancelamento = $motivo_cancelamento->make_list_select();
						$smarty->assign("list_cancelamento",$list_cancelamento);


						//passa os dados para o template
						$smarty->assign("info", $info);


					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a dele��o
					  if($_POST['for_chk']){

							$entrega_produto->deleteProdutos($_GET['identrega']);
					  	
							// deleta o registro
					  	$entrega->delete($_GET['identrega']);

					  	//obt�m erros
							$err = $entrega->err;

							//se n�o ocorreram erros
							if(count($err) == 0){
								$flags['sucesso'] = $conf['excluir'];
								
								
								
							}

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista registros
							$list = $entrega->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $entrega->err;

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
	// Forma Array de intru��es de preenchimento
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

  if($_GET['imprimir'] == 1) $smarty->display("adm_relatorio_entrega.tpl");
	else $smarty->display("adm_entrega.tpl");
?>

