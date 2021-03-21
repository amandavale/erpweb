<?php
	
	class produto {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function produto(){
			// não faz nada
		}
    
    
        /**
      método: make_list
      propósito: faz a listagem
    */
    
    function make_list_cotep_icms( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
      
      if ($ordem == "") $ordem = " ORDER BY PRD.descricao_produto ASC";
      
      // variáveis globais
      global $form;
      global $conf;
      global $db;
      global $falha;
      //---------------------
      
      $list_sql = " SELECT
                      PRD.idproduto, PRD.descricao_produto, PRD.situacao_tributaria_produto, PRD.icms_produto, PRDFL.preco_balcao_produto, UNV.sigla_unidade_venda
                    FROM
                      {$conf['db_name']}produto PRD 
                         INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda=UNV.idunidade_venda 
                          INNER JOIN {$conf['db_name']}produto_filial PRDFL ON PRD.idproduto = PRDFL.idproduto
                        
                    $filtro
                    AND PRD.produto_comercializado = '1'
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
          
          if ($list['preco_balcao_produto'] != "") $list['preco_balcao_produto'] = number_format($list['preco_balcao_produto'],2,",",""); 
          
        

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
				método: Atualiza_Preco_Sessao
				propósito: Atualizar os preços de uma determinada sessão.
		**/

		function Atualiza_Preco_Sessao( $idsecao, $porcentagem, $tipo ) {

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			if ($tipo == 'R')$operador = 1 - $porcentagem/100;
			else $operador = $porcentagem/100 + 1;
			
			
			$list_sql = "	SELECT
					 						PRD.* , PRDFL.*
										FROM
											{$conf['db_name']}produto PRD
											INNER JOIN {$conf['db_name']}produto_filial PRDFL ON PRD.idproduto = PRDFL.idproduto
										WHERE
											PRD.idsecao = $idsecao
										";

			$list_q = $db->query($list_sql);
			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){
				
				$list['preco_balcao_produto'] = $list['preco_balcao_produto']*$operador;
				$list['preco_oferta_produto'] = $list['preco_oferta_produto']*$operador;
				$list['preco_atacado_produto'] = $list['preco_atacado_produto']*$operador;
				$list['preco_telemarketing_produto'] = $list['preco_balcao_produto']*$operador;

        $update_sql = "	UPDATE
											{$conf['db_name']}produto_filial
												SET
												preco_balcao_produto = " . $list['preco_balcao_produto'] . ",
                        preco_oferta_produto = " . $list['preco_balcao_produto'] . ",
                        preco_atacado_produto = " . $list['preco_balcao_produto'] . ",
                        preco_telemarketing_produto = " . $list['preco_balcao_produto'] . "
												WHERE
												idproduto = " . $list['idproduto'] . "
												";

				
				
				//envia a query para o banco
				$update_q = $db->query($update_sql);

				}
			}
		}

		/**
		  método: Filtra_Produto_Encartelamento_Transferencia_AJAX
		  propósito: Filtra_Produto_Encartelamento_Transferencia_AJAX
		*/

		function Filtra_Produto_Encartelamento_Transferencia_AJAX ( $filtro, $campoID ) {

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
			$filtro = utf8_decode(addslashes($filtro));
			$campoID = utf8_decode($campoID);

			// campos de controle
			$campoNomeTemp = $campoID . "_NomeTemp";
			$campoFlag = $campoID . "_Flag";
			$campoTipo = $campoID . "_Tipo";
			


			$list_sql = "
									(
										SELECT
											'E' as tipo,
											ENCT.idencartelamento as id,
											SPACE(10) as codigo,
											ENCT.descricao_encartelamento as descricao,
											CONCAT('Un', SPACE(10)) as unidade,
											SPACE(10) as qtd_produto

										FROM  {$conf['db_name']}encartelamento ENCT
													INNER JOIN {$conf['db_name']}encartelamento_produto  ENCP ON ENCT.idencartelamento = ENCP.idencartelamento
													INNER JOIN {$conf['db_name']}produto PRD ON ENCP.idproduto = PRD.idproduto
													

										WHERE
											UPPER(ENCT.descricao_encartelamento) LIKE UPPER('%{$filtro}%')

										GROUP BY   ENCP.idencartelamento
									)

									UNION

									(

										SELECT
											'P' as tipo,
											PRD.idproduto as id,
											PRD.codigo_produto as codigo,
											PRD.descricao_produto as descricao,
											UNV.sigla_unidade_venda as unidade,
											PRDFL.qtd_produto
										
										FROM {$conf['db_name']}produto PRD
											INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda = UNV.idunidade_venda
											INNER JOIN {$conf['db_name']}produto_filial PRDFL ON PRDFL.idproduto = PRD.idproduto
										
										WHERE

											PRD.produto_comercializado = '1'
												AND
											PRDFL.idfilial = " . $_SESSION['idfilial_usuario'] . "
											AND
											(
												UPPER(PRD.descricao_produto) LIKE UPPER('%{$filtro}%')
													OR
												UPPER(PRD.idproduto) LIKE UPPER('%{$filtro}%')
											)

									)

									ORDER BY  descricao ASC

								";



			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);


			if($list_q){

				// testa se retornou algum registro
				if ($db->num_rows($list_q) > 0) {

					?>
					<table width="100%" cellpadding="5" cellspacing="2">
						<tr onselect="" class="cabecalho">
							<td width="10%" class="cabecalho_negrito"><?php echo ('Codigo'); ?></td>
							<td width="65%" class="cabecalho_negrito"><?php echo ('Produto'); ?></td>
							<td width="15%" class="cabecalho_negrito"><?php echo ('Estoque'); ?></td>
							<td width="10%" class="cabecalho_negrito" align="center"><?php echo ('Un.'); ?></td>
						</tr>
					<?php

					$cont = 0;
					while($list = $db->fetch_array($list_q)){

						//insere um índice na listagem
						$list['index'] = $cont+1;

						$list['qtd_produto'] = number_format($list['qtd_produto'],2,",","");
						if($list['tipo']=='E')$list['qtd_produto']="-----";
						// coloca em negrito a string que foi encontrada na palavra
						$list['descricao_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['descricao']);
						$list['codigo_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['id']);

						?>
						<tr onselect="
							this.text.value = '<?php echo (addslashes($list['descricao'])); ?>';
							$('<?php echo $campoNomeTemp; ?>').value = '<?php echo (addslashes($list['descricao'])); ?>';
							$('<?php echo $campoID; ?>').value = '<?php echo ($list['id']); ?>';
							$('<?php echo $campoTipo; ?>').value = '<?php echo ($list['tipo']); ?>';
							$('<?php echo $campoFlag; ?>').className = 'selecionou'
						">
							<td class="tb_bord_baixo">&nbsp;<?php echo ($list['codigo_negrito']); ?></td>
							<td class="tb_bord_baixo"><?php echo ($list['descricao_negrito']); ?></td>
							<td class="tb_bord_baixo" align="center"><?php echo ($list['qtd_produto']); ?></td>
							<td class="tb_bord_baixo" align="center"><?php echo ($list['unidade']); ?></td>
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
		  método: Filtra_Produto_Encartelamento_AJAX
		  propósito: Filtra_Produto_Encartelamento_AJAX
		*/

		function Filtra_Produto_Encartelamento_AJAX ( $filtro, $campoID, $tipoPreco ) {

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
			$filtro = utf8_decode(addslashes($filtro));
			$campoID = utf8_decode($campoID);
			$tipoPreco = utf8_decode($tipoPreco);

			// campos de controle
			$campoNomeTemp = $campoID . "_NomeTemp";
			$campoFlag = $campoID . "_Flag";
			$campoTipo = $campoID . "_Tipo";
			

			// verifica qual o preço vai ser utilizado
			if ($tipoPreco == "B") $campo_preco = 'preco_balcao_produto';
			else if ($tipoPreco == "O") $campo_preco = 'preco_oferta_produto';
			else if ($tipoPreco == "A") $campo_preco = 'preco_atacado_produto';
			else if ($tipoPreco == "T") $campo_preco = 'preco_telemarketing_produto';


			$list_sql = "
									(
										SELECT
											'E' as tipo,
											ENCT.idencartelamento as id,
											SPACE(10) as codigo,
											ENCT.descricao_encartelamento as descricao,
											CONCAT('Un', SPACE(10)) as unidade,
											SUM(PRDFL.{$campo_preco} * ENCP.qtd) as  preco,
											SPACE(10) as qtd_produto

										FROM  {$conf['db_name']}encartelamento ENCT
													INNER JOIN {$conf['db_name']}encartelamento_produto  ENCP ON ENCT.idencartelamento = ENCP.idencartelamento
													INNER JOIN {$conf['db_name']}produto PRD ON ENCP.idproduto = PRD.idproduto
													INNER JOIN {$conf['db_name']}produto_filial PRDFL ON PRDFL.idproduto = PRD.idproduto

										WHERE
											PRDFL.idfilial = {$_SESSION['idfilial_usuario']}
												AND
											UPPER(ENCT.descricao_encartelamento) LIKE UPPER('%{$filtro}%')

										GROUP BY   ENCP.idencartelamento
									)

									UNION

									(

										SELECT
											'P' as tipo,
											PRD.idproduto as id,
											PRD.codigo_produto as codigo,
											PRD.descricao_produto as descricao,
											UNV.sigla_unidade_venda as unidade,
											PRDFL.{$campo_preco} as  preco,
											PRDFL.qtd_produto

										FROM {$conf['db_name']}produto PRD
											INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda = UNV.idunidade_venda
											INNER JOIN {$conf['db_name']}produto_filial PRDFL ON PRDFL.idproduto = PRD.idproduto

										WHERE
											PRDFL.idfilial = {$_SESSION['idfilial_usuario']}
												AND
											PRD.produto_comercializado = '1'
												AND
											(
												UPPER(PRD.descricao_produto) LIKE UPPER('%{$filtro}%')
													OR
												UPPER(PRD.idproduto) LIKE UPPER('%{$filtro}%')
											)

									)
									
									ORDER BY  descricao ASC

								";



			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

						
			
			if($list_q){

				// testa se retornou algum registro
				if ($db->num_rows($list_q) > 0) {

					?>
					<table width="100%" cellpadding="5" cellspacing="2">
						<tr onselect="" class="cabecalho">
							<td width="10%" class="cabecalho_negrito"><?php echo ('Codigo'); ?></td>
							<td width="60%" class="cabecalho_negrito"><?php echo ('Produto'); ?></td>
							<td width="10%" class="cabecalho_negrito" align="center"><?php echo ('Un.'); ?></td>
							<td width="10%" class="cabecalho_negrito" align="center"><?php echo ('Preco(R$)'); ?></td>
							<td width="10%" class="cabecalho_negrito" align="center"><?php echo ('Estoque'); ?></td>
						</tr>
					<?php

					$cont = 0;
					while($list = $db->fetch_array($list_q)){

						//insere um índice na listagem
						$list['index'] = $cont+1;

						$list['preco'] = number_format($list['preco'],2,",",".");

						$list['qtd_produto'] = number_format($list['qtd_produto'],2,",","");
						if($list['tipo']=='E')$list['qtd_produto']="-----";

						// coloca em negrito a string que foi encontrada na palavra
						$list['descricao_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['descricao']);
						$list['codigo_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['id']);

						?>
						<tr onselect="
							this.text.value = '<?php echo (addslashes(utf8_encode($list['descricao']))); ?>';
							$('<?php echo $campoNomeTemp; ?>').value = '<?php echo (utf8_encode(addslashes($list['descricao']))); ?>';
							$('<?php echo $campoID; ?>').value = '<?php echo ($list['id']); ?>';
							$('<?php echo $campoTipo; ?>').value = '<?php echo ($list['tipo']); ?>';
							$('<?php echo $campoFlag; ?>').className = 'selecionou'
						">
							<td class="tb_bord_baixo">&nbsp;<?php echo ($list['codigo_negrito']); ?></td>
							<td class="tb_bord_baixo"><?php echo (utf8_encode($list['descricao_negrito'])); ?></td>
							<td class="tb_bord_baixo" align="center"><?php echo ($list['unidade']); ?></td>
							<td class="tb_bord_baixo" align="right"><?php echo ($list['preco']); ?></td>
							<td class="tb_bord_baixo" align="right"><?php echo ($list['qtd_produto']); ?></td>
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
		  método: Filtra_Produto_AJAX
		  propósito: Filtra_Produto_AJAX
		*/

		function Filtra_Produto_AJAX ( $filtro, $campoID , $idproduto, $mostraDetalhes) {

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
			$filtro = utf8_decode(addslashes($filtro));
			$campoID = utf8_decode($campoID);
			$mostraDetalhes = utf8_decode($mostraDetalhes);


			// campos de controle
			$campoNomeTemp = $campoID . "_NomeTemp";
			$campoFlag = $campoID . "_Flag";

			$list_sql = "		SELECT
											PRD.*, UNV.*,PRDFL.qtd_produto
										FROM
           						{$conf['db_name']}produto PRD
           							INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda=UNV.idunidade_venda
												INNER JOIN {$conf['db_name']}produto_filial PRDFL ON PRDFL.idproduto = PRD.idproduto
           							
										WHERE
											(
												UPPER(PRD.descricao_produto) LIKE UPPER('%{$filtro}%')
													OR 
												UPPER(PRD.idproduto) LIKE UPPER('%{$filtro}%')
											)
											AND
											PRD.produto_comercializado = '1' AND
											PRD.idproduto != $idproduto AND
											PRDFL.idfilial = ".$_SESSION['idfilial_usuario']."
										ORDER BY
											PRD.descricao_produto ASC ";
											

			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);


			if($list_q){

				// testa se retornou algum registro
				if ($db->num_rows($list_q) > 0) {

					?>
					<table width="100%" cellpadding="5" cellspacing="2">
						<tr onselect="" class="cabecalho">
							<td class="cabecalho_negrito" align="right"><?php echo ('Codigo'); ?></td>
							<td width="70%" class="cabecalho_negrito"><?php echo ('Produto'); ?></td>
							<td class="cabecalho_negrito" align="right"><?php echo ('Estoque'); ?></td>
							<td class="cabecalho_negrito" align="right"><?php echo ('Un.'); ?></td>
						</tr>
					<?php

					$cont = 0;
					while($list = $db->fetch_array($list_q)){

						//insere um índice na listagem
						$list['index'] = $cont+1;


						$list['qtd_produto'] = number_format($list['qtd_produto'],2,",","");

						// coloca em negrito a string que foi encontrada na palavra
						$list['descricao_produto_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['descricao_produto']);
						$list['sigla_unidade_venda_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['sigla_unidade_venda']);
						$list['codigo_produto_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['idproduto']);

						?>

						<tr onselect="
							this.text.value = '<?php echo (addslashes($list['descricao_produto'])); ?>';
							$('<?php echo $campoID; ?>').value = '<?php echo ($list['idproduto']); ?>';
							$('<?php echo $campoNomeTemp; ?>').value = '<?php echo (addslashes($list['descricao_produto'])); ?>';
							$('<?php echo $campoFlag; ?>').className = 'selecionou';

							<?php if ($mostraDetalhes == 1) { ?>
								xajax_Mostra_Detalhes_Produto_AJAX(<?php echo ($list['idproduto']); ?>);
							<?php } ?>
	
						">

							<td class="tb_bord_baixo">&nbsp;<?php echo ($list['codigo_produto_negrito']); ?></td>
							<td class="tb_bord_baixo">&nbsp;<?php echo ($list['descricao_produto_negrito']); ?></td>
							<td class="tb_bord_baixo">&nbsp;<?php echo ($list['qtd_produto']); ?></td>
							<td class="tb_bord_baixo">&nbsp;<?php echo $list['sigla_unidade_venda_negrito']; ?></td>
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
		 método: Filtra_Produto_AJAX
		 propósito: Filtra_Produto_AJAX
		 */
		
		function Filtra_Produto_OS_AJAX ( $filtro, $campoID , $idproduto, $mostraDetalhes = false) {
		
			// variáveis globais
			global $form, $conf, $db, $falha, $parametros;
			//---------------------
		
			// verifica qual a pagina atual
			if (!isset($_GET["page"])) $pg = 0;
			else $pg = $_GET["page"];
		
			// maximo numero de registros listados
			$rppg = $conf['rppg_auto_completar'];
		
			// volta o filtro para a codificação original
			$filtro = utf8_decode(addslashes($filtro));
			$campoID = utf8_decode($campoID);
			$mostraDetalhes = utf8_decode($mostraDetalhes);
			
			$filtro_departamento = 'DPRT.iddepartamento = '.$parametros->getParam('iddepartamento_os') .' AND ';
		
			// campos de controle
			$campoNomeTemp = $campoID . "_NomeTemp";
			$campoFlag = $campoID . "_Flag";
		
			$list_sql = "		SELECT
			PRD.*, UNV.*,PRDFL.qtd_produto
			FROM
			{$conf['db_name']}produto PRD
			INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda=UNV.idunidade_venda
			INNER JOIN {$conf['db_name']}produto_filial PRDFL ON PRDFL.idproduto = PRD.idproduto
			INNER JOIN {$conf['db_name']}secao SEC ON SEC.idsecao = PRD.idsecao
			INNER JOIN {$conf['db_name']}departamento DPRT ON DPRT.iddepartamento = SEC.iddepartamento
		
			WHERE
			( 	
				UPPER(PRD.descricao_produto) LIKE UPPER('%{$filtro}%')
					OR
				UPPER(PRD.idproduto) LIKE UPPER('%{$filtro}%')
			)
			AND
			$filtro_departamento
			PRDFL.idfilial = ".$_SESSION['idfilial_usuario']."
			ORDER BY
			PRD.descricao_produto ASC ";
		
			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);
		
		
				if($list_q){
		
					// testa se retornou algum registro
					if ($db->num_rows($list_q) > 0) {
		
					?>
							<table width="100%" cellpadding="5" cellspacing="2">
								<tr onselect="" class="cabecalho">
									<td class="cabecalho_negrito" align="right"><?php echo ('Codigo'); ?></td>
									<td width="70%" class="cabecalho_negrito"><?php echo ('Produto'); ?></td>
									<td class="cabecalho_negrito" align="right"><?php echo ('Estoque'); ?></td>
									<td class="cabecalho_negrito" align="right"><?php echo ('Un.'); ?></td>
								</tr>
							<?php
		
							$cont = 0;
							while($list = $db->fetch_array($list_q)){
		
								//insere um índice na listagem
								$list['index'] = $cont+1;
		
		
								$list['qtd_produto'] = number_format($list['qtd_produto'],2,",","");
								$list['descricao_produto'] = htmlentities($list['descricao_produto']);
								$filtro = htmlentities($filtro);

								// coloca em negrito a string que foi encontrada na palavra
								$list['descricao_produto_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['descricao_produto']);
								$list['sigla_unidade_venda_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['sigla_unidade_venda']);
								$list['codigo_produto_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['idproduto']);
		
								?>
		
								<tr onselect="
									this.text.value = '<?php echo (addslashes($list['descricao_produto'])); ?>';
									$('<?php echo $campoID; ?>').value = '<?php echo ($list['idproduto']); ?>';
									$('<?php echo $campoNomeTemp; ?>').value = '<?php echo (addslashes($list['descricao_produto'])); ?>';
									$('<?php echo $campoFlag; ?>').className = 'selecionou';
		
									<?php if ($mostraDetalhes == 1) { ?>
										xajax_Mostra_Detalhes_Produto_AJAX(<?php echo ($list['idproduto']); ?>);
									<?php } ?>
			
								">
		
									<td class="tb_bord_baixo">&nbsp;<?php echo ($list['codigo_produto_negrito']); ?></td>
									<td class="tb_bord_baixo">&nbsp;<?php echo ($list['descricao_produto_negrito']); ?></td>
									<td class="tb_bord_baixo">&nbsp;<?php echo ($list['qtd_produto']); ?></td>
									<td class="tb_bord_baixo">&nbsp;<?php echo $list['sigla_unidade_venda_negrito']; ?></td>
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
		  método: Busca_Dados_Produto_AJAX
		  propósito: Busca_Dados_Produto_AJAX
		  
		  $idproduto = codigo do produto
		  $qtd_produto = quantidade do produto
			$retorno = "R" se for pra retornar o registro, "A" pra retornar um array
		*/

		function Busca_Dados_Produto_AJAX ($idproduto, $qtd_produto, $retorno = "A"){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT
											PRD.*,
											UNV.sigla_unidade_venda,
											PRDFL.*

										FROM {$conf['db_name']}produto PRD
											INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda = UNV.idunidade_venda
											INNER JOIN {$conf['db_name']}produto_filial PRDFL ON PRDFL.idproduto = PRD.idproduto

										WHERE
											PRD.idproduto = $idproduto
											  AND
											PRDFL.idfilial = {$_SESSION['idfilial_usuario']}
									";

			//manda fazer a paginação
			$list_q = $db->query($list_sql);

			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					$list['qtd_produto_estoque'] = $list['qtd_produto'];
					$list['qtd_produto'] = $qtd_produto;

					if ($list['percentual_max_desconto_produto'] == "") $list['percentual_max_desconto_produto'] = "0.00";



					// se for tributato, arruma a aliquota
					if ( ($list["situacao_tributaria_produto"] == "S") || ($list["situacao_tributaria_produto"] == "T") ) {
						$icms_produto_formatado = str_replace(".","",$list["icms_produto"]);	
						if (strlen($icms_produto_formatado) == 3) $icms_produto_formatado = "0" . $icms_produto_formatado;
						else if (strlen($icms_produto_formatado) != 4) $icms_produto_formatado = "FF";
					}
					else { // se for N, I ou F, muda para NN, II ou FF
						$icms_produto_formatado = $list["situacao_tributaria_produto"] . $list["situacao_tributaria_produto"];
					}
					if ($icms_produto_formatado == "") $icms_produto_formatado = "FF";
					//----------------------------------------------------------------------------

					$list["icms_produto_formatado"] = $icms_produto_formatado;


					$list_produto = $list;

				  $list_return[] = $list;

          $cont++;
				}

				// retorna o array
				if ($retorno == "A")
					return $list_return;
				// retorna o registro
				else if ($retorno == "R")
					return $list_produto;

			}
			else{
				$this->err = $falha['listar'];
				return(0);
			}
		}


		/**
		  método: Busca_Dados_Encartelamento_AJAX
		  propósito: Busca_Dados_Encartelamento_AJAX

		  $idproduto = codigo do produto
		  $qtd_encartelamento = quantidade do encartelamento
		*/

		function Busca_Dados_Encartelamento_AJAX ($idencartelamento, $qtd_encartelamento){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT
											ENCP.* 

										FROM
											{$conf['db_name']}encartelamento_produto ENCP

										WHERE
											ENCP.idencartelamento = $idencartelamento
									";

			//manda fazer a paginação
			$list_q = $db->query($list_sql);

			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					$list_produto = $this->Busca_Dados_Produto_AJAX ($list['idproduto'], $list['qtd'], "R");

					// a quantidade do produto é a quantidade que ele tem no encartelamento vezes a quantidade de encartelamento que foi pedida
					$qtd_encartelamento = str_replace(",",".",$qtd_encartelamento);
					$qtd_produto_no_encartelamento = str_replace(",",".",$list_produto['qtd_produto']);

					$list_produto['qtd_produto'] = $qtd_produto_no_encartelamento * $qtd_encartelamento;
					$list_produto['qtd_produto'] = number_format($list_produto['qtd_produto'],2,",","");



          $list_return[] = $list_produto;

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

			if ($ordem == "") $ordem = " ORDER BY PRD.descricao_produto ASC";

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT
											PRD.*   , S.nome_secao, D.nome_departamento, UNV.*
										FROM
           						{$conf['db_name']}produto PRD
												 INNER JOIN {$conf['db_name']}secao S ON PRD.idsecao=S.idsecao
													INNER JOIN {$conf['db_name']}departamento D ON S.iddepartamento=D.iddepartamento
														INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda=UNV.idunidade_venda
           					WHERE
           					  PRD.produto_comercializado = '1' AND(
           					 	UPPER(PRD.descricao_produto) LIKE UPPER('%{$busca}%') OR
           					  UPPER(PRD.localizacao_produto) LIKE UPPER('%{$busca}%') OR
           					  UPPER(PRD.referencia_produto) LIKE UPPER('%{$busca}%')
           					  
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


					if ($list['data_cadastro_produto'] != '0000-00-00') $list['data_cadastro_produto'] = $form->FormataDataParaExibir($list['data_cadastro_produto']);
					else $list['data_cadastro_produto'] = "";


					if ($list['percentual_desconto_produto'] != "") $list['percentual_desconto_produto'] = number_format($list['percentual_desconto_produto'],2,",","");
					if ($list['peso_bruto_produto'] != "") $list['peso_bruto_produto'] = number_format($list['peso_bruto_produto'],2,",","");
					if ($list['peso_liquido_produto'] != "") $list['peso_liquido_produto'] = number_format($list['peso_liquido_produto'],2,",","");
					if ($list['qtd_unitaria_embalagem_compra_produto'] != "") $list['qtd_unitaria_embalagem_compra_produto'] = number_format($list['qtd_unitaria_embalagem_compra_produto'],2,",","");
					if ($list['qtd_unitaria_embalagem_venda_produto'] != "") $list['qtd_unitaria_embalagem_venda_produto'] = number_format($list['qtd_unitaria_embalagem_venda_produto'],2,",","");
					if ($list['ipi_produto'] != "") $list['ipi_produto'] = number_format($list['ipi_produto'],2,",","");
					if ($list['frete_produto'] != "") $list['frete_produto'] = number_format($list['frete_produto'],2,",","");
					if ($list['ipi_sobre_frete_produto'] != "") $list['ipi_sobre_frete_produto'] = number_format($list['ipi_sobre_frete_produto'],2,",","");
					if ($list['embalagem_produto'] != "") $list['embalagem_produto'] = number_format($list['embalagem_produto'],2,",","");
					if ($list['ipi_sobre_embalagem_produto'] != "") $list['ipi_sobre_embalagem_produto'] = number_format($list['ipi_sobre_embalagem_produto'],2,",","");
					if ($list['substituicao_ou_icms_produto'] != "") $list['substituicao_ou_icms_produto'] = number_format($list['substituicao_ou_icms_produto'],2,",","");
					if ($list['custo_financeiro_produto'] != "") $list['custo_financeiro_produto'] = number_format($list['custo_financeiro_produto'],2,",","");
					if ($list['comissao_interno_produto'] != "") $list['comissao_interno_produto'] = number_format($list['comissao_interno_produto'],2,",","");
					if ($list['comissao_externo_produto'] != "") $list['comissao_externo_produto'] = number_format($list['comissao_externo_produto'],2,",","");
					if ($list['comissao_representante_produto'] != "") $list['comissao_representante_produto'] = number_format($list['comissao_representante_produto'],2,",","");
					if ($list['comissao_operador_telemarketing_produto'] != "") $list['comissao_operador_telemarketing_produto'] = number_format($list['comissao_operador_telemarketing_produto'],2,",","");
					if ($list['preco_balcao_produto'] != "") $list['preco_balcao_produto'] = number_format($list['preco_balcao_produto'],2,",","");
					if ($list['preco_oferta_produto'] != "") $list['preco_oferta_produto'] = number_format($list['preco_oferta_produto'],2,",","");
					if ($list['preco_atacado_produto'] != "") $list['preco_atacado_produto'] = number_format($list['preco_atacado_produto'],2,",","");
					if ($list['preco_telemarketing_produto'] != "") $list['preco_telemarketing_produto'] = number_format($list['preco_telemarketing_produto'],2,",","");
					if ($list['preco_custo_produto'] != "") $list['preco_custo_produto'] = number_format($list['preco_custo_produto'],2,",","");




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

			if ($ordem == "") $ordem = " ORDER BY PRD.descricao_produto ASC";

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------


			if ($filtro_where != "") $filtro_where = " WHERE ( " . $filtro_where . " ) ";


			$list_sql = "	SELECT
											PRD.*   , S.nome_secao, D.nome_departamento, UNV.*
										FROM
           						{$conf['db_name']}produto PRD
												 INNER JOIN {$conf['db_name']}secao S ON PRD.idsecao=S.idsecao
													INNER JOIN {$conf['db_name']}departamento D ON S.iddepartamento=D.iddepartamento
														INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda=UNV.idunidade_venda

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

					if ($list['data_cadastro_produto'] != '0000-00-00') $list['data_cadastro_produto'] = $form->FormataDataParaExibir($list['data_cadastro_produto']);
					else $list['data_cadastro_produto'] = "";


					if ($list['percentual_desconto_produto'] != "") $list['percentual_desconto_produto'] = number_format($list['percentual_desconto_produto'],2,",","");
					if ($list['peso_bruto_produto'] != "") $list['peso_bruto_produto'] = number_format($list['peso_bruto_produto'],2,",","");
					if ($list['peso_liquido_produto'] != "") $list['peso_liquido_produto'] = number_format($list['peso_liquido_produto'],2,",","");
					if ($list['qtd_unitaria_embalagem_compra_produto'] != "") $list['qtd_unitaria_embalagem_compra_produto'] = number_format($list['qtd_unitaria_embalagem_compra_produto'],2,",","");
					if ($list['qtd_unitaria_embalagem_venda_produto'] != "") $list['qtd_unitaria_embalagem_venda_produto'] = number_format($list['qtd_unitaria_embalagem_venda_produto'],2,",","");
					if ($list['ipi_produto'] != "") $list['ipi_produto'] = number_format($list['ipi_produto'],2,",","");
					if ($list['frete_produto'] != "") $list['frete_produto'] = number_format($list['frete_produto'],2,",","");
					if ($list['ipi_sobre_frete_produto'] != "") $list['ipi_sobre_frete_produto'] = number_format($list['ipi_sobre_frete_produto'],2,",","");
					if ($list['embalagem_produto'] != "") $list['embalagem_produto'] = number_format($list['embalagem_produto'],2,",","");
					if ($list['ipi_sobre_embalagem_produto'] != "") $list['ipi_sobre_embalagem_produto'] = number_format($list['ipi_sobre_embalagem_produto'],2,",","");
					if ($list['substituicao_ou_icms_produto'] != "") $list['substituicao_ou_icms_produto'] = number_format($list['substituicao_ou_icms_produto'],2,",","");
					if ($list['custo_financeiro_produto'] != "") $list['custo_financeiro_produto'] = number_format($list['custo_financeiro_produto'],2,",","");
					if ($list['comissao_interno_produto'] != "") $list['comissao_interno_produto'] = number_format($list['comissao_interno_produto'],2,",","");
					if ($list['comissao_externo_produto'] != "") $list['comissao_externo_produto'] = number_format($list['comissao_externo_produto'],2,",","");
					if ($list['comissao_representante_produto'] != "") $list['comissao_representante_produto'] = number_format($list['comissao_representante_produto'],2,",","");
					if ($list['comissao_operador_telemarketing_produto'] != "") $list['comissao_operador_telemarketing_produto'] = number_format($list['comissao_operador_telemarketing_produto'],2,",","");
					if ($list['preco_balcao_produto'] != "") $list['preco_balcao_produto'] = number_format($list['preco_balcao_produto'],2,",","");
					if ($list['preco_oferta_produto'] != "") $list['preco_oferta_produto'] = number_format($list['preco_oferta_produto'],2,",","");
					if ($list['preco_atacado_produto'] != "") $list['preco_atacado_produto'] = number_format($list['preco_atacado_produto'],2,",","");
					if ($list['preco_telemarketing_produto'] != "") $list['preco_telemarketing_produto'] = number_format($list['preco_telemarketing_produto'],2,",","");
					if ($list['preco_custo_produto'] != "") $list['preco_telemarketing_produto'] = number_format($list['preco_custo_produto'],2,",","");




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
		  método: getById
		  propósito: busca informações
		*/
		function getById($idproduto){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											PRD.*, S.*, D.*,UNV.*
										FROM
											{$conf['db_name']}produto PRD
												 INNER JOIN {$conf['db_name']}secao S ON PRD.idsecao=S.idsecao
													INNER JOIN {$conf['db_name']}departamento D ON S.iddepartamento=D.iddepartamento
													INNER JOIN {$conf['db_name']}unidade_venda UNV ON UNV.idunidade_venda=PRD.idunidade_venda
												

										WHERE
											 PRD.idproduto = $idproduto";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				if ($get['data_cadastro_produto'] != '0000-00-00') $get['data_cadastro_produto'] = $form->FormataDataParaExibir($get['data_cadastro_produto']); 
				else $get['data_cadastro_produto'] = "";
					
				
				if ($get['percentual_max_desconto_produto'] != "") $get['percentual_max_desconto_produto'] = number_format($get['percentual_max_desconto_produto'],2,",","");
				if ($get['peso_bruto_produto'] != "") $get['peso_bruto_produto'] = number_format($get['peso_bruto_produto'],2,",",""); 
				if ($get['peso_liquido_produto'] != "") $get['peso_liquido_produto'] = number_format($get['peso_liquido_produto'],2,",",""); 
				if ($get['qtd_unitaria_embalagem_compra_produto'] != "") $get['qtd_unitaria_embalagem_compra_produto'] = number_format($get['qtd_unitaria_embalagem_compra_produto'],2,",",""); 
				if ($get['qtd_unitaria_embalagem_venda_produto'] != "") $get['qtd_unitaria_embalagem_venda_produto'] = number_format($get['qtd_unitaria_embalagem_venda_produto'],2,",",""); 
				if ($get['frete_produto'] != "") $get['frete_produto'] = number_format($get['frete_produto'],2,",",""); 
				if ($get['ipi_sobre_frete_produto'] != "") $get['ipi_sobre_frete_produto'] = number_format($get['ipi_sobre_frete_produto'],2,",",""); 
				if ($get['embalagem_produto'] != "") $get['embalagem_produto'] = number_format($get['embalagem_produto'],2,",",""); 
				if ($get['ipi_sobre_embalagem_produto'] != "") $get['ipi_sobre_embalagem_produto'] = number_format($get['ipi_sobre_embalagem_produto'],2,",",""); 
				if ($get['substituicao_ou_icms_produto'] != "") $get['substituicao_ou_icms_produto'] = number_format($get['substituicao_ou_icms_produto'],2,",",""); 
				if ($get['custo_financeiro_produto'] != "") $get['custo_financeiro_produto'] = number_format($get['custo_financeiro_produto'],2,",",""); 
				if ($get['comissao_interno_produto'] != "") $get['comissao_interno_produto'] = number_format($get['comissao_interno_produto'],2,",","");
				if ($get['comissao_externo_produto'] != "") $get['comissao_externo_produto'] = number_format($get['comissao_externo_produto'],2,",","");
				if ($get['comissao_representante_produto'] != "") $get['comissao_representante_produto'] = number_format($get['comissao_representante_produto'],2,",","");
				if ($get['comissao_operador_telemarketing_produto'] != "") $get['comissao_operador_telemarketing_produto'] = number_format($get['comissao_operador_telemarketing_produto'],2,",","");
				if ($get['icms_produto'] != "") $get['icms_produto'] = number_format($get['icms_produto'],2,",","");
				if ($get['ipi_produto'] != "") $get['ipi_produto'] = number_format($get['ipi_produto'],2,",","");

					
										
				
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
			
			if ($ordem == "") $ordem = " ORDER BY PRD.descricao_produto ASC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											PRD.*   , S.nome_secao , UNV.*, D.nome_departamento
										FROM
           						{$conf['db_name']}produto PRD 
												 INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda=UNV.idunidade_venda 
												 INNER JOIN {$conf['db_name']}secao S ON PRD.idsecao=S.idsecao
												 	INNER JOIN {$conf['db_name']}departamento D ON S.iddepartamento=D.iddepartamento
												
										$filtro
										AND PRD.produto_comercializado = '1'
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
					
					if ($list['data_cadastro_produto'] != '0000-00-00') $list['data_cadastro_produto'] = $form->FormataDataParaExibir($list['data_cadastro_produto']); 
					else $list['data_cadastro_produto'] = "";
					
					
					if ($list['percentual_desconto_produto'] != "") $list['percentual_desconto_produto'] = number_format($list['percentual_desconto_produto'],2,",",""); 
					if ($list['peso_bruto_produto'] != "") $list['peso_bruto_produto'] = number_format($list['peso_bruto_produto'],2,",",""); 
					if ($list['peso_liquido_produto'] != "") $list['peso_liquido_produto'] = number_format($list['peso_liquido_produto'],2,",",""); 
					if ($list['qtd_unitaria_embalagem_compra_produto'] != "") $list['qtd_unitaria_embalagem_compra_produto'] = number_format($list['qtd_unitaria_embalagem_compra_produto'],2,",",""); 
					if ($list['qtd_unitaria_embalagem_venda_produto'] != "") $list['qtd_unitaria_embalagem_venda_produto'] = number_format($list['qtd_unitaria_embalagem_venda_produto'],2,",",""); 
					if ($list['comissao_interno_produto'] != "") $list['comissao_interno_produto'] = number_format($list['comissao_interno_produto'],2,",","");
					if ($list['comissao_externo_produto'] != "") $list['comissao_externo_produto'] = number_format($list['comissao_externo_produto'],2,",","");
					if ($list['comissao_representante_produto'] != "") $list['comissao_representante_produto'] = number_format($list['comissao_representante_produto'],2,",","");
					if ($list['comissao_operador_telemarketing_produto'] != "") $list['comissao_operador_telemarketing_produto'] = number_format($list['comissao_operador_telemarketing_produto'],2,",","");
					if ($list['preco_balcao_produto'] != "") $list['preco_balcao_produto'] = number_format($list['preco_balcao_produto'],2,",",""); 
					if ($list['preco_oferta_produto'] != "") $list['preco_oferta_produto'] = number_format($list['preco_oferta_produto'],2,",",""); 
					if ($list['preco_atacado_produto'] != "") $list['preco_atacado_produto'] = number_format($list['preco_atacado_produto'],2,",",""); 
					if ($list['preco_telemarketing_produto'] != "") $list['preco_telemarketing_produto'] = number_format($list['preco_telemarketing_produto'],2,",",""); 
					if ($list['preco_custo_produto'] != "") $list['preco_telemarketing_produto'] = number_format($list['preco_custo_produto'],2,",","");
					
					
					
					
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
			
			
			$camposNumericos= array('percentual_max_desconto_produto', 'peso_bruto_produto', 'peso_liquido_produto', 'idunidade_venda', 'qtd_unitaria_embalagem_compra_produto', 'qtd_unitaria_embalagem_venda_produto',
					'comissao_interno_produto', 'comissao_externo_produto', 'comissao_representante_produto', 'comissao_operador_telemarketing_produto', 'icms_produto', 'ipi_produto');

			foreach($camposNumericos as $campo){
				$info[$campo] = empty($info[$campo]) ? 'NULL' : $info[$campo];
			}
			
			$set_sql = "  INSERT INTO
		                  {$conf['db_name']}produto
		                    (
		                    
												idsecao, 
												descricao_produto, 
												localizacao_produto, 
												aplicacao_produto, 
												percentual_max_desconto_produto,
												referencia_produto, 
												observacao_produto, 
												data_cadastro_produto, 
												peso_bruto_produto, 
												peso_liquido_produto, 
												idunidade_venda, 
												qtd_unitaria_embalagem_compra_produto, 
												qtd_unitaria_embalagem_venda_produto, 
												comissao_interno_produto, 
												comissao_externo_produto, 
												comissao_representante_produto, 
												comissao_operador_telemarketing_produto,
												produto_comercializado,
												codigo_produto,
												cst_produto,
												icms_produto,
												ipi_produto,
												situacao_tributaria_produto
												

												
												)
		                VALUES
		                    (
		                    
		                    " . $info['idsecao'] . ",  
												'" . $info['descricao_produto'] . "',  
												'" . $info['localizacao_produto'] . "',  
												'" . $info['aplicacao_produto'] . "',  
												" . $info['percentual_max_desconto_produto'] . ",
												'" . $info['referencia_produto'] . "',  
												'" . $info['observacao_produto'] . "',  
												'" . $info['data_cadastro_produto'] . "',  
												" . $info['peso_bruto_produto'] . ",  
												" . $info['peso_liquido_produto'] . ",  
												" . $info['idunidade_venda'] . ",  
												" . $info['qtd_unitaria_embalagem_compra_produto'] . ",  
												" . $info['qtd_unitaria_embalagem_venda_produto'] . ",  
												" . $info['comissao_interno_produto'] . ",  
												" . $info['comissao_externo_produto'] . ",  
												" . $info['comissao_representante_produto'] . ",  
												" . $info['comissao_operador_telemarketing_produto'] . ",
												'" . $info['produto_comercializado']. "',
												'" . $info['codigo_produto']. "',
												'" . $info['cst_produto']. "',
												" . $info['icms_produto'] . ",
												" . $info['ipi_produto'] . ",
												'" . $info['situacao_tributaria_produto']. "'

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
		function update($idproduto, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}produto
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
				
				if($tipo_campo == "lit"){
					$valor = addslashes($valor);
					$update_sql .= "$nome_campo = '$valor'";
				}
				elseif($tipo_campo == "num"){
					if (empty($valor)) $valor = 'NULL';
					$update_sql .= "$nome_campo = $valor";
				}
					
				$cont++;
				
				//testa se é o último
				if($cont != $cont_validos){
					$update_sql .= ", ";
				}
				
			}
			

			//completa o sql com a restrição
			$update_sql .= " WHERE  idproduto = $idproduto ";
			
			
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
		function delete($idproduto){
			
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
													{$conf['db_name']}produto
												WHERE
													 idproduto = $idproduto ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY idsecao ASC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}produto
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
