{include file="com_cabecalho.tpl"}

{include file="div_erro.tpl"}

{include file="div_login.tpl"}


<script type="text/vbscript" src="{$conf.addr}/common/js/orcamento.vbs"></script>
<script type="text/vbscript" src="{$conf.addr}/common/js/inicio.vbs"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/orcamento.js"></script>

{if $flags.okay}

<table width="100%"  border="0" cellpadding="0" cellspacing="0">

	<tr>
		<td width="25" height="20">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>

	<tr>

		<td align="center">
			Página principal.
		</td>

	</tr>

	<tr>
	 <td height="10">&nbsp;</td>
	 <td align="center" valign="bottom">&nbsp;</td>
	</tr>

	<form  action="" method="post" name="for_inicial" id="for_inicial">
		<input type="hidden" name="tef_caminho_1" id="tef_caminho_1" value="{$conf.tef_rede_visa_amex}" />
		<input type="hidden" name="tef_caminho_2" id="tef_caminho_2" value="{$conf.tef_tecban}" />
		<input type="hidden" name="tef_caminho_3" id="tef_caminho_3" value="{$conf.tef_hipercard}" />
		
		<input type="hidden" name="travar_teclado" id="travar_teclado" value="{$conf.travar_teclado}" />
		
		<input type="hidden" name="identificacao_solicitacao" id="identificacao_solicitacao" value="{$flags.identificacao}" />
	</form>

</table>

{/if}

{* Se o browser for IE, verifica se houve alguma queda de energia no micro *}
{if ($smarty.session.browser_usuario == "0") && ($smarty.session.usr_cod != "") }
	<script language="javascript">
		VerificaQuedaEnergiaTEF();
	</script>
{/if}

{include file="com_rodape.tpl"}

