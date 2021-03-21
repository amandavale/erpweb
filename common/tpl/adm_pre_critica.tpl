{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if $flags.okay}

<!-- 	<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>  -->
	<script type="text/javascript" src="{$conf.addr}/common/js/selecionar_todos_nenhum.js"></script>
	<script type="text/javascript" src="{$conf.addr}/common/js/jquery-1.6.1.min.js"></script>
<!--	<script type="text/javascript" src="{$conf.addr}/common/js/movimento.js"></script> -->

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
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">processar arquivo de pr&eacute;-cr&iacute;tica</a>
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

<!--
	       		<tr bgcolor="#F7F7F7">
					<td colspan="2" align="center">Processamento de arquivo de pr&eacute;-cr&iacute;tica</td>
	       		</tr>	
	       		<tr><td>&nbsp;</td></tr>
-->	       		
	
				<tr>
					<td>
						<table width="100%">
						<form enctype="multipart/form-data" action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_retorno" id = "for_retorno">
							<input type="hidden" name="for_chk" id="for_chk" value="1" />
				
							{if count($resultado)}

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
		
							{if $mostra_movimentos == true}
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
											<th align='center'>Descri&ccedil;&atilde;o</th>
											<th align='center'>Ocorr&ecirc;ncia</th>
											<th align='center'>Arquivo <br />de remessa</th>
										</tr>
	
										{foreach from=$resultado key=idmovimento item=conta}

							        	<tr  bgcolor = "{if $conta.index % 2 == 0}F7F7F7{else}WHITE{/if}" >
											<td align="center">{$conta.index}</td>
							        		<td align="center">
							        			<input type="checkbox" class="selecao_movimento" name="array_movimentos[{$conta.movimento}]" id="id_movimento_{$conta.movimento}"/>
							        		</td>							        		
							        		<td>
							        			<a class='menu_item' target="_blank" href = "{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$conta.movimento}">{$conta.movimento}</a>
											</td>
							        		<td>
							        			<a class='menu_item' target="_blank" href = "{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$conta.movimento}">{$conta.descricao_movimento}</a>
							        		</td>
							        		<td>
							        			<a class='menu_item' target="_blank" href = "{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$conta.movimento}">{$conta.mensagem}</a>
							        		</td>
							        		<td align="center">
							        			<a class='menu_item' target="_blank" href = "{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$conta.movimento}">{$conta.idarquivo_remessa}</a>
							        		</td>
							        	</tr>
							        
							        	<tr>
							          		<td class="row" height="1" bgcolor="#999999" colspan="14"></td>
							        	</tr>
							      		{/foreach}
						      		</table>
								</td>
							</tr>

							<tr><td>&nbsp;</td></tr>
		
							<tr>
								<td colspan="2" align="center">
									<input type='submit' class="botao_padrao" value="DESASSOCIAR" name = "Confirmar" id = "Confirmar" />
									<input type='submit' class="botao_padrao" value="CANCELAR" name = "Cancelar" id = "Cancelar" />
								</td>
							</tr>
							{else}
							<tr>
								<td colspan="2">
	  								{include file="div_resultado_inicio.tpl"}
										{$resultado[0].mensagem[0]}
	  								{include file="div_resultado_fim.tpl"}
								</td>
							</tr>

							{/if}
		
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
										{html_options values=$lista_bancos.id_banco output=$lista_bancos.nome_banco selected=$smarty.post.banco}
									</select>
								</td>
							</tr>
							
							<tr>
								<td class="req" align="right" width="40%">Arquivo:</td>
								<td>
									<input type="file" name="arquivo_pre_critica" id="arquivo_pre_critica" />
								</td>
							</tr>										

							<tr><td>&nbsp;</td></tr>
		
							<tr>
								<td colspan="2" align="center">
									<input type='submit' class="botao_padrao" value="ANALISAR" name = "Adicionar" id = "Adicionar" />
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
