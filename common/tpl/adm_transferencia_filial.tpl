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
	  	<td class="descricao_tela" WIDTH="20%">
				{$conf.area}
			</td>
	  	<td class="tela" WIDTH="5%">
				Operações:
			</td>
	  	<td class="descricao_tela">
				{if $list_permissao.adicionar == '1'}&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a>{/if}
				{if $list_permissao.listar == '1'}&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">buscar</a>{/if}
			</td>
		</tr>
	</table> 
	
	{include file="div_erro.tpl"}
	
  {if $flags.action == "listar"}
<br>
    {if $flags.sucesso != ""}
	  	{include file="div_resultado_inicio.tpl"}
	  		{$flags.sucesso}
	  	{include file="div_resultado_fim.tpl"}
		{/if}
		
		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=listar" method="post" name = "for" id = "for">
      	<input type="hidden" name="for_chk" id="for_chk" value="1" />


				<tr>
					<td align="right" width="40%">Nome do Funcionário:</td>
					<td><input class="long" type="text" name="nome_funcionario" id="nome_funcionario" maxlength="50" value="{$flags.nome_funcionario}"/></td>
				</tr>
				<tr>
					<td align="right">Filial Remetente:</td>
					<td>
						<select name="idfilial_remetente" id="idfilial_remetente">
						<option value="">[selecione]</option>
						{html_options values=$list_filial.idfilial output=$list_filial.nome_filial selected=$flags.idfilial_remetente}
						</select>
					</td>
				</tr>

				<tr>
					<td align="right">Filial Destinatária:</td>
					
					<td>
						<select name="idfilial_destinataria" id="idfilial_destinataria">
						<option value="">[selecione]</option>
						{html_options values=$list_filial.idfilial output=$list_filial.nome_filial selected=$flags.idfilial_destinataria}
						</select>
					</td>
				</tr>

				<tr>
					<td align="right">Data da Transferência:</td>
					<td>
						<input class="short" type="text" name="data_transferencia" id="data_transferencia" maxlength="10" value="{$flags.data_transferencia}" onkeydown="mask('data_transferencia', 'data')" onkeyup="mask('data_transferencia', 'data')" /> 
						<img src="{$conf.addr}/common/img/calendar.png" id="img_data_transferencia" style="cursor: pointer;" />
						(dd/mm/aaaa)
					</td>
				</tr>
				<script type="text/javascript">
					Calendar.setup(
						{ldelim}
							inputField : "data_transferencia", // ID of the input field
							ifFormat : "%d/%m/%Y", // the date format
							button : "img_data_transferencia", // ID of the button
							align  : "cR"  // alinhamento
						{rdelim}
					);
				</script>		

				<tr>
					<td align="right">Resultados por página:</td>
					<td>
						<input class="tiny" type="text" name="rpp" id="rpp" maxlength="50" value="{$flags.rpp}" onkeydown="FormataInteiro('rpp')" onkeyup="FormataInteiro('rpp')" />
						&nbsp;&nbsp;
     			</td>
				</tr>
				<tr>
 					<td align="center" colspan="2">
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
		
					<th align='center' width="20%">Funcionário</th>
					<th align='center' width="20%">Filial Remetente</th>
					<th align='center' width="20%">Filial Destinatária</th>
					<th align='center' width="10%">Data</th>
					<th align='center' width="10%">Total(R$)</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

					
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idtransferencia_filial={$list[i].idtransferencia_filial}">{$list[i].nome_funcionario}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idtransferencia_filial={$list[i].idtransferencia_filial}">{$list[i].nome_filial}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idtransferencia_filial={$list[i].idtransferencia_filial}">{$list[i].filial_destinataria}</a></td>
						<td align="center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idtransferencia_filial={$list[i].idtransferencia_filial}">{$list[i].data_transferencia}</a></td>
	        	<td align="right"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idtransferencia_filial={$list[i].idtransferencia_filial}">{$list[i].preco_total}</a></td>
					</tr>
	        
	        <tr>
	          <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
	        </tr>
	      {/section}

      </table>
      


	{else}
		{if $flags.fez_busca == 1}
  			{include file="div_resultado_nenhum.tpl"}
  		{/if}
	{/if}






		{if count($list)}
		
			<p align="center">Listando {$conf.area} de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p>
		

      
      <p align="center" id="nav">{$nav}</p>


		{/if}
		

	{elseif $flags.action == "adicionar"}

<br>
<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for" id = "for">
	<div style="width: 100%;">



			<ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados Gerais</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Tabela de Produtos</a></li>
			</ul>

      <input type="hidden" name="for_chk" id="for_chk" value="1" />
      <input type="hidden" id="preco_total" name="preco_total" value="0">
      <input  class="" type="hidden" name="data_transferencia" id="data_transferencia" maxlength="-" value=""/>
      <input  class="" type="hidden" name="idfilial_remetente" id="idfilial_remetente" maxlength="-" value=""/>


			{************************************}
			{* TAB 0 *}
			{************************************}


	<div id="tab_0" class="anchor">

		<table width="100%">
  			<tr>
					<td align="right">Data atual:</td>
					<td>
					{$data}
					</td>
				</tr>
				<tr>
				<tr>
					<td align="right">Filial Remetente:</td>
					<td>
					{$nome_filial}
					</td>
				</tr>
				<tr>
					<td class="req" align="right">Filial Destinatária:</td>
					<td>
						<select name="idfilial_destinataria" id="idfilial_destinataria">
						<option value="">[selecione]</option>
						{html_options values=$list_filial.idfilial output=$list_filial.nome_filial selected=$smarty.post.idfilial}
						</select>
					</td>
				</tr>
				<tr>
					<td align="right">Observações:</td>
					<td> <textarea rows="4" cols="36" id="observacoes" name="observacoes"></textarea>
					</td>
				</tr>

				<tr><td>&nbsp;</td></tr>

				<tr>
					<td class="req" align="right" width="40%">Funcionário:</td>
					<td>
						<select name="idfuncionario" id="idfuncionario">
						<option value="">[selecione]</option>
						{html_options values=$list_funcionario.idfuncionario output=$list_funcionario.nome_funcionario selected=$smarty.post.idfuncionario}
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
			
			<table align="center">
				<tr>
					<td align='right'> Tipo de Preço:</td>
					<td>
							<select name="tipoPreco" id="tipoPreco" onchange="javascript: xajax_Atualiza_Total_AJAX(xajax.getFormValues('for'));">
							<option {if $smarty.post.tipoPreco=="B"}selected{/if} value="B">Balcão</option>
							<option {if $smarty.post.tipoPreco=="O"}selected{/if} value="O">Oferta</option>
							<option {if $smarty.post.tipoPreco=="A"}selected{/if} value="A">Atacado</option>
							<option {if $smarty.post.tipoPreco=="T"}selected{/if} value="T">Telemarketing</option>
							<option {if $smarty.post.tipoPreco=="C"}selected{/if} value="C">Custo</option>
							</select>
					</td>
				</tr>
			</table>
			
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
												<th align='left' width="10%">Cód.</th>
												<th align='left' width="35%">Produto</th>
												<th align='center' width="10%">Un.</th>
												<th align='center' width="10%">Qtd.</th>
												<th align='center' width="15%">Preço Unit.(R$)</th>
												<th align='center' width="15%">Total(R$)</th>
												<th align='center' width="5%">Excluir?</th>
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
								    SubTotal: R$
							</td>
							<td align="right" id="Sub">
								
								  	0,00
						 	</td>
						</tr>
						
						<tr>
							<td align="right" width="85%">Desconto (%)</td>
							<td align="right">
								<input class="tiny" type="text" name="desconto" id="desconto" value="{$smarty.post.desconto}" maxlength='5' onkeydown="FormataValor('desconto')" onkeyup="FormataValor('desconto')" onblur="xajax_Calcula_Total_AJAX();" />
							</td>
						</tr>
					
						<tr>
							
						<td width="90%" align="right">
								    Total: R$
								</td>
								<td align="right" id="SubTotal">
								
								  	0,00
								</td>
							</tr>
		</table>
	</div>

		<table align="center">
        <tr><td>&nbsp;</td></tr>
				<tr>
					<td align="center">
						<input type='button' class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar"
							onClick="xajax_Verifica_Campos_Transferencia_AJAX(xajax.getFormValues('for'));"
						/>
					</td>
        </tr>

		</table>

	
</div>
</form>

	<script language="javascript">
		Processa_Tabs(0, 'tab_'); // seta o tab inicial
	</script>
	
	
	{elseif $flags.action == "editar"}

<br>
	<div style="width: 100%;">

			<ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados Gerais</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Tabela de Produtos</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}


	<div id="tab_0" class="anchor">

		<table width="100%">
				<tr>
					<td align="right" width="40%">Funcionário:</td>
					<td>
						{$info.nome_funcionario}
					</td>
					</tr>
					<tr>
  			</tr>
  			<tr>
					<td align="right">Data da transferência:</td>
					<td>
					{$info.data_transferencia}
					</td>
				</tr>
				<tr>
				<tr>
					<td align="right">Filial Remetente:</td>
					<td>
					{$info.nome_filial}
					</td>
				</tr>
				<tr>
					<td align="right">Filial Destinatária:</td>
					<td>
						{$info.nome_filial_destinataria}
					</td>
				</tr>
				<tr>
					<td align="right">Observações:</td>
					<td>{$info.observacoes}</td>
				</tr>


  		</table>
		</div>

			{************************************}
			{* TAB 1 *}
			{************************************}

		<div id="tab_1" class="anchor">
			
			<table align="center">
				<tr>
					<td align='right'> Tipo de Preço:</td>
					<td>
						{$info.tipo_preco}
					</td>
				</tr>
			</table>
			
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

									<div id="div_produtos">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='left' width="10%">Cód.</th>
												<th align='left' width="40%">Produto</th>
												<th align='center' width="10%">Un.</th>
												<th align='center' width="10%">Qtd.</th>
												<th align='center' width="15%">Preço Unit.(R$)</th>
												<th align='center' width="15%">Total(R$)</th>

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
								    SubTotal: R$
							</td>
							<td align="right">
							{$info.preco_2}
							</td>
						</tr>
						
						<tr>
							<td align="right" width="85%">Desconto (%)</td>
							<td align="right">
							{$info.desconto}
							</td>
						</tr>
					
						<tr>
							
						<td width="90%" align="right">
								    Total: R$
								</td>
								<td align="right">
								{$info.preco_total}
								</td>
							</tr>
		</table>
	
		<script type="text/javascript">
			xajax_Seleciona_Produtos_Transferidos('{$info.idtransferencia_filial}','{$info.tipo_preco}');
		</script>
		
	</div>


</div>

	<script language="javascript">
		Processa_Tabs(0, 'tab_'); // seta o tab inicial
	</script>

  {/if}

{/if}

{include file="com_rodape.tpl"}
