<?php
	
	class funcionario {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function funcionario(){
			// não faz nada
		}



		/**
		  método: Filtra_Funcionario_AJAX
		  propósito: Filtra_Funcionario_AJAX
		*/

		function Filtra_Funcionario_AJAX ( $filtro, $campoID ) {

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// verifica qual a pagina atual
			if (!isset($_GET["page"])) $pg = 0;
			else $pg = $_GET["page"];

			// maximo numero de registros listados
			$rppg = $conf['rppg_auto_completar'];

			// volta o filtro para a codificação original
			$filtro = utf8_decode($filtro);
			$campoID = utf8_decode($campoID);

			// campos de controle
			$campoNomeTemp = $campoID . "_NomeTemp";
			$campoFlag = $campoID . "_Flag";

			$list_sql = "	SELECT
					 						FNC.* , CRG.*
										FROM
											{$conf['db_name']}funcionario FNC
											INNER JOIN {$conf['db_name']}cargo CRG ON FNC.idcargo = CRG.idcargo

										WHERE
											FNC.situacao_funcionario = 'A'
											  AND
											(
												UPPER(FNC.nome_funcionario) LIKE UPPER('%{$filtro}%')
											)
										ORDER BY
											FNC.nome_funcionario ASC ";


			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);


			if($list_q){

				// testa se retornou algum registro
				if ($db->num_rows($list_q) > 0) {

					?>
					<table width="100%" cellpadding="5" cellspacing="2">
						<tr onselect="" class="cabecalho">
							<td width="70%" class="cabecalho_negrito"><?php echo ("Funcion&aacute;rio"); ?></td>
							<td width="30%" class="cabecalho_negrito"><?php echo ('Cargo'); ?></td>
						</tr>
					<?php

					$cont = 0;
					while($list = $db->fetch_array($list_q)){

						//insere um índice na listagem
						$list['index'] = $cont+1;

						// coloca em negrito a string que foi encontrada na palavra
						$list['nome_funcionario_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", htmlentities($list['nome_funcionario']));
						
						
						if ($campoID == "idfuncionario_programa")
						{
								$list['info_funcionario'] = utf8_decode("<table class=tb4cantos width=70%><tr bgcolor=#F7F7F7><td colspan=3 align=center> Filiais do Funcion&aacute;rio</td></tr> ");
								$sql_filial = "	SELECT
											 						FL.*
																FROM
																	{$conf['db_name']}funcionario FNC
																	INNER JOIN {$conf['db_name']}filial_funcionario FLFNC ON FNC.idfuncionario = FLFNC.idfuncionario
																	INNER JOIN {$conf['db_name']}filial FL ON FL.idfilial = FLFNC.idfilial
																	
																WHERE
																	FNC.idfuncionario = " . $list['idfuncionario'] . "
																ORDER BY
																	FNC.nome_funcionario ASC ";
								$sql = $db->query($sql_filial);
								
								while($list2 = $db->fetch_array($sql))
								{
									$list2['table'] = "<tr><td>Filial: " . $list2['nome_filial'] . "</td><td>Telefone: " . $form->FormataTelefoneParaExibir($list2['telefone_filial']) . "</td></tr>";
									$list['info_funcionario'] = $list['info_funcionario']  . $list2['table'];
								}						
						}
						
						$list['info_funcionario'] = $list['info_funcionario'] . "</table>";
										
						?>
						<tr onselect="
							this.text.value = '<?php echo (htmlentities($list['nome_funcionario'])); ?>';
							$('<?php echo $campoNomeTemp; ?>').value = '<?php echo ($list['nome_funcionario']); ?>';
							$('<?php echo $campoID; ?>').value = '<?php echo ($list['idfuncionario']); ?>';
							$('<?php echo $campoFlag; ?>').className = 'selecionou';

							<?php if ($campoID == "idfuncionario_programa") ?>
							$('dados_funcionario').innerHTML = '<?php echo ($list['info_funcionario']); ?>';
							
							<?php if($campoID == "idfuncionario_programa") echo "xajax_Seleciona_Programa_AJAX(xajax.getFormValues('for'))"; ?>
						">
							<td class="tb_bord_baixo"><?php echo ($list['nome_funcionario_negrito']); ?></td>
							<td class="tb_bord_baixo"><?php echo (htmlentities($list['nome_cargo'])); ?></td>
						</tr>
						
						
						<?php

	          $cont++;
					}

					// verifica a paginação
					$paginacao = "";
					if ($pg > 0) $paginacao .= "<a href='?page=" . ($pg - 1) . "' style='float:left' class='page_up'>" . ('Anterior') . "</a>";
					$paginacao .= "<a href='?page=" . ($pg + 1) .  "' style='float:right'  class='page_down'>" . ('Proximo') . "</a>";

				}
				// Nenhum registro foi encontrado
				else {
					?>
					<table width="100%" cellpadding="5" cellspacing="2">
						<tr onselect="" class="cabecalho">
							<td width="70%" class="cabecalho_negrito"><?php echo ($conf['listar']); ?></td>
						</tr>
					<?php

					// verifica a paginação
					$paginacao = "";
					if ($pg > 0) $paginacao .= "<a href='?page=" . ($pg - 1) . "' style='float:left' class='page_up'>" . ('Anterior') . "</a>";
				}

			}
			else{
				?>
				<table width="100%" cellpadding="5" cellspacing="2">
					<tr onselect="" class="cabecalho">
						<td width="70%" class="cabecalho_negrito"><?php echo ($falha['listar']); ?></td>
					</tr>
				<?php
			}

			// Encerra a tabela e coloca a paginação
			echo "</table>";
			if ($paginacao != "") echo $paginacao;

		}



		/**
		  método: Seleciona_Funcionarios_Da_Filial
		  propósito: Seleciona_Funcionarios_Da_Filial
		  
		  $tipo = Tipo do funcionario:
										"V" para vendedores,
										"T" para todos,
										"N" para não vendedores
										
			$situacao = Situação do funcionário:
									  "A" ativos,
									  "I" inativos,
									  "T" todos
		*/

		function Seleciona_Funcionarios_Da_Filial( $idfilial, $tipo = "T", $situacao = "A" ) {

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			if ($tipo == "V") $filtro = " AND FNC.tipo_vendedor_funcionario <> 'N' ";
			else if ($tipo == "N") $filtro = " AND FNC.tipo_vendedor_funcionario = 'N' ";
			else $filtro = "";

			if ($situacao == "A") $filtro .= " AND FNC.situacao_funcionario = 'A' ";
			else if ($situacao == "I") $filtro .= " AND FNC.situacao_funcionario = 'I' ";



			$list_sql = "	SELECT
					 						FNC.*
										FROM
											{$conf['db_name']}funcionario FNC
												INNER JOIN {$conf['db_name']}filial_funcionario FLFNC ON FNC.idfuncionario=FLFNC.idfuncionario
										WHERE
											FLFNC.idfilial = $idfilial
											
											$filtro
											
										ORDER BY
											FNC.nome_funcionario ASC ";

			$list_q = $db->query($list_sql);
			if($list_q){
 
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					foreach($list as $campo => $value){
						$list_return["$campo"][$cont] = $value;
					}
					
          $cont++;
				}

				return $list_return;

			}
			else{
				$this->err = $falha['listar'];
				return(0);
			}

		}




		/**
		  método: Verifica_CPF_Duplicado
		  propósito: Verifica_CPF_Duplicado
		*/
		function Verifica_CPF_Duplicado($CPF, $idfuncionario = ""){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$filtro = "";

			if ($idfuncionario != "") $filtro = " AND FNC.idfuncionario <> $idfuncionario ";

			$get_sql = "	SELECT
											FNC.*
										FROM
											{$conf['db_name']}funcionario FNC
										WHERE
											 FNC.cpf_funcionario = '$CPF'
											 
											 $filtro
											 
									";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				if ($db->num_rows($get_q) == 0) return (false);
				else return (true);

			}
			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}

		}

		


		/**
		  método: getById
		  propósito: busca informações
		*/
		function getById($idfuncionario){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
								FNC.*, CAR.nome_cargo
							FROM
								{$conf['db_name']}funcionario FNC
							LEFT JOIN cargo CAR on (FNC.idcargo = CAR.idcargo)
							WHERE
								 FNC.idfuncionario = $idfuncionario ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				if ( !empty($get['data_admissao_funcionario']) && $get['data_admissao_funcionario'] != '0000-00-00') $get['data_admissao_funcionario'] = $form->FormataDataParaExibir($get['data_admissao_funcionario']); 
				else $get['data_admissao_funcionario'] = null;
				
				if ( !empty($get['data_demissao_funcionario']) && $get['data_demissao_funcionario'] != '0000-00-00') $get['data_demissao_funcionario'] = $form->FormataDataParaExibir($get['data_demissao_funcionario']); 
				else $get['data_demissao_funcionario'] = null;
								
				if ( !empty($get['data_nascimento_funcionario']) && $get['data_nascimento_funcionario'] != '0000-00-00') $get['data_nascimento_funcionario'] = $form->FormataDataParaExibir($get['data_nascimento_funcionario']); 
				else $get['data_nascimento_funcionario'] = null;
				
				if ( !empty($get['data_nascimento_conjuge_funcionario']) && $get['data_nascimento_conjuge_funcionario'] != '0000-00-00' ) $get['data_nascimento_conjuge_funcionario'] = $form->FormataDataParaExibir($get['data_nascimento_conjuge_funcionario']); 
				else $get['data_nascimento_conjuge_funcionario'] = null;
				
				if ( !empty($get['data_afastamento']) && $get['data_afastamento'] != '0000-00-00' ) $get['data_afastamento'] = $form->FormataDataParaExibir($get['data_afastamento']); 
				else $get['data_afastamento'] = null;
				
				$get['meta_mensal_vendedor'] = number_format($get['meta_mensal_vendedor'],2,",","");
				
				$get['salario_funcionario'] = number_format($get['salario_funcionario'],2,",","");
				
				//retorna o vetor associativo com os dados
				return $get;
			}
			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}
				
		}

		/**
		  método: make_list
		  propósito: faz a listagem
		*/
		
		function make_list( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
			
			if ($ordem == "") $ordem = " ORDER BY FNC.nome_funcionario ASC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
								FNC.* , CRG.nome_cargo, BAN.nome_banco, 
								CON.agencia_funcionario, CON.agencia_dig_funcionario, 
								CON.conta_funcionario, CON.conta_dig_funcionario, 
								EDR.*, BAR.nome_bairro, CID.nome_cidade, EST.sigla_estado
							FROM
           						{$conf['db_name']}funcionario FNC 
								INNER JOIN {$conf['db_name']}cargo CRG ON FNC.idcargo=CRG.idcargo 
								LEFT JOIN {$conf['db_name']}endereco EDR ON FNC.idendereco_funcionario=EDR.idendereco
								LEFT JOIN {$conf['db_name']}conta_funcionario CON ON(FNC.idfuncionario = CON.idfuncionario AND CON.principal_funcionario = '1')
         						LEFT JOIN {$cong['db_name']}banco BAN ON CON.idbanco = BAN.idbanco
           						
								LEFT OUTER JOIN {$conf['db_name']}bairro BAR ON EDR.idbairro=BAR.idbairro
								LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade=CID.idcidade
								LEFT OUTER JOIN {$conf['db_name']}estado EST ON EDR.idestado=EST.idestado
								
							WHERE FNC.situacao_funcionario = 'A'
								
									
							$filtro
							$ordem";

			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);
			//$list_q = $db->query($list_sql);

			if($list_q){
				
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){
					
					//insere um índice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);
					
					if ($list['data_nascimento_funcionario'] != '0000-00-00') $list['data_nascimento_funcionario'] = $form->FormataDataParaExibir($list['data_nascimento_funcionario']); 
					else $list['data_nascimento_funcionario'] = null;
					
					if ($list['data_admissao_funcionario'] != '0000-00-00' && !empty($list['data_admissao_funcionario'])) $list['data_admissao_funcionario'] = $form->FormataDataParaExibir($list['data_admissao_funcionario']); 
					else $list['data_admissao_funcionario'] = null;
					
					if ($list['data_nascimento_conjuge_funcionario'] != '0000-00-00') $list['data_nascimento_conjuge_funcionario'] = $form->FormataDataParaExibir($list['data_nascimento_conjuge_funcionario']); 
					else $list['data_nascimento_conjuge_funcionario'] = null;
					
					if ($list['data_afastamento'] != '0000-00-00') $list['data_afastamento'] = $form->FormataDataParaExibir($list['data_afastamento']); 
					else $list['data_afastamento'] = null;
					
					if(!empty($list['conta_funcionario']) && !empty($list['conta_dig_funcionario']))
					$list['conta_funcionario'] .= '-'.$list['conta_dig_funcionario'];
					
					if(!empty($list['agencia_funcionario']) && !empty($list['agencia_dig_funcionario']))
					$list['agencia_funcionario'] .= '-'.$list['agencia_dig_funcionario'];
					
					if ($list['sexo_funcionario'] == 'M') $list['sexo_funcionario'] = "Masculino"; 
					else if ($list['sexo_funcionario'] == 'F') $list['sexo_funcionario'] = "Feminino"; 
					
					
					$list['endereco'] = null;
					if(!empty($list['numero'])) $list['logradouro'] .= ', '.$list['numero'];
					if(!empty($list['logradouro'])) $list['endereco'] .= $list['logradouro'] . '<br />';
					if(!empty($list['nome_bairro'])) $list['endereco'] .= 'Bairro '.$list['nome_bairro'] . ' - ';
					if(!empty($list['nome_cidade'])) $list['endereco'] .= ' '.$list['nome_cidade'] . '/'. $list['sigla_estado'] ;
					
					if($list['endereco'] != null) $list['endereco'] = strtoupper(trim($list['endereco']));
					
					$list['telefone_funcionario'] = $form->FormataTelefoneParaExibir($list['telefone_funcionario']); 
					$list['celular_funcionario'] = $form->FormataTelefoneParaExibir($list['celular_funcionario']); 
					
					if ($list['tipo_vendedor_funcionario'] == 'N') $list['tipo_vendedor_funcionario'] = "Não é Vendedor";
					if ($list['tipo_vendedor_funcionario'] == 'I') $list['tipo_vendedor_funcionario'] = "Vendedor Interno";
					if ($list['tipo_vendedor_funcionario'] == 'E') $list['tipo_vendedor_funcionario'] = "Vendedor Externo";
					if ($list['tipo_vendedor_funcionario'] == 'R') $list['tipo_vendedor_funcionario'] = "Vendedor Representante";
					if ($list['tipo_vendedor_funcionario'] == 'T') $list['tipo_vendedor_funcionario'] = "Vendedor Telemarketing";
					
			        $list_return[] = $list;
			          
			        $cont++;
				}
				
				return $list_return;
					
			}	
			else{
				$this->err = $falha['listar'];
				return(0);
			}
		}	
		
		
		
	 /**
	 * Método que encapsula os nomes para cada opção de tipo_vendedor_funcionario
	 * @param string $tipo Uma das opções do campo tipo_vendedor_funcionario
	 *
	 * @return string Descrição/nome referente à opção passada
		 */
		function getTipoVendedorDesc($tipo) {
			switch ($tipo) {
				case 'N':
					return 'Não é Vendedor';
				case 'I':
					return 'Vendedor Interno';
				case 'E':
					return 'Vendedor Externo';
				case 'R':
					return 'Vendedor Representante';
				case 'T':
					return 'Vendedor Telemarketing';
			}
		}
		
		
		
		

		/**
			método: set
		  propósito: inclui novo registro
		*/

		function set($info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			if($info['qtd_filhos'] == '') $info['qtd_filhos'] = 0;
			if($info['salario_funcionario'] == '') $info['salario_funcionario'] = 0.00;
			
			$set_sql = "  INSERT INTO
		                  {$conf['db_name']}funcionario
		                    (
		                    
												nome_funcionario, 
												sexo_funcionario, 
												idcargo, 
												login_funcionario, 
												senha_funcionario, 
												identidade_funcionario, 
												cpf_funcionario, 
												carteira_trabalho_funcionario, 
												data_nascimento_funcionario,
												data_admissao_funcionario,
												data_demissao_funcionario, 
												idendereco_funcionario, 
												telefone_funcionario, 
												celular_funcionario, 
												email_funcionario, 
												nome_conjuge_funcionario, 
												data_nascimento_conjuge_funcionario, 
												observacao_funcionario,
												tipo_vendedor_funcionario,
												meta_mensal_vendedor,
												situacao_funcionario,
												
												cor_funcionario, 		
												cabelo_funcionario, 	
												olhos_funcionario, 	
												altura_funcionario, 	
												peso_funcionario, 		
												sinais_funcionario,
												titulo_eleitor, 	
												pis_pasep, 						
												escolaridade_funcionario,	
												habilitacao, 					
												data_emissao, 					
												nome_pai, 						
												nome_mae, 						
												estado_civil, 					
												qtd_filhos, 						
												horario_trabalho, 				
												salario_funcionario,  	   
												dias_contrato_exp,
												naturalidade, 		
												nacionalidade, 		
												ctps_serie,			
												ctps_uf, 				
												data_afastamento, 	
												motivo_afastamento,
												filhos_funcionario,
												tipo_salario, 		
												intervalo_refeicao,
												rg_data_expedicao_funcionario,
												rg_orgao_emissor_funcionario,
												numeracao_blusa_funcionario,
												numeracao_calca_funcionario,
												numeracao_calcado_funcionario
												
												
												
																
												
											)
		                					VALUES
							                    (
							                    
							                    '" . $info['nome_funcionario'] . "',  
												'" . $info['sexo_funcionario'] . "',  
												 " . $info['idcargo'] . ",  
												'" . $info['login_funcionario'] . "',  
												'" . $info['senha_funcionario'] . "',  
												'" . $info['identidade_funcionario'] . "',  
												'" . $info['cpf_funcionario'] . "',  
												'" . $info['carteira_trabalho_funcionario'] . "',  
												'" . $info['data_nascimento_funcionario'] . "',  
												'" . $info['data_admissao_funcionario'] . "',
												'" . $info['data_demissao_funcionario'] . "',
												 " . $info['idendereco_funcionario'] . ",  
												'" . $info['telefone_funcionario'] . "',  
												'" . $info['celular_funcionario'] . "',  
												'" . $info['email_funcionario'] . "',  
												'" . $info['nome_conjuge_funcionario'] . "',  
												'" . $info['data_nascimento_conjuge_funcionario'] . "',  
												'" . $info['observacao_funcionario'] . "',
												'" . $info['tipo_vendedor_funcionario'] . "',
												'" . $info['meta_mensal_vendedor'] . "',
												'" . $info['situacao_funcionario'] . "',
												
												'" . $info['cor_funcionario' ] . "',		
												'" . $info['cabelo_funcionario'] . "', 	
												'" . $info['olhos_funcionario'] . "', 	
												'" . $info['altura_funcionario'] . "', 	
												'" . $info['peso_funcionario'] . "', 		
												'" . $info['sinais_funcionario'] . "',
												'" . $info['titulo_eleitor'] . "', 	
												'" . $info['pis_pasep'] . "',				
												'" . $info['escolaridade_funcionario'] . "',	
												'" . $info['habilitacao'] . "', 					
												'" . $info['data_emissao'] . "', 					
												'" . $info['nome_pai'] . "', 						
												'" . $info['nome_mae'] . "', 						
												'" . $info['estado_civil'] . "', 					
												 " . $info['qtd_filhos'] . ", 						
												'" . $info['horario_trabalho'] . "', 				
												 " . $info['salario_funcionario'] . ",  	   
												'" . $info['dias_contrato_exp'] . "',
												'" . $info['naturalidade'] . "',
												'" . $info['nacionalidade'] . "',
												'" . $info['ctps_serie'] . "',
												'" . $info['ctps_uf'] . "',
												'" . $info['data_afastamento'] . "',
												'" . $info['motivo_afastamento'] . "',
												'" . $info['filhos_funcionario'] . "',
												'" . $info['tipo_salario'] . "',
												'" . $info['intervalo_refeicao'] . "',
												'" . $info['rg_data_expedicao_funcionario'] . "',
												'" . $info['rg_orgao_emissor_funcionario'] . "',
												'" . $info['numeracao_blusa_funcionario'] . "',
												'" . $info['numeracao_calca_funcionario'] . "',
												'" . $info['numeracao_calcado_funcionario']."'
												
												)";
			
			//executa a query e testa se a consulta foi "boa"
			if($db->query($set_sql)){
				//retorna o código inserido
				$codigo = $db->insert_id();
				
				
				
				return($codigo);
			}
			else{
				$this->err = $falha['inserir'];
				return(0);
			}
		}
		
		/**
		  método: update
		  propósito: atualiza os dados
		  
		  	1) o vetor $info deve conter todos os campos tabela a serem atualizados
			2) a variável $id deve conter o código do usuário cujos dados serão atualizados
			3) campos literais deverão ter o prefixo lit e campos numéricos deverão ter o prefixo num
		*/
		
		function update($idfuncionario, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE {$conf['db_name']}funcionario SET ";

   			//varre o formulário e monta a consulta;
			$cont_validos = 0;
			foreach($info as $campo => $valor){

				$tipo_campo = substr($campo, 0, 3);
				$nome_campo = substr($campo, 3, strlen($campo) - 3);
					
				if(($tipo_campo == "lit") || ($tipo_campo == "num")){
					
					$usu_validos["$campo"] = $valor;
					$cont_validos++;
					
				}
					
			}
			
			$cont = 0;
			foreach($usu_validos as $campo => $valor){
			
				$tipo_campo = substr($campo, 0, 3);
				$nome_campo = substr($campo, 3, strlen($campo) - 3);
				
				if($tipo_campo == "lit")
					$update_sql .= "$nome_campo = '$valor'";
				elseif($tipo_campo == "num"){
					if($valor == '') $valor = 'NULL'; //Trata inserção de valor numérico vazio 
					$update_sql .= "$nome_campo = $valor";
				}
					
				$cont++;
				
				//testa se é o último
				if($cont != $cont_validos){
					$update_sql .= ", ";
				}
				
			}
			

			//completa o sql com a restrição
			$update_sql .= " WHERE  idfuncionario = $idfuncionario ";
			
			
			//envia a query para o banco
			$update_q = $db->query($update_sql);
			
			if($update_q)
			  return(1);
			else
			  $this->err = $falha['alterar'];
		}	
		
		/**
		  método: delete
		  propósito: excluir registro
		*/
		function delete($idfuncionario){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			// conjunto de dependências geradas
			
			//---------------------
			

			// verifica se pode excluir
			if (1) {

				

				$delete_sql = "	DELETE FROM
													{$conf['db_name']}funcionario
												WHERE
													 idfuncionario = $idfuncionario ";
				$delete_q = $db->query($delete_sql);

				if($delete_q){
					return(1);
				}
				else{
					$this->err = $falha['excluir'];
					return(0);
				}
				
			}
			else {
				$this->err = "Este registro não pode ser excluído, pois existem registros relacionados a ele.";
			}	

		}	

		
		/**
		  método: make_list
		  propósito: faz a listagem para colocar no select
		*/

		function make_list_select( $filtro = "", $ordem = " ORDER BY nome_funcionario ASC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

				$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}funcionario
          					$filtro
										$ordem";

				$list_q = $db->query($list_sql);
				if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					foreach($list as $campo => $value){
						$list_return["$campo"][$cont] = $value;
					}

         			 $cont++;
				}

				return $list_return;
				
				

			}
			else{
				$this->err = $falha['listar'];
				return(0);
			}
			
		}


		function Busca_Generica ( $pg, $rppg, $busca = "", $ordem = "", $url = ""){

			if ($ordem == "") $ordem = " ORDER BY FUN.nome_funcionario ASC";

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT
								FUN.*, CAR.nome_cargo, BAN.nome_banco,
								CON.agencia_funcionario, CON.agencia_dig_funcionario, 
								CON.conta_funcionario, CON.conta_dig_funcionario,
								EDR.*, BAR.nome_bairro, CID.nome_cidade, EST.sigla_estado
							FROM
           						{$conf['db_name']}funcionario FUN
           						INNER JOIN {$conf['db_name']}cargo CAR ON FUN.idcargo=CAR.idcargo
           						LEFT JOIN {$conf['db_name']}conta_funcionario CON ON(FUN.idfuncionario = CON.idfuncionario AND CON.principal_funcionario = '1')
           						LEFT JOIN {$cong['db_name']}banco BAN ON CON.idbanco = BAN.idbanco
           						
           						LEFT OUTER JOIN {$conf['db_name']}endereco EDR ON FUN.idendereco_funcionario=EDR.idendereco
								LEFT OUTER JOIN {$conf['db_name']}bairro BAR ON EDR.idbairro=BAR.idbairro
								LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade=CID.idcidade
								LEFT OUTER JOIN {$conf['db_name']}estado EST ON EDR.idestado=EST.idestado
							
           					WHERE
           						FUN.situacao_funcionario = 'A'  AND (
           						UPPER(CAR.nome_cargo) LIKE UPPER('%{$busca}%') OR
           					 	UPPER(FUN.nome_funcionario) LIKE UPPER('%{$busca}%') OR
           					 	UPPER(FUN.identidade_funcionario) LIKE UPPER('%{$busca}%') OR
           					 	UPPER(FUN.cpf_funcionario) LIKE UPPER('%{$busca}%') OR
           					 	UPPER(FUN.data_nascimento_funcionario) LIKE UPPER('%{$busca}%') OR
           					 	UPPER(FUN.data_admissao_funcionario) LIKE UPPER('%{$busca}%')
											)
										$ordem ";


			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					//insere um índice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);

					if(!empty($list['conta_funcionario']) && !empty($list['conta_dig_funcionario']))
					$list['conta_funcionario'] .= '-'.$list['conta_dig_funcionario'];
					
					if(!empty($list['agencia_funcionario']) && !empty($list['agencia_dig_funcionario']))
					$list['agencia_funcionario'] .= '-'.$list['agencia_dig_funcionario'];
					
					$list['endereco'] = null;
					if(!empty($list['numero'])) $list['logradouro'] .= ', '.$list['numero'];
					if(!empty($list['nome_bairro'])) $list['endereco'] .= $list['logradouro'];
					if(!empty($list['nome_bairro'])) $list['endereco'] .= ' <br> '.$list['nome_bairro'] . ' - ';
					if(!empty($list['nome_cidade'])) $list['endereco'] .= $list['nome_cidade'] . '/'. $list['sigla_estado'] ;
					
					
					if ($list['data_nascimento_funcionario'] != '0000-00-00') {
						$list['data_nascimento_funcionario'] = $form->FormataDataParaExibir($list['data_nascimento_funcionario']);
					}
					
					if ($list['data_admissao_funcionario'] != '0000-00-00'  && !empty($list['data_admissao_funcionario'])) {
						$list['data_admissao_funcionario'] = $form->FormataDataParaExibir($list['data_admissao_funcionario']);
					}

			        $list_return[] = $list;
			        $cont++;
				}
				
				return $list_return;

			}
			else{
				$this->err = $falha['listar'];
				return(0);
			}
		}


		/**
		  método: 	
		  propósito: Busca_Parametrizada
		*/

		function Busca_Parametrizada ( $pg, $rppg, $filtro_where = "", $ordem = "", $url = ""){

			if ($ordem == "") $ordem = " ORDER BY FUN.nome_funcionario ASC";

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------


			if ( !empty($filtro_where) ) $filtro_where = " WHERE ( " . $filtro_where . " ) ";
			

			$list_sql = "	SELECT
								FUN.*, CAR.nome_cargo, BAN.nome_banco,
								CON.agencia_funcionario, CON.agencia_dig_funcionario, 
								CON.conta_funcionario, CON.conta_dig_funcionario, 
								EDR.*, BAR.nome_bairro, CID.nome_cidade, EST.sigla_estado
							FROM
		           			{$conf['db_name']}funcionario FUN
		           			INNER JOIN {$conf['db_name']}cargo CAR ON FUN.idcargo=CAR.idcargo
		           			
		           			LEFT JOIN {$conf['db_name']}conta_funcionario CON ON(FUN.idfuncionario = CON.idfuncionario AND CON.principal_funcionario = '1')
		           			LEFT JOIN {$cong['db_name']}banco BAN ON CON.idbanco = BAN.idbanco
		           			LEFT JOIN {$cong['db_name']}filial_funcionario FUNFILIAL ON FUNFILIAL.idfuncionario = FUN.idfuncionario
		           			LEFT OUTER JOIN {$conf['db_name']}endereco EDR ON FUN.idendereco_funcionario=EDR.idendereco
							LEFT OUTER JOIN {$conf['db_name']}bairro BAR ON EDR.idbairro=BAR.idbairro
							LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade=CID.idcidade
							LEFT OUTER JOIN {$conf['db_name']}estado EST ON EDR.idestado=EST.idestado

							$filtro_where

							$ordem ";

			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					//insere um índice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);
					
					if( $list['conta_funcionario'] != '' && $list['conta_dig_funcionario'] != '')
					$list['conta_funcionario'] .= '-'.$list['conta_dig_funcionario'];
					
					if($list['agencia_funcionario'] != '' && $list['agencia_dig_funcionario'] != '')
					$list['agencia_funcionario'] .= '-'.$list['agencia_dig_funcionario'];

					$list['endereco'] = null;
					if(!empty($list['numero'])) $list['logradouro'] .= ', '.$list['numero'];
					if(!empty($list['logradouro'])) $list['endereco'] .= $list['logradouro'];
					if(!empty($list['nome_bairro'])) $list['endereco'] .= '<br />Bairro '.$list['nome_bairro'] . ' - ';
					if(!empty($list['nome_cidade'])) $list['endereco'] .= ' '.$list['nome_cidade'] . '/'. $list['sigla_estado'] ;
					
					if($list['endereco'] != null) $list['endereco'] = strtoupper(trim($list['endereco']));
					
					if(!empty($list['telefone_funcionario'])) $list['telefone_funcionario'] = $form->FormataTelefoneParaExibir($list['telefone_funcionario']);
					
					if ($list['data_nascimento_funcionario'] != '0000-00-00') {
						$list['data_nascimento_funcionario'] = $form->FormataDataParaExibir($list['data_nascimento_funcionario']);
					}
					
					if ($list['data_admissao_funcionario'] != '0000-00-00'  && !empty($list['data_admissao_funcionario'])) {
						$list['data_admissao_funcionario'] = $form->FormataDataParaExibir($list['data_admissao_funcionario']);
					}
					
					if ($list['data_demissao_funcionario'] != '0000-00-00'  && !empty($list['data_demissao_funcionario'])) {
						$list['data_demissao_funcionario'] = $form->FormataDataParaExibir($list['data_demissao_funcionario']);
					}

			        $list_return[] = $list;
			        $cont++;
				}

				return $list_return;

			}
			else{
				$this->err = $falha['listar'];
				return(0);
			}
		}

		
		
		

	} // fim da classe
?>
