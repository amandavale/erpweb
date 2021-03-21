<?php

	class comunicado {

		var $err;

		/**
	    * construtor da classe
	  	*/
		Function comunicado(){
			// não faz nada
		}


		/**
		  método: getById
		  propósito: busca informações
		*/
		Function getById($idcomunicado){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$get_sql = "	SELECT
								*
							FROM
								{$conf['db_name']}comunicado
							WHERE
								 idcomunicado = $idcomunicado ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				$get['criacao'] = $form->FormataDataHoraParaExibir($get['criacao']);

				//retorna o vetor associativo com os dados
				return $get;
			}
			else{ //deu erro no banco de dados
			
				$this->err = $falha['listar'];
				return(0);
			}

		}
		
		
		/**
		 * Busca arquivos de um comunicado e retorna os nomes em um array
		 * @param integer $id_comunicado - ID do comunicado
		 */
		function buscaArquivosComunicado($id_comunicado){
			
			global $conf;
			
			$arquivos_comunicado = array();
			
			foreach (glob($conf['path'] . "/common/comunicados/{$id_comunicado}_*") as $arquivo) {
				
				/// pega o nome do arquivo sem o prefixo "<idcomunicado>_" que é utilizado
				/// para identificar os arquivos do comunicado
				$arquivo = basename($arquivo);
				$arquivos_comunicado[] = substr($arquivo,(strpos($arquivo,'_')+1));
			}			
			
			return $arquivos_comunicado;
		}
		

		/**
		 * Busca arquivos de um comunicado e retorna os nomes em um array
		 * @param integer $id_comunicado - ID do comunicado
		 */
		function apagaArquivosComunicado($id_comunicado){
				
			global $conf;
				
			foreach (glob($conf['path'] . "/common/comunicados/{$id_comunicado}_*") as $arquivo) {

				unlink($arquivo);
			}
				
		}
		
		
		/**
		 * método: set
		 * propósito: inclui novo registro
		 */

		Function set($info){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$set_sql = "  INSERT INTO
		                  {$conf['db_name']}comunicado
		                  (
		                  	titulo,
		                  	descricao,
		                  	usuario,
		                  	notificar_email
						)
		                VALUES
		                    (
							'" . $info['titulo'] . "', 
							'" . $info['descricao'] . "',
		                    '" . $_SESSION['usr_nom'] . "',
							'" . $info['notificar_email'] . "'
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
		 * Salva arquivos e associa a um comunicado
		 * @param integer $id_comunicado
		 * @param array $arquivo - Array $_FILES recebido do formulário, com dados do arquivo
		 */
		function incluiArquivosComunicado($id_comunicado, $arquivo){
			
			global $conf;

			/// se o registro foi inserido verifica se foram fornecidos arquivos
			foreach($arquivo['arquivo']['name'] as $indice => $nome){
				if($nome){
					 
					if($arquivo['arquivo']['error'] !== 0){
						$err[] = sprintf($falha['arquivo_nao_reconhecido'],' número ' . ($indice+1));
					}
					 
					if(!move_uploaded_file(
							$arquivo['arquivo']['tmp_name'][$indice],
							$conf['path'] . '/common/comunicados/' . $id_comunicado . '_' . $nome)){
			
						$err[] = sprintf($faha['salvar_arquivo'],$nome);
					}
				}
			}
		}
		

		/**
		  método: update
		  propósito: atualiza os dados

		  1) o vetor $info deve conter todos os campos tabela a serem atualizados
			2) a variável $id deve conter o código do usuário cujos dados serão atualizados
			3) campos literais deverão ter o prefixo lit e campos numéricos deverão ter o prefixo num
		*/
		function update($id_comunicado, $info){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			//inicializa a query
			$update_sql = "	UPDATE
								{$conf['db_name']}comunicado
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
			$update_sql .= " WHERE  idcomunicado = $id_comunicado ";

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
		Function delete($id_comunicado){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// conjunto de dependências geradas
			$sql = "DELETE 
					FROM
						{$conf['db_name']}comunicado_condominio
					WHERE
						 idcomunicado = $id_comunicado ";
								 
			$n0 = $db->query($sql);

			//---------------------


			// verifica se pode excluir
			if (1 && $n0) {

				$delete_sql = "	DELETE FROM
									{$conf['db_name']}comunicado
								WHERE
									 idcomunicado = $id_comunicado";
				$delete_q = $db->query($delete_sql);

				if($delete_q){
					
					/// apaga arquivos associados ao comunicado
					$this->apagaArquivosComunicado($id_comunicado);
					
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
		  * método: Filtra_Comunicado_AJAX
		  * Busca comunicados associados aos condomínios
		  * Para o condomínio selecionado mostra a lista de comunicados associados
		  */
		function Filtra_Comunicado_AJAX ( $filtro, $campoID) {
		
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
		
			/// busca condomínios com padrão de nome digitado
			$list_sql = "	SELECT cliente_condominio.idcliente, cliente.nome_cliente
							FROM
								{$conf['db_name']}cliente_condominio
								LEFT JOIN {$conf['db_name']}cliente ON (cliente_condominio.idcliente = cliente.idcliente)
							WHERE
							(
								UPPER(nome_cliente) LIKE UPPER('%{$filtro}%')
							)
		
							ORDER BY nome_cliente ";

			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);
		
		
			if($list_q){
		
				// testa se retornou algum registro
				if ($db->num_rows($list_q) > 0) {
		
			?>
				<table width="100%" cellpadding="5" cellspacing="2">
					<tr onselect="" class="cabecalho">
						<td width="10%" class="cabecalho_negrito"><?php echo ('C&oacute;digo'); ?></td>
						<td width="60%" class="cabecalho_negrito"><?php echo ('Condom&iacute;nio'); ?></td>
					</tr>
					<?php
			
					$cont = 0;
					$filtro = htmlentities($filtro);

					/// monta informações de cada condomínio encontrado
					while($list = $db->fetch_array($list_q)){
						
						$list['nome_cliente'] = utf8_encode($list['nome_cliente']); 
								
						//insere um índice na listagem
						$list['index'] = $cont+1;
									
						?>
						<tr onselect="
							this.text.value = '<?php echo ($list['nome_cliente']); ?>';
							$('<?php echo $campoNomeTemp; ?>').value = '<?php echo ($list['nome_cliente']); ?>';
							$('<?php echo $campoID; ?>').value = '<?php echo ($list['idcliente']); ?>';
							$('<?php echo $campoFlag; ?>').className = 'selecionou';
	
						">
							<td class="tb_bord_baixo"><?php echo ($list['idcliente']); ?></td>
							<td class="tb_bord_baixo"><?php echo ($list['nome_cliente']); ?></td>
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

	} // fim da classe
?>
