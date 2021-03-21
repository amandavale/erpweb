{include file="com_cabecalho_relatorio.tpl"}
  {if $flags.sucesso != ""}
	  	{include file="div_resultado_inicio.tpl"}
	  		{$flags.sucesso}
	  	{include file="div_resultado_fim.tpl"}
		{/if}

		{if count($list)}

		
			<table width="50%" align="center">

				<tr>
					<th align='center'>Código</th>		
					<th align='center'>Tipo</th>
				</tr>

	      {section name=i loop=$list}
	      	
	      	{if $list[i].nivel <= 2}
		      	<tr>
		          <td class="row" height="1"  colspan="9"><br></td>
		        </tr>
		        
		        <tr  bgcolor = "{if $list[i].index % 2 != 0}#F7F7F7{else}WHITE{/if}" >
					<td><b>{$list[i].numero} - {$list[i].nome}</b></td>
			{else}
			
	        	<tr  bgcolor = "{if $list[i].index % 2 != 0}#F7F7F7{else}WHITE{/if}" >
					<td>{$list[i].numero} - {$list[i].nome}</td>
			{/if}
					<td>{$list[i].tipo}</td>
				</tr>		
			
				
	        
	      {/section}

      </table>



		{else}
      		{include file="div_resultado_nenhum.tpl"}
		{/if}


{include file="com_rodape_relatorio.tpl"}
