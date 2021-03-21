{include file="com_cabecalho_relatorio.tpl"}


{if count($list)}

	<table width="95%" align="center">
	
		<tr>
			<th align='center'>No</th>
			<th align='center'>Descrição da conta a receber</th>
			<th align='center'>Valor da conta a receber (R$)</th>
			<th align='center'>Data prevista de recebimento</th>
			<th align='center'>Status da conta</th>
			<th align='center'>Valor rec. (R$)</th>
			<th align='center'>Data do recebimento</th>
		</tr>

    {section name=i loop=$list}
      <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

				<td>{$list[i].index}</td>
				<td>{$list[i].descricao_conta}</td>
				<td>{$list[i].valor_conta}</td>
				<td>{$list[i].data_recebimento_prevista}</td>
				<td>{$list[i].status_conta}</td>
				<td>{$list[i].valor_recebido}</td>
				<td>{$list[i].data_recebimento}</td>
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
