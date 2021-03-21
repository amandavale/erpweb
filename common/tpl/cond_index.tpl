{include file="com_cabecalho_cond.tpl"}

{include file="div_erro.tpl"}

{include file="div_login_cond.tpl"}

{if $flags.okay}

    <table width="100%"  border="0" cellpadding="0" cellspacing="0">

        <tr>
            <td width="25" height="20">&nbsp;</td>
            <td>&nbsp;</td>
        </tr>

        <tr>
            <td>

            </td>
        </tr>

        <tr>
            <td height="10">&nbsp;</td>
            <td align="center" valign="bottom">&nbsp;</td>
        </tr>
    </table>



    <center>
        <h3>Selecione o condomínio:</h3>

        <table class="subtable" id="tbl_condominio" align="center" style="width:600px;">
            <thead>
                <tr>
                    <th class="header" align="center">No</th>
                    <th class="header" align="center">Nome do Condomínio</th>
                </tr>
            </thead>

            <tbody>
                {foreach from=$list_condominios item=condominio key=k}
                    <tr class="{if $k%2}tr_cor1{else}tr_cor2{/if}">
                        <td><a class="menu_item" href="{$smarty.server.phpself}?condominio={$condominio.idcliente}">{$k+1}</a></td>
                        <td><a class="menu_item" href="{$smarty.server.phpself}?condominio={$condominio.idcliente}">{$condominio.nome_cliente}</a></td>
                    </tr>			    
                {/foreach}
                <!--tr class="tr_cor2">
                                <td><a class="menu_item" href="/%7Eleandro/projetos/erpweb/sosprestadora/condominio/boleto.php?ac=editar&amp;idcliente=43">2</a></td>
                                <td><a class="menu_item" href="/%7Eleandro/projetos/erpweb/sosprestadora/condominio/boleto.php?ac=editar&amp;idcliente=43">Condomínio Edifício Angelo</a></td>
                                
                </tr>				    
                        <tr class="tr_cor1">
                                <td><a class="menu_item" href="/%7Eleandro/projetos/erpweb/sosprestadora/condominio/boleto.php?ac=editar&amp;idcliente=639">3</a></td>
                                <td><a class="menu_item" href="/%7Eleandro/projetos/erpweb/sosprestadora/condominio/boleto.php?ac=editar&amp;idcliente=639">Condomínio Edifício Aníbal Lopes</a></td>					
                </tr-->			    
            </tbody>
        </table>
    </center>

{/if}

{* Se o browser for IE, verifica se houve alguma queda de energia no micro *}
{if ($smarty.session.browser_usuario == "0") && ($smarty.session.usr_cod != "") }
    <script language="javascript">
        VerificaQuedaEnergiaTEF();
    </script>
{/if}

{include file="com_rodape.tpl"}

