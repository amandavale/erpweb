{include file="div_instrucoes_inicio.tpl"}
<li>Esta senha dar� acesso ao cliente para impress�o de segunda via do boleto e relat�rios do condom�nio;</li>
<li>O login de acesso ser� CPF/CNPJ do cliente;</li>
<li>A senha dever� ter no m�nimo {$parametros.tamanhoMinSenha} caracteres.</li>
{include file="div_instrucoes_fim.tpl"}

<table align="center">

    {if $flags.action != 'adicionar'}
        <tr>
            <td  align="right">Login:</td>
            <td>
                {if $info.pessoa_fisica}


                    {if $info.cpf_cliente != ""}
                        <b>{$info.cpf_cliente}</b>
                    {else}
                        Necess�rio cadastrar o CPF do cliente
                    {/if}


                {elseif $info.pessoa_juridica}


                    {if $info.cnpj_cliente != ""}
                        <b>{$info.cnpj_cliente}</b>
                    {else}
                        Necess�rio cadastrar o CNPJ do cliente
                    {/if}


                {/if}
        </td>
        </tr>
    {/if}

    <tr>
        <td align="right">Insira a <b>nova</b> Senha:</td>
        <td><input class="medium" type="password" name="senha_cliente" id="senha_cliente" maxlength="32" value="{$smarty.post.senha_cliente}" AUTOCOMPLETE="OFF" /></td>
    </tr>

    <tr>
        <td align="right">Confirme a nova Senha:</td>
        <td><input class="medium" type="password" name="re_senha_cliente" id="re_senha_cliente" maxlength="32" value="{$smarty.post.re_senha_cliente}" AUTOCOMPLETE="OFF" /></td>
    </tr>


</table>
