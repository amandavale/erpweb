{include file="com_cabecalho_relatorio.tpl"}
 <br><br>

{if count($list)}
		
	      <center><h1>RELATÓRIO DE CONDOMÍNIOS</h1></center>
        
        {if $smarty.post.tipo_impressao == "S"}        
              	<!--{*  Chama o script para ordenar a tabela  *}-->
          			<script language="javascript">
          			{literal}
          	
          				//Método para não dar conflito com o auto-complete do xajax
          				var $j = jQuery.noConflict();
          			
          				$j(document).ready(function()
          					{ 
          						$j("#tbl_condominio").tablesorter(); 
          				    } 
          				);
          				
          			{/literal}
          			</script>


					<table width="80%" align="center" class="tablesorter" id="tbl_condominio">
						<thead>
							<th class="header" width="250" align="left"  >Nome			  </th>
							<th class="header" width="250" align="left"  >Endereço		  </th>
							<th class="header" width="100" align="left"  >CNPJ			  </th>
							<th class="header" width="100" align="center">Taxa Admin. (R$)</th>
							<th class="header" width="100" align="center">Faxina (R$)	  </th>
							<th class="header" width="100" align="center">Vigia (R$)	  </th>
						</thead>
					
						<tbody>
							{section name=i loop=$list}
							<tr >
								<td>{$list[i].nome_cliente}</td>
								<td>{$list[i].endereco_string}</td>
								<td>{$list[i].cnpj}</td>
								<td align="right">{$list[i].valAdm}</td>
								<td align="right">{$list[i].valFaxina}</td>
								<td align="right">{$list[i].valVigia}</td>
							</tr>
							{/section}
						</tbody>
					</table>
					
					
					{else} 
					
					
					
					{section name=i loop=$list}

					<table width="95%" align="center">
  		      
    		    	<tr>
    					<th align='center' width='24%' colspan="9">{$list[i].nome_cliente}</th>
    				</tr>
    				{if $list[i].endereco_string != ""}
    				<tr bgcolor="#C7C7C7">
    					<td>Endere&ccedil;o:</td>
              {if $includeCpf}
    					<td colspan="7">{$list[i].endereco_string}</td>
              {else}
              <td colspan="6">{$list[i].endereco_string}</td>
              {/if}
    				</tr>
    				{/if}
    				<tr bgcolor="#C7C7C7">
    					<td width="8%">Apartamento</td>
    					<td width="8%">Ocupação</td>

              {if $includeCpf}
              <td width="23%">Proprietário</td>
              <td width="9%">CPF do Proprietário</td>
              {else}
              <td width="32%">Proprietário</td>
              {/if}
    					
    					<td width="10%">Telefone</td>

              {if $includeCpf}
              <td width="23%">Inquilino</td>
              <td width="9%">CPF do Inquilino</td>
              {else}
              <td width="32%">Inquilino</td>
              {/if}

    					<td width="10%">Telefone</td>
    				</tr>
    						
    				{foreach from=$list[i].apartamentos item=apartamento}
    					<tr bgcolor = "{if $apartamento.index % 2 == 0}D7D7D7{else}WHITE{/if}">
    						<td>
    							{$apartamento.apto}
    						</td>
    						<td>
    							{if $apartamento.sit_apt == 'P'}
                    Propriet&aacute;rio
                  {elseif $apartamento.sit_apt == 'I'}
                    Inquilino
                  {else}
                    Vazio
                  {/if}
    						</td>
    						<td>
    							{$apartamento.nome_proprietario}
    						</td>
                {if $includeCpf}
                <td>
                  {$apartamento.cpf_proprietario}
                </td>                
                {/if}

    						<td>
    							{$apartamento.telefone_proprietario}
    						</td>
    						<td>
    							{$apartamento.nome_inquilino}
    						</td>
                {if $includeCpf}
                <td>
                  {$apartamento.cpf_inquilino}
                </td>                
                {/if}

    						<td>
    							{$apartamento.telefone_inquilino}
    						</td>
    					</tr>
    				{foreachelse}
        				<tr>
        					<td colspan="4" align="center">Nâo há dados de apartamentos cadastrados para este condomínio.</td>
        				</tr>
    				{/foreach}
    	
    		        <tr>
    		          <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
    		        </tr>
    	        
    	        </table>
    	        
    	        <br /> <br />
    	      {/section}

          {/if}


{else}
  {include file="div_resultado_nenhum.tpl"}
{/if}



{include file="com_rodape_relatorio.tpl"}
