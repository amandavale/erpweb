<?php
	
	class bairro {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function bairro(){
			// n�o faz nada
		}


		/**
		  m�todo: Busca_Generica
		  prop�sito: Busca_Generica
		*/

		function Busca_Generica ( $pg, $rppg, $busca = "", $ordem = "", $url = ""){

			if ($ordem == "") $ordem = " ORDER BY BAR.nome_bairro ASC";

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT
								BAR.*, CID.*, EST.*
							FROM
           						{$conf['db_name']}bairro BAR
								INNER JOIN {$conf['db_name']}cidade CID ON BAR.idcidade=CID.idcidade
								INNER JOIN {$conf['db_name']}estado EST ON CID.idestado=EST.idestado
							WHERE  
           					 	UPPER(BAR.nome_bairro) LIKE UPPER('%{$busca}%')
								$ordem ";

			//manda fazer a pagina��o
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					//insere um �ndice na listagem
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
		  m�todo: Filtra_Bairro_AJAX
		  prop�sito: Filtra_Bairro_AJAX
		*/

		function Filtra_Bairro_AJAX ( $filtro , $idcidade, $campoID, $mostraDetalhes, $inserirBairro = false ) {

			// vari�veis globais
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

			// volta o filtro para a codifica��o original
			$filtro		= utf8_decode($filtro);
			$idcidade 	= utf8_decode($idcidade);
			$campoID 	= utf8_decode($campoID);
			$mostraDetalhes = utf8_decode($mostraDetalhes);
			
			// campos de controle
			$campoNomeTemp = $campoID . "_NomeTemp";
			$campoFlag = $campoID . "_Flag";

			if ($idcidade == "") $idcidade = "NULL";

			$list_sql = "	SELECT
											BAR.*, CID.nome_cidade , EST.*
										FROM
           									{$conf['db_name']}bairro BAR
           									INNER JOIN {$conf['db_name']}cidade CID ON BAR.idcidade=CID.idcidade
												INNER JOIN {$conf['db_name']}estado EST ON CID.idestado=EST.idestado

										WHERE
											BAR.idcidade = $idcidade
												AND
											(
												UPPER(BAR.nome_bairro) LIKE UPPER('%{$filtro}%')
											)
										ORDER BY
											BAR.nome_bairro ASC ";

			//manda fazer a pagina��o
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);


			if($list_q){

				// testa se retornou algum registro
				if ($db->num_rows($list_q) > 0) {

					?>
					<table width="100%" cellpadding="5" cellspacing="2">
						<tr onselect="" class="cabecalho">
							<td width="100%" class="cabecalho_negrito"><?php echo ('Bairro'); ?></td>
						</tr>
					<?php

					$cont = 0;
					while($list = $db->fetch_array($list_q)){

						//insere um �ndice na listagem
						$list['index'] = $cont+1;

						// coloca em negrito a string que foi encontrada na palavra
						$list['nome_bairro_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['nome_bairro']);

						if ($mostraDetalhes == 1) {
							$list['info_bairro'] = "
								<table width=95% align=center>
									<tr>
										<th align=center>Estado</th>
										<th align=center>Cidade</th>
										<th align=center>Nome do bairro</th>
									</tr>

									<tr>
										<td><a class=menu_item href=" . $conf['addr'] . "/admin/bairro.php?ac=editar&idbairro=" . $list['idbairro'] . ">" . $list['nome_estado'] . "</a></td>
										<td><a class=menu_item href=" . $conf['addr'] . "/admin/bairro.php?ac=editar&idbairro=" . $list['idbairro'] . ">" . $list['nome_cidade'] . "</a></td>
										<td><a class=menu_item href=" . $conf['addr'] . "/admin/bairro.php?ac=editar&idbairro=" . $list['idbairro'] . ">" . $list['nome_bairro'] . "</a></td>
									</tr>
								</table>";

							$list['info_bairro'] = ereg_replace("(\r\n|\n|\r)", "", $list['info_bairro']);
						}


						?>
						<tr onselect="
							this.text.value = '<?php echo ($list['nome_bairro']); ?>';
							$('<?php echo $campoNomeTemp; ?>').value = '<?php echo ($list['nome_bairro']); ?>';
							$('<?php echo $campoID; ?>').value = '<?php echo ($list['idbairro']); ?>';
							$('<?php echo $campoFlag; ?>').className = 'selecionou';
							
							<?php if ($mostraDetalhes == 1) ?>
								$('dados_bairro').innerHTML = '<?php echo ($list['info_bairro']); ?>';

						">
							<td class="tb_bord_baixo"><?php echo ($list['nome_bairro_negrito']); ?></td>
						</tr>
						<?php

	          			$cont++;
					}

					// verifica a pagina��o
					$paginacao = "";
					if ($pg > 0) $paginacao .= "<a href='?page=" . ($pg - 1) . "' style='float:left' class='page_up'>" . ('Anterior') . "</a>";
					$paginacao .= "<a href='?page=" . ($pg + 1) .  "' style='float:right'  class='page_down'>" . ('Proximo') . "</a>";

				}
				// Nenhum registro foi encontrado
				else {

					
						if($inserirBairro): ?>
							<table width="100%" cellpadding="5" cellspacing="2">
								<tr onselect="xajax_Cadastro_Rapido_Bairro_AJAX(<?php echo "$idcidade,'$filtro', $campoID"; ?>);" >
									<td width="70%" >
										<?php echo  'Adicionar bairro '.$filtro ;?>
									</td>
								</tr>
							 </table>
							 
						<?php else: ?>
						
							<table width="100%" cellpadding="5" cellspacing="2" class="cabecalho">
								<tr>
									<td width="70%" >
									 	<?php echo $conf['listar']; ?>
									</td>
								</tr>
							</table>
						<?php endif;
						

					// verifica a pagina��o
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

			// Encerra a tabela e coloca a pagina��o
			echo "</table>";
			if ($paginacao != "") echo $paginacao;

		}



		/**
		  m�todo: getById
		  prop�sito: busca informa��es
		*/
		function getById($idbairro){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											BAR.* , CID.nome_cidade , EST.*
										FROM
											{$conf['db_name']}bairro BAR
												 INNER JOIN {$conf['db_name']}cidade CID ON BAR.idcidade=CID.idcidade
												 INNER JOIN {$conf['db_name']}estado EST ON CID.idestado=EST.idestado

										WHERE
											 BAR.idbairro = $idbairro ";

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
		  m�todo: make_list
		  prop�sito: faz a listagem
		*/
		
		function make_list( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
			
			if ($ordem == "") $ordem = " ORDER BY EST.nome_estado ASC, CID.nome_cidade ASC, BAR.nome_bairro ASC ";
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											BAR.*   , CID.nome_cidade , EST.nome_estado
										FROM
           						{$conf['db_name']}bairro BAR 
												 INNER JOIN {$conf['db_name']}cidade CID ON BAR.idcidade=CID.idcidade 
												 INNER JOIN {$conf['db_name']}estado EST ON CID.idestado=EST.idestado
												
										$filtro
										$ordem";

			//manda fazer a pagina��o
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

			if($list_q){
				
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){
					
					//insere um �ndice na listagem
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
			m�todo: set
		  prop�sito: inclui novo registro
		*/

		function set($info){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			
			$set_sql = "  INSERT INTO
		                  {$conf['db_name']}bairro
		                    (
		                    
												idcidade, 
												nome_bairro  
												
												)
		                VALUES
		                    (
		                    
		                    " . $info['idcidade'] . ",  
												'" . $info['nome_bairro'] . "'   
												
												)";
			
			//executa a query e testa se a consulta foi "boa"
			if($db->query($set_sql)){
				//retorna o c�digo inserido
				$codigo = $db->insert_id();
				
				
				
				return($codigo);
			}
			else{
				$this->err = $falha['inserir'];
				return(0);
			}
		}
		
		/**
		  m�todo: update
		  prop�sito: atualiza os dados
		  
		  1) o vetor $info deve conter todos os campos tabela a serem atualizados
			2) a vari�vel $id deve conter o c�digo do usu�rio cujos dados ser�o atualizados
			3) campos literais dever�o ter o prefixo lit e campos num�ricos dever�o ter o prefixo num
		*/
		function update($idbairro, $info){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}bairro
											SET ";

   		//varre o formul�rio e monta a consulta;
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
				
				//testa se � o �ltimo
				if($cont != $cont_validos){
					$update_sql .= ", ";
				}
				
			}
			

			//completa o sql com a restri��o
			$update_sql .= " WHERE  idbairro = $idbairro ";
			
			
			//envia a query para o banco
			$update_q = $db->query($update_sql);
			
			if($update_q)
			  return(1);
			else
			  $this->err = $falha['alterar'];
		}	
		
		/**
		  m�todo: delete
		  prop�sito: excluir registro
		*/
		function delete($idbairro){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			// conjunto de depend�ncias geradas
			$sql = "SELECT 
								 * 
							FROM 
								{$conf['db_name']}endereco
							WHERE 
								 idbairro = $idbairro ";
			$verifica_q = $db->query($sql);
			$n0 = $db->num_rows($verifica_q);
			
			
			//---------------------
			

			// verifica se pode excluir
			if (1 && $n0==0) {

				

				$delete_sql = "	DELETE FROM
													{$conf['db_name']}bairro
												WHERE
													 idbairro = $idbairro ";
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
				$this->err = "Este registro n�o pode ser exclu�do, pois existem registros relacionados a ele.";
			}	

		}	

		
		/**
		  m�todo: make_list
		  prop�sito: faz a listagem para colocar no select
		*/

		function make_list_select( $filtro = "", $ordem = " ORDER BY idcidade ASC") {
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}bairro
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
