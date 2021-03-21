{include file="com_cabecalho_relatorio.tpl"}


				<center><h2>Relatório de Funcionários</h2></center><br />

{if count($list)}

			<table width="95%" align="center" id="tbl_funcionario" class="tablesorter">
							
				<thead>
				<tr>
					<th class="header" width="150"  align='center'>Nome 		   </th>
					<th class="header" width="200"  align='center'>Endereço 	   </th>
					<th class="header" width="90"   align='center'>CPF 		 	   </th>
					<th class="header" width="100"  align='center'>RG 		 	   </th>
					<th class="header" width="100"  align='center'>CTPS 	 	   </th>
					<th class="header" width="80"   align='center'>Série / UF      </th>
					<th class="header" width="80"   align='center'>Agência / Conta </th>
					<th class="header" width="90"   align='center'>Telefone 	   </th>
					<th class="header" width="90"   align='center'>Data Admissão   </th>
				</tr>
				</thead>
				
				<tbody>
				{section name=i loop=$list}
					 <tr   style="height:30px;" valign="center" >
                                                <td class="td_borderBottom" style="background-color:{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if};">&nbsp;{$list[i].nome_funcionario}</td>
						<td class="td_borderBottom" style="background-color:{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if};" >&nbsp;{$list[i].endereco}</td>
						<td class="td_borderBottom" style="background-color:{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if};"  align="center">&nbsp;{$list[i].cpf_funcionario}</td>
						<td class="td_borderBottom" style="background-color:{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if};" >&nbsp;{$list[i].identidade_funcionario}</td>
						<td class="td_borderBottom" style="background-color:{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if};" >&nbsp;{$list[i].carteira_trabalho_funcionario}</td>
						<td class="td_borderBottom" style="background-color:{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if};" >&nbsp;{if $list[i].ctps_serie}{$list[i].ctps_serie} / {$list[i].ctps_uf}{/if}</td>
						<td class="td_borderBottom" style="background-color:{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if};" >&nbsp;{if $list[i].agencia_funcionario}{$list[i].agencia_funcionario} / {$list[i].conta_funcionario}{/if}</td>
						<td class="td_borderBottom" style="background-color:{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if};" align="center">&nbsp;{$list[i].telefone_funcionario}</td>
						<td class="td_borderBottom" style="background-color:{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if};" align="center">&nbsp;{$list[i].data_admissao_funcionario}</td>
					</tr>	   
					
				{/section}
				</tbody>
			</table>


{else}
  {include file="div_resultado_nenhum.tpl"}
{/if}



{include file="com_rodape_relatorio.tpl"}
