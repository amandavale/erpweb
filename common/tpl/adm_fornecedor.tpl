{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}

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
				{if $list_permissao.adicionar == '1'}
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a>
				{/if}
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
					
					<br><br>
					<table align="center">
							<tr>
								<td>Fornecedor:</td>
								<td  align="center">
									
									<input type="hidden" name="idfornecedor" id="idfornecedor" value="{$smarty.post.idfornecedor}" />
									<input type="hidden" name="idfornecedor_NomeTemp" id="idfornecedor_NomeTemp" value="{$smarty.post.idfornecedor_NomeTemp}" />
									<input class="ultralarge" type="text" name="idfornecedor_Nome" id="idfornecedor_Nome" value="{$smarty.post.idfornecedor_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idfornecedor');
										"
									/>
									<span class="nao_selecionou" id="idfornecedor_Flag">
										&nbsp;&nbsp;&nbsp;
									</span>
								</td>
							</tr>
							<script type="text/javascript">
							    new CAPXOUS.AutoComplete("idfornecedor_Nome", function() {ldelim}
							    	return "fornecedor_ajax.php?ac=busca_fornecedor&typing=" + this.text.value + "&campoID=idfornecedor" + "&mostraDetalhes=1";
							    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							  // verifica os campos auto-complete
								VerificaMudancaCampo('idfornecedor');
							</script>

							<tr>
									<td colspan="9" align="center">
						  		  <div id="dados_fornecedor">
										</div>
									</td>
							</tr>
					</table>


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
					<th align='center'> No </th>
					<th align='center'> Nome </th>
					<th align='center'> Endereço </th>
					<th align='center'> CNPJ </th>
					<th align='center'> Telefone </th>
					<th align='center'> Pessoa Contato </th>
					<th align='center'> Celular </th>
				</tr>
			
			  {section name=i loop=$list}
			    <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
					<td width="4%"  ><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfornecedor={$list[i].idfornecedor}">{$list[i].index}</a></td>
					<td width="18%" ><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfornecedor={$list[i].idfornecedor}">{$list[i].nome_fornecedor}</a></td>
					<td width="38%" ><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfornecedor={$list[i].idfornecedor}">{$list[i].endereco}</a></td>
					<td width="10%" ><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfornecedor={$list[i].idfornecedor}">{$list[i].cpf_cnpj}</a></td>
					<td width="10%" ><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfornecedor={$list[i].idfornecedor}">{$list[i].telefone_fornecedor}</a></td>
					<td width="10%" ><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfornecedor={$list[i].idfornecedor}">{$list[i].nome_contato_fornecedor}</a></td>
					<td width="10%" ><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfornecedor={$list[i].idfornecedor}">{$list[i].celular_representante}</a></td>
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



		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=busca_parametrizada" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />


				<tr>
					<td align="right">Nome do fornecedor:</td>
					<td><input class="long" type="text" name="nome_fornecedor" id="nome_fornecedor" maxlength="50" value="{$flags.nome_fornecedor}"/></td>
				</tr>

				<tr>
					<td align="right">Nome do contato:</td>
					<td><input class="long" type="text" name="nome_contato_fornecedor" id="nome_contato_fornecedor" maxlength="50" value="{$flags.nome_contato_fornecedor}"/></td>
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
					<th align='center'> No </th>
					<th align='center'> Nome </th>
					<th align='center'> Endereço </th>
					<th align='center'> CNPJ </th>
					<th align='center'> Telefone </th>
					<th align='center'> Pessoa Contato </th>
					<th align='center'> Celular </th>
				</tr>
			
			  {section name=i loop=$list}
			    <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
					<td width="4%"  ><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfornecedor={$list[i].idfornecedor}">{$list[i].index}</a></td>
					<td width="18%" ><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfornecedor={$list[i].idfornecedor}">{$list[i].nome_fornecedor}</a></td>
					<td width="38%" ><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfornecedor={$list[i].idfornecedor}">{$list[i].endereco}</a></td>
					<td width="10%" ><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfornecedor={$list[i].idfornecedor}">{$list[i].cpf_cnpj}</a></td>
					<td width="10%" ><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfornecedor={$list[i].idfornecedor}">{$list[i].telefone_fornecedor}</a></td>
					<td width="10%" ><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfornecedor={$list[i].idfornecedor}">{$list[i].nome_contato_fornecedor}</a></td>
					<td width="10%" ><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfornecedor={$list[i].idfornecedor}">{$list[i].celular_representante}</a></td>
			    </tr>
			
			    <tr>
			      <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
			    </tr>
			  {/section}

      </table>

      <p align="center" id="nav">{$nav}</p>


			<table width="95%" align="center">
	    	<form action="{$smarty.server.PHP_SELF}?ac=busca_parametrizada&target=full" method="post" name = "for" id = "for" target="_blank">
	      		<input type="hidden" name="for_chk" id="for_chk" value="1" />
				<input type="hidden" name="nome_fornecedor" id="nome_fornecedor" value="{$flags.nome_fornecedor}"/>
				<input type="hidden" name="nome_contato_fornecedor" id="nome_contato_fornecedor" value="{$flags.nome_contato_fornecedor}"/>

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

			<form  action="{$smarty.server.PHP_SELF}?ac=editar&idfornecedor={$info.idfornecedor}" method="post" name = "for" id = "for">

			<ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados do Fornecedor</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Endereço do Fornecedor</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Dados do Representante</a></li>
				<li><a id="a_tab_3" onclick="Processa_Tabs(3, 'tab_')" href="javascript:;">Endereço do Representante</a></li>
				<li><a id="a_tab_4" onclick="Processa_Tabs(4, 'tab_')" href="javascript:;">Contas do Fornecedor</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

	<div id="tab_0" class="anchor">


		<table width="95%" align="center">

      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			<input type="hidden" name="idendereco_fornecedor" id="idendereco_fornecedor" value="{$info.idendereco_fornecedor}" />
			<input type="hidden" name="idendereco_representante_fornecedor" id="idendereco_representante_fornecedor" value="{$info.idendereco_representante_fornecedor}" />
			

			<tr>
					<td class="req" align="right" width="40%">Tipo do fornecedor:</td>
					<td>
						<input {if $info.littipo_fornecedor=="F"}checked{/if} class="radio" type="radio" name="littipo_fornecedor" id="littipo_fornecedor" value="F" />&nbsp;Pessoa Física
						<input {if $info.littipo_fornecedor=="J"}checked{/if} class="radio" type="radio" name="littipo_fornecedor" id="littipo_fornecedor" value="J" />&nbsp;Pessoa Jurídica
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Nome do fornecedor:</td>
					<td><input class="long" type="text" name="litnome_fornecedor" id="litnome_fornecedor" maxlength="100" value="{$info.litnome_fornecedor}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Ramo de atividade:</td>
					<td>
						<select name="numidramo_atividade" id="numidramo_atividade">
						<option value="">[selecione]</option>
						{html_options values=$list_ramo_atividade.idramo_atividade output=$list_ramo_atividade.descricao_atividade selected=$info.numidramo_atividade}
						</select>
					</td>
				</tr>

				
				<tr>
					<td align="right">CPF / CNPJ:</td>
					<td><input class="long" type="text" name="litcpf_cnpj" id="litcpf_cnpj" maxlength="20" value="{$info.litcpf_cnpj}"/></td>
				</tr>
				
				<tr>
					<td align="right">Inscrição estadual:</td>
					<td><input class="long" type="text" name="litinscricao_estadual_fornecedor" id="litinscricao_estadual_fornecedor" maxlength="30" value="{$info.litinscricao_estadual_fornecedor}"/></td>
				</tr>
				
				<tr>
					<td align="right">Nome do contato:</td>
					<td><input class="long" type="text" name="litnome_contato_fornecedor" id="litnome_contato_fornecedor" maxlength="100" value="{$info.litnome_contato_fornecedor}"/></td>
				</tr>
				<tr>
					<td align="right">Telefone:</td>
					<td>
						<input class="tiny" type="text" name="telefone_fornecedor_ddd" id="telefone_fornecedor_ddd" value="{$info.telefone_fornecedor_ddd}" maxlength='3' />
						<input class="short" type="text" name="littelefone_fornecedor" id="littelefone_fornecedor" value="{$info.littelefone_fornecedor}" maxlength='9'onkeydown="mask('littelefone_fornecedor', 'tel')" onkeyup="mask('littelefone_fornecedor', 'tel')" />
					</td>
				</tr>

				<tr>
					<td align="right">Fax:</td>
					<td>
						<input class="tiny" type="text" name="fax_fornecedor_ddd" id="fax_fornecedor_ddd" value="{$info.fax_fornecedor_ddd}" maxlength='2' />
						<input class="short" type="text" name="litfax_fornecedor" id="litfax_fornecedor" value="{$info.litfax_fornecedor}" maxlength='9'onkeydown="mask('litfax_fornecedor', 'tel')" onkeyup="mask('litfax_fornecedor', 'tel')" />
					</td>
				</tr>

				<tr>
					<td align="right">Email:</td>
					<td><input class="long" type="text" name="litemail_fornecedor" id="litemail_fornecedor" maxlength="100" value="{$info.litemail_fornecedor}"/></td>
				</tr>

				<tr>
					<td align="right">Site:</td>
					<td><input class="long" type="text" name="litsite_fornecedor" id="litsite_fornecedor" maxlength="100" value="{$info.litsite_fornecedor}"/></td>
				</tr>

	  	</table>
		</div>

					{************************************}
					{* TAB 1 *}
					{************************************}

			<div id="tab_1" class="anchor">
	      <table width="95%" align="center">

				<tr>
					<td align="right" width="40%">Logradouro:</td>
					<td><input class="long" type="text" name="fornecedor_logradouro" id="fornecedor_logradouro" maxlength="100" value="{$info.fornecedor_logradouro}"/></td>
				</tr>

				<tr>
					<td align="right">Nº:</td>
					<td><input class="short" type="text" name="fornecedor_numero" id="fornecedor_numero" maxlength="10" value="{$info.fornecedor_numero}"/></td>
				</tr>

				<tr>
					<td align="right">Complemento:</td>
					<td><input class="medium" type="text" name="fornecedor_complemento" id="fornecedor_complemento" maxlength="50" value="{$info.fornecedor_complemento}"/></td>
				</tr>

				<tr>
					<td align="right">Estado:</td>
					<td>
						<input type="hidden" name="fornecedor_idestado" id="fornecedor_idestado" value="{$info.fornecedor_idestado}" />
						<input type="hidden" name="fornecedor_idestado_NomeTemp" id="fornecedor_idestado_NomeTemp" value="{$info.fornecedor_idestado_NomeTemp}" />
						<input class="long" type="text" name="fornecedor_idestado_Nome" id="fornecedor_idestado_Nome" value="{$info.fornecedor_idestado_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('fornecedor_idestado', 'fornecedor_idcidade#fornecedor_idbairro');
							"
						/>
						<span class="nao_selecionou" id="fornecedor_idestado_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("fornecedor_idestado_Nome", function() {ldelim}
				    	return "estado_ajax.php?ac=busca_estado&typing=" + this.text.value + "&campoID=fornecedor_idestado";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<tr>
					<td align="right">Cidade:</td>
					<td>
						<input type="hidden" name="fornecedor_idcidade" id="fornecedor_idcidade" value="{$info.fornecedor_idcidade}" />
						<input type="hidden" name="fornecedor_idcidade_NomeTemp" id="fornecedor_idcidade_NomeTemp" value="{$info.fornecedor_idcidade_NomeTemp}" />
						<input class="long" type="text" name="fornecedor_idcidade_Nome" id="fornecedor_idcidade_Nome" value="{$info.fornecedor_idcidade_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('fornecedor_idcidade','fornecedor_idbairro');
							"
						/>
						<span class="nao_selecionou" id="fornecedor_idcidade_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("fornecedor_idcidade_Nome", function() {ldelim}
				    	return "cidade_ajax.php?ac=busca_cidade&typing=" + this.text.value + "&campoID=fornecedor_idcidade" + "&idestado=" + document.getElementById('fornecedor_idestado').value;
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<tr>
					<td align="right">Bairro:</td>
					<td>
						<input type="hidden" name="fornecedor_idbairro" id="fornecedor_idbairro" value="{$info.fornecedor_idbairro}" />
						<input type="hidden" name="fornecedor_idbairro_NomeTemp" id="fornecedor_idbairro_NomeTemp" value="{$info.fornecedor_idbairro_NomeTemp}" />
						<input class="long" type="text" name="fornecedor_idbairro_Nome" id="fornecedor_idbairro_Nome" value="{$info.fornecedor_idbairro_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('fornecedor_idbairro');
							"
						/>
						<span class="nao_selecionou" id="fornecedor_idbairro_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("fornecedor_idbairro_Nome", function() {ldelim}
				    	return "bairro_ajax.php?ac=busca_bairro&typing=" + this.text.value + "&campoID=fornecedor_idbairro" + "&idcidade=" + document.getElementById('fornecedor_idcidade').value;
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<script type="text/javascript">
				  // verifica os campos auto-complete
					VerificaMudancaCampo('fornecedor_idestado', 'fornecedor_idcidade#fornecedor_idbairro');
					VerificaMudancaCampo('fornecedor_idcidade','fornecedor_idbairro');
					VerificaMudancaCampo('fornecedor_idbairro');
				</script>

				<tr>
					<td align="right">CEP:</td>
					<td>
						<input class="short" type="text" name="fornecedor_cep" id="fornecedor_cep" value="{$info.fornecedor_cep}" maxlength='10' onkeydown="mask('fornecedor_cep', 'cep')" onkeyup="mask('fornecedor_cep', 'cep')" />
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
					<td align="right" width="40%">Nome do representante:</td>
					<td><input class="long" type="text" name="litnome_representante" id="litnome_representante" maxlength="100" value="{$info.litnome_representante}"/></td>
				</tr>

				<tr>
					<td align="right">Telefone:</td>
					<td>
						<input class="tiny" type="text" name="telefone_representante_ddd" id="telefone_representante_ddd" value="{$info.telefone_representante_ddd}" maxlength='3' />
						<input class="short" type="text" name="littelefone_representante" id="littelefone_representante" value="{$info.littelefone_representante}" maxlength='9'onkeydown="mask('littelefone_representante', 'tel')" onkeyup="mask('littelefone_representante', 'tel')" />
					</td>
				</tr>

				<tr>
					<td align="right">Celular:</td>
					<td>
						<input class="tiny" type="text" name="celular_representante_ddd" id="celular_representante_ddd" value="{$info.celular_representante_ddd}" maxlength='2' />
						<input class="short" type="text" name="litcelular_representante" id="litcelular_representante" value="{$info.litcelular_representante}" maxlength='9'onkeydown="mask('litcelular_representante', 'tel')" onkeyup="mask('litcelular_representante', 'tel')" />
					</td>
				</tr>

				<tr>
					<td align="right">Email:</td>
					<td><input class="long" type="text" name="litemail_representante" id="litemail_representante" maxlength="100" value="{$info.litemail_representante}"/></td>
				</tr>

			</table>
		</div>

			{************************************}
			{* TAB 3 *}
			{************************************}

		<div id="tab_3" class="anchor">

	 		<table width="95%" align="center">

				<tr>
					<td align="right" width="40%">Logradouro:</td>
					<td><input class="long" type="text" name="representante_fornecedor_logradouro" id="representante_fornecedor_logradouro" maxlength="100" value="{$info.representante_fornecedor_logradouro}"/></td>
				</tr>

				<tr>
					<td align="right">Nº:</td>
					<td><input class="short" type="text" name="representante_fornecedor_numero" id="representante_fornecedor_numero" maxlength="10" value="{$info.representante_fornecedor_numero}"/></td>
				</tr>

				<tr>
					<td align="right">Complemento:</td>
					<td><input class="medium" type="text" name="representante_fornecedor_complemento" id="representante_fornecedor_complemento" maxlength="50" value="{$info.representante_fornecedor_complemento}"/></td>
				</tr>

				<tr>
					<td align="right">Estado:</td>
					<td>
						<input type="hidden" name="representante_fornecedor_idestado" id="representante_fornecedor_idestado" value="{$info.representante_fornecedor_idestado}" />
						<input type="hidden" name="representante_fornecedor_idestado_NomeTemp" id="representante_fornecedor_idestado_NomeTemp" value="{$info.representante_fornecedor_idestado_NomeTemp}" />
						<input class="long" type="text" name="representante_fornecedor_idestado_Nome" id="representante_fornecedor_idestado_Nome" value="{$info.representante_fornecedor_idestado_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('representante_fornecedor_idestado', 'representante_fornecedor_cidade#representante_fornecedor_idbairro');
							"
						/>
						<span class="nao_selecionou" id="representante_fornecedor_idestado_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("representante_fornecedor_idestado_Nome", function() {ldelim}
				    	return "estado_ajax.php?ac=busca_estado&typing=" + this.text.value + "&campoID=representante_fornecedor_idestado";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<tr>
					<td align="right">Cidade:</td>
					<td>
						<input type="hidden" name="representante_fornecedor_idcidade" id="representante_fornecedor_idcidade" value="{$info.representante_fornecedor_idcidade}" />
						<input type="hidden" name="representante_fornecedor_idcidade_NomeTemp" id="representante_fornecedor_idcidade_NomeTemp" value="{$info.representante_fornecedor_idcidade_NomeTemp}" />
						<input class="long" type="text" name="representante_fornecedor_idcidade_Nome" id="representante_fornecedor_idcidade_Nome" value="{$info.representante_fornecedor_idcidade_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('representante_fornecedor_idcidade','representante_fornecedor_idbairro');
							"
						/>
						<span class="nao_selecionou" id="representante_fornecedor_idcidade_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("representante_fornecedor_idcidade_Nome", function() {ldelim}
				    	return "cidade_ajax.php?ac=busca_cidade&typing=" + this.text.value + "&campoID=representante_fornecedor_idcidade" + "&idestado=" + document.getElementById('representante_fornecedor_idestado').value;
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<tr>
					<td align="right">Bairro:</td>
					<td>
						<input type="hidden" name="representante_fornecedor_idbairro" id="representante_fornecedor_idbairro" value="{$info.representante_fornecedor_idbairro}" />
						<input type="hidden" name="representante_fornecedor_idbairro_NomeTemp" id="representante_fornecedor_idbairro_NomeTemp" value="{$info.representante_fornecedor_idbairro_NomeTemp}" />
						<input class="long" type="text" name="representante_fornecedor_idbairro_Nome" id="representante_fornecedor_idbairro_Nome" value="{$info.representante_fornecedor_idbairro_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('representante_fornecedor_idbairro');
							"
						/>
						<span class="nao_selecionou" id="representante_fornecedor_idbairro_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("representante_fornecedor_idbairro_Nome", function() {ldelim}
				    	return "bairro_ajax.php?ac=busca_bairro&typing=" + this.text.value + "&campoID=representante_fornecedor_idbairro" + "&idcidade=" + document.getElementById('representante_fornecedor_idcidade').value;
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<script type="text/javascript">
				  // verifica os campos auto-complete
					VerificaMudancaCampo('representante_fornecedor_idestado', 'representante_fornecedor_idcidade#representante_fornecedor_idbairro');
					VerificaMudancaCampo('representante_fornecedor_idcidade','representante_fornecedor_idbairro');
					VerificaMudancaCampo('representante_fornecedor_idbairro');
				</script>


				<tr>
					<td align="right">CEP:</td>
					<td>
						<input class="short" type="text" name="representante_fornecedor_cep" id="representante_fornecedor_cep" value="{$info.representante_fornecedor_cep}" maxlength='10' onkeydown="mask('representante_fornecedor_cep', 'cep')" onkeyup="mask('representante_fornecedor_cep', 'cep')" />
					</td>
				</tr>

			</table>


	</div>
	
			{************************************}
			{* TAB 4 *}
			{************************************}

			<div id="tab_4" class="anchor">

			<table width="95%" align="center">

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Dados da <b>Conta Bancária</b></td>
			        </tr>

							<tr>
								<td colspan="9" align="center">
									Banco:
									<input type="hidden" name="idbanco" id="idbanco" value="{$smarty.post.idbanco}" />
									<input type="hidden" name="idbanco_NomeTemp" id="idbanco_NomeTemp" value="{$smarty.post.idbanco_NomeTemp}" />
									<input class="extralarge" type="text" name="idbanco_Nome" id="idbanco_Nome" value="{$smarty.post.idbanco_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idbanco');
										"
									/>
									<span class="nao_selecionou" id="idbanco_Flag">
										&nbsp;&nbsp;&nbsp;
									</span>
								</td>
							</tr>
							<script type="text/javascript">
							    new CAPXOUS.AutoComplete("idbanco_Nome", function() {ldelim}
							    	return "banco_ajax.php?ac=busca_banco&typing=" + this.text.value + "&campoID=idbanco";
							    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							  // verifica os campos auto-complete
								VerificaMudancaCampo('idbanco');
							</script>


							<tr>
								<td align="right">Agência:</td>
								<td>
									<input class="medium" type="text" name="agencia_fornecedor" id="agencia_fornecedor" maxlength="12" value="{$smarty.post.agencia_fornecedor}"/>
									-
									<input class="tiny" type="text" name="agencia_dig_fornecedor" id="agencia_dig_fornecedor" maxlength="2" value="{$smarty.post.agencia_dig_fornecedor}"/>
								</td>
							</tr>

							<tr>
								<td align="right">Conta:</td>
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


							<tr>
								<td colspan="9" align="center">

									<input type='button' class="botao_padrao" value="Inserir conta bancária" name="botaoInserirConta" id="botaoInserirConta"
										onClick="xajax_Insere_Conta_Bancaria_AJAX(xajax.getFormValues('for'));"
									/>

								</td>
							</tr>

						</table>
					</td>
        </tr>


				<tr><td>&nbsp;</td></tr>


        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Tabela de Contas Bancárias da Filial</td>
								<input type="hidden" name="total_contas_bancarias" id="total_contas_bancarias" value="0" />
			        </tr>

							<tr>
								<td align="center">
									<div id="div_contas_bancarias">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='center' width="35%">Banco</th>
												<th align='center' width="15%">Agência</th>
												<th align='center' width="15%">Conta</th>
												<th align='center' width="10%">Principal ?</th>
												<th align='center' width="5%">Excluir ?</th>
											</tr>
										</table>

									</div>
								</td>
							</tr>

							<script type="text/javascript">
							  // Inicialmente, preenche todos os fornecedores que fazem parte da filial
        				xajax_Seleciona_Conta_Bancaria_AJAX('{$info.idfornecedor}');

							</script>

						</table>
					</td>
        </tr>

			</table>

			</div>
			
				
		<table align="center">

        <tr><td>&nbsp;</td></tr>
				<tr>
        	<td align="center" colspan="2">
						<input type='button' class="botao_padrao" value="ALTERAR" name = "ALTERAR" id = "ALTERAR"
							onClick="xajax_Verifica_Campos_Fornecedor_AJAX(xajax.getFormValues('for'));"
						/>
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&idfornecedor={$info.idfornecedor}','ATENÇÃO! Confirma a exclusão ?'))" >
        	</td>
        </tr>

		</table>

		</form>
	</div>

			<script language="javascript">
				Processa_Tabs(0, 'tab_'); // seta o tab inicial
			</script>
	      
	      
	{elseif $flags.action == "adicionar"}

<br>
	<div style="width: 100%;">

			    	<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for" id = "for">

			<ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados do Fornecedor</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Endereço do Fornecedor</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Dados do Representante</a></li>
				<li><a id="a_tab_3" onclick="Processa_Tabs(3, 'tab_')" href="javascript:;">Endereço do Representante</a></li>
				<li><a id="a_tab_4" onclick="Processa_Tabs(4, 'tab_')" href="javascript:;">Contas do Fornecedor</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

	<div id="tab_0" class="anchor">


		<table width="95%" align="center">


      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				<tr>
					<td class="req" align="right" width="40%">Tipo do fornecedor:</td>
					<td>
						<input {if $smarty.post.tipo_fornecedor=="F"}checked{/if} class="radio" type="radio" name="tipo_fornecedor" id="tipo_fornecedor" value="F" />&nbsp;Pessoa Física
						<input {if $smarty.post.tipo_fornecedor=="J"}checked{/if} class="radio" type="radio" name="tipo_fornecedor" id="tipo_fornecedor" value="J" />&nbsp;Pessoa Jurídica
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Nome do fornecedor:</td>
					<td><input class="long" type="text" name="nome_fornecedor" id="nome_fornecedor" maxlength="100" value="{$smarty.post.nome_fornecedor}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Ramo de atividade:</td>
					<td>
						<select name="idramo_atividade" id="idramo_atividade">
						<option value="">[selecione]</option>
						{html_options values=$list_ramo_atividade.idramo_atividade output=$list_ramo_atividade.descricao_atividade selected=$smarty.post.idramo_atividade}
						</select>
					</td>
				</tr>

				
				<tr>
					<td align="right">CPF / CNPJ:</td>
					<td><input class="long" type="text" name="cpf_cnpj" id="cpf_cnpj" maxlength="20" value="{$smarty.post.cpf_cnpj}"/></td>
				</tr>
				
				<tr>
					<td align="right">Inscrição estadual:</td>
					<td><input class="long" type="text" name="inscricao_estadual_fornecedor" id="inscricao_estadual_fornecedor" maxlength="30" value="{$smarty.post.inscricao_estadual_fornecedor}"/></td>
				</tr>
				
				<tr>
					<td align="right">Nome do contato:</td>
					<td><input class="long" type="text" name="nome_contato_fornecedor" id="nome_contato_fornecedor" maxlength="100" value="{$smarty.post.nome_contato_fornecedor}"/></td>
				</tr>

				<tr>
					<td align="right">Telefone:</td>
					<td>
						<input class="tiny" type="text" name="telefone_fornecedor_ddd" id="telefone_fornecedor_ddd" value="{$smarty.post.telefone_fornecedor_ddd}" maxlength='3' />
						<input class="short" type="text" name="telefone_fornecedor" id="telefone_fornecedor" value="{$smarty.post.telefone_fornecedor}" maxlength='9'onkeydown="mask('telefone_fornecedor', 'tel')" onkeyup="mask('telefone_fornecedor', 'tel')" />
					</td>
				</tr>

				<tr>
					<td align="right">Fax:</td>
					<td>
						<input class="tiny" type="text" name="fax_fornecedor_ddd" id="fax_fornecedor_ddd" value="{$smarty.post.fax_fornecedor_ddd}" maxlength='2' />
						<input class="short" type="text" name="fax_fornecedor" id="fax_fornecedor" value="{$smarty.post.fax_fornecedor}" maxlength='9'onkeydown="mask('fax_fornecedor', 'tel')" onkeyup="mask('fax_fornecedor', 'tel')" />
					</td>
				</tr>

				<tr>
					<td align="right">Email:</td>
					<td><input class="long" type="text" name="email_fornecedor" id="email_fornecedor" maxlength="100" value="{$smarty.post.email_fornecedor}"/></td>
				</tr>

				<tr>
					<td align="right">Site:</td>
					<td><input class="long" type="text" name="site_fornecedor" id="site_fornecedor" maxlength="100" value="{$smarty.post.site_fornecedor}"/></td>
				</tr>

  		</table>
		</div>

			{************************************}
			{* TAB 1 *}
			{************************************}

		<div id="tab_1" class="anchor">
      <table width="95%" align="center">

				<tr>
					<td align="right" width="40%">Logradouro:</td>
					<td><input class="long" type="text" name="fornecedor_logradouro" id="fornecedor_logradouro" maxlength="100" value="{$smarty.post.fornecedor_logradouro}"/></td>
				</tr>

				<tr>
					<td align="right">Nº:</td>
					<td><input class="short" type="text" name="fornecedor_numero" id="fornecedor_numero" maxlength="10" value="{$smarty.post.fornecedor_numero}"/></td>
				</tr>

				<tr>
					<td align="right">Complemento:</td>
					<td><input class="medium" type="text" name="fornecedor_complemento" id="fornecedor_complemento" maxlength="50" value="{$smarty.post.fornecedor_complemento}"/></td>
				</tr>

				<tr>
					<td align="right">Estado:</td>
					<td>
						<input type="hidden" name="fornecedor_idestado" id="fornecedor_idestado" value="{$smarty.post.fornecedor_idestado}" />
						<input type="hidden" name="fornecedor_idestado_NomeTemp" id="fornecedor_idestado_NomeTemp" value="{$smarty.post.fornecedor_idestado_NomeTemp}" />
						<input class="long" type="text" name="fornecedor_idestado_Nome" id="fornecedor_idestado_Nome" value="{$smarty.post.fornecedor_idestado_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('fornecedor_idestado', 'fornecedor_idcidade#fornecedor_idbairro');
							"
						/>
						<span class="nao_selecionou" id="fornecedor_idestado_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("fornecedor_idestado_Nome", function() {ldelim}
				    	return "estado_ajax.php?ac=busca_estado&typing=" + this.text.value + "&campoID=fornecedor_idestado";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<tr>
					<td align="right">Cidade:</td>
					<td>
						<input type="hidden" name="fornecedor_idcidade" id="fornecedor_idcidade" value="{$smarty.post.fornecedor_idcidade}" />
						<input type="hidden" name="fornecedor_idcidade_NomeTemp" id="fornecedor_idcidade_NomeTemp" value="{$smarty.post.fornecedor_idcidade_NomeTemp}" />
						<input class="long" type="text" name="fornecedor_idcidade_Nome" id="fornecedor_idcidade_Nome" value="{$smarty.post.fornecedor_idcidade_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('fornecedor_idcidade','fornecedor_idbairro');
							"
						/>
						<span class="nao_selecionou" id="fornecedor_idcidade_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("fornecedor_idcidade_Nome", function() {ldelim}
				    	return "cidade_ajax.php?ac=busca_cidade&typing=" + this.text.value + "&campoID=fornecedor_idcidade" + "&idestado=" + document.getElementById('fornecedor_idestado').value;
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<tr>
					<td align="right">Bairro:</td>
					<td>
						<input type="hidden" name="fornecedor_idbairro" id="fornecedor_idbairro" value="{$smarty.post.fornecedor_idbairro}" />
						<input type="hidden" name="fornecedor_idbairro_NomeTemp" id="fornecedor_idbairro_NomeTemp" value="{$smarty.post.fornecedor_idbairro_NomeTemp}" />
						<input class="long" type="text" name="fornecedor_idbairro_Nome" id="fornecedor_idbairro_Nome" value="{$smarty.post.fornecedor_idbairro_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('fornecedor_idbairro');
							"
						/>
						<span class="nao_selecionou" id="fornecedor_idbairro_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("fornecedor_idbairro_Nome", function() {ldelim}
				    	return "bairro_ajax.php?ac=busca_bairro&typing=" + this.text.value + "&campoID=fornecedor_idbairro" + "&idcidade=" + document.getElementById('fornecedor_idcidade').value;
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<script type="text/javascript">
				  // verifica os campos auto-complete
					VerificaMudancaCampo('fornecedor_idestado', 'fornecedor_idcidade#fornecedor_idbairro');
					VerificaMudancaCampo('fornecedor_idcidade','fornecedor_idbairro');
					VerificaMudancaCampo('fornecedor_idbairro');
				</script>



				<tr>
					<td align="right">CEP:</td>
					<td>
						<input class="short" type="text" name="fornecedor_cep" id="fornecedor_cep" value="{$smarty.post.fornecedor_cep}" maxlength='10' onkeydown="mask('fornecedor_cep', 'cep')" onkeyup="mask('fornecedor_cep', 'cep')" />
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
					<td align="right" width="40%">Nome do representante:</td>
					<td><input class="long" type="text" name="nome_representante" id="nome_representante" maxlength="100" value="{$smarty.post.nome_representante}"/></td>
				</tr>

				<tr>
					<td align="right">Telefone:</td>
					<td>
						<input class="tiny" type="text" name="telefone_representante_ddd" id="telefone_representante_ddd" value="{$smarty.post.telefone_representante_ddd}" maxlength='3' />
						<input class="short" type="text" name="telefone_representante" id="telefone_representante" value="{$smarty.post.telefone_representante}" maxlength='9'onkeydown="mask('telefone_representante', 'tel')" onkeyup="mask('telefone_representante', 'tel')" />
					</td>
				</tr>

				<tr>
					<td align="right">Celular:</td>
					<td>
						<input class="tiny" type="text" name="celular_representante_ddd" id="celular_representante_ddd" value="{$smarty.post.celular_representante_ddd}" maxlength='2' />
						<input class="short" type="text" name="celular_representante" id="celular_representante" value="{$smarty.post.celular_representante}" maxlength='9'onkeydown="mask('celular_representante', 'tel')" onkeyup="mask('celular_representante', 'tel')" />
					</td>
				</tr>

				<tr>
					<td align="right">Email:</td>
					<td><input class="long" type="text" name="email_representante" id="email_representante" maxlength="100" value="{$smarty.post.email_representante}"/></td>
				</tr>

			</table>
		</div>

			{************************************}
			{* TAB 3 *}
			{************************************}

		<div id="tab_3" class="anchor">

			<table width="95%" align="center">

				<tr>
					<td align="right" width="40%">Logradouro:</td>
					<td><input class="long" type="text" name="representante_fornecedor_logradouro" id="representante_fornecedor_logradouro" maxlength="100" value="{$smarty.post.representante_fornecedor_logradouro}"/></td>
				</tr>

				<tr>
					<td align="right">Nº:</td>
					<td><input class="short" type="text" name="representante_fornecedor_numero" id="representante_fornecedor_numero" maxlength="10" value="{$smarty.post.representante_fornecedor_numero}"/></td>
				</tr>

				<tr>
					<td align="right">Complemento:</td>
					<td><input class="medium" type="text" name="representante_fornecedor_complemento" id="representante_fornecedor_complemento" maxlength="50" value="{$smarty.post.representante_fornecedor_complemento}"/></td>
				</tr>


				<tr>
					<td align="right">Estado:</td>
					<td>
						<input type="hidden" name="representante_fornecedor_idestado" id="representante_fornecedor_idestado" value="{$smarty.post.representante_fornecedor_idestado}" />
						<input type="hidden" name="representante_fornecedor_idestado_NomeTemp" id="representante_fornecedor_idestado_NomeTemp" value="{$smarty.post.representante_fornecedor_idestado_NomeTemp}" />
						<input class="long" type="text" name="representante_fornecedor_idestado_Nome" id="representante_fornecedor_idestado_Nome" value="{$smarty.post.representante_fornecedor_idestado_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('representante_fornecedor_idestado', 'representante_fornecedor_cidade#representante_fornecedor_idbairro');
							"
						/>
						<span class="nao_selecionou" id="representante_fornecedor_idestado_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("representante_fornecedor_idestado_Nome", function() {ldelim}
				    	return "estado_ajax.php?ac=busca_estado&typing=" + this.text.value + "&campoID=representante_fornecedor_idestado";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<tr>
					<td align="right">Cidade:</td>
					<td>
						<input type="hidden" name="representante_fornecedor_idcidade" id="representante_fornecedor_idcidade" value="{$smarty.post.representante_fornecedor_idcidade}" />
						<input type="hidden" name="representante_fornecedor_idcidade_NomeTemp" id="representante_fornecedor_idcidade_NomeTemp" value="{$smarty.post.representante_fornecedor_idcidade_NomeTemp}" />
						<input class="long" type="text" name="representante_fornecedor_idcidade_Nome" id="representante_fornecedor_idcidade_Nome" value="{$smarty.post.representante_fornecedor_idcidade_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('representante_fornecedor_idcidade','representante_fornecedor_idbairro');
							"
						/>
						<span class="nao_selecionou" id="representante_fornecedor_idcidade_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("representante_fornecedor_idcidade_Nome", function() {ldelim}
				    	return "cidade_ajax.php?ac=busca_cidade&typing=" + this.text.value + "&campoID=representante_fornecedor_idcidade" + "&idestado=" + document.getElementById('representante_fornecedor_idestado').value;
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<tr>
					<td align="right">Bairro:</td>
					<td>
						<input type="hidden" name="representante_fornecedor_idbairro" id="representante_fornecedor_idbairro" value="{$smarty.post.representante_fornecedor_idbairro}" />
						<input type="hidden" name="representante_fornecedor_idbairro_NomeTemp" id="representante_fornecedor_idbairro_NomeTemp" value="{$smarty.post.representante_fornecedor_idbairro_NomeTemp}" />
						<input class="long" type="text" name="representante_fornecedor_idbairro_Nome" id="representante_fornecedor_idbairro_Nome" value="{$smarty.post.representante_fornecedor_idbairro_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('representante_fornecedor_idbairro');
							"
						/>
						<span class="nao_selecionou" id="representante_fornecedor_idbairro_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("representante_fornecedor_idbairro_Nome", function() {ldelim}
				    	return "bairro_ajax.php?ac=busca_bairro&typing=" + this.text.value + "&campoID=representante_fornecedor_idbairro" + "&idcidade=" + document.getElementById('representante_fornecedor_idcidade').value;
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<script type="text/javascript">
				  // verifica os campos auto-complete
					VerificaMudancaCampo('representante_fornecedor_idestado', 'representante_fornecedor_idcidade#representante_fornecedor_idbairro');
					VerificaMudancaCampo('representante_fornecedor_idcidade','representante_fornecedor_idbairro');
					VerificaMudancaCampo('representante_fornecedor_idbairro');
				</script>



				<tr>
					<td align="right">CEP:</td>
					<td>
						<input class="short" type="text" name="representante_fornecedor_cep" id="representante_fornecedor_cep" value="{$smarty.post.representante_fornecedor_cep}" maxlength='10' onkeydown="mask('representante_fornecedor_cep', 'cep')" onkeyup="mask('representante_fornecedor_cep', 'cep')" />
					</td>
				</tr>
				


		</table>
	</div>

			{************************************}
			{* TAB 4 *}
			{************************************}

			<div id="tab_4" class="anchor">

			<table width="95%" align="center">

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Dados da <b>Conta Bancária</b></td>
			        </tr>

							<tr>
								<td colspan="9" align="center">
									Banco:
									<input type="hidden" name="idbanco" id="idbanco" value="{$smarty.post.idbanco}" />
									<input type="hidden" name="idbanco_NomeTemp" id="idbanco_NomeTemp" value="{$smarty.post.idbanco_NomeTemp}" />
									<input class="extralarge" type="text" name="idbanco_Nome" id="idbanco_Nome" value="{$smarty.post.idbanco_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idbanco');
										"
									/>
									<span class="nao_selecionou" id="idbanco_Flag">
										&nbsp;&nbsp;&nbsp;
									</span>
								</td>
							</tr>
							<script type="text/javascript">
							    new CAPXOUS.AutoComplete("idbanco_Nome", function() {ldelim}
							    	return "banco_ajax.php?ac=busca_banco&typing=" + this.text.value + "&campoID=idbanco";
							    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							  // verifica os campos auto-complete
								VerificaMudancaCampo('idbanco');
							</script>


							<tr>
								<td align="right">Agência:</td>
								<td>
									<input class="medium" type="text" name="agencia_fornecedor" id="agencia_fornecedor" maxlength="12" value="{$smarty.post.agencia_fornecedor}"/>
									-
									<input class="tiny" type="text" name="agencia_dig_fornecedor" id="agencia_dig_fornecedor" maxlength="2" value="{$smarty.post.agencia_dig_fornecedor}"/>
								</td>
							</tr>

							<tr>
								<td align="right">Conta:</td>
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


							<tr>
								<td colspan="9" align="center">

									<input type='button' class="botao_padrao" value="Inserir conta bancária" name="botaoInserirConta" id="botaoInserirConta"
										onClick="xajax_Insere_Conta_Bancaria_AJAX(xajax.getFormValues('for'));"
									/>

								</td>
							</tr>

						</table>
					</td>
        </tr>


				<tr><td>&nbsp;</td></tr>


        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Tabela de Contas Bancárias da Filial</td>
								<input type="hidden" name="total_contas_bancarias" id="total_contas_bancarias" value="0" />
			        </tr>

							<tr>
								<td align="center">
									<div id="div_contas_bancarias">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='center' width="35%">Banco</th>
												<th align='center' width="15%">Agência</th>
												<th align='center' width="15%">Conta</th>
												<th align='center' width="10%">Principal ?</th>
												<th align='center' width="5%">Excluir ?</th>
											</tr>
										</table>

									</div>
								</td>
							</tr>

						</table>
					</td>
        </tr>

			</table>

			</div>


        
		<table align="center">
        <tr><td>&nbsp;</td></tr>

				<tr>
          <td colspan="2" align="center">
  						<input type='button' class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar"
								onClick="xajax_Verifica_Campos_Fornecedor_AJAX(xajax.getFormValues('for'));"
							/>
          </td>
        </tr>
		</table>

</form>
</div>

			<script language="javascript">
				Processa_Tabs(0, 'tab_'); // seta o tab inicial
			</script>

  {/if}

{/if}

{include file="com_rodape.tpl"}
