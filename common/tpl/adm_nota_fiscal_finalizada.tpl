<form  action="{$smarty.server.PHP_SELF}?ac=editarNF&idorcamento={$info.idorcamento}&nao_atualizar=1" method="post" name="for_orcamento" id="for_orcamento">
<input type="hidden" name="for_chk" id="for_chk" value="1" />
<input type="hidden" name="idorcamento" id="idorcamento" value="{$smarty.get.idorcamento}" />
<input type="hidden" name="maximoItensOrcamento" id="maximoItensOrcamento" value="{$smarty.post.maximoItensOrcamento}" />
<input type="hidden" name="descontoMaximoOrcamento" id="descontoMaximoOrcamento" value="{$smarty.post.descontoMaximoOrcamento}" />
<input type="hidden" name="tipoOrcamento" id="tipoOrcamento" value="{$smarty.session.tipo_orcamento}" />

<input type="hidden" name="valor_pago_temp" id="valor_pago_temp" value="{$info.valor_total}" />
<input type="hidden" name="tipoPreco" id="tipoPreco" value="{$info.littipoPreco}" />
<input type="hidden" name="desconto" id="desconto" value="{$info.numdesconto}" />
<input type="hidden" name="nota" id="nota" value="{$info.numeroNotaFormatado}" />
<input type="hidden" name="serie_ecf" id="serie_ecf" value="{$info.serie_ecf}" />
<input type="hidden" name="cancelar_emissao_fiscal" id="cancelar_emissao_fiscal" value="0" />
<input type="hidden" name="chk_imprimir_emissao_nf" id="chk_imprimir_emissao_nf" value="0" />
<input type="hidden" name="semestoque" id="semestoque" value="{$info.semestoque}" />
<input type="hidden" name="numnumeroNota" id="numnumeroNota" value="" />
<input type="hidden" name="travar_teclado" id="travar_teclado" value="{$conf.travar_teclado}" />
<input type="hidden" name="usaTEF" id="usaTEF" value="0" />
			
	<table width="95%" align="center">

    <tr>
			<td colspan="2" align="center">

				<table width="100%"  border="0" cellpadding="2" cellspacing="3" bgcolor="#FDF5E6" class="tb4cantos">

					{if $info.idmotivo_cancelamento != ""}
						<tr>
							<td class="req">
								<b>
									ATENÇÃO: {$conf.area} CANCELADO! Não será possível fazer alterações.
								</b>
							</td>
						</tr>

						<tr>
							<td class="req">
								<b>
									Motivo do cancelamento: {$info_motivo_cancelamento.descricao}
								</b>
							</td>
						</tr>
					{else}
						<tr>
							<td class="req">
								<b>
									ATENÇÃO: {$conf.area} já gravado! Não será possível fazer alterações.
								</b>
							</td>
						</tr>
					{/if}


				</table>
			</td>
    </tr>

		<tr><td>&nbsp;</td></tr>


    <tr>
			<td colspan="2" align="center">
				<table class="tb4cantos" width="100%">

	        <tr bgcolor="#F7F7F7">
						<td colspan="9" align="center">Dados Gerais: <b>{$conf.area}</b></td>
	        </tr>


					<tr>
						<td width="50%">
							<table>
								<tr>
									<td align="left">
										Filial:
										{$info_filial.nome_filial}
									</td>
								</tr>

								<tr>
									<td align="left">
										<input type="hidden" name="littipoPreco" id="tipoPreco" value="{$info.littipoPreco}" />
										Preço de:
										{if $info.littipoPreco=="B"}Balcão
										{elseif $info.littipoPreco=="O"}Oferta
										{elseif $info.littipoPreco=="A"}Atacado
										{elseif $info.littipoPreco=="T"}Telemarketing
										{/if}
									</td>
								</tr>

								{* Tem o CFOP apenas se nao for Cupom Fiscal *}
								{if $smarty.session.tipo_orcamento != "ECF"}
									<tr>
										<td align="left">
											CFOP:
											{$info_cfop.codigo}
										</td>
									</tr>
								{/if}
			
								<tr>
									<td align="left">
										<b>
											Número do Documento Fiscal:
											{$info.numeroNotaFormatado}
										</b>
									</td>
								</tr>

								<tr>
									<td align="left">
										<b>
											Número da Venda:
											{$info.idorcamento_formatado}
										</b>
									</td>
								</tr>

								{* Tem modelo e série da nota apenas se nao for Cupom Fiscal *}
								{if $smarty.session.tipo_orcamento != "ECF"}			
									<tr>
										<td align="left" colspan="2">
											Modelo da Nota:
											{$info.modeloNota}
										</td>
									</tr>
				
									<tr>
										<td align="left" colspan="2">
											Série da Nota:
											{$info.serieNota}
										</td>
									</tr>
								{/if}


							</table>
						</td>
						
						<td align="right">
							Observações:
							{$info.obs}
						</td>

					</tr>
				</table>



				<table class="tb4cantos" width="100%" cellspacing="0">

					<tr>
						<td align="center" width="35%"></td>
						<td class="tb4cantos" align='center' width="35%">Vendedor / Funcionário</td>
						<td class="tb4cantos" align='center' width="30%">Data / Hora</td>
					</tr>

					<tr>
						<td class="tb4cantos">Criação da Emissão Fiscal</td>
						<td align="center" class="tb4cantos">{$info_funcionario_criouNF.nome_funcionario}</td>
						<td align="center" class="tb4cantos">{$flags.data_criacaoNF}</td>
					</tr>


					<tr>
						<td class="tb4cantos">Criação do Orçamento</td>
						<td align="center" class="tb4cantos">{$info_funcionario_criou.nome_funcionario}</td>
						<td align="center" class="tb4cantos">{$info.datahoraCriacao_D} {$info.datahoraCriacao_H}</td>
					</tr>

					<tr>
						<td class="tb4cantos">Última alteração dos dados</td>
						<td align="center" class="tb4cantos">{$info_funcionario_alterou.nome_funcionario}</td>
						<td align="center" class="tb4cantos">{$info.datahoraUltEmissao_D} {$info.datahoraUltEmissao_H}</td>
					</tr>

				</table>

			</td>
    </tr>

    
		<tr><td>&nbsp;</td></tr>

    <tr>
			<td colspan="2" align="center">
				<table class="tb4cantos" width="100%">

	        <tr bgcolor="#F7F7F7">
						<td colspan="9" align="center">Dados do <b>CLIENTE</b></td>
	        </tr>

	        <input type="hidden" name="dados_cliente_linha_1" id="dados_cliente_linha_1" value="{$info_dados_cliente.dados_cliente_linha_1}">
	        <input type="hidden" name="dados_cliente_linha_2" id="dados_cliente_linha_2" value="{$info_dados_cliente.dados_cliente_linha_2}">
	        <input type="hidden" name="dados_cliente_linha_3" id="dados_cliente_linha_3" value="{$info_dados_cliente.dados_cliente_linha_3}">
	        <input type="hidden" name="dados_cliente_linha_4" id="dados_cliente_linha_4" value="{$info_dados_cliente.dados_cliente_linha_4}">
	        <input type="hidden" name="dados_cliente_linha_5" id="dados_cliente_linha_5" value="{$info_dados_cliente.dados_cliente_linha_5}">


					{if $flags.nao_selecionou == 1}
						<tr>
							<td align="right" width="50%">Nome do cliente provisório:</td>
							<td>{$info.litnomeClienteProv}</td>
						</tr>

						<tr>
							<td align="right">Informações do Cliente:</td>
							<td>{$info.litinfoClienteProv}</td>
						</tr>
					{else}
						<tr>
							<td>
								Cliente: {$info_dados_cliente.nome_cliente}
							</td>
						</tr>
						<tr>
							<td>
								Endereço: {$info_dados_cliente.logradouro} &nbsp;&nbsp;&nbsp; {$info_dados_cliente.numero} &nbsp;&nbsp;&nbsp;
								Bairro: {$info_dados_cliente.nome_bairro} &nbsp;&nbsp;&nbsp;
								Cidade: {$info_dados_cliente.nome_cidade} &nbsp;&nbsp;&nbsp;
								Estado: {$info_dados_cliente.sigla_estado} &nbsp;&nbsp;&nbsp;
								CEP: {$info_dados_cliente.cep}
							</td>
						</tr>
						<tr>
							<td>
								Telefone: {$info_dados_cliente.telefone_cliente} &nbsp;&nbsp;&nbsp;
					 			Fax: {$info_dados_cliente.fax_cliente} &nbsp;&nbsp;&nbsp;
					 			Insc. Est.: {$info_dados_cliente.inscricao_estadual_cliente} &nbsp;&nbsp;&nbsp;
					 			CPF/CNPJ: {$info_dados_cliente.cpf_cnpj}
							</td>
						</tr>
					{/if}

				</table>
			</td>
    </tr>


		<tr><td>&nbsp;</td></tr>

		<tr>
		  <td colspan="2">

				<table width="100%" align="center" border="0">

			    <tr>
						<td colspan="2" align="center">
							<table width="100%">

								<input type="hidden" name="total_produtos" id="total_produtos" value="0" />

								<tr>
									<td align="center" class='tb_bord_baixo_solid'>

										<div id="div_produtos">

											<table width="100%" cellpadding="5">
												<tr>
													<td align='left' width="5%">Cód.</td>
													<td align='left' width="20%">Produto</td>
													<td align='center' width="5%">Un.</td>
													<td align='center' width="10%">Qtd.</td>
													<td align='center' width="10%">Preço Un.(R$)</td>
													<td align='center' width="10%">Desc.(%)</td>
													<td align='center' width="10%">Preço(R$)</td>
													<td align='center' width="10%">Total(R$)</td>
													<td align='center' width="7%">CST</td>
													<td align='center' width="3%">ST</td>
													<td align='center' width="10%">ICMS (%)</td>
												</tr>
											</table>

										</div>
									</td>
								</tr>


								<script type="text/javascript">
								  // Inicialmente, preenche todos os produtos que fazem parte do orçamento
									xajax_Seleciona_Produtos_AJAX('{$info.idorcamento}');
								</script>


							</table>
						</td>
			    </tr>

	
					<tr><td>&nbsp;</td></tr>
	
					<tr>
						<td colspan="2" align="right">
							Desconto: R$
							{$info.numdesconto}
						</td>
					</tr>
	
					<tr><td>&nbsp;</td></tr>

					<tr>
					  <td colspan="2">
	
							<table class="tb4cantos" width="100%">
	
								<tr>
									<td align="right">
										Base de cálculo do ICMS: R$
									</td>
	
									<td align="right">
										Valor do ICMS: R$
									</td>
	
									<td align="right">
										Base de cálculo ICMS Substituição: R$
									</td>
	
									<td align="right">
										Valor do ICMS Substituição: R$
									</td>
									
									<td align="right">
										Total de Produtos: R$
									</td>
								</tr>
	
	
								<tr>
									<td align="right">
										{$info.numbase_calculo_icms}
									</td>
	
									<td align="right">
										{$info.numvalor_icms}
									</td>
	
									<td align="right">
										{$info.numbase_calc_icms_sub}
									</td>
	
									<td align="right">
										{$info.numvalor_icms_sub}
									</td>
									
									<td align="right">
										{$info.numvalor_total_produtos}
									</td>
								</tr>
	
								<tr><td>&nbsp;</td></tr>
	
								<tr>
									<td align="right">
										Valor do Frete: R$
									</td>
	
									<td align="right">
										Valor do Seguro: R$
									</td>
	
									<td align="right">
										Outras Despesas: R$
									</td>
	
									<td align="right">
										Valor Total do IPI: R$
									</td>
	
									<td align="right"  class="negrito">
										Valor Total da Nota: R$
									</td>
								</tr>
	
								<tr>
									<td align="right">
										{$info.numfrete}
									</td>
	
									<td align="right">
										{$info.numvalor_seguro}
									</td>
	
									<td align="right">
										{$info.numoutras_despesas}
									</td>
	
									<td align="right">
										{$info.numvalor_total_ipi}
									</td>
	
									<td align="right" class="negrito">
										{$info.numvalor_total_nota}
									</td>
								</tr>
	
	
							</table>
	
						</td>
					</tr>

				</table>

			</td>
		</tr>


		{* POR ENQUANTO VAI MOSTRAR OS DADOS ADICIONAIS E DE TRANSPORTADOR PARA ECF TAMBÉM *}
		{* mostra os dados do transportador apenas se nao for ECF *}
		{* if $smarty.session.tipo_orcamento != "ECF" *}

		<tr><td>&nbsp;</td></tr>

    <tr>
			<td colspan="2" align="center">
				<table class="tb4cantos" width="100%">

	        <tr bgcolor="#F7F7F7">
						<td colspan="9" align="center">Transportador</td>
	        </tr>

					<tr>
						<td>
							Transportador: {$info_transportador.nome_transportador}
						</td>
					</tr>
					<tr>
						<td>
							Endereço: {$info_transportador.logradouro} &nbsp;&nbsp;&nbsp; {$info_transportador.numero} &nbsp;&nbsp;&nbsp;
							Bairro: {$info_transportador.nome_bairro} &nbsp;&nbsp;&nbsp;
							Cidade: {$info_transportador.nome_cidade} &nbsp;&nbsp;&nbsp;
							Estado: {$info_transportador.sigla_estado} &nbsp;&nbsp;&nbsp;
							CEP: {$info_transportador.cep}
						</td>
					</tr>
					<tr>
						<td>
							Telefone: {$info_transportador.telefone_transportador} &nbsp;&nbsp;&nbsp;
				 			Fax: {$info_transportador.fax_transportador} &nbsp;&nbsp;&nbsp;
				 			Insc. Est.: {$info_transportador.inscricao_estadual_transportador} &nbsp;&nbsp;&nbsp;
				 			CPF/CNPJ: {$info_transportador.cpf_cnpj}
						</td>
					</tr>

					<tr><td>&nbsp;</td></tr>

					<tr>
						<td>
							Placa do veículo: {$info.transportador_placa_veiculo} &nbsp;&nbsp;&nbsp;
				 			Frete p/ conta: {$info.transportador_frete_por_conta_descricao} &nbsp;&nbsp;&nbsp;
				 			Quantidade: {$info.transportador_quantidade} &nbsp;&nbsp;&nbsp;
				 			Espécie: {$info.transportador_especie}
						</td>
					</tr>

					<tr>
						<td>
				 			Marca: {$info.transportador_marca} &nbsp;&nbsp;&nbsp;
				 			Número: {$info.transportador_numero} &nbsp;&nbsp;&nbsp;
				 			Peso Bruto: {$info.transportador_peso_bruto} &nbsp;&nbsp;&nbsp;
				 			Peso Líquido: {$info.transportador_peso_liquido}
						</td>
					</tr>

					<tr><td>&nbsp;</td></tr>

	        <tr bgcolor="#F7F7F7">
						<td colspan="9" align="center">Dados Adicionais</td>
	        </tr>

					<tr>
						<td>
							&nbsp;{$info.dados_adicionais}
						</td>
					</tr>

				</table>
			</td>
    </tr>
    
    {* /if *}


		<tr><td>&nbsp;</td></tr>

		{* deixa Cancelar e Imprimir, pois a NF já foi gravada anteriormente e ainda não foi cancelada *}
		{if $info.idmotivo_cancelamento == ""}

			{* Mostra a versão de visualização e impressão da nota apenas se for nota fiscal *}
			{if $smarty.session.tipo_orcamento == "NF"}

				<tr>
					<td align="center" colspan="2">
	
						<table class="tb4cantos" width="50%">
	
							<tr bgcolor="#F7F7F7">
								<td colspan="9" align="center"><b>IMPRESSÃO</b></td>
							</tr>
	
							<tr><td>&nbsp;</td></tr>
	
							<tr>
								<td align="center">
									<input name="Imprimir" type="button" class="botao_padrao" value="IMPRIMIR: {$conf.area}"
										onClick="xajax_ReImpressao_Fiscal_AJAX(xajax.getFormValues('for_orcamento'));"
									/>
								</td>
							</tr>

							<tr><td>&nbsp;</td></tr>
	
							<tr>
								<td align="center">
									<input name="Imprimir" type="button" class="botao_padrao" value="VISUALIZAR: {$conf.area}"
										onClick="xajax_ReImpressao_Fiscal_AJAX(xajax.getFormValues('for_orcamento'), 1);"
									/>
								</td>
							</tr>	
	
						</table>
	
					</td>
				</tr>

				<tr><td>&nbsp;</td></tr>

			{/if}



			<tr>
	    	<td align="center" colspan="2">

					<table class="tb4cantos" width="50%">

		        <tr bgcolor="#F7F7F7">
							<td colspan="9" align="center"><b>CANCELAMENTO</b></td>
		        </tr>

		        <tr><td>&nbsp;</td></tr>

						<tr>
							<td align="right">Motivo do cancelamento:</td>
							<td>
								<select name="idmotivo_cancelamento" id="idmotivo_cancelamento">
								<option value="">[selecione]</option>
								{html_options values=$list_motivo_cancelamento.idmotivo_cancelamento output=$list_motivo_cancelamento.descricao selected=$info.numidmotivo_cancelamento}
								</select>
							</td>
						</tr>

						<tr>
							<td align="right">Funcionário:</td>
							<td>
								<select name="idUltfuncionario" id="idfuncionario">
								<option value="">[selecione]</option>
								{html_options values=$list_funcionarios.idfuncionario output=$list_funcionarios.nome_funcionario selected=$smarty.post.idfuncionario}
								</select>
							</td>
						</tr>

						<tr>
							<td align="right">Senha:</td>
							<td>
								<input class="medium" type="password" name="senha_funcionario" id="senha_funcionario" maxlength="32" value=""/>
							</td>
						</tr>

						<tr><td>&nbsp;</td></tr>

						<tr>
							<td align="center" colspan="2">
								<input name="Cancelar" type="button" class="botao_padrao" value="CANCELAR: {$conf.area}"
									onClick="xajax_Verifica_Cancelamento_Orcamento_AJAX(xajax.getFormValues('for_orcamento'));"
								/>
							</td>
						</tr>

					</table>

				</td>
			</tr>
		{/if}



	</table>

</form>
