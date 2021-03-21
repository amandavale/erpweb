<?php

	//inclus�o de bibliotecas
	require_once("../common/lib/conf.inc.php");
	require_once("../common/lib/db.inc.php");
	require_once("../common/lib/auth.inc.php");
	require_once("../common/lib/form.inc.php");
	require_once("../common/lib/Smarty/Smarty.class.php");

	require_once("../entidades/cliente_condominio.php");
	require_once("../entidades/apartamento.php");
	
	// inicializa templating
	$smarty = new Smarty;

	// a��o selecionada
	$flags['action'] = $_GET['ac'];

	// inicializa autentica��o
	$auth = new auth();

	//inicializa classe
	$cliente_condominio = new cliente_condominio();
	$apartamento = new apartamento();

	// inicializa banco de dados
	$db = new db();

	//incializa classe para valida��o de formul�rio
	$form = new form();
        
				

	switch($flags['action']) {
		
		case "busca_apartamento":

			$apartamento->Filtra_Apartamento_AJAX($_GET['typing'], $_GET['campoID'], $_GET['mostraDetalhes']);

		break;
		
		case "seleciona_cliente_condominio":

			$apartamento->Filtra_Cliente_AJAX($_GET['typing'], $_GET['campoID'], $_GET['mostraDetalhes']);

		break;
		
	}

	
	
	
	
	function Ver_Boletos_Condominos_AJAX($post) {

 	  // vari�veis globais
	  global $form, $conf, $db, $movimento, $apartamento;

	  // cria o objeto xajaxResponse
	  $objResponse = new xajaxResponse();
	
	  
	  
	  
      if(!$form->chk_IsDate($post['data_vencimento'],'')){
      	$objResponse->addAlert(utf8_encode('A data de vencimento n�o � v�lida'));	
      }
      else{

	    $data = explode('/',$post['data_vencimento']);
	    
	    $data_de = '01/'.$data[1].'/'.$data[2];
	    $data_ate = $form->UltimoDiaMes($data[1],$data[2]).'/'.$data[1].'/'.$data[2];
	
		$redir = $conf['addr'].'/admin/movimento.php?pg=0&boletos=1&data_vencimento_de='.$data_de.'&data_vencimento_ate='.$data_ate.'&idcliente_Nome='.$_SESSION['nome_cliente'].'&idcliente='.$_SESSION['idcliente'].'&taxa_condominio=1';	        
        $objResponse->addScript('window.location = "'.utf8_encode($redir).'"');	
		
	  }
	  
      // retorna o resultado XML
      return $objResponse->getXML();
    
	}
	



 
	function Gerar_Boletos_Condominos_AJAX($post) {

 		// vari�veis globais
	  	global $form, $conf, $db, $movimento, $apartamento, $parametros;

		// cria o objeto xajaxResponse
	  	$objResponse = new xajaxResponse();
	  
      	$idcondominio = $_SESSION['idcliente'];

      	$db->query('BEGIN'); //Inicia a transa��o no banco de dados

      	$form->chk_IsDate($post['data_vencimento'], 'A data de vencimento');
      	$form->chk_empty($post['descricao'],1, 'a Descri&ccedil;&atilde;o do Movimento');
      	$form->chk_empty($post['data_geracao'],1, 'm&ecirc;s e ano referentes &agrave; gera&ccedil;&atilde;o');
      
      	if(count($post['apartamentos']) == 0){
      		$form->err[] = "Para gerar novos lan�amentos, ao menos um condom�nio deve ser selecionado." ;
      	}
      
		/// valida m�s e ano referentes � gera��o do boleto
      
      	$mes_geracao = 0;
      	$ano_geracao = 0;
      	
      	if(!empty($post['data_geracao'])){
      	
      		$mes_ano_geracao = explode('/',$post['data_geracao']);
      		$mes_geracao = intval($mes_ano_geracao[0]);      		
      		$ano_geracao = intval($mes_ano_geracao[1]);

      		if(($mes_geracao < 1) || ($mes_geracao > 12) || (strlen($ano_geracao) != 4)){
      			
      			$form->err[] = 'Preencha corretamente m&ecirc;s e ano referentes &agrave; gera&ccedil;&atilde;o.';
      		}
      		else{
      			/// formata m�s para fazer busca no banco
      			$mes_geracao = str_pad($mes_geracao, 2, '0', STR_PAD_LEFT);      			
      		}
      	}

      	
		if(count($form->err)){
      	
			$objResponse->addAlert(html_entity_decode(utf8_encode(implode("\n",$form->err))));
      	}
      	else{

			foreach($post['apartamentos'] as  $key => $idapartamento){
	                     
	  			$valor_movimento = $form->FormataMoedaParaInserir($post['valor_'.$idapartamento]);
	  		
	  			$date = explode('/', $post['data_vencimento']);
		  		//$gerou_cond_mes = $apartamento->chk_taxa_condominio_gerada($idapartamento, $date[1],$date[2],$date[0]);
		  		
	  			/// Verifica se gerou o boleto para m�s e ano de refer�ncia fornecidos no formul�rio
	  			$gerou_cond_mes = $apartamento->verificaTaxaCondominioGerada($idapartamento, $mes_geracao,$ano_geracao);

	  			/** Por enquanto foi removida a trava de gera��o de boletos a pedido da Giovana. 
	  			  * Essa verifica��o ser� alterada com uma mensagem de alerta na tela.
	  			  */
		  		if($gerou_cond_mes){
		  			$movimento->err[] = 'O condom�nio para o mes '.$mes_geracao.' j� foi gerado no apto. '.$post['apto_'.$idapartamento].'.';
	  			}
	  		
	  			if(!is_numeric($valor_movimento) || $valor_movimento <= 0.00){
					$movimento->err[] = 'O valor do movimento do apto '.$post['apto_'.$idapartamento].' deve ser maior que zero.';
	  			}
  		
		        //Verifica se a cobran�a ser� para o morador ou para o propriet�rio do apartamento
		        $idcliente_origem = $post['situacao_'.$idapartamento] == 'I' ? $post['idmorador_'.$idapartamento] : $post['idproprietario_'.$idapartamento] ;
	    	    $info['idcliente_origem'] = $idcliente_origem;
	        	$info['idcliente_destino'] = $idcondominio;      
	        	$info['idplano_credito'] = (int)$parametros->getParam('idplanoBoletoCond');
	        	$info['idfilial'] = $_SESSION['idfilial_usuario'];
		        $info['descricao_movimento'] = utf8_decode($post['descricao']);
		        $info['valor_movimento'] = $form->FormataMoedaParaInserir($post['valor_'.$idapartamento]); 
	    	    $info['data_cadastro'] = date('Y-m-d H:i:s');
	        	
	    	    /**
	    	     * Verifica se o movimento est� sendo gerado para o m�s atual.
	    	     * Se sim, a data do movimento � a data atual.
	    	     * Se n�o, a data do movimento � o �ltimo dia do m�s de refer�ncia para que o boleto
	    	     * seja gerado corretamente.
	    	     */ 
	    	    if($mes_geracao == date('m')){
	    	    	$info['data_movimento'] = date('Y-m-d');
	    	    }
	        	else{
 		    	    $info['data_movimento'] = $ano_geracao . '-' . 
 		    	    						$mes_geracao . '-' . 
 		    	    						$form->UltimoDiaMes($mes_geracao,$ano_geracao);
	        	}
	    	    
	        	$info['data_vencimento'] = $form->FormataDataParaInserir($post['data_vencimento']);
	        	$info['gerar_fatura'] = '1';
	        	$info['idapartamento'] = $idapartamento;

	      		$valores_boleto = $movimento->Calcula_MultaJurosDesc($idcondominio, $info['valor_movimento'], $post['data_vencimento'], true);

	      		$info['valor_juros'] = $valores_boleto['juros'];
	      		$info['valor_multa'] = $valores_boleto['multa'];
	      		$info['desconto'] = $valores_boleto['desconto'];
	
		        $movimento->set($info);
	        
	    	 	if(count($movimento->err)){
		  			$objResponse->addScript("document.getElementById('valor_$idapartamento').focus();");
		  			$db->query('ROLLBACK');
	  				break;
	  			}
			}
	
			if(count($movimento->err) == 0){
	      		
	        	$db->query('COMMIT');
	        	$objResponse->addAlert(utf8_encode('Lan�amentos gerados com sucesso!'));
	        
	        	$redir = $conf['addr'].'/admin/movimento.php?pg=0&boletos=1&data_vencimento_de='.$post['data_vencimento'].'&data_vencimento_ate='.$post['data_vencimento'].'&idcliente_Nome='.$_SESSION['nome_cliente'].'&idcliente='.$idcondominio;
	        
	        	$objResponse->addScript('window.location = "'.utf8_encode($redir).'"');
	        
	      	}
		  	else $objResponse->addAlert(html_entity_decode(utf8_encode(implode("\n",$movimento->err))));
      
      	}

      	// retorna o resultado XML
	    return $objResponse->getXML();
	    
	}
  
  
  
  	

 
	function chk_Condominio_Gerado_AJAX($post) {

		// vari�veis globais
		global $form, $conf, $db, $movimento, $apartamento;

		// cria o objeto xajaxResponse
		$objResponse = new xajaxResponse();
		
		foreach(explode('|', $post['apto_ids']) as  $key => $idapartamento){
	                     
	  		$valor_movimento = $form->FormataMoedaParaInserir($post['valor_'.$idapartamento]);
	      	
	  		
	  		$date = explode('/', $post['data_vencimento']);
	  		$gerou_cond_mes = $apartamento->chk_taxa_condominio_gerada($idapartamento, $date[1],$date[2],$date[0]);
			
	  		$objResponse->addClear("div_movimento_$idapartamento","innerHTML");


	  		if($gerou_cond_mes){
	  			
	  			$link = '<a href="'.$conf['addr'].'/admin/movimento.php?ac=editar&idmovimento='.$gerou_cond_mes[0]['idmovimento'].'" target="_blank">'.
								'<img src="'.$conf['addr'].'/common/img/check.gif" />'.
		  					'</a>';
	  			$objResponse->addAppend("div_movimento_$idapartamento","innerHTML", $link);
				
	  		}
			else{
				
				$input = '<input type="checkbox" id="chk_'.$idapartamento.'" name="apartamentos[]" value="'.$idapartamento.'" checked >';
				$objResponse->addAssign("div_movimento_$idapartamento","innerHTML", $input);
					
			}
	
			
		}
  
		
      	// retorna o resultado XML
		return $objResponse->getXML();	  
	  
	}

	function Verifica_Campos_Apartamento_AJAX($post) {

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			global $err;
			global $cliente_fisico;

			global $apartamento;
			//---------------------


			// cria o objeto xajaxResponse
	    	$objResponse = new xajaxResponse();


			
			

			if ($_GET['ac'] == "editar") {
			
				$situacao = $post['litsituacao'];

				$info['idapartamento'] = intval($_GET['idapartamento']);
				
				$form->chk_empty($post['litapto'], 1, 'o N�mero');
				$form->chk_empty($post['litsituacao'], 1, 'a Situa��o');
				
				
				
				 //verifica se o APTO do cliente est� duplicado
			   	if ($apartamento->Verifica_APTO_Duplicado($post['litapto'], $info['idapartamento'])){
				   $err[] = "Este n�mero de apartamento j� existe e n�o pode ser duplicado!";
				}
				
				  
			}
			else {
			
				$situacao = $post['situacao'];
       			
				$form->chk_empty($post['apto'], 1, 'o N�mero');
				$form->chk_empty($post['situacao'], 1, 'a Situa��o');
				
				 //verifica se o APTO do cliente est� duplicado
			   	if ($apartamento->Verifica_APTO_Duplicado($post['apto'])){
				   $err[] = "Este n�mero de apartamento j� existe e n�o pode ser duplicado!";
				}
			
			}
			

			if( empty($post['idproprietario'])){

				$form->chk_empty($post['nome_proprietario'], 1, 'o Nome do propriet�rio');						
				
				if(!empty($post['cpf_proprietario'])){
					if ( $form->chk_cpf($post['cpf_proprietario'], 0) && $cliente_fisico->Verifica_CPF_Duplicado($post['cpf_proprietario']) ) $form->err[] = "O CPF do propriet�rio j� existe e n�o pode ser duplicado!";				
				}
				
				
			}//----------------------------------------------------------------
							
			if( empty($post['idmorador']) && $situacao == 'I' ){

				$form->chk_empty($post['nome_morador'], 1, 'o Nome do morador');						
				
				if(!empty($post['cpf_morador'])){
					if ( $form->chk_cpf($post['cpf_morador'], 0) && $cliente_fisico->Verifica_CPF_Duplicado($post['cpf_morador']) ) $form->err[] = "O CPF do morador j� existe e n�o pode ser duplicado!";				
				}
				
				
			}//----------------------------------------------------------------

			
			$err = $form->err;
			
			
			//Verifica se o usu�rio selecionou um condom�nio
			if (!$_SESSION['idcliente']){
				$err[] = 'Ainda n�o foi selecionado um condom�nio. Por favor, selecione um na op��o "<a href="'.$conf['addr'].'/admin/apartamento.php?ac=selecionar_condominio">Selecionar Condom�nio"';
			}

			if ( ($_POST['custoFixo'] != '') && ($_POST['fracaoIdeal'] != '') ){
				$err[] = 'Os campos "Fra��o Ideal" e "Custo Fixo" possuem valores. Deve-se escolher apenas um destes campos para ser preenchido.';
			}
			
				
			// se nao houveram erros, da o submit no form
		    if(count($err) == 0) {
		    	$objResponse->addScript("document.getElementById('for_apartamento').submit();");
		    }
		    // houve erros, logo mostra-os
		    else {
					$mensagem = implode("\n",$err);
					$objResponse->addAlert(utf8_encode(html_entity_decode($mensagem)));
				}

				// retorna o resultado XML
		    return $objResponse->getXML();
	  }


?>
