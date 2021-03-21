<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_solicitante" id = "for_solicitante">
    <input type="hidden" name="for_chk" id="for_chk" value="1" />

	<table width="95%" align="center">

        <tr>
            <td width="30%" class="req" align="right">Nome do solicitante:</td>
            <td><input class="long" type="text" name="nome_solicitante" id="nome_solicitante" maxlength="100" value="{$smarty.post.nome_solicitante}"/></td>
        </tr>

        <tr>
            <td align="right">CPF:</td>
            <td><input class="long" type="text" name="cpf_solicitante" id="cpf_solicitante" value="{$smarty.post.cpf_solicitante}" maxlength="14" onkeydown="mask('cpf_solicitante', 'cpf')" onkeyup="mask('cpf_solicitante', 'cpf')"  /></td>
        </tr>
        
        <tr>
          <td class="req" align="right">Sexo:</td>
          <td>
              <input {if $smarty.post.sexo_solicitante=="M"}checked{/if} class="radio" type="radio" name="sexo_solicitante" id="sexo_solicitante" value="M" />Masculino
              <input {if $smarty.post.sexo_solicitante=="F"}checked{/if} class="radio" type="radio" name="sexo_solicitante" id="sexo_solicitante" value="F" />Feminino
          </td>
	    </tr>
	

        <tr>
            <td align="right" class="">Tel. Comercial:</td>
            <td>
                <input class="tiny" type="text" name="telefone_solicitante_ddd" id="telefone_solicitante_ddd" value="{$smarty.post.telefone_solicitante_ddd}" maxlength='2' />
                <input class="short" type="text" name="telefone_solicitante" id="telefone_solicitante" value="{$smarty.post.telefone_solicitante}" maxlength='9'onkeydown="mask('telefone_solicitante', 'tel')" onkeyup="mask('telefone_solicitante', 'tel')" />
            </td>
        </tr>

        <tr>
            <td align="right">Email:</td>
            <td><input class="long" type="text" name="email_solicitante" id="email_solicitante" maxlength="100" value="{$smarty.post.email_solicitante}"/></td>
        </tr>


        <tr><td>&nbsp;</td></tr>

		<tr>
             <td align="center" colspan="2">
                 <input type='button' class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar" onClick="xajax_Cadastro_Rapido_ClienteFisico_AJAX(xajax.getFormValues('for_solicitante'),'solicitante');" />
                 <input type='button' class="botao_padrao" value="CANCELAR" name = "CancelarFisico" id = "CancelarSolicitante" onClick="fecharLightbox(solicitante_conteudo)" />
             </td>
         </tr>
     </table>

 </form>
 
