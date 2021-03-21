{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{include file="div_erro.tpl"}

{if $flags.okay}
<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>

 <script type="text/javascript" >
  /**
   * @author : anderson unsonst
   * @access public
   * @return int
   * pega o valor do campo pai e concatena para o campo hidden cod
   **/
  function codigoPai(){ldelim}
   var idpai;
   var cod;
   var num;

    idpai = document.getElementById('idplano_Nome').value;
    num   = document.getElementById('numero').value;

   if(idpai!=""){ldelim}
     if(idpai!=0){ldelim}
	   cod   = idpai + '.'+ num;
	  {rdelim}else{ldelim}
	   cod = num;
	  {rdelim}
     {rdelim}else{ldelim}
  //se nao existir pai

    if(idpai==""){ldelim}
       cod = num;
	  {rdelim}

	 {rdelim}
            document.getElementById('codHidden').value = cod;
            // cod = ''; // zera a variavel por segurança
             //idpai = '';

  {rdelim}

    function codigoPaiedit(){ldelim}
   var idpai;
   var cod;
   var num;

    idpai = document.getElementById('idplano_Nome').value;
    num   = document.getElementById('codigo').value;

   if(idpai!=""){ldelim}
     if(idpai!=0){ldelim}
	   cod   =  num;
	  {rdelim}else{ldelim}
	   cod = num;
	  {rdelim}
     {rdelim}else{ldelim}
  //se nao existir pai

    if(idpai==""){ldelim}
       cod = num;
	  {rdelim}

	 {rdelim}
            document.getElementById('litnumero').value = cod;
/*
 alert('pai : '+idpai);
 alert('numero codigo : '+num);
 alert('codigo final : '+cod);
 */
  cod = '';  //zera a variavel
  idpai = '';
  {rdelim}
 </script>

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
					<th align='center'>Código</th>
					<th align='center'>Nome</th>
					<th align='center'>Tipo</th>
				</tr>

	      {section name=i loop=$list}
	        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idplano={$list[i].idplano}">{$list[i].index}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idplano={$list[i].idplano}">{$list[i].numero}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idplano={$list[i].idplano}">{$list[i].nome}</a></td>
						<td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idplano={$list[i].idplano}">{$list[i].tipo}</a></td>
	        </tr>

	        <tr>
	          <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
	        </tr>
	      {/section}

      </table>

      <p align="center" id="nav">{$nav}</p>
      
      	<table width="95%" align="center">
			<form action="{$smarty.server.PHP_SELF}?ac=listar&target=full" method="post" name = "for" id = "for" target="_blank">
			<input type="hidden" name="for_chk" id="for_chk" value="1" />
				
				<tr><td>&nbsp;</td></tr>

				<tr>
					<td align="center">
						<input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão">
					</td>
				</tr>

				<tr><td>&nbsp;</td></tr>

			</form>
		</table>

		{else}
      {include file="div_resultado_nenhum.tpl"}
		{/if}


	{elseif $flags.action == "editar"}

		{include file="div_instrucoes_inicio.tpl"}
      	<li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>

    	{include file="div_instrucoes_fim.tpl"}

		<table width="100%">
    	<form  action="{$smarty.server.PHP_SELF}?ac=editar&idplano={$info.idplano}" method="post" name = "for" id = "formPlano" {*onsubmit="codigoPaiedit()"*}>
      	<input type="hidden" name="for_chk" id="for_chk" value="1" />
		<input type="hidden" name="hid_pai_numero" id="hid_pai_numero" value="{$info.pai_numero}" />
		<input type="hidden" name="hid_numero" id="hid_numero" value="{$info.numero}" />

				<tr>
					<td align="right">Plano Pai:</td>
					<td>
					 <input type="hidden" name="idplano" id="idplano" value="{$info.numidpai}" />
						<input type="hidden" name="idplano_NomeTemp" id="idplano_NomeTemp" value="{$info.pai_nome}" />
                       	<input class="long" type="text" name="idplano_Nome" id="idplano_Nome" value="{$info.pai_nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('idplano');"/>
						<span class="nao_selecionou" id="idplano_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>

					</td>
				</tr>

				<tr>
					<td class="req" align="right">Código:</td>
					<td>
						<span id='spn_pai_numero'>{$info.pai_numero}</span>
						<input class="tiny" type="text" name="litnumero" id="litnumero" value="{$info.litnumero}" maxlength='10' onkeydown="FormataInteiro('numnumero')" onkeyup="FormataInteiro('numnumero')" />
					</td>
				</tr>

				<tr>
					<td class="req" align="right">Nome:</td>
					<td><input class="" type="text" name="litnome" id="litnome" maxlength="100" value="{$info.litnome}"/></td>
				</tr>

				<tr>
					<td class="req" align="right">Tipo:</td>
					<td>
						<input {if $info.littipo=="D"}checked{/if} class="radio" type="radio" name="littipo" id="littipo" value="D" />Despesa
						<input {if $info.littipo=="R"}checked{/if} class="radio" type="radio" name="littipo" id="littipo" value="R" />Receita
					</td>
				</tr>

				<tr>
					<td align="right">Descrição:</td>
					<td>
						<textarea name="litdescricao" id="litdescricao" rows='6' cols='38'>{$info.litdescricao}</textarea>
					</td>
				</tr>


        		<tr><td>&nbsp;</td></tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="button" class="botao_padrao" value="ALTERAR" onclick="xajax_Verifica_Campos_Planos_AJAX(xajax.getFormValues('formPlano'));">
        		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('formPlano','{$conf.addr}/admin/plano.php?ac=excluir&idplano={$info.idplano}','ATENÇÃO! Confirma a exclusão ?'))" >
        	</td>
        </tr>

			</form>
				<script language="javascript">
				    new CAPXOUS.AutoComplete("idplano_Nome", function() {ldelim}
				    	return "plano_ajax.php?ac=busca_plano&typing=" + this.text.value + "&idplano=" + document.getElementById('idplano').value + "&campoID=idplano";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>

					<script type="text/javascript">
				  // verifica os campos auto-complete
					VerificaMudancaCampo('idplano');
     				{if $info.pai_numero}
						document.getElementById('idplano_Nome').value = '{$info.pai_numero_js} - ' + document.getElementById('idplano_Nome').value;
					{/if}
				</script>
		</table>


	{elseif $flags.action == "adicionar"}

		{include file="div_instrucoes_inicio.tpl"}
      		<li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>
    	{include file="div_instrucoes_fim.tpl"}

		<table width="100%">

    	<form action="{$smarty.server.PHP_SELF}?ac=adicionar"  method="post" name = "formPlano" id = "formPlano" onsubmit="codigoPai();">
      	<input type="hidden" name="for_chk" id="for_chk" value="1" />
      	<input type="hidden" name="hid_pai_numero" id="hid_pai_numero" value="{$info.pai_numero}" />
      	

				<tr>
					<td align="right">Plano Pai:</td>
					<td>

					    <input type="hidden" name="idplano" id="idplano" value="{$smarty.post.idplano}" />
						<input type="hidden" name="idplano_NomeTemp" id="idplano_NomeTemp" value="{$smarty.post.idplano_NomeTemp}" />
                       	<input class="long" type="text" name="idplano_Nome" id="idplano_Nome" value="{$smarty.post.idplano_Nome}"
							onKeyUp="javascript:
								VerificaMudancaCampo('idplano');"/>
						<span class="nao_selecionou" id="idplano_Flag">
							&nbsp;&nbsp;&nbsp;
						</span>


					</td>
				</tr>

				<tr>
					<td class="req" align="right">Código :</td>
					<td>
					    <input type="hidden" name="codHidden" id="codHidden" >
					    <span id='spn_pai_numero'>{$info.pai_numero}</span>
						<input class="tiny" type="text" name="numero" id="numero" value="{$smarty.post.numero}" maxlength='10' onkeydown="FormataInteiro('numero')" onkeyup="FormataInteiro('numero');" />
					</td>
				</tr>

				<tr>
					<td class="req" align="right">Nome:</td>


					<td><input class="" type="text" name="nome" id="nome" maxlength="100" value="{$smarty.post.nome}"/></td>
				</tr>

				<tr>
					<td class="req" align="right">Tipo:</td>
					<td>
						<input {if $smarty.post.tipo=="D"}checked{/if} class="radio" type="radio" name="tipo" id="tipo" value="D" />Despesa
						<input {if $smarty.post.tipo=="R"}checked{/if} class="radio" type="radio" name="tipo" id="tipo" value="R" />Receita
					</td>
				</tr>

				<tr>
					<td align="right">Descrição:</td>
					<td>
						<textarea name="descricao" id="descricao" rows='6' cols='38'>{$smarty.post.descricao}</textarea>
					</td>
				</tr>

        <tr><td>&nbsp;</td></tr>

				<tr>
          <td colspan="2" align="center">
  						<input type="button"  class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar"
						onClick="xajax_Verifica_Campos_Planos_AJAX(xajax.getFormValues('formPlano'));codigoPai();">
           </td>
            </tr>

				</form>

				<script language="javascript">
				    new CAPXOUS.AutoComplete("idplano_Nome", function() {ldelim}
				    	return "plano_ajax.php?ac=busca_plano&typing=" + this.text.value + "&idplano=" + document.getElementById('idplano').value + "&campoID=idplano";
				    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				</script>

					<script type="text/javascript">
				  // verifica os campos auto-complete
					VerificaMudancaCampo('idplano');

				</script>
		</table>

  {/if}

{/if}

{include file="com_rodape.tpl"}
