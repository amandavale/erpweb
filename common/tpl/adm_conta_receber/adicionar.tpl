		<br>

		<div style="width: 100%;">

		<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_conta_receber" id = "for_conta_receber">
	    	<input type="hidden" name="for_chk" id="for_chk" value="1" />
			
			<ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados Gerais</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Valores</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

			<div id="tab_0" class="anchor">

			<table width="95%" align="center">


				<tr><td>&nbsp;</td></tr>

				<tr>
					<td align="right" width="40%">
					Filial:
					</td>
					<td>
						<input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
						{$info_filial.nome_filial}
					</td>
				</tr>

				
				<tr>
					<td align="right" class="req" >Descrição da conta a receber:</td>
					<td> <input type="text" name="descricao_conta" id=="descricao_conta" class="long" /></td>
				</tr>

				<tr>
					<td align="right">Observação:</td>
					<td>
						<textarea name="observacao" id="observacao" rows='6' cols='37'>{$smarty.post.observacao}</textarea>
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
				

			</table>

			</div>


			{************************************}
			{* TAB 1 *}
			{************************************}

			<div id="tab_1" class="anchor">

			<table width="95%" align="center">

				<tr>
					<td align="right" class="req" width="40%">Valor da conta a receber (R$):</td>
					<td>
						<input type="text" name="valor_basico_conta" id="valor_basico_conta" value="{$smarty.post.valor_basico_conta}" onkeydown="FormataValor('valor_basico_conta')" onkeyup="FormataValor('valor_basico_conta')" />
					</td>
				</tr>

				
				<tr>
					<td class="req" align="right">Modo de recebimento:</td>
					<td>
						<select name="sigla_modo_recebimento" id="sigla_modo_recebimento">
						<option value="">[selecione]</option>
						{html_options values=$list_modo_recebimento.sigla_modo_recebimento output=$list_modo_recebimento.descricao selected=$smarty.post.sigla_modo_recebimento}
						</select>
					</td>
				</tr>
											
				<tr>
					<td align="right" class="req">Data do Vencimento:</td>
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
				
				

			</table>

			</div>


			<script language="javascript">
				Processa_Tabs(0, 'tab_'); // seta o tab inicial
			</script>


			<table width="100%">
				
				<tr><td>&nbsp;</td></tr>

				<tr>
					<td align="center" colspan="2">
						<input type='button' class="botao_padrao" value="SALVAR" name = "ALTERAR" id = "ALTERAR"
							onClick="xajax_Verifica_Campos_Conta_Receber_AJAX(xajax.getFormValues('for_conta_receber'));"
						/>
					</td>
				</tr>

			</table>

		</form>

		</div>
