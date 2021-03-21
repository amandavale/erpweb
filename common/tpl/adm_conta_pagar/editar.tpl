		<br>

		<div style="width: 100%;">

			<form  action="{$smarty.server.PHP_SELF}?ac=editar&idconta_pagar={$info.idconta_pagar}" method="post" name = "for_conta_pagar" id = "for_conta_pagar">
	    <input type="hidden" name="for_chk" id="for_chk" value="1" />

		  <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados Gerais</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Valores</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Cheques</a></li>
			</ul>

			{************************************}
			{* TAB 0 							*}
			{************************************}

			<div id="tab_0" class="anchor">

			<table width="95%" align="center">


        <tr>
					<td colspan="2" align="center">

						<table class="tb4cantos" width="100%" cellspacing="0">

							<tr>
								<td align="center" width="35%"></td>
								<td class="tb4cantos" align='center' width="35%">Funcionário</td>
								<td class="tb4cantos" align='center' width="30%">Data / Hora</td>
							</tr>

							<tr>
								<td class="tb4cantos">Criação da Conta a Pagar</td>
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
					<td align="right" width="40%">
					Filial:
					</td>
					<td>
						<input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
						{$info_filial.nome_filial}
					</td>
				</tr>


				<tr>
					<td align="right">* Descrição da conta:</td>
					<td><input class="long" type="text" name="litdescricao_conta" id="litdescricao_conta" maxlength="100" value="{$info.litdescricao_conta}"/></td>
				</tr>

				<tr>
					<td align="right">Observação:</td>
					<td>
						<textarea name="litobservacao" id="litobservacao" rows='6' cols='38'>{$info.litobservacao}</textarea>
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
						<input class="short" type="text" name="numvalor_conta" id="numvalor_conta" value="{$info.numvalor_conta}" maxlength='10' onkeydown="FormataValor('numvalor_conta')" onkeyup="FormataValor('numvalor_conta')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Juros da conta (R$):</td>
					<td>
						<input class="short" type="text" name="numjuros_conta" id="numjuros_conta" value="{$info.numjuros_conta}" maxlength='10' onkeydown="FormataValor('numjuros_conta')" onkeyup="FormataValor('numjuros_conta')" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Multa da conta (R$):</td>
					<td>
						<input class="short" type="text" name="nummulta_conta" id="nummulta_conta" value="{$info.nummulta_conta}" maxlength='10' onkeydown="FormataValor('nummulta_conta')" onkeyup="FormataValor('nummulta_conta')" />
					</td>
				</tr>

				<tr>
					<td align="right">Valor pago (R$):</td>
					<td>
						<input class="short" type="text" name="numvalor_pago" id="numvalor_pago" value="{$info.numvalor_pago}" maxlength='10' onkeydown="FormataValor('numvalor_pago')" onkeyup="FormataValor('numvalor_pago')" />
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>				
				
				<tr>
					<td class="req" align="right">Data de vencimento:</td>
					<td>
						<input class="short" type="text" name="litdata_vencimento" id="litdata_vencimento" value="{$info.litdata_vencimento}" maxlength='10' onkeydown="mask('litdata_vencimento', 'data')" onkeyup="mask('litdata_vencimento', 'data')" />
						<img src="{$conf.addr}/common/img/calendar.png" id="img_litdata_vencimento" style="cursor: pointer;" /> (dd/mm/aaaa)
					</td>
				</tr>
				<script type="text/javascript">
					Calendar.setup(
						{ldelim}
							inputField : "litdata_vencimento", // ID of the input field
							ifFormat : "%d/%m/%Y", // the date format
							button : "img_litdata_vencimento", // ID of the button
							align  : "cR"  // alinhamento
						{rdelim}
					);
				</script>		

				
				<tr>
					<td align="right">Data do pagamento:</td>
					<td>
						<input class="short" type="text" name="litdata_pagamento" id="litdata_pagamento" value="{$info.litdata_pagamento}" maxlength='10' onkeydown="mask('litdata_pagamento', 'data')" onkeyup="mask('litdata_pagamento', 'data')" />
						<img src="{$conf.addr}/common/img/calendar.png" id="img_litdata_pagamento" style="cursor: pointer;" /> (dd/mm/aaaa)
					</td>
				</tr>
				<script type="text/javascript">
					Calendar.setup(
						{ldelim}
							inputField : "litdata_pagamento", // ID of the input field
							ifFormat : "%d/%m/%Y", // the date format
							button : "img_litdata_pagamento", // ID of the button
							align  : "cR"  // alinhamento
						{rdelim}
					);
				</script>						


				<tr>
					<td class="req" align="right">Status da conta:</td>
					<td>
						<input {if $info.litstatus_conta=="N"}checked{/if} class="radio" type="radio" name="litstatus_conta" id="litstatus_conta" value="N" />Não pago
						<input {if $info.litstatus_conta=="P"}checked{/if} class="radio" type="radio" name="litstatus_conta" id="litstatus_conta" value="P" />Pago
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
									<input {if $info.litsaiu_do_caixa=="0"}checked{/if} class="radio" type="radio" name="litsaiu_do_caixa" id="litsaiu_do_caixa" value="0" />Não
									<input {if $info.litsaiu_do_caixa=="1"}checked{/if} class="radio" type="radio" name="litsaiu_do_caixa" id="litsaiu_do_caixa" value="1" />Sim
								</td>
							</tr>
							
							<tr>
								<td align="right">Valor que saiu do caixa (R$):</td>
								<td>
									<input class="short" type="text" name="numvalor_saiu_caixa" id="numvalor_saiu_caixa" value="{$info.numvalor_saiu_caixa}" maxlength='10' onkeydown="FormataValor('numvalor_saiu_caixa')" onkeyup="FormataValor('numvalor_saiu_caixa')" />
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

							<script type="text/javascript">
								// busca os dados do cheque
								xajax_Seleciona_Cheques_Conta_Pagar_AJAX('{$info.idconta_pagar}');
							</script>		


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
						<input type='button' class="botao_padrao" value="SALVAR" name = "ALTERAR" id = "ALTERAR"
							onClick="xajax_Verifica_Campos_Conta_Pagar_AJAX(xajax.getFormValues('for_conta_pagar'));"
						/>
						&nbsp;&nbsp;&nbsp;
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for_conta_pagar','{$smarty.server.PHP_SELF}?ac=excluir&idconta_pagar={$info.idconta_pagar}','ATENÇÃO! Confirma a exclusão ?'))" >
        	</td>
        </tr>

			</table>


		</form>

		</div>


