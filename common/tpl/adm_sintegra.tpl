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
      	<li>Selecione o mês e ano do arquivo a ser gerado.</li>
        <li>Clique em GERAR.</li> 
        <li>Selecione o lugar a ser salvo o arquivo gerado.</li> 
        <li>Carregue o arquivo no validador.</li> 
    {include file="div_instrucoes_fim.tpl"}

   {if $flags.gerou_arquivo != '1'}
		<table width="100%" align="center" >
    	<form enctype="multipart/form-data" action="{$smarty.server.PHP_SELF}?ac=gerar" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
      <input type="hidden" name="total_arquivo" id="total_arquivo" value="{$conf.campos_sintegra}" />
      <tr><td colspan='2' align='center'>Data:</td></tr>
        <tr>
          <td align="right" width="50%">
            <select name="mes_1" id="mes_1">
              <option value="01">Janeiro</option>
              <option value="02">Fevereiro</option>
              <option value="03">Março</option>
              <option value="04">Abril</option>
              <option value="05">Maio</option>
              <option value="06">Junho</option>
              <option value="07">Julho</option>
              <option value="08">Agosto</option>
              <option value="09">Setembro</option>
              <option value="10">Outubro</option>
              <option value="11">Novembro</option>
              <option value="12">Dezembro</option>
            </select>
          </td>
          <td>
            <select name="ano_1" id="ano_1">
              <option value="{$ano.1}">{$ano.1}</option>   
              <option value="{$ano.2}">{$ano.2}</option>
              <option value="{$ano.3}">{$ano.3}</option>
              <option value="{$ano.4}">{$ano.4}</option>
            </select>
          </td>
        </tr> 
        
  
        <tr><td>&nbsp;</td></tr>
        
        <tr><td colspan='2' align='center'>Integrar o(s) Arquivo(s):</td></tr>
        
        <tr>
          <td colspan='2' align='center'>
          <div id='div_arquivo' name='div_arquivo' colspan='2' align='center'>
            <table>
            
            {section name=i loop=$conf.campos_sintegra}
              <tr>
                <td>
                  <input type='file' name='arquivo_{$smarty.section.i.index}' id='arquivo_{$smarty.section.i.index}' size='20'>
                </td>
              </tr>
             {/section} 
             
            </table>
          </div>
          </td>
        </tr>
        
 
   
{*     <tr>
        <td> 
            <input name="button" type="button" class="botao_padrao" value="+campos"
              onClick="xajax_insereCampoUpload(xajax.getFormValues('for'));" >
        </td>
      </tr>
*}
				<tr>
        	<td align="center" colspan="2">
        		<input name="button" type="button" class="botao_padrao" value="GERAR"
              onClick="xajax_Verifica_Campos_Sintegra_AJAX(xajax.getFormValues('for'));"  >
        	</td>
        </tr>

			</form>
		</table>



  
   
   {/if}

  {/if}

{/if}

{include file="com_rodape.tpl"}
