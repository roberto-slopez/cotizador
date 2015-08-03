$(function(){

	//Valores select
	var $VALOR_SELECT_CURSO = $('#select_curso');
	var $VALOR_SELECT_PAIS = $('#select_pais');

	function onLoad(){
		$('#list_valores').hide();
		$('#boton_imprimir').hide();
		$('#paso_2').hide();
		$('#paso_3').hide();
	}

		$VALOR_SELECT_CURSO.on('change', function(event) {

			if($VALOR_SELECT_CURSO.val() != '[ Seleccione ]'){
				$VALOR_SELECT_PAIS.attr('disabled', false);
				$('#list_valores').slideToggle('slow');
				$('#boton_imprimir').slideToggle('slow');
				$('#paso_2').slideToggle('slow');
				$('#paso_3').slideToggle('slow');
			}else{
				$VALOR_SELECT_PAIS.attr('disabled', true);

			}
		});
	onLoad();
});