{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}


<script type="text/javascript" src="{$conf.addr}/common/js/tabs.js"></script>

<script type="text/javascript" src="{$conf.addr}/common/js/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/sortOptions.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/multi-select.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/comunicado.js"></script>
<style type="text/css">@import url("{$conf.addr}/common/css/multi-select.css");</style>



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
                Opera&ccedil;&otilde;es:
            </td>
            <td class="descricao_tela">
                {if $list_permissao.adicionar == '1'}	
                    &nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a>
                {/if}

                {if $list_permissao.listar == '1'}
                    &nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">listar</a> 
                {/if}
            </td>
        </tr>
    </table>


    {include file="div_erro.tpl"}

    {if $flags.action == "listar"}
    
		<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>    

        {if $flags.sucesso != ""}
            {include file="div_resultado_inicio.tpl"}
            {$flags.sucesso}
            {include file="div_resultado_fim.tpl"}
        {/if}
        <br><br>
        
        <form  action="{$smarty.server.PHP_SELF}?ac=listar" method="post" name = "for" id = "for">
        
        <table align="center" width="90%">
            <tr>
                <td colspan="9" align="center">
                    Condom&iacute;nio:
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
            
            <tr>
                <td colspan="9" align="center">
            		<input name="Pesquisar" type="submit" class="botao_padrao" value="PESQUISAR">
            		<input name="Limpar" type="submit" class="botao_padrao" value="LIMPAR">
            	</td>
            </tr>
            
            <script type="text/javascript">
                new CAPXOUS.AutoComplete("idcliente_Nome", function() {ldelim}
                return "comunicado_ajax.php?ac=busca_comunicado&typing=" + this.text.value + "&campoID=idcliente";
                {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
            </script>


            <script type="text/javascript">
                // verifica os campos auto-complete
                VerificaMudancaCampo('idcliente');
            </script>

            <tr>
                <td colspan="9" align="center">
                    <div id="dados_comunicado">
                    </div>
                </td>
            </tr>
        </table>
        
        </form>
        
        
		{if count($comunicados)}
		
		<p align="center">Listando {$conf.area} de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p>
		
		<table width="95%" align="center">
			<tr>
				<th align=center>C&oacute;digo</th>
				<th align=center>T&iacute;tulo</th>
				<th align=center width=15%>Data de cria&ccedil;&atilde;o</th>
				<th align=center>Anexos</th>
			</tr>
			
	    	{section name=i loop=$comunicados}
	        <tr bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
				<td align="right">
					<a class=menu_item href="{$smarty.server.PHP_SELF}?ac=editar&idcomunicado={$comunicados[i].idcomunicado}">{$comunicados[i].idcomunicado}</a>
				</td>
				<td>
					<a class=menu_item href="{$smarty.server.PHP_SELF}?ac=editar&idcomunicado={$comunicados[i].idcomunicado}">{$comunicados[i].titulo}</a>
				</td>
				<td align="center">
					<a class=menu_item href="{$smarty.server.PHP_SELF}?ac=editar&idcomunicado={$comunicados[i].idcomunicado}">{$comunicados[i].criacao}</a>
				</td>
				<td>
					<a class=menu_item href="{$smarty.server.PHP_SELF}?ac=editar&idcomunicado={$comunicados[i].idcomunicado}">{$comunicados[i].anexos}</a>
				</td>
	        </tr>
	        
	        <tr>
	          <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
	        </tr>
	      	{/section}

      	</table>
      
      	<p align="center" id="nav">{$nav}</p>

		{elseif count($nenhum_resultado)}
      		{include file="div_resultado_nenhum.tpl"}
		{/if}
        
        
        

    {elseif $flags.action == "editar"}

        <br>

		<div style="width: 100%;">

			<form  action="{$smarty.server.PHP_SELF}?ac=editar&idcomunicado={$info.idcomunicado}" method="post" name = "for_comunicado" id = "for_comunicado"  enctype="multipart/form-data">
            <input type="hidden" name="for_chk" id="for_chk" value="1" />


            <ul class="anchors">
            	<li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados Gerais</a></li>
                <li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Condom&iacute;nios</a></li>
			</ul>

            {************************************}
            {* TAB 0 *}
            {************************************}

            <div id="tab_0" class="anchor" style="height:300px;" >

				<table width="95%" align="left">

                	<tr>
                    	<td width="30%" align="right">T&iacute;tulo:</td>
                        <td><input class="long" type="text" name="littitulo" id="titulo" maxlength="200" value="{$info.littitulo}"/></td>
					</tr>

                    <tr>
                    	<td align="right">Descri&ccedil;&atilde;o:</td>
						<td><textarea name="litdescricao" id="descricao" rows='6' cols='38'>{$info.litdescricao}</textarea></td>
					</tr>

                    <tr class="arquivos_comunicado">
                    	<td align="right">Arquivo(s):</td>
                       	<td>
                       		<input type="file" name="arquivo[]" />
                       	</td>
					</tr>

                    <tr>
						<td>&nbsp;</td>
                       	<td>
                    		<input type='button' class="botao_padrao" value="Incluir mais arquivos" name = "Incluir Arquivos" id = "incluir_arquivos" style="clear:both; float:center;"
                  					onclick="incluirArquivos()" />   	
                       	</td>
					</tr>

					{if count($arquivos_comunicado)}
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					
					<tr>
                    	<td align="right">Arquivo(s) j&aacute; associado(s):</td>
                    	<td>&nbsp;</td>
					</tr>

					{assign var="contador" value="0"}
					{section name=i loop=$arquivos_comunicado}
					
						{assign var="contador" value=$contador+1}
						<tr id="arquivo_{$contador}">
							<td>&nbsp;</td>
							<td>
								<a class=menu_item href="{$smarty.server.PHP_SELF}?ac=visualizar_anexo&id_comunicado={$info.idcomunicado}&arquivo={$arquivos_comunicado[i]}">{$arquivos_comunicado[i]}</a>&nbsp;<a href="#" onclick="apaga_arquivo({$contador},{$info.idcomunicado},'{$arquivos_comunicado[i]}')">
								<img src="{$conf.addr}/common/img/delete.gif" /></a> 
							</td>
						</tr>
						
					{/section}
 
					{/if}

				</table>

			</div>


            {************************************}
            {* TAB 1 *}
            {************************************}

			<div id="tab_1" class="anchor" style="height:300px;" >
               
	            <br />
	                     
				<table width="100%" align="center">
	
					<tr>
						<td align="center">Selecione os condom&iacute;nios para os quais deseja enviar os comunicados:</td>
					</tr>
	        		<tr>
	        			<td align="center">
	        				<select multiple class="select_multiplo" name="condominios[]" id="condominios" size="15">
									{html_options values=$condominios.idcliente output=$condominios.nome_cliente selected=$condominios_associados}
							</select>
						</td>
					</tr>
				</table>
            </div>

       	</div>

         <script language="javascript">
             Processa_Tabs(0, 'tab_'); // seta o tab inicial
         </script>

         <table width="95%" align="center">

             <tr><td>&nbsp;</td></tr>

             <tr>
                 <td align="center" colspan="2">

                 </td>
             </tr>

         </table>

         <center>
             <input type='submit' class="botao_padrao" value="ALTERAR" name = "ALTERAR" id = "ALTERAR" style="clear:left;" />
             <input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for_comunicado','{$smarty.server.PHP_SELF}?ac=excluir&idcomunicado={$info.idcomunicado}','ATEN&Ccedil;&Atilde;O! Confirma a exclus&atilde;o ?'))" >
         </center>

	     </form>


		</div>


    {elseif $flags.action == "adicionar"}

        <br>
        <div style="width: 100%;">

            <form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_cliente" id = "for_cliente" enctype="multipart/form-data">
            
                <input type="hidden" name="for_chk" id="for_chk" value="1" />

                <ul class="anchors">
                    <li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados Gerais</a></li>
                    <li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Condom&iacute;nios</a></li>
                </ul>

                {************************************}
                {* TAB 0 *}
                {************************************}

                <div id="tab_0" class="anchor" style="height:300px;" >

                    <table width="95%" align="left">

                        <tr>
                            <td width="30%" align="right">T&iacute;tulo:</td>
                            <td><input class="long" type="text" name="titulo" id="titulo" maxlength="200" value="{$smarty.post.titulo}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Descri&ccedil;&atilde;o:</td>
							<td><textarea name="descricao" id="descricao" rows='6' cols='38'>{$smarty.post.descricao}</textarea></td>
                        </tr>

                        <tr>
                            <td class="req"  align="right">Notificar condom&iacute;nios por email?</td>
                            <td>
                                <input {if $smarty.post.notificar_email=="N"}checked{/if} class="radio" type="radio" name="notificar_email" id="notificar_email" value="N" />N&atilde;o
                                <input {if $smarty.post.notificar_email=="S"}checked{/if} class="radio" type="radio" name="notificar_email" id="notificar_email" value="S" />Sim
                            </td>
                        </tr>
                        
                        <tr class="arquivos_comunicado">
                        	<td align="right">Arquivo(s):</td>
                        	<td>
                        		<input type="file" name="arquivo[]" />
                        	</td>
                        </tr>
                        
                        <tr>
                        	<td>&nbsp;</td>
                        	<td>
                     			<input type='button' class="botao_padrao" value="Incluir mais arquivos" name = "Incluir Arquivos" id = "incluir_arquivos" style="clear:both; float:center;"
                   					onclick="incluirArquivos()" />   	
                        	</td>
                        </tr>

                    </table>

                </div>


                {************************************}
                {* TAB 1 *}
                {************************************}

                <div id="tab_1" class="anchor" style="height:300px;" >
                
	               	<br />
	                     
					<table width="100%" align="center">
	
						<tr>
							<td align="center">Selecione os condom&iacute;nios para os quais deseja enviar os comunicados:</td>
						</tr>
	        			<tr>
	        				<td align="center">
	        					<select multiple class="select_multiplo" name="condominios[]" id="condominios" size="15">
									{html_options values=$condominios.idcliente output=$condominios.nome_cliente selected=$smarty.post.condominios}
								</select>
							</td>
						</tr>
						
					</table>

                </div>

        </div>



        <script language="javascript">
            Processa_Tabs(0, 'tab_'); // seta o tab inicial
        </script>

        <table width="95%" align="center">

            <tr><td>&nbsp;</td></tr>

            <tr>
                <td align="center" colspan="2">

                </td>
            </tr>

        </table>


        <br><br>
        <center>
            <input type='submit' class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar" style="clear:both; float:center;"
                   
                   />
                   
                   <!-- onClick="xajax_Verifica_Campos_ClienteCondominio_AJAX(xajax.getFormValues('for_cliente'));" -->
        </center>

    </form>

</div>




{/if}

{/if}

{include file="com_rodape.tpl"}
