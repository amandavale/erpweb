{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}

<script type="text/vbscript" src="{$conf.addr}/common/js/orcamento.vbs"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/orcamento.js"></script>

<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/tabs.js"></script>


{if $flags.okay}

	{* verifica se é para mostrar a tela de impressão do orçamento *}
	{if $smarty.get.chk_imprimir_orcamento}
		<script language="javascript">
			window.open('{$smarty.server.PHP_SELF}?ac=editar&idorcamento={$smarty.get.idorcamento}&imprimir=1');
	 	</script>
 	{/if}

	{* solitou a impressão da nota fiscal *}
	{if ($smarty.get.chk_imprimir_emissao_nf) && ($smarty.session.tipo_orcamento != "ECF") }
		<script language="javascript">
			window.open('{$conf.addr}/admin/nota_fiscal_impressao.php?idorcamento={$smarty.get.idorcamento}&visualizar=0');
		</script>
	{/if}

	<table class="tb4cantosAzul" width="100%"  border="0" cellpadding="5" cellspacing="0">
		<tr>
			{if $flags.intrucoes_preenchimento != ""}
		  	<td class="tela" WIDTH="1%" height="20" valign="middle">
		  		<img class="lightbulb" src="{$conf.addr}/common/img/lampada.png" width="16" height="16" border="0" align="middle" onmouseover="pmaTooltip('{$flags.intrucoes_preenchimento}'); return false;" onmouseout="swapTooltip('default'); return false;" />
				</td>
			{/if}
	  	<td class="tela" WIDTH="5%" height="20" valign="middle">
				Tela:
			</td>
	  	<td class="descricao_tela" WIDTH="15%">
				{$conf.area}
			</td>
	  	<td class="tela" WIDTH="5%">
				Operações:
			</td>
	  	<td class="descricao_tela">
				{if $smarty.session.tipo_orcamento == "O"}
					{if $list_permissao.adicionar == '1'}
					&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a>
					{/if}
					{if $list_permissao.listar == '1'}
					&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">buscar</a>
					{/if}
				{else}
					{if $list_permissao.listar == '1'}
					&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listarNF">buscar</a>
					{/if}
				{/if}
			</td>
		</tr>
	</table>
	
	<DIV id="popup_mensagem030"></DIV>

	{include file="div_erro.tpl"}
	
	
  {if $flags.action == "listar"}

    {if $flags.sucesso != ""}
	  	{include file="div_resultado_inicio.tpl"}
	  		{$flags.sucesso}
	  	{include file="div_resultado_fim.tpl"}
		{/if}

		<br>

  	<form  action="" method="post" name="for_orcamento" id="for_orcamento">
    <input type="hidden" name="for_chk" id="for_chk" value="1" />
			<table width="100%">

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">
			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca rápida do <b>Orçamento</b></td>
			        </tr>

							<tr>
			        	<td align="center" colspan="2" class="req">
									Código do Orçamento:
			        	  <input class="medium" type="text" name="idorcamento" id="idorcamento" value="{$smarty.post.idorcamento}" maxlength='20' onkeydown="FormataInteiro('idorcamento')" onkeyup="FormataInteiro('idorcamento')" />
									<input type="button" class="botao_padrao" value="Buscar!" name="button" onClick="xajax_Verifica_Campos_Busca_Rapida_AJAX(xajax.getFormValues('for_orcamento'))" />
			        	</td>
			        </tr>

						</table>
					</td>
        </tr>

			</table>
		</form>

		{if $flags.buscar_dados_orcamento == 1}
			<script type="text/javascript">
				// busca os dados do orçamento
				xajax_Verifica_Campos_Busca_Rapida_AJAX(xajax.getFormValues('for_orcamento'));
			</script>		
		{/if}

		<div id="dados_orcamento">
		</div>


  {elseif $flags.action == "listarNF"}

    {if $flags.sucesso != ""}
	  	{include file="div_resultado_inicio.tpl"}
	  		{$flags.sucesso}
	  	{include file="div_resultado_fim.tpl"}
		{/if}

		<br>

  	<form  action="" method="post" name="for_orcamento" id="for_orcamento">
    <input type="hidden" name="for_chk" id="for_chk" value="1" />
		<input type="hidden" name="tipoOrcamento" id="tipoOrcamento" value="{$smarty.session.tipo_orcamento}" />

			<table width="100%">

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">
			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca rápida: <b>{$conf.area}</b></td>
			        </tr>

							<tr>
			        	<td align="center" colspan="2" class="req">
									{if $smarty.session.tipo_orcamento != "ECF"}	
										Nº da Nota Fiscal:
									{else}
										Nº da Venda:
									{/if}
			        	  <input class="medium" type="text" name="numero_nota" id="numero_nota" value="{$smarty.post.numero_nota}" maxlength='20' onkeydown="FormataInteiro('numero_nota')" onkeyup="FormataInteiro('numero_nota')" />
									<input type="button" class="botao_padrao" value="Buscar!" name="button" onClick="xajax_Verifica_Campos_Busca_Rapida_Nota_AJAX(xajax.getFormValues('for_orcamento'))" />
			        	</td>
			        </tr>

						</table>
					</td>
        </tr>

			</table>
		</form>

		{if $flags.buscar_dados_nota == 1}
			<script type="text/javascript">
				// busca os dados da nota
				xajax_Verifica_Campos_Busca_Rapida_Nota_AJAX(xajax.getFormValues('for_orcamento'));
			</script>		
		{/if}

		<div id="dados_nota">
		</div>



	{elseif $flags.action == "editar"}

		{* Se o orçamento estiver cancelado, chama outro TPL que não permite a edição dos campos. 
		Se já foi emitido uma emissão fiscal para a venda, também chama outro TPL que não permite editar os dados	*}
	  {if ($info.idmotivo_cancelamento != "") || ($info.tipoOrcamento != "O") }

			{include file="adm_orcamento_cancelado.tpl"}

		{else}
	
		<br>

		<div style="width: 100%;">

	  	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idorcamento={$info.idorcamento}" method="post" name="for_orcamento" id="for_orcamento">
	    <input type="hidden" name="for_chk" id="for_chk" value="1" />
			<input type="hidden" name="maximoItensOrcamento" id="maximoItensOrcamento" value="{$smarty.post.maximoItensOrcamento}" />
			<input type="hidden" name="descontoMaximoOrcamento" id="descontoMaximoOrcamento" value="{$smarty.post.descontoMaximoOrcamento}" />
			<input type="hidden" name="tipoOrcamento" id="tipoOrcamento" value="{$smarty.session.tipo_orcamento}" />

		  <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados Gerais</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Tabela de Produtos</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Simulação Contas Receber</a></li>
				<li><a id="a_tab_3" onclick="Processa_Tabs(3, 'tab_')" href="javascript:;">Finalização</a></li>
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
								<td colspan="9" align="center">Emissão Fiscal</td>
			        </tr>
							<tr>
			        	<td align="center" colspan="2">
									Emitir:
									&nbsp;&nbsp;
									<select name="emitir_tipo_orcamento_redireciona" id="emitir_tipo_orcamento_redireciona">
										<option value=""></option>
										<option value="ECF">Cupom Fiscal</option>
										<option value="NF">Notal Fiscal</option>
										<option value="SD" selected>S&eacute;rie D</option>
									</select>
									&nbsp;&nbsp;
									<input type='hidden' name="url_redirecionamento" id="url_redirecionamento" value='{$smarty.server.PHP_SELF}?ac=editarNF&idorcamento={$info.idorcamento}&inicio=1' />
									<input type='button' class="botao_padrao" value="Gerar Emissão Fiscal!" name = "botaoGerarEmissaoFiscal" id = "botaoGerarEmissaoFiscal"
										onClick="xajax_Gerar_Emissao_Fiscal_AJAX(xajax.getFormValues('for_orcamento'));"
									/>
			        	</td>
			        </tr>
						</table>
						
						<br>

						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Dados Gerais: <b>ORÇAMENTO Nº {$info.idorcamento_formatado}</b></td>
			        </tr>

							<tr>
								<td width="33%" align="center">
									Filial:
								  {$info_filial.nome_filial}
								</td>


								<td width="33%" class="req" align="center">
									Preço de:
									<select name="tipoPreco" id="tipoPreco" onblur="xajax_Calcula_Total_AJAX();">
									<option {if $info.littipoPreco=="B"}selected{/if} value="B">Balcão</option>
									<option {if $info.littipoPreco=="O"}selected{/if} value="O">Oferta</option>
									<option {if $info.littipoPreco=="A"}selected{/if} value="A">Atacado</option>
									<option {if $info.littipoPreco=="T"}selected{/if} value="T">Telemarketing</option>
									</select>
								</td>


								<td width="33%" align="center">
									Validade do Orçamento (dias):
									<input class="short" type="text" name="validade" id="validade" value="{$info.numvalidade}" maxlength='10' onkeydown="FormataInteiro('validade')" onkeyup="FormataInteiro('validade')" />
								</td>
							</tr>


						</table>


						<table class="tb4cantos" width="100%" cellspacing="0">

							<tr>
								<td align="center" width="35%"></td>
								<td class="tb4cantos" align='center' width="35%">Vendedor</td>
								<td class="tb4cantos" align='center' width="30%">Data / Hora</td>
							</tr>

							<tr>
								<td class="tb4cantos">Criação do Orçamento</td>
								<td align="center" class="tb4cantos">{$info_funcionario_criou.nome_funcionario}</td>
								<td align="center" class="tb4cantos">{$info.datahoraCriacao_D} {$info.datahoraCriacao_H}</td>
							</tr>

							<tr>
								<td class="tb4cantos">Última alteração dos dados</td>
								<td align="center" class="tb4cantos">{$info_funcionario_alterou.nome_funcionario}</td>
								<td align="center" class="tb4cantos">{$info.datahoraUltEmissao_D} {$info.datahoraUltEmissao_H}</td>
							</tr>

						</table>

					</td>
        		</tr>

				<tr><td></td></tr>

		        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca do <b>CLIENTE</b></td>
			        </tr>

							<tr>
								<td colspan="9" align="center">
									Cliente:
									<input type="hidden" name="idcliente" id="idcliente" value="{$info.numidcliente}" />
									<input type="hidden" name="idcliente_NomeTemp" id="idcliente_NomeTemp" value="{$info.idcliente_NomeTemp}" />
									<input class="ultralarge" type="text" name="idcliente_Nome" id="idcliente_Nome" value="{$info.idcliente_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idcliente');
										"
									/>
									<span class="nao_selecionou" id="idcliente_Flag">
										&nbsp;&nbsp;&nbsp;
									</span>
								</td>
							</tr>
							<tr>
								<td colspan="9" align="center">
								  <div id="dados_cliente">
									</div>
								</td>
							</tr>

							<script type="text/javascript">
							    new CAPXOUS.AutoComplete("idcliente_Nome", function() {ldelim}
							    	return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente" + "&mostraDetalhes=1";
							    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							  // verifica os campos auto-complete
								VerificaMudancaCampo('idcliente');
							</script>


			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Dados provisórios do cliente</td>
			        </tr>


					<tr>
						<td colspan="9">
						  <table width="100%">

								<tr>
									<td align="center">
										Nome do cliente:
										<input class="long" type="text" name="nomeClienteProv" id="nomeClienteProv" maxlength="100" value="{$info.litnomeClienteProv}"/>
									</td>

									<td align="center">
										Informações:
										<textarea name="infoClienteProv" id="infoClienteProv" rows='4' cols='45'>{$info.litinfoClienteProv}</textarea>
									</td>
								</tr>

							</table>
						</td>
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
							    	return "produto_ajax.php?ac=busca_produto_encartelamento&typing=" + this.text.value + "&campoID=idproduto" + "&tipoPreco=" + document.getElementById('tipoPreco').value;
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
										onClick="xajax_Insere_Produto_Encartelamento_AJAX(xajax.getFormValues('for_orcamento')); xajax_Insere_Conta_Receber_A_Prazo_AJAX(xajax.getFormValues('for_orcamento'));"
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
								<td colspan="9" align="center">Tabela de Produtos do Orçamento</td>
								<input type="hidden" name="total_produtos" id="total_produtos" value="0" />
			        </tr>

							<tr>
								<td align="center">

									<div id="div_produtos">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='left' width="5%">Cód.</th>
												<th align='left' width="35%">Produto</th>
												<th align='center' width="5%">Un.</th>
												<th align='center' width="10%">Qtd.</th>
												<th align='center' width="10%">Preço Un.(R$)</th>
												<th align='center' width="10%">Desc.(%)</th>
												<th align='center' width="10%">Preço(R$)</th>
												<th align='center' width="10%">Total(R$)</th>
												<th align='center' width="5%">Excluir?</th>
											</tr>
										</table>

									</div>
								</td>
							</tr>
							
							<script type="text/javascript">
							  // Inicialmente, preenche todos os produtos que fazem parte do orçamento
								xajax_Seleciona_Produtos_AJAX('{$info.idorcamento}');
							</script>


						</table>
					</td>
        </tr>


				<tr>
					<td width="25%">

						<table width="100%" cellpadding="0">

							<tr><td>&nbsp;</td></tr>

							<tr>
							  <td>
							    Peso Bruto Total (Kg):
								</td>
							  <td align="right" id="Peso_Bruto_Total">
							  	0,00
								</td>
							</tr>

							<tr>
							  <td>
							    Peso Líquido Total (Kg):
								</td>
							  <td align="right" id="Peso_Liquido_Total">
							  	0,00
								</td>
							</tr>

							<tr><td>&nbsp;</td></tr>
						</table>

						<table width="100%" cellpadding="0" class="tb4cantos">
							<tr>
								<td colspan="2" align="center" bgcolor="#F7F7F7">
									Legenda
								</td>
							</tr>

							<tr>
							  <td colspan="2">
							    <img src="{$conf.addr}/common/img/lampada.png"> Produtos de referência.
									<br>
							    <img src="{$conf.addr}/common/img/exclamacao.png"> Falta no estoque.
									<br>
							    <img src="{$conf.addr}/common/img/delete.gif"> Excluir produto deste orçamento.
								</td>
							</tr>
						</table>

					</td>


					<td>

						<table width="100%" cellpadding="0">

							<tr>
								<input type="hidden" name="valor_total_produtos" id="valor_total_produtos" value="0.00" />
							  <td width="80%" align="right">
							    Total de Produtos: R$
								</td>
							  <td align="right" id="TotalProdutos">
							  	0,00
								</td>
							</tr>
							
							<tr>
								<td align="right">Desconto: R$</td>
								<td align="right">
									<input class="short" type="text" name="desconto" id="desconto" value="{$info.numdesconto}" maxlength='10' onkeydown="FormataValor('desconto')" onkeyup="FormataValor('desconto')" onblur="xajax_Calcula_Total_AJAX();" />
								</td>
							</tr>

							<tr>
								<td align="right">Frete: R$</td>
								<td align="right">
									<input class="short" type="text" name="frete" id="frete" value="{$info.numfrete}" maxlength='10' onkeydown="FormataValor('frete')" onkeyup="FormataValor('frete')" onblur="xajax_Calcula_Total_AJAX();" />
								</td>
							</tr>

							<tr>
								<td align="right">Outras Despesas: R$</td>
								<td align="right">
									<input class="short" type="text" name="outras_despesas" id="outras_despesas" value="{$info.numoutras_despesas}" maxlength='10' onkeydown="FormataValor('outras_despesas')" onkeyup="FormataValor('outras_despesas')" onblur="xajax_Calcula_Total_AJAX();" />
								</td>
							</tr>


							<tr>
							  <input type="hidden" name="valor_total_nota" id="valor_total_nota" value="0.00" />
							  <td width="80%" align="right">
							    Total: R$
								</td>
							  <td align="right" id="TotalNota">
							  	0,00
								</td>
							</tr>



		    		</table>

					</td>
				</tr>

				<tr>
					<td align="center" colspan="2">
						<input name="Calcular_Total" type="button" class="botao_padrao" value="Calcular Total!" />
					</td>
				</tr>

			</table>

			</div>


			{************************************}
			{* TAB 2 *}
			{************************************}

			<div id="tab_2" class="anchor">

			<table width="95%" align="center">


				<tr><td>&nbsp;</td></tr>

		        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

					        <tr bgcolor="#F7F7F7">
								<td align="left" width="60%">PARCELAS</td>
								<td align="right" width="30%">
									Total: R$
								</td>
					  			<td id="TotalFinanciar" align="right" width="10%">
					  				 0,00
					  			</td>
								<input type="hidden" name="valor_total_financiar" id="valor_total_financiar" value="0.00" />
								<input type="hidden" name="total_contas_receber_a_prazo" id="total_contas_receber_a_prazo" value="0" />
					        </tr>

					        <tr>
								<td colspan="9" align="center">
									<table width="100%">
										<tr>
											<td align='right'>
												Nº de parcelas:
												<input class="tiny" type="text" name="quantidade_de_parcelas" id="quantidade_de_parcelas" value="{$info.quantidade_de_parcelas}" maxlength='2' onkeydown="FormataInteiro('quantidade_de_parcelas')" onkeyup="FormataInteiro('quantidade_de_parcelas')" />
											</td>

										</tr>

										<tr>
											<td align='right'>
												Dias entre as parcelas:
												<input class="tiny" type="text" name="dias_entre_parcelas" id="dias_entre_parcelas" value="{$info.dias_entre_parcelas}" maxlength='10' onkeydown="FormataInteiro('dias_entre_parcelas')" onkeyup="FormataInteiro('dias_entre_parcelas')" />
											</td>

											<td align='right'>
												Data da parcela 1:
												<input class="short" type="text" name="data_parcela1" id="data_parcela1" value="{$info.data_parcela1}" maxlength='10' onkeydown="mask('data_parcela1', 'data')" onkeyup="mask('data_parcela1', 'data')" />
											</td>

											<td align='right'>
												<input name="Gravar" type="button" class="botao_padrao" value="Gerar Parcelas!"
			        						onClick="xajax_Insere_Conta_Receber_A_Prazo_AJAX(xajax.getFormValues('for_orcamento'));"
												/>
											</td>
										</tr>
									</table>

								</td>
					        </tr>


							<tr>
								<td align="center" colspan="9">

									<div id="div_contas_receber_a_prazo">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='left' width="20%">Parcela</th>
												<th align='left' width="25%">Conta de d&eacute;bito</th>
												<th align='left' width="25%">Conta de cr&eacute;dito</th>
												<th align='center' width="15%">Data</th>
												<th align='center' width="15%">Valor(R$)</th>
											</tr>
										</table>

									</div>
								</td>
							</tr>

						</table>
					</td>
        		</tr>

				<script type="text/javascript">
				  // Inicialmente, busca as contas a receber que foram geradas na simulação.
					xajax_Seleciona_Contas_Receber_AJAX('{$info.idorcamento}', xajax.getFormValues('for_orcamento'));
				</script>

        		<tr><td>&nbsp;</td></tr>

				<tr>
					<td colspan="2" align="center">

				  		<table width="100%" border="0">

				  			<tr>
								<td align="right" colspan="2">
									Total: R$
								</td>
				  				<td id="TotalNota2" colspan="9" align="right" bgcolor="#F7F7F7" width="15%">
				  					0,00
				  				</td>
							</tr>

						</table>
					</td>
        		</tr>

			</table>

			</div>




			{************************************}
			{* TAB 3 *}
			{************************************}

			<div id="tab_3" class="anchor">

			<table width="95%" align="center">
<!--
        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="70%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center"><b>VENDEDOR</b> Responsável: (Apenas vendedores da filial "{$info_filial.nome_filial}")</td>
			        </tr>

							<tr>
								<td width="40%" class="req" align="right">
									Vendedor:
								</td>
								<td>
									<select name="idUltfuncionario" id="idUltfuncionario">
									<option value="">[selecione]</option>
									{html_options values=$list_funcionarios.idfuncionario output=$list_funcionarios.nome_funcionario selected=$smarty.post.idUltfuncionario}
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
					</td>
        </tr>
-->
				<tr><td>&nbsp;</td></tr>

				<tr>
          <td colspan="2" align="center">


						<table class="tb4cantos" width="70%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center"><b>FINALIZAR</b> Orçamento</td>
			        </tr>

							<tr>
								<td>
									<input type="checkbox" class="radio" name="chk_imprimir_orcamento" id="chk_imprimir_orcamento" />
									Imprimir Orçamento
								</td>
							</tr>

							<tr>
								<td>
									<input type="checkbox" class="radio" name="chk_emitir_nf" id="chk_emitir_nf" />
									Emitir

									<select name="emitir_tipo_orcamento" id="emitir_tipo_orcamento"
										onchange = "javascript: 
											if (document.getElementById('emitir_tipo_orcamento').value != '') document.getElementById('chk_emitir_nf').checked = true;
											else document.getElementById('chk_emitir_nf').checked = false;
									">
										<option value=""></option>
										<option value="ECF">Cupom Fiscal</option>
										<option value="NF">Notal Fiscal</option>
										<option value="SD">Série D</option>
									</select>
								</td>
							</tr>

							<tr><td>&nbsp;</td></tr>

							<tr>
								<td align="center">
		  			  		<input type='button' class="botao_padrao" value="GRAVAR Orçamento" name = "Gravar" id = "Gravar"
										onClick="xajax_Verifica_Campos_Orcamento_AJAX(xajax.getFormValues('for_orcamento'));"
									/>
								</td>
							</tr>

						</table>

          </td>
        </tr>

				<tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">

						<table class="tb4cantos" width="70%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center"><b>IMPRESSÃO</b></td>
			        </tr>

			        <tr><td>&nbsp;</td></tr>

							<tr>
								<td align="center">
									<input name="Imprimir" type="button" class="botao_padrao" value="IMPRIMIR Orçamento"
										onClick="javascript: window.open('{$smarty.server.PHP_SELF}?ac=editar&idorcamento={$info.idorcamento}&imprimir=1');"
									/>
								</td>
							</tr>

						</table>

					</td>
				</tr>


				<tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">

						<table class="tb4cantos" width="70%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center"><b>CANCELAMENTO</b></td>
			        </tr>

			        <tr><td>&nbsp;</td></tr>

							<tr>
								<td align="right">Motivo do cancelamento:</td>
								<td>
									<select name="idmotivo_cancelamento" id="idmotivo_cancelamento">
									<option value="">[selecione]</option>
									{html_options values=$list_motivo_cancelamento.idmotivo_cancelamento output=$list_motivo_cancelamento.descricao selected=$info.numidmotivo_cancelamento}
									</select>
								</td>
							</tr>

							<tr><td>&nbsp;</td></tr>

							<tr>
								<td align="center" colspan="2">
									<input name="Cancelar" type="button" class="botao_padrao" value="CANCELAR Orçamento"
										onClick="xajax_Verifica_Cancelamento_Orcamento_AJAX(xajax.getFormValues('for_orcamento'));"
									/>
								</td>
							</tr>

						</table>

					</td>
				</tr>

				<tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">

						<table class="tb4cantos" width="70%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center"><b>EXCLUSÃO</b></td>
			        </tr>

			        <tr><td>&nbsp;</td></tr>

							<tr>
								<td align="center">
									<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR Orçamento"
										onClick="return(confDelete('for_orcamento','{$smarty.server.PHP_SELF}?ac=excluir&idorcamento={$info.idorcamento}','ATENÇÃO! Confirma a exclusão deste orçamento ?'))"
									/>
								</td>
							</tr>

						</table>

					</td>
				</tr>



			</table>

			</div>

			<script language="javascript">
				Processa_Tabs(0, 'tab_'); // seta o tab inicial
			</script>


		</form>

		</div>



		{* Fecha o IF que testa se o orçamento está cancelado *}
		{/if}  


	{elseif $flags.action == "editarNF"}

	  {* Se a nota fiscal estiver finalizada, chama outro TPL que não permite a edição dos campos. *}
		{if $info.tipoOrcamento != "O"}

			{include file="adm_nota_fiscal_finalizada.tpl"}

		{else}

		<br>

		<div style="width: 100%;">

			<form  action="{$smarty.server.PHP_SELF}?ac=editarNF&idorcamento={$info.idorcamento}" method="post" name="for_orcamento" id="for_orcamento">
	    <input type="hidden" name="for_chk" id="for_chk" value="1" />
	    <input type="hidden" name="endereco_base" id="endereco_base" value="{$conf.addr}" />
	    <input type="hidden" name="idorcamento" id="idorcamento" value="{$smarty.get.idorcamento}" />
	    <input type="hidden" name="idorcamentoFormatado" id="idorcamentoFormatado" value="{$info.idorcamento_formatado}" />
			<input type="hidden" name="maximoItensOrcamento" id="maximoItensOrcamento" value="{$smarty.post.maximoItensOrcamento}" />
			<input type="hidden" name="descontoMaximoOrcamento" id="descontoMaximoOrcamento" value="{$smarty.post.descontoMaximoOrcamento}" />
			<input type="hidden" name="tipoOrcamento" id="tipoOrcamento" value="{$smarty.session.tipo_orcamento}" />
			<input type="hidden" name="tipoVendedor" id="tipoVendedor" value="{$info_funcionario_alterou.tipo_vendedor_funcionario}" />
			<input type="hidden" name="acrescimo_desconto" id="acrescimo_desconto" value="0,00" />
			<input type="hidden" name="tipo_acrescimo_desconto" id="tipo_acrescimo_desconto" value="D" />
			<input type="hidden" name="ordemECF" id="ordemECF" value="0" />
			<input type="hidden" name="usaTEF" id="usaTEF" value="0" />
			<input type="hidden" name="valorTEF" id="valorTEF" value="0" />
			<input type="hidden" name="valorTEF_bk" id="valorTEF_bk" value="0" />
			<input type="hidden" name="finalizaTEF" id="finalizaTEF" value="0" />
			<input type="hidden" name="modo_recebimento_tef" id="modo_recebimento_tef" value="" />
      <input type="hidden" name="data_ecf" id="data_ecf" value="" />
      <input type="hidden" name="hora_ecf" id="hora_ecf" value="" />   
			<input type="hidden" name="travar_teclado" id="travar_teclado" value="{$conf.travar_teclado}" />
			<input type="hidden" name="serie_ecf" id="serie_ecf" value="" />
      <input type="hidden" name="serie_ecf_final" id="serie_ecf_final" value="" />   
			<input type="hidden" name="iniciou_impressao_ecf" id="iniciou_impressao_ecf" value="0" />
			<input type="hidden" name="terminou_impressao_ecf" id="terminou_impressao_ecf" value="0" />
			<input type="hidden" name="reducaoZ_efetuada" id="reducaoZ_efetuada" value="0" />
			<input type="hidden" name="abriu_cupom_ecf" id="abriu_cupom_ecf" value="0" />
			<input type="hidden" name="tipoTransacao" id="tipoTransacao" value="" />

		  <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados Gerais</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Tabela de Produtos</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Transportador / Dados Adicionais</a></li>
				<li><a id="a_tab_3" onclick="Processa_Tabs(3, 'tab_')" href="javascript:;">Contas a Receber</a></li>
				<li><a id="a_tab_4" onclick="Processa_Tabs(4, 'tab_')" href="javascript:;">Finalização</a></li>
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
								<td colspan="9" align="center">Dados Gerais: <b>{$conf.area}</b></td>
			        </tr>

							<tr>
								<td width="50%">
								  <table>
								  	<tr>
								  	  <td align="left" colspan="2">
												<b>Número da Venda: {$info.idorcamento_formatado}</b>
								  	  </td>
								  	</tr>

								  	<tr>
								  	  <td align="left" colspan="2">
								  	  	<input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
												Filial: {$info_filial.nome_filial}
								  	  </td>
								  	</tr>

										{* Deixa escolher o preço apenas se não for ECF *}
										{if $smarty.session.tipo_orcamento != "ECF"}
											<tr>
												<td class="req" align="left" colspan="2">
													Preço de:
													<select name="tipoPreco" id="tipoPreco" onblur="xajax_Calcula_Total_AJAX();">
													<option {if $info.littipoPreco=="B"}selected{/if} value="B">Balcão</option>
													<option {if $info.littipoPreco=="O"}selected{/if} value="O">Oferta</option>
													<option {if $info.littipoPreco=="A"}selected{/if} value="A">Atacado</option>
													<option {if $info.littipoPreco=="T"}selected{/if} value="T">Telemarketing</option>
													</select>
												</td>
											</tr>
										{else}
											<tr>
												<td align="left" colspan="2">
													<input type="hidden" name="tipoPreco" id="tipoPreco" value="B" />
													Preço de: Balcão
												</td>
											</tr>
										{/if}


										{* Tem o CFOP / Modelo / Série apenas se NÃO for Cupom Fiscal *}
										{if $smarty.session.tipo_orcamento != "ECF"}
											<tr>
												<td class="req" align="left">
													CFOP:
													<input class="short" type="text" name="codigo_cfop" id="codigo_cfop" value="" maxlength='4' onkeydown="FormataInteiro('codigo_cfop')" onkeyup="FormataInteiro('codigo_cfop')" onblur="xajax_Busca_Descricao_CFOP_AJAX(xajax.getFormValues('for_orcamento'));" />
												</td>
												<td id="descricao_cfop"></td>
											</tr>

											<tr>
												<td class="req" align="left" colspan="2">
													Modelo da Nota:
													<input class="short" type="text" name="modeloNota" id="modeloNota" value="{$smarty.post.modeloNota}" maxlength='10' />
												</td>
											</tr>

											<tr>
												<td class="req" align="left" colspan="2">
													Série da Nota:
													<input class="short" type="text" name="serieNota" id="serieNota" value="{$smarty.post.serieNota}" maxlength='10' />
												</td>
											</tr>
										{/if}

										{* Vai escrever o numero da nota apenas se for SD *}
										{if $smarty.session.tipo_orcamento == "SD"}
											<tr>
												<td class="req" align="left" colspan="2">
													Número do Documento Fiscal:
													<input class="medium" type="text" name="numeroNota" id="numeroNota" value="{$info.numnumeroNota}" maxlength='6' onkeydown="FormataInteiro('numeroNota')" onkeyup="FormataInteiro('numeroNota')" />
												</td>
											</tr>
										{/if}

									</table>
								</td>
								
								<td align="right">
									Observações:
									<textarea name="obs" id="obs" rows='3' cols='50'>{$info.litobs}</textarea>
								</td>

							</tr>


						</table>


						<table class="tb4cantos" width="100%" cellspacing="0">

							<tr>
								<td align="center" width="35%"></td>
								<td class="tb4cantos" align='center' width="35%">Vendedor</td>
								<td class="tb4cantos" align='center' width="30%">Data / Hora</td>
							</tr>

							<tr>
								<td class="tb4cantos">Criação do Orçamento</td>
								<td align="center" class="tb4cantos">{$info_funcionario_criou.nome_funcionario}</td>
								<td align="center" class="tb4cantos">{$info.datahoraCriacao_D} {$info.datahoraCriacao_H}</td>
							</tr>

							<tr>
								<td class="tb4cantos">Última alteração dos dados</td>
								<td align="center" class="tb4cantos">{$info_funcionario_alterou.nome_funcionario}</td>
								<td align="center" class="tb4cantos">{$info.datahoraUltEmissao_D} {$info.datahoraUltEmissao_H}</td>
							</tr>

						</table>

					</td>
        </tr>

				<tr><td></td></tr>

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca do <b>CLIENTE</b></td>
			        </tr>

			        <input type="hidden" name="dados_cliente_linha_1" id="dados_cliente_linha_1" value="{$info_dados_cliente.dados_cliente_linha_1}">
			        <input type="hidden" name="dados_cliente_linha_2" id="dados_cliente_linha_2" value="{$info_dados_cliente.dados_cliente_linha_2}">
			        <input type="hidden" name="dados_cliente_linha_3" id="dados_cliente_linha_3" value="{$info_dados_cliente.dados_cliente_linha_3}">
			        <input type="hidden" name="dados_cliente_linha_4" id="dados_cliente_linha_4" value="{$info_dados_cliente.dados_cliente_linha_4}">
			        <input type="hidden" name="dados_cliente_linha_5" id="dados_cliente_linha_5" value="{$info_dados_cliente.dados_cliente_linha_5}">

							<tr>
								<td colspan="9" align="center">
									Cliente:
									<input type="hidden" name="idcliente" id="idcliente" value="{$info.numidcliente}" />
									<input type="hidden" name="idcliente_NomeTemp" id="idcliente_NomeTemp" value="{$info.idcliente_NomeTemp}" />
									<input class="ultralarge" type="text" name="idcliente_Nome" id="idcliente_Nome" value="{$info.idcliente_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idcliente');
										"
									/>
									<span class="nao_selecionou" id="idcliente_Flag">
										&nbsp;&nbsp;&nbsp;
									</span>
								</td>
							</tr>
							<tr>
								<td colspan="9" align="center">
								  <div id="dados_cliente">
									</div>
								</td>
							</tr>

							<script type="text/javascript">
							    new CAPXOUS.AutoComplete("idcliente_Nome", function() {ldelim}
							    	return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente" + "&mostraDetalhes=1";
							    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							  // verifica os campos auto-complete
								VerificaMudancaCampo('idcliente');
							</script>


			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Dados provisórios do cliente</td>
			        </tr>


							<tr>
								<td colspan="9">
								  <table width="100%">

										<tr>
											<td align="center">
												Nome do cliente:
												<input class="long" type="text" name="nomeClienteProv" id="nomeClienteProv" maxlength="100" value="{$info.litnomeClienteProv}"/>
											</td>

											<td align="center">
												Informações:
												<textarea name="infoClienteProv" id="infoClienteProv" rows='4' cols='45'>{$info.litinfoClienteProv}</textarea>
											</td>
										</tr>

									</table>
								</td>
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
							    	return "produto_ajax.php?ac=busca_produto_encartelamento&typing=" + this.text.value + "&campoID=idproduto" + "&tipoPreco=" + document.getElementById('tipoPreco').value;
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
										onClick="xajax_Insere_Produto_Encartelamento_AJAX(xajax.getFormValues('for_orcamento')); xajax_Insere_Conta_Receber_A_Prazo_AJAX(xajax.getFormValues('for_orcamento'));"
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
								<td colspan="9" align="center">Tabela de Produtos do Orçamento</td>
								<input type="hidden" name="total_produtos" id="total_produtos" value="0" />
			        </tr>

							<tr>
								<td align="center">

									<div id="div_produtos">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='left' width="5%">Cód.</th>
												<th align='left' width="20%">Produto</th>
												<th align='center' width="5%">Un.</th>
												<th align='center' width="10%">Qtd.</th>
												<th align='center' width="10%">Preço Un.(R$)</th>
												<th align='center' width="10%">Desc.(%)</th>
												<th align='center' width="10%">Preço(R$)</th>
												<th align='center' width="10%">Total(R$)</th>
												<th align='center' width="4%">CST</th>
												<th align='center' width="3%">ST</th>
												<th align='center' width="8%">ICMS(%)</th>
												<th align='center' width="5%">Excluir?</th>
											</tr>
										</table>

									</div>
								</td>
							</tr>

							<script type="text/javascript">
							  // Inicialmente, preenche todos os produtos que fazem parte do orçamento
								xajax_Seleciona_Produtos_AJAX('{$info.idorcamento}', 'editarNF', document.getElementById('tipoOrcamento').value);
							</script>


						</table>
					</td>
        </tr>

				<tr><td>&nbsp;</td></tr>

				<tr>
					<td colspan="2" align="right">
						Desconto: R$
						<input class="short" type="text" name="desconto" id="desconto" value="{$info.numdesconto}" maxlength='10' onkeydown="FormataValor('desconto')" onkeyup="FormataValor('desconto')" onblur="xajax_Calcula_Total_AJAX();" />
					</td>
				</tr>

				<tr><td>&nbsp;</td></tr>

				<tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

							<tr>
								<td align="right">
									Base de cálculo do ICMS: R$
								</td>

								<td align="right">
									Valor do ICMS: R$
								</td>

								<td align="right">
									Base de cálculo ICMS Substituição: R$
								</td>

								<td align="right">
									Valor do ICMS Substituição: R$
								</td>
								
							  <td align="right">
									<input type="hidden" name="valor_total_produtos" id="valor_total_produtos" value="0.00" />
							    Total de Produtos: R$
								</td>
							</tr>


							<tr>
								<td align="right">
									<input class="short" type="text" name="base_calculo_icms" id="base_calculo_icms" value="{$info.numbase_calculo_icms}" maxlength='10' onkeydown="FormataValor('base_calculo_icms')" onkeyup="FormataValor('base_calculo_icms')"  />
								</td>

								<td align="right">
									<input class="short" type="text" name="valor_icms" id="valor_icms" value="{$info.numvalor_icms}" maxlength='10' onkeydown="FormataValor('valor_icms')" onkeyup="FormataValor('valor_icms')"  />
								</td>

								<td align="right">
									<input class="short" type="text" name="base_calc_icms_sub" id="base_calc_icms_sub" value="{$info.numbase_calc_icms_sub}" maxlength='10' onkeydown="FormataValor('base_calc_icms_sub')" onkeyup="FormataValor('base_calc_icms_sub')"  />
								</td>

								<td align="right">
									<input class="short" type="text" name="valor_icms_sub" id="valor_icms_sub" value="{$info.numvalor_icms_sub}" maxlength='10' onkeydown="FormataValor('valor_icms_sub')" onkeyup="FormataValor('valor_icms_sub')"  />
								</td>
								
							  <td align="right" id="TotalProdutos">
							  	0,00
								</td>
							</tr>

							<tr><td>&nbsp;</td></tr>

							<tr>
								<td align="right">
									Valor do Frete: R$
								</td>

								<td align="right">
									Valor do Seguro: R$
								</td>

								<td align="right">
									Outras Despesas: R$
								</td>

								<td align="right">
									Valor Total do IPI: R$
								</td>

							  <td align="right"  class="negrito">
									<input type="hidden" name="valor_total_nota" id="valor_total_nota" value="0.00" />
							    Valor Total da Nota: R$
								</td>
							</tr>

							<tr>
								<td align="right">
									<input class="short" type="text" name="frete" id="frete" value="{$info.numfrete}" maxlength='10' onkeydown="FormataValor('frete')" onkeyup="FormataValor('frete')" onblur="xajax_Calcula_Total_AJAX();" />
								</td>

								<td align="right">
									<input class="short" type="text" name="valor_seguro" id="valor_seguro" value="{$info.numvalor_seguro}" maxlength='10' onkeydown="FormataValor('valor_seguro')" onkeyup="FormataValor('valor_seguro')" onblur="xajax_Calcula_Total_AJAX();" />
								</td>

								<td align="right">
									<input class="short" type="text" name="outras_despesas" id="outras_despesas" value="{$info.numoutras_despesas}" maxlength='10' onkeydown="FormataValor('outras_despesas')" onkeyup="FormataValor('outras_despesas')" onblur="xajax_Calcula_Total_AJAX();" />
								</td>

								<td align="right">
									<input class="short" type="text" name="valor_total_ipi" id="valor_total_ipi" value="{$info.numvalor_total_ipi}" maxlength='10' onkeydown="FormataValor('valor_total_ipi')" onkeyup="FormataValor('valor_total_ipi')" />
								</td>

							  <td align="right" id="TotalNota" class="negrito">
							  	0,00
								</td>
							</tr>


						</table>
					</td>
				</tr>

				<tr><td>&nbsp;</td></tr>

				<tr>
					<td>

						<table width="100%" cellpadding="0">

							<tr>
								<td width="25%" valign="top">

									<table width="100%" cellpadding="0" class="tb4cantos">
										<tr>
											<td colspan="2" align="center" bgcolor="#F7F7F7">
												Legenda
											</td>
										</tr>
			
										<tr>
											<td colspan="2">
												<img src="{$conf.addr}/common/img/lampada.png"> Produtos de referência.
												<br>
												<img src="{$conf.addr}/common/img/exclamacao.png"> Falta no estoque.
												<br>
												<img src="{$conf.addr}/common/img/delete.gif"> Excluir produto deste orçamento.
											</td>
										</tr>
									</table>

								</td>

								<td width="25%" valign="top" align="center">
									<input name="Calcular_Total" type="button" class="botao_padrao" value="Calcular Total!" />
								</td>


								<td width="25%"  valign="top">

									<table width="100%" cellpadding="0" class="tb4cantos">
										<tr>
											<td colspan="2" align="center" bgcolor="#F7F7F7">
												Pesos
											</td>
										</tr>
			
										<tr>
											<td>
												Peso Bruto Total (Kg):
											</td>
											<td align="right" id="Peso_Bruto_Total">
												0,00
											</td>
										</tr>
			
										<tr>
											<td>
												Peso Líquido Total (Kg):
											</td>
											<td align="right" id="Peso_Liquido_Total">
												0,00
											</td>
										</tr>
									</table>

								</td>
							</tr>

						</table>



					</td>


				</tr>


			</table>

			</div>


			{************************************}
			{* TAB 2 *}
			{************************************}

			<div id="tab_2" class="anchor">

			<table width="95%" align="center">

				{* POR ENQUANTO VAI MOSTRAR OS DADOS ADICIONAIS E DE TRANSPORTADOR PARA ECF TAMBÉM *}

				{* Mostra os dados do Transportador e Dados adicionais apenas se NÃO for Cupom Fiscal *}
				{* if $smarty.session.tipo_orcamento == "ECF" *}
				{*
					<tr>
						<td colspan="9" align="center">
					  	{include file="div_resultado_inicio.tpl"}
								Para CUPOM FISCAL não há preenchimento do Transportador e dos Dados adicionais.
					  	{include file="div_resultado_fim.tpl"}
						</td>
					</tr>
				*}	
				{* else *}

				<tr>
					<td colspan="9" align="center">
						Transportador:
						<input type="hidden" name="idtransportador_Tipo" id="idtransportador_Tipo" value="" />
						<input type="hidden" name="idtransportador" id="idtransportador" value="{$smarty.post.idtransportador}" />
						<input type="hidden" name="idtransportador_NomeTemp" id="idtransportador_NomeTemp" value="{$smarty.post.idtransportador_NomeTemp}" />
						<input class="ultralarge" type="text" name="idtransportador_Nome" id="idtransportador_Nome" value="{$smarty.post.idtransportador_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('idtransportador');
							"
						/>
						<span class="nao_selecionou" id="idtransportador_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<tr>
					<td colspan="9" align="center">
					  <div id="dados_transportador">
						</div>
					</td>
				</tr>

				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("idtransportador_Nome", function() {ldelim}
				    	return "transportador_ajax.php?ac=busca_transportador&typing=" + this.text.value + "&campoID=idtransportador" + "&mostraDetalhes=1";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<tr>
					<td align="right" width="40%">Placa do veículo:</td>
					<td><input class="medium" type="text" name="littransportador_placa_veiculo" id="littransportador_placa_veiculo" maxlength="10" value="{$smarty.post.transportador_placa_veiculo}"/></td>
				</tr>
				
				<tr>
					<td align="right">Frete p/ conta:</td>
					<td>
						<select name="littransportador_frete_por_conta" id="littransportador_frete_por_conta">
						<option value="">[selecione]</option>
						<option value="E">Emitente</option>
						<option value="D">Destinatário</option>
						</select>
					</td>
				</tr>

				<tr>
					<td align="right">Quantidade:</td>
					<td><input class="medium" type="text" name="littransportador_quantidade" id="littransportador_quantidade" maxlength="10" value="{$smarty.post.transportador_quantidade}"/></td>
				</tr>
				
				<tr>
					<td align="right">Espécie:</td>
					<td><input class="medium" type="text" name="littransportador_especie" id="littransportador_especie" maxlength="15" value="{$smarty.post.transportador_especie}"/></td>
				</tr>

				<tr>
					<td align="right">Marca:</td>
					<td><input class="medium" type="text" name="littransportador_marca" id="littransportador_marca" maxlength="10" value="{$smarty.post.transportador_marca}"/></td>
				</tr>

				<tr>
					<td align="right">Número:</td>
					<td><input class="medium" type="text" name="littransportador_numero" id="littransportador_numero" maxlength="25" value="{$smarty.post.transportador_numero}"/></td>
				</tr>
				
				<tr>
					<td align="right">Peso Bruto:</td>
					<td><input class="medium" type="text" name="littransportador_peso_bruto" id="littransportador_peso_bruto" maxlength="15" value="{$smarty.post.transportador_peso_bruto}"/></td>
				</tr>
				
				<tr>
					<td align="right">Peso Líquido:</td>
					<td><input class="medium" type="text" name="littransportador_peso_liquido" id="littransportador_peso_liquido" maxlength="15" value="{$smarty.post.transportador_peso_liquido}"/></td>
				</tr>

				<td align="right">Dados Adicionais:</td>
				<td>
					<textarea name="litdados_adicionais" id="litdados_adicionais" rows='4' cols='45'></textarea>
				</td>

			{* /if *}

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
								<td colspan="9" align="left">Pagamento a VISTA</td>
								<input type="hidden" name="total_contas_receber_a_vista" id="total_contas_receber_a_vista" value="0" />
			        </tr>

			        <tr>
								<td colspan="9" align="center">
									<table width="100%">
										<tr>

											<td align="center">
												Modo de recebimento a vista:
												<select name="modo_recebimento_a_vista" id="modo_recebimento_a_vista">
													<option value="">[selecione]</option>
													{html_options values=$list_modo_recebimento_a_vista.sigla_modo_recebimento output=$list_modo_recebimento_a_vista.descricao selected=$smarty.post.modo_recebimento_a_vista}
												</select>

												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

												Valor: R$
												<input class="short" type="text" name="valor_recebimento_a_vista" id="valor_recebimento_a_vista" value="{$smarty.post.valor_recebimento_a_vista}" maxlength='10' onkeydown="FormataValor('valor_recebimento_a_vista')" onkeyup="FormataValor('valor_recebimento_a_vista')" />

												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

												<input name="Gravar" type="button" class="botao_padrao" value="Incluir!"
			        						onClick="xajax_Insere_Conta_Receber_A_Vista_AJAX(xajax.getFormValues('for_orcamento'));"
												/>
											</td>
										</tr>
									</table>

								</td>
			        </tr>

							<tr>
								<td align="center">

									<div id="div_contas_receber_a_vista">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='left' width="40%">Modo de recebimento</th>
												<th align='center' width="40%">Valor(R$)</th>
												<th align='center' width="20%">Excluir ?</th>
											</tr>
										</table>

									</div>
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
								<td align="left" width="60%">Pagamento a PRAZO</td>
								<td align="right" width="30%">
									Total a Financiar: R$
								</td>
				  			<td id="TotalFinanciar" align="right" width="10%">
				  				 0,00
				  			</td>
								<input type="hidden" name="valor_total_financiar" id="valor_total_financiar" value="0.00" />
								<input type="hidden" name="total_contas_receber_a_prazo" id="total_contas_receber_a_prazo" value="0" />
			        </tr>

			        <tr>
								<td colspan="9" align="center">
									<table width="100%">
										<tr>
											<td align='right'>
												Nº de parcelas:
												<input class="tiny" type="text" name="quantidade_de_parcelas" id="quantidade_de_parcelas" value="{$info.quantidade_de_parcelas}" maxlength='2' onkeydown="FormataInteiro('quantidade_de_parcelas')" onkeyup="FormataInteiro('quantidade_de_parcelas')" />
											</td>

											<td align='right'>
												Juros Parcelamento (% a.m.):
												<input class="short" type="text" name="juros_parcelamento" id="juros_parcelamento" value="{$info.juros_parcelamento}" maxlength='10' onkeydown="FormataValor('juros_parcelamento')" onkeyup="FormataValor('juros_parcelamento')" />
											</td>

											<td align='right'>
												Modo de recebimento a prazo:
												<select name="modo_recebimento_a_prazo" id="modo_recebimento_a_prazo">
													<option value="">[selecione]</option>
													{html_options values=$list_modo_recebimento_a_prazo.sigla_modo_recebimento output=$list_modo_recebimento_a_prazo.descricao selected=$info.modo_recebimento_a_prazo}
												</select>
											</td>
										</tr>

										<tr>
											<td align='right'>
												Dias entre as parcelas:
												<input class="tiny" type="text" name="dias_entre_parcelas" id="dias_entre_parcelas" value="{$info.dias_entre_parcelas}" maxlength='10' onkeydown="FormataInteiro('dias_entre_parcelas')" onkeyup="FormataInteiro('dias_entre_parcelas')" />
											</td>

											<td align='right'>
												Data da parcela 1:
												<input class="short" type="text" name="data_parcela1" id="data_parcela1" value="{$info.data_parcela1}" maxlength='10' onkeydown="mask('data_parcela1', 'data')" onkeyup="mask('data_parcela1', 'data')" />
											</td>

											<td align='right'>
												<input name="Gravar" type="button" class="botao_padrao" value="Gerar Parcelas!"
			        						onClick="xajax_Insere_Conta_Receber_A_Prazo_AJAX(xajax.getFormValues('for_orcamento'));"
												/>
											</td>
										</tr>
									</table>

								</td>
			        </tr>


							<tr>
								<td align="center" colspan="9">

									<div id="div_contas_receber_a_prazo">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='left' width="25%">Parcela</th>
												<th align='left' width="25%">Modo de Recebimento</th>
												<th align='center' width="25%">Data</th>
												<th align='center' width="25%">Valor(R$)</th>
											</tr>
										</table>

									</div>
								</td>
							</tr>

						</table>
					</td>
        </tr>

				<script type="text/javascript">
				  // Inicialmente, busca as contas a receber que foram geradas na simulação.
					xajax_Seleciona_Contas_Receber_AJAX('{$info.idorcamento}', xajax.getFormValues('for_orcamento'));
				</script>

        <tr><td>&nbsp;</td></tr>

				<tr>
					<td valign="top" width="30%">
					  {if $smarty.session.tipo_orcamento == "ECF"}
							<table border="0">
								<tr>
									<td>
										TEF:
										<select name="tef_caminho" id="tef_caminho">
											<option value=""></option>
											<option value="{$conf.tef_rede_visa_amex}">Redecard / Visa / Amex</option>
											<option value="{$conf.tef_tecban}">TecBan</option>
											<option value="{$conf.tef_hipercard}">Hipercard</option>
										</select>
									</td>
								</tr>
							</table>
						{/if}
					</td>

				  <td colspan="2" align="center">
				  	<table width="100%" border="0">

				  		<tr>
								<td align="right" colspan="2">
									Total: R$
								</td>
				  			<td id="TotalNota2" colspan="9" align="right" bgcolor="#F7F7F7" width="15%">
				  				 0,00
				  			</td>
							</tr>

				  		<tr>
								<td align="right" colspan="2">
									<input type="hidden" name="valor_total_a_vista" id="valor_total_a_vista" value="0.00" />
									Total a Vista: R$
								</td>
				  			<td id="TotalVista" colspan="9" align="right" bgcolor="#F7F7F7" width="15%">
				  				 0,00
				  			</td>
							</tr>

				  		<tr>
								<td align="right" colspan="2">
									<input type="hidden" name="valor_total_a_prazo" id="valor_total_a_prazo" value="0.00" />
									Total a Prazo: R$
								</td>
				  			<td id="TotalPrazo" colspan="9" align="right" bgcolor="#F7F7F7" width="15%">
				  				 0,00
				  			</td>
							</tr>

				  		<tr>
								<td align="right" colspan="2">
									Total Final: R$
								</td>
				  			<td id="TotalFinal" colspan="9" align="right" bgcolor="#F7F7F7" width="15%">
				  				 0,00
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
<!-- 
        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="70%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center"><b>FUNCIONÁRIO</b> Responsável: (Apenas funcionários da filial "{$info_filial.nome_filial}")</td>
			        </tr>
 
							<tr>
								<td width="40%" class="req" align="right">
									Funcionário:
								</td>
								<td>
									<select name="idfuncionarioNF" id="idfuncionarioNF">
									<option value="">[selecione]</option>
									{html_options values=$list_funcionarios.idfuncionario output=$list_funcionarios.nome_funcionario selected=$smarty.post.idfuncionarioNF}
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
					</td>
        </tr>
 -->
				<tr><td>&nbsp;</td></tr>

				<tr>
          <td colspan="2" align="center">


						<table class="tb4cantos" width="70%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center"><b>FINALIZAR</b> emissão: {$conf.area}</td>
			        </tr>

							{* Mostra a opção de imprimir apenas se for NF *}
							{if $smarty.session.tipo_orcamento == "NF"}
								<tr>
									<td>
										<input type="checkbox" class="radio" name="chk_imprimir_emissao_nf" id="chk_imprimir_emissao_nf" checked />
										Imprimir {$conf.area}
									</td>
								</tr>
							{/if}

							<tr><td>&nbsp;</td></tr>

							<tr>
								<td align="center">
									{* Se for ECF, mostra o botão com IMPRIMIR, pois a impressão será obrigatória no ECF *}
									{if $smarty.session.tipo_orcamento == "ECF"}
										<input type="hidden" name="chk_imprimir_emissao_nf" id="chk_imprimir_emissao_nf" value="1" />	
										<input name="Gravar" type="button" class="botao_padrao" value="GRAVAR e IMPRIMIR"
											onClick="
							                         document.getElementById('serie_ecf_final').value = RecuperaNumeroSerie();
							                         xajax_Verifica_Campos_Orcamento_AJAX(xajax.getFormValues('for_orcamento'));
							                      "
										/>
       
									{else}
										<input name="Gravar" type="button" class="botao_padrao" value="GRAVAR"
											onClick="xajax_Verifica_Campos_Orcamento_AJAX(xajax.getFormValues('for_orcamento'));"
										/>
									{/if}
								</td>
							</tr>

						</table>

          </td>
        </tr>


			</table>

			</div>

			<script language="javascript">
				Processa_Tabs(0, 'tab_'); // seta o tab inicial		
			</script>

			{* Verifica a série da ECF *}
			{if $smarty.session.tipo_orcamento == "ECF"}
				<script language="javascript">
					// busca o número de série de impressora
					NumeroSerie();

					// verifica se já foi feito alguma redução Z no dia
					VerificaReducaoZnoDia();

					// verifica se o número de serie está na tabela auxiliar
					// também verifica se já foi feito alguma redução Z no dia
					xajax_Verifica_Serie_ECF_AJAX(xajax.getFormValues('for_orcamento'));
				</script>
			{/if}


		</form>

		</div>


		{* Fecha o IF que testa se a NF já foi gravada *}
		{/if}

	      
	{elseif $flags.action == "adicionar"}

		<br>
		
		<div style="width: 100%;">

	  	<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name="for_orcamento" id="for_orcamento">
	    <input type="hidden" name="for_chk" id="for_chk" value="1" />
			<input type="hidden" name="maximoItensOrcamento" id="maximoItensOrcamento" value="{$smarty.post.maximoItensOrcamento}" />
			<input type="hidden" name="descontoMaximoOrcamento" id="descontoMaximoOrcamento" value="{$smarty.post.descontoMaximoOrcamento}" />
			<input type="hidden" name="tipoOrcamento" id="tipoOrcamento" value="{$smarty.session.tipo_orcamento}" />

		  <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados Gerais</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Tabela de Produtos</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Simulação Contas Receber</a></li>
				<li><a id="a_tab_3" onclick="Processa_Tabs(3, 'tab_')" href="javascript:;">Finalização</a></li>
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
								<td colspan="9" align="center">Dados Gerais: <b>OR&Ccedil;AMENTO</b></td>
			        </tr>

							<tr>
								<td width="33%" align="center">
									Filial:
								  {$info_filial.nome_filial}
								</td>


								<td width="33%" class="req" align="center">
									Preço de:
									<select name="tipoPreco" id="tipoPreco" onblur="xajax_Calcula_Total_AJAX();">
									<option {if $smarty.post.tipoPreco=="B"}selected{/if} value="B">Balc&atilde;o</option>
									<option {if $smarty.post.tipoPreco=="O"}selected{/if} value="O">Oferta</option>
									<option {if $smarty.post.tipoPreco=="A"}selected{/if} value="A">Atacado</option>
									<option {if $smarty.post.tipoPreco=="T"}selected{/if} value="T">Telemarketing</option>
									</select>
								</td>


								<td width="33%" align="center">
									Validade do Orçamento (dias):
									<input class="short" type="text" name="validade" id="validade" value="{$smarty.post.validade}" maxlength='10' onkeydown="FormataInteiro('validade')" onkeyup="FormataInteiro('validade')" />
								</td>
							</tr>


						</table>


						<table class="tb4cantos" width="100%" cellspacing="0">

							<tr>
								<td align="center" width="35%"></td>
								<td class="tb4cantos" align='center' width="35%">Vendedor</td>
								<td class="tb4cantos" align='center' width="30%">Data / Hora</td>
							</tr>

							<tr>
								<td class="tb4cantos">Criação do Or&ccedil;amento</td>
								<td align="center" class="tb4cantos">-</td>
								<td align="center" class="tb4cantos">{$flags.data_criacao}</td>
							</tr>

							<tr>
								<td class="tb4cantos">Última alteração dos dados</td>
								<td align="center" class="tb4cantos">-</td>
								<td align="center" class="tb4cantos">-</td>
							</tr>

						</table>
						
					</td>
        </tr>

				<tr><td></td></tr>

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca do <b>CLIENTE</b></td>
			        </tr>

							<tr>
								<td colspan="9" align="center">
									Cliente:
									<input type="hidden" name="idcliente" id="idcliente" value="{$smarty.post.idcliente}" />
									<input type="hidden" name="idcliente_NomeTemp" id="idcliente_NomeTemp" value="{$smarty.post.idcliente_NomeTemp}" />
									<input class="ultralarge" type="text" name="idcliente_Nome" id="idcliente_Nome" value="{$smarty.post.idcliente_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idcliente');
										"
									/>
									<span class="nao_selecionou" id="idcliente_Flag">
										&nbsp;&nbsp;&nbsp;
									</span>
								</td>
							</tr>
							<tr>
								<td colspan="9" align="center">
								  <div id="dados_cliente">
									</div>
								</td>
							</tr>

							<script type="text/javascript">
							    new CAPXOUS.AutoComplete("idcliente_Nome", function() {ldelim}
							    	return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente" + "&mostraDetalhes=1";
							    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							  // verifica os campos auto-complete
								VerificaMudancaCampo('idcliente');
							</script>


			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Dados provisórios do cliente</td>
			        </tr>


							<tr>
								<td colspan="9">
								  <table width="100%">

										<tr>
											<td align="center">
												Nome do cliente:
												<input class="long" type="text" name="nomeClienteProv" id="nomeClienteProv" maxlength="100" value="{$smarty.post.nomeClienteProv}"/>
											</td>

											<td align="center">
												Informações:
												<textarea name="infoClienteProv" id="infoClienteProv" rows='4' cols='45'>{$smarty.post.infoClienteProv}</textarea>
											</td>
										</tr>

									</table>
								</td>
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
							    	return "produto_ajax.php?ac=busca_produto_encartelamento&typing=" + this.text.value + "&campoID=idproduto" + "&tipoPreco=" + document.getElementById('tipoPreco').value;
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
										onClick="xajax_Insere_Produto_Encartelamento_AJAX(xajax.getFormValues('for_orcamento'));"
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
								<td colspan="9" align="center">Tabela de Produtos do Or&ccedil;amento</td>
								<input type="hidden" name="total_produtos" id="total_produtos" value="0" />
			        </tr>

							<tr>
								<td align="center">
								
									<div id="div_produtos">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='left' width="5%">C&oacute;d.</th>
												<th align='left' width="35%">Produto</th>
												<th align='center' width="5%">Un.</th>
												<th align='center' width="10%">Qtd.</th>
												<th align='center' width="10%">Pre&ccedil;o Un.(R$)</th>
												<th align='center' width="10%">Desc.(%)</th>
												<th align='center' width="10%">Pre&ccedil;o(R$)</th>
												<th align='center' width="10%">Total(R$)</th>
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
					<td width="25%">
					
						<table width="100%" cellpadding="0">

							<tr><td>&nbsp;</td></tr>

							<tr>
							  <td>
							    Peso Bruto Total (Kg):
								</td>
							  <td align="right" id="Peso_Bruto_Total">
							  	0,00
								</td>
							</tr>
							
							<tr>
							  <td>
							    Peso Líquido Total (Kg):
								</td>
							  <td align="right" id="Peso_Liquido_Total">
							  	0,00
								</td>
							</tr>
							
							<tr><td>&nbsp;</td></tr>
						</table>

						<table width="100%" cellpadding="0" class="tb4cantos">
							<tr>
								<td colspan="2" align="center" bgcolor="#F7F7F7">
									Legenda
								</td>
							</tr>

							<tr>
							  <td colspan="2">
							    <img src="{$conf.addr}/common/img/lampada.png"> Produtos de referência.
									<br>
							    <img src="{$conf.addr}/common/img/exclamacao.png"> Falta no estoque.
									<br>
							    <img src="{$conf.addr}/common/img/delete.gif"> Excluir produto deste orçamento.
								</td>
							</tr>
						</table>

					</td>


					<td>

						<table width="100%" cellpadding="0" border="0">

							<tr>
								<input type="hidden" name="valor_total_produtos" id="valor_total_produtos" value="0.00" />
							  <td width="80%" align="right">
							    Total de Produtos: R$
								</td>
							  <td align="right" id="TotalProdutos">
							  	0,00
								</td>
							</tr>
							
							<tr>
								<td align="right">Desconto: R$</td>
								<td align="right">
									<input class="short" type="text" name="desconto" id="desconto" value="{$smarty.post.desconto}" maxlength='10' onkeydown="FormataValor('desconto')" onkeyup="FormataValor('desconto')" onblur="xajax_Calcula_Total_AJAX();" />
								</td>
							</tr>

							<tr>
								<td align="right">Frete: R$</td>
								<td align="right">
									<input class="short" type="text" name="frete" id="frete" value="{$smarty.post.frete}" maxlength='10' onkeydown="FormataValor('frete')" onkeyup="FormataValor('frete')" onblur="xajax_Calcula_Total_AJAX();" />
								</td>
							</tr>

							<tr>
								<td align="right">Outras Despesas: R$</td>
								<td align="right">
									<input class="short" type="text" name="outras_despesas" id="outras_despesas" value="{$smarty.post.outras_despesas}" maxlength='10' onkeydown="FormataValor('outras_despesas')" onkeyup="FormataValor('outras_despesas')" onblur="xajax_Calcula_Total_AJAX();" />
								</td>
							</tr>


							<tr>
							  <input type="hidden" name="valor_total_nota" id="valor_total_nota" value="0.00" />
							  <td width="80%" align="right">
							    Total: R$
								</td>
							  <td align="right" id="TotalNota">
							  	0,00
								</td>
							</tr>




		    		</table>

					</td>
				</tr>

				<tr>
					<td align="center" colspan="2">
						<input name="Calcular_Total" type="button" class="botao_padrao" value="Calcular Total!" />
					</td>
				</tr>

			</table>

			</div>


			{************************************}
			{* TAB 2 *}
			{************************************}

			<div id="tab_2" class="anchor">

			<table width="95%" align="center">
			
				<tr><td>&nbsp;</td></tr>

		        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

					        <tr bgcolor="#F7F7F7">
								<td align="left" width="60%">PARCELAS</td>
								<td align="right" width="30%">Total: R$	</td>
					  			<td id="TotalFinanciar" align="right" width="10%">0,00</td>
								<input type="hidden" name="valor_total_financiar" id="valor_total_financiar" value="0.00" />
								<input type="hidden" name="total_contas_receber_a_prazo" id="total_contas_receber_a_prazo" value="0" />
			        		</tr>

			        		<tr>
								<td colspan="9" align="center">
									<table width="100%">
										<tr>
											<td align='right'>
												Nº de parcelas:
												<input class="tiny" type="text" name="quantidade_de_parcelas" id="quantidade_de_parcelas" value="{$smarty.post.quantidade_de_parcelas}" maxlength='2' onkeydown="FormataInteiro('quantidade_de_parcelas')" onkeyup="FormataInteiro('quantidade_de_parcelas')" />
											</td>
										</tr>

										<tr>
											<td align='right'>
												Dias entre as parcelas:
												<input class="tiny" type="text" name="dias_entre_parcelas" id="dias_entre_parcelas" value="{$smarty.post.dias_entre_parcelas}" maxlength='10' onkeydown="FormataInteiro('dias_entre_parcelas')" onkeyup="FormataInteiro('dias_entre_parcelas')" />
											</td>

											<td align='right'>
												Data da parcela 1:
												<input class="short" type="text" name="data_parcela1" id="data_parcela1" value="{$smarty.post.data_parcela1}" maxlength='10' onkeydown="mask('data_parcela1', 'data')" onkeyup="mask('data_parcela1', 'data')" />
												<img src="{$conf.addr}/common/img/calendar.png" id="img_data_parcela1" style="cursor: pointer;" />
											</td>									
											<script type="text/javascript">
												Calendar.setup(
													{ldelim}
														inputField : "data_parcela1", // ID of the input field
														ifFormat : "%d/%m/%Y", // the date format
														button : "img_data_parcela1", // ID of the button
														align  : "cR"  // alinhamento
													{rdelim}
												);
											</script>		

											<td align='right'>
												<input name="Gravar" type="button" class="botao_padrao" value="Gerar Parcelas!"
					        						onClick="xajax_Insere_Conta_Receber_A_Prazo_AJAX(xajax.getFormValues('for_orcamento'));"												/>
											</td>
										</tr>
									</table>

								</td>
			        </tr>


							<tr>
								<td align="center" colspan="9">

									<div id="div_contas_receber_a_prazo">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='left' width="20%">Parcela</th>
												<th align='center' width="25%">Conta de D&eacute;bito</th>
												<th align='center' width="25%">Conta de Cr&eacute;dito</th>
												<th align='center' width="15%">Data</th>
												<th align='center' width="15%">Valor(R$)</th>
											</tr>
										</table>

									</div>
								</td>
							</tr>

						</table>
					</td>
        </tr>

        <tr><td>&nbsp;</td></tr>

				<tr>
				  <td colspan="2" align="center">

				  	<table width="100%" border="0">

				  		<tr>
								<td align="right" colspan="2">
									Total: R$
								</td>
				  			<td id="TotalNota2" colspan="9" align="right" bgcolor="#F7F7F7" width="15%">
				  				 0,00
				  			</td>
							</tr>


						</table>
					</td>
        </tr>


			</table>

			</div>





			{************************************}
			{* TAB 3 *}
			{************************************}

			<div id="tab_3" class="anchor">

			<table width="95%" align="center">

<!-- 
        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="70%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center"><b>VENDEDOR</b> Responsável: (Apenas vendedores da filial "{$info_filial.nome_filial}")</td>
			        </tr>

							<tr>
								<td width="40%" class="req" align="right">
									Vendedor:
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
					</td>
        </tr>
						-->
				<tr><td>&nbsp;</td></tr>


				<tr>
          <td colspan="2" align="center">

          
						<table class="tb4cantos" width="70%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center"><b>FINALIZAR</b> Orçamento</td>
			        </tr>

							<tr>
								<td>
									<input type="checkbox" class="radio" name="chk_imprimir_orcamento" id="chk_imprimir_orcamento" />
									Imprimir Orçamento
								</td>
							</tr>

							<tr>
								<td>
									<input type="checkbox" class="radio" name="chk_emitir_nf" id="chk_emitir_nf" />
									Emitir

									<select name="emitir_tipo_orcamento" id="emitir_tipo_orcamento"
										onchange = "javascript: 
											if (document.getElementById('emitir_tipo_orcamento').value != '') document.getElementById('chk_emitir_nf').checked = true;
											else document.getElementById('chk_emitir_nf').checked = false;
									">
										<option value=""></option>
										<option value="ECF">Cupom Fiscal</option>
										<option value="NF">Notal Fiscal</option>
										<option value="SD">Série D</option>
									</select>
								</td>
							</tr>
							
							<tr><td>&nbsp;</td></tr>

							<tr>
								<td align="center">
		  						<input type='button' class="botao_padrao" value="ADICIONAR Orçamento" name = "Adicionar" id = "Adicionar"
										onClick="xajax_Verifica_Campos_Orcamento_AJAX(xajax.getFormValues('for_orcamento'));"
									/>
								</td>
							</tr>

						</table>

          </td>
        </tr>

			</table>
			
			</div>

			<script language="javascript">
				Processa_Tabs(0, 'tab_'); // seta o tab inicial
			</script>


		</form>

		</div>

  {/if}

{/if}


{include file="com_rodape.tpl"}
