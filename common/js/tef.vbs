' Fun��o que inicia o TEF
function IniciaModuloTEFADM(tef_caminho)

	
	Dim ret, travar, repetir_msg

	travar = 0
	repetir_msg = 0

	' verifica se o tef est� ativo
	ret = Verifica_TEF_Ativo(tef_caminho, travar, repetir_msg)
	
	if (ret= true)then
		if ( CriaArquivoIntPos001ADM() = false ) then
			IniciaModuloTEFADM = false
			Exit Function
		end if

	else

		Exit Function

	end if

	IniciaModuloTEFADM = true

end function
'---------------------------------------------------


' Fun��o que finaliza o TEF
function FinalizacaoModuloTEFADM()
	'MsgBox "FinalizacaoModuloTEFADM"

	Dim tipo_transacao, tef_diretorio, travar, campo028, campo030, texto

	' vari�vel que define o diret�rio do tef
	tef_diretorio = document.for_tef.tef_caminho.value

	' se for uma opera��o de GARANTIA DE CHEQUE, n�o deixa imprimir
	tipo_transacao = document.for_tef.tipoTransacao.value
	'MsgBox "tipo_transacao " & tipo_transacao
	
	travar = document.for_tef.travar_teclado.value


	' abre o arquivo intpos.001 para verificar se tem que ter impress�o
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
	'MsgBox ("tipo_transacao " & tipo_transacao)

	' � GARANTIA DE CHEQUE, logo n�o deixa imprimir nada, j� finaliza o processo
	' campo28 = 0  => sem impress�o de cupom
	if (tipo_transacao = "71") or (campo028 = 0) then
		'MsgBox ("tipo_transacao " & tipo_transacao & " \n campo28 " & campo28)
		'Exibe a mensagem de retorno do TEF
		MsgBox(campo030)

		if ( ConfirmacaoImpressaoTEFADM() = false ) then 
			FinalizacaoModuloTEFADM = false
			Exit Function
		end if

	' n�o � GARANTIA DE CHEQUE
	else

		if ( ImprimeTEFADM() = false ) then 
			' A aplica��o ficar� esperando a cria��o do arquivo intpos.sts
			' verifica INFINITAMENTE se o Gerenciador padr�o retornou o arquivo sts
			ret = VerificaCriacaoDoArquivo (tef_diretorio & "resp\intpos.sts", 86400, "ConfirmacaoImpressaoTEFADM")	

			FinalizacaoModuloTEFADM = false
			Exit Function
		end if

		' verifica se o tef est� ativo
		ret = Verifica_TEF_Ativo (tef_diretorio, travar,0)

		if ( EnviaConfirmacaoImpressaoADM() = false ) then 
			' A aplica��o ficar� esperando a cria��o do arquivo intpos.sts
			' verifica INFINITAMENTE se o Gerenciador padr�o retornou o arquivo sts
			ret = VerificaCriacaoDoArquivo (tef_diretorio & "resp\intpos.sts", 86400, "ConfirmacaoImpressaoTEFADM")	

			FinalizacaoModuloTEFADM = false
			Exit Function
		end if

	end if

	FinalizacaoModuloTEFADM = true

end function
'---------------------------------------------------


' Fun��o que envia a confirma��o da transa��o
function EnviaConfirmacaoImpressaoADM()
	'MsgBox "EnviaConfirmacaoImpressaoADM"
	Dim tef_diretorio, identificacao, documento_fiscal_vinculado, rede, nsu, finalizacao, campo028

	' vari�vel que define o diret�rio do tef
	tef_diretorio = document.for_tef.tef_caminho.value

	set fso = CreateObject("Scripting.FileSystemObject")

	'fso.CopyFile tef_diretorio & "resp\intpos.001", tef_diretorio & "resp\intpos222.temp"

	set arqtxt = fso.OpenTextFile(tef_diretorio & "resp\intpos.001")

	' monta o arquivo de confirma��o da impress�o
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
		elseif ( mid (texto, 1, 7) = "028-000" ) then
			campo028 = CInt( trim( mid(texto, 10) ) )
		end if
	wend

	arqtxt.Close

	' verifica se realmente tem que enviar a confirma��o de impress�o, caso teve linhas para imprimir
	if campo028 > 0 then

		set arqtxt2 = fso.CreateTextFile(tef_diretorio & "req\intpostemp.tmp", True)

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

		'fso.CopyFile tef_diretorio & "req\intpostemp.tmp", tef_diretorio & "req\intpostemp2.tmp"

		ret = DeletaArquivo(tef_diretorio & "req\intpos.001")
		fso.MoveFile tef_diretorio & "req\intpostemp.tmp", tef_diretorio & "req\intpos.001"

		' deleta o arquivo de retorno do gerenciador padr�o
		ret = DeletaArquivo(tef_diretorio & "resp\intpos.001")

		' deleta o arquivo de backup
		ret = DeletaArquivo(tef_diretorio & "intpos001_bk.txt")


		' A aplica��o ficar� esperando a cria��o do arquivo intpos.sts
		' verifica INFINITAMENTE se o Gerenciador padr�o retornou o arquivo sts
		ret = VerificaCriacaoDoArquivo (tef_diretorio & "resp\intpos.sts", 86400, "ConfirmacaoImpressaoTEFADM")

	' n�o precisa enviar a confirma��o da transa��o
	else
	  'MsgBox "nao enviou confirmacao"
		ConfirmacaoImpressaoTEFADM()
	end if
	

	EnviaConfirmacaoImpressaoADM = true

end function
'---------------------------------------------------



' Fun��o que envia a confirma��o da transa��o
function ConfirmacaoImpressaoTEFADM()
'Msgbox "ConfirmacaoImpressaoTEFADM"
	Dim tef_diretorio

	' vari�vel que define o diret�rio do tef
	tef_diretorio = document.for_tef.tef_caminho.value

	' deleta o arquivo de backup
	ret = DeletaArquivo(tef_diretorio & "intpos001_bk.txt")

	' deleta o arquivo de retorno do gerenciador padr�o
	ret = DeletaArquivo(tef_diretorio & "resp\intpos.001")
	ret = DeletaArquivo(tef_diretorio & "resp\intpos.sts")

	'esconde a mensagem 030
	ret = EscondeMensagem030()

	ConfirmacaoImpressaoTEFADM = true

end function
'---------------------------------------------------



' Fun��o que imprime o comprovante de TEF
function ImprimeTEFADM()

	Dim tef_diretorio, conteudo29, documento_fiscal_vinculado, valor, modo_recebimento_tef, resp, resp2, via, ACK, ST1, ST2, PoucoPapel, SemPapel, travar, linhas_em_branco, passo, parameter

	' vari�vel que define o diret�rio do tef
	tef_diretorio = document.for_tef.tef_caminho.value

	via = 0

	travar = document.for_tef.travar_teclado.value

	set fso = CreateObject("Scripting.FileSystemObject")

	' Imprime 2 vias do cupom da transa��o de TEF
	set arqtxt = fso.OpenTextFile(tef_diretorio & "resp\intpos.001")

	' Trava o teclado / mouse
	if travar = 1 then Inicia_Modo_TEF()

	' fecha algum relat�rio gerencial que tenha ficado pendente
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
			'iRetorno = 1
			'iRetorno = BemaWeb.RelatorioGerencialTEF("Via " & via)

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

				' Imprime atraves do relat�rio gerencial
 				iRetorno = BemaWeb.RelatorioGerencialTEF(conteudo29)
 
			end if


	  ' -----------------------------------------------
	  ' PASSO 3: SOLTA LINHAS EM BRANCO
		' -----------------------------------------------
		elseif passo = 3 then

			linhas_em_branco = linhas_em_branco + 1
			
			iRetorno = BemaWeb.RelatorioGerencialTEF(" ")

			if linhas_em_branco = 5 then
				' corta o papel
				'iRetorno = BemaWeb.AcionaGuilhotinaMFD(0)  ESTE COMANDO NAO FUNCIONA NA OCX
				iRetorno = BemaWeb.RelatorioGerencialTEF(chr(27) & chr(109))
				
				iRetorno = BemaWeb.RelatorioGerencialTEF(" ")
				iRetorno = BemaWeb.RelatorioGerencialTEF(" ")
			end if

	  ' -----------------------------------------------
	  ' PASSO 4: FECHAR O RELATORIO GERENCIAL
		' -----------------------------------------------
		elseif passo = 4 then

			iRetorno = BemaWeb.FechaRelatorioGerencial()

	  ' -----------------------------------------------
	  ' PASSO 5: FIM
		' -----------------------------------------------
		elseif passo = 5 then
			' FIM
	  end if
			
			
	  ' -----------------------------------------------
		' ANALISA SE A IMPRESS�O FOI CORRETA
	  ' -----------------------------------------------

		' verifica se acabou o papel
		iRetorno2 = BemaWeb.RetornoImpressora(ACK, ST1, ST2)
		if (ST1 >= 128) then
			SemPapel = true
			ST1 = ST1 - 128
			'MsgBox "Fim de papel"
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
		

		parameter = CheckParameter(iRetorno)

		if ((parameter = false) and (PoucoPapel = false)) then
		

			' Destrava o teclado / mouse
			if travar = 1 then Finaliza_Modo_TEF()

			resp = MsgBox("Impressora n�o responde. Tentar novamente ?", 32+4, "")

			if resp = VBYes then
			
				Inicia_Modo_TEF()

				'faz a leitura X
				'while Gera_LeituraX() = false
				
				'verifica se a impressora est� ligada
				while Impressora_Ligada() = false

					Finaliza_Modo_TEF()
					
					resp2 = MsgBox("Impressora n�o responde. Tentar novamente ?", 32+4, "")
					
					Inicia_Modo_TEF()

					if resp2 = VBNo then
					
						Finaliza_Modo_TEF()
						
						'fecha o arquivo
						arqtxt.Close

						'cancela a transa��o
						CancelaTransacaoADM()

						ImprimeTEFADM = false
						Exit Function
					end if

				wend

				' fecha o relat�rio gerencial corrente e volta para a impress�o do primeiro relat�rio gerencial
				passo = 4
				via = 0
				
			elseif resp = VBNo then
				'fecha o arquivo
				arqtxt.Close
				
				'cancela a transa��o
				CancelaTransacaoADM()

				ImprimeTEFADM = false
				Exit Function
			end if

			' Trava o teclado / mouse
			if travar = 1 then Inicia_Modo_TEF()

		' N�O OCORREU ERRO DE IMPRESS�O
		else

			' faz o controle de qual ser� o pr�ximo passo
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
			  if via = 0 then ' se deu erro na impress�o, imprime tudo novamente
			  	passo = 1 ' abrir o relatorio gerencial
			  else
			    passo = 5 ' fim
			  end if
			end if

		end if

	  ' -----------------------------------------------
	  ' FIM DA VERIFICA��O DE ERROS
	  ' -----------------------------------------------

	wend


	' Destrava o teclado / mouse
	if travar = 1 then Finaliza_Modo_TEF()

	arqtxt.Close

	ImprimeTEFADM = true

end function
'---------------------------------------------------


' Fun��o que verifica o arquivo intpos.001 criado pelo gerenciador padr�o
function VerificaRetornoArquivoIntPos001ADM()
	'MsgBox "VerificaRetornoArquivoIntPos001ADM"

	Dim tef_diretorio

	' vari�vel que define o diret�rio do tef
	tef_diretorio = document.for_tef.tef_caminho.value

	ret = DeletaArquivo(tef_diretorio & "resp\intpos.sts")

	' verifica se o gerenciador padr�o respondeu, e criou o arquivo intpos.sts
	if principal_criou_arquivo = 0 then
		ret = DeletaArquivo(tef_diretorio & "req\intpos.001")
		MsgBox "TEF n�o est� ativo!"
		VerificaRetornoArquivoIntPos001ADM = false
		Exit Function
	end if

	' A aplica��o ficar� esperando a cria��o do arquivo intpos.001
	' verifica INFINITAMENTE se o Gerenciador padr�o retornou o arquivo 001
	ret = VerificaCriacaoDoArquivo (tef_diretorio & "resp\intpos.001", 86400, "AnalisaArquivoIntPos001ADM")

	VerificaRetornoArquivoIntPos001ADM = true

end function
'---------------------------------------------------


' Fun��o que verifica o arquivo intpos.001 criado pelo gerenciador padr�o
function AnalisaArquivoIntPos001ADM()
	'MsgBox "AnalisaArquivoIntPos001ADM"

	Dim tef_diretorio, texto, conteudo, identificacao, campo028, campo030, campo009, tipo_transacao, tentativas

	' vari�vel que define o diret�rio do tef
	tef_diretorio = document.for_tef.tef_caminho.value

	identificacao = document.for_tef.identificacao.value
	conteudo = ""

	set fso = CreateObject("Scripting.FileSystemObject")

  	tentativas = 0
	while identificacao <> conteudo
		'MsgBox identificacao & " x " & conteudo
		set arqtxt = fso.OpenTextFile(tef_diretorio & "resp\intpos.001")
	
		' verifica se o campo 000-001 � igual ao do arquivo enviado
		while not arqtxt.AtEndOfStream
			texto = arqtxt.ReadLine
			if ( mid (texto, 1, 7) = "001-000" ) then
				conteudo = trim( mid(texto, 10) )
			end if
		wend

		arqtxt.Close

		' verifica se retornou o arquivo certo
		if tentativas > 0 then
		  'MsgBox "A identifica��o da solicita��o n�o corresponde com a identifica��o original."

			' deleta o arquivo de retorno do gerenciador padr�o
			ret = DeletaArquivo(tef_diretorio & "resp\intpos.001")

			' verifica INFINITAMENTE se o Gerenciador padr�o retornou o arquivo 001
			ret = VerificaCriacaoDoArquivo (tef_diretorio & "resp\intpos.001", 86400, "AnalisaArquivoIntPos001ADM")

			AnalisaArquivoIntPos001ADM = false
			Exit Function
		end if

		tentativas = tentativas + 1
	wend

	'MsgBox "arquivo v�lido, OK!!"


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


	' informa o tipo da transa��o
	document.for_tef.tipoTransacao.value = tipo_transacao


	if campo030 = "" then
	  campo030 = "Aguarde a impress�o!"
	end if

	' verifica se a transa��o foi APROVADA
	if campo009 = "0" then
		' verifica se o campo 028-000 � maior do que 0.
		if campo028 > 0 then
			'mostra a mensagem para o operador, sem o bot�o de ok
			ret = MostraMensagem030(campo030)
		end if

		' chama a fun��o de finaliza��o, depois de 1 segundo
		ret = VerificaCriacaoDoArquivo ("", 1, "FinalizacaoModuloTEFADM")

		AnalisaArquivoIntPos001ADM = true

		Exit Function

	' transa��o NEGADA
	else
	  if campo028 = "0" then
	  	MsgBox campo030
	  end if
	  
		ret = DeletaArquivo(tef_diretorio & "req\intpos.001")

		' deleta o arquivo de retorno do gerenciador padr�o
		ret = DeletaArquivo(tef_diretorio & "resp\intpos.001")

		' deleta o arquivo de backup
		ret = DeletaArquivo(tef_diretorio & "intpos001_bk.txt")

		AnalisaArquivoIntPos001ADM = false
		Exit Function		
	end if


	AnalisaArquivoIntPos001ADM = true
end function
'---------------------------------------------------



' Fun��o que cria o arquivo intpos.001
function CriaArquivoIntPos001ADM()

	'MsgBox "CriaArquivoIntPos001ADM"
	Dim tef_diretorio, identificacao, comando, valor_tef

	' vari�vel que define o diret�rio do tef
	tef_diretorio = document.for_tef.tef_caminho.value

	identificacao = document.for_tef.identificacao.value
	comando = document.for_tef.comando.value
	valor_tef = document.for_tef.valorTEF_bk.value


	'MsgBox identificacao & " " & comando & " " & valor_tef

	set fso = CreateObject("Scripting.FileSystemObject")

	' limpa os arquivos de lixo
	ret = DeletaArquivo(tef_diretorio & "resp\intpos.001")
	ret = DeletaArquivo(tef_diretorio & "resp\intpos.sts")

	set arqtxt = fso.CreateTextFile(tef_diretorio & "req\intpostemp.tmp", True)

	if comando = "ADM" then
		With arqtxt
			.WriteLine ("000-000 = ADM")
			.WriteLine ("001-000 = " & identificacao)
			.Write ("999-999 = 0")
			.Close
		End With
	elseif comando = "CHQ" then
		With arqtxt
			.WriteLine ("000-000 = CHQ")
			.WriteLine ("001-000 = " & identificacao)
			.WriteLine ("003-000 = " & valor_tef)
			.Write ("999-999 = 0")
			.Close
		End With		
	end if

	ret = DeletaArquivo(tef_diretorio & "req\intpos.001")
	fso.MoveFile tef_diretorio & "req\intpostemp.tmp", tef_diretorio & "req\intpos.001" 

	' verifica durante 7 segundos se o Gerenciador padr�o retornou o arquivo de status
	ret = VerificaCriacaoDoArquivo (tef_diretorio & "resp\intpos.sts", 7, "VerificaRetornoArquivoIntPos001ADM")

	CriaArquivoIntPos001ADM = true

end function
'---------------------------------------------------



' Fun��o cancela a transa��o
function CancelaTransacaoADM()
	'MsgBox "CancelaTransacaoADM"
	dim tef_diretorio, identificacao, documento_fiscal_vinculado, rede, nsu, finalizacao, valor, mensagem, travar

	' vari�vel que define o diret�rio do tef
	tef_diretorio = document.for_tef.tef_caminho.value

	travar = document.for_tef.travar_teclado.value

	' verifica se o tef est� ativo
	ret = Verifica_TEF_Ativo (tef_diretorio, travar,0)

	set fso = CreateObject("Scripting.FileSystemObject")

	set arqtxt = fso.OpenTextFile(tef_diretorio & "resp\intpos.001")
	set arqtxt2 = fso.CreateTextFile(tef_diretorio & "req\intpostemp.tmp", True)

	valor	= document.for_tef.valorTEF_bk.value

	' monta o arquivo de cancelamento da transa��o
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
		if len(valor) > 0 then valor = left(valor, len(valor)-2) & "," & right(valor,2)

	ret = DeletaArquivo(tef_diretorio & "req\intpos.001")
	fso.MoveFile tef_diretorio & "req\intpostemp.tmp", tef_diretorio & "req\intpos.001" 

	mensagem = "Cancelada a Transa��o:" & VBCrLf & "Rede: " & rede
	if nsu <> "" then
		mensagem = mensagem & VBCrLf & "NSU: " & nsu
	end if
	if valor <> "" then
		mensagem = mensagem & VBCrLf & "Valor: " & valor
	end if
	MsgBox mensagem


	' deleta o arquivo de retorno do gerenciador padr�o
	ret = DeletaArquivo(tef_diretorio & "resp\intpos.001")

	' deleta o arquivo de backup
	ret = DeletaArquivo(tef_diretorio & "intpos001_bk.txt")


	ret = VerificaCriacaoDoArquivo (tef_diretorio & "resp\intpos.sts", 86400, "ConfirmacaoImpressaoTEFADM")	

	CancelaTransacaoADM = true
end function
'---------------------------------------------------
