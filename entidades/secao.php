<?php
	
	class secao {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function secao(){
			// não faz nada
		}


		/**
		  método: Filtra_Secao_AJAX
		  propósito: Filtra_Secao_AJAX
		*/

		function Filtra_Secao_AJAX ( $filtro , $iddepartamento, $campoID, $mostraDetalhes ) {

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
			$idestado = utf8_decode($idestado);
			$campoID = utf8_decode($campoID);
			$mostraDetalhes = utf8_decode($mostraDetalhes);

			// campos de controle
			$campoNomeTemp = $campoID . "_NomeTemp";
			$campoFlag = $campoID . "_Flag";

			if ($iddepartamento == "") $iddepartamento = "NULL";

			$list_sql = "	SELECT
											S.*, D.*
										FROM
											{$conf['db_name']}secao S
											INNER JOIN {$conf['db_name']}departamento D ON S.iddepartamento = D.iddepartamento
										WHERE
												S.iddepartamento = $iddepartamento
												AND
											(
												UPPER(S.nome_secao) LIKE UPPER('%{$filtro}%')
											)
										ORDER BY
											S.nome_secao ASC ";

			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);


			if($list_q){

				// testa se retornou algum registro
				if ($db->num_rows($list_q) > 0) {

					?>
					<table width="100%" cellpadding="5" cellspacing="2">
						<tr onselect="" class="cabecalho">
							<td width="100%" class="cabecalho_negrito"><?php echo ('Secao'); ?></td>
						</tr>
					<?php

					$cont = 0;
					while($list = $db->fetch_array($list_q)){

						//insere um índice na listagem
						$list['index'] = $cont+1;

					if ($mostraDetalhes == 1) {
							$list['info_secao'] = "
								<table width=95% align=center>
											<tr>
												<th align=center>No</th>
            						<th align=center>Departamento</th>
												<th align=center>Nome da seção</th>
											</tr>
       					      <tr>
												<td><a class=menu_item href=" . $conf['addr'] . "/admin/secao.php?ac=editar&idsecao=" . $list['idsecao'] . ">" . $list['index'] . "</a></td>
												<td><a class=menu_item href=" . $conf['addr'] . "/admin/secao.php?ac=editar&idsecao=" . $list['idsecao'] . ">" . $list['nome_departamento'] . "</a></td>
												<td><a class=menu_item href=" . $conf['addr'] . "/admin/secao.php?ac=editar&idsecao=" . $list['idsecao'] . ">" . $list['nome_secao'] . "</a></td>
											</tr>

								</table>";

							$list['info_secao'] = ereg_replace("(\r\n|\n|\r)", "", $list['info_secao']);
						}



						// coloca em negrito a string que foi encontrada na palavra
						$list['nome_secao_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['nome_secao']);

						?>
						<tr onselect="
							this.text.value = '<?php echo ($list['nome_secao']); ?>';
							$('<?php echo $campoNomeTemp; ?>').value = '<?php echo ($list['nome_secao']); ?>';
							$('<?php echo $campoID; ?>').value = '<?php echo ($list['idsecao']); ?>';
							$('<?php echo $campoFlag; ?>').className = 'selecionou'
							<?php if ($mostraDetalhes == 1) ?>
								$('dados_secao').innerHTML = '<?php echo ($list['info_secao']); ?>';
							">
							<td class="tb_bord_baixo"><?php echo ($list['nome_secao_negrito']); ?></td>
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
		  método: getById
		  propósito: busca informações
		*/
		function getById($idsecao){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											S.* , D.*
										FROM
											{$conf['db_name']}secao S
											INNER JOIN departamento D ON D.iddepartamento = S.iddepartamento
										WHERE
											 S.idsecao = $idsecao ";

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
		  método: make_list
		  propósito: faz a listagem
		*/
		
		function make_list( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
			
			if ($ordem == "") $ordem = " ORDER BY S.nome_secao ASC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											S.*   , D.nome_departamento
										FROM
           						{$conf['db_name']}secao S 
												 INNER JOIN {$conf['db_name']}departamento D ON S.iddepartamento=D.iddepartamento 
												
										$filtro
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
			
			
			$set_sql = "  INSERT INTO
		                  {$conf['db_name']}secao
		                    (
		                    
												iddepartamento, 
												nome_secao  
												
												)
		                VALUES
		                    (
		                    
		                    " . $info['iddepartamento'] . ",  
												'" . $info['nome_secao'] . "'   
												
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
		function update($idsecao, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}secao
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
			$update_sql .= " WHERE  idsecao = $idsecao ";
			
			
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
		function delete($idsecao){
			
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
								{$conf['db_name']}produto
							WHERE 
								 idsecao = $idsecao ";
			$verifica_q = $db->query($sql);
			$n0 = $db->num_rows($verifica_q);
			
			
			//---------------------
			

			// verifica se pode excluir
			if (1 && $n0==0) {

				$delete_sql = "	DELETE FROM
													{$conf['db_name']}secao
												WHERE
													 idsecao = $idsecao ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY nome_secao ASC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						S.*, D.nome_departamento
										FROM
           						{$conf['db_name']}secao S
												 INNER JOIN {$conf['db_name']}departamento D ON S.iddepartamento=D.iddepartamento

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
					
					$list_return["departamento_secao"][$cont] = $list['nome_departamento'] . " / " . $list['nome_secao'];

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

			if ($ordem == "") $ordem = " ORDER BY S.nome_secao DESC";

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT
											D.*, S.*
										FROM
           						{$conf['db_name']}secao S
           						INNER JOIN {$conf['db_name']}departamento D ON S.iddepartamento=D.iddepartamento
           						
           					WHERE
											(
											UPPER(S.nome_secao) LIKE UPPER('%{$busca}%') OR
           					 	UPPER(D.nome_departamento) LIKE UPPER('%{$busca}%') 
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

			if ($ordem == "") $ordem = "  ORDER BY S.nome_secao DESC";

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------


			if ($filtro_where != "")
				$filtro_where = " WHERE ( " . $filtro_where . " ) ";



			$list_sql = "	SELECT
											D.*, S.*
										FROM
           						{$conf['db_name']}secao S
           						INNER JOIN {$conf['db_name']}departamento D ON S.iddepartamento=D.iddepartamento

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
		

	} // fim da classe
?>
