<?php
	
  
	class sincronia {

		var $err;
    var $db_link2;
    		
		/**
    * construtor da classe
  	*/
		function sincronia(){
    
      $this->connect();
      $this->select_db();

		}

    function connect($db_host = DB_HOST2, $db_user = DB_USER2, $db_pass = DB_PASS2) {
      $this->db_link2 = mysql_connect($db_host, $db_user, $db_pass);
      return($this->db_link2);
    }

    function select_db($db_name = DB_NAME2) {
      return(mysql_select_db($db_name, $this->db_link2));
    }

    function query($query_str) {
      return(mysql_query($query_str, $this->db_link2));
    }

    function result($sql_res, $row, $field = NULL) {
      return(mysql_result($sql_res, $row, $field));
    }

    function num_rows($sql_res) {
      return(mysql_num_rows($sql_res));
    }
    
    function insert_id() {
      return(mysql_insert_id($this->db_link2));
    }

    function fetch_array($sql_res, $res_type = MYSQL_ASSOC) {
      return(mysql_fetch_array($sql_res, $res_type));
    }

    function error() {
      return(mysql_error($this->db_link2));
    }

    function close() {
      return(mysql_close($this->db_link2));
    }



    /**
      m�todo: changeMaster
      prop�sito: muda o mestre para o ip passado.
    */
    function changeMaster($master_ip){

      // vari�veis globais
      global $form;
      global $conf;
      global $falha;
      //---------------------
      
      $sql = "CHANGE MASTER TO MASTER_HOST='".$master_ip."'";


      //executa a query no banco de dados
      $get_q = $this->query($sql);

      //testa se a consulta foi bem sucedida
      if($get_q){ //foi bem sucedida

      
        //retorna o vetor associativo com os dados
        return TRUE;
      }
      else{ //deu erro no banco de dados
        $this->err = $falha['listar'];
        return FALSE;
      }
        
    }


		
		/**
		  m�todo: showSlaveStatus
		  prop�sito: busca informa��es sobre o slave
		*/
		function showSlaveStatus(){

			// vari�veis globais
			global $form;
			global $conf;
			global $falha;
			//---------------------
			
			$sql = " SHOW SLAVE STATUS;	";


			//executa a query no banco de dados
			$get_q = $this->query($sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $this->fetch_array($get_q);
	
				
				//retorna o vetor associativo com os dados
				return $get;
			}
			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}
				
		}
    
    /**
      m�todo: showMasterStatus
      prop�sito: busca informa��es sobre o master
    */
    function showMasterStatus(){

      // vari�veis globais
      global $form;
      global $conf;
      global $falha;
      //---------------------
      
      $sql = " SHOW MASTER STATUS; ";


      //executa a query no banco de dados
      $get_q = $this->query($sql);

      //testa se a consulta foi bem sucedida
      if($get_q){ //foi bem sucedida

        $get = $this->fetch_array($get_q);
  
        
        //retorna o vetor associativo com os dados
        return $get;
      }
      else{ //deu erro no banco de dados
        $this->err = $falha['listar'];
        return(0);
      }
        
    }
    
    
    /**
      m�todo: startSlave
      prop�sito: come�a a sincronia com o mestre.
    */
    function startSlave(){

      // vari�veis globais
      global $form;
      global $conf;
      global $falha;
      //---------------------
      
      $sql = " START SLAVE;  ";


      //executa a query no banco de dados
      $get_q = $this->query($sql);

      //testa se a consulta foi bem sucedida
      if($get_q){ //foi bem sucedida

        return TRUE;
      }
      else{ //deu erro no banco de dados
        $this->err = $falha['listar'];
        return FALSE;
      }
        
    }
    
    /**
      m�todo: stopSlave
      prop�sito: encerra a sincronia com o mestre.
    */
    function stopSlave(){

      // vari�veis globais
      global $form;
      global $conf;
      global $falha;
      //---------------------
      
      $sql = " STOP SLAVE;  ";


      //executa a query no banco de dados
      $get_q = $this->query($sql);
      
      if($get_q){ //foi bem sucedida
        
        return TRUE;
        
      }
      else{ //deu erro no banco de dados
        $this->err = $falha['listar'];
        return FALSE;
      }
        
    }


	
		
		

	} // fim da classe
?>
