{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay} {include file="div_erro.tpl"} {/if}


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
					<th align='center'>Ordem</th>
					<th align='center'>Modulo</th>
					<th align='center'>Sub-Módulo</th>
					<th align='center'>Nome do programa</th>
					<th align='center'>Descrição do programa</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
							<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idprograma={$list[i].idprograma}">{$list[i].ordem_modulo}</a></td>
	        	<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idprograma={$list[i].idprograma}">{$list[i].nome_modulo}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idprograma={$list[i].idprograma}">{$list[i].nome_submodulo}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idprograma={$list[i].idprograma}">{$list[i].nome_programa}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idprograma={$list[i].idprograma}">{$list[i].descricao_programa}</a></td>
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
    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idprograma={$info.idprograma}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				
				<tr>
					<td class="req" align="right">Módulo:</td>
					<td>
						<select name="idmodulo" id="idmodulo" onchange="xajax_Seleciona_Submodulo(xajax.getFormValues('for'));">
						<option value="">[selecione]</option>
						{html_options values=$list_modulo.idmodulo output=$list_modulo.nome_modulo selected=$info.idmodulo}
						</select>
					</td>
				</tr>
		      	<script language="javascript">
		      		xajax_Seleciona_Submodulo(xajax.getFormValues('for'),{$info.numidsubmodulo});
		      	</script>
				<tr>
					<td class="req" align="right">Sub-Módulo:</td>
					<td>
			          <div id="idsubmodulodiv">
							<select name="idsubmodulo" id="idsubmodulo">
								<option value="">[selecione]</option>
							</select>
			          </div>      
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Nome do programa:</td>
					<td><input class="long" type="text" name="litnome_programa" id="litnome_programa" maxlength="50" value="{$info.litnome_programa}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Descrição do programa:</td>
					<td><input class="long" type="text" name="litdescricao_programa" id="litdescricao_programa" maxlength="100" value="{$info.litdescricao_programa}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Nome do arquivo (sem .PHP):</td>
					<td><input class="long" type="text" name="litnome_arquivo" id="litnome_arquivo" maxlength="50" value="{$info.litnome_arquivo}"/></td>
				</tr>
				
				<tr>
					<td align="right">Parâmetros :</td>
					<td><input class="long" type="text" name="litparametros_arquivo" id="litparametros_arquivo" maxlength="100" value="{$info.litparametros_arquivo}"/></td>
				</tr>
				
			
				<tr>
					<td class="req" align="right">Define o Adicionar ?</td>
					<td>
						<input {if $info.litdefine_adicionar=="0"}checked{/if} class="radio" type="radio" name="litdefine_adicionar" id="litdefine_adicionar" value="0" />Não
						<input {if $info.litdefine_adicionar=="1"}checked{/if} class="radio" type="radio" name="litdefine_adicionar" id="litdefine_adicionar" value="1" />Sim
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Define o Editar ?</td>
					<td>
						<input {if $info.litdefine_editar=="0"}checked{/if} class="radio" type="radio" name="litdefine_editar" id="litdefine_editar" value="0" />Não
						<input {if $info.litdefine_editar=="1"}checked{/if} class="radio" type="radio" name="litdefine_editar" id="litdefine_editar" value="1" />Sim
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Define o Excluir ?</td>
					<td>
						<input {if $info.litdefine_excluir=="0"}checked{/if} class="radio" type="radio" name="litdefine_excluir" id="litdefine_excluir" value="0" />Não
						<input {if $info.litdefine_excluir=="1"}checked{/if} class="radio" type="radio" name="litdefine_excluir" id="litdefine_excluir" value="1" />Sim
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Define o Listar ?</td>
					<td>
						<input {if $info.litdefine_listar=="0"}checked{/if} class="radio" type="radio" name="litdefine_listar" id="litdefine_listar" value="0" />Não
						<input {if $info.litdefine_listar=="1"}checked{/if} class="radio" type="radio" name="litdefine_listar" id="litdefine_listar" value="1" />Sim
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Ordem do programa:</td>
					<td>
						<input class="short" type="text" name="numordem_programa" id="numordem_programa" value="{$info.numordem_programa}" maxlength='10' onkeydown="FormataInteiro('numordem_programa')" onkeyup="FormataInteiro('numordem_programa')" />
					</td>
				</tr>
				
				
				

        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="submit" class="botao_padrao" value="ALTERAR">
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&idprograma={$info.idprograma}','ATENÇÃO! Confirma a exclusão ?'))" >
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
					<td class="req" align="right">Módulo:</td>
					<td>
						<select name="idmodulo" id="idmodulo" onchange="xajax_Seleciona_Submodulo(xajax.getFormValues('for'));">
						<option value="">[selecione]</option>
						{html_options values=$list_modulo.idmodulo output=$list_modulo.nome_modulo selected=$smarty.post.idmodulo}
						</select>
					</td>
				</tr>
				<script language="javascript">
      		xajax_Seleciona_Submodulo(xajax.getFormValues('for'),{$smarty.post.idsubmodulo});
      	</script>
				<tr>
					<td class="req" align="right">Sub-Módulo:</td>
					<td>
          				<div id="idsubmodulodiv">
							<select name="idsubmodulo" id="idsubmodulo">
								<option value="">[selecione]</option>
							</select>
          				</div>         
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Nome do programa:</td>
					<td><input class="long" type="text" name="nome_programa" id="nome_programa" maxlength="50" value="{$smarty.post.nome_programa}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Descrição do programa:</td>
					<td><input class="long" type="text" name="descricao_programa" id="descricao_programa" maxlength="100" value="{$smarty.post.descricao_programa}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Nome do arquivo (sem .PHP):</td>
					<td><input class="long" type="text" name="nome_arquivo" id="nome_arquivo" maxlength="50" value="{$smarty.post.nome_arquivo}"/></td>
				</tr>
				
				<tr>
					<td  align="right">Parâmetros :</td>
					<td><input class="long" type="text" name="parametros_arquivo" id="parametros_arquivo" maxlength="100" value="{$smarty.post.parametros_arquivo}"/></td>
				</tr>
								
				<tr>
					<td class="req" align="right">Define o Adicionar ?</td>
					<td>
						<input {if $smarty.post.define_adicionar=="0"}checked{/if} class="radio" type="radio" name="define_adicionar" id="define_adicionar" value="0" />Não
						<input {if $smarty.post.define_adicionar=="1"}checked{/if} class="radio" type="radio" name="define_adicionar" id="define_adicionar" value="1" />Sim
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Define o Editar ?</td>
					<td>
						<input {if $smarty.post.define_editar=="0"}checked{/if} class="radio" type="radio" name="define_editar" id="define_editar" value="0" />Não
						<input {if $smarty.post.define_editar=="1"}checked{/if} class="radio" type="radio" name="define_editar" id="define_editar" value="1" />Sim
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Define o Excluir ?</td>
					<td>
						<input {if $smarty.post.define_excluir=="0"}checked{/if} class="radio" type="radio" name="define_excluir" id="define_excluir" value="0" />Não
						<input {if $smarty.post.define_excluir=="1"}checked{/if} class="radio" type="radio" name="define_excluir" id="define_excluir" value="1" />Sim
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Define o Listar ?</td>
					<td>
						<input {if $smarty.post.define_listar=="0"}checked{/if} class="radio" type="radio" name="define_listar" id="define_listar" value="0" />Não
						<input {if $smarty.post.define_listar=="1"}checked{/if} class="radio" type="radio" name="define_listar" id="define_listar" value="1" />Sim
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Ordem do programa:</td>
					<td>
						<input class="short" type="text" name="ordem_programa" id="ordem_programa" value="{$smarty.post.ordem_programa}" maxlength='10' onkeydown="FormataInteiro('ordem_programa')" onkeyup="FormataInteiro('ordem_programa')" />
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
