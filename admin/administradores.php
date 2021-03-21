<?php


// inclusão de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");
require_once("../entidades/administradores.php");

// configurações adicionais
$conf['area'] = "Administradores"; // área

$conf['style'] = "full"; // para pgs COM a coluna da direita

// inicializa templating
$smarty = new Smarty;

// configura diretórios
$smarty->template_dir = "../common/tpl";
$smarty->compile_dir = "../common/tpl_c";

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
			
			//inicializa classe de usuários
	  	$administradores = new administradores();

			// inicializa banco de dados
			$db = new db();
			
			// para validar dados submetidos
			$form = new form();
			
			$list = $auth->check_priv($conf['priv']);
			$aux = $flags['action'];
			if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}


			switch($flags['action']) {

				//listagem dos usuários
				case "listar":

				  //obtém qual página da listagem deseja exibir
				  $pg = intval(trim($_GET['pg']));

				  //se não foi passada a página como parâmetro, faz página default igual à página 0
				  if(!$pg) $pg = 0;

				  //lista usuários
					$list = $administradores->make_list($pg, $conf['rppg']);

					//pega os erros
					$err = $administradores->err;

					//passa a listagem para o template
					$smarty->assign("list", $list);

				break;


				// ação: adicionar <<<<<<<<<<
				case "adicionar":

					if($_POST['for_chk']) {

						$form->chk_empty($_POST['adm_nom'], 1, "o nome do administrador");
						$form->chk_empty($_POST['adm_sex'], 1, "o sexo");
						$form->chk_mail($_POST['adm_ema'], 1, "o email");
						$form->chk_login($_POST['adm_log']);
						$form->chk_empty($_POST['adm_log'], 1, "o login");
						$form->chk_empty($_POST['adm_sen'], 1, "a senha");
						$form->chk_empty($_POST['adm_re_sen'], 1,"a confirmação da senha");
						$form->chk_senha($_POST['adm_sen'],$_POST['adm_re_sen']);

						$err = $form->err;

	         	//se não ocorreram erros
						if(count($err) == 0){

							//grava o usuário no banco de dados
							$administradores->set($_POST);

							//obtém os erros que ocorreram no cadastro
							$err = $administradores->err;

							//se não ocorreram erros
							if(count($err) == 0) {
								$flags['sucesso'] = $conf['inserir'];
								
								//limpa o $flags.action para que seja exibida a listagem
							  $flags['action'] = "listar";

							  //lista usuários
								$list = $administradores->make_list(0, $conf['rppg']);

								//pega os erros
								$err = $administradores->err;

								//envia a listagem para o template
								$smarty->assign("list", $list);
								
							}

						}

					}

				break;
	            
	            
	      // ação: editar <<<<<<<<<<
				case "editar":

					if($_POST['for_chk']) {

						$adm_info = $_POST;

						$form->chk_empty($_POST['litadm_nom'], 1, "o nome do administrador");
						$form->chk_empty($_POST['litadm_sex'], 1, "o sexo");
						$form->chk_mail($_POST['litadm_ema'], 1, "o email");

						$err = $form->err;

	          if(count($err) == 0) {

							$administradores->update($_SESSION['usr_cod'], $_POST);

							//obtém erros
							$err = $administradores->err;

							//se não ocorreram erros
							if(count($err) == 0){

								$flags['sucesso'] = $conf['alterar'];

							  //limpa o $flags.action para que seja exibida a listagem
							  $flags['action'] = "listar";

							  //lista usuários
								$list = $administradores->make_list(0, $conf['rppg']);

								//pega os erros
								$err = $administradores->err;

								//envia a listagem para o template
								$smarty->assign("list", $list);

							}
							
						}

					}
					else {

						//busca detalhes do usuário
						$adm_info = $administradores->getById($_SESSION[usr_cod]);

						//tratamento das informações para fazer o UPDATE
						$adm_info['litadm_nom'] = $adm_info['adm_nom'];
						$adm_info['litadm_sex'] = $adm_info['adm_sex'];
						$adm_info['litadm_ema'] = $adm_info['adm_ema'];

						//obtém os erros
						$err = $administradores->err;
					}
					
					//passa os dados do usuário para o template
					$smarty->assign("adm_info", $adm_info);

				break;
				
				
				// ação: trocar_senha <<<<<<<<<<
				case "trocar_senha":

					if($_POST['for_chk']) {

						$form->chk_empty($_POST['adm_sen'], 1, "a nova senha");
						$form->chk_empty($_POST['adm_re_sen'], 1,"a confirmação da nova senha");
						$form->chk_senha($_POST['adm_sen'],$_POST['adm_re_sen']);
						$form->chk_empty($_POST['adm_sen_atual'], 1,"a senha atual");

						$err = $form->err;

		        if(count($err) == 0) {
		        	
		        	$administradores->change_pass($_SESSION[usr_cod],$_POST['adm_sen'],$_POST['adm_sen_atual']);

							//obtém erros
							$err = $administradores->err;

							//se não ocorreram erros
							if(count($err) == 0){

								$flags['sucesso'] = $conf['senha'];

							  //limpa o $flags.action para que seja exibida a listagem
							  $flags['action'] = "listar";

							  //lista usuários
								$list = $administradores->make_list(0, $conf['rppg']);

								//pega os erros
								$err = $administradores->err;

								//envia a listagem para o template
								$smarty->assign("list", $list);

							}

						}
						
					}

				break;
				
				// mostrar os dados do administrador
				case "mostrar":


					if (isset($_GET['cod'])) {

						//busca detalhes do usuário
						$adm_info = $administradores->getById($_GET['cod']);

						//obtém os erros
						$err = $administradores->err;

						//passa os dados do usuário para o template
						$smarty->assign("adm_info", $adm_info);

					}
				
        break;
        
        
        // deleta um administrador do sistema
        case "excluir":

					//obtém o código do usuário passado na URL
				  $adm_cod = $_GET['id'];

					//verifica se foi comandada a deleção
				  if($_POST['for_chk']){
				  	
				  	$administradores->delete($adm_cod);
				  	
				  	//obtém erros
						$err = $administradores->err;

						//se não ocorreram erros
						if(count($err) == 0){

							$flags['sucesso'] = $conf['excluir'];

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista usuários
							$list = $administradores->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $administradores->err;

							//envia a listagem para o template
							$smarty->assign("list", $list);

						}

					}
       		
        break;
        
            
      }

			// encerra conexão com o banco de dados
			$db->close();

		}
		
	}

	// seta erros
	$smarty->assign("err", $err);
}

// seta flags de conteúdo
$smarty->assign("flags", $flags);

$list_permissao = $auth->check_priv($conf['priv']);
$smarty->assign("list_permissao",$list_permissao);

// mostra output
$smarty->display("adm_administradores.tpl");

?>
