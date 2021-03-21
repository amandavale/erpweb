{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}

<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>


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

  	<form  action="{$smarty.server.PHP_SELF}?ac=listar" method="post" name="for_balancete" id="for_balancete">
    <input type="hidden" name="for_chk" id="for_chk" value="1" />
			<table width="100%">

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">
			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Selecione o cliente e o período</b></td>
			        </tr>
			
							<tr>
								<td align="center">
									Filial:
									<input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
									{$info_filial.nome_filial}
								</td>
							</tr>
			
							<tr>
								<td align="right" width="25%" >Cliente:</td>
								<td colspan="9" align="left" >
									<input type="hidden" name="idcliente" id="idcliente" value="{$smarty.post.idcliente}" />
									<input type="hidden" name="idcliente_NomeTemp" id="idcliente_NomeTemp" value="{$smarty.post.idcliente_NomeTemp}" />
									<input class="ultralarge" type="text" name="idcliente_Nome" id="idcliente_Nome" value="{$smarty.post.idcliente_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idcliente');
										"
									/>
									<span class="nao_selecionou" id="idcliente_Flag">
										&nbsp;&nbsp;&nbsp;
									</span>
									
									<script type="text/javascript">
									    new CAPXOUS.AutoComplete("idcliente_Nome", function() {ldelim}
									   	return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente" + "&mostraDetalhes=1";
									    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
									
			
									  	// verifica os campos auto-complete
										VerificaMudancaCampo('idcliente');
									</script>
								</td>
							</tr>
							
							<tr>
								<td align="right" valign="bottom" >Data de movimento De:</td>
								<td align="left"> <input class="short" type="text" name="data_movimento_de" id="data_movimento_de" value="{$smarty.post.data_movimento_de}" maxlength='10' onkeydown="mask('data_movimento_de', 'data')" onkeyup="mask('data_movimento_de', 'data')" />
									<img src="{$conf.addr}/common/img/calendar.png" id="img_data_movimento_de" style="cursor: pointer;" />
									&nbsp;Até:	
									<input class="short" type="text" name="data_movimento_ate" id="data_movimento_ate" value="{$smarty.post.data_movimento_ate}" maxlength='10' onkeydown="mask('data_movimento_ate', 'data')" onkeyup="mask('data_movimento_ate', 'data')" />
									<img src="{$conf.addr}/common/img/calendar.png" id="img_data_movimento_ate" style="cursor: pointer;" /> (dd/mm/aaaa)
								</td>
							</tr>

							<script type="text/javascript">			
								Calendar.setup(
									{ldelim}
										inputField : "data_movimento_de", // ID of the input field
										ifFormat : "%d/%m/%Y", // the date format
										button : "img_data_movimento_de", // ID of the button
										align  : "cR"  // alinhamento
									{rdelim}
								);
			
								Calendar.setup(
									{ldelim}
										inputField : "data_movimento_ate", // ID of the input field
										ifFormat : "%d/%m/%Y", // the date format
										button : "img_data_movimento_ate", // ID of the button
										align  : "cR"  // alinhamento
									{rdelim}
								);						
							</script>	

							<tr>
					        	<td align="center" colspan="2">
									<input type="button" class="botao_padrao" value="Buscar!" name="button" onClick="xajax_Verifica_Campos_Busca_Balancete_AJAX(xajax.getFormValues('for_balancete'))" />
					        	</td>
					        </tr>

						</table>
					</td>
		        </tr>

			</table>
		</form>		


		{if count($list_balancete)} 

		
				<table align="center" width="400px">
					
					<tr align=center>
						<th colspan="3">Despesas</th>
					</tr>
							
					<tr style="background-color:#fff;" >
						<td align='center'>Plano </td>
						<td align='center'>Valor</td>
					</tr>	
						{foreach from=$list_balancete.despesas item=despesa}
							<tr>
								<td align='left'>	{$despesa.plano_numero} {$despesa.plano_nome}</td>
								<td align='center'>	{$despesa.movimento_valor_movimento}</td>
							</tr>
							<tr><td class="row" height="1" bgcolor="#999999" colspan="20"></td></tr>
						{/foreach}
										
				</table>
		
		
				
				<table align="center" width="400px">
					
					<tr align=center>
						<th colspan="3">Receitas</th>
					</tr>
							
					<tr style="background-color:#fff;">
						<td align='center'>Plano </td>
						<td align='center'>Valor</td>
					</tr>	
							
					{foreach from=$list_balancete.receitas item=receita}
						<tr>
							<td align='left'>	{$receita.plano_numero} {$receita.plano_nome}</td>
							<td align='center'>	{$receita.movimento_valor_movimento}</td>
						</tr>
						<tr><td class="row" height="1" bgcolor="#999999" colspan="20"></td></tr>
					{/foreach}
						
					</table>
				

				Total a Receber: R$ {$flags.total_a_receber}


			</table>

			<!--  table width="100%" border="0" cellpadding="5" cellspacing="0">
				<tr><td>
					<table width="100%" border="0" cellpadding="0" cellspacing="3" class="tb4cantosPreto">
						<tr>
							<td align='center'>
							Observação: A meta mensal deste Vendedor ï¿½ <b>R$ {$info_vendedor.meta_mensal_vendedor}.</b>
							</td>
						</tr>
					</table>
				</td></tr>
			</table -->

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
