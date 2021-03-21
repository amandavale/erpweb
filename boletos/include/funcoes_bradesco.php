<?php
// +----------------------------------------------------------------------+
// | BoletoPhp - Vers�o Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo est� dispon�vel sob a Licen�a GPL dispon�vel pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Voc� deve ter recebido uma c�pia da GNU Public License junto com     |
// | esse pacote; se n�o, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colabora��es de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de Jo�o Prado Maia e Pablo Martins F. Costa				        |
// | 														                                   			  |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordena��o Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto CEF: Elizeu Alcantara                         |
// +----------------------------------------------------------------------+

function startBoleto($dadosBol, $codigobanco){

	$codigo_banco_com_dv = geraCodigoBanco($codigobanco);

    /// C�digo da moeda - 9: real, 0: outros
	$nummoeda = "9";

	$fator_vencimento = fator_vencimento($dadosBol["data_vencimento"]);

	// valor tem 10 d�gitos, sem v�rgula
	$valor = formata_numero($dadosBol["valor_boleto"],10,0);

    // ag�ncia � 4 digitos
	$agencia = formata_numero($dadosBol["agencia"],4,0);
	
    // conta � 5 digitos
	$conta = formata_numero($dadosBol["conta"],5,0);
	
    // d�gito verificador da conta
	$conta_dv = formata_numero($dadosBol["conta_dv"],1,0);

	// carteira � 2 caracteres
	$carteira = $dadosBol["carteira_descricao"];
		
	// nosso n�mero (sem dv) � 10 digitos
	$nnum = $dadosBol["inicio_nosso_numero"] . formata_numero($dadosBol["nosso_numero"],8,0);

	// nosso n�mero completo (com dv) com 11 digitos
	$nossonumero = formata_numero($dadosBol["nosso_numero"],11,'0',STR_PAD_LEFT) .'-'. digitoVerificador_nossonumero($nnum);
		
    $nossonumero_sem_dv =  strval(formata_numero($dadosBol["nosso_numero"],11,'0',STR_PAD_LEFT));

    // 43 n�meros para o c�lculo do d�gito verificador do c�digo de barras
    $dv = digitoVerificador_barra($codigobanco . $nummoeda . $dv . $fator_vencimento . $valor . $agencia .
    $carteira . $nossonumero_sem_dv . $conta . '0', 9, 0);

    // N�mero para o c�digo de barras com 44 d�gitos
	$linha = $codigobanco . $nummoeda . $dv . $fator_vencimento . $valor . $agencia . 
                $carteira . $nossonumero_sem_dv . $conta . '0';

	$dadosBol["codigo_barras"] = $linha;
	$dadosBol["linha_digitavel"] = monta_linha_digitavel($linha);

	$dadosBol["nosso_numero"] = $nossonumero;
	$dadosBol['codigo_banco_com_dv'] = $codigo_banco_com_dv;

	
	return $dadosBol;
}

function digitoVerificador_nossonumero($numero) {

	$digito = modulo_11($numero, 7, 1);

    if($digito < 0){

        /// Se o resultado for menor que zero, o d�gito ser� igual a zero
        $digito = 0;
    }
    elseif($digito == 10){

        /// Segundo a regra do Bradesco, se o resto da divis�o for 10, o d�gito
        /// passa a ter o valor "P"

        $digito = 'P';
    }

	return $digito;
}


function digitoVerificador_barra($numero) {

	$digito = modulo_11($numero, 9, 1);

    if ($digito == 0 || $digito == 1 || ($digito > 9)) {
       $digito = 1;
    }

	return $digito;
}


// FUN��ES
// Algumas foram retiradas do Projeto PhpBoleto e modificadas para atender as particularidades de cada banco

function formata_numero($numero, $tamanho, $insert, $direcao_preenchimento = STR_PAD_LEFT) {

    $numero = str_replace(array(",","."),"",$numero);

    $numero_formatado = str_pad($numero,$tamanho,$insert,$direcao_preenchimento);

	return $numero_formatado;
}


function fbarcode($valor){

    $fino = 1 ;
    $largo = 3 ;
    $altura = 50 ;

    $barcodes[0] = "00110" ;
    $barcodes[1] = "10001" ;
    $barcodes[2] = "01001" ;
    $barcodes[3] = "11000" ;
    $barcodes[4] = "00101" ;
    $barcodes[5] = "10100" ;
    $barcodes[6] = "01100" ;
    $barcodes[7] = "00011" ;
    $barcodes[8] = "10010" ;
    $barcodes[9] = "01010" ;

    for($f1=9;$f1>=0;$f1--){ 

        for($f2=9;$f2>=0;$f2--){  

            $f = ($f1 * 10) + $f2 ;
            $texto = "" ;

            for($i=1;$i<6;$i++){ 
                $texto .=  substr($barcodes[$f1],($i-1),1) . substr($barcodes[$f2],($i-1),1);
            }
            $barcodes[$f] = $texto;
        }
    }


    //Desenho da barra


    //Guarda inicial
    ?><img src=../boletos/imagens/p.png width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
    src=../boletos/imagens/b.png width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
    src=../boletos/imagens/p.png width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
    src=../boletos/imagens/b.png width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
    <?php
    $texto = $valor ;
    if((strlen($texto) % 2) <> 0){
    	$texto = "0" . $texto;
    }

    // Draw dos dados
    while (strlen($texto) > 0) {
      $i = round(esquerda($texto,2));
      $texto = direita($texto,strlen($texto)-2);
      $f = $barcodes[$i];
      for($i=1;$i<11;$i+=2){
        if (substr($f,($i-1),1) == "0") {
          $f1 = $fino ;
        }else{
          $f1 = $largo ;
        }
    ?>
        src=../boletos/imagens/p.png width=<?php echo $f1?> height=<?php echo $altura?> border=0><img 
    <?php
        if (substr($f,$i,1) == "0") {
          $f2 = $fino ;
        }else{
          $f2 = $largo ;
        }
    ?>
        src=../boletos/imagens/b.png width=<?php echo $f2?> height=<?php echo $altura?> border=0><img 
    <?php
      }
    }

    // Draw guarda final
    ?>
    src=../boletos/imagens/p.png width=<?php echo $largo?> height=<?php echo $altura?> border=0><img 
    src=../boletos/imagens/b.png width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
    src=../boletos/imagens/p.png width=<?php echo 1?> height=<?php echo $altura?> border=0> 
      <?php
} //Fim da fun��o

function esquerda($entra,$comp){
	return substr($entra,0,$comp);
}

function direita($entra,$comp){
	return substr($entra,strlen($entra)-$comp,$comp);
}

function fator_vencimento($data) {

    if ($data != "") {
	   $data = split("/",$data);
	   $ano = $data[2];
	   $mes = $data[1];
	   $dia = $data[0];

       return(abs((_dateToDays("1997","10","07")) - (_dateToDays($ano, $mes, $dia))));
    } else {
        return "0000";
    }
}

function _dateToDays($year,$month,$day) {
    $century = substr($year, 0, 2);
    $year = substr($year, 2, 2);
    if ($month > 2) {
        $month -= 3;
    } else {
        $month += 9;
        if ($year) {
            $year--;
        } else {
            $year = 99;
            $century --;
        }
    }
    return ( floor((  146097 * $century)    /  4 ) +
            floor(( 1461 * $year)        /  4 ) +
            floor(( 153 * $month +  2) /  5 ) +
                $day +  1721119);
}

/**
 * Calcula d�gito verificador dos campos da linha digit�vel
 */
function calculaDigitoVerificadorCampo($num) { 

    $fator = 2;

    $soma = 0;

    /// registra n�mero de d�gitos para serem percorridos
    $numero_digitos = strlen($num);

    /// percorre d�gitos da direita para esquerda
    for ($i = ($numero_digitos-1); $i >= 0; $i--) {

        /// multiplica d�gito pelo fator que alterna entre 1 e 2
        $multiplicacao = $num[$i] * $fator;

        /// se a multiplica��o for maior que 10 os d�gitos do resultado devem
        /// ser somados e esse novo valor que ser� considerado
        if($multiplicacao >= 10){

            $numero_digitos_multiplicacao = strlen($multiplicacao);

            $multiplicacao = strval($multiplicacao);

            /// percorre d�gitos para calcular soma
            $soma_digitos = 0;
            for($contador = ($numero_digitos_multiplicacao-1); $contador >= 0; $contador--){
                $soma_digitos += $multiplicacao[$contador];
            }

            $valor = $soma_digitos;
        }
        else{
            $valor = $multiplicacao;
        }

        /// soma o valor da mutiplica��o correspondente ao d�gito
        $soma += $valor;

        /// alterna o fator
        if($fator == 2){
            $fator = 1;
        }
        else{
            $fator = 2;
        }
    }

    /// obt�m o valor inteiro m�ltiplo de dez logo acima da soma encontrada

    $multiplo_dez = $soma;

    while(($multiplo_dez % 10) != 0){

        $multiplo_dez++;
    }

    /// o d�gito verificador � o valor inteiro m�ltiplo de dez menos a soma
    $digito_verificador = $multiplo_dez - $soma;

    return $digito_verificador;

}


function modulo_10($num) { 

	$numtotal10 = 0;
    $fator = 2;

    // Separacao dos numeros
    for ($i = strlen($num); $i >= 0; $i--) {

        // pega cada numero isoladamente
        $numeros[$i] = substr($num,$i-1,1);

        // Efetua multiplicacao do numero pelo (falor 10)
        $temp = $numeros[$i] * $fator; 
        $temp0=0;
        foreach (preg_split('//',$temp,-1,PREG_SPLIT_NO_EMPTY) as $k=>$v){ $temp0+=$v; }
        $parcial10[$i] = $temp0; //$numeros[$i] * $fator;
        // monta sequencia para soma dos digitos no (modulo 10)
        $numtotal10 += $parcial10[$i];
        if ($fator == 2) {
            $fator = 1;
        } else {
            $fator = 2; // intercala fator de multiplicacao (modulo 10)
        }
    }
	
    // v�rias linhas removidas, vide fun��o original
    // Calculo do modulo 10
    $resto = $numtotal10 % 10;
    $digito = 10 - $resto;
    if ($resto == 0) {
        $digito = 0;
    }
	
    return $digito;
		
}

function modulo_11($num, $base=7, $r=0)  {

    $soma = 0;
    $fator = 2;

    /* Separacao dos numeros */
    for ($i = strlen($num); $i > 0; $i--) {

        // pega cada numero isoladamente
        $numeros[$i] = substr($num,$i-1,1);

        // Efetua multiplicacao do numero pelo falor
        $parcial[$i] = $numeros[$i] * $fator;

        // Soma dos digitos
        $soma += $parcial[$i];

        if ($fator == $base) {
            // restaura fator de multiplicacao para 2 
            $fator = 1;
        }

        $fator++;
    }


    $resto = $soma % 11;

    $digito = 11 - $resto;

    return $digito;

}

function monta_linha_digitavel($codigo) {
		
		// Posi��o 	Conte�do
        // 1 a 3    N�mero do banco
        // 4        C�digo da Moeda - 9 para Real
        // 5        Digito verificador do C�digo de Barras
        // 6 a 9   Fator de Vencimento
		// 10 a 19 Valor (8 inteiros e 2 decimais)
        // 20 a 44 Campo Livre definido por cada banco (25 caracteres)

        // 1. Campo - composto pelo c�digo do banco, c�digo da moeda, as cinco primeiras posi��es
        // do campo livre e DV (modulo10) deste campo
        $p1 = substr($codigo, 0, 4);
        $p2 = substr($codigo, 19, 5);
        $p3 = calculaDigitoVerificadorCampo("$p1$p2");


        $p4 = "$p1$p2$p3";
        $p5 = substr($p4, 0, 5);
        $p6 = substr($p4, 5);
        $campo1 = "$p5.$p6";

        // 2. Campo - composto pelas posi�oes 6 a 15 do campo livre
        // e livre e DV (modulo10) deste campo
        $p1 = substr($codigo, 24, 10);
        $p2 = calculaDigitoVerificadorCampo($p1);
        $p3 = "$p1$p2";
        $p4 = substr($p3, 0, 5);
        $p5 = substr($p3, 5);
        $campo2 = "$p4.$p5";

        // 3. Campo composto pelas posicoes 16 a 25 do campo livre
        // e livre e DV (modulo10) deste campo
        $p1 = substr($codigo, 34, 10);
        $p2 = calculaDigitoVerificadorCampo($p1);
        $p3 = "$p1$p2";
        $p4 = substr($p3, 0, 5);
        $p5 = substr($p3, 5);
        $campo3 = "$p4.$p5";

        // 4. Campo - digito verificador do codigo de barras
        $campo4 = substr($codigo, 4, 1);

        // 5. Campo composto pelo fator vencimento e valor nominal do documento, sem
        // indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
        // tratar de valor zerado, a representacao deve ser 000 (tres zeros).
		$p1 = substr($codigo, 5, 4);
		$p2 = substr($codigo, 9, 10);
		$campo5 = "$p1$p2";

        return "$campo1 $campo2 $campo3 $campo4 $campo5"; 
}

function geraCodigoBanco($numero) {
    $parte1 = substr($numero, 0, 3);
    $parte2 = modulo_11($parte1);
    return $parte1 . "-" . $parte2;
}


?>
