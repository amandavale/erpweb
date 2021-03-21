<?php

//inclusão de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/rotinas.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");
require_once("../common/lib/xajax/xajax.inc.php");

require_once("../entidades/cliente_condominio.php");
require_once("../entidades/cliente.php");
require_once("../entidades/ramo_atividade.php");
require_once("../entidades/motivo_bloqueio.php");
require_once("../entidades/endereco.php");
require_once("../entidades/bairro.php");
require_once("../entidades/estado.php");
require_once("../entidades/banco.php");
require_once("../entidades/apartamento.php");
require_once("../entidades/parametros.php");

require_once("cliente_ajax.php");


// configurações anotionais
$conf['area'] = "Cliente Condomínio"; // área
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

// cria o objeto xajax
$xajax = new xajax();

// registra todas as funções que serão usadas
$xajax->registerFunction("Verifica_Campos_ClienteCondominio_AJAX");

// processa as funções
$xajax->processRequests();


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
            $cliente_condominio = new cliente_condominio();

            $ramo_atividade = new ramo_atividade();
            $motivo_bloqueio = new motivo_bloqueio();
            $cliente = new cliente();
            $endereco = new endereco();
            $bairro = new bairro();
            $estado = new estado();
            $banco = new banco();
            $apartamento = new apartamento();
            $parametros = new parametros();

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

                // busca genérica
                case "busca_generica":

                    if (($_POST['for_chk']) || ($_GET['rpp'] != "")) {

                        $flags['fez_busca'] = 1;

                        if ($_POST['for_chk']) {
                            $flags['busca'] = $_POST['busca'];
                            $flags['rpp'] = $_POST['rpp'];
                        } else {
                            $flags['busca'] = $_GET['busca'];
                            $flags['rpp'] = $_GET['rpp'];
                        }

                        if ($_GET['target'] == "full")
                            $flags['rpp'] = 9999999;


                        //obtém qual página da listagem deseja exibir
                        $pg = intval(trim($_GET['pg']));

                        //se não foi passada a página como parâmetro, faz página default igual à página 0
                        if (!$pg)
                            $pg = 0;

                        //lista os registros
                        $list = $cliente_condominio->Busca_Generica($pg, $flags['rpp'], $flags['busca'], "", "ac=busca_generica&busca=" . $flags['busca'] . "&rpp=" . $flags['rpp']);

                        //pega os erros
                        $err = $cliente_condominio->err;

                        //passa a listagem para o template
                        $smarty->assign("list", $list);
                    }

                    if ($flags['rpp'] == "")
                        $flags['rpp'] = $conf['rppg'];

                    break;


                // busca parametrizada
                case "busca_parametrizada":

                    if (($_POST['for_chk']) || ($_GET['rpp'] != "")) {

                        $flags['fez_busca'] = 1;

                        if ($_POST['for_apto'])
                            $filtro_where = 'CLI.idcliente = ' . (int) $_POST['idcliente'];
                        else { // Se a busca não for de um cliente em específico, monta os filtros de busca
                            if ($_POST['for_chk']) {
                                $flags['cliente'] = $_POST['cliente'];
                                $flags['ramo_atividade'] = $_POST['ramo_atividade'];
                                $flags['email_cliente'] = $_POST['email_cliente'];
                                $flags['tipo_cliente'] = $_POST['tipo_cliente'];
                                $flags['admFinanceira'] = isset($_POST['admFinanceira'])?$_POST['admFinanceira']:'1';
                                $flags['rpp'] = $_POST['rpp'];

                            } else {
                                $flags['cliente'] = $_GET['cliente'];
                                $flags['ramo_atividade'] = $_GET['ramo_atividade'];
                                $flags['email_cliente'] = $_GET['email_cliente'];
                                $flags['tipo_cliente'] = $_GET['tipo_cliente'];
                                $flags['admFinanceira'] = $_GET['admFinanceira'];
                                $flags['rpp'] = $_GET['rpp'];
                            }

                            $parametros_get = "&cliente=" . $flags['cliente'] .
                                    "&ramo_atividade=" . $flags['ramo_atividade'] .
                                    "&email_cliente=" . $flags['email_cliente'] .
                                    "&tipo_cliente=" . $flags['tipo_cliente'] .
                                    "&admFinanceira=" . $flags['admFinanceira'];


                            $filtro_where = "";
                            if ($flags['cliente'] != "")
                                $filtro_where .= " UPPER(CLI.nome_cliente) LIKE UPPER('%" . $flags['cliente'] . "%') AND ";
                            if ($flags['ramo_atividade'] != "")
                                $filtro_where .= " ( (UPPER(RAT.descricao_atividade) LIKE UPPER('%" . $flags['ramo_atividade'] . "%'))) AND ";
                            if ($flags['email_cliente'] != "")
                                $filtro_where .= " UPPER(CLI.email_cliente) LIKE UPPER('%" . $flags['email_cliente'] . "%') AND ";
                            if ($flags['tipo_cliente'] != "")
                                $filtro_where .= " UPPER(CLI.tipo_cliente) LIKE UPPER('%" . $flags['tipo_cliente'] . "%') AND ";
                            if ($flags['admFinanceira'] != '0'){
                                $filtro_where .= "CCLI.admFinanceira = 'S'  AND ";
                            }

                            $filtro_where = substr($filtro_where, 0, strlen($filtro_where) - 4);
                        }


                        if ($_GET['target'] == "full")
                            $flags['rpp'] = 9999999;

                        //obtém qual página da listagem deseja exibir
                        $pg = intval(trim($_GET['pg']));

                        //se não foi passada a página como parâmetro, faz página default igual à página 0
                        if (!$pg)
                            $pg = 0;

                        //lista os registros
                        $list = $cliente_condominio->Busca_Parametrizada($pg, $flags['rpp'], $filtro_where, "", "ac=busca_parametrizada$parametros_get&rpp=" . $flags['rpp']);

                        //Recupera os apartamentos
                        $list = $cliente_condominio->get_apartamentos($list, null, "ac=busca_parametrizada$parametros_get&rpp=" . $flags['rpp']);

                        //pega os erros
                        $err = $cliente_condominio->err;

                        if(isset($_POST['includeCpf'])){
                            $smarty->assign("includeCpf", true);
                        }
                        else{
                            $smarty->assign("includeCpf", false);
                        }

                        //passa a listagem para o template
                        $smarty->assign("list", $list);
                    }

                    if ($flags['rpp'] == "")
                        $flags['rpp'] = $conf['rppg'];

                    break;


                // ação: adicionar <<<<<<<<<<
                case "adicionar":

                    if ($_POST['for_chk']) {

                        $form->chk_empty($_POST['nome_cliente'], 1, 'Nome do cliente');
                        $form->chk_cnpj($_POST['cnpj'], 0);
                        $form->chk_empty($_POST['idramo_atividade'], 0, 'Ramo de atividade');
 
                        $err = $form->err;

                        // verifica se o CNPJ do cliente está duplicado
                        if ($_POST['cnpj'] != '') {
                            $_POST['cnpj'] = $form->FormataCNPJParaInserir($_POST['cnpj']);
                            if ($cliente_condominio->Verifica_CNPJ_Duplicado($_POST['cnpj']))
                                $err[] = "Este CNPJ já existe e não pode ser duplicado!";
                        }


                        if (count($err) == 0) {

                            $_POST['observacao_cliente'] = nl2br($_POST['observacao_cliente']);

                            $_POST['data_bloqueio_cliente']   = $form->FormataDataParaInserir($_POST['data_bloqueio_cliente']);
                            $_POST['data_cadastro_cliente']   = $form->FormataDataParaInserir($_POST['data_cadastro_cliente']);
                            $_POST['telefone_cliente']        = $form->FormataTelefoneParaInserir($_POST['telefone_cliente_ddd'], $_POST['telefone_cliente']);
                            $_POST['fax_cliente']             = $form->FormataTelefoneParaInserir($_POST['fax_cliente_ddd'], $_POST['fax_cliente']);
                            $_POST['telefone_cobranca']       = $form->FormataTelefoneParaInserir($_POST['telefone_cobranca_ddd'], $_POST['telefone_cobranca']);
                            $_POST['data_nascimento_contato'] = $form->FormataDataParaInserir($_POST['data_nascimento_contato']);
                            $_POST['celular_contato']         = $form->FormataTelefoneParaInserir($_POST['celular_contato_ddd'], $_POST['celular_contato']);
                            $_POST['valor_contrato_cliente']  = $form->formataMoedaParaInserir($_POST['valor_contrato_cliente']);


                            if ($_POST['taxa_condominio'] == "")
                                $_POST['taxa_condominio'] = "NULL";
                            else
                                $_POST['taxa_condominio'] = $form->formataMoedaParaInserir($_POST['taxa_condominio']);

                            if ($_POST['sugestaoReserva'] == "")
                                $_POST['sugestaoReserva'] = "NULL";
                            else
                                $_POST['sugestaoReserva'] = $form->formataMoedaParaInserir($_POST['sugestaoReserva']);

                            if ($_POST['valAdm'] == "")
                                $_POST['valAdm'] = "NULL";
                            else
                                $_POST['valAdm'] = $form->formataMoedaParaInserir($_POST['valAdm']);

                            if ($_POST['valFaxina'] == "")
                                $_POST['valFaxina'] = "NULL";
                            else
                                $_POST['valFaxina'] = $form->formataMoedaParaInserir($_POST['valFaxina']);

                            if ($_POST['valVigia'] == "")
                                $_POST['valVigia'] = "NULL";
                            else
                                $_POST['valVigia'] = $form->formataMoedaParaInserir($_POST['valVigia']);

                            if ($_POST['valor_contrato_cliente'] == "")
                                $_POST['valor_contrato_cliente'] = "NULL";
                            else
                                $_POST['valor_contrato_cliente'] = $form->formataMoedaParaInserir($_POST['valor_contrato_cliente']);


                            $_POST['multa_boleto']    = empty($_POST['multa_boleto'])    ? 'NULL' : $form->formataMoedaParaInserir($_POST['multa_boleto']);
                            $_POST['juros_boleto']    = empty($_POST['juros_boleto'])    ? 'NULL' : $form->formataMoedaParaInserir($_POST['juros_boleto']);
                            $_POST['desconto_boleto'] = empty($_POST['desconto_boleto']) ? 'NULL' : $form->formataMoedaParaInserir($_POST['desconto_boleto']);
                            

                            if ($_POST['idmotivo_bloqueio'] == "")
                                $_POST['idmotivo_bloqueio'] = "NULL";
                            if ($_POST['agencia'] == "")
                                $_POST['agencia'] = "NULL";
                            if ($_POST['agenciaDigito'] == "")
                                $_POST['agenciaDigito'] = "NULL";
                            if ($_POST['conta'] == "")
                                $_POST['conta'] = "NULL";
                            if ($_POST['contaDigito'] == "")
                                $_POST['contaDigito'] = "NULL";
                            if ($_POST['numeroContrato'] == "")
                                $_POST['numeroContrato'] = "NULL";
                            if ($_POST['idbanco'] == "")
                                $_POST['idbanco'] = "NULL";


                            // Grava o registro do endereço no Banco de Dados
                            $_POST['idendereco_cliente'] = $endereco->InsereEndereco($_POST, "cliente");

                            // Grava o registro do endereço no Banco de Dados
                            $_POST['idendereco_cobranca'] = $endereco->InsereEndereco($_POST, "cobranca");


                            //grava o registro no banco de dados
                            $_POST['idcliente'] = $cliente->set($_POST);

                            //grava o registro no banco de dados
                            $cliente_condominio->set($_POST);


                            //obtém os erros que ocorreram no cadastro
                            $err = $cliente_condominio->err;

                            //se não ocorreram erros
                            if (count($err) == 0) {
                                $flags['sucesso'] = $conf['inserir'];

                                //limpa o $flags.action para que seja exibida a listagem
                                $flags['action'] = "listar";

                                //lista
                                $list = $cliente_condominio->make_list(0, $conf['rppg']);

                                //pega os erros
                                $err = $cliente_condominio->err;

                                //envia a listagem para o template
                                $smarty->assign("list", $list);
                            }
                        }
                    }

                    $list_ramo_atividade = $ramo_atividade->make_list_select();
                    $smarty->assign("list_ramo_atividade", $list_ramo_atividade);

                    $list_motivo_bloqueio = $motivo_bloqueio->make_list_select();
                    $smarty->assign("list_motivo_bloqueio", $list_motivo_bloqueio);

                    $list_banco = $banco->make_list_select();
                    $smarty->assign("list_banco", $list_banco);



                    break;


                //listagem dos registros
                case "listar":

                    //obtém qual página da listagem deseja exibir
                    $pg = intval(trim($_GET['pg']));

                    //se não foi passada a página como parâmetro, faz página default igual à página 0
                    if (!$pg)
                        $pg = 0;

                    //lista os registros
                    $list = $cliente_condominio->make_list($pg, $conf['rppg']);

                    //pega os erros
                    $err = $cliente_condominio->err;

                    //passa a listagem para o template
                    $smarty->assign("list", $list);

                    break;


                // ação: editar <<<<<<<<<<
                case "editar":

                    if ($_POST['for_chk']) {

                        $info = $_POST;

                        $info['idcliente'] = intval($_GET['idcliente']);


                        $form->chk_empty($_POST['litnome_cliente'], 1, 'Nome do cliente');
                        if ($_POST['litcnpj'] != '')
                            $form->chk_cnpj($_POST['litcnpj'], 1);
                        $form->chk_empty($_POST['numidramo_atividade'], 0, 'Ramo de atividade');

                        $err = $form->err;


                        // verifica se o CNPJ do cliente está duplicado
                        if ($_POST['litcnpj'] != '') {
                            $_POST['litcnpj'] = $form->FormataCNPJParaInserir($_POST['litcnpj']);
                            $info['litcnpj'] = $_POST['litcnpj'];
                            if ($cliente_condominio->Verifica_CNPJ_Duplicado($_POST['litcnpj'], $_GET['idcliente']))
                                $err[] = "Este CNPJ já existe e não pode ser duplicado!";
                        }


                        if (count($err) == 0) {

                            
                            //***************** Formatação de campos para inserir no banco de dados **************************//
                            
                            $_POST['litobservacao_cliente'] = nl2br($_POST['litobservacao_cliente']);
                            
                            $_POST['litdata_bloqueio_cliente']  = empty($_POST['litdata_bloqueio_cliente']) ? NULL : $form->FormataDataParaInserir($_POST['litdata_bloqueio_cliente']);
                            $_POST['litdata_cadastro_cliente']  = empty($_POST['litdata_cadastro_cliente']) ? NULL : $form->FormataDataParaInserir($_POST['litdata_cadastro_cliente']);
                            
                            $_POST['littelefone_cliente']       = $form->FormataTelefoneParaInserir($_POST['telefone_cliente_ddd'], $_POST['littelefone_cliente']);
                            $_POST['litfax_cliente']            = $form->FormataTelefoneParaInserir($_POST['fax_cliente_ddd'], $_POST['litfax_cliente']);
                            $_POST['littelefone_cobranca']      = $form->FormataTelefoneParaInserir($_POST['telefone_cobranca_ddd'], $_POST['littelefone_cobranca']);

                            $_POST['numtaxa_condominio']        = $form->formataMoedaParaInserir($_POST['numtaxa_condominio']);
                            $_POST['numsugestaoReserva']        = $form->formataMoedaParaInserir($_POST['numsugestaoReserva']);
                            $_POST['numvalAdm']                 = $form->formataMoedaParaInserir($_POST['numvalAdm']);
                            $_POST['numvalFaxina']              = $form->formataMoedaParaInserir($_POST['numvalFaxina']);
                            $_POST['numvalVigia']               = $form->formataMoedaParaInserir($_POST['numvalVigia']);
                            $_POST['numvalor_contrato_cliente'] = $form->FormataMoedaParaInserir($_POST['numvalor_contrato_cliente']);
                            $_POST['numdesconto_boleto']        = $form->FormataMoedaParaInserir($_POST['numdesconto_boleto']);
                            $_POST['nummulta_boleto']           = $form->FormataMoedaParaInserir($_POST['nummulta_boleto']);
                            $_POST['numjuros_boleto']           = $form->FormataMoedaParaInserir($_POST['numjuros_boleto']);
                            
                            //****************************************************************************************************//   

                            
                            
                            
                            //Inicia a transação no banco de dados
                            $db->query('BEGIN');
                            
                            //Faz o UPDATE das tabelas em sequência

                            // atualiza os dados do endereço
                            if (!empty($_POST['idendereco_cliente'])){
                                $endereco->AtualizaEndereco(intval($_POST['idendereco_cliente']), $_POST, "cliente");
                            }
                            elseif($_POST['cliente_logradouro']){
                                $_POST['numidendereco_cliente'] = $endereco->InsereEndereco($_POST, "cliente");
                            }

                            // atualiza os dados do endereço
                            if (!empty($_POST['idendereco_cobranca'])){
                                $endereco->AtualizaEndereco(intval($_POST['idendereco_cobranca']), $_POST, "cobranca");
                            }
                            elseif($_POST['cobranca_logradouro']){
                                // Grava o registro do endereço no Banco de Dados
                                $_POST['numidendereco_cobranca'] = $endereco->InsereEndereco($_POST, "cobranca");
                            }

                            $cliente->AtualizaCliente($_GET['idcliente'], $_POST);
                            $cliente_condominio->AtualizaClienteCondominio($_GET['idcliente'], $_POST);


                            //obtém erros
                            $err = $cliente_condominio->err;

                            //se não ocorreram erros
                            if (count($err) == 0) {
                                //Finaliza a transação do banco de dados
                                $db->query('COMMIT');
                                
                                $flags['sucesso'] = $conf['alterar'];

                                //limpa o $flags.action para que seja exibida a listagem
                                $flags['action'] = "listar";

                                //lista
                                $list = $cliente_condominio->make_list(0, $conf['rppg']);

                                //pega os erros
                                $err = $cliente_condominio->err;

                                //envia a listagem para o template
                                $smarty->assign("list", $list);
                            }
                            else{ // Tratamento de falha ao salvar
                                
                                //Cancela a transação com o banco de dados
                                $db->query('ROLLBACK');
                                
                                //Exibe mensagem de falha
                                $err = $falha['alterar'];
                                
                            }
                            
                        }
                    } else {

                        //busca detalhes
                        $info = $cliente_condominio->getById($_GET['idcliente']);

                        //tratamento das informações para fazer o UPDATE
                        $info['numidcliente'] = $info['idcliente'];
                        $info['litcnpj'] = $info['cnpj'];
                        $info['litadmFinanceira'] = $info['admFinanceira '];
                        $info['numsugestaoReserva'] = $info['sugestaoReserva'];
                        $info['numvalAdm'] = $info['valAdm'];
                        $info['numvalFaxina'] = $info['valFaxina'];
                        $info['numvalVigia'] = $info['valVigia'];
                        $info['litemissaoPropria'] = $info['emissaoPropria'];
                        $info['numidbanco'] = $info['idbanco'];
                        $info['numagencia'] = $info['agencia'];
                        $info['numagenciaDigito'] = $info['agenciaDigito'];
                        $info['numconta'] = $info['conta'];
                        $info['numcontaDigito'] = $info['contaDigito'];
                        $info['numnumeroContrato'] = $info['numeroContrato'];
                        $info['numcondominio_caixa'] = $info['condominio_caixa'];

                        $info_condominio_caixa = $cliente->getById($info['condominio_caixa']);

                        $info['numcondominio_caixa_Nome'] = $info_condominio_caixa['nome_cliente'];
                        $info['numcondominio_caixa_NomeTemp'] = $info_condominio_caixa['nome_cliente'];


                        //busca detalhes
                        $info_cliente = $cliente->getById($_GET['idcliente']);

                        //tratamento das informações para fazer o UPDATE
                        $info['litnome_cliente'] = $info_cliente['nome_cliente'];
                        $info['numidramo_atividade'] = $info_cliente['idramo_atividade'];
                        $info['littelefone_cliente'] = $info_cliente['telefone_cliente'];
                        $info['litfax_cliente'] = $info_cliente['fax_cliente'];
                        $info['litemail_cliente'] = $info_cliente['email_cliente'];
                        $info['litsite_cliente'] = $info_cliente['site_cliente'];
                        $info['litcliente_bloqueado'] = $info_cliente['cliente_bloqueado'];
                        $info['numidmotivo_bloqueio'] = $info_cliente['idmotivo_bloqueio'];
                        $info['litdata_bloqueio_cliente'] = $info_cliente['data_bloqueio_cliente'];
                        $info['numidendereco_cliente'] = $info_cliente['idendereco_cliente'];
                        $info['litmesmo_endereco'] = $info_cliente['mesmo_endereco'];
                        $info['numidendereco_cobranca'] = $info_cliente['idendereco_cobranca'];
                        $info['littelefone_cobranca'] = $info_cliente['telefone_cobranca'];
                        $info['litobservacao_cliente'] = strip_tags($info_cliente['observacao_cliente']);
                        $info['numvalor_contrato_cliente'] = $info_cliente['valor_contrato_cliente'];
                        $info['litdata_cadastro_cliente'] = $info_cliente['data_cadastro_cliente'];
                        $info['litconsumidor_final'] = $info_cliente['consumidor_final'];
                        $info['littipo_cliente'] = $info_cliente['tipo_cliente'];
                        $info['idendereco_cliente'] = $info_cliente['idendereco_cliente'];
                        $info['idendereco_cobranca'] = $info_cliente['idendereco_cobranca'];

                        // Busca os dados do endereço
                        $dados_endereco = $endereco->BuscaDadosEndereco($info_cliente['idendereco_cliente'], $info, "cliente");

                        $info['cliente_idestado_Nome'] = $dados_endereco['nome_estado'];
                        $info['cliente_idestado_NomeTemp'] = $dados_endereco['nome_estado'];

                        $info['cliente_idcidade_Nome'] = $dados_endereco['nome_cidade'];
                        $info['cliente_idcidade_NomeTemp'] = $dados_endereco['nome_cidade'];

                        $info['cliente_idbairro_Nome'] = $dados_endereco['nome_bairro'];
                        $info['cliente_idbairro_NomeTemp'] = $dados_endereco['nome_bairro'];


                        // Busca os dados do endereço
                        $dados_endereco = $endereco->BuscaDadosEndereco($info_cliente['idendereco_cobranca'], $info, "cobranca");

                        $info['cobranca_idestado_Nome'] = $dados_endereco['nome_estado'];
                        $info['cobranca_idestado_NomeTemp'] = $dados_endereco['nome_estado'];

                        $info['cobranca_idcidade_Nome'] = $dados_endereco['nome_cidade'];
                        $info['cobranca_idcidade_NomeTemp'] = $dados_endereco['nome_cidade'];

                        $info['cobranca_idbairro_Nome'] = $dados_endereco['nome_bairro'];
                        $info['cobranca_idbairro_NomeTemp'] = $dados_endereco['nome_bairro'];



                        if (strlen($info['celular_contato']) == 10) {
                            $info['litcelular_contato'] = substr($info['celular_contato'], 2, 4) . "-" . substr($info['celular_contato'], 6);
                            $info['celular_contato_ddd'] = substr($info['celular_contato'], 0, 2);
                        }

                        if (strlen($info_cliente['telefone_cliente']) == 10) {
                            $info['littelefone_cliente'] = substr($info_cliente['telefone_cliente'], 2, 4) . "-" . substr($info_cliente['telefone_cliente'], 6);
                            $info['telefone_cliente_ddd'] = substr($info_cliente['telefone_cliente'], 0, 2);
                        }

                        if (strlen($info_cliente['fax_cliente']) == 10) {
                            $info['litfax_cliente'] = substr($info_cliente['fax_cliente'], 2, 4) . "-" . substr($info_cliente['fax_cliente'], 6);
                            $info['fax_cliente_ddd'] = substr($info_cliente['fax_cliente'], 0, 2);
                        }

                        if (strlen($info_cliente['telefone_cobranca']) == 10) {
                            $info['littelefone_cobranca'] = substr($info_cliente['telefone_cobranca'], 2, 4) . "-" . substr($info_cliente['telefone_cobranca'], 6);
                            $info['telefone_cobranca_ddd'] = substr($info_cliente['telefone_cobranca'], 0, 2);
                        }


                        //obtém os erros
                        $err = $cliente_condominio->err;
                    }

                    $list_ramo_atividade = $ramo_atividade->make_list_select();
                    $smarty->assign("list_ramo_atividade", $list_ramo_atividade);

                    $list_motivo_bloqueio = $motivo_bloqueio->make_list_select();
                    $smarty->assign("list_motivo_bloqueio", $list_motivo_bloqueio);

                    $list_banco = $banco->make_list_select();
                    $smarty->assign("list_banco", $list_banco);

                    //passa os dados para o template
                    $smarty->assign("info", $info);

                    break;



                // deleta um registro do sistema
                case "excluir":

                    //verifica se foi pedido a deleção
                    if ($_POST['for_chk']) {

                        // busca o codigo do endereço
                        $info = $cliente->getById($_GET['idcliente']);

                        // deleta o registro
                        $cliente_condominio->delete($_GET['idcliente']);
                        $cliente->delete($_GET['idcliente']);

                        // deleta o endereço do banco de dados
                        $endereco->delete($info['idendereco_cliente']);

                        // deleta o endereço do banco de dados
                        $endereco->delete($info['idendereco_cobranca']);


                        //obtém erros
                        $err = $cliente_condominio->err;

                        //se não ocorreram erros
                        if (count($err) == 0) {
                            $flags['sucesso'] = $conf['excluir'];
                        }

                        //limpa o $flags.action para que seja exibida a listagem
                        $flags['action'] = "listar";

                        //lista registros
                        $list = $cliente_condominio->make_list(0, $conf['rppg']);

                        //pega os erros
                        $err = $cliente_condominio->err;

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
// Forma Array de intruções de preenchimento
$intrucoes_preenchimento = array();
if ($flags['action'] == "adicionar" || $flags['action'] == "editar") {
    $intrucoes_preenchimento[] = "Os campos em <span class=req>vermelho</span> s&atilde;o obrigat&oacute;rios.";
} else if ($flags['action'] == "busca_generica" || $flags['action'] == "busca_parametrizada") {
    $intrucoes_preenchimento[] = "Preencha os campos para realizar a busca.";
}

// Formata a mensagem para ser exibida
$flags['intrucoes_preenchimento'] = $form->FormataMensagemAjuda($intrucoes_preenchimento);

$smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));

$smarty->assign("form", $form);
$smarty->assign("flags", $flags);

//Associa o confunto de parâmetros do programa para o template
$param = $parametros->getAll();
$param['multa_boleto'] = $form->FormataMoedaParaExibir($param['multa_boleto']);
$param['juros_boleto'] = $form->FormataMoedaParaExibir($param['juros_boleto']);
$param['desconto_boleto'] = $form->FormataMoedaParaExibir($param['desconto_boleto']);
$smarty->assign("parametros",$param);




$list_permissao = $auth->check_priv($conf['priv']);
$smarty->assign("list_permissao", $list_permissao);

if ($_GET['target'] == "full")
    $smarty->display("adm_relatorio_cliente_condominio.tpl");
else
    $smarty->display("adm_cliente_condominio.tpl");
?>
