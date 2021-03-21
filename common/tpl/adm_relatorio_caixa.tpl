
{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}



{* Chama o CKEditor para exibir ferramenta de formatação de texto em textarea *}
<script type="text/javascript" src="{$conf.addr}/common/lib/ckeditor/ckeditor.js"></script>

{if $flags.action != "demonstrativo" || ($smarty.post.idcliente == '' && $flags.action == "demonstrativo")}
    <script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>
{/if}

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
            <td class="descricao_tela" WIDTH="95%">
                {$conf.area}
            </td>
        </tr>
    </table>



    {if $flags.action == "listar"}

        <br>

        <form  action="{$smarty.server.PHP_SELF}?ac=listar" method="post" name="for" id="for">
            <input type="hidden" name="for_chk" id="for_chk" value="1" />
            {include file="adm_relatorio_caixa/campos_busca.tpl"}
        </form>		


        {if count($list_caixa)} 


            <table align="center" width="99%">

                <tr align=center>
                    <th>Baixa</th>
                    <th>Vencimento</th>
                    <th width="50px">Cód.</th>
                    <th width="50px">Apto.</th>
                    <th>Origem</th>
                    <th>Destino</th>
                    <th>Descrição</th>
                    <th align="100">Crédito</th>
                    <th align="100">Débito</th>
                    <th align="100">Saldo</th>
                </tr>

                {foreach from=$list_caixa.registros key=k item=caixa}

                    <tr  bgcolor = "{if $k%2 != 0}#E7E7E7{else}#FFFFFF{/if}" >
                        <td>{if $caixa.idmovimento}<a href="{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$caixa.idmovimento}" target="_blank" class="link_geral">{/if}{$caixa.data_baixa}		 {if $caixa.idmovimento}</a>{/if}</td>
                        <td>{if $caixa.idmovimento}<a href="{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$caixa.idmovimento}" target="_blank" class="link_geral">{/if}{$caixa.data_vencimento}		 {if $caixa.idmovimento}</a>{/if}</td>
                        <td align="center">{if $caixa.idmovimento}<a href="{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$caixa.idmovimento}" target="_blank" class="link_geral">{/if}{$caixa.idmovimento}			 {if $caixa.idmovimento}</a>{/if}</td>
                        <td>{if $caixa.idmovimento}<a href="{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$caixa.idmovimento}" target="_blank" class="link_geral">{/if}{$caixa.apto}			 {if $caixa.idmovimento}</a>{/if}</td>
                        <td>{if $caixa.idmovimento}<a href="{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$caixa.idmovimento}" target="_blank" class="link_geral">{/if}{$caixa.origem}			 {if $caixa.idmovimento}</a>{/if}</td>
                        <td>{if $caixa.idmovimento}<a href="{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$caixa.idmovimento}" target="_blank" class="link_geral">{/if}{$caixa.destino}			 {if $caixa.idmovimento}</a>{/if}</td>
                        <td>{if $caixa.idmovimento}<a href="{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$caixa.idmovimento}" target="_blank" class="link_geral">{/if}{$caixa.descricao_movimento}{if $caixa.idmovimento}</a>{/if}</td>
                        <td align="right" >{if $caixa.tipo == 'credito'}{$caixa.valor_movimento}{/if}</td>
                        <td align="right" >{if $caixa.tipo == 'debito' }{$caixa.valor_movimento}{/if}</td>
                        <td align="right" >{$caixa.saldo}</td>
                    </tr>

                    <tr><td class="row" height="1" bgcolor="#999999" colspan="20"></td></tr>

                {/foreach}

                <tr  bgcolor = "#FFFFFF" >
                    <td colspan="6">&nbsp;</td>
                    <td><b>Total</b></td>
                    <td align="right" ><b>{$list_caixa.somatorio.receitas}</b></td>
                    <td align="right" ><b>{$list_caixa.somatorio.despesas}</b></td>
                    <td align="right" ><b>{$list_caixa.somatorio.saldo_final}</b></td>
                </tr>

            </table>

            <table width="95%" align="center">
                <form action="{$smarty.server.PHP_SELF}?ac=listar&target=full" method="post" name = "for" id = "for" target="_blank">
                    <input type="hidden" name="for_chk" id="for_chk" value="1" />

                    <input type="hidden" name="idcliente_relatorio" id="idcliente_relatorio" value="{$smarty.post.idcliente}" />
                    <input type="hidden" name="data_baixa_de_relatorio" id="data_baixa_de_relatorio" value="{$smarty.post.data_baixa_de}" />
                    <input type="hidden" name="data_baixa_ate_relatorio" id="data_baixa_ate_relatorio" value="{$smarty.post.data_baixa_ate}" />
                    <input type="hidden" name="cliente_Nome" id="cliente_Nome" value="{$smarty.post.idcliente_Nome}" />

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

            {if $smarty.post.for_chk}
                {include file="div_resultado_nenhum.tpl"}
            {/if}

        {/if}		




    {elseif $flags.action == "demonstrativo"}


        {if $smarty.post.idcliente}

            <table align="center" width="25%">
                <tr>
                    <td align="right" style="font-size:15px;"><b>Cliente:</b></td>
                    <td style="font-size:15px;">{$smarty.post.idcliente_Nome} </td>
                    <td><a href="{$conf.addr}/admin/relatorio_caixa.php?ac=demonstrativo" class="link_geral">Alterar seleção</a></td>
                </tr>
                <tr>
                    <td align="right" style="font-size:15px;"><b>Período:</b></td>
                    <td style="font-size:15px;">{$listMesAno.mes[$smarty.post.mesBaixa]} / {$smarty.post.anoBaixa}</td>
                </tr>
            </table>

            <form  action="{$smarty.server.PHP_SELF}?ac=demonstrativo" method="post" name="for" id="for">
                <input type="hidden" name="for_chk" id="for_chk" value="1" />
                <input type="hidden" name="dataMsg" id="dataMsg" value="" />
                <input type="hidden" name="anoBaixa" id="anoBaixa" value="{$smarty.post.anoBaixa}" />
                <input type="hidden" name="mesBaixa" id="mesBaixa" value="{$smarty.post.mesBaixa}" />
                <input type="hidden" name="idcliente" id="idcliente" value="{$smarty.post.idcliente}" />
                <input type="hidden" name="idcliente_Nome" id="idcliente_Nome" value="{$smarty.post.idcliente_Nome}" />
            </form>
        {else}

            <form  action="{$smarty.server.PHP_SELF}?ac=demonstrativo" method="post" name="for" id="for">
                <input type="hidden" name="for_chk" id="for_chk" value="1" />
                {include file="adm_relatorio_caixa/campos_busca.tpl"}
                <input type="hidden" name="dataMsg" id="dataMsg" value="" />
            </form>

        {/if}

        <form action="{$smarty.server.PHP_SELF}?ac=demonstrativo&target=full" method="post" name = "for_print" id = "for_print" target="_blank">


            <input type="hidden" name="for_chk" id="for_chk" value="1" />
            <input type="hidden" name="idcliente_relatorio" id="idcliente_relatorio" value="{$smarty.post.idcliente}" />
            <input type="hidden" name="data_baixa_de_relatorio" id="data_baixa_de_relatorio" value="{$smarty.post.data_baixa_de_relatorio}" />
            <input type="hidden" name="data_baixa_ate_relatorio" id="data_baixa_ate_relatorio" value="{$smarty.post.data_baixa_ate_relatorio}" />
            <input type="hidden" name="cliente_Nome" id="cliente_Nome" value="{$smarty.post.idcliente_Nome}" />

            <br>

            {if $list} 

                <table width="95%" align="center">		
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td align="center">
                            <input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão">
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>	
                </table>

                <table align="center" width="600px">               
                    <tr align=center>
                        <th colspan="2">DESPESAS</th>	
                    </tr>
                    {foreach from=$list.debito.contas key=k item=despesa}
                        <tr  bgcolor = "{if $k%2 != 0}#E7E7E7{else}#FFFFFF{/if}" >
                            <td>{$despesa.descricao_movimento}</td>
                            <td  align="right">{$despesa.valor_movimento}</td>
                        </tr>
                    {/foreach}
                    <tr bgcolor="C7C7C7">
                        <td><b>TOTAL DAS DESPESAS</b></td>
                        <td align="right"><b>{$list.somatorio.debito}</b></td>
                    </tr>					

                </table>

                <br /> <br />

                <table align="center" width="600px">

                    <tr align=center>
                        <th colspan="2">SALDO</th>	
                    </tr>

                    <tr bgcolor="FFFFFF"> 
                        <td>SALDO DO PERÍODO ANTERIOR</td>
                        <td align="right">{$list.somatorio.saldo_anterior}</td>
                    </tr>

                    <tr bgcolor="#E7E7E7"> 
                        <td>RECEITAS DO PERÍODO</td>
                        <td align="right">{$list.somatorio.credito}</td>
                    </tr>

                    <tr bgcolor="FFFFFF"> 
                        <td>DESPESAS DO PERÍODO</td>
                        <td align="right">{$list.somatorio.debito}</td>
                    </tr>

                    <tr bgcolor="C7C7C7"> 
                        <td><b>SALDO DO PERÍODO</b></td>
                        <td align="right"><b>{$list.somatorio.saldo_final}</b></td>
                    </tr>		
                </table>

                <br /> <br />

                <table align="center" width="600px">
                    <thead>                    
                        <tr align=center>
                            <th colspan="4">CONDÔMINOS EM ATRASO</th>	
                        </tr>
                        {if $list.em_aberto.qtd == 0}

                            <tr bgcolor="#CCCCCC">
                                <td align="center">Não há pagamentos em aberto para este período.</td>
                            </tr>

                        {else}		
                            <tr bgcolor="#CCCCCC">
                                <td width="150px" align="center">Cód.</td>
                                <td width="200px" align="center">Data de Vencimento</td>
                                <td width="150px" align="center">Apto.</td>
                                <td width="100px" align="center">Valor</td>                      
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$list.em_aberto.registros key=k item=pgto}
                                <tr  bgcolor = "{if $k%2 != 0}#E7E7E7{else}#FFFFFF{/if}" >
                                    <td align="center"><a class="link_geral" href="{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$pgto.idmovimento}" target="_blank">{$pgto.idmovimento}</a></td>
                                    <td align="center"><a class="link_geral" href="{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$pgto.idmovimento}" target="_blank">{$pgto.data_vencimento}</a></td>
                                    <td align="center"><a class="link_geral" href="{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$pgto.idmovimento}" target="_blank">{$pgto.apto}</a></td>
                                    <td align="right" ><a class="link_geral" href="{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$pgto.idmovimento}" target="_blank">{$pgto.valor_movimento}</a></td>                
                                </tr>
                            {/foreach}
                        </tbody>
                        <tfoot>
                            <tr  bgcolor="#CCCCCC">
                                <td align="right" colspan="3"><b>Total</b></td>
                                <td align="right"><b>{$list.em_aberto.somatorio}</b></td>
                            </tr>
                        </tfoot>
                    {/if} 
                </table>

                <br><br>

                <table align="center" width="600px">

                    <thead>                    
                        <tr align=center>
                            <th colspan="4">CADASTRAR MENSAGENS ADICIONAIS</th>	
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>Recuperar Mensagens Anteriores:</td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" align="center">
                                    <tr>
                                        {foreach from=$mensagens item=msg}
                                            <td><a class="link_geral" href="javascript:document.getElementById('dataMsg').value = '{$msg.data}';document.for.submit();">{$msg.data}</a></td>
                                        {/foreach}
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <textarea name="mensagem" id="mensagem" style="width:596px; height:100px" >{$mensagem}</textarea>
                                {* Chama o CKeditor para exibir as ferramentas de formatação no textarea *} 
                                <script type="text/javascript"> CKEDITOR.replace('mensagem');</script>
                            </td>
                        </tr>	
                    </tbody>

                    <tr>
                        <td align="center">
                            <input name="salvar_msg" id="salvar_msg" value="" type="hidden" />
                            <input name="btn_salvar" type="button" class="botao_padrao" value="Salvar Mensagem"
                                   onclick="document.getElementById('salvar_msg').value = '1'; document.getElementById('for_print').submit();" />
                        </td>
                    </tr>

                </table>	


                <table width="95%" align="center">		
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td align="center">
                            <input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão">
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>	
                </table>

            </form>


        {else}




            {if $smarty.post.for_chk}
                {include file="div_resultado_nenhum.tpl"}
            {/if}

        {/if}		


    {elseif $flags.action == "clientes_inadimplentes"}


        <form  action="{$smarty.server.PHP_SELF}?ac=clientes_inadimplentes" method="post" name = "for" id = "for">

        <input type="hidden" name="for_chk" id="for_chk" value="1" />

        <table align="center" width="90%">
            <tr>
                <td align="right">Cliente:</td>

                <td align="left">
                    
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
            <script type="text/javascript">
                new CAPXOUS.AutoComplete("idcliente_Nome", function() {ldelim}
                return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente" + "&mostraDetalhes=0&inserirEndereco=0";
                {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
            </script>

            <script type="text/javascript">
                // verifica os campos auto-complete
                VerificaMudancaCampo('idcliente');
            </script>


            <tr>
                <td align="right" valign="bottom" >Data de vencimento De:</td>
                <td align="left"> <input class="short" type="text" name="data_vencimento_de" id="data_vencimento_de" value="{$smarty.post.data_vencimento_de}" maxlength='10' onkeydown="mask('data_vencimento_de', 'data')" onkeyup="mask('data_vencimento_de', 'data')" />
                    <img src="{$conf.addr}/common/img/calendar.png" id="img_data_vencimento_de" style="cursor: pointer;" />
                    &nbsp;a&nbsp;   
                    <input class="short" type="text" name="data_vencimento_ate" id="data_vencimento_ate" value="{$smarty.post.data_vencimento_ate}" maxlength='10' onkeydown="mask('data_vencimento_ate', 'data')" onkeyup="mask('data_vencimento_ate', 'data')" />
                    <img src="{$conf.addr}/common/img/calendar.png" id="img_data_vencimento_ate" style="cursor: pointer;" /> (dd/mm/aaaa)
                </td>
            </tr>

            <script type="text/javascript">         
                Calendar.setup(
                {ldelim}
                    inputField : "data_vencimento_de", // ID of the input field
                    ifFormat : "%d/%m/%Y", // the date format
                    button : "img_data_vencimento_de", // ID of the button
                    align  : "cR"  // alinhamento
                {rdelim}
                );

                Calendar.setup(
                {ldelim}
                    inputField : "data_vencimento_ate", // ID of the input field
                    ifFormat : "%d/%m/%Y", // the date format
                    button : "img_data_vencimento_ate", // ID of the button
                    align  : "cR"  // alinhamento
                {rdelim}
                );                      
            </script>   



            <tr>
                <td colspan="2" align="center">
                    <input name="Submit" type="submit" class="botao_padrao" value="Buscar">
                </td>
            </tr>

        </table>

        </form>

        {if count($clientes)} 

            <script language="javascript">
                {literal}
    
                //Método para não dar conflito com o auto-complete do xajax
                var $j = jQuery.noConflict();
            
                $j(document).ready(function()
                    { 
                        $j("#tbl_clientes_inadimplentes").tablesorter(); 
                    } 
                );
                
                {/literal}
            </script>


        <form action="{$smarty.server.PHP_SELF}?ac=clientes_inadimplentes&target=full" method="post" name = "for" id = "for" target="_blank">

        <input type="hidden" name="for_chk" id="for_chk" value="1" />            

        <table width="95%" align="center">      

            <tr><td>&nbsp;</td></tr>

            <tr>
                <td align="center">
                    <input name="Submit" type="submit" class="botao_padrao" value="Tela de Impress&atilde;o">
                </td>
            </tr>

            <tr><td>&nbsp;</td></tr>    

        </table>

        </form>

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




    {elseif $flags.action == "saldo"}

        <form  action="{$smarty.server.PHP_SELF}?ac=saldo" method="post" name = "for" id = "for">

        <input type="hidden" name="for_chk" id="for_chk" value="1" />

        <table align="center" width="90%">
            <tr>
                <td align="right">Cliente:</td>

                <td align="left">
                    
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
            <script type="text/javascript">
                new CAPXOUS.AutoComplete("idcliente_Nome", function() {ldelim}
                return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente" + "&mostraDetalhes=0&inserirEndereco=0";
                {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
            </script>

            <script type="text/javascript">
                // verifica os campos auto-complete
                VerificaMudancaCampo('idcliente');
            </script>


            <tr>
                <td align="right">Tipo de cliente:</td>

                <td align="left">
                    <select name="tipo_cliente" id="tipo_cliente">
                        <option value="T" {if $smarty.post.tipo_cliente == "T"}selected{/if}>Todos</option>
                        <option value="C" {if $smarty.post.tipo_cliente == "C"}selected{/if}>Somente condom&iacute;nios</option>
                        <option value="CA" {if $smarty.post.tipo_cliente == "CA" || !$smarty.post.tipo_cliente}selected{/if}>Condom&iacute;nios adm financeira</option>

                    </select>
                </td>
            </tr>

            <tr>
                <td colspan="2" align="center">
                    <input name="Submit" type="submit" class="botao_padrao" value="Buscar">
                </td>
            </tr>

        </table>

        </form>

        {if count($saldo)} 

            <script language="javascript">
                {literal}
    
                //Método para não dar conflito com o auto-complete do xajax
                var $j = jQuery.noConflict();
            
                $j(document).ready(function()
                    { 
                        $j("#tbl_clientes_saldo").tablesorter(); 
                    } 
                );
                
                {/literal}
            </script>

        <form action="{$smarty.server.PHP_SELF}?ac=saldo&target=full" method="post" name = "for" id = "for" target="_blank">

        <input type="hidden" name="for_chk" id="for_chk" value="1" />            

        <table width="95%" align="center">      

            <tr><td>&nbsp;</td></tr>

            <tr>
                <td align="center">
                    <input name="Submit" type="submit" class="botao_padrao" value="Tela de Impress&atilde;o">
                </td>
            </tr>

            <tr><td>&nbsp;</td></tr>    

        </table>

        </form>

        
        <div ><strong><h2>{$descricao_relatorio}</h2></strong></div>
        <table align="center" width="100%" id="tbl_clientes_saldo" name="tbl_clientes_saldo" class="tablesorter">

            <thead> 

                <tr align=center>
                    <th class="header">C&oacute;digo<br />do cliente</th>
                    <th class="header">Nome</th>
                    <th class="header">Saldo (R$)</th>
                </tr>
            </thead>

            <tbody>
            {section name=i loop=$saldo}

                <!-- <tr bgcolor = "{if $saldo[i].index%2 == 0}#E7E7E7{else}#FFFFFF{/if}" > -->
                <tr>
                    <td align='right'>{if $saldo[i].idcliente}{$saldo[i].idcliente}{else}&nbsp;{/if}</td>
                    <td align='left'>{if $saldo[i].nome_cliente}{$saldo[i].nome_cliente}{else}&nbsp;{/if}</td>
                    <td align='right'>{$saldo[i].saldo}</td>
                </tr>
            {/section}

            </tbody>
        </table>

        <table align="center" width="99%" >
            <tbody>
                <tr><td>&nbsp;</td></tr>
                <tr>
                    <td align="right" style="font-size:1.1em;">Total: <b>R$ {$saldoTotal}</b></td>
                </tr>
            <tbody>
        </table>

        {else}

            {if $smarty.post.for_chk}
                {include file="div_resultado_nenhum.tpl"}
            {/if}

        {/if}    



    {elseif $flags.action == "razonete"}

        <form  action="{$smarty.server.PHP_SELF}?ac=razonete" method="post" name="for" id="for">
            <input type="hidden" name="for_chk" id="for_chk" value="1" />
            {include file="adm_relatorio_caixa/campos_busca.tpl"}
        </form>		


        {if count($list)}

            <form action="{$smarty.server.PHP_SELF}?ac=razonete&target=full" method="post" name = "for_print" id = "for_print" target="_blank">
                <input type="hidden" name="for_chk" id="for_chk" value="1" />

                <input type="hidden" name="idcliente_relatorio" id="idcliente_relatorio" value="{$smarty.post.idcliente}" />
                <input type="hidden" name="data_baixa_de_relatorio" id="data_baixa_de_relatorio" value="{$smarty.post.data_baixa_de}" />
                <input type="hidden" name="data_baixa_ate_relatorio" id="data_baixa_ate_relatorio" value="{$smarty.post.data_baixa_ate}" />
                <input type="hidden" name="cliente_Nome" id="cliente_Nome" value="{$smarty.post.idcliente_Nome}" />

                <input type="hidden" name="idplano_ini" id="idplano_ini" value="{$smarty.post.idplano_ini}" />
                <input type="hidden" name="idplano_ini_Nome" id="idplano_ini_Nome" value="{$smarty.post.idplano_ini_Nome}" />

                <input type="hidden" name="idplano_fim" id="idplano_fim" value="{$smarty.post.idplano_fim}" />
                <input type="hidden" name="idplano_fim_Nome" id="idplano_fim_Nome" value="{$smarty.post.idplano_fim_Nome}" />

                <table width="95%" align="center">		

                    <tr><td>&nbsp;</td></tr>

                    <tr>
                        <td align="center">
                            <input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão">
                        </td>
                    </tr>

                    <tr><td>&nbsp;</td></tr>	

                </table>

                <table align="center" width="95%">

                    <tr align=center>
                        <th>Data de Baixa</th>
                        <th>Tipo de Baixa</th>
                        <th>Plano</th>
                        <th>Origem / Destino</th>
                        <th>Descrição</th>
                        <th align="center">Crédito</th>						
                        <th align="center">Débito</th>
                    </tr>

                    {foreach from=$list key=k item=razonete}

                        {if $razonete.info_pai}

                            {foreach from=$razonete.movimentos key=j item=plano}

                                <tr bgcolor="#CCCCCC">
                                    <td></td>
                                    <td colspan="6">
                                        <b>{$j} - {if $plano.0.tipo == 'debito'}
                                            {$plano.0.nome_debt}
                                            {elseif $plano.0.tipo == 'credito'}
                                                {$plano.0.nome_cred}
                                                {/if}
                                                </b>
                                            </td>
                                        </tr>

                                        {foreach from=$plano key=i item=movimento}
                                            <tr  bgcolor = "{if $i%2 != 0}#E7E7E7{else}#FFFFFF{/if}" >
                                                <td>{if $movimento.idmovimento}<a href="{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$movimento.idmovimento}" target="_blank" class="link_geral">{/if}{$movimento.data_baixa}{if $movimento.idmovimento}</a>{/if}</td>

                                                <td>
                                                    {if $movimento.idmovimento}<a href="{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$movimento.idmovimento}" target="_blank" class="link_geral">{/if}{$movimento.tipo_baixa}{if $movimento.idmovimento}</a>{/if}
                                                </td>

                                                <td>
                                                {if $movimento.idmovimento}<a href="{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$movimento.idmovimento}" target="_blank" class="link_geral">{/if}
                                                    {if $movimento.tipo == 'debito'}
                                                        {$movimento.numero_debt} - {$movimento.nome_debt}
                                                    {else}
                                                        {$movimento.numero_cred} - {$movimento.nome_cred}
                                                    {/if}
                                                {if $movimento.idmovimento}</a>{/if}
                                        </td>
                                        <td>
                                        {if $movimento.idmovimento}<a href="{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$movimento.idmovimento}" target="_blank" class="link_geral">{/if}

                                            {if $movimento.tipo == 'credito'}
                                                {$movimento.origem}
                                            {elseif $movimento.tipo == 'debito'}
                                                {$movimento.destino}
                                            {/if}

                                        {if $movimento.idmovimento}</a>{/if}
                                </td>


                                <td>{if $movimento.idmovimento}<a href="{$conf.addr}/admin/movimento.php?ac=editar&idmovimento={$movimento.idmovimento}" target="_blank" class="link_geral">{/if}{$movimento.descricao_movimento}{if $movimento.idmovimento}</a>{/if}</td>
                                <td align="right" >{if $movimento.tipo == 'credito'}{$movimento.valor_movimento} {/if}</td>    								
                                <td align="right" >{if $movimento.tipo == 'debito'} {$movimento.valor_movimento}{/if}</td>
                            </tr>
                        {/foreach}

                        <tr>
                            <td colspan="2"></td>
                            <td>{assign var="numPlano" value=$j}</td>
                            <td align="right"><b>Total</b></td>
                            <td align="right"><b>{if $razonete.info_pai.tipo == 'R'}{$list.somatorio.planos.$numPlano}{/if}</b></td>
                            <td align="right"><b>{if $razonete.info_pai.tipo == 'D'}{$list.somatorio.planos.$numPlano}{/if}</b></td>
                        </tr>
                        <tr><td class="row" height="1" bgcolor="#999999" colspan="20"></td></tr>		

                        {/foreach}


                            {/if}

                                {/foreach}

                                </table>


                                <table width="95%" align="center">		
                                    <tr><td>&nbsp;</td></tr>

                                    <tr>
                                        <td align="center">
                                            <input name="Submit2" type="submit" class="botao_padrao" value="Tela de Impressão">
                                        </td>
                                    </tr>

                                    <tr><td>&nbsp;</td></tr>	

                                </table>
                            </form>


                            {else}

                                {if $smarty.post.for_chk}
                                    {include file="div_resultado_nenhum.tpl"}
                                {/if}

                                {/if}	

    {elseif $flags.action == "balancete"}

        <form  action="{$smarty.server.PHP_SELF}?ac=balancete" method="post" name="for" id="for">
            <input type="hidden" name="for_chk" id="for_chk" value="1" />
            {include file="adm_relatorio_caixa/campos_busca.tpl"}

        </form>		
        <form action="{$smarty.server.PHP_SELF}?ac=balancete&target=full" method="post" name = "for" id = "for" target="_blank">
            <input type="hidden" name="for_chk" id="for_chk" value="1" />

            <input type="hidden" name="idcliente_relatorio" id="idcliente_relatorio" value="{$smarty.post.idcliente}" />
            <input type="hidden" name="data_baixa_de_relatorio" id="data_baixa_de_relatorio" value="{$smarty.post.data_baixa_de}" />
            <input type="hidden" name="data_baixa_ate_relatorio" id="data_baixa_ate_relatorio" value="{$smarty.post.data_baixa_ate}" />
            <input type="hidden" name="cliente_Nome" id="cliente_Nome" value="{$smarty.post.idcliente_Nome}" />

            <input type="hidden" name="idplano_ini" id="idplano_ini" value="{$smarty.post.idplano_ini}" />
            <input type="hidden" name="idplano_fim" id="idplano_fim" value="{$smarty.post.idplano_fim}" />
            <input type="hidden" name="idplano_ini_Nome" id="idplano_ini_Nome" value="{$smarty.post.idplano_ini_Nome}" />
            <input type="hidden" name="idplano_fim_Nome" id="idplano_fim_Nome" value="{$smarty.post.idplano_fim_Nome}" />




            {if $list}

                <table width="95%" align="center">					
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td align="center">
                            <input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão">
                        </td>
                    </tr>

                    <tr><td>&nbsp;</td></tr>
                </table>        



                <table id="bancete" align="center" width="95%">

                    <tr>
                        <th>Plano</th>
                        <th>Nome</th>
                        <th>Nível 1</th>
                        <th>Nível 2</th>
                        <th>Nível 3</th>
                    </tr>

                {if !$smarty.post.idplano_ini}  
                    <tr bgcolor="#E2E2E2">
                        <td align="center"> -- </td>
                        <td><strong>Saldo Anterior</strong></td>
                        <td></td>
                        <td></td>
                        <td  align="right"><strong>{$list.somatorio.saldo_inicial}</strong></td>
                    </tr>
                {/if}

                {foreach from=$list key=k_avo item=avo}

                {if $avo.filhos} 
                    <tr bgcolor="#CCCCCC" >
                        <td>{$avo.dados.plano_numero}</td>
                        <td>{$avo.dados.plano_nome}</td>
                        <td align="right">{$avo.dados.somatorio_saida}</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>



                    {foreach from=$avo.filhos key=k_pai item=pai}

                        {if $pai.dados}
                            <tr>
                                <td>{$pai.dados.plano_numero}</td>
                                <td>{$pai.dados.plano_nome}</td>
                                <td>&nbsp;</td>
                                <td align="right">{$pai.dados.somatorio_saida}</td>
                                <td>&nbsp;</td>
                            </tr>
                        {/if}

                        {foreach from=$pai.filhos key=k_filho item=filho}

                            <tr bgcolor="#FFFFFF" >
                                <td>{$filho.plano_numero}</td>
                                <td>{$filho.plano_nome}</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td align="right">{$filho.valor_saida}</td>
                            </tr>

                        {/foreach}


                    {/foreach}

                {/if}

                {/foreach}
                    </table>

                    <table width="95%" align="center">					
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td align="center">
                                <input name="Submit2" type="submit" class="botao_padrao" value="Tela de Impressão">
                            </td>
                        </tr>

                        <tr><td>&nbsp;</td></tr>
                    </table>        


            {/if}    

                </form>


        {/if}


        <br>
        {if $list.somatorio && $smarty.get.ac != 'demonstrativo'}

            <table  width="250px" class="tb4cantos" align="right" style="margin-right:3%;">

                <tr>
                    <th colspan="2">Total</th>
                </tr>
                {if !$smarty.post.idplano_ini}
                    <tr bgcolor="#FFFFFF">
                        <td  align="right"><b>Saldo Anterior</b></td>
                        <td align="right">R$ {$list.somatorio.saldo_inicial}</td>
                    </tr>
                {/if}
                <tr bgcolor="#FFFFFF">
                    <td  align="right"><b>Receitas</b></td>
                    <td align="right">R$ {$list.somatorio.receitas}</td>
                </tr>


                <tr bgcolor="#FFFFFF">
                    <td align="right"><b>Despesas</b></td>
                    <td align="right">R$ {$list.somatorio.despesas}</td>
                </tr>

                <tr bgcolor="#FFFFFF"> 
                    <td align="right"><b>Saldo Final</b></td>
                    <td align="right" {if $saldo_final < 0}class="req"{/if}>R$ {$list.somatorio.saldo_final}</td>
                </tr>

            </table>

        {/if}
        <br>

        <table width="100%" border="0" cellpadding="5" cellspacing="0">
            <tr>
                <td>
                    * Baseado em Movimentações consolidadas (baixadas).
                </td>
            </tr>
        </table>



    {/if}

{include file="com_rodape.tpl"}
