<?php

  //inclusão de bibliotecas
  require_once("../common/lib/conf.inc.php");
  require_once("../common/lib/db.inc.php");
  require_once("../common/lib/auth.inc.php");
  require_once("../common/lib/form.inc.php");
  require_once("../common/lib/rotinas.inc.php");
  require_once("../common/lib/Smarty/Smarty.class.php");
  require_once("../common/lib/ziplib/ziplib.php");
  
  require_once("../entidades/parametros.php");
  

  // configurações anotionais
  $conf['area'] = "Backup da Base de Dados"; // área

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

        //Incializa classe para validação de formulário
        $form = new form();
        
		//Inicializa a classe de banco de dados
        $db = new db();
        
		//Inicializa a classe de parâmetros e compactação zip
		$parametros = new parametros();
		$zipfile = new Ziplib;
        
		$list = $auth->check_priv($conf['priv']);

		$aux = $flags['action'];
		if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}
				
				

        switch($flags['action']) {

				
	          case "listar":
	          	
					
	    			if($_POST['for_chk']) {
					
						//Definição de nomes
						$caminho_mysqldump = $parametros->getParam('caminho_mysqldump'); 				//Caminho completo de acesso ao mysqldump
						$nome_backup_bd = $parametros->getParam('nome_backup_bd');						//Nome padrão do arquivo
						$nome_arquivo = sprintf("%s%s.sql", $nome_backup_bd, date("Y-m-d_H-i-s"));		//Insere no nome a data/hora de geração do arquivo + extensão
						$caminho_destino = $conf['path'] . '\common\arq\db_backup\\'.$nome_arquivo;		//Caminho completo de acesso ao arquivo que será gerado
											
						//Faz o comando de backup no mysqldump
						$retorno = $db->backup($caminho_mysqldump, $caminho_destino);
						
						
						//Verifica se o arquivo foi gerado corretamente
						if($retorno != 0){ 
							$err[] = "Ocorreu uma falha ao tentar realizar o backup do Banco de Dados. Por favor, entre em contato com os autores.";
						}
						else{
																		
							//Faz a leitura do arquivo para compactar no formato ZIP
							$fp = fopen($caminho_destino, "r");
							$texto = NULL;
							while(!feof($fp)) $texto .= fgetc($fp);
		              		fclose($fp);
		
							//adiciona o arquivo no pacote ZIP
							$zipfile->zl_add_file($texto, $nome_arquivo ,'g9');
							
							//Prepara as informações no header para o usuário fazer o download
							header('Content-type: application/zip');
							header('Content-Disposition: attachment; filename='.$nome_backup_bd.'.zip');
							echo $zipfile->zl_pack();//Fecha o arquivo e envia para o navegador
						}
						
							
						
						
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

	$smarty->display("adm_backup.tpl");

?>

