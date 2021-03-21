{include file="com_cabecalho.tpl"}

{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}

<script type="text/javascript" src="{$conf.addr}/common/js/autocompletar.js"></script>

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


  {if $flags.action == "listar"}



		<br>
    <table width="100%">

      <tr>
        <td colspan="9" align="center">
          Produto:
          <input type="hidden" name="idproduto" id="idproduto" value="{$smarty.post.idproduto}" />
          <input type="hidden" name="idproduto_NomeTemp" id="idproduto_NomeTemp" value="{$smarty.post.idproduto_NomeTemp}" />
          <input class="ultralarge" type="text" name="idproduto_Nome" id="idproduto_Nome" value="{$smarty.post.idproduto_Nome}"
            onKeyUp="javascript:
              VerificaMudancaCampo('idproduto');
            "
          />
          <span class="nao_selecionou" id="idproduto_Flag">
            &nbsp;&nbsp;&nbsp;
          </span>
        </td>
      </tr>
  
      <script type="text/javascript">
      new CAPXOUS.AutoComplete("idproduto_Nome", function() {ldelim}
      return "produto_ajax.php?ac=busca_produto&typing=" + this.text.value + "&campoID=idproduto" + "&idproduto=0" + "&mostraDetalhes=1";
      {rdelim}, {ldelim} minChars: {$conf.minimo_auto_completar} {rdelim});
      </script>
  
  
      <script type="text/javascript">
      // verifica os campos auto-complete
      VerificaMudancaCampo('idproduto');
      </script>
      
      <tr><td>&nbsp;</td></tr>
      <tr><td>&nbsp;</td></tr>
      <tr><td>&nbsp;</td></tr>
      
      <tr>
        <td colspan="9" align="center">

          <div style="width: 100%;" id="dados_produto">
                    
          </div>
    
        </td>
      </tr>


				<tr><td>&nbsp;</td></tr>

	


			</table>



		{else}

			{if $smarty.post.for_chk}
      
      	{include file="div_resultado_nenhum.tpl"}
			{/if}

		{/if}		


		<br>

		<table width="100%" border="0" cellpadding="5" cellspacing="0">
			<tr>
				<td>
					
				</td>
			</tr>
		</table>




{/if}

{include file="com_rodape.tpl"}
