<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/material_ordem_servico.php");
	require_once("../entidades/ordem_servico.php"); 
	require_once("../entidades/fornecedor.php"); 
	require_once("../entidades/unidade_venda.php"); 
	
	

  // configurações anotionais
  $conf['area'] = "Materiais da Ordem de Serviço"; // área
  $conf['priv'] = array($conf['pri_adm']); // privilégios requeridos

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
	  		$material_ordem_servico = new material_ordem_servico();
	  		
	  		$ordem_servico = new ordem_servico(); 
				$fornecedor = new fornecedor(); 
				$unidade_venda = new unidade_venda(); 
				
	  											
        // inicializa banco de dados
        $db = new db();

        //incializa classe para validação de formulário
        $form = new form();
        
				

        switch($flags['action']) {

          // ação: adicionar <<<<<<<<<<
          case "adicionar":

            if($_POST['for_chk']) {
            	
            	
            	
							$form->chk_empty($_POST['idordem_servico'], 1, 'Ordem de Serviço'); 
							$form->chk_empty($_POST['material'], 1, 'Material'); 
							$form->chk_empty($_POST['qtd_material'], 1, 'Quantidade'); 
							$form->chk_empty($_POST['valor_unitario'], 1, 'Valor Unitário'); 
							

              $err = $form->err;

              if(count($err) == 0) {

								
	              $_POST['qtd_material'] = str_replace(",",".",$_POST['qtd_material']); 
								$_POST['valor_unitario'] = str_replace(",",".",$_POST['valor_unitario']); 
								
	              
	              
								
								if ($_POST['qtd_material'] == "") $_POST['qtd_material'] = "NULL"; 
								if ($_POST['valor_unitario'] == "") $_POST['valor_unitario'] = "NULL"; 
								

	              
								//grava o registro no banco de dados
								$material_ordem_servico->set($_POST);


								//obtém os erros que ocorreram no cadastro
								$err = $material_ordem_servico->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['inserir'];

									//limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $material_ordem_servico->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $material_ordem_servico->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}
								
              }
              
            }
            
            $list_ordem_servico = $ordem_servico->make_list_select();
						$smarty->assign("list_ordem_servico",$list_ordem_servico);

						$list_fornecedor = $fornecedor->make_list_select();
						$smarty->assign("list_fornecedor",$list_fornecedor);

						$list_unidade_venda = $unidade_venda->make_list_select();
						$smarty->assign("list_unidade_venda",$list_unidade_venda);

						

          break;


					//listagem dos registros
          case "listar":
          
					  //obtém qual página da listagem deseja exibir
					  $pg = intval(trim($_GET['pg']));

					  //se não foi passada a página como parâmetro, faz página default igual à página 0
					  if(!$pg) $pg = 0;

					  //lista os registros
						$list = $material_ordem_servico->make_list($pg, $conf['rppg']);

						//pega os erros
						$err = $material_ordem_servico->err;

						//passa a listagem para o template
						$smarty->assign("list", $list);

          break;
          
          
          // ação: editar <<<<<<<<<<
					case "editar":

						if($_POST['for_chk']) {
							
							

							$info = $_POST;
							
							$info['idmaterial'] = $_GET['idmaterial']; 
							
							
							$form->chk_empty($_POST['numidordem_servico'], 1, 'Ordem de Serviço'); 
							$form->chk_empty($_POST['litmaterial'], 1, 'Material'); 
							$form->chk_empty($_POST['numqtd_material'], 1, 'Quantidade'); 
							$form->chk_empty($_POST['numvalor_unitario'], 1, 'Valor Unitário'); 
							
							
							$err = $form->err;

		          if(count($err) == 0) {

								
								$_POST['numqtd_material'] = str_replace(",",".",$_POST['numqtd_material']); 
								$_POST['numvalor_unitario'] = str_replace(",",".",$_POST['numvalor_unitario']); 
								
								
								
		          	
								if ($_POST['numqtd_material'] == "") $_POST['numqtd_material'] = "NULL"; 
								if ($_POST['numvalor_unitario'] == "") $_POST['numvalor_unitario'] = "NULL"; 
								


								$material_ordem_servico->update($_GET['idmaterial'], $_POST);
								
								

								//obtém erros
								$err = $material_ordem_servico->err;

								//se não ocorreram erros
								if(count($err) == 0) {
									$flags['sucesso'] = $conf['alterar'];

								  //limpa o $flags.action para que seja exibida a listagem
								  $flags['action'] = "listar";

								  //lista
									$list = $material_ordem_servico->make_list(0, $conf['rppg']);

									//pega os erros
									$err = $material_ordem_servico->err;

									//envia a listagem para o template
									$smarty->assign("list", $list);

								}

							}

						}
						else {

							//busca detalhes
							$info = $material_ordem_servico->getById($_GET['idmaterial']);

							//tratamento das informações para fazer o UPDATE
							$info['numidordem_servico'] = $info['idordem_servico']; 
							$info['numidfornecedor'] = $info['idfornecedor']; 
							$info['litmaterial'] = $info['material']; 
							$info['numidunidade_venda'] = $info['idunidade_venda']; 
							$info['numqtd_material'] = $info['qtd_material']; 
							$info['numvalor_unitario'] = $info['valor_unitario']; 
							
							
							
							
							//obtém os erros
							$err = $material_ordem_servico->err;
						}

            $list_ordem_servico = $ordem_servico->make_list_select();
						$smarty->assign("list_ordem_servico",$list_ordem_servico);

						$list_fornecedor = $fornecedor->make_list_select();
						$smarty->assign("list_fornecedor",$list_fornecedor);

						$list_unidade_venda = $unidade_venda->make_list_select();
						$smarty->assign("list_unidade_venda",$list_unidade_venda);

						
            
            
            

						//passa os dados para o template
						$smarty->assign("info", $info);

					break;

          
          
          // deleta um registro do sistema
	        case "excluir":

						//verifica se foi pedido a deleção
					  if($_POST['for_chk']){
					  	
							// deleta o registro
					  	$material_ordem_servico->delete($_GET['idmaterial']);

					  	//obtém erros
							$err = $material_ordem_servico->err;

							//se não ocorreram erros
							if(count($err) == 0){
								$flags['sucesso'] = $conf['excluir'];
								
								
								
							}

						  //limpa o $flags.action para que seja exibida a listagem
						  $flags['action'] = "listar";

						  //lista registros
							$list = $material_ordem_servico->make_list(0, $conf['rppg']);

							//pega os erros
							$err = $material_ordem_servico->err;

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

  $smarty->display("adm_material_ordem_servico.tpl");
?>

