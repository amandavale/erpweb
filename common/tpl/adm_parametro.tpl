{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.oaky}{include file="div_erro.tpl"}{/if}


{if $flags.okay}


	<table class="tb4cantosAzul" width="100%"  border="0" cellpadding="5" cellspacing="0">
		<tr>
	  	<td class="tela" WIDTH="5%" height="20">
				Tela:
			</td>
	  	<td class="descricao_tela" WIDTH="10%">
				{$conf.area}
			</td>
	  	<td class="tela" WIDTH="5%">
				Operações:
			</td>
	  	<td class="descricao_tela">
	  	{if $list_permissao.editar == '1'} &nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac={$flags.destino}">Editar parâmetros</a></li>{/if}
			</td>
		</tr>
	</table>
	


	{include file="div_erro.tpl"}
	
  {if $flags.action == "listar"}

    {if $flags.sucesso != ""}
	  	{include file="div_resultado_inicio.tpl"}
	  		{$flags.sucesso}
	  	{include file="div_resultado_fim.tpl"}
		{/if}

		{if count($list)}
		

	      {section name=i loop=$list}

					<br>

					<table class="tb4cantos" width="95%" align="center" cellpadding="5" cellspacing="5">
						<tr>
							<th align='right'>Validade do Orçamento (em dias):</th>
							<td width="50%"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idparametro={$list[i].idparametro}">{$list[i].validadeOrcamento}</a></td>
						</tr>
      		</table>

					<br>
      		
      		<table class="tb4cantos" width="95%" align="center" cellpadding="5" cellspacing="5">
						<tr>
							<th align='right'>Nº máximo de Produtos por Orçamento:</th>
							<td width="50%"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idparametro={$list[i].idparametro}">{$list[i].maximoItensOrcamento}</a></td>
						</tr>
      		</table>

					<br>

      		<table class="tb4cantos" width="95%" align="center" cellpadding="5" cellspacing="5">
						<tr>
							<th align='right'>Percentual máximo de desconto global no Orçamento (%):</th>
							<td width="50%"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idparametro={$list[i].idparametro}">{$list[i].descontoMaximoOrcamento}</a></td>
						</tr>
      		</table>

					<br>

      		<table class="tb4cantos" width="95%" align="center" cellpadding="5" cellspacing="5">
						<tr>
							<th align='right'>Limite de crédito padrão (R$):</th>
							<td width="50%"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idparametro={$list[i].idparametro}">{$list[i].limiteCreditoPadrao}</a></td>
						</tr>
      		</table>

					<br>

      		<table class="tb4cantos" width="95%" align="center" cellpadding="5" cellspacing="5">
						<tr>
							<th align='right'>Juros mensal padrão do Parcelamento (% a.m.):</th>
							<td width="50%"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idparametro={$list[i].idparametro}">{$list[i].jurosPadraoParcelamento}</a></td>
						</tr>
      		</table>

					<br>

      		<table class="tb4cantos" width="95%" align="center" cellpadding="5" cellspacing="5">
						<tr>
							<th align='right'>Juros diários padrão de Atraso (% a.d.):</th>
							<td width="50%"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idparametro={$list[i].idparametro}">{$list[i].jurosPadraoAtraso}</a></td>
						</tr>
      		</table>
      		
      		<br>
      		
      		<table class="tb4cantos" width="95%" align="center" cellpadding="5" cellspacing="5">
						<tr>
							<th align='right'>Juros diários padrão de Desconto (% a.d.):</th>
							<td width="50%"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idparametro={$list[i].idparametro}">{$list[i].jurosPadraoDesconto}</a></td>
						</tr>
      		</table>
      		
      		<br>


      		<table class="tb4cantos" width="95%" align="center" cellpadding="5" cellspacing="5">
						<tr>
							<th align='right'>Porcentual máximo de atualização nos preços por seção (%):</th>
							<td width="50%"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idparametro={$list[i].idparametro}">{$list[i].porcentagem_maxima}</a></td>
						</tr>
      		</table>
				
					<br>
      		
					<table class="tb4cantos" width="95%" align="center" cellpadding="5" cellspacing="5">
						<tr>
							<th align='right'>Prazo máximo para o cancelamento de uma entrada (em dias):</th>
							<td width="50%"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idparametro={$list[i].idparametro}">{$list[i].limite_cancelamento}</a></td>
						</tr>
      		</table>

					<br>

					<table class="tb4cantos" width="95%" align="center" cellpadding="5" cellspacing="5">
						<tr>
							<th align='right'>Modelo Padrão da nota fiscal:</th>
							<td width="50%"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idparametro={$list[i].idparametro}">{$list[i].modeloPadraoNota}</a></td>
						</tr>
      		</table>

					<br>

					<table class="tb4cantos" width="95%" align="center" cellpadding="5" cellspacing="5">
						<tr>
							<th align='right'>Série Padrão da nota fiscal:</th>
							<td width="50%"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idparametro={$list[i].idparametro}">{$list[i].seriePadraoNota}</a></td>
						</tr>
      		</table>



	      {/section}


		{else}
      {include file="div_resultado_nenhum.tpl"}
		{/if}
		

	{elseif $flags.action == "editar"}
	
		{include file="div_instrucoes_inicio.tpl"}
      	<li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>
				
    {include file="div_instrucoes_fim.tpl"}

		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idparametro={$info.idparametro}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				<tr>
					<td width="50%" align="right">Valiade do Orçamento (em dias):</td>
					<td>
						<input class="short" type="text" name="numvalidadeOrcamento" id="numvalidadeOrcamento" value="{$info.numvalidadeOrcamento}" maxlength='10' onkeydown="FormataInteiro('numvalidadeOrcamento')" onkeyup="FormataInteiro('numvalidadeOrcamento')" />
					</td>
				</tr>
				
				<tr>
					<td width="50%" align="right">Nº máximo de Itens no Orçamento:</td>
					<td>
						<input class="short" type="text" name="nummaximoItensOrcamento" id="nummaximoItensOrcamento" value="{$info.nummaximoItensOrcamento}" maxlength='10' onkeydown="FormataInteiro('nummaximoItensOrcamento')" onkeyup="FormataInteiro('nummaximoItensOrcamento')" />
					</td>
				</tr>


				<tr>
					<td width="50%" align="right">Percentual máximo de desconto global no Orçamento (%):</td>
					<td>
						<input class="short" type="text" name="numdescontoMaximoOrcamento" id="numdescontoMaximoOrcamento" value="{$info.numdescontoMaximoOrcamento}" maxlength='10' onkeydown="FormataValor('numdescontoMaximoOrcamento')" onkeyup="FormataValor('numdescontoMaximoOrcamento')" />
					</td>
				</tr>


				<tr>
					<td width="50%" align="right">Limite de crédito padrão (R$):</td>
					<td>
						<input class="short" type="text" name="numlimiteCreditoPadrao" id="numlimiteCreditoPadrao" value="{$info.numlimiteCreditoPadrao}" maxlength='10' onkeydown="FormataValor('numlimiteCreditoPadrao')" onkeyup="FormataValor('numlimiteCreditoPadrao')" />
					</td>
				</tr>


				<tr>
					<td width="50%" align="right">Juros mensal padrão do Parcelamento (% a.m.):</td>
					<td>
						<input class="short" type="text" name="numjurosPadraoParcelamento" id="numjurosPadraoParcelamento" value="{$info.numjurosPadraoParcelamento}" maxlength='10' onkeydown="FormataValor('numjurosPadraoParcelamento')" onkeyup="FormataValor('numjurosPadraoParcelamento')" />
					</td>
				</tr>


				<tr>
					<td width="50%" align="right">Juros diários padrão de Atraso (% a.d.):</td>
					<td>
						<input class="short" type="text" name="numjurosPadraoAtraso" id="numjurosPadraoAtraso" value="{$info.numjurosPadraoAtraso}" maxlength='10' onkeydown="FormataValor('numjurosPadraoAtraso')" onkeyup="FormataValor('numjurosPadraoAtraso')" />
					</td>
				</tr>


				<tr>
					<td width="50%" align="right">Juros diários padrão de Desconto (% a.d.):</td>
					<td>
						<input class="short" type="text" name="numjurosPadraoDesconto" id="numjurosPadraoDesconto" value="{$info.numjurosPadraoDesconto}" maxlength='10' onkeydown="FormataValor('numjurosPadraoDesconto')" onkeyup="FormataValor('numjurosPadraoDesconto')" />
					</td>
				</tr>


				<tr>
					<td width="50%" align="right">Porcentual máximo de atualização nos preços por seção (%):</td>
					<td>
						<input class="short" type="text" name="numporcentagem_maxima" id="numporcentagem_maxima" value="{$info.numporcentagem_maxima}" maxlength='10' onkeydown="FormataValor('numporcentagem_maxima')" onkeyup="FormataValor('numporcentagem_maxima')" />
					</td>
				</tr>

				<tr>
					<td width="50%" align="right">Prazo máximo para o cancelamento de uma entrada (em dias):</td>
					<td>
						<input class="short" type="text" name="numlimite_cancelamento" id="numlimite_cancelamento" value="{$info.numlimite_cancelamento}" maxlength='10' onkeydown="FormataInteiro('numlimite_cancelamento')" onkeyup="FormataInteiro('numlimite_cancelamento')"/>
					</td>
				</tr>

				<tr>
					<td width="50%" align="right">Modelo Padrão da nota fiscal:</td>
					<td>
						<input class="short" type="text" name="litmodeloPadraoNota" id="litmodeloPadraoNota" value="{$info.litmodeloPadraoNota}" maxlength='10'/>
					</td>
				</tr>

				<tr>
					<td width="50%" align="right">Série Padrão da nota fiscal:</td>
					<td>
						<input class="short" type="text" name="litseriePadraoNota" id="litseriePadraoNota" value="{$info.litseriePadraoNota}" maxlength='10'/>
					</td>
				</tr>


        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="submit" class="botao_padrao" value="ALTERAR">
        	</td>
        </tr>

			</form>
		</table>
	      
	      
	{elseif $flags.action == "adicionar"}

		{include file="div_instrucoes_inicio.tpl"}
      	<li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>
      	
    {include file="div_instrucoes_fim.tpl"}

		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			
				
				<tr>
					<td width="50%" align="right">Valiade do Orçamento (em dias):</td>
					<td>
						<input class="short" type="text" name="validadeOrcamento" id="validadeOrcamento" value="{$smarty.post.validadeOrcamento}" maxlength='10' onkeydown="FormataInteiro('validadeOrcamento')" onkeyup="FormataInteiro('validadeOrcamento')" />
					</td>
				</tr>

				<tr>
					<td width="50%" align="right">Nº máximo de Itens no Orçamento / NF:</td>
					<td>
						<input class="short" type="text" name="maximoItensOrcamento" id="maximoItensOrcamento" value="{$smarty.post.maximoItensOrcamento}" maxlength='10' onkeydown="FormataInteiro('maximoItensOrcamento')" onkeyup="FormataInteiro('maximoItensOrcamento')" />
					</td>
				</tr>

				<tr>
					<td width="50%" align="right">Percentual máximo de desconto global no Orçamento (%):</td>
					<td>
						<input class="short" type="text" name="descontoMaximoOrcamento" id="descontoMaximoOrcamento" value="{$smarty.post.descontoMaximoOrcamento}" maxlength='10' onkeydown="FormataValor('descontoMaximoOrcamento')" onkeyup="FormataValor('descontoMaximoOrcamento')" />
					</td>
				</tr>


				<tr>
					<td width="50%" align="right">Limite de crédito padrão (R$):</td>
					<td>
						<input class="short" type="text" name="limiteCreditoPadrao" id="limiteCreditoPadrao" value="{$smarty.post.limiteCreditoPadrao}" maxlength='10' onkeydown="FormataValor('limiteCreditoPadrao')" onkeyup="FormataValor('limiteCreditoPadrao')" />
					</td>
				</tr>


				<tr>
					<td width="50%" align="right">Juros mensal padrão do Parcelamento (% a.m.):</td>
					<td>
						<input class="short" type="text" name="jurosPadraoParcelamento" id="jurosPadraoParcelamento" value="{$smarty.post.jurosPadraoParcelamento}" maxlength='10' onkeydown="FormataValor('jurosPadraoParcelamento')" onkeyup="FormataValor('jurosPadraoParcelamento')" />
					</td>
				</tr>


				<tr>
					<td width="50%" align="right">Juros diários padrão de Atraso (% a.d.):</td>
					<td>
						<input class="short" type="text" name="jurosPadraoAtraso" id="jurosPadraoAtraso" value="{$smarty.post.jurosPadraoAtraso}" maxlength='10' onkeydown="FormataValor('jurosPadraoAtraso')" onkeyup="FormataValor('jurosPadraoAtraso')" />
					</td>
				</tr>
				
				<tr>
					<td width="50%" align="right">Juros diários padrão de Desconto (% a.d.):</td>
					<td>
						<input class="short" type="text" name="jurosPadraoDesconto" id="jurosPadraoDesconto" value="{$smarty.post.jurosPadraoDesconto}" maxlength='10' onkeydown="FormataValor('jurosPadraoDesconto')" onkeyup="FormataValor('jurosPadraoDesconto')" />
					</td>
				</tr>


				<tr>
					<td width="50%" align="right">Porcentual máximo de atualização nos preços por seção(%):</td>
					<td>
						<input class="short" type="text" name="porcentagem_maxima" id="porcentagem_maxima" value="{$smarty.post.porcentagem_maxima}" maxlength='10' onkeydown="FormataValor('porcentagem_maxima')" onkeyup="FormataValor('porcentagem_maxima')" />
					</td>
				</tr>
				
				<tr>
					<td width="50%" align="right">Prazo máximo para o cancelamento de uma entrada (em dias):</td>
					<td>
						<input class="short" type="text" name="limite_cancelamento" id="limite_cancelamento" value="{$smarty.post.limite_cancelamento}" maxlength='10' onkeydown="FormataInteiro('limite_cancelamento')" onkeyup="FormataInteiro('limite_cancelamento')" />
					</td>
				</tr>

				<tr>
					<td width="50%" align="right">Modelo Padrão da nota fiscal:</td>
					<td>
						<input class="short" type="text" name="modeloPadraoNota" id="modeloPadraoNota" value="{$smarty.post.modeloPadraoNota}" maxlength='10'/>
					</td>
				</tr>

				<tr>
					<td width="50%" align="right">Série Padrão da nota fiscal:</td>
					<td>
						<input class="short" type="text" name="seriePadraoNota" id="seriePadraoNota" value="{$smarty.post.seriePadraoNota}" maxlength='10'/>
					</td>
				</tr>


        <tr><td>&nbsp;</td></tr>

				<tr>
          <td colspan="2" align="center">
  						<input type='Submit'  class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar" />
          </td>
        </tr>

				</form>
		</table>


  {/if}

{/if}

{include file="com_rodape.tpl"}
