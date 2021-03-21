{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}


{if $flags.okay}

	<table class="tb4cantosAzul" width="100%"  border="0" cellpadding="5" cellspacing="0">
		<tr>
			{if $flags.intrucoes_preenchimento != ""}
		  	<td class="tela" WIDTH="1%" height="20" valign="middle">
		  		<img class="lightbulb" src="{$conf.addr}/common/img/lampada.png" width="16" height="16" border="0" align="middle" onmouseover="pmaTooltip('{$flags.intrucoes_preenchimento}'); return false;" onmouseout="swapTooltip('default'); return false;" />
				</td>
			{/if}
	  	<td class="tela" WIDTH="5%" height="20">
				Tela:
			</td>
	  	<td class="descricao_tela" WIDTH="95%">
				{$conf.area}
			</td>
		</tr>
	</table>


  {if $flags.action == "listar"}

		<br>

		{* --------- MOSTRA O DADOS GERAIS ------------  *}
		<table class="tb4cantos" width="100%">
			<tr bgcolor="#F7F7F7">
				<td colspan="9" align="center">Dados do Relatório de Caixa Diário</td>
			</tr>
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
				<td align="right">Data / Hora:</td>
				<td>{$flags.data_hora_atual}</td>
			</tr>		
		</table>

		<br>

		{* --------- MOSTRA O TROCO ------------  *}
		<table class="tb4cantos" width="100%">
			<tr bgcolor="#F7F7F7">
				<td colspan="9" align="center">TROCO</td>
			</tr>

			<tr>
				<td width="80%" align="right"><span class="req">{$flags.msg_troco_nao_cadastrado}</span> Valor do Troco (R$):</td>
				<td align="right"><b>{$flags.valor_troco}<b></td>
			</tr>		
		</table>

		<br>


		{* --------- MOSTRA AS SAIDAS ------------  *}
		<table class="tb4cantos" width="100%">
			<tr bgcolor="#F7F7F7">
				<td colspan="9" align="center">SAÍDAS</td>
			</tr>

			<tr>
				<td colspan="9" align="center">
					<table width="100%">
			
						<tr>
							<th align='center'>No</th>
							<th align='center'>Tipo da Conta a pagar</th>
							<th align='center'>Descrição da conta</th>
							<th align='center'>Valor da conta (R$)</th>
							<th align='center'>Valor pago (R$)</th>
							<th align='center'>Data de vencimento</th>
							<th align='center'>Data do pagamento</th>
							<th align='center'>Status da conta</th>
							<th align='center'>Valor que saiu do caixa (R$)</th>
						</tr>
			
						{section name=i loop=$list_saidas}
							<tr>
								<td><a class='menu_item' href = "{$conf.addr}/admin/conta_pagar.php?ac=editar&idconta_pagar={$list_saidas[i].idconta_pagar}">{$list_saidas[i].index}</a></td>
								<td><a class='menu_item' href = "{$conf.addr}/admin/conta_pagar.php?ac=editar&idconta_pagar={$list_saidas[i].idconta_pagar}">{$list_saidas[i].descricao_conta_pagar}</a></td>
								<td><a class='menu_item' href = "{$conf.addr}/admin/conta_pagar.php?ac=editar&idconta_pagar={$list_saidas[i].idconta_pagar}">{$list_saidas[i].descricao_conta}</a></td>
								<td align="right"><a class='menu_item' href = "{$conf.addr}/admin/conta_pagar.php?ac=editar&idconta_pagar={$list_saidas[i].idconta_pagar}">{$list_saidas[i].valor_conta}</a></td>
								<td align="right"><a class='menu_item' href = "{$conf.addr}/admin/conta_pagar.php?ac=editar&idconta_pagar={$list_saidas[i].idconta_pagar}">{$list_saidas[i].valor_pago}</a></td>
								<td align="center"><a class='menu_item' href = "{$conf.addr}/admin/conta_pagar.php?ac=editar&idconta_pagar={$list_saidas[i].idconta_pagar}">{$list_saidas[i].data_vencimento}</a></td>
								<td align="center"><a class='menu_item' href = "{$conf.addr}/admin/conta_pagar.php?ac=editar&idconta_pagar={$list_saidas[i].idconta_pagar}">{$list_saidas[i].data_pagamento}</a></td>
								<td align="center"><a class='menu_item' href = "{$conf.addr}/admin/conta_pagar.php?ac=editar&idconta_pagar={$list_saidas[i].idconta_pagar}">{$list_saidas[i].status_conta}</a></td>
								<td align="right"><a class='menu_item' href = "{$conf.addr}/admin/conta_pagar.php?ac=editar&idconta_pagar={$list_saidas[i].idconta_pagar}">{$list_saidas[i].valor_saiu_caixa}</a></td>
							</tr>
							
							<tr>
								<td class="row" height="1" bgcolor="#999999" colspan="9"></td>
							</tr>
						{/section}

					</table>
				</td>
			</tr>

			<tr>
				<td width="80%" align="right">Total de Saídas (R$):</td>
				<td align="right"><b>{$flags.valor_saidas}<b></td>
			</tr>	
		</table>

		<br>


		{* --------- MOSTRA AS ENTRADAS ------------  *}
		<table class="tb4cantos" width="100%">
			<tr bgcolor="#F7F7F7">
				<td colspan="9" align="center">ENTRADAS</td>
			</tr>

			<tr>
				<td colspan="9" align="center">
					<table width="100%">
			
						<tr>
							<th align='center'>Descrição</th>
							<th align='center'>Modo</th>
							<th align='center'>Cliente</th>
							<th align='center'>D. Vencimento</th>
							<th align='center'>D. Recebimento</th>
							<th align='center'>D. Cadastro</th>
							<th align='center'>Conta (R$)</th>
							<th align='center'>Juros (R$)</th>
							<th align='center'>Multa (R$)</th>
							<th align='center'>Recebido (R$)</th>
						</tr>
			
						{section name=i loop=$list_contas_receber}
							<tr>
								<td><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].descricao_conta}</a></td>
								<td><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].descricao_modo_recebimento}</a></td>
								<td align="center"><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].nome_cliente}</a></td>
								<td align="center"><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].data_vencimento}</a></td>
								<td align="center"><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].data_recebimento}</a></td>
								<td align="center"><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].datahoraCadastro_D}</a></td>
								<td align="right"><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].valor_total_conta}</a></td>
								<td align="right"><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].valor_juros_atraso}</a></td>
								<td align="right"><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].valor_multa}</a></td>
								<td align="right"><a class='menu_item' href = "{$conf.addr}/admin/conta_receber.php?ac=editar&idconta_receber={$list_contas_receber[i].idconta_receber}">{$list_contas_receber[i].valor_recebido}</a></td>
							</tr>

							<tr>
								<td class="row" height="1" bgcolor="#999999" colspan="20"></td>
							</tr>
						{/section}

					</table>
				</td>
			</tr>

			<tr><td>&nbsp;</td></tr>

			<tr>
				<td width="80%" align="right"><b>Resumo das Entradas</b></td>
			</tr>	

			{section name=i loop=$list_modo_recebimento}
				<tr>
					<td align="right">{$list_modo_recebimento[i].campo} (R$):</td>
					<td align="right">{$list_modo_recebimento[i].valor}</td>
				</tr>
			{/section}

			<tr><td>&nbsp;</td></tr>

			<tr>
				<td width="80%" align="right">Total de Entradas (R$):</td>
				<td align="right"><b>{$flags.valor_entradas}<b></td>
			</tr>	
		</table>

		<br>

		{* --------- MOSTRA O SALDO ------------  *}
		<table class="tb4cantos" width="100%">
			<tr bgcolor="#F7F7F7">
				<td colspan="9" align="center">SALDO</td>
			</tr>

			<tr>
				<td width="80%" align="right">Saldo Final (R$):</td>
				<td align="right"><b>{$flags.valor_saldo}<b></td>
			</tr>	
		</table>



		<table width="95%" align="center">
			<form action="{$smarty.server.PHP_SELF}?ac=listar&target=full" method="post" name = "for" id = "for" target="_blank">
			<input type="hidden" name="for_chk" id="for_chk" value="1" />
				
				<tr><td>&nbsp;</td></tr>

				<tr>
					<td align="center">
						<input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão">
					</td>
				</tr>

				<tr><td>&nbsp;</td></tr>

			</form>
		</table>


  {/if}

{/if}

{include file="com_rodape.tpl"}
