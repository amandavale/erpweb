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
	  	<td class="descricao_tela" WIDTH="10%">
				{$conf.area}
			</td>
	  	<td class="tela" WIDTH="5%">
				Operações:
			</td>
	  	<td class="descricao_tela">
				{if $list_permissao.adicionar == '1'}&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a>{/if}
				{if $list_permissao.listar == '1'}&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">listar</a>{/if}
			</td>
		</tr>
	</table>

	
	{include file="div_erro.tpl"}

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
					<th align='center'>Ordem do módulo</th>
					<th align='center'>Nome do módulo</th>
					<th align='center'>Descrição do módulo</th>
					
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmodulo={$list[i].idmodulo}">{$list[i].ordem_modulo}</a></td>						
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmodulo={$list[i].idmodulo}">{$list[i].nome_modulo}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmodulo={$list[i].idmodulo}">{$list[i].descricao_modulo}</a></td>

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
	
<br>

		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idmodulo={$info.idmodulo}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				

				
				<tr>
					<td class="req" align="right">Nome do módulo:</td>
					<td><input class="long" type="text" name="litnome_modulo" id="litnome_modulo" maxlength="50" value="{$info.litnome_modulo}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Descrição do módulo:</td>
					<td><input class="long" type="text" name="litdescricao_modulo" id="litdescricao_modulo" maxlength="100" value="{$info.litdescricao_modulo}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Ordem do módulo:</td>
					<td>
						<input class="short" type="text" name="numordem_modulo" id="numordem_modulo" value="{$info.numordem_modulo}" maxlength='10' onkeydown="FormataInteiro('numordem_modulo')" onkeyup="FormataInteiro('numordem_modulo')" />
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Largura do menu módulo:</td>
					<td>
						<input class="short" type="text" name="numlargura_menu_modulo" id="numlargura_menu_modulo" value="{$info.numlargura_menu_modulo}" maxlength='10' onkeydown="FormataInteiro('numlargura_menu_modulo')" onkeyup="FormataInteiro('numlargura_menu_modulo')" />
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Largura do menu sub-módulo:</td>
					<td>
						<input class="short" type="text" name="numlargura_menu_submodulo" id="numlargura_menu_submodulo" value="{$info.numlargura_menu_submodulo}" maxlength='10' onkeydown="FormataInteiro('numlargura_menu_submodulo')" onkeyup="FormataInteiro('numlargura_menu_submodulo')" />
					</td>
				</tr>
				
				
				

        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="submit" class="botao_padrao" value="ALTERAR">
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&idmodulo={$info.idmodulo}','ATENÇÃO! Confirma a exclusão ?'))" >
        	</td>
        </tr>

			</form>
		</table>
	      
	      
	{elseif $flags.action == "adicionar"}

<br>

		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				
				<tr>
					<td class="req" align="right">Nome do módulo:</td>
					<td><input class="long" type="text" name="nome_modulo" id="nome_modulo" maxlength="50" value="{$smarty.post.nome_modulo}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Descrição do módulo:</td>
					<td><input class="long" type="text" name="descricao_modulo" id="descricao_modulo" maxlength="100" value="{$smarty.post.descricao_modulo}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Ordem do módulo:</td>
					<td>
						<input class="short" type="text" name="ordem_modulo" id="ordem_modulo" value="{$smarty.post.ordem_modulo}" maxlength='10' onkeydown="FormataInteiro('ordem_modulo')" onkeyup="FormataInteiro('ordem_modulo')" />
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Largura do menu módulo:</td>
					<td>
						<input class="short" type="text" name="largura_menu_modulo" id="largura_menu_modulo" value="{$smarty.post.largura_menu_modulo}" maxlength='10' onkeydown="FormataInteiro('largura_menu_modulo')" onkeyup="FormataInteiro('largura_menu_modulo')" />
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Largura do menu sub-módulo:</td>
					<td>
						<input class="short" type="text" name="largura_menu_submodulo" id="largura_menu_submodulo" value="{$smarty.post.largura_menu_submodulo}" maxlength='10' onkeydown="FormataInteiro('largura_menu_submodulo')" onkeyup="FormataInteiro('largura_menu_submodulo')" />
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
