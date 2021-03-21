<?php

	class apartamento {

		var $err;

		/**
	    * construtor da classe
	  	*/
		Function apartamento(){
			// não faz nada
		}


		/**
		  método: Filtra_Cliente_AJAX
		  propósito: Filtra_Cliente_AJAX
		*/

		function Filtra_Apartamento_AJAX ( $filtro, $campoID, $mostraDetalhes ) {

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			
			require_once("cliente.php");
			$cliente = new cliente();

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
								APTO.*
										FROM
											{$conf['db_name']}apartamento APTO

										WHERE
											(
											  UPPER(APTO.apto) LIKE UPPER('%{$filtro}%')
											  OR UPPER(APTO.situacao) LIKE UPPER('%{$filtro}%')
											 )
											 AND(
											  APTO.idcliente = ".$_SESSION['idcliente']."
											 )
										
										-- AND CLI.cliente_bloqueado <> '1'
											  	
										ORDER BY
											APTO.apto ASC ";


			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);


			if($list_q){

				// testa se retornou algum registro
				if ($db->num_rows($list_q) > 0) {

					?>
					<table width="100%" cellpadding="5" cellspacing="2">
						<tr onselect="" class="cabecalho">
							<td width="10%" class="cabecalho_negrito"><?php echo ('Apto.'); ?></td>
							<td width="45%" class="cabecalho_negrito"><?php echo utf8_encode('Proprietário'); ?></td>
							<td width="45%" class="cabecalho_negrito"><?php echo utf8_encode('Inquilino'); ?></td>
						</tr>
					<?php

					$cont = 0;
					while($list = $db->fetch_array($list_q)){

						//insere um índice na listagem
						$list['index'] = $cont+1;
						
						
					 	if(!empty($list['idproprietario']))	$proprietario = $cliente->getById($list['idproprietario']);
						if(!empty($list['idmorador'])) $morador = $cliente->getById($list['idmorador']);
						
						/*
						if ($list['situacao'] == 'V'){	
						
							$list['ocupacao'] = 'Vazio';
							
						}
      					elseif($list['situacao'] == 'P'){
						
							empty($list['idproprietario']) ? $list['ocupacao'] = "Ocupado pelo proprietário" : $list['ocupacao'] = $propietario['nome_cliente'];	
								
						}
						elseif($list['situacao'] == 'I'){
						
							empty($list['idmorador']) ? $list['ocupacao'] = "Ocupado pelo inquilino" : $list['ocupacao'] = $morador['nome_cliente'];	
						
						}
						*/

						if ($mostraDetalhes == 1) {


							$list['info_apartamento'] = "
												
													<table width=95% align=center>
														<tr>
			            									<th align=center>Apartamento</th>
															<th align=center>Proprietário</th>
															<th align=center>Inquilino</th>
														</tr>
		       					      					<td align=center><a class=menu_item href=" . $conf['addr'] . "/admin/apartamento.php?ac=editar&idapartamento=" . $list['idapartamento'] . ">" . $list['apto'] . "</a></td>
														<td align=center><a class=menu_item href=" . $conf['addr'] . "/admin/apartamento.php?ac=editar&idapartamento=" . $list['idapartamento'] . ">" . $proprietario['nome_cliente'] . "</a></td>
														<td align=center><a class=menu_item href=" . $conf['addr'] . "/admin/apartamento.php?ac=editar&idapartamento=" . $list['idapartamento'] . ">" . $morador['nome_cliente'] . "</a></td>
													</table>
													";


						}

                        
                        
						// coloca em negrito a string que foi encontrada na palavra
						$list['apto_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['apto']);
						$list['proprietario_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $proprietario['nome_cliente']);
						$list['morador_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $morador['nome_cliente']);


						$list['info_apartamento'] = ereg_replace("(\r\n|\n|\r)", "", $list['info_apartamento']);

						?>
						<tr onselect="
							this.text.value = '<?php echo utf8_encode($list['apto']); ?>';
							$('<?php echo $campoNomeTemp; ?>').value = '<?php echo utf8_encode($list['apto']); ?>';
							$('<?php echo $campoID; ?>').value = '<?php echo utf8_encode($list['idapartamento']); ?>';
							$('<?php echo $campoFlag; ?>').className = 'selecionou';

							<?php if ($mostraDetalhes == 1) ?>
							$('dados_apartamento').innerHTML = '<?php echo utf8_encode($list['info_apartamento']); ?>';

						">
							<td class="tb_bord_baixo"><?php echo ($list['apto_negrito']); ?></td>
							<td class="tb_bord_baixo"><?php echo utf8_encode($list['proprietario_negrito']); ?></td>
							<td class="tb_bord_baixo"><?php echo utf8_encode($list['morador_negrito']); ?></td>
						</tr>
						<?php

	          			$cont++;
					}

					// verifica a paginação
					$paginacao = "";
					if ($pg > 0) $paginacao .= "<a href='?page=" . ($pg - 1) . "' style='float:left' class='page_up'>" . utf8_encode('Anterior') . "</a>";
					$paginacao .= "<a href='?page=" . ($pg + 1) .  "' style='float:right'  class='page_down'>" . utf8_encode('Próximo') . "</a>";

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
		  método: Verifica_APTO_Duplicado
		  propósito: Verificar se existe apartamento duplicado
		*/
		Function Verifica_APTO_Duplicado($apto, $idapartamento = ""){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------


			$filtro = "";
			$apto = trim($apto);

   			if ($idapartamento != "") $filtro = " AND APTO.idapartamento <> $idapartamento ";

			$get_sql = "	SELECT
								APTO.*
							FROM
								{$conf['db_name']}apartamento APTO
							WHERE
								 APTO.apto LIKE '$apto'
								 AND APTO.idcliente=".$_SESSION['idcliente']."

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
		Function getAptoByOcupacao($idCliente, $idCondominio){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			


			$get_sql = "	SELECT
								APTO.apto
							FROM
								{$conf['db_name']}apartamento APTO left join
								{$conf['db_name']}cliente CONDOMINIO ON (APTO.idcliente = CONDOMINIO.idcliente)
											
							WHERE
								 
								 (
								 	(APTO.situacao = 'P'  AND APTO.idproprietario = $idCliente) OR
								 	(APTO.situacao = 'I'  AND APTO.idmorador = $idCliente)
								 ) AND
								 CONDOMINIO.idcliente = $idCondominio";
								
			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);


				//retorna o vetor associativo com os dados
				return $get;
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
		Function getById($idapartamento){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$get_sql = "	SELECT
								APTO.*
							FROM
								{$conf['db_name']}apartamento APTO
							WHERE
								 APTO.idapartamento = $idapartamento ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);



				if ($get['custoFixo'] != "") $get['custoFixo'] = number_format($get['custoFixo'],2,",","");
					if ($get['fundoReserva'] != "") $get['fundoReserva'] = number_format($get['fundoReserva'],2,",","");


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

		Function make_list( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){

			//A função LPAD está sendo usada para manter a ordenação correta, pois o campo "apto" é do tipo varchar
			if ($ordem == "") $ordem = " ORDER BY LPAD(APTO.apto,8,'0') "	;
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $smarty;

			global $cliente_fisico;

			if(!$cliente_fisico){
				require_once dirname(__FILE__) . '/cliente_fisico.php';
				$cliente_fisico = new cliente_fisico();
			}
			
			//---------------------

			$list_sql = "	SELECT
									APTO.* , PROP.nome_cliente as nome_proprietario, PROP.telefone_cliente as telefone_proprietario,
									PROP.idcliente as idproprietario, INQ.idcliente as idinquilino, 
									INQ.nome_cliente as nome_inquilino, INQ.telefone_cliente as telefone_inquilino
								FROM
									{$conf['db_name']}apartamento APTO
								LEFT JOIN cliente PROP on (PROP.idcliente = APTO.idproprietario)
								LEFT JOIN cliente INQ on (INQ.idcliente = APTO.idmorador)

								$filtro
								$ordem";


			if($pg === false){
				//Faz a consulta sem paginação 
				$list_q = $db->query($list_sql);
				
			}
    		else{		
    			
				//manda fazer a paginação
				$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);
    		}
    		
    		
			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;

				/// Armazena CPFs dos clientes para não precisar buscar no banco de dados mais de uma vez.
				$cpfs_clientes = array();

				while($list = $db->fetch_array($list_q)){

					//insere um índice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);

					//Formata os dados
					$list['telefone_proprietario'] = $form->FormataTelefoneParaExibir($list['telefone_proprietario']);
					$list['telefone_inquilino'] = $form->FormataTelefoneParaExibir($list['telefone_inquilino']);

					/// Busca CPF do proprietário
					$list['cpf_proprietario'] = '';
					if($list['idproprietario']){
						if(!isset($cpfs_clientes[$list['idproprietario']])){
							$dados_cliente = $cliente_fisico->getById($list['idproprietario']);
							$cpfs_clientes[$list['idproprietario']] = $dados_cliente['cpf_cliente'];
						}

						$list['cpf_proprietario'] = $cpfs_clientes[$list['idproprietario']];
					}


					/// Busca CPF do inquilino
					$list['cpf_inquilino'] = '';
					if($list['idinquilino']){
						if(!isset($cpfs_clientes[$list['idinquilino']])){
							$dados_cliente = $cliente_fisico->getById($list['idinquilino']);
							$cpfs_clientes[$list['idinquilino']] = $dados_cliente['cpf_cliente'];
						}

						$list['cpf_inquilino'] = $cpfs_clientes[$list['idinquilino']];
					}

					$list['custoFixo'] = $form->FormataMoedaParaExibir($list['custoFixo']);
					
					$list['sit_apt'] = $list['situacao']; //Situação abreviada para uso em campos hidden
					
					if($list['situacao'] == 'P') $list['situacao'] = 'Ocupado pelo proprietário';
					elseif($list['situacao'] == 'I') $list['situacao'] = 'Ocupado pelo inquilino';
					else $list['situacao'] = 'Vazio';

					$apto_ids[] = $list['idapartamento']; 
					
					$list_return[$list['apto']] = $list;
					//$list_return[] = $list;
					$cont++;
				}

				$smarty->assign("apto_ids", implode('|', $apto_ids));



				return $list_return;

			}
			else{
				$this->err = $falha['listar'];
				return(0);
			}
		}

		
		
		
		
		/*Verifica se já foi gerada a taxa de condomínio para determinado mes
		
		*@param: int $idapartamento - ID do apartamento em questão
		*		 int $mes - 2 dígitos
		*		 int $ano - 4 dígitos
		*
		*@return Caso exista, retora um array com o id e a data de vencimento
		*		 Caso não exista, retorna nulo
		*/

		/**
		 * Verificar se o método verificaTaxaCondominioGerada está substituindo este método satisfatoriamente.
		 */
		
		function chk_taxa_condominio_gerada($idapartamento, $mes, $ano, $dia = null){
			
			global $conf, $db, $form;
			
			
			if($dia != null){
				
				$filtro = "data_vencimento = '$ano-$mes-$dia' ";			
			}
			else{

			   $data_de = "$ano-$mes-01";		    
				$data_ate = "$ano-$mes-".$form->UltimoDiaMes($mes,$ano);

				$filtro = " data_vencimento BETWEEN '$data_de' AND '$data_ate' ";

			}




			$sql = "SELECT 
							idmovimento, data_movimento FROM {$conf['db_name']}movimento
					  WHERE 
							idapartamento = $idapartamento AND 
						$filtro";

			$sql_rs = $db->query($sql);
			
			if($db->num_rows($sql_rs) > 0) return $db->fetch_all($sql_rs);
			else return null;
					  
		}
		


		/**
		 * Método criado para substituir o método chk_taxa_condominio_gerada.
		 * A verificação de taxa de condomínio gerada vai ser de acordo com o campo data_movimento,
		 * não de acordo com o campo data_vencimento.
		 */
		function verificaTaxaCondominioGerada($idapartamento, $mes, $ano, $dia = null){
			
			global $conf, $db, $form;
			
			
			if($dia != null){
				
				$filtro = "data_movimento = '$ano-$mes-$dia' ";			
			}
			else{

			   $data_de = "$ano-$mes-01";		    
				$data_ate = "$ano-$mes-".$form->UltimoDiaMes($mes,$ano);

				$filtro = " data_movimento BETWEEN '$data_de' AND '$data_ate' ";

			}

			$sql = "SELECT 
							idmovimento, data_movimento FROM {$conf['db_name']}movimento
					  WHERE 
							idapartamento = $idapartamento AND 
						$filtro";

			$sql_rs = $db->query($sql);
			
			if($db->num_rows($sql_rs) > 0) return $db->fetch_all($sql_rs);
			else return null;
					  
		}



		/**
		 * Verifica se já foram gerados movimentos para um condomínio em um determinado 
		 * período
		 */
		function verificaTaxaGeradaPorCondominio($idCondominio, $mes, $ano, $dia = null){
			
			global $conf, $db, $form;
			
			
			if($dia != null){
				
				$filtro = "data_movimento = '$ano-$mes-$dia' ";
			}
			else{

			   $data_de = "$ano-$mes-01";		    
				$data_ate = "$ano-$mes-".$form->UltimoDiaMes($mes,$ano);

				$filtro = " data_movimento BETWEEN '$data_de' AND '$data_ate' ";

			}

			$sql = "SELECT 
							count(1) as 'total' 
					FROM {$conf['db_name']}movimento
					WHERE 
						idcliente_destino = $idCondominio AND 
						$filtro";

			$sql_rs = $db->query($sql);

			$movimentos_gerados = $db->fetch_array($sql_rs);


			$sql = "SELECT 
							count(1) as 'total' 
					FROM {$conf['db_name']}apartamento
					WHERE 
						idcliente = $idCondominio";

			$sql_rs = $db->query($sql);

			$total_apartamentos = $db->fetch_array($sql_rs);

			return array('total_movimentos' => $movimentos_gerados['total'], 
							'total_apartamentos' => $total_apartamentos['total']);


				//return $db->fetch_all($sql_rs);
			//else return null;
					  
		}


		/**
			método: set
		  propósito: inclui novo registro
		*/

		Function set($info){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			
			//Tratamento de campos numéricos nulos
			if (empty($info['idmorador'])) $info['idmorador'] = 'NULL';
			if (empty($info['idproprietario'])) $info['idproprietario'] = 'NULL';
			if (empty($info['idcliente'])) $info['idcliente'] = 'NULL';
							
							

			$set_sql = "  INSERT INTO
		                  {$conf['db_name']}apartamento
		                  (
							idmorador,
							idproprietario,
							idcliente,
							apto,
							situacao,
							fracaoIdeal,
							custoFixo,
							fundoReserva,
							observacao

						)
		                VALUES
		                    (
							" . $info['idmorador'] . ", 
							" . $info['idproprietario'] . ",
		                    " . $info['idcliente'] . ",
							'" . $info['apto'] . "',
							'" . $info['situacao'] . "',
							" . $info['fracaoIdeal'] . ",
							" . $info['custoFixo'] . ",
							" . $info['fundoReserva'] . ",
							'" . $info['observacao'] . "'

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
		function update($idapartamento, $info){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			//inicializa a query
			$update_sql = "	UPDATE
								{$conf['db_name']}apartamento
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
			$update_sql .= " WHERE  idapartamento = $idapartamento ";

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
		Function delete($idapartamento){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// conjunto de dependências geradas
			$sql = "SELECT
								 *
							FROM
								{$conf['db_name']}ocupacao
							WHERE
								 idapartamento = $idapartamento ";
								 
			//**** DESCOMENTAR AS DUAS LINHAS A SEGUIR QUANDO O CADASTRO DE OCUPAÇÃO ESTIVER PRONTO *****\\
			//$verifica_q = $db->query($sql);
			//$n0 = $db->num_rows($verifica_q);
			$n0 = 0;

			//---------------------


			// verifica se pode excluir
			if (1 && $n0==0) {



				$delete_sql = "	DELETE FROM
									{$conf['db_name']}apartamento
								WHERE
									 idapartamento = $idapartamento ";
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
				$this->err = "Este registro não pode ser excluído, pois existem registros relacionadas a ele.";
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


			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}cliente_juridico
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

			if ($ordem == "") $ordem = " ORDER BY CLI.nome_cliente ";

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT
											CLI.*, RAM.descricao_atividade, JCLI.*
										FROM
           						{$conf['db_name']}cliente CLI
           						INNER JOIN {$conf['db_name']}ramo_atividade RAM ON CLI.idramo_atividade=RAM.idramo_atividade
           						INNER JOIN {$conf['db_name']}cliente_juridico JCLI ON CLI.idcliente=JCLI.idcliente
           					WHERE
											(
											UPPER(RAM.descricao_atividade) LIKE UPPER('%{$busca}%') OR
           					 	UPPER(CLI.nome_cliente) LIKE UPPER('%{$busca}%') OR
           					 	UPPER(JCLI.cnpj_cliente) LIKE UPPER('%{$busca}%') OR
           					 	UPPER(CLI.email_cliente) LIKE UPPER('%{$busca}%')
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

		function Busca_Parametrizada ( $pg, $rppg, $filtro_where = "", $ordem = "", $url = ""){

			if ($ordem == "") $ordem = " ORDER BY CLI.nome_cliente ";

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------


			if ($filtro_where != "")
				$filtro_where = " WHERE ( " . $filtro_where . " ) ";



			$list_sql = "	SELECT
											CLI.*, RAT.descricao_atividade, JCLI.*
										FROM
           						{$conf['db_name']}cliente CLI
           						INNER JOIN {$conf['db_name']}ramo_atividade RAT ON CLI.idramo_atividade=RAT.idramo_atividade
           						INNER JOIN {$conf['db_name']}cliente_juridico JCLI ON CLI.idcliente=JCLI.idcliente

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
        			CLI.*, CCLI.cnpj, EDR.*, BAR.*, CID.*, EST.*
										FROM
											{$conf['db_name']}cliente CLI
             INNER JOIN {$conf['db_name']}cliente_condominio CCLI ON CLI.idcliente=CCLI.idcliente
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
									        UPPER(CCLI.cnpj) LIKE UPPER('%{$filtro}%')
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
							<td width="10%" class="cabecalho_negrito"><?php echo ('C&oacute;digo'); ?></td>
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
		       					      					<td><a class=menu_item href=" . $conf['addr'] . "/admin/apartamento.php?ac=selecionar_condominio&idcliente=" . $list['idcliente'] . ">" . $list['nome_cliente'] . "</a></td>
														<td><a class=menu_item href=" . $conf['addr'] . "/admin/apartamento.php?ac=selecionar_condominio&idcliente=" . $list['idcliente'] . ">" . $list['nome_cidade'] . " / " . $list['sigla_estado'] . "</a></td>
              											<td><a class=menu_item href=" . $conf['addr'] . "/admin/apartamento.php?ac=selecionar_condominio&idcliente=" . $list['idcliente'] . ">" . $list['telefone_cliente'] . "</a></td>
													</table>

													";

							$list['info_cliente'] = ereg_replace("(\r\n|\n|\r)", "", $list['info_cliente']);
						}

						if (strlen($filtro) > 1 )$list['nome_cliente'] = htmlentities($list['nome_cliente']);

						if ($list['cliente_bloqueado'] == "1") $list['cliente_bloqueado'] = "SIM";
						else $list['cliente_bloqueado'] = "NÃO";


						// coloca em negrito a string que foi encontrada na palavra
      					$list['idcliente_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['idcliente']);
						$list['nome_cliente_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['nome_cliente']);
						$list['cpf_cnpj_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['cnpj']);

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
		método: chk_condominio_selecionado
	  propósito: Verifica se o usuário escolheu um condomínio
	*/
    function chk_condominio_selecionado(){
    	
    	// variáveis globais
		global $conf;
			

		if (!$_SESSION['idcliente']){
			$this->err = 'Ainda não foi selecionado um condomínio. Por favor, selecione um na opção <a href="'.$conf['addr'].'/admin/apartamento.php?ac=selecionar_condominio">Selecionar Condomínio</a>';
			return false;
		}
		else{
			return true;
		}	

	}
	



	} // fim da classe
?>
