<?php

//inclusão de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");

require_once("../entidades/cliente_condominio.php");
require_once("../entidades/cliente_juridico.php");
require_once("../entidades/cliente_fisico.php");
require_once("../entidades/cliente.php");
require_once("../entidades/endereco.php");
require_once("../entidades/funcionario_cliente.php");
require_once("../entidades/parametros.php");

// inicializa templating
$smarty = new Smarty;

// ação selecionada
$flags['action'] = $_GET['ac'];

// inicializa autenticação
$auth = new auth();

//inicializa classe
$cliente_condominio = new cliente_condominio();
$cliente_juridico = new cliente_juridico();
$cliente_fisico = new cliente_fisico();
$cliente = new cliente();
$endereco = new endereco();
$funcionario_cliente = new funcionario_cliente();
$parametros = new parametros();


// inicializa banco de dados
$db = new db();

//incializa classe para validação de formulário
$form = new form();



switch ($flags['action']) {

    // busca os clientes de acordo com a busca
    case "busca_cliente":

        $cliente->Filtra_Cliente_AJAX($_GET['typing'], $_GET['campoID'], $_GET['mostraDetalhes'],1,$_GET['inserirEndereco']);
        
    break;

    // busca os clientes de acordo com a busca
    case "busca_cliente_fisico":

        $cliente_fisico->Filtra_Cliente_AJAX($_GET['typing'], $_GET['campoID'], $_GET['mostraDetalhes']);

    break;

    // busca os clientes de acordo com a busca
    case "busca_cliente_juridico":

        $cliente_juridico->Filtra_Cliente_AJAX($_GET['typing'], $_GET['campoID'], $_GET['mostraDetalhes']);

    break;

    case "busca_cliente_condominio":

        $cliente_condominio->Filtra_Cliente_AJAX($_GET['typing'], $_GET['campoID'], $_GET['mostraDetalhes']);

    break;

    case 'buscaCondominiosCliente':

        /// Busca os condomínios do cliente passar como parâmetro
        $condominiosCliente = $cliente_condominio->getCondominiosCliente($_GET['cliente']);

        foreach($condominiosCliente as $indice => $dados){
            $condominiosCliente[$indice]['nome_cliente'] = utf8_encode($dados['nome_cliente']);
        }

        echo json_encode($condominiosCliente);

        exit();

    break;
}




function buscaCondominio($idproprietario){

    // variáveis globais
    global $form;
    global $conf;
    global $db;
    global $falha;
    global $err;

    global $cliente_condominio;


    // $sql = "SELECT DISTINCT cliente.idcliente, cliente.nome_cliente " .
    //         " FROM `apartamento` " .
    //         " LEFT JOIN cliente on ( apartamento.idcliente = cliente.idcliente ) " .
    //         " WHERE apartamento.idproprietario = $idmorador";

    $condominiosCliente = $cliente_condominio->getCondominiosCliente($idproprietario);


echo '<pre>';
var_dump($condominiosCliente);
echo '</pre>';

}


/*
  Função: Insere_Cliente_AJAX
  Insere um cliente dinamicamente na tabela html
 */

function Insere_Cliente_AJAX($post) {

    // variáveis globais
    global $form;
    global $conf;
    global $db;
    global $falha;
    global $err;


    global $cliente;
    global $endereco;
    //---------------------
    // cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

    // codigo do funcionario
    $codigoCliente = $post['idcliente'];

    $form->chk_empty($post['idcliente'], 1, 'Cliente');

    $err = $form->err;

    // se houveram erros, mostra-os
    if (count($err) != 0) {
        $mensagem = implode("\n", $err);
        $objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
    }
    // se nao houveram erros, verifica se ele já nao existe na tabela
    else if (Verifica_Cliente_Existe_AJAX($post) == false) {

        // incrementa 1 na quantidade de clientes
        $total_clientes = intval($post['total_clientes']) + 1;
        $objResponse->addAssign("total_clientes", "value", $total_clientes);

        // busca os dados do cliente
        $info_cliente = $cliente->getById($codigoCliente);

        // nome da tabela criada
        $nome_tabela = "tabela_cliente_" . $codigoCliente;

        if (!empty($info_cliente['idendereco_cliente'])) {

            $dados_endereco = $endereco->getById($info_cliente['idendereco_cliente']);

            if (!empty($dados_endereco['nome_cidade']) && !empty($dados_endereco['sigla_estado']))
                $info_cliente['cidade_estado'] = $dados_endereco['nome_cidade'] . ' / ' . $dados_endereco['sigla_estado'];
            else
                $info_cliente['cidade_estado'] = $dados_endereco['nome_cidade'] . $dados_endereco['sigla_estado'];
        }


        if (!empty($info_cliente['telefone_cliente']))
            $info_cliente['telefone_cliente'] = $form->FormataTelefoneParaExibir($info_cliente['telefone_cliente']);

        // tabela de funcioanrio
        $tabela = utf8_encode("
						<table width='100%' align='left' cellpadding='5' id='$nome_tabela'>
							<tr>
								<td class='tb_bord_baixo' align='left' width='40%'>
									<input type='hidden' name='codigo_cliente_$total_clientes' id='codigo_cliente_$total_clientes' value='$codigoCliente' />
									{$info_cliente['nome_cliente']}
								</td>
									
								<td class='tb_bord_baixo' align='left' width='25%'>&nbsp;{$info_cliente['telefone_cliente']}</td>
								<td class='tb_bord_baixo' align='left' width='25%'>&nbsp;{$info_cliente['cidade_estado']}</td>
								<td class='tb_bord_baixo' align='center' width='10%'>
									<a href='javascript:;' onclick=" . '"' . "xajax_Deleta_Cliente_AJAX(" . "'" . $codigoCliente . "'" . ");" . '"' . "><img src='../common/img/delete.gif'></a>
								</td>
							</tr>
						</table><br>
					");

        // adiciona a tabela
        $objResponse->addAppend("div_clientes", "innerHTML", $tabela);

        // limpa os campos inseridos
        $objResponse->addClear("idcliente", "value");
        $objResponse->addClear("idcliente_Nome", "value");
        $objResponse->addClear("idcliente_NomeTemp", "value");
        $objResponse->addAssign("idcliente_Flag", "className", "nao_selecionou");
    }
    else {
        $objResponse->addAlert(utf8_encode("Este Cliente já está na lista!"));
    }

    // retorna o resultado XML
    return $objResponse->getXML();
}

/*
  Função: Verifica_Cliente_Existe_AJAX
  Verifica se um funcionario ja existe na tabela html
 */

function Verifica_Cliente_Existe_AJAX($post) {

    for ($i = 1; $i <= intval($post['total_clientes']); $i++) {
        if ($post["codigo_cliente_$i"] == $post['idcliente'])
            return true;
    }

    return false;
}

/*
  Função: Deleta_Cliente_AJAX
  Deleta um funcionario dinamicamente na tabela html
 */

function Deleta_Cliente_AJAX($codigoCliente) {

    // variáveis globais
    global $form;
    global $conf;
    global $db;
    global $falha;

    global $cliente;
    //---------------------
    // cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

    // nome da tabela criada
    $nome_tabela = "tabela_cliente_" . $codigoCliente;

    // busca os dados do funcionário
    $info_cliente = $cliente->getById($codigoCliente);

    // verifica se vai remover: Pula 1 linha se clicar no cancelar
    $objResponse->addConfirmCommands(1, utf8_encode("Deseja desvincular o(a) cliente(a) '{$info_cliente['nome_cliente']}' deste funcionário ?"));

    // remove a tabela
    $objResponse->addRemove($nome_tabela);

    // retorna o resultado XML
    return $objResponse->getXML();
}

/*
  Função: Seleciona_Cliente_AJAX
  Seleciona os clientes que o funcionário atende e os colocam dinamicamente na tabela html
  Params:
  integer	$idfuncionario Código do funcionário
 */

function Seleciona_Cliente_AJAX($idfuncionario) {

    // variáveis globais
    global $form;
    global $conf;
    global $db;
    global $falha;

    global $funcionario_cliente;
    //---------------------
    // cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

    $idfuncionario = (int) $idfuncionario;

    //Busca os clients que o funcionário atende
    $filtro = 'WHERE idfuncionario = ' . $idfuncionario;
    $clientes = $funcionario_cliente->make_list_select($filtro);

    //Faz um loop para inseri-los na tabela		
    foreach ($clientes['idcliente'] as $key => $idcliente) {
        $post['idcliente'] = $idcliente;
        $post['total_clientes'] = $key;
        $objResponse->loadXML(Insere_Cliente_AJAX($post));
    }

    // retorna o resultado XML
    return $objResponse->getXML();
}

/*
  Função: Verifica_Campos_ClienteFisico_AJAX
  Verifica se os campos do cliente fisico foram preenchidos
 */

function Verifica_Campos_ClienteFisico_AJAX($post) {

    // variáveis globais
    global $form;
    global $conf;
    global $db;
    global $falha;
    global $err;
    global $parametros;
    global $cliente;
    global $cliente_fisico;
    //---------------------
    // cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

    if($post['senha_cliente'] || $post['re_senha_cliente']){  
        
        $tamanhoMinSenha = $parametros->getParam('tamanhoMinSenha');
        if(strlen($post['senha_cliente']) < $tamanhoMinSenha){      
            $form->err[] = "A senha deve conter no mínimo $tamanhoMinSenha caracteres";
        }
        else{
            $form->chk_senha($post['senha_cliente'], $post['re_senha_cliente']);  
        }
             
    }

    
        
    if ($_GET['ac'] == "editar") {
        
        $form->chk_empty($post['litnome_cliente'], 1, 'Nome do cliente');
        $form->chk_cpf($post['litcpf_cliente'], 0);
        $form->chk_empty($post['litsexo_cliente'], 1, 'Sexo do cliente');
        $form->chk_empty($post['numidramo_atividade'], 0, 'Ramo de atividade');

        if ($post['litcliente_bloqueado'] == "1") {
            $form->chk_empty($post['numidmotivo_bloqueio'], 1, 'Motivo de bloqueio');
            $form->chk_IsDate($post['litdata_bloqueio_cliente'], 'Data de bloqueio do cliente');
        }

        if( (!empty($post['senha_cliente']) || !empty($post['re_senha_cliente']) ) && empty($post['litcpf_cliente']) ){  
            $form->err[] = 'Para definir uma senha de acesso, é necessário que o CPF seja cadastrado';
        }
        
        $err = $form->err;

        // verifica se o CPF do cliente está duplicado
        if ($post['litcpf_cliente'] != '') {
            $post['litcpf_cliente'] = $form->FormataCPFParaInserir($post['litcpf_cliente']);
            // if ($cliente_fisico->Verifica_CPF_Duplicado($post['litcpf_cliente'],(int)$_GET['idcliente']))
            //     $err[] = "Este CPF já existe e não pode ser duplicado!";
        }
    }
    else {
        
        if( (!empty($post['senha_cliente']) || !empty($post['re_senha_cliente']) ) && empty($post['cpf_cliente']) ){  
            $form->err[] = 'Para definir uma senha de acesso, é necessário que o CPF esteja cadastrado';
        }
        
        $form->chk_empty($post['nome_cliente'], 1, 'Nome do cliente');
        $form->chk_cpf($post['cpf_cliente'], 0);
        $form->chk_empty($post['sexo_cliente'], 1, 'Sexo do cliente');
        $form->chk_empty($post['idramo_atividade'], 0, 'Ramo de atividade');

        if ($post['cliente_bloqueado'] == "1") {
            $form->chk_empty($post['idmotivo_bloqueio'], 1, 'Motivo de bloqueio');
            $form->chk_IsDate($post['data_bloqueio_cliente'], 'Data de bloqueio do cliente');
        }

        $err = $form->err;

        // verifica se o CPF do cliente está duplicado
        if ($post['cpf_cliente'] != '') {
            $post['cpf_cliente'] = $form->FormataCPFParaInserir($post['cpf_cliente']);
            // if ($cliente_fisico->Verifica_CPF_Duplicado($post['cpf_cliente']))
            //     $err[] = "Este CPF já existe e não pode ser duplicado!";
        }
    }

    
   
    
    
    // se nao houveram erros, da o submit no form
    if (count($err) == 0) {
        $objResponse->addScript("document.getElementById('for_cliente').submit();");
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
  Função: Verifica_Campos_ClienteJuridico_AJAX
  Verifica se os campos do cliente juridico foram preenchidos
 */

function Verifica_Campos_ClienteJuridico_AJAX($post) {

    // variáveis globais
    global $form;
    global $conf;
    global $db;
    global $falha;
    global $err;

    global $cliente_juridico;
    global $cliente;
    global $parametros;
    //---------------------
    // cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

    
    if($post['senha_cliente'] || $post['re_senha_cliente']){  
        
        $tamanhoMinSenha = $parametros->getParam('tamanhoMinSenha');
        if(strlen($post['senha_cliente']) < $tamanhoMinSenha){      
            $form->err[] = "A senha deve conter no mínimo $tamanhoMinSenha caracteres";
        }
        else{
            $form->chk_senha($post['senha_cliente'], $post['re_senha_cliente']);  
        }
             
    }
    

    if ($_GET['ac'] == "editar") {
        $form->chk_empty($post['litnome_cliente'], 1, 'Nome do cliente');
        $form->chk_cnpj($post['litcnpj_cliente'], 0);
        $form->chk_empty($post['numidramo_atividade'], 0, 'Ramo de atividade');

        if ($post['litcliente_bloqueado'] == "1") {
            $form->chk_empty($post['numidmotivo_bloqueio'], 1, 'Motivo de bloqueio');
            $form->chk_IsDate($post['litdata_bloqueio_cliente'], 'Data de bloqueio do cliente');
        }
    
        
        if ((isset($post['litvencimento_boleto_cliente'])) && ((int) $post['litvencimento_boleto_cliente'] < 1 || (int) $post['litvencimento_boleto_cliente'] > 31 )) {
            $form->err[] = "O Dia de Vencimento do Boleto é inválido.";
        }

        $err = $form->err;

        // verifica se o CNPJ do cliente está duplicado
        $post['litcnpj_cliente'] = $form->FormataCNPJParaInserir($post['litcnpj_cliente']);

        // if (!empty($post['litcnpj_cliente']) && $cliente_juridico->Verifica_CNPJ_Duplicado($post['litcnpj_cliente'], $_GET['idcliente'])) {
        //     $err[] = "Este CNPJ já existe e não pode ser duplicado!";
        // }
    } else {
        $form->chk_empty($post['nome_cliente'], 1, 'Nome do cliente');
        $form->chk_cnpj($post['cnpj_cliente'], 0);
        $form->chk_empty($post['idramo_atividade'], 0, 'Ramo de atividade');

        if ($post['cliente_bloqueado'] == "1") {
            $form->chk_empty($post['idmotivo_bloqueio'], 1, 'Motivo de bloqueio');
            $form->chk_IsDate($post['data_bloqueio_cliente'], 'Data de bloqueio do cliente');
        }


        if ((isset($post['vencimento_boleto_cliente'])) && ((int) $post['vencimento_boleto_cliente'] < 1 || (int) $post['vencimento_boleto_cliente'] > 31 )) {
            $form->err[] = "O Dia de Vencimento do Boleto é inválido.";
        }

        if( (!empty($post['senha_cliente']) || !empty($post['re_senha_cliente']) ) && empty($post['cnpj_cliente']) ){  
            $form->err[] = 'Para definir uma senha de acesso, é necessário que o CNPJ seja cadastrado';
        }

        $err = $form->err;

        // verifica se o CNPJ do cliente está duplicado
        $post['cnpj_cliente'] = $form->FormataCNPJParaInserir($post['cnpj_cliente']);
        // if (!empty($post['cnpj_cliente']) && $cliente_juridico->Verifica_CNPJ_Duplicado($post['cnpj_cliente']))
        //     $err[] = "Este CNPJ já existe e não pode ser duplicado!";
    }


    // se nao houveram erros, da o submit no form
    if (count($err) == 0) {
        $objResponse->addScript("document.getElementById('for_cliente').submit();");
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
  Função: Verifica_Campos_ClienteCondominio
  Verifica se os campos do cliente juridico foram preenchidos
 */

function Verifica_Campos_ClienteCondominio_AJAX($post) {

    // variáveis globais
    global $form;
    global $conf;
    global $db;
    global $falha;
    global $err;
    global $cliente_condominio;
    //---------------------
    // cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();


    if ($_GET['ac'] == "editar") {
        $form->chk_empty($post['litnome_cliente'], 1, 'Nome do cliente');
        $form->chk_cnpj($post['litcnpj'], 0);
        $form->chk_empty($post['numidramo_atividade'], 0, 'Ramo de atividade');

        if ($post['litcliente_bloqueado'] == "1") {
            $form->chk_empty($post['numidmotivo_bloqueio'], 1, 'Motivo de bloqueio');
            $form->chk_IsDate($post['litdata_bloqueio_cliente'], 'Data de bloqueio do cliente');
        }

        $err = $form->err;

        // verifica se o CNPJ do cliente está duplicado
        if ($post['litcnpj'] != '') {
            $post['litcnpj'] = $form->FormataCNPJParaInserir($post['litcnpj']);
            // if ($cliente_condominio->Verifica_CNPJ_Duplicado($post['litcnpj'], $_GET['idcliente']))
            //     $err[] = "Este CNPJ já existe e não pode ser duplicado!";
        }
    }
    else {
        $form->chk_empty($post['nome_cliente'], 1, 'Nome do cliente');
        $form->chk_cnpj($post['cnpj'], 0);
        $form->chk_empty($post['idramo_atividade'], 0, 'Ramo de atividade');

        if ($post['cliente_bloqueado'] == "1") {
            $form->chk_empty($post['idmotivo_bloqueio'], 1, 'Motivo de bloqueio');
            $form->chk_IsDate($post['data_bloqueio_cliente'], 'Data de bloqueio do cliente');
        }

        $err = $form->err;

        // verifica se o CNPJ do cliente está duplicado
        if ($post['cnpj'] != '') {
            $post['cnpj'] = $form->FormataCNPJParaInserir($post['cnpj']);
            // if ($cliente_condominio->Verifica_CNPJ_Duplicado($post['cnpj']))
            //     $err[] = "Este CNPJ já existe e não pode ser duplicado!";
        }
    }
    

    // se nao houveram erros, da o submit no form
    if (count($err) == 0) {
        $objResponse->addScript("document.getElementById('for_cliente').submit();");
    }
    // houve erros, logo mostra-os
    else {
        $mensagem = implode("\n", $err);
        $objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
    }

    // retorna o resultado XML
    return $objResponse->getXML();
}

function Cadastro_Rapido_ClienteFisico_AJAX($post, $campo_padrao = 'cliente') {

	// variáveis globais
	global $form, $conf, $db, $falha, $err, $cliente, $cliente_fisico;
	//---------------------
	// cria o objeto xajaxResponse
	$objResponse = new xajaxResponse();

	//Valida campos
	$form->chk_empty($post["nome_$campo_padrao"], 1, "Nome do $campo_padrao");
	$form->chk_empty($post["sexo_$campo_padrao"], 1, "Sexo do $campo_padrao");
	$form->chk_cpf($post["cpf_$campo_padrao"], 0);
	$form->chk_mail($post["email_$campo_padrao"], 0);
	
	// verifica se o CPF do cliente está duplicado
    if (!empty($post["cpf_$campo_padrao"])) {
    	 
    	 //Formata o CPF
         $post["cpf_$campo_padrao"] = $form->FormataCPFParaInserir($post["cpf_$campo_padrao"]);

         //Confere duplicidade
         // if ($cliente_fisico->Verifica_CPF_Duplicado($post["cpf_$campo_padrao"]))
         //     $form->err[] = "Este CPF já existe e não pode ser duplicado!";
    }
	
    //Recupera os erros de validação do formulário
	$err = $form->err;
	
	if(count($err)){
		$mensagem = implode("\n", $err);
	}
	else{
		
		$post["telefone_$campo_padrao"] = $form->FormataTelefoneParaInserir($post['telefone_'.$campo_padrao.'_ddd'], $post["telefone_$campo_padrao"]);
		
		//Seta os campos para gravar na tabela "cliente"
		$setCliente = array(		
				'nome_cliente' 			=> $db->escape(utf8_decode($post["nome_$campo_padrao"])),
				'telefone_cliente' 		=> $db->escape(utf8_decode($post["telefone_$campo_padrao"])),
				'email_cliente' 		=> $db->escape(utf8_decode($post["email_$campo_padrao"]))
		);
				
		//grava o registro no banco de dados
		$idcliente = $cliente->set($setCliente);
		if(!empty($cliente->err)) $err[] = $cliente->err;
		
		//Grava os dados de cliente físico
		$cliente_fisico->set(array(
							'idcliente'    => $idcliente,
							'cpf_cliente'  => $db->escape($post["cpf_$campo_padrao"]),
							'sexo_cliente' => $db->escape($post["sexo_$campo_padrao"])
						 ));
		if(!empty($cliente_fisico->err)) $err[] = $cliente_fisico->err;
		
		
		if(!count($err)){
			//Seta a mensagem de sucesso
			$mensagem = "Cliente inserido com sucesso!";
			
			//Limpa os campos do formulário
			$campos = array("nome_$campo_padrao",'telefone_'.$campo_padrao.'_ddd',
							 "telefone_$campo_padrao", "email_$campo_padrao","cpf_$campo_padrao");
			
			foreach($campos as $campo){				
				$objResponse->addClear($campo, 'value');
			}
			
			//Seleciona o cliente recém-cadastrado
			$objResponse->addAssign('id'.$campo_padrao, 'value', $idcliente );
			$objResponse->addAssign('id'.$campo_padrao.'_NomeTemp', 'value',$post["nome_$campo_padrao"]);
			$objResponse->addAssign('id'.$campo_padrao.'_Nome', 'value', $post["nome_$campo_padrao"]);
			$objResponse->addAssign('id'.$campo_padrao.'_Flag', 'className', 'selecionou');
			
			//Fecha a Lightbox
			$objResponse->addScript('fecharLightbox('.$campo_padrao.'_conteudo)');
			
			
		}
		else{
			//Seta mensagens de erro
			$mensagem = implode("\n", $err);
		}
		
		
	}

	//Exibe a(s) mensagem(s) para o usuário
	$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
	
	// retorna o resultado XML
	return $objResponse->getXML();
	
}



function Cadastro_Rapido_ClienteJuridico_AJAX($post) {

	// variáveis globais
	global $form, $conf, $db, $falha, $err, $cliente, $cliente_juridico;
	//---------------------
	// cria o objeto xajaxResponse
	$objResponse = new xajaxResponse();

	//Valida campos
	$form->chk_empty($post['nome_cliente'], 1, 'Nome do cliente');
	$form->chk_cnpj($post['cnpj_cliente'], 0);
	$form->chk_mail($post['email_cliente'], 0);

	// verifica se o CPF do cliente está duplicado
	if (!empty($post['cnpj_cliente'])) {

		// verifica se o CNPJ do cliente está duplicado
        $post['cnpj_cliente'] = $form->FormataCNPJParaInserir($post['cnpj_cliente']);
        // if (!empty($post['cnpj_cliente']) && $cliente_juridico->Verifica_CNPJ_Duplicado($post['cnpj_cliente']))
        //     $err[] = "Este CNPJ já existe e não pode ser duplicado!";
	}

	//Recupera os erros de validação do formulário
	$err = $form->err;

	if(count($err)){
		$mensagem = implode("\n", $err);
	}
	else{

		$post['telefone_cliente'] = $form->FormataTelefoneParaInserir($post['telefone_cliente_ddd'], $post['telefone_cliente']);
		
		//Seta os campos para gravar na tabela "cliente"
		$setCliente = array(
				'nome_cliente' 			=> $db->escape(utf8_decode($post['nome_cliente'])),
				'telefone_cliente' 		=> $db->escape(utf8_decode($post['telefone_cliente'])),
				'email_cliente' 		=> $db->escape(utf8_decode($post['email_cliente']))
		);

		//grava o registro no banco de dados
		$idcliente = $cliente->set($setCliente);
		if(!empty($cliente->err)) $err[] = $cliente->err;

		//Grava os dados de cliente físico
		$cliente_juridico->set(array(
				'idcliente'    => $idcliente,
				'cnpj_cliente' => $db->escape(utf8_decode($post['cnpj_cliente'])),
				'nome_contato' => $db->escape(utf8_decode($post['nome_contato']))
		));
		if(!empty($cliente_juridico->err)) $err[] = $cliente_fisico->err;


		if(!count($err)){
			//Seta a mensagem de sucesso
			$mensagem = "Cliente inserido com sucesso!";

			//Limpa os campos do formulário
			$campos = array("nome_cliente",'telefone_cliente_ddd', "telefone_cliente", "email_cliente","cnpj_cliente");
			foreach($campos as $campo){
				$objResponse->addClear($campo, 'value');
			}
								
			//Seleciona o cliente recém-cadastrado
			$objResponse->addAssign('idcliente', 'value', $idcliente );
			$objResponse->addAssign('idcliente_NomeTemp', 'value',$post['nome_cliente']);
			$objResponse->addAssign('idcliente_Nome', 'value', $post['nome_cliente']);
			$objResponse->addAssign('idcliente_Flag', 'className', 'selecionou');
				
			//Fecha a Lightbox
			$objResponse->addScript('fecharLightbox(cliente_conteudo)');
				
				
		}
		else{
			//Seta mensagens de erro
			$mensagem = implode("\n", $err);
		}


	}

	//Exibe a(s) mensagem(s) para o usuário
	$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));

	// retorna o resultado XML
	return $objResponse->getXML();

}







function Cadastro_Rapido_ClienteCondominio_AJAX($post) {

	// variáveis globais
	global $form, $conf, $db, $falha, $err, $cliente, $cliente_condominio;
	//---------------------
	// cria o objeto xajaxResponse
	$objResponse = new xajaxResponse();

	//Valida campos
	$form->chk_empty($post['nome_cliente'], 1, 'Nome do cliente');
	$form->chk_cnpj($post['cnpj_cliente'], 0);
	$form->chk_mail($post['email_cliente'], 0);

	// verifica se o CPF do cliente está duplicado
	if (!empty($post['cnpj_cliente'])) {

		// verifica se o CNPJ do cliente está duplicado
		$post['cnpj_cliente'] = $form->FormataCNPJParaInserir($post['cnpj_cliente']);
		// if (!empty($post['cnpj_cliente']) && $cliente_condominio->Verifica_CNPJ_Duplicado($post['cnpj_cliente']))
		// 	$err[] = "Este CNPJ já existe e não pode ser duplicado!";
	}

	//Recupera os erros de validação do formulário
	$err = $form->err;

	if(count($err)){
		$mensagem = implode("\n", $err);
	}
	else{

		$post['telefone_cliente'] = $form->FormataTelefoneParaInserir($post['telefone_cliente_ddd'], $post['telefone_cliente']);

		//Seta os campos para gravar na tabela "cliente"
		$setCliente = array(
				'nome_cliente' 			=> $db->escape(utf8_decode($post['nome_cliente'])),
				'telefone_cliente' 		=> $db->escape(utf8_decode($post['telefone_cliente'])),
				'email_cliente' 		=> $db->escape(utf8_decode($post['email_cliente']))
		);

		//Inicializa a transação no Banco de Dados
		$db->query('BEGIN');
		
		//grava o registro no banco de dados
		$idcliente = $cliente->set($setCliente);
		if(!empty($cliente->err)) $err[] = $cliente->err;

		//Grava os dados de cliente físico
		$cliente_condominio->set(array(
				'idcliente'    => $idcliente,
				'cnpj' => $db->escape($post['cnpj_cliente']),
		));
		if(!empty($cliente_condominio->err)) $err[] = $cliente_condominio->err;


		if(!count($err)){
			
			$db->query('COMMIT');
			
			//Seta a mensagem de sucesso
			$mensagem = "Cliente inserido com sucesso!";

			//Limpa os campos do formulário
			$campos = array("nome_cliente",'telefone_cliente_ddd', "telefone_cliente", "email_cliente","cnpj_cliente");
			foreach($campos as $campo){
				$objResponse->addClear($campo, 'value');
			}
				

			//Seleciona o cliente recém-cadastrado
			$objResponse->addAssign('idcliente', 'value', $idcliente );
			$objResponse->addAssign('idcliente_NomeTemp', 'value',$post['nome_cliente']);
			$objResponse->addAssign('idcliente_Nome', 'value', $post['nome_cliente']);
			$objResponse->addAssign('idcliente_Flag', 'className', 'selecionou');

			//Fecha a Lightbox
			$objResponse->addScript('fecharLightbox(cliente_conteudo)');


		}
		else{
			$db->query('ROLLBACK');
			//Seta mensagens de erro
			$mensagem = implode("\n", $err);
		}


	}

	//Exibe a(s) mensagem(s) para o usuário
	$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));

	// retorna o resultado XML
	return $objResponse->getXML();

}
