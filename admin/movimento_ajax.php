<?php

//inclusão de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/rotinas.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");

require_once("../entidades/movimento.php");
require_once("../entidades/saldo.php");

// inicializa templating
$smarty = new Smarty;

// configura diretórios
$smarty->template_dir = "../common/tpl";
$smarty->compile_dir = "../common/tpl_c";

// seta configurações
$smarty->assign("conf", $conf);

// ação selecionada
$flags['action'] = $_GET['ac'];


// inicializa autenticação
$auth = new auth();


// libera conteúdo
//$flags['okay'] = 1;
//inicializa classe
$movimento = new movimento();

// inicializa banco de dados
$db = new db();

//incializa classe para validação de formulário
$form = new form();



switch ($flags['action']) {


    // busca os estados de acordo com a busca
    case "busca_movimento":

        if (isset($_GET['idmovimento']))
            $idmovimento = intval($_GET['idmovimento']);
        $movimento->Filtra_Movimento_AJAX($_GET['typing'], $_GET['campoID'], $idmovimento);

        break;
}



/*
  Função: Verifica_Campos_Movimento_AJAX
  Verifica se os campos foram preenchidos corretamente
 */

function Verifica_Campos_Movimento_AJAX($post) {

    // variáveis globais
    global $form;
    global $conf;
    global $db;
    global $falha;
    global $err;

    //---------------------
    // cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();



    //Verifica se ao menos um dos planos de conta foi setado
    if (empty($post['idplano_debito']) && empty($post['idplano_credito'])) {
        $form->err[] = "As contas de Crédito e Débito não estão preenchidas. Informe pelo menos uma delas.";
    }

    //Verifica se a conta de débito é de 3º Nível
    if (!empty($post['idplano_credito'])) {
        $aux = explode('-', $post['idplano_credito_Nome']);
        if (strlen(trim($aux[0])) < 8)
            $form->err[] = "É necessário que a Conta de Crédito seja de 3º Nível.";
    }

    //Verifica se a conta de crédito é de 3º Nível
    if (!empty($post['idplano_debito'])) {
        $aux = explode('-', $post['idplano_debito_Nome']);
        if (strlen(trim($aux[0])) < 8)
            $form->err[] = "É necessário que a Conta de Débito seja de 3º Nível.";
    }


    if ($_GET['ac'] == "editar") {

        //Verifica se os campos obrigatórios estão preenchidos			
        $form->chk_empty($post['litdescricao_movimento'], 1, 'Descrição');
        $form->chk_empty($post['numvalor_movimento'], 1, 'Valor');
        $form->chk_IsDate($post['litdata_movimento'], "Data de Ocorrência do Movimento");
        $form->chk_IsDate($post['litdata_vencimento'], "Data de Vencimento");
        $form->chk_moeda($post['numvalor_juros'], 1, "Valor Juros");
        $form->chk_moeda($post['numvalor_multa'], 1, "Valor Multa");

        if ($post['litbaixado'] == "1") {
            //$form->chk_empty($post['valor_pago'], 1, 'Valor pago (R$)'); 


            $form->chk_IsDate($post['data_baixa_D'], "Data da Baixa");
            $form->chk_IsHour($post['data_baixa_H']);
        }
    } else {


        $form->chk_empty($post['descricao_movimento'], 1, 'Descrição');
        $form->chk_empty($post['valor_movimento'], 1, 'Valor');
        $form->chk_IsDate($post['data_movimento'], "Data de Ocorrência do Movimento");
        $form->chk_IsDate($post['data_vencimento'], "Data de Vencimento");

        if ($post['baixado'] == "1") {
            $form->chk_IsDate($post['data_baixa_D'], "Data da Baixa");
            $form->chk_IsHour($post['data_baixa_H']);
        }
    }

    $form->chk_valor($post['numvalor_movimento'], false);

    $err = $form->err;

    // se nao houveram erros, da o submit no form
    if (count($err) == 0) {

        $objResponse->addScript("document.getElementById('for').submit();");
    }
    // houve erros, logo mostra-os
    else {
        $mensagem = implode("\n", $err);
        $objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
    }

    // retorna o resultado XML
    return $objResponse->getXML();
}

/*
  Função: Verifica_Campos_Movimento_AJAX
  Verifica se os campos foram preenchidos corretamente
 */

function Emite_Boleto_Ajax($post, $idmovimento) {

    // variáveis globais
    global $form, $conf, $db, $falha, $err;


    // cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();


    if (is_numeric($idmovimento))
        $destino = $_SERVER['PHP_SELF'] . '?ac=gerar_boleto&banco=' . $post['banco'] . '&idmovimento=' . $idmovimento;
    elseif ($idmovimento == 'full')
        $destino = $_SERVER['PHP_SELF'] . '?ac=gerar_boleto_full&banco=' . $post['banco'];

    $objResponse->addAssign('for', 'target', '_blank');
    $objResponse->addAssign('for', 'action', $destino);

    $objResponse->addScript("document.getElementById('for').submit();");

    $objResponse->addAssign('for', 'target', '');
    $objResponse->addAssign('for', 'action', $_SERVER['PHP_SELF'] . '?ac=listar&boletos=1');

    // retorna o resultado XML
    return $objResponse->getXML();
}

function Verifica_Campos_Busca_Caixa_AJAX($post) {

    // variáveis globais
    global $form;
    global $conf;
    global $db;
    global $falha;
    global $err;
    //---------------------
    // cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();


    $form->chk_empty($post['idcliente'], 1, 'Cliente');

    if (!empty($post['data_vencimento_de']))
        $form->chk_IsDate($post['data_vencimento_de'], "Data de vencimento inicial");
    if (!empty($post['data_vencimento_ate']))
        $form->chk_IsDate($post['data_vencimento_ate'], "Data de vencimento final");
    //$form->chk_IsDate($post['data_baixa_de'], "Data de Movimento inicial");
    //$form->chk_IsDate($post['data_baixa_ate'], "Data de Movimento final");


    if (!empty($post['idplano_ini']) || !empty($post['idplano_fim'])) {

        $form->chk_empty($post['idplano_ini'], true, "Plano de Contas inicial");
        $form->chk_empty($post['idplano_fim'], true, "Plano de Contas final");
    }

    $err = $form->err;



    // se nao houveram erros, da o submit no form	
    if (count($err) == 0) {

        $objResponse->addScript("document.getElementById('for').submit();");
    }// houve erros, logo mostra-os
    else {
        $mensagem = implode("\n", $err);
        $objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
    }

    // retorna o resultado XML
    return $objResponse->getXML();
}

function Atualiza_Saldo_Cliente_AJAX($post) {

    global $form, $conf, $db, $falha, $err; //, $saldo;

    $saldo = new saldo();
    $objResponse = new xajaxResponse();



    if ($post['idcliente'] == '')
        $objResponse->addAlert("Por favor, informe o cliente.");
    else {

        $atualizacao = $saldo->atualizaSaldo(date('Y-m-d'), (int) $post['idcliente']);


        if ($atualizacao === true)
            $mensagem = utf8_encode("Saldo atualizado com sucesso!");
        else
            $mensagem = utf8_encode("Falha ao atulizar os saldos. Por favor entre em contato com a F6 Sistemas.");


        $objResponse->addAlert('Saldo Atualizado com Sucesso!');

        if ($post['data_baixa_de'] != '' && $post['data_baixa_ate'] != '')
            $objResponse->addScript("document.getElementById('for').submit();");
    }


    // retorna o resultado XML
    return $objResponse->getXML();
}

function Verifica_Campos_Baixa_AJAX($post) {

    global $form, $conf, $db, $falha, $err; //, $saldo;

    $objResponse = new xajaxResponse();


    if (!count($post['baixar']))
        $form->err[] = "Por favor, selecione ao menos uma conta.";

    $form->chk_IsDate($post['data_baixa_D'], "Data da Baixa");
    $form->chk_IsHour($post['data_baixa_H'], "Hora da Baixa");

    $err = $form->err;


    if (!count($err)) {
        $objResponse->addScript("document.getElementById('for_baixa').submit();");
    } else {

        $mensagem = implode("\n", $err);
        $objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
    }

    // retorna o resultado XML
    return $objResponse->getXML();
}

// seta erros
$smarty->assign("err", $err);
$smarty->assign("form", $form);
//$smarty->assign("flags", $flags);
?>