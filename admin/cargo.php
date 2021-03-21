<?php

  //inclusão de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/rotinas.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");

require_once("../entidades/cargo.php");



// configurações anotionais
$conf['area'] = "Cargo"; // área


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
			$cargo = new cargo();
			
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
						
						$form->chk_empty($_POST['nome_cargo'], 1, 'Nome do cargo'); 
						$err = $form->err;
						
						if(count($err) == 0) {
							
							$_POST['observacao_cargo'] = nl2br($_POST['observacao_cargo']); 
							
							//grava o registro no banco de dados
							$cargo->set($_POST);
							
							
							//obtém os erros que ocorreram no cadastro
							$err = $cargo->err;
							
							//se não ocorreram erros
							if(count($err) == 0) {
								$flags['sucesso'] = $conf['inserir'];
								
								//limpa o $flags.action para que seja exibida a listagem
								$flags['action'] = "listar";
								
								//lista
								$list = $cargo->make_list(0, $conf['rppg']);
								
								//pega os erros
								$err = $cargo->err;
								
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
					$list = $cargo->make_list($pg, $conf['rppg']);
					
					//pega os erros
					$err = $cargo->err;
					
					//passa a listagem para o template
					$smarty->assign("list", $list);
					
					break;
					
					
					// ação: editar <<<<<<<<<<
				case "editar":
					
					if($_POST['for_chk']) {
						
						
						
						$info = $_POST;
						
						$info['idcargo'] = $_GET['idcargo']; 
						
						
						$form->chk_empty($_POST['litnome_cargo'], 1, 'Nome do cargo'); 
						
						
						$err = $form->err;
						
						if(count($err) == 0) {
							
							$_POST['litobservacao_cargo'] = nl2br($_POST['litobservacao_cargo']); 
														
							$cargo->update($_GET['idcargo'], $_POST);
							
							//obtém erros
							$err = $cargo->err;
							
							//se não ocorreram erros
							if(count($err) == 0) {
								$flags['sucesso'] = $conf['alterar'];
								
								//limpa o $flags.action para que seja exibida a listagem
								$flags['action'] = "listar";
								
								//lista
								$list = $cargo->make_list(0, $conf['rppg']);
								
								//pega os erros
								$err = $cargo->err;
								
								//envia a listagem para o template
								$smarty->assign("list", $list);
								
							}
							
						}
						
					}
					else {
						
						//busca detalhes
						$info = $cargo->getById($_GET['idcargo']);
						
						//tratamento das informações para fazer o UPDATE
						$info['litnome_cargo'] = $info['nome_cargo']; 
						$info['litobservacao_cargo'] = strip_tags($info['observacao_cargo']); 
						
						
						
						
						//obtém os erros
						$err = $cargo->err;
					}
					
					
					
					
					
					
					//passa os dados para o template
					$smarty->assign("info", $info);
					
					break;
					
					
					
					// deleta um registro do sistema
				case "excluir":
					
					//verifica se foi pedido a deleção
					if($_POST['for_chk']){
						
						// deleta o registro
						$cargo->delete($_GET['idcargo']);
						
						//obtém erros
						$err = $cargo->err;
						
						//se não ocorreram erros
						if(count($err) == 0){
							$flags['sucesso'] = $conf['excluir'];
							
							
							
						}
						
						//limpa o $flags.action para que seja exibida a listagem
						$flags['action'] = "listar";
						
						//lista registros
						$list = $cargo->make_list(0, $conf['rppg']);
						
						//pega os erros
						$err = $cargo->err;
						
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

$smarty->display("adm_cargo.tpl");
?>

