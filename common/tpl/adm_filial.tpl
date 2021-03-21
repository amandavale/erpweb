{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}



<script type="text/javascript" src="{$conf.addr}/common/js/tabs.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>

<script type="text/javascript" src="{$conf.addr}/common/js/jquery-1.6.1.min.js"></script>

<script type="text/javascript" src="{$conf.addr}/common/js/copia_campo.js"></script>


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
					<th align='center'>No</th>
					<th align='center'>Nome da filial</th>
					<th align='center'>CNPJ</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfilial={$list[i].idfilial}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfilial={$list[i].idfilial}">{$list[i].nome_filial}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfilial={$list[i].idfilial}">{$list[i].cnpj_filial}</a></td>
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

  	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idfilial={$info.idfilial}" method="post" name = "for_filial" id = "for_filial">
    <input type="hidden" name="for_chk" id="for_chk" value="1" />
		<input type="hidden" name="idendereco_filial" id="idendereco_filial" value="{$info.idendereco_filial}" />

		  <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados da Filial</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Endereço da Filial</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Funcionários da Filial</a></li>
				<li><a id="a_tab_3" onclick="Processa_Tabs(3, 'tab_')" href="javascript:;">Contas Bancárias da Filial</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

			<div id="tab_0" class="anchor">

			<table width="95%" align="center">

				<tr>
					<td width="30%" class="req" align="right">Nome da filial:</td>
					<td><input class="long" type="text" name="litnome_filial" id="litnome_filial" maxlength="100" value="{$info.litnome_filial}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">CNPJ:</td>
					<td><input class="long" type="text" name="litcnpj_filial" id="litcnpj_filial" maxlength="18" value="{$info.litcnpj_filial}" onkeydown="mask('litcnpj_filial', 'cnpj')" onkeyup="mask('litcnpj_filial', 'cnpj')" /></td>
				</tr>
				
				<tr>
					<td align="right">Inscrição estadual:</td>
					<td><input class="long" type="text" name="litinscricao_estadual_filial" id="litinscricao_estadual_filial" maxlength="30" value="{$info.litinscricao_estadual_filial}"/></td>
				</tr>
				
				<tr>
					<td align="right">Telefone:</td>
					<td>
						<input class="tiny" type="text" name="telefone_filial_ddd" id="telefone_filial_ddd" value="{$info.telefone_filial_ddd}" maxlength='2' />
						<input class="short" type="text" name="littelefone_filial" id="littelefone_filial" value="{$info.littelefone_filial}" maxlength='9'onkeydown="mask('littelefone_filial', 'tel')" onkeyup="mask('littelefone_filial', 'tel')" />
					</td>
				</tr>

				<tr>
					<td align="right">Fax:</td>
					<td>
						<input class="tiny" type="text" name="fax_filial_ddd" id="fax_filial_ddd" value="{$info.fax_filial_ddd}" maxlength='2' />
						<input class="short" type="text" name="litfax_filial" id="litfax_filial" value="{$info.litfax_filial}" maxlength='9'onkeydown="mask('litfax_filial', 'tel')" onkeyup="mask('litfax_filial', 'tel')" />
					</td>
				</tr>

				<tr>
					<td align="right">Email:</td>
					<td><input class="long" type="text" name="litemail_filial" id="litemail_filial" maxlength="100" value="{$info.litemail_filial}"/></td>
				</tr>

				<tr>
					<td align="right">Site:</td>
					<td>
						<input class="long" type="text" name="litsite_filial" id="litsite_filial" value="{$info.litsite_filial}" maxlength='100' />
					</td>
				</tr>


				<tr>
					<td align="right">Observação:</td>
					<td>
						<textarea name="litobservacao_filial" id="litobservacao_filial" rows='6' cols='38'>{$info.litobservacao_filial}</textarea>
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
					<td width="30%" align="right">Logradouro:</td>
					<td><input class="long" type="text" name="filial_logradouro" id="filial_logradouro" maxlength="100" value="{$info.filial_logradouro}"/></td>
				</tr>

				<tr>
					<td align="right">Nº:</td>
					<td><input class="short" type="text" name="filial_numero" id="filial_numero" maxlength="10" value="{$info.filial_numero}"/></td>
				</tr>

				<tr>
					<td align="right">Complemento:</td>
					<td><input class="medium" type="text" name="filial_complemento" id="filial_complemento" maxlength="50" value="{$info.filial_complemento}"/></td>
				</tr>

				<tr>
					<td align="right">Estado:</td>
					<td>
						<input type="hidden" name="filial_idestado" id="filial_idestado" value="{$info.filial_idestado}" />
						<input type="hidden" name="filial_idestado_NomeTemp" id="filial_idestado_NomeTemp" value="{$info.filial_idestado_NomeTemp}" />
						<input class="long" type="text" name="filial_idestado_Nome" id="filial_idestado_Nome" value="{$info.filial_idestado_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('filial_idestado', 'filial_idcidade#filial_idbairro');
							"
						/>
						<span class="nao_selecionou" id="filial_idestado_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("filial_idestado_Nome", function() {ldelim}
				    	return "estado_ajax.php?ac=busca_estado&typing=" + this.text.value + "&campoID=filial_idestado";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<tr>
					<td align="right">Cidade:</td>
					<td>
						<input type="hidden" name="filial_idcidade" id="filial_idcidade" value="{$info.filial_idcidade}" />
						<input type="hidden" name="filial_idcidade_NomeTemp" id="filial_idcidade_NomeTemp" value="{$info.filial_idcidade_NomeTemp}" />
						<input class="long" type="text" name="filial_idcidade_Nome" id="filial_idcidade_Nome" value="{$info.filial_idcidade_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('filial_idcidade','filial_idbairro');
							"
						/>
						<span class="nao_selecionou" id="filial_idcidade_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("filial_idcidade_Nome", function() {ldelim}
				    	return "cidade_ajax.php?ac=busca_cidade&typing=" + this.text.value + "&campoID=filial_idcidade" + "&idestado=" + document.getElementById('filial_idestado').value;
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<tr>
					<td align="right">Bairro:</td>
					<td>
						<input type="hidden" name="filial_idbairro" id="filial_idbairro" value="{$info.filial_idbairro}" />
						<input type="hidden" name="filial_idbairro_NomeTemp" id="filial_idbairro_NomeTemp" value="{$info.filial_idbairro_NomeTemp}" />
						<input class="long" type="text" name="filial_idbairro_Nome" id="filial_idbairro_Nome" value="{$info.filial_idbairro_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('filial_idbairro');
							"
						/>
						<span class="nao_selecionou" id="filial_idbairro_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("filial_idbairro_Nome", function() {ldelim}
				    	return "bairro_ajax.php?ac=busca_bairro&typing=" + this.text.value + "&campoID=filial_idbairro" + "&idcidade=" + document.getElementById('filial_idcidade').value;
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<script type="text/javascript">
				  // verifica os campos auto-complete
					VerificaMudancaCampo('filial_idestado', 'filial_idcidade#filial_idbairro');
					VerificaMudancaCampo('filial_idcidade','filial_idbairro');
					VerificaMudancaCampo('filial_idbairro');
				</script>


				<tr>
					<td align="right">CEP:</td>
					<td>
						<input class="short" type="text" name="filial_cep" id="filial_cep" value="{$info.filial_cep}" maxlength='10' onkeydown="mask('filial_cep', 'cep')" onkeyup="mask('filial_cep', 'cep')" />
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
								<td colspan="9" align="center">Busca dos <b>FUNCIONÁRIOS</b></td>
			        </tr>

							<tr>
								<td colspan="9" align="center">
									Funcionário:
									<input type="hidden" name="idfuncionario" id="idfuncionario" value="{$smarty.post.idfuncionario}" />
									<input type="hidden" name="idfuncionario_NomeTemp" id="idfuncionario_NomeTemp" value="{$smarty.post.idfuncionario_NomeTemp}" />
									<input class="extralarge" type="text" name="idfuncionario_Nome" id="idfuncionario_Nome" value="{$smarty.post.idfuncionario_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idfuncionario');
										"
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
										onClick="xajax_Insere_Funcionario_AJAX(xajax.getFormValues('for_filial'));"
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
								<td colspan="9" align="center">Tabela de Funcionários da Filial</td>
								<input type="hidden" name="total_funcionarios" id="total_funcionarios" value="0" />
			        </tr>

							<tr>
								<td align="center">
									<div id="div_funcionarios">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='center' width="35%">Nome</th>
												<th align='center' width="10%">Identidade</th>
												<th align='center' width="15%">Telefone</th>
												<th align='center' width="5%">Excluir ?</th>
											</tr>
										</table>

									</div>
								</td>
							</tr>

							<script type="text/javascript">
							  // Inicialmente, preenche todos os funcionarios que fazem parte da filial
								xajax_Seleciona_Funcionario_AJAX('{$info.idfilial}');
							</script>


						</table>
					</td>
        </tr>

			</table>

			</div>


			{************************************}
			{* TAB 3 *}
			{************************************}

			<div id="tab_3" class="anchor">

			<table width="95%" align="center">

        		<tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Dados da <b>Conta Bancária</b></td>
			        </tr>

							<tr>
								<td align="right">Banco:</td>
								<td align="left">									
									<input type="hidden" name="idbanco" id="idbanco" value="{$smarty.post.idbanco}" />
									<input type="hidden" name="idbanco_NomeTemp" id="idbanco_NomeTemp" value="{$smarty.post.idbanco_NomeTemp}" />
									<input class="extralarge" type="text" name="idbanco_Nome" id="idbanco_Nome" value="{$smarty.post.idbanco_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idbanco');
										"
									/>
									<span class="nao_selecionou" id="idbanco_Flag">
										&nbsp;&nbsp;&nbsp;
									</span>
								</td>
							</tr>
							<script type="text/javascript">
							    new CAPXOUS.AutoComplete("idbanco_Nome", function() {ldelim}
							    	return "banco_ajax.php?ac=busca_banco&typing=" + this.text.value + "&campoID=idbanco";
							    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							  // verifica os campos auto-complete
								VerificaMudancaCampo('idbanco');
							</script>


							<tr>
								<td align="right">Identificador:</td>
								<td>
									<input class="medium" type="text" name="identificador" id="identificador" maxlength="10" value="{$smarty.post.identificador}"/>
								</td>
							</tr>

							<tr>
								<td align="right">Agência:</td>
								<td>
									<input class="medium" type="text" name="agencia_filial" id="agencia_filial" maxlength="12" value="{$smarty.post.agencia_filial}"/>
									-
									<input class="tiny" type="text" name="agencia_dig_filial" id="agencia_dig_filial" maxlength="2" value="{$smarty.post.agencia_dig_filial}"/>
								</td>
							</tr>

							<tr>
								<td align="right">Conta:</td>
								<td>
									<input class="medium" type="text" name="conta_filial" id="conta_filial" maxlength="12" value="{$smarty.post.conta_filial}"/>
									-
									<input class="tiny" type="text" name="conta_dig_filial" id="conta_dig_filial" maxlength="2" value="{$smarty.post.conta_dig_filial}"/>
								</td>
							</tr>


							<tr>
								<td align="right">Cedente:</td>
								<td>
									<input class="long" type="text" name="conta_cedente" id="conta_cedente" maxlength="100" value="{$smarty.post.conta_cedente}"/> <input type="checkbox" name="cedente_igual_filial" id="cedente_igual_filial" onchange="copia_campo('cedente_igual_filial','nome_filial','conta_cedente')" /> Mesmo nome da filial
								</td>
							</tr>

							<tr>
								<td align="right">CNPJ do cedente:</td>
								<td>
									<input class="long" type="text" name="conta_cnpj" id="conta_cnpj" maxlength="18" value="{$smarty.post.conta_cnpj}" onkeydown="mask('conta_cnpj', 'cnpj')" onkeyup="mask('conta_cnpj', 'cnpj')" /> <input type="checkbox" name="cnpj_igual_filial" id="cnpj_igual_filial" onchange="copia_campo('cnpj_igual_filial','cnpj_filial','conta_cnpj')" /> Mesmo CNPJ da filial
								</td>
							</tr>

							<tr>
								<td align="right">Carteira:</td>
								<td>
									<input class="medium" type="text" name="carteira" id="carteira" maxlength="6" value="{$smarty.post.carteira}"/>
								</td>
							</tr>

							<tr>
								<td align="right">Conta principal?</td>
								<td>
									<input {if $smarty.post.principal_filial=="0"}checked{/if} class="radio" type="radio" name="principal_filial" id="principal_filial" value="0" />Não
									<input {if $smarty.post.principal_filial=="1"}checked{/if} class="radio" type="radio" name="principal_filial" id="principal_filial" value="1" />Sim
								</td>
							</tr>

							<tr>
								<td align="right">Prefixo do nosso n&uacute;mero:</td>
								<td>
									<input class="medium" type="text" name="prefixo_nosso_numero" id="prefixo_nosso_numero" maxlength="4" value="{$smarty.post.prefixo_nosso_numero}"/>
								</td>
							</tr>

							<tr>
								<td colspan="9" align="center">

									<input type='button' class="botao_padrao" value="Inserir conta bancária" name="botaoInserirConta" id="botaoInserirConta"
										onClick="xajax_Insere_Conta_Bancaria_AJAX(xajax.getFormValues('for_filial'));"
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
								<td colspan="9" align="center">Tabela de Contas Bancárias da Filial</td>
								<input type="hidden" name="total_contas_bancarias" id="total_contas_bancarias" value="0" />
			        </tr>

							<tr>
								<td align="center">
									<div id="div_contas_bancarias">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='center' width="20%">Banco</th>
												<th align='center' width="20%">Cedente</th>
												<th align='center' width="15%">CNPJ</th>
												<th align='center' width="5%">Agência</th>
												<th align='center' width="10%">Conta</th>
												<th align='center' width="5%">Carteira</th>
												<th align='center' width="5%">Identificador</th>
												<th align='center' width="10%">Prefixo<br>Nosso N&uacute;mero</th>
												<th align='center' width="5%">Principal?</th>
												<th align='center' width="5%">Excluir?</th>
											</tr>
										</table>

									</div>
								</td>
							</tr>

							<script type="text/javascript">
							  // Inicialmente, preenche todas as contas bancarias da filial
								xajax_Seleciona_Conta_Bancaria_AJAX('{$info.idfilial}');
							</script>


						</table>
					</td>
        </tr>

			</table>

			</div>



			<script language="javascript">
				Processa_Tabs(0, 'tab_'); // seta o tab inicial
			</script>

			<table width="95%" align="center">

       	<tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
						<input type='button' class="botao_padrao" value="ALTERAR" name = "ALTERAR" id = "ALTERAR"
							onClick="xajax_Verifica_Campos_Filial_AJAX(xajax.getFormValues('for_filial'));"
						/>
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for_filial','{$smarty.server.PHP_SELF}?ac=excluir&idfilial={$info.idfilial}','ATENÇÃO! Confirma a exclusão ?'))" >
        	</td>
        </tr>

			</table>

		</form>

		</div>

	      
	      
	      
	{elseif $flags.action == "adicionar"}

		<br>

		<div style="width: 100%;">

  	<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_filial" id = "for_filial">
    <input type="hidden" name="for_chk" id="for_chk" value="1" />

		  <ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados da Filial</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Endereço da Filial</a></li>
				<li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Funcionários da Filial</a></li>
				<li><a id="a_tab_3" onclick="Processa_Tabs(3, 'tab_')" href="javascript:;">Contas Bancárias da Filial</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

			<div id="tab_0" class="anchor">

			<table width="95%" align="center">
			
				<tr>
					<td width="30%" class="req" align="right">Nome da filial:</td>
					<td><input class="long" type="text" name="nome_filial" id="nome_filial" maxlength="100" value="{$smarty.post.nome_filial}"/></td>
				</tr>
				
				<tr>
					<td class="req" align="right">CNPJ:</td>
					<td><input class="long" type="text" name="cnpj_filial" id="cnpj_filial" maxlength="18" value="{$smarty.post.cnpj_filial}" onkeydown="mask('cnpj_filial', 'cnpj')" onkeyup="mask('cnpj_filial', 'cnpj')" /></td>
				</tr>
				
				<tr>
					<td align="right">Inscrição estadual:</td>
					<td><input class="long" type="text" name="inscricao_estadual_filial" id="inscricao_estadual_filial" maxlength="30" value="{$smarty.post.inscricao_estadual_filial}"/></td>
				</tr>
				

				<tr>
					<td align="right">Telefone:</td>
					<td>
						<input class="tiny" type="text" name="telefone_filial_ddd" id="telefone_filial_ddd" value="{$smarty.post.telefone_filial_ddd}" maxlength='2' />
						<input class="short" type="text" name="telefone_filial" id="telefone_filial" value="{$smarty.post.telefone_filial}" maxlength='9'onkeydown="mask('telefone_filial', 'tel')" onkeyup="mask('telefone_filial', 'tel')" />
					</td>
				</tr>

				<tr>
					<td align="right">Fax:</td>
					<td>
						<input class="tiny" type="text" name="fax_filial_ddd" id="fax_filial_ddd" value="{$smarty.post.fax_filial_ddd}" maxlength='2' />
						<input class="short" type="text" name="fax_filial" id="fax_filial" value="{$smarty.post.fax_filial}" maxlength='9'onkeydown="mask('fax_filial', 'tel')" onkeyup="mask('fax_filial', 'tel')" />
					</td>
				</tr>

				<tr>
					<td align="right">Email:</td>
					<td><input class="long" type="text" name="email_filial" id="email_filial" maxlength="100" value="{$smarty.post.email_filial}"/></td>
				</tr>

				<tr>
					<td align="right">Site:</td>
					<td>
						<input class="long" type="text" name="site_filial" id="site_filial" value="{$smarty.post.site_filial}" maxlength='100' />
					</td>
				</tr>

				<tr>
					<td align="right">Observação:</td>
					<td>
						<textarea name="observacao_filial" id="observacao_filial" rows='6' cols='38'>{$smarty.post.observacao_filial}</textarea>
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
					<td width="30%" align="right">Logradouro:</td>
					<td><input class="long" type="text" name="filial_logradouro" id="filial_logradouro" maxlength="100" value="{$smarty.post.filial_logradouro}"/></td>
				</tr>

				<tr>
					<td align="right">Nº:</td>
					<td><input class="short" type="text" name="filial_numero" id="filial_numero" maxlength="10" value="{$smarty.post.filial_numero}"/></td>
				</tr>

				<tr>
					<td align="right">Complemento:</td>
					<td><input class="medium" type="text" name="filial_complemento" id="filial_complemento" maxlength="50" value="{$smarty.post.filial_complemento}"/></td>
				</tr>


				<tr>
					<td align="right">Estado:</td>
					<td>
						<input type="hidden" name="filial_idestado" id="filial_idestado" value="{$smarty.post.filial_idestado}" />
						<input type="hidden" name="filial_idestado_NomeTemp" id="filial_idestado_NomeTemp" value="{$smarty.post.filial_idestado_NomeTemp}" />
						<input class="long" type="text" name="filial_idestado_Nome" id="filial_idestado_Nome" value="{$smarty.post.filial_idestado_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('filial_idestado', 'filial_idcidade#filial_idbairro');
							"
						/>
						<span class="nao_selecionou" id="filial_idestado_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("filial_idestado_Nome", function() {ldelim}
				    	return "estado_ajax.php?ac=busca_estado&typing=" + this.text.value + "&campoID=filial_idestado";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<tr>
					<td align="right">Cidade:</td>
					<td>
						<input type="hidden" name="filial_idcidade" id="filial_idcidade" value="{$smarty.post.filial_idcidade}" />
						<input type="hidden" name="filial_idcidade_NomeTemp" id="filial_idcidade_NomeTemp" value="{$smarty.post.filial_idcidade_NomeTemp}" />
						<input class="long" type="text" name="filial_idcidade_Nome" id="filial_idcidade_Nome" value="{$smarty.post.filial_idcidade_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('filial_idcidade','filial_idbairro');
							"
						/>
						<span class="nao_selecionou" id="filial_idcidade_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("filial_idcidade_Nome", function() {ldelim}
				    	return "cidade_ajax.php?ac=busca_cidade&typing=" + this.text.value + "&campoID=filial_idcidade" + "&idestado=" + document.getElementById('filial_idestado').value;
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<tr>
					<td align="right">Bairro:</td>
					<td>
						<input type="hidden" name="filial_idbairro" id="filial_idbairro" value="{$smarty.post.filial_idbairro}" />
						<input type="hidden" name="filial_idbairro_NomeTemp" id="filial_idbairro_NomeTemp" value="{$smarty.post.filial_idbairro_NomeTemp}" />
						<input class="long" type="text" name="filial_idbairro_Nome" id="filial_idbairro_Nome" value="{$smarty.post.filial_idbairro_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('filial_idbairro');
							"
						/>
						<span class="nao_selecionou" id="filial_idbairro_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("filial_idbairro_Nome", function() {ldelim}
				    	return "bairro_ajax.php?ac=busca_bairro&typing=" + this.text.value + "&campoID=filial_idbairro" + "&idcidade=" + document.getElementById('filial_idcidade').value;
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<script type="text/javascript">
				  // verifica os campos auto-complete
					VerificaMudancaCampo('filial_idestado', 'filial_idcidade#filial_idbairro');
					VerificaMudancaCampo('filial_idcidade','filial_idbairro');
					VerificaMudancaCampo('filial_idbairro');
				</script>


				<tr>
					<td align="right">CEP:</td>
					<td>
						<input class="short" type="text" name="filial_cep" id="filial_cep" value="{$smarty.post.filial_cep}" maxlength='10' onkeydown="mask('filial_cep', 'cep')" onkeyup="mask('filial_cep', 'cep')" />
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
								<td colspan="9" align="center">Busca dos <b>FUNCIONÁRIOS</b></td>
			        </tr>

							<tr>
								<td colspan="9" align="center">
									Funcionário:
									<input type="hidden" name="idfuncionario" id="idfuncionario" value="{$smarty.post.idfuncionario}" />
									<input type="hidden" name="idfuncionario_NomeTemp" id="idfuncionario_NomeTemp" value="{$smarty.post.idfuncionario_NomeTemp}" />
									<input class="extralarge" type="text" name="idfuncionario_Nome" id="idfuncionario_Nome" value="{$smarty.post.idfuncionario_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idfuncionario');
										"
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
										onClick="xajax_Insere_Funcionario_AJAX(xajax.getFormValues('for_filial'));"
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
								<td colspan="9" align="center">Tabela de Funcionários da Filial</td>
								<input type="hidden" name="total_funcionarios" id="total_funcionarios" value="0" />
			        </tr>

							<tr>
								<td align="center">
									<div id="div_funcionarios">
									
										<table width="100%" cellpadding="5">
											<tr>
												<th align='center' width="35%">Nome</th>
												<th align='center' width="10%">Identidade</th>
												<th align='center' width="15%">Telefone</th>
												<th align='center' width="5%">Excluir ?</th>
											</tr>
										</table>

									</div>
								</td>
							</tr>

						</table>
					</td>
        </tr>
        
			</table>

			</div>


			{************************************}
			{* TAB 3 *}
			{************************************}

			<div id="tab_3" class="anchor">

			<table width="95%" align="center">

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Dados da <b>Conta Bancária</b></td>
			        </tr>

							<tr>
								<td align="right">Banco:</td>
								<td align="left">
									<input type="hidden" name="idbanco" id="idbanco" value="{$smarty.post.idbanco}" />
									<input type="hidden" name="idbanco_NomeTemp" id="idbanco_NomeTemp" value="{$smarty.post.idbanco_NomeTemp}" />
									<input class="extralarge" type="text" name="idbanco_Nome" id="idbanco_Nome" value="{$smarty.post.idbanco_Nome}"
										onKeyUp="javascript:
											VerificaMudancaCampo('idbanco');
										"
									/>
									<span class="nao_selecionou" id="idbanco_Flag">
										&nbsp;&nbsp;&nbsp;
									</span>
								</td>
							</tr>
							<script type="text/javascript">
							    new CAPXOUS.AutoComplete("idbanco_Nome", function() {ldelim}
							    	return "banco_ajax.php?ac=busca_banco&typing=" + this.text.value + "&campoID=idbanco";
							    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
							</script>


							<script type="text/javascript">
							  // verifica os campos auto-complete
								VerificaMudancaCampo('idbanco');
							</script>

							<tr>
								<td align="right">Identificador:</td>
								<td>
									<input class="medium" type="text" name="identificador" id="identificador" maxlength="10" value="{$smarty.post.identificador}"/>
								</td>
							</tr>


							<tr>
								<td align="right">Agência:</td>
								<td>
									<input class="medium" type="text" name="agencia_filial" id="agencia_filial" maxlength="12" value="{$smarty.post.agencia_filial}"/>
									-
									<input class="tiny" type="text" name="agencia_dig_filial" id="agencia_dig_filial" maxlength="2" value="{$smarty.post.agencia_dig_filial}"/>
								</td>
							</tr>

							<tr>
								<td align="right">Conta:</td>
								<td>
									<input class="medium" type="text" name="conta_filial" id="conta_filial" maxlength="12" value="{$smarty.post.conta_filial}"/>
									-
									<input class="tiny" type="text" name="conta_dig_filial" id="conta_dig_filial" maxlength="2" value="{$smarty.post.conta_dig_filial}"/>
								</td>
							</tr>

							<tr>
								<td align="right">Cedente:</td>
								<td>
									<input class="long" type="text" name="conta_cedente" id="conta_cedente" maxlength="100" value="{$smarty.post.conta_cedente}"/> <input type="checkbox" name="cedente_igual_filial" id="cedente_igual_filial" onchange="copia_campo('cedente_igual_filial','nome_filial','conta_cedente')" /> Mesmo nome da filial
								</td>
							</tr>


							<tr>
								<td align="right">CNPJ do cedente:</td>
								<td>
									<input class="long" type="text" name="conta_cnpj" id="conta_cnpj" maxlength="18" value="{$smarty.post.conta_cnpj}" onkeydown="mask('conta_cnpj', 'cnpj')" onkeyup="mask('conta_cnpj', 'cnpj')" /> <input type="checkbox" name="cnpj_igual_filial" id="cnpj_igual_filial" onchange="copia_campo('cnpj_igual_filial','cnpj_filial','conta_cnpj')" /> Mesmo CNPJ da filial
								</td>
							</tr>

							<tr>
								<td align="right">Carteira:</td>
								<td>
									<input class="short" type="text" name="carteira" id="carteira" maxlength="6" value="{$smarty.post.carteira}"/>
								</td>
							</tr>

							<tr>
								<td align="right">Conta principal?</td>
								<td>
									<input {if $smarty.post.principal_filial=="0"}checked{/if} class="radio" type="radio" name="principal_filial" id="principal_filial" value="0" />Não
									<input {if $smarty.post.principal_filial=="1"}checked{/if} class="radio" type="radio" name="principal_filial" id="principal_filial" value="1" />Sim
								</td>
							</tr>

							<tr>
								<td align="right">Prefixo do nosso n&uacute;mero:</td>
								<td>
									<input class="medium" type="text" name="prefixo_nosso_numero" id="prefixo_nosso_numero" maxlength="4" value="{$smarty.post.prefixo_nosso_numero}"/>
								</td>
							</tr>

							<tr>
								<td colspan="9" align="center">

									<input type='button' class="botao_padrao" value="Inserir conta bancária" name="botaoInserirConta" id="botaoInserirConta"
										onClick="xajax_Insere_Conta_Bancaria_AJAX(xajax.getFormValues('for_filial'));"
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
								<td colspan="9" align="center">Tabela de Contas Bancárias da Filial</td>
								<input type="hidden" name="total_contas_bancarias" id="total_contas_bancarias" value="0" />
			        </tr>

							<tr>
								<td align="center">
									<div id="div_contas_bancarias">

										<table width="100%" cellpadding="5">
											<tr>
												<th align='center' width="20%">Banco</th>
												<th align='center' width="20%">Cedente</th>
												<th align='center' width="15%">CNPJ</th>
												<th align='center' width="5%">Agência</th>
												<th align='center' width="10%">Conta</th>
												<th align='center' width="5%">Carteira</th>
												<th align='center' width="5%">Identificador</th>
												<th align='center' width="10%">Prefixo<br>Nosso N&uacute;mero</th>
												<th align='center' width="5%">Principal?</th>
												<th align='center' width="5%">Excluir?</th>
											</tr>
										</table>

									</div>
								</td>
							</tr>

						</table>
					</td>
        </tr>

			</table>

			</div>




			<script language="javascript">
				Processa_Tabs(0, 'tab_'); // seta o tab inicial
			</script>

			<table width="95%" align="center">

       	<tr><td>&nbsp;</td></tr>

				<tr>
          <td colspan="2" align="center">
  						<input type='button' class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar"
								onClick="xajax_Verifica_Campos_Filial_AJAX(xajax.getFormValues('for_filial'));"
							/>
          </td>
        </tr>

			</table>

		</form>

		</div>

  {/if}

{/if}

{include file="com_rodape.tpl"}
