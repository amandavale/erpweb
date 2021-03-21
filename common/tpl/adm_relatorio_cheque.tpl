{include file="com_cabecalho_relatorio.tpl"}


{if count($list)}

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

					<td align='center'><b>{$list[i].idcheque}</b></td>
					<td>{$list[i].nome_banco}</td>
					<td>{$list[i].agencia}-{$list[i].agencia_dig}</td>
					<td>{$list[i].conta}-{$list[i].conta_dig}</td>
					<td align='center'>{$list[i].data_cheque}</td>
					<td align='right'>{$list[i].valor}</td>
					<td>{$list[i].titular_conta}</td>

				</tr>

				<tr>
					<td class="row" height="1" bgcolor="#999999" colspan="9"></td>
				</tr>
			{/section}

		<tr><td>&nbsp;</td></tr>

  </table>

{else}
  {include file="div_resultado_nenhum.tpl"}
{/if}



{include file="com_rodape_relatorio.tpl"}
