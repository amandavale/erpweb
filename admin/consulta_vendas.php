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
  require_once("../entidades/orcamento.php"); 

  require_once("consultas_ajax.php"); 


  // configurações anotionais
  $conf['area'] = "Consulta - Vendas"; // área


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
  $xajax->registerFunction("Verifica_Campos_Movimentacao_Mes_AJAX");
  

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
        $orcamento = new orcamento();
        
                          
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

							// busca os dados do relatório de recebimentos do vendedor
            if($_POST['for_chk']) {
                             
              if($_POST['data_1'] != "") $_POST['data_1'] = $form->formataDataParaInserir($_POST['data_1']);
              if($_POST['data_2'] != "") $_POST['data_2'] = $form->formataDataParaInserir($_POST['data_2']);  
     
    
              $filtro = "WHERE 
                          ORC.idfilial = ".$_SESSION['idfilial_usuario']." AND
                          DATE(ORC.datahoraCriacaoNF) >= '".$_POST['data_1']."'
                          AND DATE(ORC.datahoraCriacaoNF) <= '".$_POST['data_2']."'
                          AND ORC.tipoOrcamento <> 'O' AND ORC.idmotivo_cancelamento is null";
              $ordem = " Order by ORC.datahoraCriacaoNF ASC, ORC.idorcamento";
              $list_movimentacao = $orcamento->make_list_consulta_vendas(0,9999999,$filtro,$ordem);
     
              $smarty->assign("list_movimentacao", $list_movimentacao);

             if($_POST['data_1'] != "")  $_POST['data_1'] = $form->formataDataParaExibir($_POST['data_1']);
             if($_POST['data_2'] != "")  $_POST['data_2'] = $form->formataDataParaExibir($_POST['data_2']);   

	
       		
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

  $smarty->display("adm_consulta_vendas.tpl");
?>