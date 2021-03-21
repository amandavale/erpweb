{include file="com_cabecalho_cond.tpl"} 

{include file="div_erro.tpl"}

{include file="div_login_cond.tpl"}

{if $flags.okay}

	{if $flags.action == "listar"}

    	{if count($lista_comunicados)}

		<table class="" id="tbl_comunicado" name="tbl_comunicado" align="center" style="width:700px; margin-top:20px; border:1px solid #000;" >

			<thead>
            	<tr>
                    <th class="header" align="center" width="25%" >Data de cadastro</th>
                	<th class="header" align="center">Anexos</th>
                    <th class="header" align="center" width="10%" >Ver detalhes</th>
				</tr>
			</thead>

			<tbody>
			{foreach from=$lista_comunicados key=k item=comunicado}
				<tr class="{if $k%2}tr_cor1{else}tr_cor2{/if}"  >
					<td align="center" >
						{$comunicado.criacao}
					</td>
					<td align="center" >
						{$comunicado.anexos}
					</td>
					<td align="center">
						<a class="link_geral" href="{$conf.addr}/condominio/comunicado.php?ac=detalhar&id_comunicado={$comunicado.idcomunicado}">
							<img src="{$conf.addr}/common/img/detalhar.png" /></a>	
					</td>
               	{/foreach}
			</tbody>
		</table>
	    {else}
	        <table>
	            <tr>
	                <td>
	                    <table border="0" cellpadding="2" cellspacing="3" bgcolor="#FDF5E6" class="tb4cantos" style="margin-top:100px">
	                        <tr ><td><b>N&atilde;o h&aacute; comunicados registrados no momento.</b></td></tr>
	                    </table>
	                </td>
	            </tr>
	        </table>
	    {/if}

	{elseif $flags.action == "detalhar"}
	
		<table class="" id="tbl_comunicado" name="tbl_comunicado" align="center" style="width:600px; margin-top:20px; border:1px solid #000;" >
		
			{if $dados_comunicado.titulo != ""}
			<tr>
				<td width="30%"><b>T&iacute;tulo:</b></td>
				<td>{$dados_comunicado.titulo}</td>
			</tr>

			<tr><td colspan="2">&nbsp;</td></tr>
			{/if}

			{if $dados_comunicado.descricao != ""}
			<tr>
				<td width="30%"><b>Descri&ccedil;&atilde;o:</b></td>
				<td>{$dados_comunicado.descricao}</td>
			</tr>

			<tr><td colspan="2">&nbsp;</td></tr>
			{/if}
			
			<tr>
				<td width="30%"><b>Data de cadastro:</b></td>
				<td>{$dados_comunicado.criacao}</td>
			</tr>

			<tr><td colspan="2">&nbsp;</td></tr>
			
			
			{if count($arquivos_comunicado)}
			
			<tr>
				<td  width="30%"><b>Arquivos anexados:</b></td>
				<td>&nbsp;</td>
			</tr>

			{section name=i loop=$arquivos_comunicado}
			<tr>
				<td>&nbsp;</td>
				<td>
					<a href="{$conf.addr}/condominio/comunicado.php?ac=baixar_arquivo&id_comunicado={$dados_comunicado.idcomunicado}&nome_arquivo={$arquivos_comunicado[i]}">{$arquivos_comunicado[i]}</a> 
				</td>
			</tr>
			{/section}
			
			
			{/if}
			
		</table>
		
	
	
	{/if}	    
	 


                        </center>

                        {/if} {* Se o browser for IE, verifica se houve alguma queda de energia
                            no micro *} {if ($smarty.session.browser_usuario == "0") &&
($smarty.session.usr_cod != "") }
                            <script language="javascript">
                                VerificaQuedaEnergiaTEF();
                            </script>
                            {/if} {include file="com_rodape.tpl"}

