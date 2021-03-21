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
					<th align='center'>Ordem de Serviço</th>
					<th align='center'>Material</th>
					<th align='center'>Quantidade</th>
					<th align='center'>Valor Unitário</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmaterial={$list[i].idmaterial}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmaterial={$list[i].idmaterial}">{$list[i].descricao_ordem}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmaterial={$list[i].idmaterial}">{$list[i].material}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmaterial={$list[i].idmaterial}">{$list[i].qtd_material}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmaterial={$list[i].idmaterial}">{$list[i].valor_unitario}</a></td>
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
    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idmaterial={$info.idmaterial}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				

				
				<tr>
					<td class="req" align="right">Ordem de Serviço:</td>
					<td>
						<select name="numidordem_servico" id="numidordem_servico">
						<option value="">[selecione]</option>
						{html_options values=$list_ordem_servico.idordem_servico output=$list_ordem_servico.descricao_ordem selected=$info.numidordem_servico}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">Fornecedor:</td>
					<td>
						<select name="numidfornecedor" id="numidfornecedor">
						<option value="">[selecione]</option>
						{html_options values=$list_fornecedor.idfornecedor output=$list_fornecedor.nome_fornecedor selected=$info.numidfornecedor}
						</select>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Material:</td>
					<td><input class="long" type="text" name="litmaterial" id="litmaterial" maxlength="150" value="{$info.litmaterial}"/></td>
				</tr>
				
				<tr>
					<td align="right">Unidade:</td>
					<td>
						<select name="numidunidade_venda" id="numidunidade_venda">
						<option value="">[selecione]</option>
						{html_options values=$list_unidade_venda.idunidade_venda output=$list_unidade_venda.sigla_unidade_venda selected=$info.numidunidade_venda}
						</select>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Quantidade:</td>
					<td>
						<input class="short" type="text" name="numqtd_material" id="numqtd_material" value="{$info.numqtd_material}" maxlength='10' onkeydown="FormataValor('numqtd_material')" onkeyup="FormataValor('numqtd_material')" />
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Valor Unitário:</td>
					<td>
						<input class="short" type="text" name="numvalor_unitario" id="numvalor_unitario" value="{$info.numvalor_unitario}" maxlength='10' onkeydown="FormataValor('numvalor_unitario')" onkeyup="FormataValor('numvalor_unitario')" />
					</td>
				</tr>
				
				
				

        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="submit" class="botao_padrao" value="ALTERAR">
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&idmaterial={$info.idmaterial}','ATENÇÃO! Confirma a exclusão ?'))" >
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
					<td class="req" align="right">Ordem de Serviço:</td>
					<td>
						<select name="idordem_servico" id="idordem_servico">
						<option value="">[selecione]</option>
						{html_options values=$list_ordem_servico.idordem_servico output=$list_ordem_servico.descricao_ordem selected=$smarty.post.idordem_servico}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">Fornecedor:</td>
					<td>
						<select name="idfornecedor" id="idfornecedor">
						<option value="">[selecione]</option>
						{html_options values=$list_fornecedor.idfornecedor output=$list_fornecedor.nome_fornecedor selected=$smarty.post.idfornecedor}
						</select>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Material:</td>
					<td><input class="long" type="text" name="material" id="material" maxlength="150" value="{$smarty.post.material}"/></td>
				</tr>
				
				<tr>
					<td align="right">Unidade:</td>
					<td>
						<select name="idunidade_venda" id="idunidade_venda">
						<option value="">[selecione]</option>
						{html_options values=$list_unidade_venda.idunidade_venda output=$list_unidade_venda.sigla_unidade_venda selected=$smarty.post.idunidade_venda}
						</select>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Quantidade:</td>
					<td>
						<input class="short" type="text" name="qtd_material" id="qtd_material" value="{$smarty.post.qtd_material}" maxlength='10' onkeydown="FormataValor('qtd_material')" onkeyup="FormataValor('qtd_material')" />
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Valor Unitário:</td>
					<td>
						<input class="short" type="text" name="valor_unitario" id="valor_unitario" value="{$smarty.post.valor_unitario}" maxlength='10' onkeydown="FormataValor('valor_unitario')" onkeyup="FormataValor('valor_unitario')" />
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
