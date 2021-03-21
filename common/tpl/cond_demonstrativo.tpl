{include file="com_cabecalho_cond.tpl"} 

{include file="div_erro.tpl"}

{include file="div_login_cond.tpl"}

{if $flags.okay}

    <form  action="{$smarty.server.PHP_SELF}?ac=demonstrativo" method="post" name="for" id="for">

        <input type="hidden" name="for_chk" id="for_chk" value="1" />
        <input type="hidden" name="dataMsg" id="dataMsg" value="" />

        <table>
            <tr>
                <td class="req" align="right" valign="bottom" >Exibir demonstrativo de:</td>
                <td class="req" align="left"> 
                    <select name="mesBaixa" id="mesBaixa">
                        {foreach from=$listMesAno.mes key=k item=mes}
                            <option {if $smarty.post.mesBaixa == $k}selected{/if} value="{$k}">{$mes}</option>
                        {/foreach}
                    </select>
                    <select name="anoBaixa" id="anoBaixa">
                        {foreach from=$listMesAno.ano item=ano}
                            <option {if $smarty.post.anoBaixa == $ano}selected{/if} value="{$ano}">{$ano}</option>
                        {/foreach}
                    </select>						
                </td>

                <td align="center" colspan="2">
                    <input type="submit" class="botao_padrao" value="Buscar" name="button"  />
                </td>
            </tr>


        </table>

    </form>

    {if $mensagem  || count($list.debito.contas)}

        <form action="{$smarty.server.PHP_SELF}?ac=demonstrativo&target=full" method="post" name = "for_print" id = "for_print" target="_blank" >

            <input type="hidden" name="for_chk" id="for_chk" value="1" />
            <input type="hidden" name="mesBaixa"  id="mesBaixa" value="{$smarty.post.mesBaixa}"       />
            <input type="hidden" name="anoBaixa"  id="anoBaixa" value="{$smarty.post.anoBaixa}"       />

            <input type="submit" name="imprimir" value="Tela de Impressão" class="botao_padrao"  style="margin:20px 0px 20px" />

            <table align="center" width="600px" style="border:1px solid">  
                <thead>
                    <tr align="center">
                        <th colspan="2">DESPESAS</th>	
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$list.debito.contas key=k item=despesa}
                        <tr  bgcolor = "{if $k%2 != 0}#E7E7E7{else}#FFFFFF{/if}" >
                            <td>{$despesa.descricao_movimento}</td>
                            <td  align="right">{$despesa.valor_movimento}</td>
                        </tr>
                    {/foreach}
                </tbody>
                <tfoot>
                    <tr bgcolor="C7C7C7">
                        <td><b>TOTAL DAS DESPESAS</b></td>
                        <td align="right"><b>{$list.somatorio.debito}</b></td>
                    </tr>
                </tfoot>
            </table>

            <br> <br>

            <table align="center" width="600px" style="border:1px solid">
                <thead>
                    <tr align=center>
                        <th colspan="2">SALDO</th>	
                    </tr>
                </thead>
                <tbody>
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

                </tbody>

            </table>

            <br> <br>

            {if $mensagem}
                <table align="center" width="600px" style="border:1px solid">

                    <tbody>
                        <tr align="center">
                            <th>OBSERVAÇÕES</th>	
                        </tr>

                        <tr bgcolor="FFFFFF"> 
                            <td style="padding:12px;">{$mensagem}</td>
                        </tr>

                    </tbody>
                </table>
            {/if}


            <input type="submit" value="Tela de Impressão" class="botao_padrao" name="imprimir" style="margin:20px 0px 20px" >

        </form>

    {else}
        <table>
            <tr>
                <td>
                    <table border="0" cellpadding="2" cellspacing="3" bgcolor="#FDF5E6" class="tb4cantos" style="margin-top:100px">
                        <tr ><td><b>Não foi possível encontrar registros com a data selecionada</b></td></tr>
                    </table>
                </td>
            </tr>
        </table>
    {/if}

    <br />
</center>

{/if} 

{include file="com_rodape.tpl"}

