{include file="com_cabecalho_relatorio.tpl"}


{if count($list)}

		<table width="95%" align="center">

				<tr>
					<th align='center'> No </th>
					<th align='center'> Nome </th>
					<th align='center'> Endereço </th>
					<th align='center'> CNPJ </th>
					<th align='center'> Telefone </th>
					<th align='center'> Pessoa Contato </th>
					<th align='center'> Celular </th>
				</tr>
			
			  {section name=i loop=$list}
			    <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
					<td width="4%"  >{$list[i].index}</td>
					<td width="18%" >{$list[i].nome_fornecedor}</td>
					<td width="38%" >{$list[i].endereco}</td>
					<td width="10%" >{$list[i].cpf_cnpj}</td>
					<td width="10%" >{$list[i].telefone_fornecedor}</td>
					<td width="10%" >{$list[i].nome_contato_fornecedor}</td>
					<td width="10%" >{$list[i].celular_representante}</td>
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