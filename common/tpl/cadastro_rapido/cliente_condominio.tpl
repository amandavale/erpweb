<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_cliente_condominio" id = "for_cliente_condominio">
    <input type="hidden" name="for_chk" id="for_chk" value="1" />

	<table width="95%" align="center">

        <tr>
            <td width="30%" class="req" align="right">Nome do cliente:</td>
            <td><input class="long" type="text" name="nome_cliente" id="nome_cliente" maxlength="100" value="{$smarty.post.nome_cliente}"/></td>
        </tr>

        <tr>
            <td align="right">CNPJ:</td>
            <td><input class="long" type="text" name="cnpj_cliente" id="cnpj_cliente" maxlength="18" value="{$smarty.post.cnpj_cliente}" onkeydown="mask('cnpj_cliente', 'cnpj')" onkeyup="mask('cnpj_cliente', 'cnpj')"  /></td>
        </tr>

        <tr>
            <td align="right" class="">Tel. Comercial:</td>
            <td>
                <input class="tiny" type="text" name="telefone_cliente_ddd" id="telefone_cliente_ddd" value="{$smarty.post.telefone_cliente_ddd}" maxlength='2' />
                <input class="short" type="text" name="telefone_cliente" id="telefone_cliente" value="{$smarty.post.telefone_cliente}" maxlength='9'onkeydown="mask('telefone_cliente', 'tel')" onkeyup="mask('telefone_cliente', 'tel')" />
            </td>
        </tr>

        <tr>
            <td align="right">Email:</td>
            <td><input class="long" type="text" name="email_cliente" id="email_cliente" maxlength="100" value="{$smarty.post.email_cliente}"/></td>
        </tr>


        <tr><td>&nbsp;</td></tr>

         <tr>
             <td align="center" colspan="2">
                 <input type='button' class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar" style="clear:both; float:center;"
                   onClick="xajax_Cadastro_Rapido_ClienteCondominio_AJAX(xajax.getFormValues('for_cliente_condominio'));"
                   />
             
             	<input type='button' class="botao_padrao" value="CANCELAR" name = "CancelarCondominio" id = "CancelarCondominio" onClick="fecharLightbox(cliente_conteudo)" />
             </td>
         </tr>

     </table>

 </form>
 
