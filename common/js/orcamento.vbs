' Definição das variáveis globais do VB Script
principal_arquivo = ""
principal_funcao = ""
principal_tempo = 0
principal_contador = 0
principal_criou_arquivo = 0
'---------------------------------------------------

' verifica criação do arquivo
' 1) nome do arquivo a ser verificado
' 2) tempo máximo para verificar em segundos
' 3) função a ser chamada quando o arquivo tiver sido criado
function VerificaCriacaoDoArquivo (Arquivo, TempoSeg, FuncaoProx)
	principal_arquivo = Arquivo
 	principal_funcao = FuncaoProx  
 	principal_tempo = TempoSeg
 	principal_contador = 0
	principal_criou_arquivo = 0

 	IniciaVerificacaoArquivo()

	VerificaCriacaoDoArquivo = true
end function
'---------------------------------------------------

' Inicia a verificação da criação do arquivo com base nas variáveis definidas
function IniciaVerificacaoArquivo()

	set fso = createObject("Scripting.FileSystemObject")

	principal_contador = principal_contador + 1

	' se o arquivo já foi criado, executa a próxima função
	if fso.FileExists(principal_arquivo) and principal_arquivo <> "" then
		principal_criou_arquivo = 1
		principal_contador = 0
		iTimeoutID = setTimeOut(principal_funcao, 0, "VBScript")
  
	elseif principal_contador > principal_tempo then
		principal_criou_arquivo = 0
		principal_contador = 0
		iTimeoutID = setTimeOut(principal_funcao, 0, "VBScript")
 
	else
		iTimeoutID = setTimeOut("IniciaVerificacaoArquivo", 1000, "VBScript")
	end if

	IniciaVerificacaoArquivo = true
end function
'---------------------------------------------------

' Deleta um arquivo
function DeletaArquivo(arquivo)
	set fso = createObject("Scripting.FileSystemObject")
	if fso.FileExists(arquivo) then
		fso.DeleteFile(arquivo) 
	end if
end function
'---------------------------------------------------


' Verifica estado da impressora
function VerificaEstadoImpressora(travar_perifericos)
	Dim ACK, St1, St2, sMsg, fim_papel
	iRetorno = BemaWeb.RetornoImpressora( ACK, St1, St2 )
	' MsgBox ACK & " , " & St1 & " , " & St2

	sMsg = ""
	fim_papel = 0

	' Verifica o bit ST1
	if St1 >= 128 then
		St1 = St1 - 128
		fim_papel = 1
		'sMsg = "Fim de Papel"
	end if
	if St1  >=  64  then
		St1 = St1 - 64
		' Pouco Papel
	end if
	if  St1 >=  32   then
		St1 = St1 - 32
		sMsg = "Erro no Relógio"
	end if
	if  St1 >=  16   then
		St1 = St1 - 16
		sMsg = "Impressora em Erro"
	end if
	if  St1 >=  8   then
		St1 = St1 - 8
		sMsg = "Comando não iniciado com ESC"
	end if
	if  St1 >=  4   then
		St1 = St1 - 4
		' Comando Inexistente
	end if
	if  St1 >=  2   then
		St1 = St1 - 2
		sMsg = "Cupom Aberto"
	end if
	if  St1 >=  1   then
		St1 = St1 - 1
		sMsg = "Número de Parâmetro(s) Inválido(s)"
	end if 

	' Verifica o bit ST2
	if St2  >=  128 then
		St2 = St2 - 128
		sMsg = "Tipo de Parâmetro de Comando Inválido"
	End if
	if  St2 >=  64  then            
		St2 = St2 - 64
		sMsg = "Memória Fiscal Lotada"
	end if
	if  St2 >=  32  then             
		St2 = St2 - 32
		sMsg = "Erro na Memória RAM"
	end if
	if  St2 >=  16  then             
		St2 = St2 - 16
		sMsg = "Alíquota Não Programada"
	end if
	if  St2 >=  8  then             
		St2 = St2 - 8
		sMsg = "Capacidade de Alíquotas Lotada"
	end if
	if  St2 >=  4  then      
		St2 = St2 - 4
		sMsg = "Cancelamento Não Permitido"
	end if
	if  St2 >=  2  then             
		St2 = St2 - 2
		sMsg = "CNPJ/IE do Proprietário Não Programado"
	end if
	if  St2  >= 1  then           
		St2 = St2 - 1
		' Comando Não Executado
	end if

	if ( (sMsg <> "") or (fim_papel = 1) ) then
		' Destrava o teclado / mouse
		if travar_perifericos = 1 then Finaliza_Modo_TEF()

		if sMsg <> "" then
			MsgBox sMsg
		end if
		
		VerificaEstadoImpressora = false
		Exit Function
	end if

	VerificaEstadoImpressora = true
end function
'---------------------------------------------------

'Funcao que retorna a data e a hora do ultimo documento fiscal
function Retorna_Data_Hora()
  'MsgBox "Retorna Data e Hora"
  dim datas
  
  iRetorno = BemaWeb.DataHoraImpressora(datas, hora)
  document.for_orcamento.data_ecf.value = datas
  document.for_orcamento.hora_ecf.value = hora
  
  Retorna_Data_Hora = true
end function
'---------------------------------------------------

' Função que recupera o número de série da ECF
function RecuperaNumeroSerie()
	Dim cNumeroSerie
	iRetorno = BemaWeb.NumeroSerie( cNumeroSerie )
	RecuperaNumeroSerie = cNumeroSerie
end function
'---------------------------------------------------

' Função que recupera o número do Cupom Fiscal
function RecuperaNumeroCupom()
	Dim cNumeroCupom
	iRetorno = BemaWeb.NumeroCupom( cNumeroCupom )
	RecuperaNumeroCupom = cNumeroCupom
end function
'---------------------------------------------------

' Função que verifica se foi feito uma redução Z naquele dia
function Verifica_Reducao_Z()
  dim FlagFiscal

  iRetorno = BemaWeb.FlagsFiscais(FlagFiscal)

  if FlagFiscal >= 32 then
    FlagFiscal = FlagFiscal - 32
  end if

	' foi feito a redução Z no dia
  if FlagFiscal  >=  8  then
    FlagFiscal = FlagFiscal - 8
    Verifica_Reducao_Z = 1
	' não foi feito a redução Z no dia
	else
		Verifica_Reducao_Z = 0
  end if

end function
'---------------------------------------------------


' Função que inicia o TEF
function IniciaModuloTEF(tef_caminho)

	Dim ret, travar, repetir_msg

	travar = 0
	repetir_msg = 0

	' verifica se o tef está ativo
	ret = Verifica_TEF_Ativo (tef_caminho, travar, repetir_msg)
	
	if (ret= true)then
	
		if ( CriaArquivoIntPos001() = false ) then
			IniciaModuloTEF = false
			Exit Function
		end if
		
	else
	
		IniciaModuloTEF = false
		Exit Function
		
	end if

	IniciaModuloTEF = true
end function
'---------------------------------------------------


' Função que finaliza o TEF
function FinalizacaoModuloTEF()
	'MsgBox "FinalizacaoModuloTEF"

	Dim tipo_transacao, tef_diretorio, travar, campo028, texto, campo030
	
	' variável que define o diretório do tef
	tef_diretorio = document.for_orcamento.tef_caminho.value
	
	' se for uma operação de GARANTIA DE CHEQUE, não deixa imprimir
	tipo_transacao = document.for_orcamento.tipoTransacao.value
	'MsgBox "tipo_transacao " & tipo_transacao
	
	travar = document.for_orcamento.travar_teclado.value


	' abre o arquivo intpos.001 para verificar se tem que ter impressão
	set fso = CreateObject("Scripting.FileSystemObject")
	set arqtxt = fso.OpenTextFile(tef_diretorio & "resp\intpos.001")

	campo028 = 0
	while not arqtxt.AtEndOfStream
		texto = arqtxt.ReadLine
		
		if ( mid (texto, 1, 7) = "028-000" ) then
			campo028 = CInt( trim( mid(texto, 10) ) )

		end if

		if ( mid (texto, 1, 7) = "030-000" ) then
			campo030 = trim( mid(texto, 10) )

		end if
		
	wend
	arqtxt.Close
	' --------------------------------------------------------


	' é GARANTIA DE CHEQUE, logo não deixa imprimir nada, já finaliza o processo
	' campo28 = 0  => sem impressão de cupom
	if (tipo_transacao = "71") or (campo028 = 0) then
		MsgBox(campo030)
'		if ( ConfirmacaoImpressaoTEF() = false ) then
'			FinalizacaoModuloTEF = false
'			Exit Function
'		end if

	' não é GARANTIA DE CHEQUE
	else

		if ( ImprimeTEF() = false ) then 
			FinalizacaoModuloTEF = false
			Exit Function
		end if

		' verifica se o tef está ativo
		ret = Verifica_TEF_Ativo (tef_diretorio, travar,1)

		if ( EnviaConfirmacaoImpressao() = false ) then 
			FinalizacaoModuloTEF = false
			Exit Function
		end if

	end if

	FinalizacaoModuloTEF = true

end function
'---------------------------------------------------


' Função que envia a confirmação da transação
function EnviaConfirmacaoImpressao()

	Dim tef_diretorio, identificacao, documento_fiscal_vinculado, rede, nsu, finalizacao, time1, time2, cont

	' variável que define o diretório do tef
	tef_diretorio = document.for_orcamento.tef_caminho.value

	set fso = CreateObject("Scripting.FileSystemObject")

	set arqtxt = fso.OpenTextFile(tef_diretorio & "resp\intpos.001")
	set arqtxt2 = fso.CreateTextFile(tef_diretorio & "req\intpostemp.tmp", True)
		
	' monta o arquivo de confirmação da impressão
	while not arqtxt.AtEndOfStream
		texto = arqtxt.ReadLine

		if ( mid (texto, 1, 7) = "001-000" ) then
			identificacao = trim( mid(texto, 10) )
		elseif ( mid (texto, 1, 7) = "002-000" ) then
			documento_fiscal_vinculado = trim( mid(texto, 10) )
		elseif ( mid (texto, 1, 7) = "010-000" ) then
			rede = trim( mid(texto, 10) )
		elseif ( mid (texto, 1, 7) = "012-000" ) then
			nsu = trim( mid(texto, 10) )
		elseif ( mid (texto, 1, 7) = "027-000" ) then
			finalizacao = trim( mid(texto, 10) )
		end if
	wend

	arqtxt.Close

	With arqtxt2
	  .WriteLine ("000-000 = CNF")
	  .WriteLine ("001-000 = " & identificacao)
	  .WriteLine ("002-000 = " & documento_fiscal_vinculado)
	  .WriteLine ("010-000 = " & rede)
	  .WriteLine ("012-000 = " & nsu)
	  .WriteLine ("027-000 = " & finalizacao)
	  .Write ("999-999 = 0")
	  .Close
	End With


	ret = DeletaArquivo(tef_diretorio & "req\intpos.001")

	' da um tempo para nao dar erro na tansacao
	time1 = Time
	time2 = Time
	cont = 0
	while ( Datediff("s",time1, time2) < 1 )
		time2 = Time
		cont = cont + 1
		cont = cont + 9999 + 9999 + 9999 + 9999 + 9999 + 9999 + 9999 + 9999
		cont = cont - 9999 - 9999 - 9999 - 9999 - 9999 - 9999 - 9999 - 9999
	wend
	'Msgbox cont

	fso.MoveFile tef_diretorio & "req\intpostemp.tmp", tef_diretorio & "req\intpos.001"


	' deleta o arquivo de retorno do gerenciador padrão
	ret = DeletaArquivo(tef_diretorio & "resp\intpos.001")

	' deleta o arquivo de backup
	ret = DeletaArquivo(tef_diretorio & "intpos001_bk.txt")


	' A aplicação ficará esperando a criação do arquivo intpos.sts
	' verifica INFINITAMENTE se o Gerenciador padrão retornou o arquivo sts
	ret = VerificaCriacaoDoArquivo (tef_diretorio & "resp\intpos.sts", 86400, "ConfirmacaoImpressaoTEF")	

	EnviaConfirmacaoImpressao = true

end function
'---------------------------------------------------


' Função que envia a confirmação da transação
function ConfirmacaoImpressaoTEF()

	Dim tef_diretorio, travar

	' variável que define o diretório do tef
	tef_diretorio = document.for_orcamento.tef_caminho.value

	travar = document.for_orcamento.travar_teclado.value

	' verifica se o tef está ativo
	'ret = Verifica_TEF_Ativo (tef_diretorio, travar)

	' deleta o arquivo de backup
	ret = DeletaArquivo(tef_diretorio & "intpos001_bk.txt")

	'esconde a mensagem 030
	ret = VerificaRetornoArquivoIntPos001()

	' deleta o arquivo de retorno do gerenciador padrão
	ret = DeletaArquivo(tef_diretorio & "resp\intpos.001")
	ret = DeletaArquivo(tef_diretorio & "resp\intpos.sts")

	

	' Destrava o teclado / mouse
	if travar = 1 then Finaliza_Modo_TEF()

	' finaliza o processo
	Finaliza_TEF_ECF ()

	ConfirmacaoImpressaoTEF = true

end function
'---------------------------------------------------



' Função que imprime 2 vias do cupom da transação de TEF
function ImprimeTEF()
	'MsgBox "ImprimeTEF"
	Dim tef_diretorio, conteudo29, documento_fiscal_vinculado, valor, modo_recebimento_tef, resp, resp2, via, ACK, ST1, ST2, PoucoPapel, SemPapel, travar, passo, linhas_em_branco, usa_cupom_vinculado, iniciouImpressaoVinculado

	
	' variável que define o diretório do tef
	tef_diretorio = document.for_orcamento.tef_caminho.value


	'ret = Verifica_TEF_Ativo (tef_diretorio, travar)
	'MsgBox("tef ativo" & ret)
	
	via = 0

	documento_fiscal_vinculado = document.for_orcamento.ordemECF.value
	valor	= document.for_orcamento.valorTEF_bk.value
	modo_recebimento_tef = document.for_orcamento.modo_recebimento_tef.value
	travar = document.for_orcamento.travar_teclado.value

	set fso = CreateObject("Scripting.FileSystemObject")


	set arqtxt = fso.OpenTextFile(tef_diretorio & "resp\intpos.001")

	'MsgBox modo_recebimento_tef & " " & valor & " " & documento_fiscal_vinculado

	' Trava o teclado / mouse
	if travar = 1 then Inicia_Modo_TEF()

	' inicialmente informa que é para usar cupom vinculado
	usa_cupom_vinculado = 1

	' fecha algum relatório gerencial que tenha ficado pendente
	iRetorno = BemaWeb.FechaRelatorioGerencial()

	' primeiro passo
	passo = 1

	while passo < 5

	  ' -----------------------------------------------
	  ' PASSO 1: ABRIR O RELATORIO GERENCIAL
		' -----------------------------------------------
		if passo = 1 then

			' linhas em branco
			linhas_em_branco = 0

			'fecha o arquivo
			arqtxt.Close

			set arqtxt = fso.OpenTextFile(tef_diretorio & "resp\intpos.001")
			via = via + 1
			
			if usa_cupom_vinculado = 1 then

				if via = 1 then
					' Abre o comprovante não fiscal vinculado
'MsgBox("AbreComprovanteNaoFiscal")
				  iRetorno = BemaWeb.AbreComprovanteNaoFiscalVinculado(modo_recebimento_tef, valor, documento_fiscal_vinculado)

				else
				  ' Imprime a segunda via
					'iRetorno = BemaWeb.UsaComprovanteNaoFiscalVinculado("Via " & via)
					iRetorno = 1
				end if
				
			else
			  ' Abre o relatório gerencial
				'iRetorno = BemaWeb.RelatorioGerencialTEF("Via " & via)
				iRetorno = 1
			end if

	  ' -----------------------------------------------
	  ' PASSO 2: IMPRIMIR AS LINHAS DO TEF
		' -----------------------------------------------
		elseif passo = 2 then

			' captura as linhas 029-yyy do arquivo
			texto = arqtxt.ReadLine
			if ( mid (texto, 1, 3) = "029" ) then

				conteudo29 = mid(texto, 12, len(texto)-12)
				if conteudo29 = "" then
					conteudo29 = " "
				end if

				if usa_cupom_vinculado = 1 then
					' Imprime atraves do cumpom não fiscal vinculado
	 				iRetorno = BemaWeb.UsaComprovanteNaoFiscalVinculado(conteudo29)
	 				if (  (iniciouImpressaoVinculado <> 1) and (iRetorno = 1)  ) then
					 iniciouImpressaoVinculado = 1
	  				end if
	  				
				else
					' Imprime atraves do relatório gerencial
	 				iRetorno = BemaWeb.RelatorioGerencialTEF(conteudo29)
				end if

			end if


	  ' -----------------------------------------------
	  ' PASSO 3: SOLTA LINHAS EM BRANCO
		' -----------------------------------------------
		elseif passo = 3 then

			linhas_em_branco = linhas_em_branco + 1

			if usa_cupom_vinculado = 1 then

				iRetorno = BemaWeb.UsaComprovanteNaoFiscalVinculado(" ")

				if linhas_em_branco = 5 then
					' corta o papel
					'iRetorno = BemaWeb.AcionaGuilhotinaMFD(0)  ESTE COMANDO NAO FUNCIONA NA OCX
					iRetorno = BemaWeb.UsaComprovanteNaoFiscalVinculado(chr(27) & chr(109))

					iRetorno = BemaWeb.UsaComprovanteNaoFiscalVinculado(" ")
					iRetorno = BemaWeb.UsaComprovanteNaoFiscalVinculado(" ")
				end if
				
			else

				iRetorno = BemaWeb.RelatorioGerencialTEF(" ")

				if linhas_em_branco = 5 then
					' corta o papel
					'iRetorno = BemaWeb.AcionaGuilhotinaMFD(0)  ESTE COMANDO NAO FUNCIONA NA OCX
					iRetorno = BemaWeb.RelatorioGerencialTEF(chr(27) & chr(109))

					iRetorno = BemaWeb.RelatorioGerencialTEF(" ")
					iRetorno = BemaWeb.RelatorioGerencialTEF(" ")
				end if

			end if

	  ' -----------------------------------------------
	  ' PASSO 4: FECHAR O RELATORIO GERENCIAL
		' -----------------------------------------------
		elseif passo = 4 then

			if usa_cupom_vinculado = 1 then
				' Fecha o comprovante não fiscal vinculado
				iRetorno = BemaWeb.FechaComprovanteNaoFiscalVinculado()
				' não usa mais o cupom vinculado
				
				if (iniciouImpressaoVinculado = 1) then
					iRetorno = BemaWeb.FechaComprovanteNaoFiscalVinculado()
 					usa_cupom_vinculado = 0
 				end if
				'usa_cupom_vinculado = 0
			else
			  ' Fecha o relatório gerencial
				iRetorno = BemaWeb.FechaRelatorioGerencial()
			end if
			
	  ' -----------------------------------------------
	  ' PASSO 5: FIM
		' -----------------------------------------------
		elseif passo = 5 then
			' FIM
	  end if


	  ' -----------------------------------------------
		' ANALISA SE A IMPRESSÃO FOI CORRETA
	  ' -----------------------------------------------

		' verifica se acabou o papel

		iRetorno2 = BemaWeb.RetornoImpressora(ACK, ST1, ST2)
		'verifica se acabou o papel
		if (ST1 >= 128) then
			SemPapel = true
			ST1 = ST1 - 128
			usa_cupom_vinculado = 0
		else
			SemPapel = false
		end if

		if (ST1 >= 64) then
			PoucoPapel = true
			ST1 = ST1 - 64
			'MsgBox "Pouco papel"
		else
			PoucoPapel = false
		end if


		if ( ((CheckParameter(iRetorno) = false) and (PoucoPapel = false)) or (SemPapel = true) ) then

			' Destrava o teclado / mouse
			if travar = 1 then Finaliza_Modo_TEF()

			resp = MsgBox("Impressora não responde. Tentar novamente ?", 32+4, "")

			if resp = VBYes then

				Inicia_Modo_TEF()
				
				' ------------------------
				' NÃO É FEITO MAIS A LEITURA X, POIS DE ACORDO COM A SEVEN-PDV, NAS IMPRESSORAS TÉRMICAS NÃO
				' PRECISA EXECUTAR ESSE COMANDO
				' ------------------------
				'faz a leitura X
				'while Gera_LeituraX() = false

				'verifica se a impressora está ligada
				while Impressora_Ligada() = false

					Finaliza_Modo_TEF()
					
					resp2 = MsgBox("Impressora não responde. Tentar novamente ?", 32+4, "")
					
					Inicia_Modo_TEF()
					
                    
					if resp2 = VBNo then

						Finaliza_Modo_TEF()
						
						'fecha o arquivo
						arqtxt.Close

						'cancela a transação
						CancelaTransacao()

						ImprimeTEF = false

						
						
						Exit Function
					end if

					
					
				wend
				
				
				' fecha o relatório gerencial corrente e volta para a impressão do primeiro relatório gerencial
				passo = 4
				via = 0

			elseif resp = VBNo then
				'fecha o arquivo
				arqtxt.Close
				
				'cancela a transação
				CancelaTransacao()

				ImprimeTEF = false
				Exit Function
			end if

			' Trava o teclado / mouse
			if travar = 1 then Inicia_Modo_TEF()

		' NÃO OCORREU ERRO DE IMPRESSÃO
		else

			' faz o controle de qual será o próximo passo
			if passo = 1 then ' abrir o relatorio gerencial
			  passo = 2 ' imprime tef
			elseif passo = 2 then ' imprime tef
			  if arqtxt.AtEndOfStream then ' se ja imprimiu tudo
					if via = 1 then ' se imprimiu a via 1
			  		passo = 3 ' imprime a via 2
					elseif via = 2 then ' se imprimiu a via 2
						passo = 4 ' fecha o relatorio gerencial
					end if
			  end if

			elseif passo = 3 then ' imprime linhas em branco
			  if linhas_em_branco = 5 then
			    passo = 1 ' volta a imprimir o relatorio
			  end if

			elseif passo = 4 then ' fecha o relatorio gerencial
			  if via = 0 then ' se deu erro na impressão, imprime tudo novamente
			  	passo = 1 ' abrir o relatorio gerencial
			  else
			    passo = 5 ' fim
			  end if
			end if

		end if

	  ' -----------------------------------------------
	  ' FIM DA VERIFICAÇÃO DE ERROS
	  ' -----------------------------------------------

	wend


	'fecha o arquivo
	arqtxt.Close

	ImprimeTEF = true
		
end function
'---------------------------------------------------


' Função que verifica o arquivo intpos.001 criado pelo gerenciador padrão
function VerificaRetornoArquivoIntPos001()
'MsgBox "VerificaRetornoArquivoIntPos001"

	Dim tef_diretorio, tef_ativo

	' variável que define o diretório do tef
	tef_diretorio = document.for_orcamento.tef_caminho.value

	ret = DeletaArquivo(tef_diretorio & "resp\intpos.sts")

'	tef_ativo = Verifica_TEF_Ativo()

'MsgBox("tef_ativo " & tef_ativo)

	' verifica se o gerenciador padrão respondeu, e criou o arquivo intpos.sts
	if principal_criou_arquivo = 0 then
		ret = DeletaArquivo(tef_diretorio & "req\intpos.001")
		MsgBox "TEF não está ativo!"
		VerificaRetornoArquivoIntPos001 = false
		Exit Function
	end if

	' A aplicação ficará esperando a criação do arquivo intpos.001
	' verifica INFINITAMENTE se o Gerenciador padrão retornou o arquivo 001
	ret = VerificaCriacaoDoArquivo (tef_diretorio & "resp\intpos.001", 86400, "AnalisaArquivoIntPos001")

	VerificaRetornoArquivoIntPos001 = true

end function
'---------------------------------------------------


' Função que verifica o arquivo intpos.001 criado pelo gerenciador padrão
function AnalisaArquivoIntPos001()
	'MsgBox "AnalisaArquivoIntPos001"

	Dim tef_diretorio, texto, conteudo, identificacao, campo028, campo030, campo009, tipo_transacao, tentativas

	' variável que define o diretório do tef
	tef_diretorio = document.for_orcamento.tef_caminho.value

	identificacao = document.for_orcamento.idorcamento.value

	conteudo = ""

	set fso = CreateObject("Scripting.FileSystemObject")

	'ret = DeletaArquivo(tef_diretorio & "req\intpos.001")
	

  tentativas = 0
	while identificacao <> conteudo
		set arqtxt = fso.OpenTextFile(tef_diretorio & "resp\intpos.001")
		
		' verifica se o campo 000-001 é igual ao do arquivo enviado
		while not arqtxt.AtEndOfStream
			texto = arqtxt.ReadLine
			if ( mid (texto, 1, 7) = "001-000" ) then
				conteudo = trim( mid(texto, 10) )
			end if
		wend

		arqtxt.Close

		' verifica se retornou o arquivo certo
		if tentativas > 0 then
		  'MsgBox "A identificação da solicitação não corresponde com a identificação original."

			' deleta o arquivo de retorno do gerenciador padrão
			ret = DeletaArquivo(tef_diretorio & "resp\intpos.001")
			
			' verifica INFINITAMENTE se o Gerenciador padrão retornou o arquivo 001
			ret = VerificaCriacaoDoArquivo (tef_diretorio & "resp\intpos.001", 86400, "AnalisaArquivoIntPos001")

			AnalisaArquivoIntPos001 = false
			Exit Function
		end if

		tentativas = tentativas + 1
	wend

	'MsgBox "arquivo válido, OK!!"


	' Cria um backup do arquivo intpos.001 para o caso de queda de energia
	fso.CopyFile tef_diretorio & "resp\intpos.001", tef_diretorio & "intpos001_bk.txt", true


	' verifica se existe o campo 030-000, se existir, mostra a mensagem para o operador
	set arqtxt = fso.OpenTextFile(tef_diretorio & "resp\intpos.001")
	campo028 = 0

	while not arqtxt.AtEndOfStream
		texto = arqtxt.ReadLine

		if ( mid (texto, 1, 7) = "009-000" ) then
			campo009 = trim( mid(texto, 10) )
		end if

		if ( mid (texto, 1, 7) = "011-000" ) then
			tipo_transacao = trim( mid(texto, 10) )
		end if

		if ( mid (texto, 1, 7) = "030-000" ) then
			campo030 = trim( mid(texto, 10) )
		end if

		if ( mid (texto, 1, 7) = "028-000" ) then
			campo028 = CInt( trim( mid(texto, 10) ) )
		end if
	wend

	arqtxt.Close

	' informa o tipo da transação
	document.for_orcamento.tipoTransacao.value = tipo_transacao

	' verifica se a transação foi APROVADA
	if campo009 = "0" then
		' verifica se o campo 028-000 é maior do que 0.
		if campo028 > 0 then
			'mostra a mensagem para o operador, sem o botão de ok
			ret = MostraMensagem030(campo030)
		end if

		' chama a função de continuar o pagamento, depois de 1 segundo
		ret = VerificaCriacaoDoArquivo ("", 1, "Continua_Pagamento")

		AnalisaArquivoIntPos001 = true

		Exit Function

	' transação NEGADA
	else

		if campo028 = "0" then
	  	MsgBox campo030
	  end if
	  
		ret = DeletaArquivo(tef_diretorio & "req\intpos.001")
		
		' deleta o arquivo de retorno do gerenciador padrão
		ret = DeletaArquivo(tef_diretorio & "resp\intpos.001")

		' deleta o arquivo de backup
		ret = DeletaArquivo(tef_diretorio & "intpos001_bk.txt")

		AnalisaArquivoIntPos001 = false
		Exit Function		
	end if


	AnalisaArquivoIntPos001 = true
end function
'---------------------------------------------------


' Função que cria o arquivo intpos.001
function Continua_Pagamento ()
	' foi aprovado a transação, então faz o pagamento
	Faz_Pagamento_ECF()
end function
'---------------------------------------------------


' Função que cria o arquivo intpos.001
function CriaArquivoIntPos001()
	'MsgBox "CriaArquivoIntPos001"
	Dim tef_diretorio, identificacao, documento_fiscal_vinculado, valor, tipo_comando, travar

	' variável que define o diretório do tef
	tef_diretorio = document.for_orcamento.tef_caminho.value
	'MsgBox (tef_diretorio)
	identificacao = document.for_orcamento.idorcamento.value
	documento_fiscal_vinculado = document.for_orcamento.ordemECF.value
	valor	= document.for_orcamento.valorTEF.value

	' define se é cartão ou cheque
	if document.for_orcamento.usaTEF.value = "2" then
		tipo_comando = "CHQ"
	else
		tipo_comando = "CRT"
	end if


	set fso = CreateObject("Scripting.FileSystemObject")

	' limpa os arquivos de lixo
	ret = DeletaArquivo(tef_diretorio & "resp\intpos.001")
	ret = DeletaArquivo(tef_diretorio & "resp\intpos.sts")

	set arqtxt = fso.CreateTextFile(tef_diretorio & "req\intpostemp.tmp", True)

	With arqtxt
	  .WriteLine ("000-000 = " & tipo_comando)
	  .WriteLine ("001-000 = " & identificacao)
	  .WriteLine ("002-000 = " & documento_fiscal_vinculado)
	  .WriteLine ("003-000 = " & valor)
	  '.WriteLine ("777-XXX = TESTE REDECARD")
	  .WriteLine ("004-000 = 0")
	  .Write ("999-999 = 0")
	  .Close
	End With
	


	ret = DeletaArquivo(tef_diretorio & "req\intpos.001")
	fso.MoveFile tef_diretorio & "req\intpostemp.tmp", tef_diretorio & "req\intpos.001" 

	' verifica durante 7 segundos se o Gerenciador padrão retornou o arquivo de status
	ret = VerificaCriacaoDoArquivo (tef_diretorio & "resp\intpos.sts", 7, "VerificaRetornoArquivoIntPos001")

	CriaArquivoIntPos001 = true

end function
'---------------------------------------------------


' Função cancela a transação
function CancelaTransacao()
	
	dim tef_diretorio, identificacao, documento_fiscal_vinculado, rede, nsu, finalizacao, valor, mensagem, travar

	' variável que define o diretório do tef
	tef_diretorio = document.for_orcamento.tef_caminho.value

	travar = document.for_orcamento.travar_teclado.value

	' verifica se o tef está ativo
	ret = Verifica_TEF_Ativo (tef_diretorio, travar, 0)

	set fso = CreateObject("Scripting.FileSystemObject")

	if fso.FileExists(tef_diretorio & "resp\intpos.001") then

		set arqtxt = fso.OpenTextFile(tef_diretorio & "resp\intpos.001")
		set arqtxt2 = fso.CreateTextFile(tef_diretorio & "req\intpostemp.tmp", True)
	
		valor	= document.for_orcamento.valorTEF_bk.value
	
		' monta o arquivo de cancelamento da transação
		while not arqtxt.AtEndOfStream
			texto = arqtxt.ReadLine
	
			if ( mid (texto, 1, 7) = "001-000" ) then
				identificacao = trim( mid(texto, 10) )
			elseif ( mid (texto, 1, 7) = "002-000" ) then
				documento_fiscal_vinculado = trim( mid(texto, 10) )
			elseif ( mid (texto, 1, 7) = "010-000" ) then
				rede = trim( mid(texto, 10) )
			elseif ( mid (texto, 1, 7) = "012-000" ) then
				nsu = trim( mid(texto, 10) )
			elseif ( mid (texto, 1, 7) = "027-000" ) then
				finalizacao = trim( mid(texto, 10) )
			elseif ( mid (texto, 1, 7) = "003-000" ) then
			valor = trim( mid(texto, 10) )
		 	end if
		
		wend
	
		arqtxt.Close
	
		With arqtxt2
			.WriteLine ("000-000 = NCN")
			.WriteLine ("001-000 = " & identificacao)
			.WriteLine ("002-000 = " & documento_fiscal_vinculado)
			.WriteLine ("010-000 = " & rede)
			.WriteLine ("012-000 = " & nsu)
			.WriteLine ("027-000 = " & finalizacao) 
			.Write ("999-999 = 0")
			.Close
		End With

	' formata o campo valor
		if len(valor) > 0 then
			valor = left(valor, len(valor)-2) & "," & right(valor,2)
		end if

		ret = DeletaArquivo(tef_diretorio & "req\intpos.001")
		fso.MoveFile tef_diretorio & "req\intpostemp.tmp", tef_diretorio & "req\intpos.001" 
	
		mensagem = "Cancelada a Transação:" & VBCrLf & "Rede: " & rede
		if nsu <> "" then
			mensagem = mensagem & VBCrLf & "NSU: " & nsu
		end if
		if valor <> "" then
			mensagem = mensagem & VBCrLf & "Valor: " & valor
		end if
		MsgBox mensagem


		' deleta o arquivo de retorno do gerenciador padrão
		ret = DeletaArquivo(tef_diretorio & "resp\intpos.001")

		' deleta o arquivo de backup
		ret = DeletaArquivo(tef_diretorio & "intpos001_bk.txt")


		ret = DeletaArquivo(tef_diretorio & "resp\intpos.sts")

	end if

	CancelaTransacao = true
end function
'---------------------------------------------------


' Função que verifica se o TEF está ativo
function Verifica_TEF_Ativo (tef_diretorio, travar_teclado_mouse, repetir_msg)

'MsgBox"Verifica_TEF_Ativo " & tef_diretorio


	dim identificacao, time1, time2, cont, reiniciou_tef

	' gera um número randomico para identificação
	Randomize
	identificacao = Int( Rnd() * 100000 )
		
	set fso = CreateObject("Scripting.FileSystemObject")

	' verifica se o gerenciador padrão responde
	set arqtxt2 = fso.CreateTextFile(tef_diretorio & "req\intpostemp.tmp", True)

	With arqtxt2
		.WriteLine ("000-000 = ATV")
		.WriteLine ("001-000 = " & identificacao)
		.Write ("999-999 = 0")
		.Close
	End With
	
'MsgBox"Verifica_TEF_Ativo"
'exit function

	ret = DeletaArquivo(tef_diretorio & "req\intpos.001")
	fso.MoveFile tef_diretorio & "req\intpostemp.tmp", tef_diretorio & "req\intpos.001"

	' da um tempo para verificar se está ativo
	time1 = Time
	time2 = Time
	cont = 0
	while ( Datediff("s",time1, time2) < 7 )
		time2 = Time
		cont = cont + 1
		cont = cont + 9999 + 9999 + 9999 + 9999 + 9999 + 9999 + 9999 + 9999
		cont = cont - 9999 - 9999 - 9999 - 9999 - 9999 - 9999 - 9999 - 9999
	wend



	reiniciou_tef = 0

	while (not fso.FileExists(tef_diretorio & "resp\intpos.sts"))

		if reiniciou_tef = 0 then

			' Destrava o teclado / mouse
			if travar_teclado_mouse = 1 then Finaliza_Modo_TEF()
		

			MsgBox "TEF não está ativo!"

			if (repetir_msg = 0) then
			    Exit Function
			end if
				
		end if

    	if ( (not fso.FileExists(tef_diretorio & "req\intpos.001")) and (reiniciou_tef = 0) ) then

			' quando ele deleta o arquivo req/intpos.001, significa que reiniciou o tef
			reiniciou_tef = 1
      
			' verifica se o gerenciador padrão responde
			set arqtxt2 = fso.CreateTextFile(tef_diretorio & "req\intpostemp.tmp", True)

			With arqtxt2
				.WriteLine ("000-000 = ATV")
				.WriteLine ("001-000 = " & identificacao)
				.Write ("999-999 = 0")
				.Close
			End With

			ret = DeletaArquivo(tef_diretorio & "req\intpos.001")
			fso.MoveFile tef_diretorio & "req\intpostemp.tmp", tef_diretorio & "req\intpos.001"

		end if

	wend

	Verifica_TEF_Ativo = true

end function
'---------------------------------------------------



