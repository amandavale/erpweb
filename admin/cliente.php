<?php

//inclus�o de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/rotinas.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");
require_once("../common/lib/xajax/xajax.inc.php");

require_once("../entidades/cliente.php");

// configura��es anotionais
$conf['area'] = "Clientes"; // �rea
//configura��o de estilo
$conf['style'] = "full";

// inicializa templating
$smarty = new Smarty;

// configura diret�rios
$smarty->template_dir = "../common/tpl";
$smarty->compile_dir = "../common/tpl_c";

// a��o selecionada
$flags['action'] = $_GET['ac'];
if ($flags['action'] == "")
    $flags['action'] = "desbloquear_clientes";

// inicializa autentica��o
$auth = new auth();

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
            $err = $auth->err;
        }
    }

    // conte�do
    if ($auth->check_user()) {
        // verifica privil�gios
        if (!$auth->check_priv($conf['priv'])) {
            $err = $auth->err;
        } else {
            // libera conte�do
            $flags['okay'] = 1;

            $cliente = new cliente();

            // inicializa banco de dados
            $db = new db();

            //incializa classe para valida��o de formul�rio
            $form = new form();

            $list = $auth->check_priv($conf['priv']);
            $aux = $flags['action'];
            if ($list[$aux] != '1' && $_SESSION['usr_cargo'] != "") {
                $err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"" . $conf['addr'] . "/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial.";
                $flags['action'] = "";
            }

            switch ($flags['action']) {

                case "desbloquear_clientes":

                    $conf['area'] = "Clientes Bloqueados";

                    if (isset($_POST['for_chk']) && $_POST['for_chk']) {

                        if(isset($_POST['desbloquear_clientes']) && !empty($_POST['desbloquear_clientes'])){

                            $clientes_desbloquear = array_keys($_POST['desbloquear_clientes']);

                            if(!empty($clientes_desbloquear)){

                                $resultado_desbloqueio = $cliente->desbloquearClientes($clientes_desbloquear);

                                if(!$resultado_desbloqueio){
                                    $err[] = 'Houve um erro ao desbloquear clientes.';
                                }
                                else{
                                    if(sizeof($clientes_desbloquear) == 1){
                                        $flags['sucesso'] = 'Cliente desbloqueado com sucesso!';
                                    }
                                    else{
                                        $flags['sucesso'] = 'Clientes desbloqueados com sucesso!';
                                    }
                                }
                            }
                        }
                        else{
                            $err[] = 'Selecione pelo menos um cliente.';
                        }
                    }

                    $clientes_bloqueados = $cliente->listarClientesBloqueados();
                    $smarty->assign("clientes_bloqueados", $clientes_bloqueados);

                    break;
            }
        }
    }

    // seta erros
    $smarty->assign("err", $err);

    $smarty->assign("flags", $flags);

    // seta configura��es
    $smarty->assign("conf", $conf);

}

$smarty->display("adm_cliente.tpl");

?>
