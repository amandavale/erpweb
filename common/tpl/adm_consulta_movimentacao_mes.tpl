{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}



{if $flags.okay}

	<table class="tb4cantosAzul" width="100%"  border="0" cellpadding="5" cellspacing="0">
		<tr>
			{if $flags.intrucoes_preenchimento != ""}
		  	<td class="tela" WIDTH="1%" height="20" valign="middle">
		  		<img class="lightbulb" src="{$conf.addr}/common/img/lampada.png" width="16" height="16" border="0" align="middle" onmouseover="pmaTooltip('{$flags.intrucoes_preenchimento}'); return false;" onmouseout="swapTooltip('default'); return false;" />
				</td>
			{/if}
	  	<td class="tela" WIDTH="5%" height="20">
				Tela:
			</td>
	  	<td class="descricao_tela" WIDTH="95%">
				{$conf.area}
			</td>
		</tr>
	</table>


  {if $flags.action == "listar"}



		<br>

  	<form  action="{$smarty.server.PHP_SELF}?ac=listar" method="post" name="for" id="for">
    <input type="hidden" name="for_chk" id="for_chk" value="1" />
			<table width="100%">

        <tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">
			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Selecione o período</b></td>
			        </tr>
              
			        <tr><td></td></tr>
              
							<tr>
								<td align="center" >
									Filial:
									<input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
									{$info_filial.nome_filial}
								</td>
							</tr>


              
               <tr>
                <td colspan="9" align="center" class="req">
                   Data:
                  De: 
                  <input class="short" type="text" name="data_1" id="data_1" value="{$smarty.post.data_1}" maxlength='10' onkeydown="mask('data_1', 'data')" onkeyup="mask('data_1', 'data')" />
                  <img src="{$conf.addr}/common/img/calendar.png" id="img_data_1" style="cursor: pointer;" />
                  <script type="text/javascript">
                    Calendar.setup(
                      {ldelim}
                        inputField : "data_1", // ID of the input field
                        ifFormat : "%d/%m/%Y", // the date format
                        button : "img_data_1", // ID of the button
                        align  : "cR"  // alinhamento
                      {rdelim}
                    );
                  </script>

                  Até:  
                  <input class="short" type="text" name="data_2" id="data_2" value="{$smarty.post.data_2}" maxlength='10' onkeydown="mask('data_2', 'data')" onkeyup="mask('data_2', 'data')" /> 
                  <img src="{$conf.addr}/common/img/calendar.png" id="img_data_2" style="cursor: pointer;" />
                  <script type="text/javascript">
                    Calendar.setup(
                      {ldelim}
                        inputField : "data_2", // ID of the input field
                        ifFormat : "%d/%m/%Y", // the date format
                        button : "img_data_2", // ID of the button
                        align  : "cR"  // alinhamento
                      {rdelim}
                    );
                  </script>
                  (dd/mm/aaaa)
                </td>
              </tr>        
  
        <tr><td>&nbsp;</td></tr>

        <tr>
          <td align="center">
            <input {if $smarty.post.tipo ==1} checked {/if} class="radio" type="radio" name="tipo" id="tipo" value="1" /> Transferência&nbsp;&nbsp;
            <input {if $smarty.post.tipo ==0} checked {/if} class="radio" type="radio" name="tipo" id="tipo" value="0" /> Entrada de Mercadoria
          </td>
        </tr>

        <tr><td>&nbsp;</td></tr>

							<tr>
			        	<td align="center" colspan="2">
									<input type="button" class="botao_padrao" value="Buscar!" name="button" onClick="xajax_Verifica_Campos_Movimentacao_Mes_AJAX(xajax.getFormValues('for'))" />
			        	</td>
			        </tr>

						</table>
					</td>
        </tr>

			</table>
		</form>		


		{if count($list_movimentacao)} 

			<table border="0" width="100%">
		
		
				<tr>
					<th align='center'>Pedido</th>
					<th align='center'>Fornecedor</th>
					<th align='center'>Data da Entrada</th>
					<th align='center'>Nº da Nota</th>
          <th align='center'>Código</th>					
          <th align='center'>Produto</th>
          <th align='center'>Un.</th>  					
          <th align='center'>Qtd.</th>
					<th align='center'>Preço (R$)</th>
          <th align='center'>Total(R$)</th>     
				</tr>
		
		
				{section name=i loop=$list_movimentacao}
		
					<tr>
						<td align='center'><a class='menu_item' href = "{$conf.addr}/admin/entrada.php?ac=editar&idpedido={$list_movimentacao[i].idpedido}">{$list_movimentacao[i].idpedido}</a></td>
						<td align='left'><a class='menu_item' href = "{$conf.addr}/admin/entrada.php?ac=editar&idpedido={$list_movimentacao[i].idpedido}">{$list_movimentacao[i].nome_fornecedor}</a></td>
						<td align='center'><a class='menu_item' href = "{$conf.addr}/admin/entrada.php?ac=editar&idpedido={$list_movimentacao[i].idpedido}">{$list_movimentacao[i].data_entrada}</a></td>
						<td align='center'><a class='menu_item' href = "{$conf.addr}/admin/entrada.php?ac=editar&idpedido={$list_movimentacao[i].idpedido}">{$list_movimentacao[i].numero_nota}</a></td>
            <td align='center'><a class='menu_item' href = "{$conf.addr}/admin/entrada.php?ac=editar&idpedido={$list_movimentacao[i].idpedido}">{$list_movimentacao[i].idproduto}</a></td>      
						<td align='left'><a class='menu_item' href = "{$conf.addr}/admin/entrada.php?ac=editar&idpedido={$list_movimentacao[i].idpedido}">{$list_movimentacao[i].descricao_produto}</a></td>
            <td align='right'><a class='menu_item' href = "{$conf.addr}/admin/entrada.php?ac=editar&idpedido={$list_movimentacao[i].idpedido}">{$list_movimentacao[i].sigla_unidade_venda}</a></td>   						
            <td align='right'><a class='menu_item' href = "{$conf.addr}/admin/entrada.php?ac=editar&idpedido={$list_movimentacao[i].idpedido}">{$list_movimentacao[i].qtd}</a></td>
						<td align='right'><a class='menu_item' href = "{$conf.addr}/admin/entrada.php?ac=editar&idpedido={$list_movimentacao[i].idpedido}">{$list_movimentacao[i].valorUnit}</a></td>	
            <td align='right'><a class='menu_item' href = "{$conf.addr}/admin/entrada.php?ac=editar&idpedido={$list_movimentacao[i].idpedido}">{$list_movimentacao[i].preco_total}</a></td>        
					</tr>
					
					<tr>
						<td class="row" height="1" bgcolor="#999999" colspan="20"></td>
					</tr>
		
				{/section}
		
				<tr><td>&nbsp;</td></tr>

	


			</table>




		{/if}		


    {if count($list_movimentacao2)} 

      <table border="0" width="100%">
    
    
        <tr>
          <th align='center'>Nº da Transferência</th>
          <th align='center'>Data da Transferência</th>             
          <th align='center'>Filial Remetente</th>
          <th align='center'>Código</th>
          <th align='center'>Produto</th>
          <th align='center'>Un.</th>         
          <th align='center'>Qtd.</th>
          <th align='center'>Preço (R$)</th>
          <th align='center'>Total (R$)</th>     
        </tr>
    
    
        {section name=i loop=$list_movimentacao2}
    
          <tr>
            <td align='center'><a class='menu_item' href = "{$conf.addr}/admin/transferencia_filial.php?ac=editar&idtransferencia_filial={$list_movimentacao2[i].idtransferencia_filial}">{$list_movimentacao2[i].idtransferencia_filial}</a></td>
            <td align='center'><a class='menu_item' href = "{$conf.addr}/admin/transferencia_filial.php?ac=editar&idtransferencia_filial={$list_movimentacao2[i].idtransferencia_filial}">{$list_movimentacao2[i].data_transferencia}</a></td>            
            <td align='left'><a class='menu_item' href = "{$conf.addr}/admin/transferencia_filial.php?ac=editar&idtransferencia_filial={$list_movimentacao2[i].idtransferencia_filial}">{$list_movimentacao2[i].filial_remetente}</a></td>
            <td align='center'><a class='menu_item' href = "{$conf.addr}/admin/transferencia_filial.php?ac=editar&idtransferencia_filial={$list_movimentacao2[i].idtransferencia_filial}">{$list_movimentacao2[i].idproduto}</a></td>            
            <td align='left'><a class='menu_item' href = "{$conf.addr}/admin/transferencia_filial.php?ac=editar&idtransferencia_filial={$list_movimentacao2[i].idtransferencia_filial}">{$list_movimentacao2[i].descricao_produto}</a></td>
            <td align='right'><a class='menu_item' href = "{$conf.addr}/admin/transferencia_filial.php?ac=editar&idtransferencia_filial={$list_movimentacao2[i].idtransferencia_filial}">{$list_movimentacao2[i].sigla_unidade_venda}</a></td>            
            <td align='right'><a class='menu_item' href = "{$conf.addr}/admin/transferencia_filial.php?ac=editar&idtransferencia_filial={$list_movimentacao2[i].idtransferencia_filial}">{$list_movimentacao2[i].qtd_transferida}</a></td>
            <td align='right'><a class='menu_item' href = "{$conf.addr}/admin/transferencia_filial.php?ac=editar&idtransferencia_filial={$list_movimentacao2[i].idtransferencia_filial}">{$list_movimentacao2[i].preco_unitario_praticado}</a></td> 
            <td align='right'><a class='menu_item' href = "{$conf.addr}/admin/transferencia_filial.php?ac=editar&idtransferencia_filial={$list_movimentacao2[i].idtransferencia_filial}">{$list_movimentacao2[i].preco_total}</a></td>       
          </tr>
          
          <tr>
            <td class="row" height="1" bgcolor="#999999" colspan="20"></td>
          </tr>
    
        {/section}
    
        <tr><td>&nbsp;</td></tr>

  


      </table>



    {/if}
    

    {if $smarty.post.for_chk && !count($list_movimentacao) && !count($list_movimentacao2)}
    
      {include file="div_resultado_nenhum.tpl"}
    {/if}


		<br>

		<table width="100%" border="0" cellpadding="5" cellspacing="0">
			<tr>
				<td>
					
				</td>
			</tr>
		</table>


  {/if}




{/if}

{include file="com_rodape.tpl"}
