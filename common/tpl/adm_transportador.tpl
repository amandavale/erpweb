{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay} {include file="div_erro.tpl"} {/if}

<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/tabs.js"></script>

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
					<th align='center'>Tipo do transportador</th>
					<th align='center'>Nome do transportador</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idtransportador={$list[i].idtransportador}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idtransportador={$list[i].idtransportador}">{$list[i].tipo_transportador}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idtransportador={$list[i].idtransportador}">{$list[i].nome_transportador}</a></td>
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
					<th align='center'>Tipo do transportador</th>
					<th align='center'>Nome do transportador</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idtransportador={$list[i].idtransportador}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idtransportador={$list[i].idtransportador}">{$list[i].tipo_transportador}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idtransportador={$list[i].idtransportador}">{$list[i].nome_transportador}</a></td>
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
		
		

	{elseif $flags.action == "editar"}
	
		<br>

		<div style="width: 100%;">

  	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idtransportador={$info.idtransportador}" method="post" name = "for_transportador" id = "for_transportador">
    <input type="hidden" name="for_chk" id="for_chk" value="1" />
		<input type="hidden" name="idendereco_transportador" id="idendereco_transportador" value="{$info.idendereco_transportador}" />

		  <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados do Transportador</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Endereço do Transportador</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Observação</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

			<div id="tab_0" class="anchor">

			<table width="95%" align="center">

				<tr>
					<td width="30%" class="req" align="right">Tipo do transportador:</td>
					<td>
						<input {if $info.littipo_transportador=="F"}checked{/if} class="radio" type="radio" name="littipo_transportador" id="littipo_transportador" value="F" />Pessoa Física
						<input {if $info.littipo_transportador=="J"}checked{/if} class="radio" type="radio" name="littipo_transportador" id="littipo_transportador" value="J" />Pessoa Jurídica
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Nome do transportador:</td>
					<td><input class="long" type="text" name="litnome_transportador" id="litnome_transportador" maxlength="100" value="{$info.litnome_transportador}"/></td>
				</tr>
				
				<tr>
					<td align="right">CPF / CNPJ:</td>
					<td><input class="long" type="text" name="litcpf_cnpj" id="litcpf_cnpj" maxlength="20" value="{$info.litcpf_cnpj}"/></td>
				</tr>
				
				<tr>
					<td align="right">Inscrição estadual:</td>
					<td><input class="long" type="text" name="litinstricao_estadual_transportador" id="litinstricao_estadual_transportador" maxlength="30" value="{$info.litinstricao_estadual_transportador}"/></td>
				</tr>

				<tr>
					<td align="right">Telefone:</td>
					<td>
						<input class="tiny" type="text" name="telefone_transportador_ddd" id="telefone_transportador_ddd" value="{$info.telefone_transportador_ddd}" maxlength='2' />
						<input class="short" type="text" name="littelefone_transportador" id="littelefone_transportador" value="{$info.littelefone_transportador}" maxlength='9'onkeydown="mask('littelefone_transportador', 'tel')" onkeyup="mask('littelefone_transportador', 'tel')" />
					</td>
				</tr>

				<tr>
					<td align="right">Fax:</td>
					<td>
						<input class="tiny" type="text" name="fax_transportador_ddd" id="fax_transportador_ddd" value="{$info.fax_transportador_ddd}" maxlength='2' />
						<input class="short" type="text" name="litfax_transportador" id="litfax_transportador" value="{$info.litfax_transportador}" maxlength='9'onkeydown="mask('litfax_transportador', 'tel')" onkeyup="mask('litfax_transportador', 'tel')" />
					</td>
				</tr>

				<tr>
					<td align="right">Email:</td>
					<td><input class="long" type="text" name="litemail_transportador" id="litemail_transportador" maxlength="100" value="{$info.litemail_transportador}"/></td>
				</tr>

				<tr>
					<td align="right">Site:</td>
					<td><input class="long" type="text" name="litsite_transportador" id="litsite_transportador" maxlength="100" value="{$info.litsite_transportador}"/></td>
				</tr>

			</table>

			</div>


			{************************************}
			{* TAB 1 *}
			{************************************}

			<div id="tab_1" class="anchor">

			<table width="95%" align="center">

				<tr>
					<td width="30%" align="right">Logradouro:</td>
					<td><input class="long" type="text" name="transportador_logradouro" id="transportador_logradouro" maxlength="100" value="{$info.transportador_logradouro}"/></td>
				</tr>

				<tr>
					<td align="right">Nº:</td>
					<td><input class="short" type="text" name="transportador_numero" id="transportador_numero" maxlength="10" value="{$info.transportador_numero}"/></td>
				</tr>

				<tr>
					<td align="right">Complemento:</td>
					<td><input class="medium" type="text" name="transportador_complemento" id="transportador_complemento" maxlength="50" value="{$info.transportador_complemento}"/></td>
				</tr>


				<tr>
					<td align="right">Estado:</td>
					<td>
						<input type="hidden" name="transportador_idestado" id="transportador_idestado" value="{$info.transportador_idestado}" />
						<input type="hidden" name="transportador_idestado_NomeTemp" id="transportador_idestado_NomeTemp" value="{$info.transportador_idestado_NomeTemp}" />
						<input class="long" type="text" name="transportador_idestado_Nome" id="transportador_idestado_Nome" value="{$info.transportador_idestado_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('transportador_idestado', 'transportador_idcidade#transportador_idbairro');
							"
						/>
						<span class="nao_selecionou" id="transportador_idestado_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("transportador_idestado_Nome", function() {ldelim}
				    	return "estado_ajax.php?ac=busca_estado&typing=" + this.text.value + "&campoID=transportador_idestado";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<tr>
					<td align="right">Cidade:</td>
					<td>
						<input type="hidden" name="transportador_idcidade" id="transportador_idcidade" value="{$info.transportador_idcidade}" />
						<input type="hidden" name="transportador_idcidade_NomeTemp" id="transportador_idcidade_NomeTemp" value="{$info.transportador_idcidade_NomeTemp}" />
						<input class="long" type="text" name="transportador_idcidade_Nome" id="transportador_idcidade_Nome" value="{$info.transportador_idcidade_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('transportador_idcidade','transportador_idbairro');
							"
						/>
						<span class="nao_selecionou" id="transportador_idcidade_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("transportador_idcidade_Nome", function() {ldelim}
				    	return "cidade_ajax.php?ac=busca_cidade&typing=" + this.text.value + "&campoID=transportador_idcidade" + "&idestado=" + document.getElementById('transportador_idestado').value;
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<tr>
					<td align="right">Bairro:</td>
					<td>
						<input type="hidden" name="transportador_idbairro" id="transportador_idbairro" value="{$info.transportador_idbairro}" />
						<input type="hidden" name="transportador_idbairro_NomeTemp" id="transportador_idbairro_NomeTemp" value="{$info.transportador_idbairro_NomeTemp}" />
						<input class="long" type="text" name="transportador_idbairro_Nome" id="transportador_idbairro_Nome" value="{$info.transportador_idbairro_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('transportador_idbairro');
							"
						/>
						<span class="nao_selecionou" id="transportador_idbairro_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("transportador_idbairro_Nome", function() {ldelim}
				    	return "bairro_ajax.php?ac=busca_bairro&typing=" + this.text.value + "&campoID=transportador_idbairro" + "&idcidade=" + document.getElementById('transportador_idcidade').value;
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<script type="text/javascript">
				  // verifica os campos auto-complete
					VerificaMudancaCampo('transportador_idestado', 'transportador_idcidade#transportador_idbairro');
					VerificaMudancaCampo('transportador_idcidade','transportador_idbairro');
					VerificaMudancaCampo('transportador_idbairro');
				</script>


				<tr>
					<td align="right">CEP:</td>
					<td>
						<input class="short" type="text" name="transportador_cep" id="transportador_cep" value="{$info.transportador_cep}" maxlength='10' onkeydown="mask('transportador_cep', 'cep')" onkeyup="mask('transportador_cep', 'cep')" />
					</td>
				</tr>


			</table>

			</div>


			{************************************}
			{* TAB 2 *}
			{************************************}

			<div id="tab_2" class="anchor">

			<table width="95%" align="center">

				<tr>
					<td width="30%" align="right">Observação:</td>
					<td>
						<textarea name="litobservacao_transportador" id="litobservacao_transportador" rows='6' cols='38'>{$info.litobservacao_transportador}</textarea>
					</td>
				</tr>
				
			</table>

			</div>

			<script language="javascript">
				Processa_Tabs(0, 'tab_'); // seta o tab inicial
			</script>

			<table width="95%" align="center">

       	<tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
						<input type='button' class="botao_padrao" value="ALTERAR" name = "ALTERAR" id = "ALTERAR"
							onClick="xajax_Verifica_Campos_Transportador_AJAX(xajax.getFormValues('for_transportador'));"
						/>
						&nbsp;&nbsp;&nbsp;
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for_transportador','{$smarty.server.PHP_SELF}?ac=excluir&idtransportador={$info.idtransportador}','ATENÇÃO! Confirma a exclusão ?'))" >
        	</td>
        </tr>

			</table>

		</form>

		</div>

	      
	{elseif $flags.action == "adicionar"}

		<br>

		<div style="width: 100%;">

		<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_transportador" id = "for_transportador">
	  <input type="hidden" name="for_chk" id="for_chk" value="1" />

		  <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados do Transportador</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Endereço do Transportador</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Observação</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

			<div id="tab_0" class="anchor">

			<table width="95%" align="center">

				<tr>
					<td width="30%" class="req" align="right">Tipo do transportador:</td>
					<td>
						<input {if $smarty.post.tipo_transportador=="F"}checked{/if} class="radio" type="radio" name="tipo_transportador" id="tipo_transportador" value="F" />Pessoa Física
						<input {if $smarty.post.tipo_transportador=="J"}checked{/if} class="radio" type="radio" name="tipo_transportador" id="tipo_transportador" value="J" />Pessoa Jurídica
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Nome do transportador:</td>
					<td><input class="long" type="text" name="nome_transportador" id="nome_transportador" maxlength="100" value="{$smarty.post.nome_transportador}"/></td>
				</tr>
				
				<tr>
					<td align="right">CPF / CNPJ:</td>
					<td><input class="long" type="text" name="cpf_cnpj" id="cpf_cnpj" maxlength="20" value="{$smarty.post.cpf_cnpj}"/></td>
				</tr>
				
				<tr>
					<td align="right">Inscrição estadual:</td>
					<td><input class="long" type="text" name="instricao_estadual_transportador" id="instricao_estadual_transportador" maxlength="30" value="{$smarty.post.instricao_estadual_transportador}"/></td>
				</tr>


				<tr>
					<td align="right">Telefone:</td>
					<td>
						<input class="tiny" type="text" name="telefone_transportador_ddd" id="telefone_transportador_ddd" value="{$smarty.post.telefone_transportador_ddd}" maxlength='2' />
						<input class="short" type="text" name="telefone_transportador" id="telefone_transportador" value="{$smarty.post.telefone_transportador}" maxlength='9'onkeydown="mask('telefone_transportador', 'tel')" onkeyup="mask('telefone_transportador', 'tel')" />
					</td>
				</tr>

				<tr>
					<td align="right">Fax:</td>
					<td>
						<input class="tiny" type="text" name="fax_transportador_ddd" id="fax_transportador_ddd" value="{$smarty.post.fax_transportador_ddd}" maxlength='2' />
						<input class="short" type="text" name="fax_transportador" id="fax_transportador" value="{$smarty.post.fax_transportador}" maxlength='9'onkeydown="mask('fax_transportador', 'tel')" onkeyup="mask('fax_transportador', 'tel')" />
					</td>
				</tr>

				<tr>
					<td align="right">Email:</td>
					<td><input class="long" type="text" name="email_transportador" id="email_transportador" maxlength="100" value="{$smarty.post.email_transportador}"/></td>
				</tr>

				<tr>
					<td align="right">Site:</td>
					<td><input class="long" type="text" name="site_transportador" id="site_transportador" maxlength="100" value="{$smarty.post.site_transportador}"/></td>
				</tr>

			</table>

			</div>


			{************************************}
			{* TAB 1 *}
			{************************************}

			<div id="tab_1" class="anchor">

			<table width="95%" align="center">

				<tr>
					<td width="30%" align="right">Logradouro:</td>
					<td><input class="long" type="text" name="transportador_logradouro" id="transportador_logradouro" maxlength="100" value="{$smarty.post.transportador_logradouro}"/></td>
				</tr>

				<tr>
					<td align="right">Nº:</td>
					<td><input class="short" type="text" name="transportador_numero" id="transportador_numero" maxlength="10" value="{$smarty.post.transportador_numero}"/></td>
				</tr>

				<tr>
					<td align="right">Complemento:</td>
					<td><input class="medium" type="text" name="transportador_complemento" id="transportador_complemento" maxlength="50" value="{$smarty.post.transportador_complemento}"/></td>
				</tr>


				<tr>
					<td align="right">Estado:</td>
					<td>
						<input type="hidden" name="transportador_idestado" id="transportador_idestado" value="{$smarty.post.transportador_idestado}" />
						<input type="hidden" name="transportador_idestado_NomeTemp" id="transportador_idestado_NomeTemp" value="{$smarty.post.transportador_idestado_NomeTemp}" />
						<input class="long" type="text" name="transportador_idestado_Nome" id="transportador_idestado_Nome" value="{$smarty.post.transportador_idestado_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('transportador_idestado', 'transportador_idcidade#transportador_idbairro');
							"
						/>
						<span class="nao_selecionou" id="transportador_idestado_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("transportador_idestado_Nome", function() {ldelim}
				    	return "estado_ajax.php?ac=busca_estado&typing=" + this.text.value + "&campoID=transportador_idestado";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<tr>
					<td align="right">Cidade:</td>
					<td>
						<input type="hidden" name="transportador_idcidade" id="transportador_idcidade" value="{$smarty.post.transportador_idcidade}" />
						<input type="hidden" name="transportador_idcidade_NomeTemp" id="transportador_idcidade_NomeTemp" value="{$smarty.post.transportador_idcidade_NomeTemp}" />
						<input class="long" type="text" name="transportador_idcidade_Nome" id="transportador_idcidade_Nome" value="{$smarty.post.transportador_idcidade_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('transportador_idcidade','transportador_idbairro');
							"
						/>
						<span class="nao_selecionou" id="transportador_idcidade_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("transportador_idcidade_Nome", function() {ldelim}
				    	return "cidade_ajax.php?ac=busca_cidade&typing=" + this.text.value + "&campoID=transportador_idcidade" + "&idestado=" + document.getElementById('transportador_idestado').value;
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<tr>
					<td align="right">Bairro:</td>
					<td>
						<input type="hidden" name="transportador_idbairro" id="transportador_idbairro" value="{$smarty.post.transportador_idbairro}" />
						<input type="hidden" name="transportador_idbairro_NomeTemp" id="transportador_idbairro_NomeTemp" value="{$smarty.post.transportador_idbairro_NomeTemp}" />
						<input class="long" type="text" name="transportador_idbairro_Nome" id="transportador_idbairro_Nome" value="{$smarty.post.transportador_idbairro_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('transportador_idbairro');
							"
						/>
						<span class="nao_selecionou" id="transportador_idbairro_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("transportador_idbairro_Nome", function() {ldelim}
				    	return "bairro_ajax.php?ac=busca_bairro&typing=" + this.text.value + "&campoID=transportador_idbairro" + "&idcidade=" + document.getElementById('transportador_idcidade').value;
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<script type="text/javascript">
				  // verifica os campos auto-complete
					VerificaMudancaCampo('transportador_idestado', 'transportador_idcidade#transportador_idbairro');
					VerificaMudancaCampo('transportador_idcidade','transportador_idbairro');
					VerificaMudancaCampo('transportador_idbairro');
				</script>


				<tr>
					<td align="right">CEP:</td>
					<td>
						<input class="short" type="text" name="transportador_cep" id="transportador_cep" value="{$smarty.post.transportador_cep}" maxlength='10' onkeydown="mask('transportador_cep', 'cep')" onkeyup="mask('transportador_cep', 'cep')" />
					</td>
				</tr>

			</table>

			</div>


			{************************************}
			{* TAB 2 *}
			{************************************}

			<div id="tab_2" class="anchor">

			<table width="95%" align="center">

				<tr>
					<td width="30%" align="right">Observação:</td>
					<td>
						<textarea name="observacao_transportador" id="observacao_transportador" rows='6' cols='38'>{$smarty.post.observacao_transportador}</textarea>
					</td>
				</tr>

			</table>

			</div>
			

			<script language="javascript">
				Processa_Tabs(0, 'tab_'); // seta o tab inicial
			</script>

			<table width="95%" align="center">

       	<tr><td>&nbsp;</td></tr>

				<tr>
	        <td colspan="2" align="center">
						<input type='button' class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar"
							onClick="xajax_Verifica_Campos_Transportador_AJAX(xajax.getFormValues('for_transportador'));"
						/>
	        </td>
	      </tr>

			</table>

		</form>

		</div>

  {/if}

{/if}

{include file="com_rodape.tpl"}
