{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}

<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>

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
				Operações:
			</td>
	  	<td class="descricao_tela">
				{if $list_permissao.adicionar == '1'}
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a>
				{/if}
				{if $list_permissao.listar == '1'}
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">busca</a>
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=busca_generica">busca genérica</a>
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


<table align="center">
			<tr>
				<td colspan="9" align="center">Estado:
					<input type="hidden" name="idestado" id="idestado" value="" />
					<input type="hidden" name="idestado_NomeTemp" id="idestado_NomeTemp" value="" />
					<input class="long" type="text" name="idestado_Nome" id="idestado_Nome" value=""
						onKeyUp="javascript:
							VerificaMudancaCampo('idestado', 'idcidade');
						"
					/>
					<span class="nao_selecionou" id="idestado_Flag">
						&nbsp;&nbsp;&nbsp;
					</span>
				</td>
			</tr>
			<script type="text/javascript">
			    new CAPXOUS.AutoComplete("idestado_Nome", function() {ldelim}
			    	return "estado_ajax.php?ac=busca_estado&typing=" + this.text.value + "&campoID=idestado";
			    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
			</script>


			<tr>
				<td colspan="9" align="center">Cidade:
					<input type="hidden" name="idcidade" id="idcidade" value="" />
					<input type="hidden" name="idcidade_NomeTemp" id="idcidade_NomeTemp" value="" />
					<input class="long" type="text" name="idcidade_Nome" id="idcidade_Nome" value=""
						onKeyUp="javascript:
							VerificaMudancaCampo('idcidade');
						"
					/>
					<span class="nao_selecionou" id="idcidade_Flag">
						&nbsp;&nbsp;&nbsp;
					</span>
				</td>
			</tr>
		</table>
			<tr>
				<td colspan="9" align="center">
				  <div id="dados_cidade">
					</div>
				</td>
			</tr>


			<script type="text/javascript">
			    new CAPXOUS.AutoComplete("idcidade_Nome", function() {ldelim}
			    	return "cidade_ajax.php?ac=busca_cidade&typing=" + this.text.value + "&campoID=idcidade" + "&idestado=" + document.getElementById('idestado').value + "&mostraDetalhes=1";
			    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
			</script>


			<script type="text/javascript">
			  // verifica os campos auto-complete
				VerificaMudancaCampo('idestado', 'idcidade');
				VerificaMudancaCampo('idcidade');
			</script>






	{elseif $flags.action == "busca_generica"}

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
					<th align='center'>Estado</th>
					<th align='center'>Nome da cidade</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcidade={$list[i].idcidade}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcidade={$list[i].idcidade}">{$list[i].nome_estado}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcidade={$list[i].idcidade}">{$list[i].nome_cidade}</a></td>
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

	        <tr><td>&nbsp;</td></tr>

				</form>
			</table>


		{else}
			{if $flags.fez_busca == 1}
      	{include file="div_resultado_nenhum.tpl"}
      {/if}
		{/if}


		

	{elseif $flags.action == "editar"}
	
		{include file="div_instrucoes_inicio.tpl"}
      	<li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>
				
    {include file="div_instrucoes_fim.tpl"}

		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idcidade={$info.idcidade}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			
				<tr>
					<td class="req" align="right">Estado:</td>
					<td>
						<input type="hidden" name="numidestado" id="idestado" value="{$info.numidestado}" />
						<input type="hidden" name="idestado_NomeTemp" id="idestado_NomeTemp" value="{$info.idestado_NomeTemp}" />
						<input class="long" type="text" name="idestado_Nome" id="idestado_Nome" value="{$info.idestado_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('idestado');
							"
						/>
						<span class="selecionou" id="idestado_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("idestado_Nome", function() {ldelim}
				    	return "estado_ajax.php?ac=busca_estado&typing=" + this.text.value + "&campoID=idestado";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<script type="text/javascript">
				  // verifica os campos auto-complete
					VerificaMudancaCampo('idestado');
				</script>

				
				<tr>
					<td class="req" align="right">Nome da cidade:</td>
					<td><input class="long" type="text" name="litnome_cidade" id="litnome_cidade" maxlength="100" value="{$info.litnome_cidade}"/></td>
				</tr>
				
				
				

        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="submit" class="botao_padrao" value="ALTERAR">
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&idcidade={$info.idcidade}','ATENÇÃO! Confirma a exclusão ?'))" >
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
					<td class="req" align="right">Estado:</td>
					<td>
						<input type="hidden" name="idestado" id="idestado" value="{$smarty.post.idestado}" />
						<input type="hidden" name="idestado_NomeTemp" id="idestado_NomeTemp" value="{$smarty.post.idestado_NomeTemp}" />
						<input class="long" type="text" name="idestado_Nome" id="idestado_Nome" value="{$smarty.post.idestado_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('idestado');
							"
						/>
						<span class="nao_selecionou" id="idestado_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("idestado_Nome", function() {ldelim}
				    	return "estado_ajax.php?ac=busca_estado&typing=" + this.text.value + "&campoID=idestado";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<script type="text/javascript">
				  // verifica os campos auto-complete
					VerificaMudancaCampo('idestado');
				</script>

				
				<tr>
					<td class="req" align="right">Nome da cidade:</td>
					<td><input class="long" type="text" name="nome_cidade" id="nome_cidade" maxlength="100" value="{$smarty.post.nome_cidade}"/></td>
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
