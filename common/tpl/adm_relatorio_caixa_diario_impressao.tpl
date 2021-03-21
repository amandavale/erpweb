{include file="com_cabecalho_relatorio.tpl"}

		{* --------- MOSTRA O DADOS GERAIS ------------  *}
		<table class="tb4cantos" width="100%">
			<tr bgcolor="#F7F7F7">
				<td colspan="9" align="center">Dados do Relatório de Caixa Diário</td>
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
				<td align="right">Data / Hora:</td>
				<td>{$flags.data_hora_atual}</td>
			</tr>		
		</table>

		<br>

		{* --------- MOSTRA O TROCO ------------  *}
		<table class="tb4cantos" width="100%">
			<tr bgcolor="#F7F7F7">
				<td colspan="9" align="center">TROCO</td>
			</tr>

			<tr>
				<td width="80%" align="right"><span class="req">{$flags.msg_troco_nao_cadastrado}</span> Valor do Troco (R$):</td>
				<td align="right"><b>{$flags.valor_troco}<b></td>
			</tr>		
		</table>

		<br>


		{* --------- MOSTRA AS SAIDAS ------------  *}
		<table class="tb4cantos" width="100%">
			<tr bgcolor="#F7F7F7">
				<td colspan="9" align="center">SAÍDAS</td>
			</tr>

			<tr>
				<td colspan="9" align="center">
					<table width="100%">
			
						<tr>
							<th align='center'>No</th>
							<th align='center'>Tipo da Conta a pagar</th>
							<th align='center'>Descrição da conta</th>
							<th align='center'>Valor da conta (R$)</th>
							<th align='center'>Valor pago (R$)</th>
							<th align='center'>Data de vencimento</th>
							<th align='center'>Data do pagamento</th>
							<th align='center'>Status da conta</th>
							<th align='center'>Valor que saiu do caixa (R$)</th>
						</tr>
			
						{section name=i loop=$list_saidas}
							<tr  bgcolor = "{if $list_saidas[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
								
								<td>{$list_saidas[i].index}</td>
								<td>{$list_saidas[i].descricao_conta_pagar}</td>
								<td>{$list_saidas[i].descricao_conta}</td>
								<td align="right">{$list_saidas[i].valor_conta}</td>
								<td align="right">{$list_saidas[i].valor_pago}</td>
								<td align="center">{$list_saidas[i].data_vencimento}</td>
								<td align="center">{$list_saidas[i].data_pagamento}</td>
								<td align="center">{$list_saidas[i].status_conta}</td>
								<td align="right">{$list_saidas[i].valor_saiu_caixa}</td>
							</tr>
							
							<tr>
								<td class="row" height="1" bgcolor="#999999" colspan="9"></td>
							</tr>
						{/section}

					</table>
				</td>
			</tr>

			<tr>
				<td width="80%" align="right">Total de Saídas (R$):</td>
				<td align="right"><b>{$flags.valor_saidas}<b></td>
			</tr>	
		</table>

		<br>


		{* --------- MOSTRA AS ENTRADAS ------------  *}
		<table class="tb4cantos" width="100%">
			<tr bgcolor="#F7F7F7">
				<td colspan="9" align="center">ENTRADAS</td>
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
								<td>{$list_contas_receber[i].descricao_conta}</td>
								<td>{$list_contas_receber[i].descricao_modo_recebimento}</td>
								<td align="center">{$list_contas_receber[i].nome_cliente}</td>
								<td align="center">{$list_contas_receber[i].data_vencimento}</td>
								<td align="center">{$list_contas_receber[i].data_recebimento}</td>
								<td align="center">{$list_contas_receber[i].datahoraCadastro_D}</td>
								<td align="right">{$list_contas_receber[i].valor_total_conta}</td>
								<td align="right">{$list_contas_receber[i].valor_juros_atraso}</td>
								<td align="right">{$list_contas_receber[i].valor_multa}</td>
								<td align="right">{$list_contas_receber[i].valor_recebido}</td>
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
				<td width="80%" align="right"><b>Resumo das Entradas</b></td>
			</tr>	

			{section name=i loop=$list_modo_recebimento}
				<tr>
					<td align="right">{$list_modo_recebimento[i].campo} (R$):</td>
					<td align="right">{$list_modo_recebimento[i].valor}</td>
				</tr>
			{/section}

			<tr><td>&nbsp;</td></tr>

			<tr>
				<td width="80%" align="right">Total de Entradas (R$):</td>
				<td align="right"><b>{$flags.valor_entradas}<b></td>
			</tr>	
		</table>


		<br>

		{* --------- MOSTRA O SALDO ------------  *}
		<table class="tb4cantos" width="100%">
			<tr bgcolor="#F7F7F7">
				<td colspan="9" align="center">SALDO</td>
			</tr>

			<tr>
				<td width="80%" align="right">Saldo Final (R$):</td>
				<td align="right"><b>{$flags.valor_saldo}<b></td>
			</tr>	
		</table>



{include file="com_rodape_relatorio.tpl"}
