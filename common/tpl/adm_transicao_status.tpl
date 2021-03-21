{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{include file="div_erro.tpl"}


{if $flags.okay}

	<table width="100%"  border="0" cellpadding="0" cellspacing="0">

		<tr>
	  	<td bgcolor="#D1D1D1" WIDTH="100%" height="20">
				<b>{$conf.area}</b>
			</td>
		</tr>

	</table>

  <dir>
		<li>&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a></li>
  	<li>&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">listar</a></li>
  </dir>

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
					<th align='center'>Status</th>
					<th align='center'>Ordem de Serviço</th>
					<th align='center'>Data e Hora</th>
					<th align='center'>Data de Programação</th>
				</tr>
		      {section name=i loop=$list}
		        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
					<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idtransicao_status={$list[i].idtransicao_status}">{$list[i].index}</a></td>
					<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idtransicao_status={$list[i].idtransicao_status}">{$list[i].idstatus_os_programacao}</a></td>
					<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idtransicao_status={$list[i].idtransicao_status}">{$list[i].descricao_ordem}</a></td>
					<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idtransicao_status={$list[i].idtransicao_status}">{$list[i].data_hora_transicao}</a></td>
					<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idtransicao_status={$list[i].idtransicao_status}">{$list[i].data_programacao}</a></td>
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
    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idtransicao_status={$info.idtransicao_status}" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				

				
				<tr>
					<td align="right">Funcionário:</td>
					<td>
						<select name="numidfuncionario" id="numidfuncionario">
						<option value="">[selecione]</option>
						{html_options values=$list_funcionario.idfuncionario output=$list_funcionario.nome_funcionario selected=$info.numidfuncionario}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">Status:</td>
					<td>
						<select name="numidstatus" id="numidstatus">
						<option value="">[selecione]</option>
						{html_options values=$list_status_os_programacao.idstatus output=$list_status_os_programacao.idstatus_os_programacao selected=$info.numidstatus}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">Ordem de Serviço:</td>
					<td>
						<select name="numidordem_servico" id="numidordem_servico">
						<option value="">[selecione]</option>
						{html_options values=$list_ordem_servico.idordem_servico output=$list_ordem_servico.descricao_ordem selected=$info.numidordem_servico}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">Data e Hora:</td>
					<td>
						<input class="short" type="text" name="data_hora_transicao_D" id="data_hora_transicao_D" value="{$info.data_hora_transicao_D}" maxlength='10' onkeydown="mask('data_hora_transicao_D', 'data')" onkeyup="mask('data_hora_transicao_D', 'data')" /> (dd/mm/aaaa)
					</td>
				</tr>
				
				<tr>
					<td align="right">Data e Hora:</td>
					<td>
						<input class="short" type="text" name="data_hora_transicao_H" id="data_hora_transicao_H" value="{$info.data_hora_transicao_H}" maxlength='8' onkeydown="mask('data_hora_transicao_H', 'hora')" onkeyup="mask('data_hora_transicao_H', 'hora')" /> (hh:mm:ss)
					</td>
				</tr>
				
				<tr>
					<td align="right">Observação:</td>
					<td>
						<textarea name="litobservacao_transicao" id="litobservacao_transicao" rows='6' cols='38'>{$info.litobservacao_transicao}</textarea>
					</td>
				</tr>
				
				<tr>
					<td align="right">Data de Programação:</td>
					<td>
						<input class="short" type="text" name="litdata_programacao" id="litdata_programacao" value="{$info.litdata_programacao}" maxlength='10' onkeydown="mask('litdata_programacao', 'data')" onkeyup="mask('litdata_programacao', 'data')" /> (dd/mm/aaaa)
					</td>
				</tr>
				
				<tr>
					<td align="right">Motivo:</td>
					<td><input class="long" type="text" name="litmotivo_programacao" id="litmotivo_programacao" maxlength="200" value="{$info.litmotivo_programacao}"/></td>
				</tr>
				
				
				

        <tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="submit" class="botao_padrao" value="ALTERAR">
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&idtransicao_status={$info.idtransicao_status}','ATENÇÃO! Confirma a exclusão ?'))" >
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
					<td align="right">Funcionário:</td>
					<td>
						<select name="idfuncionario" id="idfuncionario">
						<option value="">[selecione]</option>
						{html_options values=$list_funcionario.idfuncionario output=$list_funcionario.nome_funcionario selected=$smarty.post.idfuncionario}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">Status:</td>
					<td>
						<select name="idstatus" id="idstatus">
						<option value="">[selecione]</option>
						{html_options values=$list_status_os_programacao.idstatus output=$list_status_os_programacao.idstatus_os_programacao selected=$smarty.post.idstatus}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">Ordem de Serviço:</td>
					<td>
						<select name="idordem_servico" id="idordem_servico">
						<option value="">[selecione]</option>
						{html_options values=$list_ordem_servico.idordem_servico output=$list_ordem_servico.descricao_ordem selected=$smarty.post.idordem_servico}
						</select>
					</td>
				</tr>
				
				<tr>
					<td align="right">Data e Hora:</td>
					<td>
						<input class="short" type="text" name="data_hora_transicao_D" id="data_hora_transicao_D" value="{$smarty.post.data_hora_transicao_D}" maxlength='10' onkeydown="mask('data_hora_transicao_D', 'data')" onkeyup="mask('data_hora_transicao_D', 'data')" /> (dd/mm/aaaa)
					</td>
				</tr>
				
				<tr>
					<td align="right">Data e Hora:</td>
					<td>
						<input class="short" type="text" name="data_hora_transicao_H" id="data_hora_transicao_H" value="{$smarty.post.data_hora_transicao_H}" maxlength='8' onkeydown="mask('data_hora_transicao_H', 'hora')" onkeyup="mask('data_hora_transicao_H', 'hora')" /> (hh:mm:ss)
					</td>
				</tr>
				
				<tr>
					<td align="right">Observação:</td>
					<td>
						<textarea name="observacao_transicao" id="observacao_transicao" rows='6' cols='38'>{$smarty.post.observacao_transicao}</textarea>
					</td>
				</tr>
				
				<tr>
					<td align="right">Data de Programação:</td>
					<td>
						<input class="short" type="text" name="data_programacao" id="data_programacao" value="{$smarty.post.data_programacao}" maxlength='10' onkeydown="mask('data_programacao', 'data')" onkeyup="mask('data_programacao', 'data')" /> (dd/mm/aaaa)
					</td>
				</tr>
				
				<tr>
					<td align="right">Motivo:</td>
					<td><input class="long" type="text" name="motivo_programacao" id="motivo_programacao" maxlength="200" value="{$smarty.post.motivo_programacao}"/></td>
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
