{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay} {include file="div_erro.tpl"} {/if}

{if $flags.okay}


<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/tabs.js"></script>



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
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=atualizar_preco">atualizar preços</a>
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

		<br>

		<table width="100%">

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
			return "produto_ajax.php?ac=busca_produto&typing=" + this.text.value + "&campoID=idproduto" + "&idproduto=0" + "&mostraDetalhes=1";
			{rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
			</script>
	
	
			<script type="text/javascript">
			// verifica os campos auto-complete
			VerificaMudancaCampo('idproduto');
			</script>
			
			<tr><td>&nbsp;</td></tr>
	
			<tr>
				<td colspan="9" align="center">

					<div style="width: 100%; display: none;" id="dados_produto">
								
						<ul class="anchors">
							<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados Gerais</a></li>
							<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Preços / Estoque</a></li>
							<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Comissões</a></li>
							<li><a id="a_tab_3" onclick="Processa_Tabs(3, 'tab_')" href="javascript:;">Tabela de Fornecedores</a></li>
							<li><a id="a_tab_4" onclick="Processa_Tabs(4, 'tab_')" href="javascript:;">Tabela de Referências</a></li>
							<li><a id="a_tab_5" onclick="Processa_Tabs(5, 'tab_')" href="javascript:;">Lista de Encartelamentos</a></li>
						</ul>
				
						{************************************}
						{* TAB 0 *}
						{************************************}
				
						<div id="tab_0" class="anchor">
				
				
						<table width="95%" align="center">
						
							<tr>
								<td align="right" class="negrito">ID do Produto:</td>
								<td id="idproduto_label" class="negrito"></td>
							</tr>

							<tr>
								<td align="right" width="50%">Departamento:</td>
								<td id="departamento"></td>
							</tr>
					
							<tr>
								<td align="right">Seção:</td>
								<td id="secao"></td>
							</tr>
				
							<tr>
								<td align="right">Descrição do produto:</td>
								<td id="descricao_produto"></td>
							</tr>
							
							<tr>
								<td align="right">Localização do produto:</td>
								<td id="localizacao_produto"></td>
							</tr>
							
							<tr>
								<td align="right">Aplicação do produto:</td>
								<td id="aplicacao_produto"></td>
							</tr>
							
							<tr>
								<td align="right">Máximo percentual de desconto (%):</td>
								<td id="percentual_max_desconto_produto"></td>
							</tr>
							
							<tr>
								<td align="right">Referência do produto:</td>
								<td id="referencia_produto"></td>
							</tr>
							
							<tr>
								<td align="right">Observação:</td>
								<td id="observacao_produto"></td>
							</tr>
							
							<tr>
								<td align="right">Data de cadastro:</td>
								<td id="data_cadastro_produto"></td>
							</tr>
							
							<tr>
								<td align="right">Peso bruto (Kg):</td>
								<td id="peso_bruto_produto"></td>
							</tr>
							
							<tr>
								<td align="right">Peso líquido (Kg):</td>
								<td  id="peso_liquido_produto"></td>
							</tr>
							
							<tr>
								<td align="right">Unidade de venda:</td>
								<td id="unidade_venda"></td>
							</tr>
							
							<tr>
								<td align="right">Qtd. unitária da embalagem de compra:</td>
								<td id="qtd_unitaria_embalagem_compra_produto"></td>
							</tr>
							
							<tr>
								<td align="right">Qtd. unitária da embalagem de venda:</td>
								<td id="qtd_unitaria_embalagem_venda_produto"></td>
							</tr>

							<tr>
								<td align="right">Código do produto (GTIN):</td>
								<td id="codigo_produto"></td>
							</tr>
				
							<tr>
								<td align="right">CST do produto:</td>
								<td id="cst_produto"></td>
							</tr>

							<tr>
								<td align="right">Situação tributária do produto:</td>
								<td id="situacao_tributaria_produto"></td>
							</tr>

							<tr>
								<td align="right">ICMS do produto (%):</td>
								<td id="icms_produto"></td>
							</tr>
			
							<tr>
								<td align="right">IPI do produto (%):</td>
								<td id="ipi_produto"></td>
							</tr>

							
							<tr>
								<td align="right">Produto Comercializado:</td>
								<td id="produto_comercializado"></td>
							</tr>
				
						</table> 
				
						</div>
				
						{************************************}
						{* TAB 1 *}
						{************************************}
				
						<div id="tab_1" class="anchor">
				
							<table width="95%" align="center">
				
								<tr>
									<td id="precos_estoque_filiais"></td>
								</tr>
				
							</table>
					
						</div>
				
						{************************************}
						{* TAB 2 *}
						{************************************}
				
						<div id="tab_2" class="anchor">
				
							<table width="95%" align="center">
				
							<tr>
								<td align="right" width="50%">Comissão interno (%):</td>
								<td id="comissao_interno_produto"></td>
							</tr>
							
							<tr>
								<td align="right">Comissão externo (%):</td>
								<td id="comissao_externo_produto"></td>
							</tr>
							
							<tr>
								<td align="right">Comissão representante (%):</td>
								<td id="comissao_representante_produto"></td>
							</tr>
							
							<tr>
								<td align="right">Comissão operador de telemarketing (%):</td>
								<td id="comissao_operador_telemarketing_produto"></td>
							</tr>
							
							<tr><td>&nbsp;</td></tr>
				
							</table>
				
						</div>
				
						{************************************}
						{* TAB 3 *}
						{************************************}
				
						<div id="tab_3" class="anchor">
				
						<table width="95%" align="center">
				
							<tr>
								<td colspan="2" align="center">
									<table class="tb4cantos" width="100%">
				
										<tr bgcolor="#F7F7F7">
											<td colspan="9" align="center">Tabela de Fornecedores</td>
											<input type="hidden" name="total_fornecedores" id="total_fornecedores" value="0" />
										</tr>
				
										<tr>
											<td align="center">
												<div id="div_fornecedor">
				
													<table width="100%" cellpadding="5">
														<tr>
															<th align='center' width="35%">Nome</th>
															<th align='center' width="10%">Endereço</th>
															<th align='center' width="15%">Telefone</th>
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
				
				
						{************************************}
						{* TAB 4 *}
						{************************************}
				
							<div id="tab_4" class="anchor">
				
								<table width="95%" align="center">
				
				
								<tr>
									<td colspan="2" align="center">
										<table class="tb4cantos" width="100%">
				
											<tr bgcolor="#F7F7F7">
												<td colspan="9" align="center">Tabela de Produtos</td>
												<input type="hidden" name="total_produtos" id="total_produtos" value="0" />
											</tr>
				
											<tr>
												<td align="center">
				
													<div id="div_referencia">
				
														<table width="100%" cellpadding="5">
															<tr>
															<th align='Left' width="15%">Código</th>
															<th align='Left' width="35%">Produto</th>
															<th align='center' width="10%">Unidade</th>
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


						{************************************}
						{* TAB 5 *}
						{************************************}
				
							<div id="tab_5" class="anchor">
				
								<table width="95%" align="center">
				
				
								<tr>
									<td colspan="2" align="center">
										<table class="tb4cantos" width="100%">
				
											<tr bgcolor="#F7F7F7">
												<td colspan="9" align="center">Encartelamentos dos quais este Produto faz parte.</td>
											</tr>
				
											<tr>
												<td align="center">
				
													<div id="div_encartelamentos">
				
													</div>
				
												</td>
											</tr>
				
										</table>
									</td>
								</tr>
							</table>
				
				
						</div>
				

						
						<form  action="" method="post" name = "for" id = "for">
							<table align="center">
								<tr><td>&nbsp;</td></tr>
								<tr>
									<td align="center" colspan="2">
										<input type='submit' class="botao_padrao" value="ALTERAR DADOS" name = "ALTERAR" id = "ALTERAR" />
									</td>
								</tr>
							</table>
						</form>
						
					</div>
		
					<script language="javascript">
					Processa_Tabs(0, 'tab_'); // seta o tab inicial
					</script>

				</td>
			</tr>

		</table>

		{if $flags.buscar_dados_produto == 1}
			<script type="text/javascript">
				// busca os dados do produto
				xajax_Mostra_Detalhes_Produto_AJAX('{$flags.idproduto}');
			</script>		
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
					<th align='center'>Descrição do produto</th>
					<th align='center'>Departamento / Seção</th>
					<th align='center'>Localização do produto</th>
					<th align='center'>Unidade de venda</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idproduto={$list[i].idproduto}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idproduto={$list[i].idproduto}">{$list[i].descricao_produto}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idproduto={$list[i].idproduto}">{$list[i].nome_departamento} / {$list[i].nome_secao}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idproduto={$list[i].idproduto}">{$list[i].localizacao_produto}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idproduto={$list[i].idproduto}">{$list[i].nome_unidade_venda} ({$list[i].sigla_unidade_venda})</a></td>
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
					<td align="right">Descrição do produto:</td>
					<td><input class="long" type="text" name="descricao_produto" id="descricao_produto" maxlength="50" value="{$flags.descricao_produto}"/></td>
				</tr>

				<tr>
					<td align="right">Departamento:</td>
					<td><input class="long" type="text" name="departamento_produto" id="departamento_produto" maxlength="50" value="{$flags.departamento_produto}"/></td>
				</tr>

				<tr>
					<td align="right">Seção:</td>
					<td><input class="long" type="text" name="secao_produto" id="secao_produto" maxlength="50" value="{$flags.secao_produto}"/></td>
				</tr>

				<tr>
					<td align="right">Localização:</td>
					<td><input class="long" type="text" name="localizacao_produto" id="localizacao_produto" maxlength="50" value="{$flags.localizacao_produto}"/></td>
				</tr>

				<tr>
					<td align="right">Referência:</td>
					<td><input class="long" type="text" name="referencia_produto" id="referencia_produto" maxlength="50" value="{$flags.referencia_produto}"/></td>
				</tr>

				<tr>
					<td align="right">Resultados por página:</td>
					<td>
						<input class="tiny" type="text" name="rpp" id="rpp" maxlength="50" value="{$flags.rpp}" onkeydown="FormataInteiro('rpp')" onkeyup="FormataInteiro('rpp')" />
						&nbsp;&nbsp;
     			</td>
				</tr>
				<tr>
					<td align="right">Buscar produtos Não Comercializados</td>
					<td>
						<input {if $flags.produto_comercializado != ""} checked {/if} type="checkbox" class="radio" name="produto_comercializado" id="produto_comercializado" value="{$flags.produto_comercializado}"  />
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
					<th align='center'>Descrição do produto</th>
					<th align='center'>Departamento / Seção</th>
					<th align='center'>Localização do produto</th>
					<th align='center'>Unidade de venda</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idproduto={$list[i].idproduto}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idproduto={$list[i].idproduto}">{$list[i].descricao_produto} {if $list[i].produto_comercializado == '0'} <b>(Não Comercializado)</b> {/if}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idproduto={$list[i].idproduto}">{$list[i].nome_departamento} / {$list[i].nome_secao}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idproduto={$list[i].idproduto}">{$list[i].localizacao_produto}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idproduto={$list[i].idproduto}">{$list[i].nome_unidade_venda} ({$list[i].sigla_unidade_venda})</a></td>
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
				<input type="hidden" name="descricao_produto" id="descricao_produto" value="{$flags.descricao_produto}"/>
				<input type="hidden" name="departamento_produto" id="departamento_produto" value="{$flags.departamento_produto}"/>
				<input type="hidden" name="secao_produto" id="secao_produto" value="{$flags.secao_produto}"/>
				<input type="hidden" name="localizacao_produto" id="localizacao_produto" value="{$flags.localizacao_produto}"/>
				<input type="hidden" name="referencia_produto" id="referencia_produto" value="{$flags.referencia_produto}"/>

				<tr>
					<td align="center">
						<input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão">
					</td>
				</tr>


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

			<form  action="{$smarty.server.PHP_SELF}?ac=editar&idproduto={$info.idproduto}" method="post" name = "for_produto" id = "for_produto">

			<ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados Gerais</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Preços/Estoque</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Comissões</a></li>
				<li><a id="a_tab_3" onclick="Processa_Tabs(3, 'tab_')" href="javascript:;">Tabela de Fornecedores</a></li>
				<li><a id="a_tab_4" onclick="Processa_Tabs(4, 'tab_')" href="javascript:;">Tabela de Referências</a></li>
				<li><a id="a_tab_5" onclick="Processa_Tabs(5, 'tab_')" href="javascript:;">Lista de Encartelamentos</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

	<div id="tab_0" class="anchor">


		<table width="95%" align="center">
    	
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				<tr>
					<td align="right"><b>ID do Produto:</b></td>
					<td>
						<b>{$info.idproduto}</b>
					</td>
				</tr>

				<tr>
					<td align="right" class="req">Departamento:</td>
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

				<tr>
					<td align="right" class="req">Seção:</td>
					<td>
						<input type="hidden" name="idsecao" id="idsecao" value="{$info.idsecao}" />
						<input type="hidden" name="idsecao_NomeTemp" id="idsecao_NomeTemp" value="{$info.idsecao_NomeTemp}" />
						<input class="long" type="text" name="idsecao_Nome" id="idsecao_Nome" value="{$info.idsecao_Nome}"
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
				return "secao_ajax.php?ac=busca_secao&typing=" + this.text.value + "&campoID=idsecao" + "&iddepartamento=" + document.getElementById('iddepartamento').value;
				{rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>

				<script type="text/javascript">
				// verifica os campos auto-complete
				VerificaMudancaCampo('iddepartamento', 'idsecao');
				VerificaMudancaCampo('idsecao');

				</script>


				<tr>
					<td class="req" align="right">Descrição do produto:</td>
					<td><input class="long" type="text" name="litdescricao_produto" id="litdescricao_produto" maxlength="100" value="{$info.litdescricao_produto}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Localização do produto:</td>
					<td><input class="long" type="text" name="litlocalizacao_produto" id="litlocalizacao_produto" maxlength="100" value="{$info.litlocalizacao_produto}"/></td>
				</tr>
				
				<tr>
					<td align="right">Aplicação do produto:</td>
					<td><input class="long" type="text" name="litaplicacao_produto" id="litaplicacao_produto" maxlength="100" value="{$info.litaplicacao_produto}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Máximo percentual de desconto (%):</td>
					<td>
						<input class="short" type="text" name="numpercentual_max_desconto_produto" id="numpercentual_max_desconto_produto" value="{$info.numpercentual_max_desconto_produto}" maxlength='5' onkeydown="FormataValor('numpercentual_max_desconto_produto')" onkeyup="FormataValor('numpercentual_max_desconto_produto')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Referência do produto:</td>
					<td><input class="long" type="text" name="litreferencia_produto" id="litreferencia_produto" maxlength="100" value="{$info.litreferencia_produto}"/></td>
				</tr>
				
				<tr>
					<td align="right">Observação:</td>
					<td><input class="long" type="text" name="litobservacao_produto" id="litobservacao_produto" maxlength="100" value="{$info.litobservacao_produto}"/></td>
				</tr>
				
				<tr>
					<td align="right">Data de cadastro:</td>
					<td>
						<input class="short" type="text" name="litdata_cadastro_produto" id="litdata_cadastro_produto" value="{$info.litdata_cadastro_produto}" maxlength='10' onkeydown="mask('litdata_cadastro_produto', 'data')" onkeyup="mask('litdata_cadastro_produto', 'data')" /> (dd/mm/aaaa)
					</td>
				</tr>
				
				<tr>
					<td align="right">Peso bruto (Kg):</td>
					<td>
						<input class="short" type="text" name="numpeso_bruto_produto" id="numpeso_bruto_produto" value="{$info.numpeso_bruto_produto}" maxlength='10' onkeydown="FormataValor('numpeso_bruto_produto')" onkeyup="FormataValor('numpeso_bruto_produto')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Peso líquido (Kg):</td>
					<td>
						<input class="short" type="text" name="numpeso_liquido_produto" id="numpeso_liquido_produto" value="{$info.numpeso_liquido_produto}" maxlength='10' onkeydown="FormataValor('numpeso_liquido_produto')" onkeyup="FormataValor('numpeso_liquido_produto')" />
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Unidade de venda:</td>
					<td>
						<select name="numidunidade_venda" id="numidunidade_venda">
						<option value="">[selecione]</option>
						{html_options values=$list_unidade_venda.idunidade_venda output=$list_unidade_venda.nome_sigla_unidade_venda selected=$info.numidunidade_venda}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">Qtd. unitária da embalagem de compra:</td>
					<td>
						<input class="short" type="text" name="numqtd_unitaria_embalagem_compra_produto" id="numqtd_unitaria_embalagem_compra_produto" value="{$info.numqtd_unitaria_embalagem_compra_produto}" maxlength='10' onkeydown="FormataValor('numqtd_unitaria_embalagem_compra_produto')" onkeyup="FormataValor('numqtd_unitaria_embalagem_compra_produto')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Qtd. unitária da embalagem de venda:</td>
					<td>
						<input class="short" type="text" name="numqtd_unitaria_embalagem_venda_produto" id="numqtd_unitaria_embalagem_venda_produto" value="{$info.numqtd_unitaria_embalagem_venda_produto}" maxlength='10' onkeydown="FormataValor('numqtd_unitaria_embalagem_venda_produto')" onkeyup="FormataValor('numqtd_unitaria_embalagem_venda_produto')" />
					</td>
				</tr>

				<tr>
					<td align="right">Código do produto (GTIN):</td>
					<td>
						<input class="long" type="text" name="litcodigo_produto" id="litcodigo_produto" value="{$info.litcodigo_produto}" maxlength='20' />	
					</td>
				</tr>
				
				<tr>
					<td align="right">CST do produto:</td>
					<td>
						<input class="short" type="text" name="litcst_produto" id="litcst_produto" value="{$info.litcst_produto}" maxlength='3' />	
						(Código de Situação Tributária)
					</td>
				</tr>

				<tr>
					<td class="req" align="right">Situação tributária:</td>
					<td>
						<select name="litsituacao_tributaria_produto" id="litsituacao_tributaria_produto">
							<option value="">[selecione]</option>
							<option value="I" {if $info.litsituacao_tributaria_produto == "I"}selected{/if}>(I) Isento</option>
							<option value="N" {if $info.litsituacao_tributaria_produto == "N"}selected{/if}>(N) Não Tributado</option>
							<option value="F" {if $info.litsituacao_tributaria_produto == "F"}selected{/if}>(F) Substituição Tributária</option>
							<option value="T" {if $info.litsituacao_tributaria_produto == "T"}selected{/if}>(T) Tributado pelo ICMS</option>
							<option value="S" {if $info.litsituacao_tributaria_produto == "S"}selected{/if}>(S) Tributado pelo ISSQN</option>
						</select>
					</td>
				</tr>

				<tr>
					<td align="right">ICMS do produto (%):</td>
					<td>
						<input class="short" type="text" name="numicms_produto" id="numicms_produto" value="{$info.numicms_produto}" maxlength='5' onkeydown="FormataValor('numicms_produto')" onkeyup="FormataValor('numicms_produto')" />
						Informar apenas se a Situação Tributária for "T" ou "S".
					</td>
				</tr>

				<tr>
					<td align="right">IPI do produto (%):</td>
					<td>
						<input class="short" type="text" name="numipi_produto" id="numipi_produto" value="{$info.numipi_produto}" maxlength='5' onkeydown="FormataValor('numipi_produto')" onkeyup="FormataValor('numipi_produto')" />
					</td>
				</tr>



				<tr>
					<td class="req" align="right">Produto Comercializado: </td>
					<td>
						<input {if $info.produto_comercializado=="1"}checked{/if} class="radio" type="radio" name="litproduto_comercializado" id="litproduto_comercializado" value="1" />Sim
						<input {if $info.produto_comercializado=="0"}checked{/if} class="radio" type="radio" name="litproduto_comercializado" id="produto_comercializado" value="0" />Não
					</td>
				</tr>


  		</table>
		</div>

			{************************************}
			{* TAB 1 *}
			{************************************}

		<div id="tab_1" class="anchor">
   	 {section name=i loop=$list_filial}

				<table width="95%" align="center">
					<input type="hidden" name="filial_{$list_filial[i].index}" id="filial_{$list_filial[i].index}" value="{$list_filial[i].idfilial}"/>

				{if $list_filial[i].index == 1}
					<tr>
						<td colspan="2" align="center">

								<input type='button' class="botao_padrao" value="Atribuir preços da filial '{$list_filial[i].nome_filial}' para todas" name="botaoAtribuirPreco2" id="botaoAtribuirPreco2"
									onClick="xajax_Atribui_Preco_AJAX(xajax.getFormValues('for_produto'));"
								/>

						</td>
					</tr>

	        <tr><td>&nbsp;</td></tr>
				{/if}

					<tr bgcolor="#F7F7F7">
						<td colspan="9" align="center">Preços do Produto - {$list_filial[i].nome_filial}</td>
	        </tr>

	        <tr>
	          <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
	        </tr>

					<tr>
						<td colspan="9" align="center" width="50%">Estoque : {$list_filial[i].qtd_produto} {$info.nome_unidade_venda} ({$info.sigla_unidade_venda})</td>
					</tr>

					<tr>
						<td class="req" align="right">Produto em Oferta: </td>
						<td>
							<input {if $list_filial[i].produto_em_oferta==1}checked{/if} class="radio" type="radio" name="litproduto_em_oferta_{$list_filial[i].index}" id="litproduto_em_oferta_{$list_filial[i].index}" value="1" />Sim
							<input {if $list_filial[i].produto_em_oferta==0}checked{/if} class="radio" type="radio" name="litproduto_em_oferta_{$list_filial[i].index}" id="litproduto_em_oferta_{$list_filial[i].index}" value="0" />Não
						</td>
					</tr>

					<tr>
						<td class="req" align="right" width="50%">Preço de balcão (R$):</td>
						<td>
							<input class="short" type="text" name="numpreco_balcao_produto_{$list_filial[i].index}" id="numpreco_balcao_produto_{$list_filial[i].index}" value="{$list_filial[i].preco_balcao_produto}" maxlength='10' onkeydown="FormataValor('numpreco_balcao_produto_{$list_filial[i].index}')" onkeyup="FormataValor('numpreco_balcao_produto_{$list_filial[i].index}')" />
						</td>
					</tr>

					<tr>
						<td class="req" align="right">Preço de oferta (R$):</td>
						<td>
							<input class="short" type="text" name="numpreco_oferta_produto_{$list_filial[i].index}" id="numpreco_oferta_produto_{$list_filial[i].index}" value="{$list_filial[i].preco_oferta_produto}" maxlength='10' onkeydown="FormataValor('numpreco_oferta_produto_{$list_filial[i].index}')" onkeyup="FormataValor('numpreco_oferta_produto_{$list_filial[i].index}')" />
						</td>
					</tr>

					<tr>
						<td class="req" align="right">Preço de atacado (R$):</td>
						<td>
							<input class="short" type="text" name="numpreco_atacado_produto_{$list_filial[i].index}" id="numpreco_atacado_produto_{$list_filial[i].index}" value="{$list_filial[i].preco_atacado_produto}" maxlength='10' onkeydown="FormataValor('numpreco_atacado_produto_{$list_filial[i].index}')" onkeyup="FormataValor('numpreco_atacado_produto_{$list_filial[i].index}')" />
						</td>
					</tr>

					<tr>
						<td class="req" align="right">Preço de telemarketing (R$):</td>
						<td>
							<input class="short" type="text" name="numpreco_telemarketing_produto_{$list_filial[i].index}" id="numpreco_telemarketing_produto_{$list_filial[i].index}" value="{$list_filial[i].preco_telemarketing_produto}" maxlength='10' onkeydown="FormataValor('numpreco_telemarketing_produto_{$list_filial[i].index}')" onkeyup="FormataValor('numpreco_telemarketing_produto_{$list_filial[i].index}')" />
						</td>
					</tr>

					<tr>
						<td class="req" align="right">Preço de custo (R$):</td>
						<td>
							<input class="short" type="text" name="numpreco_custo_produto_{$list_filial[i].index}" id="numpreco_custo_produto_{$list_filial[i].index}" value="{$list_filial[i].preco_custo_produto}" maxlength='10' onkeydown="FormataValor('numpreco_custo_produto_{$list_filial[i].index}')" onkeyup="FormataValor('numpreco_custo_produto_{$list_filial[i].index}')" />
						</td>
					</tr>

				</table>
		{/section}
		</div>

			{************************************}
			{* TAB 2 *}
			{************************************}

			<div id="tab_2" class="anchor">

				<table width="95%" align="center">
				

				<tr>
					<td class="req" align="right" width="50%">Comissão interno (%):</td>
					<td>
						<input class="short" type="text" name="numcomissao_interno_produto" id="numcomissao_interno_produto" value="{$info.numcomissao_interno_produto}" maxlength='5' onkeydown="FormataValor('numcomissao_interno_produto')" onkeyup="FormataValor('numcomissao_interno_produto')" />
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Comissão externo (%):</td>
					<td>
						<input class="short" type="text" name="numcomissao_externo_produto" id="numcomissao_externo_produto" value="{$info.numcomissao_externo_produto}" maxlength='5' onkeydown="FormataValor('numcomissao_externo_produto')" onkeyup="FormataValor('numcomissao_externo_produto')" />
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Comissão representante (%):</td>
					<td>
						<input class="short" type="text" name="numcomissao_representante_produto" id="numcomissao_representante_produto" value="{$info.numcomissao_representante_produto}" maxlength='5' onkeydown="FormataValor('numcomissao_representante_produto')" onkeyup="FormataValor('numcomissao_representante_produto')" />
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Comissão operador de telemarketing (%):</td>
					<td>
						<input class="short" type="text" name="numcomissao_operador_telemarketing_produto" id="numcomissao_operador_telemarketing_produto" value="{$info.numcomissao_operador_telemarketing_produto}" maxlength='5' onkeydown="FormataValor('numcomissao_operador_telemarketing_produto')" onkeyup="FormataValor('numcomissao_operador_telemarketing_produto')" />
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>

			</table>
		</div>

			{************************************}
			{* TAB 3 *}
			{************************************}

			<div id="tab_3" class="anchor">

				<table width="95%" align="center">

				<tr>

					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca dos <b>FORNECEDORES</b></td>
			        </tr>

							<tr>
								<td colspan="9" align="center">
									Fornecedor:
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
							return "fornecedor_ajax.php?ac=busca_fornecedor&typing=" + this.text.value + "&campoID=idfornecedor";
							{rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							// verifica os campos auto-complete
							VerificaMudancaCampo('idfornecedor');
							</script>

							<tr>
								<td colspan="9" align="center">

									<input type='button' class="botao_padrao" value="Inserir Fornecedor" name="botaoInserirFornecedor" id="botaoInserirFornecedor"
										onClick="xajax_Insere_Fornecedor_AJAX(xajax.getFormValues('for_produto'));"
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
								<td colspan="9" align="center">Tabela de Fornecedores</td>
								<input type="hidden" name="total_fornecedores" id="total_fornecedores" value="0" />
			        </tr>

							<tr>
								<td align="center">
									<div id="div_fornecedor">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='center' width="35%">Nome</th>
												<th align='center' width="10%">Endereço</th>
												<th align='center' width="15%">Telefone</th>
												<th align='center' width="5%">Excluir ?</th>
											</tr>
										</table>

									</div>
								</td>
							</tr>

							<script type="text/javascript">
								// Inicialmente, preenche todos os fornecedores que fazem parte da filial
								xajax_Seleciona_Fornecedor_AJAX('{$info.idproduto}');
							</script>

						</table>
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
								<td colspan="9" align="center">Busca de <b>REFERÊNCIA</b></td>
			        </tr>
							<tr>
								<td colspan="9" align="center">
									Produto:
									<input type="hidden" name="idproduto_referencia" id="idproduto_referencia" value="{$smarty.post.idproduto_referencia}" />
									<input type="hidden" name="idproduto_referencia_NomeTemp" id="idproduto_referencia_NomeTemp" value="{$smarty.post.idproduto_referencia_NomeTemp}" />
									<input class="ultralarge" type="text" name="idproduto_referencia_Nome" id="idproduto_referencia_Nome" value="{$smarty.post.idproduto_referencia_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idproduto_referencia');
										"
									/>
									<span class="nao_selecionou" id="idproduto_referencia_Flag">
										&nbsp;&nbsp;&nbsp;
									</span>
								</td>
							</tr>
							<script type="text/javascript">
							new CAPXOUS.AutoComplete("idproduto_referencia_Nome", function() {ldelim}
							return "produto_ajax.php?ac=busca_produto&typing=" + this.text.value + "&campoID=idproduto_referencia" + "&idproduto=" + {$info.idproduto};
							{rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							// verifica os campos auto-complete
							VerificaMudancaCampo('idproduto_referencia');
							</script>

							<tr>
								<td colspan="9" align="center">

									<input type='button' class="botao_padrao" value="Inserir Referência" name="botaoInserirReferencia" id="botaoInserirReferencia"
										onClick="xajax_Insere_Referencia_AJAX(xajax.getFormValues('for_produto'));"
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

									<div id="div_referencia">

										<table width="100%" cellpadding="5">
											<tr>
											<th align='Left' width="15%">Código</th>
											<th align='Left' width="35%">Produto</th>
											<th align='center' width="10%">Unidade</th>
          						<th align='center' width="5%">Excluir ?</th>
											</tr>
										</table>

									</div>

								</td>
							</tr>

						</table>
					</td>
        </tr>
							<script type="text/javascript">
								// Inicialmente, preenche todos os produtos de referencia
								xajax_Seleciona_Referencia_AJAX('{$info.idproduto}');
							</script>
        <tr><td>&nbsp;</td></tr>


	 	</table>
	</div>

		{************************************}
		{* TAB 5 *}
		{************************************}
	
			<div id="tab_5" class="anchor">
	
				<table width="95%" align="center">
	
	
				<tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">
	
							<tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Encartelamentos dos quais este Produto faz parte.</td>
							</tr>
	
							<tr>
								<td align="center">
	
									<div id="div_encartelamentos">
	
									</div>
	
								</td>
							</tr>
	
						</table>
					</td>
				</tr>
			</table>
	
			<script type="text/javascript">
				// Inicialmente, preenche todos encartelamentos deste produto
				xajax_Seleciona_Encartelamentos_AJAX('{$info.idproduto}');
			</script>
	
		</div>



		<table align="center">
			<tr><td>&nbsp;</td></tr>
			<tr>
        	<td align="center" colspan="2">
						<input type='button' class="botao_padrao" value="ALTERAR" name = "ALTERAR" id = "ALTERAR"
							onClick="xajax_Verifica_Campos_Produto_AJAX(xajax.getFormValues('for_produto'));"
							/>
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

      <form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_produto" id = "for_produto">
        
		  <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados Gerais</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Preços</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Comissões</a></li>
				<li><a id="a_tab_3" onclick="Processa_Tabs(3, 'tab_')" href="javascript:;">Tabela de Fornecedores</a></li>
				<li><a id="a_tab_4" onclick="Processa_Tabs(4, 'tab_')" href="javascript:;">Tabela de Referências</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

	<div id="tab_0" class="anchor">


		<table width="95%" align="center">
		
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				<tr>
					<td align="right" class="req">Departamento:</td>
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
					<td align="right" class="req">Seção:</td>
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
				return "secao_ajax.php?ac=busca_secao&typing=" + this.text.value + "&campoID=idsecao" + "&iddepartamento=" + document.getElementById('iddepartamento').value;
				{rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>

				<script type="text/javascript">
				// verifica os campos auto-complete
				VerificaMudancaCampo('iddepartamento', 'idsecao');
				VerificaMudancaCampo('idsecao');

				</script>



				<tr>
					<td class="req" align="right">Descrição do produto:</td>
					<td><input class="long" type="text" name="descricao_produto" id="descricao_produto" maxlength="100" value="{$smarty.post.descricao_produto}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Localização do produto:</td>
					<td><input class="long" type="text" name="localizacao_produto" id="localizacao_produto" maxlength="100" value="{$smarty.post.localizacao_produto}"/></td>
				</tr>
				
				<tr>
					<td align="right">Aplicação do produto:</td>
					<td><input class="long" type="text" name="aplicacao_produto" id="aplicacao_produto" maxlength="100" value="{$smarty.post.aplicacao_produto}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Máximo percentual de desconto (%):</td>
					<td>
						<input class="short" type="text" name="percentual_max_desconto_produto" id="percentual_max_desconto_produto" value="0,00" maxlength='5' onkeydown="FormataValor('percentual_max_desconto_produto')" onkeyup="FormataValor('percentual_max_desconto_produto')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Referência do produto:</td>
					<td><input class="long" type="text" name="referencia_produto" id="referencia_produto" maxlength="100" value="{$smarty.post.referencia_produto}"/></td>
				</tr>
				
				<tr>
					<td align="right">Observação:</td>
					<td><input class="long" type="text" name="observacao_produto" id="observacao_produto" maxlength="100" value="{$smarty.post.observacao_produto}"/></td>
				</tr>
				
				<tr>
					<td align="right">Data de cadastro:</td>
					<td>
						<input class="short" type="text" name="data_cadastro_produto" id="data_cadastro_produto" value="{$smarty.post.data_cadastro_produto}" maxlength='10' onkeydown="mask('data_cadastro_produto', 'data')" onkeyup="mask('data_cadastro_produto', 'data')" /> (dd/mm/aaaa)
					</td>
				</tr>
				
				<tr>
					<td align="right">Peso bruto (Kg):</td>
					<td>
						<input class="short" type="text" name="peso_bruto_produto" id="peso_bruto_produto" value="{$smarty.post.peso_bruto_produto}" maxlength='10' onkeydown="FormataValor('peso_bruto_produto')" onkeyup="FormataValor('peso_bruto_produto')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Peso líquido (Kg):</td>
					<td>
						<input class="short" type="text" name="peso_liquido_produto" id="peso_liquido_produto" value="{$smarty.post.peso_liquido_produto}" maxlength='10' onkeydown="FormataValor('peso_liquido_produto')" onkeyup="FormataValor('peso_liquido_produto')" />
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Unidade de venda:</td>
					<td>
						<select name="idunidade_venda" id="idunidade_venda">
						<option value="">[selecione]</option>
						{html_options values=$list_unidade_venda.idunidade_venda output=$list_unidade_venda.nome_sigla_unidade_venda selected=$smarty.post.idunidade_venda}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">Qtd. unitária da embalagem de compra:</td>
					<td>
						<input class="short" type="text" name="qtd_unitaria_embalagem_compra_produto" id="qtd_unitaria_embalagem_compra_produto" value="{$smarty.post.qtd_unitaria_embalagem_compra_produto}" maxlength='10' onkeydown="FormataValor('qtd_unitaria_embalagem_compra_produto')" onkeyup="FormataValor('qtd_unitaria_embalagem_compra_produto')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Qtd. unitária da embalagem de venda:</td>
					<td>
						<input class="short" type="text" name="qtd_unitaria_embalagem_venda_produto" id="qtd_unitaria_embalagem_venda_produto" value="{$smarty.post.qtd_unitaria_embalagem_venda_produto}" maxlength='10' onkeydown="FormataValor('qtd_unitaria_embalagem_venda_produto')" onkeyup="FormataValor('qtd_unitaria_embalagem_venda_produto')" />
					</td>
				</tr>

				<tr>
					<td align="right">Código do produto (GTIN):</td>
					<td>
						<input class="long" type="text" name="codigo_produto" id="codigo_produto" value="{$smarty.post.codigo_produto}" maxlength='20' />	
					</td>
				</tr>

				<tr>
					<td align="right">CST do produto:</td>
					<td>
						<input class="short" type="text" name="cst_produto" id="cst_produto" value="{$smarty.post.cst_produto}" maxlength='3' />
						(Código de Situação Tributária)
					</td>
				</tr>

				<tr>
					<td class="req" align="right">Situação tributária:</td>
					<td>
						<select name="situacao_tributaria_produto" id="situacao_tributaria_produto">
							<option value="">[selecione]</option>
							<option value="I">(I) Isento</option>
							<option value="N">(N) Não Tributado</option>
							<option value="F">(F) Substituição Tributária</option>
							<option value="T">(T) Tributado pelo ICMS</option>
							<option value="S">(S) Tributado pelo ISSQN</option>
						</select>
					</td>
				</tr>

				<tr>
					<td align="right">ICMS do produto (%):</td>
					<td>
						<input class="short" type="text" name="icms_produto" id="icms_produto" value="{$smarty.post.icms_produto}" maxlength='5' onkeydown="FormataValor('icms_produto')" onkeyup="FormataValor('icms_produto')" />
						Informar apenas se a Situação Tributária for "T" ou "S".
					</td>
				</tr>

				<tr>
					<td align="right">IPI do produto (%):</td>
					<td>
						<input class="short" type="text" name="ipi_produto" id="ipi_produto" value="{$smarty.post.ipi_produto}" maxlength='5' onkeydown="FormataValor('ipi_produto')" onkeyup="FormataValor('ipi_produto')" />
					</td>
				</tr>

				
				<tr>
					<td class="req" align="right">Produto Comercializado:</td>
					<td>
						<input checked class="radio" type="radio" name="produto_comercializado" id="produto_comercializado" value="1" /> Sim
						<input class="radio" type="radio" name="produto_comercializado" id="produto_comercializado" value="0" /> Não
					</td>
				</tr>


			</table> 
		</div>

			{************************************}
			{* TAB 1 *}
			{************************************}

			<div id="tab_1" class="anchor">

    {section name=i loop=$list_filial}
				<table width="95%" align="center">
				<input type="hidden" name="total_filial" id="total_filial" value="{$numero_filial}" />
		<input type="hidden" name="filial_{$list_filial[i].index}" id="filial_{$list_filial[i].index}" value="{$list_filial[i].idfilial}"/>

				{if $list_filial[i].index == 1}
					<tr>
						<td colspan="2" align="center">

								<input type='button' class="botao_padrao" value="Atribuir preços da filial '{$list_filial[i].nome_filial}' para todas" name="botaoAtribuirPreco2" id="botaoAtribuirPreco2"
									onClick="xajax_Atribui_Preco_AJAX(xajax.getFormValues('for_produto'));"
								/>

						</td>
					</tr>

	        <tr><td>&nbsp;</td></tr>
				{/if}

        <tr bgcolor="#F7F7F7">
					<td colspan="9" align="center">Preços do Produto - {$list_filial[i].nome_filial}</td>
        </tr>

        <tr>
          <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
        </tr>


				<tr>
					<td class="req" align="right">Produto em Oferta:</td>
					<td>
						<input class="radio" type="radio" name="produto_em_oferta_{$list_filial[i].index}" id="produto_em_oferta_{$list_filial[i].index}" value="1" /> Sim
						<input checked class="radio" type="radio" name="produto_em_oferta_{$list_filial[i].index}" id="produto_em_oferta_{$list_filial[i].index}" value="0" /> Não
					</td>
				</tr>


				<tr>
					<td class="req" align="right" width="50%">Preço de balcão (R$):</td>
					<td align="left">
						<input class="short" type="text" name="preco_balcao_produto_{$list_filial[i].index}" id="preco_balcao_produto_{$list_filial[i].index}" value="{$smarty.post.preco_balcao_produto}" maxlength='10' onkeydown="FormataValor('preco_balcao_produto_{$list_filial[i].index}')" onkeyup="FormataValor('preco_balcao_produto_{$list_filial[i].index}')" />
					</td>
				</tr>

				<tr>
					<td class="req" align="right">Preço de oferta (R$):</td>
					<td>
						<input class="short" type="text" name="preco_oferta_produto_{$list_filial[i].index}" id="preco_oferta_produto_{$list_filial[i].index}" value="{$smarty.post.preco_oferta_produto}" maxlength='10' onkeydown="FormataValor('preco_oferta_produto_{$list_filial[i].index}')" onkeyup="FormataValor('preco_oferta_produto_{$list_filial[i].index}')" />
					</td>
				</tr>

				<tr>
					<td class="req" align="right">Preço de atacado (R$):</td>
					<td>
						<input class="short" type="text" name="preco_atacado_produto_{$list_filial[i].index}" id="preco_atacado_produto_{$list_filial[i].index}" value="{$smarty.post.preco_atacado_produto}" maxlength='10' onkeydown="FormataValor('preco_atacado_produto_{$list_filial[i].index}')" onkeyup="FormataValor('preco_atacado_produto_{$list_filial[i].index}')" />
					</td>
				</tr>

				<tr>
					<td class="req" align="right">Preço de telemarketing (R$):</td>
					<td>
						<input class="short" type="text" name="preco_telemarketing_produto_{$list_filial[i].index}" id="preco_telemarketing_produto_{$list_filial[i].index}" value="{$smarty.post.preco_telemarketing_produto}" maxlength='10' onkeydown="FormataValor('preco_telemarketing_produto_{$list_filial[i].index}')" onkeyup="FormataValor('preco_telemarketing_produto_{$list_filial[i].index}')" />
					</td>
				</tr>

				<tr>
					<td class="req" align="right">Preço de Custo (R$):</td>
					<td>
						<input class="short" type="text" name="preco_custo_produto_{$list_filial[i].index}" id="preco_custo_produto_{$list_filial[i].index}" value="{$smarty.post.preco_custo_produto}" maxlength='10' onkeydown="FormataValor('preco_custo_produto_{$list_filial[i].index}')" onkeyup="FormataValor('preco_custo_produto_{$list_filial[i].index}')" />
					</td>
				</tr>

			</table>
	{/section}
	</div>

			{************************************}
			{* TAB 2 *}
			{************************************}

			<div id="tab_2" class="anchor">

				<table width="95%" align="center">

				<tr>
					<td class="req" align="right" width="50%">Comissão interno (%):</td>
					<td>
						<input class="short" type="text" name="comissao_interno_produto" id="comissao_interno_produto" value="{$smarty.post.comissao_interno_produto}" maxlength='5' onkeydown="FormataValor('comissao_interno_produto')" onkeyup="FormataValor('comissao_interno_produto')" />
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Comissão externo (%):</td>
					<td>
						<input class="short" type="text" name="comissao_externo_produto" id="comissao_externo_produto" value="{$smarty.post.comissao_externo_produto}" maxlength='5' onkeydown="FormataValor('comissao_externo_produto')" onkeyup="FormataValor('comissao_externo_produto')" />
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Comissão representante (%):</td>
					<td>
						<input class="short" type="text" name="comissao_representante_produto" id="comissao_representante_produto" value="{$smarty.post.comissao_representante_produto}" maxlength='5' onkeydown="FormataValor('comissao_representante_produto')" onkeyup="FormataValor('comissao_representante_produto')" />
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Comissão operador de telemarketing (%):</td>
					<td>
						<input class="short" type="text" name="comissao_operador_telemarketing_produto" id="comissao_operador_telemarketing_produto" value="{$smarty.post.comissao_operador_telemarketing_produto}" maxlength='5' onkeydown="FormataValor('comissao_operador_telemarketing_produto')" onkeyup="FormataValor('comissao_operador_telemarketing_produto')" />
					</td>
				</tr>
				
        <tr><td>&nbsp;</td></tr>

			</table>
		</div>

			{************************************}
			{* TAB 3 *}
			{************************************}

		<div id="tab_3" class="anchor">

				<table width="95%" align="center">

				<tr>

					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca dos <b>FORNECEDORES</b></td>
			        </tr>

							<tr>
								<td colspan="9" align="center">
									Fornecedor:
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
							return "fornecedor_ajax.php?ac=busca_fornecedor&typing=" + this.text.value + "&campoID=idfornecedor";
							{rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							// verifica os campos auto-complete
							VerificaMudancaCampo('idfornecedor');
							</script>

							<tr>
								<td colspan="9" align="center">

									<input type='button' class="botao_padrao" value="Inserir Fornecedor" name="botaoInserirFornecedor" id="botaoInserirFornecedor"
										onClick="xajax_Insere_Fornecedor_AJAX(xajax.getFormValues('for_produto'));"
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
								<td colspan="9" align="center">Tabela de Fornecedores</td>
								<input type="hidden" name="total_fornecedores" id="total_fornecedores" value="0" />
			        </tr>

							<tr>
								<td align="center">
									<div id="div_fornecedor">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='center' width="35%">Nome</th>
												<th align='center' width="10%">Endereço</th>
												<th align='center' width="15%">Telefone</th>
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
			{************************************}
			{* TAB 4 *}
			{************************************}

			<div id="tab_4" class="anchor">

				<table width="95%" align="center">

				<tr>

					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca de <b>REFERÊNCIA</b></td>
			        </tr>
							<tr>
								<td colspan="9" align="center">
									Produto:
									<input type="hidden" name="idproduto_referencia" id="idproduto_referencia" value="{$smarty.post.idproduto_referencia}" />
									<input type="hidden" name="idproduto_referencia_NomeTemp" id="idproduto_referencia_NomeTemp" value="{$smarty.post.idproduto_referencia_NomeTemp}" />
									<input class="ultralarge" type="text" name="idproduto_referencia_Nome" id="idproduto_referencia_Nome" value="{$smarty.post.idproduto_referencia_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idproduto_referencia');
										"
									/>
									<span class="nao_selecionou" id="idproduto_referencia_Flag">
										&nbsp;&nbsp;&nbsp;
									</span>
								</td>
							</tr>
							<script type="text/javascript">
							new CAPXOUS.AutoComplete("idproduto_referencia_Nome", function() {ldelim}
							return "produto_ajax.php?ac=busca_produto&typing=" + this.text.value + "&campoID=idproduto_referencia" + "&idproduto=0";
							{rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							// verifica os campos auto-complete
							VerificaMudancaCampo('idproduto_referencia');
							</script>

							<tr>
								<td colspan="9" align="center">

									<input type='button' class="botao_padrao" value="Inserir Referência" name="botaoInserirReferencia" id="botaoInserirReferencia"
										onClick="xajax_Insere_Referencia_AJAX(xajax.getFormValues('for_produto'));"
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

									<div id="div_referencia">

										<table width="100%" cellpadding="5">
											<tr>
											<th align='Left' width="15%">Código</th>
											<th align='Left' width="35%">Produto</th>
											<th align='center' width="10%">Unidade</th>
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
								onClick="xajax_Verifica_Campos_Produto_AJAX(xajax.getFormValues('for_produto'));"
							/>
          </td>
        </tr>
		</table>
</div>
</form>


			<script language="javascript">
			Processa_Tabs(0, 'tab_'); // seta o tab inicial
			</script>

	{elseif $flags.action == "atualizar_preco"}

	<br>


			<form  action="{$smarty.server.PHP_SELF}?ac=atualizar_preco&idproduto={$info.idproduto}" method="post" name = "for_produto" id = "for_produto">

		<table width="95%" align="center">

      <input type="hidden" name="for_chk" id="for_chk" value="1" />


				<tr>
					<td width="40%" align="right" class="req">Departamento:</td>
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

				<tr>
					<td align="right" class="req">Seção:</td>
					<td>
						<input type="hidden" name="idsecao" id="idsecao" value="{$info.idsecao}" />
						<input type="hidden" name="idsecao_NomeTemp" id="idsecao_NomeTemp" value="{$info.idsecao_NomeTemp}" />
						<input class="long" type="text" name="idsecao_Nome" id="idsecao_Nome" value="{$info.idsecao_Nome}"
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
				return "secao_ajax.php?ac=busca_secao&typing=" + this.text.value + "&campoID=idsecao" + "&iddepartamento=" + document.getElementById('iddepartamento').value;
				{rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>

				<script type="text/javascript">
				// verifica os campos auto-complete
				VerificaMudancaCampo('iddepartamento', 'idsecao');
				VerificaMudancaCampo('idsecao');

				</script>


				<tr>
					<td class="req" align="right">Porcentagem: (%)</td>
					<td>
						<input class="short" type="text" name="porcentagem" id="porcentagem" value="{$smarty.post.porcentagem}" maxlength='6' onkeydown="FormataValor('porcentagem')" onkeyup="FormataValor('porcentagem')" />
					</td>
				<tr>
					<td class="req" align="right">Tipo de Atualização: </td>
					<td>
						<input class="radio" type="radio" name="tipo_atribuicao" id="tipo_atribuicao" value="A" /> Aumentar
						<input class="radio" type="radio" name="tipo_atribuicao" id="tipo_atribuicao" value="R" /> Reduzir
					</td>
				</tr>
		</table>
		
  	<table align="center">
		 <tr><td>&nbsp;</td></tr>
				<tr>
          <td colspan="2" align="center">
  						<input type='button' class="botao_padrao" value="ATUALIZAR" name = "Atualizar" id = "Atualizar"
								onClick="xajax_Verifica_Campos_Atualizar_Produto_AJAX(xajax.getFormValues('for_produto'));"
							/>
          </td>
        </tr>
		</table>
		</form>



  {/if}

{/if}

{include file="com_rodape.tpl"}


