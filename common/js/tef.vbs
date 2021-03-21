' Função que inicia o TEF
function IniciaModuloTEFADM(tef_caminho)

	
	Dim ret, travar, repetir_msg

	travar = 0
	repetir_msg = 0

	' verifica se o tef está ativo
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


' Função que finaliza o TEF
function FinalizacaoModuloTEFADM()
	'MsgBox "FinalizacaoModuloTEFADM"

	Dim tipo_transacao, tef_diretorio, travar, campo028, campo030, texto

	' variável que define o diretório do tef
	tef_diretorio = document.for_tef.tef_caminho.value

	' se for uma operação de GARANTIA DE CHEQUE, não deixa imprimir
	tipo_transacao = document.for_tef.tipoTransacao.value
	'MsgBox "tipo_transacao " & tipo_transacao
	
	travar = document.for_tef.travar_teclado.value


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
	'MsgBox ("tipo_transacao " & tipo_transacao)

	' é GARANTIA DE CHEQUE, logo não deixa imprimir nada, já finaliza o processo
	' campo28 = 0  => sem impressão de cupom
	if (tipo_transacao = "71") or (campo028 = 0) then
		'MsgBox ("tipo_transacao " & tipo_transacao & " \n campo28 " & campo28)
		'Exibe a mensagem de retorno do TEF
		MsgBox(campo030)

		if ( ConfirmacaoImpressaoTEFADM() = false ) then 
			FinalizacaoModuloTEFADM = false
			Exit Function
		end if

	' não é GARANTIA DE CHEQUE
	else

		if ( ImprimeTEFADM() = false ) then 
			' A aplicação ficará esperando a criação do arquivo intpos.sts
			' verifica INFINITAMENTE se o Gerenciador padrão retornou o arquivo sts
			ret = VerificaCriacaoDoArquivo (tef_diretorio & "resp\intpos.sts", 86400, "ConfirmacaoImpressaoTEFADM")	

			FinalizacaoModuloTEFADM = false
			Exit Function
		end if

		' verifica se o tef está ativo
		ret = Verifica_TEF_Ativo (tef_diretorio, travar,0)

		if ( EnviaConfirmacaoImpressaoADM() = false ) then 
			' A aplicação ficará esperando a criação do arquivo intpos.sts
			' verifica INFINITAMENTE se o Gerenciador padrão retornou o arquivo sts
			ret = VerificaCriacaoDoArquivo (tef_diretorio & "resp\intpos.sts", 86400, "ConfirmacaoImpressaoTEFADM")	

			FinalizacaoModuloTEFADM = false
			Exit Function
		end if

	end if

	FinalizacaoModuloTEFADM = true

end function
'---------------------------------------------------


' Função que envia a confirmação da transação
function EnviaConfirmacaoImpressaoADM()
	'MsgBox "EnviaConfirmacaoImpressaoADM"
	Dim tef_diretorio, identificacao, documento_fiscal_vinculado, rede, nsu, finalizacao, campo028

	' variável que define o diretório do tef
	tef_diretorio = document.for_tef.tef_caminho.value

	set fso = CreateObject("Scripting.FileSystemObject")

	'fso.CopyFile tef_diretorio & "resp\intpos.001", tef_diretorio & "resp\intpos222.temp"

	set arqtxt = fso.OpenTextFile(tef_diretorio & "resp\intpos.001")

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
		elseif ( mid (texto, 1, 7) = "028-000" ) then
			campo028 = CInt( trim( mid(texto, 10) ) )
		end if
	wend

	arqtxt.Close

	' verifica se realmente tem que enviar a confirmação de impressão, caso teve linhas para imprimir
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

		' deleta o arquivo de retorno do gerenciador padrão
		ret = DeletaArquivo(tef_diretorio & "resp\intpos.001")

		' deleta o arquivo de backup
		ret = DeletaArquivo(tef_diretorio & "intpos001_bk.txt")


		' A aplicação ficará esperando a criação do arquivo intpos.sts
		' verifica INFINITAMENTE se o Gerenciador padrão retornou o arquivo sts
		ret = VerificaCriacaoDoArquivo (tef_diretorio & "resp\intpos.sts", 86400, "ConfirmacaoImpressaoTEFADM")

	' não precisa enviar a confirmação da transação
	else
	  'MsgBox "nao enviou confirmacao"
		ConfirmacaoImpressaoTEFADM()
	end if
	

	EnviaConfirmacaoImpressaoADM = true

end function
'---------------------------------------------------



' Função que envia a confirmação da transação
function ConfirmacaoImpressaoTEFADM()
'Msgbox "ConfirmacaoImpressaoTEFADM"
	Dim tef_diretorio

	' variável que define o diretório do tef
	tef_diretorio = document.for_tef.tef_caminho.value

	' deleta o arquivo de backup
	ret = DeletaArquivo(tef_diretorio & "intpos001_bk.txt")

	' deleta o arquivo de retorno do gerenciador padrão
	ret = DeletaArquivo(tef_diretorio & "resp\intpos.001")
	ret = DeletaArquivo(tef_diretorio & "resp\intpos.sts")

	'esconde a mensagem 030
	ret = EscondeMensagem030()

	ConfirmacaoImpressaoTEFADM = true

end function
'---------------------------------------------------



' Função que imprime o comprovante de TEF
function ImprimeTEFADM()

	Dim tef_diretorio, conteudo29, documento_fiscal_vinculado, valor, modo_recebimento_tef, resp, resp2, via, ACK, ST1, ST2, PoucoPapel, SemPapel, travar, linhas_em_branco, passo, parameter

	' variável que define o diretório do tef
	tef_diretorio = document.for_tef.tef_caminho.value

	via = 0

	travar = document.for_tef.travar_teclado.value

	set fso = CreateObject("Scripting.FileSystemObject")

	' Imprime 2 vias do cupom da transação de TEF
	set arqtxt = fso.OpenTextFile(tef_diretorio & "resp\intpos.001")

	' Trava o teclado / mouse
	if travar = 1 then Inicia_Modo_TEF()

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

				' Imprime atraves do relatório gerencial
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
		' ANALISA SE A IMPRESSÃO FOI CORRETA
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

			resp = MsgBox("Impressora não responde. Tentar novamente ?", 32+4, "")

			if resp = VBYes then
			
				Inicia_Modo_TEF()

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
						CancelaTransacaoADM()

						ImprimeTEFADM = false
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
				CancelaTransacaoADM()

				ImprimeTEFADM = false
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


	' Destrava o teclado / mouse
	if travar = 1 then Finaliza_Modo_TEF()

	arqtxt.Close

	ImprimeTEFADM = true

end function
'---------------------------------------------------


' Função que verifica o arquivo intpos.001 criado pelo gerenciador padrão
function VerificaRetornoArquivoIntPos001ADM()
	'MsgBox "VerificaRetornoArquivoIntPos001ADM"

	Dim tef_diretorio

	' variável que define o diretório do tef
	tef_diretorio = document.for_tef.tef_caminho.value

	ret = DeletaArquivo(tef_diretorio & "resp\intpos.sts")

	' verifica se o gerenciador padrão respondeu, e criou o arquivo intpos.sts
	if principal_criou_arquivo = 0 then
		ret = DeletaArquivo(tef_diretorio & "req\intpos.001")
		MsgBox "TEF não está ativo!"
		VerificaRetornoArquivoIntPos001ADM = false
		Exit Function
	end if

	' A aplicação ficará esperando a criação do arquivo intpos.001
	' verifica INFINITAMENTE se o Gerenciador padrão retornou o arquivo 001
	ret = VerificaCriacaoDoArquivo (tef_diretorio & "resp\intpos.001", 86400, "AnalisaArquivoIntPos001ADM")

	VerificaRetornoArquivoIntPos001ADM = true

end function
'---------------------------------------------------


' Função que verifica o arquivo intpos.001 criado pelo gerenciador padrão
function AnalisaArquivoIntPos001ADM()
	'MsgBox "AnalisaArquivoIntPos001ADM"

	Dim tef_diretorio, texto, conteudo, identificacao, campo028, campo030, campo009, tipo_transacao, tentativas

	' variável que define o diretório do tef
	tef_diretorio = document.for_tef.tef_caminho.value

	identificacao = document.for_tef.identificacao.value
	conteudo = ""

	set fso = CreateObject("Scripting.FileSystemObject")

  	tentativas = 0
	while identificacao <> conteudo
		'MsgBox identificacao & " x " & conteudo
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
			ret = VerificaCriacaoDoArquivo (tef_diretorio & "resp\intpos.001", 86400, "AnalisaArquivoIntPos001ADM")

			AnalisaArquivoIntPos001ADM = false
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
	document.for_tef.tipoTransacao.value = tipo_transacao


	if campo030 = "" then
	  campo030 = "Aguarde a impressão!"
	end if

	' verifica se a transação foi APROVADA
	if campo009 = "0" then
		' verifica se o campo 028-000 é maior do que 0.
		if campo028 > 0 then
			'mostra a mensagem para o operador, sem o botão de ok
			ret = MostraMensagem030(campo030)
		end if

		' chama a função de finalização, depois de 1 segundo
		ret = VerificaCriacaoDoArquivo ("", 1, "FinalizacaoModuloTEFADM")

		AnalisaArquivoIntPos001ADM = true

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

		AnalisaArquivoIntPos001ADM = false
		Exit Function		
	end if


	AnalisaArquivoIntPos001ADM = true
end function
'---------------------------------------------------



' Função que cria o arquivo intpos.001
function CriaArquivoIntPos001ADM()

	'MsgBox "CriaArquivoIntPos001ADM"
	Dim tef_diretorio, identificacao, comando, valor_tef

	' variável que define o diretório do tef
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

	' verifica durante 7 segundos se o Gerenciador padrão retornou o arquivo de status
	ret = VerificaCriacaoDoArquivo (tef_diretorio & "resp\intpos.sts", 7, "VerificaRetornoArquivoIntPos001ADM")

	CriaArquivoIntPos001ADM = true

end function
'---------------------------------------------------



' Função cancela a transação
function CancelaTransacaoADM()
	'MsgBox "CancelaTransacaoADM"
	dim tef_diretorio, identificacao, documento_fiscal_vinculado, rede, nsu, finalizacao, valor, mensagem, travar

	' variável que define o diretório do tef
	tef_diretorio = document.for_tef.tef_caminho.value

	travar = document.for_tef.travar_teclado.value

	' verifica se o tef está ativo
	ret = Verifica_TEF_Ativo (tef_diretorio, travar,0)

	set fso = CreateObject("Scripting.FileSystemObject")

	set arqtxt = fso.OpenTextFile(tef_diretorio & "resp\intpos.001")
	set arqtxt2 = fso.CreateTextFile(tef_diretorio & "req\intpostemp.tmp", True)

	valor	= document.for_tef.valorTEF_bk.value

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
		if len(valor) > 0 then valor = left(valor, len(valor)-2) & "," & right(valor,2)

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


	ret = VerificaCriacaoDoArquivo (tef_diretorio & "resp\intpos.sts", 86400, "ConfirmacaoImpressaoTEFADM")	

	CancelaTransacaoADM = true
end function
'---------------------------------------------------
