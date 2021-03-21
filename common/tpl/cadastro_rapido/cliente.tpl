
<ul class="anchors">
	<li><a id="a_tab_0_0" onclick="Processa_Tabs(0, 'tab_0_')" href="javascript:;">Pessoa Física</a></li>
	<li><a id="a_tab_0_1" onclick="Processa_Tabs(1, 'tab_0_')" href="javascript:;">Pessoa Jurídica</a></li>
	<li><a id="a_tab_0_2" onclick="Processa_Tabs(2, 'tab_0_')" href="javascript:;">Condomínio</a></li>
</ul>

{************************************}
{* TAB 0 *}
{************************************}

<div id="tab_0_0" class="anchor">
	{include file="cadastro_rapido/cliente_fisico.tpl"}
</div>

{************************************}
{* TAB 1 *}
{************************************}

<div id="tab_0_1" class="anchor">
	{include file="cadastro_rapido/cliente_juridico.tpl"}
</div>

{************************************}
{* TAB 2 *}
{************************************}

<div id="tab_0_2" class="anchor">
	{include file="cadastro_rapido/cliente_condominio.tpl"}
</div>

<script type="text/javascript">
	 // seta o tab inicial
	 Processa_Tabs(0, 'tab_0_');
</script>