$(document).ready(function() {
	
	$("input[type='checkbox']").attr('checked',true);

	/// mostra ou oculta linhas conforme o checkbox selecionado
	$("input[type='checkbox']").change(function(){
		
		var id = $(this).attr('id');
		
		if($(this).is(':checked')){
			$('.' + id).show();
		}
		else{
			$('.' + id).hide();
		}
	});
});