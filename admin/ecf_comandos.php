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
  require_once("../entidades/sintegra.php"); 
  

	require_once("ecf_comandos_ajax.php");
  require_once("orcamento_ajax.php");

  // configurações anotionais
  $conf['area'] = "ECF - Comandos"; // área


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
	$xajax->registerFunction("Verifica_Campos_Comando_ECF_AJAX");
  $xajax->registerFunction("Verifica_Serie_ECF_AJAX");  


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


				$filial = new filial(); 
        $sintegra = new sintegra();


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

						if ($_GET['comando'] == "sintegra") {
	
							// busca os dados da filial
							$info_filial = $filial->BuscaDadosFilial($_SESSION['idfilial_usuario']);

							$info_filial['nome_filial'] = substr($info_filial['nome_filial'], 0, 30);
							$info_filial['logradouro'] = substr($info_filial['logradouro'], 0, 30);
							$info_filial['numero'] = substr($info_filial['numero'], 0, 4);
							$info_filial['complemento'] = substr($info_filial['complemento'], 0, 20);
							$info_filial['nome_bairro'] = substr($info_filial['nome_bairro'], 0, 14);
							$info_filial['nome_cidade'] = substr($info_filial['nome_cidade'], 0, 28);
							$info_filial['cep'] = substr($sintegra->FormataCEPSintegra($info_filial['cep']), 0, 8);
							$info_filial['telefone_filial'] = substr($info_filial['telefone_filial'], 0, 11);
							$info_filial['fax_filial'] = substr($info_filial['fax_filial'], 0, 9);

							$smarty->assign("info_filial", $info_filial);

							// forma os anos
							$ano['1'] = date(Y);
							$ano['2'] = date(Y)-1;
							$ano['3'] = date(Y)-2;
							$ano['4'] = date(Y)-3;
							
							$smarty->assign("ano", $ano);

						}
						else if ($_GET['comando'] == "leiturax") {

						}
						else if ($_GET['comando'] == "lmf_data") {

						}
						else if ($_GET['comando'] == "lmf_reducao") {

						}
            else if ($_GET['comando'] == "suprimento") {

            }      
            else if ($_GET['comando'] == "sangria") {

            }  
            else if ($_GET['comando'] == "reducao_z") {

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

  $smarty->display("adm_ecf_comandos.tpl");

?>

