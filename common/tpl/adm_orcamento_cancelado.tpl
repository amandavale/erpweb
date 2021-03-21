<form  action="" method="post" name="for_orcamento" id="for_orcamento">
<input type="hidden" name="for_chk" id="for_chk" value="1" />
<input type="hidden" name="maximoItensOrcamento" id="maximoItensOrcamento" value="{$smarty.post.maximoItensOrcamento}" />
<input type="hidden" name="descontoMaximoOrcamento" id="descontoMaximoOrcamento" value="{$smarty.post.descontoMaximoOrcamento}" />

<input type="hidden" name="tipoPreco" id="tipoPreco" value="{$info.littipoPreco}" />
<input type="hidden" name="desconto" id="desconto" value="{$info.numdesconto}" />
<input type="hidden" name="frete" id="frete" value="{$info.numfrete}" />
<input type="hidden" name="outras_despesas" id="outras_despesas" value="{$info.numoutras_despesas}" />

	<table width="95%" align="center">

		<tr><td>&nbsp;</td></tr>

		{* Se tiver sido cancelado, mostra a mensagem abaixo *}
		{if $info.idmotivo_cancelamento != ""}

			<tr>
				<td colspan="2" align="center">
	
					<table width="100%"  border="0" cellpadding="2" cellspacing="3" bgcolor="#FDF5E6" class="tb4cantos">
						<tr>
							<td class="req">
								<b>
									ATENÇÃO: Este Orçamento / Venda (Número {$info.idorcamento_formatado}) foi CANCELADO! Não será possível
									fazer alterações nele.
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
	
					</table>
				</td>
			</tr>
	
			<tr><td>&nbsp;</td></tr>

		{/if}


    <tr>
			<td colspan="2" align="center">
				<table class="tb4cantos" width="100%">

	        <tr bgcolor="#F7F7F7">
						<td colspan="9" align="center">Dados Gerais: <b>ORÇAMENTO</b></td>
	        </tr>

					<tr>
						<td colspan="9" align="center">
							<b>Número do Orçamento / Venda: {$info.idorcamento_formatado}</b>
						</td>
					</tr>

					{if $info.tipoOrcamento != "O"}
						<tr>
							<td colspan="9" align="center">
								<b>Tipo da Nota: {$info.tipoOrcamento}</b>
							</td>
						</tr>
						<tr>
							<td colspan="9" align="center">
								<b>Número da Nota: {$info.numeroNotaFormatado}</b>
							</td>
						</tr>
					{/if}

					<tr>
						<td width="33%" align="center">
							Filial:
						  {$info_filial.nome_filial}
						</td>

						<td width="33%" align="center">
							<input type="hidden" name="littipoPreco" id="tipoPreco" value="{$info.littipoPreco}" />
							Preço de:
							{if $info.littipoPreco=="B"}Balcão
							{elseif $info.littipoPreco=="O"}Oferta
							{elseif $info.littipoPreco=="A"}Atacado
							{elseif $info.littipoPreco=="T"}Telemarketing
							{/if}
						</td>

						<td width="33%" align="center">
							Validade do Orçamento (dias):
						  {$info.numvalidade}
						</td>
				
					</tr>

				</table>
				

				<table class="tb4cantos" width="100%" cellspacing="0">

					<tr>
						<td align="center" width="35%"></td>
						<td class="tb4cantos" align='center' width="35%">Vendedor</td>
						<td class="tb4cantos" align='center' width="30%">Data / Hora</td>
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
								Cliente: {$info_cliente.nome_cliente}
							</td>
						</tr>
						<tr>
							<td>
								Endereço: {$info_cliente.logradouro} &nbsp;&nbsp;&nbsp; {$info_cliente.numero} &nbsp;&nbsp;&nbsp;
								Bairro: {$info_cliente.nome_bairro} &nbsp;&nbsp;&nbsp;
								Cidade: {$info_cliente.nome_cidade} &nbsp;&nbsp;&nbsp;
								Estado: {$info_cliente.sigla_estado} &nbsp;&nbsp;&nbsp;
								CEP: {$info_cliente.cep}
							</td>
						</tr>
						<tr>
							<td>
								Telefone: {$info_cliente.telefone_cliente} &nbsp;&nbsp;&nbsp;
					 			Fax: {$info_cliente.fax_cliente} &nbsp;&nbsp;&nbsp;
					 			Insc. Est.: {$info_cliente.inscricao_estadual_cliente} &nbsp;&nbsp;&nbsp;
					 			CPF/CNPJ: {$info_cliente.cpf_cnpj}
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
													<td align='left' width="10%">Cód.</td>
													<td align='left' width="30%">Produto</td>
													<td align='center' width="5%">Un.</td>
													<td align='center' width="10%">Qtd.</td>
													<td align='center' width="10%">Preço Un.(R$)</td>
													<td align='center' width="10%">Desc.(%)</td>
													<td align='center' width="10%">Preço(R$)</td>
													<td align='center' width="10%">Total(R$)</td>
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


					<tr>
					  <td colspan="2">

							<table width="100%" cellpadding="0">

								<tr>
									<input type="hidden" name="valor_total_produtos" id="valor_total_produtos" value="0.00" />
									<td width="80%" align="right">
										Total de Produtos: R$
									</td>
									<td align="right" id="TotalProdutos">
										0,00
									</td>
								</tr>
								
								<tr>
									<td align="right">Desconto: R$</td>
									<td align="right">
										{$info.numdesconto}
									</td>
								</tr>
	
								<tr>
									<td align="right">Frete: R$</td>
									<td align="right">
										{$info.numfrete}
									</td>
								</tr>
	
								<tr>
									<td align="right">Outras Despesas: R$</td>
									<td align="right">
										{$info.numoutras_despesas}
									</td>
								</tr>
	
	
								<tr>
									<input type="hidden" name="valor_total_nota" id="valor_total_nota" value="0.00" />
									<td width="80%" align="right">
										Total: R$
									</td>
									<td align="right" id="TotalNota">
										0,00
									</td>
								</tr>


			    		</table>

						</td>
					</tr>

				</table>

			</td>
		</tr>


	</table>

</form>
