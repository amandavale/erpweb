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
	  	<td class="descricao_tela" WIDTH="15%">
				{$conf.area}
			</td>
	  	<td class="tela" WIDTH="5%">
				Opera��es:
			</td>
	  	<td class="descricao_tela">
				{if $list_permissao.adicionar == '1'}&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a>{/if}
				
			</td>
		</tr>
	</table>


	{include file="div_erro.tpl"}
    {if $flags.sucesso != ""}
  	{include file="div_resultado_inicio.tpl"}
  		{$flags.sucesso}
  	{include file="div_resultado_fim.tpl"}
	{/if}	
  {if $flags.action == "listar"}



		{if count($list)}
		
			<p align="center">Listando {$conf.area} de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p>
		
			<table width="95%" align="center">
			
				
				<tr>
					<th align='center'>No</th>
					<th align='center'>Cargo</th>
					
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=adicionar&idcargo={$list[i].idcargo}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=adicionar&idcargo={$list[i].idcargo}">{$list[i].nome_cargo}</a></td>
						
	        </tr>
	        
	        <tr>
	          <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
	        </tr>
	      {/section}

      </table>
      
      <p align="center" id="nav">{$nav}</p>

		{else}
      {include file="div_resultado_nenhum.tpl"}
		{/if}
		

	{elseif $flags.action == "editar"}
	
<br>

   	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idcargo={$info.idcargo}&idprograma={$info.idprograma}" method="post" name = "for" id = "for">
		<table width="100%">
 
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			<tr>
				<td>
				<table class="tb4cantos" width="100%">
				
				<tr>
					<td class="req" align="right" width="50%">Cargo:</td>
					<td>
					{$info.nome_cargo}
					<input type="hidden" id="idcargo" name="idcargo" value="{$info.idcargo}"/>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
   		<tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Tabela de Programas</td>
								
			        </tr>

							<tr>
								<td align="center">

									<div id="div_programa">

										<table width="100%" cellpadding="5">
											<tr>
											<th align='Left' width="60%">Programa</th>
											<th align='Left' width="10%">Pode adicionar?</th>
											<th align='center' width="10%">Pode editar?</th>
											<th align='center' width="10%">Pode excluir?</th>
											<th align='center' width="10%">Pode listar?</th>
											</tr>
										</table>

									</div>

								</td>
							</tr>

						</table>
					</td>
        </tr>
			</table>
			<script type="text/javascript">
			// Inicialmente, preenche todos os fornecedores que fazem parte da filial
			xajax_Insere_Programa_AJAX();
								
			</script>
			<script type="text/javascript">
			// Inicialmente, preenche todos os fornecedores que fazem parte da filial
						
			xajax_Seleciona_Programa_AJAX(xajax.getFormValues('for'));
			
			</script>
			
			
			
			
				

        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="submit" class="botao_padrao" value="ALTERAR">
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&idcargo={$info.idcargo}&idprograma={$info.idprograma}','ATEN��O! Confirma a exclus�o ?'))" >
        	</td>
        </tr>

			</form>
		</table>
	      
	      
	{elseif $flags.action == "adicionar"}

<br>

    <form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for" id = "for">
		<table width="100%">

      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

      <table width="95%" align="center">

					<td colspan="2" align="center">
				<table class="tb4cantos" width="100%">

			   <tr>
					<td class="req" align="right" width="45%">Cargo:</td>
					<td>
						<select name="idcargo" id="idcargo" onchange="xajax_Seleciona_Programa_AJAX(xajax.getFormValues('for'));">
						<option value="">[selecione]</option>
						{html_options values=$list_cargo.idcargo output=$list_cargo.nome_cargo selected=$smarty.post.idcargo}
						</select>
					</td>
				</tr>
				
						</table>
					</td>
        </tr>


				<tr><td>&nbsp;</td></tr>
				<tr><td align="right" width="50%"><div id="mostra" onclick="xajax_Mostra_Div();">&nbsp;&nbsp;&nbsp;&bull;Mostrar</div></td><td align="left"><div onclick="xajax_Esconde_Div();">&nbsp;&nbsp;&nbsp;&bull;Esconder</div></td></tr>


        <tr>
        	
					<td colspan="2" align="center">
					
						<table class="tb4cantos" width="100%">

			        <tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">Tabela de Programas</td>
								
			        </tr>
							<tr>
								<td align="center">

									<div id="div_programa">

										<table width="100%" cellpadding="5">
											<tr>
											<th align='Left' width="55%">Programa</th>
											<th align='center' width="15%"%">Pode adicionar?</th>
											<th align='center' width="10%">Pode editar?</th>
											<th align='center' width="10%">Pode excluir?</th>
											<th align='center' width="10%">Pode listar?</th>
											</tr>
										</table>

									</div>

								</td>
							</tr>

						</table>
					</td>
        </tr>
			</table>


   
		<table align="center">
		 <tr><td>&nbsp;</td></tr>
				<tr>
          <td colspan="2" align="center">

  						<input type='button' class="botao_padrao" value="DEFINIR" name = "Adicionar" id = "Adicionar"
								onClick="xajax_Verifica_Campos_Programa_AJAX(xajax.getFormValues('for'));"
							/>
          </td>
        </tr>
		</table>
				<script type="text/javascript">
			// Inicialmente, cria a tabela de programas
			xajax_Insere_Programa_AJAX();
								
			</script>
      
     </form>
      
 
  {/if}

{/if}

{include file="com_rodape.tpl"}
