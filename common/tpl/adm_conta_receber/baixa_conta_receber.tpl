




		<br>

  	<form  action="{$smarty.server.PHP_SELF}?ac=baixa_conta_receber" method="post" name="for_conta_receber" id="for_conta_receber">
    <input type="hidden" name="for_chk" id="for_chk" value="1" />
	<input type="hidden" name="serie_ecf" id="serie_ecf" value="" /> {* Input para receber o número série da ECF cadastrada*}
			<table width="100%">

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">
			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">BAIXA DE CONTAS: Busca de <b>Contas a Receber</b></td>
			        </tr>


							<tr {if $smarty.post.tipo_conta == 'A'}style="display: none;"{/if} id="tr_cliente">
								<td align="right" width="25%" >Cliente:</td>
								<td colspan="9" align="left" >
									<input type="hidden" name="idcliente" id="idcliente" value="{if $smarty.post.tipo_conta != 'A'}{$smarty.post.idcliente}{/if}" />
									<input type="hidden" name="idcliente_NomeTemp" id="idcliente_NomeTemp" value="{$smarty.post.idcliente_NomeTemp}" />
									<input class="ultralarge" type="text" name="idcliente_Nome" id="idcliente_Nome" value="{$smarty.post.idcliente_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idcliente');
										"
									/>
									<span class="nao_selecionou" id="idcliente_Flag">
										&nbsp;&nbsp;&nbsp;
									</span>
								</td>
							</tr>
							<tr>
								<td colspan="9" align="center">
								  <div id="dados_cliente">
									</div>
								</td>
							</tr>

							<script type="text/javascript">
							    new CAPXOUS.AutoComplete("idcliente_Nome", function() {ldelim}
							    	return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente" + "&mostraDetalhes=1";
							    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							  // verifica os campos auto-complete
								VerificaMudancaCampo('idcliente');
							</script>


							{*
							<tr>
								<td colspan="9" align="center" class="req">
								*}
								
								
							<td align="right" width="25%">Tipo de Conta: </td>
								<td align="left"> 
									<select name="tipo_conta" id="tipo_conta" 
										onchange="if(this.value == 'A') {ldelim}
													document.getElementById('tr_cliente').style.display = 'none';
													document.getElementById('dados_cliente').style.display = 'none';
													document.getElementById('idcliente').value = '';
													document.getElementById('idcliente_Nome').value = '';
													VerificaMudancaCampo('idcliente');
												  {rdelim}
												  else 
												  	document.getElementById('tr_cliente').style.display = '';
												">
										<option value="">[selecione]</option>
										<option value="A" {if $smarty.post.tipo_conta == 'A'}selected{/if}>Avulsa</option>
										<option value="E" {if $smarty.post.tipo_conta == 'E'}selected{/if}>Entrada</option>
										<option value="P" {if $smarty.post.tipo_conta == 'P'}selected{/if}>Parcela</option>
									</select> 																
								</td>
								
								<td colspan="2" align="left" class="req">Data de vencimento: 
								
									De: 
									<input class="short" type="text" name="data_vencimento_de" id="data_vencimento_de" value="{$smarty.post.data_vencimento_de}" maxlength='10' onkeydown="mask('data_vencimento_de', 'data')" onkeyup="mask('data_vencimento_de', 'data')" />
									<img src="{$conf.addr}/common/img/calendar.png" id="img_data_vencimento_de" style="cursor: pointer;" />
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
											
									Até:	
									<input class="short" type="text" name="data_vencimento_ate" id="data_vencimento_ate" value="{$smarty.post.data_vencimento_ate}" maxlength='10' onkeydown="mask('data_vencimento_ate', 'data')" onkeyup="mask('data_vencimento_ate', 'data')" /> 
									<img src="{$conf.addr}/common/img/calendar.png" id="img_data_vencimento_ate" style="cursor: pointer;" />
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
									(dd/mm/aaaa)

								</td>
							</tr>
								
							<tr><td>&nbsp;</td></tr>
							
														
							<tr>
			        			<td align="center" colspan="9">
									<input type="button" class="botao_padrao" value="Buscar!" name="button" onClick="xajax_Verifica_Campos_Busca_Rapida_Conta_Receber_AJAX(xajax.getFormValues('for_conta_receber'))" />
			        			</td>
			        		</tr>
							
							

						</table>
					</td>
        		</tr>

			</table>
		</form>	



		{section name=i loop=$list_vendas}

			<table width="100%">
			
			{if $list_vendas[i].idorcamento}
			
					<tr bgcolor="#F7F7F7">
						<td colspan="9" align="center">Informações da <b>Venda</b></td>
					</tr>
			
				
	
					<tr>
	
						<td align="center">
							Código da Venda: {$list_vendas[i].idorcamento}
						</td>
	
						<td align="center">
							Nº da Nota: {$list_vendas[i].numeroNota}
						</td>
	
						<td align="center">
							Tipo: {$list_vendas[i].tipoOrcamentoDescricao}
						</td>
	
						<td align="center">
							Cliente: {$list_vendas[i].nome_cliente}
						</td>
	
						<td align="center">
							Data da Criação: {$list_vendas[i].datahoraCriacao}
						</td>
	
						<td align="center">
							Vendedor Responsável: {$list_vendas[i].funcionario_criou_orcamento}
						</td>
					</tr>
				{else}
				
					<tr bgcolor="#F7F7F7">
						<td colspan="9" align="center">Informações da <b>Conta a Receber Avulsa</b></td>
					</tr>
					
				{/if}
			
				<tr><td>&nbsp;</td></tr>

				<tr>
					<td colspan="10">
						<table border="0" width="100%">
			
				
							<tr>
								<th align='center'>Seq.</th>
								<th align='center'>Modo</th>
								<th align='center'>Descrição</th>
								<th align='center'>Conta (R$)</th>
								<th align='center'>Juros (R$)</th>
								<th align='center'>Multa (R$)</th>
								<th align='center'>Recebido (R$)</th>
								<th align='center'>D. Vencimento</th>
								<th align='center'>D. Recebimento</th>
								<th align='center'>D. Baixa</th>
								<th align='center'>Status</th>
								<th align='center'>Baixado ?</th>
							</tr>


							{section name=j loop=$list_contas_receber[i]}
				
								{if $list_contas_receber[i][j].baixa_conta_bk == "1"}
								
									<tr>
										<td align='center'>{$list_contas_receber[i][j].numero_seq_conta}</td>
										<td>{$list_contas_receber[i][j].descricao_modo_recebimento}</td>
										<td>{$list_contas_receber[i][j].descricao_conta}</td>
										<td align='right'>{$list_contas_receber[i][j].valor_total_conta}</td>
										<td align='right'>{$list_contas_receber[i][j].valor_juros_atraso}</td>
										<td align='right'>{$list_contas_receber[i][j].valor_multa}</td>
										<td align='right'>{$list_contas_receber[i][j].valor_recebido}</td>
										<td align='center'>{$list_contas_receber[i][j].data_vencimento}</td>
										<td align='center'>{$list_contas_receber[i][j].data_recebimento}</td>
										<td align='center'>{$list_contas_receber[i][j].data_baixa}</td>
										<td align='center'>{$list_contas_receber[i][j].status_conta}</td>
										<td align='center'>{$list_contas_receber[i][j].baixa_conta}</td>
									</tr>

								{else}
							
									<tr>
										<td align='center'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_receber={$list_contas_receber[i][j].idconta_receber}">{$list_contas_receber[i][j].numero_seq_conta}</a></td>
										<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_receber={$list_contas_receber[i][j].idconta_receber}">{$list_contas_receber[i][j].descricao_modo_recebimento}</a></td>
										<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_receber={$list_contas_receber[i][j].idconta_receber}">{$list_contas_receber[i][j].descricao_conta}</a></td>
										<td align='right'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_receber={$list_contas_receber[i][j].idconta_receber}">{$list_contas_receber[i][j].valor_total_conta}</a></td>
										<td align='right'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_receber={$list_contas_receber[i][j].idconta_receber}">{$list_contas_receber[i][j].valor_juros_atraso}</a></td>
										<td align='right'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_receber={$list_contas_receber[i][j].idconta_receber}">{$list_contas_receber[i][j].valor_multa}</a></td>
										<td align='right'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_receber={$list_contas_receber[i][j].idconta_receber}">{$list_contas_receber[i][j].valor_recebido}</a></td>
										<td align='center'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_receber={$list_contas_receber[i][j].idconta_receber}">{$list_contas_receber[i][j].data_vencimento}</a></td>
										<td align='center'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_receber={$list_contas_receber[i][j].idconta_receber}">{$list_contas_receber[i][j].data_recebimento}</a></td>
										<td align='center'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_receber={$list_contas_receber[i][j].idconta_receber}">{$list_contas_receber[i][j].data_baixa}</a></td>
										<td align='center'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_receber={$list_contas_receber[i][j].idconta_receber}">{$list_contas_receber[i][j].status_conta}</a></td>
										<td align='center'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idconta_receber={$list_contas_receber[i][j].idconta_receber}">{$list_contas_receber[i][j].baixa_conta}</a></td>
									</tr>

								{/if}
								
								<tr>
									<td class="row" height="1" bgcolor="#999999" colspan="20"></td>
								</tr>

							{/section}

						</table>
					</td>
				</tr>


				<tr><td>&nbsp;</td></tr>

			</table>

			<br>

		{/section}
