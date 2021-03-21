{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}


{if $flags.okay}

	<table class="tb4cantosAzul" width="100%"  border="0" cellpadding="5" cellspacing="0">
		<tr>
			{if $flags.intrucoes_preenchimento != ""}
		  	<td class="tela" WIDTH="1%" height="20" valign="middle">
		  		<img class="lightbulb" src="{$conf.addr}/common/img/lampada.png" width="16" height="16" border="0" align="middle" onmouseover="pmaTooltip('{$flags.intrucoes_preenchimento}'); return false;" onmouseout="swapTooltip('default'); return false;" />
				</td>
			{/if}
	  	<td class="tela" WIDTH="5%" height="20">
				Tela:
			</td>
	  	<td class="descricao_tela" WIDTH="95%">
				{$conf.area}
			</td>
		</tr>
	</table>

  {if $flags.sucesso != ""}
  	{include file="div_resultado_inicio.tpl"}
  		{$flags.sucesso}
  	{include file="div_resultado_fim.tpl"}
  {/if}

  
  {if count($err) > 0}{include file="div_erro.tpl"}{/if}


  {if $flags.action == "listar"}
  
	{include file="div_instrucoes_inicio.tpl"}
      	<li>Clique no botão 'Executar Backup':</li>
      	<li>Grave o arquivo e o mantenha em local seguro. </li>
		<li>É recomendável não alterar o nome do arquivo.</li>
    {include file="div_instrucoes_fim.tpl"}
    
	<br>
  	<form action="{$smarty.server.PHP_SELF}?ac=listar" method="post" name="for_comissao_vendedor" id="for_comissao_vendedor">
    	<input type="hidden" name="for_chk" id="for_chk" value="1" />
		
		<table width="100%">
			<td align="center" colspan="2">
				<input type="submit" class="botao_padrao" value="Executar Backup" name="button"  />
    		</td>
        </table>
	
	</form>


  {/if}
{/if}

{include file="com_rodape.tpl"}
