{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}


{if $flags.okay}

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
				Operações:
			</td>
	  	<td class="descricao_tela">
				{if $list_permissao.adicionar == '1'}
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a>
				{/if}
				{if $list_permissao.listar == '1'}
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">listar</a>
				{/if}
			</td>
		</tr>
	</table>


  {if $flags.action == "listar"}

    {if $flags.sucesso != ""}
	  	{include file="div_resultado_inicio.tpl"}
	  		{$flags.sucesso}
	  	{include file="div_resultado_fim.tpl"}
		{/if}

		<br>

  	<form  action="{$smarty.server.PHP_SELF}?ac=listar" method="post" name="for_troco" id="for_troco">
    <input type="hidden" name="for_chk" id="for_chk" value="1" />
			<table width="100%">

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">
			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca rápida do Troco</td>
			        </tr>

							<tr>
								<td align="right" width="40%">
								Filial:
								</td>
								<td>
									<input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
									{$info_filial.nome_filial}
								</td>
							</tr>

							<tr>
			        	<td align="right" class="req">
									Data:
								</td>
								<td>
			        	  <input class="short" type="text" name="data_troco" id="data_troco" value="" maxlength='10' onkeydown="mask('data_troco', 'data')" onkeyup="mask('data_troco', 'data')" /> 
									<img src="{$conf.addr}/common/img/calendar.png" id="img_data_troco" style="cursor: pointer;" /> (dd/mm/aaaa)
									<input type="button" class="botao_padrao" value="Buscar!" name="button" onClick="xajax_Verifica_Campos_Busca_Rapida_AJAX(xajax.getFormValues('for_troco'))" />
			        	</td>
			        </tr>									
							<script type="text/javascript">
								Calendar.setup(
									{ldelim}
										inputField : "data_troco", // ID of the input field
										ifFormat : "%d/%m/%Y", // the date format
										button : "img_data_troco", // ID of the button
										align  : "cR"  // alinhamento
									{rdelim}
								);
							</script>	


						</table>
					</td>
        </tr>

			</table>
		</form>


		<div id="dados_troco">
		</div>

		

	{elseif $flags.action == "editar"}
	
		<br>

		{if $flags.proibe == 1}

			{include file="div_resultado_inicio.tpl"}
	  		Apenas o troco da data atual pode ser editado!
	  	{include file="div_resultado_fim.tpl"}

		{else}
	
			<table width="100%">
				<form  action="{$smarty.server.PHP_SELF}?ac=editar&idtroco={$info.idtroco}" method="post" name = "for_troco" id = "for_troco">
				<input type="hidden" name="for_chk" id="for_chk" value="1" />
				
	
					<tr>
						<td align="right" width="40%">
						Filial:
						</td>
						<td>
							<input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
							{$info_filial.nome_filial}
						</td>
					</tr>
	
					<tr>
						<td align="right">Data:</td>
						<td>{$info.dia}</td>
					</tr>					
	
					<tr>
						<td align="right">Último funcionário responsável:</td>
						<td>{$info.nome_funcionario}</td>
					</tr>			

					
					<tr>
						<td class="req" align="right">Valor do troco (R$):</td>
						<td>
							<input class="short" type="text" name="numvalor_troco" id="numvalor_troco" value="{$info.numvalor_troco}" maxlength='10' onkeydown="FormataValor('numvalor_troco')" onkeyup="FormataValor('numvalor_troco')" />
						</td>
					</tr>
					
					<tr><td>&nbsp;</td></tr>
	
					<tr>
						<td class="req" align="right">
							Funcionário:
						</td>
						<td>
							<select name="idfuncionario" id="idfuncionario">
							<option value="">[selecione]</option>
							{html_options values=$list_funcionarios.idfuncionario output=$list_funcionarios.nome_funcionario selected=$smarty.post.idfuncionario}
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
					
	
					<tr><td>&nbsp;</td></tr>
	
					<tr>
						<td align="center" colspan="2">
							<input type='button' class="botao_padrao" value="ALTERAR" name = "ALTERAR" id = "ALTERAR"
								onClick="xajax_Verifica_Campos_Troco_AJAX(xajax.getFormValues('for_troco'));"
							/>
						</td>
					</tr>
	
				</form>
			</table>

		{/if}
	      
	      
	{elseif $flags.action == "adicionar"}

		<br>

		{if $flags.proibe == 1}

			{include file="div_resultado_inicio.tpl"}
	  		O troco de hoje já foi cadastrado!
	  	{include file="div_resultado_fim.tpl"}

		{else}
	
			<table width="100%">
				<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_troco" id = "for_troco">
				<input type="hidden" name="for_chk" id="for_chk" value="1" />
				
					<tr>
						<td align="right" width="40%">
						Filial:
						</td>
						<td>
							<input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
							{$info_filial.nome_filial}
						</td>
					</tr>
	
					<tr>
						<td align="right">Data:</td>
						<td>{$flags.data_criacao}</td>
					</tr>
	
					<tr>
						<td class="req" align="right">Valor do troco (R$):</td>
						<td>
							<input class="short" type="text" name="valor_troco" id="valor_troco" value="{$smarty.post.valor_troco}" maxlength='10' onkeydown="FormataValor('valor_troco')" onkeyup="FormataValor('valor_troco')" />
						</td>
					</tr>
					
					
					<tr><td>&nbsp;</td></tr>
	
					<tr>
						<td class="req" align="right">
							Funcionário:
						</td>
						<td>
							<select name="idfuncionario" id="idfuncionario">
							<option value="">[selecione]</option>
							{html_options values=$list_funcionarios.idfuncionario output=$list_funcionarios.nome_funcionario selected=$smarty.post.idfuncionario}
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
	
					<tr><td>&nbsp;</td></tr>
	
					<tr>
						<td colspan="2" align="center">
							<input type='button' class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar"
								onClick="xajax_Verifica_Campos_Troco_AJAX(xajax.getFormValues('for_troco'));"
							/>
						</td>
					</tr>
	
					</form>
			</table>
	
	  {/if}

  {/if}

{/if}

{include file="com_rodape.tpl"}
