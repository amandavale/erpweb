{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}

<script type="text/vbscript" src="{$conf.addr}/common/js/orcamento.vbs"></script>
<script type="text/javascript" src="{$conf.addr}/common/js/orcamento.js"></script>


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

  {include file="div_erro.tpl"}
  


  {if $flags.action == "listar"}

  	{if $smarty.get.comando == "sintegra"}

			{include file="div_instrucoes_inicio.tpl"}
				<li>Este relatório tem que ser gerado diretamente da máquina que está conectada na impressora de ECF.</li>
				<li>Selecione o mês e ano do arquivo a ser gerado.</li>
				<li>Clique em GERAR.</li> 
				<li>O arquivo será gravado na área de Trabalho.</li>
				<li>Reuna o arquivo gerado para essa ECF com os arquivos das outras ECFs, para enviar um único relatório do SINTEGRA.</li> 
			{include file="div_instrucoes_fim.tpl"}

	
    <form  action="" method="post" name="for_ecf" id="for_ecf">
			<input type="hidden" name="for_chk" id="for_chk" value="1" />
			
			<input type="hidden" name="razao_social" id="razao_social" value="{$info_filial.nome_filial}" />
			<input type="hidden" name="endereco" id="endereco" value="{$info_filial.logradouro}" />
			<input type="hidden" name="numero" id="numero" value="{$info_filial.numero}" />
			<input type="hidden" name="complemento" id="complemento" value="{$info_filial.complemento}" />
			<input type="hidden" name="nome_bairro" id="nome_bairro" value="{$info_filial.nome_bairro}" />
			<input type="hidden" name="nome_cidade" id="nome_cidade" value="{$info_filial.nome_cidade}" />
			<input type="hidden" name="cep" id="cep" value="{$info_filial.cep}" />
			<input type="hidden" name="telefone_filial" id="telefone_filial" value="{$info_filial.telefone_filial}" />
			<input type="hidden" name="fax_filial" id="fax_filial" value="{$info_filial.fax_filial}" />

			<input type="hidden" name="serie_ecf" id="serie_ecf" value="" />
			<input type="hidden" name="reducaoZ_efetuada" id="reducaoZ_efetuada" value="0" />
			<input type="hidden" name="travar_teclado" id="travar_teclado" value="{$conf.travar_teclado}" />
			<input type="hidden" name="usaTEF" id="usaTEF" value="0" />
			
				<table width="100%">
	
					<tr>
						<td colspan="2" align="center">
							<table class="tb4cantos" width="100%">
								<tr bgcolor="#F7F7F7">
									<td colspan="9" align="center">Gerar arquivo do Sintegra da ECF</td>
								</tr>
	
								<tr>
									<td align="right" width="50%">
										<select name="mes" id="mes">
											<option value="01">Janeiro</option>
											<option value="02">Fevereiro</option>
											<option value="03">Março</option>
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
									</td>
									<td>
										<select name="ano" id="ano">
											<option value="{$ano.1}">{$ano.1}</option>   
											<option value="{$ano.2}">{$ano.2}</option>
											<option value="{$ano.3}">{$ano.3}</option>
											<option value="{$ano.4}">{$ano.4}</option>
										</select>
									</td>
								</tr> 

								<tr><td>&nbsp;</td></tr>

								<tr>
									<td align="center" colspan="2" class="req">
										<input type="button" class="botao_padrao" value="Gerar Arquivo!" name="button" onClick="xajax_Verifica_Campos_Comando_ECF_AJAX(xajax.getFormValues('for_ecf'))" />
									</td>
								</tr>
	
							</table>
						</td>
					</tr>
	
				</table>
			</form>

  	{elseif $smarty.get.comando == "leiturax"}

			{include file="div_instrucoes_inicio.tpl"}
				<li>A Leitura X tem que ser feita diretamente da máquina que está conectada na impressora de ECF.</li>
				<li>O cupom fiscal deve estar fechado.</li> 
			{include file="div_instrucoes_fim.tpl"}

			<form  action="" method="post" name="for_ecf" id="for_ecf">
			<input type="hidden" name="for_chk" id="for_chk" value="1" />
			<input type="hidden" name="serie_ecf" id="serie_ecf" value="" />
			<input type="hidden" name="reducaoZ_efetuada" id="reducaoZ_efetuada" value="0" />
			<input type="hidden" name="travar_teclado" id="travar_teclado" value="{$conf.travar_teclado}" />
			<input type="hidden" name="usaTEF" id="usaTEF" value="0" />
			
				<table width="100%">
	
					<tr>
						<td colspan="2" align="center">
							<table class="tb4cantos" width="100%">
								<tr bgcolor="#F7F7F7">
									<td colspan="9" align="center">Leitura X</td>
								</tr>

								<tr><td>&nbsp;</td></tr>

								<tr>
									<td align="center" colspan="2" class="req">
										<input type="button" class="botao_padrao" value="Fazer Leitura X!" name="button" onClick="xajax_Verifica_Campos_Comando_ECF_AJAX(xajax.getFormValues('for_ecf'))" />
									</td>
								</tr>
	
							</table>
						</td>
					</tr>

				</table>
			</form>



  	{elseif $smarty.get.comando == "lmf_data"}

			{include file="div_instrucoes_inicio.tpl"}
				<li>A Leitura de Memória Fiscal por Data tem que ser feita diretamente da máquina que está conectada na impressora de ECF.</li>
				<li>O cupom fiscal deve estar fechado.</li> 
			{include file="div_instrucoes_fim.tpl"}

			<form  action="" method="post" name="for_ecf" id="for_ecf">
			<input type="hidden" name="for_chk" id="for_chk" value="1" />
			<input type="hidden" name="serie_ecf" id="serie_ecf" value="" />
			<input type="hidden" name="reducaoZ_efetuada" id="reducaoZ_efetuada" value="0" />
			<input type="hidden" name="travar_teclado" id="travar_teclado" value="{$conf.travar_teclado}" />
			<input type="hidden" name="usaTEF" id="usaTEF" value="0" />
			
				<table width="100%">
	
					<tr>
						<td colspan="2" align="center">
							<table class="tb4cantos" width="100%">
								<tr bgcolor="#F7F7F7">
									<td colspan="9" align="center">Leitura de Memória Fiscal por Data</td>
								</tr>
	
								<tr>
									<td colspan="9" align="center">
										Data Inicial:
										<input class="short" type="text" name="data_vencimento_de" id="data_vencimento_de" value="{$smarty.post.data_vencimento_de}" maxlength='10' onkeydown="mask('data_vencimento_de', 'data')" onkeyup="mask('data_vencimento_de', 'data')" />
										<img src="{$conf.addr}/common/img/calendar.png" id="img_data_vencimento_de" style="cursor: pointer;" />
										<script type="text/javascript">
											Calendar.setup(
												{ldelim}
													inputField : "data_vencimento_de", // ID of the input field
													ifFormat : "%d/%m/%Y", // the date format
													button : "img_data_vencimento_de", // ID of the button
													align  : "cR"  // alinhamento
												{rdelim}
											);
										</script>									
												
										Data Final:
										<input class="short" type="text" name="data_vencimento_ate" id="data_vencimento_ate" value="{$smarty.post.data_vencimento_ate}" maxlength='10' onkeydown="mask('data_vencimento_ate', 'data')" onkeyup="mask('data_vencimento_ate', 'data')" /> 
										<img src="{$conf.addr}/common/img/calendar.png" id="img_data_vencimento_ate" style="cursor: pointer;" />
										<script type="text/javascript">
											Calendar.setup(
												{ldelim}
													inputField : "data_vencimento_ate", // ID of the input field
													ifFormat : "%d/%m/%Y", // the date format
													button : "img_data_vencimento_ate", // ID of the button
													align  : "cR"  // alinhamento
												{rdelim}
											);
										</script>	
										(dd/mm/aaaa)
	
									</td>
								</tr>

								<tr><td>&nbsp;</td></tr>

								<tr>
									<td align="center" colspan="2" class="req">
										<input type="button" class="botao_padrao" value="Fazer Leitura de Memória Fiscal por Data !" name="button" onClick="xajax_Verifica_Campos_Comando_ECF_AJAX(xajax.getFormValues('for_ecf'))" />
									</td>
								</tr>
	
							</table>
						</td>
					</tr>

				</table>
			</form>


  	{elseif $smarty.get.comando == "lmf_reducao"}

			{include file="div_instrucoes_inicio.tpl"}
				<li>A Leitura de Memória Fiscal por Redução tem que ser feita diretamente da máquina que está conectada na impressora de ECF.</li>
				<li>O cupom fiscal deve estar fechado.</li> 
			{include file="div_instrucoes_fim.tpl"}

			<form  action="" method="post" name="for_ecf" id="for_ecf">
			<input type="hidden" name="for_chk" id="for_chk" value="1" />
			<input type="hidden" name="serie_ecf" id="serie_ecf" value="" />
			<input type="hidden" name="reducaoZ_efetuada" id="reducaoZ_efetuada" value="0" />
			<input type="hidden" name="travar_teclado" id="travar_teclado" value="{$conf.travar_teclado}" />
			<input type="hidden" name="usaTEF" id="usaTEF" value="0" />
			
				<table width="100%">
	
					<tr>
						<td colspan="2" align="center">
							<table class="tb4cantos" width="100%">
								<tr bgcolor="#F7F7F7">
									<td colspan="9" align="center">Leitura de Memória Fiscal por Redução</td>
								</tr>
	
								<tr>
									<td colspan="9" align="center">
										Redução Inicial:
										<input class="short" type="text" name="reducao_inicial" id="reducao_inicial" value="" maxlength='4' onkeydown="FormataInteiro('reducao_inicial')" onkeyup="FormataInteiro('reducao_inicial')" />							
												
										Redução Final:
										<input class="short" type="text" name="reducao_final" id="reducao_final" value="" maxlength='4' onkeydown="FormataInteiro('reducao_final')" onkeyup="FormataInteiro('reducao_final')" />
	
									</td>
								</tr>

								<tr><td>&nbsp;</td></tr>

								<tr>
									<td align="center" colspan="2" class="req">
										<input type="button" class="botao_padrao" value="Fazer Leitura de Memória Fiscal por Redução !" name="button" onClick="xajax_Verifica_Campos_Comando_ECF_AJAX(xajax.getFormValues('for_ecf'))" />
									</td>
								</tr>
	
							</table>
						</td>
					</tr>

				</table>
			</form>
      
    {elseif $smarty.get.comando == "suprimento"}

      {include file="div_instrucoes_inicio.tpl"}
        <li>O Registro de Suprimento de Caixa tem que ser feito diretamente da máquina que está conectada na impressora de ECF.</li>
        <li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>
      {include file="div_instrucoes_fim.tpl"}

      <form  action="" method="post" name="for_ecf" id="for_ecf">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
      <input type="hidden" name="serie_ecf" id="serie_ecf" value="" />
			<input type="hidden" name="reducaoZ_efetuada" id="reducaoZ_efetuada" value="0" />
			<input type="hidden" name="travar_teclado" id="travar_teclado" value="{$conf.travar_teclado}" />
			<input type="hidden" name="usaTEF" id="usaTEF" value="0" />
			
        <table width="100%">
  
          <tr>
            <td colspan="2" align="center">
              <table class="tb4cantos" width="100%">
                <tr bgcolor="#F7F7F7">
                  <td colspan="9" align="center">Registro de Suprimento de Caixa</td>
                </tr>
  
                <tr>
                  <td colspan="9"  align="center">
                    <a class="req">Valor R$:</a>
                    <input class="short" type="text" name="valor_suprimento" id="valor_suprimento" value="" maxlength='10' onkeydown="FormataValor('valor_suprimento')" onkeyup="FormataValor('valor_suprimento')" />    
                    Tipo de Pagamento:
                    <input class="short" type="text" name="tipo_suprimento" id="tipo_suprimento" value="Dinheiro"/>
  
                  </td>
                </tr>

                <tr><td>&nbsp;</td></tr>

                <tr>
                  <td align="center" colspan="2" class="req">
                    <input type="button" class="botao_padrao" value="Fazer Registro de Suprimento de Caixa !" name="button" onClick="xajax_Verifica_Campos_Comando_ECF_AJAX(xajax.getFormValues('for_ecf'))" />
                  </td>
                </tr>
  
              </table>
            </td>
          </tr>

        </table>
      </form>      
      
    {elseif $smarty.get.comando == "sangria"}

      {include file="div_instrucoes_inicio.tpl"}
        <li>O Registro de Sangria tem que ser feito diretamente da máquina que está conectada na impressora de ECF.</li>
        <li>Os campos em <span class="req">vermelho</span> s&atilde;o obrigat&oacute;rios.</li>
      {include file="div_instrucoes_fim.tpl"}

      <form  action="" method="post" name="for_ecf" id="for_ecf">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
      <input type="hidden" name="serie_ecf" id="serie_ecf" value="" />
			<input type="hidden" name="reducaoZ_efetuada" id="reducaoZ_efetuada" value="0" />
 			<input type="hidden" name="travar_teclado" id="travar_teclado" value="{$conf.travar_teclado}" />
			<input type="hidden" name="usaTEF" id="usaTEF" value="0" />
			
        <table width="100%">
  
          <tr>
            <td colspan="2" align="center">
              <table class="tb4cantos" width="100%">
                <tr bgcolor="#F7F7F7">
                  <td colspan="9" align="center">Registro de Sangria ou Retirada de Caixa</td>
                </tr>
  
                <tr>
                  <td colspan="9" class="req" align="center">
                    Valor R$:
                    <input class="short" type="text" name="valor_sangria" id="valor_sangria" value="" maxlength='10' onkeydown="FormataValor('valor_sangria')" onkeyup="FormataValor('valor_sangria')" />    
  
                  </td>
                </tr>

                <tr><td>&nbsp;</td></tr>

                <tr>
                  <td align="center" colspan="2" class="req">
                    <input type="button" class="botao_padrao" value="Fazer Registro de Sangria !" name="button" onClick="xajax_Verifica_Campos_Comando_ECF_AJAX(xajax.getFormValues('for_ecf'))" />
                  </td>
                </tr>
  
              </table>
            </td>
          </tr>

        </table>
      </form>      
      
    {elseif $smarty.get.comando == "reducao_z"}

      {include file="div_instrucoes_inicio.tpl"}
        <li>O Registro de Redução Z tem que ser diretamente da máquina que está conectada na impressora de ECF.</li>
        <li>O Registro de Redução Z tem que ser feito diariamente e somente no fim de cada dia.</li>        
        <li>Após ser feito o Registro de Redução Z a impressora ficará indisponível(travada) até a zero hora do dia seguinte.</li>
        
      {include file="div_instrucoes_fim.tpl"}

      <form  action="" method="post" name="for_ecf" id="for_ecf">
      <input type="hidden" name="for_chk" id="for_chk" value="1" />
      <input type="hidden" name="serie_ecf" id="serie_ecf" value="" />
			<input type="hidden" name="reducaoZ_efetuada" id="reducaoZ_efetuada" value="0" />
			<input type="hidden" name="travar_teclado" id="travar_teclado" value="{$conf.travar_teclado}" />
			<input type="hidden" name="usaTEF" id="usaTEF" value="0" />
			
        <table width="100%">
  
          <tr>
            <td colspan="2" align="center">
              <table class="tb4cantos" width="100%">
                <tr bgcolor="#F7F7F7">
                  <td colspan="9" align="center">Registro de Redução Z</td>
                </tr>
  
                <tr><td>&nbsp;</td></tr>

                <tr>
                  <td align="center" colspan="2" class="req">
                    <input type="button" class="botao_padrao" value="Fazer Registro de Redução Z !" name="button" onClick="xajax_Verifica_Campos_Comando_ECF_AJAX(xajax.getFormValues('for_ecf'))" />
                  </td>
                </tr>
  
              </table>
            </td>
          </tr>

        </table>
      </form>          
      

  	{/if}


     <script language="javascript">
        // busca o número de série de impressora
        NumeroSerie();

				// verifica se já foi feito alguma redução Z no dia
				VerificaReducaoZnoDia();

				// verifica se o número de serie está na tabela auxiliar
				// também verifica se já foi feito alguma redução Z no dia
        xajax_Verifica_Serie_ECF_AJAX(xajax.getFormValues('for_ecf'));
     </script>

	



  {/if}


{/if}

{include file="com_rodape.tpl"}
