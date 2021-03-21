<?php

  //inclusão de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/rotinas.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");

require_once("../entidades/boleto_instrucao.php");



// configurações anotionais
$conf['area'] = "Instruções de Boleto"; // área


//configuração de estilo
$conf['style'] = "full";

// inicializa templating
$smarty = new Smarty;

// configura diretórios
$smarty->template_dir = "../common/tpl";
$smarty->compile_dir =   "../common/tpl_c";

// seta configurações
$smarty->assign("conf", $conf);

// ação selecionada
$flags['action'] = $_GET['ac'];
if ($flags['action'] == "") $flags['action'] = "listar";

// inicializa autenticação
$auth = new auth();

// verifica requisição de logout
if($flags['action'] == "logout") {
	$auth->logout();
}
else {
	
	// inicializa vetor de erros
	$err = array();
	
	// verifica sessão
	if(!$auth->check_user()) {
		// verifica requisição de login
		if($_POST['usr_chk']) {
			// verifica login
			if(!$auth->login($_POST['usr_log'], $_POST['usr_sen'])) {
				$err = $auth->err;
			}
		}
		else {
			$err = $auth->err;
		}
	}
	
	// conteúdo
	if($auth->check_user()) {
		// verifica privilégios
		if(!$auth->check_priv($conf['priv'])) {
			$err = $auth->err;
		}
		else {
			// libera conteúdo
			$flags['okay'] = 1;
			
			//inicializa classe
			$boleto_instrucao = new boleto_instrucao();
			
			
			
			// inicializa banco de dados
			$db = new db();
			
			//incializa classe para validação de formulário
			$form = new form();
			
			
			$list = $auth->check_priv($conf['priv']);
			$aux = $flags['action'];
			if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}
			
			switch($flags['action']) {
			
			// ação: adicionar <<<<<<<<<<
				case "adicionar":
					
					if($_POST['for_chk']) {
						
						
						
						$form->chk_empty($_POST['texto_instrucao'], 1, 'Instrução'); 
						
						
						$err = $form->err;
						
						if(count($err) == 0) {
														
							
							//grava o registro no banco de dados
							$boleto_instrucao->set($_POST);
							
							
							//obtém os erros que ocorreram no cadastro
							$err = $boleto_instrucao->err;
							
							//se não ocorreram erros
							if(count($err) == 0) {
								$flags['sucesso'] = $conf['inserir'];
								
								//limpa o $flags.action para que seja exibida a listagem
								$flags['action'] = "listar";
								
								//lista
								$list = $boleto_instrucao->make_list(0, $conf['rppg']);
								
								//pega os erros
								$err = $boleto_instrucao->err;
								
								//envia a listagem para o template
								$smarty->assign("list", $list);
								
							}
							
						}
						
					}
					
					
					
					break;
					
					
					//listagem dos registros
				case "listar":
					
					//obtém qual página da listagem deseja exibir
					$pg = intval(trim($_GET['pg']));
					
					//se não foi passada a página como parâmetro, faz página default igual à página 0
					if(!$pg) $pg = 0;
					
					//lista os registros
					$list = $boleto_instrucao->make_list($pg, $conf['rppg']);
					
					//pega os erros
					$err = $boleto_instrucao->err;
					
					//passa a listagem para o template
					$smarty->assign("list", $list);
					
				break;
					
					
				// ação: editar <<<<<<<<<<
				case "editar":
					
					if($_POST['for_chk']) {
						
						
						
						$info = $_POST;
						
						$info['idinstrucao'] = $_GET['idinstrucao']; 
						
						
						$form->chk_empty($_POST['littexto_instrucao'], 1, 'Instrução'); 
						
						
						$err = $form->err;
						
						if(count($err) == 0) {
							
							
							$boleto_instrucao->update($_GET['idinstrucao'], $_POST);
							
							
							
							//obtém erros
							$err = $boleto_instrucao->err;
							
							//se não ocorreram erros
							if(count($err) == 0) {
								$flags['sucesso'] = $conf['alterar'];
								
								//limpa o $flags.action para que seja exibida a listagem
								$flags['action'] = "listar";
								
								//lista
								$list = $boleto_instrucao->make_list(0, $conf['rppg']);
								
								//pega os erros
								$err = $boleto_instrucao->err;
								
								//envia a listagem para o template
								$smarty->assign("list", $list);
								
							}
							
						}
						
					}
					else {
						
						//busca detalhes
						$info = $boleto_instrucao->getById($_GET['idinstrucao']);
						
						//tratamento das informações para fazer o UPDATE
						$info['littexto_instrucao'] = $info['texto_instrucao']; 
						$info['litdescricao_instrucao'] = $info['descricao_instrucao']; 
						
						
						
						
						//obtém os erros
						$err = $boleto_instrucao->err;
					}
					
					
					
					
					
					
					//passa os dados para o template
					$smarty->assign("info", $info);
					
				break;
					
					
					
					// deleta um registro do sistema
				case "excluir":
					
					//verifica se foi pedido a deleção
					if($_POST['for_chk']){
						
						// deleta o registro
						$boleto_instrucao->delete($_GET['idinstrucao']);
						
						//obtém erros
						$err = $boleto_instrucao->err;
						
						//se não ocorreram erros
						if(count($err) == 0){
							$flags['sucesso'] = $conf['excluir'];
							
							
							
						}
						
						//limpa o $flags.action para que seja exibida a listagem
						$flags['action'] = "listar";
						
						//lista registros
						$list = $boleto_instrucao->make_list(0, $conf['rppg']);
						
						//pega os erros
						$err = $boleto_instrucao->err;
						
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

$list_permissao = $auth->check_priv($conf['priv']);
$smarty->assign("list_permissao",$list_permissao);

$smarty->display("adm_boleto_instrucao.tpl");
?>