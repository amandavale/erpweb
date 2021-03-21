<?php

	//Define o hor�rio padr�o de S�o Paulo para n�o ocorrer diferen�a de hor�rio com o sevidor remoto
	date_default_timezone_set('America/Sao_Paulo');

	$conf['data_atual'] = date('d/m/Y');
	
	
	//$_GET['debug'] = 1;

	if(isset($_GET['debug']) && $_GET['debug'] == 1)
		error_reporting(E_ERROR | E_WARNING | E_PARSE);
	else
		error_reporting(0);



	$conf['aplic'] = "erpweb_sosprestadora"; 	   					// nome da aplica�o
	$conf['slog'] = "";     	// slogan

	// Instru��es em ambiente de teste
	$array_temp = split("/", $_SERVER['PHP_SELF']);
	$array_diretorios = array_reverse($array_temp);
	$conf['diretorio_area'] = $array_diretorios[1];

	// Instru��es em ambiente de produ��o
 	$conf['addr'] = 'http://' . $_SERVER['HTTP_HOST'] .  str_replace(array('/admin','/condominio'), '', dirname($_SERVER['PHP_SELF'])); // acesso cliente: endere�o base (se a barra final)
 	
	/** Endere�o do servidor de relat�rios */
	$conf['rpt_addr'] = 'http://' . $_SERVER['HTTP_HOST'];
	/** Porta do Servidor de Relat�rios */
	$conf['rpt_port'] = '8081';
	/** Nome da Aplica��o para gera��o de relat�rios */
	$conf['rpt_aplic'] = 'birt-viewer';

	$conf['priv'] = substr($array_diretorios[0],0,-4);  // nome do programa PHP requisitado
	$conf['nome_programa'] = basename($_SERVER['PHP_SELF'], '.php');  // nome do programa PHP requisitado

  $conf['empresa'] = 'SOS Prestadora de Servi�os';
	$conf['name'] = "ERPWEB";         // nome do site
	$conf['copyright'] = "";         // nome do site
	$conf['poweredby'] = "";         // nome do site
	$conf['db_name'] = "soserpweb.";    // prefixo do BD
	//$conf['path'] = "c:\\wamp\\www\\erpweb"; // acesso server: caminho base (sem a barra final)
	$conf['path'] = dirname(dirname(dirname(__FILE__)));



	//Defini��es da Sincronia
	$conf["endereco_servidor"] = "http://www.toformando.com/ip.php"; //endereco do servidor central
	$conf["proxy"] = 1;  //servidor com proxy: 0 pra nao, 1 para sim.
	$conf["endereco_proxy"] = 'tcp://proxy:3128'; //endereco do proxy
	//-----------------------------------------------------------------

	//Quantidade de campos do Sintegra;
	$conf['campos_sintegra'] = 5;
	//--------------------------------------------

	$conf['tpl'] = $conf['path'] . "/common/tpl";       //diret�rio de templates
	$conf['tpl_c'] = $conf['path'] . "/common/tpl_c";  	//diret�rio de templates compilados



	//Configura��es de email
	$conf['hdr_html_mail'] = "Content-Type: text/html; charset=ISO-8859-1\n";
	$conf['hdr_html_mail'].= "X-Mailer: PHP4 Script Language\n";
	$conf['hdr_html_mail'].= "X-Accept-Language: pt\n";
	$conf['hdr_html_mail'].= "MIME-Version: 1.0\n";
	$conf['hdr_html_mail'].= "Content-Transfer-Encoding: 7bit\n";
	//---------------------------------------------------------


	// resultados por p�gina
	$conf['rppg'] = 100;

	// resultados por p�gina nos campos AUTO-COMPLETAR
	$conf['rppg_auto_completar'] = 8;

	// minimo de caracteres digitados para buscar registros nos campos AUTO-COMPLETAR
	$conf['minimo_auto_completar'] = 2;


	// privil�gio do administrador do site
	$conf['pri_adm'] = 4;

	//Privil�gio de Cliente
	$conf['pri_cliente'] = 1;

	// constantes das contas a receber
	$conf['sigla_modo_cheque'] = "CH";
	$conf['sigla_modo_carteira'] = "CA";
	$conf['sigla_cartao_credito'] = "CC";  // se trocar esta sigla, tem que trocar tamb�m no arquivo JS
	$conf['sigla_cartao_debito'] = "CD";  // se trocar esta sigla, tem que trocar tamb�m no arquivo JS

	//--------------------------------------------


	// vari�vel que indica se deve ser travado ou n�o o teclado / mouse durante uma impress�o fiscal: 0 = n�o , 1 = sim
	$conf['travar_teclado'] = 1;
	//--------------------------------------------

	// vari�veis que definem o caminho do Gerenciador padr�o de TEF
	$conf['tef_tecban'] = "c:\\TEF_DISC\\";
	$conf['tef_rede_visa_amex'] = "c:\\tef_dial\\";
	$conf['tef_hipercard'] = "c:\\HiperTEF\\HiperLINK\\";
	//$conf['tef_hipercard'] = "c:\\HiperTEF\\HiperLINK\\1\\";
	//--------------------------------------------


	$conf['image_types'] = array("image/jpeg", "image/pjpeg");

	//Mensagens de Sucesso
	$conf['inserir'] = "Registro inserido com sucesso!";
	$conf['excluir'] = "Registro exclu�do com sucesso!";
	$conf['alterar'] = "Registro alterado com sucesso!";
	$conf['listar'] = "Nenhum registro encontrado!";
	$conf['senha'] = "Senha alterada com sucesso!";
	$conf['fotos'] = "Fotos e Thumbnails atualizados com sucesso!";
	$conf['listar_movimento_remessa'] = 'Nenhum movimento foi encontrado para gerar o arquivo de remessa.';

	//Mensagens de Falha
	$falha['inserir'] = "Falha na inser��o do registro. Verifique se este registro j� existe. Se n�o existir, por favor, entre em contato com os autores.";
	$falha['excluir'] = "Falha na exclus�o do registro. Por favor, entre em contato com os autores.";
	$falha['alterar'] = "Falha na atualiza��o do registro. Verifique se este registro j� existe. Se n�o existir, por favor, entre em contato com os autores.";
	$falha['listar'] = "Falha ao listar registros. Por favor, entre em contato com os autores.";
	$falha['senha'] = "Falha ao alterar senha. Por favor, entre em contato com os autores.";
	$falha['bd'] = "Falha de banco de dados. Por favor, entre em contato com os autores.";
	
	
	//Configura��es de fonte da Nota Fiscal
	$conf['fonte_nf']['nome'] = 'arial'; 
	$conf['fonte_nf']['tipo'] = 'B'; 
	$conf['fonte_nf']['tamanho'] = 10;
	
	//Para efeitos de login
	$conf['idfilial_padrao'] = 2; 
	
	//Nome do arquivo com a logo do cliente
	$conf['logo_cliente'] = 'logo_cliente.png';
	
	//ID do cliente usu�rio do Erpweb
	$conf['idcliente_erpweb'] = 1;

?>
