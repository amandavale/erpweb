<?php

//inclusão de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/rotinas.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");
require_once("../common/lib/xajax/xajax.inc.php");

require_once("../entidades/funcionario.php");
require_once("../entidades/filial_funcionario.php");
require_once("../entidades/filial.php");
require_once("../entidades/cargo.php"); 
require_once("../entidades/endereco.php"); 
require_once("../entidades/bairro.php");
require_once("../entidades/cidade.php");
require_once("../entidades/estado.php");
require_once("../entidades/cargo_programa.php");
require_once("../entidades/funcionario_programa.php");
require_once("../entidades/funcionario_cliente.php");
require_once("../entidades/conta_funcionario.php");

require_once("funcionario_ajax.php");
require_once("cliente_ajax.php");
require_once("conta_funcionario_ajax.php");



// configurações anotionais
$conf['area'] = "Funcionário"; // área


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

// cria o objeto xajax
$xajax = new xajax();


// registra todas as funções que serão usadas
$xajax->registerFunction("Verifica_Campos_Funcionario_AJAX");
$xajax->registerFunction("Insere_Cliente_AJAX");
$xajax->registerFunction("Verifica_Cliente_Existe_AJAX");
$xajax->registerFunction("Deleta_Cliente_AJAX");
$xajax->registerFunction("Seleciona_Cliente_AJAX");

$xajax->registerFunction("Insere_Conta_Bancaria_AJAX");
$xajax->registerFunction("Deleta_Conta_Bancaria_AJAX");
$xajax->registerFunction("Seleciona_Conta_Bancaria_AJAX");


// processa as funções
$xajax->processRequests();

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
			$funcionario = new funcionario();
			
			$cargo = new cargo(); 
			$endereco = new endereco(); 
			$bairro = new bairro();
			$cidade = new cidade();
			$estado = new estado();
			$filial = new filial();
			$cargo_programa = new cargo_programa();
			$filial_funcionario = new filial_funcionario();
			$funcionario_programa = new funcionario_programa();
			$funcionario_cliente = new funcionario_cliente();
			$conta_funcionario = new conta_funcionario();
			
			
			// inicializa banco de dados
			$db = new db();
			
			//incializa classe para validação de formulário
			$form = new form();
			
			$list = $auth->check_priv($conf['priv']);
			$aux = $flags['action'];
			if($list[$aux]!='1' && $_SESSION['usr_cargo']!="") {$err = "Voc&ecirc; est&aacute; tentando acessar uma &aacute;rea restrita!<br /><a href=\"".$conf['addr']."/admin/index.php\">Clique aqui</a> para voltar &agrave; p&aacute;gina inicial."; $flags['action'] = "";}
			
			
			switch($flags['action']) {
			
			// busca genérica
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
						
						//obtém qual página da listagem deseja exibir
						$pg = intval(trim($_GET['pg']));
						
						//se não foi passada a página como parâmetro, faz página default igual à página 0
						if(!$pg) $pg = 0;
						
						//lista os registros
						$list = $funcionario->Busca_Generica($pg, $flags['rpp'], $flags['busca'], "", "ac=busca_generica&busca=".$flags['busca']."&rpp=".$flags['rpp']);
						
						//pega os erros
						$err = $funcionario->err;
						
						//passa a listagem para o template
						$smarty->assign("list", $list);
						
					}
					
					if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];
					
				break;
					
					
					// busca parametrizada
				case "busca_parametrizada":
					
					
					if(isset($_GET['sucesso']) && $_GET['sucesso'] == 'alterar' ) $flags['sucesso'] = $conf['alterar'];
					if ($flags['rpp'] == "") $flags['rpp'] = $conf['rppg'];
					$parametros_get = NULL;
					if ( ($_POST['for_chk']) || ($_GET['rpp'] != "") ) {
						
						$flags['fez_busca'] = 1;
						
						
						//Nome dos campos de busca do formulário
						$campos = array('cargo','funcionario','cpf_funcionario', 'data_nascimento', 'situacao_funcionario', 'infoFiliais',
							'data_admissao_de', 'data_admissao_ate', 'data_demissao_de', 'data_demissao_ate', 'rpp');
						
						
						//Verifica se deve pegar os dados via POST ou GET
						if ($_POST['for_chk'] && $_GET['target'] != 'full') {
							foreach($campos as $campo) $flags[$campo] = $_POST[$campo];
						}
						else {

                                                    foreach($campos as $campo) $flags[$campo] = $_GET[$campo];
						}
						
						//Monta o filtro de busca com base nos parâmetros passados
						$filtro_where = "";
						if ($flags['cargo'] != "") $filtro_where .= " UPPER(CAR.nome_cargo) LIKE UPPER('%" . $flags['cargo'] . "%') AND ";
						if ($flags['funcionario'] != "") $filtro_where .= " ( (UPPER(FUN.nome_funcionario) LIKE UPPER('%" . $flags['funcionario'] . "%'))) AND ";
						if ($flags['cpf_funcionario'] != "") $filtro_where .= " ( (UPPER(FUN.cpf_funcionario) LIKE UPPER('%" . $flags['cpf_funcionario'] . "%')) ) AND ";
						
						$data_temp = $form->FormataDataParaInserir($flags['data_nascimento']);
						if ($flags['data_nascimento'] != "") $filtro_where .= " UPPER(FUN.data_nascimento_funcionario) LIKE UPPER('%" . $data_temp . "%') AND ";
						
						
						if(!empty($flags['data_admissao_de']) && !empty($flags['data_admissao_ate']) ){
							$data_admissao = array('de' => $form->FormataDataParaInserir($flags['data_admissao_de']),'ate'=> $form->FormataDataParaInserir($flags['data_admissao_ate']));
							$filtro_where .= " (FUN.data_admissao_funcionario BETWEEN '".$data_admissao['de']."' AND '".$data_admissao['ate']."') AND ";
						}
						
						
						if(!empty($flags['data_demissao_de']) && !empty($flags['data_demissao_ate']) ){
							$data_demissao = array('de' => $form->FormataDataParaInserir($flags['data_demissao_de']),'ate'=> $form->FormataDataParaInserir($flags['data_demissao_ate']));
							$filtro_where .= " (FUN.data_demissao_funcionario BETWEEN '".$data_demissao['de']."' AND '".$data_demissao['ate']."') AND ";
						}
						//elseif ($flags['listar_exfuncionario'] == "E") $filtro_where .= " (FUN.data_demissao_funcionario IS NOT NULL AND FUN.data_demissao_funcionario <> '0000-00-00' ) AND "; //Não lista ex-funcionario
						//else
						
						
						
						if ($flags['situacao_funcionario'] == "ativo") 
							$filtro_where .= " ( (FUN.data_demissao_funcionario IS NULL OR FUN.data_demissao_funcionario = '0000-00-00' ) AND 
								(FUN.data_afastamento IS NULL OR FUN.data_afastamento = '0000-00-00' ) ) AND "; 		
						elseif ($flags['situacao_funcionario'] == "inativo")
						$filtro_where .= " (FUN.data_demissao_funcionario IS NOT NULL AND FUN.data_demissao_funcionario != '0000-00-00' ) AND "; 		
						elseif ($flags['situacao_funcionario'] == "afastado")
						$filtro_where .= " ( (FUN.data_afastamento IS NOT NULL AND FUN.data_afastamento != '0000-00-00' ) AND 
							(FUN.data_demissao_funcionario IS NULL OR FUN.data_demissao_funcionario = '0000-00-00' ) ) AND "; 		
						
						
						
						if ($flags['infoFiliais'] != "") $filtro_where .= " FUNFILIAL.idfilial IN(".implode(',',$flags['infoFiliais']).") AND ";
						
						
						$filtro_where = substr($filtro_where,0,strlen($filtro_where)-4);
						
					
						if ($_GET['target'] == "full"){
							$flags['rpp'] = 9999999; // Relatório
						}
						
						//obtém qual página da listagem deseja exibir
						$pg = intval(trim($_GET['pg']));
						
						//se não foi passada a página como parâmetro, faz página default igual à página 0
						if(!$pg) $pg = 0;
						
						
						//Monta a URL com os parâmetros GET para a paginação
						foreach($campos as $campo ){
                                                    
                                                    
                                                    if($campo == 'infoFiliais'){
                                                        foreach($flags[$campo] as $idfiliais) $parametros_get .= "&infoFiliais[]=$idfiliais" ;
                                                    }
                                                    else{
                                                        $parametros_get .= "&$campo=".$flags[$campo] ;
                                                    }
                                                    
                                                }
						
						//lista os registros
						$list = $funcionario->Busca_Parametrizada($pg, $flags['rpp'], $filtro_where, "", "ac=busca_parametrizada$parametros_get&rpp=".$flags['rpp']);
						
						
					}
					else{
						
						$filtro_where .= " ( (FUN.data_demissao_funcionario IS NULL OR FUN.data_demissao_funcionario = '0000-00-00' ) AND 
										     (FUN.data_afastamento IS NULL OR FUN.data_afastamento = '0000-00-00' ) ) AND FUNFILIAL.idfilial = ".$_SESSION['idfilial_usuario'];
										     
						if(isset($_GET['idfuncionario'])) $filtro_where .= ' AND FUNFILIAL.idfuncionario='.(int)$_GET['idfuncionario'];										     
								
						$list = $funcionario->Busca_Parametrizada(0, $flags['rpp'], $filtro_where, "", "ac=busca_parametrizada&rpp=".$flags['rpp']);
					}
				
						$list_filial = $filial->make_list_select();
						$smarty->assign("list_filial", $list_filial);
						
						//pega os erros
						$err = $funcionario->err;
						
						//passa a listagem para o template
						$smarty->assign("list", $list);
                                                $smarty->assign("parametros_get", $parametros_get);			
						
					
					break;
					
					
					
					// ação: adicionar <<<<<<<<<<
				case "adicionar":
					
					if($_POST['for_chk']) {
						
						
						$form->chk_empty($_POST['nome_funcionario'], 1, 'Nome do funcionário'); 
						$form->chk_empty($_POST['sexo_funcionario'], 1, 'Sexo do funcionário'); 
						$form->chk_empty($_POST['idcargo'], 1, 'Cargo'); 
						$form->chk_empty($_POST['identidade_funcionario'], 1, 'Nº da identidade'); 
						$form->chk_cpf($_POST['cpf_funcionario'], 1);
						$form->chk_IsDate($_POST['data_nascimento_funcionario'], "Data de nascimento");
						$form->chk_IsDate($_POST['data_admissao_funcionario'], "Data de admissão");
						
						if(!empty($_POST['data_demissao_funcionario'])) $form->chk_IsDate($_POST['data_demissao_funcionario'], "Data de demissão");
						
						if(!empty($_POST['data_afastamento'])) $form->chk_IsDate($_POST['data_afastamento'], "Data de Emissão na Empresa");
						
						// se digitou a senha ou o login, verifica
						if ( ($_POST['login_funcionario'] != "") || ($_POST['senha_funcionario'] != "") || ($_POST['re_senha_funcionario'] != "") ) {
							$form->chk_login($_POST['login_funcionario']);
							$form->chk_empty($_POST['re_senha_funcionario'], 1,"a confirmação da senha");
							$form->chk_senha($_POST['senha_funcionario'],$_POST['re_senha_funcionario']);
						}
						
						$err = $form->err;
						
						// verifica se o CPF do funcionario está duplicado
						if ($funcionario->Verifica_CPF_Duplicado($_POST['cpf_funcionario'])) $err[] = "Este CPF já existe e não pode ser duplicado!";
						
						if(count($err) == 0) {
							
							if ($_POST['senha_funcionario'] != "") $_POST['senha_funcionario'] = md5($_POST['senha_funcionario']);
							
							$_POST['observacao_funcionario'] = nl2br($_POST['observacao_funcionario']); 			
							$_POST['situacao_funcionario'] = "A";
							$_POST['data_nascimento_funcionario'] = $form->FormataDataParaInserir($_POST['data_nascimento_funcionario']); 
							$_POST['data_admissao_funcionario'] = $form->FormataDataParaInserir($_POST['data_admissao_funcionario']);
							$_POST['data_demissao_funcionario'] = $form->FormataDataParaInserir($_POST['data_demissao_funcionario']);
							$_POST['data_nascimento_conjuge_funcionario'] = $form->FormataDataParaInserir($_POST['data_nascimento_conjuge_funcionario']); 
							$_POST['telefone_funcionario'] = $form->FormataTelefoneParaInserir($_POST['telefone_funcionario_ddd'], $_POST['telefone_funcionario']); 
							$_POST['celular_funcionario'] = $form->FormataTelefoneParaInserir($_POST['celular_funcionario_ddd'], $_POST['celular_funcionario']); 
							$_POST['data_afastamento'] = $form->FormataDataParaInserir($_POST['data_afastamento']);
							
							
							
							if(!empty($_POST['salario_funcionario']) ) $_POST['salario_funcionario'] = $form->FormataMoedaParaInserir($_POST['salario_funcionario']);
							if(!empty($_POST['data_emissao'])) $_POST['data_emissao'] = $form->FormataDataParaInserir($_POST['data_emissao']);
							
							
							//Inicia a transação no banco de dados
							$db->query('BEGIN');                	
							
							
							// Grava o registro do endereço no Banco de Dados
							$_POST['idendereco_funcionario'] = $endereco->InsereEndereco($_POST, "funcionario");
							
							//grava o registro no banco de dados
							$idfuncionario = $funcionario->set($_POST);
							
							
							// grava as contas bancarias do funcionário
							$conta_funcionario->GravaContasBancariasFuncionario($_POST, $idfuncionario);
							
							$list_programa = $cargo_programa->Consulta_Programa_Cargo($_POST['idcargo']);
							
							$list_fun_prog['idfuncionario'] = $idfuncionario;
							
							if(!empty($list_programa)){
								
								$cont=0;
								while($list_programa[$cont]['index'] == $cont){
									$list_fun_prog['idprograma'] = $list_programa[$cont]['idprograma'];
									$list_fun_prog['permissao_adicionar'] = $list_programa[$cont]['permissao_adicionar'];
									$list_fun_prog['permissao_editar'] = $list_programa[$cont]['permissao_editar'];
									$list_fun_prog['permissao_excluir'] = $list_programa[$cont]['permissao_excluir'];
									$list_fun_prog['permissao_listar'] = $list_programa[$cont]['permissao_listar'];
									
									$funcionario_programa->set($list_fun_prog);											
									
									$cont++;
								}
							}
							
							//obtém os erros que ocorreram no cadastro
							$err = $funcionario->err;
							
							
							
							// busca os ids dos clientes
							$clientes = array();
							foreach ($_POST as $key => $val) {
								if (false !== strpos($key, 'codigo_cliente_')) {
									$clientes[] = $val;
								}
							}
							
							// relaciona o funcionário aos clientes
							if(!empty($clientes)){
								$funcionario_cliente->relacionaClienteAFuncionario($idfuncionario, $clientes);
								!empty($funcionario_cliente->err) ? $err[] = $funcionario_cliente->err : '';	
							}
							
							
							
							
							$filiaisUsuario = array('total_funcionarios' => 1, 'codigo_funcionario_1' => $idfuncionario);
							
							//Vincula o funcionário às filiais selecionadas
							foreach($_POST['infoFiliais'] as $idfilial){ 
								
								
								if(!$filial_funcionario->GravaFuncionario($filiaisUsuario, $idfilial,false)){
									
									
									$db->query('ROLLBACK');
									$err[] = 'Ocorreu uma falha ao tentar vincular o funcionário à filial. Por favor entre em contato com os autores';
									break;
									
								}
								
								
							}
							
							
							
							//se não ocorreram erros
							if(count($err) == 0) {
								
								$db->query('COMMIT');
								
								$flags['sucesso'] = $conf['inserir'];
								
								//limpa o $flags.action para que seja exibida a listagem
								$flags['action'] = "listar";
								
								//lista
								$list = $funcionario->make_list(0, $conf['rppg']);
								
								//pega os erros
								$err = $funcionario->err;
								
								//envia a listagem para o template
								$smarty->assign("list", $list);
								
							}
							else $db->query('ROLLBACK');
							
						}
						
					}
					
					$infoFiliais = $filial->make_list_select();
					$smarty->assign("infoFiliais",$infoFiliais);
					
					$list_cargo = $cargo->make_list_select();
					$smarty->assign("list_cargo",$list_cargo);
					
					
					break;
					
					
					//listagem dos registros
				case "listar":
					
					//obtém qual página da listagem deseja exibir
					$pg = intval(trim($_GET['pg']));
					
					//se não foi passada a página como parâmetro, faz página default igual à página 0
					if(!$pg) $pg = 0;
					
					//lista os registros
					$list = $funcionario->make_list($pg, $conf['rppg']);
					
					//pega os erros
					$err = $funcionario->err;
					
					//passa a listagem para o template
					$smarty->assign("list", $list);
					
					break;
					
					
					// ação: editar <<<<<<<<<<
				case "editar":
					
					
					$idfuncionario = (int)$_GET['idfuncionario'];
					
					if($_POST['for_chk']) {
						
						
						
						$list_filial = $filial_funcionario->selecionaFilial($_POST, $idfuncionario);
						$smarty->assign("list_filial",$list_filial);
						
						
						$info = $_POST;
						
						$info['idfuncionario'] = $_GET['idfuncionario']; 
						
						
						$form->chk_empty($_POST['litnome_funcionario'], 1, 'Nome do funcionário'); 
						$form->chk_empty($_POST['litsexo_funcionario'], 1, 'Sexo do funcionário'); 
						$form->chk_empty($_POST['numidcargo'], 1, 'Cargo'); 
						$form->chk_empty($_POST['litidentidade_funcionario'], 1, 'Nº da identidade'); 
						$form->chk_cpf($_POST['litcpf_funcionario'], 1);
						$form->chk_IsDate($_POST['litdata_nascimento_funcionario'], "Data de nascimento"); 
						$form->chk_IsDate($_POST['litdata_admissao_funcionario'], "Data de admissão");
						
						if(!empty($_POST['litdata_demissao_funcionario']))
							$form->chk_IsDate($_POST['litdata_demissao_funcionario'], "Data de demissão");
						
						if(!empty($_POST['litdata_emissao']))
							$form->chk_IsDate($_POST['litdata_emissao'], "Data de Emissão na Empresa");
						
						// se digitou a senha ou o login, verifica
						if ( ($_POST['login_funcionario_vazio'] != "") || ($_POST['senha_funcionario'] != "") || ($_POST['re_senha_funcionario'] != "") || ($_POST['senha_atual_funcionario'] != "") ) {
							
							// se digitou algo no login, entao verifica se ele existe
							if ($_POST['login_funcionario_vazio'] != "") {
								$form->chk_login($_POST['login_funcionario_vazio']);
							}
							// se o login ja existe, entao verifica se ja digitou a senha atual
							else {
								$form->chk_empty($_POST['senha_atual_funcionario'], 1,"a senha atual");
								
								// verifica se a senha atual está correta
								if ($_POST['senha_atual_funcionario'] != "") {
									$info_senha_atual = $funcionario->getById($info['idfuncionario']);
									
									if ($info_senha_atual['senha_funcionario'] != md5($_POST['senha_atual_funcionario']))
										$form->err[] = "A senha atual está errada! Redigite novamente!";
								}
								
							}	
							
							$form->chk_empty($_POST['re_senha_funcionario'], 1,"a confirmação da senha");
							$form->chk_senha($_POST['senha_funcionario'],$_POST['re_senha_funcionario']);
							
						}
						
						$err = $form->err;
						
						// verifica se o CPF do funcionario está duplicado
						if ($funcionario->Verifica_CPF_Duplicado($_POST['litcpf_funcionario'], $_GET['idfuncionario'])) $err[] = "Este CPF já existe e não pode ser duplicado!";
						
						
						if(count($err) == 0) {
							
							if ($_POST['login_funcionario_vazio'] != "") $_POST['litlogin_funcionario'] = $_POST['login_funcionario_vazio'];
							if ($_POST['senha_funcionario'] != "") $_POST['litsenha_funcionario'] = md5($_POST['senha_funcionario']);
							
							$_POST['litobservacao_funcionario'] = nl2br($_POST['litobservacao_funcionario']); 
							
							$_POST['litdata_nascimento_funcionario'] = $form->FormataDataParaInserir($_POST['litdata_nascimento_funcionario']); 
							if(!empty($_POST['litdata_admissao_funcionario'])) $_POST['litdata_admissao_funcionario'] = $form->FormataDataParaInserir($_POST['litdata_admissao_funcionario']);
							if(!empty($_POST['litdata_demissao_funcionario'])) $_POST['litdata_demissao_funcionario'] = $form->FormataDataParaInserir($_POST['litdata_demissao_funcionario']);
							if(!empty($_POST['litdata_afastamento'])) $_POST['litdata_afastamento'] = $form->FormataDataParaInserir($_POST['litdata_afastamento']); 
							
							$_POST['littelefone_funcionario'] = $form->FormataTelefoneParaInserir($_POST['telefone_funcionario_ddd'], $_POST['littelefone_funcionario']); 
							$_POST['litcelular_funcionario'] = $form->FormataTelefoneParaInserir($_POST['celular_funcionario_ddd'], $_POST['litcelular_funcionario']); 
							
							
							if( !empty($_POST['numsalario_funcionario']) ) $_POST['numsalario_funcionario'] = $form->FormataMoedaParaInserir($_POST['numsalario_funcionario']);
							
							
							
							
							
							//Prepara o array para vincular o funcionário às filiais					
							$filiaisUsuario = array('total_funcionarios' => 1, 'codigo_funcionario_1' => (int)$_GET['idfuncionario']);
							
							//Inicia a transação no banco de dados 
							$db->query('BEGIN');
							$flagFilialFunc = true; // cria uma flag para indicar se tudo continuará ok até o final da transação
							
							//Limpa as filiais do funcionário já registradas para criar novos registros
							if($filial_funcionario->DeletaFilial((int)$_GET['idfuncionario'])){	
								
								//Percorre as filiais selecionadas
								foreach($_POST['infoFiliais'] as $idfilial){ 
									
									
									if(!$filial_funcionario->GravaFuncionario($filiaisUsuario, $idfilial,false)){
										//Se não conseguir gravar todas as filiais selecionadas para o funcionário, desfaz a operação
										
										$db->query('ROLLBACK');
										$flagFilialFunc = false;
										break;
										
									}
									
									
								}
								
							}
							else{
								//Não conseguiu limpar os registros anteriores.
								$db->query('ROLLBACK');
								$flagFilialFunc = false;
							}
							
							//Se estiver tudo ok, finaliza a transação
							if($flagFilialFunc) $db->query('COMMIT');
							
							
							
							
							
							
							// atualiza os dados do endereço
							$endereco->AtualizaEndereco($_POST['idendereco_funcionario'], $_POST, "funcionario");
							
							$info_funcionario = $funcionario->getbyID($_GET['idfuncionario']);
							
							$funcionario->update($_GET['idfuncionario'], $_POST);
							
							// grava as contas bancarias da filial
							$conta_funcionario->GravaContasBancariasFuncionario($_POST, (int)$_GET['idfuncionario']);
							
							
							// busca os ids dos clientes
							$clientes = array();
							foreach ($_POST as $key => $val) {
								if (false !== strpos($key, 'codigo_cliente_')) {
									$clientes[] = $val;
								}
							}
							
							
							
							// relaciona o funcionário aos clientes
							if(!empty($clientes)){
								$funcionario_cliente->relacionaClienteAFuncionario($idfuncionario, $clientes);
								!empty($funcionario_cliente->err) ? $err[] = $funcionario_cliente->err : '';	
							}
							
							
							
							if($info_funcionario['idcargo'] != $_POST['numidcargo']){								
								
								$list_programa = $cargo_programa->Consulta_Programa_Cargo($_POST['numidcargo']);
								$funcionario_programa->delete_programa($_GET['idfuncionario']);
								
								$list_fun_prog['idfuncionario'] = $_GET['idfuncionario'];
								
								$cont=0;
								while($list_programa[$cont]['index'] == $cont)
								{
									$list_fun_prog['idprograma']=$list_programa[$cont]['idprograma'];
									$list_fun_prog['permissao_adicionar']=$list_programa[$cont]['permissao_adicionar'];
									$list_fun_prog['permissao_editar']=$list_programa[$cont]['permissao_editar'];
									$list_fun_prog['permissao_excluir']=$list_programa[$cont]['permissao_excluir'];
									$list_fun_prog['permissao_listar']=$list_programa[$cont]['permissao_listar'];
									
									
									$funcionario_programa->set($list_fun_prog);											
									
									$cont++;
								}
								
							}
							
							//obtém erros
							$err = $funcionario->err;
							
							//se não ocorreram erros
							if(count($err) == 0) {
								
								if ($_POST['litsituacao_funcionario'] =='I') {
									$filial_funcionario->delete_funcionario_filial($_GET['idfuncionario']);
								}
								
								header('location:'.$conf['addr'].'/admin/funcionario.php?ac=busca_parametrizada&sucesso=alterar&idfuncionario='.$idfuncionario);
								
								
							}
							
						}
						
					}
					else {
						
						//busca detalhes
						$info = $funcionario->getById($idfuncionario);
						
						//tratamento das informações para fazer o UPDATE
						$info['litnome_funcionario'] = $info['nome_funcionario']; 
						$info['litsexo_funcionario'] = $info['sexo_funcionario']; 
						$info['numidcargo'] = $info['idcargo']; 
						$info['litlogin_funcionario'] = $info['login_funcionario']; 
						$info['litsenha_funcionario'] = $info['senha_funcionario']; 
						$info['litidentidade_funcionario'] = $info['identidade_funcionario']; 
						$info['litcpf_funcionario'] = $info['cpf_funcionario']; 
						$info['litcarteira_trabalho_funcionario'] = $info['carteira_trabalho_funcionario']; 
						$info['litdata_nascimento_funcionario'] = $info['data_nascimento_funcionario']; 
						$info['litdata_admissao_funcionario'] = $info['data_admissao_funcionario'];
						$info['litdata_demissao_funcionario'] = $info['litdata_afastamento'];
						$info['numidendereco_funcionario'] = $info['idendereco_funcionario']; 
						$info['littelefone_funcionario'] = $info['telefone_funcionario']; 
						$info['litcelular_funcionario'] = $info['celular_funcionario']; 
						$info['litemail_funcionario'] = $info['email_funcionario']; 
						$info['litnome_conjuge_funcionario'] = $info['nome_conjuge_funcionario']; 
						$info['litdata_nascimento_conjuge_funcionario'] = $info['data_nascimento_conjuge_funcionario']; 
						$info['litobservacao_funcionario'] = strip_tags($info['observacao_funcionario']); 
						
						// Busca os dados do endereço
						if(!empty($info['idendereco_funcionario'])) $endereco->BuscaDadosEndereco($info['idendereco_funcionario'], $info, "funcionario");
						
						
						if(!empty($info['idcargo'])) $info['dados_cargo'] = $cargo->getById($info['idcargo']);
						
						$info['dados_conta'] = $conta_funcionario->getContasFuncionario($idfuncionario); 
						
						if(!empty($info['funcionario_idbairro'])) $info_bairro = $bairro->getById($info['funcionario_idbairro']);
						$info['funcionario_idbairro_Nome'] = $info_bairro['nome_bairro'];
						$info['funcionario_idbairro_NomeTemp'] = $info_bairro['nome_bairro'];
						
						$info['funcionario_idestado_Nome'] = $info_bairro['nome_estado'];
						$info['funcionario_idestado_NomeTemp'] = $info_bairro['nome_estado'];
						
						$info['funcionario_idcidade_Nome'] = $info_bairro['nome_cidade'];
						$info['funcionario_idcidade_NomeTemp'] = $info_bairro['nome_cidade'];
						
						
						if ( strlen($info['telefone_funcionario']) == 10 ) { 
							$info['littelefone_funcionario'] = substr($info['telefone_funcionario'],2,4) . "-" . substr($info['telefone_funcionario'],6); 
							$info['telefone_funcionario_ddd'] = substr($info['telefone_funcionario'],0,2); 
						} 
						
						if ( strlen($info['celular_funcionario']) == 10 ) { 
							$info['litcelular_funcionario'] = substr($info['celular_funcionario'],2,4) . "-" . substr($info['celular_funcionario'],6); 
							$info['celular_funcionario_ddd'] = substr($info['celular_funcionario'],0,2); 
						} 
						
						$list_filial = $filial_funcionario->SelecionaFilial("",$idfuncionario);
						$smarty->assign("list_filial",$list_filial);
						
						
						
						
						//obtém os erros
						$err = $funcionario->err;
					}
					
					$list_cargo = $cargo->make_list_select();
					$smarty->assign("list_cargo",$list_cargo);
					
					$infoFiliais = $filial->make_list_select(null," ORDER BY nome_filial ASC", $idfuncionario);
					$smarty->assign("infoFiliais",$infoFiliais);
					
					$infoFilialUsuario  = $filial->BuscaDadosFilial($_SESSION['idfilial_usuario']);
					//foreach($infoFilialUsuario as $k => $v)$infoFilialUsuario[$k] = ucwords(strtolower($v));
					foreach($infoFilialUsuario as $k => $v)$infoFilialUsuario[$k] = strtoupper($v);
					
					$smarty->assign("infoFilialUsuario",$infoFilialUsuario);
					
					
					//passa os dados para o template
					$smarty->assign("info", $info);
					
				break;
					
					
					
					// deleta um registro do sistema
				case "excluir":
					
					//verifica se foi pedido a deleção
					if($_POST['for_chk']){
						
						// busca o codigo do endereço
						$info = $funcionario->getById($_GET['idfuncionario']);
						
						// deleta o registro
						$funcionario->delete($_GET['idfuncionario']);
						
						// deleta o endereço do banco de dados
						$endereco->delete($info['idendereco_funcionario']);
						
						
						//obtém erros
						$err = $funcionario->err;
						
						//se não ocorreram erros
						if(count($err) == 0){
							$flags['sucesso'] = $conf['excluir'];
							
							
							
						}
						
						//limpa o $flags.action para que seja exibida a listagem
						$flags['action'] = "listar";
						
						//lista registros
						$list = $funcionario->make_list(0, $conf['rppg']);
						
						//pega os erros
						$err = $funcionario->err;
						
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

// Forma Array de intruções de preenchimento
$intrucoes_preenchimento = array();
if ($flags['action'] == "adicionar" || $flags['action'] == "editar" ) {
	$intrucoes_preenchimento[] = "Os campos em <span class=req>vermelho</span> s&atilde;o obrigat&oacute;rios.";
}
else if ($flags['action'] == "busca_generica" || $flags['action'] == "busca_parametrizada") {
	$intrucoes_preenchimento[] = "Preencha os campos para realizar a busca.";
}

// Formata a mensagem para ser exibida
$flags['intrucoes_preenchimento'] = $form->FormataMensagemAjuda($intrucoes_preenchimento);


$smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));

$smarty->assign("form", $form);
$smarty->assign("flags", $flags);

$list_permissao = $auth->check_priv($conf['priv']);
$smarty->assign("list_permissao",$list_permissao);


if (isset($_GET['target']) && $_GET['target'] == "full")  $smarty->display("adm_relatorio_funcionario.tpl");
elseif ( isset($_GET['ficha']) && $_GET['ficha'] == "1")  $smarty->display("adm_funcionario_ficha.tpl");
else $smarty->display("adm_funcionario.tpl");

?>

