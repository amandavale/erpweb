
var $j = jQuery.noConflict();

/**
 * Oculta ou mostra informa��es de CPF
 */
function includeCpfs(){

	if($j('#includeCpf').is(':checked')){
		$j('.cpf').show();
	}
	else{
		$j('.cpf').hide();
	}
}