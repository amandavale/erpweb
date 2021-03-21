		<br>

		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=busca_generica" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />

				<tr>
					<td align="right" width="40%">
					Filial:
					</td>
					<td>
						<input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
						{$info_filial.nome_filial}
					</td>
				</tr>

				<tr>
				  <td align="right">Busca:</td>
					<td>
						<input class="long" type="text" name="busca" id="busca" maxlength="50" value="{$flags.busca}"/>
					</td>
				</tr>
				<tr>
					<td align="right">Resultados por página:</td>
					<td>
						<input class="tiny" type="text" name="rpp" id="rpp" maxlength="50" value="{$flags.rpp}" onkeydown="FormataInteiro('rpp')" onkeyup="FormataInteiro('rpp')" />
						&nbsp;&nbsp;
						<input name="Submit" type="submit" class="botao_padrao" value="Buscar">
					</td>
				</tr>

        <tr><td>&nbsp;</td></tr>

			</form>
		</table>

		{if count($list)}

			<p align="center">Listando {$conf.area} de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p>

			<table width="95%" align="center">


				<tr>
					<th align='center'>No</th>
					<!--th align='center'>Tipo da Conta a pagar</th-->
					<th align='center'>Descrição da conta</th>
					<th align='center'>Valor da conta (R$)</th>
					<th align='center'>Valor pago (R$)</th>
					<th align='center'>Data de vencimento</th>
					<th align='center'>Data do pagamento</th>
					<th align='center'>Status da conta</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_pagar={$list[i].idconta_pagar}">{$list[i].index}</a></td>
						<!--td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_pagar={$list[i].idconta_pagar}">{$list[i].descricao_conta_pagar}</a></td-->
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_pagar={$list[i].idconta_pagar}">{$list[i].descricao_conta}</a></td>
						<td align="right"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_pagar={$list[i].idconta_pagar}">{$list[i].valor_conta}</a></td>
						<td align="right"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_pagar={$list[i].idconta_pagar}">{$list[i].valor_pago}</a></td>
						<td align="center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_pagar={$list[i].idconta_pagar}">{$list[i].data_vencimento}</a></td>
						<td align="center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_pagar={$list[i].idconta_pagar}">{$list[i].data_pagamento}</a></td>
						<td align="center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_pagar={$list[i].idconta_pagar}">{$list[i].status_conta}</a></td>
	        </tr>
	        
	        <tr>
	          <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
	        </tr>
	      {/section}

      </table>

      <p align="center" id="nav">{$nav}</p>

			<table width="95%" align="center">
	    	<form action="{$smarty.server.PHP_SELF}?ac=busca_generica&target=full" method="post" name = "for" id = "for" target="_blank">
	      <input type="hidden" name="for_chk" id="for_chk" value="1" />
				<input type="hidden" name="busca" id="busca" value="{$flags.busca}"/>

					<tr>
						<td align="center">
							<input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão">
						</td>
					</tr>

	        <tr><td>&nbsp;</td></tr>

				</form>
			</table>


		{else}
			{if $flags.fez_busca == 1}
      	{include file="div_resultado_nenhum.tpl"}
      {/if}
		{/if}