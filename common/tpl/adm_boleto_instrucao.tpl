{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{include file="div_erro.tpl"}


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
	  	<td class="descricao_tela" WIDTH="15%">
				{$conf.area}
			</td>
	  	<td class="tela" WIDTH="5%">
				Opera��es:
			</td>
	  	<td class="descricao_tela">
				{if $list_permissao.adicionar == '1'}&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a>{/if}
				{if $list_permissao.listar == '1'}	 &nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">listar</a>{/if}
			</td>
		</tr>
	</table>


	
  {if $flags.action == "listar"}

    {if $flags.sucesso != ""}
	  	{include file="div_resultado_inicio.tpl"}
	  		{$flags.sucesso}
	  	{include file="div_resultado_fim.tpl"}
		{/if}

		{if count($list)}
		
			<p align="center">Listando {$conf.area} de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p>
		
			<table width="95%" align="center">
			
				
				<tr>
					<th align='center'>No</th>
					<th align='center'>Instru��o</th>
					<th align='center'>Descri��o curta</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idinstrucao={$list[i].idinstrucao}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idinstrucao={$list[i].idinstrucao}">{$list[i].texto_instrucao}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idinstrucao={$list[i].idinstrucao}">{$list[i].descricao_instrucao}</a></td>
	        </tr>
	        
	        <tr>
	          <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
	        </tr>
	      {/section}

      </table>
      
      <p align="center" id="nav">{$nav}</p>

		{else}
      {include file="div_resultado_nenhum.tpl"}
		{/if}
		

	{elseif $flags.action == "editar"}
	
		{include file="div_instrucoes_inicio.tpl"}
      	<li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>
				
    {include file="div_instrucoes_fim.tpl"}

		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idinstrucao={$info.idinstrucao}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				

				
				<tr>
					<td class="req" align="right">Instru��o:</td>
					<td><input class="extralarge" type="text" name="littexto_instrucao" id="littexto_instrucao" maxlength="200" value="{$info.littexto_instrucao}"/></td>
				</tr>
				
				<tr>
					<td align="right">Descri��o curta:</td>
					<td><input class="long" type="text" name="litdescricao_instrucao" id="litdescricao_instrucao" maxlength="100" value="{$info.litdescricao_instrucao}"/></td>
				</tr>
				
				
				

        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="submit" class="botao_padrao" value="ALTERAR">
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&idinstrucao={$info.idinstrucao}','ATEN��O! Confirma a exclus�o ?'))" >
        	</td>
        </tr>

			</form>
		</table>
	      
	      
	{elseif $flags.action == "adicionar"}

		{include file="div_instrucoes_inicio.tpl"}
      	<li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>
      	
    {include file="div_instrucoes_fim.tpl"}

		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				
				<tr>
					<td class="req" align="right">Instru��o:</td>
					<td><input class="extralarge" type="text" name="texto_instrucao" id="texto_instrucao" maxlength="200" value="{$smarty.post.texto_instrucao}"/></td>
				</tr>
				
				<tr>
					<td align="right">Descri��o curta:</td>
					<td><input class="long" type="text" name="descricao_instrucao" id="descricao_instrucao" maxlength="100" value="{$smarty.post.descricao_instrucao}"/></td>
				</tr>
				
        
        

        <tr><td>&nbsp;</td></tr>

				<tr>
          <td colspan="2" align="center">
  						<input type='Submit'  class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar" />
          </td>
        </tr>

				</form>
		</table>


  {/if}

{/if}

{include file="com_rodape.tpl"}
