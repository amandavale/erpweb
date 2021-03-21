{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay} {include file="div_erro.tpl"}{/if}



<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>

{if $flags.action == "listar" || $flags.action == "baixar_movimentos" || $flags.action == "adicionar" || $flags.action == "editar"} 

    <!--{*  Chama o script para ordenar a tabela  *}-->
    <script language="javascript">
        {literal}

		//Método para não dar conflito com o auto-complete do xajax
		var $j = jQuery.noConflict();
	
		$j(document).ready(function()
			{ 
				$j("#tbl_movimento").tablesorter(); 
		    } 
		);
		
        {/literal}
    </script>
{/if}

{if $flags.action == "adicionar" || $flags.action == "editar"}
<script type="text/javascript" src="{$conf.addr}/common/js/movimento_adicionar_editar.js"></script>
{/if}
{if $flags.action == "listar"}
<script type="text/javascript" src="{$conf.addr}/common/js/movimento_listar.js"></script>
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

            <td class="descricao_tela" WIDTH="15%">
                {$conf.area}
            </td>

            <td class="tela" WIDTH="5%">
                Operações:
            </td>

            <td class="descricao_tela">
                {if $list_permissao.adicionar == '1'}
                    &nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=adicionar">adicionar</a>
                {/if}
                {if $list_permissao.listar == '1'}
                    &nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar">listar</a>
                    &nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=listar&boletos=1">gerar boletos</a>
                {/if}
                {if $list_permissao.editar == '1'}
                    &nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=baixar_movimentos">dar baixa</a>
                {/if}
            </td>

        </tr>
    </table>


    {include file="div_erro.tpl"}



    {if $flags.action == "listar"}
    
      {include file="adm_movimento/listar.tpl"}

    {elseif $flags.action == "baixar_movimentos"}

    	{include file="adm_movimento/baixar_movimentos.tpl"}

    {elseif $flags.action == "editar"}

        <br><br>
        <div style="width: 100%;">

            <table width="100%">
                <form  action="{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$info.idmovimento}{if $smarty.get.pg}&pg={$smarty.get.pg}{/if}" method="post" name = "for" id = "for">
                    <input type="hidden" name="for_chk" id="for_chk" value="1" />


                    <tr>
                        <td align="right">Data e Hora de Cadastro:</td>
                        <td>
                            {$info.data_cadastro_D} às {$info.data_cadastro_H}
                        </td>
                    </tr>

                    <tr><td>&nbsp;</td></tr>


                    <tr>
                        <td class="req" align="right">Descrição:</td>
                        <td><input class="long" type="text" name="litdescricao_movimento" id="litdescricao_movimento" maxlength="100" value="{$info.litdescricao_movimento}"/></td>
                    </tr>

                    <tr><td>&nbsp;</td></tr>

                    <tr>
                        <td align="right" >Cliente de Origem:</td>
                        <td align="left">

                            <input type="hidden" name="idcliente_origem" id="idcliente_origem" value="{$info.cliente_origem.idcliente}" />
                            <input type="hidden" name="idcliente_origem_NomeTemp" id="idcliente_origem_NomeTemp" value="{$info.cliente_origem.nome_cliente}" />
                            <input class="ultralarge" type="text" name="idcliente_origem_Nome" id="idcliente_origem_Nome" value="{$info.cliente_origem.nome_cliente}"
                                   onKeyUp="javascript:
                                           VerificaMudancaCampo('idcliente_origem');
                                   "
                                   />
                            <span class="nao_selecionou" id="idcliente_origem_Flag">
                                &nbsp;&nbsp;&nbsp;
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td align="right" class="">Conta de Débito:</td>
                        <td>

                            <input type="hidden" name="idplano_debito" id="idplano_debito" value="{$info.plano_debito.idplano}" />
                            <input type="hidden" name="idplano_debito_NomeTemp" id="idplano_debito_NomeTemp" value="{$info.plano_debito.descricao}" />
                            <input class="long" type="text" name="idplano_debito_Nome" id="idplano_debito_Nome" value="{$info.plano_debito.descricao}"
                                   onKeyUp="javascript:
                                           VerificaMudancaCampo('idplano_debito');"/>
                            <span class="nao_selecionou" id="idplano_debito_Flag">
                                &nbsp;&nbsp;&nbsp;
                            </span>
                        </td>
                    </tr>

                    <tr><td><br></td></tr>

                    <tr>
                        <td align="right" >Cliente de Destino:</td>
                        <td align="left">

                            <input type="hidden" name="idcliente_destino" id="idcliente_destino" value="{$info.cliente_destino.idcliente}" />
                            <input type="hidden" name="idcliente_destino_NomeTemp" id="idcliente_destino_NomeTemp" value="{$info.cliente_destino.nome_cliente}" />
                            <input class="ultralarge" type="text" name="idcliente_destino_Nome" id="idcliente_destino_Nome" value="{$info.cliente_destino.nome_cliente}"
                                   onKeyUp="javascript:
                                           VerificaMudancaCampo('idcliente_destino');
                                   "
                                   />
                            <span class="nao_selecionou" id="idcliente_destino_Flag">
                                &nbsp;&nbsp;&nbsp;
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td align="right" class="">Conta de Crédito:</td>
                        <td>

                            <input type="hidden" name="idplano_credito" id="idplano_credito" value="{$info.plano_credito.idplano}" />
                            <input type="hidden" name="idplano_credito_NomeTemp" id="idplano_credito_NomeTemp" value="{$info.plano_credito.descricao}" />
                            <input class="long" type="text" name="idplano_credito_Nome" id="idplano_credito_Nome" value="{$info.plano_credito.descricao}"
                                   onKeyUp="javascript:
                                           VerificaMudancaCampo('idplano_credito');"/>
                            <span class="nao_selecionou" id="idplano_credito_Flag">
                                &nbsp;&nbsp;&nbsp;
                            </span>


                        </td>
                    </tr>




                    <script language="javascript">
				
                        new CAPXOUS.AutoComplete("idplano_credito_Nome", function() {ldelim}
                        return "plano_ajax.php?ac=busca_plano&typing=" + this.text.value + "&idplano=" + document.getElementById('idplano_credito').value + "&campoID=idplano_credito&tipo=R";
                        {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                            //---------------------------------------------------
					
                            new CAPXOUS.AutoComplete("idplano_debito_Nome", function() {ldelim}
                            return "plano_ajax.php?ac=busca_plano&typing=" + this.text.value + "&idplano=" + document.getElementById('idplano_debito').value + "&campoID=idplano_debito&tipo=D";
                        {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                            //---------------------------------------------------
					
                            new CAPXOUS.AutoComplete("idcliente_origem_Nome", function() {ldelim}
                            return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente_origem" + "&mostraDetalhes=0";
                        {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                            //---------------------------------------------------
			
			
                            new CAPXOUS.AutoComplete("idcliente_destino_Nome", function() {ldelim}
                            return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente_destino" + "&mostraDetalhes=0";
                        {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                            //---------------------------------------------------		
				
				
                            // verifica os campos auto-complete
                            VerificaMudancaCampo('idplano_debito');
                            VerificaMudancaCampo('idplano_credito');
                            VerificaMudancaCampo('idcliente_origem');
                            VerificaMudancaCampo('idcliente_destino');
				

                    </script>

                    <tr><td><br></td></tr>
                            {* <!--
                            <tr>
                            <td align="right">Conta para Geração Boleto:</td>
                            <td align="left">
                                    
                            <input type="hidden" name="idconta_filial" id="idconta_filial" value="{$info.conta_filial.idconta_filial}" />
                            <input type="hidden" name="idconta_filial_NomeTemp" id="idconta_filial_NomeTemp" value="{$info.conta_filial.nome_banco}" />
                            <input class="extralarge" type="text" name="idconta_filial_Nome" id="idconta_filial_Nome" value="{$info.conta_filial.nome_banco}"
                            onKeyUp="javascript:
                            VerificaMudancaCampo('idconta_filial');
                            "
                            />
                            <span class="nao_selecionou" id="idconta_filial_Flag">
                            &nbsp;&nbsp;&nbsp;
                            </span>
                            </td>
                            </tr>
                            <script type="text/javascript">
                            new CAPXOUS.AutoComplete("idconta_filial_Nome", function() {ldelim}
                            return "conta_filial_ajax.php?ac=busca_conta_filial&typing=" + this.text.value + "&campoID=idconta_filial";
                            {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                        
                            // verifica os campos auto-complete
                            VerificaMudancaCampo('idconta_filial');
                            </script>
                            
                            --> *}


                    <tr>
                        <td align="right">Movimento Original:</td>
                        <td align="left">

                            <input type="hidden" name="idmovimento_origem" id="idmovimento_origem" value="{$info.idmovimento_origem}" />
                            <input type="hidden" name="idmovimento_origem_NomeTemp" id="idmovimento_origem_NomeTemp" value="{$info.movimento_origem.descricao_movimento}" />
                            <input class="extralarge" type="text" name="idmovimento_origem_Nome" id="idmovimento_origem_Nome" value="{$info.movimento_origem.descricao_movimento}"
                                   onKeyUp="javascript:
                                           VerificaMudancaCampo('idmovimento_origem');
                                   "
                                   />
                            <span class="nao_selecionou" id="idmovimento_origem_Flag">
                                &nbsp;&nbsp;&nbsp;
                            </span>
                        </td>
                    </tr>
                    <script type="text/javascript">
                        new CAPXOUS.AutoComplete("idmovimento_origem_Nome", function() {ldelim}
                        return "movimento_ajax.php?ac=busca_movimento&typing=" + this.text.value + "&campoID=idmovimento_origem&idmovimento={$smarty.get.idmovimento}";
                        {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				
                            // verifica os campos auto-complete
                            VerificaMudancaCampo('idmovimento_origem');
                    </script>







                    <tr><td>&nbsp;</td></tr>												

                    <tr>
                        <td align="right">Controle:</td>
                        <td>
                            <input class="short" type="text" name="litcontrole_movimento" id="litcontrole_movimento" value="{$info.litcontrole_movimento}" maxlength='10' />
                        </td>
                    </tr>

                    <tr>
                        <td class="req" align="right">Valor:</td>
                        <td>
                            <input class="short" type="text" name="numvalor_movimento" id="numvalor_movimento" value="{$info.numvalor_movimento}" maxlength='10' onkeydown="FormataValor('numvalor_movimento')" onkeyup="FormataValor('numvalor_movimento')" onblur="calculaJurosMulta('editar')" />
                        </td>
                    </tr>

                    <tr>
                        <td align="right">Valor Juros (%):</td>
                        <td>

                            <input class="short" type="text" id="juros" name='juros' value="{$info.juros}" onkeydown="FormataValor('juros')" onkeyup="FormataValor('juros')" onblur="calculaJuros('editar')"/>

                            <span id="valor_juros_label">R$ {$info.numvalor_juros}</span>
                            <input type="hidden" name="numvalor_juros" id="numvalor_juros" value="{$info.numvalor_juros}" />
                        </td>
                    </tr>

                    <tr>
                        <td align="right">Valor Multa (%):</td>
                        <td>

                            <input class="short" type="text" id="multa" name='multa' value="{$info.multa}" onkeydown="FormataValor('multa')" onkeyup="FormataValor('multa')" onblur="calculaMulta('editar')"/>

                            <span id="valor_multa_label">R$ {$info.numvalor_multa}</span>
                            <input type="hidden" name="numvalor_multa" id="numvalor_multa"  value="{$info.numvalor_multa}" />
                        </td>
                    </tr>

                    <tr>
                        <td align="right">Desconto (R$):</td>
                        <td>
                            <input class="short" type="text" name="numdesconto" id="numdesconto" value="{$info.desconto}" maxlength='10' onkeydown="FormataValor('numdesconto')" onkeyup="FormataValor('numdesconto')" />
                        </td>
                    </tr>

                    <tr>
                        <td align="right">Gerar Boleto:</td>
                        <td>
                            <input {if $info.litgerar_fatura=="0"}checked{/if} onclick="campoTaxaBoleto();" class="radio" type="radio" name="litgerar_fatura" id="gerar_fatura_N" value="0" />Não
                            <input {if $info.litgerar_fatura=="1"}checked{/if} onclick="campoTaxaBoleto();" class="radio" type="radio" name="litgerar_fatura" id="gerar_fatura_S" value="1" />Sim
                        </td>
                    </tr>
                    <script language="javascript">
                        {literal}
          function campoTaxaBoleto(){
          
            if(document.getElementById('gerar_fatura_S').checked)
              document.getElementById('taxa_boleto').disabled = false;
            else
              document.getElementById('taxa_boleto').disabled = true;
          }
                        {/literal}
                    </script>
                    <tr>
                        <td align="right">Taxa do Boleto (R$):</td>
                        <td>
                            <input {if $info.litgerar_fatura=="0"}disabled{/if} class="short" type="text" name="numtaxa_boleto" id="taxa_boleto" value="{$info.taxa_boleto}" maxlength='10' onkeydown="FormataValor('taxa_boleto')" onkeyup="FormataValor('taxa_boleto')" />
                        </td>
                    </tr>

                    <td><tr>&nbsp;</tr></td>

                    <tr>
                        <td class="req" align="right">Data de Ocorrência do Movimento:</td>
                        <td>
                            <input class="short" type="text" name="litdata_movimento" id="litdata_movimento" value="{$info.litdata_movimento}" maxlength='10' onkeydown="mask('litdata_movimento', 'data')" onkeyup="mask('litdata_movimento', 'data')" />
                            <img src="{$conf.addr}/common/img/calendar.png" id="img_litdata_movimento" style="cursor: pointer;" /> (dd/mm/aaaa)
                        </td>
                    </tr>
                    <script type="text/javascript">
                        Calendar.setup(
                        {ldelim}
                            inputField : "litdata_movimento", // ID of the input field
                            ifFormat : "%d/%m/%Y", // the date format
                            button : "img_litdata_movimento", // ID of the button
                            align  : "cR"  // alinhamento
                        {rdelim}
                        );
                    </script>				

                    <tr>
                        <td class="req" align="right">Data de Vencimento:</td>
                        <td>
                            <input class="short" type="text" name="litdata_vencimento" id="litdata_vencimento" value="{$info.litdata_vencimento}" maxlength='10' onkeydown="mask('litdata_vencimento', 'data')" onkeyup="mask('litdata_vencimento', 'data')" />
                            <img src="{$conf.addr}/common/img/calendar.png" id="img_litdata_vencimento" style="cursor: pointer;" /> (dd/mm/aaaa)
                        </td>
                    </tr>
                    <script type="text/javascript">
                        Calendar.setup(
                        {ldelim}
                            inputField : "litdata_vencimento", // ID of the input field
                            ifFormat : "%d/%m/%Y", // the date format
                            button : "img_litdata_vencimento", // ID of the button
                            align  : "cR"  // alinhamento
                        {rdelim}
                        );
                    </script>


                    <tr>
                        <td align="right">Baixado?</td>
                        <td>
                            <input {if $info.litbaixado=="0"}checked{/if} class="radio" type="radio" name="litbaixado" id="litbaixado" value="0" 
                                                             onclick="document.getElementById('data_baixa_D').value = null; document.getElementById('data_baixa_H').value = null;"/>Não

                            <input {if $info.litbaixado=="1"}checked{/if} class="radio" type="radio" name="litbaixado" id="litbaixado" value="1" 
                                                             onclick="document.getElementById('data_baixa_D').value = '{$data_corrente.data}'; document.getElementById('data_baixa_H').value = '{$data_corrente.hora}';"/>Sim
                        </td>
                    </tr>


                    <tr>
                        <td align="right">Data da Baixa:</td>
                        <td>
                            <input class="short" type="text" name="data_baixa_D" id="data_baixa_D" value="{$info.data_baixa_D}" maxlength='10' onkeydown="mask('data_baixa_D', 'data')" onkeyup="mask('data_baixa_D', 'data')" />
                            <img src="{$conf.addr}/common/img/calendar.png" id="img_data_baixa_D" style="cursor: pointer;" /> (dd/mm/aaaa)
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
                    </script>



                    <tr>
                        <td align="right">Hora da Baixa:</td>
                        <td valign="bottom">
                            <input class="short" type="text" name="data_baixa_H" id="data_baixa_H" value="{$info.data_baixa_H}" maxlength='8' onkeydown="mask('data_baixa_H', 'hora')" onkeyup="mask('data_baixa_H', 'hora')" /> 
                            <img src="{$conf.addr}/common/img/clock.jpg" id="img_data_baixa_H" style="cursor: pointer;" onclick="setNow('data_baixa_H');" alt="Inserir horário atual" /> (hh:mm:ss)
                        </td>
                    </tr>


                    <tr>
                        <td align="right">Observação:</td>
                        <td>
                            <textarea name="litobservacao" id="litobservacao" rows='6' cols='38'>{$info.litobservacao}</textarea>
                        </td>
                    </tr>

                    <tr>
                        <td align="right">Negocia&ccedil;&atilde;o?</td>
                        <td>
                            <input {if $info.litnegociacao=="0"}checked{/if} class="radio" type="radio" name="litnegociacao" id="litnegociacao" value="0" onclick="mostraNegociacao()"/>N&atilde;o

                            <input {if $info.litnegociacao=="1"}checked{/if} class="radio" type="radio" name="litnegociacao" id="litnegociacao" value="1" onclick="mostraNegociacao()"/>Sim
                        </td>
                    </tr>

                    <tr id="negociacaoObs">
                        <td align="right">Observa&ccedil;&otilde;es sobre a negocia&ccedil;&atilde;o:</td>
                        <td>
                            <textarea name="litnegociacaoObservacoes" id="litnegociacaoObservacoes" rows='3' cols='38'>{$info.litnegociacaoObservacoes}</textarea>
                        </td>
                    </tr>

                    <tr><td>&nbsp;</td></tr>

                    <tr>
                        <td>&nbsp;</td>
                        <td align="left">
                            <input name="btn_recibo" type="button" class="botao_padrao" value="IMPRIMIR RECIBO" onClick="window.open('{$smarty.server.PHP_SELF}?ac=editar&idmovimento={$smarty.get.idmovimento}&imprimir=1');">
                        </td>
                    </tr>


                    <tr><td>&nbsp;</td></tr>

                    <tr>
                        <td align="center" colspan="2">
                            <input name="btn_alterar" type="button" class="botao_padrao" value="ALTERAR" onClick="xajax_Verifica_Campos_Movimento_AJAX(xajax.getFormValues('for'));">
                            <input name="btn_excluir" type="button" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for','{$smarty.server.PHP_SELF}?ac=excluir&idmovimento={$smarty.get.idmovimento}&pg={$smarty.get.pg}','ATENÇÃO! Confirma a exclusão ?'))" >
                        </td>
                    </tr>

                </form>
            </table>
        </div>  

    {elseif $flags.action == "adicionar"}


        <br><br>

        <div style="width: 100%;">


            <form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for" id = "for">
                <input type="hidden" name="for_chk" id="for_chk" value="1" />
                <table width="100%">	

                    <tr>
                        <td class="req" align="right">Descrição:</td>
                        <td><input class="long" type="text" name="descricao_movimento" id="descricao_movimento" maxlength="100" value="{$smarty.post.descricao_movimento}"/></td>
                    </tr>

                    <tr><td><br></td></tr>

                    <tr>
                        <td align="right" >Cliente de Origem:</td>
                        <td align="left">

                            <input type="hidden" name="idcliente_origem" id="idcliente_origem" value="{$smarty.post.idcliente_origem}" onchange="alert('teste');" />
                            <input type="hidden" name="idcliente_origem_NomeTemp" id="idcliente_origem_NomeTemp" value="{$smarty.post.idcliente_origem_NomeTemp}" />
                            <input class="ultralarge" type="text" name="idcliente_origem_Nome" id="idcliente_origem_Nome" value="{$smarty.post.idcliente_origem_Nome}"
                                   onKeyUp="javascript:
                                           VerificaMudancaCampo('idcliente_origem');
                                   "
                                   />
                            <span class="nao_selecionou" id="idcliente_origem_Flag">
                                &nbsp;&nbsp;&nbsp;
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="">Conta de Débito:</td>
                        <td>

                            <input type="hidden" name="idplano_debito" id="idplano_debito" value="{$smarty.post.idplano_debito}" />
                            <input type="hidden" name="idplano_debito_NomeTemp" id="idplano_debito_NomeTemp" value="{$smarty.post.idplano_debito_NomeTemp}" />
                            <input class="long" type="text" name="idplano_debito_Nome" id="idplano_debito_Nome" value="{$smarty.post.idplano_debito_Nome}"
                                   onKeyUp="javascript:
                                           VerificaMudancaCampo('idplano_debito');"/>
                            <span class="nao_selecionou" id="idplano_debito_Flag">
                                &nbsp;&nbsp;&nbsp;
                            </span>


                        </td>
                    </tr>

                    <tr><td><br></td></tr>


                    <tr>
                        <td align="right" >Cliente de Destino:</td>
                        <td align="left">

                            <input type="hidden" name="idcliente_destino" id="idcliente_destino" value="{$cliente_destino.idcliente}" />
                            <input type="hidden" name="idcliente_destino_NomeTemp" id="idcliente_destino_NomeTemp" value="{$cliente_destino.nome_cliente}" />
                            <input class="ultralarge" type="text" name="idcliente_destino_Nome" id="idcliente_destino_Nome" value="{$cliente_destino.nome_cliente}"
                                   onKeyUp="javascript:
                                           VerificaMudancaCampo('idcliente_destino');
                                   "
                                   />
                            <span class="nao_selecionou" id="idcliente_destino_Flag">
                                &nbsp;&nbsp;&nbsp;
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td align="right" class="">Conta de Crédito:</td>
                        <td>

                            <input type="hidden" name="idplano_credito" id="idplano_credito" value="{$smarty.post.idplano_credito}" />
                            <input type="hidden" name="idplano_credito_NomeTemp" id="idplano_credito_NomeTemp" value="{$smarty.post.idplano_credito_NomeTemp}" />
                            <input class="long" type="text" name="idplano_credito_Nome" id="idplano_credito_Nome" value="{$smarty.post.idplano_credito_Nome}"
                                   onKeyUp="javascript:
                                           VerificaMudancaCampo('idplano_credito');"/>
                            <span class="nao_selecionou" id="idplano_credito_Flag">
                                &nbsp;&nbsp;&nbsp;
                            </span>


                        </td>
                    </tr>

                    <tr><td><br></td></tr>



                    <script language="javascript">
                        new CAPXOUS.AutoComplete("idplano_credito_Nome", function() {ldelim}
                        return "plano_ajax.php?ac=busca_plano&typing=" + this.text.value + "&idplano=" + document.getElementById('idplano_credito').value + "&campoID=idplano_credito&tipo=R";
                        {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                            //---------------------------------------------------
	                                
                            new CAPXOUS.AutoComplete("idplano_debito_Nome", function() {ldelim}
                            return "plano_ajax.php?ac=busca_plano&typing=" + this.text.value + "&idplano=" + document.getElementById('idplano_debito').value + "&campoID=idplano_debito&tipo=D";
                        {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                            //---------------------------------------------------
	                                
                            new CAPXOUS.AutoComplete("idcliente_origem_Nome", function() {ldelim}
                            return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente_origem" + "&mostraDetalhes=0";
                        {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                            //---------------------------------------------------
			
			
                            new CAPXOUS.AutoComplete("idcliente_destino_Nome", function() {ldelim}
                            return "cliente_ajax.php?ac=busca_cliente&typing=" + this.text.value + "&campoID=idcliente_destino" + "&mostraDetalhes=0";
                        {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                            //---------------------------------------------------		
				
				
                            // verifica os campos auto-complete
                            VerificaMudancaCampo('idplano_debito');
                            VerificaMudancaCampo('idplano_credito');
                            VerificaMudancaCampo('idcliente_origem');
                            VerificaMudancaCampo('idcliente_destino');

                    </script>

                    {* <!==
                    <tr>
                    <td align="right">Conta para Geração Boleto:</td>
                    <td align="left">
                            
                    <input type="hidden" name="idconta_filial" id="idconta_filial" value="{$smarty.post.idconta_filial}" />
                    <input type="hidden" name="idconta_filial_NomeTemp" id="idconta_filial_NomeTemp" value="{$smarty.post.idconta_filial_NomeTemp}" />
                    <input class="extralarge" type="text" name="idconta_filial_Nome" id="idconta_filial_Nome" value="{$smarty.post.idconta_filial_Nome}"
                    onKeyUp="javascript:
                    VerificaMudancaCampo('idconta_filial');
                    "
                    />
                    <span class="nao_selecionou" id="idconta_filial_Flag">
                    &nbsp;&nbsp;&nbsp;
                    </span>
                    </td>
                    </tr>
                    <script type="text/javascript">
                    new CAPXOUS.AutoComplete("idconta_filial_Nome", function() {ldelim}
                    return "conta_filial_ajax.php?ac=busca_conta_filial&typing=" + this.text.value + "&campoID=idconta_filial";
                    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                
                    // verifica os campos auto-complete
                    VerificaMudancaCampo('idconta_filial');
                    </script>
                    --> *}

                    <tr>
                        <td align="right">Movimento Original:</td>
                        <td align="left">

                            <input type="hidden" name="idmovimento_origem" id="idmovimento_origem" value="{$smarty.post.idmovimento_origem}" />
                            <input type="hidden" name="idmovimento_origem_NomeTemp" id="idmovimento_origem_NomeTemp" value="{$smarty.post.idmovimento_origem_NomeTemp}" />
                            <input class="extralarge" type="text" name="idmovimento_origem_Nome" id="idmovimento_origem_Nome" value="{$smarty.post.idmovimento_origem_Nome}"
                                   onKeyUp="javascript:
                                           VerificaMudancaCampo('idmovimento_origem');
                                   "
                                   />
                            <span class="nao_selecionou" id="idmovimento_origem_Flag">
                                &nbsp;&nbsp;&nbsp;
                            </span>
                        </td>
                    </tr>
                    <script type="text/javascript">
                        new CAPXOUS.AutoComplete("idmovimento_origem_Nome", function() {ldelim}
                        return "movimento_ajax.php?ac=busca_movimento&typing=" + this.text.value + "&campoID=idmovimento_origem";
                        {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
				
                            // verifica os campos auto-complete
                            VerificaMudancaCampo('idmovimento_origem');
                    </script>



                    <tr><td>&nbsp;</td></tr>

                    <tr>
                        <td align="right">Controle:</td>
                        <td>
                            <input class="short" type="text" name="controle_movimento" id="controle_movimento" value="{$smarty.post.controle_movimento}" maxlength='10'  />
                        </td>
                    </tr>

                    <tr>
                        <td class="req" align="right">Valor:</td>
                        <td>
                            <input class="short" type="text" name="valor_movimento" id="valor_movimento" value="{$smarty.post.valor_movimento}" maxlength='10' onkeydown="FormataValor('valor_movimento')" onkeyup="FormataValor('valor_movimento')" onblur="calculaJurosMulta('adicionar')" />
                        </td>
                    </tr>

                    <tr>
                        <td align="right">Valor Juros (%):</td>
                        <td>
                            <input class="short" type="text" id="juros" name='juros' value="{$juros}" onkeydown="FormataValor('juros')" onkeyup="FormataValor('juros')" onblur="calculaJuros('adicionar')"/>

                            <span id="valor_juros_label"></span>
                            <input type="hidden" name="valor_juros" id="valor_juros" value="{$smarty.post.valor_juros}" />    
                        </td>
                    </tr>

                    <tr>
                        <td align="right">Valor Multa (%):</td>
                        <td>

                            <input class="short" type="text" id="multa" name='multa' value="{$multa}" onkeydown="FormataValor('multa')" onkeyup="FormataValor('multa')" onblur="calculaMulta('adicionar')"/>

                            <span id="valor_multa_label"></span>
                            <input type="hidden" name="valor_multa" id="valor_multa" value="{$smarty.post.valor_multa}" />
                        </td>
                    </tr>

                    <tr>
                        <td align="right">Desconto (R$):</td>
                        <td>
                            <input class="short" type="text" name="desconto" id="desconto" value="{$smarty.post.desconto}" maxlength='10' onkeydown="FormataValor('desconto')" onkeyup="FormataValor('desconto')" />
                        </td>
                    </tr>

                    <tr>
                        <td align="right">Gerar Boleto:</td>
                        <td>
                            <input {if $smarty.post.gerar_fatura!="1"}checked{/if} onclick="campoTaxaBoleto();" class="radio" type="radio" name="gerar_fatura" id="gerar_fatura_N" value="0" />Não
                            <input {if $smarty.post.gerar_fatura=="1"}checked{/if} onclick="campoTaxaBoleto();" class="radio" type="radio" name="gerar_fatura" id="gerar_fatura_S" value="1" />Sim
                        </td>
                    </tr>

                    <script language="javascript">
                        {literal}
          function campoTaxaBoleto(){
          
            if(document.getElementById('gerar_fatura_S').checked){
              document.getElementById('taxa_boleto').disabled = false;
              document.getElementById('banco').disabled = false;
            }
            else{
              document.getElementById('taxa_boleto').disabled = true;
              document.getElementById('banco').disabled = false;
            }
             
          }
                        {/literal}
                    </script>

                    <tr>
                        <td align="right">Taxa do Boleto (R$):</td>
                        <td>
                            <input disabled class="short" type="text" name="taxa_boleto" id="taxa_boleto" value="{$smarty.post.taxa_boleto}" maxlength='10' onkeydown="FormataValor('taxa_boleto')" onkeyup="FormataValor('taxa_boleto')" />
                        </td>
                    </tr>

                    <tr>
                        <td align="right">Banco:</td>
                        <td align="left">
                            <select id="banco" name="banco" disabled >
                                <option value="bradesco">Bradesco</option>
                                <option value="caixa">Caixa Econômica Federal</option>
                                <option value="itau">Ita&uacute;</option>
                                <option value="santander">Santander</option>
                            </select>
                        </td>
                    </tr>

                    <tr><td>&nbsp;</td></tr>

                    <tr>
                        <td class="req" align="right">Data de Ocorrência do Movimento:</td>
                        <td>
                            <input class="short" type="text" name="data_movimento" id="data_movimento" value="{$smarty.post.data_movimento}" maxlength='10' onkeydown="mask('data_movimento', 'data')" onkeyup="mask('data_movimento', 'data')" /> 
                            <img src="{$conf.addr}/common/img/calendar.png" id="img_data_movimento" style="cursor: pointer;" /> (dd/mm/aaaa)
                        </td>
                    </tr>
                    <script type="text/javascript">
                        Calendar.setup(
                        {ldelim}
                            inputField : "data_movimento", // ID of the input field
                            ifFormat : "%d/%m/%Y", // the date format
                            button : "img_data_movimento", // ID of the button
                            align  : "cR"  // alinhamento
                        {rdelim}
                        );
                    </script>				

                    <tr>
                        <td class="req" align="right">Data de Vencimento:</td>
                        <td>
                            <input class="short" type="text" name="data_vencimento" id="data_vencimento" value="{$smarty.post.data_vencimento}" maxlength='10' onkeydown="mask('data_vencimento', 'data')" onkeyup="mask('data_vencimento', 'data')" />
                            <img src="{$conf.addr}/common/img/calendar.png" id="img_data_vencimento" style="cursor: pointer;" /> (dd/mm/aaaa)
                        </td>
                    </tr>
                    <script type="text/javascript">
                        Calendar.setup(
                        {ldelim}
                            inputField : "data_vencimento", // ID of the input field
                            ifFormat : "%d/%m/%Y", // the date format
                            button : "img_data_vencimento", // ID of the button
                            align  : "cR"  // alinhamento
                        {rdelim}
                        );
                    </script>

                    <tr>
                        <td align="right">Baixado?</td>
                        <td>
                            <input {if $smarty.post.baixado!="1"}checked{/if} class="radio" type="radio" name="baixado" id="baixado" value="0" 
                                                                 onclick="document.getElementById('data_baixa_D').value = null; document.getElementById('data_baixa_H').value = null;"/>Não
                            <input {if $smarty.post.baixado=="1"}checked{/if} class="radio" type="radio" name="baixado" id="baixado" value="1" 
                                                                 onclick="document.getElementById('data_baixa_D').value = '{$data_corrente.data}'; document.getElementById('data_baixa_H').value = '{$data_corrente.hora}';"/>Sim
                        </td>
                    </tr>	

                    <tr>
                        <td align="right">Data da Baixa:</td>
                        <td>
                            <input class="short" type="text" name="data_baixa_D" id="data_baixa_D" value="{$smarty.post.data_baixa_D}" maxlength='10' onkeydown="mask('data_baixa_D', 'data')" onkeyup="mask('data_baixa_D', 'data')" />
                            <img src="{$conf.addr}/common/img/calendar.png" id="img_data_baixa_D" style="cursor: pointer;" /> (dd/mm/aaaa)
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
                    </script>	



                    <tr>
                        <td align="right">Hora da Baixa:</td>
                        <td>
                            <input class="short" type="text" name="data_baixa_H" id="data_baixa_H" value="{$smarty.post.data_baixa_H}" maxlength='8' onkeydown="mask('data_baixa_H', 'hora')" onkeyup="mask('data_baixa_H', 'hora')" /> 
                            <img src="{$conf.addr}/common/img/clock.jpg" id="img_data_baixa_H" style="cursor: pointer;" onclick="setNow('data_baixa_H');" alt="Inserir horário atual" /> (hh:mm:ss)
                        </td>
                    </tr>

                    <tr>
                        <td align="right">Gerar Recibo </td>
                        <td><input type="checkbox" name="gerar_recibo" id="gerar_recibo"></td>
                    </tr>

                    <tr>
                        <td align="right">Observa&ccedil;&atilde;o:</td>
                        <td>
                            <textarea name="observacao" id="observacao" rows='6' cols='38'>{$smarty.post.observacao}</textarea>
                        </td>
                    </tr>

                    <tr><td>&nbsp;</td></tr>

                    <tr>
                        <td colspan="2" align="center">
                            <input name="btn_adicionar" type="button" class="botao_padrao" value="Adicionar" onClick="xajax_Verifica_Campos_Movimento_AJAX(xajax.getFormValues('for'));">
                        </td>
                    </tr>
                </table>
            </form>

        </div>

    {/if}

{/if}


{include file="com_rodape.tpl"}
