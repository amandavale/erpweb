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
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">busca rápida</a>
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

  	<form  action="{$smarty.server.PHP_SELF}?ac=listar" method="post" name="for_cheque" id="for_cheque">
    <input type="hidden" name="for_chk" id="for_chk" value="1" />
			<table width="100%">

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">
			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca rápida do Cheque</td>
			        </tr>

							<tr>
			        	<td align="center" colspan="2" class="req">
									Código do Cheque:
			        	  <input class="medium" type="text" name="idcheque" id="idcheque" value="{$smarty.post.idcheque}" maxlength='20' onkeydown="FormataInteiro('idcheque')" onkeyup="FormataInteiro('idcheque')" />
									<input type="button" class="botao_padrao" value="Buscar!" name="button" onClick="xajax_Verifica_Campos_Busca_Rapida_Cheque_AJAX(xajax.getFormValues('for_cheque'))" />
			        	</td>
			        </tr>

						</table>
					</td>
        </tr>

			</table>
		</form>

		{if $flags.buscar_dados_cheque == 1}
			<script type="text/javascript">
				// busca os dados do cheque
				xajax_Verifica_Campos_Busca_Rapida_Cheque_AJAX(xajax.getFormValues('for_cheque'), '{$smarty.post.idcheque_inserido}');
			</script>		
		{/if}

		<div id="dados_cheque">
		</div>
		

	{elseif $flags.action == "editar"}
	
		<br>


		<div style="width: 100%;">

			<form  action="{$smarty.server.PHP_SELF}?ac=editar&idcheque={$info.idcheque}" method="post" name = "for_cheque" id = "for_cheque">
	    <input type="hidden" name="for_chk" id="for_chk" value="1" />

		  <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados do Cheque</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Origem / Destino</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

			<div id="tab_0" class="anchor">

			<table width="95%" align="center">


				<tr>
					<td align='right' width='40%'><b>CÓDIGO DO CHEQUE:</b></td>
					<td><b>{$info.idcheque}</b></td>
				</tr>

				<tr>
					<td class="req" align="right">Banco:</td>
					<td>
						<input type="hidden" name="numidbanco" id="idbanco" value="{$info.numidbanco}" />
						<input type="hidden" name="idbanco_NomeTemp" id="idbanco_NomeTemp" value="{$info.idbanco_NomeTemp}" />
						<input class="extralarge" type="text" name="idbanco_Nome" id="idbanco_Nome" value="{$info.idbanco_Nome}"
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
					<td class="req" align="right">Agência:</td>
					<td>
						<input class="medium" type="text" name="litagencia" id="litagencia" maxlength="15" value="{$info.litagencia}"/>
						-
						<input class="tiny" type="text" name="litagencia_dig" id="litagencia_dig" maxlength="2" value="{$info.litagencia_dig}"/>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Conta:</td>
					<td>
						<input class="medium" type="text" name="litconta" id="litconta" maxlength="15" value="{$info.litconta}"/>
						-
						<input class="tiny" type="text" name="litconta_dig" id="litconta_dig" maxlength="2" value="{$info.litconta_dig}"/>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Nº do cheque:</td>
					<td><input class="medium" type="text" name="litnumero_cheque" id="litnumero_cheque" maxlength="30" value="{$info.litnumero_cheque}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Data do cheque:</td>
					<td>
						<input class="short" type="text" name="litdata_cheque" id="litdata_cheque" value="{$info.litdata_cheque}" maxlength='10' onkeydown="mask('litdata_cheque', 'data')" onkeyup="mask('litdata_cheque', 'data')" />
						<img src="{$conf.addr}/common/img/calendar.png" id="img_litdata_cheque" style="cursor: pointer;" /> (dd/mm/aaaa)
					</td>
				</tr>
				<script type="text/javascript">
					Calendar.setup(
						{ldelim}
							inputField : "litdata_cheque", // ID of the input field
							ifFormat : "%d/%m/%Y", // the date format
							button : "img_litdata_cheque", // ID of the button
							align  : "cR"  // alinhamento
						{rdelim}
					);
				</script>						


				<tr>
					<td align="right">Data (Bom para):</td>
					<td>
						<input class="short" type="text" name="litbom_para" id="litbom_para" value="{$info.litbom_para}" maxlength='10' onkeydown="mask('litbom_para', 'data')" onkeyup="mask('litbom_para', 'data')" />
						<img src="{$conf.addr}/common/img/calendar.png" id="img_litbom_para" style="cursor: pointer;" /> (dd/mm/aaaa)
					</td>
				</tr>
				<script type="text/javascript">
					Calendar.setup(
						{ldelim}
							inputField : "litbom_para", // ID of the input field
							ifFormat : "%d/%m/%Y", // the date format
							button : "img_litbom_para", // ID of the button
							align  : "cR"  // alinhamento
						{rdelim}
					);
				</script>		

				
				<tr>
					<td align="right">Titular da conta:</td>
					<td><input class="long" type="text" name="littitular_conta" id="littitular_conta" maxlength="100" value="{$info.littitular_conta}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Valor do cheque (R$):</td>
					<td>
						<input class="short" type="text" name="numvalor" id="numvalor" value="{$info.numvalor}" maxlength='10' onkeydown="FormataValor('numvalor')" onkeyup="FormataValor('numvalor')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Observação:</td>
					<td>
						<textarea name="litobservacao" id="litobservacao" rows='6' cols='38'>{$info.litobservacao}</textarea>
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

						<table class="tb4cantos" width="100%">
							<tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">ORIGEM (Contas a Receber)</td>
							</tr>
				
							<tr>
								<td colspan="9" align="center">
									<table width="100%">
							
										<tr>
											<th align='center'>Descrição</th>
											<th align='center'>Modo</th>
											<th align='center'>Cliente</th>
											<th align='center'>D. Vencimento</th>
											<th align='center'>D. Recebimento</th>
											<th align='center'>D. Cadastro</th>
											<th align='center'>Conta (R$)</th>
											<th align='center'>Juros (R$)</th>
											<th align='center'>Multa (R$)</th>
											<th align='center'>Recebido (R$)</th>
										</tr>
							
										{section name=i loop=$list_contas_receber}
											<tr>
												<td><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].descricao_conta}</a></td>
												<td><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].descricao_modo_recebimento}</a></td>
												<td align="center"><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].nome_cliente}</a></td>
												<td align="center"><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].data_vencimento}</a></td>
												<td align="center"><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].data_recebimento}</a></td>
												<td align="center"><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].datahoraCadastro_D}</a></td>
												<td align="right"><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].valor_total_conta}</a></td>
												<td align="right"><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].valor_juros_atraso}</a></td>
												<td align="right"><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].valor_multa}</a></td>
												<td align="right"><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].valor_recebido}</a></td>
											</tr>
				
											<tr>
												<td class="row" height="1" bgcolor="#999999" colspan="20"></td>
											</tr>
										{/section}
				
									</table>
								</td>
							</tr>
				
						</table>

					</td>
				</tr>


				<tr><td>&nbsp;</td></tr>

				<tr>
					<td>

						<table class="tb4cantos" width="100%">
							<tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">DESTINO (Contas a Pagar)</td>
							</tr>

							<tr>
								<td colspan="9" align="center">
									<table width="100%">

										<tr>
											<th align='center'>Tipo da Conta a pagar</th>
											<th align='center'>Descrição da conta</th>
											<th align='center'>Valor da conta (R$)</th>
											<th align='center'>Valor pago (R$)</th>
											<th align='center'>Data de vencimento</th>
											<th align='center'>Data do pagamento</th>
											<th align='center'>Status da conta</th>
										</tr>
						
										{section name=i loop=$list_contas_pagar}
											<tr>
												
												<td><a class='menu_item' href = "{$conf.addr}/admin/conta_pagar.php?ac=editar&idconta_pagar={$list_contas_pagar[i].idconta_pagar}">{$list_contas_pagar[i].descricao_conta_pagar}</a></td>
												<td><a class='menu_item' href = "{$conf.addr}/admin/conta_pagar.php?ac=editar&idconta_pagar={$list_contas_pagar[i].idconta_pagar}">{$list_contas_pagar[i].descricao_conta}</a></td>
												<td align="right"><a class='menu_item' href = "{$conf.addr}/admin/conta_pagar.php?ac=editar&idconta_pagar={$list_contas_pagar[i].idconta_pagar}">{$list_contas_pagar[i].valor_conta}</a></td>
												<td align="right"><a class='menu_item' href = "{$conf.addr}/admin/conta_pagar.php?ac=editar&idconta_pagar={$list_contas_pagar[i].idconta_pagar}">{$list_contas_pagar[i].valor_pago}</a></td>
												<td align="center"><a class='menu_item' href = "{$conf.addr}/admin/conta_pagar.php?ac=editar&idconta_pagar={$list_contas_pagar[i].idconta_pagar}">{$list_contas_pagar[i].data_vencimento}</a></td>
												<td align="center"><a class='menu_item' href = "{$conf.addr}/admin/conta_pagar.php?ac=editar&idconta_pagar={$list_contas_pagar[i].idconta_pagar}">{$list_contas_pagar[i].data_pagamento}</a></td>
												<td align="center"><a class='menu_item' href = "{$conf.addr}/admin/conta_pagar.php?ac=editar&idconta_pagar={$list_contas_pagar[i].idconta_pagar}">{$list_contas_pagar[i].status_conta}</a></td>
											</tr>
											
											<tr>
												<td class="row" height="1" bgcolor="#999999" colspan="9"></td>
											</tr>
										{/section}

									</table>
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


			<table width="100%">
				
				<tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
						<input type='button' class="botao_padrao" value="ALTERAR" name = "ALTERAR" id = "ALTERAR"
							onClick="xajax_Verifica_Campos_Cheque_AJAX(xajax.getFormValues('for_cheque'));"
						/>
						&nbsp;&nbsp;&nbsp;
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for_cheque','{$smarty.server.PHP_SELF}?ac=excluir&idcheque={$info.idcheque}','ATENÇÃO! Confirma a exclusão ?'))" >
        	</td>
        </tr>

			</table>


		</form>

		</div>

	      
	      
	{elseif $flags.action == "adicionar"}

		<br>

		<div style="width: 100%;">

			<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_cheque" id = "for_cheque">
	    <input type="hidden" name="for_chk" id="for_chk" value="1" />

		  <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados do Cheque</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Origem / Destino</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

			<div id="tab_0" class="anchor">

			<table width="95%" align="center">

				
				<tr>
					<td class="req" align="right" width='40%'>Banco:</td>
					<td>
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
					<td class="req" align="right">Agência:</td>
					<td>
						<input class="medium" type="text" name="agencia" id="agencia" maxlength="15" value="{$smarty.post.agencia}"/>
						-
						<input class="tiny" type="text" name="agencia_dig" id="agencia_dig" maxlength="2" value="{$smarty.post.agencia_dig}"/>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Conta:</td>
					<td>
						<input class="medium" type="text" name="conta" id="conta" maxlength="15" value="{$smarty.post.conta}"/>
						-
						<input class="tiny" type="text" name="conta_dig" id="conta_dig" maxlength="2" value="{$smarty.post.conta_dig}"/>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Nº do cheque:</td>
					<td><input class="medium" type="text" name="numero_cheque" id="numero_cheque" maxlength="30" value="{$smarty.post.numero_cheque}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Data do cheque:</td>
					<td>
						<input class="short" type="text" name="data_cheque" id="data_cheque" value="{$smarty.post.data_cheque}" maxlength='10' onkeydown="mask('data_cheque', 'data')" onkeyup="mask('data_cheque', 'data')" />
						<img src="{$conf.addr}/common/img/calendar.png" id="img_data_cheque" style="cursor: pointer;" /> (dd/mm/aaaa)
					</td>
				</tr>
				<script type="text/javascript">
					Calendar.setup(
						{ldelim}
							inputField : "data_cheque", // ID of the input field
							ifFormat : "%d/%m/%Y", // the date format
							button : "img_data_cheque", // ID of the button
							align  : "cR"  // alinhamento
						{rdelim}
					);
				</script>		


				<tr>
					<td align="right">Data (Bom para):</td>
					<td>
						<input class="short" type="text" name="bom_para" id="bom_para" value="{$smarty.post.bom_para}" maxlength='10' onkeydown="mask('bom_para', 'data')" onkeyup="mask('bom_para', 'data')" />
						<img src="{$conf.addr}/common/img/calendar.png" id="img_bom_para" style="cursor: pointer;" /> (dd/mm/aaaa)
					</td>
				</tr>
				<script type="text/javascript">
					Calendar.setup(
						{ldelim}
							inputField : "bom_para", // ID of the input field
							ifFormat : "%d/%m/%Y", // the date format
							button : "img_bom_para", // ID of the button
							align  : "cR"  // alinhamento
						{rdelim}
					);
				</script>		

				
				<tr>
					<td align="right">Titular da conta:</td>
					<td><input class="long" type="text" name="titular_conta" id="titular_conta" maxlength="100" value="{$smarty.post.titular_conta}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">Valor do cheque (R$):</td>
					<td>
						<input class="short" type="text" name="valor" id="valor" value="{$smarty.post.valor}" maxlength='10' onkeydown="FormataValor('valor')" onkeyup="FormataValor('valor')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Observação:</td>
					<td>
						<textarea name="observacao" id="observacao" rows='6' cols='38'>{$smarty.post.observacao}</textarea>
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
          <td align="center">
						As informações de Origem / Destino do cheque são disonibilizadas na tela de edição dos dados.
          </td>
        </tr>

			</table>

			</div>


			<script language="javascript">
				Processa_Tabs(0, 'tab_'); // seta o tab inicial
			</script>


			<table width="100%">
				
				<tr><td>&nbsp;</td></tr>

				<tr>
          <td colspan="2" align="center">
							<input type='button' class="botao_padrao" value="ADICIONAR" name = "ADICIONAR" id = "ADICIONAR"
								onClick="xajax_Verifica_Campos_Cheque_AJAX(xajax.getFormValues('for_cheque'));"
							/>
          </td>
        </tr>

			</table>


		</form>

		</div>




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
					<th align='center'>Cód. Cheque</th>
					<th align='center'>Banco</th>
					<th align='center'>Agência</th>
					<th align='center'>Conta</th>
					<th align='center'>Data</th>
					<th align='center'>Valor(R$)</th>
					<th align='center'>Titular</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

						<td align='center'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcheque={$list[i].idcheque}"><b>{$list[i].idcheque}</b></a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcheque={$list[i].idcheque}">{$list[i].nome_banco}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcheque={$list[i].idcheque}">{$list[i].agencia}-{$list[i].agencia_dig}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcheque={$list[i].idcheque}">{$list[i].conta}-{$list[i].conta_dig}</a></td>
						<td align='center'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcheque={$list[i].idcheque}">{$list[i].data_cheque}</a></td>
						<td align='right'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcheque={$list[i].idcheque}">{$list[i].valor}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcheque={$list[i].idcheque}">{$list[i].titular_conta}</a></td>

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
					<td align="right">Banco:</td>
					<td><input class="long" type="text" name="banco" id="banco" maxlength="50" value="{$flags.banco}"/></td>
				</tr>

				<tr>
					<td align="right">Agência:</td>
					<td><input class="medium" type="text" name="agencia" id="agencia" maxlength="50" value="{$flags.agencia}"/></td>
				</tr>

				<tr>
					<td align="right">Conta:</td>
					<td><input class="medium" type="text" name="conta" id="conta" maxlength="50" value="{$flags.conta}"/></td>
				</tr>

				<tr>
					<td align="right">Nº do cheque:</td>
					<td><input class="medium" type="text" name="numero_cheque" id="numero_cheque" maxlength="50" value="{$flags.numero_cheque}"/></td>
				</tr>

				<tr>
					<td align="right">Data do cheque:</td>
					<td><input class="short" type="text" name="data_cheque" id="data_cheque" value="{$flags.data_cheque}" maxlength='10' onkeydown="mask('data_cheque', 'data')" onkeyup="mask('data_cheque', 'data')" /> (dd/mm/aaaa)</td>
				</tr>

				<tr>
					<td align="right">Titular da conta:</td>
					<td><input class="long" type="text" name="titular_conta" id="titular_conta" maxlength="100" value="{$flags.titular_conta}"/></td>
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
					<th align='center'>Cód. Cheque</th>
					<th align='center'>Banco</th>
					<th align='center'>Agência</th>
					<th align='center'>Conta</th>
					<th align='center'>Data</th>
					<th align='center'>Valor(R$)</th>
					<th align='center'>Titular</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

						<td align='center'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcheque={$list[i].idcheque}"><b>{$list[i].idcheque}</b></a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcheque={$list[i].idcheque}">{$list[i].nome_banco}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcheque={$list[i].idcheque}">{$list[i].agencia}-{$list[i].agencia_dig}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcheque={$list[i].idcheque}">{$list[i].conta}-{$list[i].conta_dig}</a></td>
						<td align='center'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcheque={$list[i].idcheque}">{$list[i].data_cheque}</a></td>
						<td align='right'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcheque={$list[i].idcheque}">{$list[i].valor}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcheque={$list[i].idcheque}">{$list[i].titular_conta}</a></td>

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
				<input type="hidden" name="banco" id="banco" value="{$flags.banco}"/>
				<input type="hidden" name="agencia" id="agencia" value="{$flags.agencia}"/>
				<input type="hidden" name="conta" id="conta" value="{$flags.conta}"/>
				<input type="hidden" name="numero_cheque" id="numero_cheque" value="{$flags.numero_cheque}"/>
				<input type="hidden" name="data_cheque" id="data_cheque" value="{$flags.data_cheque}"/>
				<input type="hidden" name="titular_conta" id="titular_conta" value="{$flags.titular_conta}"/>

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
