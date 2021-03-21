<?php

  //inclus�o de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  require_once("../entidades/secao.php");
	require_once("../entidades/departamento.php"); 
	
	

  // configura��es anotionais
  $conf['area'] = "Se��o"; // �rea


  //configura��o de estilo
  $conf['style'] = "full";

  // inicializa templating
  $smarty = new Smarty;

  // configura diret�rios
  $smarty->template_dir = "../common/tpl";
  $smarty->compile_dir =   "../common/tpl_c";

  // seta configura��es
  $smarty->assign("conf", $conf);

  // a��o selecionada
  $flags['action'] = $_GET['ac'];
  if ($flags['action'] == "") $flags['action'] = "listar";

  // inicializa autentica��o
  $auth = new auth();

  // verifica requisi��o de logout
  if($flags['action'] == "logout") {
    $auth->logout();
  }
  else {

    // inicializa vetor de erros
    $err = array();

    // verifica sess�o
    if(!$auth->check_user()) {
      // verifica requisi��o de login
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

    // conte�do
    if($auth->check_user()) {
      // verifica privil�gios
      if(!$auth->check_priv($conf['priv'])) {
        $err = $auth->err;
      }
      else {
        // libera conte�do
        $flags['okay'] = 1;

				//inicializa classe
	  		$secao = new secao();
	  		
	  		$departamento = new departamento(); 
				
	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para valida��o de formul�rio
        $form = new form();

        $list = $auth->check_priv($conf['priv']);
				$aux = $flags['action'];
				if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}

        
        switch($flags['action']) {

					// busca gen�rica
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


						  //obt�m qual p�gina da listagem deseja exibir
						  $pg = intval(trim($_GET['pg']));

						  //se n�o foi passada a p�gina como par�metro, faz p�gina default igual � p�gina 0
						  if(!$pg) $pg = 0;

						  //lista os registros
							$list = $secao->Busca_Generica($pg, $flags['rpp'], $flags['busca'], "", "ac=busca_generica&busca=".$flags['busca']."&rpp=".$flags['rpp']);

							//pega os erros
							$err = $secao->err;

							//passa a listagem para o template
							$smarty->assign("list", $list);

						}

						if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];

          break;


					// busca parametrizada
          case "busca_parametrizada":

            if ( ($_POST['for_chk']) || ($_GET['rpp'] != "") ) {

            	$flags['fez_busca'] = 1;

							if ($_POST['for_chk']) {
								$flags['nome_secao'] = $_POST['nome_secao'];
								$flags['nome_departamento'] = $_POST['nome_departamento'];
								$flags['rpp'] = $_POST['rpp'];
							}
							else {
								$flags['nome_secao'] = $_GET['nome_secao'];
								$flags['nome_departamento'] = $_GET['nome_departamento'];
 								$flags['rpp'] = $_GET['rpp'];
							}

							$parametros_get = "&nome_secao=" . $flags['nome_secao'] . "&nome_departamento=" . $flags['nome_departamento'];


							$filtro_where = "";
							if ($flags['nome_secao'] != "") $filtro_where .= " UPPER(S.nome_secao) LIKE UPPER('%" . $flags['nome_secao'] . "%') AND ";
							if ($flags['nome_departamento'] != "") $filtro_where .= " ( (UPPER(D.nome_departamento) LIKE UPPER('%" . $flags['nome_departamento'] . "%'))) AND ";
							
							$filtro_where = substr($filtro_where,0,strlen($filtro_where)-4);


							if ($_GET['target'] == "full") $flags['rpp'] = 9999999;


						  //obt�m qual p�gina da listagem deseja exibir
						  $pg = intval(trim($_GET['pg']));

						  //se n�o foi passada a p�gina como par�metro, faz p�gina default igual � p�gina 0
						  if(!$pg) $pg = 0;

						  //lista os registros
							$list = $secao->Busca_Parametrizada($pg, $flags['rpp'], $filtro_where, "", "ac=busca_parametrizada$parametros_get&rpp=".$flags['rpp']);

							//pega os erros
							$err = $secao->err;

							//passa a listagem para o template
							$smarty->assign("list", $list);

						}

						if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];

          break;


          // a��o: adicionar <<<<<<<<<<
          case "adicionar":

            if($_POST['for_chk']) {
            	
            	
            	
							$form->chk_empty($_POST['iddepartamento'], 1, 'Departamento'); 
							$form->chk_empty($_POST['nome_secao'], 1, 'Nome da se��o'); 
							

              $err = $form->err;

              if(count($err) == 0) {

								
	              
	              
	              
								
								

	              
								//grava o registro no banco de dados
								$secao->set($_POST);

								//obt�m os erros que ocorreram no cadastro
								$err = $secao->err;

								//se n�o ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['inserir'];

									//limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $secao->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $secao->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}
								
              }
              
            }
            
            $list_departamento = $departamento->make_list_select();
						$smarty->assign("list_departamento",$list_departamento);

						

          break;


					//listagem dos registros
          case "listar":
          
					  //obt�m qual p�gina da listagem deseja exibir
					  $pg = intval(trim($_GET['pg']));

					  //se n�o foi passada a p�gina como par�metro, faz p�gina default igual � p�gina 0
					  if(!$pg) $pg = 0;

					  //lista os registros
						$list = $secao->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $secao->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          break;
          
          
          // a��o: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idsecao'] = $_GET['idsecao']; 
							
							
							$form->chk_empty($_POST['numiddepartamento'], 1, 'Departamento'); 
							$form->chk_empty($_POST['litnome_secao'], 1, 'Nome da se��o'); 
							
							
							$err = $form->err;

		          if(count($err) == 0) {


								$secao->update($_GET['idsecao'], $_POST);
								
								
								

								//obt�m erros
								$err = $secao->err;

								//se n�o ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['alterar'];

								  //limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $secao->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $secao->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}

							}

						}
						else {

							//busca detalhes
							$info = $secao->getById($_GET['idsecao']);

							//tratamento das informa��es para fazer o UPDATE
							$info['numiddepartamento'] = $info['iddepartamento']; 
							$info['litnome_secao'] = $info['nome_secao']; 
							$info['iddepartamento_NomeTemp'] = $info['nome_departamento'];
							$info['iddepartamento_Nome'] = $info['nome_departamento'];
							
							
							
							
							//obt�m os erros
							$err = $secao->err;
						}

            $list_departamento = $departamento->make_list_select();
						$smarty->assign("list_departamento",$list_departamento);

						

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a dele��o
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$secao->delete($_GET['idsecao']);

					  	//obt�m erros
							$err = $secao->err;

							//se n�o ocorreram erros
							if(count($err) == 0){
								$flags['sucesso'] = $conf['excluir'];
							}

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista registros
							$list = $secao->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $secao->err;

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
	
		// Forma Array de intru��es de preenchimento
	$intrucoes_preenchimento = array();
	if ($flags['action'] == "adicionar" || $flags['action'] == "editar" ) {
		$intrucoes_preenchimento[] = "Os campos em <span class=req>vermelho</span> s&atilde;o obrigat&oacute;rios.";
	}
	else if ($flags['action'] == "busca_generica" || $flags['action'] == "busca_parametrizada") {
		$intrucoes_preenchimento[] = "Preencha os campos para realizar a busca.";
	}

	// Formata a mensagem para ser exibida
	$flags['intrucoes_preenchimento'] = $form->FormataMensagemAjuda($intrucoes_preenchimento);


  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);


  $list_permissao = $auth->check_priv($conf['priv']);
	$smarty->assign("list_permissao",$list_permissao);
  
  if ($_GET['target'] == "full")  $smarty->display("adm_relatorio_secao.tpl");
  else $smarty->display("adm_secao.tpl");
  
?>

