{include file="div_instrucoes_inicio.tpl"}
<li>Esta senha dará acesso ao cliente para impressão de segunda via do boleto e relatórios do condomínio;</li>
<li>O login de acesso será CPF/CNPJ do cliente;</li>
<li>A senha deverá ter no mínimo {$parametros.tamanhoMinSenha} caracteres.</li>
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
                        Necessário cadastrar o CPF do cliente
                    {/if}


                {elseif $info.pessoa_juridica}


                    {if $info.cnpj_cliente != ""}
                        <b>{$info.cnpj_cliente}</b>
                    {else}
                        Necessário cadastrar o CNPJ do cliente
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
