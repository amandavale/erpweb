<?php

//inclusão de bibliotecas
require_once("../common/lib/conf.inc.php");
require_once("../common/lib/db.inc.php");
require_once("../common/lib/auth.inc.php");
require_once("../common/lib/form.inc.php");
require_once("../common/lib/rotinas.inc.php");
require_once("../common/lib/Smarty/Smarty.class.php");
require_once("../common/lib/xajax/xajax.inc.php");

require_once("../entidades/filial.php");
require_once("../entidades/parametros.php");
require_once("../entidades/ordem_servico.php");
require_once("../entidades/material_ordem_servico.php");
require_once("../entidades/tipo_servico.php");
require_once("../entidades/transicao_status.php");
require_once("../entidades/status_os.php");
require_once("../entidades/programacao_status.php");
require_once("../entidades/status_os_programacao.php");
require_once("../entidades/secao.php");
require_once("../entidades/unidade_venda.php");
require_once("../entidades/ramo_atividade.php");
require_once("../entidades/movimento.php");

require_once("ordem_servico_ajax.php");
require_once("fornecedor_ajax.php");
require_once("produto_ajax.php");
require_once("cliente_ajax.php");
require_once("bairro_ajax.php");

// configurações anotionais
$conf['area'] = "Ordem de Serviço"; // área

//configuração de estilo
$conf['style'] = "full";

// inicializa templating
$smarty = new Smarty;

// configura diretórios
$smarty->template_dir = "../common/tpl";
$smarty->compile_dir =   "../common/tpl_c";

// seta configurações
$smarty->assign("conf", $conf);

// cria o objeto xajax
$xajax = new xajax();

$xajax->registerFunction("Set_Campo_Status_AJAX");
$xajax->registerFunction("Calcula_Total_OS_AJAX");
$xajax->registerFunction("Verifica_Campos_OS_AJAX");
$xajax->registerFunction("Insere_Material_OS_AJAX");
$xajax->registerFunction("Remove_Material_OS_AJAX");
$xajax->registerFunction("Set_Campo_Movmentacao_AJAX");
$xajax->registerFunction("Set_Campo_Programacao_AJAX");
$xajax->registerFunction("Cadastro_Rapido_Bairro_AJAX");
$xajax->registerFunction("Cadastro_Rapido_Produto_AJAX");
$xajax->registerFunction("Cadastro_Rapido_Fornecedor_AJAX");
$xajax->registerFunction("Cadastro_Rapido_ClienteFisico_AJAX");
$xajax->registerFunction("Cadastro_Rapido_ClienteJuridico_AJAX");
$xajax->registerFunction("Cadastro_Rapido_ClienteCondominio_AJAX");


// processa as funções
$xajax->processRequests();


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
			$flags['okay'] = false;
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
			$material_ordem_servico  = new material_ordem_servico();
			$parametros       = new parametros();
			$tipo_servico     = new tipo_servico();
			$ordem_servico    = new ordem_servico();
			$transicao_status = new transicao_status();
			$status_os_programacao = new status_os_programacao();
			$programacao_status    = new programacao_status();
			$status_os = new status_os();
			$filial    = new filial();
			$secao	   = new secao();
			$unidade_venda  = new unidade_venda();
			$ramo_atividade = new ramo_atividade();
			$movimento = new movimento();
			
			
			// inicializa banco de dados
			$db = new db();

			//incializa classe para validação de formulário
			$form = new form();



			switch($flags['action']) {

				// ação: adicionar <<<<<<<<<<
				case "adicionar":

					
					if($_POST['for_chk']) {
						 
						$form->chk_empty($_POST['descricao_ordem'], 1, 'Descrição');
						$form->chk_empty($_POST['idcliente'], 1, 'Cliente');
						$form->chk_empty($_POST['idsolicitante'], 1, 'Solicitante');
						 	

						$err = $form->err;

						if(count($err) == 0) {
					
							$_POST['observacao_ordem']    = nl2br($db->escape($_POST['observacao_ordem']));
							$_POST['endereco_cliente']    = nl2br($db->escape($_POST['endereco_cliente']));
							$_POST['endereco_fornecedor'] = nl2br($db->escape($_POST['endereco_fornecedor']));
							$_POST['previsao_servico']    = $form->FormataDataParaInserir($_POST['previsao_servico']);
	       					$_POST['idfornecedor']		  = empty($_POST['idfornecedor']) ? 'NULL' : (int)$_POST['idfornecedor'];
	       					
							
							//Inicia a transação no banco
							$db->query('BEGIN');
							
							//grava o registro no banco de dados
							$idordem_servico = $ordem_servico->set($_POST);

							//obtém os erros que ocorreram no cadastro
							$err = $ordem_servico->err;

							//se não ocorreram erros
							if(count($err) == 0) {
								
								//Grava os produtos
								foreach($_POST['material'] as $idproduto => $material){

				   
									$info_material = array(
														'idordem_servico' => $idordem_servico,
														'idproduto'       => (int)$idproduto,
														'idfornecedor'    => (int)$material['idfornecedor'],
														'numero_nf'    	  => $db->escape($material['numero_nf']),
														'qtd_produto'     => $form->FormataMoedaParaInserir($material['qtd_produto']),
														'valor_unitario'  => $form->FormataMoedaParaInserir($material['valor_unitario'])
													);
							
									$material_ordem_servico->set($info_material);
									/*
									 * TODO: Entender porque essa função retorna FALSE mesmo 
									 *        quando inserção é feita com sucesso
									if(!$material_ordem_servico->set($info_material)){
										//Em caso de falha, desfaz a operação
										$db->query('ROLLBACK');
										$err = $material_ordem_servico->err;
										break;
									}
									
									*/	
								}
								
								
								//Marca o Status inicial da OS
								$idstatus_inicial = $parametros->getParam('idstatus_inicial_os');
								$transicao_status->set(
										array(
												'idstatus'			   => $idstatus_inicial,
												'idfuncionario'		   => $_SESSION['usr_cod'],
												'idordem_servico'	   => $idordem_servico,
												'data_hora_transicao'  => date('Y-m-d H:i:s'),
												'observacao_transicao' => $_POST['observacao_ordem']
										)
								);
								
								if(count($transicao_status->err)){
									$err = $transicao_status->err;
									$db->query('ROLLBACK');
								}
								
								
								if(count($err) == 0){
								
									$db->query('COMMIT');
									
									$flags['sucesso'] = $conf['inserir'];
	
									//limpa o $flags.action para que seja exibida a listagem
									$flags['action'] = "listar";
	
									//lista
									$list = $ordem_servico->make_list(0, $conf['rppg']);
	
									//pega os erros
									$err = $ordem_servico->err;
	
									//envia a listagem para o template
									$smarty->assign("list", $list);
									
									//Abre a tela de impressão da OS
									if (isset($_POST['imprimir_os'])) {
										echo '<script type="text/javascript">window.open("'.$conf['addr'].'/admin/ordem_servico.php?ac=editar&idordem_servico='.$idordem_servico.'&imprimir=1");</script>';
									}
									
								}
							
							}
							
						}

					}


					$list_tipo_servico = $tipo_servico->make_list_select();
					$smarty->assign("list_tipo_servico",$list_tipo_servico);
					
					$list_secao = $secao->make_list_select(" WHERE S.iddepartamento = " . $parametros->getParam('iddepartamento_os'));
					$smarty->assign("list_secao",$list_secao);
					
					$list_unidade_venda = $unidade_venda->make_list_select();
					$smarty->assign("list_unidade_venda",$list_unidade_venda);
					
					$list_ramo_atividade = $ramo_atividade->make_list_select();
					$smarty->assign("list_ramo_atividade",$list_ramo_atividade);

					$smarty->assign('iddepartamento_os', $parametros->getParam('iddepartamento_os'));

					break;


					//listagem dos registros
				case "listar":

					//obtém qual página da listagem deseja exibir
					$pg = intval(trim($_GET['pg']));

					//se não foi passada a página como parâmetro, faz página default igual à página 0
					if(!$pg) $pg = 0;

					$filtro = '';
					$campos = array();
					$idstatus = null;
					
					if(isset($_GET['for_chk'])){
						
						if(!empty($_GET['num_ordem'])){
							$campos[] = 'ORS.idordem_servico = ' . ((int)$_GET['num_ordem']);
						}
						
						if(!empty($_GET['previsao_servico_de']) && !empty($_GET['previsao_servico_ate'])){
							$campos[] = "ORS.previsao_servico BETWEEN '". $form->formataDataParaInserir($_GET['previsao_servico_de']).
										"' AND '". $form->formataDataParaInserir($_GET['previsao_servico_ate'])."'";
						}
						
						if(!empty($_GET['descricao_ordem'])){
							$campos[] = "ORS.descricao_ordem LIKE '%". $db->escape($_GET['descricao_ordem'])."%'";
						}
						
						if(!empty($_GET['status_ordem_servico'])){
							
							$idstatus_os		  = (int)$_GET['status_ordem_servico'];
							$idprogramacao_status = isset($_GET['programacao_status']) ? (int)$_GET['programacao_status'] : NULL;
							$idstatus             = $status_os_programacao->getIdStatusProg($idstatus_os,$idprogramacao_status);

							$campos[] = 'TRA.idstatus = ' . $idstatus;
						}
						
						if(!empty($_GET['idcliente'])){
							$campos[] = 'ORS.idcliente = ' . ((int)$_GET['idcliente']);
						}
						
						if(!empty($_GET['idtipo_servico'])){
							$campos[] = 'ORS.idtipo_servico = ' . ((int)$_GET['idtipo_servico']);
						}
					}
					
					if(count($campos)){
						$filtro = 'WHERE '.implode(' AND ',$campos);
					}

					
					if(!empty($_GET)){
						
						foreach($_GET as $k=>$v){ 
							
							if(in_array($k,array('pg'))) continue;
							
							$url[] = "$k=$v";	 
						}
						
						$url = implode('&',$url);
					}
					else{
						$url= '';
					}

					$rppg = isset($_GET['rppg']) ? (int)$_GET['rppg'] : $conf['rppg'];
					
					//lista os registros
					$list = $ordem_servico->make_list($pg, $rppg,$filtro, $ordem = "", $url,$idstatus); 

					//pega os erros
					$err = $ordem_servico->err;

					//passa a listagem para o template
					$smarty->assign("list", $list);

					$list_status_os = $status_os->make_list_select();
					$smarty->assign("list_status_os",$list_status_os);
					
					$list_programacao_status = $programacao_status->make_list_select();
					$smarty->assign("list_programacao_status",$list_programacao_status);
					
					$list_tipo_servico = $tipo_servico->make_list_select();
					$smarty->assign("list_tipo_servico",$list_tipo_servico);
					
					break;


					// ação: editar <<<<<<<<<<
				case "editar":

					//Recupera o id da OS convertendo para inteiro (evita SQL Injection)
					$idordem_servico = (int) $_GET['idordem_servico'];
					
					if($_POST['for_chk']) {

						$info = $_POST;
						$info['idordem_servico'] = $idordem_servico;
							
						//Validações do lado servidor
						$form->chk_empty($_POST['descricao_ordem'], 1, 'Descrição');
						$form->chk_empty($_POST['numidcliente'], 1, 'Cliente');
						$form->chk_empty($_POST['numidsolicitante'], 1, 'Solicitante');
							
						$err = $form->err;

						if(!count($err)) {

							//Inicia a transação no banco de dados
							$db->query('begin');
							
							//Trata o array com os dados de atualização
							$dadosOrdemServico = array( 
												'litdescricao_ordem'  	=> $db->escape($_POST['descricao_ordem']),
												'litprevisao_servico' 	=> $form->FormataDataParaInserir($_POST['previsao_servico']),
												'numidcliente'       	=> (int)$_POST['numidcliente'],
												'numidsolicitante'    	=> (int)$_POST['numidsolicitante'],
												'numidfuncionario'   	=> $_SESSION['usr_cod'],
												'numidtipo_servico'   	=> (int)$_POST['idtipo_servico'],
												'litobservacao_ordem' 	=> $db->escape($_POST['observacao_ordem']),
												'litendereco_cliente'	=> $db->escape($_POST['endereco_cliente']),
												'litendereco_fornecedor'=> $db->escape($_POST['endereco_fornecedor']),
												'numidfornecedor' 		=> empty($_POST['idfornecedor']) ? 'NULL' : (int)$_POST['idfornecedor']
							);

							$ordem_servico->update($idordem_servico, $dadosOrdemServico);
							
							//obtém erros
							$err = $ordem_servico->err;

							//Se não houver erros, atualiza os materiais (produtos) da OS
							if(!count($err)){
							
								$valor_total_os = $material_ordem_servico->atualizaMateriaisOs($idordem_servico, $_POST['material']);
								$err = $material_ordem_servico->err;
							}							
							
							//Registra a transação de status
							if(!count($err)){

								$idprogramacao_status = isset($_POST['programacao_status']) ? (int)$_POST['programacao_status'] : NULL;
								$idstatus_os		  = (int)$_POST['status_ordem_servico'];
								$idstatus             = $status_os_programacao->getIdStatusProg($idstatus_os,$idprogramacao_status);
			
								if($_POST['idstatus_os'] != $idstatus){
									
									$transicao_status->set(array('idfuncionario'	    => $_SESSION['usr_cod'] ,
																 'idstatus'             => $idstatus,
																 'idordem_servico'      => $idordem_servico,
																 'data_hora_transicao'  => date('Y-m-d H:i:s'),
																 'observacao_transicao' => $db->escape($_POST['litobservacao_ordem']),
																 'data_programacao'     => $form->FormataDataParaInserir($_POST['data_programacao']),
																 'motivo_programacao'   => $db->escape($_POST['motivo_programacao']) )
							   		);
									
									$err = $transicao_status->err;
								}
															
							}
							
							
							if(isset($_POST['gerar_movimento']) && !empty($_POST['gerar_movimento']) && !count($err)) {
								
								$num_ordem_servico    = str_pad($idordem_servico,7,'0',STR_PAD_LEFT);
								$idfornecedor_usuario = $parametros->getParam('idfornecedor_usuario'); 
								$idcliente_destino    = $parametros->getParam('cliente_destino');
								
								$idmovimento = $movimento->set(array(
																'idcliente_origem'      => (int)$_POST['numidcliente'],
																'idcliente_destino'		=> ($idfornecedor_usuario == $_POST['idfornecedor'] ? $idcliente_destino : 'NULL'),	
																'idfilial'				=> $_SESSION['idfilial_usuario'],
																'descricao_movimento'	=> $db->escape($_POST['descricao_ordem']),
																'controle_movimento'	=> $num_ordem_servico,
																'valor_movimento'		=> $valor_total_os,
																'data_cadastro' 		=> date('Y-m-d H:i:s'),
																'data_movimento' 		=> date('Y-m-d'),
																'baixado' 				=> '0',
																'observacao'			=> 'Ordem de Serviço No. '.$num_ordem_servico,
																'gerar_fatura'			=> '1'
															));
								//$idmovimento
								$err = $movimento->err;
								
								//Abre a tela com os dados da movimentação
								if(!count($err)) {
									echo '<script type="text/javascript">window.open("'.$conf['addr'].'/admin/movimento.php?ac=editar&idmovimento='.$idmovimento.'");</script>';
								}
								
							}
							
							
							//se não ocorreram erros
							if(!count($err)) {

								$db->query('commit');
								
								$flags['sucesso'] = $conf['alterar'];

								//limpa o $flags.action para que seja exibida a listagem
								$flags['action'] = "listar";

								//lista
								$list = $ordem_servico->make_list(0, $conf['rppg']);

								//pega os erros
								$err = $ordem_servico->err;

								//envia a listagem para o template
								$smarty->assign("list", $list);

							}

						}
						

					}
					else {

						//busca detalhes
						$info = $ordem_servico->getById($idordem_servico);

						//tratamento das informações para fazer o UPDATE
						$info['litdescricao_ordem']  = $info['descricao_ordem'];
						$info['litprevisao_servico'] = $info['previsao_servico'];
						$info['numidcliente']        = $info['idcliente'];
						$info['numidsolicitante']    = $info['idsolicitante'];
						$info['numidtipo_servico']   = $info['idtipo_servico'];
						
						if(!empty($info['idfornecedor'])){
							$info['dados_fornecedor'] = $fornecedor->getById($info['idfornecedor']);
						}

						if(isset($_GET['imprimir']) && $_GET['imprimir'] ==1){
							$info['observacao_ordem'] = nl2br($info['observacao_ordem']);
						}

						list($info['materiais'], $info['valor_total_os']) = $material_ordem_servico->make_list_os($idordem_servico);
						$tranStatus                 = $transicao_status->getLastStatus($idordem_servico);
						$info['status_programacao'] = $status_os_programacao->getById($tranStatus['idstatus']);
							
						if($info['status_programacao'] && count($tranStatus)){
							$info['status_programacao'] = array_merge($info['status_programacao'], $tranStatus);
						}
						
						$info['idstatus_os'] = $tranStatus['idstatus'];
						
						$info['filial'] = $filial->BuscaDadosFilial($_SESSION['idfilial_usuario']);
						$info['filial']['endereco']['linha1'] = ucwords(strtolower($info['filial']['endereco']['linha1'])); 
						$info['filial']['nome_cidade'] 		  = ucwords(strtolower($info['filial']['nome_cidade']));						
						//obtém os erros
						$err = $ordem_servico->err;
					}

					$list_transicao_status = $transicao_status->getListStatus($idordem_servico);
					$smarty->assign("list_transicao_status",$list_transicao_status);

					$primeiroStatus = end($list_transicao_status);					
					$info['aberto_por'] = $primeiroStatus['nome_funcionario'];
					
					$list_tipo_servico = $tipo_servico->make_list_select();
					$smarty->assign("list_tipo_servico",$list_tipo_servico);
					
					$list_status_os = $status_os->make_list_select();
					$smarty->assign("list_status_os",$list_status_os);

					$list_programacao_status = $status_os_programacao->getProgramacao($info['status_programacao']['idstatus_os']);
					$smarty->assign("list_programacao_status",$list_programacao_status);
					
					$list_unidade_venda = $unidade_venda->make_list_select();
					$smarty->assign("list_unidade_venda",$list_unidade_venda);
					
					$iddepartamento_os = $parametros->getParam('iddepartamento_os');
					
					$list_secao = $secao->make_list_select(" WHERE S.iddepartamento = $iddepartamento_os");
					$smarty->assign("list_secao",$list_secao);
					
					$list_ramo_atividade = $ramo_atividade->make_list_select();
					$smarty->assign("list_ramo_atividade",$list_ramo_atividade);
					
					$smarty->assign('iddepartamento_os', $iddepartamento_os);

					//passa os dados para o template
					$smarty->assign("info", $info);

				break;



					// deleta um registro do sistema
				case "excluir":

					//verifica se foi pedido a deleção
					if($_POST['for_chk']){

						// deleta o registro
						$ordem_servico->delete($_GET['idordem_servico']);

						//obtém erros
						$err = $ordem_servico->err;

						//se não ocorreram erros
						if(count($err) == 0){
							$flags['sucesso'] = $conf['excluir'];



						}

						//limpa o $flags.action para que seja exibida a listagem
						$flags['action'] = "listar";

						//lista registros
						$list = $ordem_servico->make_list(0, $conf['rppg']);

						//pega os erros
						$err = $ordem_servico->err;

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
$smarty->assign('xajax_javascript', $xajax->getJavascript("../common/lib/xajax/"));

if ( ($flags['action'] == "editar") && ($_GET['imprimir'] == 1) ) $smarty->display("adm_ordem_servico_impressao.tpl");
else $smarty->display("adm_ordem_servico.tpl");
