<?php

  //inclus�o de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/cidade.php");
	require_once("../entidades/estado.php"); 
	
	

  // configura��es anotionais
  $conf['area'] = "Cidade"; // �rea


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
	  		$cidade = new cidade();
	  		
	  		$estado = new estado(); 
				
	  											
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
							$list = $cidade->Busca_Generica($pg, $flags['rpp'], $flags['busca'], "", "ac=busca_generica&busca=".$flags['busca']."&rpp=".$flags['rpp']);

							//pega os erros
							$err = $produto->err;

							//passa a listagem para o template
							$smarty->assign("list", $list);

						}

						if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];

          break;





          // a��o: adicionar <<<<<<<<<<
          case "adicionar":

            if($_POST['for_chk']) {
            	
            	
            	
							$form->chk_empty($_POST['idestado'], 1, 'Estado'); 
							$form->chk_empty($_POST['nome_cidade'], 1, 'Nome da cidade'); 
							

              $err = $form->err;

              if(count($err) == 0) {

								
	              
	              
	              
								
								

	              
								//grava o registro no banco de dados
								$cidade->set($_POST);


								//obt�m os erros que ocorreram no cadastro
								$err = $cidade->err;

								//se n�o ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['inserir'];

									//limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $cidade->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $cidade->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}
								
              }
              
            }
            
            $list_estado = $estado->make_list_select();
						$smarty->assign("list_estado",$list_estado);

						

          break;


					//listagem dos registros
          case "listar":
          
					  //obt�m qual p�gina da listagem deseja exibir
					  $pg = intval(trim($_GET['pg']));

					  //se n�o foi passada a p�gina como par�metro, faz p�gina default igual � p�gina 0
					  if(!$pg) $pg = 0;

					  //lista os registros
						$list = $cidade->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $cidade->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          break;
          
          
          // a��o: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idcidade'] = $_GET['idcidade']; 
							
							
							$form->chk_empty($_POST['numidestado'], 1, 'Estado'); 
							$form->chk_empty($_POST['litnome_cidade'], 1, 'Nome da cidade'); 
							
							
							$err = $form->err;

		          if(count($err) == 0) {

								
								
								
								
		          	
								


								$cidade->update($_GET['idcidade'], $_POST);
								
								

								//obt�m erros
								$err = $cidade->err;

								//se n�o ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['alterar'];

								  //limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $cidade->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $cidade->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}

							}

						}
						else {

							//busca detalhes
							$info = $cidade->getById($_GET['idcidade']);

							//tratamento das informa��es para fazer o UPDATE
							$info['numidestado'] = $info['idestado']; 
							$info['litnome_cidade'] = $info['nome_cidade']; 
							
							$info['idestado_Nome'] = $info['nome_estado'];
							$info['idestado_NomeTemp'] = $info['nome_estado'];

							//obt�m os erros
							$err = $cidade->err;
						}

            $list_estado = $estado->make_list_select();
						$smarty->assign("list_estado",$list_estado);

						
            
            
            

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a dele��o
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$cidade->delete($_GET['idcidade']);

					  	//obt�m erros
							$err = $cidade->err;

							//se n�o ocorreram erros
							if(count($err) == 0){
								$flags['sucesso'] = $conf['excluir'];
								
								
								
							}

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista registros
							$list = $cidade->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $cidade->err;

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
  
  $smarty->display("adm_cidade.tpl");
?>

