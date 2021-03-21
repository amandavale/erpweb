{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}

<script type="text/vbscript" src="{$conf.addr}/common/js/tef.vbs"></script>
<script type="text/vbscript" src="{$conf.addr}/common/js/orcamento.vbs"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/orcamento.js"></script>

<script language="javascript">

	function Modulo_Adm_TEF() {ldelim}

		var tef_caminho = document.for_tef.tef_caminho.value;
		IniciaModuloTEFADM(tef_caminho);
		
	{rdelim}

</script>


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

	<DIV id="popup_mensagem030"></DIV>

  {include file="div_erro.tpl"}

  {if $flags.action == "listar"}


		{include file="div_instrucoes_inicio.tpl"}
			<li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>
			<li>Esta tela permite executar operações administrativas de TEF.</li> 
		{include file="div_instrucoes_fim.tpl"}


		<form  action="" method="post" name="for_tef" id="for_tef">
		<input type="hidden" name="for_chk" id="for_chk" value="1" />
		<input type="hidden" name="identificacao" id="identificacao" value="1" />
		<input type="hidden" name="valorTEF_bk" id="valorTEF_bk" value="" />
		<input type="hidden" name="travar_teclado" id="travar_teclado" value="{$conf.travar_teclado}" />
		<input type="hidden" name="comando" id="comando" value="" />
		<input type="hidden" name="tipoTransacao" id="tipoTransacao" value="" />

			<table width="100%">
				<tr>
					<td align="center" colspan="2" class="req">
						TEF: 
						<select name="tef_caminho" id="tef_caminho">
							<option value=""></option>
							<option value="{$conf.tef_rede_visa_amex}">Redecard / Visa / Amex</option>
							<option value="{$conf.tef_tecban}">TecBan</option>
							<option value="{$conf.tef_hipercard}">Hipercard</option>
						</select>
					</td>
				</tr>
			</table>

			<br>

			<table width="100%">

				<tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">
							<tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">TEF: Clique no botão abaixo para executar uma operação de <b>Administração</b>.</td>
							</tr>

							<tr><td>&nbsp;</td></tr>

							<tr>
								<td align="center" colspan="2">
									<input type="button" class="botao_padrao" value="Operação de Administração!" name="button" onClick="xajax_Verifica_Campos_Comando_TEF_AJAX('ADM', xajax.getFormValues('for_tef'))" />
								</td>
							</tr>

						</table>
					</td>
				</tr>

			</table>

			<br>

			<table width="100%">

				<tr>
					<td colspan="2" align="center">
						<table class="tb4cantos" width="100%">
							<tr bgcolor="#F7F7F7">
								<td colspan="9" align="center">TEF: Clique no botão abaixo para executar uma operação de <b>Consulta de Cheque</b>.</td>
							</tr>

							<tr>
								<td width="50%" class="req" align="right">Valor (R$):</td>
								<td>
									<input class="short" type="text" name="valor_tef" id="valor_tef" value="{$smarty.post.valor_tef}" maxlength='10' onkeydown="FormataValor('valor_tef')" onkeyup="FormataValor('valor_tef')" />
								</td>
							</tr>							

							<tr><td>&nbsp;</td></tr>

							<tr>
								<td align="center" colspan="2">
									<input type="button" class="botao_padrao" value="Consulta de Cheque!" name="button" onClick="xajax_Verifica_Campos_Comando_TEF_AJAX('CHQ', xajax.getFormValues('for_tef'))" />
								</td>
							</tr>

						</table>
					</td>
				</tr>

			</table>



		</form>



  {/if}


{/if}

{include file="com_rodape.tpl"}
