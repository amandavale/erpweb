<?php

class status_os_programacao {

    var $err;

    /**
     * construtor da classe
     */
    function status_os_programacao() {
        // n�o faz nada
    }

    /**
      m�todo: getById
      prop�sito: busca informa��es
     */
    function getById($idstatus_os_programacao) {

        // vari�veis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------

        $get_sql = "	SELECT
                                SOP.*
                        FROM
                                {$conf['db_name']}status_os_programacao SOP
                        WHERE
                                 SOP.idstatus_os_programacao = $idstatus_os_programacao ";

        //executa a query no banco de dados
        $get_q = $db->query($get_sql);

        //testa se a consulta foi bem sucedida
        if ($get_q) { //foi bem sucedida
            $get = $db->fetch_array($get_q);





            //retorna o vetor associativo com os dados
            return $get;
        } else { //deu erro no banco de dados
            $this->err = $falha['listar'];
            return(0);
        }
    }


    /**
     m�todo: getIdStatusProg
     prop�sito: busca informa��es
     */
    function getIdStatusProg($idstatus_os,$idprogramacao_status) {

    	// vari�veis globais
    	global $form, $conf, $db, $falha;
    	//---------------------
    	
    	$progStatus = is_null($idprogramacao_status) ? ' IS NULL ' : " = $idprogramacao_status";
    	
    	$get_sql = "	SELECT
					    	SOP.*
				    	FROM
					    	{$conf['db_name']}status_os_programacao SOP
				    	WHERE
					    	SOP.idstatus_os   = $idstatus_os AND
					    	SOP.idprogramacao_status $progStatus ";
						    	
					    	
    	//executa a query no banco de dados
    	$get_q = $db->query($get_sql);

    	//testa se a consulta foi bem sucedida
    	if ($get_q) { //foi bem sucedida
    		
			return $db->result($get_q, 0, 'idstatus_os_programacao');
			
    	} else { //deu erro no banco de dados
    		$this->err = $falha['listar'];
    		return(0);
    	}
    }
     
     
    
    function getProgramacao($idstatus_os) {
    
    	// vari�veis globais
    	global $form, $conf, $db, $falha;
    	//---------------------
    
    	$get_sql = " SELECT 
    					SOP.*, PS.nome_programacao 
    				 FROM 
    				 	{$conf['db_name']}status_os_programacao SOP
    				 	JOIN {$conf['db_name']}programacao_status PS USING(idprogramacao_status)
    				 WHERE 
    				 	SOP.idstatus_os = $idstatus_os";
    
    	$get_q = $db->query($get_sql);
    		
    	//testa se a consulta foi bem sucedida
    	if ($get_q) { //foi bem sucedida
    
    		while($list = $db->fetch_array($get_q)){
    			$prog_status[] = $list ;
    		}
    
    		//retorna o vetor associativo com os dados
    		return $prog_status;
    	} else { //deu erro no banco de dados
    		$this->err = $falha['listar'];
    		return(0);
    	}
    }
    
    /**
      m�todo: make_list
      prop�sito: faz a listagem
     */
    function make_list($pg, $rppg, $filtro = "", $ordem = "", $url = "") {

        if ($ordem == "")
            $ordem = " ORDER BY SOP.idstatus_os ASC";

        // vari�veis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------

        $list_sql = "	SELECT
                           SOP.*   , STS.nome_status_os , PST.nome_programacao
                        FROM
                        {$conf['db_name']}status_os_programacao SOP 
                                 INNER JOIN {$conf['db_name']}status_os STS ON SOP.idstatus_os=STS.idstatus_os 
                                 LEFT JOIN {$conf['db_name']}programacao_status PST ON SOP.idprogramacao_status=PST.idprogramacao_status 

                        $filtro
                        $ordem";

        //manda fazer a pagina��o
        $list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

        if ($list_q) {

            //busca os registros no banco de dados e monta o vetor de retorno
            $list_return = array();
            $cont = 0;
            while ($list = $db->fetch_array($list_q)) {

                //insere um �ndice na listagem
                $list['index'] = $cont + 1 + ($pg * $rppg);







                $list_return[] = $list;

                $cont++;
            }

            return $list_return;
        } else {
            $this->err = $falha['listar'];
            return(0);
        }
    }

    /**
      m�todo: set
      prop�sito: inclui novo registro
     */
    function set($info) {

        // vari�veis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------

        $info['idstatus_os'] = (int)$info['idstatus_os'];
        $info['idprogramacao_status'] = empty($info['idprogramacao_status']) ? 'NULL' : (INT)$info['idprogramacao_status'];
        
        
        $set_sql = "  INSERT INTO
		                  {$conf['db_name']}status_os_programacao
		                    (
		                    
                                    idstatus_os, 
                                    idprogramacao_status  

                                    )
		                VALUES
		                    (
		                    
		                    " . $info['idstatus_os'] . ",  
                                    " . $info['idprogramacao_status'] . "   

                                    )";

        //executa a query e testa se a consulta foi "boa"
        if ($db->query($set_sql)) {
            //retorna o c�digo inserido
            $codigo = $db->insert_id();



            return($codigo);
        } else {
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
    function update($idstatus_os_programacao, $info) {

        // vari�veis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------
        //inicializa a query
        $update_sql = "	UPDATE
												{$conf['db_name']}status_os_programacao
											SET ";

        //varre o formul�rio e monta a consulta;
        $cont_validos = 0;
        foreach ($info as $campo => $valor) {

            $tipo_campo = substr($campo, 0, 3);
            $nome_campo = substr($campo, 3, strlen($campo) - 3);

            if (($tipo_campo == "lit") || ($tipo_campo == "num")) {

                $usu_validos["$campo"] = $valor;
                $cont_validos++;
            }
        }

        $cont = 0;
        foreach ($usu_validos as $campo => $valor) {

            $tipo_campo = substr($campo, 0, 3);
            $nome_campo = substr($campo, 3, strlen($campo) - 3);

            if ($tipo_campo == "lit")
                $update_sql .= "$nome_campo = '$valor'";
            elseif ($tipo_campo == "num")
                $update_sql .= "$nome_campo = $valor";

            $cont++;

            //testa se � o �ltimo
            if ($cont != $cont_validos) {
                $update_sql .= ", ";
            }
        }


        //completa o sql com a restri��o
        $update_sql .= " WHERE  idstatus_os_programacao = $idstatus_os_programacao ";


        //envia a query para o banco
        $update_q = $db->query($update_sql);

        if ($update_q)
            return(1);
        else
            $this->err = $falha['alterar'];
    }

    /**
      m�todo: delete
      prop�sito: excluir registro
     */
    function delete($idstatus_os_programacao) {

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
                       {$conf['db_name']}transicao_status
               WHERE 
                        idstatus_os_programacao = $idstatus_os_programacao ";
        $verifica_q = $db->query($sql);
        $n0 = $db->num_rows($verifica_q);


        //---------------------
        // verifica se pode excluir
        if (1 && $n0 == 0) {



            $delete_sql = "	DELETE FROM
                                        {$conf['db_name']}status_os_programacao
                                WHERE
                                         idstatus_os_programacao = $idstatus_os_programacao ";
            $delete_q = $db->query($delete_sql);

            if ($delete_q) {
                return(1);
            } else {
                $this->err = $falha['excluir'];
                return(0);
            }
        } else {
            $this->err = "Este registro n�o pode ser exclu�do, pois existem registros relacionadas a ele.";
        }
    }

    /**
      m�todo: make_list
      prop�sito: faz a listagem para colocar no select
     */
    function make_list_select($filtro = "", $ordem = " ORDER BY idstatus_os ASC") {

        // vari�veis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------


        $list_sql = "	SELECT
                                *
                        FROM
                                {$conf['db_name']}status_os_programacao
                        $filtro
                        $ordem";

        $list_q = $db->query($list_sql);
        if ($list_q) {

            //busca os registros no banco de dados e monta o vetor de retorno
            $list_return = array();
            $cont = 0;
            while ($list = $db->fetch_array($list_q)) {

                foreach ($list as $campo => $value) {
                    $list_return["$campo"][$cont] = $value;
                }

                $cont++;
            }

            return $list_return;
        } else {
            $this->err = $falha['listar'];
            return(0);
        }
    }

}

// fim da classe
?>