<table width="100%">

    <tr>
        <td colspan="2" align="center">
            <table class="tb4cantos" width="100%">
                <tr bgcolor="#F7F7F7">
                    <td colspan="9" align="center">Selecione o cliente e o período</b></td>
                </tr>

                <tr>
                    <td class="req" align="right" width="25%" >Cliente:</td>
                    <td colspan="9" align="left" >
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

                        <script type="text/javascript">
                            new CAPXOUS.AutoComplete("idcliente_Nome", function() {ldelim}
                            return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente" + "&mostraDetalhes=1";
                            {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
						

                                // verifica os campos auto-complete
                                VerificaMudancaCampo('idcliente');
                        </script>
                        &nbsp;&nbsp;&nbsp;
                        {*if $flags.action == "listar"*}
                        <input type="button" name="atualizaSaldo" id="atualizaSaldo" value="Atualizar Lançamentos"  class="botao_padrao" onClick="xajax_Atualiza_Saldo_Cliente_AJAX(xajax.getFormValues('for'))" />
                        {*/if*}
                    </td>
                </tr>
                {if $smarty.get.ac == 'demonstrativo' && $parametro.rel_SeletorData == 'select'}

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
                    </tr>

                {else}			

                    <tr>
                        <td class="req" align="right" valign="bottom" >Data de baixa De:</td>
                        <td class="req" align="left"> <input class="short" type="text" name="data_baixa_de" id="data_baixa_de" value="{$smarty.post.data_baixa_de}" maxlength='10' onkeydown="mask('data_baixa_de', 'data')" onkeyup="mask('data_baixa_de', 'data')" />
                            <img src="{$conf.addr}/common/img/calendar.png" id="img_data_baixa_de" style="cursor: pointer;" />
                            &nbsp;à&nbsp;	
                            <input class="short" type="text" name="data_baixa_ate" id="data_baixa_ate" value="{$smarty.post.data_baixa_ate}" maxlength='10' onkeydown="mask('data_baixa_ate', 'data')" onkeyup="mask('data_baixa_ate', 'data')" />
                            <img src="{$conf.addr}/common/img/calendar.png" id="img_data_baixa_ate" style="cursor: pointer;" /> (dd/mm/aaaa)
                        </td>
                    </tr>

                    <script type="text/javascript">			
                        Calendar.setup(
                        {ldelim}
                            inputField : "data_baixa_de", // ID of the input field
                            ifFormat : "%d/%m/%Y", // the date format
                            button : "img_data_baixa_de", // ID of the button
                            align  : "cR"  // alinhamento
                        {rdelim}
                        );
		
                            Calendar.setup(
                        {ldelim}
                            inputField : "data_baixa_ate", // ID of the input field
                            ifFormat : "%d/%m/%Y", // the date format
                            button : "img_data_baixa_ate", // ID of the button
                            align  : "cR"  // alinhamento
                        {rdelim}
                        );						
                    </script>	

                    <tr><td><br></td></tr>

                {/if}

                {if $smarty.get.ac == 'razonete' || $smarty.get.ac == 'balancete' }		        

                    <tr>
                        <td align="right">Intervalo de planos</td>		        		

                        <td align="left">
                            <input type="hidden" name="idplano_ini" id="idplano_ini" value="{$smarty.post.idplano_ini}" />
                            <input type="hidden" name="idplano_ini_NomeTemp" id="idplano_ini_NomeTemp" value="{$smarty.post.idplano_ini_NomeTemp}" />
                            <input class="long" type="text" name="idplano_ini_Nome" id="idplano_ini_Nome" value="{$smarty.post.idplano_ini_Nome}" onKeyUp="javascript: VerificaMudancaCampo('idplano_ini');"/>
                            <span class="nao_selecionou" id="idplano_ini_Flag">
                                &nbsp;&nbsp;
                            </span>

                            &nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;	

                            <input type="hidden" name="idplano_fim" id="idplano_fim" value="{$smarty.post.idplano_fim}" />
                            <input type="hidden" name="idplano_fim_NomeTemp" id="idplano_fim_NomeTemp" value="{$smarty.post.idplano_fim_NomeTemp}" />
                            <input class="long" type="text" name="idplano_fim_Nome" id="idplano_fim_Nome" value="{$smarty.post.idplano_fim_Nome}" onKeyUp="javascript: VerificaMudancaCampo('idplano_fim');"/>
                            <span class="nao_selecionou" id="idplano_fim_Flag">
                                &nbsp;&nbsp;&nbsp;
                            </span>

                        </td>

                    </tr>

                    <tr><td><br></td></tr>
                    <script language="javascript">
													
                        new CAPXOUS.AutoComplete("idplano_ini_Nome", function() {ldelim}
                        return "plano_ajax.php?ac=busca_plano&typing=" + this.text.value + "&idplano=" + document.getElementById('idplano_ini').value + "&campoID=idplano_ini";
                        {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                            //---------------------------------------------------
								
                            new CAPXOUS.AutoComplete("idplano_fim_Nome", function() {ldelim}
                            return "plano_ajax.php?ac=busca_plano&typing=" + this.text.value + "&idplano=" + document.getElementById('idplano_fim').value + "&campoID=idplano_fim";
                        {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                            //---------------------------------------------------
			
				
                            // verifica os campos auto-complete
                            VerificaMudancaCampo('idplano_ini');
                            VerificaMudancaCampo('idplano_fim');
					
                    </script>				

                {/if}
                <tr>
                    <td align="center" colspan="2">
                        <input type="button" class="botao_padrao" value="Buscar!" name="button" onClick="xajax_Verifica_Campos_Busca_Caixa_AJAX(xajax.getFormValues('for'))" />
                    </td>
                </tr>	

            </table>
        </td>
    </tr>

</table>
