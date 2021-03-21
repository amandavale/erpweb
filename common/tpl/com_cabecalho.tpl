<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<object classid="clsid:2F9082AF-E2A7-4F20-9D6B-3855D79A3D86" id="BemaWeb" width="14" height="14" VIEWASTEXT>
	</object>
	
	<title>{$conf.name}</title>
	<style type="text/css">@import url("{$conf.addr}/common/css/sigedeco.css");</style>
	<style type="text/css">@import url("{$conf.addr}/common/css/autocompletar.css");</style>
	<style type="text/css">@import url("{$conf.addr}/common/css/tabs.css");</style>
	<style type="text/css">@import url("{$conf.addr}/common/css/tooltip.css");</style>

	<script type="text/javascript" src="{$conf.addr}/common/js/global.js"></script>

	{* Inclusões referentes ao calendario em javascript *}
	<style type="text/css">@import url("{$conf.addr}/common/css/calendario/theme.css");</style>
	<script type="text/javascript" src="{$conf.addr}/common/js/calendar.js"></script>
	<script type="text/javascript" src="{$conf.addr}/common/js/calendar-br.js"></script>
	<script type="text/javascript" src="{$conf.addr}/common/js/calendar-setup.js"></script>
	{* ---------------------------------------------------------------- *}

	{$xajax_javascript}


	
	{* JQuery *}
	<script type="text/javascript" src="{$conf.addr}/common/js/jquery-1.6.1.min.js"></script>
	<script type="text/javascript" src="{$conf.addr}/common/js/jquery.tablesorter.min.js"></script>
	
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="expires" content="-1" />
	<meta name="robots" content="all" />
	<meta name="author" content="F6 Sistemas LTDA" />
	<meta name="description" content="ERPWEB" />
	<meta name="keywords" content="ERPWEB" />
</head>

<body leftmargin="0" topmargin="1">
<div id="TooltipContainer" name="TooltipContainer" onmouseover="holdTooltip();" onmouseout="swapTooltip('default');"></div>

{if $smarty.session.menu_usuario != ""}
	<script type="text/javascript">
	{$smarty.session.menu_usuario}
	</script>
	<script type="text/javascript" src="{$conf.addr}/common/js/menu_certo.js"></script>
{/if}

		   			
		   			
<div id = 'header'>
	<div id = 'logocliente'>
		<div id = 'auth'>
				<p align='center'><br />
				   	{if $smarty.session.usr_sex == "F"}
				    	<b>Usuária:</b> {$smarty.session.usr_nom}
					{else}
						<b>Usuário:</b> {$smarty.session.usr_nom}
					{/if}
					 | <b>Filial:</b> {$smarty.session.nomefilial_usuario}
					 | <b>Data:</b> {*$smarty.now|date_format:"%d/%m/%Y"*} {$conf.data_atual}<br />
					<a target="_parent" class="link_geral" href="{$conf.addr}/admin/index.php?ac=logout">Sair do ERPWEB</a>
				</p>
		</div>	
	</div>

	<div id = 'logoerpweb'></div>
</div>
<div id = 'linhalaranja'></div>
<div id = 'wrapper'>	

<div id = 'programa'>

</div>