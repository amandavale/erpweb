
    	{if $flags.sucesso != ""}
	  		{include file="div_resultado_inicio.tpl"}
	  			{$flags.sucesso}
	  		{include file="div_resultado_fim.tpl"}
		{/if}

		<br>

		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=busca_parametrizada" method="post" name = "for" id = "for">
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
					<td align="right">Descrição da conta a pagar:</td>
					<td><input class="long" type="text" name="descricao_conta" id="descricao_conta" maxlength="50" value="{$flags.descricao_conta}"/></td>
				</tr>

				<tr>
					<td align="right">Código do Pedido:</td>
					<td>
						<input class="medium" type="text" name="idpedido" id="idpedido" value="{$flags.idpedido}" maxlength='20' onkeydown="FormataInteiro('idpedido')" onkeyup="FormataInteiro('idpedido')" />
					</td>
				</tr>

				<tr>
					<td align="right">Data de vencimento:</td>
					<td>
						De: <input class="short" type="text" name="data_vencimento_de" id="data_vencimento_de" value="{$flags.data_vencimento_de}" maxlength='10' onkeydown="mask('data_vencimento_de', 'data')" onkeyup="mask('data_vencimento_de', 'data')" />
						<img src="{$conf.addr}/common/img/calendar.png" id="img_data_vencimento_de" style="cursor: pointer;" />
						Até:	
						<input class="short" type="text" name="data_vencimento_ate" id="data_vencimento_ate" value="{$flags.data_vencimento_ate}" maxlength='10' onkeydown="mask('data_vencimento_ate', 'data')" onkeyup="mask('data_vencimento_ate', 'data')" />
						<img src="{$conf.addr}/common/img/calendar.png" id="img_data_vencimento_ate" style="cursor: pointer;" /> (dd/mm/aaaa)
					</td>
				</tr>															
				<script type="text/javascript">
					Calendar.setup(
						{ldelim}
							inputField : "data_vencimento_de", // ID of the input field
							ifFormat : "%d/%m/%Y", // the date format
							button : "img_data_vencimento_de", // ID of the button
							align  : "cR"  // alinhamento
						{rdelim}
					);
				</script>	

				<script type="text/javascript">
					Calendar.setup(
						{ldelim}
							inputField : "data_vencimento_ate", // ID of the input field
							ifFormat : "%d/%m/%Y", // the date format
							button : "img_data_vencimento_ate", // ID of the button
							align  : "cR"  // alinhamento
						{rdelim}
					);
				</script>	


				<tr>
					<td align="right">Data de pagamento:</td>
					<td>
						De: <input class="short" type="text" name="data_pagamento_de" id="data_pagamento_de" value="{$flags.data_pagamento_de}" maxlength='10' onkeydown="mask('data_pagamento_de', 'data')" onkeyup="mask('data_pagamento_de', 'data')" />
						<img src="{$conf.addr}/common/img/calendar.png" id="img_data_pagamento_de" style="cursor: pointer;" />
						Até:	
						<input class="short" type="text" name="data_pagamento_ate" id="data_pagamento_ate" value="{$flags.data_pagamento_ate}" maxlength='10' onkeydown="mask('data_pagamento_ate', 'data')" onkeyup="mask('data_pagamento_ate', 'data')" />
						<img src="{$conf.addr}/common/img/calendar.png" id="img_data_pagamento_ate" style="cursor: pointer;" /> (dd/mm/aaaa)
					</td>
				</tr>						
				<script type="text/javascript">
					Calendar.setup(
						{ldelim}
							inputField : "data_pagamento_de", // ID of the input field
							ifFormat : "%d/%m/%Y", // the date format
							button : "img_data_pagamento_de", // ID of the button
							align  : "cR"  // alinhamento
						{rdelim}
					);
				</script>	

				<script type="text/javascript">
					Calendar.setup(
						{ldelim}
							inputField : "data_pagamento_ate", // ID of the input field
							ifFormat : "%d/%m/%Y", // the date format
							button : "img_data_pagamento_ate", // ID of the button
							align  : "cR"  // alinhamento
						{rdelim}
					);
				</script>	


				<tr>
					<td align="right">Status da conta:</td>
					<td>
						<input {if $flags.status_conta=="N"}checked{/if} class="radio" type="radio" name="status_conta" id="status_conta" value="N" />Não pago
						<input {if $flags.status_conta=="P"}checked{/if} class="radio" type="radio" name="status_conta" id="status_conta" value="P" />Pago
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

				<tr><td>&nbsp;</td></tr>


      </table>

      		<p align="center" id="nav">{$nav}</p>


			<table width="95%" align="center">
	    	<form action="{$smarty.server.PHP_SELF}?ac=busca_parametrizada&target=full" method="post" name = "for" id = "for" target="_blank">
	      <input type="hidden" name="for_chk" id="for_chk" value="1" />
				<input type="hidden" name="descricao_conta" id="descricao_conta" value="{$flags.descricao_conta}"/>
				<input type="hidden" name="idpedido" id="idpedido" value="{$flags.idpedido}"/>
				<input type="hidden" name="data_vencimento_de" id="data_vencimento_de" value="{$flags.data_vencimento_de}"/>
				<input type="hidden" name="data_vencimento_ate" id="data_vencimento_ate" value="{$flags.data_vencimento_ate}"/>
				<input type="hidden" name="data_pagamento_de" id="data_pagamento_de" value="{$flags.data_pagamento_de}"/>
				<input type="hidden" name="data_pagamento_ate" id="data_pagamento_ate" value="{$flags.data_pagamento_ate}"/>
				<input type="hidden" name="status_conta" id="status_conta" value="{$flags.status_conta}"/>

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