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

<body style="margin-top: 0.50cm; margin-left: 1.50cm;">
	<table style="width: 18.10cm;" border="0" align="left" cellpadding="0" cellspacing="0">

		{* imprime a marca Saída e o Numero da NF *}
  	<tr>
		  <td style="height: 0.80cm;" width="100%" valign="bottom">

    		<table border="0" align="left" cellpadding="0" cellspacing="0">

					<tr>
						<td style="width: 10.80cm;"></td>
						<td align="left" style="width: 1.00cm;">X</td>
						<td style="width: 4.00cm;"></td>
						<td style="width: 2.30cm;">{$info.numeroNotaFormatado}</td>
					</tr>

				</table>
		  
			</td>
 		</tr>
 		{* -------------------------------- *}

  	<tr>
		  <td style="height: 1.50cm;" width="100%" valign="top">
			</td>
 		</tr>
 		

		{* imprime natureza da operação *}
  	<tr>
		  <td style="height: 0.50cm;" width="100%" valign="top">

    		<table border="0" align="left" cellpadding="0" cellspacing="0">

					<tr>
						<td align="left" style="width: 4.90cm;">{$info_cfop.descricao_curta}</td>
						<td align="left" style="width: 2.00cm;">{$info_cfop.codigo}</td>
					</tr>

				</table>

			</td>
 		</tr>
 		{* -------------------------------- *}


  	<tr>
		  <td style="height: 0.40cm;" width="100%" valign="top">
			</td>
 		</tr>


		{* imprime a primeira linha do Destinatario / Remetente *}
  	<tr>
		  <td style="height: 0.60cm;" width="100%" valign="bottom">
    		<table border="0" align="left" cellpadding="0" cellspacing="0">

					<tr>
						<td style="width: 10.50cm;">{$info_dados_cliente.nome_cliente}</td>
						<td style="width: 5.00cm;">{$info_dados_cliente.cpf_cnpj}</td>
						<td style="width: 2.50cm;">{$info.datahoraCriacaoNF_D}</td>
					</tr>

				</table>
			</td>
 		</tr>
		{* -------------------------------- *}


		{* imprime a segunda linha do Destinatario / Remetente *}
  	<tr>
		  <td style="height: 0.60cm;" width="100%" valign="bottom">
    		<table border="0" align="left" cellpadding="0" cellspacing="0">

					<tr>
						<td style="width: 8.60cm;">{$info_dados_cliente.logradouro} {$info_dados_cliente.numero}</td>
						<td style="width: 4.10cm;">{$info_dados_cliente.nome_bairro}</td>
						<td style="width: 2.80cm;">{$info_dados_cliente.cep}</td>
						<td style="width: 2.50cm;">{$info.datahoraCriacaoNF_D}</td>
					</tr>

				</table>
			</td>
 		</tr>
 		{* -------------------------------- *}
 		

		{* imprime a terceira linha do Destinatario / Remetente *}
  	<tr>
		  <td style="height: 0.60cm;" width="100%" valign="bottom">
    		<table border="0" align="left" cellpadding="0" cellspacing="0">

					<tr>
						<td style="width: 6.50cm;">{$info_dados_cliente.nome_cidade}</td>
						<td style="width: 3.30cm;">{$info_dados_cliente.telefone_cliente}</td>
						<td style="width: 0.80cm;">{$info_dados_cliente.sigla_estado}</td>
						<td style="width: 4.90cm;" align="center">{$info_dados_cliente.inscricao_estadual_cliente}</td>
						<td style="width: 2.50cm;">{$info.datahoraCriacaoNF_H}</td>
					</tr>

				</table>
			</td>
 		</tr>
 		{* -------------------------------- *}
 		

  	<tr>
		  <td style="height: 0.80cm;" width="100%" valign="top">
			</td>
 		</tr>
 		
 		

  	<tr>
		  <td style="height: 11.00cm;" width="100%" valign="top">
		  
				<div id="div_produtos">
				

				</div>

				<script type="text/javascript">
				  // Inicialmente, preenche todos os produtos que fazem parte do orçamento
					xajax_Seleciona_Produtos_AJAX('{$info.idorcamento}');
				</script>
				
				{* tabela antiga
    		<table border="0" align="left" cellpadding="0" cellspacing="0">
					<tr>
						<td style="height: 0.50cm; width: 0.80cm;" align="left">123{$list_produtos_orcamento[i].idproduto}</td>
						<td style="width: 8.80cm;">{$list_produtos_orcamento[i].descricao_produto}</td>
						<td style="width: 0.50cm;">xx</td>
						<td style="width: 0.70cm;">xx</td>
						<td style="width: 1.00cm;">{$list_produtos_orcamento[i].sigla_unidade_venda}</td>
						<td style="width: 1.00cm;" align="right">{$list_produtos_orcamento[i].qtd}</td>
						<td style="width: 2.00cm;" align="right">{$list_produtos_orcamento[i].preco_produto}</td>
						<td style="width: 2.30cm;" align="right">{$list_produtos_orcamento[i].subtotal_produto}</td>
						<td style="width: 0.70cm;" align="center">xx</td>
					</tr>
				</table>
		    *}

				
			</td>
 		</tr>


		{* ------  imprime o desconto ------- *}
  	<tr>
		  <td style="height: 0.50cm;" width="100%" valign="bottom">
    		<table border="0" align="left" cellpadding="0" cellspacing="0">
					<tr>
						<td style="height: 0.50cm; width: 0.80cm;" align="center">&nbsp;</td>
						<td style="width: 9.00cm;" align="left">Desconto...........................</td>
						<td style="width: 7.00cm;" align="right">{$info.desconto_nota}</td>
					</tr>
				</table>
			</td>
 		</tr>
		{* -------------------------------- *}


  	<tr>
		  <td style="height: 0.50cm;" width="100%" valign="top">
			</td>
 		</tr>


		{* CALCULO DO IMPOSTO *}
  	<tr>
		  <td style="height: 1.20cm;" width="100%" valign="bottom">
    		<table border="0" align="left" cellpadding="0" cellspacing="0">

					{* ---------- LINHA 1 ---------- *}
					<tr>
						{* Base de calculo do ICMS *}
						<td style="height: 0.60cm; width: 3.20cm;" align="center">{$info.numbase_calculo_icms}</td>

						{* Valor do ICMS *}
						<td style="width: 3.50cm;" align="center">{$info.numvalor_icms}</td>

						{* Base de calculo do ICMS Substituição *}
						<td style="width: 4.00cm;" align="center">{$info.numbase_calc_icms_sub}</td>

						{* Valor do ICMS Substituição *}
						<td style="width: 3.50cm;" align="center">{$info.numvalor_icms_sub}</td>

						{* Valor total dos produtos *}
						<td style="width: 3.50cm;" align="center">{$info.numvalor_total_produtos}</td>
					</tr>


					{* ---------- LINHA 2 ---------- *}
					<tr>
						{* Valor do Frete *}
						<td style="height: 0.60cm; width: 3.20cm;" align="center">{$info.numfrete}</td>

						{* Valor do Seguro *}
						<td style="width: 3.50cm;" align="center">{$info.numvalor_seguro}</td>

						{* Outras Despesas Acessorias *}
						<td style="width: 4.00cm;" align="center">{$info.numoutras_despesas}</td>

						{* Valor total do IPI *}
						<td style="width: 3.50cm;" align="center">{$info.numvalor_total_ipi}</td>

						{* Valor total da nota *}
						<td style="width: 3.50cm;" align="center">{$info.numvalor_total_nota}</td>
					</tr>

				</table>
			</td>
 		</tr>
		{* -------------------------------- *}


  	<tr>
		  <td style="height: 0.40cm;" width="100%" valign="top">
			</td>
 		</tr>


		{* TRANSPORTADOR / VOLUME TRANSPORTADOS *}
  	<tr>
		  <td style="height: 1.80cm;" width="100%" valign="bottom">
    		<table border="0" align="left" cellpadding="0" cellspacing="0">

					{* ---------- LINHA 1 ---------- *}
					<tr>
						{* Nome / Razão social *}
						<td style="height: 0.60cm; width: 8.10cm;" align="center">{$info_transportador.nome_transportador}</td>

						{* Frete p/ conta *}
						<td style="width: 2.30cm;" align="right">{$info.transportador_frete_por_conta_cod}</td>

						{* Placa do veículo *}
						<td style="width: 2.60cm;" align="center">{$info.transportador_placa_veiculo}</td>

						{* UF *}
						<td style="width: 0.90cm;" align="center">{$info_transportador.sigla_estado}</td>

						{* CNPJ / CPF *}
						<td style="width: 4.00cm;" align="center">{$info_transportador.cpf_cnpj}</td>
					</tr>

					{* ---------- LINHA 2 ---------- *}
					<tr>
						{* Endereço *}
						<td style="height: 0.60cm; width: 8.10cm;" align="center">{$info_transportador.logradouro} &nbsp;&nbsp; {$info_transportador.numero} &nbsp;&nbsp; {$info_transportador.nome_bairro}</td>

						{* Municipio *}
						<td colspan="2" style="width: 4.90cm;" align="center">{$info_transportador.nome_cidade}</td>

						{* UF *}
						<td style="width: 0.90cm;" align="center">{$info_transportador.sigla_estado}</td>

						{* Inscrição estadual *}
						<td style="width: 4.00cm;" align="center">{$info_transportador.instricao_estadual_transportador}</td>
					</tr>

					{* ---------- LINHA 3 ---------- *}
					<tr>
					  <td colspan="5" width="100%" valign="bottom">
			    		<table border="0" align="left" cellpadding="0" cellspacing="0">
								<tr>
									{* Quantidade *}
									<td style="height: 0.60cm; width: 2.50cm;" align="center">{$info.transportador_quantidade}</td>

									{* Especie *}
									<td colspan="2" style="width: 3.30cm;" align="center">{$info.transportador_especie}</td>

									{* Marca *}
									<td style="width: 2.30cm;" align="center">{$info.transportador_marca}</td>

									{* Numero *}
									<td style="width: 4.50cm;" align="center">{$info.transportador_numero}</td>
									
									{* Peso bruto *}
									<td style="width: 3.00cm;" align="center">{$info.transportador_peso_bruto}</td>

									{* Peso Liquido *}
									<td style="width: 2.50cm;" align="center">{$info.transportador_peso_liquido}</td>
								</tr>
							</table>
						</td>
					</tr>

				</table>
			</td>
 		</tr>
		{* -------------------------------- *}


  	<tr>
		  <td style="height: 0.80cm;" width="100%" valign="top">
			</td>
 		</tr>


		{* DADOS ADICIONAIS *}
  	<tr>
		  <td style="height: 2.50cm;" width="100%" valign="top">
    		<table border="0" align="left" cellpadding="0" cellspacing="0">

					<tr>
						<td style="width: 8.00cm;">{$info.dados_adicionais}</td>
					</tr>

				</table>
			</td>
 		</tr>
		{* -------------------------------- *}


  	<tr>
		  <td style="height: 1.00cm;" width="100%" valign="top">
			</td>
 		</tr>

		{* NUMERO DA NOTA FISCAL *}
  	<tr>
		  <td style="height: 0.80cm;" width="100%" valign="bottom">
    		<table border="0" align="left" cellpadding="0" cellspacing="0">

					<tr>
						<td style="width: 14.50cm;"></td>
						<td style="width: 3.50cm;">{$info.numeroNotaFormatado}</td>
					</tr>

				</table>
			</td>
 		</tr>
		{* -------------------------------- *}



	</table>

</body>

</html>

