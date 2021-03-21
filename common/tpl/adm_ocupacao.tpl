{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{include file="div_erro.tpl"}


<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/tabs.js"></script>



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
				Operações:
			</td>
	  	<td class="descricao_tela">

	  		{if $smarty.session.idcliente}
				{if $list_permissao.adicionar == '1'}
		  		&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a>
		  		{/if}
				{if $list_permissao.listar == '1'}
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">listar</a>
					<!-- &nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=busca_generica">busca genérica</a>
					&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=busca_parametrizada">busca parametrizada</a> -->
				{/if}
				</td>

				<td class="tela" WIDTH="14%" height="20">
					Condomínio Selecionado:
				</td>

		  		<td class="descricao_tela" WIDTH="30%">
					{$smarty.session.nome_cliente}
					&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=selecionar_condominio">Alterar Seleção</a>
				</td>
			{else}
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=selecionar_condominio">Selecionar Condomínio</a>
			{/if}

		</tr>
	</table>



	<br>

	<div style="width: 100%;">

	  	<ul class="anchors">
			<li><a id="a_tab_0" onclick="" href="{$conf.addr}/admin/apartamento.php?ac=editar&idapartamento={$smarty.session.idapartamento}">Dados do Apartamento</a></li>
			<li><a id="a_tab_1" style="background-color:#FFFFFF;" href="javascript:;">Ocupação</a></li>
		</ul>



		{************************************}
		{* TAB 0 *}
		{************************************}

		<div id="tab_0" class="anchor">

	  	{if $flags.action == "listar"}

	    	{if $flags.sucesso != ""}
			  	{include file="div_resultado_inicio.tpl"}
			  		{$flags.sucesso}
			  	{include file="div_resultado_fim.tpl"}
			{/if}

				<table  width="95%" align="center">
				<tr>
					<td>
						<form name="frm_filtro" id="frm_filtro" method="post" action="{$smarty.server.PHP_SELF}?ac=listar">
							Mostrar:
							<select name="filtro" id="filtro" onchange="submit('frm_filtro')">
								<option value="T" {if $smarty.session.filtro =='T'}selected{/if} id="todos">Todos</option>
								<option value="I" {if $smarty.session.filtro =='I'}selected{/if} id="inquilino">Somente Inquilinos</option>
								<option value="P" {if $smarty.session.filtro =='P'}selected{/if} id="propietario">Somente Proprietários</option>
							</select>
						</form>
					</td>
				</tr>
				</table>

				{if count($list) && !$err}
				
				    
					
					<p align="center">
						Histórico de Ocupação do apartamento <b>{$apartamento}</b><br />
						Listando <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:
					</p>

					<table  width="95%" align="center">


						<tr>
							<th align='left' width="25px">No</th>
							<th align='left' >Cliente</th>
							<th align='left' width="80px">Data Inicial</th>
							<th align='left' width="80px">Data Final</th>
							<th align='left' width="120px">Situação</th>
							<th align='left' align='100px'>Síndico</th>
							
						</tr>

			      		{section name=i loop=$list}
			        		<tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
								<td><a class='menu_item' align='left' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].index}</a></td>
								<td><a class='menu_item' align='left' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].nome_cliente}</a></td>
								<td><a class='menu_item' align='left' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].dataInicial}</a></td>
								<td><a class='menu_item' align='left' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].dataFinal}</a></td>
								<td><a class='menu_item' align='left' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].ativa}</a></td>
								<td><a class='menu_item' align='left' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].sindico}</a></td>
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


		<table width="100%">
	    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idapartamento={$info.idapartamento}&idcliente={$info.idcliente}" method="post" name = "for_ocupacao" id = "for_ocupacao">
	      	<input type="hidden" name="for_chk" id="for_chk" value="1" />
			
				<tr>
					<td align="right">Condomínio:</td>
					<td>
					  <b>{$smarty.session.nome_cliente}</b>
					</td>
				</tr>
				
				<tr>
					<td align="right">Apartamento:</td>
					<td>
						<input type="hidden" name="apartamento_numero" id="num_apartamento" value="{$apartamento}" />
					  <b>{$apartamento}</b>
					</td>
				</tr>
				
				<tr>
					<td align="right" class="req">Cliente:</td>
					<td align="left">
						<input type="hidden" name="idcliente" id="idcliente" value="{$info.idcliente}" />
						<input type="hidden" name="idcliente_NomeTemp" id="idcliente_NomeTemp" value="{$cliente.nome_cliente}" />
						<input class="extralarge" type="text" name="idcliente_Nome" id="idcliente_Nome" value="{$cliente.nome_cliente}"
							onKeyUp="javascript:
								VerificaMudancaCampo('idcliente');
							"
						/>
						<span class="nao_selecionou" id="idcliente_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("idcliente_Nome", function() {ldelim}
				    	return "cliente_ajax.php?ac=busca_cliente_fisico&typing=" + this.text.value + "&campoID=idcliente" + "&mostraDetalhes=1";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<script type="text/javascript">
				  // verifica os campos auto-complete
					VerificaMudancaCampo('idcliente');
				</script>
				
				
				
				<tr>
					<td class="req" align="right">Tipo:</td>
					<td>
						<input {if $info.littipo=="P"}checked{/if} class="radio" type="radio" name="littipo" id="littipo" value="P" />Proprietário
						<input {if $info.littipo=="I"}checked{/if} class="radio" type="radio" name="littipo" id="littipo" value="I" />Inquilino
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Data Inicial:</td>
					<td>
						<input class="short" type="text" name="litdataInicial" id="litdataInicial" value="{$info.litdataInicial}" maxlength='10' onkeydown="mask('litdataInicial', 'data')" onkeyup="mask('litdataInicial', 'data')" /> (dd/mm/aaaa)
					</td>
				</tr>
				
				<tr>
					<td align="right">Data Final:</td>
					<td>
						<input class="short" type="text" name="litdataFinal" id="litdataFinal" value="{$info.litdataFinal}" maxlength='10' onkeydown="mask('litdataFinal', 'data')" onkeyup="mask('litdataFinal', 'data')" /> (dd/mm/aaaa)
					</td>
				</tr>
				
				<tr>
					<td align="right">Ocupação ativa ?</td>
					<td>
						<input {if $info.litativa=="S"}checked{/if} class="radio" type="radio" name="litativa" id="litativa" value="S" />Sim
						<input {if $info.litativa=="N"}checked{/if} class="radio" type="radio" name="litativa" id="litativa" value="N" />Não
					</td>
				</tr>
				
				<tr>
					<td align="right">Síndico ?</td>
					<td>
						<input {if $info.litsindico=="S"}checked{/if} class="radio" type="radio" name="litsindico" id="litsindico" value="S" />Sim
						<input {if $info.litsindico=="N"}checked{/if} class="radio" type="radio" name="litsindico" id="litsindico" value="N" />Não
					</td>
				</tr>
				
				<tr>
					<td align="right">Observações:</td>
					<td>
						<textarea name="litobservacao" id="litobservacao" rows='6' cols='38'>{$info.litobservacao}</textarea>
					</td>
				</tr>
				
				<tr>
					<td align="right">
      					<input {if $smarty.post.atribuir_endereco==1}checked{/if} style="border:none;" type="checkbox" name="atribuir_endereco" id="atribuir_endereco" />
					</td>
					<td>
						Atribuir o endereço deste apto. ao cliente.
					</td>
				</tr>
				
		        <tr><td>&nbsp;</td></tr>

				<tr>
		        	<td align="center" colspan="2">
		        		<input name="btn_alterar" type="button" class="botao_padrao" value="ALTERAR" onclick="xajax_Verifica_Campos_Ocupacao_AJAX(xajax.getFormValues('for_ocupacao'));" />
		        		<input name="btn_excluir" type="button" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for_ocupacao','{$smarty.server.PHP_SELF}?ac=excluir&idcliente={$info.idcliente}','ATENÇÃO! Confirma a exclusão ?'))" >
		        	</td>
		        </tr>

			</form>
		</table>
	      
	      
	{elseif $flags.action == "adicionar"}



		<table width="100%" >
    		<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_ocupacao" id = "for_ocupacao">
      		<input type="hidden" name="for_chk" id="for_chk" value="1" />
			

				
				<tr>
					<td align="right">Apartamento:</td>
					<td>
					  <input type="hidden" name="num_apartamento" id="apartamento_numero" value="{$apartamento}" />
					  <b>{$apartamento}</b>
					</td>
				</tr>
				
				<tr>
					<td align="right" class="req">Cliente:</td>
					<td align="left">
						<input type="hidden" name="idcliente" id="idcliente" value="{$smarty.post.idcliente}" />
						<input type="hidden" name="idcliente_NomeTemp" id="idcliente_NomeTemp" value="{$smarty.post.idcliente_NomeTemp}" />
						<input class="extralarge" type="text" name="idcliente_Nome" id="idcliente_Nome" value="{$smarty.post.idcliente_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('idcliente');
							"
						/>
						<span class="nao_selecionou" id="idcliente_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>
					</td>
				</tr>
				<script type="text/javascript">
				    new CAPXOUS.AutoComplete("idcliente_Nome", function() {ldelim}
				    	return "cliente_ajax.php?ac=busca_cliente_fisico&typing=" + this.text.value + "&campoID=idcliente" + "&mostraDetalhes=1";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>


				<script type="text/javascript">
				  // verifica os campos auto-complete
					VerificaMudancaCampo('idcliente');
				</script>
				
				<tr>
					<td class="req" align="right">Tipo:</td>
					<td>
						<input {if $smarty.post.tipo=="P"}checked{/if} class="radio" type="radio" name="tipo" id="tipo" value="P" />Proprietário
						<input {if $smarty.post.tipo=="I"}checked{/if} class="radio" type="radio" name="tipo" id="tipo" value="I" />Inquilino
					</td>
				</tr>
				
				<tr>
					<td class="req" align="right">Data Inicial:</td>
					<td>
						<input class="short" type="text" name="dataInicial" id="dataInicial" value="{$smarty.post.dataInicial}" maxlength='10' onkeydown="mask('dataInicial', 'data')" onkeyup="mask('dataInicial', 'data')" /> (dd/mm/aaaa)
					</td>
				</tr>
				
				<tr>
					<td align="right">Data Final:</td>
					<td>
						<input class="short" type="text" name="dataFinal" id="dataFinal" value="{$smarty.post.dataFinal}" maxlength='10' onkeydown="mask('dataFinal', 'data')" onkeyup="mask('dataFinal', 'data')" /> (dd/mm/aaaa)
					</td>
				</tr>
				
				<tr>
					<td align="right">Ocupação ativa ?</td>
					<td>
						<input {if $smarty.post.ativa=="S"}checked{/if} class="radio" type="radio" name="ativa" id="ativa" value="S" />Sim
						<input {if $smarty.post.ativa!="S"}checked{/if} class="radio" type="radio" name="ativa" id="ativa" value="N" />Não
					</td>
				</tr>
				
				<tr>
					<td align="right">Síndico ?</td>
					<td>
						<input {if $smarty.post.sindico=="S"}checked{/if} class="radio" type="radio" name="sindico" id="sindico" value="S" />Sim
						<input {if $smarty.post.sindico!="S"}checked{/if} class="radio" type="radio" name="sindico" id="sindico" value="N" />Não
					</td>
				</tr>
				
				
				<tr>
					<td align="right">Observações:</td>
					<td>
						<textarea name="observacao" id="observacao" rows='6' cols='38'>{$smarty.post.observacao}</textarea>
					</td>
				</tr>
				
				<tr>
					<td align="right">
      					<input {if $smarty.post.atribuir_endereco==1}checked{/if} style="border:none;" type="checkbox" name="atribuir_endereco" id="atribuir_endereco" />
					</td>
					<td>
						Atribuir o endereço deste apto. ao proprietário.
					</td>
				</tr>
				
        		<tr><td>&nbsp;</td></tr>

				<tr>
		          <td colspan="2" align="center">
		  			<input type='button'  class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar"
					  onclick="xajax_Verifica_Campos_Ocupacao_AJAX(xajax.getFormValues('for_ocupacao'));"/>
		          </td>
		        </tr>

			</form>
		</table>


  {/if}
  
	</div>
	
</div>


{/if}

{include file="com_rodape.tpl"}
