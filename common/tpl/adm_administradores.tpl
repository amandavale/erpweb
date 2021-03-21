{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}

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
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">listar</a>
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a>
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=editar">meus dados</a>
				&nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=trocar_senha">trocar senha</a>
			</td>
		</tr>
	</table>
	

	{if $flags.action == "listar"}
	
		{if $flags.sucesso != ""}
	  	{include file="div_resultado_inicio.tpl"}
	  		{$flags.sucesso}
	  	{include file="div_resultado_fim.tpl"}
		{/if}
	
		{if count($list)}

			<p align="center">Listando Administradores de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p>

			<table width="95%" align="center">
			
        <tr>
          <th align="left" bgcolor="F7F7F7" width="5%">No</th>
          <th align="left" bgcolor="F7F7F7" width="45%">Nome</th>
          <th align="left" bgcolor="F7F7F7" width="25%">Sexo</th>
          <th align="left" bgcolor="F7F7F7" width="25%">Email</th>
        </tr>
            
        {section name=i loop=$list}
          <tr bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >

						<td>
							<a class="menu_item" href="{$smarty.server.PHP_SELF}?ac=mostrar&cod={$list[i].adm_cod}">{$list[i].index}</a>
						</td>

						<td>
							<a class="menu_item" href="{$smarty.server.PHP_SELF}?ac=mostrar&cod={$list[i].adm_cod}">{$list[i].adm_nom}</a>
						</td>

						<td>
							<a class="menu_item" href="{$smarty.server.PHP_SELF}?ac=mostrar&cod={$list[i].adm_cod}">{$list[i].adm_sex}</a>
						</td>
						
						<td>
							<a class="menu_item" href="{$smarty.server.PHP_SELF}?ac=mostrar&cod={$list[i].adm_cod}">{$list[i].adm_ema}</a>
						</td>

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


	{elseif $flags.action == "adicionar"}

		{if $flags.insert == 1}
			{include file="div_resultado_cadastro.tpl"}
		{else}

			{include file="div_instrucoes_inicio.tpl"}
      	<li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>
      	<li>O login deve ter no m&aacute;ximo <b>15</b> d&iacute;gitos.</li>
				<li>A senha deve ter no m&aacute;ximo <b>8</b> d&iacute;gitos.</li>
      {include file="div_instrucoes_fim.tpl"}

			<table width="100%">
				<form method="post" name="for" id="for" action="{$smarty.server.PHP_SELF}?ac=adicionar">
      	<input type="hidden" name="for_chk" id="for_chk" value="1" />

				<tr>
  				<td class="req" align="right">nome:</td>
  				<td><input class="long" name='adm_nom' id="adm_nom" value="{$smarty.post.adm_nom}" type='text' maxlength='100' size='70' /></td>
				</tr>

				<tr>
    			<td class="req" align="right">sexo:</td>
    			<td>
						<select class="long" name="adm_sex" id="adm_sex">
							<option value="M" {if $smarty.post.adm_sex == "M"} selected {/if}>Masculino</option>
							<option value="F" {if $smarty.post.adm_sex == "F"} selected {/if}>Feminino</option>
						</select>
					</td>
  			</tr>

				<tr>
  				<td class="req" align="right">email:</td>
  				<td><input class="long" name='adm_ema' id="adm_ema" value="{$smarty.post.adm_ema}" type='text' maxlength='128' size='70' /></td>
				</tr>

				<tr>
  				<td class="req" align="right">login:</td>
  				<td><input class="long" name='adm_log' id="adm_log" value="{$smarty.post.adm_log}" type='text' maxlength='15' size='15' /></td>
				</tr>

				<tr>
  				<td class="req" align="right">senha:</td>
  				<td><input class="long" name='adm_sen' id="adm_sen" type='password' maxlength='8' size='8' /></td>
				</tr>

				<tr>
  				<td class="req" align="right">confirme a senha:</td>
  				<td><input class="long" name='adm_re_sen' id="adm_re_sen" type='password' maxlength='8' size='8' /></td>
				</tr>

				<tr>
        	<td align="center" colspan="2">
        		<input name="Submit" type="submit" class="botao_padrao" value="OK">
        	</td>
        </tr>
        
    		</form>
    	</table>
    	
    {/if}

	{elseif $flags.action == "editar"}

		{if $flags.editar == 1}
			{include file="div_resultado_alteracao.tpl"}
		{else}

			{include file="div_instrucoes_inicio.tpl"}
      	<li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>
      {include file="div_instrucoes_fim.tpl"}

			<table>
				<form method="post" name="for" id="for" action="{$smarty.server.PHP_SELF}?ac=editar">
      	<input type="hidden" name="for_chk" id="for_chk" value="1" />
				<input type="hidden" name="adm_log" id="adm_log" value="{$adm_info.adm_log}" />

      	<tr>
  				<td align="right">login:</td>
  				<td>{$adm_info.adm_log}</td>
				</tr>

    		<tr>
  				<td class="req" align="right">nome:</td>
  				<td><input class="long" name='litadm_nom' id="litadm_nom" value="{$adm_info.litadm_nom}" type='text' maxlength='100' /></td>
				</tr>

				<tr>
    			<td class="req" align="right">sexo:</td>
    			<td>
						<select class="long" name="litadm_sex" id="litadm_sex">
							<option value="M" {if $adm_info.litadm_sex == "M"} selected {/if}>Masculino</option>
							<option value="F" {if $adm_info.litadm_sex == "F"} selected {/if}>Feminino</option>
						</select>
					</td>
  			</tr>

				<tr>
  				<td class="req" align="right">email:</td>
  				<td><input class="long" name='litadm_ema' id="litadm_ema" value="{$adm_info.litadm_ema}" type='text' maxlength='128' /></td>
				</tr>

				<tr>
        	<td align="center" colspan="2">
      			<input name="Submit" type="submit" class="botao_padrao" value="OK">
        	</td>
        </tr>
        
    		</form>
    	</table>
		{/if}

	{elseif $flags.action == "trocar_senha"}

		{if $flags.TrocarSenha == 1}
			{include file="div_resultado_alteracao.tpl"}
		{else}

			{include file="div_instrucoes_inicio.tpl"}
      	<li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>
      	<li>A senha deve ter no m&aacute;ximo <b>8</b> d&iacute;gitos.</li>
      {include file="div_instrucoes_fim.tpl"}

			<table width="100%">
				<form method="post" name="for" id="for" action="{$smarty.server.PHP_SELF}?ac=trocar_senha">
	    	<input type="hidden" name="for_chk" id="for_chk" value="1" />
  		
      	<tr>
  				<td width="40%" class="req" align="right">senha nova:</td>
  				<td><input class="short" name='adm_sen' id="adm_sen" type='password' maxlength='8' size='8' /></td>
				</tr>

				<tr>
  				<td class="req" align="right">confirme a senha nova:</td>
  				<td><input class="short" name='adm_re_sen' id="adm_re_sen" type='password' maxlength='8' size='8' /></td>
				</tr>

				<tr>
  				<td class="req" align="right">senha atual:</td>
  				<td><input class="short" name='adm_sen_atual' id="adm_sen_atual" type='password' maxlength='8' size='8' /></td>
				</tr>

				<tr>
		      <td align="center" colspan="2">
      			<input name="Submit" type="submit" class="botao_padrao" value="OK">
        	</td>
        </tr>

				</form>
			</table>
    	
		{/if}

	{elseif $flags.action == "mostrar"}
  	<table width="100%">

			<form method="post" name="for" id="for" action="{$smarty.server.PHP_SELF}?ac=delete&cod={$adm_info.adm_cod}">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
      
    	<tr>
				<td align="right">nome:</td>
				<td>{$adm_info.adm_nom}</td>
      </tr>

			<tr>
      	<td align="right">sexo:</td>
      	<td>{$adm_info.adm_sex}</td>
      </tr>

      <tr>
      	<td align="right">email:</td>
      	<td>{$adm_info.adm_ema}</td>
      </tr>

			<tr>
      	<td align="center" colspan="2">
      		<input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&id={$adm_info.adm_cod}','ATENÇÃO! Confirma a exclusão deste administrador ?'))" >
				</td>
      </tr>

			</form>
		</table>
		
	{/if}

{/if}


{include file="com_rodape.tpl"}
