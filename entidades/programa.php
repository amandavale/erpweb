<?php
	
	class programa {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function programa(){
			// n�o faz nada
		}


		/**
		  m�todo: Seleciona_Submodulo
		  prop�sito: busca informa��es referente ao modulo
		*/
		function Seleciona_Submodulo($idmodulo){		
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
				$list_sql = "	SELECT
					 						idsubmodulo,nome_submodulo
										FROM
											{$conf['db_name']}submodulo
										where idmodulo = $idmodulo
										";

			$list_q = $db->query($list_sql);
			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					foreach($list as $campo => $value){
						$list_return[$cont]["$campo"] = $value;
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


		/**
		  m�todo: getById
		  prop�sito: busca informa��es
		*/
		function getById($idprograma){

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											PROG.*,MDL.idmodulo
										FROM
											{$conf['db_name']}programa PROG
											INNER JOIN {$conf['db_name']}submodulo SMDL ON PROG.idsubmodulo = SMDL.idsubmodulo
											INNER JOIN {$conf['db_name']}modulo MDL ON SMDL.idmodulo = MDL.idmodulo
										WHERE
											 PROG.idprograma = $idprograma ";

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
			
			if ($ordem == "") $ordem = " ORDER BY MDL.ordem_modulo ASC, SMDL.ordem_submodulo ASC, PROG.ordem_programa ASC";
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT
											PROG.*   , SMDL.nome_submodulo , MDL.nome_modulo, MDL.ordem_modulo
										FROM
           						{$conf['db_name']}programa PROG 
												 INNER JOIN {$conf['db_name']}submodulo SMDL ON PROG.idsubmodulo=SMDL.idsubmodulo 
												 INNER JOIN {$conf['db_name']}modulo MDL ON MDL.idmodulo=SMDL.idmodulo 
												
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
					
					
					
					
					if ($list['define_adicionar'] == '0') $list['define_adicionar'] = "N�o"; 
					else if ($list['define_adicionar'] == '1') $list['define_adicionar'] = "Sim"; 
					if ($list['define_editar'] == '0') $list['define_editar'] = "N�o"; 
					else if ($list['define_editar'] == '1') $list['define_editar'] = "Sim"; 
					if ($list['define_excluir'] == '0') $list['define_excluir'] = "N�o"; 
					else if ($list['define_excluir'] == '1') $list['define_excluir'] = "Sim"; 
					if ($list['define_listar'] == '0') $list['define_listar'] = "N�o"; 
					else if ($list['define_listar'] == '1') $list['define_listar'] = "Sim"; 
					
					
					
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
		                  {$conf['db_name']}programa
		                    (
		                    
												idsubmodulo, 
												nome_programa, 
												descricao_programa, 
												nome_arquivo, 
												parametros_arquivo, 
												define_adicionar, 
												define_editar, 
												define_excluir, 
												define_listar, 
												ordem_programa  
												
												)
		                VALUES
		                    (
		                    
		                    " . $info['idsubmodulo'] . ",  
												'" . $info['nome_programa'] . "',  
												'" . $info['descricao_programa'] . "',  
												'" . $info['nome_arquivo'] . "',  
												'" . $info['parametros_arquivo'] . "',  
												'" . $info['define_adicionar'] . "',  
												'" . $info['define_editar'] . "',  
												'" . $info['define_excluir'] . "',  
												'" . $info['define_listar'] . "',  
												" . $info['ordem_programa'] . "   
												
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
		function update($idprograma, $info){
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}programa
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
			$update_sql .= " WHERE  idprograma = $idprograma ";
			
			
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
		function delete($idprograma){
			
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
								{$conf['db_name']}cargo_programa
							WHERE 
								 idprograma = $idprograma ";
			$verifica_q = $db->query($sql);
			$n0 = $db->num_rows($verifica_q);
			
			$sql = "SELECT 
								 * 
							FROM 
								{$conf['db_name']}funcionario_programa
							WHERE 
								 idprograma = $idprograma ";
			$verifica_q = $db->query($sql);
			$n1 = $db->num_rows($verifica_q);
			
			
			//---------------------
			

			// verifica se pode excluir
			if (1 && $n0==0 && $n1==0) {

				

				$delete_sql = "	DELETE FROM
													{$conf['db_name']}programa
												WHERE
													 idprograma = $idprograma ";
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
				$this->err = "Este registro n�o pode ser exclu�do, pois existem registros relacionadas a ele.";
			}	

		}	

		
		/**
		  m�todo: make_list
		  prop�sito: faz a listagem para colocar no select
		*/

		function make_list_select( $filtro = "", $ordem = " ORDER BY ordem_programa ASC") {
			
			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}programa
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
