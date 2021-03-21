<?php
	
	class estado {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function estado(){
			// não faz nada
		}


		/**
		  método: Filtra_Estado_AJAX
		  propósito: Filtra_Estado_AJAX
		*/

		function Filtra_Estado_AJAX ( $filtro, $campoID ) {

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
											EST.*
										FROM
           						{$conf['db_name']}estado EST
										WHERE
											UPPER(EST.nome_estado) LIKE UPPER('%{$filtro}%')
											  OR
											UPPER(EST.sigla_estado) LIKE UPPER('%{$filtro}%')
										ORDER BY
											EST.nome_estado ASC ";


			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);


			if($list_q){

				// testa se retornou algum registro
				if ($db->num_rows($list_q) > 0) {
					
					?>
					<table width="100%" cellpadding="5" cellspacing="2">
						<tr onselect="" class="cabecalho">
							<td width="70%" class="cabecalho_negrito"><?php echo ('Estado'); ?></td>
							<td class="cabecalho_negrito" align="center"><?php echo ('Sigla'); ?></td>
						</tr>
					<?php

					$cont = 0;
					while($list = $db->fetch_array($list_q)){

						//insere um índice na listagem
						$list['index'] = $cont+1;

						// coloca em negrito a string que foi encontrada na palavra
						$list['nome_estado_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['nome_estado']);
						$list['sigla_estado_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['sigla_estado']);

						?>
						<tr onselect="
							this.text.value = '<?php echo ($list['nome_estado']); ?>';
							$('<?php echo $campoNomeTemp; ?>').value = '<?php echo ($list['nome_estado']); ?>';
							$('<?php echo $campoID; ?>').value = '<?php echo ($list['idestado']); ?>';
							$('<?php echo $campoFlag; ?>').className = 'selecionou'
						">
							<td class="tb_bord_baixo"><?php echo ($list['nome_estado_negrito']); ?></td>
							<td class="tb_bord_baixo" align="center"><?php echo ($list['sigla_estado_negrito']); ?></td>
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
		function getById($idestado){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											EST.*
										FROM
											{$conf['db_name']}estado EST
										WHERE
											 EST.idestado = $idestado ";

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
			
			if ($ordem == "") $ordem = " ORDER BY EST.nome_estado ASC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											EST.*  
										FROM
           						{$conf['db_name']}estado EST 
												
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
		                  {$conf['db_name']}estado
		                    (
		                    
												nome_estado, 
												sigla_estado
												
												
												)
		                VALUES
		                    (
		                    
		                    '" . $info['nome_estado'] . "',  
		                    '" . $info['sigla_estado'] . "'
												
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
		function update($idestado, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}estado
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
			$update_sql .= " WHERE  idestado = $idestado ";
			
			
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
		function delete($idestado){
			
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
								{$conf['db_name']}endereco
							WHERE 
								 idestado = $idestado ";
			$verifica_q = $db->query($sql);
			$n0 = $db->num_rows($verifica_q);
			
			
			//---------------------
			

			// verifica se pode excluir
			if (1 && $n0==0) {

				

				$delete_sql = "	DELETE FROM
													{$conf['db_name']}estado
												WHERE
													 idestado = $idestado ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY nome_estado ASC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}estado
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
		
		
		
		

	} // fim da classe
?>
