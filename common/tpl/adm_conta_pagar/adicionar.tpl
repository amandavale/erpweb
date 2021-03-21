		<br>

		<div style="width: 100%;">

	  	<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_conta_pagar" id = "for_conta_pagar">
	    <input type="hidden" name="for_chk" id="for_chk" value="1" />

		  <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados Gerais</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Valores</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Cheques</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

			<div id="tab_0" class="anchor">

			<table width="95%" align="center">

				<tr>
					<td align="right" width="40%">
					Filial:
					</td>
					<td>
						<input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
						{$info_filial.nome_filial}
					</td>
				</tr>


				<!--tr>
					<td align="right">Tipo da Conta a pagar:</td>
					<td>
						<select name="idconta_pagar_tipo" id="idconta_pagar_tipo">
						<option value="">[selecione]</option>
						{html_options values=$list_conta_pagar_tipo.idconta_pagar_tipo output=$list_conta_pagar_tipo.descricao_conta_pagar selected=$smarty.post.idconta_pagar_tipo}
						</select>
					</td>
				</tr-->
				
				<tr>
					<td align="right">* Descrição da conta:</td>
					<td><input class="long" type="text" name="descricao_conta" id="descricao_conta" maxlength="100" value="{$smarty.post.descricao_conta}"/></td>
				</tr>

				<tr>
					<td align="right">Observação:</td>
					<td>
						<textarea name="observacao" id="observacao" rows='6' cols='38'>{$smarty.post.observacao}</textarea>
					</td>
				</tr>

				<tr><td>&nbsp;</td></tr>

				 
				 <tr>
					<td align="right" class="req">Conta de Débito:</td>
					<td>

					    <input type="hidden" name="idplano_debito" id="idplano_debito" value="{$info.plano_debito.idplano}" />
						<input type="hidden" name="idplano_debito_NomeTemp" id="idplano_debito_NomeTemp" value="{$info.plano_debito.descricao}" />
                       	<input class="long" type="text" name="idplano_debito_Nome" id="idplano_debito_Nome" value="{$info.plano_debito.descricao}"
							onKeyUp="javascript:
								VerificaMudancaCampo('idplano_debito');"/>
						<span class="nao_selecionou" id="idplano_debito_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>


					</td>
				</tr>
				
				<script language="javascript">
				    new CAPXOUS.AutoComplete("idplano_debito_Nome", function() {ldelim}
				    	return "plano_ajax.php?ac=busca_plano&typing=" + this.text.value + "&idplano=" + document.getElementById('idplano_debito').value + "&campoID=idplano_debito";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>
				
				
				<tr>
					<td align="right" class="req">Conta de Crédito:</td>
					<td>

					    <input type="hidden" name="idplano_credito" id="idplano_credito" value="{$info.plano_credito.idplano}" />
						<input type="hidden" name="idplano_credito_NomeTemp" id="idplano_credito_NomeTemp" value="{$info.plano_credito.descricao}" />
                       	<input class="long" type="text" name="idplano_credito_Nome" id="idplano_credito_Nome" value="{$info.plano_credito.descricao}"
							onKeyUp="javascript:
								VerificaMudancaCampo('idplano_credito');"/>
						<span class="nao_selecionou" id="idplano_credito_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>


					</td>
				</tr>
				
				<script language="javascript">
				    new CAPXOUS.AutoComplete("idplano_credito_Nome", function() {ldelim}
				    	return "plano_ajax.php?ac=busca_plano&typing=" + this.text.value + "&idplano=" + document.getElementById('idplano_credito').value + "&campoID=idplano_credito";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				
				  	// verifica os campos auto-complete
					VerificaMudancaCampo('idplano_debito');
					VerificaMudancaCampo('idplano_credito');

				</script>				
				
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

				<!--tr>
					<td colspan="9">* Caso não esteja cadastrada em "Tipo da Conta a pagar".</td>
				</tr-->

			</table>

			</div>


			{************************************}
			{* TAB 1 *}
			{************************************}

			<div id="tab_1" class="anchor">

			<table width="95%" align="center">

				<tr>
					<td class="req" align="right">Valor da conta (R$):</td>
					<td>
						<input class="short" type="text" name="valor_conta" id="valor_conta" value="{$smarty.post.valor_conta}" maxlength='10' onkeydown="FormataValor('valor_conta')" onkeyup="FormataValor('valor_conta')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Juros da conta (R$):</td>
					<td>
						<input class="short" type="text" name="juros_conta" id="juros_conta" value="{$smarty.post.juros_conta}" maxlength='10' onkeydown="FormataValor('juros_conta')" onkeyup="FormataValor('juros_conta')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Multa da conta (R$):</td>
					<td>
						<input class="short" type="text" name="multa_conta" id="multa_conta" value="{$smarty.post.multa_conta}" maxlength='10' onkeydown="FormataValor('multa_conta')" onkeyup="FormataValor('multa_conta')" />
					</td>
				</tr>

				<tr>
					<td align="right" class="req">Valor pago (R$):</td>
					<td>
						<input class="short" type="text" name="valor_pago" id="valor_pago" value="{if $smarty.post.valor_pago}{$smarty.post.valor_pago}{else}0,00{/if}" maxlength='10' onkeydown="FormataValor('valor_pago')" onkeyup="FormataValor('valor_pago')" />
					</td>
				</tr>

				<tr><td>&nbsp;</td></tr>
				
				<tr>
					<td class="req" align="right">Data de vencimento:</td>
					<td>
						<input class="short" type="text" name="data_vencimento" id="data_vencimento" value="{$smarty.post.data_vencimento}" maxlength='10' onkeydown="mask('data_vencimento', 'data')" onkeyup="mask('data_vencimento', 'data')" />
						<img src="{$conf.addr}/common/img/calendar.png" id="img_data_vencimento" style="cursor: pointer;" /> (dd/mm/aaaa)
					</td>
				</tr>
				<script type="text/javascript">
					Calendar.setup(
						{ldelim}
							inputField : "data_vencimento", // ID of the input field
							ifFormat : "%d/%m/%Y", // the date format
							button : "img_data_vencimento", // ID of the button
							align  : "cR"  // alinhamento
						{rdelim}
					);
				</script>					


				<tr>
					<td align="right">Data do pagamento:</td>
					<td>
						<input class="short" type="text" name="data_pagamento" id="data_pagamento" value="{$smarty.post.data_pagamento}" maxlength='10' onkeydown="mask('data_pagamento', 'data')" onkeyup="mask('data_pagamento', 'data')" />
						<img src="{$conf.addr}/common/img/calendar.png" id="img_data_pagamento" style="cursor: pointer;" /> (dd/mm/aaaa)
					</td>
				</tr>
				<script type="text/javascript">
					Calendar.setup(
						{ldelim}
							inputField : "data_pagamento", // ID of the input field
							ifFormat : "%d/%m/%Y", // the date format
							button : "img_data_pagamento", // ID of the button
							align  : "cR"  // alinhamento
						{rdelim}
					);
				</script>						


				<tr>
					<td class="req" align="right">Status da conta:</td>
					<td>
						<input {if $smarty.post.status_conta=="N"}checked{/if} class="radio" type="radio" name="status_conta" id="status_conta" value="N" />Não pago
						<input {if $smarty.post.status_conta=="P"}checked{/if} class="radio" type="radio" name="status_conta" id="status_conta" value="P" />Pago
					</td>
				</tr>
				

				<tr><td>&nbsp;</td></tr>

				<tr>
          <td colspan="2" align="center">

						<table class="tb4cantos" width="70%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center"><b>Controle do Caixa Diário</b></td>
			        </tr>

			        <tr>
								<td colspan="9" align="center">Caso tenha saído algum dinheiro do caixa para ajudar a pagar esta conta, preencha os campos abaixo.</td>
			        </tr>
				
							<tr><td>&nbsp;</td></tr>

							<tr>
								<td align="right" width="50%">Saiu algum dinheiro do caixa ?</td>
								<td>
									<input {if $smarty.post.saiu_do_caixa=="0"}checked{/if} class="radio" type="radio" name="saiu_do_caixa" id="saiu_do_caixa" value="0" />Não
									<input {if $smarty.post.saiu_do_caixa=="1"}checked{/if} class="radio" type="radio" name="saiu_do_caixa" id="saiu_do_caixa" value="1" />Sim
								</td>
							</tr>
							
							<tr>
								<td align="right">Valor que saiu do caixa (R$):</td>
								<td>
									<input class="short" type="text" name="valor_saiu_caixa" id="valor_saiu_caixa" value="{$smarty.post.valor_saiu_caixa}" maxlength='10' onkeydown="FormataValor('valor_saiu_caixa')" onkeyup="FormataValor('valor_saiu_caixa')" />
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


        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Dados do Cheque</td>
			        </tr>

			        <tr>
								<td colspan="9" align="center">Informe os cheques que foram utilizados para pagar esta conta.</td>
			        </tr>

							<tr><td>&nbsp;</td></tr>

							<tr>
								<td colspan="9" align="center">

									Código do Cheque:
									<input class="medium" type="text" name="idcheque" id="idcheque" value="{$smarty.post.idcheque}" maxlength='20' onkeydown="FormataInteiro('idcheque')" onkeyup="FormataInteiro('idcheque')" />

									<input type='button' class="botao_padrao" value="Inserir cheque" name="botaoInserirCheque" id="botaoInserirCheque"
										onClick="xajax_Insere_Cheque_AJAX(xajax.getFormValues('for_conta_pagar'));"
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
								<td colspan="9" align="center">Tabela de Cheques utilizados para pagar a conta</td>
								<input type="hidden" name="total_cheques" id="total_cheques" value="0" />
			        </tr>

							<tr>
								<td align="center">
									<div id="div_cheques">


									</div>
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
					<td colspan="2" align="center">
						<input type='button' class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar"
							onClick="xajax_Verifica_Campos_Conta_Pagar_AJAX(xajax.getFormValues('for_conta_pagar'));"
						/>
					</td>
				</tr>

			</table>


		</form>

		</div>

