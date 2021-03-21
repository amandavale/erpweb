<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  require_once("../common/lib/xajax/xajax.inc.php");
  
	require_once("../entidades/filial.php"); 
	require_once("../entidades/funcionario.php");
  require_once("../entidades/conta_receber.php");

	require_once("conta_receber_ajax.php");	


  // configurações anotionais
  $conf['area'] = "Relatório da Comissão do Vendedor"; // área


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
	$xajax->registerFunction("Verifica_Campos_Busca_Rapida_Comissao_Vendedor_AJAX");


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
				$filial = new filial();
				$funcionario = new funcionario();
	  		$conta_receber = new conta_receber();	  		
	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para validação de formulário
        $form = new form();
        
				$list = $auth->check_priv($conf['priv']);
				$aux = $flags['action'];
				if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}
				
				

        switch($flags['action']) {



					//listagem dos registros
          case "listar":

						// busca os dados da filial
						$info_filial = $filial->getById($_SESSION['idfilial_usuario']);
						$smarty->assign("info_filial", $info_filial);

						// busca o dia e hora atual
						$flags['data_hora_atual'] = date('d/m/Y H:i:s');

						//busca os VENDEDORES da filial
						$list_funcionarios = $funcionario->Seleciona_Funcionarios_Da_Filial($_SESSION['idfilial_usuario'], "V");
						$smarty->assign("list_funcionarios",$list_funcionarios);


						// busca os dados do relatório de recebimentos do vendedor
            if($_POST['for_chk']) {

							if ($_GET['target'] == "full") {
								$_POST['idFuncionario']	= $_POST['idFuncionario_relatorio'];
								$_POST['data_recebimento_de']	= $_POST['data_recebimento_de_relatorio'];
								$_POST['data_recebimento_ate']	= $_POST['data_recebimento_ate_relatorio'];
							}

							// busca as informações do vendedor
							$info_vendedor = $funcionario->getById($_POST['idFuncionario']);							

							// lista as contas a receber das comissões do vendedor
							$list_contas_receber = $conta_receber->Busca_Contas_Receber_Comissao_Vendedor($_POST);

							// contabiliza o total a receber do vendedor
							$total_a_receber = 0;
							for ($i=0; $i<count($list_contas_receber); $i++) {

								$total_a_receber += $form->FormataMoedaParaInserir($list_contas_receber[$i]['valor_comissao_com_correcao']);

							} // fim do for

							$flags['total_a_receber']	= $form->FormataMoedaParaExibir($total_a_receber);


							//passa a listagem para o template
							$smarty->assign("list_contas_receber", $list_contas_receber);
							$smarty->assign("info_vendedor", $info_vendedor);
						
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

	if ($_GET['target'] == "full")  $smarty->display("adm_relatorio_comissao_vendedor_impressao.tpl");
  else $smarty->display("adm_relatorio_comissao_vendedor.tpl");
?>

