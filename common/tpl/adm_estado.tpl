{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}


{if $flags.okay}


	<table class="tb4cantosAzul" width="100%"  border="0" cellpadding="5" cellspacing="0">
		<tr>
	  	<td class="tela" WIDTH="5%" height="20">
				Tela:
			</td>
	  	<td class="descricao_tela" WIDTH="10%">
				{$conf.area}
			</td>
	  	<td class="tela" WIDTH="5%">
				Opera��es:
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
					<th align='center'>Nome do estado</th>
					<th align='center'>Sigla do estado</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idestado={$list[i].idestado}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idestado={$list[i].idestado}">{$list[i].nome_estado}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idestado={$list[i].idestado}">{$list[i].sigla_estado}</a></td>
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
    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idestado={$info.idestado}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				<tr>
					<td class="req" align="right">Nome do estado:</td>
					<td><input class="long" type="text" name="litnome_estado" id="litnome_estado" maxlength="50" value="{$info.litnome_estado}"/></td>
				</tr>

				<tr>
					<td class="req" align="right">Sigla do estado:</td>
					<td><input class="tiny" type="text" name="litsigla_estado" id="litsigla_estado" maxlength="2" value="{$info.litsigla_estado}"/></td>
				</tr>


        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="submit" class="botao_padrao" value="ALTERAR">
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&idestado={$info.idestado}','ATEN��O! Confirma a exclus�o ?'))" >
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
					<td class="req" align="right">Nome do estado:</td>
					<td><input class="long" type="text" name="nome_estado" id="nome_estado" maxlength="50" value="{$smarty.post.nome_estado}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Sigla do estado:</td>
					<td><input class="tiny" type="text" name="sigla_estado" id="sigla_estado" maxlength="2" value="{$smarty.post.sigla_estado}"/></td>
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
