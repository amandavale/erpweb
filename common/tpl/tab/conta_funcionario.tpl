<table width="95%" align="center">

	<tr>
		<td colspan="2" align="center">
		<table class="tb4cantos" width="100%">

			<tr bgcolor="#F7F7F7">
				<td colspan="9" align="center">Dados da <b>Conta Bancária</b></td>
			</tr>

			<tr>
				<td colspan="9" align="center">Banco: <input type="hidden"
					name="idbanco" id="idbanco" value="{$smarty.post.idbanco}" /> <input
					type="hidden" name="idbanco_NomeTemp" id="idbanco_NomeTemp"
					value="{$smarty.post.idbanco_NomeTemp}" /> <input
					class="extralarge" type="text" name="idbanco_Nome"
					id="idbanco_Nome" value="{$smarty.post.idbanco_Nome}"
					onKeyUp="javascript:
											VerificaMudancaCampo('idbanco');
										" /> <span class="nao_selecionou" id="idbanco_Flag">
				&nbsp;&nbsp;&nbsp; </span></td>
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
				<td align="right">Agência:</td>
				<td><input class="medium" type="text" name="agencia_funcionario"
					id="agencia_funcionario" maxlength="12"
					value="{$smarty.post.agencia_funcionario}" /> - <input class="tiny"
					type="text" name="agencia_dig_funcionario"
					id="agencia_dig_funcionario" maxlength="2"
					value="{$smarty.post.agencia_dig_funcionario}" /></td>
			</tr>

			<tr>
				<td align="right">Conta:</td>
				<td><input class="medium" type="text" name="conta_funcionario"
					id="conta_funcionario" maxlength="12"
					value="{$smarty.post.conta_funcionario}" /> - <input class="tiny"
					type="text" name="conta_dig_funcionario" id="conta_dig_funcionario"
					maxlength="2" value="{$smarty.post.conta_dig_funcionario}" /></td>
			</tr>

			<tr>
				<td align="right">Conta principal ?</td>
				<td><input {if $smarty.post.principal_funcionario == "0"}checked{/if} class="radio" type="radio" name="principal_funcionario" id="principal_funcionario" value="0" />Não
				<input {if $smarty.post.principal_funcionario == "1"}checked{/if} class="radio" type="radio" name="principal_funcionario" id="principal_funcionario" value="1" />Sim
				</td>
			</tr>


			<tr>
				<td colspan="9" align="center"><input type='button'
					class="botao_padrao" value="Inserir conta bancária"
					name="botaoInserirConta" id="botaoInserirConta"
					onClick="xajax_Insere_Conta_Bancaria_AJAX(xajax.getFormValues('for_funcionario'));" />

				</td>
			</tr>

		</table>
		</td>
	</tr>


	<tr>
		<td>&nbsp;</td>
	</tr>


	<tr>
		<td colspan="2" align="center">
		<table class="tb4cantos" width="100%">

			<tr bgcolor="#F7F7F7">
				<td colspan="9" align="center">Tabela de Contas Bancárias da Filial</td>
				<input type="hidden" name="total_contas_bancarias"
					id="total_contas_bancarias" value="0" />
			</tr>

			<tr>
				<td align="center">
				<div id="div_contas_bancarias">

				<table width="100%" cellpadding="5">
					<tr>
						<th align='center' width="35%">Banco</th>
						<th align='center' width="15%">Agência</th>
						<th align='center' width="15%">Conta</th>
						<th align='center' width="10%">Principal ?</th>
						<th align='center' width="5%">Excluir ?</th>
					</tr>
				</table>

				</div>
				</td>
			</tr>

			<script type="text/javascript">
							  // Inicialmente, preenche todos os funcionarioes que fazem parte da filial
        				xajax_Seleciona_Conta_Bancaria_AJAX('{$info.idfuncionario}');

							</script>

		</table>
		</td>
	</tr>

</table>
