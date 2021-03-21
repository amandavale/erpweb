$j(document).ready(function() {

	var negociacao = $j("input[name='litnegociacao']:checked"). val();

	if(negociacao == '0'){
		$j('#negociacaoObs').hide();
	}
});


/**
 * Mostra ou oculta o campo de negociação
 */
function mostraNegociacao(){
	var negociacao = $j("input[name='litnegociacao']:checked"). val();

	if(negociacao == '0'){
		$j('#litnegociacaoObservacoes').val('');
		$j('#negociacaoObs').hide();
	}
	else if(negociacao == '1'){
		$j('#negociacaoObs').show();
	}
}
/**
 * Calcula juros com base nos valores digitados no formulário
 * @param string operacao: tipo de operação realizada: adicionar ou editar
 */
function calculaJuros(operacao){

	if(operacao == 'adicionar'){
		var campo_valor = 'valor_movimento';
		var campo_juros = 'valor_juros';
	}
	else{
		var campo_valor = 'numvalor_movimento';
		var campo_juros = 'numvalor_juros';
	}

	var valor_juros = calculaPorcentagem(campo_valor, 'juros');

	/// Registra o valor multiplicado por 100 para ser formatado em seguida
	$j('#' + campo_juros).val(valor_juros);

	/// Formata valor
	FormataValor(campo_juros,2);

	/// Mostra valor na tela
	$j('#valor_juros_label').text('R$ ' + $j('#' + campo_juros).val());
}


/**
 * Calcula multa com base nos valores digitados no formulário
 * @param string operacao: tipo de operação realizada: adicionar ou editar
 */
function calculaMulta(operacao){

	if(operacao == 'adicionar'){
		var campo_valor = 'valor_movimento';
		var campo_multa = 'valor_multa';
	}
	else{
		var campo_valor = 'numvalor_movimento';
		var campo_multa = 'numvalor_multa';
	}

	var valor_multa = calculaPorcentagem(campo_valor, 'multa');

	/// Registra o valor multiplicado por 100 para ser formatado em seguida
	$j('#' + campo_multa).val(valor_multa);

	/// Formata valor
	FormataValor(campo_multa,2);

	/// Mostra valor na tela
	$j('#valor_multa_label').text('R$ ' + $j('#' + campo_multa).val());
}

function calculaJurosMulta(operacao){
	calculaJuros(operacao);
	calculaMulta(operacao);
}

/**
 * Calcula valor da porcentagem com base no valor e na porcentagem passados
 * como parâmetro
 */
function calculaPorcentagem(campo_valor, campo_porcentagem){

	var valor = 0;
	var valor_movimento = $j('#' + campo_valor). val();
	var porcentagem = $j('#' + campo_porcentagem).val();

	if(valor_movimento && (valor_movimento != 'undefined') && porcentagem && (porcentagem != 'undefined')){
		porcentagem = porcentagem.replace(',','.');
		valor_movimento = valor_movimento.replace(',','.');

		valor = ((porcentagem)/100 * (valor_movimento));
		valor = valor.toFixed(2);
		valor = valor.replace('.',',');
	}

	return valor;
}

