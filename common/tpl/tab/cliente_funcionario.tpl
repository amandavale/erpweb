
			

				<table width="95%" align="center">
			        <tr>
						<td colspan="2" align="center">
							<table class="tb4cantos" width="100%">
						        <tr bgcolor="#F7F7F7">
									<td colspan="9" align="center">Busca dos <b>FUNCIONÁRIOS</b></td>
			    			    </tr>
								<tr>
									<td colspan="9" align="center" class = 'req'>
										Funcionário:
										<input type="hidden" name="idfuncionario" id="idfuncionario" value="{$smarty.post.idfuncionario}" />
										<input type="hidden" name="idfuncionario_NomeTemp" id="idfuncionario_NomeTemp" value="{$smarty.post.idfuncionario_NomeTemp}" />
										<input class="extralarge" type="text" name="idfuncionario_Nome" id="idfuncionario_Nome" value="{$smarty.post.idfuncionario_Nome}"
											onKeyUp="javascript: VerificaMudancaCampo('idfuncionario'); "
										/>
										<span class="nao_selecionou" id="idfuncionario_Flag">
											&nbsp;&nbsp;&nbsp;
										</span>
									</td>
								</tr>
								<script type="text/javascript">
							    	new CAPXOUS.AutoComplete("idfuncionario_Nome", function() {ldelim}
							    		return "funcionario_ajax.php?ac=busca_funcionario&typing=" + this.text.value + "&campoID=idfuncionario";
							    	{rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
								</script>
								<script type="text/javascript">
								  // verifica os campos auto-complete
									VerificaMudancaCampo('idfuncionario');
								</script>
								<tr>
									<td colspan="9" align="center">
										<input type='button' class="botao_padrao" value="Inserir funcionário" name="botaoInserirFuncionario" id="botaoInserirFuncionario"
											onClick="xajax_Insere_Funcionario_AJAX(xajax.getFormValues('for_cliente'), 1);"
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
									<td colspan="9" align="center">Tabela de Funcionários Ligados a esse cliente</td>
									<input type="hidden" name="total_funcionarios" id="total_funcionarios" value="0" />
			        			</tr>
								<tr>
									<td align="center">
										<div id="div_funcionarios">
											<table width="100%" cellpadding="5">
												<tr>
													<th align='center' width="35%">Nome</th>
													<th align='center' width="10%">Cargo</th>
													<th align='center' width="15%">Tipo de Funcionário</th>
													<th align='center' width="5%">Excluir ?</th>
												</tr>
											</table>
										</div>
									</td>
								</tr>
								{if $smarty.get.ac == "editar"} 
									<script type="text/javascript">
										// Inicialmente, preenche todos os funcionarios que atendem este cliente
										// o segundo parâmetro indica para pegar o funcionário relacionado ao cliente
										xajax_Seleciona_Funcionario_AJAX('{$info.idcliente}', 1); 
									</script>
								{/if}
							</table>
						</td>
        			</tr>
				</table>
			
			