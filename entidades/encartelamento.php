<?php
	
	class encartelamento {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function encartelamento(){
			// n�o faz nada
		}

		/**
		  m�todo: Seleciona_Produtos_Do_Encartelamento
		  prop�sito: Seleciona_Produtos_Do_Encartelamento
		*/

		function Seleciona_Produtos_Do_Encartelamento($idencartelamento){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;

			global $flags;
			//---------------------

			$list_sql = "	SELECT
											ENCTPRD.*, PRD.*, UNV.*
										FROM
           						{$conf['db_name']}encartelamento_produto ENCTPRD
												 INNER JOIN {$conf['db_name']}produto PRD ON ENCTPRD.idproduto=PRD.idproduto
													INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda=UNV.idunidade_venda

										WHERE
											ENCTPRD.idencartelamento = $idencartelamento

										ORDER BY
												PRD.descricao_produto ASC ";

			//manda fazer a pagina��o
			$list_q = $db->query($list_sql);

			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					//insere um �ndice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);


					if ($list['preco_balcao_produto'] != "") $list['preco_balcao_produto'] = number_format($list['preco_balcao_produto'],2,",","");
					else $list['preco_balcao_produto'] = "0,00";

					if ($list['preco_oferta_produto'] != "") $list['preco_oferta_produto'] = number_format($list['preco_oferta_produto'],2,",","");
					else $list['preco_oferta_produto'] = "0,00";

					if ($list['preco_atacado_produto'] != "") $list['preco_atacado_produto'] = number_format($list['preco_atacado_produto'],2,",","");
					else $list['preco_atacado_produto'] = "0,00";

					if ($list['preco_telemarketing_produto'] != "") $list['preco_telemarketing_produto'] = number_format($list['preco_telemarketing_produto'],2,",","");
					else $list['preco_telemarketing_produto'] = "0,00";

					if ($list['qtd'] != "") $list['qtd'] = number_format($list['qtd'],2,",","");

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
		  m�todo: Busca_Generica
		  prop�sito: Busca_Generica
		*/

		function Busca_Generica ( $pg, $rppg, $busca = "", $ordem = "", $url = ""){

			if ($ordem == "") $ordem = " ORDER BY ENC.descricao_encartelamento ASC";

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT
											ENC.*
										FROM
           						{$conf['db_name']}encartelamento ENC
										WHERE
           					 	UPPER(ENC.descricao_encartelamento) LIKE UPPER('%{$busca}%') 

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
		  m�todo: Filtra_Encartelamento
		  prop�sito: Filtra_Encartelamento
		*/

		function Filtra_Encartelamento ( $filtro ) {

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT
											ENCT.*
										FROM
           						{$conf['db_name']}encartelamento ENCT
										WHERE
											UPPER(ENCT.descricao_encartelamento) LIKE UPPER('%{$filtro}%')
										ORDER BY
											ENCT.descricao_encartelamento ASC ";

			//manda fazer a pagina��o
			$list_q = $db->query($list_sql);

			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					//insere um �ndice na listagem
					$list['index'] = $cont+1;

					// busca os pre�os do encartelamento
					$sql = "
						SELECT
							SUM(PRD.preco_balcao_produto) as soma_preco_balcao_produto,
							SUM(PRD.preco_oferta_produto) as some_preco_oferta_produto,
							SUM(PRD.preco_atacado_produto) as soma_preco_atacado_produto,
							SUM(PRD.preco_telemarketing_produto) as soma_preco_telemarketing_produto,
							SUM(PRD.preco_custo_produto) as soma_preco_custo_produto
						FROM
							encartelamento_produto  ENCP,  produto PRD
						WHERE
							ENCP.idencartelamento = " . $list['idencartelamento'] . "
								AND
							ENCP.idproduto = PRD.idproduto
						GROUP BY (ENCP.idencartelamento) " ;
						
					$busca_precos_q = $db->query($sql);
					$busca_precos = $db->fetch_array($busca_precos_q);

					$list['preco_balcao_produto'] = $busca_precos['soma_preco_balcao_produto'];
					$list['preco_oferta_produto'] = $busca_precos['some_preco_oferta_produto'];
					$list['preco_atacado_produto'] = $busca_precos['soma_preco_atacado_produto'];
					$list['preco_telemarketing_produto'] = $busca_precos['soma_preco_telemarketing_produto'];
					$list['preco_custo_produto'] = $busca_precos['soma_preco_custo_produto'];

					if ($list['preco_balcao_produto'] != "") $list['preco_balcao_produto'] = number_format($list['preco_balcao_produto'],2,",","");
					else $list['preco_balcao_produto'] = "0,00";
					
					if ($list['preco_oferta_produto'] != "") $list['preco_oferta_produto'] = number_format($list['preco_oferta_produto'],2,",","");
					else $list['preco_oferta_produto'] = "0,00";
					
					if ($list['preco_atacado_produto'] != "") $list['preco_atacado_produto'] = number_format($list['preco_atacado_produto'],2,",","");
					else $list['preco_atacado_produto'] = "0,00";
					
					if ($list['preco_telemarketing_produto'] != "") $list['preco_telemarketing_produto'] = number_format($list['preco_telemarketing_produto'],2,",","");
					else $list['preco_telemarketing_produto'] = "0,00";
					
					if ($list['preco_custo_produto'] != "") $list['preco_custo_produto'] = number_format($list['preco_custo_produto'],2,",","");
					else $list['preco_custo_produto'] = "0,00";

					$list['desconto'] = "0,00";

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
		  m�todo: getById
		  prop�sito: busca informa��es
		*/
		function getById($idencartelamento){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											ENCT.*
										FROM
											{$conf['db_name']}encartelamento ENCT
										WHERE
											 ENCT.idencartelamento = $idencartelamento ";

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
			
			if ($ordem == "") $ordem = " ORDER BY ENCT.descricao_encartelamento ASC";
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											ENCT.*  
										FROM
           						{$conf['db_name']}encartelamento ENCT 
												
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
		                  {$conf['db_name']}encartelamento
		                    (
		                    
												descricao_encartelamento  
												
												)
		                VALUES
		                    (
		                    
		                    '" . $info['descricao_encartelamento'] . "'   
												
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
		function update($idencartelamento, $info){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}encartelamento
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
			$update_sql .= " WHERE  idencartelamento = $idencartelamento ";
			
			
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
		function delete($idencartelamento){
			
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
													{$conf['db_name']}encartelamento
												WHERE
													 idencartelamento = $idencartelamento ";
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

		function make_list_select( $filtro = "", $ordem = " ORDER BY descricao_encartelamento ASC") {
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}encartelamento
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
