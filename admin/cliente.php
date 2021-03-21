<?php

//inclusão de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/rotinas.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");
require_once("../common/lib/xajax/xajax.inc.php");

require_once("../entidades/cliente.php");

// configurações anotionais
$conf['area'] = "Clientes"; // área
//configuração de estilo
$conf['style'] = "full";

// inicializa templating
$smarty = new Smarty;

// configura diretórios
$smarty->template_dir = "../common/tpl";
$smarty->compile_dir = "../common/tpl_c";

// ação selecionada
$flags['action'] = $_GET['ac'];
if ($flags['action'] == "")
    $flags['action'] = "desbloquear_clientes";

// inicializa autenticação
$auth = new auth();

// verifica requisição de logout
if ($flags['action'] == "logout") {
    $auth->logout();
} else {

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
        } else {
            $err = $auth->err;
        }
    }

    // conteúdo
    if ($auth->check_user()) {
        // verifica privilégios
        if (!$auth->check_priv($conf['priv'])) {
            $err = $auth->err;
        } else {
            // libera conteúdo
            $flags['okay'] = 1;

            $cliente = new cliente();

            // inicializa banco de dados
            $db = new db();

            //incializa classe para validação de formulário
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

    // seta configurações
    $smarty->assign("conf", $conf);

}

$smarty->display("adm_cliente.tpl");

?>
