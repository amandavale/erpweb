<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  require_once("../common/lib/xajax/xajax.inc.php");  
  
  require_once("../entidades/sintegra.php");
	require_once("../entidades/filial.php"); 
	require_once("../entidades/produto.php");

  require_once("sintegra_ajax.php");


  // configurações anotionais
  $conf['area'] = "Emissão de arquivos para o COTEPE"; // área


  //configuração de estilo
  $conf['style'] = "full";

  // inicializa templating
  $smarty = new Smarty;
  
  // cria o objeto xajax
  $xajax = new xajax();
  
  // registra todas as funções que serão usadas
  $xajax->registerFunction("Verifica_Campos_Sintegra_AJAX");  
  $xajax->registerFunction("insereCampoUpload");
  $xajax->registerFunction("reinicializaSessao_AJAX");
  
  // processa as funções
  $xajax->processRequests();

  // configura diretórios
  $smarty->template_dir = "../common/tpl";
  $smarty->compile_dir =   "../common/tpl_c";



  // seta configurações
  $smarty->assign("conf", $conf);

  // ação selecionada
  $flags['action'] = $_GET['ac'];
  if ($flags['action'] == "") $flags['action'] = "gerar";

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


        $list = $auth->check_priv($conf['priv']);
        $aux = $flags['action'];
        if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}



        switch($flags['action']) {

         case "gerar":       

            if($_POST['for_chk']) {

              $sintegra = new sintegra();
              $filial = new filial();
              $produto = new produto();
      
              // inicializa banco de dados
              $db = new db();
      
              //incializa classe para validação de formulário
              $form = new form();
      
              $fp = fopen("../common/arq/cotepe.txt","w+");
      
              // P1  - IDENTIFICAÇÃO DO ESTABELECIMENTO USUÁRIO DO ECF
      
              
      
              $info_filial = $filial->getById($_SESSION['idfilial_usuario']); 
                    
        
              $registroP1['tipo'] = "P1";
              $registroP1['cnpj'] = $sintegra->formataCamposSintegra($sintegra->FormataCNPJParaSintegra($info_filial['cnpj_filial']), "N", 14);
              $registroP1['inscricao_estadual'] = $sintegra->formataCamposSintegra($info_filial['inscricao_estadual_filial'], "X", 14);
              $registroP1['razao_social'] = $sintegra->formataCamposSintegra($info_filial['nome_filial'], "X", 35);
       
              //não possui inscricao municipal, portanto é preenchida como zero
              $registroP1['inscricao_municipal'] = $sintegra->formataCamposSintegra("", "X", 35);

              $p1 = $registroP1['tipo'] . $registroP1['cnpj'] . $registroP1['inscricao_estadual'] . $registroP1['inscricao_municipal'] . $registroP1['razao_social'] . "\r\n";
      
              fwrite($fp,$p1);
              
              
              //P2 - RELAÇÃO DE MERCADORIAS E SERVIÇOS
              $registroP2 = 0;
      
              $filtro = "WHERE PRDFL.idfilial = " . $_SESSION["idfilial_usuario"];
              
              for($j=0;$j<=30;$j++)
              {
                $info_produto = $produto->make_list_cotep_icms($j,1000,$filtro);
                              
                for($i=0;$i<count($info_produto);$i++)
                {
                  $tipo = "P2";
                  $codigo = $sintegra->formataCamposSintegra($info_produto[$i]['idproduto'],'X',14);
                  $descricao = $sintegra->formataCamposSintegra($info_produto[$i]['descricao_produto'],'X',50);
                  $unidade = $sintegra->formataCamposSintegra($info_produto[$i]['sigla_unidade_venda'],'X',6);
                  $sit_trib = $info_produto[$i]['situacao_tributaria_produto'];
                  $icms = $sintegra->formataCamposSintegra($sintegra->formataMoedaParaExibir($info_produto[$i]['icms_produto']),'N',6);
                  $valor_unitario = $sintegra->formataCamposSintegra($sintegra->formataMoedaParaExibir($info_produto[$i]['preco_balcao_produto']),'N',6);
                  $registroP2++;
                  $x = $tipo . $registroP1['cnpj'] . $codigo . $descricao . $unidade . $sit_trib . $icms . $valor_unitario . "\r\n";
                  
                  fwrite($fp,$x);
                }
              }

              //criação do vetor com as informações do registro P9 - TOTALIZAÇÃO DO ARQUIVO
              
              $registroP9['tipo'] = "P9";
              $registroP9['cnpj'] = $sintegra->formataCamposSintegra($sintegra->FormataCNPJParaSintegra($info_filial['cnpj_filial']), "N", 14);
              $registroP9['inscricao_estadual'] = $sintegra->formataCamposSintegra($info_filial['inscricao_estadual_filial'], "X", 14);
              $registroP9['qtd'] = $sintegra->formataCamposSintegra($registroP2, "N", 6);
              
              $p9 = $registroP9['tipo'] . $registroP9['cnpj'] . $registroP9['inscricao_estadual'] . $registroP9['qtd'] . "\r\n";
              
              fwrite($fp,$p9);
              
              fseek($fp,0);
              
              //fechamento do arquivo
              
              
              $arquivo = "cotep.txt";
            
              header("Pragma: public");
              header("Expires: 0");
              header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
              header("Content-Type: application/force-download");
              header( "Content-Disposition: attachment; filename=".basename($arquivo));
              header( "Content-Description: File Transfer");
              readfile("../common/arq/cotepe.txt");
              exit(0);
              
              
             /*
              $nome = "cotepe.txt";
              header("Content-type: application/force-download");
              header("Content-Disposition: attachment; filename=$nome");
              while(!feof($fp))
                  echo fgetc($fp);
              fclose($fp);
              exit(0);
             
              break;
              */

           }
        }

			}
		}
	}


  
  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);


  $smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));

  $list_permissao = $auth->check_priv($conf['priv']);
  $smarty->assign("list_permissao",$list_permissao);
  
  $smarty->display("adm_consulta_cotep.tpl");


?>
