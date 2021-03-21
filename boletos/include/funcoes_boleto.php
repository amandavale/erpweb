<?php
// +----------------------------------------------------------------------+
// | BoletoPhp - Versão Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo está disponível sob a Licença GPL disponível pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Você deve ter recebido uma cópia da GNU Public License junto com     |
// | esse pacote; se não, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colaborações de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de João Prado Maia e Pablo Martins F. Costa				        |
// | 														                                   			  |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordenação Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto CEF: Elizeu Alcantara                         |
// +----------------------------------------------------------------------+

function startBoleto($dadosBol, $codigobanco, $condominio = false){

	$codigo_banco_com_dv = geraCodigoBanco($codigobanco);

    /// Código da moeda - 9: real, 0: outros
	$nummoeda = "9";

	$fator_vencimento = fator_vencimento($dadosBol["data_vencimento"]);

	// valor tem 10 dígitos, sem vírgula
	$valor = formata_numero($dadosBol["valor_boleto"],10,0);

    // agência é 4 digitos
	$agencia = formata_numero($dadosBol["agencia"],4,0);
	
    // conta é 5 digitos
	$conta = formata_numero($dadosBol["conta"],5,0);
	
    // dígito verificador da conta
	$conta_dv = formata_numero($dadosBol["conta_dv"],1,0);

	// carteira é 2 caracteres
	$carteira = $dadosBol["carteira_descricao"];

    switch($codigobanco){

        case 237:
            $dadosBol["cedente"] .= " / " . $dadosBol['cpf_cnpj'] . ' / ' .
                $dadosBol['endereco'] . ' - ' . $dadosBol['cidade_uf'] . ' - ' . $dadosBol['cep'];

            $dadosBol['cip'] = '000';

            $dadosBol["aceite"] = 'N';            

            $conta = formata_numero($dadosBol["conta"],7,0,STR_PAD_LEFT);

            $carteira = strval(formata_numero($dadosBol["carteira_descricao"],2,'0',STR_PAD_LEFT));

            $nossonumero = formata_numero($dadosBol["nosso_numero"],11,'0',STR_PAD_LEFT);

            // nosso número completo (com dv) com 11 digitos
            $nossonumero .= '-'. digitoVerificador_nossonumero($carteira . $nossonumero);

            $nossonumero_sem_dv =  strval(formata_numero($dadosBol["nosso_numero"],11,'0',STR_PAD_LEFT));

            $dadosBol["nosso_numero"] = $carteira . '/' . $nossonumero;

            // 43 números para o cálculo do dígito verificador do código de barras
            $dv = digitoVerificador_barra($codigobanco . $nummoeda . $dv . $fator_vencimento . $valor . $agencia .
            $carteira . $nossonumero_sem_dv . $conta . '0');

            // Número para o código de barras com 44 dígitos
            $linha = $codigobanco . $nummoeda . $dv . $fator_vencimento . $valor . $agencia . 
                        $carteira . $nossonumero_sem_dv . $conta . '0';

            $dadosBol["codigo_barras"] = $linha;
            $dadosBol["linha_digitavel"] = monta_linha_digitavel237($linha);
        break;


        case 756:

            /// Identificador sem caractere especial para calcular linha digitável
            $identificador = str_replace('-',"",$dadosBol['identificador']);
            $identificador = formata_numero($identificador,7,0,STR_PAD_LEFT);

            $codigo_banco_com_dv = '756-0';

            $dadosBol["cedente"] .= " / " . $dadosBol['cpf_cnpj'] . ' / ' .
                $dadosBol['endereco'] . ' - ' . $dadosBol['cidade_uf'] . ' - ' . $dadosBol['cep'];

            $dadosBol["aceite"] = 'N';            

            $carteira = strval(formata_numero($dadosBol["carteira_descricao"],2,'0',STR_PAD_LEFT));

            $modalidade = '01';

            $carteira_linha_digitavel = trim($carteira,'0');

            $dadosBol["nosso_numero"] =  strval(formata_numero($dadosBol["nosso_numero_boleto"],8,'0',STR_PAD_LEFT));

            $codigo_barras_parte1 = $codigobanco . $nummoeda;
            $codigo_barras_parte2 = $fator_vencimento . $valor . $carteira_linha_digitavel . $agencia . 
                    $modalidade . $identificador . $dadosBol["nosso_numero"] . '001';

            /// Dígito verificador do código de barras
            $dv = digitoVerificador_barra($codigo_barras_parte1. $codigo_barras_parte2);

            $codigo_barras = $codigo_barras_parte1 . $dv . $codigo_barras_parte2;

            // Número para o código de barras com 44 dígitos
            $linha = $codigobanco . $nummoeda . $carteira_linha_digitavel . $agencia . $modalidade . 
                        $identificador . $dadosBol["nosso_numero"] . '001' . $dv . $fator_vencimento . $valor;

            $dadosBol["codigo_barras"] = $codigo_barras;
            $dadosBol["linha_digitavel"] = monta_linha_digitavel756($linha);

            if(!$condominio){
                $dadosBol["cedente"] .= "&nbsp;&nbsp;&nbsp;CNPJ: " . $dadosBol['cpf_cnpj'] . '<br>' . 
                    $dadosBol['endereco'] . ' - ' . $dadosBol['cidade_uf'] . ' - ' . $dadosBol['cep'];
            }

        break;


        case 341:

            $nossonumero =  strval(formata_numero($dadosBol["nosso_numero"],8,'0',STR_PAD_LEFT));
            $nossonumero_sem_dv = $nossonumero;

            $dadosBol['dac_nosso_numero'] = modulo_10($agencia . $conta . $carteira . $nossonumero_sem_dv);

            $dac_agencia = modulo_10($agencia . $conta);

            // 43 números para o cálculo do dígito verificador do código de barras
            $dv = digitoVerificador_barra($codigobanco . $nummoeda . $fator_vencimento . $valor . 
            $carteira . $nossonumero_sem_dv . $dadosBol['dac_nosso_numero'] . $agencia . $conta . $dac_agencia . '000');

            // Número para o código de barras com 44 dígitos
            $linha = $codigobanco . $nummoeda . $dv . $fator_vencimento . $valor . $carteira . $nossonumero_sem_dv . 
                    $dadosBol['dac_nosso_numero'] . $agencia . $conta . $dac_agencia . '000';


            $dadosBol["codigo_barras"] = $linha;
            $dadosBol["linha_digitavel"] = monta_linha_digitavel341($linha);

            if(!$condominio){
                $dadosBol["cedente"] .= "&nbsp;&nbsp;&nbsp;CNPJ: " . $dadosBol['cpf_cnpj'] . '<br>' . 
                    $dadosBol['endereco'] . ' - ' . $dadosBol['cidade_uf'] . ' - ' . $dadosBol['cep'];
            }

            $dadosBol["aceite"] = 'S';

            $dadosBol["carteira"] = '109';

            $dadosBol["nosso_numero"] = $nossonumero;            

        break;
    }

    /// espécie do documento: duplicata de prestação de serviços
    $dadosBol["especie_doc"] = 'DS';

	$dadosBol['codigo_banco_com_dv'] = $codigo_banco_com_dv;

	return $dadosBol;
}

function digitoVerificador_nossonumero($numero) {

	$digito = modulo_11($numero, 7);

    if(($digito <= 0) || ($digito == 11) ){

        /// Se o resultado for menor que zero, o dígito será igual a zero
        $digito = 0;
    }
    elseif($digito == 10){

        /// Segundo a regra do Bradesco, se o resto da divisão for 10, o dígito
        /// passa a ter o valor "P"

        $digito = 'P';
    }

	return $digito;
}


function digitoVerificador_barra($numero) {

	$digito = modulo_11($numero, 9);

    if ($digito == 0 || $digito == 1 || ($digito > 9)) {
       $digito = 1;
    }

	return $digito;
}


// FUNÇÕES
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
} //Fim da função

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

        if("$ano-$mes-$dia" < "2025-02-22"){
            return(abs((_dateToDays("1997","10","07")) - (_dateToDays($ano, $mes, $dia))));
        }
        else{
            return(abs((_dateToDays("2025","02","22")) - (_dateToDays($ano, $mes, $dia))) + 1000);
        }
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
 * Calcula dígito verificador dos campos da linha digitável
 */
function calculaDigitoVerificadorCampo($num) { 

    $fator = 2;

    $soma = 0;

    /// registra número de dígitos para serem percorridos
    $numero_digitos = strlen($num);

    /// percorre dígitos da direita para esquerda
    for ($i = ($numero_digitos-1); $i >= 0; $i--) {

        /// multiplica dígito pelo fator que alterna entre 1 e 2
        $multiplicacao = $num[$i] * $fator;

        /// se a multiplicação for maior que 10 os dígitos do resultado devem
        /// ser somados e esse novo valor que será considerado
        if($multiplicacao >= 10){

            $numero_digitos_multiplicacao = strlen($multiplicacao);

            $multiplicacao = strval($multiplicacao);

            /// percorre dígitos para calcular soma
            $soma_digitos = 0;
            for($contador = ($numero_digitos_multiplicacao-1); $contador >= 0; $contador--){
                $soma_digitos += $multiplicacao[$contador];
            }

            $valor = $soma_digitos;
        }
        else{
            $valor = $multiplicacao;
        }

        /// soma o valor da mutiplicação correspondente ao dígito
        $soma += $valor;

        /// alterna o fator
        if($fator == 2){
            $fator = 1;
        }
        else{
            $fator = 2;
        }
    }

    /// obtém o valor inteiro múltiplo de dez logo acima da soma encontrada

    $multiplo_dez = $soma;

    while(($multiplo_dez % 10) != 0){

        $multiplo_dez++;
    }

    /// o dígito verificador é o valor inteiro múltiplo de dez menos a soma
    $digito_verificador = $multiplo_dez - $soma;

    return $digito_verificador;

}



function modulo_10($num)  {

    $soma = 0;
    $fator = 2;

    /* Separacao dos numeros */
    for ($i = strlen($num); $i > 0; $i--) {

        // pega cada numero isoladamente
        $numeros[$i] = substr($num,$i-1,1);

        // Efetua multiplicacao do numero pelo falor
        $multiplicacao = $numeros[$i] * $fator;


        /// se a multiplicação for maior que 10 os dígitos do resultado devem
        /// ser somados e esse novo valor que será considerado
        if($multiplicacao >= 10){

            $numero_digitos_multiplicacao = strlen($multiplicacao);

            $multiplicacao = strval($multiplicacao);

            /// percorre dígitos para calcular soma
            $soma_digitos = 0;
            for($contador = ($numero_digitos_multiplicacao-1); $contador >= 0; $contador--){
                $soma_digitos += $multiplicacao[$contador];
            }

            $valor = $soma_digitos;
        }
        else{
            $valor = $multiplicacao;
        }

        // Soma dos digitos
        $soma += $valor;

        if ($fator == 2) {
            $fator = 1;
        } else {
            $fator = 2; // intercala fator de multiplicacao (modulo 10)
        }

    }

    $resto = $soma % 10;

    $digito = 10 - $resto;

    if ($digito == 10) {
        $digito = 0;
    }

    return $digito;

}



function modulo_11($num, $base=7)  {

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

function monta_linha_digitavel237($codigo) {
		
	// Posição 	Conteúdo
    // 1 a 3    Número do banco
    // 4        Código da Moeda - 9 para Real
    // 5        Digito verificador do Código de Barras
    // 6 a 9   Fator de Vencimento
	// 10 a 19 Valor (8 inteiros e 2 decimais)
    // 20 a 44 Campo Livre definido por cada banco (25 caracteres)

    // 1. Campo - composto pelo código do banco, código da moeda, as cinco primeiras posições
    // do campo livre e DV (modulo10) deste campo
    $p1 = substr($codigo, 0, 4);
    $p2 = substr($codigo, 19, 5);

    $p3 = calculaDigitoVerificadorCampo("$p1$p2");

    $p4 = "$p1$p2$p3";
    $p5 = substr($p4, 0, 5);
    $p6 = substr($p4, 5);
    $campo1 = "$p5.$p6";

    // 2. Campo - composto pelas posiçoes 6 a 15 do campo livre
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


function monta_linha_digitavel341($codigo) {
        
    // Posição  Conteúdo
    // 1 a 3    Número do banco
    // 4        Código da Moeda - 9 para Real
    // 5        Digito verificador do Código de Barras
    // 6 a 9   Fator de Vencimento
    // 10 a 19 Valor (8 inteiros e 2 decimais)
    // 20 a 44 Campo Livre definido por cada banco (25 caracteres)

    // 1. Campo - composto pelo código do banco, código da moeda, as cinco primeiras posições
    // do campo livre e DV (modulo10) deste campo
    $p1 = substr($codigo, 0, 4);
    $p2 = substr($codigo, 19, 5);

    $p3 = modulo_10("$p1$p2");

    $p4 = "$p1$p2$p3";
    $p5 = substr($p4, 0, 5);
    $p6 = substr($p4, 5);
    $campo1 = "$p5.$p6";

    // 2. Campo - composto pelas posiçoes 6 a 15 do campo livre
    // e livre e DV (modulo10) deste campo
    $p1 = substr($codigo, 24, 10);

    $p2 = modulo_10($p1);
    $p3 = "$p1$p2";

    $p4 = substr($p3, 0, 5);
    $p5 = substr($p3, 5);
    $campo2 = "$p4.$p5";

    // 3. Campo composto pelas posicoes 16 a 25 do campo livre
    // e livre e DV (modulo10) deste campo
    $p1 = substr($codigo, 34, 10);

    $p2 = modulo_10($p1);

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


function monta_linha_digitavel756($codigo) {

    // 1. Campo - composto pelo código do banco, código da moeda, carteira, agência, DV (modulo10) deste campo
    $p1 = substr($codigo, 0, 9);

    $p3 = modulo_10("$p1");

    $p4 = "$p1$p3";

    $p5 = substr($p4, 0, 5);
    $p6 = substr($p4, 5);
    $campo1 = "$p5.$p6";

    // 2. Campo - composto pelas posiçoes 9 a 18 da linha e DV (modulo10) deste campo
    $p1 = substr($codigo, 9, 10);

    $p2 = modulo_10($p1);
    $p3 = "$p1$p2";

    $p4 = substr($p3, 0, 5);
    $p5 = substr($p3, 5);
    $campo2 = "$p4.$p5";

    // 3. Campo composto pelas posicoes 19 a 28 e DV (modulo10) deste campo
    $p1 = substr($codigo, 19, 10);

    $p2 = modulo_10($p1);

    $p3 = "$p1$p2";

    $p4 = substr($p3, 0, 5);
    $p5 = substr($p3, 5);
    $campo3 = "$p4.$p5";

    // 4. Campo - digito verificador do codigo de barras
    $campo4 = substr($codigo, 29, 1);

    // 5. Campo composto pelo fator vencimento e valor nominal do documento, sem
    // indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
    // tratar de valor zerado, a representacao deve ser 000 (tres zeros).
    $p1 = substr($codigo, 30);
    $campo5 = "$p1";

    return "$campo1 $campo2 $campo3 $campo4 $campo5"; 
}



function geraCodigoBanco($numero) {
    $parte1 = substr($numero, 0, 3);
    $parte2 = modulo_11($parte1);
    return $parte1 . "-" . $parte2;
}


?>
