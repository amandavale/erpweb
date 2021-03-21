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

* {
   margin: 0px;
   padding: 0px;
}
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

<BODY text=#000000 bgColor=#ffffff margin=0 padding=0>

<div class="documento">

<?php if(!$condominio){?>

<table width=666 cellspacing=0 cellpadding=0 border=0>
	<tr>
		<td valign=top class=cp>
			<DIV ALIGN="CENTER">Instruções de Impressão</DIV>
		</TD>
	</TR>
	<TR>
		<TD valign=top class=cp>
			<DIV ALIGN="left">
				<li>Imprima em impressora jato de tinta (ink jet) ou laser em qualidade normal ou alta (Não use modo econômico).<br>
				<li>Utilize folha A4 (210 x 297 mm) ou Carta (216 x 279 mm) e margens mínimas à esquerda e à direita do formulário.<br>
				<li>Corte na linha indicada. Não rasure, risque, fure ou dobre a região onde se encontra o código de barras.<br>
				<li>Caso não apareça o código de barras no final, pressione F5 para atualizar esta tela.
				<li>Caso tenha problemas ao imprimir, copie a seqüencia numérica abaixo e pague no caixa eletrônico ou no internet banking:<br><br>
				<span class="ld2">
					&nbsp;&nbsp;&nbsp;&nbsp;Linha Digitável: &nbsp;<?php echo $dadosboleto["linha_digitavel"]?><br>
					&nbsp;&nbsp;&nbsp;&nbsp;Valor: &nbsp;&nbsp;R$ <?php echo $dadosboleto["valor_boleto"]?><br>
				</span>
			</DIV>
		</td>
	</tr>
</table>

<table cellspacing=0 cellpadding=0 width=666 border=0><TBODY><TR><TD class=ct width=666><img height=1 src=../boletos/imagens/6.png width=665 border=0></TD></TR><TR><TD class=ct width=666><div align=right><b class=cp>Recibo 
do <?php echo $dadosboleto["label_sacado"]; ?></b></div></TD></tr></tbody></table><table width=666 cellspacing=5 cellpadding=0 border=0><tr><td width=41></TD></tr></table>
<!-- table width=666 cellspacing=5 cellpadding=0 border=0 align=Default>
  <tr>
    <td width=41><IMG src="../boletos/imagens/logo_empresa.png"></td>
    <td class=ti width=455><?php echo $dadosboleto["identificacao"]; ?> <?php echo isset($dadosboleto["cpf_cnpj"]) ? "<br>".$dadosboleto["cpf_cnpj"] : '' ?><br>
	<?php echo $dadosboleto["endereco"]; ?><br>
	<?php echo $dadosboleto["cidade_uf"]; ?><br>
    </td>
    <td align=RIGHT width=150 class=ti>&nbsp;</td>
  </tr>
</table -->


<table cellspacing=0 cellpadding=0 width=666 border=0>
	<tr>
		<td class=cp width=150  style="text-align:center"> 
			<span>
  				<IMG src="../common/img/sos_menor.png" width="97" height="50" border=0 >
  			</span>
  		</td>
  		
		<td width=3 valign=bottom>
			<img height=22 src=../boletos/imagens/3.png width=2 border=0>
		</td>
		
		<td class=cpt width=58 valign=bottom>
			<div align=center>
				<font class=bc>
					<?php echo $dadosboleto["codigo_banco_com_dv"]?>
				</font>
			</div>
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
	
	<tbody>
	<tr>
		<td colspan=5>
			<img height=2 src=../boletos/imagens/2.png width=666 border=0>
		</td>
	</tr>
	</tbody>
</table>

<table cellspacing=0 cellpadding=0 border=0>
	<tbody>
		<tr>
			<td class=ct valign=top width=7 height=13>
				<img height=13 src=../boletos/imagens/1.png width=1 border=0>
			</td>
			<td class=ct valign=top width=268 height=13>
				<?php echo $dadosboleto["label_cedente"]; ?>
			</td>
			<td class=ct valign=top width=7 height=13>
				<img height=13 src=../boletos/imagens/1.png width=1 border=0>
			</td>
			<td class=ct valign=top width=156 height=13>
				Agência/Código do <?php echo $dadosboleto["label_cedente"]; ?>
			</td>
			<td class=ct valign=top width=7 height=13>
				<img height=13 src=../boletos/imagens/1.png width=1 border=0>
			</td>
			<td class=ct valign=top width=34 height=13>
				Espécie
			</td>
			<td class=ct valign=top width=7 height=13>
				<img height=13 src=../boletos/imagens/1.png width=1 border=0>
			</td>
			<td class=ct valign=top width=53 height=13>
				Quantidade
			</td>
			<td class=ct valign=top width=7 height=13>
				<img height=13 src=../boletos/imagens/1.png width=1 border=0>
			</td>
			<td class=ct valign=top width=120 height=13>
				Nosso número
			</td>
		</tr>
		<tr>
			<td class=cp valign=top width=7 height=12>
				<img height=40 src=../boletos/imagens/1.png width=1 border=0>
			</td>
			<td class=cp valign=top width=268 height=12>
  				<span class="campo"><?php echo $dadosboleto["cedente"]; ?></span>
  			</td>
			<td class=cp valign=top width=7 height=12>
				<img height=40 src=../boletos/imagens/1.png width=1 border=0>
			</td>
			<td class=cp valign=top width=156 height=12>
  				<span class="campo"><?php echo $dadosboleto["agencia_codigo"]?></span>
  			</td>
			<td class=cp valign=top width=7 height=12>
				<img height=40 src=../boletos/imagens/1.png width=1 border=0>
			</td>
			<td class=cp valign=top  width=34 height=12>
				<span class="campo"><?php echo $dadosboleto["especie"]?></span> 
 			</td>
			<td class=cp valign=top width=7 height=12>
				<img height=40 src=../boletos/imagens/1.png width=1 border=0>
			</td>
			<td class=cp valign=top  width=53 height=12>
				<span class="campo"><?php echo $dadosboleto["quantidade"]?></span>
 			</td>
			<td class=cp valign=top width=7 height=12>
				<img height=40 src=../boletos/imagens/1.png width=1 border=0>
			</td>
			<td class=cp valign=top align=right width=120 height=12> 
  				<span class="campo"><?php echo $dadosboleto["nosso_numero"]?></span>
  			</td>
		</tr>
<tr><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=268 height=1><img height=1 src=../boletos/imagens/2.png width=268 border=0></td><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=156 height=1><img height=1 src=../boletos/imagens/2.png width=156 border=0></td><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=34 height=1><img height=1 src=../boletos/imagens/2.png width=34 border=0></td><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=53 height=1><img height=1 src=../boletos/imagens/2.png width=53 border=0></td><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=120 height=1><img height=1 src=../boletos/imagens/2.png width=120 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top colspan=3 height=13>Número
do documento</td><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=132 height=13>CPF/CNPJ</td><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=134 height=13>Vencimento</td><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=180 height=13>Valor 
documento</td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top colspan=3 height=12> 
  <span class="campo">
  <?php echo $dadosboleto["numero_documento"]?>
  </span></td>
<td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top width=132 height=12> 
  <span class="campo">
  <?php echo $dadosboleto["cpf_cnpj"]?>
  </span></td>
<td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top width=134 height=12> 
  <span class="campo">
  <?php echo ($data_venc != "") ? $dadosboleto["data_vencimento"] : "Contra Apresentação" ?>
  </span></td>
<td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top align=right width=180 height=12> 
  <span class="campo">
  <?php echo $dadosboleto["valor_boleto"]?>
  </span></td>
</tr>

<tr><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=113 height=1><img height=1 src=../boletos/imagens/2.png width=113 border=0></td><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=72 height=1><img height=1 src=../boletos/imagens/2.png width=72 border=0></td><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=132 height=1><img height=1 src=../boletos/imagens/2.png width=132 border=0></td><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=134 height=1><img height=1 src=../boletos/imagens/2.png width=134 border=0></td><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=180 height=1><img height=1 src=../boletos/imagens/2.png width=180 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=113 height=13>(-) 
Desconto / Abatimentos</td><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=112 height=13>(-) 
Outras deduções</td><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=113 height=13>(+) 
Mora / Multa</td><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=113 height=13>(+) 
Outros acréscimos</td><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=180 height=13>(=) 
Valor cobrado</td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top align=right width=113 height=12></td><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top align=right width=112 height=12></td><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top align=right width=113 height=12></td><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top align=right width=113 height=12></td><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top align=right width=180 height=12></td></tr><tr><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=113 height=1><img height=1 src=../boletos/imagens/2.png width=113 border=0></td><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=112 height=1><img height=1 src=../boletos/imagens/2.png width=112 border=0></td><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=113 height=1><img height=1 src=../boletos/imagens/2.png width=113 border=0></td><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=113 height=1><img height=1 src=../boletos/imagens/2.png width=113 border=0></td><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=180 height=1><img height=1 src=../boletos/imagens/2.png width=180 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=659 height=13><?php echo $dadosboleto["label_sacado"]; ?></td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top width=659 height=12> 
  <span class="campo">
  <?php echo $dadosboleto["sacado"]?>
  </span></td>
</tr></tbody></table>


<?php } else {?>

<table cellspacing=0 cellpadding=0 width=666 border=0>
	<tr>
		<td class=cp width=97> 
			<span class="campo">
  				<img src="../common/img/sos_menor.png" width=97 height=50 border=0 />
  			</span>
  		</td>
  		
  		<td width=507 class=cabecalho_cliente>
  		
			<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td colspan=3 class=ct height=13>
						<?php echo $dadosboleto["label_sacado"]; ?>: <span class="cabecalho"><?php echo $dadosboleto["nome_cliente"]; ?></span>
					</td>
				</tr>
 
 				<tr>
					<td class="ct informacao_cabecalho"><?php echo $dadosboleto["label_cedente"]; ?></td>
					<td class="ct informacao_cabecalho">Agência/Código do <?php echo $dadosboleto["label_cedente"]; ?></td>
					<td class="ct informacao_cabecalho">Espécie</td>
				</tr>
				<tr>
					<td class=cp valign=top width=210 height=12> 
		  				<span class="campo">
		  					<?php echo $dadosboleto["cedente"]; ?>
		  				</span>
		  			</td>
					<td class=cp valign=top width=196 height=12> 
		  				<span class="campo">
		  					<?php echo $dadosboleto["agencia_codigo"]?>
		  				</span>
		  			</td>
	
		  			<td class=cp valign=top width=186 height=12> 
		  				<span class="campo">
		  					<?php echo $dadosboleto["especie"]?>
		  				</span>
		  			</td>		  			
				</tr>

 				<tr>
					<td colspan="2" class="ct informacao_cabecalho">Endereço</td>
					<td colspan="2" class="ct informacao_cabecalho">CNPJ</td>
				</tr>
 
				<tr>
					<td colspan="2" class=cp valign=top width=210 height=12> 
		  				<span class="campo">
		  					<?php echo $dadosboleto["endereco"] . ' ' . $dadosboleto["cidade_uf"]; ?>
		  				</span>
		  			</td>
					<td class=cp valign=top width=210 height=12> 
		  				<span class="campo">
		  					<?php echo $dadosboleto["cpf_cnpj"]; ?>
		  				</span>
		  			</td>

		  		</tr>
				
				<tr>
					<td class="ct informacao_cabecalho">Número do documento</td>
					<td class="ct informacao_cabecalho">Vencimento</td>
					<td class="ct informacao_cabecalho">Valor</td>
				</tr>	
				<tr>
					<td class=cp valign=top width=172 height=12> 
		  				<span class="campo">
		  					<?php echo $dadosboleto["numero_documento"]?>
		  				</span>
		  			</td>
					<td class=cp valign=top width=172 height=12> 
		  				<span class="campo">
		  					<?php echo ($data_venc != "") ? $dadosboleto["data_vencimento"] : "Contra Apresentação" ?>
		  				</span>
		  			</td>
	
		  			<td class=cp valign=top width=172 height=12> 
		  				<span class="campo">
		  					<?php echo $dadosboleto["valor_boleto"]?>
		  				</span>
		  			</td>		  			
				</tr>
				
			</table>
	  		
  		</td>
	</tr>
</table>

<div style="min-height:5px; vertical-align:middle"><img width="666" height="1" border="0" src="../boletos/imagens/2.png"></div>

<?php }?>


			
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


<div class="via_caixa">

<table cellspacing=0 cellpadding=0 width=666 border=0>

	<tr>
		<td colspan="4" class=ct  width=666>
			<img height=1 src=../boletos/imagens/2.png width=666 border=0>
		</td>
	</tr>

	<tr height=80>
		<td class=autenticacao  width=7 height=12></td>
		<td class=autenticacao  width=326 style="padding-top:2px">Demonstrativo</td>
		<td class=autenticacao  width=7 height=12></td>
		<td class=autenticacao  width=326 style="padding-top:2px; text-align:right">Autenticação mecânica</td>
	</tr>

	<tr>
		<td class=ct width=666 colspan="4"></td>
	</tr>


	<tr>
		<td class=ct width=666 colspan="4"> 
			<div align=right style="bottom:1px">Corte na linha pontilhada</div>
		</td>
	</tr>
	<tr>
		<td class=ct width=666 colspan="4">
			<img height=1 src=../boletos/imagens/6.png width=665 border=0>
		</td>
	</tr>
</table>

<br>
<table cellspacing=0 cellpadding=0 width=666 border=0><tr><td class=cp width=150> 
  <span class="campo"><IMG src="<?php echo $dadosboleto['logo']?>" width="150" height="40" 
      border=0></span></td>
<td width=3 valign=bottom><img height=22 src=../boletos/imagens/3.png width=2 border=0></td><td class=cpt width=58 valign=bottom><div align=center><font class=bc><?php echo $dadosboleto["codigo_banco_com_dv"]?></font></div></td><td width=3 valign=bottom><img height=22 src=../boletos/imagens/3.png width=2 border=0></td><td class=ld align=right width=453 valign=bottom><span class=ld> 
<span class="campotitulo">
<?php echo $dadosboleto["linha_digitavel"]?>
</span></span></td>
</tr><tbody><tr><td colspan=5><img height=2 src=../boletos/imagens/2.png width=666 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=472 height=13>Local 
de pagamento</td><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=180 height=13>Data de Vencimento</td></tr><tr><td class=cp valign=top width=7 height=12><img height=30 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top width=472 height=12><?php echo $dadosboleto['local_pagamento'] ?></td><td class=cp valign=top width=7 height=12><img height=30 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top align=right width=180 height=12> 
  <span class="campo">
  <?php echo ($data_venc != "") ? $dadosboleto["data_vencimento"] : "Contra Apresentação" ?>
  </span></td>
</tr><tr><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=472 height=1><img height=1 src=../boletos/imagens/2.png width=472 border=0></td><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=180 height=1><img height=1 src=../boletos/imagens/2.png width=180 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=472 height=13><?php echo $dadosboleto["label_dados_cedente"]; ?></td><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=180 height=13>Agência/Código 
<?php echo $dadosboleto["label_cedente"]; ?></td></tr><tr><td class=cp valign=top width=7 height=12><img height=20 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top width=472 height=12> 
  <span class="campo">
  <?php echo $dadosboleto["cedente"]?>
  </span></td>
<td class=cp valign=top width=7 height=12><img height=20 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top align=right width=180 height=12> 
  <span class="campo">
  <?php echo $dadosboleto["agencia_codigo"]?>
  </span></td>
</tr><tr><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=472 height=1><img height=1 src=../boletos/imagens/2.png width=472 border=0></td><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=180 height=1><img height=1 src=../boletos/imagens/2.png width=180 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13> 
<img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=113 height=13>Data 
do documento</td><td class=ct valign=top width=7 height=13> <img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=133 height=13>N<u>o</u>
documento</td><td class=ct valign=top width=7 height=13> <img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=62 height=13>Espécie
doc.</td><td class=ct valign=top width=7 height=13> <img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=34 height=13>Aceite</td><td class=ct valign=top width=7 height=13> 
<img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=102 height=13>Data
processamento</td><td class=ct valign=top width=7 height=13> <img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=180 height=13>Nosso
número</td></tr><tr><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top  width=113 height=12><div align=left> 
  <span class="campo">
  <?php echo $dadosboleto["data_documento"]?>
  </span></div></td><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top width=133 height=12>
    <span class="campo">
    <?php echo $dadosboleto["numero_documento"]?>
    </span></td>
  <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top  width=62 height=12><div align=left><span class="campo">
    <?php echo $dadosboleto["especie_doc"]?>
  </span> 
 </div></td><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top  width=34 height=12><div align=left><span class="campo">
 <?php echo $dadosboleto["aceite"]?>
 </span> 
 </div></td><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top  width=102 height=12><div align=left>
   <span class="campo">
   <?php echo $dadosboleto["data_processamento"]?>
   </span></div></td><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top align=right width=180 height=12>
     <span class="campo">
     <?php echo $dadosboleto["nosso_numero"]?>
     </span></td>
</tr><tr><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=113 height=1><img height=1 src=../boletos/imagens/2.png width=113 border=0></td><td valign=top width=7 height=1> 
<img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=133 height=1><img height=1 src=../boletos/imagens/2.png width=133 border=0></td><td valign=top width=7 height=1>
<img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=62 height=1><img height=1 src=../boletos/imagens/2.png width=62 border=0></td><td valign=top width=7 height=1>
<img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=34 height=1><img height=1 src=../boletos/imagens/2.png width=34 border=0></td><td valign=top width=7 height=1> 
<img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=102 height=1><img height=1 src=../boletos/imagens/2.png width=102 border=0></td><td valign=top width=7 height=1>
<img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=180 height=1>
<img height=1 src=../boletos/imagens/2.png width=180 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr>
<td class=ct valign=top width=7 height=13> <img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top COLSPAN="3" height=13>Uso 
do banco</td><td class=ct valign=top height=13 width=7> <img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=83 height=13>Carteira</td><td class=ct valign=top height=13 width=7> 
<img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=43 height=13>Espécie</td><td class=ct valign=top height=13 width=7>
<img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=103 height=13>Quantidade</td><td class=ct valign=top height=13 width=7>
<img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=102 height=13>
Valor Documento</td><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=180 height=13>(=) 
Valor documento</td></tr><tr> <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td valign=top class=cp height=12 COLSPAN="3"><div align=left> 
 </div></td><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top  width=83> 
<div align=left> <span class="campo">
  <?php echo $dadosboleto["carteira"]?>
</span></div></td><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top  width=43><div align=left><span class="campo">
<?php echo $dadosboleto["especie"]?>
</span> 
 </div></td><td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top  width=103><span class="campo">
 <?php echo $dadosboleto["quantidade"]?>
 </span> 
 </td>
 <td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top  width=102>
   <span class="campo">
   <?php echo $dadosboleto["valor_unitario"]?>
   </span></td>
 <td class=cp valign=top width=7 height=12> <img height=12 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top align=right width=180 height=12> 
   <span class="campo">
   <?php echo $dadosboleto["valor_boleto"]?>
   </span></td>
</tr><tr><td valign=top width=7 height=1> <img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=75 border=0></td><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=31 height=1><img height=1 src=../boletos/imagens/2.png width=31 border=0></td><td valign=top width=7 height=1> 
<img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=83 height=1><img height=1 src=../boletos/imagens/2.png width=83 border=0></td><td valign=top width=7 height=1> 
<img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=43 height=1><img height=1 src=../boletos/imagens/2.png width=43 border=0></td><td valign=top width=7 height=1>
<img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=103 height=1><img height=1 src=../boletos/imagens/2.png width=103 border=0></td><td valign=top width=7 height=1>
<img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=102 height=1><img height=1 src=../boletos/imagens/2.png width=102 border=0></td><td valign=top width=7 height=1>
<img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=180 height=1><img height=1 src=../boletos/imagens/2.png width=180 border=0></td></tr></tbody> 
</table><table cellspacing=0 cellpadding=0 width=666 border=0><tbody><tr><td align=right width=10><table cellspacing=0 cellpadding=0 border=0 align=left><tbody> 
<tr> <td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td></tr><tr> 
<td class=cp valign=top width=7 height=12><img height=12 src=../boletos/imagens/1.png width=1 border=0></td></tr><tr> 
<td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=1 border=0></td></tr></tbody></table></td><td valign=top width=468 rowspan=5><font class=ct>Instruções 
(Instruções de responsabilidade do <?php echo $dadosboleto["label_cedente"]; ?>. Qualquer dúvida sobre este boleto, contate o <?php echo $dadosboleto["label_cedente"]; ?>)</font><br><br><span class=cp> <FONT class=campo>
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
</table></td></tr></tbody></table><table cellspacing=0 cellpadding=0 width=666 border=0><tbody><tr><td valign=top width=666 height=1><img height=1 src=../boletos/imagens/2.png width=666 border=0></td></tr></tbody></table><table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=659 height=13><?php echo $dadosboleto["label_sacado"]; ?></td></tr><tr><td class=cp valign=top width=7 height=12><img height=38 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top width=659 height=12><span class="campo">
<?php echo $dadosboleto["sacado"]?>
</span> 
</td>
</tr></tbody></table>

<table cellspacing=0 cellpadding=0 border=0><tbody><tr><td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=cp valign=top width=472 height=13> 
  <span class="campo">
  <?php echo $dadosboleto["endereco2"]?>
  </span></td>
<td class=ct valign=top width=7 height=13><img height=13 src=../boletos/imagens/1.png width=1 border=0></td><td class=ct valign=top width=180 height=13>Cód. 
baixa</td></tr><tr><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=472 height=1><img height=1 src=../boletos/imagens/2.png width=472 border=0></td><td valign=top width=7 height=1><img height=1 src=../boletos/imagens/2.png width=7 border=0></td><td valign=top width=180 height=1><img height=1 src=../boletos/imagens/2.png width=180 border=0></td>
		</tr>
	</tbody>
</table>

<TABLE cellSpacing=0 cellPadding=0 border=0 width=666>
	<TBODY>
		<TR>
			<TD class=ct  width=7 height=12></TD>
			<TD class=ct  width=409 >Sacador/Avalista</TD>
			<TD class=ct  width=250 ><div align=right>Autenticação mecânica - <b class=cp>Ficha de Compensação</b></div></TD>
		</TR>
		<TR>
			<TD class=ct  colspan=3 ></TD>
		</tr>
	</tbody>
</table>

<TABLE cellSpacing=0 cellPadding=0 width=666 border=0>
	<TBODY>
		<TR>
			<TD width="5mm">&nbsp;</TD>
			<TD vAlign=bottom align=left height=50><?php fbarcode($dadosboleto["codigo_barras"]); ?></TD>
		</tr>
	</tbody>
</table>

<TABLE cellSpacing=0 cellPadding=0 width=666 border=0><TR><TD class=ct width=666></TD></TR><TBODY><TR><TD class=ct width=666><div align=right>Corte 
na linha pontilhada</div></TD></TR><TR><TD class=ct width=666><img height=1 src=../boletos/imagens/6.png width=665 border=0></TD></tr></tbody></table>
</div>
</div>
</BODY></HTML>

<div style = "page-break-before:always">&nbsp;</div>

