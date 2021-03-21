<?php

  //inclus�o de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/xajax/xajax.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/ocupacao.php");
	require_once("../entidades/apartamento.php"); 
	require_once("../entidades/cliente_fisico.php"); 
	require_once("../entidades/cliente_condominio.php");
	require_once("../entidades/cliente.php");
	require_once("../entidades/endereco.php");
	
	require_once("ocupacao_ajax.php");

  // configura��es anotionais
  $conf['area'] = "Ocupa��o"; // �rea

  $conf['priv'] = 'apartamento'; // privil�gios requeridos
  
  
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
  
  // cria o objeto xajax
  $xajax = new xajax();

  // registra todas as fun��es que ser�o usadas
  $xajax->registerFunction("Verifica_Campos_Ocupacao_AJAX");

  // processa as fun��es
  $xajax->processRequests();

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
      elseif ( (!$_GET['idapartamento']) && (!$_SESSION['idapartamento']) ){
      	
		$err[] = 'Por Favor, selecione um apartamento antes de prosseguir.';
	  }
      else {
      	
      	//Grava o ID do apartamento na sess�o
      	if($_GET['idapartamento']){
		  $_SESSION['idapartamento'] = $_GET['idapartamento'];
		  
		}
      	
        // libera conte�do
        $flags['okay'] = 1;

		//inicializa classe
  		$ocupacao = new ocupacao();
  		
  		$apartamento = new apartamento();
		$endereco = new endereco();
		$cliente_fisico = new cliente_fisico(); 
		$cliente_condominio = new cliente_condominio();
		$cliente = new cliente();
				
	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para valida��o de formul�rio
        $form = new form();
        
        //Grava o ID do apartamento na sess�o
			
        
        
        switch($flags['action']) {

          // a��o: adicionar <<<<<<<<<<
          case "adicionar":


            if($_POST['for_chk']) {
            	
            	if (! $_SESSION['idapartamento']) $err[] = 'Voc� deve selecionar um apartamento';
				$form->chk_empty($_POST['idcliente'], 1, 'Cliente');
				$form->chk_empty($_POST['tipo'], 1, 'Tipo');
				$form->chk_IsDate($_POST['dataInicial'], "Data Inicial");
				

				$err = $form->err;

				if(count($err) == 0) {

					$_POST['observacao'] = nl2br($_POST['observacao']);

					$_POST['dataInicial'] = $form->FormataDataParaInserir($_POST['dataInicial']);

					if ($_POST['dataFinal'] !=''){
						$_POST['dataFinal'] = $form->FormataDataParaInserir($_POST['dataFinal']);
					}
					
					
					$_POST['idapartamento'] = $_SESSION['idapartamento'];
					
					
					//Atribui o endere�o do condom�nio ao cliente, caso o usu�rio tenha marcado a op��o
					if($_POST['atribuir_endereco']){

						    //Recupera os dados do endere�o do condom�nio
							$dados_condominio = $cliente->getById($_SESSION['idcliente']);
						    $endereco_cliente = $endereco->getById($dados_condominio['idendereco_cliente']);

							//Tratamento dos campos
							$endereco_cliente['complemento'] = 'Apto. '.$_POST['apartamento_numero'];
							if($endereco_cliente['idestado'] == '') $endereco_cliente['idestado'] = 'NULL';
							if($endereco_cliente['idcidade'] == '') $endereco_cliente['idcidade'] = 'NULL';
							if($endereco_cliente['idbairro'] == '') $endereco_cliente['idbairro'] = 'NULL';

							//Recupero o idendere�o do cliente em quest�o
						    $dados_cliente = $cliente->getById($_POST['idcliente']);

						    if($dados_cliente['idendereco_cliente'] != ''){

						        //trata o nome dos �ndices do array para utilizar a fun��o update
							   	
								$info_endereco['numidestado'] = $endereco_cliente['idestado'];
							    $info_endereco['numidcidade'] = $endereco_cliente['idcidade'];
							    $info_endereco['numidbairro'] = $endereco_cliente['idbairro'];
							    $info_endereco['litcomplemento'] = $endereco_cliente['complemento'];
							    $info_endereco['litlogradouro'] = $endereco_cliente['logradouro'];
							    $info_endereco['litnumero'] = $endereco_cliente['numero'];
							    $info_endereco['litcomplemento'] = $endereco_cliente['complemento'];
							    $info_endereco['litcep'] = $endereco_cliente['cep'];

							    //Faz o update
							    $endereco->update($dados_cliente['idendereco_cliente'], $info_endereco);
								$err = $endereco->err;


							}
							else{
								//Se o cliente n�o possuir endere�o cadastrado, seta um novo endere�o
								$info_cliente['idendereco_cliente'] = $endereco->set($endereco_cliente);
							}

							//Declara que o endere�o de cobran�a � o mesmo do endere�o do apto
							$info_cliente['litmesmo_endereco'] = 1;
							$cliente->update($_POST['idcliente'], $info_cliente);

					}
					
					
					//grava o registro no banco de dados
					$ocupacao->set($_POST);


					//obt�m os erros que ocorreram no cadastro
					$err = $ocupacao->err;
					
					
					
					//se n�o ocorreram erros
					if(count($err) == 0) {

						//Define o apartamento como vazio na tabela caso n�o tenha nenhum ativo
						$ocupacao->altera_Situacao_Apto($_SESSION['idapartamento'],$_POST['idcliente']);
						
						$flags['sucesso'] = $conf['inserir'];

						//limpa o $flags.action para que seja exibida a listagem
						$flags['action'] = "listar";

						//lista os registros
						$filtro = 'WHERE OCUP.idapartamento=' . $_SESSION['idapartamento'];
						$list = $ocupacao->make_list($pg, $conf['rppg'], $filtro);

						//pega os erros
						$err = $ocupacao->err;

						//envia a listagem para o template
						$smarty->assign("list", $list);

					}
								
              }
              
		}

		$apto = $apartamento->getById($_SESSION['idapartamento'], $_SESSION['idcliente']);
		$smarty->assign("apartamento",$apto['apto']);

		$list_cliente_fisico = $cliente_fisico->make_list_select();
		$smarty->assign("list_cliente_fisico",$list_cliente_fisico);

		break;


					//listagem dos registros
          case "listar":
          
			//obt�m qual p�gina da listagem deseja exibir
			$pg = intval(trim($_GET['pg']));

			//se n�o foi passada a p�gina como par�metro, faz p�gina default igual � p�gina 0
			if(!$pg) $pg = 0;
			
			//Verifica se foi escolhido algum filtro
			if($_POST['filtro'])
				$_SESSION['filtro'] = $_POST['filtro'];
		
			
			
			//lista os registros
			$where = 'WHERE OCUP.idapartamento=' . $_SESSION['idapartamento'];
			
			if( (isset($_SESSION['filtro'])) && ($_SESSION['filtro'] != 'T') )
				$where.=' AND OCUP.tipo = \''.$_SESSION['filtro'].'\'';
			
			
			$list = $ocupacao->make_list($pg, $conf['rppg'], $where,'',"&fitro=");

			//pega os erros
			$err = $ocupacao->err;
			
			$apto = $apartamento->getById($_SESSION['idapartamento'], $_SESSION['idcliente']);
			$smarty->assign("apartamento",$apto['apto']);

			//passa a listagem para o template
			$smarty->assign("list", $list);

          break;
          
          
          // a��o: editar <<<<<<<<<<
		  case "editar":

				if($_POST['for_chk']) {

					$info = $_POST;

					$info['idapartamento'] = $_GET['idapartamento'];
					$info['idcliente'] = $_GET['idcliente'];

					$form->chk_empty($_POST['idcliente'], 1, 'Cliente');
					$form->chk_empty($_POST['littipo'], 1, 'Tipo');
					$form->chk_IsDate($_POST['litdataInicial'], "Data Inicial");


					$err = $form->err;

					if(count($err) == 0) {

						$_POST['numidcliente'] = $_POST['idcliente'];
						$_POST['litobservacao'] = nl2br($_POST['litobservacao']);
						$_POST['litdataInicial'] = $form->FormataDataParaInserir($_POST['litdataInicial']);
						$_POST['litdataFinal'] = $form->FormataDataParaInserir($_POST['litdataFinal']);
						
						
						
						//Atribui o endere�o do condom�nio ao cliente, caso o usu�rio tenha marcado a op��o
						if($_POST['atribuir_endereco']){

							    //Recupera os dados do endere�o do condom�nio
								$dados_condominio = $cliente->getById($_SESSION['idcliente']);
							    $endereco_cliente = $endereco->getById($dados_condominio['idendereco_cliente']);

								//tratamento dos campos
								$endereco_cliente['complemento'] = 'Apto. '.$_POST['apartamento_numero'];
								if($endereco_cliente['idestado'] == '') $endereco_cliente['idestado'] = 'NULL';
								if($endereco_cliente['idcidade'] == '') $endereco_cliente['idcidade'] = 'NULL';
								if($endereco_cliente['idbairro'] == '') $endereco_cliente['idbairro'] = 'NULL';

								//Recupero o idendere�o do cliente em quest�o
							    $dados_cliente = $cliente->getById($_POST['numidcliente']);

							    if($dados_cliente['idendereco_cliente'] != ''){

							        //trata o nome dos �ndices do array para utilizar a fun��o update
								   	$info_endereco['numidestado'] = $endereco_cliente['idestado'];
								    $info_endereco['numidcidade'] = $endereco_cliente['idcidade'];
								    $info_endereco['numidbairro'] = $endereco_cliente['idbairro'];
								    $info_endereco['litcomplemento'] = $endereco_cliente['complemento'];
								    $info_endereco['litlogradouro'] = $endereco_cliente['logradouro'];
								    $info_endereco['litnumero'] = $endereco_cliente['numero'];
								    $info_endereco['litcomplemento'] = $endereco_cliente['complemento'];
								    $info_endereco['litcep'] = $endereco_cliente['cep'];

								    //Faz o update
								    $endereco->update($dados_cliente['idendereco_cliente'], $info_endereco);
									$err = $endereco->err;


								}
								else{
									//Se o cliente n�o possuir endere�o cadastrado, seta um novo endere�o
									$info_cliente['idendereco_cliente'] = $endereco->set($endereco_cliente);
								}

								//Declara que o endere�o de cobran�a � o mesmo do endere�o do apto
								$info_cliente['litmesmo_endereco'] = 1;
								$cliente->update($_POST['idcliente'], $info_cliente);

						}
						
						
						
						

						$ocupacao->update($_GET['idapartamento'],$_GET['idcliente'], $_POST);

						//obt�m erros
						$err = $ocupacao->err;
						
						//Define o apartamento como vazio na tabela caso n�o tenha nenhum ativo
						$ocupacao->altera_Situacao_Apto($_GET['idapartamento'],$_GET['idcliente']);

						//se n�o ocorreram erros
						if(count($err) == 0) {
							$flags['sucesso'] = $conf['alterar'];

							//limpa o $flags.action para que seja exibida a listagem
							$flags['action'] = "listar";

							//lista os registros
							$filtro = 'WHERE OCUP.idapartamento=' . $_SESSION['idapartamento'];
							$list = $ocupacao->make_list($pg, $conf['rppg'], $filtro);

							//pega os erros
							$err = $ocupacao->err;

							//envia a listagem para o template
							$smarty->assign("list", $list);

						}

					}

				}
				else {

					//busca detalhes
					$info = $ocupacao->getById($_SESSION['idapartamento'],$_GET['idcliente']);

					//tratamento das informa��es para fazer o UPDATE
					$info['numidapartamento'] = $info['idapartamento']; 
					$info['numidcliente'] = $info['idcliente']; 
					$info['littipo'] = $info['tipo']; 
					$info['litdataInicial'] = $info['dataInicial']; 
					$info['litdataFinal'] = $info['dataFinal']; 
					$info['litativa'] = $info['ativa']; 
					$info['litsindico'] = $info['sindico']; 
					$info['numobservacao'] = strip_tags($info['observacao']); 
					
					
					//obt�m os erros
					$err = $ocupacao->err;
				}

    			$apto = $apartamento->getById($_SESSION['idapartamento'], $_SESSION['idcliente']);
				$smarty->assign("apartamento",$apto['apto']);

				$dados_cliente = $cliente->getById($info['idcliente']);
				$smarty->assign("cliente",$dados_cliente);

				
				//passa os dados para o template
				$smarty->assign("info", $info);

			break;

          
          
          // deleta um registro do sistema
	        case "excluir":

					//verifica se foi pedido a dele��o
					if($_POST['for_chk']){
					  	
						// deleta o registro
					  	$ocupacao->delete($_SESSION['idapartamento'],$_GET['idcliente']);
					  	
					  	$ocupacao->altera_Situacao_Apto($_SESSION['idapartamento'],$_GET['idcliente']);

					  	//obt�m erros
						$err = $ocupacao->err;

						//se n�o ocorreram erros
						if(count($err) == 0){
							$flags['sucesso'] = $conf['excluir'];
						}

						//limpa o $flags.action para que seja exibida a listagem
						$flags['action'] = "listar";

						//lista os registros
						$filtro = 'WHERE OCUP.idapartamento=' . $_SESSION['idapartamento'];
						$list = $ocupacao->make_list(0, $conf['rppg'], $filtro);

						//pega os erros
						$err = $ocupacao->err;

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
	
	// Formata a mensagem para ser exibida
	if ($flags['action'] != 'listar'){
		$intrucoes_preenchimento[] = "Os campos em <span class=req>vermelho</span> s&atilde;o obrigat&oacute;rios.";
		$flags['intrucoes_preenchimento'] = $form->FormataMensagemAjuda($intrucoes_preenchimento);
	}
	
	$smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));
	$smarty->assign("form", $form);
	$smarty->assign("flags", $flags);
	
 	$list_permissao = $auth->check_priv($conf['priv']);
	$smarty->assign("list_permissao",$list_permissao);

	$smarty->display("adm_ocupacao.tpl");
?>

