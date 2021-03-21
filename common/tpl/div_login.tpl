{if !$smarty.session.usr_cod}
	<table width="100%" border="0" cellpadding="10" cellspacing="0">
		<tr>
		<form method="post" name="usr" id="usr" action="{$smarty.server.PHP_SELF}">
	  	<input type="hidden" name="usr_chk" id="usr_chk" value="1" />
	
			<td align="center" valign="top">
	
					Usu&aacute;rio:
					<input size="12" class="login" type="text" name="usr_log" id="usr_log" value="{$smarty.post.usr_log}" maxlength="15">
	
					Senha:
					<input size="12" class="login" type="password" name="usr_sen" id="usr_sen" maxlength="8">
	
					Filial:
					<select name="idfilial" id="idfilial">
						<option value="">[selecione]</option>
						{html_options values=$list_filial.idfilial output=$list_filial.nome_filial selected=$smarty.post.idfilial selected=$conf.idfilial_padrao}
					</select>
	
					<input name="Submit" type="submit" class="botao_padrao" value="OK" onClick="return(VerificaFilial());">
	
			</td>
	
	  </form>
		</tr>
	</table>
{/if}

