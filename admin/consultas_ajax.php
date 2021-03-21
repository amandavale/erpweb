<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  
  require_once("../entidades/produto.php");  
  require_once("../entidades/produto_filial.php");



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
        
  $produto = new produto();
  $produto_filial = new produto_filial();        

  

 /*
  Fun??o: Verifica_Campos_Movimentacao_Mes_AJAX
  Verifica se os campos da busca rápida do contas a receber foram preenchidos
  */
  function Verifica_Campos_Movimentacao_Mes_AJAX ($post) {

    // vari?veis globais
    global $form;
    global $conf;
    global $db;
    global $falha;
    global $err;
    //---------------------

    
    // cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();

    $form->chk_IsDate($post['data_1'], "Data inicial");
    $form->chk_IsDate($post['data_2'], "Data final");
    if($form->data1_maior($post['data_1'],$post['data_2'])) $form->err[]= "A data final deve ser maior que a inicial.";
    
    $err = $form->err;
    

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


  function Mostra_Detalhes_Produto_AJAX($idproduto){
  
    // vari?veis globais
    global $form;
    global $conf;
    global $db;
    global $falha;
    global $err;

    global $produto;
    global $produto_filial;
    //---------------------  
      
    // cria o objeto xajaxResponse
    $objResponse = new xajaxResponse();
  
    $info = $produto->getById($idproduto);
    $info_produto_filial = $produto_filial->getByIdFilial($idproduto,$_SESSION['idfilial_usuario']);
    


    $tabela = utf8_encode("<table border='0' width='95%'>
  
         <table class='tb4cantos' width='95%'' align='center' cellpadding='5' cellspacing='5'>
          <tr>
            <th align='right' width='45%'>Código</th>
            <td align='left'><a class='menu_item' href = ".$conf['addr']."/admin/produto.php?ac=listar&buscar_dados_produto=1&idproduto=".$idproduto.">".$info['codigo_produto']."</a></td>
          </tr>
          </table>

          <br>

         <table class='tb4cantos' width='95%'' align='center' cellpadding='5' cellspacing='5'>          
          <tr>
            <th align='right' width='45%'>Produto</th>
            <td align='left'><a class='menu_item' href = ".$conf['addr']."/admin/produto.php?ac=listar&buscar_dados_produto=1&idproduto=".$idproduto.">".$info['descricao_produto']."</a></td>
         </table>

          <br>

         <table class='tb4cantos' width='95%'' align='center' cellpadding='5' cellspacing='5'>
          <tr>
          </tr>
            <th align='right' width='45%'>Un.</th>
            <td align='left'><a class='menu_item' href = ".$conf['addr']."/admin/produto.php?ac=listar&buscar_dados_produto=1&idproduto=".$idproduto.">".$info['nome_unidade_venda']."</a></td>
          <tr>
        </table>

          <br>

        <table class='tb4cantos' width='95%'' align='center' cellpadding='5' cellspacing='5'>
          </tr>
            <th align='right' width='45%'>Estoque</th>
            <td align='left'><a class='menu_item' href = ".$conf['addr']."/admin/produto.php?ac=listar&buscar_dados_produto=1&idproduto=".$idproduto.">".number_format($info_produto_filial['qtd_produto'],2,",","")."</a></td>
          <tr>
        </table>

          <br>

        <table class='tb4cantos' width='95%'' align='center' cellpadding='5' cellspacing='5'>
          </tr>
            <th align='right' width='45%'>Preço de Balcão (R$)</th>
            <td align='left'><a class='menu_item' href = ".$conf['addr']."/admin/produto.php?ac=listar&buscar_dados_produto=1&idproduto=".$idproduto.">".$info_produto_filial['preco_balcao_produto']."</a></td>
          <tr>
        </table>

          <br>

        <table class='tb4cantos' width='95%'' align='center' cellpadding='5' cellspacing='5'>
          </tr>
            <th align='right' width='45%'>Preço de Oferta (R$)</th>
            <td align='left'><a class='menu_item' href = ".$conf['addr']."/admin/produto.php?ac=listar&buscar_dados_produto=1&idproduto=".$idproduto.">".$info_produto_filial['preco_oferta_produto']."</a></td>
          <tr>
        </table>

          <br>

        <table class='tb4cantos' width='95%'' align='center' cellpadding='5' cellspacing='5'>
          </tr>
            <th align='right' width='45%'>Preço de Atacado (R$)</th>
            <td align='left'><a class='menu_item' href = ".$conf['addr']."/admin/produto.php?ac=listar&buscar_dados_produto=1&idproduto=".$idproduto.">".$info_produto_filial['preco_atacado_produto']."</a></td>
          </tr>
        </table>

          <br>

        <table class='tb4cantos' width='95%'' align='center' cellpadding='5' cellspacing='5'>
          <tr>
            <th align='right' width='45%'>Preço de Telemarketing (R$)</th>
            <td align='left'><a class='menu_item' href = ".$conf['addr']."/admin/produto.php?ac=listar&buscar_dados_produto=1&idproduto=".$idproduto.">".$info_produto_filial['preco_telemarketing_produto']."</a></td>
          </tr>
        </table>

          <br>

          <tr>
            <td class='row' height='1' bgcolor=''#999999' colspan='20'></td>
          </tr>
            
        <tr><td>&nbsp;</td></tr>
        
      </table> 
      *Preços e estoque baseados na filial ".$_SESSION['nomefilial_usuario']);
      
    $objResponse->addAssign("dados_produto","innerHTML",$tabela);
      
    // retorna o resultado XML
    return $objResponse->getXML(); 


  }
 
?>