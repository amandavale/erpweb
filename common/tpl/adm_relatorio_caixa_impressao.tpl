{include file="com_cabecalho_relatorio.tpl"}


{if $flags.sucesso != ""}
    {include file="div_resultado_inicio.tpl"}
    {$flags.sucesso}
    {include file="div_resultado_fim.tpl"}
{/if}

{if $flags.action == "demonstrativo"}

    <style type="text/css">
        {literal} 	
            th, td { font-size:20px; }
            th { background-color: #ccc; color:#000; background-image:none;}
        {/literal}
    </style>


    <br><br>
    <table class="" width="900px" align="center">
        <tr bgcolor="#E7E7E7">
            <td colspan="9" align="center"><strong><h2>{$dados_cliente.nome_cliente}</h2></strong></td>
        </tr>

        <tr>
            <td align="right">Demonstrativo Financeiro de </td>
            <td align="left">{$smarty.post.data_baixa_de_relatorio} à {$smarty.post.data_baixa_ate_relatorio}</td>
        </tr>

    </table>
    <br><br>
    {if $list}
        <table align="center" width="900px"  class="tb4cantos">

            <tr align=center>
                <th colspan="2" >DESPESAS</th>	
            </tr>

            {foreach from=$list.debito.contas key=k item=despesa}
                <tr  bgcolor = "{if $k%2 != 0}#E7E7E7{else}#FFFFFF{/if}" >
                    <td>{$despesa.descricao_movimento}</td>
                    <td align="right">{$despesa.valor_movimento}</td>
                </tr>
            {/foreach}
            <tr bgcolor="C7C7C7">
                <td ><b>TOTAL DAS DESPESAS</b></td>
                <td align="right"><b>{$list.somatorio.debito}</b></td>
            </tr>					
        </table>

        <br><br>

        <table align="center" width="900px"  class="tb4cantos">

            <tr align=center>
                <th colspan="2">SALDOS</th>	
            </tr>	

            <tr bgcolor="FFFFFF"> 
                <td>SALDO DO PERÍODO ANTERIOR</td>
                <td  align="right">{$list.somatorio.saldo_anterior}</td>
            </tr>

            <tr bgcolor="#E7E7E7"> 
                <td>RECEITAS DO PERÍODO</td>
                <td  align="right">{$list.somatorio.credito}</td>
            </tr>

            <tr bgcolor="F7F7F7"> 
                <td>DESPESAS DO PERÍODO</td>
                <td  align="right">{$list.somatorio.debito}</td>
            </tr>

            <tr bgcolor="CFCFCF"> 
                <td><b>SALDO DO PERÍODO</b></td>
                <td  align="right"><b>{$list.somatorio.saldo_final}</b></td>
            </tr>

        </table>

        <br> <br>
        {if $list.em_aberto.qtd > 0}
            <table align="center" width="900px" class="tb4cantos">
                <tr align=center>
                    <th colspan="2">CONDÔMINOS EM ATRASO</th>	
                </tr>

                <tr>
                    <td width="400px">QUANTIDADE DE BOLETOS EM ABERTO:</td>
                    <td><b>{$list.em_aberto.qtd}</b></td>
                </tr>
                <tr>  
                    <td width="400px">TOTAL DO VALOR EM ABERTO:</td> 
                    <td> <b>R$ {$list.em_aberto.somatorio}</b></td>
                </tr> 
            </table>
            <br> <br>
        {/if} 

        {if $mensagem != ""}
            <table align="center" width="900px"  class="tb4cantos">
                <tr align=center>
                    <th colspan="2">OBSERVAÇÕES</th>	
                </tr>
                <tr>
                    <td>{$mensagem}</td>
                </tr>
            </table>  
        {/if}
    {/if}	

{elseif $flags.action == "clientes_inadimplentes"}

    <table align="center" width="100%">
        <tr>
            <td align="center"><strong><h2>Clientes Inadimplentes<br />{$descricao_relatorio}</h2></strong></td>
        </tr>

    </table>

    <br><br>
    {if count($clientes)} 
        <table align="center" width="100%">

            <tr align="center">
                <th>C&oacute;digo<br />do cliente</th>
                <th>Nome</th>
                <th>C&oacute;digo<br />do movimento</th>
                <th>Movimento</th>
                <th>Vencimento</th>
                <th>Valor (R$)</th>
            </tr>

            <tbody>
            {section name=i loop=$clientes}

                <tr  bgcolor = "{if $clientes[i].index % 2 == 0}#E7E7E7{else}WHITE{/if}" >
                    <td align='right'>{if $clientes[i].idcliente}{$clientes[i].idcliente}{else}&nbsp;{/if}</td>
                    <td align='left'>{if $clientes[i].nome_cliente}{$clientes[i].nome_cliente}{else}&nbsp;{/if}</td>
                    <td align='right'>{$clientes[i].idmovimento}</td>
                    <td align='left'>{$clientes[i].descricao_movimento}</td>
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
        
    {/if}


{elseif $flags.action == "saldo"}

    <table align="center" width="100%">
        <tr>
            <td align="center"><strong><h2>Relat&oacute;rio de saldos</h2>{$descricao_relatorio}</strong></td>
        </tr>

    </table>

    <br><br>
    {if count($saldo)} 
        <table align="center" width="100%">

            <tr align="center">
                <th>C&oacute;digo do cliente</th>
                <th>Nome</th>
                <th>Saldo (R$)</th>
            </tr>

            <tbody>
            {section name=i loop=$saldo}
                <tr bgcolor = "{if $saldo[i].index%2 == 0}#E7E7E7{else}#FFFFFF{/if}" >
                    <td align='right'>{if $saldo[i].idcliente}{$saldo[i].idcliente}{else}&nbsp;{/if}</td>
                    <td align='left'>{if $saldo[i].nome_cliente}{$saldo[i].nome_cliente}{else}&nbsp;{/if}</td>
                    <td align='right'>{$saldo[i].saldo}</td>
                </tr>
            {/section}

            </tbody>
        </table>
        
    {/if}

{elseif $flags.action == "razonete"}

    <style type="text/css">
        {* literal} 	
        th, td { font-size:20px; }
        th { background-color: #ccc; color:#000; }
        {/literal *}
    </style>


    <br><br>
    <table class="" width="100%" align="center">
        <tr bgcolor="">
            <td colspan="9" align="center"><strong><h2>{$dados_cliente.nome_cliente}</h2></strong></td>
        </tr>

        <tr>	
            <td colspan="2" align="center"><b>Relatório de Razonete</b></td>
        </tr>
        <tr><td><br /></td></tr>
        <tr><td><br /></td></tr>
        <tr>
            <td align="right"><b>Período:</b></td>
            <td align="left">de {$smarty.post.data_baixa_de_relatorio} à {$smarty.post.data_baixa_ate_relatorio}</td>
        </tr>

    </table>
    <br />


    <table align="center" width="100%">

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

                    <tr bgcolor="#C7C7C7">
                        <td></td>
                        <td colspan="9">
                            <b>{$j} - {if $plano.0.tipo == 'debito'}
                                {$plano.0.nome_debt}
                                {elseif $plano.0.tipo == 'credito'}
                                    {$plano.0.nome_cred}
                                    {/if}
                                    </b>
                                </td>
                            </tr>
                            {foreach from=$plano key=i item=movimento}
                                <tr  bgcolor = "{if $i%2 == 0}#E7E7E7{else}#FFFFFF{/if}" >
                                    <td>{$movimento.data_baixa}</td>

                                    <td>{$movimento.tipo_baixa}</td>

                                    <td>{if $movimento.tipo == 'debito'} {$movimento.numero_debt} - {$movimento.nome_debt} {else} {$movimento.numero_cred} - {$movimento.nome_cred} {/if}</td>
                                    <td>
                                        {if $movimento.tipo == 'credito'}
                                            {$movimento.origem}
                                        {elseif $movimento.tipo == 'debito'}
                                            {$movimento.destino}
                                        {/if}
                                    </td>
                                    <td width="30%">{$movimento.descricao_movimento}</td>
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

                                    {if $list.somatorio}
                                        <br /><br /><br /> 
                                        <center>
                                            <table  width="30%" class="tb4cantos"  style="margin-right:3%;">

                                                <tr>
                                                    <th colspan="2">Somatório Final</th>
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
                                        </center>
                                        <br /><br /><br />
                                    {/if}		

                                    {elseif $flags.action == "balancete"}

                                        {if $list}


                                            <table class="tb4cantos" width="400px" align="center">
                                                <tr bgcolor="#F7F7F7">
                                                    <td colspan="9" align="center"><strong>Balancete</strong></td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Filial:
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
                                                        {$info_filial.nome_filial}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td align="">Data / Hora:</td>
                                                    <td>{$flags.data_hora_atual}</td>
                                                </tr>

                                                <tr>
                                                    <td align=""><strong>Cliente:</strong></td>
                                                    <td>{$dados_cliente.nome_cliente}</td>
                                                </tr>

                                                <tr>
                                                    <td align=""><strong>Período:</strong></td>
                                                    <td>{$smarty.post.data_baixa_de_relatorio} até {$smarty.post.data_baixa_ate_relatorio}</td>
                                                </tr>

                                            </table>

                                            <br><br>


                                            <table id="balancete" align="center" width="95%">

                                                <tr>
                                                    <th>Plano</th>
                                                    <th>Nome</th>
                                                    <th>Nível 1</th>
                                                    <th>Nível 2</th>
                                                    <th>Nível 3</th>
                                                </tr>

                                                {if !$smarty.post.idplano_ini}		
                                                    <tr bgcolor="#CCCCCC">
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


                                                                <tr bgcolor="#E2E2E2" >
                                                                    <td>{$pai.dados.plano_numero}</td>
                                                                    <td>{$pai.dados.plano_nome}</td>
                                                                    <td>&nbsp;</td>
                                                                    <td align="right">{$pai.dados.somatorio_saida}</td>
                                                                    <td>&nbsp;</td>
                                                                </tr>

                                                            {/if}

                                                            {foreach from=$pai.filhos key=k_filho item=filho}

                                                                <tr bbgcolor = "{if $i%2 != 0}#000000{else}#FFFFFF{/if}" >
                                                                    <td>{$filho.plano_numero}</td>
                                                                    <td>{$filho.plano_nome}</td>
                                                                    <td>&nbsp;</td>
                                                                    <td>&nbsp;</td>
                                                                    <td align="right">{$filho.valor_saida}</td>
                                                                </tr>

                                                                <tr><td class="row" height="1" bgcolor="#999999" colspan="20"></td></tr>

                                                            {/foreach}


                                                        {/foreach}

                                                    {/if}

                                                {/foreach}
                                            </table>

                                            <br><br>

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
                                            <br><br>

                                        {/if} 

                                        {else}

                                            {if count($list_caixa)}

                                                <table class="tb4cantos" width="400px" align="center">
                                                    <tr bgcolor="#F7F7F7">
                                                        <td colspan="9" align="center"><strong>Dados do Relatório de Caixa</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Filial:
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="idfilial" id="idfilial" value="{$info_filial.idfilial}" />
                                                            {$info_filial.nome_filial}
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td align="">Data / Hora:</td>
                                                        <td>{$flags.data_hora_atual}</td>
                                                    </tr>

                                                    <tr>
                                                        <td align=""><strong>Cliente:</strong></td>
                                                        <td>{$dados_cliente.nome_cliente}</td>
                                                    </tr>

                                                    <tr>
                                                        <td align=""><strong>Período:</strong></td>
                                                        <td>{$smarty.post.data_baixa_de_relatorio} até {$smarty.post.data_baixa_ate_relatorio}</td>
                                                    </tr>

                                                </table>

                                                <br><br>

                                                <table align="center" width="95%">

                                                    <tr align=center>
                                                        <th>Baixa</th>
                                                        <th>Vencimento</th>
                                                        <th width="50px">Cód.</th>
                                                        <th width="50px">Apto.</th>
                                                        <th width="250px">Origem</th>
                                                        <th width="250px">Destino</th>
                                                        <th width="250px">Descrição</th>
                                                        <th align="center">Crédito</th>
                                                        <th align="center">Débito</th>
                                                        <th align="center">Saldo</th>
                                                    </tr>

                                                    {foreach from=$list_caixa.registros key=k item=caixa}

                                                        <tr  bgcolor = "{if $k%2 != 0}#F7F7F7{else}#FFFFFF{/if}" >
                                                            <td>{if $caixa.idmovimento}{$caixa.data_baixa}{/if}</td>
                                                            <td>{if $caixa.idmovimento}{$caixa.data_vencimento}{/if}</td>
                                                            <td align="center">{if $caixa.idmovimento}{$caixa.idmovimento}{/if}</td>
                                                            <td>{if $caixa.idmovimento}{$caixa.apto}{/if}</td>
                                                            <td>{if $caixa.idmovimento}{$caixa.origem}{/if}</td>
                                                            <td>{if $caixa.idmovimento}{$caixa.destino}{/if}</td>
                                                            <td>{if $caixa.idmovimento}{$caixa.descricao_movimento}{/if}</td>
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


                                            {else}
                                                {include file="div_resultado_nenhum.tpl"}
                                            {/if}


                                            {/if}

                                                {*include file="com_rodape_relatorio.tpl"*}
