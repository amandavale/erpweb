	{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay} {include file="div_erro.tpl"} {/if}

{if $flags.okay}

<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/tabs.js"></script>	


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
	  	<td class="descricao_tela" WIDTH="20%">
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

		
	<br>	
<form  action="{$smarty.server.PHP_SELF}?ac=listar" method="post" name = "for" id = "for">
	<div id="tab_0" class="">			
		
		<table width="100%">	
			
			<tr>
				<td colspan="2">

					<table width="40%"  width="35%" align="center" >
						<tr>
							<td colspan="2" align="center">
								<table class="tb4cantos" width="100%">
									<tr bgcolor="#F7F7F7">
										<td colspan="9" align="center">Busca rápida de Nota Fiscal</td>
									</tr>
									<tr>
										<td>
											<table cellpadding="3" width="100%">
													<tr>
														<td align="center" colspan="3" class="req">
															Código da Nota:
															<input class="medium" type="text" name="nota" id="nota" value="{$smarty.post.nota}" maxlength='20' onkeydown="FormataInteiro('nota')" onkeyup="FormataInteiro('nota')" />
														</td>
													</tr>
													<tr>
														<td align="center" ><input checked class="radio" type="radio" name="tipo" id="tipo" value="NF" /> Nota Fiscal</td>
														<td align="center" ><input class="radio" type="radio" name="tipo" id="tipo" value="SD" /> Série D </td>
													</tr>
													<tr>
													<td align="center" colspan = "3"><input type="button" class="botao_padrao" value="Buscar!" name="button" onClick="xajax_Busca_Rapida_AJAX(xajax.getFormValues('for'))" />
													</td>
												</tr>		
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>

				</td>
			</tr>

			<tr>
				<td>
					<br>
					<div id="info_nota" name="info_nota">
					</div>
				</td>
			</tr>

			<tr>
				<td>
					<br>
					<div id="info_orcamento" name="info_orcamento">
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<br>
					<div id="info_produto" name="info_produto">
					</div>
				</td>
			</tr>

		</table>
	
	</div>
</form>		
	

	{elseif $flags.action == "editar"}
	
<br>

{if $info.litrealizada == "N"}

<div style="width: 100%;">

    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&identrega={$info.identrega}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			<input type="hidden" name="valido" id="valido" value="0" />
		
			<ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados da Nota</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Produtos Relacionados</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Dados da Entrega</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

	<div id="tab_0" class="anchor">			

		<br>
			<div id="info_orcamento" name="info_orcamento">
			</div>

	</div>
	
			{************************************}
			{* TAB 1 *}
			{************************************}

	<div id="tab_1" class="anchor">

		<div id="produtos" name="produtos">
			 Por favor, selecione um orçamento válido.	
		</div>

	</div>

			{************************************}
			{* TAB 2 *}
			{************************************}

	<div id="tab_2" class="anchor">

		<table width="100%">

				<tr>
					<td align="right">Funcionário que criou:</td>
					<td>	{$info.nome_funcionario}
					</td>
				</tr>	

				<tr>
					<td align="right">Data da criação:</td>
					<td>	{$info.datahoraCriacao}
					</td>
				</tr>		
			
				<tr>
					<td colspan="9" align="center">
						Transportador:
						<input type="hidden" name="idtransportador_Tipo" id="idtransportador_Tipo" value="" />
						<input type="hidden" name="idtransportador" id="idtransportador" value="{$info.idtransportador}" />
						<input type="hidden" name="idtransportador_NomeTemp" id="idtransportador_NomeTemp" value="{$info.idtransportador_NomeTemp}" />
						<input class="large" type="text" name="idtransportador_Nome" id="idtransportador_Nome" value="{$info.idtransportador_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('idtransportador');
							"
						/>
						<span class="nao_selecionou" id="idtransportador_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<tr>
					<td colspan="9" align="center">
					  <div id="dados_transportador">
						</div>
					</td>
				</tr>

				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("idtransportador_Nome", function() {ldelim}
				    	return "transportador_ajax.php?ac=busca_transportador&typing=" + this.text.value + "&campoID=idtransportador" + "&mostraDetalhes=1";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>
	
			<script type="text/javascript">
				// verifica os campos auto-complete
				VerificaMudancaCampo('idtransportador');

				</script>

				
				<tr>
					<td class="req" align="right">Programada para:</td>
					<td>
						<input class="short" type="text" name="litdataMarcada" id="litdataMarcada" value="{$info.litdataMarcada}" maxlength='10' onkeydown="mask('litdataMarcada', 'data')" onkeyup="mask('litdataMarcada', 'data')" /> (dd/mm/aaaa)
					</td>
				</tr>
				
				<tr>
					<td align="right">Entrega Realizada?</td>
					<td>
						<input {if $info.litrealizada=="S"}checked{/if} class="radio" type="radio" name="litrealizada" id="litrealizada" value="S" />Sim
						<input {if $info.litrealizada=="N"}checked{/if} class="radio" type="radio" name="litrealizada" id="litrealizada" value="N" />Não
					</td>
				</tr>
		
				<tr>
					<td align="right">Realizada em:</td>
					<td>
						<input class="short" type="text" name="litdataRealizada" id="litdataRealizada" value="{$info.litdataRealizada}" maxlength='10' onkeydown="mask('litdataRealizada', 'data')" onkeyup="mask('litdataRealizada', 'data')" /> (dd/mm/aaaa)
					</td>
				</tr>

				<tr>
					<td align="right">Observações:</td>
					<td>
						<textarea name="litobs" id="litobs" rows='6' cols='38'>{$info.litobs}</textarea>
					</td>
				</tr>
				
				<tr>
					<td align="right">Placa do Veículo:</td>
					<td><input class="medium" type="text" name="litplaca" id="litplaca" maxlength="7" value="{$info.litplaca}"/></td>
				</tr>

				<tr><td ></td></tr>
				<tr><td ></td></tr>

				<tr>
					<td class="req" align="right" width="40%">Funcionário:</td>
					<td>
						<select name="numidUltFuncionario" id="numidUltFuncionario">
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




				
			<table align="center">
				<tr><td>&nbsp;</td></tr>

				<tr>
					<td align="center">
						<input name="Imprimir" type="button" class="botao_padrao" value="Imprimir Comprovante"
							onClick="javascript: window.open('{$smarty.server.PHP_SELF}?ac=editar&identrega={$info.identrega}&imprimir=1');"
						/>
					</td>
				</tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="button" class="botao_padrao" value="ALTERAR" onClick="xajax_Verifica_Campos_Entrega_AJAX(xajax.getFormValues('for'))">
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&identrega={$info.identrega}','ATENÇÃO! Confirma a exclusão ?'))" >
        	</td>
        </tr>


		</table>
	</form>

</div>
			<script language="javascript">
			Processa_Tabs(0, 'tab_'); // seta o tab inicial
			xajax_Seleciona_Produtos_Orcamento('{$info.identrega}');
			xajax_Seleciona_Info_Transportador('{$info.idtransportador}');
			</script>


{/if}
{if $info.litrealizada == "S"}      

{if $info.idmotivo_cancelamento}

<div style="width: 100%;">

<table width="100%"  border="0" cellpadding="2" cellspacing="3" bgcolor="#FDF5E6" class="tb4cantos">
	<tr><td>Esta entrega está <font color='red'><b>CANCELADA</b></font></td></tr>
	<tr><td>Motivo do Cancelamento: <font color='red'><b>{$info.descricao}</b></font></td></tr>
</table>

<br>

    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&identrega={$info.identrega}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			<input type="hidden" name="valido" id="valido" value="0" />
			
		
			<ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados da Nota</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Produtos Relacionados</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Dados da Entrega</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

	<div id="tab_0" class="anchor">		

		<br>
			<div id="info_orcamento" name="info_orcamento">
			</div>

	</div>
	
			{************************************}
			{* TAB 1 *}
			{************************************}

	<div id="tab_1" class="anchor">

		<div id="produtos" name="produtos">
			 Por favor, selecione um orçamento válido.	
		</div>

	</div>

			{************************************}
			{* TAB 2 *}
			{************************************}

	<div id="tab_2" class="anchor">

		<table width="100%">

				<tr>
					<td align="right">Funcionário que criou:</td>
					<td>	{$info.nome_funcionario}
					</td>
				</tr>	
		
				<tr>
					<td align="right">Último funcionário que alterou:</td>
					<td>	{$info.nome_ultfuncionario}
					</td>
				</tr>	

				<tr>
					<td align="right">Data da criação:</td>
					<td>	{$info.datahoraCriacao}
					</td>
				</tr>		

				<tr>
					<td align="right">Data da ultima alteracao:</td>
					<td>	{$info.datahoraUltAlteracao}
					</td>
				</tr>			
				
				<tr>
					<td colspan="9" align="center">
						Transportador:
						<input type="hidden" name="idtransportador_Tipo" id="idtransportador_Tipo" value="" />
						<input type="hidden" name="idtransportador" id="idtransportador" value="{$info.idtransportador}" />
						<input type="hidden" name="idtransportador_NomeTemp" id="idtransportador_NomeTemp" value="{$info.idtransportador_NomeTemp}" />
						<input class="large" type="text" readonly name="idtransportador_Nome" id="idtransportador_Nome" value="{$info.idtransportador_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('idtransportador');
							"
						/>
						<span class="nao_selecionou" id="idtransportador_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<tr>
					<td colspan="9" align="center">
					  <div id="dados_transportador">
						</div>
					</td>
				</tr>

				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("idtransportador_Nome", function() {ldelim}
				    	return "transportador_ajax.php?ac=busca_transportador&typing=" + this.text.value + "&campoID=idtransportador" + "&mostraDetalhes=1";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>
	
			<script type="text/javascript">
				// verifica os campos auto-complete
				VerificaMudancaCampo('idtransportador');

				</script>

				
				<tr>
					<td class="req" align="right">Programada para:</td>
					<td>
						{$info.litdataMarcada}
					</td>
				</tr>
				
				<tr>
					<td align="right">Entrega Realizada</td>
				</tr>
		
				<tr>
					<td align="right">Realizada em:</td>
					<td>
						{$info.litdataRealizada}
					</td>
				</tr>

				<tr>
					<td align="right">Observações:</td>
					<td>
						<textarea name="litobs" id="litobs" rows='6' cols='38' readonly>{$info.litobs}</textarea>
					</td>
				</tr>
				
				<tr>
					<td align="right">Placa do Veículo:</td>
					<td>{$info.litplaca}</td>
				</tr>

		</table>
				
	</div>

</form>

</div>
			<script language="javascript">
			Processa_Tabs(0, 'tab_'); // seta o tab inicial
			xajax_Seleciona_Produtos_Orcamento('{$info.identrega}','{$info.realizada}');
			xajax_Seleciona_Info_Transportador('{$info.idtransportador}');
			</script>

{elseif !$info.idmotivo_cancelamento}


<div style="width: 100%;">

    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&identrega={$info.identrega}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			<input type="hidden" name="valido" id="valido" value="0" />
			<input type="hidden" name="confirmada" id="confirmada" value="1" />

		
			<ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados da Nota</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Produtos Relacionados</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Dados da Entrega</a></li>
				<li><a id="a_tab_3" onclick="Processa_Tabs(3, 'tab_')" href="javascript:;">Cancelar Entrega</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

	<div id="tab_0" class="anchor">			

		<br>
			<div id="info_orcamento" name="info_orcamento">
			</div>

	</div>
	
			{************************************}
			{* TAB 1 *}
			{************************************}

	<div id="tab_1" class="anchor">

		<div id="produtos" name="produtos">
			 Por favor, selecione um orçamento válido.	
		</div>

	</div>

			{************************************}
			{* TAB 2 *}
			{************************************}

	<div id="tab_2" class="anchor">

		<table width="100%">

				<tr>
					<td align="right">Funcionário que criou:</td>
					<td>	{$info.nome_funcionario}
					</td>
				</tr>	
		
				<tr>
					<td align="right">Último funcionário que alterou:</td>
					<td>	{$info.nome_ultfuncionario}
					</td>
				</tr>		

				<tr>
					<td align="right">Data da criação:</td>
					<td>	{$info.datahoraCriacao}
					</td>
				</tr>		

				<tr>
					<td align="right">Data da ultima alteracao:</td>
					<td>	{$info.datahoraUltAlteracao}
					</td>
				</tr>	
				
				<tr>
					<td colspan="9" align="center">
						Transportador:
						<input type="hidden" name="idtransportador_Tipo" id="idtransportador_Tipo" value="" />
						<input type="hidden" name="idtransportador" id="idtransportador" value="{$info.idtransportador}" />
						<input type="hidden" name="idtransportador_NomeTemp" id="idtransportador_NomeTemp" value="{$info.idtransportador_NomeTemp}" />
						<input class="large" type="text" readonly name="idtransportador_Nome" id="idtransportador_Nome" value="{$info.idtransportador_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('idtransportador');
							"
						/>
						<span class="nao_selecionou" id="idtransportador_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<tr>
					<td colspan="9" align="center">
					  <div id="dados_transportador">
						</div>
					</td>
				</tr>

				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("idtransportador_Nome", function() {ldelim}
				    	return "transportador_ajax.php?ac=busca_transportador&typing=" + this.text.value + "&campoID=idtransportador" + "&mostraDetalhes=1";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>
	
			<script type="text/javascript">
				// verifica os campos auto-complete
				VerificaMudancaCampo('idtransportador');

				</script>

				
				<tr>
					<td class="req" align="right">Programada para:</td>
					<td>
						{$info.litdataMarcada}
					</td>
				</tr>
				
				<tr>
					<td align="right">Entrega Realizada</td>
				</tr>
		
				<tr>
					<td align="right">Realizada em:</td>
					<td>
						{$info.litdataRealizada}
					</td>
				</tr>

				<tr>
					<td align="right">Observações:</td>
					<td>
						<textarea name="litobs" id="litobs" rows='6' cols='38' readonly>{$info.litobs}</textarea>
					</td>
				</tr>
				
				<tr>
					<td align="right">Placa do Veículo:</td>
					<td>{$info.litplaca}</td>
				</tr>

		</table>
				
	</div>

			{************************************}
			{* TAB 3 *}
			{************************************}

	<div id="tab_3" class="anchor">

		<table width="100%">

				<tr>
					<td class="req" align="right" width="40%">Funcionário:</td>
					<td>
						<select name="numidUltFuncionario" id="numidUltFuncionario">
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

				<tr>
					<td class="req" align="right" width="40%">Motivo Cancelamento:</td>
					<td>
						<select name="numidmotivo_cancelamento" id="numidmotivo_cancelamento">
						<option value="">[selecione]</option>
						{html_options values=$list_cancelamento.idmotivo_cancelamento output=$list_cancelamento.descricao selected=$smarty.post.idmotivo_cancelamento}
						</select>
					</td>
				</tr>


		</table>

				


	</div>

			<table align="center">
				<tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
						<input name="Submit" type="button" class="botao_padrao" value="CANCELAR" onClick="xajax_Verifica_Campos_Entrega_AJAX(xajax.getFormValues('for'))">
        	</td>
        </tr>

		</table>

	</form>

</div>
			<script language="javascript">
			Processa_Tabs(0, 'tab_'); // seta o tab inicial
			xajax_Seleciona_Produtos_Orcamento('{$info.identrega}','{$info.realizada}');
			xajax_Seleciona_Info_Transportador('{$info.idtransportador}');
			</script>

{/if}



{/if}
	      
	{elseif $flags.action == "adicionar"}

<br>

<div style="width: 100%;">

    	<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			<input type="hidden" name="valido" id="valido" value="0" />
			<input type="hidden" name="idorcamento" id="idorcamento" value="" />
					
			<ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados da Nota</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Produtos Relacionados</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Dados da Entrega</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

	<div id="tab_0" class="anchor">			

		<table width="100%">	
			
			<tr>
				<td colspan="2">

					<table width="40%"  width="35%" align="center" >
						<tr>
							<td colspan="2" align="center">
								<table class="tb4cantos" width="100%">
									<tr bgcolor="#F7F7F7">
										<td colspan="9" align="center">Busca rápida de Nota Fiscal</td>
									</tr>
									<tr>
										<td>
											<table cellpadding="3" width="100%">
													<tr>
														<td align="center" colspan="3" class="req">
															Código da Nota:
															<input class="medium" type="text" name="nota" id="nota" value="{$smarty.post.nota}" maxlength='20' onkeydown="FormataInteiro('nota')" onkeyup="FormataInteiro('nota')" />
														</td>
													</tr>
													<tr>
														<td align="center" ><input checked class="radio" type="radio" name="tipo" id="tipo" value="NF" /> Nota Fiscal</td>
														<td align="center" ><input class="radio" type="radio" name="tipo" id="tipo" value="SD" /> Série D </td>
													</tr>
													<tr>
													<td align="center" colspan = "3"><input type="button" class="botao_padrao" value="Buscar!" name="button" onClick="xajax_Verifica_Campos_Busca_Rapida_AJAX(xajax.getFormValues('for'))" />
													</td>
												</tr>		
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>

				</td>
			</tr>

			<tr>
				<td>
					<br>
					<div id="info_orcamento" name="info_orcamento">
					</div>
				</td>
			</tr>
		</table>
	
	</div>
	
			{************************************}
			{* TAB 1 *}
			{************************************}

	<div id="tab_1" class="anchor">

		<div id="produtos" name="produtos">
			 Por favor, selecione um orçamento válido.	
		</div>

	</div>

			{************************************}
			{* TAB 2 *}
			{************************************}

	<div id="tab_2" class="anchor">

		<table width="100%">	

				<tr>
					<td colspan="9" align="center">
						Transportador:
						<input type="hidden" name="idtransportador_Tipo" id="idtransportador_Tipo" value="" />
						<input type="hidden" name="idtransportador" id="idtransportador" value="{$smarty.post.idtransportador}" />
						<input type="hidden" name="idtransportador_NomeTemp" id="idtransportador_NomeTemp" value="{$smarty.post.idtransportador_NomeTemp}" />
						<input class="large" type="text" name="idtransportador_Nome" id="idtransportador_Nome" value="{$smarty.post.idtransportador_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('idtransportador');
							"
						/>
						<span class="nao_selecionou" id="idtransportador_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<tr>
					<td colspan="9" align="center">
					  <div id="dados_transportador">
						</div>
					</td>
				</tr>

				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("idtransportador_Nome", function() {ldelim}
				    	return "transportador_ajax.php?ac=busca_transportador&typing=" + this.text.value + "&campoID=idtransportador" + "&mostraDetalhes=1";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>

				
				<tr>
					<td class="req" align="right">Programada para:</td>
					<td>
						<input class="short" type="text" name="dataMarcada" id="dataMarcada" value="{$smarty.post.dataMarcada}" maxlength='10' onkeydown="mask('dataMarcada', 'data')" onkeyup="mask('dataMarcada', 'data')" /> (dd/mm/aaaa)
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Entrega Realizada?</td>
					<td>
						<input {if $smarty.post.realizada=="S"}checked{/if} class="radio" type="radio" name="realizada" id="realizada" value="S" />Sim
						<input {if $smarty.post.realizada=="N"}checked{/if} checked class="radio" type="radio" name="realizada" id="realizada" value="N" />Não
					</td>
				</tr>

				<tr>
					<td align="right">Realizada em:</td>
					<td>
						<input class="short" type="text" name="dataRealizada" id="dataRealizada" value="{$smarty.post.dataRealizada}" maxlength='10' onkeydown="mask('dataRealizada', 'data')" onkeyup="mask('dataRealizada', 'data')" /> (dd/mm/aaaa)
					</td>
				</tr>
	
				<tr>
					<td align="right">Observações:</td>
					<td>
						<textarea name="obs" id="obs" rows='6' cols='38'>{$smarty.post.obs}</textarea>
					</td>
				</tr>
				
				<tr>
					<td align="right">Placa do Veículo:</td>
					<td><input class="medium" type="text" name="placa" id="placa" maxlength="7" value="{$smarty.post.placa}"/></td>
				</tr>
		
				<tr><td ></td></tr>
				<tr><td ></td></tr>

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






			<table align="center">
				<tr><td>&nbsp;</td></tr>

				<tr>
          <td colspan="2" align="center">
  						<input type='button'  class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar" 
							onClick="xajax_Verifica_Campos_Entrega_AJAX(xajax.getFormValues('for'));"/>
          </td>
        </tr>
			</table>
</form>
</div>
			<script language="javascript">
			Processa_Tabs(0, 'tab_'); // seta o tab inicial
			</script>

  {/if}

{/if}

{include file="com_rodape.tpl"}
