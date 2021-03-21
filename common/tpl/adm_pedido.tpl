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
	  	<td class="descricao_tela" WIDTH="15%">
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
					<th align='center'><b>Nº do pedido</b></th>
					<th align='center'>Fornecedor</th>
					<th align='center'>Filial</th>
					<th align='center'>Data de Previsão</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td align='center'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idpedido={$list[i].idpedido}"><b>{$list[i].idpedido}</b></a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idpedido={$list[i].idpedido}">{$list[i].nome_fornecedor}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idpedido={$list[i].idpedido}">{$list[i].nome_filial}</a></td>
						<td align='center'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idpedido={$list[i].idpedido}">{$list[i].previsao_entrega}</a></td>
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

	<div style="width: 100%;">
	
    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idpedido={$info.idpedido}" method="post" name = "for" id = "for">
			<input type="hidden" name="for_chk" id="for_chk" value="1" />
			<input type="hidden" name="gerar" id="gerar" value="0" />
    	
			<ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados do Pedido</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Produtos Relacionados</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

		<div id="tab_0" class="anchor">

			
			<table width="100%">

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">
			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Dados do Fornecedor</td>
			        </tr>

							<tr>
			        	<td align="center">
									<a target="_blank" class='menu_item' href="{$conf.addr}/admin/fornecedor.php?ac=editar&idfornecedor={$info_fornecedor.idfornecedor}">
										Nome: {$info_fornecedor.nome_fornecedor}
									</a>		
			        	</td>
			        	<td align="center">
									<a target="_blank" class='menu_item' href="{$conf.addr}/admin/fornecedor.php?ac=editar&idfornecedor={$info_fornecedor.idfornecedor}">
										Cidade: {$info_fornecedor.nome_cidade} / {$info_fornecedor.sigla_estado}
									</a>
			        	</td>
			        	<td align="center">
									<a target="_blank" class='menu_item' href="{$conf.addr}/admin/fornecedor.php?ac=editar&idfornecedor={$info_fornecedor.idfornecedor}">
										Telefone: {$info_fornecedor.telefone_fornecedor}
									</a>
			        	</td>
			        </tr>

						</table>
					</td>
        </tr>

				<tr><td>&nbsp;</td></tr>

        <tr>
					<td colspan="2" align="center">

						<table class="tb4cantos" width="100%" cellspacing="0">

							<tr>
								<td align="center" width="35%"></td>
								<td class="tb4cantos" align='center' width="35%">Funcionário</td>
								<td class="tb4cantos" align='center' width="30%">Data / Hora</td>
							</tr>

							<tr>
								<td class="tb4cantos">Criação do registro</td>
								<td align="center" class="tb4cantos">{$info_funcionario_criou.nome_funcionario}</td>
								<td align="center" class="tb4cantos">{$info.datahoraCriacao_D} {$info.datahoraCriacao_H}</td>
							</tr>

							<tr>
								<td class="tb4cantos">Última alteração dos dados</td>
								<td align="center" class="tb4cantos">{$info_funcionario_alterou.nome_funcionario}</td>
								<td align="center" class="tb4cantos">{$info.datahoraUltAlteracao_D} {$info.datahoraUltAlteracao_H}</td>
							</tr>

						</table>

					</td>
        </tr>

				<tr>
					<td align="right" width="40%"><b>Nº do pedido:</b></td>
					<td>
						<b>{$info.idpedido}</b>
					</td>
				</tr>
							
				<tr>
					<td align="right">Filial:</td>
					<td>
					{$info.filial_nome}
					</td>
				</tr>
				
				<tr>
					<td align="right" width="40%">Data de Previsão:</td>
					<td>
						<input class="short" type="text" name="litprevisao_entrega" id="litprevisao_entrega" value="{$info.litprevisao_entrega}" maxlength='10' onkeydown="mask('litprevisao_entrega', 'data')" onkeyup="mask('litprevisao_entrega', 'data')" />
						<img src="{$conf.addr}/common/img/calendar.png" id="img_litprevisao_entrega" style="cursor: pointer;" />
 						(dd/mm/aaaa)
					</td>
				</tr>							
				<script type="text/javascript">
					Calendar.setup(
						{ldelim}
							inputField : "litprevisao_entrega", // ID of the input field
							ifFormat : "%d/%m/%Y", // the date format
							button : "img_litprevisao_entrega", // ID of the button
							align  : "cR"  // alinhamento
						{rdelim}
					);
				</script>		

								
				<tr>
					<td align="right">Observações:</td>
					<td>
						<textarea name="litobs" id="litobs" rows='6' cols='38'>{$info.litobs}</textarea>
					</td>
				</tr>

				<tr><td>&nbsp;</td></tr>

				<tr>
					<td class="req" align="right">
						Funcionário:
					</td>
					<td>
						<select name="idfuncionario" id="idfuncionario">
						<option value="">[selecione]</option>
						{html_options values=$list_funcionarios.idfuncionario output=$list_funcionarios.nome_funcionario selected=$smarty.post.idfuncionario}
						</select>
					</td>
				</tr>

				<tr>
					<td class="req" align="right">
						Senha:
					</td>
					<td>
						<input class="medium" type="password" name="senha_funcionario" id="senha_funcionario" maxlength="32" value=""/>
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
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca dos <b>PRODUTOS</b></td>
			        </tr>

							<tr>
								<td colspan="9" align="center">
									Produto:
									<input type="hidden" name="idproduto_Tipo" id="idproduto_Tipo" value="" />
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
							    	return "produto_ajax.php?ac=busca_produto_encartelamento_transferencia&typing=" + this.text.value + "&campoID=idproduto";
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

		  						<input type='button' class="botao_padrao" value="Inserir produto" name = "botaoInserirProduto" id = "botaoInserirProduto"
										onClick="xajax_Insere_Produto_Encartelamento_AJAX(xajax.getFormValues('for'));"
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

									<div id="div_produtos">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='center' width="10%">Cód.</th>
												<th align='left' width="60%">Produto</th>
												<th align='center' width="10%">Un.</th>
												<th align='center' width="10%">Qtd.</th>
												<th align='center' width="10%">Excluir ?</th>
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

			<table width="100%">
				
        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="3">
        		<input name="button" type="button" class="botao_padrao" value="ALTERAR"
        		onClick="xajax_Verifica_Campos_Pedido_AJAX(xajax.getFormValues('for'));">

						&nbsp;&nbsp;&nbsp;

        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&idpedido={$info.idpedido}','ATENÇÃO! Confirma a exclusão ?'))" >

						&nbsp;&nbsp;&nbsp;

        		<input name="button" type="button" class="botao_padrao" value="GERAR ENTRADA"
        		onClick="xajax_Verifica_Campos_Pedido_AJAX(xajax.getFormValues('for'),1);">
        	</td>
        </tr>

			</table>
		
		</form>

	</div>


		<script language="javascript">
				Processa_Tabs(0, 'tab_'); // seta o tab inicial
				xajax_Seleciona_Produtos_Pedidos('{$info.idpedido}');
		</script>
	      
	      
	{elseif $flags.action == "adicionar"}
	
	<br>

	<div style="width: 100%;">



    	<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />

      <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados do Pedido</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Produtos Relacionados</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

		<div id="tab_0" class="anchor">			

		 <table width="95%" align="center">			
				<tr>
		 			<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca de Fornecedor</b></td>
			        </tr>

						<tr>
								<td colspan="9" class="req" align="center">
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
							return "fornecedor_ajax.php?ac=busca_fornecedor&typing=" + this.text.value + "&campoID=idfornecedor" + "&mostraDetalhes=1";
							{rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							// verifica os campos auto-complete
							VerificaMudancaCampo('idfornecedor');
							</script>
			 	<tr>
					<td align="center" colspan="9"><br>
						<div id="dados_fornecedor" align="center">
						</div><br>
					</td>
				</tr>
				</table>
				</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>

				<tr>
					<td align="right" width="40%">Filial:</td>
					<td>
					{$filial}
					</td>
				</tr>
				
				<tr>
					<td align="right" width="40%">Data atual:</td>
					<td>
					{$data}
					</td>
				</tr>
				
				<tr>
					<td align="right" width="40%">Data de Previsão:</td>
					<td>
						<input class="short" type="text" name="previsao_entrega" id="previsao_entrega" value="{$smarty.post.previsao_entrega}" maxlength='10' onkeydown="mask('previsao_entrega', 'data')" onkeyup="mask('previsao_entrega', 'data')" />
						<img src="{$conf.addr}/common/img/calendar.png" id="img_previsao_entrega" style="cursor: pointer;" />
 						(dd/mm/aaaa)
					</td>
				</tr>					
				<script type="text/javascript">
					Calendar.setup(
						{ldelim}
							inputField : "previsao_entrega", // ID of the input field
							ifFormat : "%d/%m/%Y", // the date format
							button : "img_previsao_entrega", // ID of the button
							align  : "cR"  // alinhamento
						{rdelim}
					);
				</script>						


				<tr><td>&nbsp;</td></tr>

				<tr>
					<td align="right">Observações:</td>
					<td>
						<textarea name="obs" id="obs" rows='6' cols='38'>{$smarty.post.obs}</textarea>
					</td>
				</tr>

				<tr><td>&nbsp;</td></tr>

				<tr>
					<td class="req" align="right">
						Funcionário:
					</td>
					<td>
						<select name="idfuncionario" id="idfuncionario">
						<option value="">[selecione]</option>
						{html_options values=$list_funcionarios.idfuncionario output=$list_funcionarios.nome_funcionario selected=$smarty.post.idfuncionario}
						</select>
					</td>
				</tr>

				<tr>
					<td class="req" align="right">
						Senha:
					</td>
					<td>
						<input class="medium" type="password" name="senha_funcionario" id="senha_funcionario" maxlength="32" value=""/>
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
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca dos <b>PRODUTOS</b></td>
			        </tr>

							<tr>
								<td colspan="9" align="center">
									Produto:
									<input type="hidden" name="idproduto_Tipo" id="idproduto_Tipo" value="" />
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
							    	return "produto_ajax.php?ac=busca_produto_encartelamento_transferencia&typing=" + this.text.value + "&campoID=idproduto";
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

		  						<input type='button' class="botao_padrao" value="Inserir produto" name = "botaoInserirProduto" id = "botaoInserirProduto"
										onClick="xajax_Insere_Produto_Encartelamento_AJAX(xajax.getFormValues('for'));"
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

									<div id="div_produtos">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='center' width="10%">Cód.</th>
												<th align='left' width="60%">Produto</th>
												<th align='center' width="10%">Un.</th>
												<th align='center' width="10%">Qtd.</th>
												<th align='center' width="10%">Excluir ?</th>
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

			<table width="100%">
				
        <tr><td>&nbsp;</td></tr>

				<tr>
          <td colspan="2" align="center">
  						<input type='button'  class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar" 
							onClick="xajax_Verifica_Campos_Pedido_AJAX(xajax.getFormValues('for'));">
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
