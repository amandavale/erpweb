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
	  	<td class="descricao_tela" WIDTH="15%">
				{$conf.area}
			</td>
	  	<td class="tela" WIDTH="5%">
				Operações:
			</td>
	  	<td class="descricao_tela">
				{if $list_permissao.adicionar == '1'}
	  		&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a>
	  		{/if}
				{if $list_permissao.listar == '1'}
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">listar</a>
				{/if}
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
					<th align='center'>No</th>
					<th align='center'>Sigla</th>
					<th align='center'>Descrição</th>
					<th align='center'>Modo a vista ?</th>
					<th align='center'>Modo a prazo ?</th>
					<th align='center'>Baixa automática ?</th>
					<th align='center'>Dias baixa aut. vista</th>
					<th align='center'>Dias baixa aut. prazo</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&sigla_modo_recebimento={$list[i].sigla_modo_recebimento}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&sigla_modo_recebimento={$list[i].sigla_modo_recebimento}">{$list[i].sigla_modo_recebimento}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&sigla_modo_recebimento={$list[i].sigla_modo_recebimento}">{$list[i].descricao}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&sigla_modo_recebimento={$list[i].sigla_modo_recebimento}">{$list[i].a_vista}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&sigla_modo_recebimento={$list[i].sigla_modo_recebimento}">{$list[i].a_prazo}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&sigla_modo_recebimento={$list[i].sigla_modo_recebimento}">{$list[i].baixa_automatica}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&sigla_modo_recebimento={$list[i].sigla_modo_recebimento}">{$list[i].dias_baixa_automatica_vista}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&sigla_modo_recebimento={$list[i].sigla_modo_recebimento}">{$list[i].dias_baixa_automatica_prazo}</a></td>
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
	

		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&sigla_modo_recebimento={$info.sigla_modo_recebimento}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				

				
				<tr>
					<td class="req" align="right">Sigla do Modo de recebimento:</td>
					<td><input class="tiny" type="text" name="litsigla_modo_recebimento" id="litsigla_modo_recebimento" maxlength="2" value="{$info.litsigla_modo_recebimento}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Descrição do Modo de recebimento:</td>
					<td><input class="long" type="text" name="litdescricao" id="litdescricao" maxlength="30" value="{$info.litdescricao}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Modo a vista ?</td>
					<td>
						<input {if $info.lita_vista=="0"}checked{/if} class="radio" type="radio" name="lita_vista" id="lita_vista" value="0" />Não
						<input {if $info.lita_vista=="1"}checked{/if} class="radio" type="radio" name="lita_vista" id="lita_vista" value="1" />Sim
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Modo a prazo ?</td>
					<td>
						<input {if $info.lita_prazo=="0"}checked{/if} class="radio" type="radio" name="lita_prazo" id="lita_prazo" value="0" />Não
						<input {if $info.lita_prazo=="1"}checked{/if} class="radio" type="radio" name="lita_prazo" id="lita_prazo" value="1" />Sim
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Baixa automática ?</td>
					<td>
						<input {if $info.litbaixa_automatica=="0"}checked{/if} class="radio" type="radio" name="litbaixa_automatica" id="litbaixa_automatica" value="0" />Não
						<input {if $info.litbaixa_automatica=="1"}checked{/if} class="radio" type="radio" name="litbaixa_automatica" id="litbaixa_automatica" value="1" />Sim
					</td>
				</tr>
				
				<tr>
					<td align="right">Dias para a baixa automática a vista:</td>
					<td>
						<input class="short" type="text" name="numdias_baixa_automatica_vista" id="numdias_baixa_automatica_vista" value="{$info.numdias_baixa_automatica_vista}" maxlength='10' onkeydown="FormataInteiro('numdias_baixa_automatica_vista')" onkeyup="FormataInteiro('numdias_baixa_automatica_vista')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Dias para a baixa automática a prazo:</td>
					<td>
						<input class="short" type="text" name="numdias_baixa_automatica_prazo" id="numdias_baixa_automatica_prazo" value="{$info.numdias_baixa_automatica_prazo}" maxlength='10' onkeydown="FormataInteiro('numdias_baixa_automatica_prazo')" onkeyup="FormataInteiro('numdias_baixa_automatica_prazo')" />
					</td>
				</tr>
				
				

        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="submit" class="botao_padrao" value="ALTERAR">
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&sigla_modo_recebimento={$info.sigla_modo_recebimento}','ATENÇÃO! Confirma a exclusão ?'))" >
        	</td>
        </tr>

			</form>
		</table>
	      
	      
	{elseif $flags.action == "adicionar"}

		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			
			
				<tr>
					<td class="req" align="right">Sigla do Modo de recebimento:</td>
					<td><input class="tiny" type="text" name="sigla_modo_recebimento" id="sigla_modo_recebimento" maxlength="2" value="{$smarty.post.sigla_modo_recebimento}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Descrição do Modo de recebimento:</td>
					<td><input class="long" type="text" name="descricao" id="descricao" maxlength="30" value="{$smarty.post.descricao}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Modo a vista ?</td>
					<td>
						<input {if $smarty.post.a_vista=="0"}checked{/if} class="radio" type="radio" name="a_vista" id="a_vista" value="0" />Não
						<input {if $smarty.post.a_vista=="1"}checked{/if} class="radio" type="radio" name="a_vista" id="a_vista" value="1" />Sim
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Modo a prazo ?</td>
					<td>
						<input {if $smarty.post.a_prazo=="0"}checked{/if} class="radio" type="radio" name="a_prazo" id="a_prazo" value="0" />Não
						<input {if $smarty.post.a_prazo=="1"}checked{/if} class="radio" type="radio" name="a_prazo" id="a_prazo" value="1" />Sim
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Baixa automática ?</td>
					<td>
						<input {if $smarty.post.baixa_automatica=="0"}checked{/if} class="radio" type="radio" name="baixa_automatica" id="baixa_automatica" value="0" />Não
						<input {if $smarty.post.baixa_automatica=="1"}checked{/if} class="radio" type="radio" name="baixa_automatica" id="baixa_automatica" value="1" />Sim
					</td>
				</tr>
				
				<tr>
					<td align="right">Dias para a baixa automática a vista:</td>
					<td>
						<input class="short" type="text" name="dias_baixa_automatica_vista" id="dias_baixa_automatica_vista" value="{$smarty.post.dias_baixa_automatica_vista}" maxlength='10' onkeydown="FormataInteiro('dias_baixa_automatica_vista')" onkeyup="FormataInteiro('dias_baixa_automatica_vista')" />
					</td>
				</tr>

				<tr>
					<td align="right">Dias para a baixa automática a prazo:</td>
					<td>
						<input class="short" type="text" name="dias_baixa_automatica_prazo" id="dias_baixa_automatica_prazo" value="{$smarty.post.dias_baixa_automatica_prazo}" maxlength='10' onkeydown="FormataInteiro('dias_baixa_automatica_prazo')" onkeyup="FormataInteiro('dias_baixa_automatica_prazo')" />
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
