
			

				<table width="95%" align="center">
			        <tr>
						<td colspan="2" align="center">
							<table class="tb4cantos" width="100%">
						        <tr bgcolor="#F7F7F7">
									<td colspan="9" align="center">Busca dos <b>CLIENTES</b></td>
			    			    </tr>
								
								
								<tr>
									<td colspan="9" align="center">
										Cliente:
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
									</td>
								</tr>
									
								<script type="text/javascript">
								    new CAPXOUS.AutoComplete("idcliente_Nome", function() {ldelim}
								    	return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente" + "&mostraDetalhes=0";
								    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
								</script>
	
	
								<script type="text/javascript">
								  // verifica os campos auto-complete
									VerificaMudancaCampo('idcliente');
								</script>

								<tr>
									<td colspan="9" align="center">
										<input type='button' class="botao_padrao" value="Inserir Cliente" name="botaoInserirCliente" id="botaoInserirCliente"
											onClick="xajax_Insere_Cliente_AJAX(xajax.getFormValues('for_funcionario'), 1);"
										/>
									</td>
								</tr>
							</table>
						</td>
        			</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
        			<tr>
						<td colspan="2" align="center">
							<table class="tb4cantos" width="100%">
						        <tr bgcolor="#F7F7F7">
									<td colspan="9" align="center">Tabela de clientes atendidos por esse funcionário</td>
									<input type="hidden" name="total_clientes" id="total_clientes" value="0" />
			        			</tr>
								<tr>
									<td align="center">
										<div id="div_clientes">
											<table width="100%" cellpadding="5">
												<tr>
													<th align='center' width="40%">Nome</th>
													<th align='center' width="25%">Telefone</th>
													<th align='center' width="25%">Cidade / Estado</th>
													<th align='center' width="10%">Excluir ?</th>
												</tr>
											</table>
										</div>
									</td>
								</tr>
								{if $smarty.get.ac == "editar"} 
									<script type="text/javascript">
										// Inicialmente, preenche todos os funcionarios que atendem este cliente
										// o segundo parâmetro indica para pegar o funcionário relacionado ao cliente
										xajax_Seleciona_Cliente_AJAX('{$info.idfuncionario}');
									</script>
								{/if}
							</table>
						</td>
        			</tr>
				</table>
			
			