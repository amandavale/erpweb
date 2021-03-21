{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}

<!--{*  Chama o script para ordenar a tabela  *}-->
<script language="javascript">

    {literal}

        //Método para não dar conflito com o auto-complete do xajax
        var $j = jQuery.noConflict();
    
        $j(document).ready(function()
        {
            $j("#tbl_clientes_inadimplentes").tablesorter(); 
        });
        
    {/literal}
</script>


{if $flags.okay}

    <table class="tb4cantosAzul" width="100%"  border="0" cellpadding="5" cellspacing="0">
        <tr>
            <td class="tela" WIDTH="5%" height="20">
                Tela:
            </td>
            <td class="descricao_tela" WIDTH="95%">
                {$conf.area}
            </td>
        </tr>
    </table>



    {if $flags.action == "listar"}

        {if count($clientes)} 


            <table align="center" width="99%" class="tablesorter" id="tbl_clientes_inadimplentes" name="tbl_clientes_inadimplentes">

                <thead> 

                    <tr align=center>
                        <th class="header">C&oacute;digo<br />do cliente</th>
                        <th class="header">Nome</th>
                        <th class="header">C&oacute;digo<br />do movimento</th>
                        <th class="header">Movimento</th>
                        <th class="header">Vencimento</th>
                        <th class="header">Valor (R$)</th>
                    </tr>
                </thead>

                <tbody>
                {section name=i loop=$clientes}

                    <tr  bgcolor = "{if $clientes[i].index % 2 == 0}#F7F7F7{else}WHITE{/if}" >
                        <td align='right'>{if $clientes[i].idcliente}{$clientes[i].idcliente}{else}&nbsp;{/if}</td>
                        <td align='center'>{if $clientes[i].nome_cliente}{$clientes[i].nome_cliente}{else}&nbsp;{/if}</td>
                        <td align='right'>{$clientes[i].idmovimento}</td>
                        <td align='center'>{$clientes[i].descricao_movimento}</td>
                        <td align='center'>{$clientes[i].data_vencimento}</td>
                        <td align='right'>{$clientes[i].valor_movimento}</td>
                    </tr>
                {/section}

                </tbody>
            </table>

            <table align="center" width="99%" >
                <tbody>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td align="right" style="font-size:1.1em;">Valor total: <b>R$ {$total}</b></td>
                    </tr>
                <tbody>
            </table>


        {else}

            {if $smarty.post.for_chk}
                {include file="div_resultado_nenhum.tpl"}
            {/if}

        {/if}		

    {/if}

{/if}

{include file="com_rodape.tpl"}
