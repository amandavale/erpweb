{include file="com_cabecalho_relatorio.tpl"}


{if count($list)}

	<table width="95%" align="center">
	
		<tr>

			<th align='center'>Nº da Nota</th>

			{* Tem o CFOP apenas se nao for Cupom Fiscal *}
			{if $smarty.session.tipo_orcamento != "ECF"}
				<th align='center'>CFOP</th>
			{/if}

			<th align='center'>Cliente</th>
			<th align='center'>Filial</th>
			<th align='center'>Valor (R$)</th>
			<th align='center'>Data/Hora (1)</th>
			<th align='center'>Funcionário (2)</th>
		</tr>

    {section name=i loop=$list}
      <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

				<td align='center'><b>{$list[i].numeroNota} {if $list[i].idmotivo_cancelamento != ""}(Cancelado){/if}</b></td>

				{* Tem o CFOP apenas se nao for Cupom Fiscal *}
				{if $smarty.session.tipo_orcamento != "ECF"}
					<td>{$list[i].descricao_cfop}-{$list[i].codigo_cfop}</td>
				{/if}

				<td>{$list[i].cliente_descricao}</td>
				<td>{$list[i].nome_filial}</td>
				<td align="right">{$list[i].valor}</td>
				<td align="center">{$list[i].datahoraCriacaoNF}</td>
				<td>{$list[i].funcionario_emitiu_NF}</td>
      </tr>


      <tr>
        <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
      </tr>
    {/section}

		<tr><td>&nbsp;</td></tr>

		<tr>
      <td colspan="9">Legenda:</td>
    </tr>
		<tr>
      <td colspan="9">(1) Data/Hora de criação da Nota Fiscal.</td>
    </tr>
		<tr>
      <td colspan="9">(2) Funcionário que criou a Nota Fiscal.</td>
    </tr>

  </table>

{else}
  {include file="div_resultado_nenhum.tpl"}
{/if}



{include file="com_rodape_relatorio.tpl"}
