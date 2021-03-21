<?php

	class plano {

		var $err;

		/**
    * construtor da classe
  	*/
		function plano(){
			// não faz nada
		}


		/**
		  método: getById
		  propósito: busca informações
		*/
		function getById($idplano){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$get_sql = "SELECT PLA.*
						FROM
							{$conf['db_name']}plano PLA
						WHERE
							 PLA.idplano = $idplano ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				$get['descricao'] = $get['numero'] . ' - ' . $get['nome'];

				//retorna o vetor associativo com os dados
				return $get;
			}
			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}

		}




		function Filtra_Plano_AJAX ( $filtro, $campoID, $mostraDetalhes = null, $tipo=null ) {

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

			
			
			//Valores aceitos: 'R' (receitas) / 'D' (despesas)
			$filtro_tipo =  ($tipo == null) ? null :  " AND P.tipo = '$tipo' ";
			
			$list_sql = "	SELECT
											P.*
										FROM
           						{$conf['db_name']}plano P
										WHERE
										(
											UPPER(P.numero) LIKE UPPER('%{$filtro}%')
                                          OR
                                            UPPER(P.nome) LIKE UPPER('%{$filtro}%')
										)
                                        $filtro_tipo
                                            
										ORDER BY
											P.numero ASC ";


			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);


			if($list_q){

				// testa se retornou algum registro
				if ($db->num_rows($list_q) > 0) {

					?>
					<table width="100%" cellpadding="5" cellspacing="2">
						<tr onselect="" class="cabecalho">
							<td width="70%" class="cabecalho_negrito"><?php echo ('Plano de Contas'); ?></td>

						</tr>
					<?php

					//Trata problemas de codificação de carcteres
					strlen($filtro)==0 ? $filtro = '%' : '' ;
					$filtro = htmlentities($filtro);					
					//-------------------------------------------
					
					$cont = 0;
					while($list = $db->fetch_array($list_q)){

						//insere um índice na listagem
						$list['index'] = $cont+1;
						
						$list['nome'] = htmlentities($list['nome']);

						// coloca em negrito a string que foi encontrada na palavra
						$list['nome_plano_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['numero'].' - '.$list['nome']);


						?>
						<tr onselect="
							this.text.value = '<?php echo ($list['numero']); ?> - <?php print ($list['nome']); ?>';
							$('<?php echo $campoNomeTemp; ?>').value = '<?php echo ($list['numero']); ?> - <?php print ($list['nome']); ?>';
							$('<?php echo $campoID; ?>').value = '<?php echo ($list['idplano']); ?>';
							$('<?php echo $campoFlag; ?>').className = 'selecionou';
							$('spn_pai_numero').innerHTML = '<?php echo ($list['numero'].'.'); ?>';
							$('hid_pai_numero').value = '<?php echo ($list['numero'].'.'); ?>'
						">
							<td class="tb_bord_baixo"><?php echo ($list['nome_plano_negrito']);?> </td>

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
						<td width="70%" class="cabecalho_negrito"><?php echo ($list_sql); ?></td>
					</tr>
				<?php
			}

			// Encerra a tabela e coloca a paginação
			echo "</table>";
			if ($paginacao != "") echo $paginacao;

		}


		/**
		  método: make_list
		  propósito: faz a listagem
		*/

		function make_list( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){

			if ($ordem == "") $ordem = " ORDER BY PLA.numero ASC";

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT PLA.*   , PLA.idplano
							FROM
							{$conf['db_name']}plano PLA
				
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

					if ($list['tipo'] == 'D') $list['tipo'] = "Despesa";
					else if ($list['tipo'] == 'R') $list['tipo'] = "Receita";

					//Marca qual o nivel de hierarquia do plano
					$niveis = explode('.',$list['numero']);
					$list['nivel'] = count($niveis); 


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
		  * método: pesquisaPlano
		  * propósito: faz pesquisa de plano sem paginação
		  * 
		  *
		  */
		
		function pesquisaPlano($filtro = "", $ordem = ""){
		
			if ($ordem == "") $ordem = " ORDER BY PLA.numero ASC";
		
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
		
			$list_sql = "SELECT PLA.*
						 FROM
						 	{$conf['db_name']}plano PLA
						 $filtro
						 $ordem";
						 	
			//manda fazer a paginação
			$list_q = $db->query($list_sql);
		
			if($list_q){
		
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				
				while($list = $db->fetch_array($list_q)){
		
					if ($list['tipo'] == 'D'){
						$list['tipo'] = "Despesa";
					}
					else if ($list['tipo'] == 'R'){
						$list['tipo'] = "Receita";
					}
		
					//Marca qual o nivel de hierarquia do plano
					$niveis = explode('.',$list['numero']);
					$list['nivel'] = count($niveis);
		
					$list_return[] = $list;
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


			$set_sql = "INSERT INTO
		                  {$conf['db_name']}plano
		                    (
												idpai,
												numero,
												nome,
												tipo,
												descricao

												)
		                      VALUES
		                             (

		                                        '" . $info['idplano_Nome'] . "',
												'" . $info['codHidden'] . "',
												'" . $info['nome'] . "',
												'" . $info['tipo'] . "',
											    '" . $info['descricao'] . "'

												)";

			//executa a query e testa se a consulta foi "boa"
			if($db->query($set_sql)){
				//retorna o código inserido
				$codigo = $db->insert_id();

				return($codigo);
			}
			else{
				$this->err = $falha['inserir'] . '<br><br>' . $db->error() . '<br><br>' . $set_sql;
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
		function update($idplano, $info){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			//inicializa a query
			$update_sql = "	UPDATE 	{$conf['db_name']}plano
											SET ";

   		//varre o formulário e monta a consulta;
			$cont_validos = 0;
			foreach($info as $campo => $valor){

				$tipo_campo = substr($campo, 0, 3);
				$nome_campo = substr($campo, 3, strlen($campo) - 3);

				if( ($tipo_campo == "lit") || ($tipo_campo == "num") ){

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
			$update_sql .= " WHERE  idplano = $idplano ";


             //print $update_sql;
			//envia a query para o banco
			$update_q = $db->query($update_sql);

			if($update_q)
			  return(1);
			else
			  $this->err = $falha['alterar'];
			   //$this->err = $db->error();
		}

		/**
		  *método: delete
		  *propósito: excluir registro
	      */
		function delete($idplano){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// conjunto de dependências geradas
			
			//Por hora, as verificações comentadas abaixo ficarão assim pois ainda não está definido se as tabelas
			// "conta_receber" e "conta_pagar" serão vinculadas a tabela "movimento"
			/*$sql = "SELECT
								 *
							FROM
								{$conf['db_name']}conta_receber
							WHERE
								 idplano = $idplano ";
			$verifica_q = $db->query($sql);
			$n0 = $db->num_rows($verifica_q);
			
			$sql = "SELECT
								 *
							FROM
								{$conf['db_name']}conta_pagar
							WHERE
								 idplano = $idplano ";
			$verifica_q = $db->query($sql);
			$n1 = $db->num_rows($verifica_q);
			*/
			
			$sql = "SELECT
								 *
							FROM
								{$conf['db_name']}plano
							WHERE
								 idpai = $idplano ";
			$verifica_q = $db->query($sql);
			$n2 = $db->num_rows($verifica_q);


			//---------------------


			// verifica se pode excluir
			if (/*$n0==0 && $n1==0 && */$n2==0) {



				$delete_sql = "	DELETE FROM {$conf['db_name']}plano
												WHERE
													 idplano = $idplano ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY numero ASC") {

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------


			$list_sql = "SELECT * FROM {$conf['db_name']}plano
										$filtro
										$ordem";

			$list_q = $db->query($list_sql);
			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					foreach($list as $campo => $value){
						$list_return[$campo][$cont] = $value;
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
		
		
		
		
		function getByNumero($numero){

			// ---------- variáveis globais -----------
				global $form, $conf, $db, $falha;
			//-----------------------------------------

			$get_sql = "SELECT PLA.* FROM {$conf['db_name']}plano PLA WHERE PLA.numero = '$numero' ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				$get['descricao'] = $get['numero'] . ' - ' . $get['nome'];

				//retorna o vetor associativo com os dados
				return $get;
			}
			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}

		}

		
		

	} // fim da classe
?>
