' Função que verif	ica se houve queda de energia no micro
function VerificaQuedaEnergiaTEF()
	'MsgBox "VerificaQuedaEnergiaTEF"

	Dim tipo, identificacao, tef_caminho1, tef_caminho2, tef_caminho3, travar, nao_transacao, arquivo_bkp
	tef_caminho1 = document.for_inicial.tef_caminho_1.value
	tef_caminho2 = document.for_inicial.tef_caminho_2.value
	tef_caminho3 = document.for_inicial.tef_caminho_3.value
	
	travar = document.for_inicial.travar_teclado.value

	set fso = CreateObject("Scripting.FileSystemObject")

	if fso.FileExists(tef_caminho1 & "intpos001_bk.txt") OR fso.FileExists(tef_caminho1 & "resp/intpos.001") then
		tef_diretorio = tef_caminho1
	elseif fso.FileExists(tef_caminho2 & "intpos001_bk.txt") OR fso.FileExists(tef_caminho1 & "resp/intpos.001") then
		tef_diretorio = tef_caminho2
	elseif fso.FileExists(tef_caminho3 & "intpos001_bk.txt") OR fso.FileExists(tef_caminho1 & "resp/intpos.001") then
		tef_diretorio = tef_caminho3
	end if


	' verifica se existe o arquivo intpos001_bk.txt, se existir, houve queda de energia


	
	if fso.FileExists(tef_diretorio & "resp\intpos.001") then
		arquivo_bkp = "resp\intpos.001"

	elseif fso.FileExists(tef_diretorio & "intpos001_bk.txt") then
		arquivo_bkp = "intpos001_bk.txt"
		
	end if
	

	'if fso.FileExists(tef_diretorio & "intpos001_bk.txt") then
	if fso.FileExists(tef_diretorio & arquivo_bkp) then

		' deleta o arquivo de status criado pelo gerenciador padrão
		ret = DeletaArquivo(tef_diretorio & "resp\intpos.sts")

		' recupera os dados do arquivo intpos001_bk.txt
		'set arqtxt = fso.OpenTextFile(tef_diretorio & "intpos001_bk.txt")
		set arqtxt = fso.OpenTextFile(tef_diretorio & arquivo_bkp)
	
		' monta o arquivo de cancelamento da transação
		while not arqtxt.AtEndOfStream
			texto = arqtxt.ReadLine

			if ( mid (texto, 1, 7) = "000-000" ) then
				tipo = trim( mid(texto, 10) )
				
			end if
			
			if ( mid (texto, 1, 7) = "009-000") then
				nao_transacao = mid(texto, 11, 12)
			end if
				

		wend

		arqtxt.Close

		' se for diferente de CRT, CHQ e ADM, sai da função
		if tipo <> "CRT" and tipo <> "CHQ" and tipo <> "ADM" then
			' deleta o arquivo de status criado pelo gerenciador padrão
			ret = DeletaArquivo(tef_diretorio & "intpos001_bk.txt")
			ret = DeletaArquivo(tef_diretorio & "resp\intpos.001")

			VerificaQuedaEnergiaTEF = false
			Exit Function
		end if


		' verifica se o tef está ativo
		ret = Verifica_TEF_Ativo (tef_diretorio, travar, 1)

		if  (nao_transacao <> "FF") then
		
			VerificaQuedaEnergiaTEF_Cancela()
		end if

	end if
		
	VerificaQuedaEnergiaTEF = true
	

end function
'---------------------------------------------------




' Função que verifica se houve queda de energia no micro
function VerificaQuedaEnergiaTEF_Cancela()
	

	Dim tipo, identificacao, documento_fiscal_vinculado, rede, nsu, finalizacao, tef_caminho1, tef_caminho2, tef_caminho3, mensagem

	tef_caminho1 = document.for_inicial.tef_caminho_1.value
	tef_caminho2 = document.for_inicial.tef_caminho_2.value
	tef_caminho3 = document.for_inicial.tef_caminho_3.value
	
	travar = document.for_inicial.travar_teclado.value

	set fso = CreateObject("Scripting.FileSystemObject")

	if fso.FileExists(tef_caminho1 & "intpos001_bk.txt") OR fso.FileExists(tef_caminho1 & "resp/intpos.001") then
		tef_diretorio = tef_caminho1
	elseif fso.FileExists(tef_caminho2 & "intpos001_bk.txt") OR fso.FileExists(tef_caminho1 & "resp/intpos.001") then
		tef_diretorio = tef_caminho2
	elseif fso.FileExists(tef_caminho3 & "intpos001_bk.txt") OR fso.FileExists(tef_caminho1 & "resp/intpos.001") then
		tef_diretorio = tef_caminho3
	end if

'	set fso = CreateObject("Scripting.FileSystemObject")

'	if fso.FileExists(tef_caminho1 & "intpos001_bk.txt") then
'		tef_diretorio = tef_caminho1
'	elseif fso.FileExists(tef_caminho2 & "intpos001_bk.txt") then
'		tef_diretorio = tef_caminho2
'	elseif fso.FileExists(tef_caminho3 & "intpos001_bk.txt") then
'		tef_diretorio = tef_caminho3
'	end if


	' se não criou o arquivo, é porque o TEF não está ativo.
	if (not fso.FileExists(tef_diretorio & "resp\ativo.001")) and (not fso.FileExists(tef_diretorio & "resp\intpos.sts")) then
		VerificaQuedaEnergiaTEF_Cancela = false
		Exit Function
	end if


	ret = DeletaArquivo(tef_diretorio & "resp\intpos.sts")



	if fso.FileExists(tef_diretorio & "resp\intpos.001") then
		arquivo_bkp = "resp\intpos.001"

	elseif fso.FileExists(tef_diretorio & "intpos001_bk.txt") then
		arquivo_bkp = "intpos001_bk.txt"

	end if



	' verifica se existe o arquivo intpos001_bk.txt, se existir, houve queda de energia
	if fso.FileExists(tef_diretorio & arquivo_bkp) then

		' deleta o arquivo de status criado pelo gerenciador padrão
		ret = DeletaArquivo(tef_diretorio & "resp\intpos.sts")

		' recupera os dados do arquivo intpos001_bk.txt
		set arqtxt = fso.OpenTextFile(tef_diretorio & arquivo_bkp)

		' monta o arquivo de cancelamento da transação
		while not arqtxt.AtEndOfStream
			texto = arqtxt.ReadLine

			if ( mid (texto, 1, 7) = "000-000" ) then
				tipo = trim( mid(texto, 10) )
			elseif ( mid (texto, 1, 7) = "001-000" ) then
				identificacao = trim( mid(texto, 10) )
			elseif ( mid (texto, 1, 7) = "002-000" ) then
				documento_fiscal_vinculado = trim( mid(texto, 10) )
			elseif ( mid (texto, 1, 7) = "003-000" ) then
				valor = trim( mid(texto, 10) )
			elseif ( mid (texto, 1, 7) = "010-000" ) then
				rede = trim( mid(texto, 10) )
			elseif ( mid (texto, 1, 7) = "012-000" ) then
				nsu = trim( mid(texto, 10) )
			elseif ( mid (texto, 1, 7) = "027-000" ) then
				finalizacao = trim( mid(texto, 10) )
			end if

		wend

		arqtxt.Close


		set arqtxt2 = fso.CreateTextFile(tef_diretorio & "req\intpostemp.tmp", True)

		' formata o campo valor
		if len(valor) > 0 then valor = left(valor, len(valor)-2) & "," & right(valor,2)		

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
	

		ret = DeletaArquivo(tef_diretorio & "req\intpos.001")
		fso.MoveFile tef_diretorio & "req\intpostemp.tmp", tef_diretorio & "req\intpos.001" 
	
		' deleta o arquivo de retorno do gerenciador padrão
		ret = DeletaArquivo(tef_diretorio & "intpos001_bk.txt")

		mensagem = "Cancelada a Transação:" & VBCrLf & "Rede: " & rede
		if nsu <> "" then
			mensagem = mensagem & VBCrLf & "NSU: " & nsu
		end if
		if valor <> "" then
			mensagem = mensagem & VBCrLf & "Valor: " & valor
		end if
		MsgBox mensagem


		' Fecha o comprovante não fiscal vinculado
		iRetorno = BemaWeb.FechaComprovanteNaoFiscalVinculado()

	  ' Fecha o relatório gerencial
		iRetorno = BemaWeb.FechaRelatorioGerencial()
				

		' houve resposta, então deleta o arquivo de status criado pelo gerenciador padrão
		ret = DeletaArquivo(tef_diretorio & "resp\intpos.001")
		ret = DeletaArquivo(tef_diretorio & "resp\intpos.sts")

	end if

	VerificaQuedaEnergiaTEF_Cancela = true

end function
'---------------------------------------------------

