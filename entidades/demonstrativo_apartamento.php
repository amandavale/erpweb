<?php

	class demonstrativo_apartamento {

		var $err;
		
		function demonstrativo_apartamento(){
			// não faz nada
		}

		

		/**
		 * método: getByApartamento
		 * propósito: busca informações de um determinado apartamento através do seu id
		 */
		
		function getByApartamento($idapartamento){

			global $conf;

			//inicializa banco de dados
			$db = new db();
			
			$get_sql = "	SELECT
								*
							FROM
								{$conf['db_name']}demonstrativo_apartamento
							WHERE
								idapartamento = $idapartamento";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				//retorna o vetor associativo com os dados do usuário
				return $get;
			}
			else{//deu erro no banco de dados
				$this->err = "Erro ao recuperar informa&ccedil;&otilde;es do demonstrativo. Entre em contato com a F6 Sistemas.";
				return(0);
			}
				
		}

		

		/**
		 * método: set
		 * propósito: inclui um novo demonstrativo
		 */
		function set($info){
			
			global $conf;
			global $falha;


                        /// Retira quebras de linha do início e do fim do demonstrativo e deixa apenas uma
	                #$_POST['demonstrativo'] = trim($_POST['demonstrativo'],"<p>");
                        $info['demonstrativo'] = str_replace(array("<p>","</p>"),array("<br />",""),$info['demonstrativo']);



			//inicializa banco de dados
			$db = new db();

			$this->delete($info['idapartamento']);
				
			//insere o usuário no sistema
			$set_sql = "INSERT INTO
							{$conf['db_name']}demonstrativo_apartamento (idapartamento, demonstrativo)
						VALUES
							(" . $info['idapartamento'] . ", '" . $info['demonstrativo'] . "')";

			//executa a query e testa se a consulta foi "boa"
			if($db->query($set_sql)){

				return true;
			}
			else{
				$this->err = $falha['inserir'];
				return(0);
			}
		}
		
		
		/**
		 * método: delete
		 * propósito: excluir registro pelo código de apartamento passado
		*/
		function delete($idapartamento){
			
			global $conf;
	 		global $falha;
			
			//inicializa banco de dados
			$db = new db();

			$delete_sql = "	DELETE FROM
								{$conf['db_name']}demonstrativo_apartamento
							WHERE
								idapartamento = $idapartamento ";

			$delete_q = $db->query($delete_sql);
			
			if($delete_q){
				return(1);
			}	
			else{
				$this->err = $falha['excluir'];
				return(0);
			}
		}

	}
?>
