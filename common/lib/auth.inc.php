<?php

require_once("db.inc.php");
require_once("Smarty/Smarty.class.php");

/**
 *     classe: auth
 *  prop�sito: Gerencia a autenica��o de usu�rios.
 */
class auth {

    var $err;

    /**
     * construtor: auth
     *  prop�sito: Inicializa a sess�o atual.
     */
    function auth() {

        ini_set("session.gc_maxlifetime", "30");
        session_start();
        //phpinfo();
    }

    /**
     *     m�todo: check_user
     *  prop�sito: Verifica o status da sess�o do usu�rio.
     */
    function check_user() {
        global $conf;
        global $smarty;

        $db = new db();

        //Verifica se est� logado
        if ((isset($_SESSION['usr_cod'])) && ($_SESSION['aplic'] == $conf['aplic'])) {

            //Verifica se � um cond�mino tentando acessar a �rea de ADMIN
            if (isset($_SESSION['cond_login']) && $conf['diretorio_area'] != 'condominio') {                
                $this->err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"" . $conf['addr'] . "/condominio/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial.";
                return(0);
            }
            //Verifica se � funcion�rio tentando acessar a �rea de CONDOM�NIO
            elseif (!isset($_SESSION['cond_login']) && $conf['diretorio_area'] == 'condominio') {
                $this->err = htmlentities('Para acessar esta �rea, � necess�rio estar logado como cond�mino');
                return(0);
            }

            return(1);
        } else {
            $this->err = "Voc&ecirc; precisa efetuar seu login para ter acesso a esta &aacute;rea!";

            $list_sql = "	SELECT
				*
				FROM
				{$conf['db_name']}filial
				ORDER BY nome_filial ASC";

            $list_q = $db->query($list_sql);
            if ($list_q) {

                //busca os registros no banco de dados e monta o vetor de retorno
                $list_return = array();
                $cont = 0;
                while ($list = $db->fetch_array($list_q)) {


                    foreach ($list as $campo => $value) {
                        $list_return["$campo"][$cont] = $value;
                    }

                    $cont++;
                }
            }

            $smarty->assign("list_filial", $list_return);

            return(0);
        }
    }

    /**
     *     m�todo: login
     *  prop�sito: Autentica o usu�rio.
     */
    function login(&$user, &$pass) {

        global $conf, $form;

        $user = trim(strtolower($user));
        $pass = trim($pass);
        $hash = md5($pass);

        // Verifica em qual tabela devemos procurar o usu�rio
        $db = new db();


        // ******************* Condom�nios ******************
        $ClienteCond = false;
        if ($form->chk_cpf($user, 0)) {

            $ClienteCond = array('tabela' => 'cliente_fisico', 'campo' => 'cpf_cliente');
            $user = $form->FormataCPFParaInserir($user);
        } elseif ($form->chk_cnpj($user, 0)) {

            $ClienteCond = array('tabela' => 'cliente_juridico', 'campo' => 'cnpj_cliente');
            $user = $form->FormataCNPJParaInserir($user);
        }

        if ($ClienteCond != false) {
            $sql = "SELECT * 
					FROM {$conf['db_name']}{$ClienteCond['tabela']} COND
					JOIN cliente CLI USING(idcliente)
					WHERE 
						COND.{$ClienteCond['campo']} = '$user' AND 
						CLI.senha_cliente = '$hash'";


            $usr_q = $db->query($sql);
            $n0 = $db->num_rows($usr_q);


            if ($n0 == 1)
                $resultado_condomino = $this->log_cond($usr_q);
        }
        // ********************************************************************
        // ************ Administradores ************
        $sql = "SELECT * FROM {$conf['db_name']}administrador WHERE adm_log = '$user' AND adm_sen = '$hash'";
        $usr_q = $db->query($sql);
        $n1 = $db->num_rows($usr_q);
        if ($n1 == 1)
            $resultado_adm = $this->log_adm($user, $hash);
        // ********************************************************************
        // ************ Funcion�rios ************
        $sql = "SELECT * FROM {$conf['db_name']}funcionario WHERE login_funcionario = '$user' AND senha_funcionario = '$hash'";
        $usr_q = $db->query($sql);
        $n2 = $db->num_rows($usr_q);


        // verifica se o usuario logou na sua filial
        if ($n2 == 1) {
            $usr = $db->fetch_array($usr_q);

            // verifica se o usuario est� na filial que ele escolheu
            $sql_filial = " SELECT
				FLFNC.*
				FROM
				{$conf['db_name']}filial_funcionario FLFNC
				WHERE
				FLFNC.idfilial = " . $_POST['idfilial'] . " AND  FLFNC.idfuncionario = " . $usr['idfuncionario'];

            $filial_q = $db->query($sql_filial);
            $n3 = $db->num_rows($filial_q);

            // se ele est� na filial escolhida, loga o usuario
            if ($n3 == 1) {
                $resultado_funcionario = $this->log_funcionario($user, $hash);
            }
            else
                $resultado_funcionario = 0;
        }
        // ********************************************************************


        $_SESSION['aplic'] = $conf['aplic'];

        if ($resultado_adm || $resultado_funcionario || $resultado_condomino) {

            // id da filial do usuario
            $_SESSION['idfilial_usuario'] = $_POST['idfilial'];

            $get_sql = "	SELECT
								FLI.*
							FROM
								{$conf['db_name']}filial FLI
							WHERE
								FLI.idfilial = " . $_POST['idfilial'] . " ";

            //executa a query no banco de dados
            $get_q = $db->query($get_sql);

            //testa se a consulta foi bem sucedida
            if ($get_q) { //foi bem sucedida
                $get = $db->fetch_array($get_q);
            }

            // nome da filial do usuario
            $_SESSION['nomefilial_usuario'] = $get['nome_filial'];

            //seta 1 para navegadores mozilla e 0 para navegadores IE ou outros
            // navegadores IE
            if (stripos($_SERVER['HTTP_USER_AGENT'], "MSIE"))
                $_SESSION['browser_usuario'] = "0";
            // navegadores mozilla
            else
                $_SESSION['browser_usuario'] = "1";


            switch ($_SESSION['usr_pri']) {
                case $conf['pri_adm']:  // Administadores
                    header("location: {$conf['addr']}/admin/index.php");
                    break;

                case $conf['pri_cliente']:  // Clientes Cond�minos
                    header("location: {$conf['addr']}/condominio/index.php");
                    break;

                default: // Outros usu�rios
                    header("location: {$conf['addr']}");
                    break;
            }

            return(1);
        } else {
            if ($n0 == 0 && $n1 == 0 && $n2 == 0)
                $this->err = "Usu&aacute;rio ou senha inv&aacute;lidos!";
            else if ($n3 == 0)
                $this->err = "Voc� n�o trabalha na filial escolhida! Selecione uma filial em que voc� trabalha!";

            return(0);
        }
    }

    /**
     *     m�todo: log_cond
     *  prop�sito: Autentica cond�minos.
     */
    function log_cond($usr_q) {

        global $conf;

        $db = new db();

        $n = $db->num_rows($usr_q);

        if ($n == 1) {
            $usr = $db->fetch_array($usr_q);

            $usr['nome_cliente'] = explode(" ", $usr['nome_cliente']);

            $_SESSION['usr_cod'] = $usr['idcliente'];
            $_SESSION['cpf_cliente'] = $usr['cpf_cliente'];
            $_SESSION['usr_pri'] = $conf['pri_cliente'];
            $_SESSION['usr_nom'] = trim($usr['nome_cliente'][0]);
            $_SESSION['usr_sex'] = $usr['sexo_cliente'];
            $_SESSION['cond_login'] = true;
            return(1);
        } else {
            $this->err = "Registro replicado no banco de dados!<br /> Por favor, contate os administradores do sistema.";
            return(0);
        }
    }

    /**
     *     m�todo: log_funcionario
     *  prop�sito: Autentica adminstradores.
     */
    function log_funcionario($user, $hash) {

        global $conf;

        $db = new db();

        $sql = "SELECT * FROM {$conf['db_name']}funcionario WHERE login_funcionario = '$user' AND senha_funcionario = '$hash'";
        $usr_q = $db->query($sql);
        $n = $db->num_rows($usr_q);

        if ($n == 0) {
            $this->err = "Usu&aacute;rio ou senha inv&aacute;lidos!";
            return(0);
        } elseif ($n == 1) {
            $usr = $db->fetch_array($usr_q);

            $usr['nome_funcionario'] = explode(" ", $usr['nome_funcionario']);

            $_SESSION['usr_cod'] = $usr['idfuncionario'];
            $_SESSION['usr_log'] = $usr['login_funcionario'];
            $_SESSION['usr_pri'] = $conf['pri_adm'];
            $_SESSION['usr_nom'] = trim($usr['nome_funcionario'][0]);
            $_SESSION['usr_sex'] = $usr['sexo_funcionario'];
            $_SESSION['usr_cargo'] = $usr['idcargo'];

            return(1);
        } else {
            $this->err = "Registro replicado no banco de dados!<br /> Por favor, contate os administradores do sistema.";
            return(0);
        }
    }

    /**
     *     m�todo: log_adm
     *  prop�sito: Autentica adminstradores.
     */
    function log_adm($user, $hash) {

        global $conf;

        $db = new db();

        $sql = "SELECT * FROM {$conf['db_name']}administrador WHERE adm_log = '$user' AND adm_sen = '$hash'";
        $usr_q = $db->query($sql);
        $n = $db->num_rows($usr_q);

        if ($n == 0) {
            $this->err = "Usu&aacute;rio ou senha inv&aacute;lidos!";
            return(0);
        } elseif ($n == 1) {
            $usr = $db->fetch_array($usr_q);

            $usr['adm_nom'] = explode(" ", $usr['adm_nom']);

            $_SESSION['usr_cod'] = $usr['adm_cod'];
            $_SESSION['usr_log'] = $usr['adm_log'];
            $_SESSION['usr_pri'] = $conf['pri_adm'];
            $_SESSION['usr_nom'] = trim($usr['adm_nom'][0]);
            $_SESSION['usr_sex'] = $usr['adm_sex'];
            $_SESSION['usr_cargo'] = "";

            return(1);
        } else {
            $this->err = "Registro replicado no banco de dados!<br /> Por favor, contate os administradores do sistema.";
            return(0);
        }
    }

    /**
     *     m�todo: check_priv
     *  prop�sito: Verifica se o usu�rio tem os privil�gios necess�rios para
     *             acessar a �rea requisitada.
     * 						Para a ades�o de um novo case favor cadastrar em uma variavel: $list['nome_do_case']
     * 						e liberar tanto para o funcionario quanto ao administrador.
     */
    function check_priv($req) {

        global $conf;

        $db = new db();

        if ($_SESSION['usr_cargo'] == "") {

            // Monta vetor com todas as permiss�es de "case" do sistema para o Administrador

            $list['adicionar'] = '1';
            $list['gerar'] = '1';
            $list['gerar_boleto'] = '1';

            $list['editar'] = '1';
            $list['atualizar_preco'] = '1';
            $list['editarNF'] = '1';
            $list['negociar_conta_receber'] = '1';
            $list['baixa_conta_receber'] = '1';
            $list['baixar_movimentos'] = '1';


            $list['excluir'] = '1';

            $list['listar'] = '1';
            $list['busca_generica'] = '1';
            $list['busca_parametrizada'] = '1';
            $list['busca_genericaNF'] = '1';
            $list['busca_parametrizadaNF'] = '1';
            $list['listarNF'] = '1';

            $list['balancete'] = '1';
            $list['razonete'] = '1';
            $list['demonstrativo'] = '1';

            $list['gerar_boleto_full'] = '1';
            
            $list['visualizar_anexo'] = '1';            
            
            return ($list);
        } else {


            $get_sql = "	SELECT 
				FPROG.* , PROG.*
				FROM
				{$conf['db_name']}funcionario_programa FPROG
				INNER JOIN {$conf['db_name']}programa PROG ON FPROG.idprograma = PROG.idprograma
				WHERE
				FPROG.idfuncionario = " . $_SESSION['usr_cod'] . "
				AND
				PROG.nome_arquivo = '$req'								
				";


            $get_q = $db->query($get_sql);


            $list = $db->fetch_array($get_q);

            // Monta vetor com todas as permiss�es de "case" que o funcionario tem.
            // Se o Case for do tipo ADICIONAR, coloque ele dentro deste if
            if ($list['permissao_adicionar'] == '1') {
                $list['adicionar'] = '1';
                $list['gerar'] = '1';
            }

            // Se o Case for do tipo EDITAR, coloque ele dentro deste if
            if ($list['permissao_editar'] == '1') {
                $list['editar'] = '1';
                $list['atualizar_preco'] = '1';
                $list['editarNF'] = '1';
                $list['negociar_conta_receber'] = '1';
                $list['efetua_negociacao'] = '1';
                $list['baixa_conta_receber'] = '1';
                $list['gerar_boleto'] = '1';
                $list['baixar_movimentos'] = '1';
            }

            // Se o Case for do tipo EXCLUIR, coloque ele dentro deste if
            if ($list['permissao_excluir'] == '1') {
                $list['excluir'] = '1';
            }

            // Se o Case for do tipo LISTAR, coloque ele dentro deste if
            if ($list['permissao_listar'] == '1') {
                $list['listar'] = '1';
                $list['busca_generica'] = '1';
                $list['busca_parametrizada'] = '1';
                $list['busca_genericaNF'] = '1';
                $list['busca_parametrizadaNF'] = '1';
                $list['listarNF'] = '1';
                $list['selecionar_condominio'] = '1';
                $list['gerar_boleto_full'] = '1';

                $list['balancete'] = '1';
                $list['razonete'] = '1';
                $list['demonstrativo'] = '1';
                $list['clientes_inadimplentes'] = '1';
                $list['saldo'] = '1';
                $list['visualizar_anexo'] = '1';
                $list['desbloquear_clientes'] = '1';

                $list['baixar_arquivo_remessa'] = '1';
                $list['detalhar_arquivo_remessa'] = '1';
                $list['desfazer_remessa'] = '1';
            }


            if ($list) {
                return($list);
            } else {
                $this->err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"" . $conf['addr'] . "/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial. <br> Contate o adminstrador do sistema para rever suas permiss&otilde;es.";
                return(0);
            }
        }
    }

    /**
     *  m�todo: Monta_Menu_Usuario
     *  prop�sito: Monta_Menu_Usuario
     */


      // function Monta_Menu_Usuario ($idfuncionario) {

      // global $conf;

      // $db = new db();
      // if($_SESSION['usr_cargo'] !=""){
      // $get_sql = "	SELECT DISTINCT
      // MDL.*, count(DISTINCT SMDL.idsubmodulo) as qtd_sub
      // FROM
      // {$conf['db_name']}modulo MDL
      // INNER JOIN {$conf['db_name']}submodulo SMDL ON MDL.idmodulo = SMDL.idmodulo
      // INNER JOIN {$conf['db_name']}programa PROG ON SMDL.idsubmodulo = PROG.idsubmodulo
      // INNER JOIN {$conf['db_name']}funcionario_programa FPROG ON PROG.idprograma = FPROG.idprograma
      // WHERE
      // FPROG.idfuncionario = $idfuncionario

      // GROUP BY MDL.idmodulo
      // ORDER BY MDL.ordem_modulo ASC";
      // $get_q = $db->query($get_sql);



      // $cont=0;
      // $list_modulo = array();
      // while($list = $db->fetch_array($get_q)){



      // $list['index'] = $cont+1;

      // $list_modulo[] = $list;
      // $list['menu_mod'] = 'Menu'.$list['index'].'=new Array("'.$list['nome_modulo'].'","#","",'.$list['qtd_sub'].',20,'.$list['largura_menu_modulo'].');';
      // $menu_final= $menu_final . $list['menu_mod'];

      // $get_sql2 = "	SELECT DISTINCT
      // SMDL.*, count(DISTINCT PROG.idprograma) as qtd_prog, count(FPROG.idfuncionario) as qtd_func, PROG.*
      // FROM
      // {$conf['db_name']}submodulo SMDL
      // INNER JOIN {$conf['db_name']}programa PROG ON SMDL.idsubmodulo = PROG.idsubmodulo
      // INNER JOIN {$conf['db_name']}funcionario_programa FPROG ON PROG.idprograma = FPROG.idprograma
      // WHERE
      // SMDL.idmodulo = " . $list['idmodulo'] . "

      // GROUP BY SMDL.ordem_submodulo
      // ORDER BY SMDL.ordem_submodulo ASC";

      // $get_q2 = $db->query($get_sql2);



      // $cont2=0;
      // $list_submodulo = array();
      // while($list2 = $db->fetch_array($get_q2)){


      // $list2['index'] = $cont2+1;

      // if($list2['submodulo_final']==1)$list2['menu_sub'] = 'Menu'.$list['index'].'_'.$list2['index'].'=new Array("'.$list2['nome_submodulo'].'","' . $conf['addr'] . '/admin/'.$list2['nome_arquivo'].'.php'.$list2["parametros_arquivo"].'","",0,20,'.$list2['largura_menu_programa'].');';
      // else $list2['menu_sub']= 'Menu'.$list['index'].'_'.$list2['index'].'=new Array("'.$list2['nome_submodulo'].'","#","",'.$list2['qtd_prog'].',20,'.$list['largura_menu_submodulo'].');';

      // $menu_final = $menu_final . $list2['menu_sub'];
      // $list_submodulo[] = $list2;

      // $get_sql3 = "	SELECT DISTINCT
      // PROG.*, count(DISTINCT PROG.idprograma) as qtd_prog
      // FROM
      // {$conf['db_name']}programa PROG
      // INNER JOIN {$conf['db_name']}funcionario_programa FPROG ON PROG.idprograma = FPROG.idprograma
      // WHERE
      // PROG.idsubmodulo = " . $list2['idsubmodulo'] . "

      // GROUP BY PROG.ordem_programa
      // ORDER BY PROG.ordem_programa ASC";

      // $get_q3 = $db->query($get_sql3);

      // $cont3=0;
      // $list_programa = array();
      // while($list3 = $db->fetch_array($get_q3)){

      // $list3['index'] = $cont3+1;
      // if($list2['submodulo_final'] == 0)$list3['menu_prog'] = 'Menu'.$list['index'].'_'.$list2['index'].'_'.$list3['index'].'=new Array("'.$list3['nome_programa'].'","' . $conf['addr'] . '/admin/' . $list3['nome_arquivo'] . '.php' . $list3['parametros_arquivo'].'","",0,20,'.$list2['largura_menu_programa'].');';
      // $list_programa[] = $list3;

      // $menu_final = $menu_final . $list3['menu_prog'];

      // $cont3++;
      // }

      // $cont2++;
      // }
      // $cont++;
      // }

      // $menu_final = $menu_final . 'var NoOffFirstLineMenus='.count($list_modulo).';';

      // }
      // else{
      // $menu_final = '
      // Menu1=new Array("Empresa","#","",3,20,60);
      // Menu1_1=new Array("Filial","' . $conf['addr'] . '/admin/filial.php","",0,20,160);
      // Menu1_2=new Array("Departamento","' . $conf['addr'] . '/admin/departamento.php","",0,20,80);
      // Menu1_3=new Array("Se��o","' . $conf['addr'] . '/admin/secao.php","",0,20,80);



      // Menu2=new Array("Venda","#","",7,20,70);
      // Menu2_1=new Array("CFOP","' . $conf['addr'] . '/admin/cfop.php","",0,20,160);
      // Menu2_2=new Array("Motivo de Cancelamento","' . $conf['addr'] . '/admin/motivo_cancelamento.php","",0,20,160);
      // Menu2_3=new Array("Or�amento","' . $conf['addr'] . '/admin/orcamento.php?tipo=O","",0,20,160);
      // Menu2_4=new Array("Cupom Fiscal","' . $conf['addr'] . '/admin/orcamento.php?tipo=ECF","",0,20,160);
      // Menu2_5=new Array("Nota Fiscal","' . $conf['addr'] . '/admin/orcamento.php?tipo=NF","",0,20,160);
      // Menu2_6=new Array("S�rie D","' . $conf['addr'] . '/admin/orcamento.php?tipo=SD","",0,20,160);
      // Menu2_7=new Array("Entrega de Mercadorias","' . $conf['addr'] . '/admin/entrega.php","",0,20,160);


      // Menu3=new Array("Financeiro","#","",5,20,70);
      // Menu3_1=new Array("Contas a receber","#","",4,20,160);
      // Menu3_1_1=new Array("Cadastro de contas a receber","' . $conf['addr'] . '/admin/conta_receber.php","",0,20,180);
      // Menu3_1_2=new Array("Baixa de contas a receber","' . $conf['addr'] . '/admin/conta_receber.php?ac=baixa_conta_receber","",0,20,160);
      // Menu3_1_3=new Array("Negociar contas a receber","' . $conf['addr'] . '/admin/conta_receber.php?ac=negociar_conta_receber","",0,20,160);
      // Menu3_1_4=new Array("Modo de recebimento","' . $conf['addr'] . '/admin/modo_recebimento.php","",0,20,160);

      // Menu3_2=new Array("Contas a pagar","#","",2,20,160);
      // Menu3_2_1=new Array("Cadastro de contas a pagar","' . $conf['addr'] . '/admin/conta_pagar.php","",0,20,180);
      // Menu3_2_2=new Array("Tipo de contas a pagar","' . $conf['addr'] . '/admin/conta_pagar_tipo.php","",0,20,160);

      // Menu3_3=new Array("Cheques","' . $conf['addr'] . '/admin/cheque.php","",0,20,160);
      // Menu3_4=new Array("Troco","' . $conf['addr'] . '/admin/troco.php","",0,20,160);
      // Menu3_5=new Array("Plano de Contas","' . $conf['addr'] . '/admin/plano.php","",0,20,160);
      // Menu3_6=new Array("Movimenta��o","' . $conf['addr'] . '/admin/movimento.php","",0,20,160);

      // Menu4=new Array("Fornecedores","#","",3,20,80);
      // Menu4_1=new Array("Cadastro de Fornecedor","' . $conf['addr'] . '/admin/fornecedor.php","",0,20,190);
      // Menu4_2=new Array("Pedidos de Compras","' . $conf['addr'] . '/admin/pedido.php","",0,20,160);
      // Menu4_3=new Array("Entrada de Mercadorias","' . $conf['addr'] . '/admin/entrada.php","",0,20,160);


      // Menu5=new Array("Produtos","#","",4,20,60);
      // Menu5_1=new Array("Unidade de Venda","' . $conf['addr'] . '/admin/unidade_venda.php","",0,20,160);
      // Menu5_2=new Array("Produto","' . $conf['addr'] . '/admin/produto.php","",0,20,160);
      // Menu5_3=new Array("Encartelamento","' . $conf['addr'] . '/admin/encartelamento.php","",0,20,160);
      // Menu5_4=new Array("Transfer�ncia","' . $conf['addr'] . '/admin/transferencia_filial.php","",0,20,160);


      // Menu6=new Array("Clientes","#","",4,20,60);
      // Menu6_1=new Array("Cliente Pessoa F�sica","' . $conf['addr'] . '/admin/cliente_fisico.php","",0,20,160);
      // Menu6_2=new Array("Cliente Pessoa Jur�dica","' . $conf['addr'] . '/admin/cliente_juridico.php","",0,20,160);
      // Menu6_3=new Array("Motivo de Bloqueio","' . $conf['addr'] . '/admin/motivo_bloqueio.php","",0,20,160);
      // Menu6_4=new Array("Condom�nio","#","",2,20,160);
      // Menu6_4_1=new Array("Condom�nios","' . $conf['addr'] . '/admin/cliente_condominio.php","",0,20,160);
      // Menu6_4_2=new Array("Apartamentos","' . $conf['addr'] . '/admin/apartamento.php?ac=selecionar_condominio","",0,20,160);

      // Menu7=new Array("Funcion�rios","#","",2,20,80);
      // Menu7_1=new Array("Cargo","' . $conf['addr'] . '/admin/cargo.php","",0,20,160);
      // Menu7_2=new Array("Funcion�rio","' . $conf['addr'] . '/admin/funcionario.php","",0,20,160);

      // Menu8=new Array("Endere�os","#","",3,20,70);
      // Menu8_1=new Array("Estado","' . $conf['addr'] . '/admin/estado.php","",0,20,160);
      // Menu8_2=new Array("Cidade","' . $conf['addr'] . '/admin/cidade.php","",0,20,160);
      // Menu8_3=new Array("Bairro","' . $conf['addr'] . '/admin/bairro.php","",0,20,160);


      // Menu9=new Array("Utilit�rios","#","",5,20,70);
      // Menu9_1=new Array("Transportador","' . $conf['addr'] . '/admin/transportador.php","",0,20,160);
      // Menu9_2=new Array("Banco","' . $conf['addr'] . '/admin/banco.php","",0,20,160);
      // Menu9_3=new Array("Ramo de atividade","' . $conf['addr'] . '/admin/ramo_atividade.php","",0,20,160);
      // Menu9_4=new Array("Par�metros","' . $conf['addr'] . '/admin/parametro.php","",0,20,160);
      // Menu9_5=new Array("Aliq. ICMS","' . $conf['addr'] . '/admin/aliquota_icms.php","",0,20,160);

      // Menu10=new Array("Relat�rios","#","",7,20,70);
      // Menu10_1=new Array("ECF","#","",7,20,160);
      // Menu10_1_1=new Array("Gerar arquivo SINTEGRA","' . $conf['addr'] . '/admin/ecf_comandos.php?comando=sintegra","",0,20,200);
      // Menu10_1_2=new Array("Leitura X","' . $conf['addr'] . '/admin/ecf_comandos.php?comando=leiturax","",0,20,200);
      // Menu10_1_3=new Array("Leitura Mem�ria Fiscal (Data)","' . $conf['addr'] . '/admin/ecf_comandos.php?comando=lmf_data","",0,20,200);
      // Menu10_1_4=new Array("Leitura Mem�ria Fiscal (Redu��o)","' . $conf['addr'] . '/admin/ecf_comandos.php?comando=lmf_reducao","",0,20,200);
      // Menu10_1_5=new Array("Registro de Suprimento de Caixa","' . $conf['addr'] . '/admin/ecf_comandos.php?comando=suprimento","",0,20,200);
      // Menu10_1_6=new Array("Registro de Caixa (Sangria)","' . $conf['addr'] . '/admin/ecf_comandos.php?comando=sangria","",0,20,200);
      // Menu10_1_7=new Array("Redu��o Z","' . $conf['addr'] . '/admin/ecf_comandos.php?comando=reducao_z","",0,20,200);

      // Menu10_2=new Array("SINTEGRA","' . $conf['addr'] . '/admin/sintegra.php","",0,20,160);
      // Menu10_3=new Array("Caixa di�rio","' . $conf['addr'] . '/admin/relatorio_caixa_diario.php","",0,20,160);
      // Menu10_4=new Array("Comiss�o do Vendedor","' . $conf['addr'] . '/admin/relatorio_comissao_vendedor.php","",0,20,160);

      // Menu10_5=new Array("Consultas","#","",5,20,160);
      // Menu10_5_1=new Array("Entrada de Mercadoria","' . $conf['addr'] . '/admin/consulta_movimentacao_mes.php","",0,20,200);
      // Menu10_5_2=new Array("Sa�da de Mercadoria","' . $conf['addr'] . '/admin/consulta_movimentacao_mes_saida.php","",0,20,200);
      // Menu10_5_3=new Array("Vendas","' . $conf['addr'] . '/admin/consulta_vendas.php","",0,20,200);
      // Menu10_5_4=new Array("Orcamentos","' . $conf['addr'] . '/admin/consulta_orcamento.php","",0,20,200);
      // Menu10_5_5=new Array("Pre�os de Produto","' . $conf['addr'] . '/admin/consulta_preco_produto.php","",0,20,200);

      // Menu10_6=new Array("TEF","' . $conf['addr'] . '/admin/tef.php","",0,20,160);
      // Menu10_7=new Array("Relat�rio COTEPE","' . $conf['addr'] . '/admin/cotep_icms.php","",0,20,160);

      // Menu11= new Array("Sistema","#","",7,20,70);
      // Menu11_1=new Array("M�dulo","' . $conf['addr'] . '/admin/modulo.php","",0,20,160);
      // Menu11_2=new Array("SubM�dulo","' . $conf['addr'] . '/admin/submodulo.php","",0,20,160);
      // Menu11_3=new Array("Programa","' . $conf['addr'] . '/admin/programa.php","",0,20,160);
      // Menu11_4=new Array("Permiss�es do Cargo","' . $conf['addr'] . '/admin/cargo_programa.php","",0,20,160);
      // Menu11_5=new Array("Permiss�es do Funcion�rio","' . $conf['addr'] . '/admin/funcionario_programa.php","",0,20,160);
      // Menu11_6=new Array("Administrador","' . $conf['addr'] . '/admin/administradores.php","",0,20,80);
      // Menu11_7=new Array("Sincroniza��o","' . $conf['addr'] . '/admin/sincronia.php","",0,20,80);

      // var NoOffFirstLineMenus=11;	// Number of first level items
      // ';
      // }

      // return($menu_final);

      // }


    /**
     *  m�todo: Monta_Menu_Usuario
     *  prop�sito: Monta_Menu_Usuario
     */
    function Monta_Menu_Usuario() {
        global $conf;
        /** Testa se � um funcion�rio ou � administrador */
        $filtroFunc = '';
        if (!empty($_SESSION['usr_cargo'])) {
            $filtroFunc = " INNER JOIN {$conf['db_name']}funcionario_programa FPROG ON PROG.idprograma = FPROG.idprograma
				WHERE
				FPROG.idfuncionario = {$_SESSION['usr_cod']}";
        }

        $db = new db();

        /** Monta Array de m�dulos */
        $get_sql = "	SELECT DISTINCT MDL.*
			FROM
			{$conf['db_name']}modulo MDL
			INNER JOIN {$conf['db_name']}submodulo SMDL ON MDL.idmodulo = SMDL.idmodulo
			INNER JOIN {$conf['db_name']}programa PROG ON SMDL.idsubmodulo = PROG.idsubmodulo
			$filtroFunc";
        $get_q = $db->query($get_sql);

        $modulo = array();
        while ($list = $db->fetch_array($get_q)) {
            $modulo[$list['idmodulo']] = $list;
        }

        /** Fim do Array de m�dulos */
        /** Monta Array de sub-m�dulos */
        $get_sql = "	SELECT DISTINCT SMDL.*
			FROM
			{$conf['db_name']}submodulo SMDL
			INNER JOIN {$conf['db_name']}programa PROG ON SMDL.idsubmodulo = PROG.idsubmodulo
			$filtroFunc";
        $get_q = $db->query($get_sql);

        $submodulo = array();
        while ($list = $db->fetch_array($get_q)) {
            $submodulo[$list['idsubmodulo']] = $list;
        }
        /** Fim do Array de sub-m�dulos */
        /** Monta Array de programas */
        $get_sql = "	SELECT DISTINCT PROG.*
			FROM
			{$conf['db_name']}programa PROG
			$filtroFunc";
        $get_q = $db->query($get_sql);

        $programa = array();
        while ($list = $db->fetch_array($get_q)) {
            $programa[$list['idprograma']] = $list;
        }
        /** Fim do Array de sub-m�dulos */
        $get_sql = "	SELECT MDL.*, SMDL.*, PROG.*
			FROM
			{$conf['db_name']}modulo MDL
			INNER JOIN {$conf['db_name']}submodulo SMDL ON MDL.idmodulo = SMDL.idmodulo
			INNER JOIN {$conf['db_name']}programa PROG ON SMDL.idsubmodulo = PROG.idsubmodulo
			$filtroFunc
			ORDER BY
			MDL.ordem_modulo ASC,
			SMDL.ordem_submodulo ASC,
			PROG.ordem_programa ASC";
        $get_q = $db->query($get_sql);

        $menu = array();
        while ($list = $db->fetch_array($get_q)) {
            $menu[$list['idmodulo']][$list['idsubmodulo']] [$list['idprograma']] = $list['nome_programa'];
        }
        $menuFinal = '';
        $contMod = 1;
        foreach ($menu as $idModulo => $mod) {
            $menuFinal .= 'Menu' . $contMod . '=new Array("' . $modulo[$idModulo]['nome_modulo'] . '","#","",' . count($mod) . ',20,' . $modulo[$idModulo]['largura_menu_modulo'] . ');';
            $contSub = 1;
            foreach ($mod as $idSubmodulo => $sub) {

                /** Verifica se o sub-m�dulo � final e se h� pelo menos um programa vinculado a ele
                 *  Atribui como programa o primeiro elemento do array sub(que cont�m os ids dos programas
                 *  do Sub-m�dulo corrente
                 */
                if ($submodulo[$idSubmodulo]['submodulo_final'] == 1 && count($sub))
                    $menuFinal .= 'Menu' . $contMod . '_' . $contSub . '=new Array("' . $submodulo[$idSubmodulo]['nome_submodulo'] . '","' . $conf['addr'] . '/admin/' . $programa[key($sub)]['nome_arquivo'] . '.php' . $programa[key($sub)]["parametros_arquivo"] . '","",0,20,' . $submodulo[$idSubmodulo]['largura_menu_programa'] . ');';
                else {

                    $menuFinal .= 'Menu' . $contMod . '_' . $contSub . '=new Array("' . $submodulo[$idSubmodulo]['nome_submodulo'] . '","#","",' . count($sub) . ',20,' . $modulo[$idModulo]['largura_menu_submodulo'] . ');';
                    $contProg = 1;
                    foreach ($sub as $idPrograma => $prog) {
                        $menuFinal .= 'Menu' . $contMod . '_' . $contSub . '_' . $contProg . '=new Array("' . $programa[$idPrograma]['nome_programa'] . '","' . $conf['addr'] . '/admin/' . $programa[$idPrograma]['nome_arquivo'] . '.php' . $programa[$idPrograma]['parametros_arquivo'] . '","",0,20,' . $submodulo[$idSubmodulo]['largura_menu_programa'] . ');';
                        $contProg++;
                    }
                }
                $contSub++;
            }
            $contMod++;
        }
        return $menuFinal . 'var NoOffFirstLineMenus=' . count($menu) . ';'; // Number of first level items;
    }

    /**
     *     m�todo: logout
     *  prop�sito: Destr�i dados da sess�o atual.
     */
    function logout() {
        session_unset();
        session_destroy();
    }

}

?>
