<?php
	
	class transportador {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function transportador(){
			// n�o faz nada
		}

		
		
			/**
		  m�todo: Busca_Generica
		  prop�sito: Busca_Generica
		*/

		function Busca_Generica ( $pg, $rppg, $busca = "", $ordem = "", $url = ""){

			if ($ordem == "") $ordem = " ORDER BY TRP.nome_transportador ASC";

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT
											TRP.*
										FROM
           						{$conf['db_name']}transportador TRP
           					WHERE
           					  (
           					 	UPPER(TRP.nome_transportador) LIKE UPPER('%{$busca}%') OR
           					  UPPER(TRP.cpf_cnpj) LIKE UPPER('%{$busca}%')
											)
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


					if ($list['tipo_transportador'] == 'F') $list['tipo_transportador'] = "Pessoa F�sica";
					else if ($list['tipo_transportador'] == 'J') $list['tipo_transportador'] = "Pessoa Jur�dica";

					$list['telefone_transportador'] = $form->FormataTelefoneParaExibir($list['telefone_transportador']);


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
		  m�todo: Filtra_Transportador_AJAX
		  prop�sito: Filtra_Transportador_AJAX
		*/

		function Filtra_Transportador_AJAX ( $filtro, $campoID, $mostraDetalhes ) {

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
			$filtro = utf8_decode($filtro);
			$campoID = utf8_decode($campoID);
			$mostraDetalhes = utf8_decode($mostraDetalhes);

			// campos de controle
			$campoNomeTemp = $campoID . "_NomeTemp";
			$campoFlag = $campoID . "_Flag";

			$list_sql = "	SELECT
											TRP.*, EDR.*, BAR.*, CID.*, EST.*
										FROM
											{$conf['db_name']}transportador TRP
												 LEFT OUTER JOIN {$conf['db_name']}endereco EDR ON TRP.idendereco_transportador=EDR.idendereco
												 LEFT OUTER JOIN {$conf['db_name']}bairro BAR ON EDR.idbairro=BAR.idbairro
												 LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade=CID.idcidade
												 LEFT OUTER JOIN {$conf['db_name']}estado EST ON EDR.idestado=EST.idestado

										WHERE
											UPPER(TRP.nome_transportador) LIKE UPPER('%{$filtro}%')
											  OR
											UPPER(TRP.cpf_cnpj) LIKE UPPER('%{$filtro}%')

										ORDER BY
											TRP.nome_transportador ASC ";


			//manda fazer a pagina��o
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);


			if($list_q){

				// testa se retornou algum registro
				if ($db->num_rows($list_q) > 0) {

					?>
					<table width="100%" cellpadding="5" cellspacing="2">
						<tr onselect="" class="cabecalho">
							<td width="55%" class="cabecalho_negrito"><?php echo ('Transportador'); ?></td>
							<td width="25%" class="cabecalho_negrito" align="center"><?php echo ('CPF/CNPJ'); ?></td>
							<td width="20%" class="cabecalho_negrito" align="center"><?php echo ('Tel.'); ?></td>
						</tr>
					<?php

					$cont = 0;
					while($list = $db->fetch_array($list_q)){

						//insere um �ndice na listagem
						$list['index'] = $cont+1;

						$list['telefone_transportador'] = $form->FormataTelefoneParaExibir($list['telefone_transportador']);


						// coloca em negrito a string que foi encontrada na palavra
						$list['nome_transportador_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['nome_transportador']);
						$list['cpf_cnpj_negrito'] = preg_replace("'$filtro'i","<span class='substring_negrito'>\\0</span>", $list['cpf_cnpj']);

						if ($mostraDetalhes == 1)
							$list['info_transportador'] = "<table class=tb4cantos width=40%><tr><td>Endere�o: " . $list['logradouro'] . " " . $list['numero'] . " " . $list['complemento'] . " CEP " .  $list['cep'] . "</td></tr><tr><td>Bairro: " . $list['nome_bairro'] . " " . $list['nome_cidade'] . " " . $list['sigla_estado'] . "</td></tr><tr><td>Telefone: " . $list['telefone_transportador'] . "</td></tr><tr><td>CPF/CNPJ: " . $list['cpf_cnpj'] . "</td></tr></table>";

						?>
						<tr onselect="
							this.text.value = '<?php echo ($list['nome_transportador']); ?>';
							$('<?php echo $campoNomeTemp; ?>').value = '<?php echo ($list['nome_transportador']); ?>';
							$('<?php echo $campoID; ?>').value = '<?php echo ($list['idtransportador']); ?>';
							$('<?php echo $campoFlag; ?>').className = 'selecionou';

							<?php if ($mostraDetalhes == 1) ?>
								$('dados_transportador').innerHTML = '<?php echo $list['info_transportador']; ?>';

						">
							<td class="tb_bord_baixo"><?php echo ($list['nome_transportador_negrito']); ?></td>
							<td class="tb_bord_baixo" align="center">&nbsp;<?php echo ($list['cpf_cnpj_negrito']); ?></td>
							<td class="tb_bord_baixo" align="center">&nbsp;<?php echo ($list['telefone_transportador']); ?></td>
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
					?>
					<table width="100%" cellpadding="5" cellspacing="2">
						<tr onselect="" class="cabecalho">
							<td width="70%" class="cabecalho_negrito"><?php echo ($conf['listar']); ?></td>
						</tr>
					<?php

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
		  m�todo: BuscaDadosTransportador
		  prop�sito: BuscaDadosTransportador
		*/

		function BuscaDadosTransportador ( $idTransportador ) {

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			// verifica se eh um cliente fisico
			$get_sql = "	SELECT
											TRP.*, EDR.*, BAR.*, CID.*, EST.*
										FROM
											{$conf['db_name']}transportador TRP
												 LEFT OUTER JOIN {$conf['db_name']}endereco EDR ON TRP.idendereco_transportador=EDR.idendereco
												 LEFT OUTER JOIN {$conf['db_name']}bairro BAR ON EDR.idbairro=BAR.idbairro
												 LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade=CID.idcidade
												 LEFT OUTER JOIN {$conf['db_name']}estado EST ON EDR.idestado=EST.idestado
												 
										WHERE
											TRP.idtransportador = $idTransportador

										";


			//executa a query no banco de dados
			$get_transportador_q = $db->query($get_sql);
			$get_transportador = $db->fetch_array($get_transportador_q);


			$get_transportador['telefone_transportador'] = $form->FormataTelefoneParaExibir($get_transportador['telefone_transportador']);
			$get_transportador['fax_transportador'] = $form->FormataTelefoneParaExibir($get_transportador['fax_transportador']);


			//retorna o vetor associativo com os dados
			return $get_transportador;

		}




		/**
		  m�todo: getById
		  prop�sito: busca informa��es
		*/
		function getById($idtransportador){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											TRP.*
										FROM
											{$conf['db_name']}transportador TRP
										WHERE
											 TRP.idtransportador = $idtransportador ";

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
			
			if ($ordem == "") $ordem = " ORDER BY TRP.nome_transportador ASC";
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											TRP.*   , EDR.idendereco
										FROM
           						{$conf['db_name']}transportador TRP 
												 INNER JOIN {$conf['db_name']}endereco EDR ON TRP.idendereco_transportador=EDR.idendereco 
												
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
					
					
					
					
					if ($list['tipo_transportador'] == 'F') $list['tipo_transportador'] = "Pessoa F�sica"; 
					else if ($list['tipo_transportador'] == 'J') $list['tipo_transportador'] = "Pessoa Jur�dica"; 
					
					$list['telefone_transportador'] = $form->FormataTelefoneParaExibir($list['telefone_transportador']); 
					$list['fax_transportador'] = $form->FormataTelefoneParaExibir($list['fax_transportador']); 
					
					
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
		                  {$conf['db_name']}transportador
		                    (
		                    
												tipo_transportador, 
												nome_transportador, 
												cpf_cnpj, 
												instricao_estadual_transportador, 
												idendereco_transportador, 
												telefone_transportador, 
												fax_transportador, 
												email_transportador, 
												site_transportador, 
												observacao_transportador  
												
												)
		                VALUES
		                    (
		                    
		                    '" . $info['tipo_transportador'] . "',  
												'" . $info['nome_transportador'] . "',  
												'" . $info['cpf_cnpj'] . "',  
												'" . $info['instricao_estadual_transportador'] . "',  
												" . $info['idendereco_transportador'] . ",  
												'" . $info['telefone_transportador'] . "',  
												'" . $info['fax_transportador'] . "',  
												'" . $info['email_transportador'] . "',  
												'" . $info['site_transportador'] . "',  
												'" . $info['observacao_transportador'] . "'   
												
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
		function update($idtransportador, $info){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}transportador
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
			$update_sql .= " WHERE  idtransportador = $idtransportador ";
			
			
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
		function delete($idtransportador){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			// conjunto de depend�ncias geradas
			
			//---------------------
			

			// verifica se pode excluir
			if (1) {

				

				$delete_sql = "	DELETE FROM
													{$conf['db_name']}transportador
												WHERE
													 idtransportador = $idtransportador ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY nome_transportador ASC") {
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}transportador
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
