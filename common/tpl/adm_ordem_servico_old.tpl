{include file="com_cabecalho.tpl"}
<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>
{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}


{if $flags.okay}

	<table class="tb4cantosAzul" width="100%"  border="0" cellpadding="5" cellspacing="0">
		<tr>
	  	<td class="tela" WIDTH="5%" height="20">
				Tela:
			</td>
	  	<td class="descricao_tela" WIDTH="20%">
				{$conf.area}
			</td>
	  	<td class="tela" WIDTH="5%">
				Operações:
			</td>
	  	<td class="descricao_tela">
				{if $list_permissao.adicionar == '1'}&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a>{/if}
				{if $list_permissao.listar == '1'}&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">listar</a>{/if}
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
		
			<p align="center">Listando {$conf.area} de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p>
		
			<table width="95%" align="center">
			
				
				<tr>
					<th align='center'>No</th>
					<th align='center'>Código do CFOP</th>
					<th align='center'>Descrição do CFOP</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcfop={$list[i].idcfop}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcfop={$list[i].idcfop}">{$list[i].codigo}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcfop={$list[i].idcfop}">{$list[i].descricao}</a></td>
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
	
		{include file="div_instrucoes_inicio.tpl"}
      	<li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>
				
    {include file="div_instrucoes_fim.tpl"}

		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idcfop={$info.idcfop}" method="post" name = "for" id = "for">
      		<input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				<tr>
					<td align="right" width="25%">Cliente:</td>
					<td colspan="9" align="left"><input type="hidden" name="idcliente" id="idcliente" value="{$smarty.post.idcliente}" />
						<input type="hidden" name="idcliente_NomeTemp" id="idcliente_NomeTemp" value="{$smarty.post.idcliente_Nome}" /> 
						<input class="ultralarge" type="text" name="idcliente_Nome" id="idcliente_Nome" value="{$smarty.post.idcliente_Nome}" onKeyUp="javascript: VerificaMudancaCampo('idcliente');" />
						<span class="nao_selecionou" id="idcliente_Flag">&nbsp;&nbsp;&nbsp; </span> 
						<script type="text/javascript">
						    new CAPXOUS.AutoComplete("idcliente_Nome", function() {ldelim}
						   	return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente" + "&mostraDetalhes=1";
						    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
						  	// verifica os campos auto-complete
							VerificaMudancaCampo('idcliente');
						</script>
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Descrição do CFOP:</td>
					<td><input class="long" type="text" name="litdescricao" id="litdescricao" maxlength="100" value="{$info.litdescricao}"/></td>
				</tr>
				
				
				

	        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="submit" class="botao_padrao" value="ALTERAR">
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&idcfop={$info.idcfop}','ATENÇÃO! Confirma a exclusão ?'))" >
        	</td>
        </tr>

			</form>
		</table>
	      
	      
	{elseif $flags.action == "adicionar"}

		{include file="div_instrucoes_inicio.tpl"}
      		<li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>
    	{include file="div_instrucoes_fim.tpl"}

		<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_ordem_servico" id = "for_ordem_servico">
			<input type="hidden" name="for_chk" id="for_chk" value="1" />
			<input type="hidden" name="maximoItensOrcamento" id="maximoItensOrcamento" value="{$smarty.post.maximoItensOrcamento}" />
			<table width="90%">
			
					<tr>
						<td align="right" width="25%">Cliente:</td>
						<td colspan="9" align="left">
							<input type="hidden" name="idcliente" id="idcliente" value="{$smarty.post.idcliente}" />
							<input type="hidden" name="idcliente_NomeTemp" id="idcliente_NomeTemp" value="{$smarty.post.idcliente_Nome}" /> 
							<input class="ultralarge" type="text" name="idcliente_Nome" id="idcliente_Nome" value="{$smarty.post.idcliente_Nome}" onKeyUp="javascript: VerificaMudancaCampo('idcliente');" />
							<span  class="nao_selecionou" id="idcliente_Flag">&nbsp;&nbsp;&nbsp;</span>
							
							<a style="margin-left:10px;" class="link_geral" href="#"><img src="{$conf.addr}/common/img/add.gif" />Cadastrar Cliente</a>
							 
						</td>
					</tr>
					<tr>
						<td align="right" width="25%">Solicitado por:</td>
						<td colspan="9" align="left">
							<input type="hidden" name="idsolicitante" id="idsolicitante" value="{$smarty.post.idsolicitante}" />
							<input type="hidden" name="idsolicitante_NomeTemp" id="idsolicitante_NomeTemp" value="{$smarty.post.idsolicitante_Nome}" /> 
							<input class="ultralarge" type="text" name="idsolicitante_Nome" id="idsolicitante_Nome" value="{$smarty.post.idsolicitante_Nome}" onKeyUp="javascript: VerificaMudancaCampo('idsolicitante');" />
							<span  class="nao_selecionou" id="idsolicitante_Flag">&nbsp;&nbsp;&nbsp; </span>
							
							<a style="margin-left:10px;" class="link_geral" href="#"><img src="{$conf.addr}/common/img/add.gif" />Cadastrar Pessoa Física</a>
						</td>
					</tr>
					<tr>
						<td align="right" width="25%">Aberto por:</td>
						<td colspan="9" align="left">
							<input type="hidden" name="idfuncionario" id="idfuncionario" value="{$smarty.post.idfuncionario}" />
							<input type="hidden" name="idfuncionario_NomeTemp" id="idfuncionario_NomeTemp" value="{$smarty.post.idfuncionario_Nome}" /> 
							<input class="large" type="text" name="idfuncionario_Nome" id="idfuncionario_Nome" value="{$smarty.post.idfuncionario_Nome}" onKeyUp="javascript: VerificaMudancaCampo('idfuncionario');" />
							<span  class="nao_selecionou" id="idfuncionario_Flag">&nbsp;&nbsp;&nbsp; </span>
							
							<a style="margin-left:10px;" class="link_geral" href="#"><img src="{$conf.addr}/common/img/add.gif" />Cadastrar Funcionário</a>
						</td>
					</tr>
					
					<tr>
						<td align="right">Previsão</td>
						<td valign="bottom">
							<input class="short" type="text" name="previsao_ordem_servico" id="previsao_ordem_servico" value="{$smarty.post.previsao_ordem_servico}" maxlength='10' onkeydown="mask('previsao_ordem_servico', 'data')" onkeyup="mask('previsao_ordem_servico', 'data')" />
							<img src="{$conf.addr}/common/img/calendar.png" id="img_previsao_ordem_servico" style="cursor: pointer;" />
						</td>
					</tr>
					
					
					<tr>
						<td align="right">Status:</td>
						<td>
							<select onchange="set_campos_execucao()" id="status_ordem_servico">
								<option value="enviar">A Enviar</option>
								<option value="autorizado">Autorizado</option>
								<option value="execucao">Execução</option>
							</select>
							
							<div  id="div_status_ordem_servico" style="display:none;">
								
								<select onchange="set_status_campo()" id="programacao_status">
									<option value=""></option>
									<option value="programado">Programado para o dia:</option>
									<option value="adiantado">Adiantado para o dia:</option>
									<option value="adiado">Adiado</option>
								</select>
								
								<div id="div_data_status" style="display:none;">
									<input class="short" type="text" name="programado_data" id="programado_data" value="{$smarty.post.programado_data}" maxlength='10' onkeydown="mask('programado_data', 'data')" onkeyup="mask('programado_data', 'data')" />
									<img src="{$conf.addr}/common/img/calendar.png" id="img_programado_data" style="cursor: pointer;" />
								</div>
								
								<div id="div_motivo_status" style="display:none;">
									Motivo: <input type="text" name="" id="motivo_adiado" class="long" />
								</div>
								
							</div>
						</td>
					</tr>
					<tr><td colspan="2"><h4 style="text-align:center;">Materiais</h4></td></tr>
					<tr>
						<td align="right">Produto:</td>
						<td align="left">
							<input type="hidden" name="idproduto_Tipo" id="idproduto_Tipo" value="" />
							<input type="hidden" name="idproduto" id="idproduto" value="{$smarty.post.idproduto}" />
							<input type="hidden" name="idproduto_NomeTemp" id="idproduto_NomeTemp" value="{$smarty.post.idproduto_NomeTemp}" />
							<input class="ultralarge" type="text" name="idproduto_Nome" id="idproduto_Nome" value="{$smarty.post.idproduto_Nome}"
								onKeyUp="javascript:
									VerificaMudancaCampo('idproduto');
								"
							/>
							<span class="nao_selecionou" id="idproduto_Flag">
								&nbsp;&nbsp;&nbsp;
							</span>
						</td>
					</tr>
					
					<tr>
						<td align="right"><label style="margin-left:10px;" for="idfuncionario">Executado por:</label></td>
						<td align="left">
							
							<input type="hidden" name="idfuncionario" id="idfuncionario" value="{$smarty.post.idfuncionario}" />
							<input type="hidden" name="idfuncionario_NomeTemp" id="idfuncionario_NomeTemp" value="{$smarty.post.idfuncionario_Nome}" /> 
							<input class="long" type="text" name="idfuncionario_Nome" id="idfuncionario_Nome" value="{$smarty.post.idfuncionario_Nome}" onKeyUp="javascript: VerificaMudancaCampo('idfuncionario');" />
							<span  class="nao_selecionou" id="idfuncionario_Flag">&nbsp;&nbsp;&nbsp; </span>
							
						<label style="margin-left:10px;" for="idfuncionario">Tipo de Serviço:</label>
							<input type="hidden" name="idfuncionario" id="idfuncionario" value="{$smarty.post.idfuncionario}" />
							<input type="hidden" name="idfuncionario_NomeTemp" id="idfuncionario_NomeTemp" value="{$smarty.post.idfuncionario_Nome}" /> 
							<input class="long" type="text" name="idfuncionario_Nome" id="idfuncionario_Nome" value="{$smarty.post.idfuncionario_Nome}" onKeyUp="javascript: VerificaMudancaCampo('idfuncionario');" />
							<span  class="nao_selecionou" id="idfuncionario_Flag">&nbsp;&nbsp;&nbsp; </span>
						</td>
					</tr>
												
						

					<script type="text/javascript">
					    new CAPXOUS.AutoComplete("idproduto_Nome", function() {ldelim}
					    	return "produto_ajax.php?ac=busca_produto_encartelamento&typing=" + this.text.value + "&campoID=idproduto" + "&tipoPreco=B";// + document.getElementById('tipoPreco').value;
					    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
					
					  // verifica os campos auto-complete
						VerificaMudancaCampo('idproduto');
					</script>
					<tr>
						<td align="right">Quantidade</td>
						<td align="left">
						<input class="short" type="text" name="qtd_produto" id="qtd_produto" value="{$smarty.post.qtd_produto}" maxlength='10' onkeydown="FormataValor('qtd_produto')" onkeyup="FormataValor('qtd_produto')" />
						<input type='button' class="botao_padrao" value="Inserir produto" name = "botaoInserirProduto" id = "botaoInserirProduto" onClick="xajax_Insere_Produto_Encartelamento_AJAX(xajax.getFormValues('for_ordem_servico'));" style="margin-top:10px;" />
						</td>
					</tr>
					
					<tr><td>&nbsp;</td></tr>
	
	
        			<tr>
						<td colspan="2" align="center">
							<table class="tb4cantos" width="90%" align="center">
	
				        		<tr bgcolor="#F7F7F7">
									<td colspan="9" align="center">Tabela de Produtos do Orçamento
										<input type="hidden" name="total_produtos" id="total_produtos" value="0" />
									</td>
				        		</tr>
	
								<tr>
									<td align="center">
	
										<div id="div_produtos">
											<table width="100%" cellpadding="5">
												<tr>
													<th align='left' width="5%">Cód.</th>
													<th align='left' width="35%">Produto</th>
													<th align='center' width="5%">Un.</th>
													<th align='center' width="10%">Qtd.</th>
													<th align='center' width="10%">Preço Un.(R$)</th>
													<th align='center' width="10%">Desc.(%)</th>
													<th align='center' width="10%">Preço(R$)</th>
													<th align='center' width="10%">Total(R$)</th>
													
													<th align='center' width="5%">Excluir?</th>
												</tr>
											</table>
	
										</div>
									</td>
								</tr>
	
								<script type="text/javascript">
								  // Inicialmente, preenche todos os produtos que fazem parte do orçamento
									xajax_Seleciona_Produtos_AJAX('{$info.idorcamento}', 'editarNF', document.getElementById('tipoOrcamento').value);
								</script>
	
	
							</table>
						</td>
        			</tr>
	
					<tr>
						<td colspan="2" align="center">
							<input type='Submit'  class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar" style="margin-top:50px;" />
						</td>
			        </tr>
			</table>
		</form>
		<script type="text/javascript">
					
		    new CAPXOUS.AutoComplete("idfuncionario_Nome", function() {ldelim}
		   	return "funcionario_ajax.php?ac=busca_funcionario&typing=" + this.text.value + "&campoID=idfuncionario";
		    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
		    
		    new CAPXOUS.AutoComplete("idsolicitante_Nome", function() {ldelim}
		   	return "cliente_ajax.php?ac=busca_cliente_fisico&typing=" + this.text.value + "&campoID=idsolicitante";
		    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
		  	
		    new CAPXOUS.AutoComplete("idcliente_Nome", function() {ldelim}
		   	return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente";
		    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
		    
		    // verifica os campos auto-complete
		    VerificaMudancaCampo('idfuncionario');
			VerificaMudancaCampo('idsolicitante');
			VerificaMudancaCampo('idcliente');
		
			{literal}
				
				Calendar.setup(
					{
						inputField : "previsao_ordem_servico", // ID of the input field
						ifFormat : "%d/%m/%Y", // the date format
						button : "img_previsao_ordem_servico", // ID of the button
						align  : "cR"  // alinhamento
					}
				);
			
				Calendar.setup(
					{
						inputField : "programado_data", // ID of the input field
						ifFormat : "%d/%m/%Y", // the date format
						button : "img_programado_data", // ID of the button
						align  : "cR"  // alinhamento
					}
				);
				
				function set_campos_execucao(){
					
					display = $('status_ordem_servico').value == 'execucao' ? 'inline' : 'none'; 
					$('div_status_ordem_servico').style.display   = display;
					
				}
				
				function set_status_campo(){
					
					status = $('programacao_status').value; 
					
					if(status == ''){
						$('div_data_status').style.display   = 'none';
						$('div_motivo_status').style.display = 'none';
					}
					else if(status == 'adiado'){
						$('div_data_status').style.display   = 'none';
						$('div_motivo_status').style.display = 'inline';
					}
					else{
						$('div_data_status').style.display   = 'inline';
						$('div_motivo_status').style.display = 'none';
					}
					
				}
			{/literal}
			
			
		</script>
						

  {/if}

{/if}

{include file="com_rodape.tpl"}