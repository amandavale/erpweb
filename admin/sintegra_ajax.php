<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");



  // inicializa templating
  $smarty = new Smarty;

  // ação selecionada
  $flags['action'] = $_GET['ac'];

  // inicializa autenticação
  $auth = new auth();

  // inicializa banco de dados
  $db = new db();

  //incializa classe para validação de formulário
  $form = new form();
        

  /*
	Função: Verifica_Campos_Sintegra_AJAX
	Verifica se os campos do SINTEGRA de estoque foram preenchidos
	*/

	function Verifica_Campos_Sintegra_AJAX ($post) {

		// variáveis globais
		global $form;
		global $conf;
		global $db;
		global $falha;
		global $err;

		//---------------------


			// cria o objeto xajaxResponse
	    $objResponse = new xajaxResponse();
	    
     

      //if(($post['mes_2'] == $post['mes_1'] AND $post['ano_2'] < $post['ano_1']) OR ($post['mes_2'] < $post['mes_1'] AND $post['ano_2'] <= $post['ano_1']))

        //$err[] = "A data do primeiro mês tem que ser inferior ou igual ao do segundo mês.";

	   	
			
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
  Função: insereCampoUpload
  insere mais um campo upload no formulario.
  */

  function insereCampoUpload ($post) {

    // variáveis globais
    global $form;
    global $conf;
    global $db;
    global $falha;
    global $err;

    //---------------------
    
      // cria o objeto xajaxResponse
      $objResponse = new xajaxResponse();
      
      
      
      $total = $post['total_arquivo'];
      $total++;
      
      $tabela = "<tr>
                  <td>
                    <input type='file' name='arquivo_'".$total."' id='arquivo_'".$total."' size='20'>
                  </td>
                </tr>";
                
      $nome_tabela = "tabela_".$total;
      
      //conserta bug do addAppend no mozilla  
        
      if($_SESSION['browser_usuario'])
      {
        // adiciona a tabela
        $objResponse->addCreate("div_arquivo", "table", "$nome_tabela");
        $objResponse->addAssign("$nome_tabela", "innerHTML", "$tabela");
      }
      
      else
      {
        $tabela = '<table id="'.$nome_tabela.'">'.$tabela.'</table>';
        $objResponse->addAppend("div_produtos", "innerHTML", $tabela);
      }

      $objResponse->addAssign("total_arquivo","value", $total);
      
      return $objResponse->getXML();
    
    
  }



?>
