<?php

 /**
  *     classe: form
  *  propósito: Gerencia a validação de formulários.
  */

class form {

    var $err;

   /**
    * construtor: form
    *  propósito:
    */
    
    function form() {
      $this->err = array();
    }
    

     //Formata uma data e hora providos da impressora fiscal para inserir (padrão "aaaa-mm-dd hh:ii:ss")
    function FormataDataHoraECFParaInserir($data, $hora) {
    
        
      $data2 = substr($data,4,2)."-".substr($data,2,2)."-".substr($data,0,2)." ".substr($hora,0,2).":".substr($hora,2,2).":".substr($hora,4,2);

      return($data2);
    }   



     //Formata uma data e hora para inserir (padrão "aaaa-mm-dd hh:ii:ss")
    function FormataDataHoraParaInserir($data) {
        $data1 = split("/",$data);
        
    	$data2 = $data1[2] . "-" . $data1[1] . "-" . $data1[0];
       
       return($data2);
    }  
    
    //Formata uma data para exibir (padrão "dd/mm/aaaa hh:ii:ss")
    function FormataDataHoraParaExibir($data) {
        $data1 = split("-",$data);
        $data3 = split(" ",$data1[2]);
        $data2 = $data3[0] . "/" . $data1[1] . "/" . $data1[0] . " " .$data3[1];

        return(trim($data2));
    } 
    
    function chk_empty($field, $req, $label, $line = 0) {
      $field = trim($field);
      if($req || strlen($field)) {
        if(strlen($field)) {
          return(1);
        }
        else {
          if($line){
            $this->err[] = "<b>Linha $line:</b> Voc&ecirc; deve informar $label!";
          }
          else{
            $this->err[] = "Voc&ecirc; deve informar $label!";
          }
          return(0);
        }
      }
      else {
        return(1);
      }
    }
    

		// Verifica se o valor informado é um número.
    function chk_moeda($field_original, $aceita_zero=1, $label="") {

			$field = $this->FormataMoedaParaInserir($field_original);

			if ($aceita_zero == 1) {
				if ($field_original == "") {
 					return(1);
				}
				else if ($field < 0) {
					if ($label != "") $this->err[] = "O campo $label tem que ser maior ou igual a 0 ou está com o formato incorreto!";
					return(0);
				}
				else if (!(ereg("^[0-9]+[.,][0-9]{2}$", $field_original))) {
					if ($label != "") $this->err[] = "O campo $label tem que ser maior ou igual a 0 ou está com o formato incorreto!";
					return(0);
        }
			}
			else {
				if ($field <= 0) { 
					if ($label != "") $this->err[] = "O campo $label tem que ser maior do que 0 ou está com o formato incorreto!";
 					return(0);
				}
				else if (!(ereg("^[0-9]+[.,][0-9]{2}$", $field)) && ($field != "") ) {
					if ($label != "") $this->err[] = "O campo $label tem que ser maior do que 0 ou está com o formato incorreto!";
					return(0);
				}
			}

 			return(1);
    }



    function chk_valor($field, $req, $line = 0) {
      $field = trim($field);

      if($req || strlen($field)) {
        //if(ereg("^[0-9]+[.,][0-9]{2}$", $field)) {
        if(ereg("^-?[0-9]+[.,][0-9]{2}$", $field)) { //Aceita valor negativo (sinal "-")
          return(1);
        }
        elseif(!strlen($field)) {
          $this->err[] = "Voc&ecirc; deve informar um valor v&aacute;lido!";
        }
        else {
          if($line) {
            $this->err[] = "<b>Linha $line:</b> O valor informado n&atilde;o &eacute; v&aacute;lido!";
          }
          else{
            $this->err[] = "O valor informado n&atilde;o &eacute; v&aacute;lido!";
          }
          return(0);
        }
      }
      else {
        return(1);
      }
    }

    function chk_select($field, $req, $label) {
      $field = trim($field);

      if($req || strlen($field)) {
        if(strlen($field)) {
          return(1);
        }
        else {
          $this->err[] = "Voc&ecirc; deve selecionar $label!";
          return(0);
        }
      }
      else {
        return(1);
      }
    }
    
    function chk_mail($field, $req) {
      $field = trim($field);
      
      if($req || strlen($field)) {
        if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $field)) {
          return(1);
        }
        elseif(!strlen($field)) {
          $this->err[] = "Voc&ecirc; deve informar um e-mail v&aacute;lido!";
          return(0);
        }
        else{
          $this->err[] = "O e-mail informado n&atilde;o &eacute; v&aacute;lido!";
          return(0);
        }
      }
      else {
        return(1);
      }
    }

    function chk_url($field, $req) {
			/*      
			$field = trim($field);

      if($req || strlen($field)) {
        if(eregi("", $field)) {
          return(1);
        }
        elseif(empty($field)) {
          $this->err[] = "Voc&ecirc; deve informar um web-site v&aacute;lido!";
        }
        else{
          $this->err[] = "O web-site informado n&atilde;o &eacute; v&aacute;lido!";
          return(0);
        }
      }
      else {
        return(1);
      }*/
      
      return(1);
    }

    function chk_cpf($field, $req) {

      $field = ereg_replace("[^[:alnum:]]", "", $field);
      
      if($req || strlen($field)) {

        if(!strlen($field)) {
          $this->err[] = "Voc&ecirc; deve informar um CPF v&aacute;lido!";
          return(0);
        }
        elseif(strlen($field) != 11) {
          $this->err[] = "O CPF informado n&atilde;o &eacute; v&aacute;lido!";
          return(0);
        }
        else {
          $sum = 0;
          for($i = 0; $i < 10; $i++) {
            if($field[$i] == $field [$i + 1]) {
              $sum++;
            }
          }
          if($sum == 10) {
            $this->err[] = "O CPF informado n&atilde;o &eacute; v&aacute;lido!";
            return(0);
          }
          else {

            $g = strlen($field) - 2;
            if($this->test_digit($field, "CPF", $g)) {
              $g = strlen($field) - 1;
              if($this->test_digit($field, "CPF", $g)) {
                return(1);
              }
              else {
                $this->err[] = "O CPF informado n&atilde;o &eacute; v&aacute;lido!";
                return(0);
              }
            }
            else {
              $this->err[] = "O CPF informado n&atilde;o &eacute; v&aacute;lido!";
              return(0);
            }

          }
        }
      }
      else {
        return(1);
      }
    }
    
    function chk_cnpj($field, $req) {
      $field = ereg_replace("[^[:alnum:]]", "", $field);

      if($req || strlen($field)) {

        if(!strlen($field)) {
          $this->err[] = "Voc&ecirc; deve informar um CNPJ v&aacute;lido!";
          return(0);
        }
        elseif(strlen($field) != 14) {
          $this->err[] = "O CNPJ informado n&atilde;o &eacute; v&aacute;lido!";
          return(0);
        }
        else {

            $g = strlen($field) - 2;
            if($this->test_digit($field, "CNPJ", $g)) {
              $g = strlen($field) - 1;
              if($this->test_digit($field, "CNPJ", $g)) {
                return(1);
              }
              else {
                $this->err[] = "O CNPJ informado n&atilde;o &eacute; v&aacute;lido!";
                return(0);
              }
            }
            else {
              $this->err[] = "O CNPJ informado n&atilde;o &eacute; v&aacute;lido!";
              return(0);
            }

        }
      }
      else {
        return(1);
      }
    }

    function test_digit($num, $type, $g) {
      $dig = 0;
      $ind = 2;

      for($f = $g; $f > 0; $f--) {
        $dig += $num[$f-1] * $ind;
        if(strtoupper($type) == "CNPJ") {
          if($ind > 8) {
            $ind = 2;
          }
          else {
            $ind++;
          }
        }
        else {
          $ind++;
        }
      }

      $dig %= 11;

      if($dig < 2) {
        $dig = 0;
      }
      else {
        $dig = 11 - $dig;
      }

			/*
      if($dig != intval($num[$g])) {
        return(0);
      }
      else {
        return(1);
      }
      */
      if("$dig" != $num[$g]) {
        return(0);
      }
      else {
        return(1);
      }
    }

    function chk_cep($field, $req) {
      $field = ereg_replace("[^[:alnum:]]", "", $field);

      if($req || strlen($field)) {
        if(ereg("^[0-9]{8}$", $field)) {
          return(1);
        }
        elseif(!strlen($field)) {
          $this->err[] = "Voc&ecirc; deve informar um CEP v&aacute;lido!";
        }
        else{
          $this->err[] = "O CEP informado n&atilde;o &eacute; v&aacute;lido!";
          return(0);
        }
      }
      else {
        return(1);
      }
    }

    function chk_tel($ddd, $num, $req, $type='') {
      $field = trim($ddd) . trim($num);
      $field = ereg_replace("[^[:alnum:]]", "", $field);

      if($req || strlen($field)) {
        $tel = ereg_replace("^([0-9]{2})([0-9]{7,8})$", "\\1-\\2", $field);

        if(eregi("^[0-9]{2}-[0-9]{7,8}$", $tel)) {
          return(1);
        }
        elseif(!strlen($field)) {
          $this->err[] = "Voc&ecirc; deve informar um telefone $type v&aacute;lido!";
          return(0);
        }
        else{
          $this->err[] = "O telefone $type informado n&atilde;o &eacute; v&aacute;lido!";
          return(0);
        }
      }
      else {
        return(1);
      }
    }
    
    function chk_senha($senha1, $senha2) {
      if($senha1 != $senha2) {
          $this->err[] = "As senhas digitadas n&atilde;o conferem. Por favor, digite novamente.";
          return(0);
        }
        else {
          return(1);
        }
    }
    
    //Formata uma data para inserir (padrão "aaaa-mm-dd")
    function FormataDataParaInserir($data) {
        $data1 = split("/",$data);
        $data2 = $data1[2] . "-" . $data1[1] . "-" . $data1[0];
       
        return($data2);
    }  
    
	//Formata uma data para exibir (padrão "dd/mm/aaaa")
	function FormataDataParaExibir($data) {
		$data1 = split("-",$data);
		$data2 = $data1[2] . "/" . $data1[1] . "/" . $data1[0];

		return($data2);
	}


    //Formata Moeda para inserir: De 10,00 para 10.00
    function FormataMoedaParaInserir($valor) {
  		$valor = str_replace(".","",$valor);
		$valor = str_replace(",",".",$valor);	      
        return($valor);
    }  
    
    //Formata Moeda para exibir
    function FormataMoedaParaExibir($valor) {
        $valor = number_format($valor,2,",","");
        return($valor);
    }


    function FormataMoedaParaExibirPontuacao($valor) {
        $valor = number_format($valor,2,",",".");
        return($valor);
    }


	function UltimoDiaMes($mes, $ano){
		return idate('d', mktime(0, 0, 0, ($mes + 1), 0, $ano));
	}
  

    // funcao que valida data no formato dd/mm/aaaa
    function chk_IsDate($data, $label, $line = 0) {
        $dataArray = explode("/",$data);
        $valida = checkdate($dataArray[1],$dataArray[0],$dataArray[2]);

        if ( (!$valida) || (strlen($data)<10) ) {
            if($line){
                $this->err[] = "<b>Linha $line:</b> " . $label . " n&atilde;o &eacute; v&aacute;lida!";
            }
            else{
                $this->err[] = $label . " n&atilde;o &eacute; v&aacute;lida!";
            }
            return(0);
        }
        else {
            return(1);
        }  
    }
  
    //Esta função serve para validar horas
    function chk_IsHour($hora) {
        $hora1 = explode(":",$hora);
        $h = intval($hora1[0]);
        $m = intval($hora1[1]);
        $s = intval($hora1[2]);
    
        if (($h >= 24) || ($h < 0) || ($m >= 60) || ($m < 0) || ($s >= 60) || ($s < 0)) {
            $this->err[] = "A hora n&atilde;o &eacute; v&aacute;lida!";
            return(0);
        }
        else {
            return(1);
        }
    }
  
	/*
		Se Data Inicial é maior que a Data Final a função retorna "true", senão retorna "false".
		Obs: independe se a data está no formato AAAA-MM-DD ou DD/MM/AAAA.
	*/
	function data1_maior($dt_inicial, $dt_final){
	
		//Se a data estiver no formato AAAA-MM-DD, converte para o formato DD/MM/AAAA
		if(strstr($dt_inicial, "-")) $dt_inicial = $this->FormataDataParaExibir($dt_inicial);
		if(strstr($dt_final, "-")) $dt_final = $this->FormataDataParaExibir($dt_final);
		
		//Separa o dia, mes e ano em variáveis
		list($dia_i, $mes_i, $ano_i) = explode("/", $dt_inicial); //Data inicial
		list($dia_f, $mes_f, $ano_f) = explode("/", $dt_final); //Data final
		
		// Obtem tempo unix no formato timestamp
		$mk_i = mktime(0, 0, 0, $mes_i, $dia_i, $ano_i); 
		$mk_f = mktime(0, 0, 0, $mes_f, $dia_f, $ano_f); 
		
		//Acha a diferença entre as datas
		$diferenca = $mk_f - $mk_i; 
		
		//Faz o retorno da função
		return ($diferenca < 0) ? true : false ;
	    
	} 
  
  
  
    /*
    Realiza paginação
    $sql = query básica(sem os limites)
    $pg = página corrente
    $rppg = resultados por página
    $url_param recebe os parâmetros da URL(ex.:ac=listar, id=1)
    retorna a query já com os limites para ser aproveitado na rotina principal
    */
    function paginacao_completa($sql, $pg, $rppg, $url = "" ){

      	global $db;
      	global $smarty;
      	global $err;

		$sql_q = $db->query($sql);

		if(!$sql_q){
			$err[] = $falha['bd'];
		}
		else{
			$total_registros = $db->num_rows($sql_q);
			$num_pags = ceil($total_registros/$rppg);
		}

		$pg_ultima = $num_pags-1;
		$pg_corrente = $pg*$rppg;

		//busca os registro da página correta
		$sql .= " LIMIT $pg_corrente, $rppg";

    	$sql_q = $db->query($sql);
    
        // contadores de resultados
		$ind['first'] = $pg_corrente + 1;
		$ind['last'] = $pg_corrente + $db->num_rows($sql_q);
		$ind['total'] = $total_registros;

		// seta índices de resultados da página atual (primeiro e último) e total de resultados
		$smarty->assign("ind", $ind);

		$pg_prox = $pg + 1;
		$pg_ant  = $pg - 1;

		$nav = array();
		if($pg == 0)
			$nav[] = "<< primeira < anterior";
		else
			$nav[] = "<a class = 'link_geral' href = '?pg=0&$url'><< primeira</a> |
					  <a class = 'link_geral' href = '?pg=$pg_ant&$url'>< anterior</a>";


		if($pg+5 > $pg_ultima && $pg_ultima > 10)
			$paginas['antes'] = $pg_ultima - 9;
		elseif($pg-4<=0) 
			$paginas['antes']=0;
		else 
			$paginas['antes']=$pg-4;
		

		
		if($pg+5 > $pg_ultima)
			$paginas['depois'] = $pg_ultima + 1;
		elseif($pg+6>10)
			$paginas['depois'] = $pg+6 ;
		else
		   $paginas['depois'] =10;
	
	
		//echo " antes ".$paginas['antes'] ." | depois " . $paginas['depois'] ."  | ultima " . $pg_ultima ;
		//for($i=0 ; $i<$num_pags;$i++){
		for( $i=$paginas['antes'] ; $i<$paginas['depois']; $i++ ){
			$aux_i = $i+1;
			if ($pg == $i)
				$nav[] = "<a class = 'link_geral' href = '?pg=$i&$url'><b>$aux_i</b></a>";
			else
				$nav[] = "<a class = 'link_geral' href = '?pg=$i&$url'>$aux_i</a>";
		}

		if($pg == $pg_ultima)
			$nav[] = "próxima > última >>";
		else
			$nav[] = "<a class = 'link_geral' href = '?pg=$pg_prox&$url'>próxima ></a> |
					  <a class = 'link_geral' href = '?pg=$pg_ultima&$url'>última >></a>";

		$nav = implode(" | ", $nav);
		$smarty->assign("nav", $nav);

		return $sql_q;
	}
	
   /**
    *     método: chk_login
    *  propósito: Verifica se já existe o login passado como parâmetro
    */

    function chk_login($login) {
    	
			global $db;

		 	$retorno = false;

			//Verifica se o login é válido
			$sql_log_q = $db->query("	SELECT
																	*
								  							FROM
																	funcionario
								   							WHERE
																 	login_funcionario = '$login'");

  		$n1 = $db->num_rows($sql_log_q);

			//Verifica se o login é válido
			$sql_log_q = $db->query("	SELECT
																	*
								  							FROM
																	administrador
								   							WHERE
																 	adm_log = '$login'");

  		$n2 = $db->num_rows($sql_log_q);


  		if($n1 || $n2) $retorno = true;

  		if($retorno)
  		  $this->err[] = "O login <b>$login</b> j&aacute; existe no sistema. Escolha outro!";

  		return $retorno;

		}
		
		
		//Formata o Telefone para exibir na tela
    function FormataTelefoneParaExibir($telefone) {

			if ( strlen($telefone) == 0 ) {
				$tel = "";
    	}
    	else {
		    $ddd = "(" . substr($telefone,0,2) . ") ";
				$resto = substr($telefone,2);

				if ( strlen($telefone) == 10 ) {
		    	$resto = substr($telefone,2,4) . "-" . substr($telefone,6,4);
		    }
		    elseif ( strlen($telefone) == 9 ) {
		    	$resto = substr($telefone,2,3) . "-" . substr($telefone,5,4);
		    }
		    else {
		    	$ddd = "";
		    	$resto = $telefone;
				}
	    
		    $tel = $ddd . $resto;
		  }
	    
      return($tel);
  	}
		
		//Formata o Telefone para inserir no BD
    function FormataTelefoneParaInserir($ddd, $telefone) {
	    $tel = $ddd . ereg_replace("[^[:alnum:]]", "", $telefone);
      return($tel);
  	}


  	//Formata o CEP para exibir na tela
    function FormataCepParaExibir($cep) {
	    $cep_bk =  substr($cep,0,2). "." . substr($cep,2,3) . "-" . substr($cep,5);
      return($cep_bk);
  	}
  	
  	//Formata o CEP para inserir no BD
    function FormataCepParaInserir($cep) {
	    $cep_bk = ereg_replace("[^[:alnum:]]", "", $cep);
      return($cep_bk);
  	}
  	

  	//Formata o CNPJ para inserir no BD
    function FormataCNPJParaInserir($cnpj) {
	    $cnpj_bk = ereg_replace("[^[:alnum:]]", "", $cnpj);

			if(strlen($cnpj_bk) == 14) {
				$cnpj_bk =  substr($cnpj_bk,0,2). "." . substr($cnpj_bk,2,3) . "." . substr($cnpj_bk,5,3) . "/" . substr($cnpj_bk,8,4) . "-" . substr($cnpj_bk,12);
				return($cnpj_bk);
			}
			else return($cnpj);

  	}

  	//Formata o CPF para inserir no BD
    function FormataCPFParaInserir($cpf) {
	    $cpf_bk = ereg_replace("[^[:alnum:]]", "", $cpf);

			if(strlen($cpf_bk) == 11) {
				$cpf_bk =  substr($cpf_bk,0,3). "." . substr($cpf_bk,3,3) . "." . substr($cpf_bk,6,3) . "-" . substr($cpf_bk,9);
				return($cpf_bk);
			}
			else return($cpf);

  	}

		/* Formata a mensagem de ajuda
		 $array_mensagens == array com as mensagens a serem exibidas
		 $tipo_titulo = 1  ==>>> Instruções de preenchimento
		 $tipo_titulo = 2  ==>>> Indique também
		*/
		function FormataMensagemAjuda($array_mensagens, $tipo_titulo = 1) {
			$mensagem_retorno = "";
			
			if (count($array_mensagens) > 0) {
				
				if ($tipo_titulo == 1) {
					$mensagem_retorno = "<b>Instru&ccedil;&otilde;es de preenchimento:</b><br>";
					
					for ($i=0; $i<count($array_mensagens); $i++) {
						$mensagem_retorno .= "<li>" . $array_mensagens[$i] . "</li>";
					}
				}
				else if ($tipo_titulo == 2) {
					$mensagem_retorno = "<b>Indique também !!!</b><br>";
					
					for ($j=0; $j<count($array_mensagens); $j++) {
						$link = "<a class=link_referencia href=# onclick=xajax_Insere_Referencia_AJAX(" . $array_mensagens[$j]['idproduto_referencia'] . ") >";
						$link .= $array_mensagens[$j]['descricao_produto'] . " (" . $array_mensagens[$j]['sigla_unidade_venda'] . "): R$ " . $array_mensagens[$j]['preco'];
						$link .= "</a>";
						
						$mensagem_retorno .= "<li>" . $link . "</li>";
					}

				}
				
				
			}	
			
			return ($mensagem_retorno);

		}	


	// calcula quandos dias existem entre 2 datas
	function date_diff($date1, $date2) {
		 $s = strtotime($date2)-strtotime($date1);
		 $d = intval($s/86400);
		 $s -= $d*86400;
		 $h = intval($s/3600);
		 $s -= $h*3600;
		 $m = intval($s/60);
		 $s -= $m*60;
		 return array("d"=>$d,"h"=>$h,"m"=>$m,"s"=>$s);
	}


	/*
		MatFin_Calcula_Taxa_Diaria
		Dada uma taxa mensal, calcula a taxa diaria dos juros
	*/
	function MatFin_Calcula_Taxa_Diaria ($taxa_mensal) {

      $taxa_mensal = str_replace(",",".",$taxa_mensal);
      $taxa_mensal = 1 + ($taxa_mensal / 100);
      
      $taxa_diaria = pow($taxa_mensal, (1/30))  -1;

			return ($taxa_diaria);

		}


		/*
			MatFin_Calcula_Juros_Do_Periodo
			Dada a taxa de juros diario, calcula a taxa dos juros de um determinado periodo
		*/
		function MatFin_Calcula_Juros_Do_Periodo ($taxa_diaria, $dias) {

			$taxa_periodo =  pow((1+$taxa_diaria),$dias) - 1;

			return ($taxa_periodo);

		}


		/*
			MatFin_Calcula_FV
			Data a taxa de juros diario, o numero de dias e o PV calcula o FV
		*/
		function MatFin_Calcula_FV ($taxa_diaria, $dias, $pv) {

			$fv =  $pv * pow((1+$taxa_diaria),$dias);

			return ($fv);

		}


		/*
			MatFin_Calcula_PV
			Data a taxa de juros diario, o numero de dias e o FV calcula o PV
		*/
		function MatFin_Calcula_PV ($taxa_diaria, $dias, $fv) {

			$pv =  $fv * pow((1+$taxa_diaria),($dias * -1));

			return ($pv);

		}

	
	
	   /* Calcula o PMT
	    $r = taxa
	    $np  = numero de periodos
	    $pv  = valor presente
	    $fv  = valor futuro
	    $tipo = END para imediata e BGN para antecipada
	    $prec = precisao
	   */
		function MatFin_Calcula_PMT ($r,$np,$pv,$fv,$tipo,$prec) {
			if(!$fv) $fv = 0;

			if ($tipo == "BGN") $expoente = $np - 1;
			else $expoente = $np;
			
			if ($r > 0) {
				$mypmt=$r * (-$fv+pow((1+$r), $expoente )*$pv)/(-1+pow((1+$r),$np));
			}
			// sem juros
			else {
				$mypmt = $pv / $np;
			}	
			return round($mypmt, $prec);
		}


	/**
	 * Função para tratar os problemas com ponto flutuante
	 * Todas as operações de comparação entre dados decimais (porcentagem, monetários, etc) devem utilizar
	 * essa função em TODOS os operadores.
	 * @see http://server/trac/erpweb/ticket/162
	 * @param float $val Valor float a ser tratado
	 * @param int $length Quantidade de casas decimais a serem consideradas
	 * @return int
	 */
	function Mat_Decimal($val, $length = 2) {
		// multiplica o valor por 10^$length e converte para string
		$val = strval($val * pow(10, $length));
		return (int)$val;
	}


	/*Função para incrementar ou subtrair dias/semana/meses/anos de uma data
	 * 
	 * @param date $data - Data no formato Y-m-d
	 * @param $strDiff - String unix para alterar a data '-1 day', '+3 months', '+2 weeks'
	 * return date 
	 */
	
	function Altera_Data($data, $strDiff, $formato = 'Y-m-d'){
		
		$date = new DateTime($data);
		$date->modify($strDiff);
		return $date->format($formato);
		
				
	}


	function make_list_mesAno($qtdAnos = 3){
		
		$list['mes'] = array("01" =>'Janeiro', "02" =>'Fevereiro', "03" =>'Março',    "04" =>'Abril', 
							 "05" =>'Maio',    "06" =>'Junho',     "07" =>'Julho',    "08" =>'Agosto', 
							 "09" =>'Setembro',"10" =>'Outubro',   "11" =>'Novembro', "12" =>'Dezembro');
		
		for($i=0; $i <= $qtdAnos; $i++){

			$ano = date('Y') - $i;

			$list['ano'][] = $ano;			
		}
		
							 
		return($list);							 
							  
	}
	
        
        /*  Retorna o última dia do mês
         * 
         *  @param (int) $mes
         *  @param (int) $ano
         * 
         */
	function get_LastDayMonth($mes, $ano){
		return date("d",mktime(0, 0, 0, ($mes+1), 0, $ano));
	}

        /**  Retorna a diferença em dias a partir de duas datas no formato Y-m-d
         * 
         *  @param (date) $date1
         *  @param (date) $date2
         * 
         */
        function get_days_diff($date1,$date2){
  //var_dump($date1,$date2);          
            $datetime1 = new DateTime($date1);
            $datetime2 = new DateTime($date2);
            $interval = $datetime1->diff($datetime2);
            return $interval->format('%a');
        }
        
        /*  Retorna quantos porcentos representa um valor com relação ao total
         * 
         *  @param (float) $valor
         *  @param (float) $valorTotal
         * 
         */
        function valorPorcentagem($valor, $valorTotal) { 
            $resultado = ($valor/100) * $valorTotal;
            
            return round($resultado,2);
        }
        
        
    /**
     * Valida arquivo enviado por upload
     * @param array $arquivo - array $_FILES recebido do formulário
     * @return boolean $validacao - retorna true se o arquivo for válido e
     * retorna false caso contrário
     */
    public function validateUpload($arquivo){
    	 
    	global $form;
    	global $auth;
    
    	$validacao = true;
    
    	if(isset($arquivo['error'])){
    	  
    		switch ($arquivo['error']) {
    
    			case UPLOAD_ERR_INI_SIZE:
    			case UPLOAD_ERR_FORM_SIZE:
    				$this->err[] = 'Arquivo acima do tamanho permitido pelo sistema.';
    				$validacao = false;
    				break;
    			case UPLOAD_ERR_PARTIAL:
    				$this->err[] = 'Arquivo enviado parcialmente.';
    				$validacao = false;
    				break;
    					
    			case UPLOAD_ERR_NO_FILE:
    				$this->err[] = 'Arquivo não fornecido.';
    				$validacao = false;
    				break;
    		}
    	}
    	else{
    		$this->err[] = 'Arquivo não fornecido.';
    		$validacao = false;
    	}
    
    	return $validacao;
    }
    

    /**
     * Verifica se $data_inicial não é posterior a $data_final
     * @param String $data_inicial Data inicial que deve ser tomada por base
     * @param String $data_final Data final para comparação
     * @param boolean $formata_data Indica se a data precisa ser formatada para yyyy-mm-dd
     * @return boolean Retorna true se a data não é posterior e retorna false a data é posterior
     */
    function verificaSeDataNaoPosterior($data_inicial, $data_final, $formata_data = true){
        
        if($formata_data){
            $data_inicial = $this->FormataDataHoraParaInserir($data_inicial);
            $data_final = $this->FormataDataHoraParaInserir($data_final);
        }
        
        if($data_inicial <= $data_final){
            return true;
        }
        else{
            return false;
        }
    }
     
}
?>
