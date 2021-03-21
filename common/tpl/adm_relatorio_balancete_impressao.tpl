{include file="com_cabecalho_relatorio.tpl"}


		<br>


			<table width="100%">

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">
			
							<tr>
								<td align="center">
									Filial:
									<input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
									{$info_filial.nome_filial}
								</td>
							</tr>


							<tr>
								<td colspan="9" align="center">
									Vendedor: {$info_vendedor.nome_funcionario}
								</td>
							</tr>
			
							<tr>
								<td colspan="9" align="center">
									* Data:
									De: {$smarty.post.data_recebimento_de}
									Até:	
									{$smarty.post.data_recebimento_ate}
								</td>
							</tr>

						</table>
					</td>
        </tr>

			</table>


		{if count($list_contas_receber)} 

			<table border="0" width="100%">
		
		
				<tr>
					<th align='center'>Descrição</th>
					<th align='center'>Modo</th>
					<th align='center'>D. Vencimento</th>
					<th align='center'>D. Recebimento</th>
					<th align='center'>D. Baixa</th>
					<th align='center'>V. Básico(R$)</th>
					<th align='center'>J. Parcela(R$)</th>
					<th align='center'>J. Atraso(R$)</th>
					<th align='center'>Multa(R$)</th>
					<th align='center'>Recebido(R$)</th>
					<th align='center'>Comissão(%)</th>
					<th align='center'>Fat.Corr(%)</th>
					<th align='center'>V. Comissão(R$)</th>
				</tr>
		
		
				{section name=i loop=$list_contas_receber}
		
					<tr>
						<td align='left'>{$list_contas_receber[i].descricao_conta}</td>
						<td align='left'>{$list_contas_receber[i].descricao_modo_recebimento}</td>
						<td align='center'>{$list_contas_receber[i].data_vencimento}</td>
						<td align='center'>{$list_contas_receber[i].data_recebimento}</td>
						<td align='center'>{$list_contas_receber[i].data_baixa}</td>
						<td align='right'>{$list_contas_receber[i].valor_basico_conta}</td>
						<td align='right'>{$list_contas_receber[i].valor_juros_parcela}</td>
						<td align='right'>{$list_contas_receber[i].valor_juros_atraso}</td>
						<td align='right'>{$list_contas_receber[i].valor_multa}</td>
						<td align='right'>{$list_contas_receber[i].valor_recebido}</td>
						<td align='right'>{$list_contas_receber[i].porcentagem_comissao_vendedor}</td>
						<td align='right'>{$list_contas_receber[i].fator_correcao_comissao_vendedor}</td>
						<td align='right'>{$list_contas_receber[i].valor_comissao_com_correcao}</td>
	
	
					</tr>
					
					<tr>
						<td class="row" height="1" bgcolor="#999999" colspan="20"></td>
					</tr>
		
				{/section}
		
				<tr><td>&nbsp;</td></tr>

				<tr>
					<td align="right" colspan="20" class="negrito">
						Total a Receber: R$ {$flags.total_a_receber}
					</td>
				</tr>


			</table>

		{else}

			{if $smarty.post.for_chk}
      	{include file="div_resultado_nenhum.tpl"}
			{/if}

		{/if}		


		<br>

		<table width="100%" border="0" cellpadding="5" cellspacing="0">
			<tr>
				<td>
					* Data baseada na Data da Baixa da conta a receber.
				</td>
			</tr>
		</table>


{include file="com_rodape_relatorio.tpl"}
