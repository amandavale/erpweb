{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if $flags.okay}

<!-- 	<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>  -->
	<script type="text/javascript" src="{$conf.addr}/common/js/selecionar_todos_nenhum.js"></script>
	<script type="text/javascript" src="{$conf.addr}/common/js/jquery-1.6.1.min.js"></script>
	<script type="text/javascript" src="{$conf.addr}/common/js/movimento.js"></script>

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
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">processar arquivo de retorno</a>
				{/if}
			</td>
		</tr>
	</table>

	{include file="div_erro.tpl"}


    {if $flags.sucesso != ""}
	  	{include file="div_resultado_inicio.tpl"}
	  		{$flags.sucesso}
	  	{include file="div_resultado_fim.tpl"}
	{/if}


	{if $flags.action == "adicionar"}

	<tr>
		<td colspan="2" align="center">
			<table class="tb4cantos" width="100%">

	       		<tr bgcolor="#F7F7F7">
					<td colspan="2" align="center">Processamento de arquivo de retorno</td>
	       		</tr>	
	       		<tr><td>&nbsp;</td></tr>	
	
				<tr>
					<td>
						<table width="100%">
						<form enctype="multipart/form-data" action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_retorno" id = "for_retorno">
							<input type="hidden" name="for_chk" id="for_chk" value="1" />
				
							{if count($contas_pagas)}		

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
									<b>$nome_banco</b>
								</td>
							</tr>
		
							<tr>
								<td colspan="2">
								
									<div style="background-color:41fb41; width:14%; margin-bottom:4px">
										<input type="checkbox" id="nao_baixados" /> Mostrar n&atilde;o baixados
									</div> 
									<div style="background-color:5571ff; width:14%; margin-bottom:4px">
										<input type="checkbox" id="baixados" /> Mostrar baixados
									</div> 
									<div style="background-color:ff6d5a; width:14%; margin-bottom:4px">
										<input type="checkbox" id="nao_reconhecidos" /> Mostrar n&atilde;o reconhecidos
									</div> 
									
								</td>
								
							</tr>
		
							<tr>
								<td colspan="2">
									<table width="100%" align="center">
										<tr>
											<th>&nbsp;</th>
											<th width="10%">
												Selecionar
												<br />
												<a class='menu_item' style="color:white;" href="javascript:selecionar_todos();">Marcar todos</a>
												<br />
												<a class='menu_item' style="color:white" href="javascript:selecionar_nenhum()">Desmarcar todos</a> 
											
											</th>
											<th align='center'>Movimento</th>
											<th align='center'>Nosso n&uacute;mero</th>
											<th align='center'>Cliente origem</th>
											<th align='center'>Cliente destino</th>
											<th align='center'>Vencimento</th>
											<th align='center'>Valor<br />do movimento</th>	
											<th align='center'>Valor pago</th>
											<th align='center'>Multa</th>
											<th align='center'>Juros</th>
											<th align='center'>Desconto</th>
											<th align='center'>Tarifa<br/>do boleto</th>
											<th align='center'>Ocorr&ecirc;ncia</th>
										</tr>
	
										{foreach from=$contas_pagas item=conta}
										
										{if $conta.baixado == 1}
											{assign var="classe" value="baixados"}
										{elseif $conta.conta_existe}
											{assign var="classe" value="nao_baixados"}
										{else}
											{assign var="classe" value="nao_reconhecidos"}
										{/if}
										
							        	<tr  bgcolor = "{if $classe == baixados}5571ff{elseif $classe == nao_baixados}41fb41{else}ff6d5a{/if}" class="{$classe}">
											<td align="center">{$conta.numero}</td>
							        		<td align="center">
							        			{if $conta.conta_existe && $conta.baixado == 0}
							        			<input type="checkbox" class="selecao_movimento" name="array_movimentos[{$conta.idmovimento}]" 
							        					id="id_movimento_{$conta.idmovimento}"/>
							        			{else}
							        			&nbsp;
							        			{/if}
							        		</td>
							        		<td>{$conta.idmovimento}</td>
							        		<td>{$conta.nosso_numero}</td>
							        		<td>{$conta.nome_cliente_origem}</td>
							        		<td>{$conta.nome_cliente_destino}</td>
							        		<td align="center">{$conta.data_vencimento}</td>
							        		<td align="right">{$conta.valor}</td>
							        		<td align="right">{$conta.valor_pago_exibir}</td>
							        		<td align="right">{$conta.multa_exibir}</td>
							        		<td align="right">{$conta.juros_exibir}</td>
							        		<td align="right">{$conta.desconto_exibir}</td>
							        		<td align="right">{$conta.tarifa_boleto_exibir}</td>
							        		<td>{$conta.movimento}</td>
							        		
							        	</tr>
							        
							        	<tr class="{$classe}">
							          		<td class="row" height="1" bgcolor="#999999" colspan="14"></td>
							        	</tr>
							      		{/foreach}
						      		</table>
								</td>
							</tr>

							<tr><td>&nbsp;</td></tr>
		
							<tr>
								<td colspan="2" align="center">
									<input type='submit' class="botao_padrao" value="CONFIRMAR" name = "Confirmar" id = "Confirmar" />
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
							
							<tr><td>&nbsp;</td></tr>

							<tr>
								<td align="right" class="req" width="40%">Banco:</td>
								<td>
									<select name="banco" id="banco" >
										<option value="">[selecione]</option>
										{html_options values=$lista_bancos.id_banco output=$lista_bancos.nome_banco selected=$smarty.post.banco}
									</select>
								</td>
							</tr>
							
							<tr>
								<td class="req" align="right" width="40%">Arquivo:</td>
								<td>
									<input type="file" name="arquivo_retorno" id="arquivo_retorno" />
								</td>
							</tr>										

							<tr><td>&nbsp;</td></tr>
		
							<tr>
								<td colspan="2" align="center">
									<input type='submit' class="botao_padrao" value="VISUALIZAR PAGAMENTOS" name = "Adicionar" id = "Adicionar" />
								</td>
							</tr>
							{/if}
						</form>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	
	{/if}

{/if}

{include file="com_rodape.tpl"}
