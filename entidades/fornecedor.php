<?php
	
	class fornecedor {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function fornecedor(){
			// não faz nada
		}

/*
		RetornaFornecedor, utilizado no Insere_Fornecedor_Ajax
	 que está no arquivo : admin/fornecedor_ajax.php
*/
		function RetornaFornecedor($idfornecedor){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$get_sql = "	SELECT
											FORN.*,EDR.*,EST.*,CID.*
										FROM
           						{$conf['db_name']}fornecedor FORN
           						INNER JOIN {$conf['db_name']}endereco EDR	ON FORN.idendereco_fornecedor = EDR.idendereco
								 			LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade = CID.idcidade
								 			LEFT OUTER JOIN {$conf['db_name']}estado EST ON EDR.idestado = EST.idestado
										WHERE
										  FORN.idfornecedor = $idfornecedor
										ORDER BY
											FORN.nome_fornecedor ASC
											";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);

				$get['telefone_fornecedor'] = $form->FormataTelefoneParaExibir($get['telefone_fornecedor']);



				//retorna o vetor associativo com os dados
				return $get;
			}
			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}

		}
		
		/**
		  método: Filtra_Fornecedor_AJAX
		  propósito: Filtra_Fornecedor_AJAX
		*/

		function Filtra_Fornecedor_AJAX ( $filtro, $campoID, $mostraDetalhes,$resumido=false, $inserirEndereco = false ) {

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
											FORN.*,EDR.*,EST.*,CID.*
										FROM
           						{$conf['db_name']}fornecedor FORN
           						INNER JOIN {$conf['db_name']}endereco EDR	ON FORN.idendereco_fornecedor = EDR.idendereco
								 			LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade = CID.idcidade
								 			LEFT OUTER JOIN {$conf['db_name']}estado EST ON EDR.idestado = EST.idestado
										WHERE
											UPPER(FORN.nome_fornecedor) LIKE UPPER('%{$filtro}%') OR
											UPPER(CID.nome_cidade) LIKE UPPER('%{$filtro}%') OR
											UPPER(EST.sigla_estado) LIKE UPPER('%{$filtro}%') OR
											UPPER(FORN.telefone_fornecedor) LIKE UPPER('%{$filtro}%')
										ORDER BY
											FORN.nome_fornecedor ASC
											";


			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);


			if($list_q){

				// testa se retornou algum registro
				if ($db->num_rows($list_q) > 0) {

					?>
					<table width="100%" cellpadding="5" cellspacing="2">
						<tr onselect="" class="cabecalho">
							<td width="70%" class="cabecalho_negrito"><?php echo ('Fornecedor'); ?></td>
						
							<?php if(!$resumido):?>							
							<td class="cabecalho_negrito" align="center"><?php echo ('Cidade'); ?></td>
							<td class="cabecalho_negrito" align="right"><?php echo ('Telefone'); ?></td>
							<?php endif;?>
						</tr>
					<?php

					$cont = 0;
					while($list = $db->fetch_array($list_q)){

						//insere um índice na listagem
						$list['index'] = $cont+1;
						
						$list['telefone_fornecedor'] = $form->FormataTelefoneParaExibir($list['telefone_fornecedor']);
						
						if ($mostraDetalhes == 1) {
								$list['info_fornecedor'] = "
									<table width=95% align=center>
										<tr>
											<th>Fornecedor</th>
											<th align=center>Nome da cidade</th>
											<th align=center>Telefone</th>
										</tr>
	
										<tr>
											<td><a class=menu_item href=" . $conf['addr'] . "/admin/fornecedor.php?ac=editar&idfornecedor=" . $list['idfornecedor'] . ">Fornecedor: " . $list['nome_fornecedor'] . "</a></td>
											<td align=center><a class=menu_item href=" . $conf['addr'] . "/admin/fornecedor.php?ac=editar&idfornecedor=" . $list['idfornecedor'] . ">Cidade: " . $list['nome_cidade'] . " / " . $list['sigla_estado'] . "</a></td>
											<td align=center><a class=menu_item href=" . $conf['addr'] . "/admin/fornecedor.php?ac=editar&idfornecedor=" . $list['idfornecedor'] . ">Telefone: " . $list['telefone_fornecedor'] . "</a></td>
										</tr>
									</table>";

								$list['info_fornecedor'] = ereg_replace("(\r\n|\n|\r)", "", $list['info_fornecedor']);
							}


						$list['nome_fornecedor'] = htmlentities($list['nome_fornecedor']);
						$filtro = htmlentities($filtro);
							
						// coloca em negrito a string que foi encontrada na palavra
						$list['nome_fornecedor_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['nome_fornecedor']);
						
						if(!$resumido){
							$list['nome_cidade_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['nome_cidade']);
							$list['sigla_estado_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['sigla_estado']);
							$list['telefone_fornecedor_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['telefone_fornecedor']);
							if ($list['nome_cidade_negrito'] != ""){$list['nome_cidade_negrito'] = utf8_encode($list['nome_cidade_negrito'])."/".utf8_encode($list['sigla_estado_negrito']);}
						}						
						?>
						
						<tr onselect="
							this.text.value ='<?php echo ($list['nome_fornecedor']); ?>';
							$('<?php echo $campoNomeTemp; ?>').value = '<?php echo ($list['nome_fornecedor']); ?>';
							$('<?php echo $campoID; ?>').value = '<?php echo ($list['idfornecedor']); ?>';
							
							<?php if ($inserirEndereco): ?>
								$('endereco_fornecedor').value = '<?php echo utf8_encode($endereco->formataStringEndereco($list));?>';
							<?php endif; ?>
								
							$('<?php echo $campoFlag; ?>').className = 'selecionou'
							<?php if ($mostraDetalhes == 1) ?>
								$('dados_fornecedor').innerHTML = '<?php echo ($list['info_fornecedor']);
							?>';
							
						">
							<td class="tb_bord_baixo">&nbsp;<?php echo ($list['nome_fornecedor_negrito']); ?></td>
							<?php if(!$resumido):?>
								<td class="tb_bord_baixo">&nbsp;<?php echo ($list['nome_cidade_negrito']); ?></td>
								<td class="tb_bord_baixo">&nbsp;<?php echo ($list['telefone_fornecedor_negrito']); ?></td>
							<?php endif;?>
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
		  método: Busca_Generica
		  propósito: Busca_Generica
		*/

		function Busca_Generica ( $pg, $rppg, $busca = "", $ordem = "", $url = ""){

			if ($ordem == "") $ordem = " ORDER BY FORN.nome_fornecedor ASC";

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT
								FORN.*   , EDR1.idendereco , RAT.descricao_atividade , EDR2.idendereco,
								EST1.sigla_estado, CID1.nome_cidade, BAI1.nome_bairro, EDR1.logradouro,
								EDR1.numero, EDR1.complemento 
									
							FROM {$conf['db_name']}fornecedor FORN
								 INNER JOIN {$conf['db_name']}endereco EDR1 ON FORN.idendereco_fornecedor=EDR1.idendereco
								 INNER JOIN {$conf['db_name']}ramo_atividade RAT ON FORN.idramo_atividade=RAT.idramo_atividade
								 INNER JOIN {$conf['db_name']}endereco EDR2 ON FORN.idendereco_representante_fornecedor=EDR2.idendereco
												 
														 -- Tabelas de Endreço --					 
								 LEFT JOIN {$conf['db_name']}estado EST1 ON EST1.idestado = EDR1.idestado
								 LEFT JOIN {$conf['db_name']}cidade CID1 ON CID1.idcidade = EDR1.idcidade												 
								 LEFT JOIN {$conf['db_name']}bairro BAI1 ON BAI1.idbairro = EDR1.idbairro

           					WHERE (
           					 		UPPER(FORN.nome_fornecedor) LIKE UPPER('%{$busca}%') OR
           						  	UPPER(FORN.nome_contato_fornecedor) LIKE UPPER('%{$busca}%')
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

					$list['endereco'] = null;
					if(!empty($list['numero'])) $list['logradouro'] .= ', '.$list['numero'];
					if(!empty($list['logradouro'])) $list['endereco'] .= $list['logradouro'];
					if(!empty($list['nome_bairro'])) $list['endereco'] .= ' - '.$list['nome_bairro'];
					if(!empty($list['nome_cidade'])) $list['endereco'] .= ' - '.$list['nome_cidade'] . '/'. $list['sigla_estado'] ;
 	
					if ($list['tipo_fornecedor'] == 'F') $list['tipo_fornecedor'] = "Pessoa Física";
					else if ($list['tipo_fornecedor'] == 'J') $list['tipo_fornecedor'] = "Pessoa Jurídica";

					$list['telefone_fornecedor'] = $form->FormataTelefoneParaExibir($list['telefone_fornecedor']);
					$list['fax_fornecedor'] = $form->FormataTelefoneParaExibir($list['fax_fornecedor']);
					$list['telefone_representante'] = $form->FormataTelefoneParaExibir($list['telefone_representante']);
					$list['celular_representante'] = $form->FormataTelefoneParaExibir($list['celular_representante']);



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

			if ($ordem == "") $ordem = " ORDER BY FORN.nome_fornecedor ASC";

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------


			if ($filtro_where != "") $filtro_where = " WHERE ( " . $filtro_where . " ) ";


			$list_sql = "	SELECT
								FORN.*   , EDR1.idendereco , RAT.descricao_atividade , EDR2.idendereco,
								EST1.sigla_estado, CID1.nome_cidade, BAI1.nome_bairro, EDR1.logradouro,
								EDR1.numero, EDR1.complemento 
							FROM
           						{$conf['db_name']}fornecedor FORN
							INNER JOIN {$conf['db_name']}endereco EDR1 ON FORN.idendereco_fornecedor=EDR1.idendereco
							INNER JOIN {$conf['db_name']}ramo_atividade RAT ON FORN.idramo_atividade=RAT.idramo_atividade
							INNER JOIN {$conf['db_name']}endereco EDR2 ON FORN.idendereco_representante_fornecedor=EDR2.idendereco

 												-- Tabelas de Endreço --					 
							LEFT JOIN {$conf['db_name']}estado EST1 ON EST1.idestado = EDR1.idestado
							LEFT JOIN {$conf['db_name']}cidade CID1 ON CID1.idcidade = EDR1.idcidade												 
							LEFT JOIN {$conf['db_name']}bairro BAI1 ON BAI1.idbairro = EDR1.idbairro
												 
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

					
					
					$list['endereco'] = null;
					if(!empty($list['numero'])) $list['logradouro'] .= ', '.$list['numero'];
					if(!empty($list['nome_bairro'])) $list['endereco'] .= $list['logradouro'];
					if(!empty($list['nome_bairro'])) $list['endereco'] .= ' - '.$list['nome_bairro'];
					if(!empty($list['nome_cidade'])) $list['endereco'] .= ' - '.$list['nome_cidade'] . '/'. $list['sigla_estado'] ;
 

					if ($list['tipo_fornecedor'] == 'F') $list['tipo_fornecedor'] = "Pessoa Física";
					else if ($list['tipo_fornecedor'] == 'J') $list['tipo_fornecedor'] = "Pessoa Jurídica";

					$list['telefone_fornecedor'] = $form->FormataTelefoneParaExibir($list['telefone_fornecedor']);
					$list['fax_fornecedor'] = $form->FormataTelefoneParaExibir($list['fax_fornecedor']);
					$list['telefone_representante'] = $form->FormataTelefoneParaExibir($list['telefone_representante']);
					$list['celular_representante'] = $form->FormataTelefoneParaExibir($list['celular_representante']);

			
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
		function getById($idfornecedor){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											FORN.*
										FROM
											{$conf['db_name']}fornecedor FORN
        						WHERE
											 FORN.idfornecedor = $idfornecedor ";

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
			
			if ($ordem == "") $ordem = " ORDER BY FORN.nome_fornecedor ASC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											FORN.*   , EDR1.idendereco , RAT.descricao_atividade , EDR2.idendereco
										FROM
           						{$conf['db_name']}fornecedor FORN 
												 INNER JOIN {$conf['db_name']}endereco EDR1 ON FORN.idendereco_fornecedor=EDR1.idendereco
												 INNER JOIN {$conf['db_name']}ramo_atividade RAT ON FORN.idramo_atividade=RAT.idramo_atividade 
												 INNER JOIN {$conf['db_name']}endereco EDR2 ON FORN.idendereco_representante_fornecedor=EDR2.idendereco
												
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
					
					
					
					
					if ($list['tipo_fornecedor'] == 'F') $list['tipo_fornecedor'] = "Pessoa Física"; 
					else if ($list['tipo_fornecedor'] == 'J') $list['tipo_fornecedor'] = "Pessoa Jurídica"; 
					
					$list['telefone_fornecedor'] = $form->FormataTelefoneParaExibir($list['telefone_fornecedor']); 
					$list['fax_fornecedor'] = $form->FormataTelefoneParaExibir($list['fax_fornecedor']); 
					$list['telefone_representante'] = $form->FormataTelefoneParaExibir($list['telefone_representante']); 
					$list['celular_representante'] = $form->FormataTelefoneParaExibir($list['celular_representante']); 
					
					
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
		                  {$conf['db_name']}fornecedor
		                    (
		                    
												tipo_fornecedor, 
												nome_fornecedor, 
												cpf_cnpj, 
												inscricao_estadual_fornecedor, 
												nome_contato_fornecedor, 
												idendereco_fornecedor, 
												telefone_fornecedor, 
												fax_fornecedor, 
												email_fornecedor, 
												site_fornecedor, 
												idramo_atividade, 
												nome_representante, 
												idendereco_representante_fornecedor, 
												telefone_representante, 
												celular_representante, 
												email_representante  
												
												)
		                VALUES
		                    (
		                    
		                    '" . $info['tipo_fornecedor'] . "',  
												'" . $info['nome_fornecedor'] . "',  
												'" . $info['cpf_cnpj'] . "',  
												'" . $info['inscricao_estadual_fornecedor'] . "',  
												'" . $info['nome_contato_fornecedor'] . "',  
												" . $info['idendereco_fornecedor'] . ",  
												'" . $info['telefone_fornecedor'] . "',  
												'" . $info['fax_fornecedor'] . "',  
												'" . $info['email_fornecedor'] . "',  
												'" . $info['site_fornecedor'] . "',  
												" . $info['idramo_atividade'] . ",  
												'" . $info['nome_representante'] . "',  
												" . $info['idendereco_representante_fornecedor'] . ",  
												'" . $info['telefone_representante'] . "',  
												'" . $info['celular_representante'] . "',  
												'" . $info['email_representante'] . "'   
												
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
		function update($idfornecedor, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}fornecedor
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
			$update_sql .= " WHERE  idfornecedor = $idfornecedor ";
			
			
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
		function delete($idfornecedor){
			
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
													{$conf['db_name']}fornecedor
												WHERE
													 idfornecedor = $idfornecedor ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY nome_fornecedor ASC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}fornecedor
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
