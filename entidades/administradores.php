<?php
	
	class administradores {

		var $err;
		
		/**
    * construtor: administradores
    *  propósito: Inicializa o administrador
  	*/
		function administradores(){
			// não faz nada
		}

		
		/**
		  método: change_pass
			propósito: alterar a senha do usuário
		*/
		
		function change_pass($adm_cod, $new_pass, $old_pass) {

			global $conf;
			global $falha;

			//inicializa banco de dados
			$db = new db();

			//verifica se a senha digitada é válida
			$old_pass_md5 = md5($old_pass);
			
			$pass_sql = "	SELECT
											adm_cod
										FROM
											{$conf['db_name']}administrador
										WHERE
											adm_cod = $adm_cod AND adm_sen = '$old_pass_md5'";
			$pass_q = $db->query($pass_sql);

			if($db->num_rows($pass_q)){

				//faz a atualização da senha
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
		  método: getById
		  propósito: busca informações de um determinado usuário através do seu id
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
				
				//retorna o vetor associativo com os dados do usuário
				return $get;
			}
			else{//deu erro no banco de dados
				$this->err = "Erro ao recuperar informa&ccedil;&otilde;es do administrador. Entre em contato com a F6 Sistemas.";
				return(0);
			}
				
		}

		/**
		  método: make_list
		  propósito: faz a listagem dos administradores
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

			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg);

			if($list_q){
				
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){
					
					//insere um índeice na listagem
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
			método: set
		  propósito: inclui um novo usuário no sistema
		*/

		function set($adm_info){
			
			global $conf;
			global $falha;

			//inicializa banco de dados
			$db = new db();
				
			//insere o usuário no sistema
			$set_sql = "INSERT INTO
										{$conf['db_name']}administrador
											(adm_nom, adm_log, adm_sen, adm_sex, adm_ema)
									VALUES
										('" . $adm_info['adm_nom'] . "', '" . $adm_info['adm_log'] . "',
										'" . md5($adm_info['adm_sen']) . "', '" . $adm_info['adm_sex'] . "',
										'" . $adm_info['adm_ema'] . "')";

			//executa a query e testa se a consulta foi "boa"
			if($db->query($set_sql)){
				//retorna o código do usuário inserido
				return $db->insert_id();
			}
			else{
				$this->err = $falha['inserir'];
				return(0);
			}
		}
		
		/**
		  método: update
		  propósito: atualiza os dados de usuário no banco de dados
		*/
		function update($adm_cod, $adm_info){
			
			global $conf;
			global $falha;
			
			//inicializa banco de dados
			$db = new db();
			
			//o vetor $usu_info deve conter todos os campos tabela a serem atualizados
			//a variável $usu_cod deve conter o código do usuário cujos dados serão atualizados
			//campos literais deverão ter o prefixo lit e campos numéricos deverão ter o prefixo num
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}administrador\n
											SET ";
			
			//obtém o número de campos
			$num_campos = count($adm_info);
			
   		//varre o formulário e monta a consulta;
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
				
				//testa se é o último
				if($cont != $cont_validos){
					$update_sql .= ",\n";
				}
				
			}
			

			//completa o sql com a restrição
			$update_sql .= "\nWHERE adm_cod = $adm_cod";
			
			//envia a query para o banco
			$update_q = $db->query($update_sql);
			
			if($update_q)
			  return(1);
			else
			  $this->err = $falha['alterar'];
		}	
		
		/**
		  método: delete
		  propósito: excluir usuário pelo código passado
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
		  método: make_list
		  propósito: faz a listagem para colocar no select
		*/

		function make_list_select( $filtro = "", $ordem = "ORDER BY adm_nom ASC") {

			// variáveis globais
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
