{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay} {include file="div_erro.tpl"} {/if}

<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>

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
				{if $list_permissao.listar == '1'}
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">listar</a>
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=busca_generica">busca genérica</a>
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=busca_parametrizada">busca parametrizada</a>
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
		<br>
			<table align="center">
				<tr>
					<td align="right">Departamento:</td>
					<td>
						<input type="hidden" name="iddepartamento" id="iddepartamento" value="{$smarty.post.iddepartamento}" />
						<input type="hidden" name="iddepartamento_NomeTemp" id="iddepartamento_NomeTemp" value="{$smarty.post.iddepartamento_NomeTemp}" />
						<input class="long" type="text" name="iddepartamento_Nome" id="iddepartamento_Nome" value="{$smarty.post.iddepartamento_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('iddepartamento', 'idsecao');
							"
						/>
						<span class="nao_selecionou" id="iddepartamento_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("iddepartamento_Nome", function() {ldelim}
				    	return "departamento_ajax.php?ac=busca_departamento&typing=" + this.text.value + "&campoID=iddepartamento";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>

				<tr>
					<td align="right">Seção:</td>
					<td>
						<input type="hidden" name="idsecao" id="idsecao" value="{$smarty.post.idsecao}" />
						<input type="hidden" name="idsecao_NomeTemp" id="idsecao_NomeTemp" value="{$smarty.post.idsecao_NomeTemp}" />
						<input class="long" type="text" name="idsecao_Nome" id="idsecao_Nome" value="{$smarty.post.idsecao_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('idsecao');
							"
						/>
						<span class="nao_selecionou" id="idsecao_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("idsecao_Nome", function() {ldelim}
				    	return "secao_ajax.php?ac=busca_secao&typing=" + this.text.value + "&iddepartamento=" + document.getElementById('iddepartamento').value + "&campoID=idsecao" + "&mostraDetalhes=1";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>

				<script type="text/javascript">
				  // verifica os campos auto-complete
					VerificaMudancaCampo('iddepartamento', 'idsecao');
					VerificaMudancaCampo('idsecao');

				</script>
			</table>
				<tr>
						<td colspan="9" align="center">
			  		  <div id="dados_secao">
							</div>
						</td>
				</tr>



	{elseif $flags.action == "editar"}
	
	<br>

		<table width="100%">
    	<form action="{$smarty.server.PHP_SELF}?ac=editar&idsecao={$info.idsecao}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />

				
				<tr>
					<td align="right" class="req" width="40%">Departamento:</td>
					<td>
						<input type="hidden" name="iddepartamento" id="iddepartamento" value="{$info.iddepartamento}" />
						<input type="hidden" name="iddepartamento_NomeTemp" id="iddepartamento_NomeTemp" value="{$info.iddepartamento_NomeTemp}" />
						<input class="long" type="text" name="iddepartamento_Nome" id="iddepartamento_Nome" value="{$info.iddepartamento_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('iddepartamento', 'idsecao');
							"
						/>
						<span class="nao_selecionou" id="iddepartamento_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("iddepartamento_Nome", function() {ldelim}
				    	return "departamento_ajax.php?ac=busca_departamento&typing=" + this.text.value + "&campoID=iddepartamento";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>
				
				<script type="text/javascript">
				  // verifica os campos auto-complete
					VerificaMudancaCampo('iddepartamento');

				</script>
				
				<tr>
					<td class="req" align="right">Nome da seção:</td>
					<td><input class="long" type="text" name="litnome_secao" id="litnome_secao" maxlength="50" value="{$info.litnome_secao}"/></td>
				</tr>
				

        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="submit" class="botao_padrao" value="ALTERAR">
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&idsecao={$info.idsecao}','ATENÇÃO! Confirma a exclusão ?'))" >
        	</td>
        </tr>

			</form>
		</table>
	      
	      
	{elseif $flags.action == "adicionar"}


		<br>
		<table width="100%">
    	<form action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />

				
				<tr>
					<td align="right" class="req" width="40%">Departamento:</td>
					<td>
						<input type="hidden" name="iddepartamento" id="iddepartamento" value="{$info.iddepartamento}" />
						<input type="hidden" name="iddepartamento_NomeTemp" id="iddepartamento_NomeTemp" value="{$info.iddepartamento_NomeTemp}" />
						<input class="long" type="text" name="iddepartamento_Nome" id="iddepartamento_Nome" value="{$info.iddepartamento_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('iddepartamento');
							"
						/>
						<span class="nao_selecionou" id="iddepartamento_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("iddepartamento_Nome", function() {ldelim}
				    	return "departamento_ajax.php?ac=busca_departamento&typing=" + this.text.value + "&campoID=iddepartamento";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>
				
				<script type="text/javascript">
				  // verifica os campos auto-complete
					VerificaMudancaCampo('iddepartamento');

				</script>
				
				<tr>
					<td class="req" align="right">Nome da seção:</td>
					<td><input class="long" type="text" name="nome_secao" id="nome_secao" maxlength="50" value="{$smarty.post.nome_secao}"/></td>
				</tr>
				
        
        <tr><td>&nbsp;</td></tr>

				<tr>
          <td colspan="2" align="center">
  						<input type='Submit'  class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar" />
          </td>
        </tr>

				</form>
		</table>

	{elseif $flags.action == "busca_generica"}

<br>

		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=busca_generica" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />

				<tr>
				  <td align="right">Busca:</td>
					<td>
						<input class="long" type="text" name="busca" id="busca" maxlength="50" value="{$flags.busca}"/>
					</td>
				</tr>
				<tr>
					<td align="right">Resultados por página:</td>
					<td>
						<input class="tiny" type="text" name="rpp" id="rpp" maxlength="50" value="{$flags.rpp}" onkeydown="FormataInteiro('rpp')" onkeyup="FormataInteiro('rpp')" />
						&nbsp;&nbsp;
						<input name="Submit" type="submit" class="botao_padrao" value="Buscar">
					</td>
				</tr>

        <tr><td>&nbsp;</td></tr>

			</form>
		</table>

		{if count($list)}

			<p align="center">Listando {$conf.area} de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p>

			<table width="95%" align="center">


				<tr>
					<th align='center'>No</th>
					<th align='center'>Departamento</th>
					<th align='center'>Nome da seção</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idsecao={$list[i].idsecao}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idsecao={$list[i].idsecao}">{$list[i].nome_departamento}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idsecao={$list[i].idsecao}">{$list[i].nome_secao}</a></td>
	        </tr>

	        <tr>
	          <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
	        </tr>
	      {/section}

      </table>

      <p align="center" id="nav">{$nav}</p>

			<table width="95%" align="center">
	    	<form action="{$smarty.server.PHP_SELF}?ac=busca_generica&target=full" method="post" name = "for" id = "for" target="_blank">
	      <input type="hidden" name="for_chk" id="for_chk" value="1" />
				<input type="hidden" name="busca" id="busca" value="{$flags.busca}"/>

					<tr>
						<td align="center">
							<input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão">
						</td>
					</tr>

	        <tr><td>&nbsp;</td></tr>

				</form>
			</table>


		{else}
			{if $flags.fez_busca == 1}
      	{include file="div_resultado_nenhum.tpl"}
      {/if}
		{/if}


	{elseif $flags.action == "busca_parametrizada"}

<br>


		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=busca_parametrizada" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />


				<tr>
					<td align="right">Nome da Seção:</td>
					<td><input class="long" type="text" name="nome_secao" id="nome_secao" maxlength="50" value="{$flags.nome_secao}"/></td>
				</tr>
				
				<tr>
					<td align="right">Nome do Departamento:</td>
					<td><input class="long" type="text" name="nome_departamento" id="nome_departamento" maxlength="50" value="{$flags.nome_departamento}"/></td>
				</tr>

				<tr>
					<td align="right">Resultados por página:</td>
					<td>
						<input class="tiny" type="text" name="rpp" id="rpp" maxlength="50" value="{$flags.rpp}" onkeydown="FormataInteiro('rpp')" onkeyup="FormataInteiro('rpp')" />
						&nbsp;&nbsp;
						<input name="Submit" type="submit" class="botao_padrao" value="Buscar">
					</td>
				</tr>

        <tr><td>&nbsp;</td></tr>


			</form>
		</table>


		{if count($list)}

			<p align="center">Listando {$conf.area} de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p>

			<table width="95%" align="center">

				<tr>
					<th align='center'>No</th>
					<th align='center'>Departamento</th>
					<th align='center'>Nome da seção</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idsecao={$list[i].idsecao}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idsecao={$list[i].idsecao}">{$list[i].nome_departamento}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idsecao={$list[i].idsecao}">{$list[i].nome_secao}</a></td>
	        </tr>

	        <tr>
	          <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
	        </tr>
	      {/section}

				<tr><td>&nbsp;</td></tr>


      </table>

      <p align="center" id="nav">{$nav}</p>


			<table width="95%" align="center">
	    	<form action="{$smarty.server.PHP_SELF}?ac=busca_parametrizada&target=full" method="post" name = "for" id = "for" target="_blank">
	      <input type="hidden" name="for_chk" id="for_chk" value="1" />
				<input type="hidden" name="nome_secao" id="nome_secao" value="{$flags.nome_secao}"/>
				<input type="hidden" name="nome_departamento" id="nome_departamento" value="{$flags.nome_departamento}"/>

					<tr>
						<td align="center">
							<input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão">
						</td>
					</tr>

	        <tr><td>&nbsp;</td></tr>

				</form>
			</table>


		{else}
			{if $flags.fez_busca == 1}
      	{include file="div_resultado_nenhum.tpl"}
      {/if}
		{/if}


  {/if}

{/if}

{include file="com_rodape.tpl"}
