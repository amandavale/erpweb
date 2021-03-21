<table align="center" cellspacing="5">

	<tr>
		<td align="right" width="25%" >Cliente:</td>
		<td colspan="9" align="left" >
			<input type="hidden" name="idcliente" id="idcliente" value="{$smarty.post.idcliente}" />
			<input type="hidden" name="idcliente_NomeTemp" id="idcliente_NomeTemp" value="{$smarty.post.idcliente_Nome}" />
			<input class="ultralarge" type="text" name="idcliente_Nome" id="idcliente_Nome" value="{$smarty.post.idcliente_Nome}"
				onKeyUp="javascript:
					VerificaMudancaCampo('idcliente');
				" onchange="buscaCondominio();"
			/>
			<span class="nao_selecionou" id="idcliente_Flag">
				&nbsp;&nbsp;&nbsp;
			</span>
			
			<script type="text/javascript">
			    new CAPXOUS.AutoComplete("idcliente_Nome", function() {ldelim}
			   	return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente" + "&mostraDetalhes=1";
			    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
			

			  	// verifica os campos auto-complete
				VerificaMudancaCampo('idcliente');

			</script>
		</td>
	</tr>

	<tr>
		<td align="right" width="25%" >Condom&iacute;nio:</td>
		<td id="condominio" colspan="9" align="left" >
			<input type="hidden" name="idcondominio_selecionado" id="idcondominio_selecionado" value="{$smarty.post.idcondominio}" />
			<select name="idcondominio" id="select_condominio">
				<option value="">Selecione</option>
			</select>

		</td>
	</tr>

	<tr>
		<td align="right" valign="bottom" >Data de vencimento De:</td>
		<td align="left"> <input class="short" type="text" name="data_vencimento_de" id="data_vencimento_de" value="{$smarty.post.data_vencimento_de}" maxlength='10' onkeydown="mask('data_vencimento_de', 'data')" onkeyup="mask('data_vencimento_de', 'data')" />
			<img src="{$conf.addr}/common/img/calendar.png" id="img_data_vencimento_de" style="cursor: pointer;" />
			&nbsp;Até:	
			<input class="short" type="text" name="data_vencimento_ate" id="data_vencimento_ate" value="{$smarty.post.data_vencimento_ate}" maxlength='10' onkeydown="mask('data_vencimento_ate', 'data')" onkeyup="mask('data_vencimento_ate', 'data')" />
			<img src="{$conf.addr}/common/img/calendar.png" id="img_data_vencimento_ate" style="cursor: pointer;" /> (dd/mm/aaaa)
		</td>
	</tr>
	
	<tr>
		<td align="right" valign="bottom" >Data de movimento De:</td>
		<td align="left"> <input class="short" type="text" name="data_movimento_de" id="data_movimento_de" value="{$smarty.post.data_movimento_de}" maxlength='10' onkeydown="mask('data_movimento_de', 'data')" onkeyup="mask('data_movimento_de', 'data')" />
			<img src="{$conf.addr}/common/img/calendar.png" id="img_data_movimento_de" style="cursor: pointer;" />
			&nbsp;Até:	
			<input class="short" type="text" name="data_movimento_ate" id="data_movimento_ate" value="{$smarty.post.data_movimento_ate}" maxlength='10' onkeydown="mask('data_movimento_ate', 'data')" onkeyup="mask('data_movimento_ate', 'data')" />
			<img src="{$conf.addr}/common/img/calendar.png" id="img_data_movimento_ate" style="cursor: pointer;" /> (dd/mm/aaaa)
		</td>
	</tr>
	
	{if $flags.action != 'baixar_movimentos'}	
		<tr>
			<td align="right" valign="bottom" >Data de baixa De:</td>
			<td align="left"> <input class="short" type="text" name="data_baixa_de" id="data_baixa_de" value="{$smarty.post.data_baixa_de}" maxlength='10' onkeydown="mask('data_baixa_de', 'data')" onkeyup="mask('data_baixa_de', 'data')" />
				<img src="{$conf.addr}/common/img/calendar.png" id="img_data_baixa_de" style="cursor: pointer;" />
				&nbsp;Até:	
				<input class="short" type="text" name="data_baixa_ate" id="data_baixa_ate" value="{$smarty.post.data_baixa_ate}" maxlength='10' onkeydown="mask('data_baixa_ate', 'data')" onkeyup="mask('data_baixa_ate', 'data')" />
				<img src="{$conf.addr}/common/img/calendar.png" id="img_data_baixa_ate" style="cursor: pointer;" /> (dd/mm/aaaa)
			</td>
		</tr>
		
		<tr>
			<td align="right">Baixados</td>
			<td align="left">
				<select name="baixado" id="baixado">
					<option value="">[selecione]</option>
					<option value="1" {if $smarty.post.baixado == "1"}selected{/if} >baixados</option>
					<option value="0" {if $smarty.post.baixado == "0"}selected{/if} >não-baixados</option>
				</select> 
			</td>
		</tr>

		<tr>
			<td align="right">Negociados</td>
			<td align="left">
				<select name="negociacao" id="negociacao">
					<option value="">[selecione]</option>
					<option value="1" {if $smarty.post.negociacao == "1"}selected{/if} >Sim</option>
					<option value="0" {if $smarty.post.negociacao == "0"}selected{/if} >N&atilde;o</option>
				</select> 
			</td>
		</tr>

	{/if}	
	<tr>
		<td align="right" >Resultados por p&aacute;g.</td>
		<td align="left" ><input type="text" class="tiny" name="rppg" id="rppg" value="{if !$smarty.post.rppg}{$conf.rppg}{else}{$smarty.post.rppg}{/if}"></td>
	</tr>
	
	
	
	
	<tr><td>&nbsp;</td></tr>
	{if $smarty.get.boletos == '1' && count($list) > 0}	

		<tr>
			<td align="right" >Banco:</td>
			<td align="left" >

				<select name="banco" id="banco" >
					<option value="">[selecione]</option>
					{html_options values=$lista_bancos.id_banco output=$lista_bancos.nome_banco selected=$smarty.post.banco}
				</select>

				&nbsp;&nbsp;&nbsp;
				<input type="button" class="botao_padrao" value="Imprimir Todos os Boletos" onClick="xajax_Emite_Boleto_Ajax(xajax.getFormValues('for'),'full')" />
			</td>
		</tr>


	{literal}
	<script language="javascript">
		
		function insertAll(){
		
		
			var instrucoes = document.getElementById('slc_instrucoes');
		
			for (var i = 0; i < instrucoes.options.length; i++) {
		
				var curOption = instrucoes.options[i];
				if (curOption.selected) {
				  document.getElementById('instrucoes').value += curOption.text + "\n";
				}
		
			} 
		}
	</script>
	
	{/literal}


		
		<tr>
			<td align="right" valign="top">Selecione as Instruções:</td>
			<td>
			<select name="slc_instrucoes" id="slc_instrucoes" MULTIPLE SIZE=4 style="background-color:transparent; width:382px; ">
			{foreach from=$instrucoes item=instrucao}
				<option ondblclick="limitLineNumbers(document.getElementById('instrucoes'), 4); document.getElementById('instrucoes').value += this.text + '\n';" value={$instrucao.texto_instrucao}>{$instrucao.texto_instrucao}</option>
			{/foreach}
			</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="button" onclick="insertAll();" value="Inserir Selecionadas" /> &nbsp;&nbsp;&nbsp;
				<input type="button" onclick="document.getElementById('instrucoes').value = null"; value="Limpar Instruções" />
			</td>
		</tr>	
		<tr>
			<td align="right" valign="top">Instruções:</td>
			<td align="left">
				<textarea cols="50" rows="3" value="" name="instrucoes" id="instrucoes" onkeyup="limitLineNumbers(this, 4);">{*$boleto_instrucoes*}</textarea>
				{literal} <!-- Limita o número de linhas do textarea -->
				<script language='javascript' >
					function limitLineNumbers(textarea,limit){
						var val=textarea.value.replace(/\r/g,'').split('\n');
						if(val.length>limit){
						alert('É permitido no máximo '+limit+' linhas');
						textarea.value=val.slice(0,-1).join('\n')
						}
					}
				</script>
				{/literal}  
			</td>
		</tr>

	{* Chama o CKEditor para exibir ferramenta de formatação de texto em textarea *}
	<script type="text/javascript" src="{$conf.addr}/common/lib/ckeditor/ckeditor.js"></script> 

        <tr>
			<td align="right" width="396px">Informa&ccedil;&otilde;es para todos os boletos no campo "Demonstrativo":</td>
            <td>
                <textarea name="demonstrativo" id="demonstrativo" style="width:396px; height:100px" >{$smarty.post.demonstrativo}</textarea>
                {* Chama o CKeditor para exibir as ferramentas de formatação no textarea *} 
                <script type="text/javascript"> CKEDITOR.replace('demonstrativo');</script>
            </td>
        </tr>	

		<tr><td>&nbsp;</td></tr>
		
	{/if}
	
	<tr>
		<td colspan="9" align="center"><input name="Submit" type="submit" class="botao_padrao" value="Buscar"></td>
	</tr>
															
	<script type="text/javascript">
		Calendar.setup(
			{ldelim}
				inputField : "data_vencimento_de", // ID of the input field
				ifFormat : "%d/%m/%Y", // the date format
				button : "img_data_vencimento_de", // ID of the button
				align  : "cR"  // alinhamento
			{rdelim}
		);

		Calendar.setup(
			{ldelim}
				inputField : "data_vencimento_ate", // ID of the input field
				ifFormat : "%d/%m/%Y", // the date format
				button : "img_data_vencimento_ate", // ID of the button
				align  : "cR"  // alinhamento
			{rdelim}
		);

		Calendar.setup(
			{ldelim}
				inputField : "data_movimento_de", // ID of the input field
				ifFormat : "%d/%m/%Y", // the date format
				button : "img_data_movimento_de", // ID of the button
				align  : "cR"  // alinhamento
			{rdelim}
		);

		Calendar.setup(
			{ldelim}
				inputField : "data_movimento_ate", // ID of the input field
				ifFormat : "%d/%m/%Y", // the date format
				button : "img_data_movimento_ate", // ID of the button
				align  : "cR"  // alinhamento
			{rdelim}
		);


		{if $flags.action != 'baixar_movimentos'}

		Calendar.setup(
			{ldelim}
				inputField : "data_baixa_de", // ID of the input field
				ifFormat : "%d/%m/%Y", // the date format
				button : "img_data_baixa_de", // ID of the button
				align  : "cR"  // alinhamento
			{rdelim}
		);

		Calendar.setup(
			{ldelim}
				inputField : "data_baixa_ate", // ID of the input field
				ifFormat : "%d/%m/%Y", // the date format
				button : "img_data_baixa_ate", // ID of the button
				align  : "cR"  // alinhamento
			{rdelim}
		);

		{/if}
		
	</script>	
	

	<!--  tr><th colspan="4">Dados do Cedente</th></tr>
	<tr>
		<td align="right">Nome:</td>
		<td align="left"><input type="text" name="nome_cedente" id="nome_cedente" class="long" value="{$dados_filial.nome_filial}" /></td>
	</tr>
	
	<tr>
		<td align="right">CNPJ:</td>
		<td align="left"><input type="text" name="cnpj_cedente" id="cnpj_cedente" class="long" value="{$dados_filial.cnpj_filial}" /></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td align="right">Endereço:</td>
		<td align="left">
			<input type="text" name="endereco_linha1" id="endereco_linha1" class="long" value="{$dados_filial.endereco.linha1}" /> <br />
			<input type="text" name="endereco_linha2" id="endereco_linha2" class="long" value="{$dados_filial.endereco.linha2}" />
		</td>
	</tr -->
</table>