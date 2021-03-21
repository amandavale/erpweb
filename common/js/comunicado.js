
$(document).ready(function() {
	
	/// inicia select m�ltiplo das fontes RSS
	$('.select_multiplo').multiselectable({
        selectableLabel: 'Condom�nios cadastrados',
        selectedLabel: 'Condom�nios selecionados',
        moveRightText: '',
        moveLeftText: ''
	});

});


/**
 * Inclui campos de sele��o de arquivos na tela
 */

function incluirArquivos(){
	
	var html = '<tr class="arquivos_comunicado">' +
               '	<td>&nbsp;</td>' +
               '	<td>' +
               '		<input type="file" name="arquivo[]" />' +
               '	</td>' +
               '</tr>';
	
	$('.arquivos_comunicado').last().after(html);
}


/**
 * Chama fun��o via ajax para apagar arquivo associado a um comunicado 
 * @param indice_arquivo - �ndice do arquivo no formul�rio, para identifica��o do arquivo
 * @param id_comunicado - ID do comunicado
 * @param nome_arquivo - Nome do arquivo que ser� apagado
 */
function apaga_arquivo(indice_arquivo,id_comunicado, nome_arquivo){
	
	$.ajax({
		url: 'comunicado_ajax.php?ac=apaga_arquivo',
		async:false,
		type: 'post',
		data: {
			id_comunicado:id_comunicado, nome_arquivo:nome_arquivo
		},
        dataType: 'json',
		success: function (data) {
		
			if(data == true){
				$('#arquivo_' + indice_arquivo).remove();
			}
		}
	});
	
}
	
	
