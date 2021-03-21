{include file="com_cabecalho.tpl"}
<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/tabs.js"></script>
{include file="div_login.tpl"}

{include file="div_erro.tpl"}
<style>
{literal}
	/* Sobrescreve a propriedade do sigedeco.css */
	.ultralarge { width: 616px; }
	.long { width: 282px; }
	
	.subtable tbody tr:nth-child(odd){
	   background-color: #DDD ;
	   
	}
	.subtable tbody td, #tbl_material_os tfoot td{
		padding: 8px;
	}
{/literal}	
</style>

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
	  	<td class="descricao_tela" WIDTH="20%">
				{$conf.area}
			</td>
	  	<td class="tela" WIDTH="5%">
				Operações:
			</td>
	  	<td class="descricao_tela">
				{*if $list_permissao.adicionar == '1'*}
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a>
				{*/if}
				{if $list_permissao.listar == '1'*}
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">listar</a>
				{*/if*}
			</td>
		</tr>
	</table>

  {if $flags.action == "listar"}

	    {if $flags.sucesso != ""}
		  	{include file="div_resultado_inicio.tpl"}
		  		{$flags.sucesso}
		  	{include file="div_resultado_fim.tpl"}
		{/if}
		
		
		
		<form name="frm_ordem_servico" id="frm_ordem_servico" method="get" action="" >
			<input type="hidden" name="for_chk" id="for_chk" value="1" />
			<table style="margin:50px 20%; width:80%;">
				<tr>
					<td align="right">N&ordm; da Ordem</td>
					<td><input type="text" name="num_ordem" id="num_ordem" value="{$smarty.get.num_ordem}" class="short" maxlength='10' onkeydown="FormataInteiro('num_ordem')" onkeyup="FormataInteiro('num_ordem')" /></td>
				</tr>
				
				<tr>
					<td align="right">Previsão de:</td>
					<td>
						<input class="short" type="text" name="previsao_servico_de" id="previsao_servico_de" value="{$smarty.get.previsao_servico_de}" maxlength='10' onkeydown="mask('previsao_servico_de', 'data')" onkeyup="mask('previsao_servico_de', 'data')" />
						<img src="{$conf.addr}/common/img/calendar.png" id="img_previsao_servico_de" style="cursor: pointer;" />
						
						<span style="margin:0 10px;">Até</span>
						
						<input class="short" type="text" name="previsao_servico_ate" id="previsao_servico_ate" value="{$smarty.get.previsao_servico_ate}" maxlength='10' onkeydown="mask('previsao_servico_ate', 'data')" onkeyup="mask('previsao_servico_ate', 'data')" />
						<img src="{$conf.addr}/common/img/calendar.png" id="img_previsao_servico_ate" style="cursor: pointer;" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Descrição</td>
					<td><input type="text" value="{$smarty.get.descricao_ordem}" name="descricao_ordem" id="descricao_ordem" class="extralarge" /></td>
				</tr>
				
				<tr>
					<td align="right">Status:</td>
					<td>
						<select onchange="xajax_Set_Campo_Status_AJAX(this.value);" id="status_ordem_servico" name="status_ordem_servico">								
							<option value=""></option>
							{html_options values=$list_status_os.idstatus_os output=$list_status_os.nome_status_os selected=$smarty.get.status_ordem_servico}
						</select>
							
						<div id="div_status_ordem_servico"  style="display:inline">
							{if $smarty.get.programacao_status != ''}
								<select name="programacao_status" id="programacao_status" onchange="xajax_Set_Campo_Programacao_AJAX(this.value); $('idstatus_os').value='';" >
									{html_options values=$list_programacao_status.idprogramacao_status output=$list_programacao_status.nome_programacao selected=$smarty.get.programacao_status}
								</select>
							{/if}
							<div id="div_campo_complementar" style="display:inline;">

								{if $smarty.get.data_programacao != ''}
									<input class="short" type="text" name="data_programacao" id="data_programacao" value="{$info.status_programacao.data_programacao}" maxlength='10' onkeydown="mask('data_programacao', 'data')" onkeyup="mask('data_programacao', 'data')" onchange="$('idstatus_os').value='';" />
	    							<img src="{$conf.addr}/common/img/calendar.png" id="img_data_programacao" style="cursor: pointer;" />
	    							<script type="text/javascript">
	    								Calendar.setup(
	    									{ldelim}
	    										inputField : "data_programacao", // ID of the input field
	    										ifFormat : "%d/%m/%Y", // the date format
	    										button : "img_data_programacao", // ID of the button
	    										align  : "cR"  // alinhamento
	    									{rdelim}
	    								);
	    							</script>
								{elseif $smarty.get.motivo_programacao != ''}
									Motivo: <input type="text" name="motivo_programacao" id="motivo_programacao" value="{$info.status_programacao.motivo_programacao}" class="long" onchange="$('idstatus_os').value='';" />
								{/if}
								
							</div>
						</div>
					</td>
				</tr>
				
				<tr>
					<td align="right">Cliente:</td>
					<td colspan="9" align="left">
						<input type="hidden" name="idcliente" id="idcliente" value="{$smarty.get.idcliente}" />
						<input type="hidden" name="idcliente_NomeTemp" id="idcliente_NomeTemp" value="{$smarty.get.idcliente_Nome}" /> 
						<input class="ultralarge" type="text" name="idcliente_Nome" id="idcliente_Nome" value="{$smarty.get.idcliente_Nome}" onKeyUp="javascript: VerificaMudancaCampo('idcliente');" />
						<span  class="nao_selecionou" id="idcliente_Flag">&nbsp;&nbsp;&nbsp;</span>
					</td>
				</tr>
				<tr>
					<td align="right">Tipo de Serviço</td>
					<td>
						<select name="idtipo_servico" id="idtipo_servico">
							<option value=""></option>
							{html_options values=$list_tipo_servico.idtipo_servico output=$list_tipo_servico.nome_servico selected=$smarty.get.idtipo_servico}
						</select>
					</td>		
				</tr>

				<tr>
					<td align="right" >Resultados por p&aacute;gina</td>
					<td align="left" ><input type="text" class="tiny" name="rppg" id="rppg" value="{if !$smarty.get.rppg}{$conf.rppg}{else}{$smarty.get.rppg}{/if}"></td>
				</tr>

				<tr>
					<td colspan="2" >
						<input name="btn_buscar" type="submit" class="botao_padrao" value="Buscar"  style="margin-left:25%" />
						<input name="btn_limpar" type="button" onclick="window.location ='{$conf.addr}/admin/ordem_servico.php?ac=listar'" class="botao_padrao" value="Limpar Filtros"  style="margin-left:10px" />
					</td>
				</tr>				 	 	 	 	
			</table>
			
		</form>

		{if count($list)}
		
		
			<table width="95%" align="center">
			
				
				<tr>
					<th align='center'>N&ordm; OS</th>
					<th align='center'>Previsão</th>
					<th align='center'>Descrição</th>
					<th align='center'>Status</th>
					<th align='center'>Cliente</th>
					<th align='center'>Serviço</th>
				</tr>
		
			    {section name=i loop=$list}
			        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" style="height: 25px;">
			        	<td align='center'><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idordem_servico={$list[i].idordem_servico}">{$list[i].num_ordem_servico}</a></td>
						<td align="center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idordem_servico={$list[i].idordem_servico}">{$list[i].previsao_servico}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idordem_servico={$list[i].idordem_servico}">{$list[i].descricao_ordem}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idordem_servico={$list[i].idordem_servico}">{$list[i].ultimo_status}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idordem_servico={$list[i].idordem_servico}">{$list[i].nome_cliente}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idordem_servico={$list[i].idordem_servico}">{$list[i].nome_servico}</a></td>
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
		
		
		<script type="text/javascript">
		    new CAPXOUS.AutoComplete("idcliente_Nome", function() {ldelim}
		   	return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente";
		    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
		    
		    // verifica os campos auto-complete
			VerificaMudancaCampo('idcliente');
			
			Calendar.setup(
				{ldelim}
					inputField : "previsao_servico_de", // ID of the input field
					ifFormat : "%d/%m/%Y", // the date format
					button : "img_previsao_servico_de", // ID of the button
					align  : "cR"  // alinhamento
				{rdelim}
			);
			
			
			Calendar.setup(
					{ldelim}
						inputField : "previsao_servico_ate", // ID of the input field
						ifFormat : "%d/%m/%Y", // the date format
						button : "img_previsao_servico_ate", // ID of the button
						align  : "cR"  // alinhamento
					{rdelim}
				);
			
		</script>
		

	{elseif $flags.action == "editar"}


		{include file="div_instrucoes_inicio.tpl"}
      		<li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>
    	{include file="div_instrucoes_fim.tpl"}


		<ul class="anchors">
			<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados da Ordem de Serviço</a></li>
			<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Histórico de Status</a></li>
		</ul>
		
    	<form  action="" method="post" name = "for_ordem_servico" id = "for_ordem_servico">
      		<input type="hidden" name="for_chk" id="for_chk" value="1" />
      		<input type="hidden" name="idstatus_os" id="idstatus_os" value="{$info.idstatus_os}" />
      	
      		{************************************}
			{* TAB 0 *}
			{************************************}

			<div id="tab_0" class="anchor">

      		
      		
	      		<table width="100%">		
	      		
	      			<tr>
						<td align="right">Status:</td>
						<td>
							<select onchange="xajax_Set_Campo_Status_AJAX(this.value); $('idstatus_os').value='';" id="status_ordem_servico" name="status_ordem_servico">								
								{html_options values=$list_status_os.idstatus_os output=$list_status_os.nome_status_os selected=$info.status_programacao.idstatus_os}
							</select>
								
							<div id="div_status_ordem_servico"  style="display:inline">
								{if $info.status_programacao.idprogramacao_status}
									<select name="programacao_status" id="programacao_status" onchange="xajax_Set_Campo_Programacao_AJAX(this.value); $('idstatus_os').value='';" >
										{foreach from=$list_programacao_status item=status}
								  			<option {if $status.idprogramacao_status == $info.status_programacao.idprogramacao_status}selected="selected"{/if} value="{$status.idprogramacao_status}">{$status.nome_programacao}</option>
								  		{/foreach}
									</select>
								{/if}
								<div id="div_campo_complementar" style="display:inline;">

									{if $info.status_programacao.campo_complementar == 'data_programacao'}
										<input class="short" type="text" name="data_programacao" id="data_programacao" value="{$info.status_programacao.data_programacao}" maxlength='10' onkeydown="mask('data_programacao', 'data')" onkeyup="mask('data_programacao', 'data')" onchange="$('idstatus_os').value='';" />
		    							<img src="{$conf.addr}/common/img/calendar.png" id="img_data_programacao" style="cursor: pointer;" />
		    							<script type="text/javascript">
		    								Calendar.setup(
		    									{ldelim}
		    										inputField : "data_programacao", // ID of the input field
		    										ifFormat : "%d/%m/%Y", // the date format
		    										button : "img_data_programacao", // ID of the button
		    										align  : "cR"  // alinhamento
		    									{rdelim}
		    								);
		    							</script>
									{elseif $info.status_programacao.campo_complementar == 'motivo_programacao'}
										Motivo: <input type="text" name="motivo_programacao" id="motivo_programacao" value="{$info.status_programacao.motivo_programacao}" class="long" onchange="$('idstatus_os').value='';" />
									{/if}
									
								</div>
							</div>
						</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					
					<tr>
						<td align="right"><b>Ordem N&ordm;:</b></td>
						<td>{$info.num_ordem_servico}</td>
					</tr>
					<tr>
						<td align="right"><b>Aberto por:</b></td>
						<td>{$info.aberto_por}</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td class="req" align="right">Descrição:</td>
						<td><input class="ultralarge" type="text" name="descricao_ordem" id="descricao_ordem" maxlength="100" value="{$info.descricao_ordem}"/></td>
					</tr>
					
					<tr>
						<td align="right" class="req" width="25%">Cliente:</td>
						<td colspan="9" align="left">
							<input type="hidden" name="numidcliente" id="numidcliente" value="{$info.idcliente}" />
							<input type="hidden" name="numidcliente_NomeTemp" id="numidcliente_NomeTemp" value="{$info.idcliente_Nome}" /> 
							<input class="ultralarge" type="text" name="numidcliente_Nome" id="numidcliente_Nome" value="{$info.idcliente_Nome}" onKeyUp="javascript: VerificaMudancaCampo('numidcliente');" />
							<span  class="nao_selecionou" id="numidcliente_Flag">&nbsp;&nbsp;&nbsp;</span>
							
							<a onclick="abrirLightbox(cliente_conteudo);"  style="margin-left:10px;" class="link_geral" href="JavaScript:window.scrollTo(0,0);"><img src="{$conf.addr}/common/img/add.gif" />Cadastrar Cliente</a>
							 
						</td>
					</tr>
					<tr>
						<td align="right">Endereço do Cliente:</td>
						<td>
							<textarea maxlength="1500" name="endereco_cliente" id="endereco_cliente" rows='2' cols='38' style="height:50px" class="ultralarge">{$info.endereco_cliente}</textarea>
						</td>
					</tr>
					
					<tr>
							<td align="right" class="req" width="25%">Solicitado por:</td>
							<td colspan="9" align="left">
								<input type="hidden" name="numidsolicitante" id="numidsolicitante" value="{$info.idsolicitante}" />
								<input type="hidden" name="numidsolicitante_NomeTemp" id="numidsolicitante_NomeTemp" value="{$info.idsolicitante_Nome}" /> 
								<input class="ultralarge" type="text" name="numidsolicitante_Nome" id="numidsolicitante_Nome" value="{$info.idsolicitante_Nome}" onKeyUp="javascript: VerificaMudancaCampo('numidsolicitante');" />
								<span  class="nao_selecionou" id="numidsolicitante_Flag">&nbsp;&nbsp;&nbsp;</span>
								
								<a onclick="abrirLightbox(solicitante_conteudo)" style="margin-left:10px;" class="link_geral" href="JavaScript:window.scrollTo(0,0);"><img src="{$conf.addr}/common/img/add.gif" />Cadastrar Pessoa Física</a>
							</td>
						</tr>
					
					<tr>
						<td align="right" class="req">Previsão:</td>
						<td>
							<input class="short" type="text" name="previsao_servico" id="previsao_servico" value="{$info.previsao_servico}" maxlength='10' onkeydown="mask('previsao_servico', 'data')" onkeyup="mask('previsao_servico', 'data')" />
							<img src="{$conf.addr}/common/img/calendar.png" id="img_previsao_servico" style="cursor: pointer;" />
					
							<span style="margin-left:10px;"  class="req">Tipo de Serviço:</span>
							<select name="idtipo_servico" id="idtipo_servico">
							<option value="">[selecione]</option>
							{html_options values=$list_tipo_servico.idtipo_servico output=$list_tipo_servico.nome_servico selected=$info.idtipo_servico}
							</select>		
						</td>
					</tr>
					
					<tr>
						<td align="right">Solicitações:</td>
						<td>
							<textarea maxlength="1500" name="observacao_ordem" id="observacao_ordem" rows='2' cols='38' style="height:150px" class="ultralarge">{$info.observacao_ordem}</textarea>
						</td>
					</tr>
					
	        		<tr><td>&nbsp;</td></tr>
	        		
	        		
	        		
	        		<tr><td colspan="2"><h4 style="text-align:center;">Dados do Fornecedor</h4></td></tr>
	        		
	        			<tr>
							<td align="right"><label style="margin-left:10px;" for="idfornecedor">Fornecedor:</label></td>
							<td align="left">
								
								<input type="hidden" name="idfornecedor" id="idfornecedor" value="{$info.idfornecedor}" />
								<input type="hidden" name="idfornecedor_NomeTemp" id="idfornecedor_NomeTemp" value="{$info.dados_fornecedor.nome_fornecedor}" /> 
								<input class="ultralarge" type="text" name="idfornecedor_Nome" id="idfornecedor_Nome" value="{$info.dados_fornecedor.nome_fornecedor}" onKeyUp="javascript: VerificaMudancaCampo('idfornecedor');" />
								<span  class="nao_selecionou" id="idfornecedor_Flag">&nbsp;&nbsp;&nbsp;</span>

								<a onclick="abrirLightbox(fornecedor_conteudo)" style="margin-left:10px;" class="link_geral" href="JavaScript:window.scrollTo(0,0);"><img src="{$conf.addr}/common/img/add.gif" />Cadastrar Fornecedor</a>
							</td>
						</tr>
						
						<tr>
							<td align="right">Endereço do Fornecedor:</td>
							<td>
								<textarea maxlength="1500" name="endereco_fornecedor" id="endereco_fornecedor" rows='2' cols='38' style="height:50px" class="ultralarge">{$info.endereco_fornecedor}</textarea>
							</td>
						</tr>
						
						<tr><td>&nbsp;</td></tr>
						
						<tr><td colspan="2"><h4 style="text-align:center;">Serviços / Materiais Utilizados</h4></td></tr>
						
						<tr>
							<td align="right">Serviço / Material:</td>
							<td align="left">
								<input type="hidden" name="idproduto_Tipo" id="idproduto_Tipo" value="" />
								<input type="hidden" name="idproduto" id="idproduto" value="{$info.idproduto}" />
								<input type="hidden" name="idproduto_NomeTemp" id="idproduto_NomeTemp" value="{$info.idproduto_NomeTemp}" />
								<input class="ultralarge" type="text" name="idproduto_Nome" id="idproduto_Nome" value="{$info.idproduto_Nome}"
									onKeyUp="javascript:
										VerificaMudancaCampo('idproduto');
									"
								/>
								<span class="nao_selecionou" id="idproduto_Flag">&nbsp;&nbsp;&nbsp;</span>
								
								<a onclick="abrirLightbox(produto_conteudo)" style="margin-left:10px;" class="link_geral" href="JavaScript:window.scrollTo(0,0);"><img src="{$conf.addr}/common/img/add.gif" />Cadastrar Serviço / Material</a>
								
							</td>
						</tr>
						
						<tr>
							<td align="right">Nota Fiscal</td>
							<td>
								<input class="short" type="text" name="numero_nf" id="numero_nf" value="{$info.numero_nf}" maxlength='10' onkeydown="FormataInteiro('numero_nf')" onkeyup="FormataInteiro('numero_nf')" />
							</td>
						</tr>
						
						<tr>
							<td align="right">Quantidade</td>
							<td align="left">
							<input class="short" type="text" name="qtd_produto" id="qtd_produto" value="{$info.qtd_produto}" maxlength='10' onkeydown="FormataValor('qtd_produto')" onkeyup="FormataValor('qtd_produto')" />
							
							<span style="margin-left:10px;">Valor Unitário: R$</span>
							<input class="short" type="text" name="valor_unitario" id="valor_unitario" value="{$info.valor_unitario}" maxlength='10' onkeydown="FormataValor('valor_unitario')" onkeyup="FormataValor('valor_unitario')" />
							
							<input style="margin-left:10px;" type='button' class="botao_padrao" value="Inserir" name = "botaoInserirProduto" id = "botaoInserirProduto" onClick="xajax_Insere_Material_OS_AJAX(xajax.getFormValues('for_ordem_servico'));" style="margin-top:10px;" />
							</td>
						</tr>
						
						<tr><td>&nbsp;</td></tr>
		
		
	        			<tr>
							<td colspan="2" align="center">
								<table class="tb4cantos subtable" width="70%" align="center"  name="tbl_material_os" id="tbl_material_os">
									<thead>
										<tr>
											<th align='left' width="10%">Nota Fiscal</th>
											<th align='left' width="25%">Fornecedor</th>
											<th align='left' width="25%">Serviço / Material</th>
											<th align='center' width="5%">Un.</th>
											<th align='center' width="10%">Qtd.</th>
											<th align='center' width="10%">Preço Un.(R$)</th>
											<th align='center' width="10%">Total(R$)</th>
											<th align='center' width="5%">Excluir</th>
										</tr>
										</thead>
										<tbody id="tbl_materiais">
											 {foreach from=$info.materiais item=material }
												<tr id="linha_produto_{$material.idproduto}">
													<input type="hidden" name="material[{$material.idproduto}][qtd_produto]"    value="{$material.qtd_produto}"    />
													<input type="hidden" name="material[{$material.idproduto}][valor_unitario]" value="{$material.valor_unitario}" />
													<input type="hidden" name="material[{$material.idproduto}][idfornecedor]"   value="{$material.idfornecedor}"   />
													<input type="hidden" name="material[{$material.idproduto}][numero_nf]"   	value="{$material.numero_nf}"      />
													
													<td>{$material.numero_nf}</td>
													<td>{$material.nome_fornecedor}</td>
													<td>{$material.descricao_produto}</td>
													<td>{$material.sigla_unidade_venda}</td>
													<td align="right">{$material.qtd_produto}</td>
													<td align="right">{$material.valor_unitario}</td>
													<td align="right">{$material.valor_total}</td>
													<td align='center' ><img onclick="xajax_Remove_Material_OS_AJAX({$material.idproduto});" style="cursor:pointer;cursor:hand;" src="{$conf.addr}/common/img/delete.gif" /></td>
												</tr>
											 {/foreach}
										</tbody>
										<tfoot>
											<tr>
												<td colspan="6">&nbsp;</td>
												<td align="right">
													<b>R$ <span id="valor_total_os">{$info.valor_total_os}</span></b>
													
												</td>
												<td>&nbsp;</td>
											</tr>
										</tfoot>
								</table>
							</td>
	        			</tr>
		    		
					<tr><td>&nbsp;</td></tr>
					<tr>
			          <td colspan="2" align="center">
		          		<input name="imprimir_os" type="button" class="botao_padrao"  value="Imprimir" onClick="window.open('{$smarty.server.PHP_SELF}?ac=editar&idordem_servico={$smarty.get.idordem_servico}&imprimir=1');" />
 						<input type="button" onClick="xajax_Verifica_Campos_OS_AJAX(xajax.getFormValues('for_ordem_servico'));"  class="botao_padrao" value="Salvar" name="editar" id="editar" style="margin-left:10px;" />
		        		<input name="Submit" type="button" class="botao_padrao" value="Excluir" onClick="return(confDelete('for_ordem_servico','{$smarty.server.PHP_SELF}?ac=excluir&idordem_servico={$info.idordem_servico}','ATENÇÃO! Confirma a exclusão ?'))" style="margin-left:10px;" >
			          </td>
			        </tr>
				</table>
				
			</div>
			
			{************************************}
			{* TAB 1 *}
			{************************************}

			<div id="tab_1" class="anchor">
				
				{if count($list_transicao_status)}
		
					<table width="50%" style="margin: 0 auto;" class="subtable">
						<thead>
							<tr>
								<th align='center'>Status</th>
								<th align='center'>Programação</th>
								<!-- <th align='center'>Data Programada / Motivo</th>  -->
								<th align='center'>Data e Hora</th>
								<th align='center'>Funcionário</th>
							</tr>
						</thead>
					  <tbody>
				      {foreach from=$list_transicao_status item=status}
				        <tr>
				        	<td>{$status.nome_status_os}</td>
							<td>{$status.nome_programacao}</td>
						 	<!-- <td>{$status.complementar}</td>  -->
							<td align="center">{$status.data_hora_transicao}</td>
							<td align="center"	>{$status.nome_funcionario}</td>
				        </tr>
				      {/foreach}
				      </tbody>
		      		</table>
		      
		      		<p align="center" id="nav">{$nav}</p>
		
				{else}
		      		{include file="div_resultado_nenhum.tpl"}
				{/if}
		  </div>
			
		</form>
		

		<script type="text/javascript">

			Processa_Tabs(0, 'tab_'); // seta o tab inicial
		    
		    new CAPXOUS.AutoComplete("numidsolicitante_Nome", function() {ldelim}
		   	return "cliente_ajax.php?ac=busca_cliente_fisico&typing=" + this.text.value + "&campoID=numidsolicitante";
		    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
		  	
		    new CAPXOUS.AutoComplete("numidcliente_Nome", function() {ldelim}
		   	return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=numidcliente" + "&inserirEndereco=true";
		    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
		    
		    new CAPXOUS.AutoComplete("idproduto_Nome", function() {ldelim}
	    	return "produto_ajax.php?ac=busca_produto_os&typing=" + this.text.value + "&campoID=idproduto";
	    	{rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
	    
		    new CAPXOUS.AutoComplete("idfornecedor_Nome", function() {ldelim}
	    	return "fornecedor_ajax.php?ac=busca_fornecedor&typing=" + this.text.value + "&campoID=idfornecedor"  + "&inserirEndereco=true";
	    	{rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim}); 
		
	  		// verifica os campos auto-complete
			VerificaMudancaCampo('idproduto');
			VerificaMudancaCampo('idfornecedor');
			VerificaMudancaCampo('numidsolicitante');
			VerificaMudancaCampo('numidcliente');
			
			Calendar.setup(
				{ldelim}
					inputField : "previsao_servico", // ID of the input field
					ifFormat : "%d/%m/%Y", // the date format
					button : "img_previsao_servico", // ID of the button
					align  : "cR"  // alinhamento
				{rdelim}
			);
			
		</script>
		
	
	      
	{elseif $flags.action == "adicionar"}

		{include file="div_instrucoes_inicio.tpl"}
      		<li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>
    	{include file="div_instrucoes_fim.tpl"}

		
    	<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_ordem_servico" id = "for_ordem_servico">
      		<input type="hidden" name="for_chk" id="for_chk" value="1" />
      		
      		<table width="100%">
      		
				<tr>
					<td align="right"><b>Aberto por:</b></td>
					<td>{$smarty.session.usr_nom}</td>
				</tr>
				<tr><td>&nbsp;</td></tr>		
				<tr>
					<td class="req" align="right">Descrição:</td>
					<td><input class="ultralarge" type="text" name="descricao_ordem" id="descricao_ordem" maxlength="100" value="{$smarty.post.descricao_ordem}"/></td>
				</tr>
				
				<tr>
					<td align="right" class="req" width="25%">Cliente:</td>
					<td colspan="9" align="left">
						<input type="hidden" name="idcliente" id="idcliente" value="{$smarty.post.idcliente}" />
						<input type="hidden" name="idcliente_NomeTemp" id="idcliente_NomeTemp" value="{$smarty.post.idcliente_Nome}" /> 
						<input class="ultralarge" type="text" name="idcliente_Nome" id="idcliente_Nome" value="{$smarty.post.idcliente_Nome}" onKeyUp="javascript: VerificaMudancaCampo('idcliente');" />
						<span  class="nao_selecionou" id="idcliente_Flag">&nbsp;&nbsp;&nbsp;</span>
						
						<a onclick="abrirLightbox(cliente_conteudo);"  style="margin-left:10px;" class="link_geral" href="JavaScript:window.scrollTo(0,0);"><img src="{$conf.addr}/common/img/add.gif" />Cadastrar Cliente</a>
						 
					</td>
				</tr>
				<tr>
					<td align="right">Endereço do Cliente:</td>
					<td>
						<textarea maxlength="1500" name="endereco_cliente" id="endereco_cliente" rows='2' cols='38' style="height:50px" class="ultralarge">{$info.endereco_cliente}</textarea>
					</td>
				</tr>
				<tr>
						<td align="right" class="req" width="25%">Solicitado por:</td>
						<td colspan="9" align="left">
							<input type="hidden" name="idsolicitante" id="idsolicitante" value="{$smarty.post.idsolicitante}" />
							<input type="hidden" name="idsolicitante_NomeTemp" id="idsolicitante_NomeTemp" value="{$smarty.post.idsolicitante_Nome}" /> 
							<input class="ultralarge" type="text" name="idsolicitante_Nome" id="idsolicitante_Nome" value="{$smarty.post.idsolicitante_Nome}" onKeyUp="javascript: VerificaMudancaCampo('idsolicitante');" />
							<span  class="nao_selecionou" id="idsolicitante_Flag">&nbsp;&nbsp;&nbsp;</span>
							
							<a onclick="abrirLightbox(solicitante_conteudo)" style="margin-left:10px;" class="link_geral" href="JavaScript:window.scrollTo(0,0);"><img src="{$conf.addr}/common/img/add.gif" />Cadastrar Pessoa Física</a>
						</td>
					</tr>
	  			
				<tr>
					<td align="right"  class="req">Previsão:</td>
					<td>
						<input class="short" type="text" name="previsao_servico" id="previsao_servico" value="{$smarty.post.previsao_servico}" maxlength='10' onkeydown="mask('previsao_servico', 'data')" onkeyup="mask('previsao_servico', 'data')" />
						<img src="{$conf.addr}/common/img/calendar.png" id="img_previsao_servico" style="cursor: pointer;" />
						
						<span style="margin-left:10px;"  class="req">Tipo de Serviço:</span>
						<select name="idtipo_servico" id="idtipo_servico">
						<option value="">[selecione]</option>
						{html_options values=$list_tipo_servico.idtipo_servico output=$list_tipo_servico.nome_servico selected=$smarty.post.idtipo_servico}
						</select>
						
					</td>
				</tr>
								
				<tr>
					<td align="right">Solicitações:</td>
					<td>
						<textarea maxlength="1500" name="observacao_ordem" id="observacao_ordem" rows='2' cols='38' style="height:100px" class="ultralarge">{$smarty.post.observacao_ordem}</textarea>
					</td>
				</tr>
        		<tr><td>&nbsp;</td></tr>
        		
       			<tr><td colspan="2"><h4 style="text-align:center;">Dados do Fornecedor</h4></td></tr>
		
       			<tr>
					<td align="right"><label style="margin-left:10px;" for="idfornecedor">Fornecedor:</label></td>
					<td align="left">
						
						<input type="hidden" name="idfornecedor" id="idfornecedor" value="{$smarty.post.idfornecedor}" />
						<input type="hidden" name="idfornecedor_NomeTemp" id="idfornecedor_NomeTemp" value="{$smarty.post.idfornecedor_Nome}" /> 
						<input class="ultralarge" type="text" name="idfornecedor_Nome" id="idfornecedor_Nome" value="{$smarty.post.idfornecedor_Nome}" onKeyUp="javascript: VerificaMudancaCampo('idfornecedor');" />
						<span  class="nao_selecionou" id="idfornecedor_Flag">&nbsp;&nbsp;&nbsp;</span>
						
						<a onclick="abrirLightbox(fornecedor_conteudo)" style="margin-left:10px;" class="link_geral" href="JavaScript:window.scrollTo(0,0);"><img src="{$conf.addr}/common/img/add.gif" />Cadastrar Fornecedor</a>
						
					</td>
				</tr>
				
				<tr>
					<td align="right">Endereço do Fornecedor:</td>
					<td>
						<textarea maxlength="1500" name="endereco_fornecedor" id="endereco_fornecedor" rows='2' cols='38' style="height:50px" class="ultralarge">{$smarty.post.endereco_fornecedor}</textarea>
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr><td colspan="2"><h4 style="text-align:center;">Serviços / Materiais Utilizados</h4></td></tr>
				
					<tr>
						<td align="right">Serviço / Material:</td>
						<td align="left">
							<input type="hidden" name="idproduto_Tipo" id="idproduto_Tipo" value="" />
							<input type="hidden" name="idproduto" id="idproduto" value="{$smarty.post.idproduto}" />
							<input type="hidden" name="idproduto_NomeTemp" id="idproduto_NomeTemp" value="{$smarty.post.idproduto_NomeTemp}" />
							<input class="ultralarge" type="text" name="idproduto_Nome" id="idproduto_Nome" value="{$smarty.post.idproduto_Nome}"
								onKeyUp="javascript:
									VerificaMudancaCampo('idproduto');
								"
							/>
							<span class="nao_selecionou" id="idproduto_Flag">&nbsp;&nbsp;&nbsp;</span>
							
							<a onclick="abrirLightbox(produto_conteudo)" style="margin-left:10px;" class="link_geral" href="JavaScript:window.scrollTo(0,0);"><img src="{$conf.addr}/common/img/add.gif" />Cadastrar Serviço / Material</a>
							
						</td>
					</tr>
				
					<tr>
						<td align="right">Nota Fiscal</td>
						<td>
							<input class="short" type="text" name="numero_nf" id="numero_nf" value="{$info.numero_nf}" maxlength='10' onkeydown="FormataInteiro('numero_nf')" onkeyup="FormataInteiro('numero_nf')" />
						</td>
					</tr>
					
					<tr>
						<td align="right">Quantidade</td>
						<td align="left">
						<input class="short" type="text" name="qtd_produto" id="qtd_produto" value="{$smarty.post.qtd_produto}" maxlength='10' onkeydown="FormataValor('qtd_produto')" onkeyup="FormataValor('qtd_produto')" />
						
						<span style="margin-left:10px;">Valor Unitário: R$</span>
						<input class="short" type="text" name="valor_unitario" id="valor_unitario" value="{$smarty.post.valor_unitario}" maxlength='10' onkeydown="FormataValor('valor_unitario')" onkeyup="FormataValor('valor_unitario')" />
						
						<input style="margin-left:10px;" type='button' class="botao_padrao" value="Inserir" name = "botaoInserirProduto" id = "botaoInserirProduto" onClick="xajax_Insere_Material_OS_AJAX(xajax.getFormValues('for_ordem_servico'));" style="margin-top:10px;" />
						</td>
					</tr>
					
					<tr><td>&nbsp;</td></tr>
		
        			<tr>
						<td colspan="2" align="center">
							<table class="tb4cantos subtable" width="70%" align="center"  name="tbl_material_os" id="tbl_material_os">
								<thead>
									<tr>
										<th align='left'   width="10%">Nota Fiscal</th>
										<th align='left'   width="25%">Fornecedor</th>
										<th align='left'   width="25%">Serviço / Material</th>
										<th align='center' width="10%">Qtd.</th>
										<th align='center' width="5%" >Un.</th>
										<th align='center' width="10%">Preço Un.(R$)</th>
										<th align='center' width="10%">Total(R$)</th>
										<th align='center' width="5%" >Excluir</th>
									</tr>
								</thead>
								<tbody id="tbl_materiais">
									<!-- Materiais inseridos entram aqui via ajax -->
								</tbody>
								<tfoot>
									<tr>
										<td colspan="6">&nbsp;</td>
										<td align="right">
											<b>R$ <span id="valor_total_os">0,00</span></b>
										</td>
										<td>&nbsp;</td>
									</tr>
								</tfoot>
							</table>
						</td>
        			</tr>
	    		
				<tr><td>&nbsp;</td></tr>
				
				<tr>
		          <td colspan="2" align="center">
					<input type="button" onClick="xajax_Verifica_Campos_OS_AJAX(xajax.getFormValues('for_ordem_servico'));"  class="botao_padrao" value="Adicionar" name="adicionar" id="adicionar" style="margin-left:10px;" /> 						
		          </td>
		        </tr>
		        <tr><td>&nbsp;</td></tr>
		        <tr>
					<td colspan="2" align="center">
						<input type="checkbox" checked="checked" id="imprimir_os" name="imprimir_os" /> <label for="imprimir_os">Imprimir Ordem de Serviço</label>
					</td>
				</tr>
			</table>
			
		</form>
		
		
		<script type="text/javascript">

		    
		    new CAPXOUS.AutoComplete("idsolicitante_Nome", function() {ldelim}
		   	return "cliente_ajax.php?ac=busca_cliente_fisico&typing=" + this.text.value + "&campoID=idsolicitante";
		    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
		  	
		    new CAPXOUS.AutoComplete("idcliente_Nome", function() {ldelim}
		   	return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente"  + "&inserirEndereco=true";
		    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
		
		    new CAPXOUS.AutoComplete("idproduto_Nome", function() {ldelim}
	    	return "produto_ajax.php?ac=busca_produto_os&typing=" + this.text.value + "&campoID=idproduto";
	    	{rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
		    
		    new CAPXOUS.AutoComplete("idfornecedor_Nome", function() {ldelim}
	    	return "fornecedor_ajax.php?ac=busca_fornecedor&typing=" + this.text.value + "&campoID=idfornecedor" + "&inserirEndereco=true";
	    	{rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim}); 
		
	  		// verifica os campos auto-complete
			VerificaMudancaCampo('idproduto');
			VerificaMudancaCampo('idfornecedor');
			VerificaMudancaCampo('idsolicitante');
			VerificaMudancaCampo('idcliente');
		
			Calendar.setup(
				{ldelim}
					inputField : "previsao_servico", // ID of the input field
					ifFormat : "%d/%m/%Y", // the date format
					button : "img_previsao_servico", // ID of the button
					align  : "cR"  // alinhamento
				{rdelim}
			);
	
			
		</script>


  {/if}

{/if}



{*<!-- CONTEÙDO PARA CADASTROS RÁPIDOS -->*}

<!--  EXEMPLO:
<p>This is the main content. To display a lightbox click <a href = "javascript:void(0)" onclick = "abrirLightbox(cliente_conteudo)">here</a></p>

<div id="cliente_conteudo" class="white_content">
	Adicionando Cliente... <a href = "javascript:void(0)" onclick = "fecharLightbox(cliente_conteudo)">Close</a>
</div>
 -->



<div id="cliente_conteudo" class="white_content" style="height:300px; padding-top:10px; width:630px;">
	<h3>Cadastrar Cliente</h3>
	{include file="cadastro_rapido/cliente.tpl"}
</div>

<div id="solicitante_conteudo" class="white_content" style="height:250px; padding-top:10px; width:630px;">
	<h3>Cadastrar Cliente</h3>
	{include file="cadastro_rapido/solicitante.tpl"}
</div>


<div id="fornecedor_conteudo" class="white_content" style="height:320px; padding-top:10px; width:630px;">
	<h3>Cadastrar Fornecedor</h3>
	{include file="cadastro_rapido/fornecedor.tpl"}
</div>


<div id="produto_conteudo" class="white_content" style="height:200px; padding-top:10px; width:630px;">
	<h3>Cadastrar Serviço / Material</h3>
	{include file="cadastro_rapido/produto.tpl"}
</div>

<div id="fade" class="black_overlay"></div>


{* *************************************** *}

{include file="com_rodape.tpl"}
