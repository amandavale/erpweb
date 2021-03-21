{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}

<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/tabs.js"></script>


{if $flags.action == "listar" || $flags.action == "busca_parametrizada" }
    <!--{*  Chama o script para ordenar a tabela  *}-->
    <script language="javascript">
        {* literal}
        
        //Método para não dar conflito com o auto-complete do xajax
        var $j = jQuery.noConflict();

        $j(document).ready(function()
        { 
        $j("#tbl_funcionario").tablesorter(); 
        } 
        );
        
        {/literal *}
    </script>
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
            <td class="descricao_tela" WIDTH="10%">
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
                    &nbsp;&nbsp;&nbsp;&bull; <a class="link_geral" href="{$smarty.server.PHP_SELF}?ac=busca_parametrizada">busca</a>
                {/if}
            </td>
        </tr>
    </table>
    <br /> 
    <br />



    {include file="div_erro.tpl"}

    {if $flags.action == "listar"}

        {if $flags.sucesso != ""}
            {include file="div_resultado_inicio.tpl"}
            {$flags.sucesso}
            {include file="div_resultado_fim.tpl"}
        {/if}

        {if count($list)}

            <p align="center">Listando {$conf.area} de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p>

            <table width="95%" align="center" id="tbl_funcionario" class="tablesorter">

                <thead>
                    <tr>
                        <th class="header" width="150"  align='center'>Nome 		 </th>
                        <th class="header" width="200"  align='center'>Endereço 	 </th>
                        <th class="header" width="90"   align='center'>CPF 		 	 </th>
                        <th class="header" width="100"  align='center'>RG 		 	 </th>
                        <th class="header" width="100"  align='center'>CTPS 		 </th>
                        <th class="header" width="80"   align='center'>Agência /Conta</th>
                        <th class="header" width="90"   align='center'>Telefone 	 </th>
                        <th class="header" width="90"   align='center'>Data Admissão </th>
                    </tr>
                </thead>

                <tbody>
                    {section name=i loop=$list}
                        <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" style="height:30px;" valign="center" >
                            <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].nome_funcionario}</a></td>
                            <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].endereco}</a></td>
                            <td align="center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].cpf_funcionario}</a></td>
                            <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].identidade_funcionario}</a></td>
                            <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].carteira_trabalho_funcionario}</a></td>
                            <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].agencia_funcionario} / {$list[i].conta_funcionario}</a></td>
                            <td align="center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].telefone_funcionario}</a></td>
                            <td align="center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].data_admissao_funcionario}</a></td>
                        </tr>	    
                    {/section}
                </tbody>
            </table>


            <p align="center" id="nav">{$nav}</p>

        {else}
            {include file="div_resultado_nenhum.tpl"}
        {/if}


    {elseif $flags.action == "editar"}

        <br>

        <div style="width: 100%;">

            <form action="{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$info.idfuncionario}" method="post" name = "for_funcionario" id = "for_funcionario">
                <input type="hidden" name="for_chk" id="for_chk" value="1" />
                <input type="hidden" name="idendereco_funcionario" id="idendereco_funcionario" value="{$info.idendereco_funcionario}" />

                <ul class="anchors">
                    <li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados do Funcion&aacute;rio</a></li>
                    <li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Endereço do Funcion&aacute;rio</a></li>
                    <li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Dados Adicionais</a></li>
                    <li><a id="a_tab_3" onclick="Processa_Tabs(3, 'tab_')" href="javascript:;">Dados de Acesso</a></li>
                    <li><a id="a_tab_4" onclick="Processa_Tabs(4, 'tab_')" href="javascript:;">Filiais em que o Funcion&aacute;rio trabalha</a></li>
                    <li><a id="a_tab_5" onclick="Processa_Tabs(5, 'tab_')" href="javascript:;">Clientes</a></li>
                    <li><a id="a_tab_6" onclick="Processa_Tabs(6, 'tab_')" href="javascript:;">Contas do Funcion&aacute;rio</a></li>
                </ul>

                {************************************}
                {* TAB 0 *}
                {************************************}

                <div id="tab_0" class="anchor">


                    <table width="95%" align="center">

                        <tr>
                            <td align="right">Filiais:</td>
                            <td>
                                <select style="width: 300px;" name="infoFiliais[]" id="infoFiliais" multiple="multiple">							
                                    {section name=i loop=$infoFiliais.idfilial}
                                        <option value="{$infoFiliais.idfilial[i]}" {if $infoFiliais.selected[i]}selected{/if} >{$infoFiliais.nome_filial[i]}</option>
                                    {/section}
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td class="req" align="right">Cargo:</td>
                            <td>
                                <select name="numidcargo" id="numidcargo">
                                    <option value="">[selecione]</option>
                                    {html_options values=$list_cargo.idcargo output=$list_cargo.nome_cargo selected=$info.numidcargo}
                                </select>
                            </td>
                        </tr>


                        <tr>
                            <td width="40%" class="req" align="right">Nome do funcion&aacute;rio:</td>
                            <td><input class="long" type="text" name="litnome_funcionario" id="litnome_funcionario" maxlength="100" value="{$info.litnome_funcionario}"/></td>
                        </tr>

                        <tr>
                            <td class="req" align="right">Sexo do funcion&aacute;rio:</td>
                            <td>
                                <input {if $info.litsexo_funcionario=="M"}checked{/if} class="radio" type="radio" name="litsexo_funcionario" id="litsexo_funcionario" value="M" />Masculino
                                <input {if $info.litsexo_funcionario=="F"}checked{/if} class="radio" type="radio" name="litsexo_funcionario" id="litsexo_funcionario" value="F" />Feminino
                            </td>
                        </tr>
						
						<tr>
                            <td class="req" align="right">CPF:</td>
                            <td><input class="long" type="text" name="litcpf_funcionario" id="litcpf_funcionario" maxlength="14" value="{$info.litcpf_funcionario}" onkeydown="mask('litcpf_funcionario', 'cpf')" onkeyup="mask('litcpf_funcionario', 'cpf')" /></td>
                        </tr>
						
						<tr><td>&nbsp;</td></tr>

                        <tr>
                            <td class="req" align="right">Nº da identidade:</td>
                            <td><input class="long" type="text" name="litidentidade_funcionario" id="litidentidade_funcionario" maxlength="20" value="{$info.litidentidade_funcionario}"/></td>
                        </tr>


                        <tr>
                            <td align="right">Data de Expedição:</td>
                            <td>
                              <input class="short" type="text" name="litrg_data_expedicao_funcionario" id="litrg_data_expedicao_funcionario" value="{$info.rg_data_expedicao_funcionario}" maxlength='10' onkeydown="mask('litrg_data_expedicao_funcionario', 'data')" onkeyup="mask('litrg_data_expedicao_funcionario', 'data')" />
                            	<span style="padding-left:35px">Órgão Emissor:</span>
                            	<input class="tiny" type="text" name="litrg_orgao_emissor_funcionario" id="litrg_orgao_emissor_funcionario" maxlength="5" value="{$info.rg_orgao_emissor_funcionario}"/>
                            </td>
                        </tr>
                        

                        <tr><td>&nbsp;</td></tr>

                        <tr>
                            <td align="right">Carteira de trabalho:</td>
                            <td><input class="long" type="text" name="litcarteira_trabalho_funcionario" id="litcarteira_trabalho_funcionario" maxlength="20" value="{$info.litcarteira_trabalho_funcionario}"/></td>
                        </tr>

                        <td align="right">Série:</td>
                        <td>
                            <input class="medium" type="text" name="litctps_serie" id="litctps_serie" maxlength="10" value="{$info.ctps_serie}"/>
                            UF: <input class="tiny" type="text" name="litctps_uf" id="litctps_uf" maxlength="2" value="{$info.ctps_uf}"/>
                        </td>
                        </tr>

                        <tr><td>&nbsp;</td></tr>


                        <tr>
                            <td width="40%" align="right">Naturalidade:</td>
                            <td><input class="long" type="text" name="litnaturalidade" id="litnaturalidade" maxlength="50" value="{$info.naturalidade}"/></td>
                        </tr>

                        <tr>
                            <td width="40%" align="right">Nacionalidade:</td>
                            <td><input class="medium" type="text" name="litnacionalidade" id="litnacionalidade" maxlength="50" value="{$info.nacionalidade}"/></td>
                        </tr>

                        <tr>
                            <td class="req" align="right">Data de nascimento:</td>
                            <td>
                                <input class="short" type="text" name="litdata_nascimento_funcionario" id="litdata_nascimento_funcionario" value="{$info.litdata_nascimento_funcionario}" maxlength='10' onkeydown="mask('litdata_nascimento_funcionario', 'data')" onkeyup="mask('litdata_nascimento_funcionario', 'data')" /> (dd/mm/aaaa)
                            </td>
                        </tr>

                        <tr>
                            <td class="req" align="right">Data de admissão:</td>
                            <td>
                                <input class="short" type="text" name="litdata_admissao_funcionario" id="litdata_admissao_funcionario" value="{$info.litdata_admissao_funcionario}" maxlength='10' onkeydown="mask('litdata_admissao_funcionario', 'data')" onkeyup="mask('litdata_admissao_funcionario', 'data')" /> (dd/mm/aaaa)
                            </td>
                        </tr>

                        <tr>
                            <td class="" align="right">Data de demissão:</td>
                            <td>
                                <input class="short" type="text" name="litdata_demissao_funcionario" id="litdata_demissao_funcionario" value="{$info.data_demissao_funcionario}" maxlength='10' onkeydown="mask('litdata_demissao_funcionario', 'data')" onkeyup="mask('litdata_demissao_funcionario', 'data')" /> (dd/mm/aaaa)
                            </td>
                        </tr>

                        <tr><td>&nbsp;</td></tr>

                        <tr>
                            <td class="" align="right">Data de afastamento:</td>
                            <td>
                                <input class="short" type="text" name="litdata_afastamento" id="litdata_afastamento" value="{$info.data_afastamento}" maxlength='10' onkeydown="mask('litdata_afastamento', 'data')" onkeyup="mask('litdata_afastamento', 'data')" /> (dd/mm/aaaa)
                            </td>
                        </tr>

                        <tr>
                            <td width="40%" align="right">Motivo do afastamento:</td>
                            <td><input class="long" type="text" name="litmotivo_afastamento" id="litmotivo_afastamento" maxlength="50" value="{$info.motivo_afastamento}"/></td>
                        </tr>

                    </table>
                </div>

                {************************************}
                {* TAB 1 *}
                {************************************}

                <div id="tab_1" class="anchor">

                    <table width="95%" align="center">

                        <tr>
                            <td width="40%" align="right">Logradouro:</td>
                            <td><input class="long" type="text" name="funcionario_logradouro" id="funcionario_logradouro" maxlength="100" value="{$info.funcionario_logradouro}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Nº:</td>
                            <td><input class="short" type="text" name="funcionario_numero" id="funcionario_numero" maxlength="10" value="{$info.funcionario_numero}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Complemento:</td>
                            <td><input class="medium" type="text" name="funcionario_complemento" id="funcionario_complemento" maxlength="50" value="{$info.funcionario_complemento}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Estado:</td>
                            <td>
                                <input type="hidden" name="funcionario_idestado" id="funcionario_idestado" value="{$info.funcionario_idestado}" />
                                <input type="hidden" name="funcionario_idestado_NomeTemp" id="funcionario_idestado_NomeTemp" value="{$info.funcionario_idestado_NomeTemp}" />
                                <input class="long" type="text" name="funcionario_idestado_Nome" id="funcionario_idestado_Nome" value="{$info.funcionario_idestado_Nome}"
                                       onKeyUp="javascript:
                                               VerificaMudancaCampo('funcionario_idestado', 'funcionario_idcidade#funcionario_idbairro');
                                       "
                                       />
                                <span class="nao_selecionou" id="funcionario_idestado_Flag">
                                    &nbsp;&nbsp;&nbsp;
                                </span>
                            </td>
                        </tr>
                        <script type="text/javascript">
                            new CAPXOUS.AutoComplete("funcionario_idestado_Nome", function() {ldelim}
                            return "estado_ajax.php?ac=busca_estado&typing=" + this.text.value + "&campoID=funcionario_idestado";
                            {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                        </script>


                        <tr>
                            <td align="right">Cidade:</td>
                            <td>
                                <input type="hidden" name="funcionario_idcidade" id="funcionario_idcidade" value="{$info.funcionario_idcidade}" />
                                <input type="hidden" name="funcionario_idcidade_NomeTemp" id="funcionario_idcidade_NomeTemp" value="{$info.funcionario_idcidade_NomeTemp}" />
                                <input class="long" type="text" name="funcionario_idcidade_Nome" id="funcionario_idcidade_Nome" value="{$info.funcionario_idcidade_Nome}"
                                       onKeyUp="javascript:
                                               VerificaMudancaCampo('funcionario_idcidade','funcionario_idbairro');
                                       "
                                       />
                                <span class="nao_selecionou" id="funcionario_idcidade_Flag">
                                    &nbsp;&nbsp;&nbsp;
                                </span>
                            </td>
                        </tr>
                        <script type="text/javascript">
                            new CAPXOUS.AutoComplete("funcionario_idcidade_Nome", function() {ldelim}
                            return "cidade_ajax.php?ac=busca_cidade&typing=" + this.text.value + "&campoID=funcionario_idcidade" + "&idestado=" + document.getElementById('funcionario_idestado').value;
                            {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                        </script>


                        <tr>
                            <td align="right">Bairro:</td>
                            <td>
                                <input type="hidden" name="funcionario_idbairro" id="funcionario_idbairro" value="{$info.funcionario_idbairro}" />
                                <input type="hidden" name="funcionario_idbairro_NomeTemp" id="funcionario_idbairro_NomeTemp" value="{$info.funcionario_idbairro_NomeTemp}" />
                                <input class="long" type="text" name="funcionario_idbairro_Nome" id="funcionario_idbairro_Nome" value="{$info.funcionario_idbairro_Nome}"
                                       onKeyUp="javascript:
                                               VerificaMudancaCampo('funcionario_idbairro');
                                       "
                                       />
                                <span class="nao_selecionou" id="funcionario_idbairro_Flag">
                                    &nbsp;&nbsp;&nbsp;
                                </span>
                            </td>
                        </tr>
                        <script type="text/javascript">
                            new CAPXOUS.AutoComplete("funcionario_idbairro_Nome", function() {ldelim}
                            return "bairro_ajax.php?ac=busca_bairro&typing=" + this.text.value + "&campoID=funcionario_idbairro" + "&idcidade=" + document.getElementById('funcionario_idcidade').value;
                            {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                        </script>


                        <script type="text/javascript">
                            // verifica os campos auto-complete
                            VerificaMudancaCampo('funcionario_idestado', 'funcionario_idcidade#funcionario_idbairro');
                            VerificaMudancaCampo('funcionario_idcidade','funcionario_idbairro');
                            VerificaMudancaCampo('funcionario_idbairro');
                        </script>

                        <tr>
                            <td align="right">CEP:</td>
                            <td>
                                <input class="short" type="text" name="funcionario_cep" id="funcionario_cep" value="{$info.funcionario_cep}" maxlength='10' onkeydown="mask('funcionario_cep', 'cep')" onkeyup="mask('funcionario_cep', 'cep')" />
                            </td>
                        </tr>

                    </table>
                </div>

                {************************************}
                {* TAB 2 *}
                {************************************}

                <div id="tab_2" class="anchor">

                    <table width="95%" align="center">


                        <tr>
                            <td width="40%" align="right">Telefone:</td>
                            <td>
                                <input class="tiny" type="text" name="telefone_funcionario_ddd" id="telefone_funcionario_ddd" value="{$info.telefone_funcionario_ddd}" maxlength='2' />
                                <input class="short" type="text" name="littelefone_funcionario" id="littelefone_funcionario" value="{$info.littelefone_funcionario}" maxlength='9'onkeydown="mask('littelefone_funcionario', 'tel')" onkeyup="mask('littelefone_funcionario', 'tel')" />
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Celular:</td>
                            <td>
                                <input class="tiny" type="text" name="celular_funcionario_ddd" id="celular_funcionario_ddd" value="{$info.celular_funcionario_ddd}" maxlength='2' />
                                <input class="short" type="text" name="litcelular_funcionario" id="litcelular_funcionario" value="{$info.litcelular_funcionario}" maxlength='9'onkeydown="mask('litcelular_funcionario', 'tel')" onkeyup="mask('litcelular_funcionario', 'tel')" />
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Email:</td>
                            <td><input class="long" type="text" name="litemail_funcionario" id="litemail_funcionario" maxlength="100" value="{$info.litemail_funcionario}"/></td>
                        </tr>


                        <tr><td>&nbsp;</td></tr>

                        <tr>
                            <td align="right">Cor:</td>
                            <td><input class="medium" type="text" name="litcor_funcionario" id="litcor_funcionario" maxlength="100" value="{$info.cor_funcionario}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Cabelo:</td>
                            <td><input class="medium" type="text" name="litcabelo_funcionario" id="litcabelo_funcionario" maxlength="100" value="{$info.cabelo_funcionario}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Olhos:</td>
                            <td><input class="short" type="text" name="litolhos_funcionario" id="litolhos_funcionario" maxlength="100" value="{$info.olhos_funcionario}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Altura:</td>
                            <td><input class="tiny" type="text" name="litaltura_funcionario" id="litaltura_funcionario" maxlength="100" value="{$info.altura_funcionario}" onkeydown="FormataValor('litaltura_funcionario')" onkeyup="FormataValor('litaltura_funcionario')" />(m)</td>
                        </tr>

                        <tr>
                            <td align="right">Peso:</td>
                            <td><input class="tiny" type="text" name="litpeso_funcionario" id="litpeso_funcionario" maxlength="100" value="{$info.peso_funcionario}" onkeydown="FormataValor('litpeso_funcionario')" onkeyup="FormataValor('litpeso_funcionario')" />(kg)</td>
                        </tr>

                        <tr>
                            <td align="right">Sinais:</td>
                            <td><input class="long" type="text" name="litsinais_funcionario" id="litsinais_funcionario" maxlength="100" value="{$info.sinais_funcionario}" /></td>
                        </tr>


						<tr>
                            <td align="right">Numeração da Blusa:</td>
                            <td><input class="tiny" type="text" name="litnumeracao_blusa_funcionario" id="litnumeracao_blusa_funcionario" maxlength="3" value="{$info.numeracao_blusa_funcionario}"/></td>
                        </tr>
                        
                        <tr>
                            <td align="right">Numeração de Calça:</td>
                            <td><input class="tiny" type="text" name="litnumeracao_calca_funcionario" id="litnumeracao_calca_funcionario" maxlength="3" value="{$info.numeracao_calca_funcionario}"/></td>
                        </tr>
                        
                        <tr>
                            <td align="right">Numeração de Calçado:</td>
                            <td><input class="tiny" type="text" name="litnumeracao_calcado_funcionario" id="litnumeracao_calcado_funcionario" maxlength="3" value="{$info.numeracao_calcado_funcionario}"/></td>
                        </tr>
 
                        <tr><td>&nbsp;</td></tr>

                        <tr>
                            <td align="right">Título de Eleitor:</td>
                            <td><input class="long" type="text" name="littitulo_eleitor" id="littitulo_eleitor" maxlength="100" value="{$info.titulo_eleitor}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Nº do PIS/PASEP:</td>
                            <td><input class="long" type="text" name="litpis_pasep" id="litpis_pasep" maxlength="100" value="{$info.pis_pasep}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Carteira de Habilitação:</td>
                            <td><input class="long" type="text" name="lithabilitacao" id="lithabilitacao" maxlength="100" value="{$info.habilitacao}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Nome do Pai:</td>
                            <td><input class="long" type="text" name="litnome_pai" id="litnome_pai" maxlength="200" value="{$info.nome_pai}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Nome da Mãe:</td>
                            <td><input class="long" type="text" name="litnome_mae" id="litnome_mae" maxlength="200" value="{$info.nome_mae}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Estado Civil:</td>
                            <td><input class="long" type="text" name="litestado_civil" id="litestado_civil" maxlength="100" value="{$info.estado_civil}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Nome do conjuge:</td>
                            <td><input class="long" type="text" name="litnome_conjuge_funcionario" id="litnome_conjuge_funcionario" maxlength="100" value="{$info.litnome_conjuge_funcionario}"/></td>
                        </tr>

                        <tr><td>&nbsp;</td></tr>

                        <tr>
                            <td align="right">Escolaridade:</td>
                            <td><input class="long" type="text" name="litescolaridade_funcionario" id="litescolaridade_funcionario" maxlength="100" value="{$info.escolaridade_funcionario}"/></td>
                        </tr>

                        <tr>
                            <td align="right">N&ordm; de Filhos:</td>
                            <td><input class="tiny" type="text" name="numqtd_filhos" id="numqtd_filhos" maxlength="100" value="{$info.qtd_filhos}" onkeydown="FormataInteiro('numqtd_filhos')" onkeyup="FormataInteiro('numqtd_filhos')" /></td>
                        </tr>

                        <tr>
                            <td align="right" valign="top">Nome dos Filhos:</td>
                            <td>
                                <textarea name="litfilhos_funcionario" id="litfilhos_funcionario" rows='5' class="long">{$info.filhos_funcionario}</textarea>
                            </td>
                        </tr>

                        <tr><td>&nbsp;</td></tr>

                        <tr>
                            <td align="right">Horário de trabalho:</td>
                            <td><input class="long" type="text" name="lithorario_trabalho" id="lithorario_trabalho" maxlength="100" value="{$info.horario_trabalho}"/></td>
                        </tr>
                        <tr>
                            <td align="right">Intervalo intra-jornada:</td>
                            <td><input class="long" type="text" name="litintervalo_refeicao" id="litintervalo_refeicao" maxlength="100" value="{$info.intervalo_refeicao}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Salário contratual (R$):</td>
                            <td><input class="short" type="text" name="numsalario_funcionario" id="numsalario_funcionario" maxlength="100" value="{$info.salario_funcionario}" onkeydown="FormataValor('numsalario_funcionario')" onkeyup="FormataValor('numsalario_funcionario')" /></td>
                        </tr>

                        <tr>
                            <td align="right">Tipo de salário:</td>
                            <td>
                                <select name="littipo_salario" id="littipo_salario">
                                    <option value="">[selecione]</option>
                                    <option value="M" {if $info.tipo_salario == "M"}selected{/if}>Mensal</option>
                                    <option value="H" {if $info.tipo_salario == "H"}selected{/if}>Por Hora</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Dias de contrato de experiência:</td>
                            <td><input class="tiny" type="text" name="litdias_contrato_exp" id="litdias_contrato_exp" maxlength="100" value="{$info.dias_contrato_exp}"  onkeydown="FormataInteiro('litdias_contrato_exp')" onkeyup="FormataInteiro('litdias_contrato_exp')" /></td>
                        </tr>

                        <tr>
                            <td align="right">Observação:</td>
                            <td>
                                <textarea name="litobservacao_funcionario" id="litobservacao_funcionario" rows='6' cols='38'>{$info.litobservacao_funcionario}</textarea>
                            </td>
                        </tr>


                    </table>
                </div>

                {************************************}
                {* TAB 3 *}
                {************************************}

                <div id="tab_3" class="anchor">

                    <table width="95%" align="center">


                        <tr>
                            <td width="40%" align="right">Login:</td>
                            <td>
                                {if $info.login_funcionario != ""}
                                    {$info.login_funcionario}
                                    <input class="medium" type="hidden" name="login_funcionario" id="login_funcionario" value="{$info.login_funcionario}"/>
                                {else}
                                    <input class="medium" type="text" name="login_funcionario_vazio" id="login_funcionario_vazio" maxlength="15" value="" AUTOCOMPLETE="OFF"/>

                                {/if}
                            </td>
                        </tr>

                        {if $info.login_funcionario != ""}
                            <tr>
                                <td align="right">Digite a senha <b>atual</b>:</td>
                                <td>
                                    <input class="medium" type="password" name="senha_atual_funcionario" id="senha_atual_funcionario" maxlength="32" value="{$smarty.post.senha_atual_funcionario}" AUTOCOMPLETE="OFF" />
                                </td>
                            </tr>
                        {/if}

                        <tr>
                            <td align="right">Insira a <b>nova</b> Senha:</td>
                            <td><input class="medium" type="password" name="senha_funcionario" id="senha_funcionario" maxlength="32" value="{$smarty.post.senha_funcionario}" AUTOCOMPLETE="OFF" /></td>
                        </tr>

                        <tr>
                            <td align="right">Confirme a nova Senha:</td>
                            <td><input class="medium" type="password" name="re_senha_funcionario" id="re_senha_funcionario" maxlength="32" value="{$smarty.post.re_senha_funcionario}" AUTOCOMPLETE="OFF" /></td>
                        </tr>


                    </table>
                </div>

                {************************************}
                {* TAB 4 *}
                {************************************}

                <div id="tab_4" class="anchor">

                    <table width="95%" align="center">


                        <tr>
                            <td class="row" height="1" bgcolor="#999999" colspan="9"></td>
                        </tr>

                        <tr><td>&nbsp;</td></tr>
                        {if count($list_filial)}
                            {section name=i loop=$list_filial}
                                <tr>
                                    <td width="40%">
                                        <a class="link_geral" href="{$conf.addr}/admin/filial.php?ac=editar&idfilial={$list_filial[i].idfilial}">
                                            {$list_filial[i].nome_filial}</a>
                                    </td>
                                </tr>
                            {/section}
                        {else}
                            <tr>
                                <td>
                                    {include file="div_resultado_nenhum.tpl"}
                                </td>
                            </tr>
                        {/if}


                        <tr><td>&nbsp;</td></tr>

                    </table>

                </div>



                {************************************}
                {* TAB 5 *}
                {************************************}

                <div id="tab_5" class="anchor">
                    {include file="tab/funcionario_cliente.tpl"}
                </div>


                {************************************}
                {* TAB 6 *}
                {************************************}

                <div id="tab_6" class="anchor">
                    {include file="tab/conta_funcionario.tpl"}
                </div>


                <table align="center">
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td align="center" colspan="2">
                            <input type='button' class="botao_padrao" value="ALTERAR" name = "ALTERAR" id = "ALTERAR" onClick="xajax_Verifica_Campos_Funcionario_AJAX(xajax.getFormValues('for_funcionario'));"  />
                        </td>

                        <td align="center" colspan="2">
                            <input type='button' class="botao_padrao" value="VISUALIZAR FICHA" name = "btn_FichaFuncionario" id = "btn_FichaFuncionario" onClick="window.open('{$smarty.server.PHP_SELF}?ac=editar&ficha=1&idfuncionario={$info.idfuncionario}');" />
                        </td>

                    </tr>
                </table>

            </form>
        </div>



        <script language="javascript">
            Processa_Tabs(0, 'tab_'); // seta o tab inicial
        </script>

    {elseif $flags.action == "adicionar"}


        <br>

        <div style="width: 100%;">

            <form  action="{$smarty.server.PHP_SELF}?ac=adicionar" method="post" name = "for_funcionario" id = "for_funcionario">
                <ul class="anchors">
                    <li><a id="a_tab_0" onclick="Processa_Tabs(0, 'tab_')" href="javascript:;">Dados do Funcionário</a></li>
                    <li><a id="a_tab_1" onclick="Processa_Tabs(1, 'tab_')" href="javascript:;">Endereço do Funcionário</a></li>
                    <li><a id="a_tab_2" onclick="Processa_Tabs(2, 'tab_')" href="javascript:;">Dados Adicionais</a></li>
                    <li><a id="a_tab_3" onclick="Processa_Tabs(3, 'tab_')" href="javascript:;">Dados de Acesso</a></li>
                    <li><a id="a_tab_4" onclick="Processa_Tabs(4, 'tab_')" href="javascript:;">Clientes</a></li>

                </ul>

                {************************************}
                {* TAB 0 *}
                {************************************}

                <div id="tab_0" class="anchor">

                    <input type="hidden" name="for_chk" id="for_chk" value="1" />

                    <table width="95%" align="center">

                        <tr>
                            <td align="right">Filiais:</td>
                            <td>
                                <select style="width: 300px;" name="infoFiliais[]" id="infoFiliais" multiple="multiple">
                                    {section name=i loop=$infoFiliais.idfilial}
                                        <option value="{$infoFiliais.idfilial[i]}" {if $infoFiliais.idfilial[i] == $smarty.session.idfilial_usuario}selected{/if} >{$infoFiliais.nome_filial[i]}</option>
                                    {/section}
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td class="req" align="right">Cargo:</td>
                            <td>
                                <select name="idcargo" id="idcargo">
                                    <option value="">[selecione]</option>
                                    {html_options values=$list_cargo.idcargo output=$list_cargo.nome_cargo selected=$smarty.post.idcargo}
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td width="40%" class="req" align="right">Nome do funcionário:</td>
                            <td><input class="long" type="text" name="nome_funcionario" id="nome_funcionario" maxlength="100" value="{$smarty.post.nome_funcionario}"/></td>
                        </tr>

                        <tr>
                            <td class="req" align="right">Sexo do funcionário:</td>
                            <td>
                                <input {if $smarty.post.sexo_funcionario=="M"}checked{/if} class="radio" type="radio" name="sexo_funcionario" id="sexo_funcionario" value="M" />Masculino
                                <input {if $smarty.post.sexo_funcionario=="F"}checked{/if} class="radio" type="radio" name="sexo_funcionario" id="sexo_funcionario" value="F" />Feminino
                            </td>
                        </tr>
                         <tr>
                            <td class="req" align="right">CPF:</td>
                            <td><input class="long" type="text" name="cpf_funcionario" id="cpf_funcionario" maxlength="14" value="{$smarty.post.cpf_funcionario}" onkeydown="mask('cpf_funcionario', 'cpf')" onkeyup="mask('cpf_funcionario', 'cpf')" /></td>
                        </tr>
                        
 						<tr><td>&nbsp;</td>
                        
                        <tr>
                            <td class="req" align="right">Nº da identidade:</td>
                            <td><input class="long" type="text" name="identidade_funcionario" id="identidade_funcionario" maxlength="20" value="{$smarty.post.identidade_funcionario}"/></td>
                        </tr>
                        
                        <tr>
                            <td align="right">Data de Expedição:</td>
                            <td>
                                <input class="short" type="text" name="rg_data_expedicao_funcionario" id="rg_data_expedicao_funcionario" value="{$smarty.post.rg_data_expedicao_funcionario}" maxlength='10' onkeydown="mask('rg_data_expedicao_funcionario', 'data')" onkeyup="mask('rg_data_expedicao_funcionario', 'data')" />
                            	<span style="padding-left:35px">Órgão Emissor:</span>
                            	<input class="tiny" type="text" name="rg_orgao_emissor_funcionario" id="identidade_funcionario" maxlength="5" value=""/>
                            </td>
                        </tr>

                        <tr><td>&nbsp;</td></tr>

                        <tr>
                            <td align="right">Carteira de trabalho:</td>
                            <td><input class="long" type="text" name="carteira_trabalho_funcionario" id="carteira_trabalho_funcionario" maxlength="20" value="{$smarty.post.carteira_trabalho_funcionario}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Série:</td>
                            <td>
                                <input class="medium" type="text" name="ctps_serie" id="ctps_serie" maxlength="10" value="{$smarty.post.ctps_serie}"/>
                                
                                <span style="padding-left:85px">UF:</span> 
                                <input class="tiny" type="text" name="ctps_uf" id="ctps_uf" maxlength="2" value="{$smarty.post.ctps_uf}"/>
                            </td>
                        </tr>
                        
                        <tr><td>&nbsp;</td></tr>

                        <tr>
                            <td width="40%" align="right">Naturalidade:</td>
                            <td><input class="long" type="text" name="naturalidade" id="naturalidade" maxlength="50" value="{$smarty.post.naturalidade}"/></td>
                        </tr>

                        <tr>
                            <td width="40%" align="right">Nacionalidade:</td>
                            <td><input class="medium" type="text" name="nacionalidade" id="nacionalidade" maxlength="50" value="{$smarty.post.nacionalidade}"/></td>
                        </tr>

                        <tr>
                            <td class="req" align="right">Data de nascimento:</td>
                            <td>
                                <input class="short" type="text" name="data_nascimento_funcionario" id="data_nascimento_funcionario" value="{$smarty.post.data_nascimento_funcionario}" maxlength='10' onkeydown="mask('data_nascimento_funcionario', 'data')" onkeyup="mask('data_nascimento_funcionario', 'data')" /> (dd/mm/aaaa)
                            </td>
                        </tr>

                        <tr>
                            <td class="req" align="right">Data de admissão:</td>
                            <td>
                                <input class="short" type="text" name="data_admissao_funcionario" id="data_admissao_funcionario" value="{$smarty.post.data_admissao_funcionario}" maxlength='10' onkeydown="mask('data_admissao_funcionario', 'data')" onkeyup="mask('data_admissao_funcionario', 'data')" /> (dd/mm/aaaa)
                            </td>
                        </tr>

                        <tr>
                            <td class="" align="right">Data de demissão:</td>
                            <td>
                                <input class="short" type="text" name="data_demissao_funcionario" id="data_demissao_funcionario" value="{$smarty.post.data_demissao_funcionario}" maxlength='10' onkeydown="mask('data_demissao_funcionario', 'data')" onkeyup="mask('data_demissao_funcionario', 'data')" /> (dd/mm/aaaa)
                            </td>
                        </tr>

                        <tr><td>&nbsp;</td></tr>

                        <tr>
                            <td class="" align="right">Data de afastamento:</td>
                            <td>
                                <input class="short" type="text" name="data_afastamento" id="data_afastamento" value="{$smarty.post.data_afastamento}" maxlength='10' onkeydown="mask('data_afastamento', 'data')" onkeyup="mask('data_afastamento', 'data')" /> (dd/mm/aaaa)
                            </td>
                        </tr>

                        <tr>
                            <td width="40%" align="right">Motivo do afastamento:</td>
                            <td><input class="long" type="text" name="motivo_afastamento" id="motivo_afastamento" maxlength="50" value="{$smarty.post.motivo_afastamento}"/></td>
                        </tr>
                    </table>
                </div>

                {************************************}
                {* TAB 1 *}
                {************************************}

                <div id="tab_1" class="anchor">

                    <table width="95%" align="center">
                        <tr>
                            <td colspan="9" align="center"></td>
                        </tr>
                        <tr>
                            <td width="40%" align="right">Logradouro:</td>
                            <td><input class="long" type="text" name="funcionario_logradouro" id="funcionario_logradouro" maxlength="100" value="{$smarty.post.funcionario_logradouro}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Nº:</td>
                            <td><input class="short" type="text" name="funcionario_numero" id="funcionario_numero" maxlength="10" value="{$smarty.post.funcionario_numero}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Complemento:</td>
                            <td><input class="medium" type="text" name="funcionario_complemento" id="funcionario_complemento" maxlength="50" value="{$smarty.post.funcionario_complemento}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Estado:</td>
                            <td>
                                <input type="hidden" name="funcionario_id"adicionar"estado" id="funcionario_idestado" value="{$info.funcionario_idestado}" />
                                       <input type="hidden" name="funcionario_idestado_NomeTemp" id="funcionario_idestado_NomeTemp" value="{$info.funcionario_idestado_NomeTemp}" />
                                <input class="long" type="text" name="funcionario_idestado_Nome" id="funcionario_idestado_Nome" value="{$info.funcionario_idestado_Nome}"
                                       onKeyUp="javascript:
                                               VerificaMudancaCampo('funcionario_idestado', 'funcionario_idcidade#funcionario_idbairro');
                                       "
                                       />
                                <span class="nao_selecionou" id="funcionario_idestado_Flag">
                                    &nbsp;&nbsp;&nbsp;
                                </span>
                            </td>
                        </tr>
                        <script type="text/javascript">
                            new CAPXOUS.AutoComplete("funcionario_idestado_Nome", function() {ldelim}
                            return "estado_ajax.php?ac=busca_estado&typing=" + this.text.value + "&campoID=funcionario_idestado";
                            {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                        </script>


                        <tr>
                            <td align="right">Cidade:</td>
                            <td>
                                <input type="hidden" name="funcionario_idcidade" id="funcionario_idcidade" value="{$info.funcionario_idcidade}" />
                                <input type="hidden" name="funcionario_idcidade_NomeTemp" id="funcionario_idcidade_NomeTemp" value="{$info.funcionario_idcidade_NomeTemp}" />
                                <input class="long" type="text" name="funcionario_idcidade_Nome" id="funcionario_idcidade_Nome" value="{$info.funcionario_idcidade_Nome}"
                                       onKeyUp="javascript:
                                               VerificaMudancaCampo('funcionario_idcidade','funcionario_idbairro');
                                       "
                                       />
                                <span class="nao_selecionou" id="funcionario_idcidade_Flag">
                                    &nbsp;&nbsp;&nbsp;
                                </span>
                            </td>
                        </tr>
                        <script type="text/javascript">
                            new CAPXOUS.AutoComplete("funcionario_idcidade_Nome", function() {ldelim}
                            return "cidade_ajax.php?ac=busca_cidade&typing=" + this.text.value + "&campoID=funcionario_idcidade" + "&idestado=" + document.getElementById('funcionario_idestado').value;
                            {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                        </script>


                        <tr>
                            <td align="right">Bairro:</td>
                            <td>
                                <input type="hidden" name="funcionario_idbairro" id="funcionario_idbairro" value="{$info.funcionario_idbairro}" />
                                <input type="hidden" name="funcionario_idbairro_NomeTemp" id="funcionario_idbairro_NomeTemp" value="{$info.funcionario_idbairro_NomeTemp}" />
                                <input class="long" type="text" name="funcionario_idbairro_Nome" id="funcionario_idbairro_Nome" value="{$info.funcionario_idbairro_Nome}"
                                       onKeyUp="javascript:
                                               VerificaMudancaCampo('funcionario_idbairro');
                                       "
                                       />
                                <span class="nao_selecionou" id="funcionario_idbairro_Flag">
                                    &nbsp;&nbsp;&nbsp;
                                </span>
                            </td>
                        </tr>
                        <script type="text/javascript">
                            new CAPXOUS.AutoComplete("funcionario_idbairro_Nome", function() {ldelim}
                            return "bairro_ajax.php?ac=busca_bairro&typing=" + this.text.value + "&campoID=funcionario_idbairro" + "&idcidade=" + document.getElementById('funcionario_idcidade').value;
                            {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
                        </script>


                        <script type="text/javascript">
                            // verifica os campos auto-complete
                            VerificaMudancaCampo('funcionario_idestado', 'funcionario_idcidade#funcionario_idbairro');
                            VerificaMudancaCampo('funcionario_idcidade','funcionario_idbairro');
                            VerificaMudancaCampo('funcionario_idbairro');
                        </script>

                        <tr>
                            <td align="right">CEP:</td>
                            <td>
                                <input class="short" type="text" name="funcionario_cep" id="funcionario_cep" value="{$smarty.post.funcionario_cep}" maxlength='10' onkeydown="mask('funcionario_cep', 'cep')" onkeyup="mask('funcionario_cep', 'cep')" />
                            </td>
                        </tr>
                    </table>
                </div>

                {************************************}
                {* TAB 2 *}
                {************************************}

                <div id="tab_2" class="anchor">

                    <table width="95%" align="center">
                        <tr>

                            <td width="40%" align="right">Telefone:</td>
                            <td>
                                <input class="tiny" type="text" name="telefone_funcionario_ddd" id="telefone_funcionario_ddd" value="{$smarty.post.telefone_funcionario_ddd}" maxlength='2' />
                                <input class="short" type="text" name="telefone_funcionario" id="telefone_funcionario" value="{$smarty.post.telefone_funcionario}" maxlength='9'onkeydown="mask('telefone_funcionario', 'tel')" onkeyup="mask('telefone_funcionario', 'tel')" />
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Celular:</td>
                            <td>
                                <input class="tiny" type="text" name="celular_funcionario_ddd" id="celular_funcionario_ddd" value="{$smarty.post.celular_funcionario_ddd}" maxlength='2' />
                                <input class="short" type="text" name="celular_funcionario" id="celular_funcionario" value="{$smarty.post.celular_funcionario}" maxlength='9'onkeydown="mask('celular_funcionario', 'tel')" onkeyup="mask('celular_funcionario', 'tel')" />
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Email:</td>
                            <td><input class="long" type="text" name="email_funcionario" id="email_funcionario" maxlength="100" value="{$smarty.post.email_funcionario}"/></td>
                        </tr>

                        <tr><td>&nbsp;</td></tr>

                        <tr>
                            <td align="right">Cor:</td>
                            <td><input class="medium" type="text" name="cor_funcionario" id="cor_funcionario" maxlength="100" value="{$smarty.post.cor_funcionario}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Cabelo:</td>
                            <td><input class="medium" type="text" name="cabelo_funcionario" id="cabelo_funcionario" maxlength="100" value="{$smarty.post.cabelo_funcionario}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Olhos:</td>
                            <td><input class="short" type="text" name="olhos_funcionario" id="olhos_funcionario" maxlength="100" value="{$smarty.post.olhos_funcionario}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Altura:</td>
                            <td><input class="tiny" type="text" name="altura_funcionario" id="altura_funcionario" maxlength="100" value="{$smarty.post.altura_funcionario}" onkeydown="FormataValor('altura_funcionario')" onkeyup="FormataValor('altura_funcionario')" />(m)</td>
                        </tr>

                        <tr>
                            <td align="right">Peso:</td>
                            <td><input class="tiny" type="text" name="peso_funcionario" id="peso_funcionario" maxlength="100" value="{$smarty.post.peso_funcionario}" onkeydown="FormataValor('peso_funcionario')" onkeyup="FormataValor('peso_funcionario')" />(kg)</td>
                        </tr>

                        <tr>
                            <td align="right">Sinais:</td>
                            <td><input class="long" type="text" name="sinais_funcionario" id="sinais_funcionario" maxlength="100" value="{$smarty.post.sinais_funcionario}"/></td>
                        </tr>

						<tr>
                            <td align="right">Numeração da Blusa:</td>
                            <td><input class="tiny" type="text" name="numeracao_blusa_funcionario" id="numeracao_blusa_funcionario" maxlength="3" value="{$smarty.post.numeracao_blusa_funcionario}"/></td>
                        </tr>
                        
                        <tr>
                            <td align="right">Numeração de Calça:</td>
                            <td><input class="tiny" type="text" name="numeracao_calca_funcionario" id="numeracao_calca_funcionario" maxlength="3" value="{$smarty.post.numeracao_calca_funcionario}"/></td>
                        </tr>
                        
                        <tr>
                            <td align="right">Numeração de Calçado:</td>
                            <td><input class="tiny" type="text" name="numeracao_calcado_funcionario" id="numeracao_calcado_funcionario" maxlength="3" value="{$smarty.post.numeracao_calcado_funcionario}"/></td>
                        </tr>
 
                        <tr><td>&nbsp;</td></tr>

                        <tr>
                            <td align="right">Título de Eleitor:</td>
                            <td><input class="long" type="text" name="titulo_eleitor" id="titulo_eleitor" maxlength="100" value="{$smarty.post.titulo_eleitor}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Nº do PIS/PASEP:</td>
                            <td><input class="long" type="text" name="pis_pasep" id="pis_pasep" maxlength="100" value="{$smarty.post.pis_pasep}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Carteira de Habilitação:</td>
                            <td><input class="long" type="text" name="habilitacao" id="habilitacao" maxlength="100" value="{$smarty.post.habilitacao}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Nome do Pai:</td>
                            <td><input class="long" type="text" name="nome_pai" id="nome_pai" maxlength="200" value="{$smarty.post.nome_pai}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Nome da Mãe:</td>
                            <td><input class="long" type="text" name="nome_mae" id="nome_mae" maxlength="200" value="{$smarty.post.nome_mae}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Estado Civil:</td>
                            <td><input class="long" type="text" name="estado_civil" id="estado_civil" maxlength="100" value="{$smarty.post.estado_civil}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Nome do conjuge:</td>
                            <td><input class="long" type="text" name="nome_conjuge_funcionario" id="nome_conjuge_funcionario" maxlength="100" value="{$smarty.post.nome_conjuge_funcionario}"/></td>
                        </tr>

                        <tr><td>&nbsp;</td></tr>

                        <tr>
                            <td align="right">Escolaridade:</td>
                            <td><input class="long" type="text" name="escolaridade_funcionario" id="escolaridade_funcionario" maxlength="100" value="{$smarty.post.escolaridade_funcionario}"/></td>
                        </tr>

                        <tr>
                            <td align="right">N&ordm; de Filhos:</td>
                            <td><input class="tiny" type="text" name="qtd_filhos" id="" maxlength="100" value="{$smarty.post.qtd_filhos}" onkeydown="FormataInteiro('qtd_filhos')" onkeyup="FormataInteiro('qtd_filhos')" /></td>
                        </tr>

                        <tr>
                            <td align="right" valign="top">Nome dos Filhos:</td>
                            <td>
                                <textarea name="filhos_funcionario" id="filhos_funcionario" rows='5' class="long">{$smarty.post.observacao_funcionario}</textarea>
                            </td>
                        </tr>

                        <tr><td>&nbsp;</td></tr>

                        <tr>
                            <td align="right">Horário de trabalho:</td>
                            <td><input class="long" type="text" name="horario_trabalho" id="horario_trabalho" maxlength="100" value="{$smarty.post.horario_trabalho}"/></td>
                        </tr>
                        <tr>
                            <td align="right">Intervalo intra-jornada:</td>
                            <td><input class="long" type="text" name="intervalo_refeicao" id="intervalo_refeicao" maxlength="100" value="{$smarty.post.intervalo_refeicao}"/></td>
                        </tr>
                        <tr>
                            <td align="right">Salário contratual (R$):</td>
                            <td><input class="short" type="text" name="salario_funcionario" id="salario_funcionario" maxlength="100" value="{$smarty.post.salario_funcionario}" onkeydown="FormataValor('salario_funcionario')" onkeyup="FormataValor('salario_funcionario')" /></td>
                        </tr>

                        <tr>
                            <td align="right">Tipo de salário:</td>
                            <td>
                                <select name="tipo_salario" id="tipo_salario">
                                    <option value="">[selecione]</option>
                                    <option value="M" {if $smarty.post.tipo_salario == "M"}selected{/if}>Mensal</option>
                                    <option value="H" {if $smarty.post.tipo_salario == "H"}selected{/if}>Por Hora</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td align="right">Dias de contrato de experiência:</td>
                            <td><input class="tiny" type="text" name="dias_contrato_exp" id="dias_contrato_exp" maxlength="100" value="{$smarty.post.dias_contrato_exp}"  onkeydown="FormataInteiro('dias_contrato_exp')" onkeyup="FormataInteiro('dias_contrato_exp')" /></td>
                        </tr>

                        <tr>
                            <td align="right">Observação:</td>
                            <td>
                                <textarea name="observacao_funcionario" id="observacao_funcionario" rows='6' cols='38'>{$smarty.post.observacao_funcionario}</textarea>
                            </td>
                        </tr>

                    </table>
                </div>

                {************************************}
                {* TAB 3 *}
                {************************************}

                <div id="tab_3" class="anchor">

                    <table width="95%" align="center">


                        <tr>
                            <td width="40%" align="right">Login:</td>
                            <td><input class="medium" type="text" name="login_funcionario" id="login_funcionario" maxlength="15" value="{$smarty.post.login_funcionario}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Senha:</td>
                            <td><input class="medium" type="password" name="senha_funcionario" id="senha_funcionario" maxlength="32" value="{$smarty.post.senha_funcionario}"/></td>
                        </tr>

                        <tr>
                            <td align="right">Redigite a Senha:</td>
                            <td><input class="medium" type="password" name="re_senha_funcionario" id="re_senha_funcionario" maxlength="32" value="{$smarty.post.re_senha_funcionario}"/></td>
                        </tr>
                    </table>
                </div>


                {************************************}
                {* TAB 4 *}
                {************************************}

                <div id="tab_4" class="anchor">
                    {include file="tab/funcionario_cliente.tpl"}
                </div>



                <table align="center">
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td colspan="9" align="center">
                            <input type='button' class="botao_padrao" value="ADICIONAR" name = "Adicionar" id = "Adicionar"
                                   onClick="xajax_Verifica_Campos_Funcionario_AJAX(xajax.getFormValues('for_funcionario'));">
                        </td>
                    </tr>
                </table>

            </form>

        </div>


        <script language="javascript">
            Processa_Tabs(0, 'tab_'); // seta o tab inicial
        </script>




    {elseif $flags.action == "busca_generica"}

        <table width="100%">
            <form  action="{$smarty.server.PHP_SELF}?ac=busca_generica" method="post" name = "for" id = "for">
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

            </form>
        </table>

        {if count($list)}

            <p align="center">Listando {$conf.area} de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p>

            <table width="95%" align="center">

                <tr>
                    <th align='center'> Nome 		  	 </th>
                    <th align='center'> Endereço 	  	 </th>
                    <th align='center'> CPF 		  	 </th>
                    <th align='center'> RG 			  	 </th>
                    <th align='center'> CTPS 		  	 </th>
                    <th align='center'> Banco   	  	 </th>
                    <th align='center'> Agência     	 </th>
                    <th align='center'> Conta 		  	 </th>
                    <th align='center'> Telefone 	  	 </th>
                    <th align='center'> Função 		 </th>
                    <th align='center'> Data Admissão </th>
                </tr>


                {section name=i loop=$list}
                    <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" style="height:30px;" valign="center" >
                        <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].nome_funcionario}</a></td>
                        <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].endereco}</a></td>
                        <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].cpf_funcionario}</a></td>
                        <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].identidade_funcionario}</a></td>
                        <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].carteira_trabalho_funcionario}</a></td>
                        <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].nome_banco}</a></td>
                        <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].agencia_funcionario}</a></td>
                        <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].conta_funcionario}</a></td>
                        <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].telefone_funcionario}</a></td>
                        <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].nome_cargo}</a></td>
                        <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].data_admissao_funcionario}</a></td>
                    </tr>

                    <tr>
                        <td class="row" height="1" bgcolor="#999999" colspan="11"></td>
                    </tr>
                {/section}

            </table>


            <p align="center" id="nav">{$nav}</p>

            <form action="{$smarty.server.PHP_SELF}?ac=busca_generica&target=full" method="post" name="for" id="for" target="_blank">
                <table width="95%" align="center">
                    <input type="hidden" name="for_chk" id="for_chk" value="1" /> 
                    <input type="hidden" name="busca" id="busca" value="{$flags.busca}" />

                    <tr>
                        <td align="center"><input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão"></td>
                    </tr>

                    <tr><td>&nbsp;</td></tr>

                </table>
            </form>

        {else} 

            {if $flags.fez_busca == 1} 
                {include file="div_resultado_nenhum.tpl"}
            {/if} 

        {/if} 

    {elseif $flags.action == "busca_parametrizada"}



        {if $flags.sucesso != ""}
            {include file="div_resultado_inicio.tpl"}
            {$flags.sucesso}
            {include file="div_resultado_fim.tpl"}
            <br><br>
        {/if}



        <form  action="{$smarty.server.PHP_SELF}?ac=busca_parametrizada" method="post" name = "for" id = "for">
            <input type="hidden" name="for_chk" id="for_chk" value="1" />

            <table width="100%">

                <tr>
                    <td align="right">Filiais:</td>
                    <td>
                        <select style="width: 300px;" name="infoFiliais[]" id="infoFiliais" multiple="multiple">
                            {if $smarty.post.for_chk}
                                {section name=i loop=$list_filial.idfilial}
                                    <option value="{$list_filial.idfilial[i]}" {if $list_filial.idfilial[i]|in_array:$flags.infoFiliais}selected{/if} >{$list_filial.nome_filial[i]}</option>
                                {/section}
                            {else}
                                {section name=i loop=$list_filial.idfilial}
                                    <option value="{$list_filial.idfilial[i]}" {if $list_filial.idfilial[i] == $smarty.session.idfilial_usuario}selected{/if} >{$list_filial.nome_filial[i]}</option>
                                {/section}
                            {/if}
                        </select>
                    </td>
                </tr>

                <tr>
                    <td align="right">Cargo:</td>
                    <td><input class="long" type="text" name="cargo" id="cargo" maxlength="50" value="{$flags.cargo}"/></td>
                </tr>

                <tr>
                    <td align="right">Funcionário:</td>
                    <td><input class="long" type="text" name="funcionario" id="funcionario" maxlength="50" value="{$flags.funcionario}"/></td>
                </tr>

                <tr>
                    <td align="right">CPF:</td>
                    <td><input class="long" type="text" name="cpf_funcionario" id="cpf_funcionario" maxlength="14" value="{$flags.cpf_funcionario}" onkeydown="mask('cpf_funcionario', 'cpf')" onkeyup="mask('cpf_funcionario', 'cpf')"/></td>
                </tr>

                <tr>
                    <td align="right">Data de Nascimento:</td>
                    <td><input class="short" type="text" name="data_nascimento" id="data_nascimento" value="{$flags.data_nascimento}" maxlength='10' onkeydown="mask('data_nascimento', 'data')" onkeyup="mask('data_nascimento', 'data')" /> (dd/mm/aaaa)</td>
                </tr>

                <tr>
                    <td align="right" valign="bottom" >Data de Admiss&atilde;o de:</td>
                    <td align="left"> <input class="short" type="text" name="data_admissao_de" id="data_admissao_de" value="{$flags.data_admissao_de}" maxlength='10' onkeydown="mask('data_admissao_de', 'data')" onkeyup="mask('data_admissao_de', 'data')" />
                        <img src="{$conf.addr}/common/img/calendar.png" id="img_data_admissao_de" style="cursor: pointer;" />
                        &nbsp;at&eacute;:	
                        <input class="short" type="text" name="data_admissao_ate" id="data_admissao_ate" value="{$flags.data_admissao_ate}" maxlength='10' onkeydown="mask('data_admissao_ate', 'data')" onkeyup="mask('data_admissao_ate', 'data')" />
                        <img src="{$conf.addr}/common/img/calendar.png" id="img_data_admissao_ate" style="cursor: pointer;" /> (dd/mm/aaaa)
                    </td>
                </tr>

                <tr>
                    <td align="right" valign="bottom" >Data de Demiss&atilde;o de:</td>
                    <td align="left"> <input class="short" type="text" name="data_demissao_de" id="data_demissao_de" value="{$flags.data_demissao_de}" maxlength='10' onkeydown="mask('data_demissao_de', 'data')" onkeyup="mask('data_demissao_de', 'data')" />
                        <img src="{$conf.addr}/common/img/calendar.png" id="img_data_demissao_de" style="cursor: pointer;" />
                        &nbsp;at&eacute;:	
                        <input class="short" type="text" name="data_demissao_ate" id="data_demissao_ate" value="{$flags.data_demissao_ate}" maxlength='10' onkeydown="mask('data_demissao_ate', 'data')" onkeyup="mask('data_demissao_ate', 'data')" />
                        <img src="{$conf.addr}/common/img/calendar.png" id="img_data_demissao_ate" style="cursor: pointer;" /> (dd/mm/aaaa)
                    </td>
                </tr>

                <tr>
                    <td align="right">Situa&ccedil;&atilde;o do Funcion&aacute;rio:</td>
                    <td>
                        <select name="situacao_funcionario" id="situacao_funcionario" />
                <option value="ativo"    {if $flags.situacao_funcionario == "ativo"   }selected{/if} >Ativo    </option>
                <option value="inativo"  {if $flags.situacao_funcionario == "inativo" }selected{/if} >Inativo  </option>
                <option value="afastado" {if $flags.situacao_funcionario == "afastado"}selected{/if} >Afastados</option>
                </select>	
                </td>
                </tr>

                <tr>
                    <td align="right">Resultados por p&aacute;gina:</td>
                    <td>
                        <input class="tiny" type="text" name="rpp" id="rpp" maxlength="50" value="{$flags.rpp}" onkeydown="FormataInteiro('rpp')" onkeyup="FormataInteiro('rpp')" />
                        &nbsp;&nbsp;
                        <input name="Submit" type="submit" class="botao_padrao" value="Buscar">
                    </td>
                </tr>

                <tr><td>&nbsp;</td></tr>

            </table>
        </form>

        <script type="text/javascript">
            Calendar.setup(
            {ldelim}
                inputField : "data_admissao_de", // ID of the input field
                ifFormat : "%d/%m/%Y", // the date format
                button : "img_data_admissao_de", // ID of the button
                align  : "cR"  // alinhamento
            {rdelim}
            );
	
                Calendar.setup(
            {ldelim}
                inputField : "data_admissao_ate", // ID of the input field
                ifFormat : "%d/%m/%Y", // the date format
                button : "img_data_admissao_ate", // ID of the button
                align  : "cR"  // alinhamento
            {rdelim}
            );
	
	
			
	
                Calendar.setup(
            {ldelim}
                inputField : "data_demissao_de", // ID of the input field
                ifFormat : "%d/%m/%Y", // the date format
                button : "img_data_demissao_de", // ID of the button
                align  : "cR"  // alinhamento
            {rdelim}
            );
	
                Calendar.setup(
            {ldelim}
                inputField : "data_demissao_ate", // ID of the input field
                ifFormat : "%d/%m/%Y", // the date format
                button : "img_data_demissao_ate", // ID of the button
                align  : "cR"  // alinhamento
            {rdelim}
            );
        </script>


        {if count($list)}


            <form action="{$smarty.server.PHP_SELF}?ac=busca_parametrizada&target=full{$parametros_get}" method="post" name = "for" id = "for" target="_blank">
                <table width="95%" align="center">

                    <input type="hidden" name="for_chk" id="for_chk" value="1" />

                    <input type="hidden" name="funcionario" 			 id="funcionario" 			value="{$flags.funcionario}" />
                    <input type="hidden" name="cargo" 					 id="cargo" 					value="{$flags.cargo}" />
                    <input type="hidden" name="cpf_funcionario" 		 id="cpf_funcionario" 		value="{$flags.cpf_funcionario}" />
                    <input type="hidden" name="data_nascimento" 		 id="data_nascimento" 		value="{$flags.data_nascimento}" />
                    <input type="hidden" name="data_admissao_de" 	 id="data_admissao_de" 		value="{$smarty.post.data_admissao_de}" />
                    <input type="hidden" name="data_admissao_ate" 	 id="data_admissao_ate" 	value="{$smarty.post.data_admissao_ate}" />
                    <input type="hidden" name="data_demissao_de" 	 id="data_demissao_de" 		value="{$smarty.post.data_demissao_de}" />
                    <input type="hidden" name="data_demissao_ate" 	 id="data_demissao_ate" 	value="{$smarty.post.data_demissao_ate}" />
                    <input type="hidden" name="situacao_funcionario" id="situacao_funcionario" value="{$flags.situacao_funcionario}" />
                    <input type="hidden" name="listar_exfuncionario" id="listar_exfuncionario" value="{$flags.listar_exfuncionario}" />		


                    <tr><td>&nbsp;</td></tr>

                    <tr>
                        <td align="center">
                            <input name="Submit" type="submit" class="botao_padrao" value="Tela de Impressão">
                        </td>
                    </tr>

                    <tr><td>&nbsp;</td></tr>

                </table>



                <p align="center">Listando {$conf.area} de <b>{$ind.first}</b> a <b>{$ind.last}</b> de um total de <b>{$ind.total}</b>:</p>

                <table width="95%" align="center" id="tbl_funcionario" class="tablesorter">

                    <thead>
                        <tr>
                            <th class="header" width="150"  align='center'>Nome			   </th>
                            <th class="header" width="200"  align='center'>Endereço		   </th>
                            <th class="header" width="90"   align='center'>CPF			   </th>
                            <th class="header" width="100"  align='center'>RG			   </th>
                            <th class="header" width="100"  align='center'>CTPS			   </th>
                            <th class="header" width="80"   align='center'>Série / UF      </th>
                            <th class="header" width="80"   align='center'>Agência / Conta </th>
                            <th class="header" width="90"   align='center'>Telefone		   </th>
                            <th class="header" width="90"   align='center'>Data Admissão   </th>
                        </tr>
                    </thead>

                    <tbody>
                        {section name=i loop=$list}
                            <tr  bgcolor = "{if $list[i].index % 2 == 0}F7F7F7{else}WHITE{/if}" style="height:30px;" valign="center" >
                                <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].nome_funcionario}</a></td>
                                <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].endereco}</a></td>
                                <td align="center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].cpf_funcionario}</a></td>
                                <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].identidade_funcionario}</a></td>
                                <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].carteira_trabalho_funcionario}</a></td>
                                <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{if $list[i].ctps_serie}{$list[i].ctps_serie}</a> / <a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].ctps_uf}</a>{/if}</td>
                                <td><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{if $list[i].agencia_funcionario != ''}{$list[i].agencia_funcionario}</a> / <a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].conta_funcionario}</a>{/if}</td>						
                                <td align="center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].telefone_funcionario}</a></td>
                                <td align="center"><a class='menu_item' href = "{$smarty.server.PHP_SELF}?ac=editar&idfuncionario={$list[i].idfuncionario}">{$list[i].data_admissao_funcionario}</a></td>
                            </tr>	    
                        {/section}
                    </tbody>
                </table>

                <p align="center" id="nav">{$nav}</p>


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

            {if $flags.fez_busca == 1}
                {include file="div_resultado_nenhum.tpl"}
            {/if}

        {/if}


    {/if}

{/if}

{include file="com_rodape.tpl"}
