{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{include file="div_erro.tpl"}


{if $flags.okay}

	<table width="100%"  border="0" cellpadding="0" cellspacing="0">

		<tr>
	  	<td bgcolor="#D1D1D1" WIDTH="100%" height="20">
				<b>{$conf.area}</b>
			</td>
		</tr>

	</table>

  <dir>
		<li>&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a></li>
  	<li>&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">listar</a></li>
  </dir>

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
					<th align='center'>Nome do Parâmetro</th>
					<th align='center'>Valor do Parâmetro</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idparametros={$list[i].idparametros}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idparametros={$list[i].idparametros}">{if $list[i].descricao_parametro == ""}{$list[i].nome_parametro}{else}{$list[i].descricao_parametro}{/if}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idparametros={$list[i].idparametros}">{$list[i].valor_parametro}</a></td>
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
    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idparametros={$info.idparametros}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				
				<tr>
					<td class="req" align="right">Descrição do Parâmetro:</td>
					<td><input class="long" type="text" name="litdescricao_parametro" id="litdescricao_parametro" value="{$info.litdescricao_parametro}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Nome do Parâmetro:</td>
					<td><input class="long" type="text" name="litnome_parametro" id="litnome_parametro" maxlength="20" value="{$info.litnome_parametro}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Valor do Parâmetro:</td>
					<td>
						{*<input class="medium" type="text" name="litvalor_parametro" id="litvalor_parametro" maxlength="100" value="{$info.litvalor_parametro}"/>*}					
						<textarea cols="40" rows="3" name="litvalor_parametro" id="litvalor_parametro">{$info.litvalor_parametro}</textarea>
					</td>
				</tr>
				
				
				

        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="submit" class="botao_padrao" value="ALTERAR">
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&idparametros={$info.idparametros}','ATENÇÃO! Confirma a exclusão ?'))" >
        	</td>
        </tr>

			</form>
		</table>
	      
	      
	{elseif $flags.action == "adicionar"}
{$flags.okay}
		{include file="div_instrucoes_inicio.tpl"}
      	<li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>
      	
    {include file="div_instrucoes_fim.tpl"}

		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			
				<tr>
					<td class="req" align="right">Descrição do Parâmetro:</td>
					<td><input class="long" type="text" name="descricao_parametro" id="descricao_parametro" value="{$smarty.post.descricao_parametro}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Nome do Parâmetro:</td>
					<td><input class="long" type="text" name="nome_parametro" id="nome_parametro" maxlength="20" value="{$smarty.post.nome_parametro}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Valor do Parâmetro:</td>
					<td>
						{*<input class="medium" type="text" name="valor_parametro" id="valor_parametro" maxlength="100" value="{$smarty.post.valor_parametro}"/>*}
						<textarea cols="40" rows="3" name="valor_parametro" id="valor_parametro">{$info.litvalor_parametro}</textarea>
					</td>
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
