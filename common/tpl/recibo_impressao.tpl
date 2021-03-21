<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>{$conf.name}</title>
	<style type="text/css"> {* @import url("{$conf.addr}/common/css/sigedeco.css");*}
{literal}
	.fieldLabel { font-weight:bold; }
	.tableBorder{ border:solid 1px; }
	.folha { page-break-after: always; } 
	
	table{
		cellpadding:5px;
	}
	
	body{
		font-family: Arial, Helvetica, sans-serif;
		font-size: 11px;
		color: #333333;
	}
	
{/literal}	
	</style>  
	
	{$xajax_javascript}

	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="expires" content="-1" />
	<meta name="robots" content="all" />
	<meta name="author" content="F6 Sistemas LTDA" />
	<meta name="description" content="ERPWEB" />
	<meta name="keywords" content="ERPWEB" />
</head>

<body style="margin-top: 0.3cm; margin-left: 0.50cm;">
{capture name='recibo'}

	<table  align="center" width="30cm" class="tableBorder">
	<tbody>		
		<tr>
			<td colspan="4">
				<table>
					<tr>
						<td width="150px;" align="left"><img src="{$conf.addr}/common/img/sos.png" /></td> 
						<td align="center">
							<center>
							&bull; Faxinas em Empresas, Lojas e Prédios &bull; Administração de Condomínio &bull; Porteiros <br>
							&bull; Limpeza em caixa D'água, Dedetização, etc. <br><br>
							
							{$info.filial.endereco.linha1} - {$info.filial.nome_cidade} <br/> {$info.filial.sigla_estado} - Fone/Fax: {$info.filial.telefone_formatado}
							</center>   
						</td>
					</tr>		
				</table>
			</td>
		</tr>
	
		<tr>
			<td colspan="4" >
				<table width="100%">	
					<tr>
						<td width="85%" align="center" class="tableBorder">TERCERIZAÇÃO: SEGURANÇA E TRANQUILIDADE...</td>
						<td width="15%" align="left" class="tableBorder"> <span class="fieldLabel">N&ordm;</span> {$info.num_movimento}</td>
					</tr>
				</table>
			</td>
		</tr>
		
		<tr>
			<td colspan="4">
				<table width="100%">
					<tr>
						<td class="fieldLabel" width="90px">Cliente:</td>
						<td align="left"  width="300px" >
							{$info.cliente_recibo.nome_cliente}
						</td>
						
						
						<td class="fieldLabel" align="right">Data:</td>
						<td align="left">{$info.litdata_movimento}</td>
						
					</tr>
					
					<tr>
						<td class="fieldLabel">Endereço:</td>
						<td align="left">
							{$info.cliente_recibo.logradouro},  
	            			{$info.cliente_recibo.numero}
	            			{$info.cliente_recibo.complemento}		
						</td>
					</tr>
					
					<tr>
						<td class="fieldLabel">Bairro:</td>
						<td align="left">{$info.cliente_recibo.nome_bairro}</td>
						
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
					<table>
						<tr>
							<td class="fieldLabel" width="90px">Cidade:</td>
							<td align="left" width="417px">{$info.cliente_recibo.nome_cidade}</td>
							
							<td align="left">
								<span class="fieldLabel" width="90px">UF: </span>
								{$info.cliente_recibo.sigla_estado}
							</td>
							
						</tr>
						
						<tr>
							<td class="fieldLabel">CNPJ/CPF:</td>
							<td align="left">{$info.cliente_recibo.cpf_cnpj}</td>
							
							<td class="fieldLabel" width="90px">Insc. Est.:</td>
							<td align="left">{$info.cliente_recibo.inscricao_estadual_cliente}</td>
						</tr>
						
							
					</table>
				</td>
		</tr>
			<tr>
			
			<td colspan="4">	
				<table>	
			
					<tr>
						<td class="fieldLabel" >Serviço Contratado:</td>
						<td align="left" >{$info.descricao_movimento}</td>
					</tr>
					
					<tr >
						<td class="fieldLabel" width="180px">Valor Total do Serviço:</td>
						<td align="left">R$ {$info.numvalor_movimento}</td>
					</tr>	
			
				</table>
			</td>
		</tr>	
			
		<tr>
			<td class="tableBorder" colspan="1"  width="400px">	
				<table >
			
					<tr>
						<td class="fieldLabel"  width="140px">Data do Serviço:</td>
						<td align="left" >{$info.litdata_movimento}</td>
					</tr>
					
					<tr>
						<td class="fieldLabel">Vencimento:</td>
						<td align="left">{$info.data_vencimento}</td>					
					</tr>
					
				
				</table>
			</td>
			<td align="left" colspan="1" class="tableBorder" >
				<table >
				
						<tr>	
							<td class="fieldLabel"  align="left">
								RECEBEMOS:
 	 
							</td>
						</tr>

						<tr >
							<td align="center"> 
								____/____/____ 
								<br>&nbsp;  
							</td>
						</tr>
						<tr>
							<td align="center">
								___________________________________________________<br>
									{if $info.cliente_destino.nome_cliente} 
                    {$info.cliente_destino.nome_cliente} 
                  {else}
                    SOS PRESTADORA DE SERVIÇO LTDA
                  {/if}
							
              </td>
						</tr>
				</table>
			</td>
		</tr>		
			
		</tbody>		
	</table>
{/capture}

{$smarty.capture.recibo}
<hr style="margin:0.5cm 0 0.5cm 0;">
{$smarty.capture.recibo}
</body>

</html>
