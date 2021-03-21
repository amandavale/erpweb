{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{include file="div_erro.tpl"}


{if $flags.okay}

	<table class="tb4cantosAzul" width="100%"  border="0" cellpadding="5" cellspacing="0">

		<tr>
	  	<td bgcolor="#F7F7F7" WIDTH="100%" height="20">
				{$conf.area}
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
					<th align='center'>Fornecedor</th>
					<th align='center'>Banco</th>
					<th align='center'>Agência</th>
					<th align='center'>Conta</th>
					<th align='center'>Conta principal ?</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_fornecedor={$list[i].idconta_fornecedor}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_fornecedor={$list[i].idconta_fornecedor}">{$list[i].nome_fornecedor}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_fornecedor={$list[i].idconta_fornecedor}">{$list[i].nome_banco}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_fornecedor={$list[i].idconta_fornecedor}">{$list[i].agencia_fornecedor}-{$list[i].agencia_dig_fornecedor}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_fornecedor={$list[i].idconta_fornecedor}">{$list[i].conta_fornecedor}-{$list[i].conta_dig_fornecedor}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_fornecedor={$list[i].idconta_fornecedor}">{$list[i].principal_fornecedor}</a></td>
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
    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idconta_fornecedor={$info.idconta_fornecedor}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				

				
				<tr>
					<td class="req" align="right">Fornecedor:</td>
					<td>
						<select name="numidfornecedor" id="numidfornecedor">
						<option value="">[selecione]</option>
						{html_options values=$list_fornecedor.idfornecedor output=$list_fornecedor.nome_fornecedor selected=$info.numidfornecedor}
						</select>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Banco:</td>
					<td>
						<select name="numidbanco" id="numidbanco">
						<option value="">[selecione]</option>
						{html_options values=$list_banco.idbanco output=$list_banco.nome_banco selected=$info.numidbanco}
						</select>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Agência:</td>
					<td>
						<input class="medium" type="text" name="litagencia_fornecedor" id="litagencia_fornecedor" maxlength="12" value="{$info.litagencia_fornecedor}"/>
						-
						<input class="tiny" type="text" name="litagencia_dig_fornecedor" id="litagencia_dig_fornecedor" maxlength="2" value="{$info.litagencia_dig_fornecedor}"/>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Conta:</td>
					<td>
						<input class="medium" type="text" name="litconta_fornecedor" id="litconta_fornecedor" maxlength="12" value="{$info.litconta_fornecedor}"/>
						-
						<input class="tiny" type="text" name="litconta_dig_fornecedor" id="litconta_dig_fornecedor" maxlength="2" value="{$info.litconta_dig_fornecedor}"/>
					</td>
				</tr>
				
				
				<tr>
					<td align="right">Conta principal ?</td>
					<td>
						<input {if $info.litprincipal_fornecedor=="0"}checked{/if} class="radio" type="radio" name="litprincipal_fornecedor" id="litprincipal_fornecedor" value="0" />Não
						<input {if $info.litprincipal_fornecedor=="1"}checked{/if} class="radio" type="radio" name="litprincipal_fornecedor" id="litprincipal_fornecedor" value="1" />Sim
					</td>
				</tr>
				
				
				

        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="submit" class="botao_padrao" value="ALTERAR">
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&idconta_fornecedor={$info.idconta_fornecedor}','ATENÇÃO! Confirma a exclusão ?'))" >
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
					<td class="req" align="right">Fornecedor:</td>
					<td>
						<select name="idfornecedor" id="idfornecedor">
						<option value="">[selecione]</option>
						{html_options values=$list_fornecedor.idfornecedor output=$list_fornecedor.nome_fornecedor selected=$smarty.post.idfornecedor}
						</select>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Banco:</td>
					<td>
						<select name="idbanco" id="idbanco">
						<option value="">[selecione]</option>
						{html_options values=$list_banco.idbanco output=$list_banco.nome_banco selected=$smarty.post.idbanco}
						</select>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Agência:</td>
					<td>
						<input class="medium" type="text" name="agencia_fornecedor" id="agencia_fornecedor" maxlength="12" value="{$smarty.post.agencia_fornecedor}"/>
						-
						<input class="tiny" type="text" name="agencia_dig_fornecedor" id="agencia_dig_fornecedor" maxlength="2" value="{$smarty.post.agencia_dig_fornecedor}"/>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Conta:</td>
					<td>
						<input class="medium" type="text" name="conta_fornecedor" id="conta_fornecedor" maxlength="12" value="{$smarty.post.conta_fornecedor}"/>
						-
						<input class="tiny" type="text" name="conta_dig_fornecedor" id="conta_dig_fornecedor" maxlength="2" value="{$smarty.post.conta_dig_fornecedor}"/>
					</td>
				</tr>

				<tr>
					<td align="right">Conta principal ?</td>
					<td>
						<input {if $smarty.post.principal_fornecedor=="0"}checked{/if} class="radio" type="radio" name="principal_fornecedor" id="principal_fornecedor" value="0" />Não
						<input {if $smarty.post.principal_fornecedor=="1"}checked{/if} class="radio" type="radio" name="principal_fornecedor" id="principal_fornecedor" value="1" />Sim
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
