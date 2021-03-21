<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/bairro.php");
	require_once("../entidades/cidade.php"); 
	require_once("../entidades/estado.php");
	

  // configurações anotionais
  $conf['area'] = "Bairro"; // área


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
	  		$bairro = new bairro();
	  		
	  		$cidade = new cidade(); 
	  		$estado = new estado();
				
	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para validação de formulário
        $form = new form();
        
        $list = $auth->check_priv($conf['priv']);
				$aux = $flags['action'];
				if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}

				
        switch($flags['action']) {

					// busca genérica
          case "busca_generica":

            if ( ($_POST['for_chk']) || ($_GET['rpp'] != "") ) {

            	$flags['fez_busca'] = 1;

							if ($_POST['for_chk']) {
								$flags['busca'] = $_POST['busca'];
								$flags['rpp'] = $_POST['rpp'];
							}
							else {
								$flags['busca'] = $_GET['busca'];
								$flags['rpp'] = $_GET['rpp'];
							}

							if ($_GET['target'] == "full") $flags['rpp'] = 9999999;


						  //obtém qual página da listagem deseja exibir
						  $pg = intval(trim($_GET['pg']));

						  //se não foi passada a página como parâmetro, faz página default igual à página 0
						  if(!$pg) $pg = 0;

						  //lista os registros
							$list = $bairro->Busca_Generica($pg, $flags['rpp'], $flags['busca'], "", "ac=busca_generica&busca=".$flags['busca']."&rpp=".$flags['rpp']);

							//pega os erros
							$err = $bairro->err;

							//passa a listagem padicionarara o template
							$smarty->assign("list", $list);

						}

						if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];

          break;




          // ação: adicionar <<<<<<<<<<
          case "adicionar":

            if($_POST['for_chk']) {
            	
            	
            	$form->chk_empty($_POST['idestado'], 1, 'Estado');
							$form->chk_empty($_POST['idcidade'], 1, 'Cidade'); 
							$form->chk_empty($_POST['nome_bairro'], 1, 'Nome do bairro'); 
							

              $err = $form->err;

              if(count($err) == 0) {

								
	              
	              
	              
								
								

	              
								//grava o registro no banco de dados
								$bairro->set($_POST);


								//obtém os erros que ocorreram no cadastro
								$err = $bairro->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['inserir'];

									//limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $bairro->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $bairro->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}
								
              }
              
            }
            
            $list_cidade = $cidade->make_list_select();
						$smarty->assign("list_cidade",$list_cidade);

						

          break;


					//listagem dos registros
          case "listar":
          
					  //obtém qual página da listagem deseja exibir
					  $pg = intval(trim($_GET['pg']));

					  //se não foi passada a página como parâmetro, faz página default igual à página 0
					  if(!$pg) $pg = 0;

					  //lista os registros
						$list = $bairro->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $bairro->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          break;
          
          
          // ação: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idbairro'] = $_GET['idbairro']; 
							
							$form->chk_empty($_POST['idestado'], 1, 'Estado');
							$form->chk_empty($_POST['numidcidade'], 1, 'Cidade'); 
							$form->chk_empty($_POST['litnome_bairro'], 1, 'Nome do bairro'); 
							
							
							$err = $form->err;

		          if(count($err) == 0) {

								
								
								
								
		          	
								


								$bairro->update($_GET['idbairro'], $_POST);
								
								

								//obtém erros
								$err = $bairro->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['alterar'];

								  //limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $bairro->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $bairro->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}

							}

						}
						else {

							//busca detalhes
							$info = $bairro->getById($_GET['idbairro']);

							//tratamento das informações para fazer o UPDATE
							$info['numidcidade'] = $info['idcidade']; 
							$info['litnome_bairro'] = $info['nome_bairro']; 
							
							$info['idestado_Nome'] = $info['nome_estado'];
							$info['idestado_NomeTemp'] = $info['nome_estado'];

							$info['idcidade_Nome'] = $info['nome_cidade'];
							$info['idcidade_NomeTemp'] = $info['nome_cidade'];

							//obtém os erros
							$err = $bairro->err;
						}

            $list_cidade = $cidade->make_list_select();
						$smarty->assign("list_cidade",$list_cidade);



						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a deleção
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$bairro->delete($_GET['idbairro']);

					  	//obtém erros
							$err = $bairro->err;

							//se não ocorreram erros
							if(count($err) == 0){
								$flags['sucesso'] = $conf['excluir'];
								
								
								
							}

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista registros
							$list = $bairro->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $bairro->err;

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
  
  $smarty->display("adm_bairro.tpl");
?>

