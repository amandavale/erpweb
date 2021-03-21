<form  action="" method="post" name = "for_produto" id = "for_produto">
    <input type="hidden" name="for_chk" id="for_chk" value="1" />

	<table width="95%" align="center">
	
		<tr>
			<td class="req" align="right">Descrição do Material / Serviço:</td>
			<td><input class="long" type="text" name="descricao_produto" id="descricao_produto" maxlength="100" value="{$smarty.post.descricao_produto}"/></td>
		</tr>
		
		<tr>
			<td class="req" align="right">Seção:</td>
			<td>
				<select name="idsecao" id="idsecao">
				{html_options values=$list_secao.idsecao output=$list_secao.nome_secao}
				</select>
			</td>
		</tr>
		
		<tr>
			<td class="req" align="right">Unidade de venda:</td>
			<td>
				<select name="idunidade_venda" id="idunidade_venda">
				{html_options values=$list_unidade_venda.idunidade_venda output=$list_unidade_venda.nome_sigla_unidade_venda selected=1}
				</select>
			</td>
		</tr>
		
        <tr><td>&nbsp;</td></tr>

		<tr>
             <td align="center" colspan="2">
                 <input type='button' class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar" onClick="xajax_Cadastro_Rapido_Produto_AJAX(xajax.getFormValues('for_produto'));" />
                 <input type='button' class="botao_padrao" value="CANCELAR" name = "CancelarProduto" id = "CancelarProduto" onClick="fecharLightbox(produto_conteudo)" />
             </td>
         </tr>
     </table>

 </form>
 
