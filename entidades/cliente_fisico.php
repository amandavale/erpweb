<?php
	
	class cliente_fisico {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function cliente_fisico(){
			// não faz nada
		}

		/**
		  método: Filtra_Cliente_AJAX
		  propósito: Filtra_Cliente_AJAX
		*/

		function Filtra_Cliente_AJAX ( $filtro, $campoID, $mostraDetalhes ) {

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
			$mostraDetalhes = utf8_decode($mostraDetalhes);

			// campos de controle
			$campoNomeTemp = $campoID . "_NomeTemp";
			$campoFlag = $campoID . "_Flag";

			$list_sql = "	SELECT
											CLI.*, FCLI.cpf_cliente, EDR.*, BAR.*, CID.*, EST.*
										FROM
											{$conf['db_name']}cliente CLI
												 INNER JOIN {$conf['db_name']}cliente_fisico FCLI ON CLI.idcliente=FCLI.idcliente
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
										)
										
										AND  CLI.cliente_bloqueado <> '1' 
											
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
							<td width="10%" class="cabecalho_negrito"><?php echo utf8_encode('Código'); ?></td>
							<td width="60%" class="cabecalho_negrito"><?php echo utf8_encode('Cliente'); ?></td>
							<td class="cabecalho_negrito" align="center"><?php echo utf8_encode('CPF/CNPJ'); ?></td>
						</tr>
					<?php
					
					$filtro = htmlentities($filtro);
					$cont = 0;
					while($list = $db->fetch_array($list_q)){

						//insere um índice na listagem
						$list['index'] = $cont+1;
							if ($mostraDetalhes == 1) {
								$list['info_cliente'] = "
									<table width=95% align=center>
												<tr>
               						<th align=center>Cliente</th>
													<th align=center>Cidade</th>
													<th align=center>Telefone</th>
												</tr>
	       					      <tr>
														<td><a class=menu_item href=" . $conf['addr'] . "/admin/cliente_fisico.php?ac=editar&idcliente=" . $list['idcliente'] . ">" . htmlentities($list['nome_cliente']) . "</a></td>
														<td><a class=menu_item href=" . $conf['addr'] . "/admin/cliente_fisico.php?ac=editar&idcliente=" . $list['idcliente'] . ">" . htmlentities($list['nome_cidade']) . " / " . $list['sigla_estado'] . "</a></td>
														<td><a class=menu_item href=" . $conf['addr'] . "/admin/cliente_fisico.php?ac=editar&idcliente=" . $list['idcliente'] . ">" . $list['telefone_cliente'] . "</a></td>
													</tr>

									</table>";

								$list['info_cliente'] = ereg_replace("(\r\n|\n|\r)", "", $list['info_cliente']);
							}

        				if (strlen($filtro) > 1 )$list['nome_cliente'] = htmlentities($list['nome_cliente']);
        				
						if ($list['cpf_cliente'] != "") $list['cpf_cnpj'] = $list['cpf_cliente'];
						else $list['cpf_cnpj'] = $list['cnpj_cliente'];

						if ($list['cliente_bloqueado'] == "1") $list['cliente_bloqueado'] = "SIM";
						else $list['cliente_bloqueado'] = "NÃO";


						// coloca em negrito a string que foi encontrada na palavra
						$list['idcliente_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['idcliente']);
						$list['nome_cliente_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['nome_cliente']);
						$list['cpf_cnpj_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['cpf_cnpj']);

						?>
						<tr onselect="
							this.text.value = '<?php echo utf8_encode($list['nome_cliente']); ?>';
							$('<?php echo $campoNomeTemp; ?>').value = '<?php echo utf8_encode($list['nome_cliente']); ?>';
							$('<?php echo $campoID; ?>').value = '<?php echo utf8_encode($list['idcliente']); ?>';
							$('<?php echo $campoFlag; ?>').className = 'selecionou';

							<?php if ($mostraDetalhes == 1) ?>
							$('dados_cliente').innerHTML = '<?php echo utf8_encode($list['info_cliente']); ?>';

						">
							<td class="tb_bord_baixo"><?php echo ($list['idcliente_negrito']); ?></td>
							<td class="tb_bord_baixo"><?php echo utf8_encode($list['nome_cliente_negrito']); ?></td>
							<td class="tb_bord_baixo" align="center">&nbsp;<?php echo utf8_encode($list['cpf_cnpj_negrito']); ?></td>
						</tr>
						<?php

	          $cont++;
					}

					// verifica a paginação
					$paginacao = "";
					if ($pg > 0) $paginacao .= "<a href='?page=" . ($pg - 1) . "' style='float:left' class='page_up'>" . utf8_encode('Anterior') . "</a>";
					$paginacao .= "<a href='?page=" . ($pg + 1) .  "' style='float:right'  class='page_down'>" . utf8_encode('Pr&oacute;ximo') . "</a>";

				}
				// Nenhum registro foi encontrado
				else {
					?>
					<table width="100%" cellpadding="5" cellspacing="2">
						<tr onselect="" class="cabecalho">
							<td width="70%" class="cabecalho_negrito"><?php echo utf8_encode($conf['listar']); ?></td>
						</tr>
					<?php

					// verifica a paginação
					$paginacao = "";
					if ($pg > 0) $paginacao .= "<a href='?page=" . ($pg - 1) . "' style='float:left' class='page_up'>" . utf8_encode('Anterior') . "</a>";
				}

			}
			else{
				?>
				<table width="100%" cellpadding="5" cellspacing="2">
					<tr onselect="" class="cabecalho">
						<td width="70%" class="cabecalho_negrito"><?php echo utf8_encode($falha['listar']); ?></td>
					</tr>
				<?php
			}

			// Encerra a tabela e coloca a paginação
			echo "</table>";
			if ($paginacao != "") echo $paginacao;

		}






		/**
		  método: Verifica_CPF_Duplicado
		  propósito: Verifica_CPF_Duplicado
		*/
		function Verifica_CPF_Duplicado($CPF, $idcliente = ""){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$filtro = "";

			if ($idcliente != "") $filtro = " AND FCLI.idcliente <> $idcliente ";

			$get_sql = "	SELECT
											FCLI.*
										FROM
											{$conf['db_name']}cliente_fisico FCLI
										WHERE
											 FCLI.cpf_cliente = '$CPF'

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
		  método: AtualizaClienteFisico
		  propósito: AtualizaClienteFisico
		*/
		function AtualizaClienteFisico($idcliente, $post){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// retira os campos que nao estao na tabela clientes
			unset($post['litnome_cliente']);
			unset($post['numidramo_atividade']);
			unset($post['littelefone_cliente']);
			unset($post['litfax_cliente']);
			unset($post['litemail_cliente']);
			unset($post['litsite_cliente']);
			unset($post['litcliente_bloqueado']);
			unset($post['numidmotivo_bloqueio']);
			unset($post['litdata_bloqueio_cliente']);
			unset($post['numidendereco_cliente']);
			unset($post['litmesmo_endereco']);
			unset($post['numidendereco_cobranca']);
			unset($post['littelefone_cobranca']);
			unset($post['litobservacao_cliente']);
			unset($post['numvalor_contrato_cliente']);
			unset($post['litdata_cadastro_cliente']);
			unset($post['litconsumidor_final']);
			unset($post['littipo_cliente']);
                        unset($post['litsenha_cliente']);

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
                                                FCLI.*
                                        FROM
                                                {$conf['db_name']}cliente_fisico FCLI
                                        WHERE
                                                    FCLI.idcliente = $idcliente ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				if ($get['data_nascimento_cliente'] != '0000-00-00') $get['data_nascimento_cliente'] = $form->FormataDataParaExibir($get['data_nascimento_cliente']); 
				else $get['data_nascimento_cliente'] = "";
				
				if ($get['data_nascimento_conjugue'] != '0000-00-00') $get['data_nascimento_conjugue'] = $form->FormataDataParaExibir($get['data_nascimento_conjugue']); 
				else $get['data_nascimento_conjugue'] = "";
					
					
				if ($get['vencimento_boleto_cliente'] != '0000-00-00') $get['vencimento_boleto_cliente'] = $form->FormataDataParaExibir($get['vencimento_boleto_cliente']); 
				else $get['vencimento_boleto_cliente'] = "";
					
					
				
				if ($get['salario_cliente'] != "") $get['salario_cliente'] = number_format($get['salario_cliente'],2,",",""); 
				if ($get['salario_conjuge'] != "") $get['salario_conjuge'] = number_format($get['salario_conjuge'],2,",",""); 
				

                                //Flag para indicar que o clietne é Pessoa física
                                $get['pessoa_fisica'] = true; 
				
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
											FCLI.*   , CLI.nome_cliente, CLI.telefone_cliente , EDR.idendereco ,CLI.email_cliente
										FROM
           						{$conf['db_name']}cliente_fisico FCLI 
												 INNER JOIN {$conf['db_name']}cliente CLI ON FCLI.idcliente=CLI.idcliente 
												 INNER JOIN {$conf['db_name']}endereco EDR ON FCLI.idendereco_trabalho=EDR.idendereco
												
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
					
					if ($list['data_nascimento_cliente'] != '0000-00-00') $list['data_nascimento_cliente'] = $form->FormataDataParaExibir($list['data_nascimento_cliente']); 
					else $list['data_nascimento_cliente'] = "";
					if ($list['data_nascimento_conjugue'] != '0000-00-00') $list['data_nascimento_conjugue'] = $form->FormataDataParaExibir($list['data_nascimento_conjugue']); 
					else $list['data_nascimento_conjugue'] = "";
					
					
					if ($list['salario_cliente'] != "") $list['salario_cliente'] = number_format($list['salario_cliente'],2,",",""); 
					if ($list['salario_conjuge'] != "") $list['salario_conjuge'] = number_format($list['salario_conjuge'],2,",",""); 
					
					if ($list['estado_civil_cliente'] == 'S') $list['estado_civil_cliente'] = "Solteiro"; 
					else if ($list['estado_civil_cliente'] == 'C') $list['estado_civil_cliente'] = "Casado"; 
					
					$list['telefone_cliente'] = $form->FormataTelefoneParaExibir($list['telefone_cliente']);
					$list['celular_cliente'] = $form->FormataTelefoneParaExibir($list['celular_cliente']); 
					
								
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
			if(empty($info['salario_cliente'])) $info['salario_cliente'] = 'NULL';
			if(empty($info['idendereco_trabalho'])) $info['idendereco_trabalho'] = 'NULL';
			if(empty($info['salario_conjuge'])) $info['salario_conjuge'] = 'NULL';
			//---------------------------------
			
			
			
			$set_sql = "  INSERT INTO
		                  {$conf['db_name']}cliente_fisico
		                    (
    
									idcliente, 
									cpf_cliente, 
									sexo_cliente,
									identidade_cliente, 
									data_nascimento_cliente, 
									tel_residencial_cliente, 
									celular_cliente, 
									estado_civil_cliente, 
									carteira_profissional_cliente, 
									nome_empregadora_cliente, 
									profissao_cliente, 
									cargo_cliente, 
									salario_cliente, 
									nome_pai_cliente, 
									nome_mae_cliente, 
									idendereco_trabalho, 
									nome_conjuge_cliente, 
									data_nascimento_conjugue, 
									empregadora_conjuge, 
									profissao_conjuge, 
									cargo_conjuge, 
									salario_conjuge  
								
								)
				                VALUES
				                    (
				                    
				                    " . $info['idcliente'] . ",  
									'" . $info['cpf_cliente'] . "',  
									'" . $info['sexo_cliente'] . "',
									'" . $info['identidade_cliente'] . "',  
									'" . $info['data_nascimento_cliente'] . "',  
									'" . $info['tel_residencial_cliente'] . "',  
									'" . $info['celular_cliente'] . "',  
									'" . $info['estado_civil_cliente'] . "',  
									'" . $info['carteira_profissional_cliente'] . "',  
									'" . $info['nome_empregadora_cliente'] . "',  
									'" . $info['profissao_cliente'] . "',  
									'" . $info['cargo_cliente'] . "',  
									" . $info['salario_cliente'] . ",  
									'" . $info['nome_pai_cliente'] . "',  
									'" . $info['nome_mae_cliente'] . "',  
									" . $info['idendereco_trabalho'] . ",  
									'" . $info['nome_conjuge_cliente'] . "',  
									'" . $info['data_nascimento_conjugue'] . "',
									'" . $info['empregadora_conjuge'] . "',  
									'" . $info['profissao_conjuge'] . "',  
									'" . $info['cargo_conjuge'] . "',  
									" . $info['salario_conjuge'] . "   
									
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
												{$conf['db_name']}cliente_fisico
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
				elseif($tipo_campo == "num")
					$update_sql .= "$nome_campo = $valor";
					
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
													{$conf['db_name']}cliente_fisico
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY idcliente ASC") {
			
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
								{$conf['db_name']}cliente_fisico

								$filtro
								 CLI.cliente_bloqueado <> '1' 
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
		

		/**
		  método: Busca_Generica
		  propósito: Busca_Generica
		*/

		
		function Busca_Generica ( $pg, $rppg, $busca = "", $ordem = "", $url = ""){

			if ($ordem == "") $ordem = " ORDER BY CLI.nome_cliente ";

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT
											CLI.*, RAM.descricao_atividade, FCLI.*
										FROM
           						{$conf['db_name']}cliente CLI
           						INNER JOIN {$conf['db_name']}ramo_atividade RAM ON CLI.idramo_atividade=RAM.idramo_atividade
           						INNER JOIN {$conf['db_name']}cliente_fisico FCLI ON CLI.idcliente=FCLI.idcliente
           					WHERE
											(
												UPPER(RAM.descricao_atividade) LIKE UPPER('%{$busca}%') OR
				           					 	UPPER(CLI.nome_cliente) LIKE UPPER('%{$busca}%') OR
				           					 	UPPER(FCLI.cpf_cliente) LIKE UPPER('%{$busca}%') OR
				           					 	UPPER(CLI.email_cliente) LIKE UPPER('%{$busca}%')
											)
											
											 CLI.cliente_bloqueado <> '1' 
											
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
		  método: Busca_Parametrizada
		  propósito: Busca_Parametrizada
		*/

		function Busca_Parametrizada ( $pg, $rppg, $filtro_where = " ", $ordem = "", $url = ""){

			if ($ordem == "") $ordem = " ORDER BY CLI.nome_cliente ";

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------


			if ($filtro_where != "") $filtro_where = " WHERE ( " . $filtro_where . " ) AND  CLI.cliente_bloqueado <> '1' ";
			else $filtro_where = "WHERE  CLI.cliente_bloqueado <> '1'";



			$list_sql = "	SELECT
      								CLI.*, RAT.descricao_atividade, FCLI.*,
      								EDR.*, BAR.*, CID.*, EST.*
      							FROM
             					{$conf['db_name']}cliente CLI
             					LEFT JOIN {$conf['db_name']}ramo_atividade RAT ON CLI.idramo_atividade=RAT.idramo_atividade
             					INNER JOIN {$conf['db_name']}cliente_fisico FCLI ON CLI.idcliente=FCLI.idcliente
                      LEFT OUTER JOIN {$conf['db_name']}endereco EDR ON CLI.idendereco_cliente=EDR.idendereco
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
					
					$list['telefone_cliente'] = $form->FormataTelefoneParaExibir($list['telefone_cliente']);
					$list['celular_cliente'] = $form->FormataTelefoneParaExibir($list['celular_cliente']);
					
					$list['endereco'] = null;
					if(!empty($list['numero'])) $list['logradouro'] .= ', '.$list['numero'];
					if(!empty($list['logradouro']))  $endereco[] = $list['logradouro'];
					if(!empty($list['nome_bairro'])) $endereco[] = 'Bairro '.$list['nome_bairro'];
					if(!empty($list['nome_cidade'])) $endereco[] =  $list['nome_cidade'] . '/'. $list['sigla_estado'] ;
					
					if(count($endereco)) $list['endereco'] = strtoupper(implode(' - ',$endereco));
					$endereco = null;
					
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
