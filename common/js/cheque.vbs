' Função que inicia o TEF
function IniciaModuloTEFCheque()
	'MsgBox "IniciaModuloTEFCheque"

	if ( CriaArquivoIntPos001Cheque() = false ) then 
		IniciaModuloTEFCheque = false
		Exit Function
	end if

	if ( VerificaRetornoArquivoIntPos001Cheque() = false ) then 
		IniciaModuloTEFCheque = false
		Exit Function
	end if

	if ( ImprimeTEFCheque() = false ) then 
		IniciaModuloTEFCheque = false
		Exit Function
	end if

	if ( EnviaConfirmacaoImpressaoCheque() = false ) then 
		IniciaModuloTEFCheque = false
		Exit Function
	end if

	IniciaModuloTEFCheque = true

end function
'---------------------------------------------------



' Função que envia a confirmação da transação
function EnviaConfirmacaoImpressaoCheque()

	Dim tef_diretorio, identificacao, documento_fiscal_vinculado, rede, nsu, finalizacao

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

	fso.MoveFile tef_diretorio & "req\intpostemp.tmp", tef_diretorio & "req\intpos.001" 

	' deleta o arquivo de retorno do gerenciador padrão
	if fso.FileExists(tef_diretorio & "resp\intpos.001") then
		fso.DeleteFile(tef_diretorio & "resp\intpos.001") 
	end if

	
	' aguarda o retorno do status do gerenciador padrão
	while ( not fso.FileExists(tef_diretorio & "resp\intpos.sts") ) 
		MsgBox "Comprovante impresso com sucesso!"
	wend

	' deleta o arquivo de retorno do status do gerenciador padrão
	if fso.FileExists(tef_diretorio & "resp\intpos.sts") then
		fso.DeleteFile(tef_diretorio & "resp\intpos.sts") 
	end if

	EnviaConfirmacaoImpressaoCheque = true

end function
'---------------------------------------------------



' Função que imprime o comprovante de TEF
function ImprimeTEFCheque()

	Dim tef_diretorio, conteudo29, documento_fiscal_vinculado, valor, modo_recebimento_tef, resp, resp2, via, ACK, ST1, ST2, PoucoPapel, SemPapel, travar

	' variável que define o diretório do tef
	tef_diretorio = document.for_orcamento.tef_caminho.value

	via = 0

	travar = document.for_orcamento.travar_teclado.value

	set fso = CreateObject("Scripting.FileSystemObject")

	' Imprime 1 via do cupom da transação de TEF
	set arqtxt = fso.OpenTextFile(tef_diretorio & "resp\intpos.001")

	'MsgBox modo_recebimento_tef & " " & valor & " " & documento_fiscal_vinculado

	' Trava o teclado / mouse
	if travar = 1 then Inicia_Modo_TEF()


	' captura as linhas 029-yyy do arquivo
	while not arqtxt.AtEndOfStream
		texto = arqtxt.ReadLine
		if ( mid (texto, 1, 3) = "029" ) then

			conteudo29 = mid(texto, 12, len(texto)-12)
			if conteudo29 = "" then
				conteudo29 = " "
			end if

			' Imprime atraves do relatório gerencial
			iRetorno = BemaWeb.RelatorioGerencialTEF(conteudo29)

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


 			if ( ((CheckParameter(iRetorno) = false) and (PoucoPapel = false)) or (SemPapel = true) ) then

				'fecha o arquivo
				arqtxt.Close
			
				// Destrava o teclado / mouse
				if travar = 1 then Finaliza_Modo_TEF()

				resp = MsgBox("Impressora não responde. Tentar novamente ?", 32+4, "")

				if resp = VBYes then

					'faz a leitura X
					while Gera_LeituraX() = false
				
						resp2 = MsgBox("Impressora não responde. Tentar novamente ?", 32+4, "")
				
						if resp2 = VBNo then
							'cancela a transação
							CancelaTransacaoCheque()
					
							ImprimeTEFCheque = false
							Exit Function
						end if
				
					wend

					' abre o arquivo novamente para imprimir a via 1 através do relatório gerencial
					set arqtxt = fso.OpenTextFile(tef_diretorio & "resp\intpos.001")

				elseif resp = VBNo then
					'cancela a transação
					CancelaTransacaoCheque()

					ImprimeTEFCheque = false
					Exit Function
				end if

				// Trava o teclado / mouse
				if travar = 1 then Inicia_Modo_TEF()

			end if

		end if

	wend

	iRetorno = BemaWeb.FechaRelatorioGerencial()
	CheckParameter(iRetorno)		


	// Destrava o teclado / mouse
	if travar = 1 then Finaliza_Modo_TEF()

	arqtxt.Close

	ImprimeTEFCheque = true

end function
'---------------------------------------------------


' Função que verifica o arquivo intpos.001 criado pelo gerenciador padrão
function VerificaRetornoArquivoIntPos001Cheque()
	'MsgBox "VerificaRetornoArquivoIntPos001Cheque"

	Dim tef_diretorio, texto, conteudo, identificacao, campo028

	' variável que define o diretório do tef
	tef_diretorio = document.for_orcamento.tef_caminho.value


	identificacao = document.for_orcamento.idorcamentoFormatado.value
	conteudo = ""

	set fso = CreateObject("Scripting.FileSystemObject")

	while identificacao <> conteudo

		' verifica se já o Gerenciador padrão já retornou
		while ( not fso.FileExists(tef_diretorio & "resp\intpos.001") ) 
			MsgBox "Finalize a operação com o cartão e depois pressione ENTER para imprimir o comprovante..."
		wend	
	
		set arqtxt = fso.OpenTextFile(tef_diretorio & "resp\intpos.001")
	
		' verifica se o campo 000-001 é igual ao do arquivo enviado
		while not arqtxt.AtEndOfStream
			texto = arqtxt.ReadLine
			if ( mid (texto, 1, 7) = "001-000" ) then
				conteudo = trim( mid(texto, 10) )
			end if
		wend

		arqtxt.Close

	wend

	'MsgBox "arquivo válido, OK!!"

	' verifica se existe o campo 030-000, se existir, mostra a mensagem para o operador
	set arqtxt = fso.OpenTextFile(tef_diretorio & "resp\intpos.001")
	campo028 = 0

	while not arqtxt.AtEndOfStream
		texto = arqtxt.ReadLine
		if ( mid (texto, 1, 7) = "030-000" ) then
			conteudo = trim( mid(texto, 10) )
			MsgBox conteudo
		end if

		if ( mid (texto, 1, 7) = "028-000" ) then
			campo028 = CInt( trim( mid(texto, 10) ) )
		end if
	wend

	arqtxt.Close

	' verifica se o campo 028-000 é maior do que 0.
	if campo028 > 0 then
		VerificaRetornoArquivoIntPos001Cheque = true
		Exit Function
	else
		' deleta o arquivo de retorno do gerenciador padrão
		if fso.FileExists(tef_diretorio & "resp\intpos.001") then
			fso.DeleteFile(tef_diretorio & "resp\intpos.001") 
		end if

		VerificaRetornoArquivoIntPos001Cheque = false
		Exit Function
	end if

	VerificaRetornoArquivoIntPos001Cheque = true
end function
'---------------------------------------------------


' Função que cria o arquivo intpos.001
function CriaArquivoIntPos001Cheque()

	Dim tef_diretorio, identificacao, documento_fiscal_vinculado, valor, time1, time2, intervalo

	' variável que define o diretório do tef
	tef_diretorio = document.for_orcamento.tef_caminho.value


	'MsgBox "CriaArquivoIntPos001Cheque"

	identificacao = document.for_orcamento.idorcamentoFormatado.value
	documento_fiscal_vinculado = document.for_orcamento.ordemECF.value
	valor	= document.for_orcamento.valorTEF.value

	'MsgBox identificacao & " " & documento_fiscal_vinculado & " " & valor

	set fso = CreateObject("Scripting.FileSystemObject")

	' limpa os arquivos de lixo
	if fso.FileExists(tef_diretorio & "resp\intpos.001") then
		fso.DeleteFile(tef_diretorio & "resp\intpos.001") 
	end if

	if fso.FileExists(tef_diretorio & "resp\intpos.sts") then
		fso.DeleteFile(tef_diretorio & "resp\intpos.sts") 
	end if

	set arqtxt = fso.CreateTextFile(tef_diretorio & "req\intpostemp.tmp", True)

	With arqtxt
	  .WriteLine ("000-000 = CHQ")
	  .WriteLine ("001-000 = " & identificacao)
	  .WriteLine ("002-000 = " & documento_fiscal_vinculado)
	  .WriteLine ("003-000 = " & valor)
	  .Write ("999-999 = 0")
	  .Close
	End With

	fso.MoveFile tef_diretorio & "req\intpostemp.tmp", tef_diretorio & "req\intpos.001" 

	time1 = time()
	
	' verifica durante 7 segundos se o Gerenciador padrão retornou o arquivo de status
	while ( not fso.FileExists(tef_diretorio & "resp\intpos.sts") ) 
		time2 = time()	
		intervalo = DateDiff("s", time1, time2) 

		' como não houve resposta, deleta o arquivo da requisição
		if intervalo > 7 then
			MsgBox "TEF não está ativo!"

			if fso.FileExists(tef_diretorio & "req\intpos.001") then
				fso.DeleteFile(tef_diretorio & "req\intpos.001") 
			end if
			if fso.FileExists(tef_diretorio & "resp\intpos.sts") then
				fso.DeleteFile(tef_diretorio & "resp\intpos.sts") 
			end if

			CriaArquivoIntPos001Cheque = false
			Exit Function
		end if

		MsgBox "Pressione ENTER e aguarde uns instantes..."

	wend
	
	' houve resposta, então deleta o arquivo de status criado pelo gerenciador padrão
	if fso.FileExists(tef_diretorio & "resp\intpos.sts") then
		fso.DeleteFile(tef_diretorio & "resp\intpos.sts") 
	end if

	CriaArquivoIntPos001Cheque = true

end function
'---------------------------------------------------


' Função cancela a transação
function CancelaTransacaoCheque()
	'MsgBox "CancelaTransacaoCheque"
	dim tef_diretorio, identificacao, documento_fiscal_vinculado, rede, nsu, finalizacao, valor

	' variável que define o diretório do tef
	tef_diretorio = document.for_orcamento.tef_caminho.value


	set fso = CreateObject("Scripting.FileSystemObject")

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

	fso.MoveFile tef_diretorio & "req\intpostemp.tmp", tef_diretorio & "req\intpos.001" 

	' deleta o arquivo de retorno do gerenciador padrão
	if fso.FileExists(tef_diretorio & "resp\intpos.001") then
		fso.DeleteFile(tef_diretorio & "resp\intpos.001") 
	end if

	MsgBox "Cancelada a Transação:" & VBCrLf & "Doc No: " & nsu & VBCrLf & "Rede: " & rede & VBCrLf & "Valor: " & valor

	CancelaTransacaoCheque = true
end function
'---------------------------------------------------
