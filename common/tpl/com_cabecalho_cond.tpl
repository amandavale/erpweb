<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <object classid="clsid:2F9082AF-E2A7-4F20-9D6B-3855D79A3D86" id="BemaWeb" width="14" height="14" VIEWASTEXT>
        </object>

        <title>{$conf.name}</title>
        <style type="text/css">@import url("{$conf.addr}/common/css/sigedeco.css");</style>
        {* <style type="text/css">@import url("{$conf.addr}/common/css/autocompletar.css");</style> *}
        <style type="text/css">@import url("{$conf.addr}/common/css/tabs.css");</style>
        <style type="text/css">@import url("{$conf.addr}/common/css/tooltip.css");</style>

        <style type="text/css">@import url("{$conf.addr}/common/lib/bootstrap/css/bootstrap.min.css");</style>


        <script type="text/javascript" src="{$conf.addr}/common/js/global.js"></script>

        {* Inclusões referentes ao calendario em javascript *}
        <style type="text/css">@import url("{$conf.addr}/common/css/calendario/theme.css");</style>
        <script type="text/javascript" src="{$conf.addr}/common/js/calendar.js"></script>
        <script type="text/javascript" src="{$conf.addr}/common/js/calendar-br.js"></script>
        <script type="text/javascript" src="{$conf.addr}/common/js/calendar-setup.js"></script>
        {* ---------------------------------------------------------------- *}

        {$xajax_javascript}

        {* JQuery *}
        <script type="text/javascript" src="{$conf.addr}/common/js/jquery-1.6.1.min.js"></script>
        <script type="text/javascript" src="{$conf.addr}/common/js/jquery.tablesorter.min.js"></script>

        <!-- ToolTip -->
        <style type="text/css">@import url("{$conf.addr}/common/js/jquery-tispy/tipsy.css");</style>
        <script type="text/javascript" src="{$conf.addr}/common/js/jquery-tispy/jquery.tipsy.js"></script>



        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
        <meta http-equiv="pragma" content="no-cache" />
        <meta http-equiv="expires" content="-1" />
        <meta name="robots" content="all" />
        <meta name="author" content="F6 Sistemas LTDA" />
        <meta name="description" content="ERPWEB" />
        <meta name="keywords" content="ERPWEB" />
    </head>

    <body leftmargin="0" topmargin="1"> 
        <div id="TooltipContainer" name="TooltipContainer" onmouseover="holdTooltip();" onmouseout="swapTooltip('default');"></div>

        <div id = 'header'>
            <div id = 'logocliente'>
                <div id = 'auth'>
                    {if $smarty.session.cond_login}
                        <p align='center'><br />

                            Voc&ecirc; est&aacute; logado como: <b>{$smarty.session.usr_nom}</b>
                            <br />
                            <a target="_parent" class="link_geral" href="{$conf.addr}/condominio/index.php?ac=logout">Sair do ERPWEB</a>
                        </p>
                    {/if}
                </div>	
            </div>

            <div id = 'logoerpweb'></div>
        </div>
        <div id = 'linhalaranja'></div>
        <div id = 'wrapper'>	

            <div id = 'programa'>

            </div>

        {if $smarty.session.cond_login}

                <table class="tb4cantosAzul" width="100%"  border="0" cellpadding="5" cellspacing="0">
                    <tr>
                        <td class="tela" style="width:130px; height:20px;">
                            Você está em: <span class="descricao_tela" style="text-align:left;">{$conf.area}</span>
                        </td>

                        {if $smarty.session.condominio.nome_condominio}
                            <td class="tela" style="width:500px;">

                                Condomínio {if $smarty.session.maisDeUmCondominio}Selecionado{/if}:

                                <span class="descricao_tela" style="text-align:left;">
                                    {$smarty.session.condominio.nome_condominio}
                                {if $smarty.session.maisDeUmCondominio} &nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$conf.addr}/condominio">Alterar Seleção</a>{/if}
                            </span>

                        </td>
                    {/if}
                </tr>
            </table>


       	{/if}

        <center>


            {if $smarty.session.cond_login && $conf.nome_programa != 'index' && $flags.okay}
                <table style="float:left; background-color:#fff; width:180px;" class="subtable" >
                    <thead>
                        <tr><th>Menu Principal</th></tr>
                    </thead>		
                    <tbody style="text-align:center;">
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td>
                                <a class="link_geral" href="{$conf.addr}/condominio/boleto.php"><img src="{$conf.addr}/common/img/boleto.png" /><br />2&ordf; via de Boleto</a>
                            </td>	
                        </tr>
                        
                        <tr><td><br /></td></tr>

                        <tr>
                            <td>
                                <a class="link_geral" href="{$conf.addr}/condominio/demonstrativo.php"><img src="{$conf.addr}/common/img/demonstrativo.jpg" /><br />Demonstrativo Financeiro</a>
                            </td>
                        </tr>
                        
                        <tr><td><br /></td></tr>

                        <tr>
                            <td>
                                <a class="link_geral" href="{$conf.addr}/condominio/comunicado.php"><img src="{$conf.addr}/common/img/comunicado.png" /><br />Comunicados</a>
                            </td>
                        </tr>
                        
                        <tr><td><br /></td></tr>

                        <tr>
                            <td>
                                <a class="link_geral" href="http://www.sosprestadora.com.br" target="_blank"><img src="{$conf.addr}/common/img/website.png" /><br />Website SOS Prestadora</a>
                            </td>
                        </tr>
                        
                        <tr><td><br /></td></tr>

                        {if $smarty.session.maisDeUmCondominio}
                            <tr>
                                <td>
                                    <a class="link_geral" href="{$conf.addr}/condominio/"><img src="{$conf.addr}/common/img/condominio.png" /><br />Selecionar Outro Condomínio</a>
                                </td>	
                            </tr>
                        {/if}
                        <tr><td><br /></td></tr>
                    </tbody>

                </table>


                <div id="menuDivider" style="float:left;  width:1px; height:100%; "></div>              
                

            {/if}