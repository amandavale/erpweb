<?php

  //inclusão de bibliotecas
	require_once("../common/lib/conf.inc.php");
	require_once("../common/lib/db.inc.php");
	require_once("../common/lib/auth.inc.php");
	require_once("../common/lib/form.inc.php");
	require_once("../common/lib/Smarty/Smarty.class.php");

	require_once("../entidades/funcionario.php");
	require_once("../entidades/produto.php");
	require_once("../entidades/transferencia_filial.php");
	require_once("../entidades/programa.php");
	require_once("../entidades/funcionario_programa.php");
	require_once("../entidades/cargo_programa.php");



  // inicializa templating
  $smarty = new Smarty;

  // ação selecionada
  $flags['action'] = $_GET['ac'];

  // inicializa autenticação
  $auth = new auth();


	//inicializa classe
	$funcionario = new funcionario();
	$transferencia_filial = new transferencia_filial();
	$programa = new programa();
	$cargo_programa = new cargo_programa();
	$funcionario_programa = new funcionario_programa();


  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();
        
				

  switch($flags['action']) {



	}




	/*
	Função: Verifica_Campos_Transferencia_AJAX
	Verifica se os campos da tranferencia de estoque foram preenchidos
	*/

	function Verifica_Campos_Programa_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;



		//---------------------


		// cria o objeto xajaxResponse
   		 $objResponse = new xajaxResponse();

   		 
		if ($_GET['ac'] == "editar") {
			

		}
		else {	

			$form->chk_empty($post['idfuncionario_programa'],1,"o funcionário");
			
			
			$err = $form->err;
			
		}

		// se nao houveram erros, da o submit no form
    if(count($err) == 0) {


    	$objResponse->addScript("document.getElementById('for').submit();");
    	

    }
    // houve erros, logo mostra-os
    else {
			$mensagem = implode("\n",$err);
			$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
		}

		// retorna o resultado XML
    return $objResponse->getXML();
  }


	/*
	Função: Insere_Produto_Encartelamento_AJAX
	Insere um produto ou um encartelamento dinamicamente na tabela html
	*/
	function Insere_Programa_AJAX () {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		global $cargo_programa;
		global $programa;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();


		$tabela = $cargo_programa->Monta_Tabela_Programa();
		
		
		// adiciona a tabela
		$objResponse->addAppend("div_programa", "innerHTML", $tabela);

		// retorna o resultado XML
    return $objResponse->getXML();

  }
  
  function Limpa_Programa_AJAX ($post){
  	
  	// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
    
    $cont = 1;
     
    while(isset($post["modulo_$cont"]))
    {
    		$objResponse->addClear("permissao_adicionar_modulo_$cont","checked");
				$objResponse->addClear("permissao_editar_modulo_$cont","checked");
				$objResponse->addClear("permissao_excluir_modulo_$cont","checked");
				$objResponse->addClear("permissao_listar_modulo_$cont","checked");
				$cont++;
    }
    
    $cont = 1;
     
    while(isset($post["submodulo_$cont"]))
    {
    		$objResponse->addClear("permissao_adicionar_submodulo_$cont","checked");
				$objResponse->addClear("permissao_editar_submodulo_$cont","checked");
				$objResponse->addClear("permissao_excluir_submodulo_$cont","checked");
				$objResponse->addClear("permissao_listar_submodulo_$cont","checked");
				$cont++;
    }
    
    $cont = 1;
     
    while(isset($post["programa_$cont"]))
    {
    		$objResponse->addClear("permissao_adicionar_programa_$cont","checked");
				$objResponse->addClear("permissao_editar_programa_$cont","checked");
				$objResponse->addClear("permissao_excluir_programa_$cont","checked");
				$objResponse->addClear("permissao_listar_programa_$cont","checked");
				$cont++;
    }
    
    // retorna o resultado XML
    return $objResponse->getXML();
  	
  }
 


  

	/*
	Função: Deleta_Produto_Encartelamento_AJAX
	Deleta um produto dinamicamente na tabela html
	*/
	function Deleta_Programa_AJAX ($idprograma) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;

		global $programa;
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

		// busca os dados do programa
		$info_programa = $programa->getById($idprograma);
		
		// verifica se vai remover: Pula 1 linha se clicar no cancelar
		$objResponse->addConfirmCommands(1, utf8_encode("Deseja retirar as permissões de '" . $info_programa['nome_programa'] . "' deste cargo ?"));

		// remove a tabela
		$objResponse->addRemove($nome_tabela);

		// retorna o resultado XML
    return $objResponse->getXML();

  }


  	/*
	Fun??o: Seleciona_Referencia_AJAX
 	Seleciona as referencias do produto e colocam eles dinamicamente na tabela html
	*/
	function Seleciona_Programa_AJAX ($post) {

		// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;


		//---------------------
		
		
		
		

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
    
  	
    
    $objResponse->loadXML(Limpa_Programa_AJAX($post));
    
    
   	$codigoFuncionario = $post['idfuncionario_programa'];

			$list_sql = "	SELECT
											FPROG.*   , FNC.nome_funcionario , PROG.* , FNC.idfuncionario as cargo, SMDL.* , MDL.*
										FROM
           						{$conf['db_name']}funcionario_programa FPROG
           							INNER JOIN {$conf['db_name']}funcionario FNC ON FPROG.idfuncionario=FNC.idfuncionario
           							INNER JOIN {$conf['db_name']}programa PROG ON FPROG.idprograma = PROG.idprograma
           							INNER JOIN {$conf['db_name']}submodulo SMDL ON PROG.idsubmodulo = SMDL.idsubmodulo
           							INNER JOIN {$conf['db_name']}modulo MDL ON SMDL.idmodulo = MDL.idmodulo

										WHERE
											  FPROG.idfuncionario = $codigoFuncionario
											  
										ORDER BY
											FNC.nome_funcionario ASC ";
											



		//manda fazer a pagina??o
		$list_q = $db->query($list_sql);


		if($list_q){

			//busca os registros no banco de dados e monta o vetor de retorno
			$cont = 0;

			while($list = $db->fetch_array($list_q)){
				

				
				$idsubmodulo = $list['idsubmodulo'];
				$idprograma = $list['idprograma'];
				$post['idprograma'] = $list['idprograma'];

				
				
				if($list['permissao_adicionar']==1)	
				{
					if($list['submodulo_final']==1) $objResponse->addAssign("permissao_adicionar_submodulo_$idsubmodulo","checked",true);
					$objResponse->addAssign("permissao_adicionar_programa_$idprograma","checked",true);
				}
				
				
				if($list['permissao_editar']==1)	
				{
					if($list['submodulo_final']==1) $objResponse->addAssign("permissao_editar_submodulo_$idsubmodulo","checked",true);
					$objResponse->addAssign("permissao_editar_programa_$idprograma","checked",true);
				}
				
				if($list['permissao_excluir']==1)	
				{
					if($list['submodulo_final']==1) $objResponse->addAssign("permissao_excluir_submodulo_$idsubmodulo","checked",true);
					$objResponse->addAssign("permissao_excluir_programa_$idprograma","checked",true);
				}
				
				if($list['permissao_listar']==1)	
				{
					if($list['submodulo_final']==1) $objResponse->addAssign("permissao_listar_submodulo_$idsubmodulo","checked",true);
					$objResponse->addAssign("permissao_listar_programa_$idprograma","checked",true);
				}

        $cont++;
			} // fim do while

		}

		// retorna o resultado XML
    return $objResponse->getXML();

	}
	
	
	/* função seleciona_herdeiro();
	   ao selecionar um determinado checkbox marca todos os check box herdeiros
	*/
	
	function Seleciona_Herdeiro($tipo,$idpai,$permissao,$post = "")
	{
				// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;

		
		//---------------------

		// cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
		
		// se nao tiver nada no post, da um submit no form para pegar ele
		if ($post == "") {
			$objResponse->addScript("xajax_Seleciona_Herdeiro($tipo,$idpai,$permissao,xajax.getFormValues('for'));");
			return $objResponse->getXML();
		}
		else {
		
		
		if($tipo == 0) $tipo = "modulo"; else $tipo = "submodulo";
		if($permissao == 0) $permissao = "adicionar"; else if($permissao == 1) $permissao = "editar"; else if($permissao==2) $permissao = "excluir"; else $permissao = "listar";
		
    
		if($tipo == "modulo")
		{
			$aux = "permissao_".$permissao."_modulo_".$idpai;
			
		
			
			$list_sql = "	SELECT
											PROG.* , SMDL.* , MDL.*
											FROM
	           						{$conf['db_name']}programa PROG 
	           							INNER JOIN {$conf['db_name']}submodulo SMDL ON PROG.idsubmodulo = SMDL.idsubmodulo
	           							INNER JOIN {$conf['db_name']}modulo MDL ON SMDL.idmodulo = MDL.idmodulo
	
											WHERE
												  MDL.idmodulo = $idpai
												  
											 ";
		}
		else 
		{
			$aux = "permissao_".$permissao."_submodulo_".$idpai;
			
			
			$list_sql = "	SELECT
												PROG.* , SMDL.* , MDL.*
											FROM
	           						{$conf['db_name']}programa PROG 
	           							INNER JOIN {$conf['db_name']}submodulo SMDL ON PROG.idsubmodulo = SMDL.idsubmodulo
	           							INNER JOIN {$conf['db_name']}modulo MDL ON SMDL.idmodulo = MDL.idmodulo
	
											WHERE
												  SMDL.idsubmodulo = $idpai
												  
											";
		}


		//manda fazer a pagina??o
		$list_q = $db->query($list_sql);


		if($list_q){


			while($list = $db->fetch_array($list_q)){
				
				$idsubmodulo = $list['idsubmodulo'];
				$idprograma = $list['idprograma'];
				$post['idprograma'] = $list['idprograma'];
				$post['idcargo'] = $list['cargo'];
				
				if($post["$aux"] == true)
				{
						if($permissao=="adicionar")	
						{
							if($tipo == "modulo") $objResponse->addAssign("permissao_adicionar_submodulo_$idsubmodulo","checked",true);
							$objResponse->addAssign("permissao_adicionar_programa_$idprograma","checked",true);
						}
						
						if($permissao=="editar")	
						{
							if($tipo == "modulo")$objResponse->addAssign("permissao_editar_submodulo_$idsubmodulo","checked",true);
							$objResponse->addAssign("permissao_editar_programa_$idprograma","checked",true);
						}
						
						if($permissao=="excluir")	
						{
							if($tipo == "modulo") $objResponse->addAssign("permissao_excluir_submodulo_$idsubmodulo","checked",true);
							$objResponse->addAssign("permissao_excluir_programa_$idprograma","checked",true);
						}
						
						if($permissao=="listar")	
						{
							if($tipo == "modulo")$objResponse->addAssign("permissao_listar_submodulo_$idsubmodulo","checked",true);
							$objResponse->addAssign("permissao_listar_programa_$idprograma","checked",true);
						}
				}
				else 
				{
						if($permissao=="adicionar")	
						{
							if($tipo == "modulo") $objResponse->addAssign("permissao_adicionar_submodulo_$idsubmodulo","checked",false);
							$objResponse->addAssign("permissao_adicionar_programa_$idprograma","checked",false);
						}
						
						if($permissao=="editar")	
						{
							if($tipo == "modulo")$objResponse->addAssign("permissao_editar_submodulo_$idsubmodulo","checked",false);
							$objResponse->addAssign("permissao_editar_programa_$idprograma","checked",false);
						}
						
						if($permissao=="excluir")	
						{
							if($tipo == "modulo") $objResponse->addAssign("permissao_excluir_submodulo_$idsubmodulo","checked",false);
							$objResponse->addAssign("permissao_excluir_programa_$idprograma","checked",false);
						}
						
						if($permissao=="listar")	
						{
							if($tipo == "modulo")$objResponse->addAssign("permissao_listar_submodulo_$idsubmodulo","checked",false);
							$objResponse->addAssign("permissao_listar_programa_$idprograma","checked",false);
						}
				}

			} // fim do while

		
			}
		
			// retorna o resultado XML
    	return $objResponse->getXML();
		
		}
	}
		 
  	/* função Manipula_Div();
	   ao selecionar um determinado checkbox marca todos os check box herdeiros
	*/
	
	function Manipula_Div($id,$tipo,$display)
	{
				// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;

		
		//---------------------
		$objResponse = new xajaxResponse();
	
			
			if($tipo == 0) 
			{	
						$tipo = "modulo"; 
						$div2 = "div_m_" . $id;
						$div = "div_" . $tipo . "_" . $id;
						if($display == 0)	
						{ 
							$objResponse->addAssign("$div","style.display","block"); 
							$objResponse->addEvent("$div2","onclick",null);
							$objResponse->addEvent("$div2","onclick","xajax_Manipula_Div($id,0,1);");
							$objResponse->addReplace("$div2","innerHTML","+","-");
						}
						else
						{ 
							$objResponse->addAssign("$div","style.display","none"); 
							$objResponse->addEvent("$div2","onclick",null);
							$objResponse->addEvent("$div2","onclick","xajax_Manipula_Div($id,0,0);");
							$objResponse->addReplace("$div2","innerHTML","-","+");
						}
			}
			else
			{
						$tipo = "submodulo";
						$div2 = "div_s_" . $id;
						$div = "div_" . $tipo . "_" . $id;
					
						if($display == 0)	
						{ 
							$objResponse->addAssign("$div","style.display","block"); 
							$objResponse->addEvent("$div2","onclick",null);
							$objResponse->addEvent("$div2","onclick","xajax_Manipula_Div($id,1,1);");
							$objResponse->addReplace("$div2","innerHTML","+","-");
						}
						else
						{	 
							$objResponse->addAssign("$div","style.display","none"); 
							$objResponse->addEvent("$div2","onclick",null);
							$objResponse->addEvent("$div2","onclick","xajax_Manipula_Div($id,1,0);");
							$objResponse->addReplace("$div2","innerHTML","-","+");
						}
			}
			
	
			
		// retorna o resultado XML
		return $objResponse->getXML();
		
	}

 	/* função Mostra_Div();
	   ao selecionar todas as divs recebem visibilidade
	*/
	
	function Mostra_Div()
	{
				// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;

		global $programa;
		
		//---------------------
		$objResponse = new xajaxResponse();
		
    $aux = $programa->make_list(0,99999);
    
    $cont =0;
    while($cont <= count($aux))
    {
      $objResponse->loadXML(Manipula_Div($aux[$cont]["idprograma"],0,0));
      $objResponse->loadXML(Manipula_Div($aux[$cont]["idprograma"],1,0));
      $cont++;
    }    
		
		// retorna o resultado XML
		return $objResponse->getXML();
		
	}
	
	 /* função Esconde_Div();
	   ao selecionar todas as divs recebem hide
	*/
	
		function Esconde_Div()
	{
				// vari?veis globais
		global $form;
		global $conf;
		global $db;
		global $falha;

		global $programa;
		
		//---------------------
		$objResponse = new xajaxResponse();
		
    $aux = $programa->make_list(0,99999);
    
    $cont =0;
    while($cont <= count($aux))
    {
      $objResponse->loadXML(Manipula_Div($aux[$cont]["idprograma"],0,1));
      $objResponse->loadXML(Manipula_Div($aux[$cont]["idprograma"],1,1));
      $cont++;
    }    
		
		// retorna o resultado XML
		return $objResponse->getXML();
		
	}


?>
