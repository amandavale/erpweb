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
					<th align='center'>Logradouro</th>
					<th align='center'>Nº</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idendereco={$list[i].idendereco}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idendereco={$list[i].idendereco}">{$list[i].logradouro}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idendereco={$list[i].idendereco}">{$list[i].numero}</a></td>
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
    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idendereco={$info.idendereco}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				

				
				<tr>
					<td align="right">Logradouro:</td>
					<td><input class="long" type="text" name="litlogradouro" id="litlogradouro" maxlength="100" value="{$info.litlogradouro}"/></td>
				</tr>
				
				<tr>
					<td align="right">Nº:</td>
					<td><input class="short" type="text" name="litnumero" id="litnumero" maxlength="10" value="{$info.litnumero}"/></td>
				</tr>
				
				<tr>
					<td align="right">Complemento:</td>
					<td><input class="medium" type="text" name="litcomplemento" id="litcomplemento" maxlength="50" value="{$info.litcomplemento}"/></td>
				</tr>
				
				<tr>
					<td align="right">Bairro:</td>
					<td>
						<select name="numidbairro" id="numidbairro">
						<option value="">[selecione]</option>
						{html_options values=$list_bairro.idbairro output=$list_bairro.nome_bairro selected=$info.numidbairro}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">Cidade:</td>
					<td><input class="long" type="text" name="litcidade" id="litcidade" maxlength="100" value="{$info.litcidade}"/></td>
				</tr>
				
				<tr>
					<td align="right">Estado:</td>
					<td>
						<select name="numidestado" id="numidestado">
						<option value="">[selecione]</option>
						{html_options values=$list_estado.idestado output=$list_estado.nome_estado selected=$info.numidestado}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">CEP:</td>
					<td>
						<input class="short" type="text" name="litcep" id="litcep" value="{$info.litcep}" maxlength='10' onkeydown="mask('litcep', 'cep')" onkeyup="mask('litcep', 'cep')" />
					</td>
				</tr>
				
				
				

        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="submit" class="botao_padrao" value="ALTERAR">
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&idendereco={$info.idendereco}','ATENÇÃO! Confirma a exclusão ?'))" >
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
					<td align="right">Logradouro:</td>
					<td><input class="long" type="text" name="logradouro" id="logradouro" maxlength="100" value="{$smarty.post.logradouro}"/></td>
				</tr>
				
				<tr>
					<td align="right">Nº:</td>
					<td><input class="short" type="text" name="numero" id="numero" maxlength="10" value="{$smarty.post.numero}"/></td>
				</tr>
				
				<tr>
					<td align="right">Complemento:</td>
					<td><input class="medium" type="text" name="complemento" id="complemento" maxlength="50" value="{$smarty.post.complemento}"/></td>
				</tr>
				
				<tr>
					<td align="right">Bairro:</td>
					<td>
						<select name="idbairro" id="idbairro">
						<option value="">[selecione]</option>
						{html_options values=$list_bairro.idbairro output=$list_bairro.nome_bairro selected=$smarty.post.idbairro}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">Cidade:</td>
					<td><input class="long" type="text" name="cidade" id="cidade" maxlength="100" value="{$smarty.post.cidade}"/></td>
				</tr>
				
				<tr>
					<td align="right">Estado:</td>
					<td>
						<select name="idestado" id="idestado">
						<option value="">[selecione]</option>
						{html_options values=$list_estado.idestado output=$list_estado.nome_estado selected=$smarty.post.idestado}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">CEP:</td>
					<td>
						<input class="short" type="text" name="cep" id="cep" value="{$smarty.post.cep}" maxlength='10' onkeydown="mask('cep', 'cep')" onkeyup="mask('cep', 'cep')" />
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
