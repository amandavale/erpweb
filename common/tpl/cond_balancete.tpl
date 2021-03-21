{include file="com_cabecalho_cond.tpl"} 

{include file="div_erro.tpl"}

{include file="div_login_cond.tpl"}

{if $flags.okay}


    {literal}

        <table width="70%">
            <tr>
                <td>


                    <div id="wrapper">	

                        <div id="programa">

                        </div>




   
                            <input type="hidden" value="1" id="for_chk" name="for_chk">
                            <table width="100%">

                                <tbody><tr>
                                        <td align="center" colspan="2">
                                            <table width="100%" class="tb4cantos">
                                                <tbody><tr bgcolor="#F7F7F7">
                                                        <td align="center" colspan="9">Selecione o cliente e o período</td>
                                                    </tr>

                                                    <tr><td><br></td></tr>

                                                    <tr>
                                                        <td align="right">Intervalo de planos</td>		        		

                                                        <td align="left"> 

                                                            <select name="mesBaixa" id="mesBaixa">
                                                                <option value="01">Janeiro</option>
                                                                <option value="02">Fevereiro</option>
                                                                <option selected="selected" value="03">Março</option>
                                                                <option value="04">Abril</option>
                                                                <option value="05">Maio</option>
                                                                <option value="06">Junho</option>
                                                                <option value="07">Julho</option>
                                                                <option value="08">Agosto</option>
                                                                <option value="09">Setembro</option>
                                                                <option value="10">Outubro</option>
                                                                <option value="11">Novembro</option>
                                                                <option value="12">Dezembro</option>
                                                            </select>

                                                            <select name="anoBaixa" id="anoBaixa">
                                                                <option selected="selected" value="2012">2012</option>
                                                                <option value="2011">2011</option>
                                                                <option value="2010">2010</option>
                                                                <option value="2009">2009</option>
                                                            </select>

                                                            <input type="submit" value="Buscar" class="botao_padrao" name="Submit">

                                                        </td>
                                                    </tr>


                                                </tbody></table>
                                        </td>
                                    </tr>

                                </tbody></table>
     
                            <input type="hidden" value="1" id="for_chk" name="for_chk">

                            <input type="hidden" value="69" id="idcliente_relatorio" name="idcliente_relatorio">
                            <input type="hidden" value="01/07/2012" id="data_baixa_de_relatorio" name="data_baixa_de_relatorio">
                            <input type="hidden" value="31/08/2012" id="data_baixa_ate_relatorio" name="data_baixa_ate_relatorio">
                            <input type="hidden" value="Condomínio Edifício Residencial Sabrina" id="cliente_Nome" name="cliente_Nome">

                            <input type="hidden" value="" id="idplano_ini" name="idplano_ini">
                            <input type="hidden" value="" id="idplano_fim" name="idplano_fim">
                            <input type="hidden" value="" id="idplano_ini_Nome" name="idplano_ini_Nome">
                            <input type="hidden" value="" id="idplano_fim_Nome" name="idplano_fim_Nome">





                            <table width="95%" align="center">					
                                <tbody><tr><td>&nbsp;</td></tr>

                                    <tr><td>&nbsp;</td></tr>
                                </tbody></table>        



                            <table width="95%" align="center" id="bancete">

                                <tbody><tr>
                                        <th>Plano</th>
                                        <th>Nome</th>
                                        <th>Nível 1</th>
                                        <th>Nível 2</th>
                                        <th>Nível 3</th>
                                    </tr>


                                    <tr bgcolor="#E2E2E2">
                                        <td align="center"> -- </td>
                                        <td><strong>Saldo Anterior</strong></td>
                                        <td></td>
                                        <td></td>
                                        <td align="right"><strong>4983,67</strong></td>
                                    </tr>





                                    <tr bgcolor="#CCCCCC">
                                        <td>1</td>
                                        <td>Receitas</td>
                                        <td align="right">1166,09</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>




                                    <tr>
                                        <td>1.10</td>
                                        <td>Receita de Serviços</td>
                                        <td>&nbsp;</td>
                                        <td align="right">1166,09</td>
                                        <td>&nbsp;</td>
                                    </tr>


                                    <tr bgcolor="#FFFFFF">
                                        <td>1.10.004</td>
                                        <td>Receitas Diversas</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td align="right">1166,09</td>
                                    </tr>







                                    <tr bgcolor="#CCCCCC">
                                        <td>2</td>
                                        <td>Despesas</td>
                                        <td align="right">712,36</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>




                                    <tr>
                                        <td>2.30</td>
                                        <td>Despesas Operacionais</td>
                                        <td>&nbsp;</td>
                                        <td align="right">246,65</td>
                                        <td>&nbsp;</td>
                                    </tr>


                                    <tr bgcolor="#FFFFFF">
                                        <td>2.30.003</td>
                                        <td>CEMIG</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td align="right">14,26</td>
                                    </tr>


                                    <tr bgcolor="#FFFFFF">
                                        <td>2.30.004</td>
                                        <td>DMAES</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td align="right">232,39</td>
                                    </tr>




                                    <tr>
                                        <td>2.70</td>
                                        <td>Despesas Diversas</td>
                                        <td>&nbsp;</td>
                                        <td align="right">465,71</td>
                                        <td>&nbsp;</td>
                                    </tr>


                                    <tr bgcolor="#FFFFFF">
                                        <td>2.70.001</td>
                                        <td>Despesas Diversas</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td align="right">465,71</td>
                                    </tr>


                                </tbody></table>

                            <table width="95%" align="center">					
                                <tbody><tr><td>&nbsp;</td></tr>
                                    <tr>
                                        <td align="center">
                                            <input type="submit" value="Tela de Impressão" class="botao_padrao" name="Submit2">
                                        </td>
                                    </tr>

                                    <tr><td>&nbsp;</td></tr>
                                </tbody></table>        




                        <br>

                        <table width="250px" align="right" style="margin-right:3%;" class="tb4cantos">

                            <tbody><tr>
                                    <th colspan="2">Total</th>
                                </tr>
                                <tr bgcolor="#FFFFFF">
                                    <td align="right"><b>Saldo Anterior</b></td>
                                    <td align="right">R$ 4983,67</td>
                                </tr>
                                <tr bgcolor="#FFFFFF">
                                    <td align="right"><b>Receitas</b></td>
                                    <td align="right">R$ 1166,09</td>
                                </tr>


                                <tr bgcolor="#FFFFFF">
                                    <td align="right"><b>Despesas</b></td>
                                    <td align="right">R$ 712,36</td>
                                </tr>

                                <tr bgcolor="#FFFFFF"> 
                                    <td align="right"><b>Saldo Final</b></td>
                                    <td align="right">R$ 5437,40</td>
                                </tr>

                            </tbody></table>

                        <br>

                        <table width="100%" cellspacing="0" cellpadding="5" border="0">
                            <tbody><tr>
                                    <td>
                                        * Baseado em Movimentações consolidadas (baixadas).
                                    </td>
                                </tr>
                            </tbody></table>




                    </div>


                </td>
            </tr>
        </table>
    {/literal}
    
{/if} {include file="com_rodape.tpl"}

