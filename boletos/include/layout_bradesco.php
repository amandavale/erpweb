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
// | 																	                                    |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordenação Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto Santander-Banespa : Fabio R. Lenharo		      |
// +----------------------------------------------------------------------+
?>

<?php 
    require_once dirname(__FILE__) . '/descricao_boleto.php';

    $dadosboleto["label_cedente"] = (isset($dadosboleto["label_cedente"])?ucfirst($dadosboleto["label_cedente"]):'Cedente');

    $dadosboleto["label_sacado"] = (isset($dadosboleto["label_sacado"])?ucfirst($dadosboleto["label_sacado"]):'Sacado');
?>


<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.0 Transitional//EN'>
<HTML>
<HEAD>
<TITLE><?php echo $dadosboleto["identificacao"]; ?></TITLE>
<META http-equiv=Content-Type content=text/html charset=ISO-8859-1>
<meta name="Generator" content="Projeto BoletoPHP - www.boletophp.com.br - Licença GPL" />
<style type=text/css>
<!--.cp {  font: bold 9px Arial; color: black}
<!--.ti {  font: 9px Arial, Helvetica, sans-serif}
<!--.ld { font: bold 15px Arial; color: #000000}
<!--.ct, .informacao_cabecalho, .autenticacao { FONT: 9px "Arial Narrow"; COLOR: #000033}
<!-- .autenticacao { vertical-align:top } 
<!-- .informacao_cabecalho { valign:top; width:196; height:13 }
<!--.cn { FONT: 9px Arial; COLOR: black }
<!--.bc { font: bold 20px Arial; color: #000000 }
<!--.ld2 { font: bold 12px Arial; color: #000000 }
<!--.cabecalho_cliente { font: 9px "Arial Narrow"; align:left; width:100%; padding-left:5px}
<!--.descricao_condominio { font: 9px "Arial Narrow"; align:left; width:100%}
<!--.descricao_condominio tr { line-height: 9px;}
<!--.cabecalho { font: bold 9px Arial Narrow }
<!--.documento { height:25cm; position:relative }
<!--.via_caixa { bottom:0; position:relative}
--></style> 
</head>

<!-- table width=666 cellspacing=5 cellpadding=0 border=0 align=Default>
  <tr>
    <td width=41><IMG SRC="../boletos/imagens/logo_empresa.png"></td>
    <td class=ti width=455><?php echo $dadosboleto["identificacao"]; ?> <?php echo isset($dadosboleto["cpf_cnpj"]) ? "<br>".$dadosboleto["cpf_cnpj"] : '' ?><br>
	<?php echo $dadosboleto["endereco"]; ?><br>
	<?php echo $dadosboleto["cidade_uf"]; ?><br>
    </td>
    <td align=RIGHT width=150 class=ti>&nbsp;</td>
  </tr>
</table -->
<BR>
<table cellspacing=0 cellpadding=0 width=666 border=0>
    <tr>
        <td class=cp width=150> 
            <span class="campo">
                <?php if($dadosboleto['logo'] == 'sos'){ ?>
                <IMG src="../common/img/sos_menor.png" width="97" height="50" border=0 >
                <?php } else { ?>
                <IMG src="../boletos/imagens/logobradesco.gif" width="140" height="37" 
                border=0>
                <?php } ?>
            </span>
        </td>
        <td width=3 valign=bottom>
            <img height=22 src=../boletos/imagens/3.png width=2 border=0>
        </td>
        <td class=cpt width=58 valign=bottom>
            <div align=center><font class=bc><?php echo $dadosboleto["codigo_banco_com_dv"]?></font></div>
        </td>
        <td width=3 valign=bottom>
            <img height=22 src=../boletos/imagens/3.png width=2 border=0>
        </td>
        <td class=ld align=right width=453 valign=bottom>
            <span class=ld> 
                <span class="campotitulo">
                    <?php echo $dadosboleto["linha_digitavel"]?>
                </span>
            </span>
        </td>
    </tr>
    
    <tr>
        <td colspan=5>
            <img height=2 src=../boletos/imagens/2.png width=666 border=0>
        </td>
    </tr>
</table>

<table cellspacing=0 cellpadding=0 border=0>
    <tbody>
        <tr>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=349 height=13>Cedente</td>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=110 height=13>Agência/Código do Cedente</td>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=34 height=13>Espécie</td>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=45 height=13>Quantidade</td>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=93 height=13>Nosso número</td>
        </tr>
        <tr>
            <td class=cp valign=top width=7 height=38><img height=38 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top width=349 height=12> 
                <span class="campo"><?php echo $dadosboleto["cedente"]; ?></span>
            </td>
            <td class=cp valign=top width=7 height=38><img height=38 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top width=110 height=12> 
                <span class="campo">
                    <?php echo $dadosboleto["ponto_venda"]." ".$dadosboleto["codigo_cliente"]?>
                </span>
            </td>
            <td class=cp valign=top width=7 height=38><img height=38 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top  width=34 height=12>
                <span class="campo">
                    <?php echo $dadosboleto["especie"]?>
                </span> 
            </td>
            <td class=cp valign=top width=7 height=38><img height=38 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top  width=45 height=12>
                <span class="campo">
                    <?php echo $dadosboleto["quantidade"]?>
                </span> 
            </td>
            <td class=cp valign=top width=7 height=38><img height=38 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top align=right width=93 height=12> 
                <span class="campo">
                    <?php print $dadosboleto["nosso_numero"]; ?>
                </span>
            </td>
        </tr>
        <tr>
            <td colspan=10 valign=top width=665 height=1><img height=1 src=../boletos/imagens/2.png width=666 border=0></td>
        </tr>
    </tbody>
</table>

<table cellspacing=0 cellpadding=0 border=0>
    <tbody>
        <tr>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top colspan=3 height=13>Número do documento</td>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=168 height=13>CPF/CNPJ</td>
            <td class=ct valign=top width=10 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=148 height=13>Vencimento</td>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=145 height=13>Valor documento</td>
        </tr>
        <tr>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top colspan=3 height=12> 
                <span class="campo">
                    <?php echo $dadosboleto["numero_documento"]?>
                </span>
            </td>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top width=168 height=12> 
                <span class="campo">
                    <?php echo $dadosboleto["cpf_cnpj"]?>
                </span>
            </td>
            <td class=cp valign=top width=10 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top width=148 height=12> 
                <span class="campo">
                    <?php echo $dadosboleto["data_vencimento"]?>
                </span>
            </td>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top align=right width=145 height=12> 
                <span class="campo">
                    <?php echo $dadosboleto["valor_boleto"]?>
                </span>
            </td>
        </tr>
        <tr>
            <td colspan=10 valign=top width=665 height=1><img height=1 src=../boletos/imagens/2.png width=666 border=0></td>
        </tr>
    </tbody>
</table>

<table cellspacing=0 cellpadding=0 border=0>
    <tbody>
        <tr>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=113 height=13>(-) Desconto / Abatimentos</td>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=112 height=13>(-) Outras deduções</td>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=110 height=13>(+) Mora / Multa</td>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=150 height=13>(+) Outros acréscimos</td>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=143 height=13>(=) Valor cobrado</td>
        </tr>
        <tr>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top align=right width=113 height=12></td>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top align=right width=112 height=12></td>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top align=right width=110 height=12></td>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top align=right width=150 height=12></td>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top align=right width=143 height=12></td>
        </tr>
        <tr>
            <td colspan=10 valign=top width=665 height=1><img height=1 src=../boletos/imagens/2.png width=666 border=0></td>
        </tr>
    </tbody>
</table>

<table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=659 height=13>Sacado</td></tr><tr><td class=cp valign=top width=7 height=12><img height=44 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top width=659 height=12> 
  <span class="campo">
  <?php echo $dadosboleto["sacado"]?>
  </span></td>
</tr>
<tr><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=659 height=1><img height=1 src=../boletos/imagens/2.png width=659 border=0></td></tr></tbody></table>

<?php if($condominio){?>

<table cellspacing=0 cellpadding=0 border=0 vertical-align=middle>
    <tbody>
        
        <tr>
            <td class=ct  width=7 height=12></td>

            <!-- Coluna da esquerda - demonstrativo -->
            <td width=300 >
                <?php echo retornaDescricaoCondominio($descricao_condominio, 
                        'DEMONSTRATIVO DO CONDOM&Iacute;NIO - ' . $dadosboleto['mes_relatorios']); ?>           
            </td>
            
            <td class=ct valign=top width=7 height=100%><img height=100% src=../boletos/imagens/1.png width=1 border=0></td>
            
            <!-- Coluna da direita - caixa proprietário -->
            <td class=ct  width=300>

                <?php if(is_array($descricao_caixa) && !empty($descricao_caixa)) {echo retornaDescricaoCaixa($descricao_caixa, 
                        $dadosboleto['mes_relatorios']); } ?>
            </td>
        </tr>
        
        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
        
    </tbody>
</table>

<?php }?>


<table cellspacing=0 cellpadding=0 border=0>
    <tbody>
        <tr>
            <td class=ct  width=7 height=12></td>
            <td class=ct  width=564 >Demonstrativo</td>
            <td class=ct  width=7 height=12></td>
            <td class=ct  width=88 >Autenticação mecânica</td>
        </tr>
        <tr>
            <td  width=7 ></td>
            <td class=cp width=564 >
                <span class="campo">
                    <?php echo $dadosboleto["demonstrativo1"]?><hr />
                    <?php echo $dadosboleto["demonstrativo2"]?><br/>
                    <?php //echo $dadosboleto["demonstrativo3"]?>
                </span>
            </td>
            <td class=cp width=7 ></td>
            <td class=cp width=88 ></td>
        </tr>
    </tbody>
</table>

<table cellspacing=0 cellpadding=0 width=666 border=0>
    <tbody>
        <tr>
            <td width=7></td>
            <td  width=500 class=cp> 
                <br><br><br> 
            </td>
            <td width=159></td>
        </tr>
    </tbody>
</table>

<table cellspacing=0 cellpadding=0 width=666 border=0><tr><td class=ct width=666></td></tr><tbody><tr><td class=ct width=666> 
<div align=right>Corte na linha pontilhada</div></td></tr><tr><td class=ct width=666><img height=1 src=../boletos/imagens/6.png width=665 border=0></td></tr></tbody></table><br><table cellspacing=0 cellpadding=0 width=666 border=0><tr><td class=cp width=150> 
  <span class="campo"><IMG 
      src="../boletos/imagens/logobradesco.gif" width="140" height="37"
      border=0></span></td>
<td width=3 valign=bottom><img height=22 src=../boletos/imagens/3.png width=2 border=0></td><td class=cpt width=58 valign=bottom><div align=center><font class=bc><?php echo $dadosboleto["codigo_banco_com_dv"]?></font></div></td><td width=3 valign=bottom><img height=22 src=../boletos/imagens/3.png width=2 border=0></td><td class=ld align=right width=453 valign=bottom><span class=ld> 
<span class="campotitulo">
<?php echo $dadosboleto["linha_digitavel"]?>
</span></span></td>
</tr>
    <tbody>
        <tr>
            <td colspan=5><img height=2 src=../boletos/imagens/2.png width=666 border=0></td>
        </tr>
    </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
    <tbody>
        <tr>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=472 height=13>Local de pagamento</td>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=180 height=13>Vencimento</td>
        </tr>
        <tr>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top width=472 height=12>Pagável preferencialmente na Rede Bradesco ou Bradesco Expresso</td>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top align=right width=180 height=12>
                <span class="campo">
                    <?php echo $dadosboleto["data_vencimento"]?>
                </span>
            </td>
        </tr>
        <tr>
            <td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td>
            <td valign=top width=472 height=1><img height=1 src=../boletos/imagens/2.png width=472 border=0></td>
            <td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td>
            <td valign=top width=180 height=1><img height=1 src=../boletos/imagens/2.png width=180 border=0></td>
        </tr>
    </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
    <tbody>
        <tr>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=472 height=13>Beneficiário / CNPJ / Endereço</td>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=180 height=13>Agência / Código do Beneficiário</td>
        </tr>
        <tr>
            <td class=cp valign=top width=7 height=22><img height=22 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top width=472 height=12> 
                <span class="campo">
                    <?php echo $dadosboleto["cedente"]?>
                </span>
            </td>
            <td class=cp valign=top width=7 height=22><img height=22 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top align=right width=180 height=12> 
                <span class="campo">

                    <?php echo $dadosboleto["ponto_venda"]." ".$dadosboleto["codigo_cliente"]?>
                </span>
            </td>
        </tr>
        <tr>
            <td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td>
            <td valign=top width=472 height=1><img height=1 src=../boletos/imagens/2.png width=472 border=0></td>
            <td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td>
            <td valign=top width=180 height=1><img height=1 src=../boletos/imagens/2.png width=180 border=0></td>
        </tr>
    </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
    <tbody>
        <tr>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=113 height=13>Data do documento</td>
            <td class=ct valign=top width=7 height=13> <img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=153 height=13>N<u>o</u> documento</td>
            <td class=ct valign=top width=7 height=13> <img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=62 height=13>Espécie doc.</td>
            <td class=ct valign=top width=7 height=13> <img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=34 height=13>Aceite</td>
            <td class=ct valign=top width=7 height=13> <img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=82 height=13>Data processamento</td>
            <td class=ct valign=top width=7 height=13> <img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=180 height=13>Nosso número</td>
        </tr>
        <tr>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top  width=113 height=12>
                <div align=left> 
                    <span class="campo">
                        <?php echo $dadosboleto["data_documento"]?>
                    </span>
                </div>
            </td>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top width=153 height=12> 
                <span class="campo">
                    <?php echo $dadosboleto["numero_documento"]?>
                </span>
            </td>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top  width=62 height=12>
                <div align=left>
                    <span class="campo">
                        <?php echo $dadosboleto["especie_doc"]?>
                    </span> 
                </div>
            </td>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top  width=34 height=12>
                <div align=left>
                    <span class="campo">
                        <?php echo $dadosboleto["aceite"]?>
                    </span> 
                </div>
            </td>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top  width=82 height=12>
                <div align=left> 
                    <span class="campo">
                        <?php echo $dadosboleto["data_processamento"]?>
                    </span>
                </div>
            </td>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top align=right width=180 height=12> 
                <span class="campo">
                    <?php echo $dadosboleto["nosso_numero"]; ?>
                </span>
            </td>
        </tr>
        <tr>
            <td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td>
            <td valign=top width=113 height=1><img height=1 src=../boletos/imagens/2.png width=113 border=0></td>
            <td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td>
            <td valign=top width=153 height=1><img height=1 src=../boletos/imagens/2.png width=153 border=0></td>
            <td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td>
            <td valign=top width=62 height=1><img height=1 src=../boletos/imagens/2.png width=62 border=0></td>
            <td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td>
            <td valign=top width=34 height=1><img height=1 src=../boletos/imagens/2.png width=34 border=0></td>
            <td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td>
            <td valign=top width=82 height=1><img height=1 src=../boletos/imagens/2.png width=82 border=0></td>
            <td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td>
            <td valign=top width=180 height=1><img height=1 src=../boletos/imagens/2.png width=180 border=0></td>
        </tr>
    </tbody>
</table>

<table cellspacing=0 cellpadding=0 border=0>
    <tbody>
        <tr> 
            <td class=ct valign=top width=7 height=13> <img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top height=13> Uso do Banco</td>
            <td class=ct valign=top width=7 height=13> <img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top height=13> CIP</td>            
            <td class=ct valign=top width=7 height=13> <img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top height=13> Carteira</td>
            <td class=ct valign=top height=13 width=7><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=63 height=13>Moeda</td>
            <td class=ct valign=top height=13 width=7> <img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=103 height=13>Quantidade</td>
            <td class=ct valign=top height=13 width=7> <img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=82 height=13> Valor</td>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=180 height=13>(=) Valor do Documento</td>
        </tr>
        <tr>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td valign=top class=cp height=12>
                <div align=left></div>    
                <div align=left>
                    <span class="campo">

                    </span>
                </div>
            </td>

            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td valign=top class=cp height=12>
                <div align=left></div>    
                <div align=left>
                    <span class="campo">
                        <?php echo $dadosboleto["cip"]?>
                    </span>
                </div>
            </td>

            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td valign=top class=cp height=12>
                <div align=left></div>    
                <div align=left>
                    <span class="campo">
                        <?php echo $dadosboleto["carteira_descricao"]?>
                    </span>
                </div>
            </td>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top  width=63>
                <div align=left>
                    <span class="campo">
                        <?php echo $dadosboleto["especie"]?>
                    </span> 
                </div>
            </td>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top  width=103>
                <span class="campo">
                    <?php echo $dadosboleto["quantidade"]?>
                </span> 
            </td>
            <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top width=82> 
                <span class="campo">
                    <?php echo $dadosboleto["valor_unitario"]?>
                </span>
            </td>
            <td class=cp valign=top width=7 height=12> <img height=12 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top align=right width=180 height=12> 
                <span class="campo">
                    <?php echo $dadosboleto["valor_boleto"]?>
                </span>
            </td>
        </tr>
        <tr>
            <td valign=top width=7 height=1> <img height=1 src=../boletos/imagens/2.png width=7 border=0></td>
            <td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=75 border=0></td>
            <td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td>
            <td valign=top width=31 height=1><img height=1 src=../boletos/imagens/2.png width=31 border=0></td>
            <td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td>
            <td valign=top width=83 height=1><img height=1 src=../boletos/imagens/2.png width=83 border=0></td>
            <td valign=top width=7 height=1> <img height=1 src=../boletos/imagens/2.png width=7 border=0></td>
            <td valign=top width=63 height=1><img height=1 src=../boletos/imagens/2.png width=63 border=0></td>
            <td valign=top width=7 height=1> <img height=1 src=../boletos/imagens/2.png width=7 border=0></td>
            <td valign=top width=103 height=1><img height=1 src=../boletos/imagens/2.png width=103 border=0></td>
            <td valign=top width=7 height=1> <img height=1 src=../boletos/imagens/2.png width=7 border=0></td>
            <td valign=top width=82 height=1><img height=1 src=../boletos/imagens/2.png width=82 border=0></td>
            <td valign=top width=7 height=1> <img height=1 src=../boletos/imagens/2.png width=7 border=0></td>
            <td valign=top width=180 height=1><img height=1 src=../boletos/imagens/2.png width=180 border=0></td>
        </tr>
    </tbody> 
</table>

<table cellspacing=0 cellpadding=0 width=666 border=0><tbody><tr><td align=right width=10><table cellspacing=0 cellpadding=0 border=0 align=left><tbody> 
<tr> <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td></tr><tr> 
<td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td></tr><tr> 
<td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=1 border=0></td></tr></tbody></table></td><td valign=top width=468 rowspan=5><font class=ct>Instruções 
(Texto de responsabilidade do cedente)</font><br><br><span class=cp> <FONT class=campo>
<?php echo $dadosboleto["instrucoes1"]; ?><br>
<?php echo $dadosboleto["instrucoes2"]; ?><br>
<?php echo $dadosboleto["instrucoes3"]; ?><br>
<?php echo $dadosboleto["instrucoes4"]; ?></FONT><br><br> 
</span></td>
<td align=right width=188><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=180 height=13>(-) 
Desconto / Abatimentos</td></tr><tr> <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top align=right width=180 height=12></td></tr><tr> 
<td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=180 height=1><img height=1 src=../boletos/imagens/2.png width=180 border=0></td></tr></tbody></table></td></tr><tr><td align=right width=10> 
<table cellspacing=0 cellpadding=0 border=0 align=left><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td></tr><tr><td valign=top width=7 height=1> 
<img height=1 src=../boletos/imagens/2.png width=1 border=0></td></tr></tbody></table></td><td align=right width=188><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=180 height=13>(-) 
Outras deduções</td></tr><tr><td class=cp valign=top width=7 height=12> <img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top align=right width=180 height=12></td></tr><tr><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=180 height=1><img height=1 src=../boletos/imagens/2.png width=180 border=0></td></tr></tbody></table></td></tr><tr><td align=right width=10> 
<table cellspacing=0 cellpadding=0 border=0 align=left><tbody><tr><td class=ct valign=top width=7 height=13> 
<img height=13 src=../boletos/imagens/1.png width=1 border=0></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td></tr><tr><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=1 border=0></td></tr></tbody></table></td><td align=right width=188> 
<table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=180 height=13>(+) 
Mora / Multa</td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top align=right width=180 height=12></td></tr><tr> 
<td valign=top width=7 height=1> <img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=180 height=1> 
<img height=1 src=../boletos/imagens/2.png width=180 border=0></td></tr></tbody></table></td></tr><tr><td align=right width=10><table cellspacing=0 cellpadding=0 border=0 align=left><tbody><tr> 
<td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td></tr><tr><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=1 border=0></td></tr></tbody></table></td><td align=right width=188> 
<table cellspacing=0 cellpadding=0 border=0><tbody><tr> <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=180 height=13>(+) 
Outros acréscimos</td></tr><tr> <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top align=right width=180 height=12></td></tr><tr><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=180 height=1><img height=1 src=../boletos/imagens/2.png width=180 border=0></td></tr></tbody></table></td></tr><tr><td align=right width=10><table cellspacing=0 cellpadding=0 border=0 align=left><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td></tr></tbody></table></td><td align=right width=188><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=180 height=13>(=) 
Valor cobrado</td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top align=right width=180 height=12></td></tr></tbody> 
</table></td></tr></tbody></table>

<table cellspacing=0 cellpadding=0 width=666 border=0>
    <tbody>
        <tr>
            <td valign=top width=666 height=1><img height=1 src=../boletos/imagens/2.png width=666 border=0></td>
        </tr>
    </tbody>
</table>
<table cellspacing=0 cellpadding=0 border=0>
    <tbody>
        <tr>
            <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=ct valign=top width=659 height=13>Nome do Pagador / CPF / CNPJ / Endereço</td>
        </tr>
        <tr>
            <td class=cp valign=top width=7 height=15><img height=44 src=../boletos/imagens/1.png width=1 border=0></td>
            <td class=cp valign=top width=659 height=15>
                <span class="campo">
                    <?php echo $dadosboleto["sacado"]?>
                </span> 
            </td>
        </tr>
    </tbody>
</table>

<!-- <table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top width=659 height=12><span class="campo">
<?php //echo $dadosboleto["endereco1"]?>
</span> 
</td>
</tr></tbody></table>
 -->
<table cellspacing=0 cellpadding=0 border=0>
    <tbody>

        <tr>
            <td class=ct valign=top width=7 height=13>
                <img height=13 src=../boletos/imagens/1.png width=1 border=0>
            </td>
            <td class=cp valign=top width=472 height=13> 
                <span class="campo">
                    <?php echo $dadosboleto["endereco2"]?>
                </span>
            </td>
            <td class=ct valign=top width=7 height=13>
                <img height=13 src=../boletos/imagens/1.png width=1 border=0>
            </td>
            <td class=ct valign=top width=180 height=13>Cód. baixa
            </td>
        </tr>

        <tr>
            <td valign=top width=7 height=1>
                <img height=1 src=../boletos/imagens/2.png width=7 border=0>
            </td>
            <td valign=top width=472 height=1>
                <img height=1 src=../boletos/imagens/2.png width=472 border=0>
            </td>
            <td valign=top width=7 height=1>
                <img height=1 src=../boletos/imagens/2.png width=7 border=0>
            </td>
            <td valign=top width=180 height=1>
                <img height=1 src=../boletos/imagens/2.png width=180 border=0>
            </td>
        </tr>
    </tbody>
</table>

<TABLE cellSpacing=0 cellPadding=0 border=0 width=666><TBODY><TR><TD class=ct  width=7 height=12></TD><TD class=ct  width=409 >Sacador/Avalista</TD><TD class=ct  width=250 ><div align=right>Autenticação 
mecânica - Ficha de Compensação</div></TD></TR><TR><TD class=ct  colspan=3 ></TD></tr></tbody></table>

<TABLE cellSpacing=0 cellPadding=0 width=666 border=0>
    <TBODY>
        <TR>
            <TD style="padding-left:5mm" vAlign=bottom align=left height=50><?php fbarcode($dadosboleto["codigo_barras"]); ?> </TD>
        </tr>
    </tbody>
</table>

<TABLE cellSpacing=0 cellPadding=0 width=666 border=0><TR><TD class=ct width=666></TD></TR><TBODY><TR><TD class=ct width=666><div align=right>Corte 
na linha pontilhada</div></TD></TR><TR><TD class=ct width=666><img height=1 src=../boletos/imagens/6.png width=665 border=0></TD></tr></tbody></table>
</BODY></HTML>
<div style = "page-break-after:always">&nbsp;</div>