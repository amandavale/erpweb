<?php

require_once dirname(__FILE__) . '/boleto.php';

class movimento {

    var $err;

    /**
     * construtor da classe
     */
    function movimento() {
        // não faz nada
    }

	function TemPermissaoBoleto($idmovimento, $idcliente) {
        return true;
        /* TODO: Arrumar consulta
         * 
         *            global $form, $db, $conf, $falha;

          $sql = "SELECT
          MOVIM.idmovimento
          FROM {$conf['db_name']}movimento MOVIM
          JOIN apartamento APTO ON (APTO.idmorador = $idcliente OR APTO.idproprietario = $idcliente)
          WHERE
          (MOVIM.idcliente_origem = $idcliente OR MOVIM.idcliente_destino = $idcliente) AND MOVIM.idmovimento = $idmovimento ";

          //executa a query no banco de dados
          $sql_rs = $db->query($sql);

          //testa se a consulta foi bem sucedida
          if($db->num_rows($sql_rs) > 0){ //foi bem sucedida
          return true;
          }

          return false;
         */
    }

    /**
      * método: getById
      * propósito: busca informações
     */
    function getById($idmovimento) {

        // variáveis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------

        $get_sql = "SELECT
                                MOVIM.*, APTO.apto
                            FROM 
                                {$conf['db_name']}movimento MOVIM
                                LEFT JOIN apartamento APTO USING(idapartamento)
                            WHERE
                                MOVIM.idmovimento = $idmovimento ";

        //executa a query no banco de dados
        $get_q = $db->query($get_sql);

        //testa se a consulta foi bem sucedida
        if ($get_q) { //foi bem sucedida
            $get = $db->fetch_array($get_q);

            if ($get['data_movimento'] != '0000-00-00')
                $get['data_movimento'] = $form->FormataDataParaExibir($get['data_movimento']);
            else
                $get['data_movimento'] = "";
            if ($get['data_vencimento'] != '0000-00-00')
                $get['data_vencimento'] = $form->FormataDataParaExibir($get['data_vencimento']);
            else
                $get['data_vencimento'] = "";

            if ($get['data_cadastro'] != '0000-00-00 00:00:00') {
                $array = split(" ", $get['data_cadastro']);
                $get['data_cadastro_D'] = $form->FormataDataParaExibir($array[0]);
                $get['data_cadastro_H'] = $array[1];
            }
            if ($get['data_baixa'] != '0000-00-00 00:00:00') {
                $array = split(" ", $get['data_baixa']);
                $get['data_baixa_D'] = $form->FormataDataParaExibir($array[0]);
                $get['data_baixa_H'] = $array[1];
            }

            $get['juros'] = $form->FormataMoedaParaExibir($get['valor_juros'] * 100 / $get['valor_movimento']);
            $get['multa'] = $form->FormataMoedaParaExibir($get['valor_multa'] * 100 / $get['valor_movimento']);


            if (!empty($get['valor_movimento']))
                $get['valor_movimento'] = number_format($get['valor_movimento'], 2, ",", "");
            if (!empty($get['valor_juros']))
                $get['valor_juros'] = number_format($get['valor_juros'], 2, ",", "");
            if (!empty($get['valor_multa']))
                $get['valor_multa'] = number_format($get['valor_multa'], 2, ",", "");
            if (!empty($get['desconto']))
                $get['desconto'] = number_format($get['desconto'], 2, ",", "");
            if (!empty($get['taxa_boleto']))
                $get['taxa_boleto'] = number_format($get['taxa_boleto'], 2, ",", "");


            //retorna o vetor associativo com os dados
            return $get;
        }
        else { //deu erro no banco de dados
            $this->err = $falha['listar'];
            return(0);
        }
    }

    function Calcula_MultaJurosDesc($idcondominio, $valor, $vencimento, $info_remessa = false) {

        global $form, $conf, $db, $falha, $cliente_condominio;

        $retorno = array('novo_vencimento' => NULL,
            'multa' => 0.00,
            'juros' => 0.00,
            'desconto' => 0.00,
            'valor_total' => 0.00
        );

        $dadosCondominio = $cliente_condominio->getById($idcondominio);
        
        //Pega a data de vencimento e a data atual no formato americano
        $dataVencimento = $form->FormataDataParaInserir($vencimento);
        $dataHoje = date('Y-m-d');

        $valDesconto = $form->FormataMoedaParaInserir($dadosCondominio['desconto_boleto']);

        $retorno['vencido'] = false;
        $retorno['instrucoes'] = $dadosCondominio['instrucoes_boleto'];


        //Aplica Multa e Juros caso o cliente gere o boleto após a data de vencimento
        if (strtotime($dataHoje) > strtotime($dataVencimento)) {

            $retorno['vencido'] = true;
            $diasExcedentes = $form->get_days_diff($dataHoje, $dataVencimento);
            
            $valor        = $form->formataMoedaParaInserir($valor);
            $juros_boleto = $form->formataMoedaParaInserir($dadosCondominio['juros_boleto']);
            $multa_boleto = $form->formataMoedaParaInserir($dadosCondominio['multa_boleto']);

            $multa = $form->valorPorcentagem($multa_boleto, $valor);
            $juros = ( $form->valorPorcentagem($juros_boleto, $valor) * $diasExcedentes );

            $retorno['novo_vencimento'] = $form->Altera_Data(date('Y-m-d'), '+3 Days', 'd/m/Y');
            $retorno['multa'] = $form->FormataMoedaParaExibir($multa);
            $retorno['juros'] = $form->FormataMoedaParaExibir($juros);
            
        } elseif ($valDesconto > 0) { //Aplica desconto de pagamento até a data de vencimento caso houver
            $retorno['desconto'] = $form->FormataMoedaParaExibir($valDesconto);
        }

        if($info_remessa){

            /// Se o parâmetro $info_remessa foi definido como true calcula valores de juros, multa e desconto
            /// para informação a gerar na remessa
            $juros_boleto = $form->formataMoedaParaInserir($dadosCondominio['juros_boleto']);
            $multa_boleto = $form->formataMoedaParaInserir($dadosCondominio['multa_boleto']);

            $retorno['multa'] = $form->valorPorcentagem($multa_boleto, $valor);
            $retorno['juros'] = $form->valorPorcentagem($juros_boleto, $valor);
            $retorno['desconto'] = $valDesconto;

        }

        $retorno['valor_total'] = $form->FormataMoedaParaExibir($valor + ($multa + $juros) - $valDesconto);


        return $retorno;
    }


    /**
     * Busca movimentos para geração de arquivo de remessa
     * @param array $dados - Array contendo dados do filtro escolhido na tela de busca, para busca dos movimentos.
     */
    function buscaMovimentosRemessa($dados){

        // variáveis globais
        global $form, $conf, $db, $falha;

        $boleto = new boleto();

        /// Array com os dados dos movimentos, será retornado ao fim do método
        $dados_movimentos = array();

        /// Array que armazena informações de movimentos com falta de informação no endereço
        $erro_endereco = array();

        /// Array com filtro de busca. Sempre buscará somente movimentos que geram boleto, ainda
        /// não foram baixados e ainda não foram incluídos em um arquivo de remessa
        $query = array("gerar_fatura = '1'", "(baixado = '0' OR baixado = '')", "idarquivo_remessa IS NULL");

        if(isset($dados['data_movimento_de']) && $dados['data_movimento_de']){
            $dados['data_movimento_de'] = $form->formataDataParaInserir($dados['data_movimento_de']);
            $query[] = "data_movimento >= '" . $dados['data_movimento_de'] . "'";
        }

        if(isset($dados['data_movimento_ate']) && $dados['data_movimento_ate']){
            $dados['data_movimento_ate'] = $form->formataDataParaInserir($dados['data_movimento_ate']);
            $query[] = "data_movimento <= '" . $dados['data_movimento_ate'] . "'";
        }

        if(isset($dados['data_vencimento_de']) && $dados['data_vencimento_de']){
            $dados['data_vencimento_de'] = $form->formataDataParaInserir($dados['data_vencimento_de']);
            $query[] = "data_vencimento >= '" . $dados['data_vencimento_de'] . "'";
        }

        if(isset($dados['data_vencimento_ate']) && $dados['data_vencimento_ate']){
            $dados['data_vencimento_ate'] = $form->formataDataParaInserir($dados['data_vencimento_ate']);
            $query[] = "data_vencimento <= '" . $dados['data_vencimento_ate'] . "'";
        }

        $list_sql = 'SELECT idmovimento FROM movimento WHERE ' . implode(' AND ', $query);

        $list_q = $db->query($list_sql);
        
        if ($list_q) {

            while ($row = $db->fetch_array($list_q)) {

                $dados_remessa = $boleto->geraInformacoesCobranca($row['idmovimento']);

                $valor = str_replace('.','', $dados_remessa['valor']);
                $valor = str_replace(',','.', $valor);

                /// Indica se houve alguma irregularidade nas informações da remessa
                $dados_remessa['erro'] = '0';

                /// verifica se o endereço está preenchido
                if(
                        (
                            !$dados_remessa['logradouro'] || !$dados_remessa['cidade'] || !$dados_remessa['bairro'] 
                            || !$dados_remessa['uf']
                        )
                    )

                    {

                    /// Se o endereço não estiver preenchido inclui a informação para mostrar na tela e não
                    /// deixar incluir o movimento na remessa
                    $dados_remessa['erro'] = '1';
                }

                /// Se os dados necessários estiverem preenchidos inclui o movimento para geração de remessa
                $dados_movimentos[$row['idmovimento']] =  $dados_remessa;
            }
        }

        return array('movimentos' => $dados_movimentos, 'erro_endereco' => $erro_endereco);
    }


    /**
     * Busca todos os movimentos associados a um arquivo de remessa
     * @param integer $idarquivo_remessa - ID do arquivo de remessa
     * @return array $dados_movimentos - Array contendo os dados dos movimentos encontrados
     */
    function buscaConteudoArquivoRemessa($idarquivo_remessa){

        global $db;

        $boleto = new boleto();

        /// Array com os dados dos movimentos, será retornado ao fim do método
        $dados_movimentos = array();


        $list_sql = 'SELECT idmovimento FROM movimento WHERE idarquivo_remessa = ' . $idarquivo_remessa ;

        $list_q = $db->query($list_sql);
        
        if ($list_q) {

            while ($row = $db->fetch_array($list_q)) {                
                $dados_movimentos[$row['idmovimento']] =  $boleto->geraInformacoesCobranca($row['idmovimento']);
            }
        }

        return $dados_movimentos;

    }
    
    
    function pesquisaMovimento($filtro = '', $ordem = ''){

    	// variáveis globais
    	global $form, $conf, $db, $falha;
    	 
    	if ($ordem == '')
    		$ordem = ' ORDER BY idmovimento';
    	
    	$list_sql = 'SELECT 
    					movimento.*, 
      					PDebito.numero as numero_debito, PDebito.nome as nome_debito, 
      					PCredito.numero as numero_credito , PCredito.nome as nome_credito ' .
				    'FROM ' .
    				"		{$conf['db_name']}movimento " .
    				"LEFT JOIN {$conf['db_name']}plano PDebito ON (movimento.idplano_debito = PDebito.idplano) " .
    				"LEFT JOIN {$conf['db_name']}plano PCredito ON (movimento.idplano_credito = PCredito.idplano) " .
    				$filtro . 
    				' ' .
    				$ordem;

    	//manda fazer a paginação
    	$list_q = $db->query($list_sql);
    	
    	if ($list_q) {
    	
    		//busca os registros no banco de dados e monta o vetor de retorno
    		$list_return = array();
    	
    		while ($row = $db->fetch_array($list_q)) {
    			
    			$row['idplano_debito_descricao'] = $row['numero_debito'] . ' - ' . $row['nome_debito'];
    			$row['idplano_credito_descricao'] = $row['numero_credito'] . ' - ' . $row['nome_credito'];
    			
    			$list_return[] = $row;
    		}
    		
    		return $list_return;
    	
    	}
    	else{
    		return false;
    	}
    }
    

    function make_list($pg, $rppg, $filtro = "", $ordem = "", $url = "") {

        if ($ordem == "")
            $ordem = " ORDER BY MOVIM.data_movimento DESC";

        // variáveis globais
        global $form, $conf, $db, $falha, $cliente;

        if(!$cliente){
            $cliente = new cliente();
        }
        
        //---------------------
        //Faz o tratamento do string recebida em $url para tramento de parâmetros
        if (!empty($url)) {

            $parametros = array();

            $paramForm = array_filter(explode('&', $url), 'strlen');
            foreach ($paramForm as $param) {
                $paramArr = explode('=', $param);
                $parametros[$paramArr[0]] = $paramArr[1];
            }
        }



        $list_sql = "	SELECT
                                MOVIM.*   , PLA.nome , CFLI.conta_filial , 
                                MOVIM.descricao_movimento , FLI.nome_filial,
                                APT.apto,
                                CLIDES.nome_cliente AS nome_cliente_dest,
                                CLIDES.idcliente AS idcliente_dest,
                                CLIORI.nome_cliente AS nome_cliente_orig,
                                CLIORI.idcliente AS idcliente_orig
                        FROM
                                {$conf['db_name']}movimento MOVIM
                                LEFT JOIN  {$conf['db_name']}plano PLA ON ( (MOVIM.idplano_debito=PLA.idplano))
                                LEFT JOIN  {$conf['db_name']}conta_filial CFLI ON MOVIM.idconta_filial=CFLI.idconta_filial 
                                LEFT JOIN  {$conf['db_name']}cliente CLIDES ON CLIDES.idcliente = MOVIM.idcliente_destino
                                LEFT JOIN  {$conf['db_name']}cliente CLIORI ON CLIORI.idcliente = MOVIM.idcliente_origem
                                LEFT JOIN  {$conf['db_name']}apartamento APT ON APT.idapartamento = MOVIM.idapartamento
                                INNER JOIN {$conf['db_name']}filial FLI ON MOVIM.idfilial=FLI.idfilial 

                        $filtro
                        $ordem";

        if ($_GET['target'] == 'full')
            $rppg = 99999;
        elseif ($rppg == '')
            $rppg = $conf['rppg'];

        //manda fazer a paginação
        $list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);


        if ($list_q) {

            //busca os registros no banco de dados e monta o vetor de retorno
            $list_return = $listAll = array();
            $cont = 0;
            $soma_movimento = $somaCredito = $somaDebito = 0.00;

            while ($row = $db->fetch_array($list_q)) {

				/** Monta versão completa da identificação do apartamento, onde 
				 *  o nome do condômino é incluído na descricao */

				if(isset($row['apto']) && !empty($row['apto']))
					$row['apto_completo'] = $row['apto'] . ' - ' . $row['nome_cliente_orig']; 

            	//Se um cliente foi selecionado, insere o nome dele na coluna Cliente da listagem de movimentos, independente de ser cliente de débito ou crédito
                if (isset($parametros['idcliente'])) {

                    $row['nome_cliente'] = $parametros['idcliente'] == $row['idcliente_dest'] ? $row['nome_cliente_dest'] : $row['nome_cliente_orig'];
                    $listAll[] = $row;
                    
                }//Se foi setado cliente de origem e destino, verifica se o registro deve aparecer duas vezes na listagem (uma como crédito e outra como débito) 
                elseif (!empty($row['idcliente_dest']) && !empty($row['idcliente_orig']) && !(isset($_GET['ac']) && $_GET['ac'] == 'baixar_movimentos') ) {

                    //Guara a referência do cliente de destino
                    $idcliente_dest      = $row['idcliente_dest'];
                    $nome_cliente_dest   = $row['nome_cliente_dest'];


                    //Insere na lista somente se o registro for pertinente a algum condomínio, ou ao Cliente Erpweb (quem está usando o software)
					if($cliente->chk_cliente_condominio($row['idcliente_orig']) || $row['idcliente_orig'] == $conf['idcliente_erpweb']){                    
	                    
						//Define a linha do registro com cliente de origem
						$row['nome_cliente'] = $row['nome_cliente_orig'];
	                    
						//Remove da linha a referência de cliente de destino
	                    unset($row['idcliente_dest'], $row['nome_cliente_dest']);
	                    $listAll[] = $row;
					}
	
					//Mesmo tratamento de linha de registro, agora sob a ótica do cliente de destino	
					if($cliente->chk_cliente_condominio($idcliente_dest) || $idcliente_dest == $conf['idcliente_erpweb']){
						
						//Define a linha do registro com cliente de destino
	                    $row['idcliente_dest'] = $idcliente_dest;
	                    
	                    //Remove da linha a referência de cliente de origem
	                    $row['nome_cliente']   = $nome_cliente_dest;
	                    unset($row['idcliente_orig'], $row['nome_cliente_orig']);
	                    $listAll[] = $row;
					}               


                } 
                else {

                    $row['nome_cliente'] = (!empty($row['idcliente_dest'])) ? $row['nome_cliente_dest'] : $row['nome_cliente_orig'];                    
                    $listAll[] = $row;
                    
                }
            }

            foreach ($listAll as $list) {
                //Incrementa o valor do registro no somatório total
                $soma_movimento += $list['valor_movimento'];

                //insere um índice na listagem
                $list['index'] = $cont + 1 + ($pg * $rppg);


                //*********************** Formatação dos Dados para listagem ********************************/

                if ($list['data_movimento'] != '0000-00-00')
                    $list['data_movimento'] = $form->FormataDataParaExibir($list['data_movimento']);
                else
                    $list['data_movimento'] = "";


                if ($list['data_vencimento'] != '0000-00-00')
                    $list['data_vencimento'] = $form->FormataDataParaExibir($list['data_vencimento']);
                else
                    $list['data_vencimento'] = "";


                if ($list['data_cadastro'] != '0000-00-00 00:00:00') {
                    $array = split(" ", $list['data_cadastro']);
                    $list['data_cadastro'] = $form->FormataDataParaExibir($array[0]) . " " . $array[1];
                }
                else
                    $list['data_cadastro'] = "";


                if ($list['data_baixa'] != '0000-00-00 00:00:00') {
                    $array = split(" ", $list['data_baixa']);
                    $list['data_baixa'] = $form->FormataDataParaExibir($array[0]); // . " " . $array[1];
                }
                else
                    $list['data_baixa'] = "";

                if ($list['valor_movimento'] != "")
                    $list['valor_movimento'] = number_format($list['valor_movimento'], 2, ",", "");


                if ($list['valor_juros'] != "")
                    $list['valor_juros'] = number_format($list['valor_juros'], 2, ",", "");


                if ($list['valor_multa'] != "")
                    $list['valor_multa'] = number_format($list['valor_multa'], 2, ",", "");

                if ($list['baixado'] == '0')
                    $list['baixado'] = "Não";
                elseif ($list['baixado'] == 'Sim')
                    $list['baixado'] = "";


                if ($list['gerar_fatura'] == '0')
                    $list['gerar_fatura'] = "Não";
                elseif ($list['gerar_fatura'] == '1')
                    $list['gerar_fatura'] = "Sim";


                //***************************************************************************************//
                //Verifica se o valor da movimentação deve ir para a coluna de crédito ou débito
                if (isset($parametros['idcliente'])) {

                    if ($parametros['idcliente'] == $list['idcliente_orig']) {

                        $list['valor_debito'] = $list['valor_movimento'];
                        $list['valor_credito'] = NULL;
                        $somaDebito += str_replace(',', '.', $list['valor_movimento']);
                    } else {
                        $list['valor_debito'] = NULL;
                        $list['valor_credito'] = $list['valor_movimento'];
                        $somaCredito += str_replace(',', '.', $list['valor_movimento']);
                    }
                } elseif (!empty($list['idcliente_orig'])) {
                    $list['valor_debito'] = $list['valor_movimento'];
                    $list['valor_credito'] = NULL;
                    $somaDebito += str_replace(',', '.', $list['valor_movimento']);
                } elseif (!empty($list['idcliente_dest'])) {
                    $list['valor_debito'] = NULL;
                    $list['valor_credito'] = $list['valor_movimento'];
                    $somaCredito += str_replace(',', '.', $list['valor_movimento']);
                }
                //---------------------------------------------------------------------------


                $list_return[] = $list;
                $cont++;
            }

            //Usa a última linha da listagem para informar o somatório dos movimentos
            $list_return[] = array('descricao_movimento' => 'Total',
					                'valor_movimento' 	  => 'R$ '.number_format($soma_movimento,2,',','.'), //$form->FormataMoedaParaExibir($soma_movimento),
					                'valor_debito'        => 'R$ '.number_format($somaDebito,2,',','.'), //$form->FormataMoedaParaExibir($somaDebito),
					                'valor_credito'       => 'R$ '.number_format($somaCredito,2,',','.')  //$form->FormataMoedaParaExibir($somaCredito) 
            				);

            return $list_return;
        } else {
            $this->err = $falha['listar'];
            return(0);
        }
    }

    /**
     * método: make_list
     * propósito: faz a listagem
     */
    function make_list_boleto($pg, $rppg, $filtro = "", $ordem = "", $url = "", $comTotalizador = true) {

        if ($ordem == "")
            $ordem = " ORDER BY MOVIM.data_movimento DESC";

        // variáveis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------

        $list_sql = "	SELECT
                                    MOVIM.*   , PLA.nome , CFLI.conta_filial , 
                                    MOVIM.descricao_movimento , FLI.nome_filial,
                                    APT.apto,
                                    IF(APT.apto <> '',CLIDES.idcliente, NULL) AS idcondominio,
                                    CLIDES.nome_cliente AS nome_cliente_dest,
                                    CLIORI.nome_cliente AS nome_cliente_orig  
                            FROM
                            {$conf['db_name']}movimento MOVIM
                                        LEFT JOIN  {$conf['db_name']}plano PLA ON ( (MOVIM.idplano_debito=PLA.idplano))
                                        LEFT JOIN  {$conf['db_name']}conta_filial CFLI ON MOVIM.idconta_filial=CFLI.idconta_filial 
                                        LEFT JOIN  {$conf['db_name']}cliente CLIDES ON CLIDES.idcliente = MOVIM.idcliente_destino
                                        LEFT JOIN  {$conf['db_name']}cliente CLIORI ON CLIORI.idcliente = MOVIM.idcliente_origem
                                        LEFT JOIN  {$conf['db_name']}apartamento APT ON APT.idapartamento = MOVIM.idapartamento
                                        INNER JOIN {$conf['db_name']}filial FLI ON MOVIM.idfilial=FLI.idfilial 

                                        $filtro
                                        $ordem";

        if ($_GET['target'] == 'full')
            $rppg = 99999;
        elseif ($rppg == '')
            $rppg = $conf['rppg'];

        //manda fazer a paginação
        $list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);


        if ($list_q) {

            //busca os registros no banco de dados e monta o vetor de retorno
            $list_return = array();
            $cont = 0;
            $soma_movimento = 0.00;

            while ($list = $db->fetch_array($list_q)) {

                //Incrementa o valor do registro no somatório total
                $soma_movimento += $list['valor_movimento'];


                //insere um índice na listagem
                $list['index'] = $cont + 1 + ($pg * $rppg);

                if ($list['data_movimento'] != '0000-00-00')
                    $list['data_movimento'] = $form->FormataDataParaExibir($list['data_movimento']);
                else
                    $list['data_movimento'] = "";

                if ($list['data_vencimento'] != '0000-00-00') {

                    $list['data_vencimento'] = $form->FormataDataParaExibir($list['data_vencimento']);
                }
                else
                    $list['data_vencimento'] = "";


                //Dados específicos para geração atomática de segunda via do boleto
                if ($list['data_vencimento'] != '') {
                    $list['boleto_2via'] = $this->Calcula_MultaJurosDesc($list['idcondominio'], $list['valor_movimento'], $list['data_vencimento']);
                }

                if ($list['data_cadastro'] != '0000-00-00 00:00:00') {
                    $array = split(" ", $list['data_cadastro']);
                    $list['data_cadastro'] = $form->FormataDataParaExibir($array[0]) . " " . $array[1];
                }
                else
                    $list['data_cadastro'] = "";

                if ($list['data_baixa'] != '0000-00-00 00:00:00') {
                    $array = split(" ", $list['data_baixa']);
                    $list['data_baixa'] = $form->FormataDataParaExibir($array[0]); // . " " . $array[1];
                }
                else
                    $list['data_baixa'] = "";

                if ($list['valor_movimento'] != "")
                    $list['valor_movimento'] = number_format($list['valor_movimento'], 2, ",", "");
                if ($list['valor_juros'] != "")
                    $list['valor_juros'] = number_format($list['valor_juros'], 2, ",", "");
                if ($list['valor_multa'] != "")
                    $list['valor_multa'] = number_format($list['valor_multa'], 2, ",", "");

                if ($list['baixado'] == '0')
                    $list['baixado'] = "Não";
                else if ($list['baixado'] == 'Sim')
                    $list['baixado'] = "";
                if ($list['gerar_fatura'] == '0')
                    $list['gerar_fatura'] = "Não";
                else if ($list['gerar_fatura'] == '1')
                    $list['gerar_fatura'] = "Sim";


                $list_return[] = $list;
                $cont++;
            }

            if ($comTotalizador) {
                //Usa a última linha da listagem para informar o TOTAL dos movimentos
                $list_return[] = array('descricao_movimento' => 'Total', 'valor_movimento' => $form->FormataMoedaParaExibir($soma_movimento));
            }
            return $list_return;
        } else {
            $this->err = $falha['listar'];
            return(0);
        }
    }

    /**
     * método: set
     * propósito: inclui novo registro
     */
    function set($info) {
    	
        // variáveis globais
        global $form;
        global $conf;
        global $db;
        global $falha;

        //---------------------
        //Tratamento dos campos numéricos caso eles estejam vazios
        $fields = array('idplano_debito', 'idplano_credito', 'idcliente_origem', 'idcliente_destino',
            'idconta_pagar', 'idconta_receber', 'idconta_filial', 'idmovimento_origem',
            'idfilial', 'valor_movimento', 'valor_juros', 'valor_multa', 'desconto',
            'idapartamento', 'taxa_boleto', 'idorcamento', 'tipo_baixa');
        
        foreach ($fields as $field){
            if (empty($info[$field])){
                $info[$field] = 'NULL';
            }
        }

    	$set_sql = "  INSERT INTO
						{$conf['db_name']}movimento(
					  
	          			idcliente_origem,
						idcliente_destino,
	          			idconta_pagar,
						idconta_receber,
						idplano_debito, 
						idplano_credito, 
						idconta_filial, 
						idmovimento_origem, 
						idfilial, 
						descricao_movimento, 
						controle_movimento, 
						valor_movimento, 
						data_cadastro, 
						data_movimento, 
						data_baixa, 
						baixado, 
						data_vencimento, 
						observacao, 
						valor_juros, 
						valor_multa,
						desconto, 
						gerar_fatura,
						taxa_boleto,
						idapartamento,
						idorcamento,
                        tipo_baixa
					)
	                VALUES
	                    (
	                  
        					  " . $info['idcliente_origem'] . ",  
      	 				    " . $info['idcliente_destino'] . ",  
          					" . $info['idconta_pagar'] . ",  
          					" . $info['idconta_receber'] . ",  
          					" . $info['idplano_debito'] . ",  
							" . $info['idplano_credito'] . ",  
							" . $info['idconta_filial'] . ",  
							" . $info['idmovimento_origem'] . ",  
							" . $info['idfilial'] . ",  
							'" . $info['descricao_movimento'] . "',  
							'" . $info['controle_movimento'] . "',  
							" . $info['valor_movimento'] . ",  
							'" . $info['data_cadastro'] . "',  
							'" . $info['data_movimento'] . "',  
							'" . $info['data_baixa'] . "',  
							'" . $info['baixado'] . "',  
							'" . $info['data_vencimento'] . "',  
							'" . $info['observacao'] . "',  
							" . $info['valor_juros'] . ",  
							" . $info['valor_multa'] . ",  
							" . $info['desconto'] . ",  
							'" . $info['gerar_fatura'] . "',
							" . $info['taxa_boleto'] . ",
            				" . $info['idapartamento'] . ",
            				" . $info['idorcamento'] . ",
                            '" . $info['tipo_baixa'] . "'
					)";

        //executa a query e testa se a consulta foi "boa"
        if ($db->query($set_sql)) {

            $codigo = $db->insert_id(); //retorna o código inserido

            return($codigo);
        } else {

            $this->err = $falha['inserir'];
            return(0);
        }
    }


    /**
     * Busca dados padrão para geração de um movimento:
     * ID e descrição de conta de débito e conta de crédito
     * @param integer $id_cliente_origem - ID do cliente de onde será debitado o valor.
     * 						Se não for condomínio a conta de débito deve ser vazia
     * @return array $dados_movimento - Retorna um array com ID e descrição das contas de débito e crédito
     */
    function buscaDadosPadraoMovimento($id_cliente_origem){

    	// variáveis globais
    	global $form;
    	global $conf;
    	global $db;
    	global $falha;
    	//---------------------
    	
    	require_once dirname(__FILE__) . '/parametros.php';
    	require_once dirname(__FILE__) . '/plano.php';
    	require_once dirname(__FILE__) . '/cliente_condominio.php';

    	$parametros = new parametros();
    	$plano = new plano();
    	$cliente_condominio = new cliente_condominio();
    	 
    	/// busca conta de crédito
    	$hierarquia_conta_credito = $parametros->getParam('conta_credito_movimentacao');
    	$conta_credito = $plano->pesquisaPlano(" WHERE PLA.numero = '" . $hierarquia_conta_credito . "'");
    	$id_conta_credito = $conta_credito[0]['idplano'];
    	$descricao_conta_credito = $conta_credito[0]['numero'] . ' - ' . $conta_credito[0]['nome'];
    	 
    	/// busca cliente destino
    	$cliente_destino = $parametros->getParam('cliente_destino');
    	 
    	/// verifica se o cliente é um condomínio para definir conta de débito
    	$dados_cliente_origem = $cliente_condominio->getById($id_cliente_origem);
    	 
    	if(!isset($dados_cliente_origem['idcliente'])){
    		$id_conta_debito = '';
    		$descricao_conta_debito = '';
    	}
    	else{
    	
    		/// busca conta de débito
    		$hierarquia_conta_debito = $parametros->getParam('conta_debito_movimentacao');
    		$dados_conta_debito = $plano->pesquisaPlano(" WHERE PLA.numero = '" . $hierarquia_conta_debito . "'");
    		$id_conta_debito = $dados_conta_debito[0]['idplano'];
    		$descricao_conta_debito = $dados_conta_debito[0]['numero'] . ' - ' . $dados_conta_debito[0]['nome'];
    	}
    	
    	$dados_movimento = array(
    							'id_conta_debito' => $id_conta_debito,
    							'descricao_conta_debito' => $descricao_conta_debito,
    							'id_conta_credito' => $id_conta_credito,
    							'descricao_conta_credito' => $descricao_conta_credito
    						);
    	
    	return $dados_movimento;
    	 
    }
    
    
    /**
     * Gera movimentos fornecidos no array $dados, para o orçamento passado como parâmetro
     * @param integer $id_orcamento
     * @param array $dados
     * @return boolean
     */
    function geraMovimentoOrcamento($id_orcamento, $dados){
    	    	
    	// variáveis globais
    	global $form;
    	global $conf;
    	global $db;
    	global $falha;
    	//---------------------
    	   
    	require_once dirname(__FILE__) . '/parametros.php';
    	require_once dirname(__FILE__) . '/plano.php';
    	require_once dirname(__FILE__) . '/cliente_condominio.php';
    	
    	$sucesso_insercao = true;
    	
    	$parametros = new parametros();
    	$plano = new plano();
    	$cliente_condominio = new cliente_condominio();
    	
    	$descricao_padrao = $parametros->getParam('descricao_movimentacao');

    	/// busca conta de crédito
    	$hierarquia_conta_credito = $parametros->getParam('conta_credito_movimentacao');
    	$conta_credito = $plano->pesquisaPlano(" WHERE PLA.numero = '" . $hierarquia_conta_credito . "'");
    	$conta_credito = $conta_credito[0]['idplano'];
    	
    	/// busca cliente destino
    	$cliente_destino = $parametros->getParam('cliente_destino');
    	
    	/// verifica se o cliente é um condomínio para definir conta de débito
    	$dados_cliente_origem = $cliente_condominio->getById($dados['idcliente']);
    	
    	if(!isset($dados_cliente_origem['idcliente'])){
    		$conta_debito = '';
    	}
    	else{
    		
    		/// busca conta de débito
    		$hierarquia_conta_debito = $parametros->getParam('conta_debito_movimentacao');
    		$dados_conta_debito = $plano->pesquisaPlano(" WHERE PLA.numero = '" . $hierarquia_conta_debito . "'");
    		$conta_debito = $dados_conta_debito[0]['idplano'];
    	}
    	
    	for($i = 1; $i <= $dados['quantidade_de_parcelas']; $i++){
    		
    		///verifica se conta de débito foi fornecida. Se não foi fornecida, usa o valor padrão
    		if(isset($dados['CR_idplano_debito_' . $i]) && ctype_digit($dados['CR_idplano_debito_' . $i])){
    			$conta_debito = $dados['CR_idplano_debito_' . $i]; 
    		}
    		
    		///verifica se conta de crédito foi fornecida. Se não foi fornecida, usa o valor padrão
    		if(isset($dados['CR_idplano_credito_' . $i]) && ctype_digit($dados['CR_idplano_credito_' . $i])){
    			$conta_credito = $dados['CR_idplano_credito_' . $i];
    		}
    		
    		$dados_movimentacao = array();
    		
    		$dados_movimentacao['descricao_movimento'] = sprintf($descricao_padrao,$id_orcamento,$i);    		
    		$dados_movimentacao['observacao'] = 'Movimentação gerada a partir da geração do orçamento.';
    		$dados_movimentacao['valor_movimento'] = $form->FormataMoedaParaInserir($dados['CR_valor_' . $i]);
    		$dados_movimentacao['valor_juros'] = $dados['jurosParcelamento'];
    		$dados_movimentacao['valor_multa'] = '0.0';
    		$dados_movimentacao['desconto'] = '0.0';
    		$dados_movimentacao['taxa_boleto'] = '0.0';
    		$dados_movimentacao['data_movimento'] = date('Y-m-d');
    		$dados_movimentacao['data_vencimento'] = $form->FormataDataParaInserir($dados['CR_data_' . $i]);
    		$dados_movimentacao['data_cadastro'] = date("Y-m-d H:i:s");
    		$dados_movimentacao['idfilial'] = $_SESSION['idfilial_usuario'];  //Insere a filial em que o usuário está logado
    		$dados_movimentacao['idcliente_origem'] = $dados['idcliente'];
    		$dados_movimentacao['idcliente_destino'] = $cliente_destino;
    		$dados_movimentacao['idplano_debito'] = $conta_debito;
    		$dados_movimentacao['idplano_credito'] = $conta_credito;
    		$dados_movimentacao['idorcamento'] = $id_orcamento;

    		$id_movimento = $this->set($dados_movimentacao);
    		
    		if(!$id_movimento){
    			$sucesso_insercao = false;
    		}
    		else{
    			
    			$dados_atualizacao = array(
    					'litdata_baixa' => date('Y-m-d H:i:s'),
    					'litbaixado' => '1'
    			);
    			
    			if(!$this->update($id_movimento, $dados_atualizacao)){
    				$erros = true;
    			}
    			 
    		}
    	}

    	return $sucesso_insercao;
    }
    
    

    /**
     * método: update
     * propósito: atualiza os dados
     *
     * 1) o vetor $info deve conter todos os campos tabela a serem atualizados
     * 2) a variável $id deve conter o código do usuário cujos dados serão atualizados
     * 3) campos literais deverão ter o prefixo lit e campos numéricos deverão ter o prefixo nu
     */
    function update($idmovimento, $info) {

        // variáveis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------

        //inicializa a query
        $update_sql = "	UPDATE
		{$conf['db_name']}movimento
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
            elseif ($tipo_campo == "num") {
                if (empty($valor))
                    $valor = 'NULL';
                $update_sql .= "$nome_campo = $valor";
            }


            $cont++;

            //testa se é o último
            if ($cont != $cont_validos) {
                $update_sql .= ", ";
            }
        }


        //completa o sql com a restrição
        $update_sql .= " WHERE  idmovimento = $idmovimento ";

        //envia a query para o banco
        $update_q = $db->query($update_sql);

        if ($update_q)
            return(1);
        else
            $this->err = $falha['alterar'];
    }

    /**
     * método: delete
     * propósito: excluir registro
     */
    function delete($idmovimento) {

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
			{$conf['db_name']}movimento
												WHERE
													 idmovimento = $idmovimento ";
            $delete_q = $db->query($delete_sql);

            if ($delete_q) {
                return(1);
            } else {
                $this->err = $falha['excluir'];
                return(0);
            }
        } else {
            $this->err = "Este registro não pode ser excluído, pois existem registros relacionadas a ele.";
        }
    }

    
    
    /**
     * Apaga movimentos associados a um orçamento
     * @param integer $id_orcamento
     * @return boolean - Retorna true em caso de sucesso e false em caso de erro
     */
    function apagaMovimentosOrcamento($id_orcamento) {
    
    	// variáveis globais
    	global $form;
    	global $conf;
    	global $db;
    	global $falha;

    	$delete_sql = "	DELETE FROM " .
    				  " {$conf['db_name']}movimento " .
    				  ' WHERE idorcamento = ' . $id_orcamento;
    	
    	$delete_q = $db->query($delete_sql);
    
    	if ($delete_q) {
    		return(1);
    	}
    	else {
  			return(0);
   		}
    }

    /**
     * método: make_list
     * propósito: faz a listagem para colocar no select
     */
    function make_list_select($filtro = "", $ordem = " ORDER BY data_movimento ASC") {

        // variáveis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------


        $list_sql = "	SELECT
		 						*
							FROM
							{$conf['db_name']}movimento
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

    /**
     * método: Filtra_Movimento_AJAX
     * propósito: Processa o campo auto-complete no formulário
     */
    function Filtra_Movimento_AJAX($filtro, $campoID, $idmovimento = "") {

        // variáveis globais
        global $form;
        global $conf;
        global $db;
        global $falha;
        //---------------------
        // verifica qual a pagina atual
        if (!isset($_GET["page"]))
            $pg = 0;
        else
            $pg = $_GET["page"];

        // maximo numero de registros listados
        $rppg = $conf['rppg_auto_completar'];

        // volta o filtro para a codificação original
        $filtro = utf8_decode($filtro);
        $campoID = utf8_decode($campoID);

        // campos de controle
        $campoNomeTemp = $campoID . "_NomeTemp";
        $campoFlag = $campoID . "_Flag";


        //Insere o filtro no editar, para que o usuário não coloque a própria movimentação como a de origem
        if (!empty($idmovimento)) {
            $filtro_editar = 'AND idmovimento <> ' . $idmovimento;
        }


        $list_sql = "	SELECT
	 						*
						FROM
						{$conf['db_name']}movimento
							 
						WHERE
							(
								UPPER(descricao_movimento) LIKE UPPER('%{$filtro}%')
								  OR
								UPPER(controle_movimento) LIKE UPPER('%{$filtro}%')
								  OR
								UPPER(valor_movimento) LIKE UPPER('%{$filtro}%')
								  OR
								UPPER(observacao) LIKE UPPER('%{$filtro}%')
								  
							)
													 	 	
							
							AND idfilial = " . $_SESSION['idfilial_usuario'] . " 
							 	 	 	
							$filtro_editar
							
							
						ORDER BY
							data_movimento ASC ";


        //manda fazer a paginação
        $list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

        if ($list_q) {

            // testa se retornou algum registro
            if ($db->num_rows($list_q) > 0) {
                ?>
                <table width="100%" cellpadding="5" cellspacing="2">
                    <tr onselect="" class="cabecalho">
                        <td width="85%" class="cabecalho_negrito"><?php echo ('Descri&ccedil;&atilde;o'); ?></td>
                        <td class="cabecalho_negrito" align="center"><?php echo ('Controle'); ?></td>
                        <td class="cabecalho_negrito" align="center"><?php echo ('Valor R$'); ?></td>
                    </tr>
                <?php
                //Trata problemas de codificação de carcteres
                strlen($filtro) == 0 ? $filtro = '%' : '';
                $filtro = htmlentities($filtro);
                //-------------------------------------------

                $cont = 0;
                while ($list = $db->fetch_array($list_q)) {

                    //insere um índice na listagem
                    $list['index'] = $cont + 1;
                    $list['descricao_movimento'] = htmlentities($list['descricao_movimento']);
                    $list['valor_movimento'] = str_replace('.', ',', $list['valor_movimento']);

                    // coloca em negrito a string que foi encontrada na palavra
                    $list['descricao_movimento_negrito'] = preg_replace("'$filtro'i", "<span class='substring_negrito'>\\0</span>", $list['descricao_movimento']);
                    $list['controle_movimento_negrito'] = preg_replace("'$filtro'i", "<span class='substring_negrito'>\\0</span>", $list['controle_movimento']);
                    $list['valor_movimento_negrito'] = preg_replace("'$filtro'i", "<span class='substring_negrito'>\\0</span>", $list['valor_movimento']);
                    ?>
                    <tr
                        onselect="
                            this.text.value = '<?php echo ($list['descricao_movimento']); ?>';
                            $('<?php echo $campoNomeTemp; ?>').value = '<?php echo ($list['nome_banco']); ?>';
                            $('<?php echo $campoID; ?>').value = '<?php echo ($list['idmovimento']); ?>';
                            $('<?php echo $campoFlag; ?>').className = 'selecionou'
                        ">
                        <td class="tb_bord_baixo"><?php echo ($list['descricao_movimento_negrito']); ?></td>
                        <td class="tb_bord_baixo" align="left"><?php echo ($list['controle_movimento_negrito']); ?></td>
                        <td class="tb_bord_baixo" align="left"><?php echo ($list['valor_movimento_negrito']); ?></td>
                    </tr>
                    <?php
                    $cont++;
                }

                // verifica a paginação
                $paginacao = "";
                if ($pg > 0)
                    $paginacao .= "<a href='?page=" . ($pg - 1) . "' style='float:left' class='page_up'>" . ('Anterior') . "</a>";
                $paginacao .= "<a href='?page=" . ($pg + 1) . "' style='float:right'  class='page_down'>" . ('Pr&oacute;ximo') . "</a>";
            }
            // Nenhum registro foi encontrado
            else {
                ?>
                <table width="100%" cellpadding="5" cellspacing="2">
                    <tr onselect="" class="cabecalho">
                        <td width="70%" class="cabecalho_negrito"><?php echo ($conf['listar']); ?></td>
                    </tr>
                    <?php
                    // verifica a paginação
                    $paginacao = "";
                    if ($pg > 0)
                        $paginacao .= "<a href='?page=" . ($pg - 1) . "' style='float:left' class='page_up'>" . ('Anterior') . "</a>";
                }
            }
            else {
                ?>
                <table width="100%" cellpadding="5" cellspacing="2">
                    <tr onselect="" class="cabecalho">
                        <td width="70%" class="cabecalho_negrito"><?php echo ($falha['listar']); ?></td>
                    </tr>
                    <?php
            }

            // Encerra a tabela e coloca a paginação
            echo "</table>";
            if ($paginacao != "")
                echo $paginacao;
        }

    /**
     * método: make_list
     * propósito: faz a listagem
     */
    function make_list_balancete($post) {

        //	if ($ordem == "") $ordem = " ORDER BY MOVIM.data_movimento DESC";
        // variáveis globais
        global $form, $conf, $db, $falha, $smarty, $saldo;
        //---------------------

        if (!empty($post['data_baixa1']) && !empty($post['data_baixa2'])) {

            $filtro_data = " AND (movimento.`data_baixa` BETWEEN '" . $post['data_baixa1'] . " 00:00:00' AND  '" . $post['data_baixa2'] . " 23:59:59') ";
        }

        $idcliente = (int) $post['idcliente'];

        $list_sql = "# Movimentos de entrada
			(
				
				SELECT
					'receitas' as tipo,
					CASE CHAR_LENGTH(plano.`numero`)
					WHEN 8 THEN 3
					WHEN 5 THEN 2
					WHEN 1 THEN 1 END
					as nivel,
				    plano.`idplano` AS plano_idplano,
				    plano_pai.`numero` AS planopai_numero,
					plano_pai.`nome` AS planopai_nome,
				    plano_avo.`numero` AS planoavo_numero,
					plano_avo.`nome` AS planoavo_nome,
				    plano.`numero` AS plano_numero,
				    plano.`nome` AS plano_nome,
				    #plano.`tipo` AS plano_tipo,
				    plano.`descricao` AS plano_descricao,
				    #movimento.`idfilial` AS movimento_idfilial,
				    sum( movimento.`valor_movimento`) AS movimento_valor_movimento 
				    #movimento.`data_cadastro` AS movimento_data_cadastro,
				    #movimento.`data_movimento` AS movimento_data_movimento,
				    #movimento.`data_baixa` AS movimento_data_baixa,
				    #movimento.`baixado` AS movimento_baixado,
				    #movimento.`data_vencimento` AS movimento_data_vencimento
				FROM
					`cliente` cliente LEFT JOIN 
					`movimento` movimento ON cliente.`idcliente` = movimento.`idcliente_destino` LEFT JOIN
				    `plano` plano ON movimento.`idplano_credito` = plano.`idplano` LEFT JOIN
					`plano` plano_pai ON plano.`idpai` = plano_pai.`idplano` LEFT JOIN
					`plano` plano_avo ON plano_pai.`idpai` = plano_avo.`idplano`
				WHERE	
					movimento.`baixado` = 1 AND
					cliente.`idcliente` = $idcliente AND
					movimento.`idfilial`  = " . $_SESSION['idfilial_usuario'] . "
					$filtro_data
				GROUP BY
					plano_idplano, plano_numero, plano_nome, plano_descricao, planopai_numero, planoavo_numero, planopai_nome, planoavo_nome
					
			)

			UNION

			# Movimentos de saída
			(
				SELECT
					'despesas' as tipo,
					CASE CHAR_LENGTH(plano.`numero`)
					WHEN 8 THEN 3
					WHEN 5 THEN 2
					WHEN 1 THEN 1 END
					as nivel,
				    plano.`idplano` AS plano_idplano,
				    plano_pai.`numero` AS planopai_numero,
					plano_pai.`nome` AS planopai_nome,
				    plano_avo.`numero` AS planoavo_numero,
					plano_avo.`nome` AS planoavo_nome,
				    plano.`numero` AS plano_numero,
				    plano.`nome` AS plano_nome,
				    #plano.`tipo` AS plano_tipo,
				    plano.`descricao` AS plano_descricao,
				    #movimento.`idfilial` AS movimento_idfilial,
				    sum( movimento.`valor_movimento`) AS movimento_valor_movimento 
				    #movimento.`data_cadastro` AS movimento_data_cadastro,
				    #movimento.`data_movimento` AS movimento_data_movimento,
				    #movimento.`data_baixa` AS movimento_data_baixa,
				    #movimento.`baixado` AS movimento_baixado,
				    #movimento.`data_vencimento` AS movimento_data_vencimento
				FROM
					`cliente` cliente LEFT JOIN 
					`movimento` movimento ON cliente.`idcliente` = movimento.`idcliente_origem` LEFT JOIN
					`plano` plano ON movimento.`idplano_debito` = plano.`idplano` LEFT JOIN
					`plano` plano_pai ON plano.`idpai` = plano_pai.`idplano` LEFT JOIN
					`plano` plano_avo ON plano_pai.`idpai` = plano_avo.`idplano`
				WHERE	
					movimento.`baixado` = 1 AND
					cliente.`idcliente` = $idcliente AND
					movimento.`idfilial`  = " . $_SESSION['idfilial_usuario'] . "
					$filtro_data
				GROUP BY
					plano_idplano, plano_numero, plano_nome, plano_descricao, planopai_numero, planoavo_numero, planopai_nome, planoavo_nome
			
			)

			";

        if ($_GET['target'] == 'full')
            $rppg = 99999;
        elseif ($rppg == '')
            $rppg = $conf['rppg'];

        //Retira os pontos do número do plano e os deixa com 6 dígitos para fazer a comparação dos filtros
        if ($post['plano'] != null) {
            $post['plano'] = str_replace('.', '', $post['plano']);
            $post['plano']['inicio'] = str_pad($post['plano']['inicio'], 6, '0');
            $post['plano']['fim'] = str_pad($post['plano']['fim'], 6, '0');
        }

        //manda fazer a paginação
        $list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

        if ($list_q) {

            //Se não houver filtro de planos, calcula o saldo anterior				
            if (!$post['plano']) {

                //Recupera o saldo inicial com base na data anterior do primeiro registro
                $dataAnterior = $form->Altera_Data($post['data_baixa1'], '-1 day');
                $saldoInicial = $saldo->getById($idcliente, $dataAnterior);

                $list_return['receitas'] = floatval($saldoInicial['saldo']);
            }
            //busca os registros no banco de dados e monta o vetor de retorno
            $cont = 0;
            while ($list = $db->fetch_array($list_q)) {
                //Caso o plano não esteja no intervalo selecionado, passa para o próximo registro
                $plano_numero = str_pad(str_replace('.', '', $list['plano_numero']), 6, 0);
                if ($post['plano'] != null && !($plano_numero >= $post['plano']['inicio'] && $plano_numero <= $post['plano']['fim'] )) {

                    continue;
                }//--------------------
                //insere um índice na listagem
                $list['index'] = $cont + 1 + ($pg * $rppg);

                if ($list['planoavo_numero']) {

                    $avo = $list['planoavo_numero'];
                    $pai = $list['planopai_numero'];
                    $plano = $list['plano_numero'];

                    //Atribui os dados de nível 3
                    $list['valor_saida'] = $form->FormataMoedaParaExibir($list['movimento_valor_movimento']);
                    $list_return[$avo]['filhos'][$pai]['filhos'][$plano] = $list;
                    // ---------------------------------------------------
                    //Atribui os dados de nível 2 
                    if (!$list_return[$avo]['filhos'][$pai]['dados']) {
                        $list_return[$avo]['filhos'][$pai]['dados'] = array('plano_numero' => $list['planopai_numero'],
                            'plano_nome' => $list['planopai_nome'],
                            'tipo' => $list['tipo'],
                            'somatorio' => 0.00);
                    }

                    $list_return[$avo]['filhos'][$pai]['dados']['somatorio'] += $list['movimento_valor_movimento'];

                    $list_return[$avo]['filhos'][$pai]['dados']['somatorio_saida'] =
                            $form->FormataMoedaParaExibir($list_return[$avo]['filhos'][$pai]['dados']['somatorio']);
                    // ---------------------------------------------------
                    //Atribui os dados de nível 1 
                    if (!$list_return[$avo]['dados']) {
                        $list_return[$avo]['dados'] = array('plano_numero' => $list['planoavo_numero'],
                            'plano_nome' => $list['planoavo_nome'],
                            'tipo' => $list['tipo'],
                            'somatorio' => 0.00);
                    }
                    $list_return[$avo]['dados']['somatorio'] += $list['movimento_valor_movimento'];
                    $list_return[$avo]['dados']['somatorio_saida'] = $form->FormataMoedaParaExibir($list_return[$avo]['dados']['somatorio']);
                    // ---------------------------------------------------


                    if ($list['tipo'] == 'despesas')
                        $list_return['despesas'] += $list['movimento_valor_movimento'];
                    elseif ($list['tipo'] == 'receitas')
                        $list_return['receitas'] += $list['movimento_valor_movimento'];
                }

                $cont++;
            }

            //Formata modedas para exibir fora do loop
            $list_return['somatorio']['saldo_inicial'] = $form->FormataMoedaParaExibir($saldoInicial['saldo']);
            $list_return['somatorio']['saldo_final'] = $form->FormataMoedaParaExibir($list_return['receitas'] - $list_return['despesas']);
            $list_return['somatorio']['despesas'] = $form->FormataMoedaParaExibir($list_return['despesas']);
            $list_return['somatorio']['receitas'] = $form->FormataMoedaParaExibir($list_return['receitas'] - $saldoInicial['saldo']);

            return $list_return;
        }
        else {

            $this->err = $falha['listar'];
            return(0);
        }
    }

                /*
                 * Propósito: Faz a consulta e o tratamento dos dados para o relatório de Caixa e Razonete
                 * 			  
                 *
                 * Parâmetros: 
                 * 			   int $idcliente
                 * 					date $dataInicio
                 * 					date $dataFim
                 * 					bool $razonete - Define se a função está sendo usada para gerar relatório de Razonete		
                 * 			   array $planos  - Filtro para intervalo de plano de contas (usar $planos['inicio'] e $planos['fim']
                 * 									
                 * 									
                 *
                 * Retorno: array $list
                 *
                 */

                function make_list_caixa($idcliente, $dataInicio, $dataFim, $razonete = false, $planos = null) {

                    global $conf, $db, $form, $saldo, $plano;
                    $saldoAtual = 0.00;

                    //Ordena por plano de conta no relatório de Razonete		
                    $orderPlanos = $razonete ? 'plano_geral ASC, MOV.data_baixa ASC ' : 'MOV.data_baixa ASC';


                    $sql_q = "SELECT	MOV.idmovimento,
							SUBSTR(MOV.data_baixa,1,10) as data_baixa, 
							MOV.data_vencimento,							
							MOV.descricao_movimento,  
							IF(MOV.idcliente_origem = $idcliente,'debito','credito') as tipo,
							IF(MOV.idcliente_origem = $idcliente,PLA_DEBT.numero,PLA_CRED.numero) as plano_geral,
							MOV.valor_movimento,
                            MOV.tipo_baixa,
							APT.apto,
              			ORIG.nome_cliente as origem,
							DEST.nome_cliente as destino,
							
							PLA_DEBT.numero as numero_debt,
							PLA_DEBT.nome as nome_debt,
							
							PLA_CRED.numero as numero_cred,
							PLA_CRED.nome as nome_cred
						
						FROM  
								
							`movimento` MOV
							LEFT JOIN cliente ORIG ON (MOV.idcliente_origem = ORIG.idcliente)
							LEFT JOIN cliente DEST ON (MOV.idcliente_destino = DEST.idcliente)
							
							LEFT JOIN plano PLA_DEBT ON (MOV.idplano_debito = PLA_DEBT.idplano)
							LEFT JOIN plano PLA_CRED ON (MOV.idplano_credito = PLA_CRED.idplano) 
							
							LEFT JOIN apartamento APT USING(idapartamento)
							
						WHERE  
								
							( MOV.idcliente_origem = $idcliente OR MOV.idcliente_destino = $idcliente ) AND
							( MOV.data_baixa BETWEEN '$dataInicio 00:00:00' AND '$dataFim 23:59:59' ) AND 
							MOV.baixado = '1'

							$filtroPlano							

						ORDER BY $orderPlanos  ";

                    if ($sql_rs = $db->query($sql_q)) {


                        //Busca o saldo anterior caso não haja filtro de planos
                        if ($planos == null) {

                            //Recupera a data anterior do período para buscar o saldo	
                            $dataAnterior = $form->Altera_Data($dataInicio, '-1 day');

                            //Busca o saldo anterior
                            $saldoInicial = $saldo->getById($idcliente, $dataAnterior);

                            //Inicializa saldo
                            if (!empty($saldoInicial['saldo']))
                                $saldoAtual = $saldoInicial['saldo'];
                        }
                        else { //Retira os pontos do número do plano e os deixa com 6 dígitos para fazer a comparação dos filtros
                            $planos = str_replace('.', '', $planos);
                            $planos['inicio'] = str_pad($planos['inicio'], 6, '0');
                            $planos['fim'] = str_pad($planos['fim'], 6, '0');
                        }




                        while ($linha = $db->fetch_array($sql_rs)) {


                            //Caso o plano não esteja no intervalo selecionado, passa para o próximo registro
                            $plano_geral = str_pad(str_replace('.', '', $linha['plano_geral']), 6, 0);
                            if ($planos != null && !($plano_geral >= $planos['inicio'] && $plano_geral <= $planos['fim'] )) {

                                continue;
                            }//--------------------
                            //Faz o somatório de crédito e débito		
                            if ($linha['tipo'] == 'credito') {
                                $NumPlano = $linha['numero_cred'];
                                $receitas += $linha['valor_movimento'];
                            } elseif ($linha['tipo'] == 'debito') {

                                $NumPlano = $linha['numero_debt'];
                                $despesas += $linha['valor_movimento'];
                            }

                            if ($razonete) { // Se for razonete, separa por plano de conta.
                                //Recupera os dados do plano pai
                                $PlanoExplode = explode('.', $NumPlano);
                                $PlanoPai = $plano->getByNumero($PlanoExplode[0] . '.' . $PlanoExplode[1]);
                                $idPai = $PlanoPai['idplano'];

                                //Inicializa a posição do array com o id e os dados do Plano "pai"
                                if (!isset($list[$idPai]['total'])) {
                                    $list[$idPai]['total'] = 0.00;
                                    $list[$idPai]['info_pai'] = $PlanoPai;
                                }


                                //Calcula os somatórios para cada plano
                                if (!isset($somatorio[$NumPlano]))
                                    $somatorio[$NumPlano] = 0.00;
                                $somatorio[$NumPlano] += $linha['valor_movimento'];
                                $list['somatorio']['planos'][$NumPlano] = $form->FormataMoedaParaExibir($somatorio[$NumPlano]);


                                //Formata os valores para exibir na tela						
                                $list[$idPai]['total_saida'] = $form->FormataMoedaParaExibir($list[$idPai]['total']);
                                $linha['data_baixa'] = $form->FormataDataParaExibir($linha['data_baixa']);

                                switch($linha['tipo_baixa']){
                                    case 'A':
                                        $linha['tipo_baixa'] = 'Automática';
                                    break;
                                    case 'M':
                                        $linha['tipo_baixa'] = 'Manual';
                                    break;
                                }
                                $linha['valor_movimento'] = $form->FormataMoedaParaExibir($linha['valor_movimento']);
                                $list[$idPai]['movimentos'][$NumPlano][] = $linha;

                            }
                            else {

                                //Calcula o saldo 
                                if ($linha['tipo'] == 'credito')
                                    $saldoAtual += $linha['valor_movimento'];
                                else
                                    $saldoAtual -= $linha['valor_movimento'];

                                //Formata a saída
                                $linha['saldo'] = $form->FormataMoedaParaExibir($saldoAtual);
                                $linha['data_baixa'] = $form->FormataDataParaExibir($linha['data_baixa']);
                                $linha['data_vencimento'] = $form->FormataDataParaExibir($linha['data_vencimento']);
                                $linha['valor_movimento'] = $form->FormataMoedaParaExibir($linha['valor_movimento']);

                                $list['registros'][] = $linha;
                            }
                        }

                        //Formata o somatório para exibir
                        $list['somatorio']['saldo_inicial'] = $form->FormataMoedaParaExibir($saldoInicial['saldo']);
                        $list['somatorio']['despesas'] = $form->FormataMoedaParaExibir($despesas);
                        $list['somatorio']['receitas'] = $form->FormataMoedaParaExibir($receitas);
                        $list['somatorio']['saldo_final'] = $form->FormataMoedaParaExibir(($receitas - $despesas) + $saldoInicial['saldo']);


                        //Se houver saldo anterior, insere na primeira posição da lista
                        if ($saldoInicial['saldo']) {

                            $saldoAnterior['saldo'] = $form->FormataMoedaParaExibir($saldoInicial['saldo']);
                            $saldoAnterior['data_baixa'] = $form->FormataDataParaExibir($saldoInicial['data']);
                            $saldoAnterior['descricao_movimento'] = 'Saldo Anterior';

                            array_unshift($list['registros'], $saldoAnterior);
                        }

                        return $list;
                    } else {

                        $this->err = $falha['listar'];
                        return false;
                    }
                }

                /*
                 * Propósito: Pega os campos enviados do formulário para montar a cláusula WHERE
                 * 			  da consulta em forma de string
                 *
                 * Parâmetros: 
                 * 			   array $post - Dados do post enviado com os campos de filtro
                 *
                 * 			   bool $parametros_get - Define se deverá ser retornado os paramtros
                 * 									  em formato GET para ser utilizado na função
                 * 									  make_list
                 *
                 * Retorno: $filtro
                 *
                 */

                function cria_filtro_movimento($post, $parametros_get = false) {

                    global $conf, $db, $form;

                    $filtro = 'WHERE FLI.idfilial = ' . $_SESSION['idfilial_usuario'] . ' ';


                    if (!empty($post['data_vencimento_de']) && !empty($post['data_vencimento_de'])) {

                        $data_vencimento_de = $form->FormataDataParaInserir($post['data_vencimento_de']);
                        $data_vencimento_ate = $form->FormataDataParaInserir($post['data_vencimento_ate']);

                        $filtro .=" AND ( data_vencimento BETWEEN '$data_vencimento_de 00:00:00' AND '$data_vencimento_ate 23:59:59' )";


                        if ($parametros_get) {
                            $parametroBusca = '&data_vencimento_de=' . $post['data_vencimento_de'];
                            $parametroBusca .= '&data_vencimento_ate=' . $post['data_vencimento_ate'];
                        }
                    }


                    if (!empty($post['data_movimento_de']) && !empty($post['data_movimento_de'])) {

                        $data_movimento_de = $form->FormataDataParaInserir($post['data_movimento_de']);
                        $data_movimento_ate = $form->FormataDataParaInserir($post['data_movimento_ate']);

                        $filtro .=" AND ( data_movimento BETWEEN '$data_movimento_de 00:00:00' AND '$data_movimento_ate 23:59:59' ) ";

                        if ($parametros_get) {
                            $parametroBusca .= '&data_movimento_de=' . $post['data_movimento_de'];
                            $parametroBusca .= '&data_movimento_ate=' . $post['data_movimento_ate'];
                        }
                    }



                    if (!empty($post['data_baixa_de']) && !empty($post['data_baixa_de'])) {

                        $data_baixa_de = $form->FormataDataParaInserir($post['data_baixa_de']);
                        $data_baixa_ate = $form->FormataDataParaInserir($post['data_baixa_ate']);

                        $filtro .=" AND ( data_baixa BETWEEN '$data_baixa_de 00:00:00' AND '$data_baixa_ate 23:59:59' ) ";

                        if ($parametros_get) {
                            $parametroBusca .= '&data_baixa_de=' . $post['data_baixa_de'];
                            $parametroBusca .= '&data_baixa_ate=' . $post['data_baixa_ate'];
                        }
                    }


                    if (!empty($post['idcliente'])) {

                        $idcliente = (int) $post['idcliente'];
                        $filtro .=" AND ( idcliente_origem = $idcliente OR idcliente_destino = $idcliente ) ";

                        if ($parametros_get)
                            $parametroBusca .= "&idcliente=$idcliente&idcliente_Nome=" . $post['idcliente_Nome'];
                    }

                    if (!empty($post['idcondominio'])) {

                        $idcondominio = (int) $post['idcondominio'];
                        $filtro .=" AND ( APT.idcliente = $idcondominio ) ";

                        if ($parametros_get)
                            $parametroBusca .= "&idcondominio=$idcondominio";
                    }


                    if (!empty($post['rppg'])) {
                        if ($parametros_get)
                            $parametroBusca .= "&rppg=" . $post['rppg'];
                        $rppg = $post['rppg'];
                    }

                    if ($post['baixado'] != "") {

                        if ($parametros_get)
                            $parametroBusca .= "&baixado=" . $post['baixado'];

                        //Pode haver movimentos não-baixados com o campo baixado igual a NULL ou igual a '0'
                        $filtro .= $post['baixado'] == '0' ? " AND baixado <> '1' " : " AND baixado = '1' ";
                    }


                    if ($post['negociacao'] != "") {

                        if ($parametros_get)
                            $parametroBusca .= "&negociacao=" . $post['negociacao'];

                        //Pode haver movimentos não negociados com o campo negociado igual a NULL ou igual a '0'
                        $filtro .= $post['negociacao'] == '0' ? " AND (negociacao = '0' || negociacao is NULL) " : " AND negociacao = '1' ";
                    }

                    // Caso seja listagem para impressão de boletos, filtra as movimentações
                    if (isset($_GET['boletos']) && $_GET['boletos'] == '1') {
                        if ($parametros_get)
                            $parametroBusca .= "&boletos=1";
                        $filtro .= ' AND gerar_fatura = 1';
                    }

                    //Filtra somente o que é taxas de condompinio
                    if (isset($_GET['taxa_condominio']) && $_GET['taxa_condominio'] == '1') {
                        if ($parametros_get)
                            $parametroBusca .= "&taxa_condominio=1";
                        $filtro .= ' AND MOVIM.idapartamento IS NOT NULL';
                    }


                    $busca['filtro'] = $filtro;
                    if ($parametros_get)
                        $busca['parametros_get'] = $parametroBusca;


                    return $busca;
                }

                
	/**
	 * Cria relatório de condomínio
	 * Utilizado para inclusão no boleto de condomínio
	 * @param integer $idcliente - ID do condomínio
	 * @param string $dataInicio - Data de início da geração do relatório
	 * @param string $dataFim - Data final do período para geração do relatório
	 */
	function criaRelatorioCondominio($idcliente, $dataInicio, $dataFim) {
		
		global $db, $form, $saldo;
		
		$relatorio = $this->make_list_demonstrativo($idcliente, $dataInicio, $dataFim);

		// Pega a última mensagem cadastrada para o demonstrativo
		$relatorio['mensagem'] = $this->getLast_msg_demonstrativo($idcliente);
		
		return $relatorio;
		
	}                

    /**
     * Cria relatório de caixa proprietário de um cliente.
     * Busca todos os caixas-proprietários e retorna uma string contendo todos
     * os relatórios associados
     * @param integer $idcliente - ID do cliente ao qual os caixas estão associados
     * @param date $dataInicio - Data inicial do período no formato aaaa-mm-dd
     * @param date $dataFim - Data final do período no formato aaaa-mm-dd     
     * @return string - Retorna uma string com todos os relatórios encontrados
     */
    function criaRelatorioCaixaProprietario($idcliente, $dataInicio, $dataFim){

        global $db, $form, $cliente_condominio;

        if(!$cliente_condominio){

            require_once dirname(__FILE__) . '/cliente_condominio.php';
            $cliente_condominio = new cliente_condominio();
        }

        /// array contendo relatórios dos caixas
        $relatorio_caixa = array();

        /// busca todos os caixas-proprietários associados ao cliente
        $caixas_proprietarios = $cliente_condominio->buscaCaixaProprietario($idcliente);

        /// gera relatório de cada caixa-proprietário encontrado
        if($caixas_proprietarios && is_array($caixas_proprietarios)){
        	
        	$contador = 0;
        	
            while(key($caixas_proprietarios) !== null){

                $dados_caixa = current($caixas_proprietarios);

                $relatorio_caixa[$contador] = $this->make_list_demonstrativo($dados_caixa['idcliente'],$dataInicio,$dataFim);
                
                // Pega a última mensagem cadastrada para o demonstrativo
                $mensagem = $this->getLast_msg_demonstrativo($id_caixa);

                $relatorio_caixa[$contador]['mensagem'] = $mensagem;

                $relatorio_caixa[$contador]['nome_cliente'] = $dados_caixa['nome_cliente'];

                $contador++;
                
                next($caixas_proprietarios);
            }
        }

        return $relatorio_caixa;

    }


	function make_list_demonstrativo($idcliente, $dataInicio, $dataFim) {

		global $db, $form, $saldo;
		
		if(!$saldo){
			require_once '../entidades/saldo.php';
			$saldo = new saldo();
		}
		
		$list = array();


		$sql_q = "SELECT	
					MOV.idmovimento,
					SUBSTR(MOV.data_baixa,1,10) as data_baixa, 
					MOV.descricao_movimento,
       				MOV.data_vencimento,  
					IF(MOV.idcliente_origem = $idcliente,'debito','credito') as tipo,
					MOV.valor_movimento,
					ORIG.nome_cliente as origem,
					DEST.nome_cliente as destino
					
				FROM 
					`movimento` MOV
					LEFT JOIN cliente ORIG ON (MOV.idcliente_origem = ORIG.idcliente)
					LEFT JOIN cliente DEST ON (MOV.idcliente_destino = DEST.idcliente)
					       	
			 	WHERE 
					( MOV.idcliente_origem = $idcliente OR MOV.idcliente_destino = $idcliente ) AND
					( MOV.data_baixa BETWEEN '$dataInicio 00:00:00' AND '$dataFim 23:59:59' ) AND 
					MOV.baixado = '1' ";

		$sql_rs = $db->query($sql_q);

		if ($sql_rs) {

        	//Recupera o saldo inicial com base na data anterior ao primeiro registro
            $dataAnterior = $form->Altera_Data($db->result($db->query($sql_q), 0, 'data_baixa'), '-1 day');

			//Para não buscar um saldo posterior ao período selecionado pelo usuário	  			 
            if ($dataAnterior == $dataInicio || $form->data1_maior($dataAnterior, $dataInicio)) {
            	$dataAnterior = $form->Altera_Data($dataInicio, '-1 day');
			}

            $saldoAnterior = $saldo->getById($idcliente, $dataAnterior);

            if(!$saldoAnterior) $saldoAnterior = $saldo->getById($idcliente);
                        
			$list['somatorio']['credito'] = 0.00;


            while ($linha = $db->fetch_array($sql_rs)) {

				// Faz o somatório de receitas e de despesas
                $tipo = $linha['tipo']; // Despesa ou Receita 

                if (!isset($list['somatorio'][$tipo]))
                	$list['somatorio'][$tipo] = 0.00;

				$list['somatorio'][$tipo] += $linha['valor_movimento'];

                $linha['valor_movimento'] = $form->FormataMoedaParaExibir($linha['valor_movimento']);
                $linha['data_baixa'] = $form->FormataDataParaExibir($linha['data_baixa']);
                $linha['data_vencimento'] = $form->FormataDataParaExibir($linha['data_vencimento']);

                $list[$tipo]['contas'][] = $linha;
			}

			//Calcula o saldo final
            $list['somatorio']['saldo_final'] = $saldoAnterior['saldo'] + ($list['somatorio']['credito'] - $list['somatorio']['debito']);

            //Formata valores do somatório para exibir ao usuário
            $list['somatorio']['saldo_final'] = $form->FormataMoedaParaExibir($list['somatorio']['saldo_final']);
            $list['somatorio']['saldo_anterior'] = $form->FormataMoedaParaExibir($saldoAnterior['saldo']);
            $list['somatorio']['debito'] = $form->FormataMoedaParaExibir($list['somatorio']['debito']);
            $list['somatorio']['credito'] = $form->FormataMoedaParaExibir($list['somatorio']['credito']);

            $list['em_aberto'] = $this->getCondominosEmDebito($idcliente, $dataFim);

		}
        elseif (!$sql_rs) {
        	$this->err = "Erro ao buscar os lançamentos. Por favor, entre me contato com  a F6 Sistemas.";
            return false;
		}

		return($list);
	}

    /**
     * Propósito: Busca apartamentos em débito de um Condomínio.
     *
     * Parâmetros: (int) $idcliente_condominio - Concomínio em questão     
     *             (date) $data - Data final do período, aceita os formatos AAAA-MM-DD e DD/MM/AAAA
     *
     * Retorno: (array) $list - Array com os registros dos apartamentos e o somatório das contas                  
     *                                                                   
     */
    function getCondominosEmDebito($idcliente_condominio, $data) {
        global $conf, $db, $form;

        //Se a data estiver no formato DD/MM/AAAA, converte para o formato AAAA-MM-DD
        if (strstr($data, "/"))
            $data = $form->FormataDataParaInserir($data);

        $sql = "  SELECT 
                    MOV.idmovimento, MOV.valor_movimento, MOV.data_vencimento,
                    APT.apto 
                FROM 
                    movimento MOV 
                    JOIN apartamento APT USING(idapartamento)
                    JOIN cliente_condominio COND ON (APT.idcliente = COND.idcliente)
                WHERE  
                    MOV.baixado <> '1' AND 
                    MOV.data_vencimento <= '$data' AND 
                    COND.idcliente = $idcliente_condominio
                    ORDER BY APT.apto ASC, MOV.data_vencimento DESC";
                ;

        if ($sql_q = $db->query($sql)) {

            $list = array('somatorio' => 0.00, 'registros' => array());

            while ($row = $db->fetch_array($sql_q)) {

                $list['somatorio'] += $row['valor_movimento'];

                $row['valor_movimento'] = $form->FormataMoedaParaExibir($row['valor_movimento']);
                $row['data_vencimento'] = $form->FormataDataParaExibir($row['data_vencimento']);

                $list['registros'][] = $row;
            }

            $list['somatorio'] = $form->FormataMoedaParaExibir($list['somatorio']);
            $list['qtd'] = count($list['registros']);

            return $list;
        } else {
            $this->err = $conf['listar'];
            return false;
        }
    }

    /**
     * Propósito - Grava uma mensagem para ser exibida no relatório de demonstrativo
     *
     * Parâmetros - (int) $idcondominio - O id do comdominio em questão
     *              (data) $data - A data em que a mensagem está sendo gravada
     *              (string) $mensagem - Mensagem a ser gravada          
     *
     *                                                                            */
    function salva_msg_demonstrativo($idcondominio, $data, $mensagem) {

        global $conf, $db;

        $sql_check = "SELECT idcondominio FROM demonstrativo_msg  WHERE idcondominio = $idcondominio AND data = '$data'";
        $sql_check_q = $db->query($sql_check);


        if ($db->num_rows($sql_check_q)) {
            $sql = "UPDATE demonstrativo_msg SET mensagem = '$mensagem' WHERE idcondominio = $idcondominio AND data = '$data'";
        } else {
            $sql = "INSERT INTO demonstrativo_msg(idcondominio, data, mensagem) VALUES ($idcondominio, '$data', '$mensagem')";
        }


        if ($db->query($sql)) {
            return true;
        } else {
            $this->err = $falha['inserir'];
            return false;
        }
    }

	/*
     * @description Pega a última mensagem salva no demonstrativo financeiro. Aceita datas no formato "Y-m-d" ou "d/m/Y"
     * 
     * @param $idcondominio - O id do condomínio em questão
     * @param $data - Opcional, busca pela data exata da mensagem se o terceiro parâmetro não for passado
     * @param $dataFim - Opcional. Busca mensagem entre a $data e $dataFim 
     * 
     */

	function getLast_msg_demonstrativo($idcondominio, $data = NULL, $dataFim = NULL) {

		global $conf, $db, $form;

		$filtroData = NULL;

		if (!empty($data)) {

			if (strstr($data, '/') !== false)
            	$data = $form->formataDataParaInserir($data);

			if (!empty($dataFim)) {

				if (strstr($dataFim, '/') !== false)
					$dataFim = $form->formataDataParaInserir($dataFim);

				$filtroData = " AND data BETWEEN '$data' AND '$dataFim' ";
			}
			else {
				$filtroData = " AND data = '$data' ";
			}
		}

		$sql = "SELECT mensagem FROM demonstrativo_msg  
            		WHERE idcondominio = $idcondominio
					$filtroData
                	ORDER BY data DESC
                	LIMIT 1 ";

		$sql_q = $db->query($sql);

		if ($db->num_rows($sql_q))
			$mensagem = $db->result($sql_q, 0, 'mensagem');
		else
			$mensagem = null;

		return $mensagem;
	}

	
	
    function list_msg_demonstrativo($idcondominio) {
        global $conf, $db, $form;

        $sql = "SELECT idcondominio, data, mensagem, SUBSTR(data,6,2) as mes FROM `demonstrativo_msg`   
                WHERE idcondominio = $idcondominio
                -- GROUP BY MES 
                ORDER BY data ASC
                LIMIT 10 ";

        $sql_q = $db->query($sql);

        if ($db->num_rows($sql_q)) {

            while ($list = $db->fetch_array($sql_q)) {
                $list['data'] = $form->formataDataParaExibir($list['data']);
                $mensagem[$list['mes']] = $list;
            }
        }
        else
            $mensagem = null;

        return $mensagem;
    }
                
                
	/**
     * Faz interpretação do conteúdo do arquivo de retorno passado como parâmetro
     * @param string $banco - Banco relacionado ao arquivo: CE (Caixa Econômica) ou BR (Bradesco)
     * @param integer $filial - ID da filial
     * @param array $conteudo_retorno - Array contendo o conteúdo do arquivo
     */
    public function interpretaRetorno($banco, $filial, $conteudo_retorno){
               
    	require_once dirname(dirname(__FILE__)) . '/common/lib/boletos/retorno_bancos.php';
                
        $retorno_bancos = new RetornoBancos($banco, $filial, $conteudo_retorno);
                
        if($retorno_bancos->interpretaConteudoRetorno()){
                
        	$contas_pagas = $retorno_bancos->obtemContasPagas();
                
            $retorno = array('erro' => false, 'contas_pagas' => $contas_pagas);
		}
        else{
                
        	$retorno = array('erro' => true);
		}
                
        return $retorno;
	}


    /**
     * Obtém resultado da análise de um arquivo de pré-crítica
     * @param $banco - Código do banco que será analisado. 
     *                  Disponíveis até o momento: CE - Caixa Econômica Feferal
     * @param string $conteudo_pre_critica - Conteúdo do arquivo fornecido no formulário
     */
    public function interpretaPreCritica($banco, $idfilial, $conteudo_pre_critica){

        global $conf;
        global $db;

        require_once dirname(dirname(__FILE__)) . '/common/lib/boletos/pre_critica_bancos.php';

        $pre_critica_bancos = new PreCriticaBancos($banco, $idfilial, $conteudo_pre_critica);

        if($pre_critica_bancos->interpretaConteudoPreCritica()){

            $resultado = $pre_critica_bancos->obtemResultado();

            /**
             * Monta um array com IDs dos movimentos encontrados para conferir dados.
             * Verifica se os movimentos com problema já não estão pagos, pois o movimento
             * pode já ter sido enviado em outra remessa e já ter sido pago
             **/
            $array_movimentos = array_keys($resultado);

            /// Se retornou pelo menos um movimento com erro, busca informações adicionais e
            /// verifica se já houve pagamento de algum movimento. Se já houve, retira da lista
            if((sizeof($array_movimentos) > 1) || ($array_movimentos[0] != 0)){

                /// Busca movimentos retornados da pré-crítica que já foram pagos
                $list_sql = 'SELECT idmovimento, idarquivo_remessa, descricao_movimento ' .
                            ' FROM movimento ' .
                            ' WHERE idmovimento IN (' . implode(',', $array_movimentos) . ')';

                $list_q = $db->query($list_sql);
        
                if ($list_q) {

                    /// índice para alternar cores das linhas na tela
                    $index = 1;

                    while ($row = $db->fetch_array($list_q)) {

                        if(($row['baixado'] == '1') || !$row['idarquivo_remessa']){

                            /// Se o movimento já foi pago, é retirado do array com resultado da pré-crítica
                            unset($resultado[$row['idmovimento']]);
                        }
                        else{

                            $resultado[$row['idmovimento']]['idarquivo_remessa'] = $row['idarquivo_remessa'];
                            $resultado[$row['idmovimento']]['descricao_movimento'] = $row['descricao_movimento'];
                            $resultado[$row['idmovimento']]['mensagem'] = implode('<br />', $resultado[$row['idmovimento']]['mensagem']);
                            $resultado[$row['idmovimento']]['index'] = $index;

                            $index++;
                        }
                    }

                    /// Percorre novamente o array com os dados dos movimentos, verificando se há
                    /// algum movimento que não existe no banco de dados. Se não houver, o retira do array
                    foreach ($resultado as $idmovimento => $dados) {
                        if(!isset($dados['index'])){
                            unset($resultado[$idmovimento]);
                        }
                    }

                    $pre_critica = array('erro' => false, 'resultado' => $resultado);
                }
                else{
                    /// se houver erro ao verificar os movimentos retorna erro para não 
                    /// correr o risco de gerar remessa de movimento que já foi pago
                    $pre_critica = array('erro' => true);
                }
            }
            else{
                /// Se não houver conferência a fazer retorna o resultado
                $pre_critica = array('erro' => false, 'resultado' => $resultado);
            }           
        }
        else{
            $pre_critica = array('erro' => true);
        }

        return $pre_critica;
    }

    /**
     * Desassocia arquivo de remessa dos movimentos cujos IDs são passados como parâmetro
     * @param array $movimentos_desassociar - Array contendo como valores os IDs dos movimentos
     * @return boolean - Retorna true em caso de sucesso e false em caso de erro
     */
    public function desassociaRemessa($movimentos_desassociar){

        global $conf;
        global $db;
        global $form;
        global $falha;

        if(is_array($movimentos_desassociar) && !empty($movimentos_desassociar)){

            $update_sql = 'UPDATE movimento ' .
                            'SET idarquivo_remessa = NULL ' .
                            'WHERE idmovimento IN (' . implode(',',$movimentos_desassociar) . ')';

            //envia a query para o banco
            $update_q = $db->query($update_sql);

            if ($update_q){
                return(1);
            }
            else{
                $this->err = $falha['alterar'];
            }
        }
    }


	/**
     * Verifica se as contas do array recebido existem no banco de dados
     * e estão associadas à filial passada como parâmetro
     * @param array $contas_pagas
     * @param integer $filial
    */
	public function validaContasPagas($contas_pagas, $filial){

	   	global $conf;
       	global $db;
       	global $form;

       	$ids_movimentos = array_keys($contas_pagas);

		$consulta = 
					' SELECT movimento.*, cliente_origem.nome_cliente AS nome_cliente_origem, cliente_destino.nome_cliente AS nome_cliente_destino ' .
					' FROM movimento ' .
					' LEFT JOIN cliente cliente_origem ON (movimento.idcliente_origem = cliente_origem.idcliente) ' .
					' LEFT JOIN cliente cliente_destino ON (movimento.idcliente_destino = cliente_destino.idcliente) ' .
					' WHERE idmovimento IN (' . implode(',',$ids_movimentos) . ')';

		$list_q = $db->query($consulta);

        if($list_q){

			$indice = 1;

            while($list = $db->fetch_array($list_q)){

            	$list['data_vencimento'] = $form->FormataDataParaExibir($list['data_vencimento']);

          		/// inclui informação de que conta existe no banco de dados
           		/// para facilitar exibição na tela
           		$list['conta_existe'] = true;
           		$list['numero'] = $indice;

           		$contas_pagas[$list['idmovimento']] += $list;

          		$indice++;
       		}
    	}

    	return $contas_pagas;                			
	}
	
	
	/**
	 * Registra pagamento das contas pagas passadas como parâmetro.
	 * @param array $contas_pagas - Array contendo como chave o ID da conta a receber e como
	 * 								valor um array contendo as informações da conta paga
     * @param string $tipo_baixa - Indica o tipo de baixa: A - automática, M - manual
	 */
	public function registraPagamento($contas_pagas, $tipo_baixa = 'A'){
	
		global $conf;
		global $db;
		global $form;

		$erros = false;

		/// percorre array de contas pagas e faz atualização
		foreach($contas_pagas as $id_conta => $dados){

			$dados_atualizacao = array(
                                    'numvalor_movimento' => $dados['valor_pago'],
									'litdata_baixa' => date('Y-m-d H:i:s'),
									'litbaixado' => '1',
                                    'littipo_baixa' => $tipo_baixa,
									'numtaxa_boleto' => $dados['tarifa_boleto'],
									'numvalor_juros' => $dados['juros'],
									'numvalor_multa' => $dados['multa'],
									'numdesconto' => $dados['desconto'] 	
			);

            /// Se o cliente pagou multa, juros ou teve desconto, o valor do movimento será alterado.
            /// Nesse caso registra no campo Observações o valor original.
            if(($dados['juros'] > 0.0) || ($dados['multa'] > 0.0) || ($dados['desconto'] > 0)){

                $dados_movimento = $this->getById($dados['idmovimento']);

                $observacao = 'Valor original: R$ ' . $dados_movimento['valor_movimento'] .
                                "\n\n" .
                                $dados_movimento['observacao'];

                $dados_atualizacao['litobservacao'] = $observacao;
            }

			if(!$this->update($id_conta, $dados_atualizacao)){
				$erros = true;
			}
		}
			
		if($erros){
			$this->err = array('Houve erro ao registrar pagamento de alguns movimentos.');
			return false;
		}
		else{
			return true;
		}	
	}


    /**
     * Busca clientes inadimplentes.
     * No momento busca apenas clientes avulsos que devem a SOS
     * @return $clientes_inadimplentes - Retorna um array com informações sobre os clientes inadimplentes
     */
    public function buscaClientesInadimplentes($dados_pesquisa){

        global $conf;
        global $db;
        global $form;

        require_once dirname(__FILE__) . '/parametros.php';

        $parametros = new parametros();

        /// ID do cliente da SOS, cuja conta recebe pagamento dos clientes
        $id_cliente_sos = $parametros->getParam('cliente_destino');

        /// array contendo campos para pesquisa
        $filtro = array("baixado != '1'", 'idcliente_destino = ' . $id_cliente_sos);

        /// verifica se foi selecionada pesquisa por cliente
        if($dados_pesquisa['idcliente']) {

            $filtro[] = 'idcliente_origem = ' . $dados_pesquisa['idcliente'];
        }

        /// verifica se foi selecionada data inicial para vencimento dos movimentos
        if($dados_pesquisa['data_vencimento_de']){

            $dados_pesquisa['data_vencimento_de'] = $form->FormataDataParaInserir($dados_pesquisa['data_vencimento_de']);
            $filtro[] = "data_vencimento >= '" . $dados_pesquisa['data_vencimento_de'] . "'";
        }

        /// verifica se foi selecionada data final para vencimento dos movimentos
        if($dados_pesquisa['data_vencimento_ate']){
            $dados_pesquisa['data_vencimento_ate'] = $form->FormataDataParaInserir($dados_pesquisa['data_vencimento_ate']);
            $filtro[] = "data_vencimento <= '" . $dados_pesquisa['data_vencimento_ate'] . "'";
        }

        /// se não foi selecionada data para pesquisa dos movimentos busca os movimentos
        /// que venceram até ontem
        if(!$dados_pesquisa['data_vencimento_de'] && !$dados_pesquisa['data_vencimento_ate']){

            /// data do dia anterior
            $data_ontem = $form->Altera_Data(date('Y-m-d'),"-1 day");

            $filtro[] = "data_vencimento <= '" . $data_ontem . "'";
        }


        $campos_busca = implode(' AND ', $filtro);

        /// array que será retornado com clientes inadimplentes        
        $clientes_inadimplentes = array();




        $consulta = 'SELECT cliente.idcliente, cliente.nome_cliente, descricao_movimento, ' .
                    'idmovimento, data_vencimento, valor_movimento ' .
                    'FROM movimento ' .
                    'LEFT JOIN cliente ON (movimento.idcliente_origem = cliente.idcliente) ' .
                    'WHERE ' . $campos_busca . 
                    'ORDER BY data_vencimento';

        $resultado = $db->query($consulta);

        if ($resultado) {

            /// valor total da dívida
            $total = 0;

            $contador = 0;

            while ($linha = $db->fetch_array($resultado)) {

                $linha['index'] = $contador;
                $linha['data_vencimento'] = $form->FormataDataParaExibir($linha['data_vencimento']);

                /// soma valor da dívida
                $total += $linha['valor_movimento'];

                $linha['valor_movimento'] = number_format($linha['valor_movimento'], 2, ',', '.' );

//                $linha['valor_movimento'] = $form->FormataMoedaParaExibir($linha['valor_movimento']);

                $clientes_inadimplentes[] = $linha;

                $contador++;
            }

            $total = number_format($total, 2, ',', '.' );

            $clientes_inadimplentes['total'] = $total;
        }

        return $clientes_inadimplentes;

    }
}

            // fim da classe
