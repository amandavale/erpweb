<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/parametro.php");
	
	

  // configurações anotionais
  $conf['area'] = "Parâmetros"; // área


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
	  		$parametro = new parametro();

	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para validação de formulário
        $form = new form();
        
				
				// verifica se os parametros ja foram cadastrados
				$list_parametro = $parametro->make_list(0, $conf['rppg']);
				
				if ( count ($list_parametro) == 0 ) {
					$flags['destino'] = 'adicionar';
				}
				else {
					$flags['destino'] = 'editar&idparametro=' . $list_parametro[0]['idparametro'];
					if ($flags['action'] == 'adicionar') $flags['action'] = 'editar';
					$_GET['idparametro'] = $list_parametro[0]['idparametro'];
				}
				//------------------------------------------------


				$list = $auth->check_priv($conf['priv']);
				$aux = $flags['action'];
				if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}

				
        switch($flags['action']) {

          // ação: adicionar <<<<<<<<<<
          case "adicionar":


            if($_POST['for_chk']) {


              $err = $form->err;

              if(count($err) == 0) {

								if ($_POST['validadeOrcamento'] == "") $_POST['validadeOrcamento'] = "NULL";
								if ($_POST['maximoItensOrcamento'] == "") $_POST['maximoItensOrcamento'] = "NULL";
								if ($_POST['descontoMaximoOrcamento'] == "") $_POST['descontoMaximoOrcamento'] = "NULL";
								if ($_POST['limiteCreditoPadrao'] == "") $_POST['limiteCreditoPadrao'] = "NULL";
								if ($_POST['jurosPadraoParcelamento'] == "") $_POST['jurosPadraoParcelamento'] = "NULL";
								if ($_POST['jurosPadraoAtraso'] == "") $_POST['jurosPadraoAtraso'] = "NULL";
								if ($_POST['porcentagem_maxima'] == "") $_POST['porcentagem_maxima'] = "NULL";
								if ($_POST['limite_cancelamento'] == "") $_POST['limite_cancelamento'] = "NULL";								
	              
	              
								$_POST['descontoMaximoOrcamento'] = str_replace(",",".",$_POST['descontoMaximoOrcamento']);
             		$_POST['limiteCreditoPadrao'] = str_replace(",",".",$_POST['limiteCreditoPadrao']);
             		$_POST['jurosPadraoParcelamento'] = str_replace(",",".",$_POST['jurosPadraoParcelamento']);
		          	$_POST['jurosPadraoAtraso'] = str_replace(",",".",$_POST['jurosPadraoAtraso']);
		          	$_POST['jurosPadraoDesconto'] = str_replace(",",".",$_POST['jurosPadraoDesconto']);
		          	$_POST['porcentagem_maxima'] = str_replace(",",".",$_POST['porcentagem_maxima']);

	              
								//grava o registro no banco de dados
								$parametro->set($_POST);


								//obtém os erros que ocorreram no cadastro
								$err = $parametro->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['inserir'];

									//limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $parametro->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $parametro->err;

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
						$list = $parametro->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $parametro->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          break;
          
          
          // ação: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idparametro'] = $_GET['idparametro'];
							
							
							
							$err = $form->err;

		          if(count($err) == 0) {

								$_POST['numdescontoMaximoOrcamento'] = str_replace(",",".",$_POST['numdescontoMaximoOrcamento']);
             		$_POST['numlimiteCreditoPadrao'] = str_replace(",",".",$_POST['numlimiteCreditoPadrao']);
             		$_POST['numjurosPadraoParcelamento'] = str_replace(",",".",$_POST['numjurosPadraoParcelamento']);
		          	$_POST['numjurosPadraoAtraso'] = str_replace(",",".",$_POST['numjurosPadraoAtraso']);
		          	$_POST['numjurosPadraoDesconto'] = str_replace(",",".",$_POST['numjurosPadraoDesconto']);
		          	$_POST['numporcentagem_maxima'] = str_replace(",",".",$_POST['numporcentagem_maxima']);
		          	

								if ($_POST['numvalidadeOrcamento'] == "") $_POST['numvalidadeOrcamento'] = "NULL";
								if ($_POST['nummaximoItensOrcamento'] == "") $_POST['nummaximoItensOrcamento'] = "NULL";
								if ($_POST['numdescontoMaximoOrcamento'] == "") $_POST['numdescontoMaximoOrcamento'] = "NULL";
								if ($_POST['numlimiteCreditoPadrao'] == "") $_POST['numlimiteCreditoPadrao'] = "NULL";
								if ($_POST['numjurosPadraoParcelamento'] == "") $_POST['numjurosPadraoParcelamento'] = "NULL";
								if ($_POST['numjurosPadraoAtraso'] == "") $_POST['numjurosPadraoAtraso'] = "NULL";
								if ($_POST['numjurosPadraoDesconto'] == "") $_POST['numjurosPadraoDesconto'] = "NULL";
								if ($_POST['numporcentagem_maxima'] == "") $_POST['numporcentagem_maxima'] = "NULL";
								if ($_POST['numlimite_cancelamento'] == "") $_POST['numlimite_cancelamento'] = "NULL";		


								$parametro->update($_GET['idparametro'], $_POST);
								
								

								//obtém erros
								$err = $parametro->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['alterar'];

								  //limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $parametro->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $parametro->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}

							}

						}
						else {

							//busca detalhes
							$info = $parametro->getById($_GET['idparametro']);

							//tratamento das informações para fazer o UPDATE
							$info['numvalidadeOrcamento'] = $info['validadeOrcamento'];
							$info['nummaximoItensOrcamento'] = $info['maximoItensOrcamento'];
							$info['numdescontoMaximoOrcamento'] = $info['descontoMaximoOrcamento'];
							$info['numlimiteCreditoPadrao'] = $info['limiteCreditoPadrao'];
							$info['numjurosPadraoParcelamento'] = $info['jurosPadraoParcelamento'];
							$info['numjurosPadraoAtraso'] = $info['jurosPadraoAtraso'];
							$info['numjurosPadraoDesconto'] = $info['jurosPadraoDesconto'];
							$info['numporcentagem_maxima'] = $info['porcentagem_maxima'];
							$info['numlimite_cancelamento'] = $info['limite_cancelamento'];
							$info['litmodeloPadraoNota'] = $info['modeloPadraoNota'];
							$info['litseriePadraoNota'] = $info['seriePadraoNota'];


							//obtém os erros
							$err = $parametro->err;
							
						}

            
            
            
            

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a deleção
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$parametro->delete($_GET['idparametro']);

					  	//obtém erros
							$err = $parametro->err;

							//se não ocorreram erros
							if(count($err) == 0){
								$flags['sucesso'] = $conf['excluir'];
								
								
								
							}

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista registros
							$list = $parametro->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $parametro->err;

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
  
  $smarty->display("adm_parametro.tpl");
?>

