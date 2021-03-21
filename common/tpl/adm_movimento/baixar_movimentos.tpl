
        {if $flags.sucesso != ""}
            {include file="div_resultado_inicio.tpl"}
            {$flags.sucesso}
            {include file="div_resultado_fim.tpl"}
        {/if}

        <form  action="{$smarty.server.PHP_SELF}?ac=baixar_movimentos" method="post" name = "for" id = "for" >
            <input type="hidden" name="chk_busca" id="chk_busca" value="1" />

            <div id="campos_busca">
                {include file="adm_movimento/div_campos_busca.tpl}
            </div>
        </form>



        {if count($list)}


            <form  action="{$smarty.server.PHP_SELF}?ac=listar&target=full&{$parBusca}&pg=0" method="post" name = "for_print" id = "for_print" target="_blank">
                <center> <input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão"> </center>
            </form>

            <form  action="{$smarty.server.PHP_SELF}?ac=baixar_movimentos" method="post" name = "for_baixa" id = "for_baixa" >	
                <input type="hidden" name="chk_baixa" id="chk_baixa" value="1" />

                <br><br><br>			

                <table align="center" class="tb4cantos" width="300 px;" >
                    <tr><td colspan="2" ><strong>Defina a Data e Hora para baixa das contas:</strong></td>

                    <tr>
                        <td align="right">Data da Baixa:</td>
                        <td>
                            <input class="short" type="text" name="data_baixa_D" id="data_baixa_D" value="{$conf.data_atual}" maxlength='10' onkeydown="mask('data_baixa_D', 'data')" onkeyup="mask('data_baixa_D', 'data')" />
                            <img src="{$conf.addr}/common/img/calendar.png" id="img_data_baixa_D" style="cursor: pointer;" /> (dd/mm/aaaa)
                        </td>
                    </tr>


                    <tr>
                        <td align="right">Hora da Baixa:</td>
                        <td valign="bottom">
                            <input class="short" type="text" name="data_baixa_H" id="data_baixa_H" value="{$info.data_baixa_H}" maxlength='8' onkeydown="mask('data_baixa_H', 'hora')" onkeyup="mask('data_baixa_H', 'hora')" /> 
                            <img src="{$conf.addr}/common/img/clock.jpg" id="img_data_baixa_H" style="cursor: pointer;" onclick="setNow('data_baixa_H');" alt="Inserir horário atual" /> (hh:mm:ss)
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input type="button" class="botao_padrao" name="btn_baixar" id="btn_baixar" value="Baixar Contas Selecionaras" 
                                   onClick="xajax_Verifica_Campos_Baixa_AJAX(xajax.getFormValues('for_baixa'));" />
                        </td>
                    </tr>

                    <script type="text/javascript">
                        Calendar.setup(
                        {ldelim}
                                        inputField : "data_baixa_D", // ID of the input field
                                        ifFormat : "%d/%m/%Y", // the date format
                                        button : "img_data_baixa_D", // ID of the button
                                        align  : "cR"  // alinhamento
                        {rdelim}
                                    );
						
                                        setNow('data_baixa_H');
                    </script>
                </table>

                <br><br><br>
                <p align="center" id="nav">{$nav}</p> <br>
                <table align="center" width="100%"  class="tablesorter" id="tbl_movimento" name="tbl_movimento"	>			
                    <thead> 

                        <tr>
                            <th width="100" align="center" class="header">Descrição</th>
                            <th width="140" align="center" class="header">Cliente de Origem</th>
                            <th width="140" align="center" class="header">Cliente de Destino</th>
                            <th width="30"  align="center" class="header">Apto.</th>
                            <th width="25"  align="center" class="header">Valor Original</th>
                            <th width="25"  align="center" class="header">Juros</th>
                            <th width="25"  align="center" class="header">Multa</th>
                            <th width="25"  align="center" class="header">Desconto</th>
                            <th width="55"  align="center" class="header">Data de Ocorrência</th>
                            <th width="55"  align="center" class="header">Data de Vencimento</th>
                            <th width="35"  align="center" class="header">Baixar</th>		
                        </tr>

                    </thead>

                    <tbody> 
                        {section name=i loop=$list}
                            {if $list[i].idmovimento}
                                <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >		
                                    <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$list[i].idmovimento}">{$list[i].descricao_movimento}</a></td>
                                    <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$list[i].idmovimento}">{$list[i].nome_cliente_orig}</a></td>
                                    <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$list[i].idmovimento}">{$list[i].nome_cliente_dest}</a></td>
                                    <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$list[i].idmovimento}">{$list[i].apto}</a></td>
                                    <td align="right"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$list[i].idmovimento}">{$list[i].valor_movimento}</a></td>
                                    <td align="center"><input class="short" type="text" name="juros_{$list[i].idmovimento}" id="juros_{$list[i].idmovimento}" value="0,00" maxlength='8' onkeydown='FormataValor("juros_{$list[i].idmovimento}")' onkeyup='FormataValor("juros_{$list[i].idmovimento}")' /></td>
                                    <td align="center"><input class="short" type="text" name="multa_{$list[i].idmovimento}" id="multa_{$list[i].idmovimento}" value="0,00" maxlength='8' onkeydown='FormataValor("multa_{$list[i].idmovimento}")' onkeyup='FormataValor("multa_{$list[i].idmovimento}")' /></td>
                                    <td align="center"><input class="short" type="text" name="desconto_{$list[i].idmovimento}" id="desconto_{$list[i].idmovimento}" value="0,00" maxlength='8' onkeydown='FormataValor("desconto_{$list[i].idmovimento}")' onkeyup='FormataValor("desconto_{$list[i].idmovimento}")' /></td>																	
                                    <td align="center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$list[i].idmovimento}">{$list[i].data_movimento}</a></td>
                                    <td align="center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$list[i].idmovimento}">{$list[i].data_vencimento}</a></td>
                                    <td align="center"><input type="checkbox" name="baixar[]" value="{$list[i].idmovimento}" tabindex="{$list[i].index}" /></td>
                                </tr>
                            {else}
                            </tbody> 
                            <tfoot>
                                <tr>
                                    <td colspan="3" align="right">&nbsp;</td>
                                    <td><b>{$list[i].descricao_movimento}</b></td>
                                    <td align="right"><b>{$list[i].valor_movimento}</b>&nbsp;</td>
                                    <td colspan="6" align="right">&nbsp;</td>
                                </tr>
                            </tfoot>
                        {/if}
                    {/section} 
                </table>

            </form>


            <p align="center" id="nav">{$nav}</p>

            <form  action="{$smarty.server.PHP_SELF}?ac=listar&target=full&{$parBusca}&pg=0" method="post" name = "for_print2" id = "for_print2" target="_blank">
                <center> <input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão"> </center>
            </form>

        {elseif $smarty.post.chk_busca == '1'}

            {include file="div_resultado_nenhum.tpl"}

        {/if}
