{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if $flags.okay}

	<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>
	<script type="text/javascript" src="{$conf.addr}/common/js/selecionar_todos_nenhum.js"></script>

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
		  	<td class="descricao_tela" WIDTH="10%">
				{$conf.area}
			</td>
	  		<td class="tela" WIDTH="5%">
				Opera&ccedil;&otilde;es:
			</td>
	  		<td class="descricao_tela">
				{if $list_permissao.adicionar == '1'}
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">gerar arquivo de remessa</a>
				{/if}
				{if $list_permissao.listar == '1'}
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">listar</a>
				{/if}
			</td>
		</tr>
	</table>

	{include file="div_erro.tpl"}

	{if $flags.action == "listar"}

    {if $flags.sucesso != ""}
	  	{include file="div_resultado_inicio.tpl"}
	  		{$flags.sucesso}
	  	{include file="div_resultado_fim.tpl"}
	{/if}

	<br>

  	<form  action="{$smarty.server.PHP_SELF}?ac=listar" method="post" name="for_retorno" id="for_retorno">
    <input type="hidden" name="for_chk" id="for_chk" value="1" />
	<table width="100%">

        <tr>
			<td colspan="2" align="center">
				<table class="tb4cantos" width="100%">
			        <tr bgcolor="#F7F7F7">
						<td colspan="9" align="center">Busca de arquivos de remessa</td>
			        </tr>

					<tr>
						<td align="right" width="25%">Filial:</td>
						<td>
							<input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
							{$info_filial.nome_filial}
						</td>
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
						<td colspan="2" align="center">
						  <div id="dados_cliente">
							</div>
						</td>
					</tr>

					<script type="text/javascript">
					    new CAPXOUS.AutoComplete("idcliente_Nome", function() {ldelim}
					    	return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente" + "&mostraDetalhes=1";
					    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
					
						 // verifica os campos auto-complete
						VerificaMudancaCampo('idcliente');
					</script>

					<tr><td colspan="2">&nbsp;</td></tr>

					<tr>
						<td align="right" width="25%">Data de gera&ccedil;&atilde;o  De: </td>
						<td colspan="" align="left">
						
							<input class="short" type="text" name="data_geracao_de" id="data_geracao_de" value="{$smarty.post.data_geracao_de}" maxlength='10' onkeydown="mask('data_geracao_de', 'data')" onkeyup="mask('data_geracao_de', 'data')" />
							<img src="{$conf.addr}/common/img/calendar.png" id="img_data_geracao_de" style="cursor: pointer;" />
							<script type="text/javascript">
								Calendar.setup(
									{ldelim}
										inputField : "data_geracao_de", // ID of the input field
										ifFormat : "%d/%m/%Y", // the date format
										button : "img_data_geracao_de", // ID of the button
										align  : "cR"  // alinhamento
									{rdelim}
								);
							</script>									
									
							At&eacute;:
							<input class="short" type="text" name="data_geracao_ate" id="data_geracao_ate" value="{$smarty.post.data_geracao_ate}" maxlength='10' onkeydown="mask('data_geracao_ate', 'data')" onkeyup="mask('data_geracao_ate', 'data')" /> 
							<img src="{$conf.addr}/common/img/calendar.png" id="img_data_geracao_ate" style="cursor: pointer;" />
							<script type="text/javascript">
								Calendar.setup(
									{ldelim}
										inputField : "data_geracao_ate", // ID of the input field
										ifFormat : "%d/%m/%Y", // the date format
										button : "img_data_geracao_ate", // ID of the button
										align  : "cR"  // alinhamento
									{rdelim}
								);
							</script>	
							(dd/mm/aaaa)
						</td>
					</tr>

					<tr>
						<td align="right" width="25%">Banco:</td>
						<td>
					
							<select name="modo_recebimento" id="modo_recebimento" >
								{html_options values=$lista_bancos_remessa.modo_recebimento output=$lista_bancos_remessa.nome_banco selected=$smarty.post.modo_recebimento}
							</select>
							
						</td>
					</tr> 																

					<tr>
	        			<td align="center" colspan="2">
							<input type="submit" class="botao_padrao" value="Buscar" name="Buscar" />
							<input type="submit" class="botao_padrao" value="Limpar busca" name="Limpar" />
	        			</td>
	        		</tr>
				</table>
			</td>
        </tr>
	</table>
	</form>
	

	{if count($dados_arquivos)}
	
	<p align="center">Listando {$conf.area} de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p>

	<table width="80%" align="center">
		<tr>
			<th align='center'>Nome do arquivo</th>
			<th align='center'>Data de gera&ccedil;&atilde;o</th>						
			<th align='center'>Baixar <br />arquivo</th>
			<th align='center'>Desfazer <br />remessa</th>
		</tr>
	
		{section name=i loop=$dados_arquivos}
		<tr  bgcolor = "{if $dados_arquivos[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
		
		
			<td align="center">
				<a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=detalhar_arquivo_remessa&idarquivo_remessa={$dados_arquivos[i].idarquivo_remessa}">
					{$dados_arquivos[i].nome_arquivo}
				</a>
			</td>
		    <td align="center">
		    	<a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=detalhar_arquivo_remessa&idarquivo_remessa={$dados_arquivos[i].idarquivo_remessa}">
		    		{$dados_arquivos[i].timestamp}
		    	</a>
		    </td>
 		    <td align="center">
		    	<a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=baixar_arquivo_remessa&idarquivo_remessa={$dados_arquivos[i].idarquivo_remessa}">
			    	<img src="{$conf.addr}/common/img/ok.png" id="img_baixar_arquivo_{$dados_arquivos[i].idarquivo_remessa}" style="cursor: pointer;" />
				</a>
		    </td>
		    <td align="center">
		    	<a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=desfazer_remessa&idarquivo_remessa={$dados_arquivos[i].idarquivo_remessa}&nome_remessa={$dados_arquivos[i].nome_arquivo}" onClick="return(confDeleteSimples('ATEN&Ccedil;&Atilde;O! Confirma a desassocia&ccedil;&atilde;o da remessa {$dados_arquivos[i].nome_arquivo}?'))">
			    	<img src="{$conf.addr}/common/img/desfazer.png" id="img_desfazer_remessa_{$dados_arquivos[i].idarquivo_remessa}" style="cursor: pointer;" />
				</a>
		    </td>
		</tr>
		        
		<tr>
			<td class="row" height="1" bgcolor="#999999" colspan="9"></td>
		</tr>
		{/section}
	</table>
	
    <p align="center" id="nav">{$nav}</p>	
	{/if}
	
	
	{elseif $flags.action == "detalhar_arquivo_remessa"}

	<tr>
		<td align="center">
			<table class="tb4cantos" width="100%">
	        	<tr bgcolor="#F7F7F7">
					<td colspan="2" align="center">Movimentos do arquivo de remessa</td>
	        	</tr>
				<tr>
					<td align="right" width="25%">Filial:</td>
					<td>
						<input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
						{$info_filial.nome_filial}
					</td>
				</tr>

				<tr>
					<td colspan="2">
						<table width="95%" align="center">
							<tr>
								<th align='center'>C&oacute;d.</th>
								<th>Descri&ccedil;&atilde;o</th>
								<th>Apartamento</th>
								<th>Cliente</th>
								<th align='center'>Valor</th>
								<th align='center'>Vencimento</th>
							</tr>

							{foreach from=$movimentos_remessa item=conta}
							
				        	<tr  bgcolor = "{if $conta.index % 2 == 0}F7F7F7{else}WHITE{/if}" >
				        		<td align="center">{$conta.idmovimento}</td>
				        		<td>{$conta.descricao_movimento}</td>
				        		<td>{$conta.apartamento}</td>
				        		<td>{$conta.cliente_label}</td>
				        		<td align="right">{$conta.valor_boleto}</td>
				        		<td align="center">{$conta.data_vencimento}</td>
				        	</tr>
				        
				        	<tr>
				          		<td class="row" height="1" bgcolor="#999999" colspan="9"></td>
				        	</tr>
				      		{/foreach}
			      		</table>
			      	</td>
				</tr>
			</table>
		</td>
	</tr>

	{elseif $flags.action == "adicionar"}

	<tr>
		<td colspan="2" align="center">
			<table class="tb4cantos" width="100%">

	       		<tr bgcolor="#F7F7F7">
					<td colspan="2" align="center">Gera&ccedil;&atilde;o de arquivo de remessa</td>
	       		</tr>	
	       		<tr><td>&nbsp;</td></tr>	
	
				<tr>
					<td>

						<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_retorno" id = "for_retorno">
							<input type="hidden" name="for_chk" id="for_chk" value="1" />

						<table width="100%">
				
							{if count($movimentos_remessa)}

							<tr>
								<td align="right" width="40%">Filial:</td>
								<td>
									<input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
									{$info_filial.nome_filial}
								</td>
							</tr>

							<tr>
								<td align="right" width="40%">Banco:</td>
								<td>
									<b>{$nome_banco}</b>
								</td>
							</tr>
		
							<tr>
								<td colspan="2">
									<table width="95%" align="center">

										<tr><td colspan="7">&nbsp;</td></tr>

										<tr>
											<td colspan="7">
												Movimentos que estiverem em vermelho possuem irregularidade de dados e n&atilde;o podem ser inclu&iacute;dos na remessa at&eacute; que sejam corrigidos.
											</td>
										</tr>

										<tr><td colspan="7">&nbsp;</td></tr>

										<tr>
											<th width="10%">
												<a class='menu_item' style="color:white;" href="javascript:selecionar_todos();">Marcar todos</a>
												<br />
												<a class='menu_item' style="color:white" href="javascript:selecionar_nenhum()">Desmarcar todos</a> 
											
											</th>
											<th align='center'>C&oacute;d.</th>
											<th>Descri&ccedil;&atilde;o</th>
											<th>Apartamento</th>
											<th>Cliente</th>
											<th align='center'>Valor</th>
											<th align='center'>Vencimento</th>
										</tr>
	
										{foreach from=$movimentos_remessa item=conta}
										
							        	<tr  bgcolor = "{if $conta.erro == '1'}ff6d5a{else}WHITE{/if}" >
							        		<td align="center">
							        			{if $conta.erro == '0'}
							        			<input type="checkbox" class="selecao_conta_receber" name="movimento[{$conta.idmovimento}]" 
							        					id="id_cr_{$conta.idmovimento}"/>
							        			{else}
							        				&nbsp;
							        			{/if}
							        		</td>
							        		<td align="center">{$conta.idmovimento}</td>
 							        		<td>{$conta.descricao_movimento}</td>
 							        		<td>{$conta.apartamento}</td>
							        		<td>{$conta.cliente_label}</td>
							        		<td align="right">{$conta.valor_boleto}</td>
							        		<td align="center">{$conta.data_vencimento}</td>
							        	</tr>
							        
							        	<tr>
							          		<td class="row" height="1" bgcolor="#999999" colspan="9"></td>
							        	</tr>
							      		{/foreach}
						      		</table>
								</td>
							</tr>

							<tr><td>&nbsp;</td></tr>
		
							<tr>
								<td colspan="2" align="center">
									<input type='submit' class="botao_padrao" value="CONFIRMAR GERA&Ccedil;&Atilde;O" name = "Confirmar" id = "Confirmar" />
									<input type='submit' class="botao_padrao" value="CANCELAR" name = "Cancelar" id = "Cancelar" />
								</td>
							</tr>
		
							{else}
					
							<tr>
								<td align="right" width="40%">Filial:</td>
								<td>
									<input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
									{$info_filial.nome_filial}
								</td>
							</tr>
										
							<tr>
								<td align="right" width="40%">Data de vencimento  De: </td>
								<td colspan="" align="left">
								
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
						
									At&eacute;:
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
		
							<tr>
								<td align="right" width="40%">Data de gera&ccedil;&atilde;o De: </td>
								<td colspan="" align="left">
									
									
									<input class="short" type="text" name="data_movimento_de" id="data_movimento_de" value="{$smarty.post.data_movimento_de}" maxlength='10' onkeydown="mask('data_movimento_de', 'data')" onkeyup="mask('data_movimento_de', 'data')" />
									<img src="{$conf.addr}/common/img/calendar.png" id="img_data_movimento_de" style="cursor: pointer;" />
									<script type="text/javascript">
										Calendar.setup(
											{ldelim}
												inputField : "data_movimento_de", // ID of the input field
												ifFormat : "%d/%m/%Y", // the date format
												button : "img_data_movimento_de", // ID of the button
												align  : "cR"  // alinhamento
											{rdelim}
										);
									</script>									
														
									At&eacute;:
									<input class="short" type="text" name="data_movimento_ate" id="data_movimento_ate" value="{$smarty.post.data_movimento_ate}" maxlength='10' onkeydown="mask('data_movimento_ate', 'data')" onkeyup="mask('data_movimento_ate', 'data')" /> 
									<img src="{$conf.addr}/common/img/calendar.png" id="img_data_movimento_ate" style="cursor: pointer;" />
									<script type="text/javascript">
										Calendar.setup(
											{ldelim}
												inputField : "data_movimento_ate", // ID of the input field
												ifFormat : "%d/%m/%Y", // the date format
												button : "img_data_movimento_ate", // ID of the button
												align  : "cR"  // alinhamento
											{rdelim}
										);
									</script>	
									(dd/mm/aaaa)
					
								</td>
							</tr>
			
							<tr>
								<td align="right" class="req" width="40%">Banco:</td>
								<td>
									<select name="modo_recebimento" id="modo_recebimento" >
										{html_options values=$lista_bancos_remessa.modo_recebimento output=$lista_bancos_remessa.nome_banco selected=$smarty.post.modo_recebimento}
									</select>
								</td>
							</tr> 																
							<tr><td>&nbsp;</td></tr>
		
							<tr>
								<td colspan="2" align="center">
									<input type='submit' class="botao_padrao" value="GERAR ARQUIVO" name = "Adicionar" id = "Adicionar" />
								</td>
							</tr>
							{/if}
						</table>
						</form>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	
	{/if}

{/if}

{include file="com_rodape.tpl"}
