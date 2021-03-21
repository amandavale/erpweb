<?php
	

	class cliente {

		var $err;
		
		/**
	    * construtor da classe
	  	*/
		function cliente(){
			// não faz nada
		}

                
                
                
                /** Verifica se a senha do cliente está correta
                 *
                 * @global obj    $db
                 * @global obj    $form
                 * @global obj    $parametros
                 * @global array  $conf
                 * @param  string $senha
                 * @param  int    $idcliente
                 * @return bool 
                 */
                function chk_senha_cliente($senha, $idcliente){
                    
                    global $db, $conf, $form, $parametros;
                    
                    $sql    = "SELECT idcliente FROM {$conf['db_name']}cliente WHERE senha_cliente = MD5('$senha') AND idcliente = $idcliente ";
                    $sql_rs = $db->query($sql);
         
                    return ( $db->num_rows($sql_rs) > 0 ) ? true : false;
                    
                }
                
                
                
		/**
		  método: Filtra_Cliente_AJAX
		  propósito: Filtra_Cliente_AJAX
		*/

		function Filtra_Cliente_AJAX ( $filtro, $campoID, $mostraDetalhes, $setVencimento=1, $inserirEndereco = false ) {

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			global $endereco;
			//---------------------

			// verifica qual a pagina atual
			if (!isset($_GET["page"])) $pg = 0;
			else $pg = $_GET["page"];

			// maximo numero de registros listados
			$rppg = $conf['rppg_auto_completar'];

			// volta o filtro para a codificação original
			$filtro = utf8_decode($filtro);
			$campoID = utf8_decode($campoID);
			$mostraDetalhes = utf8_decode($mostraDetalhes);

			// campos de controle
			$campoNomeTemp = $campoID . "_NomeTemp";
			$campoFlag = $campoID . "_Flag";

			$list_sql = "	SELECT
											CLI.*, FCLI.cpf_cliente, JCLI.cnpj_cliente, EDR.*, BAR.*, CID.*, EST.*
										FROM
											{$conf['db_name']}cliente CLI
												 LEFT OUTER JOIN {$conf['db_name']}cliente_condominio COND ON CLI.idcliente=COND.idcliente
												 LEFT OUTER JOIN {$conf['db_name']}cliente_fisico FCLI ON CLI.idcliente=FCLI.idcliente
												 LEFT OUTER JOIN {$conf['db_name']}cliente_juridico JCLI ON CLI.idcliente=JCLI.idcliente
												 LEFT OUTER JOIN {$conf['db_name']}endereco EDR ON CLI.idendereco_cliente=EDR.idendereco
												 LEFT OUTER JOIN {$conf['db_name']}bairro BAR ON EDR.idbairro=BAR.idbairro
												 LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade=CID.idcidade
												 LEFT OUTER JOIN {$conf['db_name']}estado EST ON EDR.idestado=EST.idestado

										WHERE
										(
											UPPER(CLI.idcliente) LIKE UPPER('%{$filtro}%')
											  OR
											UPPER(CLI.nome_cliente) LIKE UPPER('%{$filtro}%')
											  OR
											UPPER(FCLI.cpf_cliente) LIKE UPPER('%{$filtro}%')
											  OR
											UPPER(JCLI.cnpj_cliente) LIKE UPPER('%{$filtro}%')
										)

										AND ( 
										
												(COND.idcliente IS NOT NULL) OR 
												(FCLI.idcliente IS NOT NULL) OR 
												(JCLI.idcliente IS NOT NULL) 
											)											
										
										AND CLI.cliente_bloqueado <> '1'
										
										ORDER BY
											CLI.nome_cliente ASC ";


			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);


			if($list_q){

				// testa se retornou algum registro
				if ($db->num_rows($list_q) > 0) {

					?>					
					<table width="100%" cellpadding="5" cellspacing="2">
						<tr onselect="" class="cabecalho">
							<td width="10%" class="cabecalho_negrito"><?php echo ('Codigo'); ?></td>
							<td width="60%" class="cabecalho_negrito"><?php echo ('Cliente'); ?></td>
							<td class="cabecalho_negrito" align="center"><?php echo ('CPF/CNPJ'); ?></td>
						</tr>
					<?php

					$cont = 0;
					$filtro = htmlentities($filtro);
					
					while($list = $db->fetch_array($list_q)){

						$list['nome_cliente'] = htmlentities($list['nome_cliente']);
						
						//insere um índice na listagem
						$list['index'] = $cont+1;

						if ($list['cpf_cliente'] != "") $list['cpf_cnpj'] = $list['cpf_cliente'];
						else $list['cpf_cnpj'] = $list['cnpj_cliente'];

						if ($list['cliente_bloqueado'] == "1") $list['cliente_bloqueado'] = "SIM";
						else $list['cliente_bloqueado'] = "NÃO";


						// coloca em negrito a string que foi encontrada na palavra
						$list['idcliente_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['idcliente']);
						$list['nome_cliente_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['nome_cliente']);
						$list['cpf_cnpj_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['cpf_cnpj']);

						if ($mostraDetalhes == 1)
							$list['info_cliente'] = utf8_encode("<table class=tb4cantos width=40%><tr><td>Endereço: " . $list['logradouro'] . " " . $list['numero'] . " " . $list['complemento'] . " CEP " .  $list['cep'] . "</td></tr><tr><td>Bairro: " . $list['nome_bairro'] . " " . $list['nome_cidade'] . " " . $list['sigla_estado'] . "</td></tr><tr><td>Telefone: " . $form->FormataTelefoneParaExibir($list['telefone_cliente']) . "</td></tr><tr><td>CPF/CNPJ: " . $list['cpf_cnpj'] . " Cliente bloqueado: " . $list['cliente_bloqueado'] . "</td></tr></table>");

						?>
						<tr onselect="
							this.text.value = '<?php echo ($list['nome_cliente']); ?>';
							$('<?php echo $campoNomeTemp; ?>').value = '<?php echo ($list['nome_cliente']); ?>';
							$('<?php echo $campoID; ?>').value = '<?php echo ($list['idcliente']); ?>';
							$('<?php echo $campoFlag; ?>').className = 'selecionou';
							<?php if ($inserirEndereco): ?>
								$('endereco_cliente').value = '<?php echo utf8_encode($endereco->formataStringEndereco($list));?>';
							<?php endif; ?>

							<?php if ($mostraDetalhes == 1) ?>
								$('dados_cliente').innerHTML = '<?php echo $list['info_cliente']; ?>';
								
						">
							<td class="tb_bord_baixo"><?php echo ($list['idcliente_negrito']); ?></td>
							<td class="tb_bord_baixo"><?php echo ($list['nome_cliente_negrito']); ?></td>
							<td class="tb_bord_baixo" align="center"><?php echo ($list['cpf_cnpj_negrito']); ?></td>
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
		 * Verifica se o cliente é um Condomínio
		 * 
		 * @param integer $idcliente
		 * @return boolean - true se o cliente for condomínio, false caso contrário.
		 */
		function chk_cliente_condominio($idcliente){
			
			//variáveis globais
			global $conf, $db, $falha;
				
			$get_sql = " SELECT idcliente FROM {$conf['db_name']}cliente_condominio WHERE idcliente = $idcliente ";
		
			//executa a query no banco de dados
			$get_q = $db->query($get_sql);
			
			if(!$get_q){
				$this->err = $falha['listar'];
				return false;
			}
			else{
				return ((bool)$db->num_rows($get_q));
			}	
				
			
		}



		/**
		 * Busca dados do cliente
		 * @param integer $idCliente - ID do cliente cujos dados devem ser buscados
		 * @param boolean $busca_endereco_apartamento - Indica se o endereço do apartamento deve ser buscado
		 *					caso o endereço encontrado seja vazio e o cliente esteja associado a um apartamento
		 */
		function BuscaDadosCliente ( $idCliente, $busca_endereco_apartamento = false ) {

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// verifica se eh um cliente fisico
			$get_sql = "	SELECT
	                            CLI.*, FCLI.cpf_cliente, JCLI.cnpj_cliente, CCLI.condominio_caixa, EDR.*, BAR.*, CID.*, EST.*
                            FROM
	                            {$conf['db_name']}cliente CLI
                            LEFT OUTER JOIN {$conf['db_name']}cliente_fisico FCLI ON CLI.idcliente=FCLI.idcliente                                                            
                            LEFT OUTER JOIN {$conf['db_name']}cliente_juridico JCLI ON CLI.idcliente=JCLI.idcliente
                            LEFT OUTER JOIN {$conf['db_name']}cliente_condominio CCLI ON CLI.idcliente=CCLI.idcliente
    	                    LEFT OUTER JOIN {$conf['db_name']}endereco EDR ON CLI.idendereco_cliente=EDR.idendereco
                            LEFT OUTER JOIN {$conf['db_name']}bairro BAR ON EDR.idbairro=BAR.idbairro
                            LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade=CID.idcidade
                            LEFT OUTER JOIN {$conf['db_name']}estado EST ON EDR.idestado=EST.idestado

                            WHERE
         	                   CLI.idcliente = $idCliente
                               AND CLI.cliente_bloqueado <> '1'
                            ";

			//executa a query no banco de dados
			$get_cliente_q = $db->query($get_sql);
			$get_cliente = $db->fetch_array($get_cliente_q);

			/// Se o endereço do cliente está vazio e foi definido que o endereço do apartamento deve ser buscado,
			/// verifica se o cliente é morador ou proprietário de algum apartamento e busca o endereço do condomínio
			if($busca_endereco_apartamento &&
				(!$get_cliente['logradouro'] || !$get_cliente['nome_cidade'] || !$get_cliente['nome_bairro'] || !$get_cliente['nome_estado'])){

				/// Se não houver endereço preenchido, verifica se o cliente é morador ou proprietário de algum apartamento 
				/// e preenche o endereço com esses dados
				$query = "SELECT idcliente FROM {$conf['db_name']}apartamento WHERE idmorador = $idCliente OR idproprietario = $idCliente LIMIT 1";
				$get_query = $db->query($query);
				$get_cliente_apartamento = $db->fetch_array($get_query);

				if($get_cliente_apartamento['idcliente']){

					/// se o cliente for morador ou proprietário de algum apartamento busca o endereço do condomínio
					$get_sql = "SELECT
	                            	EDR.*, BAR.*, CID.*, EST.*
                            	FROM
                            		{$conf['db_name']}cliente CLI 
	                                LEFT OUTER JOIN {$conf['db_name']}endereco EDR ON CLI.idendereco_cliente=EDR.idendereco
                            		LEFT OUTER JOIN {$conf['db_name']}bairro BAR ON EDR.idbairro=BAR.idbairro
                            		LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade=CID.idcidade
                            		LEFT OUTER JOIN {$conf['db_name']}estado EST ON EDR.idestado=EST.idestado

                            WHERE
         	                   CLI.idcliente = {$get_cliente_apartamento['idcliente']}
                               AND CLI.cliente_bloqueado <> '1'
                            ";

					$get_cliente_q = $db->query($get_sql);
					$endereco = $db->fetch_array($get_cliente_q);

					$get_cliente['logradouro'] = $endereco['logradouro'];
					$get_cliente['nome_cidade'] = $endereco['nome_cidade'];
					$get_cliente['nome_bairro'] = $endereco['nome_bairro'];
					$get_cliente['nome_estado'] = $endereco['nome_estado'];
					$get_cliente['cep'] = $endereco['cep'];
					$get_cliente['numero'] = $endereco['numero'];
				}
			}

			// cliente fisico
			if ($get_cliente['cpf_cliente'] != "") {
				$get_cliente['cpf_cnpj'] = $get_cliente['cpf_cliente'];
				$get_cliente['dados_cliente_linha_2'] = "CPF:" . $get_cliente['cpf_cliente'] . " ID:" . $get_cliente['identidade_cliente'];
			}
			// cliente juridico
			else {
				$get_cliente['cpf_cnpj'] = $get_cliente['cnpj_cliente'];
				$get_cliente['dados_cliente_linha_2'] = "CNPJ:" . $get_cliente['cnpj_cliente'] . " IE:" . $get_cliente['inscricao_estadual_cliente'];
			}

			if ($get_cliente['cliente_bloqueado '] == "1") $get_cliente['cliente_bloqueado'] = "SIM";
			else $get_cliente['cliente_bloqueado'] = "NÃO";
			//----------------------------------------------
			
			$get_cliente['telefone_cliente'] = $form->FormataTelefoneParaExibir($get_cliente['telefone_cliente']);
			$get_cliente['fax_cliente']      = $form->FormataTelefoneParaExibir($get_cliente['fax_cliente']);

			// Arruma o array de retorno para o cupom fiscal
			$get_cliente['dados_cliente_linha_1'] = $get_cliente['nome_cliente'];

			$get_cliente['dados_cliente_linha_3'] = $get_cliente['logradouro']  . " " . $get_cliente['numero'];
			$get_cliente['dados_cliente_linha_4'] = $get_cliente['nome_bairro'] . "-" . $get_cliente['nome_cidade'];
			$get_cliente['dados_cliente_linha_5'] = $get_cliente['nome_estado'] . " CEP:" . $get_cliente['cep'];

			//retorna o vetor associativo com os dados
			return $get_cliente;

		}




		/**
		  método: AtualizaCliente
		  propósito: AtualizaCliente
		*/
		function AtualizaCliente($idcliente, $post){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// retira os campos que estao na tabela clientes juridicos
			unset($post['litcnpj_cliente']);
			unset($post['litinscricao_estadual_cliente']);
			unset($post['litnome_fantasia']);
			unset($post['litnome_contato']);
			unset($post['litdata_nascimento_contato']);
			unset($post['litcelular_contato']);


			// retira os campos que estao na tabela clientes fisicos
			unset($post['numidcliente']);
			unset($post['litcpf_cliente']);
			unset($post['litsexo_cliente']);
			unset($post['litidentidade_cliente']);
			unset($post['litdata_nascimento_cliente']);
			unset($post['littel_residencial_cliente']);
			unset($post['litcelular_cliente']);
			unset($post['litestado_civil_cliente']);
			unset($post['litcarteira_profissional_cliente']);
			unset($post['litnome_empregadora_cliente']);
			unset($post['litprofissao_cliente']);
			unset($post['litcargo_cliente']);
			unset($post['numsalario_cliente']);
			unset($post['litnome_pai_cliente']);
			unset($post['litnome_mae_cliente']);
			unset($post['numidendereco_trabalho']);
			unset($post['litnome_conjuge_cliente']);
			unset($post['litdata_nascimento_conjugue']);
			unset($post['litempregadora_conjuge']);
			unset($post['litprofissao_conjuge']);
			unset($post['litcargo_conjuge']);
			unset($post['numsalario_conjuge']);
			
			//Rerita os campos que estao na tabela clientes condominios
			unset($post['litcnpj']);
			unset($post['numsugestaoReserva']);
			unset($post['numvalAdm']);
			unset($post['numvalFaxina']);
			unset($post['numvalVigia']);
			unset($post['litemissaoPropria']);
			unset($post['numidbanco']);
			unset($post['numagencia']);
			unset($post['numagenciaDigito']);
			unset($post['numconta']);
			unset($post['numcontaDigito']);
			unset($post['numnumeroContrato']);
			unset($post['litadmFinanceira']);
			unset($post['numtaxa_condominio']);
			unset($post['litsugestaoVencimento']);
			unset($post['numsugestaoReserva']);
			unset($post['numcondominio_caixa']);
			unset($post['numcondominio_caixa_Nome']);
			unset($post['numcondominio_caixa_NomeTemp']);
                        
            unset($post['numdesconto_boleto']);
            unset($post['nummulta_boleto']);
            unset($post['numjuros_boleto']);
            unset($post['litinstrucoes_boleto']);

			$this->update($idcliente, $post);

			return(1);

		}

		


		/**
		  método: getById
		  propósito: busca informações
		*/
		function getById($idcliente){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
								CLI.*
							FROM
								{$conf['db_name']}cliente CLI
							WHERE
								 CLI.idcliente = $idcliente ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				if ($get['data_bloqueio_cliente'] != '0000-00-00') $get['data_bloqueio_cliente'] = $form->FormataDataParaExibir($get['data_bloqueio_cliente']); 
				else $get['data_bloqueio_cliente'] = "";
				
				if ($get['data_cadastro_cliente'] != '0000-00-00') $get['data_cadastro_cliente'] = $form->FormataDataParaExibir($get['data_cadastro_cliente']); 
				else $get['data_cadastro_cliente'] = "";
							
				if ($get['valor_contrato_cliente'] != "") $get['valor_contrato_cliente'] = number_format($get['valor_contrato_cliente'],2,",",""); 
					

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
			
			if ($ordem == "") $ordem = " ORDER BY CLI.nome_cliente ASC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			
			($filtro != "") ? ($filtro .= ' AND ') : ($filtro = ' WHERE ') ;
			
			
			$list_sql = "	SELECT
											CLI.*   , RAT.descricao_atividade , MBLOQ.motivo_bloqueio , EDR.idendereco , EDR.idendereco
										FROM
           						{$conf['db_name']}cliente CLI 
												 INNER JOIN {$conf['db_name']}ramo_atividade RAT ON CLI.idramo_atividade=RAT.idramo_atividade 
												 INNER JOIN {$conf['db_name']}motivo_bloqueio MBLOQ ON CLI.idmotivo_bloqueio=MBLOQ.idmotivo_bloqueio 
												 INNER JOIN {$conf['db_name']}endereco EDR ON CLI.idendereco_cliente=EDR.idendereco_cliente 
												 INNER JOIN {$conf['db_name']}endereco EDR ON CLI.idendereco_cobranca=EDR.idendereco_cobranca 
												
										$filtro
										
										CLI.cliente_bloqueado <> '1'
										
										$ordem";

			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

			if($list_q){
				
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){
					
					//insere um índice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);
					
					if ($list['data_bloqueio_cliente'] != '0000-00-00') $list['data_bloqueio_cliente'] = $form->FormataDataParaExibir($list['data_bloqueio_cliente']); 
					else $list['data_bloqueio_cliente'] = "";
					if ($list['data_cadastro_cliente'] != '0000-00-00') $list['data_cadastro_cliente'] = $form->FormataDataParaExibir($list['data_cadastro_cliente']); 
					else $list['data_cadastro_cliente'] = "";
					
					
					if ($list['valor_contrato_cliente'] != "") $list['valor_contrato_cliente'] = number_format($list['valor_contrato_cliente'],2,",",""); 
					
					if ($list['cliente_bloqueado'] == '0') $list['cliente_bloqueado'] = "Não"; 
					else if ($list['cliente_bloqueado'] == '1') $list['cliente_bloqueado'] = "Sim"; 
					if ($list['mesmo_endereco'] == '0') $list['mesmo_endereco'] = "Não"; 
					else if ($list['mesmo_endereco'] == '1') $list['mesmo_endereco'] = "Sim"; 
					if ($list['consumidor_final'] == '0') $list['consumidor_final'] = "Não"; 
					else if ($list['consumidor_final'] == '1') $list['consumidor_final'] = "Sim"; 
					
					$list['telefone_cliente'] = $form->FormataTelefoneParaExibir($list['telefone_cliente']); 
					$list['fax_cliente'] = $form->FormataTelefoneParaExibir($list['fax_cliente']); 
					$list['telefone_cobranca'] = $form->FormataTelefoneParaExibir($list['telefone_cobranca']); 
					
					
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
			
			//Tratamento dos campos numéricos (caso venham vazios)
			if(empty($info['idramo_atividade'])) $info['idramo_atividade'] = 'NULL';
			if(empty($info['idmotivo_bloqueio'])) $info['idmotivo_bloqueio'] = 'NULL';
			if(empty($info['idendereco_cliente'])) $info['idendereco_cliente'] = 'NULL';
			if(empty($info['idendereco_cobranca'])) $info['idendereco_cobranca'] = 'NULL';
			if(empty($info['valor_contrato_cliente'])) $info['valor_contrato_cliente'] = 'NULL';
			//------------------------------------------------------------------------		
			
			//Tratamento de campos numéricos vazios
			if( empty($info['idramo_atividade']) ) $info['idramo_atividade'] = 'NULL';
			
			$set_sql = "  INSERT INTO
		                  {$conf['db_name']}cliente
		                    (
		                    
                                    nome_cliente, 
                                    idramo_atividade, 
                                    telefone_cliente, 
                                    fax_cliente, 
                                    email_cliente, 
                                    site_cliente, 
                                    cliente_bloqueado, 
                                    idmotivo_bloqueio, 
                                    data_bloqueio_cliente, 
                                    idendereco_cliente, 
                                    mesmo_endereco, 
                                    idendereco_cobranca, 
                                    telefone_cobranca, 
                                    observacao_cliente, 
                                    valor_contrato_cliente,
                                    vencimento_boleto_cliente, 
                                    data_cadastro_cliente, 
                                    consumidor_final,
                                    tipo_cliente,
                                    senha_cliente

                                )
		                VALUES
                                (

                                    '" . $info['nome_cliente'] . "',  
                                    " . $info['idramo_atividade'] . ",  
                                    '" . $info['telefone_cliente'] . "',  
                                    '" . $info['fax_cliente'] . "',  
                                    '" . $info['email_cliente'] . "',  
                                    '" . $info['site_cliente'] . "',  
                                    '" . $info['cliente_bloqueado'] . "',  
                                    " . $info['idmotivo_bloqueio'] . ",  
                                    '" . $info['data_bloqueio_cliente'] . "',  
                                    " . $info['idendereco_cliente'] . ",  
                                    '" . $info['mesmo_endereco'] . "',  
                                    " . $info['idendereco_cobranca'] . ",  
                                    '" . $info['telefone_cobranca'] . "',  
                                    '" . $info['observacao_cliente'] . "',  
                                    " . $info['valor_contrato_cliente'] . ",   
                                    '" . $info['vencimento_boleto_cliente'] . "',
                                    '" . $info['data_cadastro_cliente'] . "',  
                                    '" . $info['consumidor_final'] . "',   
                                    '" . $info['tipo_cliente'] . "',
                                    '" . $info['senha_cliente'] . "'
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
		function update($idcliente, $info){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
				
			//inicializa a query
			$update_sql = "	UPDATE
			{$conf['db_name']}cliente
											SET ";

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
					if(empty($valor)) $valor = 'NULL';
					$update_sql .= "$nome_campo = $valor";
				}
					
					
				$cont++;

				//testa se é o último
				if($cont != $cont_validos){
					$update_sql .= ", ";
				}

			}
				

			//completa o sql com a restrição
			$update_sql .= " WHERE  idcliente = $idcliente ";
				
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
		function delete($idcliente){
			
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
													{$conf['db_name']}cliente
												WHERE
													 idcliente = $idcliente ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY nome_cliente ASC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			($filtro != "") ? ($filtro .= ' AND ') : ($filtro = ' WHERE ') ;
			
			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}cliente
										$filtro
										$ordem cliente_bloqueado <> '1' ";

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
		 * Valida token e retorna dados do cliente associado
		 * @param string $token - Token para validação
		 */
		function validaToken($token){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;

			//---------------------

			$consulta = 'SELECT * ' .
						"FROM {$conf['db_name']}cliente " .
						"WHERE token = '$token' LIMIT 1";

			$resultado = $db->query($consulta);

			if($resultado && ($dados_cliente = $db->fetch_array($resultado))){
				return $dados_cliente;
			}
			else{
				return false;
			}
		}

		/**
		 * Lista clientes bloqueados
		 * @return array $return - Retorna array com a lista de clientes bloqueados
		 */
		function listarClientesBloqueados(){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			
			$list_sql = 'SELECT idcliente, nome_cliente, motivo_bloqueio, data_bloqueio_cliente ' . 
						"FROM {$conf['db_name']}cliente 
						 LEFT JOIN {$conf['db_name']}motivo_bloqueio ON (cliente.idmotivo_bloqueio = motivo_bloqueio.idmotivo_bloqueio) " .
						 "WHERE cliente_bloqueado = '1' " .
						 'ORDER BY nome_cliente';

			//manda fazer a paginação
			$list_q = $db->query($list_sql);

			$index = 0;
			if($list_q){
				
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();

				while($list = $db->fetch_array($list_q)){
					$list['index'] = $index;
					$list['data_bloqueio_cliente'] = $form->FormataDataParaExibir($list['data_bloqueio_cliente']);
					$list_return[] = $list;
					$index++;
				}
				
				return $list_return;
			}	
			else{
				$this->err = $falha['listar'];
				return(0);
			}
		}

		/**
		 * Realiza desbloqueio dos clientes cujos IDs foram passados como parâmetro
		 * @param array $clientes_desbloquear - Array contendo IDs dos clientes que devem ser desbloqueados
		 * @return boolean - Retorna true se os clientes foram desbloqueados e retorna false se houve erro
		 */
		public function desbloquearClientes($clientes_desbloquear){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			/// Indica se os desbloqueios foram feitos com sucesso
			$sucesso = true;

			if(is_array($clientes_desbloquear) && !empty($clientes_desbloquear)){

				while(key($clientes_desbloquear) !== null){

					$idcliente = current($clientes_desbloquear);

					if(!$this->update($idcliente, array('litcliente_bloqueado' => '0', 
													'litdata_bloqueio_cliente' => '',
													'numidmotivo_bloqueio' => NULL))){
						$sucesso = false;
					}

					next($clientes_desbloquear);
				}

				return $sucesso;
			}


		}

	} // fim da classe
?>
