<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_cliente_fisico" id = "for_cliente_fisico">
    <input type="hidden" name="for_chk" id="for_chk" value="1" />

	<table width="95%" align="center">

        <tr>
            <td width="30%" class="req" align="right">Nome do cliente:</td>
            <td><input class="long" type="text" name="nome_cliente" id="nome_cliente" maxlength="100" value="{$smarty.post.nome_cliente}"/></td>
        </tr>

        <tr>
            <td align="right">CPF:</td>
            <td><input class="long" type="text" name="cpf_cliente" id="cpf_cliente" value="{$smarty.post.cpf_cliente}" maxlength="14" onkeydown="mask('cpf_cliente', 'cpf')" onkeyup="mask('cpf_cliente', 'cpf')"  /></td>
        </tr>
        
        <tr>
          <td class="req" align="right">Sexo:</td>
          <td>
              <input {if $smarty.post.sexo_cliente=="M"}checked{/if} class="radio" type="radio" name="sexo_cliente" id="sexo_cliente" value="M" />Masculino
              <input {if $smarty.post.sexo_cliente=="F"}checked{/if} class="radio" type="radio" name="sexo_cliente" id="sexo_cliente" value="F" />Feminino
          </td>
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
                 <input type='button' class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar" onClick="xajax_Cadastro_Rapido_ClienteFisico_AJAX(xajax.getFormValues('for_cliente_fisico'));" />
                 <input type='button' class="botao_padrao" value="CANCELAR" name = "CancelarFisico" id = "CancelarFisico" onClick="fecharLightbox(cliente_conteudo)" />
             </td>
         </tr>
     </table>

 </form>
 
