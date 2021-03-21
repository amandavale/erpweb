<?php

	define("DB_HOST", "192.168.0.242");
	define("DB_USER", "root");
	define("DB_PASS", "phprime");
	define("DB_NAME", "soserpweb");




 /**
  *    classe: db
  * propï¿½ito: Gerencia o acesso a bases de dados.
  */

  class db {

    var $db_link;

    function db() {
      $this->connect();
      $this->select_db();
    }

    function connect($db_host = DB_HOST, $db_user = DB_USER, $db_pass = DB_PASS) {
      $this->db_link = mysql_connect($db_host, $db_user, $db_pass);
      return($this->db_link);
    }

    function select_db($db_name = DB_NAME) {
      return(mysql_select_db($db_name, $this->db_link));
    }

    function query($query_str) {

	global $conf;
	$result = mysql_query($query_str, $this->db_link);
		
	return($result);
	
    }

    function result($sql_res, $row, $field = NULL) {
      return(mysql_result($sql_res, $row, $field));
    }

    function num_rows($sql_res) {
      return(mysql_num_rows($sql_res));
    }
    
    function insert_id() {
      return(mysql_insert_id($this->db_link));
    }

    function fetch_array($sql_res, $res_type = MYSQL_ASSOC) {
      return(mysql_fetch_array($sql_res, $res_type));
    }

    function error() {
      return(mysql_error($this->db_link));
    }

    function close() {
      return(mysql_close($this->db_link));
    }
	
	
	/** Realiza o Backup do banco de dados
	  @Parâmetros
	   $caminho_mysqldump: Caminho completo de acesso ao mysqldump (Ex: C:\wamp\bin\mysql\mysql5.1.36\bin\mysqldump.exe)
	   $caminho_destino: Caminho completo de acesso ao arquivo que será gerado. (Ex: C:\wamp\www\erpweb\common\arq\db_backup\bd_erpweb2009-09-09_11-11-28.sql)
	   
	  @Retorno
	   0 = O comando foi executado com êxito.
	   Qualquer nº diferente de 0: Erro ao executar o comando.
	*/
    function backup($caminho_mysqldump, $caminho_destino){
		
		//Chama a variável global
		global $conf;
		
		//Prepara a linha de comando
		$command = sprintf("%s --opt -h %s -u %s --password=%s --default-character-set=latin1 -c -B %s -K -x > %s",$caminho_mysqldump, DB_HOST, DB_USER, DB_PASS, DB_NAME, $caminho_destino);
	    
		//Executa o comando
		system($command,$result);
				
		
		return($result);
	  
    }
    /** Escapa caracteres especiais para realizar consultas SQL
     * 
     * @param string $string
     * @return string $string 
     */
    function escape($string){
        return mysql_real_escape_string($string);
    }
    
    /**
	 * Retorna um array com todos os registros da consulta
	 * @param resource $sql_res Resource com a query
	 * @param mixed $index_key String com o campo cujo valor será o índice do registro no array
	 * 								ou boolean false, indicando que é para indexar seqüencialmente (padrão)
	 * @return array
	 */
	function fetch_all($sql_res, $index_key = false) {
		$result = array();
		while ($linha = $this->fetch_array($sql_res)) {
			if ($index_key && isset($linha[$index_key])) {
				$result[$linha[$index_key]] = $linha;
			} else {
				$result[] = $linha;
			}
		}
		return $result;
	}
	
    
  }

?>
