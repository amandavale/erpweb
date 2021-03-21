<?php

	//inclusão de bibliotecas
	require_once("../common/lib/conf.inc.php");
	require_once("../common/lib/db.inc.php");
	require_once("../common/lib/auth.inc.php");
	require_once("../common/lib/form.inc.php");
	require_once("../common/lib/rotinas.inc.php");
	//require_once("../common/lib/fpdf/fpdf.php");


  require_once("../entidades/orcamento.php");
	require_once("../entidades/cfop.php"); 
	require_once("../entidades/cliente.php"); 
	require_once("../entidades/transportador.php"); 


	require('../common/lib/fpdf/WriteHTML.php');


	//class PDF extends FPDF {
	class PDF extends PDF_HTML {


		var $orientation = 'P'; // oritenção do documento
		var $unit = 'mm'; // unidade em mm
				
		var $largura_pagina = 211; // largura da pagina
		var $altura_pagina = 280; //altura da pagina
	
	
		// Cria o Cabeçalho da Pagina
		function Header() {
			$this->tMargin = 0;
		}
	
	
		// Cria o Rodapé da Pagina
		function Footer() {
		}
	
	
	
		// inicializa o documento
		function Inicializacao() {
	
			// informa as características
			$format = array($this->largura_pagina, $this->altura_pagina); // formato da pagina
			$this->FPDF($this->orientation, $this->unit, $format);
			$this->SetFont('Times', '', 10);
	
			// adiciona uma pagina
			$this->AddPage();
	
		}
	
	
	
	} // fim da classe



  // inicializa autenticação
  $auth = new auth();



  // verifica requisição de logout
  if($flags['action'] == "logout") {
    $auth->logout();
  }
  else {

    // inicializa vetor de erros
    $err = array();

    // verifica sessão
    if(!$auth->check_user()) {
      // verifica requisição de login
      if($_POST['usr_chk']) {
        // verifica login
        if(!$auth->login($_POST['usr_log'], $_POST['usr_sen'])) {
          $err = $auth->err;
        }
      }
      else {
        $err = $auth->err;
      }
    }

    // conteúdo
    if($auth->check_user()) {
      // verifica privilégios
      if(!$auth->check_priv($conf['priv'])) {
        $err = $auth->err;
      }
      else {
        // libera conteúdo
        $flags['okay'] = 1;

				//inicializa classe
	  		$orcamento = new orcamento();
				$cfop = new cfop();
				$cliente = new cliente();
				$transportador = new transportador();

        // inicializa banco de dados
        $db = new db();

        //incializa classe para validação de formulário
        $form = new form();
     		
				
				// cria o objeto PDF
				$pdf=new PDF();
				
				// Inicia o documento
				$pdf->Inicializacao();
				
				//busca detalhes da nota
				$info = $orcamento->getById($_GET['idorcamento']);

				// formata o numero da nota fiscal
				$numeroDaNota = "000000" . $info['numeroNota'];
				$info['numeroNotaFormatado'] = substr($numeroDaNota,strlen($numeroDaNota)-6,6);
				//---------------------------------------------------------------------------
	
				//$info['dados_adicionais'] = strip_tags($info['dados_adicionais']);
				//$_POST['litdados_adicionais'] = nl2br($_POST['litdados_adicionais']);
				//$info['dados_adicionais'] = "aaa /n aaa";

				if ($info['transportador_frete_por_conta'] == "E") {
					$info['transportador_frete_por_conta_cod'] = "1";
				}
				else if ($info['transportador_frete_por_conta'] == "D") {
					$info['transportador_frete_por_conta_cod'] = "2";
				}

				// busca o CFOP
				$info_cfop = $cfop->getById($info['idcfop']);

				// busca dados do cliente
				$info_dados_cliente = $cliente->BuscaDadosCliente($info['idcliente']);

				// busca os dados do transportador
				$info_transportador = $transportador->BuscaDadosTransportador($info['idtransportador']);

				// busca os produtos do orçamento
				$list_produtos = $orcamento->Seleciona_Produtos_Do_Orcamento($_GET['idorcamento']);

				
				// verifica se vai colcoar imagem de fundo ou não
				$com_imagem = $_GET['visualizar'];
				
				// versão de análise
				if ($com_imagem == 1) {
					// Escreve a imagem de fundo
					$pdf->Image("{$conf['addr']}/common/img/nf_deposito_2.JPG", 0, 0, 211, 280);
					
					$y_ajuste = 0;
					$x_ajuste = 2;
				}
				// versão de impressão
				else {
					$y_ajuste = -7;
					$x_ajuste = 0;
				}
				

				// imprime a marca Saída e o Numero da NF 
				$pdf->Text(136 + $x_ajuste, 15 + $y_ajuste, "X");  // Marca um X na Saída
				$pdf->Text(185 + $x_ajuste, 15 + $y_ajuste , "{$info['numeroNotaFormatado']}"); // Número da nota
				//---------------------------------------
				
				// imprime natureza da operação
				$pdf->Text(30 + $x_ajuste, 34 + $y_ajuste, "{$info_cfop['descricao_curta']}"); // Natureza da operação
				$pdf->Text(77 + $x_ajuste, 34 + $y_ajuste, "{$info_cfop['codigo']}"); // CFOP
				//---------------------------------------
				
				// imprime a primeira linha do Destinatario / Remetente
				$pdf->Text(29 + $x_ajuste, 44 + $y_ajuste, "{$info_dados_cliente['nome_cliente']}"); // Nome do Destinatário
				$pdf->Text(133 + $x_ajuste, 44 + $y_ajuste, "{$info_dados_cliente['cpf_cnpj']}"); // CNPJ / CPF
				$pdf->Text(183 + $x_ajuste, 44 + $y_ajuste, "{$info['datahoraCriacaoNF_D']}"); // Data da emissão
				//---------------------------------------
				
				// imprime a segunda linha do Destinatario / Remetente
				$pdf->Text(29 + $x_ajuste, 50 + $y_ajuste, "{$info_dados_cliente['logradouro']} {$info_dados_cliente['numero']}"); // Endereço
				$pdf->Text(115 + $x_ajuste, 50 + $y_ajuste, "{$info_dados_cliente['nome_bairro']}"); // Bairro
				$pdf->Text(155 + $x_ajuste, 50 + $y_ajuste, "{$info_dados_cliente['cep']}"); // CEP
				$pdf->Text(183 + $x_ajuste, 50 + $y_ajuste, "{$info['datahoraCriacaoNF_D']}"); // Data de Saída / Entrada
				//---------------------------------------
				
				
				// imprime a terceira linha do Destinatario / Remetente
				$pdf->Text(29 + $x_ajuste, 57 + $y_ajuste, "{$info_dados_cliente['nome_cidade']}"); // Município
				$pdf->Text(91 + $x_ajuste, 57 + $y_ajuste, "{$info_dados_cliente['telefone_cliente']}"); // Fone / Fax
				$pdf->Text(125 + $x_ajuste, 57 + $y_ajuste, "{$info_dados_cliente['sigla_estado']}"); // UF
				$pdf->Text(135 + $x_ajuste, 57 + $y_ajuste, "{$info_dados_cliente['inscricao_estadual_cliente']}"); // Inscrição estadual
				$pdf->Text(183 + $x_ajuste, 57 + $y_ajuste, "{$info['datahoraCriacaoNF_H']}"); // Hora de saída
				//---------------------------------------
				
				// Tabela de dados do produto
				$altura_inicial = 70;

				for ($i=0; $i<count($list_produtos); $i++) {
		
					// o preço utilizado é o preço que foi usado na venda. Não utiliza o preço atualizado dos produtos
					$preco = $list_produtos[$i]['preco_unitario_produto'];
					$desconto_produto = str_replace(",",".",$list_produtos[$i]["desconto_produto"]);
					$quantidade_produto = str_replace(",",".",$list_produtos[$i]["qtd_produto"]);
		
					$preco_produto_final = $preco * (1 - ($desconto_produto/100));
					$preco_produto_final = number_format($preco_produto_final,2,",","");
					$preco_produto_final = str_replace(",",".",$preco_produto_final);
		
					$total_produto_final = $preco_produto_final * $quantidade_produto;
		
					$preco_produto_final = number_format($preco_produto_final,2,",","");
					$quantidade_produto = number_format($quantidade_produto,2,",","");
					$total_produto_final = number_format($total_produto_final,2,",","");
					//---------------------------------------------------------------------
				
					$pdf->Text(28 + $x_ajuste, $altura_inicial + $y_ajuste, "{$list_produtos[$i]['idproduto']}"); // Código do produto
					$pdf->Text(36 + $x_ajuste, $altura_inicial + $y_ajuste, "{$list_produtos[$i]['descricao_produto']}"); // Descrição do produto
					$pdf->Text(129 + $x_ajuste, $altura_inicial + $y_ajuste, "{$list_produtos[$i]['cst_produto']}"); // CST
					$pdf->Text(136 + $x_ajuste, $altura_inicial + $y_ajuste, "{$list_produtos[$i]['sigla_unidade_venda']}"); // Unidade
					$pdf->Text(147 + $x_ajuste, $altura_inicial + $y_ajuste, "$quantidade_produto"); // Quantidade
					$pdf->Text(158 + $x_ajuste, $altura_inicial + $y_ajuste, "$preco_produto_final"); // Valor unitário
					$pdf->Text(180 + $x_ajuste, $altura_inicial + $y_ajuste, "$total_produto_final"); // Valor total
					$pdf->Text(201 + $x_ajuste, $altura_inicial + $y_ajuste, number_format($list_produtos[$i]['aliquota_icms_produto'], 0) ); // ICMS

					$altura_inicial += 5;
				} // fim do for
				//---------------------------------------
				
				
				
				// Imprime o Desconto
				$pdf->Text(160 + $x_ajuste, 180 + $y_ajuste, "Desconto.............. {$info['desconto']}"); // informativo
				//---------------------------------------
				
				
				// ********** CALCULO DO IMPOSTO ********
				
				// LINHA 1
				$pdf->Text(35 + $x_ajuste, 191 + $y_ajuste, "{$info['base_calculo_icms']}"); // Base de calculo do ICMS
				$pdf->Text(70 + $x_ajuste, 191 + $y_ajuste, "{$info['valor_icms']}"); // Valor do ICMS
				$pdf->Text(110 + $x_ajuste, 191 + $y_ajuste, "{$info['base_calc_icms_sub']}"); // Base de calculo do ICMS Substituição
				$pdf->Text(155 + $x_ajuste, 191 + $y_ajuste, "{$info['valor_icms_sub']}"); // Valor do ICMS Substituição
				$pdf->Text(185 + $x_ajuste, 191 + $y_ajuste, "{$info['valor_total_produtos']}"); // Valor total dos produtos
				//---------------------------------------
				
				// LINHA 2
				$pdf->Text(35 + $x_ajuste, 197 + $y_ajuste, "{$info['frete']}"); // Valor do Frete
				$pdf->Text(70 + $x_ajuste, 197 + $y_ajuste, "{$info['valor_seguro']}"); // Valor do Seguro
				$pdf->Text(110 + $x_ajuste, 197 + $y_ajuste, "{$info['outras_despesas']}"); // Outras Despesas Acessorias
				$pdf->Text(155 + $x_ajuste, 197 + $y_ajuste, "{$info['valor_total_ipi']}"); // Valor total do IPI
				$pdf->Text(185 + $x_ajuste, 197 + $y_ajuste, "{$info['valor_total_nota']}"); // Valor total da nota
				//---------------------------------------
				
				// *************** TRANSPORTADOR / VOLUME TRANSPORTADOS ********************
				
				// LINHA 1
				$pdf->Text(28 + $x_ajuste, 207 + $y_ajuste, "{$info_transportador['nome_transportador']}"); // Nome / Razão social
				$pdf->Text(130 + $x_ajuste, 207 + $y_ajuste, "{$info['transportador_frete_por_conta_cod']}"); // Frete p/ conta
				$pdf->Text(137 + $x_ajuste, 207 + $y_ajuste, "{$info['transportador_placa_veiculo']}"); // Placa do veículo
				$pdf->Text(157 + $x_ajuste, 207 + $y_ajuste, "{$info_transportador['sigla_estado']}"); // UF
				$pdf->Text(170 + $x_ajuste, 207 + $y_ajuste, "{$info_transportador['cpf_cnpj']}"); // CNPJ / CPF
				//---------------------------------------
				
				// LINHA 2
				$pdf->Text(28 + $x_ajuste, 213 + $y_ajuste, "{$info_transportador['logradouro']} {$info_transportador['numero']} {$info_transportador['nome_bairro']}"); // Endereço
				$pdf->Text(110 + $x_ajuste, 213 + $y_ajuste, "{$info_transportador['nome_cidade']}"); // Municipio
				$pdf->Text(157 + $x_ajuste, 213 + $y_ajuste, "{$info_transportador['sigla_estado']}"); // UF
				$pdf->Text(170 + $x_ajuste, 213 + $y_ajuste, "{$info_transportador['instricao_estadual_transportador']}"); // Inscrição estadual
				//---------------------------------------
				
				// LINHA 3
				$pdf->Text(30 + $x_ajuste, 219 + $y_ajuste, "{$info['transportador_quantidade']}"); // Quantidade
				$pdf->Text(60 + $x_ajuste, 219 + $y_ajuste, "{$info['transportador_especie']}"); // Especie
				$pdf->Text(90 + $x_ajuste, 219 + $y_ajuste, "{$info['transportador_marca']}"); // Marca
				$pdf->Text(125 + $x_ajuste, 219 + $y_ajuste, "{$info['transportador_numero']}"); // Numero
				$pdf->Text(160 + $x_ajuste, 219 + $y_ajuste, "{$info['transportador_peso_bruto']}"); // Peso bruto
				$pdf->Text(195 + $x_ajuste, 219 + $y_ajuste, "{$info['transportador_peso_liquido']}"); // Peso Liquido
				//---------------------------------------
				
				// DADOS ADICIONAIS
				//$pdf->Text(28 + $x_ajuste, 230 + $y_ajuste, "{$info['dados_adicionais']}"); // DADOS ADICIONAIS
				$array_dados_adicionais = split("<br />", "{$info['dados_adicionais']}");
				$altura_dados_adicionais = 230 + $y_ajuste;
				for ($i=0; $i<count($array_dados_adicionais); $i++) {
					$pdf->SetXY(28 + $x_ajuste, $altura_dados_adicionais);
					$pdf->WriteHTML("$array_dados_adicionais[$i]");
					$altura_dados_adicionais += 5;
				}
				//---------------------------------------
				
				// NUMERO DA NOTA FISCAL
				$pdf->Text(184 + $x_ajuste, 270 + $y_ajuste, "{$info['numeroNotaFormatado']}"); // NUMERO DA NOTA FISCAL
				//---------------------------------------
				
				
				// Mostra o PDF
				$pdf->Output();

      }
      
  	}
  	
	}




?>
