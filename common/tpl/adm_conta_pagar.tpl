{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}

<script type="text/javascript" src="{$conf.addr}/common/js/tabs.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>

{if $flags.okay}

	<table class="tb4cantosAzul" width="100%"  border="0" cellpadding="5" cellspacing="0">
		<tr>
			{if $flags.intrucoes_preenchimento != ""}
		  		<td class="tela" WIDTH="1%" height="20" valign="middle">
		  			<img class="lightbulb" src="{$conf.addr}/common/img/lampada.png" width="16" height="16" border="0" align="middle" onmouseover="pmaTooltip('{$flags.intrucoes_preenchimento}'); return false;" onmouseout="swapTooltip('default'); return false;" />
				</td>
			{/if}
	  	<td class="tela" WIDTH="5%" height="20">
				Tela:
			</td>
	  	<td class="descricao_tela" WIDTH="10%">
				{$conf.area}
			</td>
	  	<td class="tela" WIDTH="5%">
				Operações:
			</td>
	  	<td class="descricao_tela">
				{if $list_permissao.adicionar == '1'}
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a>
				{/if}
				{if $list_permissao.listar == '1'}
				{* <!-- &nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=busca_generica">busca genérica</a --> *}
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=busca_parametrizada">busca</a>
				{/if}
			</td>
		</tr>
	</table>

{include file="div_erro.tpl"}

	{if $flags.action == "editar"}

			{include file="adm_conta_pagar/editar.tpl"}

	      
	{elseif $flags.action == "adicionar"}

			{include file="adm_conta_pagar/adicionar.tpl"}

	{elseif $flags.action == "busca_parametrizada"}

		{include file="adm_conta_pagar/busca_parametrizada.tpl"}
		
		
	{* <!-- 29/05/2009 - A busca genérica foi desabilitada por não haver necessidade de uso,
		  			as buscas serão realizadas no modo de busca_parametrizada.
					
	{elseif $flags.action == "busca_generica"}

		{include file="adm_conta_pagar/busca_generica.tpl"}
		
	------------------------------------------------------------------------> *}

    {/if}

{/if}

{include file="com_rodape.tpl"}
