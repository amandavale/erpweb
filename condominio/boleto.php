<?php

// inclusão de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");
require_once("../common/lib/xajax/xajax.inc.php");

require_once("../entidades/filial.php");
require_once("../entidades/movimento.php");
require_once("../entidades/funcionario.php");
require_once("../entidades/cargo_programa.php");
require_once("../entidades/boleto.php");
require_once("../entidades/cliente.php");
require_once("../entidades/cliente_condominio.php");
require_once("../entidades/parametros.php");
require_once("../admin/movimento_ajax.php");


// configurações adicionais
$conf['area'] = "2&ordf; via de Boleto"; // ï¿½ea
// inicializa templating
$smarty = new Smarty;

// configura diretórios
$smarty->template_dir = "../common/tpl";
$smarty->compile_dir = "../common/tpl_c";

// seta configurações
$smarty->assign("conf", $conf);

// ação selecionada
$flags['action'] = $_GET['ac'];
if ($flags['action'] == "")
    $flags['action'] = "listar";

// inicializa autenticação
$auth = new auth();


// cria o objeto xajax
$xajax = new xajax();

// registra todas as funções que serão usadas
$xajax->registerFunction("Emite_Boleto_Ajax");


// processa as funções
$xajax->processRequests();




// inicializa banco de dados
$db = new db();

//incializa classe para validação de formulário
$form = new form();

$filial             = new filial();
$boleto             = new boleto();
$cliente            = new cliente();
$movimento          = new movimento();
$parametros         = new parametros();
$funcionario        = new funcionario();
$cargo_programa     = new cargo_programa();
$cliente_condominio = new cliente_condominio();



$list_filial = $filial->make_list_select();
$smarty->assign("list_filial", $list_filial);

// verifica requisição de logout
if ($flags['action'] == "logout") {
    $auth->logout();
} 
else {
    // inicializa vetor de erros
    $err = array();

    // verifica sessão
    if (!$auth->check_user()) {

        // verifica requisição de login
        if ($_POST['usr_chk']) {

            // verifica login
            if (!$auth->login($_POST['usr_log'], $_POST['usr_sen'])) {
                $err = $auth->err;
            }
        } 
        else {
            $err = $auth->err;
        }
    } 
    else {
        // verifica privilï¿½ios
        // libera contedo
        $flags['okay'] = 1;
        
        
        switch ($flags['action']) {

            // ação: adicionar <<<<<<<<<<
            case "listar":
               // var_dump($_SESSION);               
                $pg = intval(trim($_GET['pg']));
                
                $Y = date('Y'); $m = date('m');
                
                $dataInicio = $form->Altera_Data("$Y-$m", '-1 Year','Y-m').'-01'; 
                $dataFim    = $form->Altera_Data("$Y-$m-01", '+2 Month','Y-m-d');
              
                $filtro = " WHERE 
                                APT.idcliente = {$_SESSION['condominio']['idcliente']} AND 
                               (APT.idmorador = {$_SESSION['usr_cod']} OR APT.idproprietario = {$_SESSION['usr_cod']}) AND
                               (MOVIM.idcliente_origem = {$_SESSION['usr_cod']} OR MOVIM.idcliente_destino = {$_SESSION['usr_cod']}) AND 
                                MOVIM.data_vencimento BETWEEN '$dataInicio' AND '$dataFim' ";

                $list_movimento = $movimento->make_list_boleto($pg, $conf['rppg'], $filtro, NULL, NULL, FALSE);


                //pega os erros
                $err = $movimento->err;

                //passa a listagem para o template
                $smarty->assign("list_movimento", $list_movimento);

            break;
        
        
            case "gerar_boleto":
     
                $idmovimento = (int)$_GET['idmovimento'];
        
                if($movimento->TemPermissaoBoleto($idmovimento, $_SESSION['usr_cod'])){
                
                    //Recuoera os dados de cedente para a geração do boleto
                    $dadosBoleto = $boleto->SetDadosCedente($idmovimento);

                    $MultaJurosDesc = $movimento->Calcula_MultaJurosDesc($_SESSION['condominio']['idcliente'],$dadosBoleto['valor_boleto'], $dadosBoleto['data_vencimento']);

                    $dadosBoleto['data_vencimento'] = $MultaJurosDesc['novo_vencimento'];
                    $dadosBoleto['valor_boleto']    = $MultaJurosDesc['valor_total'];
                    $dadosBoleto['instrucoes1']     = nl2br($MultaJurosDesc['instrucoes']);

                    $boleto->caixa($dadosBoleto); 
                    die();
                }
                else{
                    $err[] = "Visualização de boleto não autorizada. Caso isso tenha sido um erro de sistema, por favor entre em contato com a {$conf['empresa']}.";
                    $flags['okay'] = false;
                }
                
                
            break;
        }
        
    }

    // seta erros
    $smarty->assign("err", $err);
}

//Passa as funções do ajax para o tpl
$smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));

// identificador para a operação de TEF
$flags['identificacao'] = rand(0, 999999999);


// seta flags de contedo
$smarty->assign("flags", $flags);

$list_permissao = $auth->check_priv($conf['priv']);
$smarty->assign("list_permissao", $list_permissao);

// mostra output
$smarty->display("cond_boleto.tpl");
?>