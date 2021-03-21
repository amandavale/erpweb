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

	
	{if $flags.action == "listar"}
  
  <br>
	    <table align="center" width="50%" class="tb4cantos">
      <form  action="{$smarty.server.PHP_SELF}?ac=listar" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
      

        <tr>
          <td align="center" colspan="2">
          Arquivo de log do Master  : {$status_master.File}
          </td>
        </tr>  
        
        <tr>
          <td align="center" colspan="2">
          Arquivo de log do Slave : {$status_slave.File}
          </td>
        </tr>  


        
        
        

        <tr><td>&nbsp;</td></tr>
    </table>
    <table width="100%">
    <tr><td>&nbsp;</td></tr>
        <tr>
          <td align="center" colspan="2">
            <input name="Submit" type="submit" class="botao_padrao" value="SINCRONIZAR">
          </td>
        </tr>

      </form>
    </table>

 
  {/if}

{/if}

{include file="com_rodape.tpl"}
