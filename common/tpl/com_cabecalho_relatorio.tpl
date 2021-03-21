<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>{$conf.name}</title>
	<style type="text/css">@import url("{$conf.addr}/common/css/sigedeco.css");</style>
	
	
	
	<script type="text/javascript" src="{$conf.addr}/common/js/global.js"></script>
	
	{* JQuery *}
	<script type="text/javascript" src="{$conf.addr}/common/js/jquery-1.6.1.min.js"></script>
	<script type="text/javascript" src="{$conf.addr}/common/js/jquery.tablesorter.min.js"></script>
	

	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="expires" content="-1" />
	<meta name="robots" content="all" />
	<meta name="author" content="F6 Sistemas LTDA" />
	<meta name="description" content="Sigedeco" />
	<meta name="keywords" content="Sigedeco" />
</head>


				
				
<body leftmargin="0" topmargin="0" background-color: #0030CE>
	<table width="100%" height="100%" border="0" align="center" cellpadding="0" cellspacing="0">

  	<tr>
		  <td height="100%" width="95%" valign="top">

			{*<script language="javascript">window.print();</script>*}


<table align="center" width="95%">
	<tr>
		<td><image src="{$conf.addr}/common/img/{$conf.logo_cliente}" /></td>
		{if $infoFilialUsuario}
			<td style="text-align:center; text-transform:uppercase">
				<center><h3 style="width:100%">{$infoFilialUsuario.nome_filial}</h3></center>
				{$infoFilialUsuario.logradouro}, {$infoFilialUsuario.numero} {$infoFilialUsuario.complemento} - Bairro {$infoFilialUsuario.nome_bairro} 
			  - {$infoFilialUsuario.nome_cidade}                - {$infoFilialUsuario.nome_estado}         <br />
			    {if $infoFilialUsuario.telefone_filial} Telefone: {$infoFilialUsuario.telefone_formatado}  {/if}
			    {if $infoFilialUsuario.fax_filial}    - Fax:      {$infoFilialUsuario.fax_formatado}       {/if}
			</td>
		{/if}
		<td style="text-align:right;"><image src="{$conf.addr}/common/img/logo_erpweb.jpg" /></td>
	</tr>

</table>







