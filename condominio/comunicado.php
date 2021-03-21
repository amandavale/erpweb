<?php

// inclus�o de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");

require_once("../entidades/comunicado.php");
require_once("../entidades/comunicado_condominio.php");



// configura��es adicionais
$conf['area'] = "Comunicados"; // �ea
// inicializa templating
$smarty = new Smarty;

// configura diret�rios
$smarty->template_dir = "../common/tpl";
$smarty->compile_dir = "../common/tpl_c";

// seta configura��es
$smarty->assign("conf", $conf);

// a��o selecionada
$flags['action'] = $_GET['ac'];
if ($flags['action'] == "")
    $flags['action'] = "listar";


// inicializa autentica��o
$auth = new auth();

// inicializa banco de dados
$db = new db();

//incializa classe para valida��o de formul�rio
$form = new form();

$comunicado = new comunicado();
$comunicado_condominio = new comunicado_condominio();


// verifica requisi��o de logout
if ($flags['action'] == "logout") {
    $auth->logout();
} else {
    // inicializa vetor de erros
    $err = array();

    // verifica sess�o
    if (!$auth->check_user()) {

        // verifica requisi��o de login
        if ($_POST['usr_chk']) {

            // verifica login
            if (!$auth->login($_POST['usr_log'], $_POST['usr_sen'])) {
                $err = $auth->err;
            }
        } else {
            //$err = $auth->err;
        }
    }
    else{
    	
        // libera contedo
        $flags['okay'] = 1;

        
        switch($flags['action']){
        	
        	case 'listar':
        		
        		/// busca os comunicados aos quais o condominio est� associado
        		$lista_comunicados = $comunicado_condominio->buscaComunicadosCondominio($_SESSION['condominio']['idcliente']);
        		$smarty->assign('lista_comunicados',$lista_comunicados);
        		
        	break;
        	
        	
        	case 'detalhar':
        		
				// busca dados do comunicado
    	        $dados_comunicado = $comunicado->getById($_GET['id_comunicado']);
    	        $smarty->assign('dados_comunicado', $dados_comunicado);
    	        
    	        /// busca arquivos associados ao comunicado
    	        $arquivos_comunicado = $comunicado->buscaArquivosComunicado($_GET['id_comunicado']);
    	        $smarty->assign('arquivos_comunicado',$arquivos_comunicado);
    	         
        		        		
        	break;
        	
        	
        	case 'baixar_arquivo':
        		
        		/**
        		 * Mostra tela para download do arquivo
        		 */
        		
        		$arquivo = $conf['path'] . '/common/comunicados/' . $_GET['id_comunicado'] . '_' . $_GET['nome_arquivo'];

        		$finfo = finfo_open(FILEINFO_MIME_TYPE);
        		$tipo_arquivo = finfo_file($finfo,$arquivo);
        		
        		header('Content-type: ' . $tipo_arquivo);
        		header('Content-Disposition: attachment; filename="' . $_GET['nome_arquivo'] . '"');
        		readfile($arquivo);        		
        		
        		exit();
        		
        	break;
        	
        }
    }


    // seta erros
    $smarty->assign("err", $err);
}


// seta flags de contedo
$smarty->assign("flags", $flags);

// mostra output
$smarty->display('cond_comunicado.tpl');
?>