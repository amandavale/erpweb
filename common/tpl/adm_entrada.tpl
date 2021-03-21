{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay} {include file="div_erro.tpl"} {/if}

<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/tabs.js"></script>	

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
	  	<td class="descricao_tela" WIDTH="15%">
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

	{include file="div_erro.tpl"}
	
  {if $flags.action == "listar"}

    {if $flags.sucesso != ""}
	  	{include file="div_resultado_inicio.tpl"}
	  		{$flags.sucesso}
	  	{include file="div_resultado_fim.tpl"}
		{/if}

		{if count($list)}
		
			<p align="center">Listando {$conf.area} de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p>
		
			<table width="95%" align="center">
			
				
				<tr>
					<th align='center'>Nº da Nota</th>
					<th align='center'>Nº Pedido</th>
					<th align='center'>Fornecedor</th>
					<th align='center'>Filial</th>
					<th align='center'>Data da Entrada</th>
					<th align='center'>Valor da Nota (R$)</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td align= "center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idpedido={$list[i].idpedido}"><b>{$list[i].numero_nota}</b>{if $list[i].idmotivo_cancelamento != ""}<font color='red'> (CANCELADA)</font>{/if}</a></td>
						<td align= "center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idpedido={$list[i].idpedido}"><b>{$list[i].idpedido}</b></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idpedido={$list[i].idpedido}">{$list[i].nome_fornecedor}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idpedido={$list[i].idpedido}">{$list[i].nome_filial}</a></td>
						<td align="center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idpedido={$list[i].idpedido}">{$list[i].data_entrada}</a></td>
						<td align="right"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idpedido={$list[i].idpedido}">{$list[i].valor_nota}</a></td>
					</tr>
	        
	        <tr>
	          <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
	        </tr>
	      {/section}

      </table>
      
      <p align="center" id="nav">{$nav}</p>

		{else}
      {include file="div_resultado_nenhum.tpl"}
		{/if}
		

	{elseif $flags.action == "editar"}
	
	<br>

	<div style="width: 100%;">

		{if $info.idmotivo_cancelamento}
			<table width="100%"  border="0" cellpadding="2" cellspacing="3" bgcolor="#FDF5E6" class="tb4cantos">
				<tr><td>Esta entrega está <font color='red'><b>CANCELADA</b></font></td></tr>
				<tr><td>Motivo do Cancelamento: <font color='red'><b>{$info.descricao}</b></font></td></tr>
			</table>
		{/if}
	
		<form  action="{$smarty.server.PHP_SELF}?ac=editar&idpedido={$info.idpedido}" method="post" name = "for" id = "for">
		<input type="hidden" name="for_chk" id="for_chk" value="1" />
		<input type="hidden" name="desconto" id="desconto" value="{$info.desconto}" />
		<input type="hidden" name="data" id="data" value="{$info.data_entrada}" />
		<input type="hidden" name="outras_despesas" id="outras_despesas" value="{$info.outras_despesas}" />
		<input type="hidden" name="seguro" id="seguro" value="{$info.seguro}" />
		<input type="hidden" name="frete" id="frete" value="{$info.frete}" />

		<ul class="anchors">
			<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados da Entrada</a></li>
			<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Produtos Relacionados</a></li>
			{if !$info.idmotivo_cancelamento}
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Contas a pagar</a></li>
				<li><a id="a_tab_3" onclick="Processa_Tabs(3, 'tab_')" href="javascript:;">Cancelamento de Entrada</a></li>
			{/if}
		</ul>

		{************************************}
		{* TAB 0 *}
		{************************************}

		<div id="tab_0" class="anchor">			

		 <table width="95%" align="center">			
				<tr>
		 			<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca de Fornecedor</b></td>
			        </tr>

							<tr>
								<td align="center" colspan="9">
									<br>
									<div id="dados_fornecedor" align="center">
									</div>
									<br>
								</td>
							</tr>
					</table>
					</td>
				</tr>
				
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
								<td class="tb4cantos">Criação da entrada</td>
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
					<td colspan="2" align="center">

						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Dados Gerais: <b>Nota de Entrada</b></td>
			        </tr>

							<tr>
								<td width="50%">
								  <table>

								  	<tr>
								  	  <td align="left" colspan="2">
												<b>Nº do pedido: {$info.idpedido}</b>
								  	  </td>
								  	</tr>

								  	<tr>
								  	  <td align="left" colspan="2">
												Filial: {$info.nome_filial}
								  	  </td>
								  	</tr>

										<tr>
											<td align="left" colspan="2">
												Valor da nota (R$):
												{$info.valor_nota}
											</td>
										</tr>

										<tr>
											<td align="left">
												CFOP:
												{$info.codigo_cfop}
											</td>
										</tr>
						
										<tr>
											<td align="left" colspan="2">
												Número da Nota:
												{$info.numero_nota}
											<td>
										</tr>

										<tr>
											<td align="left" colspan="2">
												Modelo da Nota:
												{$info.modelo_nota}
											</td>
										</tr>
			
										<tr>
											<td align="left" colspan="2">
												Série da Nota:
												{$info.serie_nota}
											</td>
										</tr>


									</table>
								</td>
								
								<td align="right">
									<table>

										<tr>
											<td width="50%" align="right">Data da Entrada:</td>
											<td>
												{$info.data_entrada}
											</td>
										</tr>
										
										<tr>
											<td align="right">Data da Emissão da Nota:</td>
											<td>
												{$info.data_emissao_nota}
											</td>
										</tr>

										<tr>
											<td align="right">Observações:</td>
											<td>	
												{$info.obs}
											</td>
										</tr>

									</table>
								</td>

							</tr>


						</table>

					</td>
        </tr>

			
		</table>

		</div>

			{************************************}
			{* TAB 1 *}
			{************************************}

		<div id="tab_1" class="anchor">
			
		
 			<table width="95%" align="center">
			
				<tr><td>&nbsp;</td></tr>

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Tabela de Produtos</td>
								<input type="hidden" name="total_produtos" id="total_produtos" value="0" />
			        </tr>

							<tr>
								<td align="center">

									<div id="div_produtos">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='center' width="5%">Cód.</th>
												<th align='left' width="30%">Produto</th>
												<th align='center' width="10%">Un.</th>
												<th align='center' width="10%">Qtd.</th>
												<th align='center' width="10%">Preço Unit.(R$)</th>
												<th align='center' width="10%">Total(R$)</th>
												<th align='center' width="10%">CST</th>
												<th align='center' width="10%">ICMS (%)</th>
												<th align='center' width="5%">IPI (%)</th>
											</tr>
										</table>

									</div>
								</td>
							</tr>
						</table>
						<br>
						</td>
						</tr>

						<tr>
							
							<td width="90%" align="right">
								    Desconto: R$
							</td>
							<td align="right" id="desconto">
								
								  	{$info.desconto}
						 	</td>
						</tr>
						
		</table>
<br>
						<table class="tb4cantos" width="95%" align="center">

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
									<input type="hidden" name="valor_total_produtos" id="valor_total_produtos" value="0.00" />
							    Total de Produtos: R$
								</td>
							</tr>


							<tr>
								<td align="right">
									{$info.base_calculo_icms}
								</td>

								<td align="right">
									{$info.icms}
								</td>

								<td align="right">
									{$info.base_calculo_icms_substituicao}
								</td>

								<td align="right">
									{$info.icms_substituicao}
								</td>
								
							  <td align="right" id="Sub">
							  	0,00
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
									<input type="hidden" name="valor_total_nota" id="valor_total_nota" value="0.00" />
							    Valor Total da Nota: R$
								</td>
							</tr>

							<tr>
								<td align="right">
									{$info.frete}
								</td>

								<td align="right">
									{$info.seguro}
								</td>

								<td align="right">
									{$info.outras_despesas}
								</td>

								<td align="right">
									{$info.ipi}
								</td>

							  <td align="right" id="SubTotal" class="negrito">
							  	0,00
								</td>
							</tr>


						</table>		
				
			<br>	

		</div>

			{************************************}
			{* TAB 2 *}
			{************************************}
{if !$info.idmotivo_cancelamento}
		<div id="tab_2" class="anchor">		
		
		 	<table width="95%" align="center">
			
				<tr><td>&nbsp;</td></tr>

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Tabela de Contas</td>
								<input type="hidden" name="total_contas" id="total_contas" value="0" />
			        </tr>

							<tr>
								<td align="center">

									<div id="div_contas">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='center' width="10%">Parcela</th>
												<th align='center' width="35%">Vencimento</th>
												<th align='center' width="40%">Valor (R$)</th>
											</tr>
										</table>

									</div>
								</td>
							</tr>
						</table>
						</td>
						</tr>

						<tr>
							
							<td width="90%" align="right">
								    Total: R$
								</td>
								<td align="right" id="total_contas_pagar">
								
								  	0,00
							</td>
					</tr>
			</table>
		
		</div>

			{************************************}
			{* TAB 3 *}
			{************************************}

		<div id="tab_3" class="anchor" >		
		
		<table width="95%" align="center">
				
				<tr>
					<td class="req" align="right" width="40%">Funcionário:</td>
					<td>
						<select name="idUltFuncionario" id="idUltFuncionario">
						<option value="">[selecione]</option>
						{html_options values=$list_funcionario.idfuncionario output=$list_funcionario.nome_funcionario selected=$smarty.post.idUltFuncionario}
						</select>
					</td>
				</tr>
					
				<tr>
					<td class="req" align="right">
						&nbsp;&nbsp;&nbsp;
						Senha:</td>
						<td>
						<input class="medium" type="password" name="senha_funcionario" id="senha_funcionario" maxlength="32" value=""/>
					</td>
				</tr>
		
				<tr>
					<td class="req" align="right" width="40%">Motivo Cancelamento:</td>
					<td>
						<select name="idmotivo_cancelamento" id="idmotivo_cancelamento">
						<option value="">[selecione]</option>
						{html_options values=$list_cancelamento.idmotivo_cancelamento output=$list_cancelamento.descricao selected=$smarty.post.idmotivo_cancelamento}
						</select>
					</td>
				</tr>
				
			</table>
		
		</div>

			<table width="100%">
				
        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="cancelar" type="button" class="botao_padrao" value="CANCELAR"
        			onClick="xajax_Verifica_Campos_Entrada_AJAX(xajax.getFormValues('for'));">
        	</td>
        </tr>
			</table>
{/if}		
				
		</form>

	</div>


		<script language="javascript">
				Processa_Tabs(0, 'tab_'); // seta o tab inicial
				xajax_Seleciona_Produtos_Pedidos('{$info.idpedido}');
				xajax_Seleciona_Conta_Pedido('{$info.idpedido}');
				xajax_Seleciona_Info_Fornecedor('{$info.idpedido}');
		</script>
	      
</div>	      


	{elseif $flags.action == "adicionar"}
	
	<br>

	<div style="width: 100%;">



    	<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
      <input type="hidden" name="valor_total" id="valor_total" value="0" />

      <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados da Entrada</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Produtos Relacionados</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Contas a pagar</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

		<div id="tab_0" class="anchor">			

		 <table width="95%" align="center">			
				<tr>
		 			<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca de Fornecedor</b></td>
			        </tr>

						<tr>
								<td colspan="9" class="req" align="center">
									Fornecedor:
									<input type="hidden" name="idfornecedor" id="idfornecedor" value="{$smarty.post.idfornecedor}" />
									<input type="hidden" name="idfornecedor_NomeTemp" id="idfornecedor_NomeTemp" value="{$smarty.post.idfornecedor_NomeTemp}" />
									<input class="ultralarge" type="text" name="idfornecedor_Nome" id="idfornecedor_Nome" value="{$smarty.post.idfornecedor_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idfornecedor');
										"
									/>
									<span class="nao_selecionou" id="idfornecedor_Flag">
										&nbsp;&nbsp;&nbsp;
									</span>
								</td>
							</tr>
							<script type="text/javascript">
							new CAPXOUS.AutoComplete("idfornecedor_Nome", function() {ldelim}
							return "fornecedor_ajax.php?ac=busca_fornecedor&typing=" + this.text.value + "&campoID=idfornecedor" + "&mostraDetalhes=1";
							{rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							// verifica os campos auto-complete
							VerificaMudancaCampo('idfornecedor');
							</script>
			 			<tr>
							<td align="center" colspan="9"><br>
							<div id="dados_fornecedor" align="center">
							</div><br>
							</td>
						</tr>
					</table>
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>


        <tr>
					<td colspan="2" align="center">

						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Dados Gerais: <b>Nota de Entrada</b></td>
			        </tr>

							<tr>
								<td width="50%">
								  <table>
								  	<tr>
								  	  <td align="left" colspan="2">
												Filial: {$filial}
								  	  </td>
								  	</tr>

										<tr>
											<td class="req" align="left" colspan="2">
												Valor da nota (R$):
												<input class="short" type="text" name="valor_nota" id="valor_nota" value="{$smarty.post.valor_nota}" maxlength='10' onkeydown="FormataValor('valor_nota')" onkeyup="FormataValor('valor_nota')" />
											</td>
										</tr>

										<tr>
											<td class="req" align="left">
												CFOP:
												<input class="short" type="text" name="codigo_cfop" id="codigo_cfop" value="{$smarty.post.codigo_cfop}" maxlength='4' onkeydown="FormataInteiro('codigo_cfop')" onkeyup="FormataInteiro('codigo_cfop')" onblur="xajax_Busca_Descricao_CFOP_AJAX(xajax.getFormValues('for'));" />
											</td>
											<td id="descricao_cfop"></td>
										</tr>
						
										<tr>
											<td class="req" align="left" colspan="2">
												Número da Nota:
												<input class="medium" type="text" name="numero_nota" id="numero_nota" maxlength="10" value="{$smarty.post.numero_nota}" onkeydown="FormataInteiro('numero_nota')" onkeyup="FormataInteiro('numero_nota')"/>
											<td>
										</tr>

										<tr>
											<td class="req" align="left" colspan="2">
												Modelo da Nota:
												<input class="medium" type="text" name="modelo_nota" id="modelo_nota" maxlength="10" value="{$smarty.post.modelo_nota}" />
											</td>
										</tr>
			
										<tr>
											<td class="req" align="left" colspan="2">
												Série da Nota:
												<input class="medium" type="text" name="serie_nota" id="serie_nota" maxlength="10" value="{$smarty.post.serie_nota}"/>
											</td>
										</tr>

									</table>
								</td>
								
								<td align="right">
									<table>

										<tr>
											<td class="req" align="right">Data da Entrada:</td>
											<td>
												<input class="short" type="text" name="data_entrada" id="data_entrada" value="{$smarty.post.data_entrada}" maxlength='10' onkeydown="mask('data_entrada', 'data')" onkeyup="mask('data_entrada', 'data')" />
												<img src="{$conf.addr}/common/img/calendar.png" id="img_data_entrada" style="cursor: pointer;" /> 
												(dd/mm/aaaa)
											</td>
										</tr>
										<script type="text/javascript">
											Calendar.setup(
												{ldelim}
													inputField : "data_entrada", // ID of the input field
													ifFormat : "%d/%m/%Y", // the date format
													button : "img_data_entrada", // ID of the button
													align  : "cR"  // alinhamento
												{rdelim}
											);
										</script>		
						
										
										<tr>
											<td class="req" align="right">Data da Emissão da Nota:</td>
											<td>
												<input class="short" type="text" name="data_emissao_nota" id="data_emissao_nota" value="{$smarty.post.data_emissao_nota}" maxlength='10' onkeydown="mask('data_emissao_nota', 'data')" onkeyup="mask('data_emissao_nota', 'data')" />
												<img src="{$conf.addr}/common/img/calendar.png" id="img_data_emissao_nota" style="cursor: pointer;" /> 
												(dd/mm/aaaa)
											</td>
										</tr>
										<script type="text/javascript">
											Calendar.setup(
												{ldelim}
													inputField : "data_emissao_nota", // ID of the input field
													ifFormat : "%d/%m/%Y", // the date format
													button : "img_data_emissao_nota", // ID of the button
													align  : "cR"  // alinhamento
												{rdelim}
											);
										</script>		



										<tr>
											<td align="right">Observações:</td>
											<td>	
												<textarea name="obs" id="obs" rows='3' cols='50'>{$smarty.post.obs}</textarea>
											</td>
										</tr>

									</table>
								</td>

							</tr>


						</table>

					</td>
        </tr>

	
				<tr><td>&nbsp;</td></tr>

				<tr>
					<td class="req" align="right" width="40%">Funcionário:</td>
					<td>
						<select name="idfuncionario" id="idfuncionario">
						<option value="">[selecione]</option>
						{html_options values=$list_funcionario.idfuncionario output=$list_funcionario.nome_funcionario selected=$smarty.post.idfuncionario}
						</select>
					</td>
				</tr>
					
				<tr>
					<td class="req" align="right">
					  &nbsp;&nbsp;&nbsp;
						Senha:</td>
						<td>
						<input class="medium" type="password" name="senha_funcionario" id="senha_funcionario" maxlength="32" value=""/>
					</td>
  			</tr>

		</table>

		</div>

			{************************************}
			{* TAB 1 *}
			{************************************}

		<div id="tab_1" class="anchor">
			
		
 			<table width="95%" align="center">
			
        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca dos <b>PRODUTOS</b></td>
			        </tr>

							<tr>
								<td colspan="9" align="center">
									Produto:
									<input type="hidden" name="idproduto" id="idproduto" value="{$smarty.post.idproduto}" />
									<input type="hidden" name="idproduto_NomeTemp" id="idproduto_NomeTemp" value="{$smarty.post.idproduto_NomeTemp}" />
									<input class="ultralarge" type="text" name="idproduto_Nome" id="idproduto_Nome" value="{$smarty.post.idproduto_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idproduto');
										"
									/>
									<span class="nao_selecionou" id="idproduto_Flag">
										&nbsp;&nbsp;&nbsp;
									</span>
								</td>
							</tr>

							<script type="text/javascript">
							    new CAPXOUS.AutoComplete("idproduto_Nome", function() {ldelim}
							    	return "produto_ajax.php?ac=busca_produto&typing=" + this.text.value + "&campoID=idproduto" + "&idproduto=0";
							    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							  // verifica os campos auto-complete
								VerificaMudancaCampo('idproduto');
							</script>

							<tr>
								<td colspan="9" align="center">
									Quantidade:
									<input class="short" type="text" name="qtd_produto" id="qtd_produto" value="{$smarty.post.qtd_produto}" maxlength='10' onkeydown="FormataValor('qtd_produto')" onkeyup="FormataValor('qtd_produto')" />
									
									&nbsp;&nbsp;&nbsp;Preço UN. R$:
									<input class="short" type="text" name="preco_custo_unitario" id="preco_custo_unitario" value="{$smarty.post.preco_custo_unitario}" maxlength='10' onkeydown="FormataValor('preco_custo_unitario')" onkeyup="FormataValor('preco_custo_unitario')" />
									
		  						<input type='button' class="botao_padrao" value="Inserir produto" name = "botaoInserirProduto" id = "botaoInserirProduto"
										onClick="xajax_Insere_Produto_Encartelamento_AJAX(xajax.getFormValues('for'));"
									/>
								</td>
							</tr>

						</table>
					</td>
        </tr>


				<tr><td>&nbsp;</td></tr>



        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Tabela de Produtos</td>
								<input type="hidden" name="total_produtos" id="total_produtos" value="0" />
			        </tr>

							<tr>
								<td align="center">

									<div id="div_produtos">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='center' width="5%">Cód.</th>
												<th align='left' width="30%">Produto</th>
												<th align='center' width="7%">Un.</th>
												<th align='center' width="8%">Qtd.</th>
												<th align='center' width="10%">Preço Unit.(R$)</th>
												<th align='center' width="10%">Total(R$)</th>
												<th align='center' width="5%">CST</th>
												<th align='center' width="10%">ICMS(%)</th>
												<th align='center' width="10%">IPI(%)</th>
												<th align='center' width="5%">Excluir?</th>
											</tr>
										</table>

									</div>
								</td>
							</tr>
						</table>
						</td>
						</tr>

						
						<tr>
							<td align="right" width="85%">Desconto R$</td>
							<td align="right">
								<input class="tiny" type="text" name="desconto" id="desconto" value="{$smarty.post.desconto}" maxlength='10' onkeydown="FormataValor('desconto')" onkeyup="FormataValor('desconto')" onblur="xajax_Calcula_Total_AJAX();" />
							</td>
						</tr>

		</table>
		
		<br>	
						<table class="tb4cantos" width="95%" align="center">

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
									<input type="hidden" name="valor_total_produtos" id="valor_total_produtos" value="0.00" />
							    Total de Produtos: R$
								</td>
							</tr>


							<tr>
								<td align="right">
									<input class="short" type="text" name="base_calculo_icms" id="base_calculo_icms" value="{$smarty.post.base_calculo_icms}" maxlength='10' onkeydown="FormataValor('base_calculo_icms')" onkeyup="FormataValor('base_calculo_icms')" onblur="xajax_Calcula_Total_AJAX();"/>
								</td>

								<td align="right">
									<input class="short" type="text" name="icms" id="icms" value="{$smarty.post.icms}" maxlength='10' onkeydown="FormataValor('icms')" onkeyup="FormataValor('icms')" onblur="xajax_Calcula_Total_AJAX();"/>
								</td>

								<td align="right">
									<input class="short" type="text" name="base_calculo_icms_substituicao" id="base_calculo_icms_substituicao" value="{$smarty.post.base_calculo_icms_substituicao}" maxlength='10' onkeydown="FormataValor('base_calculo_icms_substituicao')" onkeyup="FormataValor('base_calculo_icms_substituicao')" />
								</td>

								<td align="right">
									<input class="short" type="text" name="icms_substituicao" id="icms_substituicao" value="{$smarty.post.icms_substituicao}" maxlength='10' onkeydown="FormataValor('icms_substituicao')" onkeyup="FormataValor('icms_substituicao')" />
								</td>
								
							  <td align="right" id="Sub">
							  	0,00
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
									<input type="hidden" name="valor_total_nota" id="valor_total_nota" value="0.00" />
							    Valor Total da Nota: R$
								</td>
							</tr>

							<tr>
								<td align="right">
									<input class="short" type="text" name="frete" id="frete" value="{$smarty.post.frete}" maxlength='10' onkeydown="FormataValor('frete')" onkeyup="FormataValor('frete')" onblur="xajax_Calcula_Total_AJAX();"/>
								</td>

								<td align="right">
									<input class="short" type="text" name="seguro" id="seguro" value="{$smarty.post.seguro}" maxlength='10' onkeydown="FormataValor('seguro')" onkeyup="FormataValor('seguro')" onblur="xajax_Calcula_Total_AJAX();"/>
								</td>

								<td align="right">
									<input class="short" type="text" name="outras_despesas" id="outras_despesas" value="{$smarty.post.outras_despesas}" maxlength='10' onkeydown="FormataValor('outras_despesas')" onkeyup="FormataValor('outras_despesas')" onblur="xajax_Calcula_Total_AJAX();"/>
								</td>

								<td align="right">
									<input class="short" type="text" name="ipi" id="ipi" value="{$smarty.post.ipi}" maxlength='10' onkeydown="FormataValor('ipi')" onkeyup="FormataValor('ipi')" />
								</td>

							  <td align="right" id="SubTotal" class="negrito">
							  	0,00
								</td>
							</tr>


						</table>


		
		</div>

			{************************************}
			{* TAB 2 *}
			{************************************}

		<div id="tab_2" class="anchor">		
		
		 			<table width="95%" align="center">
			
        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Cadastro de Contas</td>
			        </tr>
						
							<tr>
								<td align="right" >
									Valor R$:</td>
									<td><input class="short" type="text" name="valor_conta" id="valor_conta" value="{$smarty.post.valor_conta}" maxlength='10' onkeydown="FormataValor('valor_conta')" onkeyup="FormataValor('valor_conta')" />
								</td>
							</tr>
							<tr>
								<td align="right"  width="45%">
									Data de Vencimento:
								</td>
								<td>
									<input class="short" type="text" name="data_vencimento" id="data_vencimento" value="{$smarty.post.data_vencimento}" maxlength='10' onkeydown="mask('data_vencimento', 'data')" onkeyup="mask('data_vencimento', 'data')" />
									<img src="{$conf.addr}/common/img/calendar.png" id="img_data_vencimento" style="cursor: pointer;" /> 
									(dd/mm/aaaa)
								</td>
							</tr>
							<script type="text/javascript">
								Calendar.setup(
									{ldelim}
										inputField : "data_vencimento", // ID of the input field
										ifFormat : "%d/%m/%Y", // the date format
										button : "img_data_vencimento", // ID of the button
										align  : "cR"  // alinhamento
									{rdelim}
								);
							</script>

							<tr>
								<td colspan="9" align="center"><br>
		  						<input type='button' class="botao_padrao" value="Inserir conta" name = "botaoInserirConta" id = "botaoInserirConta"
										onClick="xajax_Insere_Contas_Pagar_AJAX(xajax.getFormValues('for'));"
									/>
								</td>
							</tr>

						</table>
					</td>
        </tr>


				<tr><td>&nbsp;</td></tr>



        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Tabela de Contas</td>
								<input type="hidden" name="total_contas" id="total_contas" value="0" />
			        </tr>

							<tr>
								<td align="center">

									<div id="div_contas">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='center' width="10%">Parcela</th>
												<th align='center' width="35%">Vencimento</th>
												<th align='center' width="40%">Valor (R$)</th>
												<th align='center' width="15%">Excluir ?</th>
											</tr>
										</table>

										</div>
										</td>
									</tr>
								</table>
								</td>
							</tr>
							<tr>
							
								<td width="90%" align="right">
								    Total: R$
								</td>
								<td align="right" id="total_contas_pagar">
								
								  	0,00
								</td>
							</tr>
		</table>

		
		</div>

			<table width="100%">
				
        <tr><td>&nbsp;</td></tr>

				<tr>
          <td colspan="2" align="center">
  						<input type='button'  class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar" 
									onClick="xajax_Verifica_Campos_Entrada_AJAX(xajax.getFormValues('for'));"/>
          </td>
        </tr>

		</table>
		</form>
  </div>


		<script language="javascript">
				Processa_Tabs(0, 'tab_'); // seta o tab inicial
		</script>

	{elseif $flags.action == "gerar_pedido"}
	
	<br>

	<div style="width: 100%;">



    	<form  action="{$smarty.server.PHP_SELF}?ac=gerar_pedido&idpedido={$info.idpedido}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			<input type="hidden" name="valor_total" id="valor_total" value="0" />

      <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados da Entrada</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Produtos Relacionados</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Contas a pagar</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

		<div id="tab_0" class="anchor">			

		 <table width="95%" align="center">			
				<tr>
		 			<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca de Fornecedor</b></td>
			        </tr>

						<tr>
								<td colspan="9" class="req" align="center">
									Fornecedor:
									<input type="hidden" name="idfornecedor" id="idfornecedor" value="{$info.idfornecedor}" />
									<input type="hidden" name="idfornecedor_NomeTemp" id="idfornecedor_NomeTemp" value="{$info.idfornecedor_NomeTemp}" />
									<input class="ultralarge" type="text" name="idfornecedor_Nome" id="idfornecedor_Nome" value="{$info.idfornecedor_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idfornecedor');
										"
									/>
									<span class="nao_selecionou" id="idfornecedor_Flag">
										&nbsp;&nbsp;&nbsp;
									</span>
								</td>
							</tr>
							<script type="text/javascript">
							new CAPXOUS.AutoComplete("idfornecedor_Nome", function() {ldelim}
							return "fornecedor_ajax.php?ac=busca_fornecedor&typing=" + this.text.value + "&campoID=idfornecedor" + "&mostraDetalhes=1";
							{rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							// verifica os campos auto-complete
							VerificaMudancaCampo('idfornecedor');
							</script>
			 			<tr>
							<td align="center" colspan="9"><br>
							<div id="dados_fornecedor" align="center">
							</div><br>
							</td>
						</tr>
					</table>
					</td>
				</tr>
				
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
								<td class="tb4cantos">Criação da entrada</td>
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
					<td colspan="2" align="center">

						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Dados Gerais: <b>Nota de Entrada</b></td>
			        </tr>

							<tr>
								<td width="50%">
								  <table>
								  	<tr>
								  	  <td align="left" colspan="2">
												Filial: {$info.filial_nome}
								  	  </td>
								  	</tr>

										<tr>
											<td class="req" align="left" colspan="2">
												Valor da nota (R$):
												<input class="short" type="text" name="numvalor_nota" id="numvalor_nota" value="{$info.numvalor_nota}" maxlength='10' onkeydown="FormataValor('numvalor_nota')" onkeyup="FormataValor('numvalor_nota')" />
											</td>
										</tr>

										<tr>
											<td class="req" align="left">
												CFOP:
												<input class="short" type="text" name="codigo_cfop" id="codigo_cfop" value="{$smarty.post.codigo_cfop}" maxlength='4' onkeydown="FormataInteiro('codigo_cfop')" onkeyup="FormataInteiro('codigo_cfop')" onblur="xajax_Busca_Descricao_CFOP_AJAX(xajax.getFormValues('for'));" />
											</td>
											<td id="descricao_cfop"></td>
										</tr>
						
										<tr>
											<td class="req" align="left" colspan="2">
												Número da Nota:
												<input class="medium" type="text" name="litnumero_nota" id="numero_nota" maxlength="10" value="{$info.numero_nota}" onkeydown="FormataInteiro('litnumero_nota')" onkeyup="FormataInteiro('litnumero_nota')"/>
											<td>
										</tr>

										<tr>
											<td class="req" align="left" colspan="2">
												Modelo da Nota:
												<input class="medium" type="text" name="litmodelo_nota" id="litmodelo_nota" maxlength="10" value="{$info.litmodelo_nota}"/>
											</td>
										</tr>

										<tr>
											<td class="req" align="left" colspan="2">
												Série da Nota:
												<input class="medium" type="text" name="litserie_nota" id="litserie_nota" maxlength="10" value="{$info.litserie_nota}"/>
											</td>
										</tr>

									</table>
								</td>
								
								<td align="right">
									<table>

										<tr>
											<td class="req" align="right">Data da Entrada:</td>
											<td>
												<input class="short" type="text" name="litdata_entrada" id="litdata_entrada" value="{$info.litdata_entrada}" maxlength='10' onkeydown="mask('litdata_entrada', 'data')" onkeyup="mask('litdata_entrada', 'data')" />
												<img src="{$conf.addr}/common/img/calendar.png" id="img_litdata_entrada" style="cursor: pointer;" /> 
												(dd/mm/aaaa)
											</td>
										</tr>
										<script type="text/javascript">
											Calendar.setup(
												{ldelim}
													inputField : "litdata_entrada", // ID of the input field
													ifFormat : "%d/%m/%Y", // the date format
													button : "img_litdata_entrada", // ID of the button
													align  : "cR"  // alinhamento
												{rdelim}
											);
										</script>

										
										<tr>
											<td class="req" align="right">Data da Emissão da Nota:</td>
											<td>
												<input class="short" type="text" name="litdata_emissao_nota" id="litdata_emissao_nota" value="{$info.litdata_emissao_nota}" maxlength='10' onkeydown="mask('litdata_emissao_nota', 'data')" onkeyup="mask('litdata_emissao_nota', 'data')" />
												<img src="{$conf.addr}/common/img/calendar.png" id="img_litdata_emissao_nota" style="cursor: pointer;" /> 
												(dd/mm/aaaa)
											</td>
										</tr>
										<script type="text/javascript">
											Calendar.setup(
												{ldelim}
													inputField : "litdata_emissao_nota", // ID of the input field
													ifFormat : "%d/%m/%Y", // the date format
													button : "img_litdata_emissao_nota", // ID of the button
													align  : "cR"  // alinhamento
												{rdelim}
											);
										</script>


										<tr>
											<td align="right">Observações:</td>
											<td>	
												<textarea name="litobs" id="litobs" rows='3' cols='50'>{$info.litobs}</textarea>
											</td>
										</tr>

									</table>
								</td>

							</tr>


						</table>

					</td>
        </tr>

	
				<tr><td>&nbsp;</td></tr>


				<tr>
					<td class="req" align="right" width="40%">Funcionário:</td>
					<td>
						<select name="idfuncionario" id="idfuncionario">
						<option value="">[selecione]</option>
						{html_options values=$list_funcionario.idfuncionario output=$list_funcionario.nome_funcionario selected=$smarty.post.idfuncionario}
						</select>
					</td>
				</tr>
					
				<tr>
					<td class="req" align="right">
					  &nbsp;&nbsp;&nbsp;
						Senha:</td>
						<td>
						<input class="medium" type="password" name="senha_funcionario" id="senha_funcionario" maxlength="32" value=""/>
					</td>
  			</tr>

		</table>

		</div>

			{************************************}
			{* TAB 1 *}
			{************************************}

		<div id="tab_1" class="anchor">
			
		
 			<table width="95%" align="center">
			
        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Busca dos <b>PRODUTOS</b></td>
			        </tr>

							<tr>
								<td colspan="9" align="center">
									Produto:
									<input type="hidden" name="idproduto" id="idproduto" value="{$info.idproduto}" />
									<input type="hidden" name="idproduto_NomeTemp" id="idproduto_NomeTemp" value="{$info.idproduto_NomeTemp}" />
									<input class="ultralarge" type="text" name="idproduto_Nome" id="idproduto_Nome" value="{$info.idproduto_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idproduto');
										"
									/>
									<span class="nao_selecionou" id="idproduto_Flag">
										&nbsp;&nbsp;&nbsp;
									</span>
								</td>
							</tr>

							<script type="text/javascript">
							    new CAPXOUS.AutoComplete("idproduto_Nome", function() {ldelim}
							    	return "produto_ajax.php?ac=busca_produto&typing=" + this.text.value + "&campoID=idproduto" + "&idproduto=0";
							    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							  // verifica os campos auto-complete
								VerificaMudancaCampo('idproduto');
							</script>

							<tr>
								<td colspan="9" align="center">
									Quantidade:
									<input class="short" type="text" name="qtd_produto" id="qtd_produto" value="{$info.qtd_produto}" maxlength='10' onkeydown="FormataValor('qtd_produto')" onkeyup="FormataValor('qtd_produto')" />
									
									&nbsp;&nbsp;&nbsp;Preço UN. R$:
									<input class="short" type="text" name="preco_custo_unitario" id="preco_custo_unitario" value="{$info.preco_custo_unitario}" maxlength='10' onkeydown="FormataValor('preco_custo_unitario')" onkeyup="FormataValor('preco_custo_unitario')" />
									
		  						<input type='button' class="botao_padrao" value="Inserir produto" name = "botaoInserirProduto" id = "botaoInserirProduto"
										onClick="xajax_Insere_Produto_Encartelamento_AJAX(xajax.getFormValues('for'));"
									/>
								</td>
							</tr>

						</table>
					</td>
        </tr>


				<tr><td>&nbsp;</td></tr>



        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Tabela de Produtos</td>
								<input type="hidden" name="total_produtos" id="total_produtos" value="0" />
			        </tr>

							<tr>
								<td align="center">

									<div id="div_produtos">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='center' width="5%">Cód.</th>
												<th align='left' width="30%">Produto</th>
												<th align='center' width="7%">Un.</th>
												<th align='center' width="8%">Qtd.</th>
												<th align='center' width="10%">Preço Unit.(R$)</th>
												<th align='center' width="10%">Total(R$)</th>
												<th align='center' width="5%">CST</th>
												<th align='center' width="10%">ICMS(%)</th>
												<th align='center' width="10%">IPI(%)</th>
												<th align='center' width="5%">Excluir?</th>
											</tr>
										</table>

									</div>
								</td>
							</tr>
						</table>
	<br>	
						</td>
						</tr>

				
						<tr>
							<td align="right" width="85%">Desconto R$</td>
							<td align="right">
								<input class="tiny" type="text" name="desconto" id="desconto" value="{$info.desconto}" maxlength='10' onkeydown="FormataValor('desconto')" onkeyup="FormataValor('desconto')" onblur="xajax_Calcula_Total_AJAX();" />
							</td>
						</tr>
					
		</table>

<br>
						<table class="tb4cantos" align="center" width="95%">

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
									<input type="hidden" name="valor_total_produtos" id="valor_total_produtos" value="0.00" />
							    Total de Produtos: R$
								</td>
							</tr>


							<tr>
								<td align="right">
									<input class="short" type="text" name="numbase_calculo_icms" id="numbase_calculo_icms" value="{$info.numbase_calculo_icms}" maxlength='10' onkeydown="FormataValor('numbase_calculo_icms')" onkeyup="FormataValor('numbase_calculo_icms')" onblur="xajax_Calcula_Total_AJAX();"/>
								</td>

								<td align="right">
									<input class="short" type="text" name="numicms" id="numicms" value="{$info.numicms}" maxlength='10' onkeydown="FormataValor('numicms')" onkeyup="FormataValor('numicms')" onblur="xajax_Calcula_Total_AJAX();"/>
								</td>

								<td align="right">
									<input class="short" type="text" name="numbase_calculo_icms_substituicao" id="numbase_calculo_icms_substituicao" value="{$info.numbase_calculo_icms_substituicao}" maxlength='10' onkeydown="FormataValor('numbase_calculo_icms_substituicao')" onkeyup="FormataValor('numbase_calculo_icms_substituicao')" onblur="xajax_Calcula_Total_AJAX();"/>
								</td>

								<td align="right">
									<input class="short" type="text" name="numicms_substituicao" id="numicms_substituicao" value="{$info.numicms_substituicao}" maxlength='10' onkeydown="FormataValor('numicms_substituicao')" onkeyup="FormataValor('numicms_substituicao')" onblur="xajax_Calcula_Total_AJAX();"/>
								</td>
								
							  <td align="right" id="Sub">
							  	0,00
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
									<input type="hidden" name="valor_total_nota" id="valor_total_nota" value="0.00" />
							    Valor Total da Nota: R$
								</td>
							</tr>

							<tr>
								<td align="right">
									<input class="short" type="text" name="numfrete" id="numfrete" value="{$info.numfrete}" maxlength='10' onkeydown="FormataValor('numfrete')" onkeyup="FormataValor('numfrete')" onblur="xajax_Calcula_Total_AJAX();"/>
								</td>

								<td align="right">
									<input class="short" type="text" name="numseguro" id="numseguro" value="{$info.numseguro}" maxlength='10' onkeydown="FormataValor('numseguro')" onkeyup="FormataValor('numseguro')" onblur="xajax_Calcula_Total_AJAX();"/>
								</td>

								<td align="right">
									<input class="short" type="text" name="numoutras_despesas" id="numoutras_despesas" value="{$info.numoutras_despesas}" maxlength='10' onkeydown="FormataValor('numoutras_despesas')" onkeyup="FormataValor('numoutras_despesas')" onblur="xajax_Calcula_Total_AJAX();"/>
								</td>

								<td align="right">
									<input class="short" type="text" name="numipi" id="numipi" value="{$info.numipi}" maxlength='10' onkeydown="FormataValor('numipi')" onkeyup="FormataValor('numipi')" onblur="xajax_Calcula_Total_AJAX();"/>
								</td>

							  <td align="right" id="SubTotal" class="negrito">
							  	0,00
								</td>
							</tr>


				</table>

<br>	

			
	</div>

			{************************************}
			{* TAB 2 *}
			{************************************}

		<div id="tab_2" class="anchor">		
		
		 <table width="95%" align="center">
			
        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Cadastro de Contas</td>
			        </tr>
						
							<tr>
								<td align="right" >
									Valor R$:</td>
									<td><input class="short" type="text" name="valor_conta" id="valor_conta" value="{$info.valor_conta}" maxlength='10' onkeydown="FormataValor('valor_conta')" onkeyup="FormataValor('valor_conta')" />
								</td>
							</tr>
							<tr>
								<td align="right"  width="45%">
									Data de Vencimento:
								</td>
								<td>
									<input class="short" type="text" name="data_vencimento" id="data_vencimento" value="{$info.data_vencimento}" maxlength='10' onkeydown="mask('data_vencimento', 'data')" onkeyup="mask('data_vencimento', 'data')" />
									<img src="{$conf.addr}/common/img/calendar.png" id="img_data_vencimento" style="cursor: pointer;" /> 
									(dd/mm/aaaa)
								</td>
							</tr>
							<script type="text/javascript">
								Calendar.setup(
									{ldelim}
										inputField : "data_vencimento", // ID of the input field
										ifFormat : "%d/%m/%Y", // the date format
										button : "img_data_vencimento", // ID of the button
										align  : "cR"  // alinhamento
									{rdelim}
								);
							</script>		

							<tr>
								<td colspan="9" align="center"><br>
		  						<input type='button' class="botao_padrao" value="Inserir conta" name = "botaoInserirConta" id = "botaoInserirConta"
										onClick="xajax_Insere_Contas_Pagar_AJAX(xajax.getFormValues('for'));"
									/>
								</td>
							</tr>

						</table>
					</td>
        </tr>


				<tr><td>&nbsp;</td></tr>



        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Tabela de Contas</td>
								<input type="hidden" name="total_contas" id="total_contas" value="0" />
			        </tr>

							<tr>
								<td align="center">

									<div id="div_contas">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='center' width="10%">Parcela</th>
												<th align='center' width="35%">Vencimento</th>
												<th align='center' width="40%">Valor (R$)</th>
												<th align='center' width="15%">Excluir ?</th>
											</tr>
										</table>

									</div>
								</td>
							</tr>
						</table>
						</td>
						</tr>

						<tr>
							
						<td width="90%" align="right">
								    Total: R$
								</td>
								<td align="right" id="total_contas_pagar">
								
								  	0,00
								</td>
							</tr>
		</table>

		
		</div>


				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="button" class="botao_padrao" value="GERAR"
							onClick="xajax_Verifica_Campos_Entrada_AJAX(xajax.getFormValues('for'));"/>
        	</td>
        </tr>
			</table>
		
		</form>

	</div>




		<script language="javascript">
				Processa_Tabs(0, 'tab_'); // seta o tab inicial
				xajax_Seleciona_Produtos_Pedidos('{$info.idpedido}');
		</script>

		
  {/if}

{/if}

{include file="com_rodape.tpl"}