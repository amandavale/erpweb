<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
	require_once("../entidades/plano.php");

  // inicializa templating
  $smarty = new Smarty;

  // configura diretórios
  $smarty->template_dir = "../common/tpl";
  $smarty->compile_dir =   "../common/tpl_c";

  // seta configurações
  $smarty->assign("conf", $conf);

  // ação selecionada
  $flags['action'] = $_GET['ac'];


  // inicializa autenticação
  $auth = new auth();

	//inicializa classe
	$plano = new plano();

  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();



  switch($flags['action']) {


		// busca os estados de acordo com a busca
		case "busca_plano":

			$plano->Filtra_Plano_AJAX($_GET['typing'],  $_GET['campoID'], $_GET['mostraDetalhes'], $_GET['tipo']);
           // var_dump($_GET['idplano']);
		break;

  }




  // seta erros
  $smarty->assign("err", $err);

  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);


/**
*
* confere se os campos estiverem vazios
* util xajax
*/

function Verifica_Campos_Planos_AJAX ($post) {

	// variáveis globais
	global $form;
	global $conf;
	global $db;
	global $falha;
	global $err;
	global $cliente_juridico;
	global $plano;
	
	// cria o objeto xajaxResponse
	$objResponse = new xajaxResponse();


	if ($_GET['ac'] == "editar") {
		$form->chk_empty($post['litnumero'], 1, 'Código');
		$form->chk_empty($post['litnome'], 1, 'Nome');
		$form->chk_empty($post['littipo'], 1, 'Tipo');
        $err = $form->err;
        

		$numero = $post['hid_pai_numero'] . $post['litnumero'];

		if ($post['hid_numero'] != $numero){
			$info_plano = $plano->make_list(0,1," WHERE PLA.numero = '$numero'");
			if( isset($info_plano[0]['numero']) ) $err[] = "Este código de plano já existe, por favor escolha outro.";
		}
		
		
	}
	else{

		$form->chk_empty($post['numero'], 1, 'Código');
		$form->chk_empty($post['nome'], 1, 'Nome');
		$form->chk_empty($post['tipo'], 1, 'Tipo');
		$err = $form->err;
		
		$numero = $post['hid_pai_numero'] . $post['numero'];
		$info_plano = $plano->make_list(0,1," WHERE PLA.numero = '$numero' ");
		if( isset($info_plano[0]['numero']) ) $err[] = "Este código de plano já existe, por favor escolha outro.";
	}
	
 	
 	
 	//echo $info_plano[0]['numero'];

	// se nao houveram erros, da o submit no form
	if(count($err) == 0) {
		$objResponse->addScript("document.getElementById('formPlano').submit();");
	}else{
		$mensagem = implode("\n",$err);
		$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
	}

	// retorna o resultado XML
	return $objResponse->getXML();
}


?>
