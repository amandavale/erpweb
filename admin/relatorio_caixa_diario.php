<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  require_once("../common/lib/xajax/xajax.inc.php");
  
  require_once("../entidades/troco.php");
  require_once("../entidades/filial.php");
  require_once("../entidades/funcionario.php");
  require_once("../entidades/conta_pagar.php");
  require_once("../entidades/conta_receber.php");

  // configurações anotionais
  $conf['area'] = "Relatório de Caixa Diário"; // área


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
	  		$troco = new troco();	  		
				$filial = new filial();
				$funcionario = new funcionario();
	  		$conta_pagar = new conta_pagar();	  		
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

						// inicializa as variaveis
						$valor_troco = 0;
						$valor_entradas = 0;
						$valor_saidas = 0;
						$valor_saldo = 0;							


						//busca detalhes do troco
						$dia = date('Y-m-d');
						$info_troco = $troco->RecuperaTroco($dia, $_SESSION['idfilial_usuario']);
						
						// troco cadastrado
						if ($info_troco['idtroco'] != "") {
							$valor_troco = $form->FormataMoedaParaInserir($info_troco['valor_troco']);
							$flags['valor_troco'] = $info_troco['valor_troco'];
						}
						// troco não cadastrado
						else {
							$flags['valor_troco'] = $form->FormataMoedaParaExibir($valor_troco);
							$flags['msg_troco_nao_cadastrado'] = "* O troco de hoje não foi cadastrado.";
						}
						//------------------------------------------------------------------------
						

						// busca as saídas
						$list_saidas = $conta_pagar->BuscaSaidasDoDia($dia, $_SESSION['idfilial_usuario']);

						for ($i=0; $i<count($list_saidas); $i++) {
							$valor_saidas += $form->FormataMoedaParaInserir($list_saidas[$i]['valor_saiu_caixa']);
						}

						$flags['valor_saidas'] = $form->FormataMoedaParaExibir($valor_saidas);

						//passa a listagem para o template
						$smarty->assign("list_saidas", $list_saidas);
						//------------------------------------------------------------------------


						// busca as entradas
						$list_contas_receber = $conta_receber->Busca_Contas_Receber_Caixa_Diario();

						$array_modo_recebimento = array();

						for ($i=0; $i<count($list_contas_receber); $i++) {
							$valor_entradas += $form->FormataMoedaParaInserir($list_contas_receber[$i]['valor_recebido']);

							$campo = $list_contas_receber[$i]['descricao_modo_recebimento'];
							$array_modo_recebimento["$campo"] += $form->FormataMoedaParaInserir($list_contas_receber[$i]['valor_recebido']);
						}

						$list_modo_recebimento = array();
						$i = 0;
						foreach($array_modo_recebimento as $campo => $valor){
							$list_modo_recebimento[$i]['campo'] = $campo;
							$list_modo_recebimento[$i]['valor'] = $form->FormataMoedaParaExibir($valor);
							$i++;
						}

						$flags['valor_entradas'] = $form->FormataMoedaParaExibir($valor_entradas);

						//passa a listagem para o template
						$smarty->assign("list_contas_receber", $list_contas_receber);						
						$smarty->assign("list_modo_recebimento", $list_modo_recebimento);			
						//------------------------------------------------------------------------




						// calcula o saldo final
						$valor_saldo = $valor_troco + $valor_entradas - $valor_saidas;
						$flags['valor_saldo'] = $form->FormataMoedaParaExibir($valor_saldo);
						//------------------------------------------------------------------------


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

	if ($_GET['target'] == "full")  $smarty->display("adm_relatorio_caixa_diario_impressao.tpl");
  else $smarty->display("adm_relatorio_caixa_diario.tpl");
?>

