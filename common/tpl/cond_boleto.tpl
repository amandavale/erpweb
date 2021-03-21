{include file="com_cabecalho_cond.tpl"} 

{include file="div_erro.tpl"}

{include file="div_login_cond.tpl"}

<script type="text/vbscript" src="{$conf.addr}/common/js/orcamento.vbs"></script>
<script type="text/vbscript" src="{$conf.addr}/common/js/inicio.vbs"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/orcamento.js"></script>
{literal}
    <script type="text/javascript">

    $(function() {

        $(".link_boleto").tipsy({html: true, opacity: 0.8 });

    });

    </script>

    <style>

        .vencido td{color:#F00;}

    </style>
{/literal}
{if $flags.okay}


    {if count($list_movimento)}

        <form name="for" id="for" action="" method="post">


            <table class="" id="tbl_movimento" name="tbl_movimento " align="center" style="width:700px; margin-top:20px; border:1px solid #000;" >

                <thead>
                    <tr>
                        <th class="header" align="center" width="5%" >Apto.</th>
                        <th class="header" align="center" width="5%" >Valor</th>
                        <th class="header" align="center" width="5%" >Data de Vencimento</th>
                        <th class="header" align="center" width="5%" >Quitado em</th>
                        <th class="header" align="center" width="5%" >Boleto / 2&ordf; Via</th>
                    </tr>
                </thead>

                <tbody>
                    {foreach from=$list_movimento key=k item=movimento}
                        <tr class="{if $k%2}tr_cor1{else}tr_cor2{/if} {if $movimento.boleto_2via.vencido && $movimento.baixado != '1' }vencido{/if}"  >
                            <td align="center" > 
                                {$movimento.apto}
                            </td>
                            <td align="right" >
                                {$movimento.valor_movimento}
                            </td>
                            <td align="center">
                                {$movimento.data_vencimento}
                            </td>
                            <td align="center">
                                {$movimento.data_baixa}
                            </td>
                            <td align="center">
                                {if $movimento.baixado == '1'}
                                    <span style="cursor:default" title="Boleto Quitado">--</span>
                                {else}
                                    <a  {if $movimento.boleto_2via.vencido}
                                        original-title="<div style='width:120px; margin:2px 2px 15px 2px;'>
                                        <div style='float:left'>Valor Original:</div><div style='float:right'>&nbsp;&nbsp;{$movimento.valor_movimento}</div><br />
                                        <div style='float:left'>Multa:</div><div style='float:right'>{$movimento.boleto_2via.multa}</div><br />
                                        <div style='float:left'>Juros:</div><div style='float:right'>{$movimento.boleto_2via.juros}</div><br />
                                        <div style='float:left'>Total:</div><div style='float:right'>{$movimento.boleto_2via.valor_total}</div>
                                        </div>" 
                                    {/if}
                                    class="link_boleto"
                                    href="{$smarty.server.phpself}?ac=gerar_boleto&idmovimento={$movimento.idmovimento}" target="_blank"><img src="{$conf.addr}/common/img/boleto.jpeg" alt="Imprimir"></a>
                                    {/if}
                                </td>
                            </tr>

                            {/foreach}
                            </tbody>
                        </table>

                    </form>
    {else}
        <table>
            <tr>
                <td>
                    <table border="0" cellpadding="2" cellspacing="3" bgcolor="#FDF5E6" class="tb4cantos" style="margin-top:100px">
                        <tr ><td><b>Não há cobranças registradas no momento.</b></td></tr>
                    </table>
                </td>
            </tr>
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

