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
	<meta name="description" content="Sigedeco" />
	<meta name="keywords" content="Sigedeco" />
</head>

<body style="margin-top: 0.50cm; margin-left: 0.50cm;">

	<form  action="" method="post" name="for_orcamento" id="for_orcamento">

		<table width="95%" align="center" border="0">
			<tr>
				<td align="center">
					<b>{$info_filial.nome_filial}</b> - <b> Comprovante de Entrega de Mercadoria </b> # {$info.identrega}
				</td>
			</tr>

			<tr>
				<td align="center">
					CNPJ. {$info_filial.cnpj_filial}
					&nbsp;&nbsp;&nbsp;&nbsp;
					Insc. Est. {$info_filial.inscricao_estadual_filial}
				</td>
			</tr>

			<tr>
				<td align="center" >
					{$info_endereco_filial.logradouro} {$info_endereco_filial.numero}
					&nbsp;&nbsp;&nbsp;
					{$info_endereco_filial.nome_bairro} {$info_endereco_filial.nome_cidade} {$info_endereco_filial.sigla_estado}
					&nbsp;&nbsp;&nbsp;
					CEP: {$info_endereco_filial.cep}
					&nbsp;&nbsp;&nbsp;
					Telefone: {$info_filial.telefone_filial}
					&nbsp;&nbsp;&nbsp;
					FAX: {$info_filial.fax_filial}
				</td>
			</tr>

		</table>

		<table width="95%" align="center" border="0">

			<tr>
				<td>
					Entrega referente a {if $info_nota.tipoOrcamento == "SD"}serie D <b>{$info_nota.numeroNota}</b>{/if}{if $info_nota.tipoOrcamento == "NF"}nota fiscal <b>{$info_nota.numeroNota}</b>{/if}
				</td>
			</tr>	
			<tr>
				<td>
					Cliente: {$info_cliente.nome_cliente}
				</td>
			</tr>
			<tr>
				<td>
					Endereço: {$info_cliente.logradouro} &nbsp;&nbsp;&nbsp; {$info_cliente.numero} &nbsp;&nbsp;&nbsp;
					Bairro: {$info_cliente.nome_bairro} &nbsp;&nbsp;&nbsp;
					Cidade: {$info_cliente.nome_cidade} &nbsp;&nbsp;&nbsp;
					Estado: {$info_cliente.sigla_estado} &nbsp;&nbsp;&nbsp;
					CEP: {$info_cliente.cep}
				</td>
			</tr>
			<tr>
				<td>
					Telefone: {$info_cliente.telefone_cliente} &nbsp;&nbsp;&nbsp;
		 			Fax: {$info_cliente.fax_cliente} &nbsp;&nbsp;&nbsp;
		 			Insc. Est.: {$info_cliente.inscricao_estadual_cliente} &nbsp;&nbsp;&nbsp;
		 			CPF/CNPJ: {$info_cliente.cpf_cnpj}
				</td>
			</tr>
		</table>

		<table width="95%" align="center" border="0">
			<tr>
				<td>
					Transportador: {$info_transportador.nome_transportador} &nbsp;&nbsp;&nbsp;
					CPF/CNPJ: {$info_transportador.cpf_cnpj}
				</td>
			</tr>
		</table>

		<table width="95%" align="center" border="0">

	    <tr>
				<td colspan="2" align="center">
					<table width="100%">

						<input type="hidden" name="total_produtos" id="total_produtos" value="0" />

						<tr>
							<td align="center" >

								<div id="div_produtos">

									<table width="100%" cellpadding="5">
										<tr>
											<td align='center' width="10%">Cód.</td>
											<td align='left' width="30%">Produto</td>
											<td align='center' width="5%">Unidade</td>
											<td align='center' width="10%">Qtd.</td>
										</tr>
									</table>

								</div>
							</td>
						</tr>
						


						<script type="text/javascript">
						  // Inicialmente, preenche todos os produtos que fazem parte do orçamento
							xajax_Seleciona_Produtos_Impressao_AJAX('{$info.identrega}');
						</script>


					</table>
				</td>
	    </tr>


			<tr>
			  <td colspan="2">

					<table width="100%" cellpadding="0">
						<tr>
							<td td align="center">
								<div id="info_produto" name="info_produto"> </div>
							</td>
						<tr>
	    		</table>

				</td>
			</tr>
			
		</table>
		
		<br>
		<br>
		<table width="25%" align="center" border="0">
	    <tr>
				<td align="center" class='tb_bord_baixo_solid'>
					&nbsp;
				</td>
			</tr>
	    <tr>
				<td align="center">
					{$info_cliente.nome_cliente}
				</td>
			</tr>

	    <tr>
				<td align="center">
					{$info.data_impresao}
				</td>
			</tr>

		</table>



	</form>


</body>

</html>
