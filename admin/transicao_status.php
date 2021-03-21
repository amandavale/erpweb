<?php

//inclusão de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/rotinas.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");

require_once("../entidades/transicao_status.php");
require_once("../entidades/funcionario.php");
require_once("../entidades/status_os_programacao.php");
require_once("../entidades/ordem_servico.php");



// configurações anotionais
$conf['area'] = "Transição de Status"; // área
$conf['priv'] = array($conf['pri_adm']); // privilégios requeridos
//configuração de estilo
$conf['style'] = "full";

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

            //inicializa classe
            $transicao_status = new transicao_status();

            $funcionario = new funcionario();
            $status_os_programacao = new status_os_programacao();
            $ordem_servico = new ordem_servico();


            // inicializa banco de dados
            $db = new db();

            //incializa classe para validação de formulário
            $form = new form();



            switch ($flags['action']) {

                // ação: adicionar <<<<<<<<<<
                case "adicionar":

                    if ($_POST['for_chk']) {

                        $err = $form->err;

                        if (count($err) == 0) {

                            $_POST['observacao_transicao']  = nl2br($_POST['observacao_transicao']);
                            $_POST['data_programacao']      = $form->FormataDataParaInserir($_POST['data_programacao']);
                            $_POST['data_hora_transicao_D'] = $form->FormataDataParaInserir($_POST['data_hora_transicao_D']);
                            $_POST['data_hora_transicao']   = $_POST['data_hora_transicao_D'] . " " . $_POST['data_hora_transicao_H'];


                            //grava o registro no banco de dados
                            $transicao_status->set($_POST);


                            //obtém os erros que ocorreram no cadastro
                            $err = $transicao_status->err;

                            //se não ocorreram erros
                            if (count($err) == 0) {
                                $flags['sucesso'] = $conf['inserir'];

                                //limpa o $flags.action para que seja exibida a listagem
                                $flags['action'] = "listar";

                                //lista
                                $list = $transicao_status->make_list(0, $conf['rppg']);

                                //pega os erros
                                $err = $transicao_status->err;

                                //envia a listagem para o template
                                $smarty->assign("list", $list);
                            }
                        }
                    }

                    $list_funcionario = $funcionario->make_list_select();
                    $smarty->assign("list_funcionario", $list_funcionario);

                    $list_status_os_programacao = $status_os_programacao->make_list_select();
                                        
                    $smarty->assign("list_status_os_programacao", $list_status_os_programacao);

                    $list_ordem_servico = $ordem_servico->make_list_select();
                    $smarty->assign("list_ordem_servico", $list_ordem_servico);



                break;


                //listagem dos registros
                case "listar":

                    //obtém qual página da listagem deseja exibir
                    $pg = intval(trim($_GET['pg']));

                    //se não foi passada a página como parâmetro, faz página default igual à página 0
                    if (!$pg) $pg = 0;

                    //lista os registros
                    $list = $transicao_status->make_list($pg, $conf['rppg']);

                    //pega os erros
                    $err = $transicao_status->err;

                    //passa a listagem para o template
                    $smarty->assign("list", $list);

                    break;


                // ação: editar <<<<<<<<<<
                case "editar":

                    if ($_POST['for_chk']) {


                        $info = $_POST;

                        $info['idtransicao_status'] = $_GET['idtransicao_status'];

                        $err = $form->err;

                        if (count($err) == 0) {

                            $_POST['litobservacao_transicao'] = nl2br($_POST['litobservacao_transicao']);
                            $_POST['litdata_programacao']     = $form->FormataDataParaInserir($_POST['litdata_programacao']);
                            $_POST['data_hora_transicao_D']   = $form->FormataDataParaInserir($_POST['data_hora_transicao_D']);
                            $_POST['litdata_hora_transicao']  = $_POST['data_hora_transicao_D'] . " " . $_POST['data_hora_transicao_H'];


                            $transicao_status->update($_GET['idtransicao_status'], $_POST);

                            //obtém erros
                            $err = $transicao_status->err;

                            //se não ocorreram erros
                            if (count($err) == 0) {
                                $flags['sucesso'] = $conf['alterar'];

                                //limpa o $flags.action para que seja exibida a listagem
                                $flags['action'] = "listar";

                                //lista
                                $list = $transicao_status->make_list(0, $conf['rppg']);

                                //pega os erros
                                $err = $transicao_status->err;

                                //envia a listagem para o template
                                $smarty->assign("list", $list);
                            }
                        }
                    } else {

                        //busca detalhes
                        $info = $transicao_status->getById($_GET['idtransicao_status']);

                        //tratamento das informações para fazer o UPDATE
                        $info['numidfuncionario'] = $info['idfuncionario'];
                        $info['numidstatus'] = $info['idstatus'];
                        $info['numidordem_servico'] = $info['idordem_servico'];
                        $info['litdata_hora_transicao'] = $info['data_hora_transicao'];
                        $info['litobservacao_transicao'] = strip_tags($info['observacao_transicao']);
                        $info['litdata_programacao'] = $info['data_programacao'];
                        $info['litmotivo_programacao'] = $info['motivo_programacao'];




                        //obtém os erros
                        $err = $transicao_status->err;
                    }

                    $list_funcionario = $funcionario->make_list_select();
                    $smarty->assign("list_funcionario", $list_funcionario);

                    $list_status_os_programacao = $status_os_programacao->make_list_select();
                    $smarty->assign("list_status_os_programacao", $list_status_os_programacao);

                    $list_ordem_servico = $ordem_servico->make_list_select();
                    $smarty->assign("list_ordem_servico", $list_ordem_servico);






                    //passa os dados para o template
                    $smarty->assign("info", $info);

                    break;



                // deleta um registro do sistema
                case "excluir":

                    //verifica se foi pedido a deleção
                    if ($_POST['for_chk']) {

                        // deleta o registro
                        $transicao_status->delete($_GET['idtransicao_status']);

                        //obtém erros
                        $err = $transicao_status->err;

                        //se não ocorreram erros
                        if (count($err) == 0) {
                            $flags['sucesso'] = $conf['excluir'];
                        }

                        //limpa o $flags.action para que seja exibida a listagem
                        $flags['action'] = "listar";

                        //lista registros
                        $list = $transicao_status->make_list(0, $conf['rppg']);

                        //pega os erros
                        $err = $transicao_status->err;

                        //envia a listagem para o template
                        $smarty->assign("list", $list);
                    }

                    break;
            }
        }
    }

    // seta erros
    $smarty->assign("err", $err);
}

$smarty->assign("form", $form);
$smarty->assign("flags", $flags);

$smarty->display("adm_transicao_status.tpl");
?>

