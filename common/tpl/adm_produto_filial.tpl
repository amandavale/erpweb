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
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfilial={$list[i].idfilial}&idproduto={$list[i].idproduto}">{$list[i].index}</a></td>
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
    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idfilial={$info.idfilial}&idproduto={$info.idproduto}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				

				
				<tr>
					<td align="right">Filial:</td>
					<td>
						<select name="numidfilial" id="numidfilial">
						<option value="">[selecione]</option>
						{html_options values=$list_filial.idfilial output=$list_filial.nome_filial selected=$info.numidfilial}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">Descrição do produto:</td>
					<td>
						<select name="numidproduto" id="numidproduto">
						<option value="">[selecione]</option>
						{html_options values=$list_produto.idproduto output=$list_produto.descricao_produto selected=$info.numidproduto}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">Quantidade em estoque do produto:</td>
					<td>
						<input class="short" type="text" name="numqtd_produto" id="numqtd_produto" value="{$info.numqtd_produto}" maxlength='10' onkeydown="FormataValor('numqtd_produto')" onkeyup="FormataValor('numqtd_produto')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Preço de balcão (R$):</td>
					<td>
						<input class="short" type="text" name="numpreco_balcao_produto" id="numpreco_balcao_produto" value="{$info.numpreco_balcao_produto}" maxlength='10' onkeydown="FormataValor('numpreco_balcao_produto')" onkeyup="FormataValor('numpreco_balcao_produto')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Preço de oferta (R$):</td>
					<td>
						<input class="short" type="text" name="numpreco_oferta_produto" id="numpreco_oferta_produto" value="{$info.numpreco_oferta_produto}" maxlength='10' onkeydown="FormataValor('numpreco_oferta_produto')" onkeyup="FormataValor('numpreco_oferta_produto')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Preço de atacado (R$):</td>
					<td>
						<input class="short" type="text" name="numpreco_atacado_produto" id="numpreco_atacado_produto" value="{$info.numpreco_atacado_produto}" maxlength='10' onkeydown="FormataValor('numpreco_atacado_produto')" onkeyup="FormataValor('numpreco_atacado_produto')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Preço de telemarketing (R$):</td>
					<td>
						<input class="short" type="text" name="numpreco_telemarketing_produto" id="numpreco_telemarketing_produto" value="{$info.numpreco_telemarketing_produto}" maxlength='10' onkeydown="FormataValor('numpreco_telemarketing_produto')" onkeyup="FormataValor('numpreco_telemarketing_produto')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Preço de custo (R$):</td>
					<td>
						<input class="short" type="text" name="numpreco_custo_produto" id="numpreco_custo_produto" value="{$info.numpreco_custo_produto}" maxlength='10' onkeydown="FormataValor('numpreco_custo_produto')" onkeyup="FormataValor('numpreco_custo_produto')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Produto em oferta ?:</td>
					<td>
						<input {if $info.litproduto_em_oferta=="0"}checked{/if} class="radio" type="radio" name="litproduto_em_oferta" id="litproduto_em_oferta" value="0" />Não
						<input {if $info.litproduto_em_oferta=="1"}checked{/if} class="radio" type="radio" name="litproduto_em_oferta" id="litproduto_em_oferta" value="1" />Sim
					</td>
				</tr>
				
				
				

        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="submit" class="botao_padrao" value="ALTERAR">
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&idfilial={$info.idfilial}&idproduto={$info.idproduto}','ATENÇÃO! Confirma a exclusão ?'))" >
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
					<td align="right">Filial:</td>
					<td>
						<select name="idfilial" id="idfilial">
						<option value="">[selecione]</option>
						{html_options values=$list_filial.idfilial output=$list_filial.nome_filial selected=$smarty.post.idfilial}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">Descrição do produto:</td>
					<td>
						<select name="idproduto" id="idproduto">
						<option value="">[selecione]</option>
						{html_options values=$list_produto.idproduto output=$list_produto.descricao_produto selected=$smarty.post.idproduto}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">Quantidade em estoque do produto:</td>
					<td>
						<input class="short" type="text" name="qtd_produto" id="qtd_produto" value="{$smarty.post.qtd_produto}" maxlength='10' onkeydown="FormataValor('qtd_produto')" onkeyup="FormataValor('qtd_produto')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Preço de balcão (R$):</td>
					<td>
						<input class="short" type="text" name="preco_balcao_produto" id="preco_balcao_produto" value="{$smarty.post.preco_balcao_produto}" maxlength='10' onkeydown="FormataValor('preco_balcao_produto')" onkeyup="FormataValor('preco_balcao_produto')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Preço de oferta (R$):</td>
					<td>
						<input class="short" type="text" name="preco_oferta_produto" id="preco_oferta_produto" value="{$smarty.post.preco_oferta_produto}" maxlength='10' onkeydown="FormataValor('preco_oferta_produto')" onkeyup="FormataValor('preco_oferta_produto')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Preço de atacado (R$):</td>
					<td>
						<input class="short" type="text" name="preco_atacado_produto" id="preco_atacado_produto" value="{$smarty.post.preco_atacado_produto}" maxlength='10' onkeydown="FormataValor('preco_atacado_produto')" onkeyup="FormataValor('preco_atacado_produto')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Preço de telemarketing (R$):</td>
					<td>
						<input class="short" type="text" name="preco_telemarketing_produto" id="preco_telemarketing_produto" value="{$smarty.post.preco_telemarketing_produto}" maxlength='10' onkeydown="FormataValor('preco_telemarketing_produto')" onkeyup="FormataValor('preco_telemarketing_produto')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Preço de custo (R$):</td>
					<td>
						<input class="short" type="text" name="preco_custo_produto" id="preco_custo_produto" value="{$smarty.post.preco_custo_produto}" maxlength='10' onkeydown="FormataValor('preco_custo_produto')" onkeyup="FormataValor('preco_custo_produto')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Produto em oferta ?:</td>
					<td>
						<input {if $smarty.post.produto_em_oferta=="0"}checked{/if} class="radio" type="radio" name="produto_em_oferta" id="produto_em_oferta" value="0" />Não
						<input {if $smarty.post.produto_em_oferta=="1"}checked{/if} class="radio" type="radio" name="produto_em_oferta" id="produto_em_oferta" value="1" />Sim
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
