<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>{$conf.name}</title>
	<style type="text/css">@import url("{$conf.addr}/common/css/sigedeco.css");</style>
	
	{$xajax_javascript}

	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="expires" content="-1" />
	<meta name="robots" content="all" />
	<meta name="author" content="F6 Sistemas LTDA" />
	<meta name="description" content="ERPWEB" />
	<meta name="keywords" content="ERPWEB" />
</head>

<body style="margin-top: 0.50cm; margin-left: 0.50cm;">

	<form  action="" method="post" name="for_orcamento" id="for_orcamento">
  <input type="hidden" name="for_chk" id="for_chk" value="1" />
	<input type="hidden" name="maximoItensOrcamento" id="maximoItensOrcamento" value="{$smarty.post.maximoItensOrcamento}" />
	<input type="hidden" name="descontoMaximoOrcamento" id="descontoMaximoOrcamento" value="{$smarty.post.descontoMaximoOrcamento}" />

	<input type="hidden" name="tipoPreco" id="tipoPreco" value="{$info.littipoPreco}" />
	<input type="hidden" name="desconto" id="desconto" value="{$info.numdesconto}" />
	<input type="hidden" name="frete" id="frete" value="{$info.numfrete}" />
	<input type="hidden" name="outras_despesas" id="outras_despesas" value="{$info.numoutras_despesas}" />


	{include file="adm_orcamento_impressao_conteudo.tpl"}
	
	<hr />
	{include file="adm_orcamento_impressao_conteudo.tpl"}


	</form>


</body>

</html>
