<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>{$conf.name}</title>
	<style type="text/css"> {* @import url("{$conf.addr}/common/css/sigedeco.css");*}
	{literal}
		
		body{
			font-family: Arial, Helvetica, sans-serif;
			font-size: 11px;
			color: #333333;
		}
		
		table{ cellpadding:5px; }
		.fieldLabel { font-weight:bold; }
		.tableBorder{ border:solid 1px; }
		.folha { page-break-after: always; }
		.subtable tbody tr:nth-child(odd){ background-color: #DDD ;}
		.subtable tbody td, #tbl_material_os tfoot td{padding: 8px;}
		.borderBottom td, .borderBottom th {border-bottom:1px solid black;}
		.ultralarge { width: 616px; }
		.long { width: 282px; }
		
	{/literal}	
	</style>
		
	{$xajax_javascript}

	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="pragma"  content="no-cache" />
	<meta http-equiv="expires" content="-1" />
	<meta name="robots"      content="all" />
	<meta name="author"      content="F6 Sistemas LTDA" />
	<meta name="description" content="ERPWEB" />
	<meta name="keywords"    content="ERPWEB" />
</head>

<body style="margin-top: 0.50cm; margin-left: 0.50cm;">
<br><br><br><br>


	<table  align="center" width="1000px" style="margin:0 auto;" class="tableBorder">
	<tbody>
		
		<tr>
			<td colspan="4">
				<table>
					<tr>
						<td width="150px;" align="left"><img src="{$conf.addr}/common/img/sos.png" /></td> 
						<td align="center">
							<div style="margin-left:5%;">
							&bull; Faxinas em Empresas, Lojas e Prédios &bull; Administração de Condomínio &bull; Porteiros <br>
							&bull; Limpeza em caixa D'água, Dedetização, etc. <br><br>
							{$info.filial.endereco.linha1} - {$info.filial.nome_cidade} / {$info.filial.sigla_estado}<br />
							Fone/Fax: {$info.filial.telefone_formatado}
							</div>   
						</td>
						<td width="150px;"><img src="{$conf.addr}/common/img/logo_erpweb.jpg" style="margin-left:20%;" /></td>
					</tr>		
				</table>
			</td>
		</tr>
	
		<tr>
			<td colspan="4" >
				<table width="100%" class="tableBorder">	
					<tr>
						<td width="85%" align="center" style="border-right:1px solid black"><b>ORDEM DE SERVIÇO</b></td>
						<td width="15%" align="left" ><span class="fieldLabel">N&ordm;</span> {$info.num_ordem_servico}</td>
					</tr>
				</table>
			</td>
		</tr>
		
		<tr>
			<td colspan="4" >
			
				<table class="tableBorder" style="width:100%; padding:20px 0;">
	
						<td class="fieldLabel" width="90px">Solicitante:</td>
						<td align="left"  width="300px" >
							{$info.idsolicitante_Nome}
						</td>
						
						<td class="fieldLabel" align="right">Previsão:</td>
						<td align="left">{$info.previsao_servico}</td>
					</tr>
					<tr>
						<td class="fieldLabel" >Descrição:</td>
						<td align="left" colspan="3"  style="width:300px">
								{$info.descricao_ordem}
						</td>
					</tr>
					
					<tr>
						<td class="fieldLabel" >Status:</td>
						<td align="left" colspan="3" style="width:300px">
								{$list_transicao_status.0.nome_status_os} - {$list_transicao_status.0.nome_programacao}
						</td>
					</tr>
					
					
				</table>
			</td>
		</tr>
		<tr>	
		
		<tr>	
			<td colspan="4">
				<table class="tableBorder" style="width:100%; padding:20px 0;">		
				
							<tr>
								<td class="fieldLabel" style="width:200px" valign="top">Cliente:</td>
								<td align="left"   valign="top">
									{$info.idcliente_Nome}
								</td>
							</tr>
									
							<tr>
								<td >
									<b>Endereço do Cliente:</b>
								</td>
								<td>
									{$info.endereco_cliente}
								</td>
							</tr>
				</table>
			</td>
		</tr>
					
		<tr>
			<td colspan="4">
				<table class="tableBorder" style="width:100%; padding:20px 0;" >
							
							<tr>
								
								<td class="fieldLabel" style="width:200px" valign="top">Fornecedor:</td>
								<td align="left"   valign="top">
									{$info.dados_fornecedor.nome_fornecedor}
								</td>
							</tr>
							
							<tr>						
								<td >
									<b>Endereço do Fornecedor:</b>
								</td>
								<td>
									{$info.endereco_fornecedor}
								</td>
							</tr>
				</table>
			</td>
		</tr>			

		<tr>
			<td colspan="2" align="center" class="tableborder" style="margin-left:10px">
				<table class="tb4cantos subtable" width="100%" 	name="tbl_material_os" id="tbl_material_os" style="margin-left:15px;">
					<thead>
						<tr class="borderBottom">
							<th align='left' width="10%">Nota Fiscal</th>
							<!-- <th align='left' width="20%">Fornecedor</th> -->
							<th align='left' width="50%">Serviço / Material</th>
							<th align='center' >Qtd.</th>
							<th align='center' >Valor Un.(R$)</th>
							<th align='center' >Total(R$)</th>
						</tr>
						</thead>
						<tbody id="tbl_materiais">
							 {foreach from=$info.materiais item=material }
								<tr class="borderBottom" >
									<td>&nbsp;{$material.numero_nf}</td>
									<!--  <td>{$material.nome_fornecedor}</td> -->
									<td>{$material.descricao_produto}</td>
									<td align="right">&nbsp;{$material.qtd_produto}</td>
									<td align="right">&nbsp;{$material.valor_unitario}</td>
									<td align="right">&nbsp;{$material.valor_total}</td>
								</tr>
							 {/foreach}
						</tbody>
						<tfoot>
							<tr >
								<td colspan="4" align="right">&nbsp;</td>
								<td align="right" >
									<b>R$ <span id="valor_total_os">{$info.valor_total_os}</span></b>
									
								</td>
								<td>&nbsp;</td>
							</tr>
						</tfoot>
				</table>
			</td>
		</tr>
			
		<tr valign="top" style="height:150px">
			<td class="tableBorder" colspan="1"  width="400px">	
				<table >
					<tr>
						<td class="fieldLabel" >Solicitações:</td>
					</tr>
					<tr>
						<td align="left" >{$info.observacao_ordem}</td>
					</tr>
				</table>
			</td>
		</tr>		
			
		</tbody>		
	</table>




</body>

</html>