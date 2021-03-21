{include file="com_cabecalho_relatorio.tpl"}


{if count($list)}

	<table width="95%" align="center">
	
		<tr>
			<th align='center'>No</th>
			<th align='center'>Descrição do produto</th>
			<th align='center'>Departamento / Seção</th>
			<th align='center'>Localização do produto</th>
			<th align='center'>Unidade de venda</th>
		</tr>

	  {section name=i loop=$list}
	    <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

				<td>{$list[i].index}</td>
				<td>{$list[i].descricao_produto}</td>
				<td>{$list[i].nome_departamento} / {$list[i].nome_secao}</td>
				<td>{$list[i].localizacao_produto}</td>
				<td>{$list[i].nome_unidade_venda} ({$list[i].sigla_unidade_venda})</td>
	    </tr>

	    <tr>
	      <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
	    </tr>
	  {/section}

  </table>

{else}
  {include file="div_resultado_nenhum.tpl"}
{/if}



{include file="com_rodape_relatorio.tpl"}
