{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{include file="div_erro.tpl"}


{if $flags.okay}

	<script type="text/javascript" src="{$conf.addr}/common/js/selecionar_todos_nenhum.js"></script>

	<table class="tb4cantosAzul" width="100%"  border="0" cellpadding="5" cellspacing="0">
		<tr>
	  		<td class="tela" height="20" width="50px">Tela:</td>
	  		<td class="descricao_tela">{$conf.area}</td>
		</tr>
	</table>

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
					<th align='center'>Nome do cliente</th>
					<th align='center'>Ramo de atividade</th>
					<th align='center'>Telefone</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
	        	
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].nome_cliente}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].descricao_atividade}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].telefone_cliente}</a></td>
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
    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idcliente={$info.idcliente}" method="post" name = "for" id = "for">
    		<input type="hidden" name="for_chk" id="for_chk" value="1" />
							
			<tr>
				<td class="req" align="right">Nome do cliente:</td>
				<td><input class="long" type="text" name="litnome_cliente" id="litnome_cliente" maxlength="100" value="{$info.litnome_cliente}"/></td>
			</tr>
			
			<tr>
				<td class="req" align="right">Ramo de atividade:</td>
				<td>
					<select name="numidramo_atividade" id="numidramo_atividade">
					<option value="">[selecione]</option>
					{html_options values=$list_ramo_atividade.idramo_atividade output=$list_ramo_atividade.descricao_atividade selected=$info.numidramo_atividade}
					</select>
				</td>
			</tr>
			
			<tr>
				<td align="right">Telefone:</td>
				<td>
					<input class="tiny" type="text" name="telefone_cliente_ddd" id="telefone_cliente_ddd" value="{$info.telefone_cliente_ddd}" maxlength='2' />
					<input class="short" type="text" name="littelefone_cliente" id="littelefone_cliente" value="{$info.littelefone_cliente}" maxlength='9'onkeydown="mask('littelefone_cliente', 'tel')" onkeyup="mask('littelefone_cliente', 'tel')" />
				</td>
			</tr>
			
			<tr>
				<td align="right">Fax:</td>
				<td>
					<input class="tiny" type="text" name="fax_cliente_ddd" id="fax_cliente_ddd" value="{$info.fax_cliente_ddd}" maxlength='2' />
					<input class="short" type="text" name="litfax_cliente" id="litfax_cliente" value="{$info.litfax_cliente}" maxlength='9'onkeydown="mask('litfax_cliente', 'tel')" onkeyup="mask('litfax_cliente', 'tel')" />
				</td>
			</tr>
			
			<tr>
				<td align="right">Email:</td>
				<td><input class="long" type="text" name="litemail_cliente" id="litemail_cliente" maxlength="100" value="{$info.litemail_cliente}"/></td>
			</tr>
			
			<tr>
				<td align="right">Site:</td>
				<td><input class="long" type="text" name="litsite_cliente" id="litsite_cliente" maxlength="100" value="{$info.litsite_cliente}"/></td>
			</tr>
			
			<tr>
				<td align="right">Cliente bloqueado ?</td>
				<td>
					<input {if $info.litcliente_bloqueado=="0"}checked{/if} class="radio" type="radio" name="litcliente_bloqueado" id="litcliente_bloqueado" value="0" />Não
					<input {if $info.litcliente_bloqueado=="1"}checked{/if} class="radio" type="radio" name="litcliente_bloqueado" id="litcliente_bloqueado" value="1" />Sim
				</td>
			</tr>
			
			<tr>
				<td align="right">Motivo do bloqueio:</td>
				<td>
					<select name="numidmotivo_bloqueio" id="numidmotivo_bloqueio">
					<option value="">[selecione]</option>
					{html_options values=$list_motivo_bloqueio.idmotivo_bloqueio output=$list_motivo_bloqueio.motivo_bloqueio selected=$info.numidmotivo_bloqueio}
					</select>
				</td>
			</tr>
			
			<tr>
				<td align="right">Data do bloqueio:</td>
				<td>
					<input class="short" type="text" name="litdata_bloqueio_cliente" id="litdata_bloqueio_cliente" value="{$info.litdata_bloqueio_cliente}" maxlength='10' onkeydown="mask('litdata_bloqueio_cliente', 'data')" onkeyup="mask('litdata_bloqueio_cliente', 'data')" /> (dd/mm/aaaa)
				</td>
			</tr>
			
			<tr>
				<td align="right">Endereço do cliente:</td>
				<td>
					<select name="numidendereco_cliente" id="numidendereco_cliente">
					<option value="">[selecione]</option>
					{html_options values=$list_endereco.idendereco_cliente output=$list_endereco.idendereco selected=$info.numidendereco_cliente}
					</select>
				</td>
			</tr>
			
			<tr>
				<td align="center" colspan="2">
					O endereço do cliente é o mesmo endereço da cobrança ?
					<input {if $info.litmesmo_endereco=="0"}checked{/if} class="radio" type="radio" name="litmesmo_endereco" id="litmesmo_endereco" value="0" />Não
					<input {if $info.litmesmo_endereco=="1"}checked{/if} class="radio" type="radio" name="litmesmo_endereco" id="litmesmo_endereco" value="1" />Sim
				</td>
			</tr>
			
			<tr>
				<td align="right">Endereço da cobrança:</td>
				<td>
					<select name="numidendereco_cobranca" id="numidendereco_cobranca">
					<option value="">[selecione]</option>
					{html_options values=$list_endereco.idendereco_cobranca output=$list_endereco.idendereco selected=$info.numidendereco_cobranca}
					</select>
				</td>
			</tr>
			
			<tr>
				<td align="right">Telefone da cobrança:</td>
				<td>
					<input class="tiny" type="text" name="telefone_cobranca_ddd" id="telefone_cobranca_ddd" value="{$info.telefone_cobranca_ddd}" maxlength='2' />
					<input class="short" type="text" name="littelefone_cobranca" id="littelefone_cobranca" value="{$info.littelefone_cobranca}" maxlength='9'onkeydown="mask('littelefone_cobranca', 'tel')" onkeyup="mask('littelefone_cobranca', 'tel')" />
				</td>
			</tr>
			
			<tr>
				<td align="right">Observação:</td>
				<td>
					<textarea name="litobservacao_cliente" id="litobservacao_cliente" rows='6' cols='38'>{$info.litobservacao_cliente}</textarea>
				</td>
			</tr>
			
			<tr>
				<td align="right">Valor do Contrato (R$):</td>
				<td>
					<input class="short" type="text" name="numvalor_contrato_cliente" id="numvalor_contrato_cliente" value="{$info.numvalor_contrato_cliente}" maxlength='10' onkeydown="FormataValor('numvalor_contrato_cliente')" onkeyup="FormataValor('numvalor_contrato_cliente')" />
				</td>
			</tr>
			
			<tr>
				<td align="right">Data de cadastro:</td>
				<td>
					<input class="short" type="text" name="litdata_cadastro_cliente" id="litdata_cadastro_cliente" value="{$info.litdata_cadastro_cliente}" maxlength='10' onkeydown="mask('litdata_cadastro_cliente', 'data')" onkeyup="mask('litdata_cadastro_cliente', 'data')" /> (dd/mm/aaaa)
				</td>
			</tr>
			
			<tr>
				<td align="right">O cliente é um consumidor final ?</td>
				<td>
					<input {if $info.litconsumidor_final=="0"}checked{/if} class="radio" type="radio" name="litconsumidor_final" id="litconsumidor_final" value="0" />Não
					<input {if $info.litconsumidor_final=="1"}checked{/if} class="radio" type="radio" name="litconsumidor_final" id="litconsumidor_final" value="1" />Sim
				</td>
			</tr>
				
        	<tr><td>&nbsp;</td></tr>

			<tr>
        		<td align="center" colspan="2">
        			<input name="Submit" type="submit" class="botao_padrao" value="ALTERAR">
        			<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&idcliente={$info.idcliente}','ATENÇÃO! Confirma a exclusão ?'))" >
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
				<td class="req" align="right">Nome do cliente:</td>
				<td><input class="long" type="text" name="nome_cliente" id="nome_cliente" maxlength="100" value="{$smarty.post.nome_cliente}"/></td>
			</tr>
			
			<tr>
				<td class="req" align="right">Ramo de atividade:</td>
				<td>
					<select name="idramo_atividade" id="idramo_atividade">
					<option value="">[selecione]</option>
					{html_options values=$list_ramo_atividade.idramo_atividade output=$list_ramo_atividade.descricao_atividade selected=$smarty.post.idramo_atividade}
					</select>
				</td>
			</tr>
			
			<tr>
				<td align="right">Telefone:</td>
				<td>
					<input class="tiny" type="text" name="telefone_cliente_ddd" id="telefone_cliente_ddd" value="{$smarty.post.telefone_cliente_ddd}" maxlength='2' />
					<input class="short" type="text" name="telefone_cliente" id="telefone_cliente" value="{$smarty.post.telefone_cliente}" maxlength='9'onkeydown="mask('telefone_cliente', 'tel')" onkeyup="mask('telefone_cliente', 'tel')" />
				</td>
			</tr>
			
			<tr>
				<td align="right">Fax:</td>
				<td>
					<input class="tiny" type="text" name="fax_cliente_ddd" id="fax_cliente_ddd" value="{$smarty.post.fax_cliente_ddd}" maxlength='2' />
					<input class="short" type="text" name="fax_cliente" id="fax_cliente" value="{$smarty.post.fax_cliente}" maxlength='9'onkeydown="mask('fax_cliente', 'tel')" onkeyup="mask('fax_cliente', 'tel')" />
				</td>
			</tr>
			
			<tr>
				<td align="right">Email:</td>
				<td><input class="long" type="text" name="email_cliente" id="email_cliente" maxlength="100" value="{$smarty.post.email_cliente}"/></td>
			</tr>
			
			<tr>
				<td align="right">Site:</td>
				<td><input class="long" type="text" name="site_cliente" id="site_cliente" maxlength="100" value="{$smarty.post.site_cliente}"/></td>
			</tr>
			
			<tr>
				<td align="right">Cliente bloqueado ?</td>
				<td>
					<input {if $smarty.post.cliente_bloqueado=="0"}checked{/if} class="radio" type="radio" name="cliente_bloqueado" id="cliente_bloqueado" value="0" />Não
					<input {if $smarty.post.cliente_bloqueado=="1"}checked{/if} class="radio" type="radio" name="cliente_bloqueado" id="cliente_bloqueado" value="1" />Sim
				</td>
			</tr>
			
			<tr>
				<td align="right">Motivo do bloqueio:</td>
				<td>
					<select name="idmotivo_bloqueio" id="idmotivo_bloqueio">
					<option value="">[selecione]</option>
					{html_options values=$list_motivo_bloqueio.idmotivo_bloqueio output=$list_motivo_bloqueio.motivo_bloqueio selected=$smarty.post.idmotivo_bloqueio}
					</select>
				</td>
			</tr>
			
			<tr>
				<td align="right">Data do bloqueio:</td>
				<td>
					<input class="short" type="text" name="data_bloqueio_cliente" id="data_bloqueio_cliente" value="{$smarty.post.data_bloqueio_cliente}" maxlength='10' onkeydown="mask('data_bloqueio_cliente', 'data')" onkeyup="mask('data_bloqueio_cliente', 'data')" /> (dd/mm/aaaa)
				</td>
			</tr>
			
			<tr>
				<td align="right">Endereço do cliente:</td>
				<td>
					<select name="idendereco_cliente" id="idendereco_cliente">
					<option value="">[selecione]</option>
					{html_options values=$list_endereco.idendereco_cliente output=$list_endereco.idendereco selected=$smarty.post.idendereco_cliente}
					</select>
				</td>
			</tr>
			
			<tr>
				<td align="center" colspan="2">
					O endereço do cliente é o mesmo endereço da cobrança ?
					<input {if $smarty.post.mesmo_endereco=="0"}checked{/if} class="radio" type="radio" name="mesmo_endereco" id="mesmo_endereco" value="0" />Não
					<input {if $smarty.post.mesmo_endereco=="1"}checked{/if} class="radio" type="radio" name="mesmo_endereco" id="mesmo_endereco" value="1" />Sim
				</td>
			</tr>
			
			<tr>
				<td align="right">Endereço da cobrança:</td>
				<td>
					<select name="idendereco_cobranca" id="idendereco_cobranca">
					<option value="">[selecione]</option>
					{html_options values=$list_endereco.idendereco_cobranca output=$list_endereco.idendereco selected=$smarty.post.idendereco_cobranca}
					</select>
				</td>
			</tr>
			
			<tr>
				<td align="right">Telefone da cobrança:</td>
				<td>
					<input class="tiny" type="text" name="telefone_cobranca_ddd" id="telefone_cobranca_ddd" value="{$smarty.post.telefone_cobranca_ddd}" maxlength='2' />
					<input class="short" type="text" name="telefone_cobranca" id="telefone_cobranca" value="{$smarty.post.telefone_cobranca}" maxlength='9'onkeydown="mask('telefone_cobranca', 'tel')" onkeyup="mask('telefone_cobranca', 'tel')" />
				</td>
			</tr>
			
			<tr>
				<td align="right">Observação:</td>
				<td>
					<textarea name="observacao_cliente" id="observacao_cliente" rows='6' cols='38'>{$smarty.post.observacao_cliente}</textarea>
				</td>
			</tr>
			
			<tr>
				<td align="right">Valor do Contrato (R$):</td>
				<td>
					<input class="short" type="text" name="valor_contrato_cliente" id="valor_contrato_cliente" value="{$smarty.post.valor_contrato_cliente}" maxlength='10' onkeydown="FormataValor('valor_contrato_cliente')" onkeyup="FormataValor('valor_contrato_cliente')" />
				</td>
			</tr>
			
			<tr>
				<td align="right">Data de cadastro:</td>
				<td>
					<input class="short" type="text" name="data_cadastro_cliente" id="data_cadastro_cliente" value="{$smarty.post.data_cadastro_cliente}" maxlength='10' onkeydown="mask('data_cadastro_cliente', 'data')" onkeyup="mask('data_cadastro_cliente', 'data')" /> (dd/mm/aaaa)
				</td>
			</tr>
			
			<tr>
				<td align="right">O cliente é um consumidor final ?</td>
				<td>
					<input {if $smarty.post.consumidor_final=="0"}checked{/if} class="radio" type="radio" name="consumidor_final" id="consumidor_final" value="0" />Não
					<input {if $smarty.post.consumidor_final=="1"}checked{/if} class="radio" type="radio" name="consumidor_final" id="consumidor_final" value="1" />Sim
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

	{elseif $flags.action == "desbloquear_clientes"}

	    {if $flags.sucesso != ""}
		  	{include file="div_resultado_inicio.tpl"}
	  			{$flags.sucesso}
	  		{include file="div_resultado_fim.tpl"}
		{/if}

		<tr>
			<td colspan="2" align="center">
				<table class="tb4cantos" width="100%">
		
					<tr>
						<td>
							<table width="100%">
							<form enctype="multipart/form-data" action="{$smarty.server.PHP_SELF}?ac=desbloquear_clientes" method="post" name = "for_retorno" id = "for_retorno">
								<input type="hidden" name="for_chk" id="for_chk" value="1" />
					
								{if count($clientes_bloqueados)}
			
								<tr>
									<td colspan="2">
										<table width="100%" align="center">
											<tr>
												<th width="10%">
													<a class='menu_item' style="color:white;" href="javascript:selecionar_todos();">Marcar todos</a>
													<br />
													<a class='menu_item' style="color:white" href="javascript:selecionar_nenhum()">Desmarcar todos</a> 
												
												</th>
												<th align='center' colspan="2">Cliente</th>
												<th align='center'>Data de bloqueio</th>
												<th align='center'>Motivo de bloqueio</th>
											</tr>
		
											{section name=i loop=$clientes_bloqueados}

								        	<tr  bgcolor = "{if $clientes_bloqueados[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

								        		<td align="center">
								        			<input type="checkbox" class="selecao_movimento" name="desbloquear_clientes[{$clientes_bloqueados[i].idcliente}]" 
								        					id="id_cliente_{$clientes_bloqueados[i].idcliente}"/>
								        		</td>
								        		<td align="right" style="padding-right:5px">{$clientes_bloqueados[i].idcliente}</td>
								        		<td>{$clientes_bloqueados[i].nome_cliente}</td>
								        		<td align="center">{$clientes_bloqueados[i].data_bloqueio_cliente}</td>
								        		<td>{$clientes_bloqueados[i].motivo_bloqueio}</td>
								        	</tr>
								        
								        	<tr class="{$classe}">
								          		<td class="row" height="1" bgcolor="#999999" colspan="5"></td>
								        	</tr>
								      		{/section}
							      		</table>
									</td>
								</tr>

								<tr><td>&nbsp;</td></tr>
			
								<tr>
									<td colspan="2" align="center">
										<input type='submit' class="botao_padrao" value="DESBLOQUEAR" name = "Desbloquear" id = "Desbloquear" />
										<input type='submit' class="botao_padrao" value="CANCELAR" name = "Cancelar" id = "Cancelar" />
									</td>
								</tr>
			
								{/if}
							</form>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>



  	{/if}

{/if}

{include file="com_rodape.tpl"}
