

        {if $flags.sucesso != ""}
            {include file="div_resultado_inicio.tpl"}
            {$flags.sucesso}
            {include file="div_resultado_fim.tpl"}
        {/if}


        <br><br>

        <form  action="{$smarty.server.PHP_SELF}?ac=listar{if $smarty.get.boletos}&boletos=1{/if}" method="post" name = "for" id = "for" >
            <input type="hidden" name="for_chk" id="for_chk" value="1" />
            {include file="adm_movimento/div_campos_busca.tpl}
        </form>

        <br><br>


        {if count($list)}


            <form  action="{$smarty.server.PHP_SELF}?ac=listar&target=full&{$parBusca}&pg=0" method="post" name = "for" id = "for" target="_blank">
                <center> <input name="Submit" type="submit" class="botao_padrao" value="Tela de Impress&atilde;o"> </center>


                <br />

                <p align="center">Listando {$conf.area} de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p> 
                <p align="center" id="nav_top">{$nav}</p>


                <table align="center" width="100%"  class="tablesorter" id="tbl_movimento" name="tbl_movimento"	>

                    <thead> 
                        <tr>
                            <th align="center" width="3%"  class="header">C&oacute;d.</th>
                            <th align="center" width="25%" class="header">Descri&ccedil;&atilde;o</th>
                            <th align="center" width="20%"  class="header">Apto.</th>
                            <th align="center" width="20%" class="header">Cliente</th>
                            <th align="center" width="10%"  class="header">Cr&eacute;dito</th>
                            <th align="center" width="10%"  class="header">D&eacute;bito</th>
                            <th align="center" width="5%"  class="header">Baixa</th>
                            <th align="center" width="5%"  class="header">Vencimento</th>
                            <th align="center" width="5%"  class="header">Baixado?</th>
                            <th align="center" width="5%"  class="header">Tipo de baixa</th>
                            {if $smarty.get.boletos == '1'}
                                <th align='center' width="5%" class="header">Imprimir Boleto</th>
                            {/if}
                        </tr>
                    </thead>

                    <tbody> 
                        {section name=i loop=$list}

                            {if $list[i].idmovimento}
                                <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >	    						
                                    <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$list[i].idmovimento}{if $smarty.get.pg}&pg={$smarty.get.pg}{/if}">{$list[i].idmovimento}</a></td>
                                    <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$list[i].idmovimento}{if $smarty.get.pg}&pg={$smarty.get.pg}{/if}">{$list[i].descricao_movimento}</a></td>
                                    <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$list[i].idmovimento}{if $smarty.get.pg}&pg={$smarty.get.pg}{/if}">{$list[i].apto_completo}</a></td>
                                    <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$list[i].idmovimento}{if $smarty.get.pg}&pg={$smarty.get.pg}{/if}">{$list[i].nome_cliente}</a></td>
                                    <td align="right"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$list[i].idmovimento}{if $smarty.get.pg}&pg={$smarty.get.pg}{/if}">{$list[i].valor_credito}</a></td>
                                    <td align="right"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$list[i].idmovimento}{if $smarty.get.pg}&pg={$smarty.get.pg}{/if}">{$list[i].valor_debito}</a></td>
                                    <td align="center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$list[i].idmovimento}{if $smarty.get.pg}&pg={$smarty.get.pg}{/if}">{$list[i].data_baixa}</a></td>                                   
                                    <td align="center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$list[i].idmovimento}{if $smarty.get.pg}&pg={$smarty.get.pg}{/if}">{$list[i].data_vencimento}</a></td>
                                    <td align="center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$list[i].idmovimento}{if $smarty.get.pg}&pg={$smarty.get.pg}{/if}">{if $list[i].baixado == 1} Sim {else} N&atilde;o {/if}</a>
                                    </td>
                                    <td align="center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$list[i].idmovimento}{if $smarty.get.pg}&pg={$smarty.get.pg}{/if}">{if $list[i].tipo_baixa == "M"} Manual {elseif $list[i].tipo_baixa == "A"}Automática{else} &nbsp; {/if}</a>
                                    </td>

                                    {if $smarty.get.boletos == '1'}
                                        <td align="center">
                                            <a href="#" onClick="xajax_Emite_Boleto_Ajax(xajax.getFormValues('for'),{$list[i].idmovimento})"> <img src="{$conf.addr}/common/img/boleto.jpeg" alt="Imprimir"/></a>	
                                        </td>
                                    {/if}
                                </tr>
                            {else}
                            </tbody> 
                            <tfoot>
                                <tr>
                                    <td colspan="4" align="right">Totais:&nbsp;</td>
                                    <td align="right"><b>{$list[i].valor_credito}</b>&nbsp;&nbsp;</td>

                                    <td align="right"><b>{$list[i].valor_debito}</b>&nbsp;&nbsp;</td>

                                    <td colspan="{if $smarty.get.boletos == '1'}5{else}4{/if}" align="right">&nbsp;</td>
                                </tr>
                            </tfoot>
                        {/if}
                    {/section}
                </table>




                <p align="center" id="nav">{$nav}</p>


                <center> <input name="Submit2" type="submit" class="botao_padrao" value="Tela de Impressão"> </center>
            </form>

        {elseif count($smarty.server.post) > 0}
            {include file="div_resultado_nenhum.tpl"}
        {/if}

