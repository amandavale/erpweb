<?php

//inclusão de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");

require_once("../entidades/funcionario.php");


// inicializa templating
$smarty = new Smarty;

// ação selecionada
$flags['action'] = $_GET['ac'];

// inicializa autenticação
$auth = new auth();

//inicializa classe
$funcionario = new funcionario();

// inicializa banco de dados
$db = new db();

//incializa classe para validação de formulário
$form = new form();



switch ($flags['action']) {


    // busca os funcionarios de acordo com a busca
    case "busca_funcionario":

        $funcionario->Filtra_Funcionario_AJAX($_GET['typing'], $_GET['campoID']);

        break;
}

// seta erros
$smarty->assign("err", $err);

$smarty->assign("form", $form);
$smarty->assign("flags", $flags);



/*
  Função: Insere_Funcionario_AJAX
  Insere um funcionario dinamicamente na tabela html
 */

function Insere_Funcionario_AJAX($post, $cliente = false) {

    // variáveis globais
    global $form;
    global $conf;
    global $db;
    global $falha;
    global $err;

    global $funcionario;
    //---------------------
    // cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

    // codigo do funcionario
    $codigoFuncionario = $post['idfuncionario'];

    $form->chk_empty($post['idfuncionario'], 1, 'Funcionário');

    $err = $form->err;

    // se houveram erros, mostra-os
    if (count($err) != 0) {
        $mensagem = implode("\n", $err);
        $objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
    }
    // se nao houveram erros, verifica se ele já nao existe na tabela
    else if (Verifica_Funcionario_Existe_AJAX($post) == false) {

        // incrementa 1 na quantidade de funcionarios
        $total_funcionarios = intval($post['total_funcionarios']) + 1;
        $objResponse->addAssign("total_funcionarios", "value", $total_funcionarios);

        if (empty($post['nome_funcionario'])) {
            // busca os dados do funcionário
            $info_funcionario = $funcionario->getById($codigoFuncionario);
        } else {
            $info_funcionario = $post;
        }



        // nome da tabela criada
        $nome_tabela = "tabela_funcionario_" . $codigoFuncionario;

        // Monta uma tabela diferente quando é para listar funcionários ligados a clientes
        if ($cliente) {
            $tabela = utf8_encode("
						<table width='100%' cellpadding='5' id='$nome_tabela'>
							<tr>
								<td class='tb_bord_baixo' align='left' width='35%'>
									<input type='hidden' name='codigo_funcionario_$total_funcionarios' id='codigo_funcionario_$total_funcionarios' value='$codigoFuncionario' />
									{$info_funcionario['nome_funcionario']}
								</td>
								<td class='tb_bord_baixo' align='center' width='10%'>&nbsp;{$info_funcionario['nome_cargo']}</td>
								<td class='tb_bord_baixo' align='center' width='5%'>
									<a href='javascript:;' onclick=" . '"xajax_Deleta_Funcionario_AJAX(' . "'$codigoFuncionario'" . ",'" . ((int) $cliente) . "');" . '"' . "><img src='../common/img/delete.gif'></a>
								</td>
							</tr>
						</table>
				");
        } else {// tabela de funcioanrio
            $tabela = utf8_encode("
						<table width='100%' cellpadding='5' id='$nome_tabela'>
							<tr>
								<td class='tb_bord_baixo' align='left' width='35%'>
									<input type='hidden' name='codigo_funcionario_$total_funcionarios' id='codigo_funcionario_$total_funcionarios' value='$codigoFuncionario' />

									{$info_funcionario['nome_funcionario']}
								</td>
								<td class='tb_bord_baixo' align='center' width='10%'>&nbsp;{$info_funcionario['identidade_funcionario']}</td>
								<td class='tb_bord_baixo' align='center' width='15%'>&nbsp;{$info_funcionario['telefone_funcionario']}</td>
								<td class='tb_bord_baixo' align='center' width='5%'>
									<a href='javascript:;' onclick=" . '"' . "xajax_Deleta_Funcionario_AJAX(" . "'" . $codigoFuncionario . "'" . ");" . '"' . "><img src='../common/img/delete.gif'></a>
								</td>
							</tr>
						</table>
					");
        }

        // adiciona a tabela
        $objResponse->addAppend("div_funcionarios", "innerHTML", $tabela);

        // limpa os campos inseridos
        $objResponse->addClear("idfuncionario", "value");
        $objResponse->addClear("idfuncionario_Nome", "value");
        $objResponse->addClear("idfuncionario_NomeTemp", "value");
        $objResponse->addAssign("idfuncionario_Flag", "className", "nao_selecionou");
    } else {
        $objResponse->addAlert(utf8_encode("Este Funcionário já está na lista!"));
    }

    // retorna o resultado XML
    return $objResponse->getXML();
}

/*
  Função: Verifica_Funcionario_Existe_AJAX
  Verifica se um funcionario ja existe na tabela html
 */

function Verifica_Funcionario_Existe_AJAX($post) {

    for ($i = 1; $i <= intval($post['total_funcionarios']); $i++) {
        if ($post["codigo_funcionario_$i"] == $post['idfuncionario'])
            return true;
    }

    return false;
}

/*
  Função: Deleta_Funcionario_AJAX
  Deleta um funcionario dinamicamente na tabela html
 */

function Deleta_Funcionario_AJAX($codigoFuncionario) {

    // variáveis globais
    global $form;
    global $conf;
    global $db;
    global $falha;

    global $funcionario;
    //---------------------
    // cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

    // nome da tabela criada
    $nome_tabela = "tabela_funcionario_" . $codigoFuncionario;

    // busca os dados do funcionário
    $info_funcionario = $funcionario->getById($codigoFuncionario);


    if (($conf['nome_programa'] == "cliente_fisico") || ($conf['nome_programa'] == "cliente_juridico")) {
        $pergunta = "Deseja desvincular o(a) funcionário(a) '{$info_funcionario['nome_funcionario']}' deste cliente ?";
    } else {
        $pergunta = "Deseja excluir o(a) funcionário(a) '{$info_funcionario['nome_funcionario']}' desta filial ?";
    }

    // verifica se vai remover: Pula 1 linha se clicar no cancelar
    $objResponse->addConfirmCommands(1, utf8_encode($pergunta));

    // remove a tabela
    $objResponse->addRemove($nome_tabela);

    // retorna o resultado XML
    return $objResponse->getXML();
}

/*
  Função: Seleciona_Funcionario_AJAX
  Seleciona os funcionarios que trabalham na filial e colocam eles dinamicamente na tabela html
  Params:
  integer	$codigo Código da filial/cliente
  bool $cliente Indica se é para buscar os funcionários do cliente
 */

function Seleciona_Funcionario_AJAX($codigo, $cliente = false) {

    // variáveis globais
    global $form;
    global $conf;
    global $db;
    global $falha;

    global $funcionario;
    //---------------------
    // cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

    $codigo = (int) $codigo;

    if ($cliente) {
        // busca os funcionários que atendem o cliente
        $list_sql = "
			SELECT
				FUN_CLI.*  , FNC.nome_funcionario, FNC.tipo_vendedor_funcionario, CRG.nome_cargo
			FROM
				{$conf['db_name']}funcionario_cliente FUN_CLI
			INNER JOIN 
				{$conf['db_name']}funcionario FNC ON FUN_CLI.idfuncionario=FNC.idfuncionario
			INNER JOIN
				{$conf['db_name']}cargo CRG ON FNC.idcargo = CRG.idcargo
			WHERE
				FUN_CLI.idcliente = $codigo
			ORDER BY
				FNC.nome_funcionario ASC";
    } else {
        // busca os funcionários da filial
        $list_sql = "
			SELECT
				FLFNC.*  , FNC.*
			FROM
				{$conf['db_name']}filial_funcionario FLFNC
				INNER JOIN {$conf['db_name']}funcionario FNC ON FLFNC.idfuncionario=FNC.idfuncionario
			WHERE
				FLFNC.idfilial = $codigo
			ORDER BY
				FNC.nome_funcionario ASC	";
    }

    // busca os dados
    $list_q = $db->query($list_sql);

    if ($list_q) {

        //busca os registros no banco de dados e monta o vetor de retorno
        $cont = 0;
        $post = array();
        while ($list = $db->fetch_array($list_q)) {
            // Junta os dados da query aos já presentes na requisição
            $post = array_merge($post, $list);
//			$post['idfuncionario'] = $list['idfuncionario'];
            $post['total_funcionarios'] = $cont;

            // acrescenta o XML que foi retornado no objeto atual
            $objResponse->loadXML(Insere_Funcionario_AJAX($post, $cliente));

            $cont++;
        } // fim do while
    }

    // retorna o resultado XML
    return $objResponse->getXML();
}

/*
  Função: Verifica_Campos_Funcionario_AJAX
  Verifica se os campos do funcionario foram preenchidos
 */

function Verifica_Campos_Funcionario_AJAX($post) {

    // vari?veis globais
    global $form;
    global $conf;
    global $db;
    global $falha;
    global $err;

    global $funcionario;
    //---------------------
    // cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

    if ($_GET['ac'] == "editar") {

        $form->chk_empty($post['litnome_funcionario'], 1, 'Nome do funcionário');
        $form->chk_empty($post['litsexo_funcionario'], 1, 'Sexo do funcionário');
        $form->chk_empty($post['numidcargo'], 1, 'Cargo');
        $form->chk_empty($post['litidentidade_funcionario'], 1, 'Nº da identidade');
        $form->chk_cpf($post['litcpf_funcionario'], 1);
        $form->chk_IsDate($post['litdata_nascimento_funcionario'], "Data de nascimento");
        $form->chk_IsDate($post['litdata_admissao_funcionario'], "Data de admissão");

        if (!empty($post['litdata_demissao_funcionario']))
            $form->chk_IsDate($post['litdata_demissao_funcionario'], "Data de demissão");

        if (!empty($post['litdata_emissao']))
            $form->chk_IsDate($post['litdata_emissao'], "A Data de Emissão na Empresa");

        // se digitou a senha ou o login, verifica
        if (($post['login_funcionario_vazio'] != "") || ($post['senha_funcionario'] != "") || ($post['re_senha_funcionario'] != "")) {

            // se digitou algo no login, entao verifica se ele existe
            if ($post['login_funcionario_vazio'] != "") {
                $form->chk_login($post['login_funcionario_vazio']);
            }
            // se o login ja existe, entao verifica se ja digitou a senha atual
            else {
                $form->chk_empty($post['senha_atual_funcionario'], 1, "a senha atual");

                // verifica se a senha atual está correta
                if ($post['senha_atual_funcionario'] != "") {
                    $info_senha_atual = $funcionario->getById($_GET['idfuncionario']);

                    if ($info_senha_atual['senha_funcionario'] != md5($post['senha_atual_funcionario'])) {
                        $form->err[] = "A senha atual está errada! Redigite novamente!";
                    }
                }
            }

            $form->chk_empty($post['re_senha_funcionario'], 1, "senha");
            $form->chk_senha($post['senha_funcionario'], $post['re_senha_funcionario']);
        }


        $err = $form->err;

        // verifica se o CPF do funcionario está duplicado
        if ($funcionario->Verifica_CPF_Duplicado($post['litcpf_funcionario'], $_GET['idfuncionario']))
            $err[] = "Este CPF já existe e não pode ser duplicado!";
        $info_funcionario = $funcionario->getById($_GET['idfuncionario']);



        if ($info_funcionario['idcargo'] != $post['numidcargo'])
            $objResponse->addConfirmCommands(1, utf8_encode("Você está alterando o cargo de um funcionário. Junto com o cargo, suas permissões irão ser redefinidas. Deseja realmente alterar o cargo do funcionário '{$post['litnome_funcionario']}' ? "));
    }
    else {

        $form->chk_empty($post['nome_funcionario'], 1, 'Nome do funcionário');
        $form->chk_empty($post['sexo_funcionario'], 1, 'Sexo do funcionário');
        $form->chk_empty($post['idcargo'], 1, 'Cargo');
        $form->chk_empty($post['identidade_funcionario'], 1, 'Nº da identidade');
        $form->chk_cpf($post['cpf_funcionario'], 1);
        $form->chk_IsDate($post['data_admissao_funcionario'], "Data de admissão");

        if (!empty($post['data_emissao']))
            $form->chk_IsDate($post['data_emissao'], "A Data de Emissão na Empresa");
        if (!empty($post['data_demissao_funcionario']))
            $form->chk_IsDate($post['data_demissao_funcionario'], "A Data de Demissão");

        if (!empty($post['data_demissao_funcionario']))
            $form->chk_IsDate($post['data_demissao_funcionario'], "Data de demissão");

        if (($post['login_funcionario'] != "") || ($post['senha_funcionario'] != "") || ($post['re_senha_funcionario'] != "")) {
            $form->chk_login($post['login_funcionario']);
            $form->chk_empty($post['re_senha_funcionario'], 1, "senha");
            $form->chk_senha($post['senha_funcionario'], $post['re_senha_funcionario']);
        }

        $err = $form->err;

        if ($funcionario->Verifica_CPF_Duplicado($post['cpf_funcionario']))
            $err[] = "Este CPF já existe e não pode ser duplicado!";
    }


    // se nao houveram erros, da o submit no form
    if (count($err) == 0) {

        if ($post['litsituacao_funcionario'] == 'I') {
            // informa o usuario q o funcionario inativo é apago de suas respectivas filiais.
            $objResponse->addConfirmCommands(1, utf8_encode("Deseja alterar a situação do funcionário '{$post['litnome_funcionario']}' para inativo ? Se sim, o funcionário será excluido de suas respectivas filiais e não mais listado."));


            $objResponse->addScript("document.getElementById('for_funcionario').submit();");
        } else {
            $objResponse->addScript("document.getElementById('for_funcionario').submit();");
        }
    }
    // houve erros, logo mostra-os
    else {
        $mensagem = implode("\n", $err);
        $objResponse->addAlert(utf8_encode(html_entity_decode(strip_tags($mensagem))));
    }

    // retorna o resultado XML
    return $objResponse->getXML();
}

