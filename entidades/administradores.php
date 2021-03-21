<?php
	
	class administradores {

		var $err;
		
		/**
    * construtor: administradores
    *  prop�sito: Inicializa o administrador
  	*/
		function administradores(){
			// n�o faz nada
		}

		
		/**
		  m�todo: change_pass
			prop�sito: alterar a senha do usu�rio
		*/
		
		function change_pass($adm_cod, $new_pass, $old_pass) {

			global $conf;
			global $falha;

			//inicializa banco de dados
			$db = new db();

			//verifica se a senha digitada � v�lida
			$old_pass_md5 = md5($old_pass);
			
			$pass_sql = "	SELECT
											adm_cod
										FROM
											{$conf['db_name']}administrador
										WHERE
											adm_cod = $adm_cod AND adm_sen = '$old_pass_md5'";
			$pass_q = $db->query($pass_sql);

			if($db->num_rows($pass_q)){

				//faz a atualiza��o da senha
				$new_pass_md5 = md5($new_pass);
				
				$pass_sql = "	UPDATE {$conf['db_name']}administrador
												SET adm_sen = '$new_pass_md5'
											WHERE
											  adm_cod = $adm_cod";
				$pass_q = $db->query($pass_sql);

				if($pass_q){
					return(1);
				}
				else{
					$this->err = $falha['senha'];
					return(0);
				}
				
			}	
			else{
				$this->err = "Senha atual inv&aacute;lida!";
				return(0);
			}	

		}	

		/**
		  m�todo: getById
		  prop�sito: busca informa��es de um determinado usu�rio atrav�s do seu id
		*/
		
		function getById($adm_cod){

			global $conf;

			//inicializa banco de dados
			$db = new db();
			
			$get_sql = "	SELECT
											*
										FROM
											{$conf['db_name']}administrador
										WHERE
											adm_cod = $adm_cod";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				//retorna o vetor associativo com os dados do usu�rio
				return $get;
			}
			else{//deu erro no banco de dados
				$this->err = "Erro ao recuperar informa&ccedil;&otilde;es do administrador. Entre em contato com a F6 Sistemas.";
				return(0);
			}
				
		}

		/**
		  m�todo: make_list
		  prop�sito: faz a listagem dos administradores
		*/
		
		function make_list( $pg, $rppg, $filtro = "", $ordem = "ORDER BY adm_nom ASC"){
			
			//inicializa banco de dados
			$db = new db();

			global $conf;
			global $err;
			global $smarty;
			global $form;
			global $falha;
			
			$list_sql = "	SELECT
											*
										FROM
											{$conf['db_name']}administrador
										$filtro
										$ordem";

			//manda fazer a pagina��o
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg);

			if($list_q){
				
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){
					
					//insere um �ndeice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);;
					  
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
		  prop�sito: inclui um novo usu�rio no sistema
		*/

		function set($adm_info){
			
			global $conf;
			global $falha;

			//inicializa banco de dados
			$db = new db();
				
			//insere o usu�rio no sistema
			$set_sql = "INSERT INTO
										{$conf['db_name']}administrador
											(adm_nom, adm_log, adm_sen, adm_sex, adm_ema)
									VALUES
										('" . $adm_info['adm_nom'] . "', '" . $adm_info['adm_log'] . "',
										'" . md5($adm_info['adm_sen']) . "', '" . $adm_info['adm_sex'] . "',
										'" . $adm_info['adm_ema'] . "')";

			//executa a query e testa se a consulta foi "boa"
			if($db->query($set_sql)){
				//retorna o c�digo do usu�rio inserido
				return $db->insert_id();
			}
			else{
				$this->err = $falha['inserir'];
				return(0);
			}
		}
		
		/**
		  m�todo: update
		  prop�sito: atualiza os dados de usu�rio no banco de dados
		*/
		function update($adm_cod, $adm_info){
			
			global $conf;
			global $falha;
			
			//inicializa banco de dados
			$db = new db();
			
			//o vetor $usu_info deve conter todos os campos tabela a serem atualizados
			//a vari�vel $usu_cod deve conter o c�digo do usu�rio cujos dados ser�o atualizados
			//campos literais dever�o ter o prefixo lit e campos num�ricos dever�o ter o prefixo num
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}administrador\n
											SET ";
			
			//obt�m o n�mero de campos
			$num_campos = count($adm_info);
			
   		//varre o formul�rio e monta a consulta;
			$cont_validos = 0;
			foreach($adm_info as $campo => $valor){

				$tipo_campo = substr($campo, 0, 3);
				$nome_campo = substr($campo, 3, strlen($campo) - 3);

				if(trim($valor) != ""){
					
					if(($tipo_campo == "lit") || ($tipo_campo == "num")){
						
						$usu_validos["$campo"] = $valor;
						$cont_validos++;
						
					}
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
					$update_sql .= ",\n";
				}
				
			}
			

			//completa o sql com a restri��o
			$update_sql .= "\nWHERE adm_cod = $adm_cod";
			
			//envia a query para o banco
			$update_q = $db->query($update_sql);
			
			if($update_q)
			  return(1);
			else
			  $this->err = $falha['alterar'];
		}	
		
		/**
		  m�todo: delete
		  prop�sito: excluir usu�rio pelo c�digo passado
		*/
		function delete($adm_cod){
			
			global $conf;
	 		global $falha;
			
			//inicializa banco de dados
			$db = new db();

			$delete_sql = "	DELETE FROM
												{$conf['db_name']}administrador
											WHERE
												adm_cod = $adm_cod ";
			$delete_q = $db->query($delete_sql);
			
			if($delete_q){
				return(1);
			}	
			else{
				$this->err = $falha['excluir'];
				return(0);
			}	

		}


		/**
		  m�todo: make_list
		  prop�sito: faz a listagem para colocar no select
		*/

		function make_list_select( $filtro = "", $ordem = "ORDER BY adm_nom ASC") {

			// vari�veis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------


			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}administrador
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

		

	}
?>
