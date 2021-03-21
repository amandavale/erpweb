<?php

	class produto_filial {

		var $err;

		/**
    * construtor da classe
  	*/
		function produto_filial(){
			// n�o faz nada
		}


		/**
			m�todo: Dar_Alta_Estoque
		  prop�sito: Dar_Alta_Estoque
		*/

		function Dar_Alta_Estoque ($idfilial, $idproduto, $qtd_alta){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$set_sql = "  UPDATE
		                  {$conf['db_name']}produto_filial
		                SET
		                  qtd_produto = qtd_produto + $qtd_alta
										WHERE
											idfilial = $idfilial
											  AND
											idproduto = $idproduto
									";

			//executa a query e testa se a consulta foi "boa"

			if($db->query($set_sql)){
				return(1);
			}
			else{
				$this->err = $falha['alterar'];
				return(0);
			}
		}




		/**
			m�todo: Dar_Baixa_Estoque
		  prop�sito: Dar_Baixa_Estoque
		*/

		function Dar_Baixa_Estoque ($idfilial, $idproduto, $qtd_baixa){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$set_sql = "  UPDATE
		                  {$conf['db_name']}produto_filial
		                SET
		                  qtd_produto = qtd_produto - $qtd_baixa
										WHERE
											idfilial = $idfilial
											  AND
											idproduto = $idproduto
									";

			//executa a query e testa se a consulta foi "boa"

			if($db->query($set_sql)){
				return(1);
			}
			else{
				$this->err = $falha['alterar'];
				return(0);
			}
		}

		/**
		  m�todo: getByIdFilial
		  prop�sito: busca informa��es de uma unica filial
		*/
		function getByIdFilial($idproduto,$idfilial){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$get_sql = "	SELECT
											PRD.*, S.*, D.*,PRDFL.*
										FROM
											{$conf['db_name']}produto PRD
												 INNER JOIN {$conf['db_name']}secao S ON PRD.idsecao=S.idsecao
													INNER JOIN {$conf['db_name']}departamento D ON S.iddepartamento=D.iddepartamento
													INNER JOIN {$conf['db_name']}produto_filial PRDFL ON PRD.idproduto=PRDFL.idproduto

										WHERE
											 PRD.idproduto = $idproduto
											 AND PRDFL.idfilial = $idfilial
											 ";

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
					if ($get['comissao_interno_produto'] != "") $get['comissao_interno_produto'] = number_format($get['comissao_interno_produto'],2,",","");
					if ($get['comissao_externo_produto'] != "") $get['comissao_externo_produto'] = number_format($get['comissao_externo_produto'],2,",","");
					if ($get['comissao_representante_produto'] != "") $get['comissao_representante_produto'] = number_format($get['comissao_representante_produto'],2,",","");
					if ($get['comissao_operador_telemarketing_produto'] != "") $get['comissao_operador_telemarketing_produto'] = number_format($get['comissao_operador_telemarketing_produto'],2,",","");
					if ($get['preco_balcao_produto'] != "") $get['preco_balcao_produto'] = number_format($get['preco_balcao_produto'],2,",","");
					if ($get['preco_oferta_produto'] != "") $get['preco_oferta_produto'] = number_format($get['preco_oferta_produto'],2,",","");
					if ($get['preco_atacado_produto'] != "") $get['preco_atacado_produto'] = number_format($get['preco_atacado_produto'],2,",","");
					if ($get['preco_telemarketing_produto'] != "") $get['preco_telemarketing_produto'] = number_format($get['preco_telemarketing_produto'],2,",","");
					if ($get['preco_custo_produto'] != "") $get['preco_custo_produto'] = number_format($get['preco_custo_produto'],2,",","");





				//retorna o vetor associativo com os dados
				return $get;
			}

			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}


	}





		/**
		  m�todo: getById
		  prop�sito: busca informa��es
		*/
		function getById($idproduto){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$get_sql = "	SELECT
											PRD.*, S.*, D.*,PRDFL.*
										FROM
											{$conf['db_name']}produto PRD
												 INNER JOIN {$conf['db_name']}secao S ON PRD.idsecao=S.idsecao
													INNER JOIN {$conf['db_name']}departamento D ON S.iddepartamento=D.iddepartamento
													INNER JOIN {$conf['db_name']}produto_filial PRDFL ON PRD.idproduto=PRDFL.idproduto

										WHERE
											 PRD.idproduto = $idproduto
											 ";

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
					if ($get['comissao_interno_produto'] != "") $get['comissao_interno_produto'] = number_format($get['comissao_interno_produto'],2,",","");
					if ($get['comissao_externo_produto'] != "") $get['comissao_externo_produto'] = number_format($get['comissao_externo_produto'],2,",","");
					if ($get['comissao_representante_produto'] != "") $get['comissao_representante_produto'] = number_format($get['comissao_representante_produto'],2,",","");
					if ($get['comissao_operador_telemarketing_produto'] != "") $get['comissao_operador_telemarketing_produto'] = number_format($get['comissao_operador_telemarketing_produto'],2,",","");
					if ($get['preco_balcao_produto'] != "") $get['preco_balcao_produto'] = number_format($get['preco_balcao_produto'],2,",","");
					if ($get['preco_oferta_produto'] != "") $get['preco_oferta_produto'] = number_format($get['preco_oferta_produto'],2,",","");
					if ($get['preco_atacado_produto'] != "") $get['preco_atacado_produto'] = number_format($get['preco_atacado_produto'],2,",","");
					if ($get['preco_telemarketing_produto'] != "") $get['preco_telemarketing_produto'] = number_format($get['preco_telemarketing_produto'],2,",","");
					if ($get['preco_custo_produto'] != "") $get['preco_custo_produto'] = number_format($get['preco_custo_produto'],2,",","");





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

			if ($ordem == "") $ordem = " ORDER BY PRD.descricao_produto ASC";

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT
											PRD.*   , S.nome_secao , UNV.*, D.nome_departamento,PRDFL.*
										FROM
           						{$conf['db_name']}produto PRD
												 INNER JOIN {$conf['db_name']}unidade_venda UNV ON PRD.idunidade_venda=UNV.idunidade_venda
												 INNER JOIN {$conf['db_name']}secao S ON PRD.idsecao=S.idsecao
												 	INNER JOIN {$conf['db_name']}departamento D ON S.iddepartamento=D.iddepartamento
												 	INNER JOIN {$conf['db_name']}produto_filial PRDFL ON PRD.idproduto=PRDFL.idproduto

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
		                  {$conf['db_name']}produto_filial
		                    (
                        idproduto,
                        idfilial,
												preco_balcao_produto,
												preco_oferta_produto,
												preco_atacado_produto,
												preco_telemarketing_produto,
												preco_custo_produto,
												qtd_produto,
												produto_em_oferta

												)
		                VALUES
		                    (

												" . $info['idproduto'] . ",
												" . $info['idfilial'] . ",
		                  						" . $info['preco_balcao_produto'] . ",
												" . $info['preco_oferta_produto'] . ",
												" . $info['preco_atacado_produto'] . ",
												" . $info['preco_telemarketing_produto'] . ",
												" . $info['preco_custo_produto'] . ",
												" . $info['qtd_produto'] . ",
												'" . $info['produto_em_oferta'] . "'

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
		function update($idproduto, $idfilial, $info){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}produto_filial
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
			$update_sql .= " WHERE  idproduto = $idproduto
											 AND  	idfilial = $idfilial";

			
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
		function delete($idproduto){

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
													{$conf['db_name']}produto_filial
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
				$this->err = "Este registro n�o pode ser exclu�do, pois existem registros relacionados a ele.";
			}

		}


		/**
		  m�todo: make_list
		  prop�sito: faz a listagem para colocar no select
		*/

		function make_list_select( $filtro = "", $ordem = " ORDER BY idsecao ASC") {

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------


			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}produto_filial
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


		function set_Nova_Filial($codigo_filial){


			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT
											idproduto
										FROM
											{$conf['db_name']}produto
										";

			//manda fazer a pagina��o
			$list_q = $db->query($list_sql);

			$filial_produto_array['idfilial'] = $codigo_filial;
			$filial_produto_array['preco_balcao_produto'] = 0.00;
			$filial_produto_array['preco_oferta_produto'] = 0.00;
			$filial_produto_array['preco_atacado_produto'] = 0.00;
			$filial_produto_array['preco_telemarketing_produto'] = 0.00;
			$filial_produto_array['preco_custo_produto'] = 0.00;
			$filial_produto_array['qtd_produto'] = 0;

			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				while($list = $db->fetch_array($list_q)){

					$filial_produto_array['idproduto'] = $list['idproduto'];

					$this->set($filial_produto_array);

				}

			}
			else{
				$this->err = $falha['listar'];
				return(0);
			}
		}

		/**
		  m�todo: make_list_filial
		  prop�sito: faz a listagem dos pre�os e das filials
		*/

		function make_list_filial( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){

			if ($ordem == "") $ordem = " ORDER BY PRD.descricao_produto ASC";

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------

			$list_sql = "	SELECT
											PRD.*, PRDFL.* , FLI.*
										FROM
           						{$conf['db_name']}produto PRD
												 	INNER JOIN {$conf['db_name']}produto_filial PRDFL ON PRD.idproduto=PRDFL.idproduto
													INNER JOIN {$conf['db_name']}filial FLI ON FLI.idfilial=PRDFL.idfilial
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
					if ($list['preco_custo_produto'] != "") $list['preco_custo_produto'] = number_format($list['preco_custo_produto'],2,",","");
					if ($list['qtd_produto'] != "") $list['qtd_produto'] = number_format($list['qtd_produto'],2,",","");




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
