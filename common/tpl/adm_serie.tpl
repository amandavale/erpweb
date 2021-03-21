{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay} {include file="div_erro.tpl"} {/if}


<script language="vbscript">

	function RecuperaNumeroSerie()
		Dim cNumeroSerie

		iRetorno = BemaWeb.NumeroSerie( cNumeroSerie )
		document.for_ecf.serie_ecf.value = cNumeroSerie

		RecuperaNumeroSerie = true
	end function

</script>


{if $flags.okay}

	<table class="tb4cantosAzul" width="100%"  border="0" cellpadding="5" cellspacing="0">
		<tr>
	  	<td class="tela" WIDTH="5%" height="20">
				Tela:
			</td>
	  	<td class="descricao_tela" WIDTH="10%">
				{$conf.area}
			</td>
	  	<td class="tela" WIDTH="5%">
				Operações:
			</td>
	  	<td class="descricao_tela">
				{if $list_permissao.adicionar == '1'}&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a>{/if}
			</td>
		</tr>
	</table>

	{include file="div_erro.tpl"}

	      
	{if $flags.action == "adicionar"}

    {if $flags.sucesso != ""}
	  	{include file="div_resultado_inicio.tpl"}
	  		{$flags.sucesso}
	  	{include file="div_resultado_fim.tpl"}
		{/if}

		<br>

		{include file="div_instrucoes_inicio.tpl"}
      	<li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>
      	
    {include file="div_instrucoes_fim.tpl"}

		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_ecf" id = "for_ecf">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			<input type="hidden" name="serie_ecf" id="serie_ecf" value="" />

				
        <tr><td>&nbsp;</td></tr>

				<tr>
					<td align="center" colspan="2" class="req">
						<input type="button" class="botao_padrao" value="Gerar !" name="button" onClick="xajax_Verifica_Campos_Serie_AJAX(xajax.getFormValues('for_ecf'))" />
					</td>
				</tr>

				</form>
		</table>


  {/if}

{/if}

{include file="com_rodape.tpl"}
