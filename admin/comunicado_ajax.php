<?php

	//inclusão de bibliotecas
	require_once("../common/lib/conf.inc.php");
	require_once("../common/lib/db.inc.php");
	require_once("../common/lib/auth.inc.php");
	require_once("../common/lib/form.inc.php");
	require_once("../common/lib/Smarty/Smarty.class.php");
	
	require_once("../entidades/comunicado.php");
	
	global $conf;
	
	// inicializa templating
	$smarty = new Smarty;
	
	// ação selecionada
	$flags['action'] = $_GET['ac'];
	
	// inicializa autenticação
	$auth = new auth();
	
	//inicializa classe
	$comunicado = new comunicado();
	
	
	// inicializa banco de dados
	$db = new db();
	
	//incializa classe para validação de formulário
	$form = new form();
	
	
	
	switch ($flags['action']) {
	
	    // busca os clientes de acordo com a busca
	    case "busca_comunicado":
	        $comunicado->Filtra_Comunicado_AJAX($_GET['typing'], $_GET['campoID']);
	    break;
	    
	    case 'apaga_arquivo':
	    	
			$caminho_arquivo = $conf['path'] . '/common/comunicados/' . $_POST['id_cliente'] . '_' . $_POST['nome_arquivo'];
			if(file_exists($caminho_arquivo)){
				
				if(unlink($caminho_arquivo)){
					echo json_encode(true);
				}
				else{
					echo json_encode(false);
				}
			}
			else{
				echo json_encode(false);
			}
			
			exit();

    	break;
	
	}
