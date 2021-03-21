{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}


{if $flags.action == "adicionar" || $flags.action == "editar"}
	{* Chama o CKEditor para exibir ferramenta de formatação de texto em textarea *}
	<script type="text/javascript" src="{$conf.addr}/common/lib/ckeditor/ckeditor.js"></script> 
{/if}



<!--{*  Chama o script para ordenar a tabela  *}-->
<script language="javascript">
{literal}

	//Método para não dar conflito com o auto-complete do xajax
	var $j = jQuery.noConflict();

	$j(document).ready(function()
		{ 
			$j("#tbl_apartamento").tablesorter(); 
	    } 
	);
	
{/literal}
</script>

<script type="text/javascript" src="{$conf.addr}/common/js/tabs.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/apartamentos_listar.js"></script>

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
			  		&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=gerar_boleto">gerar boletos</a>
	        	{/if}
				
				{if $list_permissao.listar == '1'}
				  &nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">listar</a>
          		
				{/if}
				
				</td>

				<td class="tela" WIDTH="14%" height="20">
					Condomínio Selecionado:
				</td>
				
		  		<td class="descricao_tela" WIDTH="30%">
					{$smarty.session.nome_cliente}
					&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$conf.addr}/admin/apartamento.php?ac=selecionar_condominio">Alterar Seleção</a>
				</td>
			{else}
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$conf.addr}/admin/apartamento.php?ac=selecionar_condominio">Selecionar Condomínio</a>
			{/if}
			
		</tr>
	</table>
	

	{include file="div_erro.tpl"}
	
	
    {if $flags.action == "selecionar_condominio"}

    {if $flags.sucesso != ""}
	  	{include file="div_resultado_inicio.tpl"}
	  		{$flags.sucesso}
	  	{include file="div_resultado_fim.tpl"}
	{/if}
	
	
	<table width="95%">
	    {if $smarty.session.idcliente}
			<tr>
				<td class="tb4cantos" WIDTH="14%" height="20" align="center">
					Condomínio Selecionado:
	    			<b>{$smarty.session.nome_cliente}</b>
				</td>
			</tr>
		{/if}
		<tr>
			<td colspan="9" align="center">
				Selecione o condomínio:
				<input type="hidden" name="idcliente" id="idcliente" value="{$smarty.post.idcliente}" />
				<input type="hidden" name="idcliente_NomeTemp" id="idcliente_NomeTemp" value="{$smarty.post.idcliente_NomeTemp}" />
				<input class="ultralarge" type="text" name="idcliente_Nome" id="idcliente_Nome" value="{$smarty.post.idcliente_Nome}"
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
		    	return "apartamento_ajax.php?ac=seleciona_cliente_condominio&typing=" + this.text.value + "&campoID=idcliente" + "&mostraDetalhes=1";
		    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
		</script>


		<script type="text/javascript">
		  // verifica os campos auto-complete
			VerificaMudancaCampo('idcliente');
		</script>

		<tr>
			<td colspan="9" align="center">
		  		<div id="dados_cliente">
				</div>
			</td>
		</tr>
	</table>

  {elseif $flags.action == "listar"}

    {if $flags.sucesso != ""}
	  	{include file="div_resultado_inicio.tpl"}
	  		{$flags.sucesso}
	  	{include file="div_resultado_fim.tpl"}
	  {/if}				
		
		<br><br>
    				
        <table width="95%" align="center">
			<tr>
				<td colspan="9" align="center">
					Apartamento:
					<input type="hidden" name="idapartamento" id="idapartamento" value="{$smarty.post.idapartamento}" />
					<input type="hidden" name="idapartamento_NomeTemp" id="idapartamento_NomeTemp" value="{$smarty.post.idapartamento_NomeTemp}" />
					<input class="extralarge" type="text" name="idapartamento_Nome" id="idapartamento_Nome" value="{$smarty.post.idapartamento_Nome}"
						onKeyUp="javascript:
							VerificaMudancaCampo('idapartamento');
						"
					/>
					<span class="nao_selecionou" id="idapartamento_Flag">
						&nbsp;&nbsp;&nbsp;
					</span>
				</td>
			</tr>

			<script type="text/javascript">
			    new CAPXOUS.AutoComplete("idapartamento_Nome", function() {ldelim}
			    	return "apartamento_ajax.php?ac=busca_apartamento&typing=" + this.text.value + "&campoID=idapartamento" + "&mostraDetalhes=1";
			    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
			</script>


			<script type="text/javascript">
			  // verifica os campos auto-complete
				VerificaMudancaCampo('idapartamento');
			</script>

			<tr>
				<td colspan="9" align="center">
	  		  <div id="dados_apartamento">
					</div>
				</td>
			</tr>

            <tr><td>&nbsp;</td></tr>

		</table>

    <form action="{$conf.addr}/admin/cliente_condominio.php?ac=busca_parametrizada&target=full" method="post" name = "for" id = "for" target="_blank">      
    	<table align="center">
            
	    	<input type="hidden" name="for_apto" id="for_apto" value="1" />
	        <input type="hidden" name="for_chk" id="for_chk" value="1" />
	        <input type="hidden" name="idcliente" id="idcliente" value="{$smarty.session.idcliente}" />

            <tr>
				<td align="center">
					<input type="checkbox" id="includeCpf" name="includeCpf" checked style="padding-top:2px;" onclick="includeCpfs()" > Incluir CPFs
				</td>
			</tr>

            <tr>
				<td align="center">
					<input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão">
    			</td>
    		</tr>
		</table>
	</form>			
					<br><br>
		
							
		<table width="95%" align="center" class="tablesorter" id="tbl_apartamento">
			
	      	<thead>
				<tr>
					<th class="header" >Número</th>
				    <th class="header" >Proprietário</th>
				    <th class="header cpf" >CPF do Proprietário</th>
				    <th class="header" >Tel. do Proprietário</th>
				    <th class="header" >Inquilino</th>
				    <th class="header cpf" >CPF do Inquilino</th>
					<th class="header" >Tel. do Inquilino</th>
				</tr>
			</thead>
		
	   		<tbody>
	   			{foreach from=$list key=i item=apartamento}	
			   		<tr  bgcolor = "{if $i % 2 == 0}F7F7F7{else}WHITE{/if}" >
						<td align='center'><a class='menu_item' href="{$smarty.server.PHP_SELF}?ac=editar&idapartamento={$apartamento.idapartamento}">{$apartamento.apto}</a></td>
						<td><a class='menu_item' href="{$smarty.server.PHP_SELF}?ac=editar&idapartamento={$apartamento.idapartamento}">{$apartamento.nome_proprietario}</a></td>
						<td class='cpf'><a class='menu_item' href="{$smarty.server.PHP_SELF}?ac=editar&idapartamento={$apartamento.idapartamento}">{$apartamento.cpf_proprietario}</a></td>
						<td><a class='menu_item' href="{$smarty.server.PHP_SELF}?ac=editar&idapartamento={$apartamento.idapartamento}">{$apartamento.telefone_proprietario}</a></td>
						<td><a class='menu_item' href="{$smarty.server.PHP_SELF}?ac=editar&idapartamento={$apartamento.idapartamento}">{$apartamento.nome_inquilino}</a></td>
						<td class='cpf'><a class='menu_item' href="{$smarty.server.PHP_SELF}?ac=editar&idapartamento={$apartamento.idapartamento}">{$apartamento.cpf_inquilino}</a></td>
						<td><a class='menu_item' href="{$smarty.server.PHP_SELF}?ac=editar&idapartamento={$apartamento.idapartamento}">{$apartamento.telefone_inquilino}</a></td>
					</tr>
				{/foreach}		
			</tbody>
			
		</table>					
		
		<p align="center" id="nav">{$nav}</p>



  {elseif $flags.action == "gerar_boleto"}


	
	<br>

	<div style="height:60%;">
	

  	<form  action="{$smarty.server.PHP_SELF}?ac=gerar_boleto" method="post" name = "for" id = "for">
     	<input type="hidden" name="for_chk" id="for_chk" value="1" />
     	
       <center>
  		<table width="500" class="tb4cantos" style="margin:25px 0 25px 0;" class="subtable">
  			
        <tr> 
          <th align="center" colspan="2">{$smarty.session.nome_cliente}</th>
        </tr>
        
        <tr>
            <td align="right" valign="bottom">Informe a data de vencimento do condomínio: </td>
	  			<td align="left"> 
	  				<input class="short" type="text" name="data_vencimento" id="data_vencimento" value="{$dados_condominio.sugestaoVencimento}" maxlength='10' onkeydown="mask('data_vencimento', 'data');" onkeyup="mask('data_vencimento', 'data');" onchange="if(strlen($('data_vencimento').value) == 10) xajax_chk_Condominio_Gerado_AJAX(xajax.getFormValues('for'));" />
	  				<img src="{$conf.addr}/common/img/calendar.png" id="img_data_vencimento" style="cursor: pointer;" /> (dd/mm/aaaa)
		  			<script type="text/javascript">
	    					Calendar.setup(
	    						{ldelim}
	    							inputField : "data_vencimento", // ID of the input field
	    							ifFormat : "%d/%m/%Y", // the date format
	    							button : "img_data_vencimento", // ID of the button
	    							align  : "cR"  // alinhamento
	    						{rdelim}
	    					);
	        				
	    			</script>
	  			</td>
  			</tr>

	        <tr>
	            <td align="right" valign="bottom">Informe m&ecirc;s e ano referentes &agrave; gera&ccedil;&atilde;o (mm/aaaa): </td>
	  			<td align="left"> 
	  				<input class="short" type="text" name="data_geracao" id="data_geracao" value="{$dados_condominio.sugestaoGeracao}" maxlength='7' />
	  			</td>
  			</tr>

        	<tr>
            	<td align="right">Descrição do Movimento: </td>
            	<td align="left"><input type="text" name="descricao" id="descricao" class="long" value="Taxa de Condomínio"></td>
        	</tr> 
  		</table> 
  		
  		
        <a class="link_geral" href="#" onclick="xajax_Ver_Boletos_Condominos_AJAX(xajax.getFormValues('for'));">&bull; Ver Lançamentos de Condomínio no mês do vencimento informado</a> 
      <br><br>
 
     
 		</center>  
      <table width="95%" align="center" class="tablesorter" id="tbl_apartamento">
  			
        	<thead>
  				<tr>
	  				<th width="10" align="center" class="header" >Número</th>
	  			    <th width="300" align="center" class="header" >Proprietário</th>
	  			    <th width="300" align="center" class="header" >Inquilino</th>
	  				<th width="100" align="center" class="header" >Valor (R$)</th>

			  		<td width="70"  align="center"  style=" font-size:8pt; font-weight:bold; padding:4px; background-color:#0089D1; color:#FFF;" >Gerar Lançamento
						<input type="checkbox" id="checkAll" name="checkAll" checked style="padding-top:2px; " onclick=" if(this.checked)SetAllCheckBoxes('for', 'apartamentos[]', true); else SetAllCheckBoxes('for', 'apartamentos[]', false);" >
					</td>

  				</tr>
      		</thead>
 		
  	   		<tbody>

  	   		<input type="hidden" value="{$apto_ids}" name="apto_ids" >
  	   			{foreach from=$list key=i item=apartamento}	
  			   	<tr  bgcolor = "{if $i % 2 == 0}CCCCCC{else}WHITE{/if}" >
	  			   	<input type="hidden" value="{$apartamento.idmorador}"         name="idmorador_{$apartamento.idapartamento}">
	  			   	<input type="hidden" value="{$apartamento.idproprietario}"    name="idproprietario_{$apartamento.idapartamento}">
	  			   	<input type="hidden" value="{$apartamento.sit_apt}"           name="situacao_{$apartamento.idapartamento}">
	  			   	<input type="hidden" value="{$apartamento.apto}"              name="apto_{$apartamento.idapartamento}">
	  					
  					<td align='center'>{$apartamento.apto}</td>
  					<td>{$apartamento.nome_proprietario}</td>
  					<td>{$apartamento.nome_inquilino}</td>
  					<td align='center'><input type="text" class="short" id="valor_{$apartamento.idapartamento}" name="valor_{$apartamento.idapartamento}"  value="{$dados_condominio.taxa_condominio}" maxlength='10' onkeydown="FormataValor('valor_{$apartamento.idapartamento}')" onkeyup="FormataValor('valor_{$apartamento.idapartamento}')"></td>
  					
  					
  					<td align='center'>
  						<div id="div_movimento_{$apartamento.idapartamento}">
  							
  							{if $apartamento.gerou_cond_mes}
	  							<a href="{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$apartamento.gerou_cond_mes.0.idmovimento}" target="_blank">
									<img src="{$conf.addr}/common/img/check.gif" />
		  						</a>
  							{else}	
  								<input type="checkbox" id="chk_{$apartamento.idapartamento}" name="apartamentos[]" value="{$apartamento.idapartamento}" checked >
							{/if}  						
  						</div>
  					</td>
  					
  				</tr>
  				{/foreach}		
  			
        	</tbody>
  			
  		</table>
      
      <center>
        <input type="button" class="botao_padrao" value="Gerar Lançamentos" name="btn_GerarBoletos" id="btn_GerarBoletos" onclick="xajax_Gerar_Boletos_Condominos_AJAX(xajax.getFormValues('for'));" />
      </center>	
      				
    </form>  		
  	
  	  	<table width="300px" cellpadding="0" class="tb4cantos" align="left">
		<tr>
			<td colspan="2" align="center" bgcolor="#CCCCCC">
				Legenda
			</td>
		</tr>

		<tr bgcolor="#FFFFFF">
		  <td colspan="2">  
		    <img src="{$conf.addr}/common/img/check.gif"> Lançamento já gerado para a data de vencimento informada.
		  </td>
		</tr>
	</table>

     </div> 

	{elseif $flags.action == "editar"}
	
		<br>

		<div style="width: 100%;">

	  	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idapartamento={$info.idapartamento}" method="post" name = "for_apartamento" id = "for_apartamento">
		<input type="hidden" name="for_chk" id="for_chk" value="1" />
		

		  	<ul class="anchors">
				<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados do Apartamento</a></li>
				<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Ocupação</a></li>
			</ul>

			{************************************}
			{* TAB 0 *}
			{************************************}

			<div id="tab_0" class="anchor">

				<table width="95%" align="center">

					<tr>
						<td align="right">Condomíno:</td>
						<td> <b>{$smarty.session.nome_cliente}</b></td>
					</tr>

					<tr>
						<td class="req" align="right">Número:</td>
						<td><input class="medium" type="text" name="litapto" id="litapto" maxlength="10" value="{$info.litapto}"/></td>
					</tr>

					<tr>
						<td class="req" align="right">Situação:</td>
						<td>
							<select name="litsituacao" id="litsituacao">
							<option {if $info.litsituacao=="P"}selected{/if} value="P">Ocupado pelo Proprietário</option>
							<option {if $info.litsituacao=="I"}selected{/if} value="I">Ocupado pelo Inquilino</option>
							<option {if $info.litsituacao=="V"}selected{/if} value="V">Vazio</option>
							</select>
						</td>
					</tr>

					<tr>
						<td align="right">Fração Ideal:</td>
						<td><input class="medium" type="text" name="numfracaoIdeal" id="numfracaoIdeal" value="{$info.numfracaoIdeal}"
								onkeydown="FormataValor('numfracaoIdeal',4);"
								onkeyup="FormataValor('numfracaoIdeal',4);"
								onchange="document.getElementById('numcustoFixo').value = '';"
								/>
						</td>
					</tr>

					<tr>
						<td align="right">Custo Fixo:</td>
						<td>
							<input class="short" type="text" name="numcustoFixo" id="numcustoFixo" value="{$info.numcustoFixo}" maxlength='10'
								onkeydown="FormataValor('numcustoFixo');"
								onkeyup="FormataValor('numcustoFixo');"
								onchange="document.getElementById('numfracaoIdeal').value = '';"
								/>
						</td>
					</tr>

					<tr>
						<td align="right">Fundo de Reserva:</td>
						<td>
							<input class="short" type="text" name="numfundoReserva" id="numfundoReserva" value="{$info.numfundoReserva}" maxlength='10' onkeydown="FormataValor('numfundoReserva')" onkeyup="FormataValor('numfundoReserva')" />
						</td>
					</tr>

					<tr>
						<td align="right">Observações:</td>
						<td>
							<textarea name="litobservacao" id="litobservacao" rows='6' cols='38'>{$info.litobservacao}</textarea>
						</td>
					</tr>

                    <tr>
						<td align="right">Informa&ccedil;&otilde;es do campo "Demonstrativo" do boleto:</td>	                        	
                        <td>
                            <textarea name="demonstrativo" id="demonstrativo" style="width:596px; height:100px" >{$info.demonstrativo}</textarea>
                            {* Chama o CKeditor para exibir as ferramentas de formatação no textarea *} 
                            <script type="text/javascript"> CKEDITOR.replace('demonstrativo');</script>
                        </td>
                    </tr>	

			        <tr><td>&nbsp;</td></tr>
					
				</table>
				
			
			</div>
			
						
			
			{************************************}
			{* TAB 1 *}
			{************************************}

			<div id="tab_1" class="anchor">
			
			<table width="95%" align="center">
				
				<table width="700px" align="center" class="tb4cantos">
				
					<tr bgcolor="#F7F7F7">
						<td colspan="9" align="center">Busca do <b>Proprietário</b></td>
				    </tr>
	
					<tr>
						<td colspan="9" align="center">
							Proprietário:
							<input type="hidden" name="idproprietario" id="idproprietario" value="{$info.proprietario.idcliente}" />
							<input type="hidden" name="idproprietario_NomeTemp" id="idproprietario_NomeTemp" value="{$info.proprietario.nome_cliente}" />
							<input class="ultralarge" type="text" name="idproprietario_Nome" id="idproprietario_Nome" value="{$info.proprietario.nome_cliente}"
								onKeyUp="javascript:
									VerificaMudancaCampo('idproprietario');
								"
							/>
							<span class="nao_selecionou" id="idproprietario_Flag">
								&nbsp;&nbsp;&nbsp;
							</span>
						</td>
					</tr>
					
					<tr><td><br></td></tr>
									
					<tr>
						<td>
							<table align="center" >
							
								<tr>
									<td colspan="9">Caso o Proprietário não esteje cadastrado, preencha os dados no formulário a seguir para cadastra-lo:</td>
								</tr>
								
								<tr>
									<td align="right"  class="req">Nome:</td>
									<td align="left" ><input class="long" type="text" name="nome_proprietario" id="nome_proprietario" maxlength="100" value="{$smarty.post.nome_proprietario}"/></td>
								</tr>
								
								<tr>
									<td align="right" class="">Tel. Residencial:</td>
									<td align="left">
										<input class="tiny" type="text" name="tel_residencial_proprietario_ddd" id="tel_residencial_proprietario_ddd" value="{$smarty.post.tel_residencial_proprietario_ddd}" maxlength='2' />
										<input class="short" type="text" name="tel_residencial_proprietario" id="tel_residencial_proprietario" value="{$smarty.post.tel_residencial_proprietario}" maxlength='9'onkeydown="mask('tel_residencial_proprietario', 'tel')" onkeyup="mask('tel_residencial_proprietario', 'tel')" />									
									</td>
								</tr>
								
								<tr>
									<td align="right" class="">Tel. Celular:</td>
									<td align="left">
										<input class="tiny" type="text" name="celular_proprietario_ddd" id="celular_proprietario_ddd" value="{$smarty.post.celular_proprietario_ddd}" maxlength='2' />
										<input class="short" type="text" name="celular_proprietario" id="celular_proprietario" value="{$smarty.post.celular_proprietario}" maxlength='9'onkeydown="mask('celular_proprietario', 'tel')" onkeyup="mask('celular_proprietario', 'tel')" />									
									</td>
								</tr>
								
								<tr>
									<td align="right" class="">Tel. Comercial:</td>
									<td align="left">
										<input class="tiny" type="text" name="telefone_proprietario_ddd" id="telefone_proprietario_ddd" value="{$smarty.post.telefone_proprietario_ddd}" maxlength='2' />
										<input class="short" type="text" name="telefone_proprietario" id="telefone_proprietario" value="{$smarty.post.telefone_proprietario}" maxlength='9'onkeydown="mask('telefone_proprietario', 'tel')" onkeyup="mask('telefone_proprietario', 'tel')" />									
									</td>
								</tr>
								
								<tr>
									<td align="right">CPF:</td>
									<td align="left"><input class="long" type="text" name="cpf_proprietario" id="cpf_proprietario" value="{$smarty.post.cpf_proprietario}" maxlength="14" onkeydown="mask('cpf_proprietario', 'cpf')" onkeyup="mask('cpf_proprietario', 'cpf')"  /></td>
								</tr>							
								
							</table>
						</td>
					</tr>
					
					
				</table>
				
				<br><br>
			
				<table width="700px" align="center" class="tb4cantos">
					
					
					<tr bgcolor="#F7F7F7">
						<td colspan="9" align="center">Busca do <b>Morador</b></td>
				    </tr>
					<tr>
						<td colspan="9" align="center">
							Morador:
							<input type="hidden" name="idmorador" id="idmorador" value="{$info.morador.idcliente}" />
							<input type="hidden" name="idmorador_NomeTemp" id="idmorador_NomeTemp" value="{$info.morador.nome_cliente}" />
							<input class="ultralarge" type="text" name="idmorador_Nome" id="idmorador_Nome" value="{$info.morador.nome_cliente}"
								onKeyUp="javascript:
									VerificaMudancaCampo('idmorador');
								"
							/>
							<span class="nao_selecionou" id="idmorador_Flag">
								&nbsp;&nbsp;&nbsp;
							</span>
						</td>
					</tr>
					
					<tr><td><br></td></tr>
					
					<tr>
						<td>
							<table align="center" >
							
								<tr>
									<td colspan="9">Caso o Morador não esteje cadastrado, preencha os dados no formulário a seguir para cadastra-lo:</td>
								</tr>
								
								<tr>
									<td align="right" class="req">Nome:</td>
									<td align="left"><input class="long" type="text" name="nome_morador" id="nome_morador" maxlength="100" value="{$smarty.post.nome_morador}"/></td>
								</tr>
								
								<tr>
									<td align="right" class="">Tel. Residencial:</td>
									<td align="left">
										<input class="tiny" type="text" name="tel_residencial_morador_ddd" id="tel_residencial_morador_ddd" value="{$smarty.post.tel_residencial_morador_ddd}" maxlength='2' />
										<input class="short" type="text" name="tel_residencial_morador" id="tel_residencial_morador" value="{$smarty.post.tel_residencial_morador}" maxlength='9'onkeydown="mask('tel_residencial_morador', 'tel')" onkeyup="mask('tel_residencial_morador', 'tel')" />									
									</td>
								</tr>
								
								<tr>
									<td align="right" class="">Tel. Celular:</td>
									<td align="left">
										<input class="tiny" type="text" name="celular_morador_ddd" id="celular_morador_ddd" value="{$smarty.post.celular_morador_ddd}" maxlength='2' />
										<input class="short" type="text" name="celular_morador" id="celular_morador" value="{$smarty.post.celular_morador}" maxlength='9'onkeydown="mask('celular_morador', 'tel')" onkeyup="mask('celular_morador', 'tel')" />									
									</td>
								</tr>
								
								<tr>
									<td align="right" class="">Tel. Comercial:</td>
									<td align="left">
										<input class="tiny" type="text" name="telefone_morador_ddd" id="telefone_morador_ddd" value="{$smarty.post.telefone_morador_ddd}" maxlength='2' />
										<input class="short" type="text" name="telefone_morador" id="telefone_morador" value="{$smarty.post.telefone_morador}" maxlength='9'onkeydown="mask('telefone_morador', 'tel')" onkeyup="mask('telefone_morador', 'tel')" />									
									</td>
								</tr>
								
								<tr>
									<td align="right">CPF:</td>
									<td align="left"><input class="long" type="text" name="cpf_morador" id="cpf_morador" value="{$smarty.post.cpf_morador}" maxlength="14" onkeydown="mask('cpf_morador', 'cpf')" onkeyup="mask('cpf_morador', 'cpf')"  /></td>
								</tr>							
								
							</table>
						</td>
					</tr>
			

					<script type="text/javascript">
					    
						new CAPXOUS.AutoComplete("idproprietario_Nome", function() {ldelim}
					    	return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idproprietario" + "&mostraDetalhes=0";
					    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
						
						
						new CAPXOUS.AutoComplete("idmorador_Nome", function() {ldelim}
					    	return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idmorador" + "&mostraDetalhes=0";
					    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
					
					
					  	// verifica os campos auto-complete
						VerificaMudancaCampo('idproprietario');
						
						// verifica os campos auto-complete
						VerificaMudancaCampo('idmorador');
						
					</script>
	
	
	
	
					
			        
					
				</table>
				
				<br><br>
				
			</table>
			</div>
			
				<script language="javascript">
					Processa_Tabs(0, 'tab_'); // seta o tab inicial
				</script>
				
				<table align=center>
					<tr><td>&nbsp;</td></tr>
					<tr>
			        	<td align="center" colspan="2">
			        		<input name="Submit" type="button" class="botao_padrao" value="ALTERAR"
								onClick="xajax_Verifica_Campos_Apartamento_AJAX(xajax.getFormValues('for_apartamento')); "
							>
			        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for_apartamento','{$smarty.server.PHP_SELF}?ac=excluir&idapartamento={$info.idapartamento}','ATENÇÃO! Confirma a exclusão ?'))" >
			        	</td>
			        </tr>
				</table>
				
			</form>
			
		</div>
	      
		
		
		
		
	{elseif $flags.action == "adicionar"}

		<br>

		<div style="width: 100%;">

			  	<form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_apartamento" id = "for_apartamento">
			    <input type="hidden" name="for_chk" id="for_chk" value="1" />

					<ul class="anchors">
						<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados do Apartamento</a></li>
						<li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Ocupação</a></li>
					</ul>

     				<div id="tab_0" class="anchor">

						<table width="95%" align="center">

							<tr>
								<td align="right">Condomíno:</td>
								<td>
	     							<b>{$smarty.session.nome_cliente}</b>
								</td>
							</tr>

							<tr>
								<td class="req" align="right">Número:</td>
								<td><input class="medium" type="text" name="apto" id="apto" maxlength="10" value="{$smarty.post.apto}"/></td>
							</tr>

							<tr>
								<td class="req" align="right">Situação:</td>
								<td>
									<select name="situacao" id="situacao">
									<option {if $smarty.post.situacao=="P"}selected{/if} value="P">Ocupado pelo Proprietário</option>
									<option {if $smarty.post.situacao=="I"}selected{/if} value="I">Ocupado pelo Inquilino</option>
									<option {if $smarty.post.situacao=="V"}selected{/if} value="V">Vazio</option>
									</select>
								</td>
							</tr>

							<tr>
								<td align="right">Fração Ideal:</td>
								<td><input class="medium" type="text" name="fracaoIdeal" id="fracaoIdeal" value="{$smarty.post.fracaoIdeal}"
									onkeydown="FormataValor('fracaoIdeal',4);"
									onkeyup="FormataValor('fracaoIdeal',4);"

									onchange="document.getElementById('custoFixo').value = '';"
									/>
								</td>
							</tr>

							<tr>
								<td align="right">Custo Fixo:</td>
								<td>
									<input class="short" type="text" name="custoFixo" id="custoFixo" value="{$smarty.post.custoFixo}" maxlength='10'
									onkeydown="FormataValor('custoFixo');"
									onkeyup="FormataValor('custoFixo');"
									onchange="document.getElementById('fracaoIdeal').value = '';"
									/>
								</td>
							</tr>

							<tr>
								<td align="right">Fundo de Reserva:</td>
								<td>
									<input class="short" type="text" name="fundoReserva" id="fundoReserva" value="{$smarty.post.fundoReserva}" maxlength='10' onkeydown="FormataValor('fundoReserva')" onkeyup="FormataValor('fundoReserva')" />
								</td>
							</tr>

							<tr>
								<td align="right">Observações:</td>
								<td>
									<textarea name="observacao" id="observacao" rows='6' cols='38'>{$smarty.post.observacao}</textarea>
								</td>
							</tr>

	                        <tr>
								<td align="right">Informa&ccedil;&otilde;es do campo "Demonstrativo" do boleto:</td>	                        	
	                            <td>
	                                <textarea name="demonstrativo" id="demonstrativo" style="width:596px; height:100px" >{$smarty.post.demonstrativo}</textarea>
	                                {* Chama o CKeditor para exibir as ferramentas de formatação no textarea *} 
	                                <script type="text/javascript"> CKEDITOR.replace('demonstrativo');</script>
	                            </td>
	                        </tr>	

							<tr><td><br></td></tr>
						</table>
						
						

			</div>
			
			{************************************}
			{* TAB 1 *}
			{************************************}

			<div id="tab_1" class="anchor">
			
			<table width="95%" align="center">
				
				<table width="700px" align="center" class="tb4cantos">
				
					<tr bgcolor="#F7F7F7">
						<td colspan="9" align="center">Busca do <b>Proprietário</b></td>
				    </tr>
	
					<tr>
						<td colspan="9" align="center">
							Proprietário:
							<input type="hidden" name="idproprietario" id="idproprietario" value="{$info.proprietario.idcliente}" />
							<input type="hidden" name="idproprietario_NomeTemp" id="idproprietario_NomeTemp" value="{$info.proprietario.nome_cliente}" />
							<input class="ultralarge" type="text" name="idproprietario_Nome" id="idproprietario_Nome" value="{$info.proprietario.nome_cliente}"
								onKeyUp="javascript:
									VerificaMudancaCampo('idproprietario');
								"
							/>
							<span class="nao_selecionou" id="idproprietario_Flag">
								&nbsp;&nbsp;&nbsp;
							</span>
						</td>
					</tr>
					
					<tr><td><br></td></tr>
									
					<tr>
						<td>
							<table align="center" >
							
								<tr>
									<td colspan="9">Caso o Proprietário não esteje cadastrado, preencha os dados no formulário a seguir para cadastra-lo:</td>
								</tr>
								
								<tr>
									<td align="right"  class="req">Nome:</td>
									<td align="left" ><input class="long" type="text" name="nome_proprietario" id="nome_proprietario" maxlength="100" value="{$smarty.post.nome_proprietario}"/></td>
								</tr>
								
								<tr>
									<td align="right" class="">Tel. Residencial:</td>
									<td align="left">
										<input class="tiny" type="text" name="tel_residencial_proprietario_ddd" id="tel_residencial_proprietario_ddd" value="{$smarty.post.tel_residencial_proprietario_ddd}" maxlength='2' />
										<input class="short" type="text" name="tel_residencial_proprietario" id="tel_residencial_proprietario" value="{$smarty.post.tel_residencial_proprietario}" maxlength='9'onkeydown="mask('tel_residencial_proprietario', 'tel')" onkeyup="mask('tel_residencial_proprietario', 'tel')" />									
									</td>
								</tr>
								
								<tr>
									<td align="right" class="">Tel. Celular:</td>
									<td align="left">
										<input class="tiny" type="text" name="celular_proprietario_ddd" id="celular_proprietario_ddd" value="{$smarty.post.celular_proprietario_ddd}" maxlength='2' />
										<input class="short" type="text" name="celular_proprietario" id="celular_proprietario" value="{$smarty.post.celular_proprietario}" maxlength='9'onkeydown="mask('celular_proprietario', 'tel')" onkeyup="mask('celular_proprietario', 'tel')" />									
									</td>
								</tr>
								
								<tr>
									<td align="right" class="">Tel. Comercial:</td>
									<td align="left">
										<input class="tiny" type="text" name="telefone_proprietario_ddd" id="telefone_proprietario_ddd" value="{$smarty.post.telefone_proprietario_ddd}" maxlength='2' />
										<input class="short" type="text" name="telefone_proprietario" id="telefone_proprietario" value="{$smarty.post.telefone_proprietario}" maxlength='9'onkeydown="mask('telefone_proprietario', 'tel')" onkeyup="mask('telefone_proprietario', 'tel')" />									
									</td>
								</tr>
								
								<tr>
									<td align="right">CPF:</td>
									<td align="left"><input class="long" type="text" name="cpf_proprietario" id="cpf_proprietario" value="{$smarty.post.cpf_proprietario}" maxlength="14" onkeydown="mask('cpf_proprietario', 'cpf')" onkeyup="mask('cpf_proprietario', 'cpf')"  /></td>
								</tr>							
								
							</table>
						</td>
					</tr>
					
					
				</table>
				
				<br><br>
			
				<table width="700px" align="center" class="tb4cantos">
					
					
					<tr bgcolor="#F7F7F7">
						<td colspan="9" align="center">Busca do <b>Morador</b></td>
				    </tr>
					<tr>
						<td colspan="9" align="center">
							Morador:
							<input type="hidden" name="idmorador" id="idmorador" value="{$info.morador.idcliente}" />
							<input type="hidden" name="idmorador_NomeTemp" id="idmorador_NomeTemp" value="{$info.morador.nome_cliente}" />
							<input class="ultralarge" type="text" name="idmorador_Nome" id="idmorador_Nome" value="{$info.morador.nome_cliente}"
								onKeyUp="javascript:
									VerificaMudancaCampo('idmorador');
								"
							/>
							<span class="nao_selecionou" id="idmorador_Flag">
								&nbsp;&nbsp;&nbsp;
							</span>
						</td>
					</tr>
					
					<tr><td><br></td></tr>
					
					<tr>
						<td>
							<table align="center" >
							
								<tr>
									<td colspan="9">Caso o Morador não esteje cadastrado, preencha os dados no formulário a seguir para cadastra-lo:</td>
								</tr>
								
								<tr>
									<td align="right" class="req">Nome:</td>
									<td align="left"><input class="long" type="text" name="nome_morador" id="nome_morador" maxlength="100" value="{$smarty.post.nome_morador}"/></td>
								</tr>
								
								<tr>
									<td align="right" class="">Tel. Residencial:</td>
									<td align="left">
										<input class="tiny" type="text" name="tel_residencial_morador_ddd" id="tel_residencial_morador_ddd" value="{$smarty.post.tel_residencial_morador_ddd}" maxlength='2' />
										<input class="short" type="text" name="tel_residencial_morador" id="tel_residencial_morador" value="{$smarty.post.tel_residencial_morador}" maxlength='9'onkeydown="mask('tel_residencial_morador', 'tel')" onkeyup="mask('tel_residencial_morador', 'tel')" />									
									</td>
								</tr>
								
								<tr>
									<td align="right" class="">Tel. Celular:</td>
									<td align="left">
										<input class="tiny" type="text" name="celular_morador_ddd" id="celular_morador_ddd" value="{$smarty.post.celular_morador_ddd}" maxlength='2' />
										<input class="short" type="text" name="celular_morador" id="celular_morador" value="{$smarty.post.celular_morador}" maxlength='9'onkeydown="mask('celular_morador', 'tel')" onkeyup="mask('celular_morador', 'tel')" />									
									</td>
								</tr>
								
								<tr>
									<td align="right" class="">Tel. Comercial:</td>
									<td align="left">
										<input class="tiny" type="text" name="telefone_morador_ddd" id="telefone_morador_ddd" value="{$smarty.post.telefone_morador_ddd}" maxlength='2' />
										<input class="short" type="text" name="telefone_morador" id="telefone_morador" value="{$smarty.post.telefone_morador}" maxlength='9'onkeydown="mask('telefone_morador', 'tel')" onkeyup="mask('telefone_morador', 'tel')" />									
									</td>
								</tr>
								
								<tr>
									<td align="right">CPF:</td>
									<td align="left"><input class="long" type="text" name="cpf_morador" id="cpf_morador" value="{$smarty.post.cpf_morador}" maxlength="14" onkeydown="mask('cpf_morador', 'cpf')" onkeyup="mask('cpf_morador', 'cpf')"  /></td>
								</tr>							
								
							</table>
						</td>
					</tr>
			

					<script type="text/javascript">
					    
						new CAPXOUS.AutoComplete("idproprietario_Nome", function() {ldelim}
					    	return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idproprietario" + "&mostraDetalhes=0";
					    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
						
						
						new CAPXOUS.AutoComplete("idmorador_Nome", function() {ldelim}
					    	return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idmorador" + "&mostraDetalhes=0";
					    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
					
					
					  	// verifica os campos auto-complete
						VerificaMudancaCampo('idproprietario');
						
						// verifica os campos auto-complete
						VerificaMudancaCampo('idmorador');
						
					</script>
	
	
	
	
					
			        
					
				</table>
				
				<br><br>
				
			</table>
					
			
			</div>
			
				<script language="javascript">
					Processa_Tabs(0, 'tab_'); // seta o tab inicial
				</script>
											
			</form>
			
			
			<table align="center">
					<tr>
						<td colspan="2" align="center">
							<input type='button' class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar"
							onClick="xajax_Verifica_Campos_Apartamento_AJAX(xajax.getFormValues('for_apartamento')); "/>
						</td>
					</tr>
					
				</table>
			</form>

			
	
	      
		
	</div>
			
		


	{elseif $flags.action == "busca_generica"}

<br>

		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=busca_generica" method="post" name = "for" id = "for">
      	<input type="hidden" name="for_chk" id="for_chk" value="1" />

				<tr>
				  <td align="right">Busca:</td>
					<td>
						<input class="long" type="text" name="busca" id="busca" maxlength="50" value="{$flags.busca}"/>
					</td>
				</tr>
				<tr>
					<td align="right">Resultados por página:</td>
					<td>
						<input class="tiny" type="text" name="rpp" id="rpp" maxlength="50" value="{$flags.rpp}" onkeydown="FormataInteiro('rpp')" onkeyup="FormataInteiro('rpp')" />
						&nbsp;&nbsp;
						<input name="Submit" type="submit" class="botao_padrao" value="Buscar">
					</td>
				</tr>

        		<tr><td>&nbsp;</td></tr>

			</form>
		</table>

		{if count($list)}

			<p align="center">Listando {$conf.area} de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p>

			<table width="95%" align="center">


				<tr>
					<th align='center'>No</th>
					<th align='center'>Nome do cliente</th>
					<th align='center'>CNPJ do cliente</th>
					<th align='center'>Email do cliente</th>
					<th align='center'>Telefone</th>
					<th align='center'>Nome do contato</th>
					<th align='center'>Celular do contato</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].nome_cliente}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].cnpj_cliente}</a></td>
            <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].email_cliente}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].telefone_cliente}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].nome_contato}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].celular_contato}</a></td>
	        </tr>

	        <tr>
	          <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
	        </tr>
	      {/section}

      </table>

      <p align="center" id="nav">{$nav}</p>

			<table width="95%" align="center">
	    	<form action="{$smarty.server.PHP_SELF}?ac=busca_generica&target=full" method="post" name = "for" id = "for" target="_blank">
	      <input type="hidden" name="for_chk" id="for_chk" value="1" />
				<input type="hidden" name="busca" id="busca" value="{$flags.busca}"/>

					<tr>
						<td align="center">
							<input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão">
						</td>
					</tr>

	        <tr><td>&nbsp;</td></tr>

				</form>
			</table>


		{else}
			{if $flags.fez_busca == 1}
      	{include file="div_resultado_nenhum.tpl"}
      {/if}
		{/if}
	{elseif $flags.action == "busca_parametrizada"}

<br>


		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=busca_parametrizada" method="post" name = "for" id = "for">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />


				<tr>
					<td align="right">Cliente:</td>
					<td><input class="long" type="text" name="cliente" id="cliente" maxlength="50" value="{$flags.cliente}"/></td>
				</tr>

				<tr>
					<td align="right">Ramo de Atividade:</td>
					<td><input class="long" type="text" name="ramo_atividade" id="ramo_atividade" maxlength="50" value="{$flags.ramo_atividade}"/></td>
				</tr>

				<tr>
					<td align="right">CNPJ:</td>
					<td><input class="long" type="text" name="cnpj_cliente" id="cnpj_cliente" maxlength="14" value="{$flags.cnpj_cliente}" onkeydown="mask('cnpj_cliente', 'cnpj')" onkeyup="mask('cnpj_cliente', 'cnpj')"/></td>
				</tr>

				<tr>
					<td align="right">Email:</td>
					<td><input class="long" type="text" name="email_cliente" id="email_cliente" maxlength="50" value="{$flags.email_cliente}"/></td>
				</tr>

				<tr>
					<td align="right">Resultados por página:</td>
					<td>
						<input class="tiny" type="text" name="rpp" id="rpp" maxlength="50" value="{$flags.rpp}" onkeydown="FormataInteiro('rpp')" onkeyup="FormataInteiro('rpp')" />
						&nbsp;&nbsp;
						<input name="Submit" type="submit" class="botao_padrao" value="Buscar">
					</td>
				</tr>

        <tr><td>&nbsp;</td></tr>


			</form>
		</table>


		{if count($list)}

			<p align="center">Listando {$conf.area} de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p>

			<table width="95%" align="center">

				<tr>
					<th align='center'>No</th>
					<th align='center'>Nome do cliente</th>
					<th align='center'>CNPJ do cliente</th>
					<th align='center'>Email do cliente</th>
					<th align='center'>Telefone</th>
					<th align='center'>Nome do contato</th>
					<th align='center'>Celular do contato</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].nome_cliente}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].cnpj_cliente}</a></td>
            <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].email_cliente}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].telefone_cliente}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].nome_contato}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].celular_contato}</a></td>
	        </tr>

	        <tr>
	          <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
	        </tr>
	      {/section}

				<tr><td>&nbsp;</td></tr>


      </table>

      <p align="center" id="nav">{$nav}</p>


			<table width="95%" align="center">
	    	<form action="{$smarty.server.PHP_SELF}?ac=busca_parametrizada&target=full" method="post" name = "for" id = "for" target="_blank">
	      <input type="hidden" name="for_chk" id="for_chk" value="1" />
				<input type="hidden" name="cliente" id="cliente" value="{$flags.cliente}"/>
				<input type="hidden" name="ramo_atividade" id="ramo_atividade" value="{$flags.ramo_atividade}"/>
				<input type="hidden" name="cnpj_cliente" id="cnpj_cliente" value="{$flags.cnpj_cliente}"/>
				<input type="hidden" name="email_cliente" id="email_cliente" value="{$flags.email_cliente}"/>

					<tr>
						<td align="center">
							<input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão">
						</td>
					</tr>

	        <tr><td>&nbsp;</td></tr>

				</form>
			</table>


		{else}
			{if $flags.fez_busca == 1}
      	{include file="div_resultado_nenhum.tpl"}
      {/if}
		{/if}



  {/if}

{/if}

</div>
<script type="text/javascript" language="javascript" src="{$conf.addr}/common/js/tooltip.js"></script>
</body>
</html>

