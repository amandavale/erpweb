<tr>
 	<td height="100%" valign="top" bgcolor="#FFFFFF">
		<table width="100%" height="100%" border="0" cellpadding="5" cellspacing="5">
   		<tr>

				{* menu de administradores *}
				{if $smarty.session.usr_cod != ""}

	     		<td width="160" height="100%" valign="top">

						{****************************}
						{* MENU  Empresa *}
						{****************************}


						{* ULTIMO MENU INSERIDO: menu13 *}

<noscript>
	Ative o <strong>JavaScript</strong> para que o menu funcione corretamente.
</noscript>
						<table class="tb4cantosAzul" width="100%" border="0" cellpadding="0" cellspacing="0">

							<tr>
								<td align="left" height="30">
{*menu empresa*}
								<strong><a class="menu_item" href="javascript:MostraMenu('menu1')">Empresa</a></strong><br />
								<div id='menu1' class = "div_padrao">
									<a href="{$conf.addr}/admin/filial.php" class="linque" target="_parent">Filial</a>
									<a href="{$conf.addr}/admin/conta_filial.php" class="linque" target="_parent">Contas Bancárias da Filial</a>
									<a href="{$conf.addr}/admin/departamento.php" class="linque" target="_parent">Departamento</a>
									<a href="{$conf.addr}/admin/secao.php" class="linque" target="_parent">Seção</a>
								</div>
{*menu orcamento/nf*}
								<strong><a class="menu_item" href="javascript:MostraMenu('menu2')">Or&ccedil;amento/Emissão Fiscal</a></strong><br />
								<div id='menu2' class = "div_padrao">
									<a href="{$conf.addr}/admin/cfop.php" class="linque" target="_parent">CFOP</a>
									<a href="{$conf.addr}/admin/motivo_cancelamento.php" class="linque" target="_parent">Motivo de Cancelamento</a>
									<a href="{$conf.addr}/admin/orcamento_frame.php?tipo=O" class="linque" target="_parent">Orçamento</a>
									<a href="{$conf.addr}/admin/orcamento_frame.php?tipo=ECF" class="linque" target="_parent">Cupom Fiscal</a>
									<a href="{$conf.addr}/admin/orcamento_frame.php?tipo=NF" class="linque" target="_parent">Nota Fiscal</a>
									<a href="{$conf.addr}/admin/orcamento_frame.php?tipo=SD" class="linque" target="_parent">Série D</a>
								</div>

{*menu contas a receber*}
								<strong><a class="menu_item" href="javascript:MostraMenu('menu13')">Contas a receber</a></strong><br />
								<div id='menu13' class = "div_padrao">
									<a href="{$conf.addr}/admin/conta_receber_frame.php?tipo=lancamento_nao_pago" class="linque" target="_parent">Contas NÃO quitadas</a>
									<a href="{$conf.addr}/admin/conta_receber_frame.php?tipo=lancamento_pago" class="linque" target="_parent">Contas Quitadas</a>
									<a href="{$conf.addr}/admin/conta_receber_frame.php?tipo=baixa"" class="linque" target="_parent">Baixa de contas a receber</a>
								</div>

								
{*menu contas a pagar*}
								<strong><a class="menu_item" href="javascript:MostraMenu('menu12')">Contas a pagar</a></strong><br />
								<div id='menu12' class = "div_padrao">
									<a href="{$conf.addr}/admin/conta_pagar_frame.php?tipo=lancamento_nao_pago" class="linque" target="_parent">Contas NÃO quitadas</a>
									<a href="{$conf.addr}/admin/conta_pagar_frame.php?tipo=lancamento_pago" class="linque" target="_parent">Contas Quitadas</a>
									<a href="{$conf.addr}/admin/conta_pagar_frame.php?tipo=baixa"" class="linque" target="_parent">Baixa de contas a pagar</a>
								</div>

{*menu compras*}
								<strong><a class="menu_item" href="javascript:MostraMenu('menu3')">Compras</a></strong><br />
								<div id='menu3' class = "div_padrao">
									<a href="{$conf.addr}/admin/pedido_frame.php" class="linque" target="_parent">Pedidos de Compras</a>
									<a href="{$conf.addr}/admin/entrada.php" class="linque" target="_parent">Entrada de Mercadorias</a>
								</div>
{*menu produtos*}
								<strong><a class="menu_item" href="javascript:MostraMenu('menu4')">Produtos</a></strong><br />
								<div id='menu4' class = "div_padrao">
									<a href="{$conf.addr}/admin/unidade_venda.php" class="linque" target="_parent">Unidade de Venda</a>
									<a href="{$conf.addr}/admin/produto.php" class="linque" target="_parent">Produto</a>
									<a href="{$conf.addr}/admin/encartelamento_frame.php" class="linque" target="_parent">Encartelamento</a>
								</div>
								
{*menu clientes*}
								<strong><a class="menu_item" href="javascript:MostraMenu('menu5')">Clientes</a></strong><br />
								<div id='menu5' class = "div_padrao">
									<a href="{$conf.addr}/admin/cliente_fisico.php" class="linque" target="_parent">Cliente Pessoa Física</a>
									<a href="{$conf.addr}/admin/cliente_juridico.php" class="linque" target="_parent">Cliente Pessoa Jurídica</a>
									<a href="{$conf.addr}/admin/motivo_bloqueio.php" class="linque" target="_parent">Motivo de Bloqueio</a>
								</div>
								
{*menu funcionários*}
								<strong><a class="menu_item" href="javascript:MostraMenu('menu6')">Funcion&aacute;rios</a></strong><br />
								<div id='menu6' class = "div_padrao">
									<a href="{$conf.addr}/admin/cargo.php" class="linque" target="_parent">Cargo</a>
									<a href="{$conf.addr}/admin/funcionario.php" class="linque" target="_parent">Funcionário</a>
									<a href="{$conf.addr}/admin/vendedor.php" class="linque" target="_parent">Vendedor</a>
								</div>
								
{*menu fornecedores*}
								<strong><a class="menu_item" href="javascript:MostraMenu('menu7')">Fornecedores</a></strong><br />
								<div id='menu7' class = "div_padrao">
									<a href="{$conf.addr}/admin/fornecedor.php" class="linque" target="_parent">Fornecedor</a>
									<a href="{$conf.addr}/admin/conta_fornecedor.php" class="linque" target="_parent">Contas Bancárias do Fornecedor</a>
								</div>
								
{*menu endereços*}
								<strong><a class="menu_item" href="javascript:MostraMenu('menu8')">Endere&ccedil;os</a></strong><br />
								<div id='menu8' class = "div_padrao">
									<a href="{$conf.addr}/admin/estado.php" class="linque" target="_parent">Estado</a>
									<a href="{$conf.addr}/admin/bairro.php" class="linque" target="_parent">Bairro</a>
								</div>
								
{*menu utilitarios*}
								<strong><a class="menu_item" href="javascript:MostraMenu('menu9')">Utilit&aacute;rios</a></strong><br />
								<div id='menu9' class = "div_padrao">
									<a href="{$conf.addr}/admin/transportador.php" class="linque" target="_parent">Transportador</a>
									<a href="{$conf.addr}/admin/banco.php" class="linque" target="_parent">Banco</a>
									<a href="{$conf.addr}/admin/ramo_atividade.php" class="linque" target="_parent">Ramo de atividade</a>
									<a href="{$conf.addr}/admin/parametro.php" class="linque" target="_parent">Parâmetros</a>
								</div>
								
{*menu de contas*}
								<strong><a class="menu_item" href="javascript:MostraMenu('menu10')">Contas</a></strong><br />
								<div id='menu10' class = "div_padrao">
									<a href="{$conf.addr}/admin/grupo_conta.php" class="linque" target="_parent">Grupo de Conta</a>
									<a href="{$conf.addr}/admin/conta.php" class="linque" target="_parent">Conta</a>
								</div>
								
{*menu de pagamentos*}
								<strong><a class="menu_item" href="javascript:MostraMenu('menu11')">Pagamentos</a></strong><br />
								<div id='menu11' class = "div_padrao">
									<a href="{$conf.addr}/admin/cobranca.php" class="linque" target="_parent">Cobrança</a>
									<a href="{$conf.addr}/admin/plano_pagamento.php" class="linque" target="_parent">Plano de pagamento</a>
								</div>
								
								</td>
							</tr>
						 </table>
					</td>
				{/if}

     		<td height="100%" valign="top">
