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

  	<form  action="{$smarty.server.PHP_SELF}?ac=listar" method="post" name="for_comissao_vendedor" id="for_comissao_vendedor">
    <input type="hidden" name="for_chk" id="for_chk" value="1" />
			<table width="100%">

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">
			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Selecione o vendedor e o período de recebimento</b></td>
			        </tr>
			
							<tr>
								<td align="center">
									Filial:
									<input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
									{$info_filial.nome_filial}
								</td>
							</tr>


							<tr>
								<td colspan="9" align="center"  class="req">
									Vendedor:
									<select name="idFuncionario" id="idFuncionario">
									<option value="">[selecione]</option>
									{html_options values=$list_funcionarios.idfuncionario output=$list_funcionarios.nome_funcionario selected=$smarty.post.idFuncionario}
									</select>
								</td>
							</tr>
			
							<tr>
								<td colspan="9" align="center" class="req">
									* Data:
									De: 
									<input class="short" type="text" name="data_recebimento_de" id="data_recebimento_de" value="{$smarty.post.data_recebimento_de}" maxlength='10' onkeydown="mask('data_recebimento_de', 'data')" onkeyup="mask('data_recebimento_de', 'data')" />
									<img src="{$conf.addr}/common/img/calendar.png" id="img_data_recebimento_de" style="cursor: pointer;" />
									<script type="text/javascript">
										Calendar.setup(
											{ldelim}
												inputField : "data_recebimento_de", // ID of the input field
												ifFormat : "%d/%m/%Y", // the date format
												button : "img_data_recebimento_de", // ID of the button
												align  : "cR"  // alinhamento
											{rdelim}
										);
									</script>

									Até:	
									<input class="short" type="text" name="data_recebimento_ate" id="data_recebimento_ate" value="{$smarty.post.data_recebimento_ate}" maxlength='10' onkeydown="mask('data_recebimento_ate', 'data')" onkeyup="mask('data_recebimento_ate', 'data')" /> 
									<img src="{$conf.addr}/common/img/calendar.png" id="img_data_recebimento_ate" style="cursor: pointer;" />
									<script type="text/javascript">
										Calendar.setup(
											{ldelim}
												inputField : "data_recebimento_ate", // ID of the input field
												ifFormat : "%d/%m/%Y", // the date format
												button : "img_data_recebimento_ate", // ID of the button
												align  : "cR"  // alinhamento
											{rdelim}
										);
									</script>
									(dd/mm/aaaa)
								</td>
							</tr>






							<tr>
			        	<td align="center" colspan="2">
									<input type="button" class="botao_padrao" value="Buscar!" name="button" onClick="xajax_Verifica_Campos_Busca_Rapida_Comissao_Vendedor_AJAX(xajax.getFormValues('for_comissao_vendedor'))" />
			        	</td>
			        </tr>

						</table>
					</td>
        </tr>

			</table>
		</form>		


		{if count($list_contas_receber)} 

			<table border="0" width="100%">
		
		
				<tr>
					<th align='center'>Descrição</th>
					<th align='center'>Modo</th>
					<th align='center'>D. Vencimento</th>
					<th align='center'>D. Recebimento</th>
					<th align='center'>D. Baixa</th>
					<th align='center'>V. Básico(R$)</th>
					<th align='center'>J. Parcela(R$)</th>
					<th align='center'>J. Atraso(R$)</th>
					<th align='center'>Multa(R$)</th>
					<th align='center'>Recebido(R$)</th>
					<th align='center'>Comissão(%)</th>
					<th align='center'>Fat.Corr(%)</th>
					<th align='center'>V. Comissão(R$)</th>
				</tr>
		
		
				{section name=i loop=$list_contas_receber}
		
					<tr>
						<td align='left'>{$list_contas_receber[i].descricao_conta}</td>
						<td align='left'>{$list_contas_receber[i].descricao_modo_recebimento}</td>
						<td align='center'>{$list_contas_receber[i].data_vencimento}</td>
						<td align='center'>{$list_contas_receber[i].data_recebimento}</td>
						<td align='center'>{$list_contas_receber[i].data_baixa}</td>
						<td align='right'>{$list_contas_receber[i].valor_basico_conta}</td>
						<td align='right'>{$list_contas_receber[i].valor_juros_parcela}</td>
						<td align='right'>{$list_contas_receber[i].valor_juros_atraso}</td>
						<td align='right'>{$list_contas_receber[i].valor_multa}</td>
						<td align='right'>{$list_contas_receber[i].valor_recebido}</td>
						<td align='right'>{$list_contas_receber[i].porcentagem_comissao_vendedor}</td>
						<td align='right'>{$list_contas_receber[i].fator_correcao_comissao_vendedor}</td>
						<td align='right'>{$list_contas_receber[i].valor_comissao_com_correcao}</td>
	
	
					</tr>
					
					<tr>
						<td class="row" height="1" bgcolor="#999999" colspan="20"></td>
					</tr>
		
				{/section}
		
				<tr><td>&nbsp;</td></tr>

				<tr>
					<td align="right" colspan="20" class="negrito">
						Total a Receber: R$ {$flags.total_a_receber}
					</td>
				</tr>


			</table>

			<table width="100%" border="0" cellpadding="5" cellspacing="0">
				<tr><td>
					<table width="100%" border="0" cellpadding="0" cellspacing="3" class="tb4cantosPreto">
						<tr>
							<td align='center'>
							Observação: A meta mensal deste Vendedor é <b>R$ {$info_vendedor.meta_mensal_vendedor}.</b>
							</td>
						</tr>
					</table>
				</td></tr>
			</table>

			<table width="95%" align="center">
				<form action="{$smarty.server.PHP_SELF}?ac=listar&target=full" method="post" name = "for" id = "for" target="_blank">
				<input type="hidden" name="for_chk" id="for_chk" value="1" />

				<input type="hidden" name="idFuncionario_relatorio" id="idFuncionario_relatorio" value="{$smarty.post.idFuncionario}" />
				<input type="hidden" name="data_recebimento_de_relatorio" id="data_recebimento_de_relatorio" value="{$smarty.post.data_recebimento_de}" />
				<input type="hidden" name="data_recebimento_ate_relatorio" id="data_recebimento_ate_relatorio" value="{$smarty.post.data_recebimento_ate}" />

					
					<tr><td>&nbsp;</td></tr>
	
					<tr>
						<td align="center">
							<input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão">
						</td>
					</tr>
	
					<tr><td>&nbsp;</td></tr>
	
				</form>
			</table>


		{else}

			{if $smarty.post.for_chk}
      	{include file="div_resultado_nenhum.tpl"}
			{/if}

		{/if}		


		<br>

		<table width="100%" border="0" cellpadding="5" cellspacing="0">
			<tr>
				<td>
					* Data baseada na Data da Baixa da conta a receber.
				</td>
			</tr>
		</table>


  {/if}




{/if}

{include file="com_rodape.tpl"}
