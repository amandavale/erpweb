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
  require_once("../entidades/conta_pagar.php");
	require_once("../entidades/endereco.php"); 
	require_once("../entidades/filial.php"); 
	require_once("../entidades/cidade.php"); 
	require_once("../entidades/estado.php"); 
	require_once("../entidades/bairro.php"); 
	require_once("../entidades/funcionario.php");

  require_once("sintegra_ajax.php");


  // configurações anotionais
  $conf['area'] = "Emissão de arquivos para o SINTEGRA"; // área


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
              $endereco = new endereco();
              $estado = new estado();
              $cidade = new cidade();
              $bairro = new bairro();
              $funcionario = new funcionario();
      
              // inicializa banco de dados
              $db = new db();
      
              //incializa classe para validação de formulário
              $form = new form();
      

              

      
      
              //Formatação das datas
              
              $_POST['data_1'] = "01"."/".$_POST['mes_1']."/".$_POST['ano_1'];
              $_POST['data_2'] = date("d",mktime(0,0,0,$_POST['mes_1']+1,0,$_POST['ano_1']))."/".$_POST['mes_1']."/".$_POST['ano_1'];
      
              $_POST['data_1'] = $form->FormataDataParaInserir($_POST['data_1']);
              $_POST['data_2'] = $form->FormataDataParaInserir($_POST['data_2']);
      
              //criação do vetor com as informações do registro 10 - Mestre do estabelecimento
      
              $registro10 = array();
      
              $info_filial = $filial->getById($_SESSION['idfilial_usuario']); 
              $info_endereco_filial = $endereco->getById($info_filial['idendereco_filial']);
              $info_estado_filial = $estado->getById($info_endereco_filial['idestado']);
              $info_cidade_filial = $cidade->getById($info_endereco_filial['idcidade']);
              $info_bairro_filial = $bairro->getById($info_endereco_filial['idbairro']);
        
              $registro10['tipo'] = "10";
              $registro10['cnpj'] = $sintegra->formataCamposSintegra($sintegra->FormataCNPJParaSintegra($info_filial['cnpj_filial']), "N", 14);
              $registro10['inscricao_estadual'] = $sintegra->formataCamposSintegra($info_filial['inscricao_estadual_filial'], "X", 14);
              $registro10['razao_social'] = $sintegra->formataCamposSintegra($info_filial['nome_filial'], "X", 35);
              $registro10['estado'] = $sintegra->formataCamposSintegra($info_estado_filial['sigla_estado'], "X", 2);
              $registro10['municipio'] = $sintegra->formataCamposSintegra($info_cidade_filial['nome_cidade'], "X", 30);
              $registro10['fax'] = $sintegra->formataCamposSintegra($info_filial['fax_filial'], "N", 10);
              $registro10['data_inicio'] = $sintegra->formataDataSintegra($_POST['data_1']);
              $registro10['data_termino'] = $sintegra->formataDataSintegra($_POST['data_2']);
      
              $registro10['identificacao'] = '3';
              $registro10['operacoes'] = '3';
              $registro10['finalidade'] = '1';
      
      
              //criação do vetor com as informações do registro 11 - Dados Complementares do Informante
      
              $registro11 = array();
      
              $info_funcionario = $funcionario->getById($_SESSION['usr_cod']);
            
              $registro11['tipo'] = "11";
              $registro11['logradouro'] = $sintegra->formataCamposSintegra($info_endereco_filial['logradouro'], "X", 34);
              $registro11['numero'] = $sintegra->formataCamposSintegra($info_endereco_filial['numero'], "N", 5);
              $registro11['complemento'] = $sintegra->formataCamposSintegra($info_endereco_filial['complemento'], "X", 22);
              $registro11['bairro'] = $sintegra->formataCamposSintegra($info_bairro_filial['nome_bairro'], "X", 15);
              $registro11['cep'] = $sintegra->formataCamposSintegra($sintegra->FormataCEPSintegra($info_endereco_filial['cep']), "N", 8);
              $registro11['nome_contato'] = $sintegra->formataCamposSintegra($info_funcionario['nome_funcionario'], "X", 28);
              $registro11['telefone'] = $sintegra->formataCamposSintegra($info_filial['telefone_filial'], "N", 12);
      
      
      
              //criação dos vetores com as informações dos registros do tipo 50 - Nota Fiscal Eletronica
      
              $registro50_saida = array();
              $registro50_entrada = array();
              
              $filtro = "WHERE O.tipoOrcamento = 'NF'
                        AND (
                          O.datahoraCriacaoNF
                          BETWEEN '".$_POST['data_1']."'
                          AND '".$_POST['data_2']."'
                        )
                        AND O.idfilial = ". $_SESSION['idfilial_usuario'];
      
              $registro50_saida = $sintegra->make_list_saida_50(0, 99999999999, $filtro);
      
              $filtro = "WHERE PED.status_pedido = 'E'
                        AND (
                          PED.data_entrada
                          BETWEEN '".$_POST['data_1']."'
                          AND '".$_POST['data_2']."'
                        )
                          AND PED.idfilial = ". $_SESSION['idfilial_usuario'];
      
              $registro50_entrada = $sintegra->make_list_entrada_50(0, 99999999999, $filtro);
      
              //criacao dos vetores com as informações dos registros do tipo 54 - Produto
      
              $registro54_saida = array();
              $registro54_entrada = array();
      
              $filtro = "WHERE O.tipoOrcamento = 'NF'
                        AND (
                          O.datahoraCriacaoNF
                          BETWEEN '".$_POST['data_1']."'
                          AND '".$_POST['data_2']."'
                        )
                          AND O.idfilial = ". $_SESSION['idfilial_usuario'];
      
              $registro54_saida = $sintegra->make_list_saida_54(0, 99999999999, $filtro);
      
              $filtro = "WHERE PED.status_pedido = 'E'
                        AND (
                          PED.data_entrada
                          BETWEEN '".$_POST['data_1']."'
                          AND '".$_POST['data_2']."'
                        )
                          AND PED.idfilial = ". $_SESSION['idfilial_usuario'];
      
              $registro54_entrada = $sintegra->make_list_entrada_54(0, 99999999999, $filtro);
              
              
              //criacao dos vetores com as informações dos registros provenientes da ECF - Registros do tipo 60 e do tipo 75

              for($i=0;$i<=$_POST['total_arquivo'];$i++)
              {
                if(strlen($_FILES["arquivo_$i"]["name"]) > 0)
                {               
                  $fpECF = fopen($_FILES["arquivo_$i"]["tmp_name"],"r");
                  $registro_ECF[$i] = $sintegra->armazenaRegistrosECF($fpECF);
                  
                }
              }
              
              
              //criacao dos vetores com as informações dos registros do tipo 61 - Documentos não emitidos por impressora fiscal (Serie D)
              
              $registro61 = array();
              
              $filtro = "WHERE O.tipoOrcamento = 'SD'
                        AND (
                          O.datahoraCriacao
                          BETWEEN '".$_POST['data_1']."'
                          AND '".$_POST['data_2']."'
                        )
                          AND O.idfilial = ". $_SESSION['idfilial_usuario'];
       
              $registro61 = $sintegra->make_list_registro_61(0, 99999999999, $filtro);              
              

              //criacao dos vetores com as informações dos registros do tipo 75 - Código de Produto ou Serviço
              
              $registro75_entrada = array();	
              $registro75_saida = array();
      
              $filtro = "WHERE PED.status_pedido = 'E'
                        AND (
                          PED.data_entrada
                          BETWEEN '".$_POST['data_1']."'
                          AND '".$_POST['data_2']."'
                        )
                          AND PED.idfilial = ". $_SESSION['idfilial_usuario'];
      
      
      
              $registro75_saida = $sintegra->make_list_saida_75($_POST, 0, 99999999999, $filtro);
      
              $filtro = "WHERE O.tipoOrcamento = 'NF'
                        AND (
                          O.datahoraCriacaoNF
                          BETWEEN '".$_POST['data_1']."'
                          AND '".$_POST['data_2']."'
                        )
                          AND O.idfilial = ". $_SESSION['idfilial_usuario'];
      
      
              $registro75_entrada = $sintegra->make_list_entrada_75($_POST, 0, 99999999999, $filtro);
      
              //criacao dos vetores com as informações dos registros do tipo 90 - Totalização do Arquivo
      
              $registro90 = array();
          
              $registro90['tipo'] = "90";
              $registro90['cnpj'] = $sintegra->formataCamposSintegra($sintegra->FormataCNPJParaSintegra($info_filial['cnpj_filial']), "N", 14);
              $registro90['inscricao_estadual'] = $sintegra->formataCamposSintegra($info_filial['inscricao_estadual_filial'], "X", 14);
              if(count($registro50_entrada) + count($registro50_saida) > 0){
                $registro90['50'] = '50';
                $registro90['qtd_50'] = $sintegra->formataCamposSintegra((count($registro50_entrada) + count($registro50_saida)),"N" , 8);
                }
              if(count($registro54_entrada) + count($registro54_saida) > 0){
                $registro90['54'] = '54';
                $registro90['qtd_54'] = $sintegra->formataCamposSintegra((count($registro54_entrada) + count($registro54_saida)),"N" , 8);
              }
              if(count($registro75_entrada) + count($registro75_saida) > 0){
                $registro90['75'] = '75';
                $registro90['qtd_75'] = $sintegra->formataCamposSintegra((count($registro75_saida) + count($registro75_entrada)),"N" , 8);
              }
              
              $registro90['qtd_90'] = 1;
      
      
              // Criação do arquivo do sintegra.
                    
              $fp = fopen("../common/arq/sintegra.txt", "w+");
      
              // Montagem do registro 10
              $registro_sintegra['10'] = "" . $registro10['tipo'] . $registro10['cnpj'] . $registro10['inscricao_estadual'] . $registro10['razao_social'] . $registro10['municipio'] . $registro10['estado'] . $registro10['fax'] . $registro10['data_inicio'] . $registro10['data_termino'] . $registro10['identificacao'] . $registro10['operacoes'] . $registro10['finalidade'] . "\r\n";
        
              fwrite($fp , $registro_sintegra['10'] );
              
              //Montagem do registro 11
              $registro_sintegra['11'] = $registro11['tipo'].$registro11['logradouro'].$registro11['numero'].$registro11['complemento'].$registro11['bairro'] . $registro11['cep'] . $registro11['nome_contato'] . $registro11['telefone']. "\r\n";
      
              fwrite($fp , $registro_sintegra['11'] );        
      
              //Montagem dos registros 50
      
              for($i=0; $i< count($registro50_saida); $i++)
                {
                  $registro_sintegra["50s$i"] = $registro50_saida[$i]['tipo'] . $registro50_saida[$i]['cnpj_cpf'] . $registro50_saida[$i]['inscricao_estadual'] . $registro50_saida[$i]['data_emissao'] . $registro50_saida[$i]['UF'] . $registro50_saida[$i]['modelo'] . $registro50_saida[$i]['serie'] . $registro50_saida[$i]['numero'] . $registro50_saida[$i]['CFOP'] . $registro50_saida[$i]['emitente'] . $registro50_saida[$i]['valor_total'] . $registro50_saida[$i]['base_calc_icms'] . $registro50_saida[$i]['valor_icms'] . $registro50_saida[$i]['isentas'] . $registro50_saida[$i]['outras'] . $registro50_saida[$i]['aliquota_icms'] . $registro50_saida[$i]['situacao']. "\r\n";         
                  fwrite($fp , $registro_sintegra["50s$i"] ); 
                }
      
              for($i=0; $i< count($registro50_entrada); $i++)
                {
                  $registro_sintegra["50e$i"] = $registro50_entrada[$i]['tipo'] . $registro50_entrada[$i]['cnpj_cpf'] . $registro50_entrada[$i]['inscricao_estadual'] . $registro50_entrada[$i]['data_emissao'] . $registro50_entrada[$i]['UF'] . $registro50_entrada[$i]['modelo'] . $registro50_entrada[$i]['serie'] . $registro50_entrada[$i]['numero'] . $registro50_entrada[$i]['CFOP'] . $registro50_entrada[$i]['emitente'] . $registro50_entrada[$i]['valor_total'] . $registro50_entrada[$i]['base_calc_icms'] . $registro50_entrada[$i]['valor_icms'] . $registro50_entrada[$i]['aliquota_icms'] . $registro50_entrada[$i]['situacao']. "\r\n";         
                  fwrite($fp , $registro_sintegra["50e$i"] ); 
                }
      
              //Montagem dos registros 54
      
              for($i=0; $i< count($registro54_saida); $i++)
                {
                  $registro_sintegra["54s$i"] = $registro54_saida[$i]['tipo'] . $registro54_saida[$i]['cnpj_cpf'] . $registro54_saida[$i]['modelo'] . $registro54_saida[$i]['serie'] . $registro54_saida[$i]['numero'] . $registro54_saida[$i]['CFOP'] .  $registro54_saida[$i]['cst'] . $registro54_saida[$i]['ordem'] . $registro54_saida[$i]['codigo_produto'] . $registro54_saida[$i]['quantidade'] . $registro54_saida[$i]['valor_bruto'] . $registro54_saida[$i]['valor_desconto'] . $registro54_saida[$i]['base_calculo_icms'] . $registro54_saida[$i]['base_calculo_icms_tributaria'] . $registro54_saida[$i]['ipi'] . $registro54_saida[$i]['aliquota_icms']. "\r\n";         
                  fwrite($fp , $registro_sintegra["54s$i"] ); 
                }
      
              for($i=0; $i< count($registro54_entrada); $i++)
                {
                  $registro_sintegra["54e$i"] = $registro54_entrada[$i]['tipo'] . $registro54_entrada[$i]['cnpj_cpf'] . $registro54_entrada[$i]['modelo'] . $registro54_entrada[$i]['serie'] . $registro54_entrada[$i]['numero'] . $registro54_entrada[$i]['CFOP'] .  $registro54_entrada[$i]['cst'] . $registro54_entrada[$i]['ordem'] . $registro54_entrada[$i]['codigo_produto'] . $registro54_entrada[$i]['quantidade'] . $registro54_entrada[$i]['valor_bruto'] . $registro54_entrada[$i]['valor_desconto'] . $registro54_entrada[$i]['base_calculo_icms'] . $registro54_entrada[$i]['base_calculo_icms_tributaria'] . $registro54_entrada[$i]['ipi'] . $registro54_entrada[$i]['aliquota_icms']. "\r\n";         
                  fwrite($fp , $registro_sintegra["54e$i"] ); 
                }
              
              //Montagem dos registros 60
            
              $registro90['qtd_60'] = 0;
            
              for($a=1;$a<=$_POST['total_arquivo']+1;$a++)
              {
                $i=0;
                while(isset($registro_ECF[$a]["60_$i"]))
                {
                  fwrite($fp,$registro_ECF[$a]["60_$i"]);
                  $i++;
                  $registro90['qtd_60'] = $registro90['qtd_60'] + 1; 
                }
              }
             
              //Montagem dos registros 75
              for($a=1;$a<=$_POST['total_arquivo']+1;$a++)
              {
                $i=0;
                while(isset($registro_ECF[$a]["75_$i"]))
                {
                  $retorno_75["$a$i"] = $registro_ECF[$a]["75_$i"];
                  $i++;
                }
              }

              for($i=0; $i< count($registro75_saida); $i++)
                {
                  $registro_sintegra75["75s$i"] = $registro75_saida[$i]['tipo'] . $registro75_saida[$i]['data_inicial'] . $registro75_saida[$i]['data_final'] . $registro75_saida[$i]['codigo_produto'] . $registro75_saida[$i]['cncm'] . $registro75_saida[$i]['descricao'] . $registro75_saida[$i]['unidade_venda'] . $registro75_saida[$i]['aliquota_ipi'] . $registro75_saida[$i]['aliquota_icms'] . $registro75_saida[$i]['reducao_base_calculo_icms'] . $registro75_saida[$i]['base_calculo_icms_subs_tributaria'] . "\r\n";
                 
                }
      
              for($i=0; $i< count($registro75_entrada); $i++)
                {
                  $registro_sintegra75["75e$i"] = $registro75_entrada[$i]['tipo'] . $registro75_entrada[$i]['data_inicial'] . $registro75_entrada[$i]['data_final'] . $registro75_entrada[$i]['codigo_produto'] . $registro75_entrada[$i]['cncm'] . $registro75_entrada[$i]['descricao'] . $registro75_entrada[$i]['unidade_venda'] . $registro75_entrada[$i]['aliquota_ipi'] . $registro75_entrada[$i]['aliquota_icms'] . $registro75_entrada[$i]['reducao_base_calculo_icms'] . $registro75_entrada[$i]['base_calculo_icms_subs_tributaria'] . "\r\n";

                }
                      
              $vetor_75_final = $sintegra->comparaRegistro75($registro_sintegra75, $retorno_75);
              
              foreach ($vetor_75_final as $vetor)
              {
                fwrite($fp, $vetor);

              }
              
              //montagem da quantidade de registros no arquivo
              
              $registro90['qtd_total'] = $sintegra->formataCamposSintegra((3 + $registro90['qtd_50'] + $registro90['qtd_54'] + $registro90['qtd_60'] + count($vetor_75_final)),"N" , 8);
              
              $registro90['qtd_75'] = $sintegra->formataCamposSintegra(count($vetor_75_final),"N",8);
              
              $registro90['qtd_60'] = $sintegra->formataCamposSintegra($registro90['qtd_60'],"N",8);
              
              $registro90['60'] = "60";
              
              
              

              $registro_sintegra['90'] = $registro90['tipo'] . $registro90['cnpj'] . $registro90['inscricao_estadual'] . $registro90['50'] . $registro90['qtd_50'] . $registro90['54'] . $registro90['qtd_54'] . $registro90['60'] . $registro90['qtd_60'] . $registro90['75'] . $registro90['qtd_75'] . "99". $registro90['qtd_total'] ;
      
              while(strlen($registro_sintegra['90']) <= 124) 
              { 
                $registro_sintegra['90'] = $registro_sintegra['90'] . " ";
              }
      
              $registro_sintegra['90'] = $registro_sintegra['90'] . $registro90['qtd_90'] . "\r\n";
              
              fwrite($fp , $registro_sintegra['90'] ); 

            
              fseek($fp,0);

              $nome = "sintegra.txt";
              header("Content-type: application/force-download");
              header("Content-Disposition: attachment; filename=$nome");
              while(!feof($fp))
                  echo fgetc($fp);
              fclose($fp);
              exit(0);
              

              break;
      
              

           }
        }

			}
		}
	}

  $ano['1'] = date(Y);
  $ano['2'] = date(Y)-1;
  $ano['3'] = date(Y)-2;
  $ano['4'] = date(Y)-3;
  
  $smarty->assign("ano", $ano);
  
  
  $smarty->assign("form", $form);
  $smarty->assign("flags", $flags);


  $smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));

  $list_permissao = $auth->check_priv($conf['priv']);
  $smarty->assign("list_permissao",$list_permissao);
  
  $smarty->display("adm_sintegra.tpl");


?>
