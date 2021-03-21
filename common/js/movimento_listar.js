var $j = jQuery.noConflict();

$j(document).ready(function() {


	var cliente = $j('#idcliente').val();
	var condominio = $j('#idcondominio_selecionado').val();
	if(cliente){
		buscaCondominio();

		if(condominio){
			$j('#select_condominio').val(condominio);
		}
	}

});


function buscaCondominio(){

	var cliente = $j('#idcliente').val();
	var i;
	var conteudoHtml = '<select name="idcondominio" id="select_condominio">' +
						'<option value=""> Selecione </option>';

	$j.ajax({
		url: 'cliente_ajax.php?ac=buscaCondominiosCliente',
		async:false,
		type: 'get',
		data: {
			cliente:cliente
		},
        dataType: 'json',
		success: function (data) {

			for (i = 0; i < data.length; i++) {
				conteudoHtml += "<option value="+ data[i]['idcliente'] + ">" + data[i]['nome_cliente']+ "</option>";
			} 

			conteudoHtml += '</select>';

			$j('#condominio').html(conteudoHtml);
		}		
	});

}