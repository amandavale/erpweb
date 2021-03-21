<?php

//inclusão de bibliotecas
$diretorio_raiz = dirname(dirname(__FILE__));
require_once("$diretorio_raiz/common/lib/conf.inc.php");
require_once("$diretorio_raiz/common/lib/db.inc.php");
require_once("$diretorio_raiz/common/lib/form.inc.php");
require_once("$diretorio_raiz/entidades/saldo.php");

//Instancia as  classes
$db = new db();
$form = new form();
$saldo = new saldo();


/// Calcula saldo com data de um dia antes, porque a rotina é caculada após a virada do dia
$data_fim = $form->Altera_Data(date('Y-m-d'),"-1 day");

$data_referencia_saldo = $form->Altera_Data(date('Y-m-d'),"-2 day");

/// Arquivo onde são gravados logs
$arquivo_saida = '/tmp/log_saldo';
file_put_contents($arquivo_saida, '');

/// Marca saldos pendentes como processados para ter controle dos registros que estão sendo considerados.
/// Assim, caso algum registro seja feito na tabela enquanto a rotina é executada, não será perdido
$sql_saldo = "UPDATE saldo_pendente SET saldo_processado='1'";
$sql_res_saldo = $db->query($sql_saldo);


//Seleciona os clientes cadastrados na tabela movimento
$sql_clientes = "( SELECT DISTINCT idcliente_origem as idcliente FROM movimento  WHERE baixado = '1' )
				  						  UNION
				 ( SELECT DISTINCT idcliente_destino as idcliente FROM movimento  WHERE baixado = '1')";

$sql_res = $db->query($sql_clientes);

$sql_saldo = "SELECT * FROM saldo_pendente WHERE atualizacao <= '$data_fim 23:59:59' AND saldo_processado='1'";

$sql_res_saldo = $db->query($sql_saldo);

/// array que armazena na chave o ID do cliente e no valor a data de movimento atualizado 
$saldo_pendente = array();

/// Registra a data de atualização de movimento de cada cliente encontrado
while($linha = $db->fetch_array($sql_res_saldo)){
	if(isset($saldo_pendente[$linha['idcliente']])){
		/// se já existe uma data de movimento para esse cliente, verifica se a nova data é menor.
		/// se for, substitui
		if($saldo_pendente[$linha['idcliente']] > $linha['data']){
			$saldo_pendente[$linha['idcliente']] = $linha['data'];
		}
	}
	else{
		/// registra data de movimento do cliente
		$saldo_pendente[$linha['idcliente']] = $linha['data'];	
	}
}

//echo "\n\n"; var_dump($saldo_pendente); echo "\n\n";

//Inicia a variável de erro
$err = array();

//Aumenta o tempo limite de execução do script
set_time_limit(0);

while($linha = $db->fetch_array($sql_res)){
	
	if(!empty($linha['idcliente'])){ 

		$data_inicio = $data_referencia_saldo;

		/// se existir data de movimento pendente de calcular saldo, usa essa data como inicial do período
		if(isset($saldo_pendente[$linha['idcliente']])){
			$data_inicio = $saldo_pendente[$linha['idcliente']];
		}

		/// Verifica qual a maior data para a qual o saldo foi calculado para esse cliente.
		/// Caso a maior data seja menor que a data de movimento encontrada no saldo pendente,
		/// a data inicial passa a ser a menor.
		$sql_maxima_data = "SELECT max(data) as data FROM saldo WHERE idcliente = " . $linha['idcliente'];
		$sql_res_maxima_data = $db->query($sql_maxima_data);
		$linha_maxima_data = $db->fetch_array($sql_res_maxima_data);

		/// se a data máxima de saldo que está no banco de dados for menor, considera a data do banco de dados
		if($linha_maxima_data['data'] && ($linha_maxima_data['data'] < $data_inicio)){
			$data_inicio = $linha_maxima_data['data'];
		}

		/// calcula a partir de dois dias antes da data encontrada, porque no método de cáculo considera a data
		/// de um dia a mais.
		$data_inicio = $form->Altera_Data(date($data_inicio),"-2 day");	

file_put_contents($arquivo_saida, "Atualizando saldo do cliente " . $linha['idcliente'] . " - $data_inicio\n",FILE_APPEND);
//echo "\n\n Atualizando saldo do cliente " . $linha['idcliente'] . " - $data_inicio\n";

		//Faz uma transação por cliente
		$db->query('BEGIN');		
		if(!$saldo->atualizaSaldo($data_fim,$linha['idcliente'],$data_inicio)){
			$err[] = $linha['idcliente'];
		}
		$db->query('COMMIT');	
	}
}

$count_err = count($err);

/// se não houve erro apaga registros que foram processados
if(count($err) == 0){
	$sql_saldo = "DELETE FROM saldo_pendente WHERE saldo_processado='1'";
	$sql_res_saldo = $db->query($sql_saldo);
}

$saida = $count_err > 0 ?  "Falha ao gravar o saldo de $count_err clientes:\n\n".implode("\n",$err) : "Saldo atualizado com sucesso!";

echo $saida;