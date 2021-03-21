{include file="com_cabecalho_relatorio_logo.tpl"}


{if count($list)}

		<!--{*  Chama o script para ordenar a tabela  *}-->
		{literal}
		<script language="javascript">

			//Método para não dar conflito com o auto-complete do xajax
			var $j = jQuery.noConflict();
		
			$j(document).ready(function()
				{ 
					$j("#tbl_cliente").tablesorter(); 
			    } 
			);
			

		</script>
		{/literal}
									
		<table width="95%" align="center" class="tablesorter" id="tbl_cliente">
		<thead>
				<tr>
					<th width="30%" class="header" align='center'>Nome</th>
					<th width="50%" class="header" align='center'>Endereço</th>
					<th width="10%" class="header" align='center'>Telefone</th>
					<th width="10%" class="header" align='center'>Celular</th>
				</tr>
			</thead>
		<tbody>
	      {section name=i loop=$list}
	        <tr class="borda_inferior">
    				<td>{$list[i].nome_cliente}&nbsp;</td>
    				<td>{$list[i].endereco}&nbsp;</td>
				    <td>{$list[i].telefone_cliente}&nbsp;</td>
				    <td>{$list[i].celular_cliente}&nbsp;</td>
	        </tr>
	      {/section}
		</tbody>

  </table>

{else}
  {include file="div_resultado_nenhum.tpl"}
{/if}



{include file="com_rodape_relatorio.tpl"}
