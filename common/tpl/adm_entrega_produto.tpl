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
					<th align='center'>Entrega</th>
					<th align='center'>Produto</th>
					<th align='center'>Quantidade</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&identrega={$list[i].identrega}&idproduto={$list[i].idproduto}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&identrega={$list[i].identrega}&idproduto={$list[i].idproduto}">{$list[i].identrega}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&identrega={$list[i].identrega}&idproduto={$list[i].idproduto}">{$list[i].descricao_produto}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&identrega={$list[i].identrega}&idproduto={$list[i].idproduto}">{$list[i].qtd}</a></td>
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
    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&identrega={$info.identrega}&idproduto={$info.idproduto}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				

				
				<tr>
					<td class="req" align="right">Entrega:</td>
					<td>
						<select name="numidentrega" id="numidentrega">
						<option value="">[selecione]</option>
						{html_options values=$list_entrega.identrega output=$list_entrega.identrega selected=$info.numidentrega}
						</select>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Produto:</td>
					<td>
						<select name="numidproduto" id="numidproduto">
						<option value="">[selecione]</option>
						{html_options values=$list_produto.idproduto output=$list_produto.descricao_produto selected=$info.numidproduto}
						</select>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Quantidade:</td>
					<td><input class="" type="text" name="numqtd" id="numqtd" value="{$info.numqtd}"/></td>
				</tr>
				
				
				

        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="submit" class="botao_padrao" value="ALTERAR">
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&identrega={$info.identrega}&idproduto={$info.idproduto}','ATENÇÃO! Confirma a exclusão ?'))" >
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
					<td class="req" align="right">Entrega:</td>
					<td>
						<select name="identrega" id="identrega">
						<option value="">[selecione]</option>
						{html_options values=$list_entrega.identrega output=$list_entrega.identrega selected=$smarty.post.identrega}
						</select>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Produto:</td>
					<td>
						<select name="idproduto" id="idproduto">
						<option value="">[selecione]</option>
						{html_options values=$list_produto.idproduto output=$list_produto.descricao_produto selected=$smarty.post.idproduto}
						</select>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Quantidade:</td>
					<td><input class="" type="text" name="qtd" id="qtd" value="{$smarty.post.qtd}"/></td>
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
