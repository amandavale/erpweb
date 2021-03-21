<?php

// inclus�o de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");

require_once("../entidades/saldo.php");
require_once("../entidades/filial.php");
require_once("../entidades/movimento.php");
require_once("../entidades/funcionario.php");
require_once("../entidades/cargo_programa.php");


// configura��es adicionais
$conf['area'] = "Demonstrativo Financeiro"; // �ea
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


$nomeTpl = 'cond_demonstrativo.tpl';

// inicializa autentica��o
$auth = new auth();

// inicializa banco de dados
$db = new db();

//incializa classe para valida��o de formul�rio
$form = new form();

$saldo = new saldo();
$filial = new filial();
$movimento = new movimento();
$funcionario = new funcionario();
$cargo_programa = new cargo_programa();

$list_filial = $filial->make_list_select();
$smarty->assign("list_filial", $list_filial);






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

    // contedo
    if ($auth->check_user()) {
        // verifica privil�ios
        // libera contedo
        $flags['okay'] = 1;

        // busca o dia e hora atual
        $flags['data_hora_atual'] = date('d/m/Y H:i:s');

        // Monta a listagem de anos e meses					
        $listMesAno = $form->make_list_mesAno();
        $smarty->assign("listMesAno", $listMesAno);

        //Converte os valores para inteiro por quest�es de seguran�a
        $_POST['mesBaixa'] = (int) $_POST['mesBaixa'];
        $_POST['anoBaixa'] = (int) $_POST['anoBaixa'];

        // busca os dados do relat�rio de recebimentos do vendedor
        if (!$_POST['for_chk']) {
            //Pega a data do m�s anterior para deixar pr�-selecionado no filtro de busca
            list($_POST['anoBaixa'], $_POST['mesBaixa']) = explode('-', $form->Altera_Data(date('Y-m-d'), '-1 month', $formato = 'Y-m-d'));
        }

        $ulltimoDiaMes = $form->get_LastDayMonth($_POST['mesBaixa'], $_POST['anoBaixa']);

        $data1 = $_POST['anoBaixa'] . '-' . $_POST['mesBaixa'] . '-01';
        $data2 = $_POST['anoBaixa'] . '-' . $_POST['mesBaixa'] . '-' . $ulltimoDiaMes;

        $list = array();
        $mensagem = '';
        
        //Faz a busca somente de meses anteriores ao m�s corrente.
		if( $_POST['anoBaixa'].$_POST['mesBaixa'] < date('Ym') ){
	        // lista as contas a receber das comiss�es do vendedor
	        $list = $movimento->make_list_demonstrativo($_SESSION['condominio']['idcliente'], $data1, $data2, true);
	
	        // Pega a �ltima mensagem cadastrada para o demonstrativo
	        $mensagem = $movimento->getLast_msg_demonstrativo($_SESSION['condominio']['idcliente'], $data1, $data2);
		}   
		
        //Registra as vari�veis no smarty
        $smarty->assign('list', $list);
        $smarty->assign("mensagem", $mensagem);

        //Trata a tela de impress�o do demonstrativo financeiro
        if (isset($_POST['imprimir'])) {
            
            $flags['action'] = 'demonstrativo';
            
            $nomeTpl  = 'adm_relatorio_caixa_impressao.tpl';
            
            $dados_cliente['nome_cliente'] = $_SESSION['condominio']['nome_condominio'];
            $smarty->assign('dados_cliente',$dados_cliente);
            
            $mesBaixa =str_pad($_POST['mesBaixa'], 2,'0',STR_PAD_LEFT);
            $_POST['data_baixa_de_relatorio']  = '01/'. $mesBaixa  .'/'.$_POST['anoBaixa'];;
            $_POST['data_baixa_ate_relatorio'] = $ulltimoDiaMes.'/'. $mesBaixa  .'/'.$_POST['anoBaixa'];
            
        }
    }


    // seta erros
    $smarty->assign("err", $err);
}


// identificador para a opera��o de TEF
$flags['identificacao'] = rand(0, 999999999);


// seta flags de contedo
$smarty->assign("flags", $flags);

$list_permissao = $auth->check_priv($conf['priv']);
$smarty->assign("list_permissao", $list_permissao);

// mostra output
$smarty->display($nomeTpl);
?>