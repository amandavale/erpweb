{if !$smarty.session.cond_login}

<form method="post" name="usr" id="usr" action="{$smarty.server.PHP_SELF}">
  	<input type="hidden" name="usr_chk" id="usr_chk" value="1" />
	  	
	<table width="350px" border="0" cellpadding="3" cellspacing="0" align="center" style="margin-top:20px;">
		<tr>			
			<td align="right">
				CPF / CNPJ
			</td>
			<td align="left">
				<input size="12" class="login long" type="text" name="usr_log" id="usr_log" value="{$smarty.post.usr_log}" maxlength="20"> <br />
			</td>
		</tr>
		<tr>
			<td align="right">Senha:</td>
			<td align="left">
				<input size="12" class="login" type="password" name="usr_sen" id="usr_sen" maxlength="8">
				<input name="Submit" type="submit" class="botao_padrao" value="OK" onClick="return(VerificaFilial());">
			</td>
		</tr>
	</table>
	
</form>

<p align='center' style="margin-top:25px">
	&bull; <a class="link_geral" href="http://www.sosprestadora.com.br">Voltar para o Website SOS Prestadora</a>
</p>

{/if}

