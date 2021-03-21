{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}

<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/tabs.js"></script>


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
                    &nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=busca_parametrizada">buscar</a> 
                {/if}


            </td>
        </tr>
    </table>


    {include file="div_erro.tpl"}


    {if $flags.action == "listar"}

        {if $flags.sucesso != ""}
            {include file="div_resultado_inicio.tpl"}
            {$flags.sucesso}
            {include file="div_resultado_fim.tpl"}
        {/if}
        <br><br>
        <table align="center" width="90%">						
            <tr>
                <td colspan="9" align="center">
                    Cliente:
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
                return "cliente_ajax.php?ac=busca_cliente_condominio&typing=" + this.text.value + "&campoID=idcliente" + "&mostraDetalhes=1";
                {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
            </script>


            <script type="text/javascript">
                // verifica os campos auto-complete
                VerificaMudancaCampo('idcliente');
            </script>

            <tr>
                <td colspan="9" align="center">
                    <div id="dados_cliente">
                    </div>
                </td>
            </tr>
        </table>

    {elseif $flags.action == "editar"}

        <br>


        <div style="width: 100%;">

            <form  action="{$smarty.server.PHP_SELF}?ac=editar&idcliente={$info.idcliente}" method="post" name = "for_cliente" id = "for_cliente">
                <input type="hidden" name="for_chk" id="for_chk" value="1" />
                <input type="hidden" name="idendereco_cliente" id="idendereco_cliente" value="{$info.idendereco_cliente}" />
                <input type="hidden" name="idendereco_cobranca" id="idendereco_cobranca" value="{$info.idendereco_cobranca}" />


                <ul class="anchors">
                    <li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados Gerais</a></li>
                    <li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Dados do Condomínio</a></li>
                    <li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Endereços</a></li>
                    <li><a id="a_tab_3" onclick="Processa_Tabs(3, 'tab_')" href="javascript:;">Outras Informações</a></li>
                    <li><a id="a_tab_4" onclick="Processa_Tabs(4, 'tab_')" href="javascript:;">Configurações do Boleto</a></li>
                </ul>

                {************************************}
                {* TAB 0 *}
                {************************************}

                <div id="tab_0" class="anchor">

                    <table width="95%" align="center">

                        <tr>
                            <td width="30%" class="req" align="right">Nome:</td>
                            <td><input class="long" type="text" name="litnome_cliente" id="litnome_cliente" maxlength="100" value="{$info.litnome_cliente}"/></td>
                        </tr>

                        <tr>
                            <td align="right">CNPJ:</td>
                            <td><input class="long" type="text" name="litcnpj" id="litcnpj" maxlength="18" value="{$info.litcnpj}" onkeydown="mask('litcnpj', 'cnpj')" onkeyup="mask('litcnpj', 'cnpj')" /></td>
                        </tr>

                        <tr>
                            <td align="right">Ramo de atividade:</td>
                            <td>
                                <select name="numidramo_atividade" id="numidramo_atividade">
                                    <option value="">[selecione]</option>
                                    {html_options values=$list_ramo_atividade.idramo_atividade output=$list_ramo_atividade.descricao_atividade selected=$info.numidramo_atividade}
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Telefone:</td>
                            <td>
                                <input class="tiny" type="text" name="telefone_cliente_ddd" id="telefone_cliente_ddd" value="{$info.telefone_cliente_ddd}" maxlength='2' />
                                <input class="short" type="text" name="littelefone_cliente" id="littelefone_cliente" value="{$info.littelefone_cliente}" maxlength='9'onkeydown="mask('littelefone_cliente', 'tel')" onkeyup="mask('littelefone_cliente', 'tel')" />
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Fax:</td>
                            <td>
                                <input class="tiny" type="text" name="fax_cliente_ddd" id="fax_cliente_ddd" value="{$info.fax_cliente_ddd}" maxlength='2' />
                                <input class="short" type="text" name="litfax_cliente" id="litfax_cliente" value="{$info.litfax_cliente}" maxlength='9'onkeydown="mask('litfax_cliente', 'tel')" onkeyup="mask('litfax_cliente', 'tel')" />
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Email:</td>
                            <td><input class="long" type="text" name="litemail_cliente" id="litemail_cliente" maxlength="100" value="{$info.litemail_cliente}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Site:</td>
                            <td><input class="long" type="text" name="litsite_cliente" id="litsite_cliente" maxlength="100" value="{$info.litsite_cliente}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Cliente bloqueado ?</td>
                            <td>
                                <input {if $info.litcliente_bloqueado=="0"}checked{/if} class="radio" type="radio" name="litcliente_bloqueado" id="litcliente_bloqueado" value="0" />Não
                                <input {if $info.litcliente_bloqueado=="1"}checked{/if} class="radio" type="radio" name="litcliente_bloqueado" id="litcliente_bloqueado" value="1" />Sim
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Motivo do bloqueio:</td>
                            <td>
                                <select name="numidmotivo_bloqueio" id="numidmotivo_bloqueio">
                                    <option value="">[selecione]</option>
                                    {html_options values=$list_motivo_bloqueio.idmotivo_bloqueio output=$list_motivo_bloqueio.motivo_bloqueio selected=$info.numidmotivo_bloqueio}
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Data do bloqueio:</td>
                            <td>
                                <input class="short" type="text" name="litdata_bloqueio_cliente" id="litdata_bloqueio_cliente" value="{$info.litdata_bloqueio_cliente}" maxlength='10' onkeydown="mask('litdata_bloqueio_cliente', 'data')" onkeyup="mask('litdata_bloqueio_cliente', 'data')" /> (dd/mm/aaaa)
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Tipo de Cliente:</td>
                            <td>
                                <select name="littipo_cliente" id="littipo_cliente">
                                    <option value="">[selecione]</option>							
                                    <option value="A" {if $info.littipo_cliente == "A"}selected{/if}>Cliente Avulso</option>
                                    <option value="F" {if $info.littipo_cliente == "F"}selected{/if} >Contrato Fixo</option>
                                </select>
                            </td>
                        </tr>


                    </table>

                </div>


                {************************************}
                {*  TAB 1 					*}
                {************************************}

                <div id="tab_1" class="anchor" >

                    <table width="80%" align="center">

                        <tr>
                            <td>
                                <table>
                                
						            <tr>
						            	<td align="right" width="30%">Condom&iacute;nio associado ao caixa propriet&aacute;rio:</td>
						            	
						                <td align="left">
						                    
						                    <input type="hidden" name="numcondominio_caixa" id="numcondominio_caixa" value="{$info.numcondominio_caixa}" />
						                    <input type="hidden" name="numcondominio_caixa_NomeTemp" id="numcondominio_caixa_NomeTemp" value="{$info.numcondominio_caixa_NomeTemp}" />
						                    <input class="ultralarge" type="text" name="numcondominio_caixa_Nome" id="numcondominio_caixa_Nome" value="{$info.numcondominio_caixa_Nome}"
						                           onKeyUp="javascript:
						                                   VerificaMudancaCampo('numcondominio_caixa');
						                           "
						                           />
						                    <span class="nao_selecionou" id="numcondominio_caixa_Flag">
						                        &nbsp;&nbsp;&nbsp;
						                    </span>
						                </td>
						            </tr>
						            <script type="text/javascript">
						                new CAPXOUS.AutoComplete("numcondominio_caixa_Nome", function() {ldelim}
						                return "cliente_ajax.php?ac=busca_cliente_condominio&typing=" + this.text.value + "&campoID=numcondominio_caixa" + "&mostraDetalhes=1";
						                {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
						            </script>
						
						
						            <script type="text/javascript">
						                // verifica os campos auto-complete
						                VerificaMudancaCampo('numcondominio_caixa');
						            </script>
                                
                                    <tr>
                                        <td align="right"  width="30%">Administração Financeira?</td>
                                        <td  align="left">
                                            <input {if $info.admFinanceira=="S"}checked{/if} class="radio" type="radio" name="litadmFinanceira" id="litadmFinanceira" value="S" />Sim
                                            <input {if $info.admFinanceira=="N"}checked{/if} class="radio" type="radio" name="litadmFinanceira" id="litadmFinanceira" value="N" />Não
                                        </td>
                                    </tr>


                                    <tr>
                                        <td align="right"  width="30%">Valor do Condomínio:</td>
                                        <td align="left">
                                            <input class="short" type="text" name="numtaxa_condominio" id="numtaxa_condominio" value="{$info.taxa_condominio}" maxlength='10' onkeydown="FormataValor('numtaxa_condominio')" onkeyup="FormataValor('numtaxa_condominio')" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"  width="30%">Dia de Vencimento do Condomínio:</td>
                                        <td align="left">
                                            <input class="tiny" maxlength="2" type="text" name="litsugestaoVencimento" id="litsugestaoVencimento" value="{$info.sugestaoVencimento}" maxlength='10' onkeydown="FormataInteiro('litsugestaoVencimento');" onkeyup="FormataInteiro('litsugestaoVencimento');"  />
                                        </td>
                                    </tr>


                                    <tr>
                                        <td align="right"  width="30%">Fundo de Reserva:</td>
                                        <td  align="left">
                                            <input class="short" type="text" name="numsugestaoReserva" id="numsugestaoReserva" value="{$info.numsugestaoReserva}" maxlength='10' onkeydown="FormataValor('numsugestaoReserva')" onkeyup="FormataValor('numsugestaoReserva')" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"  width="30%">Taxa de Administração:</td>
                                        <td  align="left">
                                            <input class="short" type="text" name="numvalAdm" id="numvalAdm" value="{$info.numvalAdm}" maxlength='10' onkeydown="FormataValor('numvalAdm')" onkeyup="FormataValor('numvalAdm')" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"  width="30%">Preço Faxina:</td>
                                        <td  align="left">
                                            <input class="short" type="text" name="numvalFaxina" id="numvalFaxina" value="{$info.numvalFaxina}" maxlength='10' onkeydown="FormataValor('numvalFaxina')" onkeyup="FormataValor('numvalFaxina')" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"  width="30%">Preço Vigia:</td>
                                        <td  align="left">
                                            <input class="short" type="text" name="numvalVigia" id="numvalVigia" value="{$info.numvalVigia}" maxlength='10' onkeydown="FormataValor('numvalVigia')" onkeyup="FormataValor('numvalVigia')" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"  width="30%">Emissão Própria?</td>
                                        <td  align="left">
                                            <input {if $info.litemissaoPropria!="S"}checked{/if} class="radio" type="radio" name="litemissaoPropria" id="litemissaoPropria" value="N" onclick="document.getElementById('dados_banco').style.display='none';" />Não
                                            <input {if $info.litemissaoPropria=="S"}checked{/if} class="radio" type="radio" name="litemissaoPropria" id="litemissaoPropria" value="S" onclick="document.getElementById('dados_banco').style.display='block';"/>Sim
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>

                        <tr>
                            <td>

                                <table id="dados_banco" style="{if $info.litemissaoPropria!="S"}display:none;{/if}" align="left">
                                    <tr>
                                        <td align="right">Banco:</td>
                                        <td>
                                            <select name="numidbanco" id="numidbanco">
                                                <option value="">[selecione]</option>
                                                {html_options values=$list_banco.idbanco output=$list_banco.nome_banco selected=$info.numidbanco}
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right">Ag&ecirc;ncia:</td>
                                        <td>
                                            <input class="short" type="text" name="numagencia" id="numagencia" value="{$info.numagencia}" maxlength='10' onkeydown="FormataInteiro('numagencia')" onkeyup="FormataInteiro('numagencia')" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right">Dígito Agência:</td>
                                        <td>
                                            <input class="tiny" type="text" name="numagenciaDigito" id="numagenciaDigito" value="{$info.numagenciaDigito}" maxlength='10' onkeydown="FormataInteiro('numagenciaDigito')" onkeyup="FormataInteiro('numagenciaDigito')" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right">Conta:</td>
                                        <td>
                                            <input class="short" type="text" name="numconta" id="numconta" value="{$info.numconta}" maxlength='10' onkeydown="FormataInteiro('numconta')" onkeyup="FormataInteiro('numconta')" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right">Dígito Conta:</td>
                                        <td>
                                            <input class="tiny" type="text" name="numcontaDigito" id="numcontaDigito" value="{$info.numcontaDigito}" maxlength='10' onkeydown="FormataInteiro('numcontaDigito')" onkeyup="FormataInteiro('numcontaDigito')" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right">Número do Contrato:</td>
                                        <td>
                                            <input class="short" type="text" name="numnumeroContrato" id="numnumeroContrato" value="{$info.numnumeroContrato}" maxlength='10' onkeydown="FormataInteiro('numnumeroContrato')" onkeyup="FormataInteiro('numnumeroContrato')" />
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                    </table>

                </div>


                {************************************}
                {* TAB 2 *}
                {************************************}

                <div id="tab_2" class="anchor">

                    <div style="width: 100%;">

                        <ul class="anchors">
                            <li><a id="a_tab_2_0" onclick="Processa_Tabs(0, 'tab_2_')" href="javascript:;">Endereço do Cliente</a></li>
                            <li><a id="a_tab_2_1" onclick="Processa_Tabs(1, 'tab_2_')" href="javascript:;">Endereço de Cobrança</a></li>
                        </ul>


                        {************************************}
                        {* TAB 2.0 *}
                        {************************************}

                        <div id="tab_2_0" class="anchor">

                            <table width="95%" align="center">

                                <tr>
                                    <td width="30%" align="right">Logradouro:</td>
                                    <td><input class="long" type="text" name="cliente_logradouro" id="cliente_logradouro" maxlength="100" value="{$info.cliente_logradouro}"/></td>
                                </tr>

                                <tr>
                                    <td align="right">Nº:</td>
                                    <td><input class="short" type="text" name="cliente_numero" id="cliente_numero" maxlength="10" value="{$info.cliente_numero}"/></td>
                                </tr>

                                <tr>
                                    <td align="right">Complemento:</td>
                                    <td><input class="medium" type="text" name="cliente_complemento" id="cliente_complemento" maxlength="50" value="{$info.cliente_complemento}"/></td>
                                </tr>

                                <tr>
                                    <td align="right">Estado:</td>
                                    <td>
                                        <input type="hidden" name="cliente_idestado" id="cliente_idestado" value="{$info.cliente_idestado}" />
                                        <input type="hidden" name="cliente_idestado_NomeTemp" id="cliente_idestado_NomeTemp" value="{$info.cliente_idestado_NomeTemp}" />
                                        <input class="long" type="text" name="cliente_idestado_Nome" id="cliente_idestado_Nome" value="{$info.cliente_idestado_Nome}"
                                               onKeyUp="javascript:
                                                       VerificaMudancaCampo('cliente_idestado', 'cliente_idcidade#cliente_idbairro');
                                               "
                                               />
                                        <span class="nao_selecionou" id="cliente_idestado_Flag">
                                            &nbsp;&nbsp;&nbsp;
                                        </span>
                                    </td>
                                </tr>
                                <script type="text/javascript">
                                    new CAPXOUS.AutoComplete("cliente_idestado_Nome", function() {ldelim}
                                    return "estado_ajax.php?ac=busca_estado&typing=" + this.text.value + "&campoID=cliente_idestado";
                                    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                                </script>


                                <tr>
                                    <td align="right">Cidade:</td>
                                    <td>
                                        <input type="hidden" name="cliente_idcidade" id="cliente_idcidade" value="{$info.cliente_idcidade}" />
                                        <input type="hidden" name="cliente_idcidade_NomeTemp" id="cliente_idcidade_NomeTemp" value="{$info.cliente_idcidade_NomeTemp}" />
                                        <input class="long" type="text" name="cliente_idcidade_Nome" id="cliente_idcidade_Nome" value="{$info.cliente_idcidade_Nome}"
                                               onKeyUp="javascript:
                                                       VerificaMudancaCampo('cliente_idcidade','cliente_idbairro');
                                               "
                                               />
                                        <span class="nao_selecionou" id="cliente_idcidade_Flag">
                                            &nbsp;&nbsp;&nbsp;
                                        </span>
                                    </td>
                                </tr>
                                <script type="text/javascript">
                                    new CAPXOUS.AutoComplete("cliente_idcidade_Nome", function() {ldelim}
                                    return "cidade_ajax.php?ac=busca_cidade&typing=" + this.text.value + "&campoID=cliente_idcidade" + "&idestado=" + document.getElementById('cliente_idestado').value;
                                    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                                </script>


                                <tr>
                                    <td align="right">Bairro:</td>
                                    <td>
                                        <input type="hidden" name="cliente_idbairro" id="cliente_idbairro" value="{$info.cliente_idbairro}" />
                                        <input type="hidden" name="cliente_idbairro_NomeTemp" id="cliente_idbairro_NomeTemp" value="{$info.cliente_idbairro_NomeTemp}" />
                                        <input class="long" type="text" name="cliente_idbairro_Nome" id="cliente_idbairro_Nome" value="{$info.cliente_idbairro_Nome}"
                                               onKeyUp="javascript:
                                                       VerificaMudancaCampo('cliente_idbairro');
                                               "
                                               />
                                        <span class="nao_selecionou" id="cliente_idbairro_Flag">
                                            &nbsp;&nbsp;&nbsp;
                                        </span>
                                    </td>
                                </tr>
                                <script type="text/javascript">
                                    new CAPXOUS.AutoComplete("cliente_idbairro_Nome", function() {ldelim}
                                    return "bairro_ajax.php?ac=busca_bairro&typing=" + this.text.value + "&campoID=cliente_idbairro" + "&idcidade=" + document.getElementById('cliente_idcidade').value;
                                    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                                </script>


                                <script type="text/javascript">
                                    // verifica os campos auto-complete
                                    VerificaMudancaCampo('cliente_idestado', 'cliente_idcidade#cliente_idbairro');
                                    VerificaMudancaCampo('cliente_idcidade','cliente_idbairro');
                                    VerificaMudancaCampo('cliente_idbairro');
                                </script>


                                <tr>
                                    <td align="right">CEP:</td>
                                    <td>
                                        <input class="short" type="text" name="cliente_cep" id="cliente_cep" value="{$info.cliente_cep}" maxlength='10' onkeydown="mask('cliente_cep', 'cep')" onkeyup="mask('cliente_cep', 'cep')" />
                                    </td>
                                </tr>


                            </table>

                        </div>


                        {************************************}
                        {* TAB 2.1 *}
                        {************************************}

                        <div id="tab_2_1" class="anchor">

                            <table width="95%" align="center">

                                <tr>
                                    <td align="center" colspan="2">
                                        O endereço do cliente é o mesmo endereço da cobrança?
                                        <input {if $info.litmesmo_endereco=="0"}checked{/if} class="radio" type="radio" name="litmesmo_endereco" id="litmesmo_endereco" value="0" />Não
                                        <input {if $info.litmesmo_endereco=="1"}checked{/if} class="radio" type="radio" name="litmesmo_endereco" id="litmesmo_endereco" value="1" />Sim
                                    </td>
                                </tr>

                                <tr>
                                    <td align="center" colspan="2">
                                        Se não for o mesmo, preencha os campos abaixo.
                                    </td>
                                </tr>

                                <tr>
                                    <td align="right">Logradouro:</td>
                                    <td><input class="long" type="text" name="cobranca_logradouro" id="cobranca_logradouro" maxlength="100" value="{$info.cobranca_logradouro}"/></td>
                                </tr>

                                <tr>
                                    <td align="right">Nº:</td>
                                    <td><input class="short" type="text" name="cobranca_numero" id="cobranca_numero" maxlength="10" value="{$info.cobranca_numero}"/></td>
                                </tr>

                                <tr>
                                    <td align="right">Complemento:</td>
                                    <td><input class="medium" type="text" name="cobranca_complemento" id="cobranca_complemento" maxlength="50" value="{$info.cobranca_complemento}"/></td>
                                </tr>

                                <tr>
                                    <td align="right">Estado:</td>
                                    <td>
                                        <input type="hidden" name="cobranca_idestado" id="cobranca_idestado" value="{$info.cobranca_idestado}" />
                                        <input type="hidden" name="cobranca_idestado_NomeTemp" id="cobranca_idestado_NomeTemp" value="{$info.cobranca_idestado_NomeTemp}" />
                                        <input class="long" type="text" name="cobranca_idestado_Nome" id="cobranca_idestado_Nome" value="{$info.cobranca_idestado_Nome}"
                                               onKeyUp="javascript:
                                                       VerificaMudancaCampo('cobranca_idestado', 'cobranca_idcidade#cobranca_idbairro');
                                               "
                                               />
                                        <span class="nao_selecionou" id="cobranca_idestado_Flag">
                                            &nbsp;&nbsp;&nbsp;
                                        </span>
                                    </td>
                                </tr>
                                <script type="text/javascript">
                                    new CAPXOUS.AutoComplete("cobranca_idestado_Nome", function() {ldelim}
                                    return "estado_ajax.php?ac=busca_estado&typing=" + this.text.value + "&campoID=cobranca_idestado";
                                    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                                </script>


                                <tr>
                                    <td align="right">Cidade:</td>
                                    <td>
                                        <input type="hidden" name="cobranca_idcidade" id="cobranca_idcidade" value="{$info.cobranca_idcidade}" />
                                        <input type="hidden" name="cobranca_idcidade_NomeTemp" id="cobranca_idcidade_NomeTemp" value="{$info.cobranca_idcidade_NomeTemp}" />
                                        <input class="long" type="text" name="cobranca_idcidade_Nome" id="cobranca_idcidade_Nome" value="{$info.cobranca_idcidade_Nome}"
                                               onKeyUp="javascript:
                                                       VerificaMudancaCampo('cobranca_idcidade','cobranca_idbairro');
                                               "
                                               />
                                        <span class="nao_selecionou" id="cobranca_idcidade_Flag">
                                            &nbsp;&nbsp;&nbsp;
                                        </span>
                                    </td>
                                </tr>
                                <script type="text/javascript">
                                    new CAPXOUS.AutoComplete("cobranca_idcidade_Nome", function() {ldelim}
                                    return "cidade_ajax.php?ac=busca_cidade&typing=" + this.text.value + "&campoID=cobranca_idcidade" + "&idestado=" + document.getElementById('cobranca_idestado').value;
                                    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                                </script>


                                <tr>
                                    <td align="right">Bairro:</td>
                                    <td>
                                        <input type="hidden" name="cobranca_idbairro" id="cobranca_idbairro" value="{$info.cobranca_idbairro}" />
                                        <input type="hidden" name="cobranca_idbairro_NomeTemp" id="cobranca_idbairro_NomeTemp" value="{$info.cobranca_idbairro_NomeTemp}" />
                                        <input class="long" type="text" name="cobranca_idbairro_Nome" id="cobranca_idbairro_Nome" value="{$info.cobranca_idbairro_Nome}"
                                               onKeyUp="javascript:
                                                       VerificaMudancaCampo('cobranca_idbairro');
                                               "
                                               />
                                        <span class="nao_selecionou" id="cobranca_idbairro_Flag">
                                            &nbsp;&nbsp;&nbsp;
                                        </span>
                                    </td>
                                </tr>
                                <script type="text/javascript">
                                    new CAPXOUS.AutoComplete("cobranca_idbairro_Nome", function() {ldelim}
                                    return "bairro_ajax.php?ac=busca_bairro&typing=" + this.text.value + "&campoID=cobranca_idbairro" + "&idcidade=" + document.getElementById('cobranca_idcidade').value;
                                    {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                                </script>


                                <script type="text/javascript">
                                    // verifica os campos auto-complete
                                    VerificaMudancaCampo('cobranca_idestado', 'cobranca_idcidade#cobranca_idbairro');
                                    VerificaMudancaCampo('cobranca_idcidade','cobranca_idbairro');
                                    VerificaMudancaCampo('cobranca_idbairro');
                                </script>

                                <tr>
                                    <td align="right">CEP:</td>
                                    <td>
                                        <input class="short" type="text" name="cobranca_cep" id="cobranca_cep" value="{$info.cobranca_cep}" maxlength='10' onkeydown="mask('cobranca_cep', 'cep')" onkeyup="mask('cobranca_cep', 'cep')" />
                                    </td>
                                </tr>


                                <tr>
                                    <td align="right">Telefone da cobrança:</td>
                                    <td>
                                        <input class="tiny" type="text" name="telefone_cobranca_ddd" id="telefone_cobranca_ddd" value="{$info.telefone_cobranca_ddd}" maxlength='2' />
                                        <input class="short" type="text" name="littelefone_cobranca" id="littelefone_cobranca" value="{$info.littelefone_cobranca}" maxlength='9'onkeydown="mask('littelefone_cobranca', 'tel')" onkeyup="mask('littelefone_cobranca', 'tel')" />
                                    </td>
                                </tr>


                            </table>

                        </div>

                    </div>

                </div>


                {************************************}
                {* TAB 3 *}
                {************************************}

                <div id="tab_3" class="anchor">

                    <table width="95%" align="center">


                        <tr>
                            <td width="30%" align="right">Observação:</td>
                            <td>
                                <textarea name="litobservacao_cliente" id="litobservacao_cliente" rows='6' cols='38'>{$info.litobservacao_cliente}</textarea>
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Valor do Contrato (R$):</td>
                            <td>
                                <input class="short" type="text" name="numvalor_contrato_cliente" id="numvalor_contrato_cliente" value="{$info.numvalor_contrato_cliente}" maxlength='10' onkeydown="FormataValor('numvalor_contrato_cliente')" onkeyup="FormataValor('numvalor_contrato_cliente')" />
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Data de cadastro:</td>
                            <td>
                                <input class="short" type="text" name="litdata_cadastro_cliente" id="litdata_cadastro_cliente" value="{$info.litdata_cadastro_cliente}" maxlength='10' onkeydown="mask('litdata_cadastro_cliente', 'data')" onkeyup="mask('litdata_cadastro_cliente', 'data')" /> (dd/mm/aaaa)
                            </td>
                        </tr>

                        <tr>
                            <td align="right">O cliente é um consumidor final ?</td>
                            <td>
                                <input {if $info.litconsumidor_final=="0"}checked{/if} class="radio" type="radio" name="litconsumidor_final" id="litconsumidor_final" value="0" />Não
                                <input {if $info.litconsumidor_final=="1"}checked{/if} class="radio" type="radio" name="litconsumidor_final" id="litconsumidor_final" value="1" />Sim
                            </td>
                        </tr>


                    </table>

                </div>

                {************************************}
                {* TAB 4 *}
                {************************************}

                <div id="tab_4" class="anchor" style="min-height:300px">

                    <table width="95%" align="center">


                        <tr>
                            <td align="right">Multa (%):</td>
                            <td>
                                <input class="short" type="text" name="nummulta_boleto" id="nummulta_boleto" value="{$info.multa_boleto}" maxlength='10' onkeydown="FormataValor('nummulta_boleto')" onkeyup="FormataValor('nummulta_boleto')" />
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Juros diários (%):</td>
                            <td>
                                <input class="short" type="text" name="numjuros_boleto" id="numjuros_boleto" value="{$info.juros_boleto}" maxlength='10' onkeydown="FormataValor('numjuros_boleto')" onkeyup="FormataValor('numjuros_boleto')" />
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Desconto para pagamento antes do vencimento (R$):</td>
                            <td>
                                <input class="short" type="text" name="numdesconto_boleto" id="numdesconto_boleto" value="{$info.desconto_boleto}" maxlength='10' onkeydown="FormataValor('numdesconto_boleto')" onkeyup="FormataValor('numdesconto_boleto')" />
                            </td>
                        </tr>

                        <tr>
                            <td width="30%" align="right">Instruções:</td>
                            <td>
                                <textarea name="litinstrucoes_boleto" id="litinstrucoes_boleto" rows='6' cols='70'>{$info.instrucoes_boleto}</textarea>
                            </td>
                        </tr>

                    </table>

                </div>

                <script language="javascript">
                    Processa_Tabs(0, 'tab_'); // seta o tab inicial
                    Processa_Tabs(0, 'tab_2_'); // seta o tab inicial
                </script>

                <table width="95%" align="center">

                    <tr><td>&nbsp;</td></tr>

                    <tr>
                        <td align="center" colspan="2">

                        </td>
                    </tr>

                </table>

                <center>
                    <input type='button' class="botao_padrao" value="ALTERAR" name = "ALTERAR" id = "ALTERAR" style="clear:left;"
                           onClick="xajax_Verifica_Campos_ClienteCondominio_AJAX(xajax.getFormValues('for_cliente'));"
                           />
                    <input name="Submit" type="submit" class="botao_padrao" value="EXCLUIR" onClick="return(confDelete('for_cliente','{$smarty.server.PHP_SELF}?ac=excluir&idcliente={$info.idcliente}','ATENÇÃO! Confirma a exclusão ?'))" >
                </center>

            </form>


        </div>


    {elseif $flags.action == "adicionar"}

        <br>


        <div style="width: 100%;">

            <form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_cliente" id = "for_cliente">
                <input type="hidden" name="for_chk" id="for_chk" value="1" />

                <ul class="anchors">
                    <li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados Gerais do Cliente</a></li>
                    <li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Dados do Condomínio</a></li>
                    <li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Endereços</a></li>
                    <li><a id="a_tab_3" onclick="Processa_Tabs(3, 'tab_')" href="javascript:;">Outras Informações</a></li>
                    <li><a id="a_tab_4" onclick="Processa_Tabs(4, 'tab_')" href="javascript:;">Configurações de Boleto</a></li>
                </ul>

                {************************************}
                {* TAB 0 *}
                {************************************}

                <div id="tab_0" class="anchor" style="height:300px;" >

                    <table width="95%" align="left">

                        <tr>
                            <td width="30%" class="req" align="right">Nome do cliente:</td>
                            <td><input class="long" type="text" name="nome_cliente" id="nome_cliente" maxlength="100" value="{$smarty.post.nome_cliente}"/></td>
                        </tr>

                        <tr>
                            <td align="right">CNPJ:</td>
                            <td><input class="long" type="text" name="cnpj" id="cnpj" maxlength="20" value="{$smarty.post.cnpj}" onkeydown="mask('cnpj', 'cnpj')" onkeyup="mask('cnpj', 'cnpj')"  /></td>
                        </tr>

                        <tr>
                            <td align="right">Ramo de atividade:</td>
                            <td>
                                <select name="idramo_atividade" id="idramo_atividade">
                                    <option value="">[selecione]</option>
                                    {html_options values=$list_ramo_atividade.idramo_atividade output=$list_ramo_atividade.descricao_atividade selected=12}
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Telefone:</td>
                            <td>
                                <input class="tiny" type="text" name="telefone_cliente_ddd" id="telefone_cliente_ddd" value="{$smarty.post.telefone_cliente_ddd}" maxlength='2' />
                                <input class="short" type="text" name="telefone_cliente" id="telefone_cliente" value="{$smarty.post.telefone_cliente}" maxlength='9'onkeydown="mask('telefone_cliente', 'tel')" onkeyup="mask('telefone_cliente', 'tel')" />
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Fax:</td>
                            <td>
                                <input class="tiny" type="text" name="fax_cliente_ddd" id="fax_cliente_ddd" value="{$smarty.post.fax_cliente_ddd}" maxlength='2' />
                                <input class="short" type="text" name="fax_cliente" id="fax_cliente" value="{$smarty.post.fax_cliente}" maxlength='9'onkeydown="mask('fax_cliente', 'tel')" onkeyup="mask('fax_cliente', 'tel')" />
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Email:</td>
                            <td><input class="long" type="text" name="email_cliente" id="email_cliente" maxlength="100" value="{$smarty.post.email_cliente}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Site:</td>
                            <td><input class="long" type="text" name="site_cliente" id="site_cliente" maxlength="100" value="{$smarty.post.site_cliente}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Cliente bloqueado ?</td>
                            <td>
                                <input {if $smarty.post.cliente_bloqueado=="0"}checked{/if} class="radio" type="radio" name="cliente_bloqueado" id="cliente_bloqueado" value="0" />Não
                                <input {if $smarty.post.cliente_bloqueado=="1"}checked{/if} class="radio" type="radio" name="cliente_bloqueado" id="cliente_bloqueado" value="1" />Sim
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Motivo do bloqueio:</td>
                            <td>
                                <select name="idmotivo_bloqueio" id="idmotivo_bloqueio">
                                    <option value="">[selecione]</option>
                                    {html_options values=$list_motivo_bloqueio.idmotivo_bloqueio output=$list_motivo_bloqueio.motivo_bloqueio selected=$smarty.post.idmotivo_bloqueio}
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Data do bloqueio:</td>
                            <td>
                                <input class="short" type="text" name="data_bloqueio_cliente" id="data_bloqueio_cliente" value="{$smarty.post.data_bloqueio_cliente}" maxlength='10' onkeydown="mask('data_bloqueio_cliente', 'data')" onkeyup="mask('data_bloqueio_cliente', 'data')" /> (dd/mm/aaaa)
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Tipo de Cliente:</td>
                            <td>
                                <select name="tipo_cliente" id="tipo_cliente">
                                    <option value="A">Cliente Avulso</option>
                                    <option value="F" {if $smarty.post.tipo_cliente == "F"}selected{/if} >Contrato Fixo</option>
                                </select>
                            </td>
                        </tr>


                    </table>

                </div>


                {************************************}
                {* TAB 1 *}
                {************************************}

                <div id="tab_1" class="anchor" style="height:300px";>
                
					<br><br>
					
                    <table width="100%" align="center">

                    	<tr>
                        	<td>
                            	<table width="100%" align="center">
                            	
						            <tr>
						            	<td align="right" width="30%">Condom&iacute;nio associado ao caixa propriet&aacute;rio:</td>
						            	
						                <td align="left">
						                    
						                    <input type="hidden" name="condominio_caixa" id="condominio_caixa" value="{$smarty.post.condominio_caixa}" />
						                    <input type="hidden" name="condominio_caixa_NomeTemp" id="condominio_caixa_NomeTemp" value="{$smarty.post.condominio_caixa_NomeTemp}" />
						                    <input class="ultralarge" type="text" name="condominio_caixa_Nome" id="condominio_caixa_Nome" value="{$smarty.post.condominio_caixa_Nome}"
						                           onKeyUp="javascript:
						                                   VerificaMudancaCampo('condominio_caixa');
						                           "
						                           />
						                    <span class="nao_selecionou" id="condominio_caixa_Flag">
						                        &nbsp;&nbsp;&nbsp;
						                    </span>
						                </td>
						            </tr>
						            <script type="text/javascript">
						                new CAPXOUS.AutoComplete("condominio_caixa_Nome", function() {ldelim}
						                return "cliente_ajax.php?ac=busca_cliente_condominio&typing=" + this.text.value + "&campoID=condominio_caixa" + "&mostraDetalhes=1";
						                {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
						            </script>
						
						
						            <script type="text/javascript">
						                // verifica os campos auto-complete
						                VerificaMudancaCampo('idcliente');
						            </script>
                            	
	                                <tr>
	                                    <td align="right" width="30%">Administração Financeira?</td>
	                                    <td align="left" >
	                                        <input {if $smarty.post.admFinanceira=="S"}checked{/if} class="radio" type="radio" name="admFinanceira" id="admFinanceira" value="S" />Sim
	                                        <input {if $smarty.post.admFinanceira=="N"}checked{/if} class="radio" type="radio" name="admFinanceira" id="admFinanceira" value="N" />Não
	                                    </td>
	                                </tr>
	
	
	                                <tr>
	                                    <td align="right" width="30%">Valor do Condomínio:</td>
	                                    <td align="left">
	                                        <input class="short" type="text" name="taxa_condominio" id="taxa_condominio" value="{$smarty.post.taxa_condominio}" maxlength='10' onkeydown="FormataValor('taxa_condominio')" onkeyup="FormataValor('taxa_condominio')" />
	                                    </td>
	                                </tr>
	
	                                <tr>
	                                    <td align="right" width="30%">Dia de Vencimento do Condomínio:</td>
	                                    <td align="left">
	                                        <input class="tiny" maxlength="2" type="text" name="sugestaoVencimento" id="sugestaoVencimento" value="{$smarty.post.sugestaoVencimento}" maxlength='10' onkeydown="FormataInteiro('sugestaoVencimento');" onkeyup="FormataInteiro('sugestaoVencimento');"  />
	                                    </td>
	                                </tr>
	
	                                <tr>
	                                    <td align="right" width="30%">Fundo de Reserva:</td>
	                                    <td align="left">
	                                        <input class="short" type="text" name="sugestaoReserva" id="sugestaoReserva" value="{$smarty.post.sugestaoReserva}" maxlength='10' onkeydown="FormataValor('sugestaoReserva')" onkeyup="FormataValor('sugestaoReserva')" />
	                                    </td>
	                                </tr>
	
	                                <tr>
	                                    <td align="right" width="30%">Taxa de Administração:</td>
	                                    <td align="left">
	                                        <input class="short" type="text" name="valAdm" id="valAdm" value="{$smarty.post.valAdm}" maxlength='10' onkeydown="FormataValor('valAdm')" onkeyup="FormataValor('valAdm')" />
	                                    </td>
	                                </tr>
	
	                                <tr>
	                                    <td align="right" width="30%">Preço Faxina:</td>
	                                    <td align="left">
	                                        <input class="short" type="text" name="valFaxina" id="valFaxina" value="{$smarty.post.valFaxina}" maxlength='10' onkeydown="FormataValor('valFaxina')" onkeyup="FormataValor('valFaxina')" />
	                                    </td>
	                                </tr>
	
	                                <tr>
	                                    <td align="right" width="30%">Preço Vigia:</td>
	                                    <td align="left">
	                                        <input class="short" type="text" name="valVigia" id="valVigia" value="{$smarty.post.valVigia}" maxlength='10' onkeydown="FormataValor('valVigia')" onkeyup="FormataValor('valVigia')" />
	                                    </td>
	                                </tr>
	
	                                <tr>
	                                    <td align="right" width="30%">Emissão Própria ?</td>
	                                    <td>
	                                        <input {if $smarty.post.emissaoPropria!="S"}checked{/if} class="radio" type="radio" name="emissaoPropria" id="emissaoPropria" value="N" onclick="document.getElementById('dados_banco').style.display='none';" />Não
	                                        <input {if $smarty.post.emissaoPropria=="S"}checked{/if} class="radio" type="radio" name="emissaoPropria" id="emissaoPropria" value="S" onclick="document.getElementById('dados_banco').style.display='block';" />Sim
	                                    </td>
	                                </tr>

	                            </table>

    	                    </td>
        	            </tr>

            	        <tr>
                	        <td>
                    	        <table id="dados_banco" style="{if $smarty.post.emissaoPropria != "S"}display:none;{/if}" align="left">
                        	        <tr>
	                                    <td align="right">Banco:</td>
	                                    <td>
	                                        <select name="idbanco" id="idbanco">
	                                            <option value="">[selecione]</option>
	                                            {html_options values=$list_banco.idbanco output=$list_banco.nome_banco selected=$smarty.post.idbanco}
	                                        </select>
	                                    </td>
	                                </tr>

	                                <tr>
	                                    <td align="right">Agencia:</td>
	                                    <td>
	                                        <input class="short" type="text" name="agencia" id="agencia" value="{$smarty.post.agencia}" maxlength='10' onkeydown="FormataInteiro('agencia')" onkeyup="FormataInteiro('agencia')" />
	                                    </td>
	                                </tr>
	
	                                <tr>
	                                    <td align="right">Dígito Agência:</td>
	                                    <td>
	                                        <input class="tiny" type="text" name="agenciaDigito" id="agenciaDigito" value="{$smarty.post.agenciaDigito}" maxlength='10' onkeydown="FormataInteiro('agenciaDigito')" onkeyup="FormataInteiro('agenciaDigito')" />
	                                    </td>
	                                </tr>
	
	                                <tr>
	                                    <td align="right">Conta:</td>
	                                    <td>
	                                        <input class="short" type="text" name="conta" id="conta" value="{$smarty.post.conta}" maxlength='10' onkeydown="FormataInteiro('conta')" onkeyup="FormataInteiro('conta')" />
	                                    </td>
	                                </tr>
	
	                                <tr>
	                                    <td align="right">Dígito Conta:</td>
	                                    <td>
	                                        <input class="tiny" type="text" name="contaDigito" id="contaDigito" value="{$smarty.post.contaDigito}" maxlength='10' onkeydown="FormataInteiro('contaDigito')" onkeyup="FormataInteiro('contaDigito')" />
	                                    </td>
	                                </tr>

	                                <tr>
	                                    <td align="right">Número do Contrato:</td>
	                                    <td>
	                                        <input class="short" type="text" name="numeroContrato" id="numeroContrato" value="{$smarty.post.numeroContrato}" maxlength='10' onkeydown="FormataInteiro('numeroContrato')" onkeyup="FormataInteiro('numeroContrato')" />
	                                    </td>
	                                </tr>
	                            </table>

	                        </td>
    	                </tr>
                    </table>

                </div>


                {************************************}
                {* TAB 2 *}
                {************************************}

                <div id="tab_2" class="anchor" style="height:300px;">

                     <div style="width: 100%;">

                     <ul class="anchors">
                        <li><a id="a_tab_2_0" onclick="Processa_Tabs(0, 'tab_2_')" href="javascript:;">Endereço do Cliente</a></li>
                        <li><a id="a_tab_2_1" onclick="Processa_Tabs(1, 'tab_2_')" href="javascript:;">Endereço de Cobrança</a></li>
                    </ul>


                    {************************************}
                    {* TAB 2.0 *}
                    {************************************}

                    <div id="tab_2_0" class="anchor">

                        <table width="95%" align="center">

                            <tr>
                                <td width="30%" align="right">Logradouro:</td>
                                <td><input class="long" type="text" name="cliente_logradouro" id="cliente_logradouro" maxlength="100" value="{$smarty.post.cliente_logradouro}"/></td>
                            </tr>

                            <tr>
                                <td align="right">Nº:</td>
                                <td><input class="short" type="text" name="cliente_numero" id="cliente_numero" maxlength="10" value="{$smarty.post.cliente_numero}"/></td>
                            </tr>

                            <tr>
                                <td align="right">Complemento:</td>
                                <td><input class="medium" type="text" name="cliente_complemento" id="cliente_complemento" maxlength="50" value="{$smarty.post.cliente_complemento}"/></td>
                            </tr>

                            <tr>
                                <td align="right">Estado:</td>
                                <td>
                                    <input type="hidden" name="cliente_idestado" id="cliente_idestado" value="{$smarty.post.cliente_idestado}" />
                                    <input type="hidden" name="cliente_idestado_NomeTemp" id="cliente_idestado_NomeTemp" value="{$smarty.post.cliente_idestado_NomeTemp}" />
                                    <input class="long" type="text" name="cliente_idestado_Nome" id="cliente_idestado_Nome" value="{$smarty.post.cliente_idestado_Nome}"
                                           onKeyUp="javascript:
                                                   VerificaMudancaCampo('cliente_idestado', 'cliente_idcidade#cliente_idbairro');
                                           "
                                           />
                                    <span class="nao_selecionou" id="cliente_idestado_Flag">
                                        &nbsp;&nbsp;&nbsp;
                                    </span>
                                </td>
                            </tr>
                            <script type="text/javascript">
                                new CAPXOUS.AutoComplete("cliente_idestado_Nome", function() {ldelim}
                                return "estado_ajax.php?ac=busca_estado&typing=" + this.text.value + "&campoID=cliente_idestado";
                                {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                            </script>


                            <tr>
                                <td align="right">Cidade:</td>
                                <td>
                                    <input type="hidden" name="cliente_idcidade" id="cliente_idcidade" value="{$smarty.post.cliente_idcidade}" />
                                    <input type="hidden" name="cliente_idcidade_NomeTemp" id="cliente_idcidade_NomeTemp" value="{$smarty.post.cliente_idcidade_NomeTemp}" />
                                    <input class="long" type="text" name="cliente_idcidade_Nome" id="cliente_idcidade_Nome" value="{$smarty.post.cliente_idcidade_Nome}"
                                           onKeyUp="javascript:
                                                   VerificaMudancaCampo('cliente_idcidade','cliente_idbairro');
                                           "
                                           />
                                    <span class="nao_selecionou" id="cliente_idcidade_Flag">
                                        &nbsp;&nbsp;&nbsp;
                                    </span>
                                </td>
                            </tr>
                            <script type="text/javascript">
                                new CAPXOUS.AutoComplete("cliente_idcidade_Nome", function() {ldelim}
                                return "cidade_ajax.php?ac=busca_cidade&typing=" + this.text.value + "&campoID=cliente_idcidade" + "&idestado=" + document.getElementById('cliente_idestado').value;
                                {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                            </script>


                            <tr>
                                <td align="right">Bairro:</td>
                                <td>
                                    <input type="hidden" name="cliente_idbairro" id="cliente_idbairro" value="{$smarty.post.cliente_idbairro}" />
                                    <input type="hidden" name="cliente_idbairro_NomeTemp" id="cliente_idbairro_NomeTemp" value="{$smarty.post.cliente_idbairro_NomeTemp}" />
                                    <input class="long" type="text" name="cliente_idbairro_Nome" id="cliente_idbairro_Nome" value="{$smarty.post.cliente_idbairro_Nome}"
                                           onKeyUp="javascript:
                                                   VerificaMudancaCampo('cliente_idbairro');
                                           "
                                           />
                                    <span class="nao_selecionou" id="cliente_idbairro_Flag">
                                        &nbsp;&nbsp;&nbsp;
                                    </span>
                                </td>
                            </tr>
                            <script type="text/javascript">
                                new CAPXOUS.AutoComplete("cliente_idbairro_Nome", function() {ldelim}
                                return "bairro_ajax.php?ac=busca_bairro&typing=" + this.text.value + "&campoID=cliente_idbairro" + "&idcidade=" + document.getElementById('cliente_idcidade').value;
                                {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                            </script>


                            <script type="text/javascript">
                                // verifica os campos auto-complete
                                VerificaMudancaCampo('cliente_idestado', 'cliente_idcidade#cliente_idbairro');
                                VerificaMudancaCampo('cliente_idcidade','cliente_idbairro');
                                VerificaMudancaCampo('cliente_idbairro');
                            </script>


                            <tr>
                                <td align="right">CEP:</td>
                                <td>
                                    <input class="short" type="text" name="cliente_cep" id="cliente_cep" value="{$smarty.post.cliente_cep}" maxlength='10' onkeydown="mask('cliente_cep', 'cep')" onkeyup="mask('cliente_cep', 'cep')" />
                                </td>
                            </tr>


                        </table>

                    </div>


                    {************************************}
                    {* TAB 2.1 *}
                    {************************************}

                    <div id="tab_2_1" class="anchor">

                        <table width="95%" align="center">

                            <tr>
                                <td align="center" colspan="2">
                                    O endereço do cliente é o mesmo endereço da cobrança ?
                                    <input {if $smarty.post.mesmo_endereco=="0"}checked{/if} class="radio" type="radio" name="mesmo_endereco" id="mesmo_endereco" value="0" />Não
                                    <input {if $smarty.post.mesmo_endereco=="1"}checked{/if} class="radio" type="radio" name="mesmo_endereco" id="mesmo_endereco" value="1" />Sim
                                </td>
                            </tr>

                            <tr>
                                <td align="center" colspan="2">
                                    Se não for o mesmo, preencha os campos abaixo.
                                </td>
                            </tr>

                            <tr>
                                <td align="right">Logradouro:</td>
                                <td><input class="long" type="text" name="cobranca_logradouro" id="cobranca_logradouro" maxlength="100" value="{$smarty.post.cobranca_logradouro}"/></td>
                            </tr>

                            <tr>
                                <td align="right">Nº:</td>
                                <td><input class="short" type="text" name="cobranca_numero" id="cobranca_numero" maxlength="10" value="{$smarty.post.cobranca_numero}"/></td>
                            </tr>

                            <tr>
                                <td align="right">Complemento:</td>
                                <td><input class="medium" type="text" name="cobranca_complemento" id="cobranca_complemento" maxlength="50" value="{$smarty.post.cobranca_complemento}"/></td>
                            </tr>

                            <tr>
                                <td align="right">Estado:</td>
                                <td>
                                    <input type="hidden" name="cobranca_idestado" id="cobranca_idestado" value="{$smarty.post.cobranca_idestado}" />
                                    <input type="hidden" name="cobranca_idestado_NomeTemp" id="cobranca_idestado_NomeTemp" value="{$smarty.post.cobranca_idestado_NomeTemp}" />
                                    <input class="long" type="text" name="cobranca_idestado_Nome" id="cobranca_idestado_Nome" value="{$smarty.post.cobranca_idestado_Nome}"
                                           onKeyUp="javascript:
                                                   VerificaMudancaCampo('cobranca_idestado', 'cobranca_idcidade#cobranca_idbairro');
                                           "
                                           />
                                    <span class="nao_selecionou" id="cobranca_idestado_Flag">
                                        &nbsp;&nbsp;&nbsp;
                                    </span>
                                </td>
                            </tr>
                            <script type="text/javascript">
                                new CAPXOUS.AutoComplete("cobranca_idestado_Nome", function() {ldelim}
                                return "estado_ajax.php?ac=busca_estado&typing=" + this.text.value + "&campoID=cobranca_idestado";
                                {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                            </script>


                            <tr>
                                <td align="right">Cidade:</td>
                                <td>
                                    <input type="hidden" name="cobranca_idcidade" id="cobranca_idcidade" value="{$smarty.post.cobranca_idcidade}" />
                                    <input type="hidden" name="cobranca_idcidade_NomeTemp" id="cobranca_idcidade_NomeTemp" value="{$smarty.post.cobranca_idcidade_NomeTemp}" />
                                    <input class="long" type="text" name="cobranca_idcidade_Nome" id="cobranca_idcidade_Nome" value="{$smarty.post.cobranca_idcidade_Nome}"
                                           onKeyUp="javascript:
                                                   VerificaMudancaCampo('cobranca_idcidade','cobranca_idbairro');
                                           "
                                           />
                                    <span class="nao_selecionou" id="cobranca_idcidade_Flag">
                                        &nbsp;&nbsp;&nbsp;
                                    </span>
                                </td>
                            </tr>
                            <script type="text/javascript">
                                new CAPXOUS.AutoComplete("cobranca_idcidade_Nome", function() {ldelim}
                                return "cidade_ajax.php?ac=busca_cidade&typing=" + this.text.value + "&campoID=cobranca_idcidade" + "&idestado=" + document.getElementById('cobranca_idestado').value;
                                {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                            </script>


                            <tr>
                                <td align="right">Bairro:</td>
                                <td>
                                    <input type="hidden" name="cobranca_idbairro" id="cobranca_idbairro" value="{$smarty.post.cobranca_idbairro}" />
                                    <input type="hidden" name="cobranca_idbairro_NomeTemp" id="cobranca_idbairro_NomeTemp" value="{$smarty.post.cobranca_idbairro_NomeTemp}" />
                                    <input class="long" type="text" name="cobranca_idbairro_Nome" id="cobranca_idbairro_Nome" value="{$smarty.post.cobranca_idbairro_Nome}"
                                           onKeyUp="javascript:
                                                   VerificaMudancaCampo('cobranca_idbairro');
                                           "
                                           />
                                    <span class="nao_selecionou" id="cobranca_idbairro_Flag">
                                        &nbsp;&nbsp;&nbsp;
                                    </span>
                                </td>
                            </tr>
                            <script type="text/javascript">
                                new CAPXOUS.AutoComplete("cobranca_idbairro_Nome", function() {ldelim}
                                return "bairro_ajax.php?ac=busca_bairro&typing=" + this.text.value + "&campoID=cobranca_idbairro" + "&idcidade=" + document.getElementById('cobranca_idcidade').value;
                                {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                            </script>


                            <script type="text/javascript">
                                // verifica os campos auto-complete
                                VerificaMudancaCampo('cobranca_idestado', 'cobranca_idcidade#cobranca_idbairro');
                                VerificaMudancaCampo('cobranca_idcidade','cobranca_idbairro');
                                VerificaMudancaCampo('cobranca_idbairro');
                            </script>


                            <tr>
                                <td align="right">CEP:</td>
                                <td>
                                    <input class="short" type="text" name="cobranca_cep" id="cobranca_cep" value="{$smarty.post.cobranca_cep}" maxlength='10' onkeydown="mask('cobranca_cep', 'cep')" onkeyup="mask('cobranca_cep', 'cep')" />
                                </td>
                            </tr>

                            <tr>
                                <td align="right">Telefone da cobrança:</td>
                                <td>
                                    <input class="tiny" type="text" name="telefone_cobranca_ddd" id="telefone_cobranca_ddd" value="{$smarty.post.telefone_cobranca_ddd}" maxlength='2' />
                                    <input class="short" type="text" name="telefone_cobranca" id="telefone_cobranca" value="{$smarty.post.telefone_cobranca}" maxlength='9'onkeydown="mask('telefone_cobranca', 'tel')" onkeyup="mask('telefone_cobranca', 'tel')" />
                                </td>
                            </tr>


                        </table>

                    </div>

                </div>

                {************************************}
                {* TAB 3 *}
                {************************************}

                <div id="tab_3" class="anchor" >

                    <table width="95%" align="center">


                        <tr>
                            <td width="30%" align="right">Observação:</td>
                            <td>
                                <textarea name="observacao_cliente" id="observacao_cliente" rows='6' cols='38'>{$smarty.post.observacao_cliente}</textarea>
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Valor do Contrato (R$):</td>
                            <td>
                                <input class="short" type="text" name="valor_contrato_cliente" id="valor_contrato_cliente" value="{$smarty.post.valor_contrato_cliente}" maxlength='10' onkeydown="FormataValor('valor_contrato_cliente')" onkeyup="FormataValor('valor_contrato_cliente')" />
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Data de cadastro:</td>
                            <td>
                                <input class="short" type="text" name="data_cadastro_cliente" id="data_cadastro_cliente" value="{$smarty.post.data_cadastro_cliente}" maxlength='10' onkeydown="mask('data_cadastro_cliente', 'data')" onkeyup="mask('data_cadastro_cliente', 'data')" /> (dd/mm/aaaa)
                            </td>
                        </tr>

                        <tr>
                            <td align="right">O cliente é um consumidor final ?</td>
                            <td>
                                <input {if $smarty.post.consumidor_final=="0"}checked{/if} class="radio" type="radio" name="consumidor_final" id="consumidor_final" value="0" />Não
                                <input {if $smarty.post.consumidor_final=="1"}checked{/if} class="radio" type="radio" name="consumidor_final" id="consumidor_final" value="1" />Sim
                            </td>
                        </tr>


                    </table>

                </div>


                {************************************}
                {* TAB 4 *}
                {************************************}

                <div id="tab_4" class="anchor" style="min-height:300px">

                    <table width="95%" align="center">


                        <tr>
                            <td align="right">Multa (%):</td>
                            <td>
                                <input class="short" type="text" name="multa_boleto" id="multa_boleto" value="{$parametros.multa_boleto}" maxlength='10' onkeydown="FormataValor('multa_boleto')" onkeyup="FormataValor('multa_boleto')" />
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Juros diários (%):</td>
                            <td>
                                <input class="short" type="text" name="juros_boleto" id="juros_boleto" value="{$parametros.juros_boleto}" maxlength='10' onkeydown="FormataValor('juros_boleto')" onkeyup="FormataValor('juros_boleto')" />
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Desconto para pagamento antes do vencimento (R$):</td>
                            <td>
                                <input class="short" type="text" name="desconto_boleto" id="desconto_boleto" value="{$parametros.desconto_boleto}" maxlength='10' onkeydown="FormataValor('desconto_boleto')" onkeyup="FormataValor('desconto_boleto')" />
                            </td>
                        </tr>

                        <tr>
                            <td width="30%" align="right">Instruções:</td>
                            <td>
                                <textarea name="instrucoes_boleto" id="instrucoes_boleto" rows='6' cols='38'>{$parametros.instrucoes_boleto}</textarea>
                            </td>
                        </tr>

                    </table>

                </div>


        </div>



        <script language="javascript">
            Processa_Tabs(0, 'tab_'); // seta o tab inicial
            Processa_Tabs(0, 'tab_2_'); // seta o tab inicial
        </script>

        <table width="95%" align="center">

            <tr><td>&nbsp;</td></tr>

            <tr>
                <td align="center" colspan="2">

                </td>
            </tr>

        </table>


        <br><br>
        <center>
            <input type='button' class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar" style="clear:both; float:center;"
                   onClick="xajax_Verifica_Campos_ClienteCondominio_AJAX(xajax.getFormValues('for_cliente'));"
                   />
        </center>

    </form>

</div>



{elseif $flags.action == "busca_generica"}

    <br>

    <form  action="{$smarty.server.PHP_SELF}?ac=busca_generica" method="post" name = "for" id = "for">
        <table width="100%">

            <input type="hidden" name="for_chk" id="for_chk" value="1" />

            <tr>
                <td align="right">Busca:</td>
                <td>
                    <input class="long" type="text" name="busca" id="busca" maxlength="50" value="{$flags.busca}"/>
                </td>
            </tr>
            <tr>
                <td align="right">Resultados por página:</td>
                <td>
                    <input class="tiny" type="text" name="rpp" id="rpp" maxlength="50" value="{$flags.rpp}" onkeydown="FormataInteiro('rpp')" onkeyup="FormataInteiro('rpp')" />
                    &nbsp;&nbsp;
                    <input name="Submit" type="submit" class="botao_padrao" value="Buscar">
                </td>
            </tr>

            <tr><td>&nbsp;</td></tr>


        </table>
    </form>

    {if count($list)}

        <p align="center">Listando {$conf.area} de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p>

        <table width="95%" align="center">


            <tr>
                <th align='center'>No</th>
                <th align='center'>Nome </th>
                <th align='center'>CNPJ </th>
                <th align='center'>Email </th>
                <th align='center'>Telefone</th>
                <th align='center'>Nome do contato</th>
                <th align='center'>Celular do contato</th>
            </tr>

            {section name=i loop=$list}
                <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
                    <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].index}</a></td>
                    <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].nome_cliente}</a></td>
                    <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].cnpj_cliente}</a></td>
                    <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].email_cliente}</a></td>
                    <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].telefone_cliente}</a></td>
                    <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].nome_contato}</a></td>
                    <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].celular_contato}</a></td>
                </tr>

                <tr>
                    <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
                </tr>
            {/section}

        </table>

        <p align="center" id="nav">{$nav}</p>

        <table width="95%" align="center">
            <form action="{$smarty.server.PHP_SELF}?ac=busca_generica&target=full" method="post" name = "for" id = "for" target="_blank">
                <input type="hidden" name="for_chk" id="for_chk" value="1" />
                <input type="hidden" name="busca" id="busca" value="{$flags.busca}"/>

                <tr>
                    <td align="center">
                        <input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão">
                    </td>
                </tr>

                <tr><td>&nbsp;</td></tr>

            </form>
        </table>


    {else}
        {if $flags.fez_busca == 1}
            {include file="div_resultado_nenhum.tpl"}
        {/if}
    {/if}
{elseif $flags.action == "busca_parametrizada"}

    <br>

    <form  action="{$smarty.server.PHP_SELF}?ac=busca_parametrizada" method="post" name = "for" id = "for">
        <input type="hidden" name="for_chk" id="for_chk" value="1" />	

        <table width="100%">


            <tr>
                <td align="right">Cliente:</td>
                <td><input class="long" type="text" name="cliente" id="cliente" maxlength="50" value="{$flags.cliente}"/></td>
            </tr>

            <tr>
                <td align="right">Ramo de Atividade:</td>
                <td><input class="long" type="text" name="ramo_atividade" id="ramo_atividade" maxlength="50" value="{$flags.ramo_atividade}"/></td>
            </tr>

            <tr>
                <td align="right">CNPJ:</td>
                <td><input class="long" type="text" name="cnpj_cliente" id="cnpj_cliente" maxlength="14" value="{$flags.cnpj_cliente}" onkeydown="mask('cnpj_cliente', 'cnpj')" onkeyup="mask('cnpj_cliente', 'cnpj')"/></td>
            </tr>

            <tr>
                <td align="right">Email:</td>
                <td><input class="long" type="text" name="email_cliente" id="email_cliente" maxlength="50" value="{$flags.email_cliente}"/></td>
            </tr>

            <tr>
                <td align="right">Tipo de Cliente:</td>
                <td>
                    <select name="tipo_cliente" id="tipo_cliente">
                        <option value="">[selecione]</option>							
                        <option value="A" {if $flags.tipo_cliente == "A"}selected{/if}>Cliente Avulso</option>
                        <option value="F" {if $flags.tipo_cliente == "F"}selected{/if} >Contrato Fixo</option>
                    </select>
                </td>
            </tr>


            <tr>
                <td align="right">Somente administra&ccedil;&atilde;o financeira:</td>
                <td>
                    <input {if $flags.admFinanceira == "1" || !$flags.admFinanceira}checked{/if} class="radio" type="radio" name="admFinanceira" id="admFinanceira" value="1" />Sim
                    <input {if $flags.admFinanceira=="0"}checked{/if} class="radio" type="radio" name="admFinanceira" id="admFinanceira" value="0" />N&atilde;o
                </td>
            </tr>

            <tr>
                <td align="right">Resultados por página:</td>
                <td>
                    <input class="tiny" type="text" name="rpp" id="rpp" maxlength="50" value="{$flags.rpp}" onkeydown="FormataInteiro('rpp')" onkeyup="FormataInteiro('rpp')" />
                    &nbsp;&nbsp;
                    <input name="Submit" type="submit" class="botao_padrao" value="Buscar">
                </td>
            </tr>

            <tr><td>&nbsp;</td></tr>

        </table>

    </form>

    {if count($list)}

        <p align="center">Listando {$conf.area} de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p>

        <form action="{$smarty.server.PHP_SELF}?ac=busca_parametrizada&target=full" method="post" name = "for" id = "for" target="_blank">
            <input type="hidden" name="for_chk" id="for_chk" value="1" />
            <input type="hidden" name="cliente" id="cliente" value="{$flags.cliente}"/>
            <input type="hidden" name="ramo_atividade" id="ramo_atividade" value="{$flags.ramo_atividade}"/>
            <input type="hidden" name="cnpj_cliente" id="cnpj_cliente" value="{$flags.cnpj_cliente}"/>
            <input type="hidden" name="email_cliente" id="email_cliente" value="{$flags.email_cliente}"/>
            <input type="hidden" name="tipo_cliente" id="tipo_cliente" value="{$flags.tipo_cliente}"/>
            <input type="hidden" name="admFinanceira" id="admFinanceira" value="{$flags.admFinanceira}"/>

            <table width="95%" align="center">			
                <tr>
                    <td align="center">
                        Tela de Impressão: 
                        <select name="tipo_impressao" id="tipo_impressao" >
                            <option value="D">Detalhada</option>
                            <option value="S">Simples</option>
                        </select>
                        <input name="Submit" type="submit" class="botao_padrao" value="OK">
                    </td>
                </tr>
                <tr><td>&nbsp;</td></tr>
            </table>

            <br />



            <!--{*  Chama o script para ordenar a tabela  *}-->
            <script language="javascript">
                {literal}
    	
    				//Método para não dar conflito com o auto-complete do xajax
    				var $j = jQuery.noConflict();
    			
    				$j(document).ready(function()
    					{ 
    						$j("#tbl_condominio").tablesorter(); 
    				    } 
    				);
    				
                {/literal}
            </script>



            <table width="95%" align="center" class="tablesorter" id="tbl_condominio">
                <thead>
                    <tr>
                        <th class="header" align='center'>No</th>
                        <th class="header" align='center'>Nome do cliente</th>
                        <th class="header" align='center'>CNPJ do cliente</th>
                        <th class="header" align='center'>Email do cliente</th>
                        <th class="header" align='center'>Telefone</th>
                        <th class="header" align='center'>Nome do contato</th>
                        <th class="header" align='center'>Celular do contato</th>
                    </tr>
                </thead>

                <tbody>
                    {section name=i loop=$list}
                        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" >
                            <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].index}</a></td>
                            <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].nome_cliente}</a></td>
                            <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].cnpj}</a></td>
                            <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].email_cliente}</a></td>
                            <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].telefone_cliente}</a></td>
                            <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].nome_contato}</a></td>
                            <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idcliente={$list[i].idcliente}">{$list[i].celular_contato}</a></td>
                        </tr>			    
                    {/section}
                </tbody>
            </table>

            <p align="center" id="nav">{$nav}</p>



        </form>



    {else}
        {if $flags.fez_busca == 1}
            {include file="div_resultado_nenhum.tpl"}
        {/if}
    {/if}



{/if}

{/if}

{include file="com_rodape.tpl"}
