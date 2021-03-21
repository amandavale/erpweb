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
					<th align='center'>Descrição do encartelamento</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idencartelamento={$list[i].idencartelamento}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idencartelamento={$list[i].idencartelamento}">{$list[i].descricao_encartelamento}</a></td>
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
					<th align='center'>Descrição do Encartelamento</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idencartelamento={$list[i].idencartelamento}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idencartelamento={$list[i].idencartelamento}">{$list[i].descricao_encartelamento}</a></td>
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

		<form  action="{$smarty.server.PHP_SELF}?ac=editar&idencartelamento={$info.idencartelamento}" method="post" name = "for_encartelamento" id = "for_encartelamento">

			  <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados Gerais</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Tabela de Produtos</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

			<div id="tab_0" class="anchor">




		<table width="95%" align="center">
    	
      <input type="hidden" name="for_chk" id="for_chk" value="1" />

				
				<tr>
					<td class="req" align="right">Descrição do encartelamento:</td>
					<td><input class="long" type="text" name="litdescricao_encartelamento" id="litdescricao_encartelamento" maxlength="100" value="{$info.litdescricao_encartelamento}"/></td>
				</tr>
				
				
			</table>
			</div>

			{************************************}
			{* TAB 1 *}
			{************************************}

			<div id="tab_1" class="anchor">

				<table width="95%" align="center">

				<tr>
				  <td>
				  	<table align="center">
							<tr>
								<td align='right'> Tipo de Preço:</td>
								<td>
										<select name="tipoPreco" id="tipoPreco" onchange="javascript: xajax_Atualiza_Total_AJAX(xajax.getFormValues('for_encartelamento'));">
										<option {if $smarty.post.tipoPreco=="B"}selected{/if} value="B">Balcão</option>
										<option {if $smarty.post.tipoPreco=="O"}selected{/if} value="O">Oferta</option>
										<option {if $smarty.post.tipoPreco=="A"}selected{/if} value="A">Atacado</option>
										<option {if $smarty.post.tipoPreco=="T"}selected{/if} value="T">Telemarketing</option>
										</select>
								</td>
								<td align='right'>	Filial: </td>
								<td>
										<select name="idfilial" id="idfilial" onchange="javascript: xajax_Atualiza_Total_AJAX(xajax.getFormValues('for_encartelamento'));">
										{html_options values=$list_filial.idfilial output=$list_filial.nome_filial selected=$smarty.post.idfilial}
										</select>
								</td>
							</tr>
						</table>
					</td>
				<tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca dos <b>PRODUTOS</b></td>
			        </tr>

							<tr>
								<td colspan="9" align="center">
									Produto:
									<input type="hidden" name="idproduto" id="idproduto" value="{$smarty.post.idproduto}" />
									<input type="hidden" name="idproduto_NomeTemp" id="idproduto_NomeTemp" value="{$smarty.post.idproduto_NomeTemp}" />
									<input class="ultralarge" type="text" name="idproduto_Nome" id="idproduto_Nome" value="{$smarty.post.idproduto_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idproduto');
										"
									/>
									<span class="nao_selecionou" id="idproduto_Flag">
										&nbsp;&nbsp;&nbsp;
									</span>
								</td>
							</tr>


							<script type="text/javascript">
							    new CAPXOUS.AutoComplete("idproduto_Nome", function() {ldelim}
							    	return "produto_ajax.php?ac=busca_produto&typing=" + this.text.value + "&campoID=idproduto" + "&idproduto=0";
							    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>



							<script type="text/javascript">
							  // verifica os campos auto-complete
								VerificaMudancaCampo('idproduto');
							</script>

							<tr>
								<td colspan="9" align="center">
									Quantidade:
									<input class="short" type="text" name="qtd_produto" id="qtd_produto" value="{$smarty.post.qtd_produto}" maxlength='10' onkeydown="FormataValor('qtd_produto')" onkeyup="FormataValor('qtd_produto')" />

									<input type='button' class="botao_padrao" value="Inserir Produto" name="botaoInserirProduto" id="botaoInserirProduto"
										onClick="xajax_Insere_Produto_AJAX(xajax.getFormValues('for_encartelamento'));"
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
								<td colspan="9" align="center">Tabela de Produtos</td>
								<input type="hidden" name="total_produtos" id="total_produtos" value="0" />
			        </tr>

							<tr>
								<td align="center">
									<div id="div_encartelamento">

									<table width="100%" id="tabela_itens" cellpadding="5">

										<tr>
											<th align='center' width="10%">Cód.</th>
											<th align='center' width="25%">Produto</th>
											<th align='center' width="10%">Unidade</th>
											<th align='center' width="10%">Qtd.</th>
											<th align='center' width="15%">Preço Unit.(R$)</th>
           						<th align='center' width="10%">Total(R$)</th>
											<th align='center' width="5%">Excluir ?</th>
										</tr>

									</table>

								</div>
							</td>
						</tr>

							<script type="text/javascript">
							  // Inicialmente, preenche todos os fornecedores que fazem parte da filial
        				xajax_Seleciona_Produto_AJAX('{$info.idencartelamento}', xajax.getFormValues('for_encartelamento'));
							</script>

						<tr>
						</table>
					</td>
      	</tr>
					<tr>
					  <td width="90%" align="right">
						    <b>Total: R$</b>
							</td>
						  <td align="right" id="SubTotal">
						  	0,00
							</td>
						</tr>
		</table>
		</div>

  <br>

		<table align="center">
			  <tr>
          <td colspan="9" align="center">
  						  <input type='button' class="botao_padrao" value="ALTERAR" name = "Alterar" id = "Alterar"
								onClick="xajax_Verifica_Campos_Encartelamento_AJAX(xajax.getFormValues('for_encartelamento'));">
					</td>
	
					<td>&nbsp;&nbsp;&nbsp;</td>

					<td>
     						<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for_encartelamento','{$smarty.server.PHP_SELF}?ac=excluir&idencartelamento={$info.idencartelamento}','ATENÇÃO! Confirma a exclusão ?'))" >
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

		<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_encartelamento" id = "for_encartelamento">

		  <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados Gerais</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Tabela de Produtos</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

	<div id="tab_0" class="anchor">


		<table width="95%" align="center">

      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			
			<tr>
				  <td align="center">
					<table>
						<tr>
      				<td class="req" align="center">Descrição do encartelamento:</td>
							<td><input class="long" type="text" name="descricao_encartelamento" id="descricao_encartelamento" maxlength="100" value="{$smarty.post.descricao_encartelamento}"/></td>
						</tr>
					</table>
					</td>
				</tr>

			</table>
			</div>

			{************************************}
			{* TAB 1 *}
			{************************************}

			<div id="tab_1" class="anchor">
			
				<table width="95%" align="center">

				<tr>
				  <td>
				  	<table align="center">
							<tr>
								<td align='right'> Tipo de Preço:</td>
								<td>
										<select name="tipoPreco" id="tipoPreco" onchange="javascript: xajax_Atualiza_Total_AJAX(xajax.getFormValues('for_encartelamento'));">
										<option {if $smarty.post.tipoPreco=="B"}selected{/if} value="B">Balcão</option>
										<option {if $smarty.post.tipoPreco=="O"}selected{/if} value="O">Oferta</option>
										<option {if $smarty.post.tipoPreco=="A"}selected{/if} value="A">Atacado</option>
										<option {if $smarty.post.tipoPreco=="T"}selected{/if} value="T">Telemarketing</option>
										</select>
								</td>
								<td align='right'>	Filial: </td>
								<td>
										<select name="idfilial" id="idfilial" onchange="javascript: xajax_Atualiza_Total_AJAX(xajax.getFormValues('for_encartelamento'));">
										{html_options values=$list_filial.idfilial output=$list_filial.nome_filial selected=$smarty.post.idfilial}
										</select>
								</td>
							</tr>
						</table>
					</td>
				<tr>

				<tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca dos <b>PRODUTOS</b></td>
			        </tr>

							<tr>
								<td colspan="9" align="center">
									Produto:
									<input type="hidden" name="idproduto" id="idproduto" value="{$smarty.post.idproduto}" />
									<input type="hidden" name="idproduto_NomeTemp" id="idproduto_NomeTemp" value="{$smarty.post.idproduto_NomeTemp}" />
									<input class="ultralarge" type="text" name="idproduto_Nome" id="idproduto_Nome" value="{$smarty.post.idproduto_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idproduto');
										"
									/>
									<span class="nao_selecionou" id="idproduto_Flag">
										&nbsp;&nbsp;&nbsp;
									</span>
								</td>
							</tr>

							<script type="text/javascript">
							    new CAPXOUS.AutoComplete("idproduto_Nome", function() {ldelim}
							    	return "produto_ajax.php?ac=busca_produto&typing=" + this.text.value + "&campoID=idproduto" + "&idproduto=0";
							    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							  // verifica os campos auto-complete
								VerificaMudancaCampo('idproduto');
							</script>

							<tr>
								<td colspan="9" align="center">
									Quantidade:
									<input class="short" type="text" name="qtd_produto" id="qtd_produto" value="{$smarty.post.qtd_produto}" maxlength='10' onkeydown="FormataValor('qtd_produto')" onkeyup="FormataValor('qtd_produto')" />

									<input type='button' class="botao_padrao" value="Inserir Produto" name="botaoInserirProduto" id="botaoInserirProduto"
										onClick="xajax_Insere_Produto_AJAX(xajax.getFormValues('for_encartelamento'));"
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
								<td colspan="9" align="center">Tabela de Produtos</td>
								<input type="hidden" name="total_produtos" id="total_produtos" value="0" />
			        </tr>

							<tr>
								<td align="center">
									<div id="div_encartelamento">

									<table width="100%" id="tabela_itens" cellpadding="5">

										<tr>
											<th align='center' width="10%">Cód.</th>
											<th align='center' width="25%">Produto</th>
											<th align='center' width="10%">Unidade</th>
											<th align='center' width="10%">Qtd.</th>
											<th align='center' width="15%">Preço Unit.(R$)</th>
           						<th align='center' width="10%">Total(R$)</th>
											<th align='center' width="5%">Excluir ?</th>
										</tr>

									</table>

								</div>
							</td>
						</tr>

						</table>
					</td>
      	</tr>
					<tr>
					  <td width="90%" align="right">
						    <b>Total: R$</b>
							</td>
						  <td align="right" id="SubTotal">
						  	0,00
							</td>
						</tr>
		</table>
		</div>

<br>

		<table align="center">
			  <tr>
          <td colspan="9" align="center">
  						  <input type='button' class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar"
								onClick="xajax_Verifica_Campos_Encartelamento_AJAX(xajax.getFormValues('for_encartelamento'));"
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
