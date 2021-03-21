<?php

//inclusão de bibliotecas
require_once('../common/lib/conf.inc.php');
require_once('../common/lib/db.inc.php');
require_once('../common/lib/auth.inc.php');
require_once('../common/lib/form.inc.php');
require_once('../common/lib/rotinas.inc.php');
require_once('../common/lib/Smarty/Smarty.class.php');
require_once('../common/lib/xajax/xajax.inc.php');

require_once('../entidades/cliente_fisico.php');
require_once('../entidades/cliente.php');
require_once('../entidades/ramo_atividade.php');
require_once('../entidades/motivo_bloqueio.php');
require_once('../entidades/endereco.php');
require_once('../entidades/bairro.php');
require_once('../entidades/estado.php');
require_once('../entidades/funcionario_cliente.php');
require_once('../entidades/parametros.php');
require_once('../entidades/movimento.php');

require_once("cliente_ajax.php");
require_once("funcionario_ajax.php");


// configurações anotionais
$conf['area'] = "Cliente Pessoa Física"; // área
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
$xajax->registerFunction("Verifica_Campos_ClienteFisico_AJAX");
$xajax->registerFunction('Verifica_Funcionario_Existe_AJAX');
$xajax->registerFunction('Seleciona_Funcionario_AJAX');
$xajax->registerFunction('Insere_Funcionario_AJAX');
$xajax->registerFunction('Deleta_Funcionario_AJAX');


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
            $cliente_fisico = new cliente_fisico();

            $ramo_atividade = new ramo_atividade();
            $motivo_bloqueio = new motivo_bloqueio();
            $cliente = new cliente();
            $endereco = new endereco();
            $bairro = new bairro();
            $estado = new estado();
            $funcionario_cliente = new funcionario_cliente();
            $parametros = new parametros();
            $movimento = new movimento();
            

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
                        $list = $cliente_fisico->Busca_Generica($pg, $flags['rpp'], $flags['busca'], "", "ac=busca_generica&busca=" . $flags['busca'] . "&rpp=" . $flags['rpp']);

                        //pega os erros
                        $err = $cliente_fisico->err;

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

                        if ($_POST['for_chk']) {
                            $flags['cliente'] = $_POST['cliente'];
                            $flags['ramo_atividade'] = $_POST['ramo_atividade'];
                            $flags['cpf_cliente'] = $_POST['cpf_cliente'];
                            $flags['email_cliente'] = $_POST['email_cliente'];
                            $flags['tipo_cliente'] = $_POST['tipo_cliente'];
                            $flags['rpp'] = $_POST['rpp'];
                        } else {
                            $flags['cliente'] = $_GET['cliente'];
                            $flags['ramo_atividade'] = $_GET['ramo_atividade'];
                            $flags['cpf_cliente'] = $_GET['cpf_cliente'];
                            $flags['email_cliente'] = $_GET['email_cliente'];
                            $flags['tipo_cliente'] = $_GET['tipo_cliente'];
                            $flags['rpp'] = $_GET['rpp'];
                        }

                        $parametros_get = "&cliente=" . $flags['cliente'] . "&ramo_atividade=" . $flags['ramo_atividade'] . "&cpf_cliente=" . $flags['cpf_cliente'] . "&email_cliente=" . $flags['email_cliente'] . "&tipo_cliente=" . $flags['tipo_cliente'];


                        $filtro_where = "";
                        if ($flags['cliente'] != "")
                            $filtro_where .= " UPPER(CLI.nome_cliente) LIKE UPPER('%" . $flags['cliente'] . "%') AND ";
                        if ($flags['ramo_atividade'] != "")
                            $filtro_where .= " ( (UPPER(RAT.descricao_atividade) LIKE UPPER('%" . $flags['ramo_atividade'] . "%'))) AND ";
                        if ($flags['cpf_cliente'] != "")
                            $filtro_where .= " ( (UPPER(FCLI.cpf_cliente) LIKE UPPER('%" . $flags['cpf_cliente'] . "%')) ) AND ";
                        if ($flags['email_cliente'] != "")
                            $filtro_where .= " UPPER(CLI.email_cliente) LIKE UPPER('%" . $flags['email_cliente'] . "%') AND ";
                        if ($flags['tipo_cliente'] != "")
                            $filtro_where .= " UPPER(CLI.tipo_cliente) LIKE UPPER('%" . $flags['tipo_cliente'] . "%') AND ";

                        $filtro_where = substr($filtro_where, 0, strlen($filtro_where) - 4);


                        if ($_GET['target'] == "full")
                            $flags['rpp'] = 9999999;


                        //obtém qual página da listagem deseja exibir
                        $pg = intval(trim($_GET['pg']));

                        //se não foi passada a página como parâmetro, faz página default igual à página 0
                        if (!$pg)
                            $pg = 0;

                        //lista os registros
                        $list = $cliente_fisico->Busca_Parametrizada($pg, $flags['rpp'], $filtro_where, "", "ac=busca_parametrizada$parametros_get&rpp=" . $flags['rpp']);

                        //pega os erros
                        $err = $cliente_fisico->err;

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
                        //$form->chk_cpf($_POST['cpf_cliente'], 0);
                        $form->chk_empty($_POST['sexo_cliente'], 1, 'Sexo do cliente');
                        $form->chk_empty($_POST['idramo_atividade'], 0, 'Ramo de atividade');
                        
                        
                        if($_POST['senha_cliente'] || $_POST['re_senha_cliente']){  

                            if(empty($_POST['cpf_cliente'])){
                                $form->err[] = 'Para definir uma senha de acesso, é necessário que o CPF esteja cadastrado';
                            }
                            
                            $tamanhoMinSenha = $parametros->getParam('tamanhoMinSenha');
                            if(strlen($_POST['senha_cliente']) < $tamanhoMinSenha){      
                                $form->err[] = "A senha deve conter no mínimo $tamanhoMinSenha caracteres";
                            }
                            else{
                                $form->chk_senha($post['senha_cliente'], $post['re_senha_cliente']);  
                            }

                        }

                        $err = $form->err;

                        // verifica se o CPF do cliente está duplicado
                        if ($_POST['cpf_cliente'] != '') {
                            $_POST['cpf_cliente'] = $form->FormataCPFParaInserir($_POST['cpf_cliente']);
                            // if ($cliente_fisico->Verifica_CPF_Duplicado($_POST['cpf_cliente']))
                            //     $err[] = "Este CPF já existe e não pode ser duplicado!";
                        }

                        // busca os ids dos funcionários
                        $funcionarios = array();
                        foreach ($_POST as $key => $val) {
                            if (false !== strpos($key, 'codigo_funcionario_')) {
                                $funcionarios[] = $val;
                            }
                        }

                        
                        

                        if (count($err) == 0) {


                            $_POST['observacao_cliente'] = nl2br($_POST['observacao_cliente']);

                            $_POST['valor_contrato_cliente'] = str_replace(",", ".", $_POST['valor_contrato_cliente']);

                            $_POST['data_bloqueio_cliente'] = $form->FormataDataParaInserir($_POST['data_bloqueio_cliente']);
                            $_POST['data_cadastro_cliente'] = $form->FormataDataParaInserir($_POST['data_cadastro_cliente']);

                            $_POST['telefone_cliente'] = $form->FormataTelefoneParaInserir($_POST['telefone_cliente_ddd'], $_POST['telefone_cliente']);
                            $_POST['fax_cliente'] = $form->FormataTelefoneParaInserir($_POST['fax_cliente_ddd'], $_POST['fax_cliente']);
                            $_POST['telefone_cobranca'] = $form->FormataTelefoneParaInserir($_POST['telefone_cobranca_ddd'], $_POST['telefone_cobranca']);

                            if ($_POST['valor_contrato_cliente'] == "")
                                $_POST['valor_contrato_cliente'] = "NULL";
                            if ($_POST['idmotivo_bloqueio'] == "")
                                $_POST['idmotivo_bloqueio'] = "NULL";



                            $_POST['salario_cliente'] = str_replace(",", ".", $_POST['salario_cliente']);
                            $_POST['salario_conjuge'] = str_replace(",", ".", $_POST['salario_conjuge']);
                            $_POST['data_nascimento_cliente'] = $form->FormataDataParaInserir($_POST['data_nascimento_cliente']);
                            $_POST['data_nascimento_conjugue'] = $form->FormataDataParaInserir($_POST['data_nascimento_conjugue']);


                            $_POST['celular_cliente'] = $form->FormataTelefoneParaInserir($_POST['celular_cliente_ddd'], $_POST['celular_cliente']);
                            $_POST['tel_residencial_cliente'] = $form->FormataTelefoneParaInserir($_POST['tel_residencial_cliente_ddd'], $_POST['tel_residencial_cliente']);

                            if ($_POST['salario_cliente'] == "")
                                $_POST['salario_cliente'] = "NULL";
                            if ($_POST['salario_conjuge'] == "")
                                $_POST['salario_conjuge'] = "NULL";
                            
                            if($_POST['senha_cliente'] != ''){
                                $_POST['senha_cliente'] = md5($_POST['senha_cliente']);
                            }


                            // Grava o registro do endereço no Banco de Dados
                            $_POST['idendereco_cliente'] = $endereco->InsereEndereco($_POST, "cliente");

                            // Grava o registro do endereço no Banco de Dados
                            $_POST['idendereco_cobranca'] = $endereco->InsereEndereco($_POST, "cobranca");

                            // Grava o registro do endereço no Banco de Dados
                            $_POST['idendereco_trabalho'] = $endereco->InsereEndereco($_POST, "trabalho");


                            //grava o registro no banco de dados
                            $_POST['idcliente'] = $cliente->set($_POST);
                            !empty($cliente->err) ? $err[] = $cliente->err : '';

                            //grava o registro no banco de dados
                            $cliente_fisico->set($_POST);

                            //obtém os erros que ocorreram no cadastro
                            $err = $cliente_fisico->err;


                            // relaciona os funcionários ao cliente
                            if (!empty($funcionarios)) {
                                $funcionario_cliente->relacionaFuncionarioACliente($_POST['idcliente'], $funcionarios);
                                !empty($cliente_funcionario->err) ? $err[] = $cliente_funcionario->err : '';
                            }


                            //se não ocorreram erros
                            if (count($err) == 0) {
                                $flags['sucesso'] = $conf['inserir'];

                                //limpa o $flags.action para que seja exibida a listagem
                                $flags['action'] = "listar";

                                //lista
                                $list = $cliente_fisico->make_list(0, $conf['rppg']);

                                //pega os erros
                                $err = $cliente_fisico->err;

                                //envia a listagem para o template
                                $smarty->assign("list", $list);
                            }
                        }
                    }

                    $list_ramo_atividade = $ramo_atividade->make_list_select();
                    $smarty->assign("list_ramo_atividade", $list_ramo_atividade);

                    $list_motivo_bloqueio = $motivo_bloqueio->make_list_select();
                    $smarty->assign("list_motivo_bloqueio", $list_motivo_bloqueio);


                break;


                //listagem dos registros
                case "listar":

                    //obtém qual página da listagem deseja exibir
                    $pg = intval(trim($_GET['pg']));

                    //se não foi passada a página como parâmetro, faz página default igual à página 0
                    if (!$pg)
                        $pg = 0;

                    //lista os registros
                    $list = $cliente_fisico->make_list($pg, $conf['rppg']);

                    //pega os erros
                    $err = $cliente_fisico->err;

                    //passa a listagem para o template
                    $smarty->assign("list", $list);

                    break;


                
                    
                    
                    
                // ação: editar <<<<<<<<<<
                case "editar":

                    if ($_POST['for_chk']) {

                        $info = $_POST;

                        $info['idcliente'] = $_GET['idcliente'];

                        $form->chk_empty($_POST['litnome_cliente'], 1, 'Nome do cliente');
                        $form->chk_cpf($_POST['litcpf_cliente'], 0);
                        $form->chk_empty($_POST['litsexo_cliente'], 1, 'Sexo do cliente');
                        $form->chk_empty($_POST['numidramo_atividade'], 0, 'Ramo de atividade');
                        
                        if($_POST['senha_cliente'] || $_POST['re_senha_cliente']){      
                            $form->chk_senha($_POST['senha_cliente'], $_POST['re_senha_cliente']);       
                        }

                        $err = $form->err;

                        // verifica se o CPF do cliente está duplicado
                        if ($_POST['litcpf_cliente'] != '') {
                            $_POST['litcpf_cliente'] = $form->FormataCPFParaInserir($_POST['litcpf_cliente']);
                            $info['litcpf_cliente'] = $_POST['litcpf_cliente'];
                            // if ($cliente_fisico->Verifica_CPF_Duplicado($_POST['litcpf_cliente'], $_GET['idcliente']))
                            //     $err[] = "Este CPF já existe e não pode ser duplicado!";
                        }

                        // busca os ids dos funcionários
                        $funcionarios = array();
                        foreach ($_POST as $key => $val) {
                            if (false !== strpos($key, 'codigo_funcionario_')) {
                                $funcionarios[] = $val;
                            }
                        }
                        
                        

                        if (count($err) == 0) {


                            $_POST['litobservacao_cliente'] = nl2br($_POST['litobservacao_cliente']);

                            $_POST['numvalor_contrato_cliente'] = str_replace(",", ".", $_POST['numvalor_contrato_cliente']);

                            $_POST['litdata_bloqueio_cliente'] = $form->FormataDataParaInserir($_POST['litdata_bloqueio_cliente']);
                            $_POST['litdata_cadastro_cliente'] = $form->FormataDataParaInserir($_POST['litdata_cadastro_cliente']);

                            $_POST['littelefone_cliente'] = $form->FormataTelefoneParaInserir($_POST['telefone_cliente_ddd'], $_POST['littelefone_cliente']);
                            $_POST['litfax_cliente'] = $form->FormataTelefoneParaInserir($_POST['fax_cliente_ddd'], $_POST['litfax_cliente']);
                            $_POST['littelefone_cobranca'] = $form->FormataTelefoneParaInserir($_POST['telefone_cobranca_ddd'], $_POST['littelefone_cobranca']);

                            if ($_POST['numvalor_contrato_cliente'] == "")
                                $_POST['numvalor_contrato_cliente'] = "NULL";

                            if ($_POST['numidmotivo_bloqueio'] == "")
                                $_POST['numidmotivo_bloqueio'] = "NULL";


                            $_POST['numsalario_cliente'] = str_replace(",", ".", $_POST['numsalario_cliente']);
                            $_POST['numsalario_conjuge'] = str_replace(",", ".", $_POST['numsalario_conjuge']);

                            $_POST['litdata_nascimento_cliente'] = $form->FormataDataParaInserir($_POST['litdata_nascimento_cliente']);
                            $_POST['litdata_nascimento_conjugue'] = $form->FormataDataParaInserir($_POST['litdata_nascimento_conjugue']);


                            $_POST['litcelular_cliente'] = $form->FormataTelefoneParaInserir($_POST['celular_cliente_ddd'], $_POST['litcelular_cliente']);
                            $_POST['littel_residencial_cliente'] = $form->FormataTelefoneParaInserir($_POST['tel_residencial_cliente_ddd'], $_POST['littel_residencial_cliente']);

                            if ($_POST['numsalario_cliente'] == "")
                                $_POST['numsalario_cliente'] = "NULL";
                            if ($_POST['numsalario_conjuge'] == "")
                                $_POST['numsalario_conjuge'] = "NULL";


                            //Atualiza senha do cliente
                            if($_POST['senha_cliente'] != ''){
                                $_POST['litsenha_cliente'] = md5($_POST['senha_cliente']);
                            }

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

                            // atualiza os dados do endereço
                            if (!empty($_POST['idendereco_trabalho'])){
                                $endereco->AtualizaEndereco(intval($_POST['idendereco_trabalho']), $_POST, "trabalho");
                            }
                            elseif($_POST['trabalho_logradouro']){
                                // Grava o registro do endereço no Banco de Dados
                                $_POST['numidendereco_trabalho'] = $endereco->InsereEndereco($_POST, "trabalho");
                            }

                            $idcliente = intval($_GET['idcliente']);
                            
                            // atualiza os dados do cliente
                            $cliente->AtualizaCliente($idcliente, $_POST);
                            !empty($cliente->err) ? $err[] = $cliente->err : '';

                            $cliente_fisico->AtualizaClienteFisico($idcliente, $_POST);
                            !empty($cliente_fisico->err) ? $err[] = $cliente_fisico->err : '';


                            // Atualiza a relação de funcionários que atendem o cliente
                            if (!empty($funcionarios)) {
                                $funcionario_cliente->relacionaFuncionarioACliente($idcliente, $funcionarios);
                                !empty($funcionario_cliente->err) ? $err[] = $funcionario_cliente->err : '';
                            }




                            //obtém erros
                            $err = $cliente_fisico->err;

                            //se não ocorreram erros
                            if (count($err) == 0) {
                                $flags['sucesso'] = $conf['alterar'];

                                //limpa o $flags.action para que seja exibida a listagem
                                $flags['action'] = "listar";

                                //lista
                                $list = $cliente_fisico->make_list(0, $conf['rppg']);

                                //pega os erros
                                $err = $cliente_fisico->err;

                                //envia a listagem para o template
                                $smarty->assign("list", $list);
                            }
                        }
                    } else {

                        //busca detalhes
                        $info = $cliente_fisico->getById($_GET['idcliente']);

                        //tratamento das informações para fazer o UPDATE
                        $info['numidcliente'] = $info['idcliente'];
                        $info['litcpf_cliente'] = $info['cpf_cliente'];
                        $info['litsexo_cliente'] = $info['sexo_cliente'];
                        $info['litidentidade_cliente'] = $info['identidade_cliente'];
                        $info['litdata_nascimento_cliente'] = $info['data_nascimento_cliente'];
                        $info['litcelular_cliente'] = $info['celular_cliente'];
                        $info['litestado_civil_cliente'] = $info['estado_civil_cliente'];
                        $info['litcarteira_profissional_cliente'] = $info['carteira_profissional_cliente'];
                        $info['litnome_empregadora_cliente'] = $info['nome_empregadora_cliente'];
                        $info['litprofissao_cliente'] = $info['profissao_cliente'];
                        $info['litcargo_cliente'] = $info['cargo_cliente'];
                        $info['numsalario_cliente'] = $info['salario_cliente'];
                        $info['litnome_pai_cliente'] = $info['nome_pai_cliente'];
                        $info['litnome_mae_cliente'] = $info['nome_mae_cliente'];
                        $info['numidendereco_trabalho'] = $info['idendereco_trabalho'];
                        $info['litnome_conjuge_cliente'] = $info['nome_conjuge_cliente'];
                        $info['litdata_nascimento_conjugue'] = $info['data_nascimento_conjugue'];
                        $info['litempregadora_conjuge'] = $info['empregadora_conjuge'];
                        $info['litprofissao_conjuge'] = $info['profissao_conjuge'];
                        $info['litcargo_conjuge'] = $info['cargo_conjuge'];
                        $info['numsalario_conjuge'] = $info['salario_conjuge'];


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
                        
                        if($info_cliente['senha_cliente'] != ''){
                            $info['senha_cliente'] = true;
                        }
                        
                        // Busca os dados do endereço
                        if (!empty($info_cliente['idendereco_cliente'])) {
                            $dados_endereco = $endereco->BuscaDadosEndereco($info_cliente['idendereco_cliente'], $info, "cliente");

                            $info['cliente_idestado_Nome'] = $dados_endereco['nome_estado'];
                            $info['cliente_idestado_NomeTemp'] = $dados_endereco['nome_estado'];

                            $info['cliente_idcidade_Nome'] = $dados_endereco['nome_cidade'];
                            $info['cliente_idcidade_NomeTemp'] = $dados_endereco['nome_cidade'];

                            $info['cliente_idbairro_Nome'] = $dados_endereco['nome_bairro'];
                            $info['cliente_idbairro_NomeTemp'] = $dados_endereco['nome_bairro'];

                            $dados_endereco = NULL;
                        }


                        // Busca os dados do endereço
                        if (!empty($info_cliente['idendereco_cobranca'])) {
                            $dados_endereco = $endereco->BuscaDadosEndereco($info_cliente['idendereco_cobranca'], $info, "cobranca");

                            $info['cobranca_idestado_Nome'] = $dados_endereco['nome_estado'];
                            $info['cobranca_idestado_NomeTemp'] = $dados_endereco['nome_estado'];

                            $info['cobranca_idcidade_Nome'] = $dados_endereco['nome_cidade'];
                            $info['cobranca_idcidade_NomeTemp'] = $dados_endereco['nome_cidade'];

                            $info['cobranca_idbairro_Nome'] = $dados_endereco['nome_bairro'];
                            $info['cobranca_idbairro_NomeTemp'] = $dados_endereco['nome_bairro'];

                            $dados_endereco = NULL;
                        }


                        // Busca os dados do endereço
                        if (!empty($info_cliente['idendereco_trabalho'])) {
                            $dados_endereco = $endereco->BuscaDadosEndereco($info['idendereco_trabalho'], $info, "trabalho");

                            $info['trabalho_idestado_Nome'] = $dados_endereco['nome_estado'];
                            $info['trabalho_idestado_NomeTemp'] = $dados_endereco['nome_estado'];

                            $info['trabalho_idcidade_Nome'] = $dados_endereco['nome_cidade'];
                            $info['trabalho_idcidade_NomeTemp'] = $dados_endereco['nome_cidade'];

                            $info['trabalho_idbairro_Nome'] = $dados_endereco['nome_bairro'];
                            $info['trabalho_idbairro_NomeTemp'] = $dados_endereco['nome_bairro'];

                            $dados_endereco = NULL;
                        }



                        if (strlen($info['celular_cliente']) == 10) {
                            $info['litcelular_cliente'] = substr($info['celular_cliente'], 2, 4) . "-" . substr($info['celular_cliente'], 6);
                            $info['celular_cliente_ddd'] = substr($info['celular_cliente'], 0, 2);
                        }

                        if (strlen($info['tel_residencial_cliente']) == 10) {
                            $info['tel_residencial_cliente'] = substr($info['tel_residencial_cliente'], 2, 4) . "-" . substr($info['tel_residencial_cliente'], 6);
                            $info['tel_residencial_cliente_ddd'] = substr($info['tel_residencial_cliente'], 0, 2);
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
                        $err = $cliente_fisico->err;
                    }

                    $list_ramo_atividade = $ramo_atividade->make_list_select();
                    $smarty->assign("list_ramo_atividade", $list_ramo_atividade);

                    $list_motivo_bloqueio = $motivo_bloqueio->make_list_select();
                    $smarty->assign("list_motivo_bloqueio", $list_motivo_bloqueio);

                    //passa os dados para o template
                    $smarty->assign("info", $info);

                    break;



                // deleta um registro do sistema
                case "excluir":

                    //verifica se foi pedido a deleção
                    if ($_POST['for_chk']) {

                        // busca o codigo do endereço
                        $info_cliente = $cliente->getById($_GET['idcliente']);
                        $info = $cliente_fisico->getById($_GET['idcliente']);

                        // deleta o registro
                        $cliente_fisico->delete($_GET['idcliente']);
                        $cliente->delete($_GET['idcliente']);

                        // deleta o endereço do banco de dados
                        $endereco->delete($info_cliente['idendereco_cliente']);

                        // deleta o endereço do banco de dados
                        $endereco->delete($info_cliente['idendereco_cobranca']);

                        // deleta o endereço do banco de dados
                        $endereco->delete($info['idendereco_trabalho']);


                        //obtém erros
                        $err = $cliente_fisico->err;

                        //se não ocorreram erros
                        if (count($err) == 0) {
                            $flags['sucesso'] = $conf['excluir'];
                        }

                        //limpa o $flags.action para que seja exibida a listagem
                        $flags['action'] = "listar";

                        //lista registros
                        $list = $cliente_fisico->make_list(0, $conf['rppg']);

                        //pega os erros
                        $err = $cliente_fisico->err;

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

$list_permissao = $auth->check_priv($conf['priv']);
$smarty->assign("list_permissao", $list_permissao);

$smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));

$smarty->assign("form", $form);
$smarty->assign("flags", $flags);
$smarty->assign("parametros",$parametros->getAll());

if ($_GET['target'] == "full")
    $smarty->display("adm_relatorio_cliente_fisico.tpl");
else
    $smarty->display("adm_cliente_fisico.tpl");
?>
