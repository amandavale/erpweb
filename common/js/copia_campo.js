
var $j = jQuery.noConflict();

/**
 * Copia o valor de um campo para outro, dependendo de o checkbox associado estar selecionado
 * @param campo_checkbox - ID do campo checkbox que deve estar selecionado para o campo ser copiado
 * @param campo_original - ID do campo original de onde o valor será copiado
 * @param campo_copiar - ID do campo para o qual será copiado o valor
 */
function copia_campo(campo_checkbox, campo_original, campo_copiar){

	if($j('#' + campo_checkbox).is(':checked')){

		$j('#' + campo_copiar).val($j('#' + campo_original).val());
	}
	else{
		$j('#' + campo_copiar).val('');
	}
}