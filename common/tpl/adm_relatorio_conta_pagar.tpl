{include file="com_cabecalho_relatorio.tpl"}


{if count($list)}

	<table width="95%" align="center">

		<tr>
			<th align='center'>No</th>
			<th align='center'>Tipo da Conta a pagar</th>
			<th align='center'>Descrição da conta</th>
			<th align='center'>Valor da conta (R$)</th>
			<th align='center'>Valor pago (R$)</th>
			<th align='center'>Data de vencimento</th>
			<th align='center'>Data do pagamento</th>
			<th align='center'>Status da conta</th>
		</tr>
		
		{section name=i loop=$list}
			<tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
				
				<td>{$list[i].index}</td>
				<td>{$list[i].descricao_conta_pagar}</td>
				<td>{$list[i].descricao_conta}</td>
				<td align="right">{$list[i].valor_conta}</td>
				<td align="right">{$list[i].valor_pago}</td>
				<td align="center">{$list[i].data_vencimento}</td>
				<td align="center">{$list[i].data_pagamento}</td>
				<td align="center">{$list[i].status_conta}</td>
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
