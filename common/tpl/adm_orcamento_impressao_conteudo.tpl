		<table width="95%" align="center" border="0">

			<tr>
				<td><img src="{$conf.addr}/common/img/logo_cliente.png" style="margin-right: 20mm" /></td>
			</tr>


			<tr>
				<td align="center">
					<b>DOCUMENTO AUXILIAR DE VENDA - OR&Ccedil;AMENTO</b>
				</td>
			</tr>

			<tr>
				<td align="center" class='tb_bord_baixo_solid'>
					<b>N&Atilde;O &Eacute; DOCUMENTO FISCAL - N&Atilde;O &Eacute; V&Aacute;LIDO COMO GARANTIA DE MERCADORIA</b>
				</td>
			</tr>

			<tr>
				<td align="center">
					{$info_filial.nome_filial}
				</td>
			</tr>

			<tr>
				<td align="center">
					CNPJ. {$info_filial.cnpj_filial}
					&nbsp;&nbsp;&nbsp;&nbsp;
					Insc. Est. {$info_filial.inscricao_estadual_filial}
				</td>
			</tr>

			<tr>
				<td align="center" class='tb_bord_baixo_solid'>
					{$info_endereco_filial.logradouro} {$info_endereco_filial.numero}
					&nbsp;&nbsp;&nbsp;
					{$info_endereco_filial.nome_bairro} {$info_endereco_filial.nome_cidade} {$info_endereco_filial.sigla_estado}
					&nbsp;&nbsp;&nbsp;
					CEP: {$info_endereco_filial.cep}
					&nbsp;&nbsp;&nbsp;
					Telefone: {$info_filial.telefone_filial}
					&nbsp;&nbsp;&nbsp;
					FAX: {$info_filial.fax_filial}
				</td>
			</tr>

		</table>

		<table width="95%" align="center" border="0">
			<tr>
				<td align="center">
					<b>Or&ccedil;amento N&ordm; {$info.idorcamento_formatado}</b>
					&nbsp;&nbsp;&nbsp;
					N&ordm; do Documento fiscal: ____________
					&nbsp;&nbsp;&nbsp;
					Data de emiss&atilde;o: {$info.datahoraUltEmissao_D} {$info.datahoraUltEmissao_H}
					&nbsp;&nbsp;&nbsp;
					Vendedor: {$info_funcionario_alterou.nome_funcionario}
				</td>
			</tr>
			
			<tr>
				<td>
					Cliente: {$info_cliente.nome_cliente}
				</td>
			</tr>
			<tr>
				<td>
					Endere&ccedil;o: {$info_cliente.logradouro} &nbsp;&nbsp;&nbsp; {$info_cliente.numero} &nbsp;&nbsp;&nbsp;
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
		</table>


		<table width="95%" align="center" border="0">

	    <tr>
				<td colspan="2" align="center">
					<table width="100%">

						<input type="hidden" name="total_produtos" id="total_produtos" value="0" />

						<tr>
							<td align="center" class='tb_bord_baixo_solid'>

								<div id="div_produtos">

									<table width="100%" cellpadding="5">
										<tr>
											<td align='left' width="10%">C&oacute;d.</td>
											<td align='left' width="30%">Produto</td>
											<td align='center' width="5%">Un.</td>
											<td align='center' width="10%">Qtd.</td>
											<td align='center' width="10%">Pre&ccedil;o Un.(R$)</td>
											<td align='center' width="10%">Desc.(%)</td>
											<td align='center' width="10%">Pre&ccedil;o(R$)</td>
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
		
		
		<table width="95%" align="center" border="0">
	    <tr>
				<td align="center" class='tb_bord_baixo_solid'>
					&nbsp;
				</td>
			</tr>
	    <tr>
				<td align="center">
					&Eacute; VEDADA A AUTENTICA&Ccedil;&Atilde;O DESTE DOCUMENTO.
				</td>
			</tr>
		</table>
