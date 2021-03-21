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
			        	<td align="center" colspan="2">
									<input type="button" class="botao_padrao" value="Buscar!" name="button1" onClick="xajax_Verifica_Campos_Movimentacao_Mes_AJAX(xajax.getFormValues('for'));"/>
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
          <th align='center'>Nº da Nota</th>
          <th align='center'>Tipo</th>          
          <th align='center'>Data da Venda</th>             
          <th align='center'>Funcionário Responsável</th>
          <th align='center'>Cliente</th>
          <th align='center'>Valor da Venda (R$)</th>
        </tr>
    
    
        {section name=i loop=$list_movimentacao}
    
          <tr>
            <td align='center'><a class='menu_item' href = "{$conf.addr}/admin/orcamento.php?ac=editarNF&idorcamento={$list_movimentacao[i].idorcamento}">{$list_movimentacao[i].numeroNota}</a></td>
            <td align='left'><a class='menu_item' href = "{$conf.addr}/admin/orcamento.php?ac=editarNF&idorcamento={$list_movimentacao[i].idorcamento}">{$list_movimentacao[i].tipoOrcamento}</a></td>      
            <td align='center'><a class='menu_item' href = "{$conf.addr}/admin/orcamento.php?ac=editarNF&idorcamento={$list_movimentacao[i].idorcamento}">{$list_movimentacao[i].datahoraCriacaoNF}</a></td>           
            <td align='left'><a class='menu_item' href = "{$conf.addr}/admin/orcamento.php?ac=editarNF&idorcamento={$list_movimentacao[i].idorcamento}">{$list_movimentacao[i].nome_funcionario}</a></td>
            <td align='left'><a class='menu_item' href = "{$conf.addr}/admin/orcamento.php?ac=editarNF&idorcamento={$list_movimentacao[i].idorcamento}">{$list_movimentacao[i].nome_cliente}</a></td>
            <td align='right'><a class='menu_item' href = "{$conf.addr}/admin/orcamento.php?ac=editarNF&idorcamento={$list_movimentacao[i].idorcamento}">{$list_movimentacao[i].valor_total_nota}</a></td> 
          </tr>
					
					<tr>
						<td class="row" height="1" bgcolor="#999999" colspan="20"></td>
					</tr>
		
				{/section}
		
				<tr><td>&nbsp;</td></tr>

	




		{else}

			{if $smarty.post.for_chk}
      
      	{include file="div_resultado_nenhum.tpl"}
			{/if}

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

