{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}

<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/tabs.js"></script>

<script type="text/vbscript" src="{$conf.addr}/common/js/orcamento.vbs"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/orcamento.js"></script>



<script type="text/javascript" src="{$conf.addr}/common/js/jquery-autocomplete/lib/jquery.bgiframe.min.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/jquery-autocomplete/lib/jquery.ajaxQueue.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/jquery-autocomplete/lib/thickbox-compressed.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/jquery-autocomplete/jquery.autocomplete.js"></script>

<!--css -->
<link rel="stylesheet" type="text/css" href="{$conf.addr}/common/js/jquery-autocomplete/jquery.autocomplete.css"/>
<link rel="stylesheet" type="text/css" href="{$conf.addr}/common/js/jquery-autocomplete/lib/thickbox.css"/>




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
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">buscar</a>
				{/if}
				{if $list_permissao.editar == '1'}
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=baixa_conta_receber">dar baixa</a>
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=negociar_conta_receber">negociar</a>
				{/if}
			</td>
		</tr>
	</table>

	{if $flags.action == "adicionar"}
	
		{include file="adm_conta_receber/adicionar.tpl"}
	
	{elseif $flags.action == "listar"}
	
		{include file="adm_conta_receber/listar.tpl"}

  {elseif $flags.action == "negociar_conta_receber"}


		<br>

  	<form  action="{$smarty.server.PHP_SELF}?ac=negociar_conta_receber" method="post" name="for_conta_receber" id="for_conta_receber">
    <input type="hidden" name="for_chk" id="for_chk" value="1" />
			<table width="100%">

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">
			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">NEGOCIAÇÃO: Busca de <b>Contas a Receber</b></td>
			        </tr>


							<tr>
								<td colspan="9" align="center"  class="req">
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


			
							<tr>
								<td colspan="9" align="center">
									Data de vencimento:
									De: 
									<input class="short" type="text" name="data_vencimento_de" id="data_vencimento_de" value="{$smarty.post.data_vencimento_de}" maxlength='10' onkeydown="mask('data_vencimento_de', 'data')" onkeyup="mask('data_vencimento_de', 'data')" />
									<img src="{$conf.addr}/common/img/calendar.png" id="img_data_vencimento_de" style="cursor: pointer;" />
									<script type="text/javascript">
										Calendar.setup(
											{ldelim}
												inputField : "data_vencimento_de", // ID of the input field
												ifFormat : "%d/%m/%Y", // the date format
												button : "img_data_vencimento_de", // ID of the button
												align  : "cR"  // alinhamento
											{rdelim}
										);
									</script>									
											
									At&eacute;:	
									<input class="short" type="text" name="data_vencimento_ate" id="data_vencimento_ate" value="{$smarty.post.data_vencimento_ate}" maxlength='10' onkeydown="mask('data_vencimento_ate', 'data')" onkeyup="mask('data_vencimento_ate', 'data')" /> 
									<img src="{$conf.addr}/common/img/calendar.png" id="img_data_vencimento_ate" style="cursor: pointer;" />
									<script type="text/javascript">
										Calendar.setup(
											{ldelim}
												inputField : "data_vencimento_ate", // ID of the input field
												ifFormat : "%d/%m/%Y", // the date format
												button : "img_data_vencimento_ate", // ID of the button
												align  : "cR"  // alinhamento
											{rdelim}
										);
									</script>	
									(dd/mm/aaaa)

								</td>
							</tr>

							<tr>
			        	<td align="center" colspan="2">
									<input type="button" class="botao_padrao" value="Buscar!" name="button" onClick="xajax_Verifica_Campos_Busca_Rapida_Conta_Receber_AJAX(xajax.getFormValues('for_conta_receber'))" />
			        	</td>
			        </tr>

						</table>
					</td>
        </tr>

			</table>
		</form>		




		{section name=i loop=$list_vendas}

			<table width="100%">
				<tr bgcolor="#F7F7F7">
					<td colspan="9" align="center">Informações da <b>Venda</b></td>
				</tr>

				<tr>

					<td align="center">
						<a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=efetua_negociacao&idvenda={$list_vendas[i].idorcamento}">
							NEGOCIAR
						</a>
					</td>

					<td align="center">
						Código da Venda: {$list_vendas[i].idorcamento}
					</td>

					<td align="center">
						Nº da Nota: {$list_vendas[i].numeroNota}
					</td>

					<td align="center">
						Tipo: {$list_vendas[i].tipoOrcamentoDescricao}
					</td>

					<td align="center">
						Cliente: {$list_vendas[i].nome_cliente}
					</td>

					<td align="center">
						Data da Criação: {$list_vendas[i].datahoraCriacao}
					</td>

					<td align="center">
						Vendedor Responsável: {$list_vendas[i].funcionario_criou_orcamento}
					</td>
				</tr>

				<tr><td>&nbsp;</td></tr>

				<tr>
					<td colspan="10">
						<table border="0" width="100%">
			
				
							<tr>
								<th align='center'>Seq.</th>
								<th align='center'>Modo</th>
								<th align='center'>Descrição</th>
								<th align='center'>Conta (R$)</th>
								<th align='center'>Juros (R$)</th>
								<th align='center'>Multa (R$)</th>
								<th align='center'>Recebido (R$)</th>
								<th align='center'>D. Vencimento</th>
								<th align='center'>D. Recebimento</th>
								<th align='center'>D. Baixa</th>
								<th align='center'>Status</th>
								<th align='center'>Baixado ?</th>
							</tr>


							{section name=j loop=$list_contas_receber[i]}
				
								<tr>
									<td align='center'>{$list_contas_receber[i][j].numero_seq_conta}</td>
									<td>{$list_contas_receber[i][j].descricao_modo_recebimento}</td>
									<td>{$list_contas_receber[i][j].descricao_conta}</td>
									<td align='right'>{$list_contas_receber[i][j].valor_total_conta}</td>
									<td align='right'>{$list_contas_receber[i][j].valor_juros_atraso}</td>
									<td align='right'>{$list_contas_receber[i][j].valor_multa}</td>
									<td align='right'>{$list_contas_receber[i][j].valor_recebido}</td>
									<td align='center'>{$list_contas_receber[i][j].data_vencimento}</td>
									<td align='center'>{$list_contas_receber[i][j].data_recebimento}</td>
									<td align='center'>{$list_contas_receber[i][j].data_baixa}</td>
									<td align='center'>{$list_contas_receber[i][j].status_conta}</td>
									<td align='center'>{$list_contas_receber[i][j].baixa_conta}</td>
								</tr>
								
								<tr>
									<td class="row" height="1" bgcolor="#999999" colspan="20"></td>
								</tr>

							{/section}

						</table>
					</td>
				</tr>


				<tr><td>&nbsp;</td></tr>

			</table>

			<br>

		{/section}



  {elseif $flags.action == "efetua_negociacao"}

		<br>


		<div style="width: 100%;">

			<form  action="{$smarty.server.PHP_SELF}?ac=efetua_negociacao&idvenda={$smarty.get.idvenda}" method="post" name = "for_orcamento" id = "for_orcamento">
	    		<input type="hidden" name="for_chk" id="for_chk" value="1" />
				<input type="hidden" name="idvenda" id="idvenda" value="{$smarty.get.idvenda}" />
				<input type="hidden" name="serie_ecf" id="serie_ecf" value="" /> {* Input para receber o número série da ECF cadastrada*}

		  <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Selecionar Contas a Receber</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Selecionar Contas a Receber</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Gerar novas Contas a Receber</a></li>
				<li><a id="a_tab_3" onclick="Processa_Tabs(3, 'tab_')" href="javascript:;">Finalização</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

			<div id="tab_0" class="anchor">

			<table width="99%" align="center">


        <tr>
					<td colspan="2" align="center">
	
						<table class="tb4cantos" width="100%">
							<tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Informações da <b>Venda</b></td>
							</tr>
			
							<tr>
								<td align="center">
									Código da Venda: {$list_vendas[0].idorcamento}
								</td>
			
								<td align="center">
									Nº da Nota: {$list_vendas[0].numeroNota}
								</td>
			
								<td align="center">
									Tipo: {$list_vendas[0].tipoOrcamentoDescricao}
								</td>
			
								<td align="center">
									Cliente: {$list_vendas[0].nome_cliente}
								</td>
			
								<td align="center">
									Data da Criação: {$list_vendas[0].datahoraCriacao}
								</td>
			
								<td align="center">
									Vendedor Responsável: {$list_vendas[0].funcionario_criou_orcamento}
								</td>
							</tr>
						</table>

					</td>
        </tr>
				
				<tr><td>&nbsp;</td></tr>

				<tr>
					<td colspan="10">
						<table border="0" width="100%">
			
				
							<tr>
								<th align='center'>Neg.</th>

								<th align='center'>Seq.</th>
								<th align='center'>Modo</th>
								<th align='center'>Descrição</th>
								<th align='center'>Conta (R$)</th>
								<th align='center'>Juros (R$)</th>
								<th align='center'>Multa (R$)</th>
								<th align='center'>Recebido (R$)</th>
								<th align='center'>D. Vencimento</th>
								<th align='center'>D. Recebimento</th>
								<th align='center'>D. Baixa</th>
								<th align='center'>Status</th>
								<th align='center'>Baixado ?</th>

								<th align='center'>J. Corr(R$)</th>
								<th align='center'>V. Corr(R$)</th>
							</tr>


							{section name=i loop=$list_contas_receber}
				
								<tr>

									{if $list_contas_receber[i].negociar == "1"}
										<td align='center'>
											<input type="checkbox" name="id_cr_{$list_contas_receber[i].idconta_receber}" id="id_cr_{$list_contas_receber[i].idconta_receber}"
												onclick="xajax_Limpa_Divida_Atual_AJAX(xajax.getFormValues('for_orcamento'));"
											/>
										</td>
									{else}
										<td align='center'></td>
									{/if}		

									<td align='center'>{$list_contas_receber[i].numero_seq_conta}</td>
									<td>{$list_contas_receber[i].descricao_modo_recebimento}</td>
									<td>{$list_contas_receber[i].descricao_conta}</td>
									<td align='right'>{$list_contas_receber[i].valor_total_conta}</td>
									<td align='right'>{$list_contas_receber[i].valor_juros_atraso}</td>
									<td align='right'>{$list_contas_receber[i].valor_multa}</td>
									<td align='right'>{$list_contas_receber[i].valor_recebido}</td>
									<td align='center'>{$list_contas_receber[i].data_vencimento}</td>
									<td align='center'>{$list_contas_receber[i].data_recebimento}</td>
									<td align='center'>{$list_contas_receber[i].data_baixa}</td>
									<td align='center'>{$list_contas_receber[i].status_conta}</td>
									<td align='center'>{$list_contas_receber[i].baixa_conta}</td>

									<td align='center' id="juros_cr_{$list_contas_receber[i].idconta_receber}"></td>
									<td align='center' id="valor_cr_{$list_contas_receber[i].idconta_receber}"></td>
								</tr>

								<tr>
									<td class="row" height="1" bgcolor="#999999" colspan="20"></td>
								</tr>

							{/section}

						</table>
					</td>
				</tr>


				<tr><td>&nbsp;</td></tr>

				<tr>
					<td colspan="10">
						<table border="0" width="100%">

							<tr>
								<td align="right" width="15%">(+) Juros de atraso (% a.d.):</td>
								<td width="30%">
									<input class="short" type="text" name="juros_atraso" id="juros_atraso" value="{$info.juros_atraso}" maxlength='5' onkeydown="FormataValor('juros_atraso')" onkeyup="FormataValor('juros_atraso')" />
									(Aplicado as contas vencidas)
								</td>

								<td align="right" width="45%">Valor Negociado: (R$)</td>
								<td align="right" id="valor_negociado" class="negrito"></td>
							</tr>

							<tr>
								<td align="right">(-) Juros de desconto (% a.d.):</td>
								<td>
									<input class="short" type="text" name="juros_desconto" id="juros_desconto" value="{$info.juros_desconto}" maxlength='5' onkeydown="FormataValor('juros_desconto')" onkeyup="FormataValor('juros_desconto')" />
									(Aplicado as contas NÃO vencidas)
								</td>

								<td align="right" width="45%">Valor da Dívida Atual (em {$flags.data_atual}): (R$)</td>
								<td align="right" id="valor_divida_atual" class="negrito"></td>
								<input type="hidden" name="valor_total_nota" id="valor_total_nota" value="0.00" />
							</tr>	

							<tr>
								<td align="right"></td>
								<td>
									<input type='button' class="botao_padrao" value="Calcular Dívida Atual" name = "Calcular_Divida" id = "Calcular_Divida"
										onClick="xajax_Calcular_Divida_Atual_AJAX(xajax.getFormValues('for_orcamento'));"
									/>
								</td>
								<input type="hidden" name="valor_basico_total" id="valor_basico_total" value="0.00" />
							</tr>	

						</table>
					</td>
				</tr>



			</table>

			</div>


			{************************************}
			{* TAB 1 *}
			{************************************}
						
			<div id="tab_1" class="anchor" style="height:350px;">

			<table width="95%" align="center">

				<tr>
				  <td colspan="3" align="center">
				  
				  	<tr><td><br></td></tr>
					
					<tr><td align="center" colspan="2"><b>Plano de Contas para pagamento À VISTA</b></td></tr>
					
					<tr><td><br></td></tr>
					
					<tr>
						<td align="right" class="req">Conta de Débito:</td>
						<td>
	
						    <input type="hidden" name="idplano_debito_vista" id="idplano_debito_vista" value="{$info.plano_debito.idplano}" />
							<input type="hidden" name="idplano_debito_vista_NomeTemp" id="idplano_debito_vista_NomeTemp" value="{$info.plano_debito.descricao}" />
	                       	<input class="long" type="text" name="idplano_debito_vista_Nome" id="idplano_debito_vista_Nome" value="{$info.plano_debito.descricao}"
								onKeyUp="javascript:
									VerificaMudancaCampo('idplano_debito_vista');"/>
							<span class="nao_selecionou" id="idplano_debito_vista_Flag">
								&nbsp;&nbsp;&nbsp;
							</span>
	
	
						</td>
					</tr>
					
								
					
					<tr>
						<td align="right" class="req">Conta de Crédito:</td>
						<td>
	
						    <input type="hidden" name="idplano_credito_vista" id="idplano_credito_vista" value="{$info.plano_credito.idplano}" />
							<input type="hidden" name="idplano_credito_vista_NomeTemp" id="idplano_credito_vista_NomeTemp" value="{$info.plano_credito.descricao}" />
	                       	<input class="long" type="text" name="idplano_credito_vista_Nome" id="idplano_credito_vista_Nome" value="{$info.plano_credito.descricao}"
								onKeyUp="javascript:
									VerificaMudancaCampo('idplano_credito_vista');"/>
							<span class="nao_selecionou" id="idplano_credito_vista_Flag">
								&nbsp;&nbsp;&nbsp;
							</span>
	
	
						</td>
					</tr>
					<tr><td><br></td></tr>
					
					
					
					<tr><td><br></td></tr>
					
					<tr><td align="center"  colspan="2"><b>Plano de Contas para pagamento A PRAZO</b></td></tr>
					
					<tr><td><br></td></tr>
					 
					<tr>
						<td align="right" width="40%" class="req">Conta de Débito:</td>
						<td>
	
						    <input type="hidden" name="idplano_debito_prazo" id="idplano_debito_prazo" value="{$info.plano_debito.idplano}" />
							<input type="hidden" name="idplano_debito_prazo_NomeTemp" id="idplano_debito_prazo_NomeTemp" value="{$info.plano_debito.descricao}" />
	                       	<input class="long" type="text" name="idplano_debito_prazo_Nome" id="idplano_debito_prazo_Nome" value="{$info.plano_debito.descricao}"
								onKeyUp="javascript:
									VerificaMudancaCampo('idplano_debito_prazo');"/>
							<span class="nao_selecionou" id="idplano_debito_prazo_Flag">
								&nbsp;&nbsp;&nbsp;
							</span>
	
	
						</td>
					</tr>
					
								
					
					<tr>
						<td align="right" class="req">Conta de Crédito:</td>
						<td>
	
						    <input type="hidden" name="idplano_credito_prazo" id="idplano_credito_prazo" value="{$info.plano_credito.idplano}" />
							<input type="hidden" name="idplano_credito_prazo_NomeTemp" id="idplano_credito_prazo_NomeTemp" value="{$info.plano_credito.descricao}" />
	                       	<input class="long" type="text" name="idplano_credito_prazo_Nome" id="idplano_credito_prazo_Nome" value="{$info.plano_credito.descricao}"
								onKeyUp="javascript:
									VerificaMudancaCampo('idplano_credito_prazo');"/>
							<span class="nao_selecionou" id="idplano_credito_prazo_Flag">
								&nbsp;&nbsp;&nbsp;
							</span>
	
	
						</td>
					</tr>
					
					<script language="javascript">
					    new CAPXOUS.AutoComplete("idplano_credito_vista_Nome", function() {ldelim}
					    	return "plano_ajax.php?ac=busca_plano&typing=" + this.text.value + "&idplano=" + document.getElementById('idplano_credito_vista').value + "&campoID=idplano_credito_vista";
					    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
						
											
					    new CAPXOUS.AutoComplete("idplano_debito_vista_Nome", function() {ldelim}
					    	return "plano_ajax.php?ac=busca_plano&typing=" + this.text.value + "&idplano=" + document.getElementById('idplano_debito_vista').value + "&campoID=idplano_debito_vista";
					    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
					
						new CAPXOUS.AutoComplete("idplano_credito_prazo_Nome", function() {ldelim}
					    	return "plano_ajax.php?ac=busca_plano&typing=" + this.text.value + "&idplano=" + document.getElementById('idplano_credito_prazo').value + "&campoID=idplano_credito_prazo";
					    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
						
											
					    new CAPXOUS.AutoComplete("idplano_debito_prazo_Nome", function() {ldelim}
					    	return "plano_ajax.php?ac=busca_plano&typing=" + this.text.value + "&idplano=" + document.getElementById('idplano_debito_prazo').value + "&campoID=idplano_debito_prazo";
					    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
					
					
					  	// verifica os campos auto-complete
						VerificaMudancaCampo('idplano_debito_vista');
						VerificaMudancaCampo('idplano_credito_vista');
						VerificaMudancaCampo('idplano_debito_prazo');
						VerificaMudancaCampo('idplano_credito_prazo');
	
					</script>				
					
					<tr><td>&nbsp;</td></tr>
				  
				  
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

												<input name="Gravar" type="button" class="botao_padrao" value="Incluir"
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
			{* TAB 3 *}
			{************************************}

			<div id="tab_3" class="anchor">

			<table width="95%" align="center">

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="70%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center"><b>FUNCIONÁRIO</b> Responsável: (Apenas funcionários da filial "{$info_filial.nome_filial}")</td>
			        </tr>

							<tr>
								<td class="req" align="right" width="40%">
									Funcionário:
								</td>
								<td>
									<select name="idUltFuncionario" id="idUltFuncionario">
									<option value="">[selecione]</option>
									{html_options values=$list_funcionarios.idfuncionario output=$list_funcionarios.nome_funcionario selected=$smarty.post.idUltFuncionario}
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


							<tr><td>&nbsp;</td></tr>
			
							<tr>
								<td align="center" colspan="2">
									<input type='button' class="botao_padrao" value="EXECUTAR Negociação" name = "ALTERAR" id = "ALTERAR"
										onClick="NumeroSerie();xajax_Verifica_Campos_Negociacao_AJAX(xajax.getFormValues('for_orcamento'));"
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



	{elseif $flags.action == "editar"}
	
		{include file="adm_conta_receber/editar.tpl"}

	{elseif $flags.action == "baixa_conta_receber"}

		{include file="adm_conta_receber/baixa_conta_receber.tpl"}

  	{/if}
	

{/if}

{include file="com_rodape.tpl"}
