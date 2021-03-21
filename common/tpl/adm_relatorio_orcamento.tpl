{include file="com_cabecalho_relatorio.tpl"}


{if count($list)}

	<table width="95%" align="center">
	
		<tr>
			<th align='center'>Cód. Orçamento</th>
			<th align='center'>Data/Hora (1)</th>
			<th align='center'>Funcionário (2)</th>
			<th align='center'>Cliente</th>
			<th align='center'>Filial</th>
			<th align='center'>* Valor (R$)</th>
		</tr>

	  {section name=i loop=$list}
	    <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

				<td align='center'><b>{$list[i].idorcamento} {if $list[i].idmotivo_cancelamento != ""}(Cancelado){/if}</b></td>
				<td>{$list[i].datahoraCriacao}</td>
				<td>{$list[i].funcionario_criou_orcamento}</td>
				<td>{$list[i].cliente_descricao}</td>
				<td>{$list[i].nome_filial}</td>
				<td align="right">{$list[i].valor}</td>
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
	    <td colspan="9">(1) Data/Hora de criação do orçamento.</td>
	  </tr>
		<tr>
	    <td colspan="9">(2) Funcionário que criou o orçamento.</td>
	  </tr>
		<tr>
      <td colspan="9">* Sujeito a alterações, caso os preços dos produtos tenham sido alterados.</td>
    </tr>

  </table>

{else}
  {include file="div_resultado_nenhum.tpl"}
{/if}



{include file="com_rodape_relatorio.tpl"}
