<form  action="" method="post" name = "for_fornecedor" id = "for_fornecedor">
    <input type="hidden" name="for_chk" id="for_chk" value="1" />



	<ul class="anchors">
		<li><a id="a_tab_1_0" onclick="Processa_Tabs(0, 'tab_1_')" href="javascript:;">Dados do Fornecedor</a></li>
		<li><a id="a_tab_1_1" onclick="Processa_Tabs(1, 'tab_1_')" href="javascript:;">Endereço do Fornecedor</a></li>
	</ul>
	
	{************************************}
	{* TAB 3 *}
	{************************************}
	
	<div id="tab_1_0" class="anchor">
	      
		<table width="95%" align="center">
				<tr>
					<td class="req" align="right" width="40%">Tipo do fornecedor:</td>
					<td>
						<input {if $smarty.post.tipo_fornecedor=="F"}checked{/if} class="radio" type="radio" name="tipo_fornecedor" id="tipo_fornecedor" value="F" />&nbsp;Pessoa Física
						<input {if $smarty.post.tipo_fornecedor=="J"}checked{/if} class="radio" type="radio" name="tipo_fornecedor" id="tipo_fornecedor" value="J" />&nbsp;Pessoa Jurídica
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Nome do fornecedor:</td>
					<td><input class="long" type="text" name="nome_fornecedor" id="nome_fornecedor" maxlength="100" value="{$smarty.post.nome_fornecedor}"/></td>
				</tr>
				
				<tr>
					<td align="right">Ramo de Atividade:</td>
					<td>
						<select id="idramo_atividade" name="idramo_atividade">								
							{html_options values=$list_ramo_atividade.idramo_atividade output=$list_ramo_atividade.descricao_atividade selected=0}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">CPF / CNPJ:</td>
					<td><input class="long" type="text" name="cpf_cnpj" id="cpf_cnpj" maxlength="20" value="{$smarty.post.cpf_cnpj}"/></td>
				</tr>
				
				<tr>
					<td align="right">Nome do contato:</td>
					<td><input class="long" type="text" name="nome_contato_fornecedor" id="nome_contato_fornecedor" maxlength="100" value="{$smarty.post.nome_contato_fornecedor}"/></td>
				</tr>

				<tr>
					<td align="right">Telefone:</td>
					<td>
						<input class="tiny" type="text" name="telefone_fornecedor_ddd" id="telefone_fornecedor_ddd" value="{$smarty.post.telefone_fornecedor_ddd}" maxlength='3' />
						<input class="short" type="text" name="telefone_fornecedor" id="telefone_fornecedor" value="{$smarty.post.telefone_fornecedor}" maxlength='9'onkeydown="mask('telefone_fornecedor', 'tel')" onkeyup="mask('telefone_fornecedor', 'tel')" />
					</td>
				</tr>

				<tr>
					<td align="right">Email:</td>
					<td><input class="long" type="text" name="email_fornecedor" id="email_fornecedor" maxlength="100" value="{$smarty.post.email_fornecedor}"/></td>
				</tr>
				
		
  		</table>
		</div>

		{************************************}
		{* TAB 4 *}
		{************************************}

		<div id="tab_1_1" class="anchor">
	      <table width="95%" align="center">
      		
					
			<tr>
				<td align="right" width="40%">Logradouro:</td>
				<td><input class="long" type="text" name="fornecedor_logradouro" id="fornecedor_logradouro" maxlength="100" value="{$smarty.post.fornecedor_logradouro}"/></td>
			</tr>
	
			<tr>
				<td align="right">Nº:</td>
				<td><input class="short" type="text" name="fornecedor_numero" id="fornecedor_numero" maxlength="10" value="{$smarty.post.fornecedor_numero}"/></td>
			</tr>
	
			<tr>
				<td align="right">Complemento:</td>
				<td><input class="medium" type="text" name="fornecedor_complemento" id="fornecedor_complemento" maxlength="50" value="{$smarty.post.fornecedor_complemento}"/></td>
			</tr>
	
			<tr>
				<td align="right">Estado:</td>
				<td>
					<input type="hidden" name="fornecedor_idestado" id="fornecedor_idestado" value="{$smarty.post.fornecedor_idestado}" />
					<input type="hidden" name="fornecedor_idestado_NomeTemp" id="fornecedor_idestado_NomeTemp" value="{$smarty.post.fornecedor_idestado_NomeTemp}" />
					<input class="long" type="text" name="fornecedor_idestado_Nome" id="fornecedor_idestado_Nome" value="{$smarty.post.fornecedor_idestado_Nome}"
						onKeyUp="javascript:
							VerificaMudancaCampo('fornecedor_idestado', 'fornecedor_idcidade#fornecedor_idbairro');
						"
					/>
					<span class="nao_selecionou" id="fornecedor_idestado_Flag">
						&nbsp;&nbsp;&nbsp;
					</span>
				</td>
			</tr>
			<script type="text/javascript">
			    new CAPXOUS.AutoComplete("fornecedor_idestado_Nome", function() {ldelim}
			    	return "estado_ajax.php?ac=busca_estado&typing=" + this.text.value + "&campoID=fornecedor_idestado";
			    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
			</script>
	
	
			<tr>
				<td align="right">Cidade:</td>
				<td>
					<input type="hidden" name="fornecedor_idcidade" id="fornecedor_idcidade" value="{$smarty.post.fornecedor_idcidade}" />
					<input type="hidden" name="fornecedor_idcidade_NomeTemp" id="fornecedor_idcidade_NomeTemp" value="{$smarty.post.fornecedor_idcidade_NomeTemp}" />
					<input class="long" type="text" name="fornecedor_idcidade_Nome" id="fornecedor_idcidade_Nome" value="{$smarty.post.fornecedor_idcidade_Nome}"
						onKeyUp="javascript:
							VerificaMudancaCampo('fornecedor_idcidade','fornecedor_idbairro');
						"
					/>
					<span class="nao_selecionou" id="fornecedor_idcidade_Flag">
						&nbsp;&nbsp;&nbsp;
					</span>
				</td>
			</tr>
			<script type="text/javascript">
			    new CAPXOUS.AutoComplete("fornecedor_idcidade_Nome", function() {ldelim}
			    	return "cidade_ajax.php?ac=busca_cidade&typing=" + this.text.value + "&campoID=fornecedor_idcidade" + "&idestado=" + document.getElementById('fornecedor_idestado').value;
			    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
			</script>
	
	
			<tr>
				<td align="right">Bairro:</td>
				<td>
					<input type="hidden" name="fornecedor_idbairro" id="fornecedor_idbairro" value="{$smarty.post.fornecedor_idbairro}" />
					<input type="hidden" name="fornecedor_idbairro_NomeTemp" id="fornecedor_idbairro_NomeTemp" value="{$smarty.post.fornecedor_idbairro_NomeTemp}" />
					<input class="long" type="text" name="fornecedor_idbairro_Nome" id="fornecedor_idbairro_Nome" value="{$smarty.post.fornecedor_idbairro_Nome}"
						onKeyUp="javascript:
							VerificaMudancaCampo('fornecedor_idbairro');
						"
					/>
					<span class="nao_selecionou" id="fornecedor_idbairro_Flag">
						&nbsp;&nbsp;&nbsp;
					</span>
				</td>
			</tr>
			<script type="text/javascript">
			    new CAPXOUS.AutoComplete("fornecedor_idbairro_Nome", function() {ldelim}
			    	return "bairro_ajax.php?ac=busca_bairro&typing=" + this.text.value + "&campoID=fornecedor_idbairro" + {*"&inserirBairro=true" + *} "&idcidade=" + document.getElementById('fornecedor_idcidade').value;
			    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
			</script>
	
	
			<script type="text/javascript">
			  // verifica os campos auto-complete
				VerificaMudancaCampo('fornecedor_idestado', 'fornecedor_idcidade#fornecedor_idbairro');
				VerificaMudancaCampo('fornecedor_idcidade','fornecedor_idbairro');
				VerificaMudancaCampo('fornecedor_idbairro');
			</script>
	
	
	
			<tr>
				<td align="right">CEP:</td>
				<td>
					<input class="short" type="text" name="fornecedor_cep" id="fornecedor_cep" value="{$smarty.post.fornecedor_cep}" maxlength='10' onkeydown="mask('fornecedor_cep', 'cep')" onkeyup="mask('fornecedor_cep', 'cep')" />
				</td>
			</tr>
	
		</table>		
	

		</div>

		<table>
	        <tr><td>&nbsp;</td></tr>
	         <tr>
	             <td align="center" colspan="2">
	                <input type='button' class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar" onClick="xajax_Cadastro_Rapido_Fornecedor_AJAX(xajax.getFormValues('for_fornecedor'));" />
	             	<input type='button' class="botao_padrao" value="CANCELAR" name = "CancelarFornecedor" id = "CancelarFornecedor" onClick="fecharLightbox(fornecedor_conteudo)" />
	             </td>
	         </tr>
	     </table>

 </form>
<script type="text/javascript">
Processa_Tabs(0, 'tab_1_');
</script> 
