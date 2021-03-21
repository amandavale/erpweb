
$(document).ready(function() {
	
	/// inicia select múltiplo das fontes RSS
	$('.select_multiplo').multiselectable({
        selectableLabel: 'Condomínios cadastrados',
        selectedLabel: 'Condomínios selecionados',
        moveRightText: '',
        moveLeftText: ''
	});

});


/**
 * Inclui campos de seleção de arquivos na tela
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
 * Chama função via ajax para apagar arquivo associado a um comunicado 
 * @param indice_arquivo - índice do arquivo no formulário, para identificação do arquivo
 * @param id_comunicado - ID do comunicado
 * @param nome_arquivo - Nome do arquivo que será apagado
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
	
	
