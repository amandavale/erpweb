{include file="com_cabecalho_relatorio.tpl"}

<script type="text/javascript" src="{$conf.addr}/common/js/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/jquery.tablesorter.min.js"></script>
<!--{*  Chama o script para ordenar a tabela  *}-->
<script language="javascript">
{literal}
$(document).ready(function()
	{ 
        $("#tbl_movimento").tablesorter(); 
    } 
);
{/literal}
</script>

    <br><br>
  	<table class="" width="900px" align="center">
		<tr>
			<td colspan="9" align="center"><strong><h2>Movimentação Financeira</h2></strong></td>
		</tr>				
	</table>
	<br><br>

{if count($list)}

	<table width="95%" align="center" class="tablesorter" id="tbl_movimento" name="tbl_movimento" >	
		<thead>
			<tr>
				<th align='center'>No</th>
				<th align='center'>Cod</th>
				<th align='center'>Descrição</th>
				<th align='center'>Apto.</th>
				<th align='center'>Cliente de Origem</th>
				<th align='center'>Cliente de Destino</th>
				<th align='center'>Valor</th>
				<th align='center'>Data de Baixa</th>
				<th align='center'>Data de Vencimento</th>
				<th align='center'>Baixado?</th>
			</tr>
		</thead>
		
		<tbody>
		
	      {section name=i loop=$list}
				{if $list[i].idmovimento}
			     <tr  background-color = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
					<td align='center'>&nbsp;{$list[i].index}</td>
					<td align='center'>&nbsp;{$list[i].idmovimento}</td>
					<td>&nbsp;{$list[i].descricao_movimento}</td>
					<td>&nbsp;{$list[i].apto_completo}</td>
					<td>&nbsp;{$list[i].nome_cliente_orig}</td>
					<td>&nbsp;{$list[i].nome_cliente_dest}</td>
					<td align="right" >&nbsp;{$list[i].valor_movimento}</td>
					<td align='center'>&nbsp;{$list[i].data_baixa}</td>
					<td align='center'>&nbsp;{$list[i].data_vencimento}</td>
					<td align='center'>&nbsp;{if $list[i].baixado == 1}Sim{else}Não{/if}</td>
			     </tr>
				{else}
					</tbody> 
    	      	<tfoot>
                <tr>
                  <td colspan="4" align="right">&nbsp;</td>
                  <td align="right"><b>{$list[i].descricao_movimento}</b></td>
                  <td align="right"><b>{$list[i].valor_movimento}</b>&nbsp;</td>
                  <td colspan="4" align="right">&nbsp;</td>
                </tr>
              </tfoot>
				{/if}
	      {/section}

		

  </table>

{else}
  {include file="div_resultado_nenhum.tpl"}
{/if}



{include file="com_rodape_relatorio.tpl"}
