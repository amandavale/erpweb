{include file="com_cabecalho_relatorio.tpl"}

{literal}
  <style type="text/css">
		
		ul.tabs,.tab_container{
			width: 1000px; 
			float:center;
		
		}
		
		ul.tabs {
			
			padding: 0;
			margin: 0;
			float: left;
			list-style: none;
			height: 32px;
			border-bottom: 1px solid #999; 
		}
			
		ul.tabs li {
			float: left;
			font-size:18px;
			font-weight:bold;
			margin: 0;
			padding: 0 10px;
			line-height: 40px;
			overflow: hidden;
			position: relative; 
			font-size:26px;
		}
		
		.tab_container {
			margin-top:20px;
			overflow: hidden;
			clear: both;
			float: left;  
		}
		
		th, td {
		    color:#000;
		    font-family: Arial,Helvetica,sans-serif;
		    font-size: 15px; 
		    vertical-align:top;
		}   
		
		h3{ 
		  width:330px; 
		} 
		 
		 
	   .td_field{
	 	  color:#444;
	 	  padding: 0px 0px 6px ;
	    }
		 
		.td_label{
	 	  padding: 0px 0px 6px 10px;
	    } 
		
				    
  </style>
{/literal}



{include file="div_login.tpl"}

{if !$flags.okay}{include file="div_erro.tpl"}{/if}

{if $flags.okay}
		

		<ul class="tabs">
			<li>{$info.litnome_funcionario}</li>
		</ul>
		
		<div class="tab_container">		
			
<table>
	<tr>
		<td>
					
			<table width="80%" align="left" style="margin-right:50px;">
				
				<tr><td colspan="2"><h3>Dados Profissionais</h3></td></tr>
				
				{if count($list_filial)}
				
				  {foreach from=$list_filial key=i item=filial}
					<tr>
						<td class="td_label" width="25%" >Filial {if $i+1 > 1}{$i+1}{/if}:</td>
						<td class="td_field" width="75%">{$filial.nome_filial}</td>
					</tr>
				  {/foreach}
					
				{/if}
				
				
				{if $info.dados_cargo.nome_cargo}
				<tr>
					<td class="td_label" width="35%" >Cargo:</td>
					<td class="td_field">{$info.dados_cargo.nome_cargo}</td>
				</tr>
				{/if}
				
				{if $info.litcarteira_trabalho_funcionario}
				<tr>
					<td class="td_label">Carteira de trabalho:</td>
					<td class="td_field">{$info.litcarteira_trabalho_funcionario}</td>
				</tr>
				{/if}
				
				{if $info.ctps_serie}
				<tr>
					<td class="td_label">Série:</td>
					<td class="td_field">{$info.ctps_serie}</td>
				</tr>
				{/if}
				
				{if $info.ctps_uf }
				<tr>
					<td class="td_label">UF:</td>
					<td class="td_field">{$info.ctps_uf}</td>
				</tr>
				{/if}
				
				{if $info.pis_pasep }
				<tr>
					<td class="td_label">Nº do PIS/PASEP:</td>
					<td class="td_field">{$info.pis_pasep}</td>
				</tr>				
				{/if}
				
				{if $info.litdata_admissao_funcionario }
				<tr>
					<td class="td_label">Data de admissão:</td>
					<td class="td_field">{$info.litdata_admissao_funcionario}</td>
				</tr>
				{/if}
				
				{if $info.data_demissao_funcionario }
				<tr>
					<td class="td_label">Data de demissão:</td>
					<td class="td_field">{$info.data_demissao_funcionario}</td>
				</tr>						
				{/if}
				
				{if $info.data_afastamento }
				<tr>
					<td class="td_label">Data de afastamento:</td>
					<td class="td_field">{$info.data_afastamento}</td>
				</tr>				
				{/if}
				
				{if $info.motivo_afastamento }
				<tr>
					<td class="td_label">Motivo do afastamento:</td>
					<td class="td_field">{$info.motivo_afastamento}</td>
				</tr>				
				{/if}
				
				{if $info.horario_trabalho }
				<tr>
					<td class="td_label">Horário de trabalho:</td>
					<td class="td_field" width="30%">{$info.horario_trabalho}</td>
				</tr>
				{/if}
				
				{if $info.intervalo_refeicao }
				<tr>
					<td class="td_label">Intervalo intra-jornada:</td>
					<td class="td_field">{$info.intervalo_refeicao}</td>
				</tr>		
				{/if}
				
				{if $info.salario_funcionario && $info.salario_funcionario != '0,00' }
				<tr>
					<td class="td_label">Salário contratual (R$):</td>
					<td class="td_field">{$info.salario_funcionario}</td>
				</tr>		
				{/if}
				
				{if $info.tipo_salario }
				<tr>
					<td class="td_label">Tipo de salário:</td>
					<td class="td_field">{if $info.tipo_salario == "M"}Mensal{elseif $info.tipo_salario == "H"}Por Hora{/if}</td>
				</tr>						
				{/if}
				
				{if $info.dias_contrato_exp }
				<tr>
					<td class="td_label">Dias de contrato de experiência:</td>
					<td class="td_field">{$info.dias_contrato_exp}</td>
				</tr>								
				{/if}
				
				{if $info.litobservacao_funcionario }
				<tr>
					<td class="td_label">Observação:</td>
					<td class="td_field">{$info.litobservacao_funcionario}</td>
				</tr>
				{/if}
				
				
				<tr><td><br /><br /></td></tr>
				<tr><td colspan="2"><h3>Dados Pessoais</h3></td></tr>
				{if $info.litsexo_funcionario }
				<tr>
					<td class="td_label" width="40%" >Sexo:</td>
					<td class="td_field">{if $info.litsexo_funcionario=="M"}Masculino{else}Feminino{/if}</td>
				</tr>								
				{/if}
				
				{if $info.nome_pai }
				<tr>
					<td class="td_label">Nome do Pai:</td>
					<td class="td_field">{$info.nome_pai}</td>
				</tr>				
				{/if}
				
				{if $info.nome_mae }
				<tr>
					<td class="td_label">Nome da Mãe:</td>
					<td class="td_field">{$info.nome_mae}</td>
				</tr>				
				{/if}
				
				{if $info.estado_civil }
				<tr>
					<td class="td_label">Estado Civil:</td>
					<td class="td_field">{$info.estado_civil}</td>
				</tr>				
				{/if}
				
				{if $info.litnome_conjuge_funcionario }
				<tr>
					<td class="td_label">Nome do conjuge:</td>
					<td class="td_field">{$info.litnome_conjuge_funcionario}</td>
				</tr>				
				{/if}
				
				{if $info.escolaridade_funcionario }
				<tr>
					<td class="td_label">Escolaridade:</td>
					<td class="td_field">{$info.escolaridade_funcionario}</td>
				</tr>				
				{/if}
				
				{if $info.qtd_filhos }
				<tr>
					<td class="td_label">N&ordm; de Filhos:</td>
					<td class="td_field">{$info.qtd_filhos}</td>
				</tr>				
				{/if}
				
				{if $info.filhos_funcionario }
				<tr>
					<td  valign="top" class="td_label">Nome dos Filhos:</td>
					<td class="td_field">{$info.filhos_funcionario}</td>
				</tr>
				{/if}
				
				<tr><td><br /><br /></td></tr>
				<tr><td colspan="2"><h3>Aspectos Físicos</h3></td></tr>
				
				{if $info.cor_funcionario }
				<tr>
					<td class="td_label" >Cor:</td>
					<td class="td_field">{$info.cor_funcionario}</td>
				</tr>				
				{/if}
				
				{if $info.cabelo_funcionario }
				<tr>
					<td class="td_label" >Cabelo:</td>
					<td class="td_field">{$info.cabelo_funcionario}</td>
				</tr>				
				{/if}
				
				{if $info.olhos_funcionario }
				<tr>
					<td class="td_label" >Olhos:</td>
					<td class="td_field">{$info.olhos_funcionario}</td>
				</tr>				
				{/if}
				
				{if $info.altura_funcionario }
				<tr>
					<td class="td_label">Altura:</td>
					<td class="td_field">{if $info.altura_funcionario}{$info.altura_funcionario} m{/if}</td>
				</tr>				
				{/if}
				
				{if $info.peso_funcionario }
				<tr>
					<td class="td_label">Peso:</td>
					<td class="td_field">{if $info.peso_funcionario}{$info.peso_funcionario} kg{/if}</td>
				</tr>				
				{/if}
				
				{if $info.sinais_funcionario }
				<tr>
					<td class="td_label">Sinais:</td>
					<td class="td_field">{$info.sinais_funcionario}</td>
				</tr>
				{/if}
				
				
				{if $info.numeracao_blusa_funcionario}
					<tr>
						<td class="td_label" width="50%" >Numeração da Blusa</td>
						<td class="td_field">{$info.numeracao_blusa_funcionario}</td>
					</tr>
				{/if}
				{if $info.numeracao_calca_funcionario}
					<tr>
						<td class="td_label" width="50%" >Numeração da Calça</td>
						<td class="td_field">{$info.numeracao_calca_funcionario}</td>
					</tr>
				{/if}
				{if $info.numeracao_calcado_funcionario}
					<tr>
						<td class="td_label" width="50%" >Numeração do Calçado</td>
						<td class="td_field">{$info.numeracao_calcado_funcionario}</td>
					</tr>
				{/if}
				
				
			</table>


</td>
<td>
				
			<table width="100%" align="left" style="clear:right">
				
				<tr><td colspan="2"><h3>Identificação</h3></td></tr>
				
				{if $info.litidentidade_funcionario }
					<tr>
						<td class="td_label" width="50%" >Nº da identidade:</td>
						<td class="td_field">{$info.litidentidade_funcionario}</td>
					</tr>	
		
					{if $info.rg_data_expedicao_funcionario}
						<tr>
							<td class="td_label" width="50%" >Data de Expedição</td>
							<td class="td_field">{$info.rg_data_expedicao_funcionario} {if $info.rg_orgao_emissor_funcionario}- {$info.rg_orgao_emissor_funcionario}{/if}</td>
						</tr>					
					{/if}
										
				{/if}
				
				{if $info.litcpf_funcionario }
				<tr>
					<td class="td_label" >CPF:</td>
					<td class="td_field">{$info.litcpf_funcionario}</td>
				</tr>
				{/if}
				
				{if $info.titulo_eleitor }
				<tr>
					<td class="td_label">Título de Eleitor:</td>
					<td class="td_field">{$info.titulo_eleitor}</td>
				</tr>
				{/if}
				
				{if $info.habilitacao }
				<tr>
					<td class="td_label">Carteira de Habilitação:</td>
					<td class="td_field">{$info.habilitacao}</td>
				</tr>
				{/if}
				
				{if $info.naturalidade }
				<tr>
					<td  class="td_label">Naturalidade:</td>
					<td class="td_field">{$info.naturalidade}</td>
				</tr>				
				{/if}
				
				{if $info.nacionalidade }
				<tr>
					<td class="td_label" >Nacionalidade:</td>
					<td class="td_field">{$info.nacionalidade}</td>
				</tr>				
				{/if}
				
				{if $info.litdata_nascimento_funcionario }
				<tr>
					<td class="td_label">Data de nascimento:</td>
					<td class="td_field">{$info.litdata_nascimento_funcionario}</td>
				</tr>
				{/if}
				
				
				
				<tr><td><br /><br /></td></tr>
				<tr><td  colspan="2"><h3>Endere&ccedil;o</h3></td></tr>
				
		
				
				{if $info.funcionario_logradouro }
				<tr>
					<td  width="17%" class="td_label" >Logradouro:</td>
					<td class="td_field">{$info.funcionario_logradouro}</td>
				</tr>	
				{/if}
				
				{if $info.funcionario_numero }
				<tr>
					<td class="td_label">Nº:</td>
					<td class="td_field">{$info.funcionario_numero}</td>
				</tr>	
				{/if}
				
				{if $info.funcionario_complemento }
				<tr>
					<td class="td_label">Complemento:</td>
					<td class="td_field">{$info.funcionario_complemento}</td>
				</tr>	
				{/if}
				
				{if $info.funcionario_idestado_Nome }
				<tr>
					<td class="td_label">Estado:</td>
					<td class="td_field">{$info.funcionario_idestado_Nome}</td>
				</tr>
				{/if}
				
				{if $info.funcionario_idcidade_Nome }
				<tr>
					<td class="td_label">Cidade:</td>
					<td class="td_field">{$info.funcionario_idcidade_Nome}</td>
				</tr>					
				{/if}
				
				{if $info.funcionario_idbairro_NomeTemp }
				<tr>
					<td class="td_label">Bairro:</td>
					<td class="td_field">{$info.funcionario_idbairro_NomeTemp}</td>
				</tr>
				{/if}
				
				{if $info.funcionario_cep }
					<tr>
						<td class="td_label" >CEP:</td>
						<td{$info.funcionario_cep}</td>
					</tr>
				{/if}
				
				<tr><td><br /><br /></td></tr>
				<tr><td colspan="2"><h3>Informações de Contato</h3></td></tr>
				{if $info.littelefone_funcionario }				
				<tr>
					<td class="td_label" >Telefone:</td>
					<td class="td_field">
						{if $info.telefone_funcionario_ddd}({$info.telefone_funcionario_ddd}){/if}
						{$info.littelefone_funcionario}
					</td>
				</tr>				
				{/if}
				
				{if $info.litcelular_funcionario }
				<tr>
					<td class="td_label" >Celular:</td>
					<td class="td_field">
						{if $info.celular_funcionario_ddd}({$info.celular_funcionario_ddd}){/if}
						{$info.litcelular_funcionario}
					</td>
				</tr>
				{/if}
				
				{if $info.litemail_funcionario }
				<tr>
					<td class="td_label" >Email:</td>
					<td class="td_field">{$info.litemail_funcionario}</td>
				</tr>
				{/if}
				
				{if count($info.dados_conta)}
				<tr><td><br /><br /></td></tr>
				<tr><td colspan="2"><h3>Contas Bancárias</h3></td></tr>
				{foreach from=$info.dados_conta item=conta}
					<tr>
						<td class="td_label">Banco: </td>
						<td class="td_field">{$conta.nome_banco}</td>	
					</tr>
					<tr>						
						<td class="td_label">Agência: </td>
						<td class="td_field">{$conta.agencia_funcionario} {if $conta.agencia_dig_funcionario}- {$conta.agencia_dig_funcionario}{/if}</td>
					</tr>
					<tr>	
						<td class="td_label">Conta: </td>
						<td class="td_field">{$conta.conta_funcionario} {if $conta.conta_dig_funcionario}- {$conta.conta_dig_funcionario}{/if}</td>
					</tr>
					<tr><td><br /></td></tr>
				{/foreach}
				{/if}
			</table>
		
		
		
		</td>
	</tr>
</table>			
		</div>
<div id='assinatura' style='clear:both'>
	<br /><br /><br /><br />
	<p align='center'>___________________________________________</p>
	<p align='center'>{$info.litnome_funcionario}</p>
</div>

        		
{/if}
	      
{include file="com_rodape_relatorio.tpl"}
