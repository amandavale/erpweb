<!--





	// função que verifica o retorno da impressora
	function CheckParameter(iRetorno) {
		//alert(' chek parameter ' + iRetorno);
		var msg = "";
		var erro = "";
		var erro_comunicacao = "";

		var frame_orcamento = document;
		var travar = frame_orcamento.getElementById("travar_teclado").value;

		if (iRetorno == 0)
			//msg = "Erro de comunicação!";
			erro_comunicacao = "1";
		else if (iRetorno == -2)
			msg = "Erro de parâmetros!";
		else if (iRetorno == -4)
			msg = "Arquivo .INI não encontrado!";
		else if (iRetorno == -5)
			msg = "Erro ao abrir Port de comunicação!";
		else if (iRetorno == -6)
			msg = "Impressora desligada!";
		else if (iRetorno != 1)
			msg = "Outro erro!";

		if ( (msg.length > 0) || (erro_comunicacao == "1") ) {
			// Destrava o teclado / mouse
			if (travar == 1) Finaliza_Modo_TEF();

			if (msg.length > 0) alert(msg);

			if (frame_orcamento.getElementById("usaTEF") != null) {
				if ( (frame_orcamento.getElementById("usaTEF").value == "0") && (erro_comunicacao == "1") ) {
					msg = "Erro de comunicação!";
					alert(msg);
     		}
			}

			erro = "1";
		}

		if (VerificaEstadoImpressora(travar) == false) erro = "1";

		// se ocorreu erro, processa
		if ( (erro == "1") ) {
			// se não abriu o cupom ainda, volta o registro para orçamento, pois não houve emissão fiscal
			if (frame_orcamento.getElementById("abriu_cupom_ecf") != null) {
				if (frame_orcamento.getElementById("abriu_cupom_ecf").value == "0") {
					if (frame_orcamento.getElementById("iniciou_impressao_ecf") != null) {
						xajax_Define_Status_Impressao_ECF_AJAX (frame_orcamento.getElementById("idorcamento").value, "0", "", "", "O");
					}
				}
				// verifica se já terminou de imprimir a ecf, para identificar se o problema ocorreu na ECF ou na TEF
				else if (frame_orcamento.getElementById("terminou_impressao_ecf") != null) {
					if ( (frame_orcamento.getElementById("terminou_impressao_ecf").value == "0") && (iRetorno == 0) ) {
						// se tiver usando tef, cancela a transação
						if (frame_orcamento.getElementById("usaTEF") != null) {
							if (frame_orcamento.getElementById("usaTEF").value != "0") {
								if (CancelaTransacao() == false) return false;
			     		}
						}

						var idorcamento = frame_orcamento.getElementById("idorcamento").value;
						var url = "";
						url = frame_orcamento.getElementById("endereco_base").value;
						url += "/admin/orcamento.php?ac=listarNF&sucesso=alterar&numero_nota=" + idorcamento + "&chk_imprimir_emissao_nf=0";
						url += "&idorcamento=" + idorcamento;

						frame_orcamento.getElementById("for_orcamento").action = url;
						frame_orcamento.getElementById("for_orcamento").submit();
					}
				}
			}

			return false;
		}

		return true;
	}
	//-------------------------------------------------------------


	// Funções da Impressora Fiscal
	
	function ReimpressaoNaoFiscalVinculadoMFD(){
		alert('ReimpressaoNaoFiscalVinculadoMFD');
		iRetorno = BemaWeb.ReimpressaoNaoFiscalVinculadoMFD();
		if (CheckParameter(iRetorno) == false) return false;
		return true;
	}
	
	function AbreCupom(CPFCNPJ){
  		//alert(' Abre cupon ');
		iRetorno = BemaWeb.AbreCupom(CPFCNPJ);
		if (CheckParameter(iRetorno) == false) return false;
		return true;
	}
	function UsaUnidadeMedida(Unidade){
		//alert(' usa medida ');
		iRetorno = BemaWeb.UsaUnidadeMedida(Unidade);
		if (CheckParameter(iRetorno) == false) return false;
		return true;
	}
	function VendeItem(CodigoProduto, Descricao, Aliquota, sTipoQtde, Quantidade, iDecimal, Valor, sTipoDesconto, Desconto){
		//alert(' vende item ');
		iRetorno = BemaWeb.VendeItem(CodigoProduto, Descricao, Aliquota, sTipoQtde, Quantidade, iDecimal, Valor, sTipoDesconto, Desconto);
		if (CheckParameter(iRetorno) == false) return false;
		return true;
	}
	function VendeItemDepartamento(CodigoProduto, Descricao, Aliquota, ValorUnitario, Quantidade, Acrescimo, Desconto, IndiceDepartamento, UnidadeMedida){
		//alert(' vende item Departamento ');
		iRetorno = BemaWeb.VendeItemDepartamento(CodigoProduto, Descricao, Aliquota, ValorUnitario, Quantidade, Acrescimo, Desconto, IndiceDepartamento, UnidadeMedida);
		if (CheckParameter(iRetorno) == false) return false;
		return true;
	}
	function IniciaFechamentoCupom(AcrescimoDesconto, TipoAcrescimoDesconto, ValorAcrescimoDesconto){
		//alert(' inicia fechamento ');
		iRetorno = BemaWeb.IniciaFechamentoCupom(AcrescimoDesconto, TipoAcrescimoDesconto, ValorAcrescimoDesconto);
		if (CheckParameter(iRetorno) == false) return false;
		return true;
	}
	function EfetuaFormaPagamento(FormaPagamento,ValorPago){
		//alert(' efetua forma pagamento ');
		iRetorno = BemaWeb.EfetuaFormaPagamento(FormaPagamento,ValorPago);
		if (CheckParameter(iRetorno) == false) return false;
		return true;
	}
	function EfetuaFormaPagamentoDescricaoForma(FormaPagamento,ValorPago,DescricaoFormaPagamento){
		//alert(' efetua forma pagamento descricao forma ');
		iRetorno = BemaWeb.EfetuaFormaPagamentoDescricaoForma(FormaPagamento,ValorPago,DescricaoFormaPagamento);
		if (CheckParameter(iRetorno) == false) return false;
		return true;
	}
	function TerminaFechamentoCupom(MensagemPromocional){
		//alert(' termina fechamento cupom ');
		iRetorno = BemaWeb.TerminaFechamentoCupom(MensagemPromocional);
		if (CheckParameter(iRetorno) == false) return false;
		return true;
	}
	function NumeroCupom(){
		//alert(' Numero Cupom ');
		// identifica o frame
		var frame_orcamento = document;
		frame_orcamento.getElementById("ordemECF").value = RecuperaNumeroCupom();
		return true;
	}

	function ModuloTEF(tef_caminho){
		// identifica o frame
		var frame_orcamento = document;
		var retornoTEF1;
		retornoTEF1 = IniciaModuloTEF(tef_caminho);
		frame_orcamento.getElementById("finalizaTEF").value = retornoTEF1;
		return retornoTEF1;
	}
	function FinalizaModuloTEF(){
		//alert(' Finaliza Modulo TEF ');
		// identifica o frame
		var frame_orcamento = document;
		var retornoTEF2;
		retornoTEF2 = FinalizacaoModuloTEF();

		// se deu erro na finalização do tef, finaliza a operação por aki
		if (retornoTEF2 == false){
			frame_orcamento.getElementById("finalizaTEF").value = "-1"; // erro no tef
			if ( Finaliza_TEF_ECF () == false ) return false;
		}

		return retornoTEF2;
	}

	function ModuloTEF_Cheque(){
		//alert(' Modulo TEF cheque ');
		// identifica o frame
		var frame_orcamento = document;
		frame_orcamento.getElementById("finalizaTEF").value = IniciaModuloTEFCheque();
		return true;
	}
	//--------------------------------------------------------------------------

	// Função que Imprime o cupom fiscal
	function Imprime_Cupom_Fiscal () {

		// identifica o frame
		var frame_orcamento = document;
    
		// variável que define se vai travar o teclado / mouse durante a impressão do cupom
		//var travar = frame_orcamento.getElementById("travar_teclado").value;

		// Trava o teclado / mouse
		//if (travar == "1") Inicia_Modo_TEF();
		//------------------------------------------------

	    // recupera data e hora da ECF para o sistema.
	    if (VerificaNumeroSerie() == false) return false;

	    // recupera data e hora da ECF para o sistema.
	    if (Retorna_Data_Hora() == false) return false;

		//  Se for ECF, continua a função para imprimir o Cupom Fiscal
		if ( frame_orcamento.getElementById("tipoOrcamento").value != "ECF" ) {
			return false;
		}

		// ------>>> Abre o Cupom
		if ( AbreCupom('') == false ) return false;

		//------------------------------------------------

		// informa que já abriu o cupom fiscal
		frame_orcamento.getElementById("abriu_cupom_ecf").value = "1";
    //------------------------------------------------

    // ------>>> Recupera o número do Cupom Fiscal
    if ( NumeroCupom() == false ) return false;
    //------------------------------------------------

    // ------>>> Recupera o número do Cupom Fiscal
    if ( Insere_Itens_Vendidos_ECF () == false ) return false;
    //------------------------------------------------

		return true;
	}
	//--------------------------------------------------------------------------


	// Função que Imprime os itens vendidos no cupom fiscal
	function Insere_Itens_Vendidos_ECF () {

		// identifica o frame
		var frame_orcamento = document;

		// ------>>> Insere os itens vendidos
		var i;
		var total_produtos = parseInt(frame_orcamento.getElementById("total_produtos").value); // pega o numero de itens

		for (i=1; i<=total_produtos; i++) {

			if ( frame_orcamento.getElementById("idproduto_" + i) != null ) {

				var codigo_pro_temp = frame_orcamento.getElementById("idproduto_" + i).value; // codigo do produto

				var descricao_pro_temp = frame_orcamento.getElementById("descricao_produto_" + i).value; // descrição do produto
				descricao_pro_temp = descricao_pro_temp.substring(0,28);

				//var aliquota_pro_temp = "FF"; // aliquota do produto
				var aliquota_pro_temp = frame_orcamento.getElementById("icms_produto_formatado_" + i).value; // aliquota do produto
				var tipo_qtd_pro_temp = "F"; // Tipo da quantidade: I = inteira, F = fracionária

				//var qtd_pro_temp = frame_orcamento.getElementById("qtd_item_" + i).value; // quantidade do produto
				var qtd_pro_temp = frame_orcamento.getElementById("qtd_produto_" + i).value + '0'; // quantidade do produto

				var casas_decimais_pro_temp = '2'; // casas decimais do valor unitario

				// valor unitario do produto com 2 dígitos: para a função VendeItem()
				var preco_pro_temp = frame_orcamento.getElementById("preco_produto_final_" + i).innerHTML;
				
				// valor unitario do produto com 3 dígitos: para a função VendeItemDepartamento()
				//var preco_pro_temp = frame_orcamento.getElementById("celula_preco_" + i).innerHTML + '0';

				var tipo_desconto_pro_temp = '$'; // tipo do desconto: $ para valor e % para porcentagem
				var valor_desconto_pro_temp = '0'; // valor do desconto
				var valor_acrescimo_pro_temp = '0'; // valor do acrescimo


				// fazer uma leitura z e ver quais departamentos estão cadastrados para usar a VendeItemDepartamento ()
				var indice_departamento_pro_temp = '00'; // indice do departamento

				// unidade do produto
				var unidade_medida_pro_temp = '';
				unidade_medida_pro_temp = frame_orcamento.getElementById("sigla_unidade_venda_" + i).innerHTML;
				unidade_medida_pro_temp = unidade_medida_pro_temp.substring(0,2);
				//alert(unidade_medida_pro_temp);

				// A função VendeItem() não tem a unidade, logo tem que chamar esta função antes
				if ( UsaUnidadeMedida(unidade_medida_pro_temp) == false ) return false;

				// VendeItem() NÃO tem a unidade do produto, neste caso usar a função Bematech_FI_UsaUnidadeMedida("KG") antes de VendeItem
				/*
				alert('codigo '  + codigo_pro_temp);
				alert('descricao_pro_temp ' + descricao_pro_temp);
				alert('aliquota_pro_temp ' + aliquota_pro_temp);
				alert('tipo_qtd_pro_temp ' + tipo_qtd_pro_temp);
				alert('qtd_pro_temp ' + qtd_pro_temp);
				alert('casas_decimais_pro_temp ' + casas_decimais_pro_temp);
				alert('preco_pro_temp ' + preco_pro_temp);
				alert('tipo_desconto_pro_temp ' + tipo_desconto_pro_temp);
				alert('valor_desconto_pro_temp ' + valor_desconto_pro_temp);
				*/
				if ( VendeItem(codigo_pro_temp, descricao_pro_temp, aliquota_pro_temp, tipo_qtd_pro_temp, qtd_pro_temp, casas_decimais_pro_temp, preco_pro_temp, tipo_desconto_pro_temp, valor_desconto_pro_temp) == false ) return false;
				//if ( VendeItem('001', 'coca cola', 'FF', 'I', '1', '2', '1,00', '%', '0') == false ) return false;


				// VendeItemDepartamento() tem a unidade do produto, porém não funciona direito
				//if ( VendeItemDepartamento(codigo_pro_temp, descricao_pro_temp, aliquota_pro_temp, preco_pro_temp, qtd_pro_temp, valor_acrescimo_pro_temp, valor_desconto_pro_temp, indice_departamento_pro_temp, unidade_medida_pro_temp) == false ) return false;

			} // if

		} // for
		//------------------------------------------------
		

    // ------>>> Fecha o cupom fiscal
    if ( Inicia_Fechamento_Cupom_ECF () == false ) return false;
    //------------------------------------------------



		return true;
	}
	//--------------------------------------------------------------------------



	// Função que Fecha o cupom fiscal
	function Inicia_Fechamento_Cupom_ECF () {

		// identifica o frame
		var frame_orcamento = document;

		// ------>>> Inicia o fechamento do cupom
		var tipo_desconto_cupom = '$'; // tipo do desconto do cupom
		var acrescimo_desconto = frame_orcamento.getElementById("acrescimo_desconto").value; // valor do acrescimo_desconto
		var tipo_acrescimo_desconto = frame_orcamento.getElementById("tipo_acrescimo_desconto").value; // valor do acrescimo_desconto

		//alert('acrescimo_desconto ' + acrescimo_desconto);

		//alert('IniciaFechamentoCupom ' + tipo_acrescimo_desconto);

		if ( IniciaFechamentoCupom(tipo_acrescimo_desconto, tipo_desconto_cupom, acrescimo_desconto) == false ) return false;
		//------------------------------------------------
		
    // ------>>> Verifica se vai usar o tef
    if ( Verifica_TEF_ECF () == false ) return false;
    //------------------------------------------------

		return true;
	}
	//--------------------------------------------------------------------------



	// Função que verifica o tef da ecf
	function Verifica_TEF_ECF () {

		// identifica o frame
		var frame_orcamento = document;
		var tef_caminho = frame_orcamento.getElementById("tef_caminho").value

		// ------>>> Verifica se vai usar TEF
		if ( (frame_orcamento.getElementById("usaTEF").value != "0") && (frame_orcamento.getElementById("tef_caminho").value != "") ) {
			

			if ( (frame_orcamento.getElementById("usaTEF").value == "1") || (frame_orcamento.getElementById("usaTEF").value == "2") ) {

				if ( ModuloTEF(tef_caminho) == false ) return false;
			}

		}
		// não usa tef
		else {

			// ------>>> Faz o pagamento
			if ( Faz_Pagamento_ECF () == false ) return false;
			//------------------------------------------------

		}
		//------------------------------------------------


		return true;
	}
	//--------------------------------------------------------------------------


	// Função que faz o pagamento
	function Faz_Pagamento_ECF () {

		// identifica o frame
		var frame_orcamento = document;

		// ------>>> Efetua a forma de pagamento
		//if ( EfetuaFormaPagamento('teste', '0,00') == false ) return false;

		var total_contas_receber_a_vista = parseInt(frame_orcamento.getElementById("total_contas_receber_a_vista").value); // pega o numero de contas a receber a vista
		var total_contas_receber_a_prazo = parseInt(frame_orcamento.getElementById("total_contas_receber_a_prazo").value); // pega o numero de contas a receber a prazo

		var descricao_vista;
		var valor_vista;
		var sigla_vista;

		var descricao_prazo;
		var valor_prazo;
		var sigla_prazo;

		// percorre as contas a receber a Vista
		for (i=1; i<=total_contas_receber_a_vista; i++) {

			if ( frame_orcamento.getElementById("sigla_" + i) != null ) {
				descricao_vista = frame_orcamento.getElementById("descricao_vista_" + i).innerHTML;
				valor_vista = frame_orcamento.getElementById("valor_vista_" + i).innerHTML;
				sigla_vista = frame_orcamento.getElementById("sigla_" + i).value;

				// se for cartão de crédito ou débito, imprime TEF como forma de pagamento
				if ( (sigla_vista == "CC") || (sigla_vista == "CD")) descricao_vista = "TEF";
    			// se for Cheque, imprime CONSULTA CHEQUE como forma de pagamento
				else if (sigla_vista == "CH") descricao_vista = "CONSULTA CHEQUE";

				else descricao_vista = descricao_vista.substring(0,15);



				if ( EfetuaFormaPagamento(descricao_vista, valor_vista) == false ) return false;
			} // if

		} // for


		// percorre as contas a receber a Prazo
		for (i=1; i<=total_contas_receber_a_prazo; i++) {
			if ( frame_orcamento.getElementById("CR_descricao_" + i) != null ) {
				descricao_prazo = frame_orcamento.getElementById("CR_modo_prazo_" + i).innerHTML;
				valor_prazo = frame_orcamento.getElementById("TotalFinanciar").innerHTML;
				sigla_prazo = frame_orcamento.getElementById("modo_recebimento_a_prazo").value;

				// se for cartão de crédito ou débito, imprime TEF como forma de pagamento
				if ( (sigla_vista == "CC") || (sigla_vista == "CD")) descricao_prazo = "TEF";
				else if (sigla_vista == "CH") descricao_prazo = "CONSULTA CHEQUE";
				else descricao_prazo = descricao_prazo.substring(0,15);

				if ( EfetuaFormaPagamento(descricao_prazo, valor_prazo) == false ) return false;

				break; // faz apenas 1 execução, pois não percorre todas as contas a receber a prazo !
			} // if
		} // for
		//------------------------------------------------

    // ------>>> termina o fechamento
    if ( Termina_Fechamento_ECF () == false ) return false;
    //------------------------------------------------

		return true;
	}
	//--------------------------------------------------------------------------


	// Função termina o fechamento do cupom fiscal
	function Termina_Fechamento_ECF () {

		// identifica o frame
		var frame_orcamento = document;

		// ------>>> Termina o Fechamento do Cupom

		var linha_0 = 'Numero da Venda: ' + frame_orcamento.getElementById("idorcamentoFormatado").value + '\n';
		linha_0 += '\n';

		var linha_1 = frame_orcamento.getElementById("dados_cliente_linha_1").value;
		if (linha_1 != '') linha_1 += '\n';
		var linha_2 = frame_orcamento.getElementById("dados_cliente_linha_2").value;
		if (linha_2 != '') linha_2 += '\n';
		var linha_3 = frame_orcamento.getElementById("dados_cliente_linha_3").value;
		if (linha_3 != '') linha_3 += '\n';
		var linha_4 = frame_orcamento.getElementById("dados_cliente_linha_4").value;
		if (linha_4 != '') linha_4 += '\n';
		var linha_5 = frame_orcamento.getElementById("dados_cliente_linha_5").value;
		if (linha_5 != '') linha_5 += '\n';
		var linha_6 = 'Obrigado, volte sempre !!!';

		var mensagem_final = linha_0 + linha_1 + linha_2 + linha_3 + linha_4 + linha_5 + linha_6;

		if ( TerminaFechamentoCupom( mensagem_final ) == false ) return false;
		//------------------------------------------------


		// inicia o processo de impressão do tef, caso exista
		xajax_Inicia_Processo_TEF_AJAX (frame_orcamento.getElementById("idorcamento").value, frame_orcamento.getElementById("ordemECF").value);
		//------------------------------------------------

		return true;
	}
	//--------------------------------------------------------------------------


	// Função termina o fechamento do cupom fiscal
	function Imprime_TEF_ECF () {

		// identifica o frame
		var frame_orcamento = document;

		// ------>>> Finaliza modo TEF, caso tenha usado TEF
		if ( (frame_orcamento.getElementById("usaTEF").value != "0") && (frame_orcamento.getElementById("tef_caminho").value != "") ) {

			if ( (frame_orcamento.getElementById("usaTEF").value == "1") || (frame_orcamento.getElementById("usaTEF").value == "2") ) {
				if ( FinalizaModuloTEF() == false ) return false;
			}

		}
		// não usa tef
		else {

			// ------>>> imprime o comprovante de tef, se necessário
			if ( Finaliza_TEF_ECF () == false ) return false;
			//------------------------------------------------

		}
		//------------------------------------------------

		return true;
	}
	//--------------------------------------------------------------------------


	// Função termina o fechamento do cupom fiscal
	function Finaliza_TEF_ECF () {

		// identifica o frame
		var frame_orcamento = document;

		// faz o submit no formulário para gravar os dados no banco
    	frame_orcamento.getElementById("for_orcamento").submit();

		return true;
	}
	//--------------------------------------------------------------------------



	// Função que Gera o relatório do Sintegra
	function Gera_Relatorio_Sintegra () {
		//alert(' Gera_Relatorio_Sintegra ');
		// identifica o frame
		var frame_orcamento = document;

		var	cMes         = frame_orcamento.getElementById("mes").value;
		var	cAno         = frame_orcamento.getElementById("ano").value;
		var	cRazaoSocial = frame_orcamento.getElementById("razao_social").value;
		var	cEndereco    = frame_orcamento.getElementById("endereco").value;
		var	cNumero      = frame_orcamento.getElementById("numero").value;
		var	cComplemento = frame_orcamento.getElementById("complemento").value;
		var	cBairro      = frame_orcamento.getElementById("nome_bairro").value;
		var	cCidade      = frame_orcamento.getElementById("nome_cidade").value;
		var	cCEP         = frame_orcamento.getElementById("cep").value;
		var	cTelefone    = frame_orcamento.getElementById("telefone_filial").value;
		var	cFax         = frame_orcamento.getElementById("fax_filial").value;
		var	cContato     = 'Contato'; 
		var cArquivo     = 'SINTEGRA_' + cMes + '_' + cAno + '.TXT';

		/*
		alert('arquivo ' + cArquivo);
		alert('mes ' + cMes);
		alert('ano ' + cAno);
		alert('razao ' + cRazaoSocial);
		alert('endereco ' + cEndereco);
		alert('numero ' + cNumero);
		alert('complemento ' + cComplemento);
		alert('bairro ' + cBairro);
		alert('cidade ' + cCidade);
		alert('cep ' + cCEP);
		alert('telefone ' + cTelefone);
		alert('fax ' + cFax);
		*/

		iRetorno = BemaWeb.RelatorioSintegraMFD(63, cArquivo, cMes, cAno, cRazaoSocial, cEndereco, cNumero, cComplemento, cBairro, cCidade, cCEP, cTelefone, cFax, cContato); 
		
		if (CheckParameter(iRetorno) == false) return false;
		return true;
	}
	//--------------------------------------------------------------------------


	// Função que busca o número de série da impressora
	function NumeroSerie(){
		//alert(' Numero Serie ');
		// identifica o frame
		var frame_orcamento = document;
		frame_orcamento.getElementById("serie_ecf").value = RecuperaNumeroSerie();
		return true;
	}
	//--------------------------------------------------------------------------

  // Função que busca o número de série da impressora e compara com o número de serie de abertura do arquivo
  function VerificaNumeroSerie(){
    // alert(' Numero Serie ');
    // identifica o frame
    var frame_orcamento = document;
    
    var numero_serie = RecuperaNumeroSerie();
        
    if(frame_orcamento.getElementById("serie_ecf").value == numero_serie)
    {
      return true;
    }
    else
    {
      alert("A impressora atual não confere com a impressora que abriu o cupom ou está desligada.");
      return false;
    }
  }
  //--------------------------------------------------------------------------


	// Função que verifica se a impressora está ligada
	function Impressora_Ligada(){
		//alert(' Impressora Ligada ');
		iRetorno = BemaWeb.VerificaImpressoraLigada();

		if (iRetorno == 1) return true;
		else return false;
	}
  //--------------------------------------------------------------------------
  
  
	// Função que gera uma leitura X
	function Gera_LeituraX(){
		//alert(' leitura X ');
		var frame_orcamento = document;
		var travar = frame_orcamento.getElementById("travar_teclado").value;

		// Trava o teclado / mouse
		if (travar == 1) Inicia_Modo_TEF();

		iRetorno = BemaWeb.LeituraX();
		
		// Destrava o teclado / mouse
		if (travar == 1) Finaliza_Modo_TEF();

		if (CheckParameter(iRetorno) == false) return false;
		return true;
	}
	//--------------------------------------------------------------------------

	// Função que gera uma LMF por data
	function Gera_LMF_Data(){
		//alert(' leitura LMF por data ');
		// identifica o frame
		var frame_orcamento = document;
		var data_inicial = frame_orcamento.getElementById("data_vencimento_de").value;
		var data_final = frame_orcamento.getElementById("data_vencimento_ate").value;

		iRetorno = BemaWeb.LeituraMemoriaFiscalData(data_inicial, data_final);
		if (CheckParameter(iRetorno) == false) return false;
		return true;
	}
	//--------------------------------------------------------------------------

	// Função que gera uma LMF por reducao
	function Gera_LMF_Reducao(){
		//alert(' leitura LMF por reducao ');
		// identifica o frame
		var frame_orcamento = document;
		var reducao_inicial = frame_orcamento.getElementById("reducao_inicial").value;
		var reducao_final = frame_orcamento.getElementById("reducao_final").value;

		iRetorno = BemaWeb.LeituraMemoriaFiscalReducao(reducao_inicial, reducao_final);
		if (CheckParameter(iRetorno) == false) return false;
		return true;
	}
	//--------------------------------------------------------------------------

	// Função que inicia o modo TEF e trava o teclado / mouse
	function Inicia_Modo_TEF(){
		//alert(' Inicia_Modo_TEF ');
		iRetorno = BemaWeb.IniciaModoTEF();
		return true;
	}
	//--------------------------------------------------------------------------


	// Função que finaliza o modo TEF e destrava o teclado / mouse
	function Finaliza_Modo_TEF(){
		//alert(' Finaliza_Modo_TEF ');
		iRetorno = BemaWeb.FinalizaModoTEF();
		return true;
	}
	//--------------------------------------------------------------------------
  

  // Função que gera um suprimento
  function Suprimento(){
		//alert(' Suprimento ');
    var frame = document;
    var valor = frame.getElementById("valor_suprimento").value;
    var tipo = frame.getElementById("tipo_suprimento").value;  
  
    if (tipo)
      iRetorno = BemaWeb.Suprimento(valor,tipo);
    else 
      iRetorno = BemaWeb.Suprimento(valor,"");

    if (CheckParameter(iRetorno) == false) return false;

    return true;
  }
  //--------------------------------------------------------------------------
  
 
  // Função que gera uma sangria
  function Sangria(){
		//alert(' Sangria ');
    var frame = document;
    var valor = frame.getElementById("valor_sangria").value;

    iRetorno = BemaWeb.Sangria(valor);
    
    if (CheckParameter(iRetorno) == false) return false;
    return true;
  }  
  //--------------------------------------------------------------------------

 
  // Função que faz a redução z na ECF
  function ReducaoZ(){
    //alert(' Reducao Z ');
    iRetorno = BemaWeb.ReducaoZ("","");
    
    if (CheckParameter(iRetorno) == false) return false;
    return true;
  }  
  //--------------------------------------------------------------------------

  // Função que verifica se o cupom que é para ser cancelado é o ultimo cupom emitido
  function Verifica_Cancelamento_Cupom(){

    var frame = document;
    var numero_nota = frame.getElementById("nota").value;
    
    var queda_energia = frame.getElementById("semestoque").value;
    
    if (queda_energia == 1) {
    	var numero_cupom = RecuperaNumeroCupom();
    	frame.getElementById("numnumeroNota").value = numero_cupom;
    	
			iRetorno = BemaWeb.CancelaCupom();
			if (CheckParameter(iRetorno) == false) return false;

			frame.getElementById('for_orcamento').submit();
    }
    else {
      if (VerificaNumeroSerie() == false) return false;
      
			var numero_cupom = RecuperaNumeroCupom();
        
			if (numero_nota == numero_cupom) {    
				iRetorno = BemaWeb.CancelaCupom();
				if (CheckParameter(iRetorno) == false) return false;  

				frame.getElementById('for_orcamento').submit();
			}
			else
				alert("Não foi possível efetuar o cancelamento deste cupom, pois não é o último cupom a ser emitido.");
    }

		return true;
	}  
  //--------------------------------------------------------------------------


	// Esconde a mensagem 030
	function EscondeMensagem030(){
   	document.getElementById('popup_mensagem030').style.display = 'none';
    return true;
	}
  //--------------------------------------------------------------------------

	// Mostra a mensagem 030
	function MostraMensagem030(mensagem) {
		//alert(mensagem + 'aaaa');
   	document.getElementById('popup_mensagem030').innerHTML = mensagem;
   	document.getElementById('popup_mensagem030').style.display = 'block';
    //setTimeout ("EscondeMensagem030()", 3000);
    return true;
	}
  //--------------------------------------------------------------------------


	// Função se foi feito alguma redução Z no dia
	function VerificaReducaoZnoDia(){
		//alert(' VerificaReducaoZnoDia ');
		// identifica o frame
		var frame_orcamento = document;
		frame_orcamento.getElementById("reducaoZ_efetuada").value = Verifica_Reducao_Z();
		return true;
	}
	//--------------------------------------------------------------------------



//-->
