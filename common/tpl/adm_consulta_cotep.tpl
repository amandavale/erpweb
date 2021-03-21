{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}


{if $flags.okay}

	<table class="tb4cantosAzul" width="100%"  border="0" cellpadding="5" cellspacing="0">
		<tr>
	  	<td class="tela" WIDTH="5%" height="20">
				Tela:
			</td>
	  	<td class="descricao_tela" WIDTH="95%">
				{$conf.area}
			</td>
		</tr>
	</table>

	
	{include file="div_erro.tpl"}

	
	{if $flags.action == "gerar"}
	
		{include file="div_instrucoes_inicio.tpl"}
        <li>Clique em GERAR ARQUIVO COTEPE.</li> 
        <li>Selecione o lugar a ser salvo o arquivo gerado.</li> 
    {include file="div_instrucoes_fim.tpl"}

   {if $flags.gerou_arquivo != '1'}
		<table width="100%" align="center" >
    	<form enctype="multipart/form-data" action="{$smarty.server.PHP_SELF}?ac=gerar" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
       
 
   
				<tr>
        	<td align="center" colspan="2">
        		<input name="button" type="submit" class="botao_padrao" value="GERAR ARQUIVO COTEPE"
                >
        	</td>
        </tr>

			</form>
		</table>



  
   
   {/if}

  {/if}

{/if}

{include file="com_rodape.tpl"}
