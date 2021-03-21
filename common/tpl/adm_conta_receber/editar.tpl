
	<br>

		<div style="width: 100%;">

			<form  action="{$smarty.server.PHP_SELF}?ac=editar&idconta_receber={$info.idconta_receber}" method="post" name = "for_conta_receber" id = "for_conta_receber">
		    	<input type="hidden" name="for_chk" id="for_chk" value="1" />
				<input type="hidden" name="idconta_receber" id="idconta_receber" value="{$info.idconta_receber}" />
				<input type="hidden" name="sigla_modo_recebimento_original" id="sigla_modo_recebimento_original" value="{$info.litsigla_modo_recebimento}" />
				
				<input type="hidden" name="serie_ecf" id="serie_ecf" value="" /> {* Input para receber o número série da ECF cadastrada*}
		   <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados Gerais</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Valores</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Cheques</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

			<div id="tab_0" class="anchor">

			<table width="95%" align="center">

			{if $list_vendas[0].idorcamento != ''}
        		<tr>		
					<td colspan="2" align="center">
	
						<table class="tb4cantos" width="100%">
							<tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Informações da <b>Venda</b></td>
							</tr>
			
							<tr>
								<td align="center">
									Código da Venda: {$list_vendas[0].idorcamento}
								</td>
			
								<td align="center">
									Nº da Nota: {$list_vendas[0].numeroNota}
								</td>
			
								<td align="center">
									Tipo: {$list_vendas[0].tipoOrcamentoDescricao}
								</td>
			
								<td align="center">
									Cliente: {$list_vendas[0].nome_cliente}
								</td>
			
								<td align="center">
									Data da Criação: {$list_vendas[0].datahoraCriacao}
								</td>
			
								<td align="center">
									Vendedor Responsável: {$list_vendas[0].funcionario_criou_orcamento}
								</td>
							</tr>
						</table>

					</td>
		        </tr>
				
				{/if}
				
				<tr><td>&nbsp;</td></tr>

        <tr>
					<td colspan="2" align="center">

						<table class="tb4cantos" width="100%" cellspacing="0">

							<tr>
								<td align="center" width="35%"></td>
								<td class="tb4cantos" align='center' width="35%">Funcionário</td>
								<td class="tb4cantos" align='center' width="30%">Data / Hora</td>
							</tr>

							<tr>
								<td class="tb4cantos">Criação da Conta a Receber</td>
								<td align="center" class="tb4cantos">{$info_funcionario_criou.nome_funcionario}</td>
								<td align="center" class="tb4cantos">{$info.datahoraCriacao_D} {$info.datahoraCriacao_H}</td>
							</tr>

							<tr>
								<td class="tb4cantos">Última alteração dos dados</td>
								<td align="center" class="tb4cantos">{$info_funcionario_alterou.nome_funcionario}</td>
								<td align="center" class="tb4cantos">{$info.datahoraUltAlteracao_D} {$info.datahoraUltAlteracao_H}</td>
							</tr>

						</table>

					</td>
        </tr>

				<tr><td>&nbsp;</td></tr>

				<tr>
					<td align="right" width="40%">
					Filial:
					</td>
					<td>
						<input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
						{$info_filial.nome_filial}
					</td>
				</tr>


				{* Se já tiver dado baixa ou negociado a conta, não deixa alterar *}	
				{* Se o modo não for do tipo CARTEIRA, não deixa alterar *}
				{if ($info.baixa_conta == '1') || ($info.status_conta == 'NE') || ($info.sigla_modo_recebimento != $conf.sigla_modo_carteira) } 
					<tr>
						<td align="right">Modo de recebimento:</td>
						<td>{$info.descricao_modo_recebimento}</td>
					</tr>
				{else}
					<tr>
						<td class="req" align="right">Modo de recebimento:</td>
						<td>
							<select name="litsigla_modo_recebimento" id="litsigla_modo_recebimento">
							<option value="">[selecione]</option>
							{html_options values=$list_modo_recebimento.sigla_modo_recebimento output=$list_modo_recebimento.descricao selected=$info.litsigla_modo_recebimento}
							</select>
						</td>
					</tr>
				{/if}

								
				<tr>
					<td align="right">Número sequencial da conta:</td>
					<td>{$info.numnumero_seq_conta}</td>
				</tr>
				
				<tr>
					<td align="right">Descrição da conta a receber:</td>
					<td>{$info.litdescricao_conta}</td>
					<input type="hidden" id="descricao_conta" name="descricao_conta" value="{$info.litdescricao_conta}" />
				</tr>

				{* Se já tiver dado baixa ou negociado a conta, não deixa alterar *}	
				{if ($info.baixa_conta == '1') || ($info.status_conta == 'NE')} 

					
					<tr>
						<td align="right">Observação:</td>
						<td>{$info.observacao}</td>
					</tr>
					
					
				{else}
					<tr>
						<td align="right">Observação:</td>
						<td>
							<textarea name="litobservacao" id="litobservacao" rows='6' cols='38'>{$info.litobservacao}</textarea>
						</td>
					</tr>
					
					<tr><td>&nbsp;</td></tr>
	
					 
					 <tr>
						<td align="right" class="req">Conta de Débito:</td>
						<td>
	
						    <input type="hidden" name="idplano_debito" id="idplano_debito" value="{$info.plano_debito.idplano}" />
							<input type="hidden" name="idplano_debito_NomeTemp" id="idplano_debito_NomeTemp" value="{$info.plano_debito.descricao}" />
	                       	<input class="long" type="text" name="idplano_debito_Nome" id="idplano_debito_Nome" value="{$info.plano_debito.descricao}"
								onKeyUp="javascript:
									VerificaMudancaCampo('idplano_debito');"/>
							<span class="nao_selecionou" id="idplano_debito_Flag">
								&nbsp;&nbsp;&nbsp;
							</span>
	
	
						</td>
					</tr>
					
					<script language="javascript">
					    new CAPXOUS.AutoComplete("idplano_debito_Nome", function() {ldelim}
					    	return "plano_ajax.php?ac=busca_plano&typing=" + this.text.value + "&idplano=" + document.getElementById('idplano_debito').value + "&campoID=idplano_debito";
					    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
					</script>
					
					
					<tr>
						<td align="right" class="req">Conta de Crédito:</td>
						<td>
	
						    <input type="hidden" name="idplano_credito" id="idplano_credito" value="{$info.plano_credito.idplano}" />
							<input type="hidden" name="idplano_credito_NomeTemp" id="idplano_credito_NomeTemp" value="{$info.plano_credito.descricao}" />
	                       	<input class="long" type="text" name="idplano_credito_Nome" id="idplano_credito_Nome" value="{$info.plano_credito.descricao}"
								onKeyUp="javascript:
									VerificaMudancaCampo('idplano_credito');"/>
							<span class="nao_selecionou" id="idplano_credito_Flag">
								&nbsp;&nbsp;&nbsp;
							</span>
	
	
						</td>
					</tr>
					
					<script language="javascript">
					    new CAPXOUS.AutoComplete("idplano_credito_Nome", function() {ldelim}
					    	return "plano_ajax.php?ac=busca_plano&typing=" + this.text.value + "&idplano=" + document.getElementById('idplano_credito').value + "&campoID=idplano_credito";
					    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
					
					  	// verifica os campos auto-complete
						VerificaMudancaCampo('idplano_debito');
						VerificaMudancaCampo('idplano_credito');
	
					</script>				
					
					
					<tr><td>&nbsp;</td></tr>
	
					<tr>
						<td class="req" align="right">
							Funcionário:
						</td>
						<td>
							<select name="idUltFuncionario" id="idUltFuncionario">
							<option value="">[selecione]</option>
							{html_options values=$list_funcionarios.idfuncionario output=$list_funcionarios.nome_funcionario selected=$smarty.post.idUltFuncionario}
							</select>
						</td>
					</tr>
	
					<tr>
						<td class="req" align="right">
							Senha:
						</td>
						<td>
							<input class="medium" type="password" name="senha_funcionario" id="senha_funcionario" maxlength="32" value=""/>
						</td>
					</tr>
				{/if}

			</table>

			</div>


			{************************************}
			{* TAB 1 *}
			{************************************}

			<div id="tab_1" class="anchor">

			<table width="95%" align="center">

				<tr>
					<td align="right"  width="40%">Valor da conta a receber (R$):</td>
					<td>
						<input type="hidden" name="valor_conta_receber" id="valor_conta_receber" value="{$info.valor_conta_receber}" />
						{$info.valor_conta_receber}
					</td>
				</tr>


				{* Se já tiver dado baixa ou negociado a conta, não deixa alterar *}	
				{* Se for modo de recebimento igual a CHEQUE, então não deixa digitar a data de recebimento *}
				{if ($info.baixa_conta == '1') || ($info.status_conta == 'NE') || ($info.sigla_modo_recebimento == $conf.sigla_modo_cheque)} 

					<tr>
						<td align="right">Valor dos juros de atraso (R$):</td>
						<td>{$info.numvalor_juros_atraso}</td>
						<input type="hidden" name="numvalor_juros_atraso" id="numvalor_juros_atraso" value="{$info.numvalor_juros_atraso}" />
					</tr>
					
					<tr>
						<td align="right">Valor da multa (R$):</td>
						<td>{$info.numvalor_multa}</td>
						<input type="hidden" name="numvalor_multa" id="numvalor_multa" value="{$info.numvalor_multa}" />
					</tr>
	
					<tr>
						<td align="right"><b>Total (R$):</b></td>
						<td id="total_final_cr" class="negrito"></td>
					</tr>

					<tr>
						<td align="right">Valor recebido (R$):</td>
						<td>{$info.numvalor_recebido}</td>
					</tr>

				{else}
					<tr>
						<td align="right">Valor dos juros de atraso (R$):</td>
						<td>
							<input class="short" type="text" name="numvalor_juros_atraso" id="numvalor_juros_atraso" value="{$info.numvalor_juros_atraso}" maxlength='10' onkeydown="FormataValor('numvalor_juros_atraso')" onkeyup="FormataValor('numvalor_juros_atraso')"  onblur="xajax_Calcula_Total_CR_AJAX();"/>
						</td>
					</tr>
					
					<tr>
						<td align="right">Valor da multa (R$):</td>
						<td>
							<input class="short" type="text" name="numvalor_multa" id="numvalor_multa" value="{$info.numvalor_multa}" maxlength='10' onkeydown="FormataValor('numvalor_multa')" onkeyup="FormataValor('numvalor_multa')"  onblur="xajax_Calcula_Total_CR_AJAX();"/>
						</td>
					</tr>
	
					<tr>
						<td align="right"><b>Total (R$):</b></td>
						<td id="total_final_cr" class="negrito"></td>
					</tr>

					<tr>
						<td align="right">Valor recebido (R$):</td>
						<td>
							<input class="short" type="text" name="numvalor_recebido" id="numvalor_recebido" value="{$info.numvalor_recebido}" maxlength='10' onkeydown="FormataValor('numvalor_recebido')" onkeyup="FormataValor('numvalor_recebido')" />
						</td>
					</tr>
				{/if}


 				<tr><td>&nbsp;</td></tr>

				<tr>
					<td align="right">Data de vencimento:</td>
					<td>
						<input type="hidden" name="data_vencimento" id="data_vencimento" value="{$info.litdata_vencimento}" />
						{$info.litdata_vencimento}
					</td>
				</tr>


				{* Se já tiver dado baixa ou negociado a conta, não deixa alterar *}	
				{* Se for modo de recebimento igual a CHEQUE, então não deixa digitar a data de recebimento *}
				{if ($info.baixa_conta == '1') || ($info.status_conta == 'NE') || ($info.sigla_modo_recebimento == $conf.sigla_modo_cheque)} 
					<tr>
						<td align="right">Data do recebimento:</td>
						<td>
							{$info.litdata_recebimento}
						</td>
					</tr>

				{else}
					<tr>
						<td align="right">Data do recebimento:</td>
						<td>
							<input class="short" type="text" name="litdata_recebimento" id="litdata_recebimento" value="{$info.litdata_recebimento}" maxlength='10' onkeydown="mask('litdata_recebimento', 'data')" onkeyup="mask('litdata_recebimento', 'data')" />
							<img src="{$conf.addr}/common/img/calendar.png" id="img_litdata_recebimento" style="cursor: pointer;" /> (dd/mm/aaaa)
						</td>
					</tr>
					<script type="text/javascript">
						Calendar.setup(
							{ldelim}
								inputField : "litdata_recebimento", // ID of the input field
								ifFormat : "%d/%m/%Y", // the date format
								button : "img_litdata_recebimento", // ID of the button
								align  : "cR"  // alinhamento
							{rdelim}
						);
					</script>

					<tr>
						<td align="right">Juros de atraso (% a.d.):</td>
						<td>
							<input class="short" type="text" name="juros_atraso" id="juros_atraso" value="{$info.juros_atraso}" maxlength='5' onkeydown="FormataValor('juros_atraso')" onkeyup="FormataValor('juros_atraso')" />

							<input type='button' class="botao_padrao" value="Calcular Juros de Atraso" name = "Juros" id = "Juros"
								onClick="xajax_Calcular_Juros_Atraso_AJAX(xajax.getFormValues('for_conta_receber'));"
							/>
						</td>
					</tr>
				{/if}


				{if $info.baixa_conta == '1'}
					<tr>
						<td align="right">Data da baixa da conta a receber:</td>
						<td>
							{$info.litdata_baixa}
						</td>
					</tr>
				{/if}

 				<tr><td>&nbsp;</td></tr>

				<tr>
					<td align="right"><b>Status da conta:</b></td>
					<td>
						<input type="hidden" name="status_conta" id="status_conta" value="" />
						<b>{$info.status_conta_descricao}</b>
					</td>
				</tr>

				{* Se já tiver dado baixa ou negociado a conta, não deixa alterar *}	
				{if ($info.baixa_conta == '1') || ($info.status_conta == 'NE')} 
					<tr>
						<td align="right"><b>Conta baixada:</b></td>

						{if $info.baixa_conta == '1'}
							<td><b>Sim</b></td>
						{else}
							<td><b>Não</b></td>
						{/if}

					</tr>
				{else}
					<tr>
						<td align="right">Dar baixa ?</td>
						<td>
							<input {if $info.litbaixa_conta=="0"}checked{/if} class="radio" type="radio" name="litbaixa_conta" id="litbaixa_conta_N" value="0" />Não
							<input {if $info.litbaixa_conta=="1"}checked{/if} class="radio" type="radio" name="litbaixa_conta" id="litbaixa_conta_S" value="1" />Sim
						</td>
					</tr>
				{/if}

			</table>

			</div>


			{************************************}
			{* TAB 2 *}
			{************************************}

			<div id="tab_2" class="anchor">

			<table width="95%" align="center">

				{* Se já tiver dado baixa ou negociado a conta, não deixa alterar *}	
				{* Se for diferente de cheque ou carteira, não deixa alterar *}
				{if ($info.baixa_conta == '1') || 
						($info.status_conta == 'NE') || 
						( ($info.sigla_modo_recebimento != $conf.sigla_modo_cheque) && ($info.sigla_modo_recebimento != $conf.sigla_modo_carteira) )} 
					{* não exibe a tela para inserir o cheque *}
				{else}
					<tr>
						<td colspan="2" align="center">
							<table class="tb4cantos" width="100%">
	
								<tr bgcolor="#F7F7F7">
									<td colspan="9" align="center">Dados do Cheque</td>
								</tr>
	
								<tr>
									<td colspan="9" align="center">Informe os cheques que foram recebidos por esta conta.</td>
								</tr>
	
								<tr><td>&nbsp;</td></tr>
	
								<tr>
									<td colspan="9" align="center">
	
										Código do Cheque:
										<input class="medium" type="text" name="idcheque" id="idcheque" value="{$smarty.post.idcheque}" maxlength='20' onkeydown="FormataInteiro('idcheque')" onkeyup="FormataInteiro('idcheque')" />
	
										<input type='button' class="botao_padrao" value="Inserir cheque" name="botaoInserirCheque" id="botaoInserirCheque"
											onClick="xajax_Insere_Cheque_AJAX(xajax.getFormValues('for_conta_receber'));"
										/>
	
									</td>
								</tr>
	
							</table>
						</td>
					</tr>
	
	
					<tr><td>&nbsp;</td></tr>
				{/if}

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Tabela de Cheques recebidos por esta conta</td>
								<input type="hidden" name="total_cheques" id="total_cheques" value="0" />
			        </tr>

							<tr>
								<td align="center">
									<div id="div_cheques">


									</div>
								</td>
							</tr>

							<script type="text/javascript">
								// busca os dados do cheque
								xajax_Seleciona_Cheques_Conta_Receber_AJAX('{$info.idconta_receber}', '{$info.baixa_conta}');
							</script>


						</table>
					</td>
        </tr>



			</table>

			</div>


			<script language="javascript">
				Processa_Tabs(0, 'tab_'); // seta o tab inicial
			</script>

			<script type="text/javascript">
				// calcula o total da conta a receber
				xajax_Calcula_Total_CR_AJAX();
				
		
				
			</script>	


			{* Se já tiver dado baixa ou negociado a conta, não deixa alterar *}	
			{if ($info.baixa_conta == '1') || ($info.status_conta == 'NE')} 
				{* não o botão alterar *}
			{else}
				<table width="100%">
					
					<tr><td>&nbsp;</td></tr>
	
					<tr>
						<td align="center" colspan="2">
							<input type='button' class="botao_padrao" value="SALVAR" name = "ALTERAR" id = "ALTERAR"
							onClick="xajax_Verifica_Campos_Conta_Receber_AJAX(xajax.getFormValues('for_conta_receber'));" />
							<!--{* if $smarty.session.browser_usuario == 1} 
								onClick="if ( document.getElementById('litbaixa_conta_S').checked )
											alert('Para relizar a baixa, é necessário utilizar o ECF e o navegador Internet Explorer.');
										else
											xajax_Verifica_Campos_Conta_Receber_AJAX(xajax.getFormValues('for_conta_receber'));"
											
							{else}
								onClick="NumeroSerie();xajax_Verifica_Campos_Conta_Receber_AJAX(xajax.getFormValues('for_conta_receber'));"
							{/if *}-->
							
						</td>
					</tr>
	
				</table>
			{/if}	

		</form>

		</div>


