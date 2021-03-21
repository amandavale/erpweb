<?php

class filial {

    var $err;

    /**
     * construtor da classe
     */
    function filial() {
        // não faz nada
    }

    /**
      método: BuscaDadosFilial
      propósito: busca informações
     */
    function BuscaDadosFilial($idfilial) {

        // variáveis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------

        $get_sql = "	SELECT
                                FLI.*, EDR.*, BAR.*, CID.*, EST.*
                        FROM
                                {$conf['db_name']}filial FLI
                                LEFT OUTER JOIN {$conf['db_name']}endereco EDR ON FLI.idendereco_filial=EDR.idendereco
                                LEFT OUTER JOIN {$conf['db_name']}bairro BAR ON EDR.idbairro=BAR.idbairro
                                LEFT OUTER JOIN {$conf['db_name']}cidade CID ON EDR.idcidade=CID.idcidade
                                LEFT OUTER JOIN {$conf['db_name']}estado EST ON EDR.idestado=EST.idestado

                        WHERE
                                    FLI.idfilial = $idfilial ";

        //executa a query no banco de dados
        $get_q = $db->query($get_sql);

        //testa se a consulta foi bem sucedida
        if ($get_q) { //foi bem sucedida
            $get = $db->fetch_array($get_q);


            if ($get['telefone_filial'] != '')
                $get['telefone_formatado'] = $form->formataTelefoneParaExibir($get['telefone_filial']);
            if ($get['fax_filial'] != '')
                $get['fax_formatado'] = $form->formataTelefoneParaExibir($get['fax_filial']);

            $get['endereco']['linha1'] = $get['logradouro'];
            if ($get['numero'] != '')
                $get['endereco']['linha1'] .= ', ' . $get['numero'];
            if ($get['complemento'] != '')
                $get['endereco']['linha1'] .= ' - ' . $get['complemento'];
            if ($get['nome_bairro'] != '')
                $get['endereco']['linha1'] .= ' Bairro ' . $get['nome_bairro'];
            $get['endereco']['linha2'] = $get['nome_cidade'] . ' - ' . $get['sigla_estado'];
            if ($get['cep'] != '')
                $get['endereco']['linha2'] .= ' - ' . $get['cep'];

            //retorna o vetor associativo com os dados
            return $get;
        }
        else { //deu erro no banco de dados
            $this->err = $falha['listar'];
            return(0);
        }
    }

    /**
      método: Busca_Numero_Nota_Fiscal
      propósito: Busca_Numero_Nota_Fiscal
     */
    function Busca_Numero_Nota_Fiscal($idfilial) {

        // variáveis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------
        // busca o proximo numero da nota fiscal de uma filial
        $info_filial = $this->getById($idfilial);
        $numeroNota = $info_filial['prox_numero_nf_filial'];
        //-------------------------------------------------------
        // atualiza o proximo numero da nota fiscal da filial
        $get_sql = "	UPDATE
											{$conf['db_name']}filial
										SET
											prox_numero_nf_filial = prox_numero_nf_filial + 1
										WHERE
											idfilial = $idfilial
									";

        //executa a query no banco de dados
        $get_q = $db->query($get_sql);

        return($numeroNota);
    }

    /**
      método: Verifica_CNPJ_Duplicado
      propósito: Verifica_CNPJ_Duplicado
     */
    function Verifica_CNPJ_Duplicado($CNPJ, $idfilial = "") {

        // variáveis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------

        $filtro = "";

        if ($idfilial != "")
            $filtro = " AND FLI.idfilial <> $idfilial ";

        $get_sql = "	SELECT
											FLI.*
										FROM
											{$conf['db_name']}filial FLI
										WHERE
											 FLI.cnpj_filial = '$CNPJ'

											 $filtro

									";

        //executa a query no banco de dados
        $get_q = $db->query($get_sql);

        //testa se a consulta foi bem sucedida
        if ($get_q) { //foi bem sucedida
            if ($db->num_rows($get_q) == 0)
                return (false);
            else
                return (true);
        }
        else { //deu erro no banco de dados
            $this->err = $falha['listar'];
            return(0);
        }
    }

    /**
      método: getById
      propósito: busca informações
     */
    function getById($idfilial) {

        // variáveis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------

        $get_sql = "	SELECT
							FLI.*
						FROM
							{$conf['db_name']}filial FLI
						WHERE
							 FLI.idfilial = $idfilial ";

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
      método: make_list
      propósito: faz a listagem
     */
    function make_list($pg, $rppg, $filtro = "", $ordem = "", $url = "") {

        if ($ordem == "")
            $ordem = " ORDER BY FLI.nome_filial ASC";

        // variáveis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------

        $list_sql = "	SELECT
											FLI.*   , EDR.idendereco
										FROM
           						{$conf['db_name']}filial FLI 
												 INNER JOIN {$conf['db_name']}endereco EDR ON FLI.idendereco_filial=EDR.idendereco
												
										$filtro
										$ordem";

        //manda fazer a paginação
        $list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

        if ($list_q) {

            //busca os registros no banco de dados e monta o vetor de retorno
            $list_return = array();
            $cont = 0;
            while ($list = $db->fetch_array($list_q)) {

                //insere um índice na listagem
                $list['index'] = $cont + 1 + ($pg * $rppg);


                $list['telefone_filial'] = $form->FormataTelefoneParaExibir($list['telefone_filial']);
                $list['fax_filial'] = $form->FormataTelefoneParaExibir($list['fax_filial']);


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
      método: set
      propósito: inclui novo registro
     */
    function set($info) {

        // variáveis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------


        $set_sql = "  INSERT INTO
		                  {$conf['db_name']}filial
		                    (
		                    
												nome_filial, 
												cnpj_filial, 
												inscricao_estadual_filial, 
												idendereco_filial, 
												telefone_filial, 
												fax_filial, 
												email_filial, 
												site_filial, 
												prox_numero_nf_filial,
												observacao_filial
												
												)
		                VALUES
		                    (
		                    
		                    '" . $info['nome_filial'] . "',  
												'" . $info['cnpj_filial'] . "',  
												'" . $info['inscricao_estadual_filial'] . "',  
												" . $info['idendereco_filial'] . ",  
												'" . $info['telefone_filial'] . "',  
												'" . $info['fax_filial'] . "',  
												'" . $info['email_filial'] . "',  
												'" . $info['site_filial'] . "',  
												" . $info['prox_numero_nf_filial'] . ",
												'" . $info['observacao_filial'] . "'
												
												)";

        //executa a query e testa se a consulta foi "boa"
        if ($db->query($set_sql)) {
            //retorna o código inserido
            $codigo = $db->insert_id();



            return($codigo);
        } else {
            $this->err = $falha['inserir'];
            return(0);
        }
    }

    /**
      método: update
      propósito: atualiza os dados

      1) o vetor $info deve conter todos os campos tabela a serem atualizados
      2) a variável $id deve conter o código do usuário cujos dados serão atualizados
      3) campos literais deverão ter o prefixo lit e campos numéricos deverão ter o prefixo num
     */
    function update($idfilial, $info) {

        // variáveis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------
        //inicializa a query
        $update_sql = "	UPDATE
												{$conf['db_name']}filial
											SET ";

        //varre o formulário e monta a consulta;
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

            //testa se é o último
            if ($cont != $cont_validos) {
                $update_sql .= ", ";
            }
        }


        //completa o sql com a restrição
        $update_sql .= " WHERE  idfilial = $idfilial ";


        //envia a query para o banco
        $update_q = $db->query($update_sql);

        if ($update_q)
            return(1);
        else
            $this->err = $falha['alterar'];
    }

    /**
      método: delete
      propósito: excluir registro
     */
    function delete($idfilial) {

        // variáveis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------
        // conjunto de dependências geradas
        //---------------------
        // verifica se pode excluir
        if (1) {



            $delete_sql = "	DELETE FROM
													{$conf['db_name']}filial
												WHERE
													 idfilial = $idfilial ";
            $delete_q = $db->query($delete_sql);


            if ($delete_q) {
                return(1);
            } else {
                $this->err = $falha['excluir'];
                return(0);
            }
        } else {
            $this->err = "Este registro não pode ser excluído, pois existem registros relacionados a ele.";
        }
    }

    /**
      método: make_list
      propósito: faz a listagem para colocar no select
     */
    function make_list_select($filtro = "", $ordem = " ORDER BY nome_filial ASC", $idfuncionario = null) {

        // variáveis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------


        if (!is_null($idfuncionario)) {

            $sqlFilialFuncionario = "SELECT idfilial FROM filial_funcionario WHERE idfuncionario = $idfuncionario ";
            $queryFilialFunc = $db->query($sqlFilialFuncionario);

            while ($list = $db->fetch_array($queryFilialFunc)) {
                $arrayFilialFunc[] = $list['idfilial'];
            }
        }


        $list_sql = "	SELECT
		 					FIL.*
							FROM
								{$conf['db_name']}filial FIL
							
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
                    
                    if(is_array($arrayFilialFunc)){
                        $list_return['selected'][$cont] = in_array($list['idfilial'], $arrayFilialFunc);
                    }
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