{include file="com_cabecalho_relatorio.tpl"}


{if count($list)}

	<table width="95%" align="center">
	
		<tr>
			<th align='center'>No</th>
			<th align='center'>Data/Hora (1)</th>
			<th align='center'>Funcion�rio (2)</th>
			<th align='center'>Funcion�rio (3)</th>
			<th align='center'>Cliente</th>
			<th align='center'>Filial</th>
			<th align='center'>Pre�o</th>
			<th align='center'>Validade (4)</th>
		</tr>

    {section name=i loop=$list}
      <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

				<td>{$list[i].index}</td>
				<td>{$list[i].datahoraCriacao}</td>
				<td>{$list[i].funcionario_criou_orcamento}</td>
				<td>{$list[i].funcionario_ultima_alteracao}</td>
				<td>{$list[i].cliente_descricao}</td>
				<td>{$list[i].nome_filial}</td>
				<td>{$list[i].tipoPreco}</td>
				<td align='center'>{$list[i].validade}</td>
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
      <td colspan="9">(1) Data/Hora de cria��o do or�amento.</td>
    </tr>
		<tr>
      <td colspan="9">(2) Funcion�rio que abriu o or�amento.</td>
    </tr>
		<tr>
      <td colspan="9">(3) Funcion�rio que fez a �ltima altera��o nos dados.</td>
    </tr>
		<tr>
      <td colspan="9">(4) Validade em dias �teis.</td>
    </tr>

  </table>

{else}
  {include file="div_resultado_nenhum.tpl"}
{/if}



{include file="com_rodape_relatorio.tpl"}
