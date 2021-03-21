{include file="com_cabecalho_relatorio_logo.tpl"}

{* Força a impressão para LANDSCAPE no IE *}
<style type="text/css" media="print">

 #landscape {ldelim} 
  width: 100%; 
  height: 100%; 
  margin: 0% 0% 0% 0%; 
  filter: progid:DXImageTransform.Microsoft.BasicImage(Rotation=3);
{rdelim}
</style>

{if count($list)}

	<!--{*  Chama o script para ordenar a tabela  *}-->
		<script language="javascript">
		{literal}

			//Método para não dar conflito com o auto-complete do xajax
			var $j = jQuery.noConflict();
		
			$j(document).ready(function()
				{ 
					$j("#tbl_cliente").tablesorter(); 
			    } 
			);
			
		{/literal}
		</script>

    <div id="landscaoe">

		<table width="95%" align="center" id="tbl_cliente" class="tablesorter">
			<thead>
				<tr>
					<th width="20%" align='center'>Nome do cliente	</th>
					<th width="20%" align='center'>Endere&ccedil;o	</th>
					<th width="10%" align='center'>CNPJ				</th>
					<th width="10%" align='center'>Telefone			</th>
					<th width="10%" align='center'>Nome do contato	</th>
					<th width="10%" align='center'>Celular			</th>
					<th width="10%" align='center'>Valor do Contrato</th>
					<th width="10%" align='center'>Dia de Vencimento do Boleto</th>
				</tr>
			</thead>
			
			<tbody>
		      {section name=i loop=$list}
		        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" style="height:30px;" valign="center" class="borda_inferior">
					<td align='left'  >{$list[i].nome_cliente}		&nbsp;</td>
					<td align='left'  >{$list[i].endereco}			&nbsp;</td>
					<td align='center'>{$list[i].cnpj_cliente}		&nbsp;</td>
	            	<td align='center'>{$list[i].telefone_cliente}	&nbsp;</td>
					<td align='left'  >{$list[i].nome_contato}		&nbsp;</td>
					<td align='center'>{$list[i].celular_contato}   &nbsp;</td>
					<td align='left'  >R$ {$list[i].valor_contrato_cliente}&nbsp;</td>
					<td align='center'  >{$list[i].vencimento_boleto_cliente}&nbsp;</td>
		        </tr>
		      {/section}
	
			</tbody>
	
	  </table>

{else}
  {include file="div_resultado_nenhum.tpl"}
{/if}

</div>

{include file="com_rodape_relatorio.tpl"}
