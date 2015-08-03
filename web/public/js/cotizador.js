$(function(){

	//Select (los nombres siempre son form_(el select) ver CotizadorController:105 para los nombres de los selects)
	var $selectCurso = $('#form_curso');
	var $selectPais = $('#form_pais');
	var $selectCiudad = $('#form_ciudad');
	var $selectCentro = $('#form_centro');

	function onLoad(){
		$('#list_valores').hide();
		$('#boton_imprimir').hide();
		$('#paso_2').hide();
		$('#paso_3').hide();
	}

		$selectCurso.on('change', function(event) {
			if($selectCurso.val() != '[ Seleccione ]'){
				$selectPais.attr('disabled', false);
				$('#list_valores').slideToggle('slow');
				$('#boton_imprimir').slideToggle('slow');
				$('#paso_2').slideToggle('slow');
				$('#paso_3').slideToggle('slow');
			}else{
				$selectPais.attr('disabled', true);
			}
		});

		//Cuando cambie el pais hidratar select cidudad
		$selectPais.on('change', function(event) {
			$selectCiudad.empty();
			$selectCiudad.append('<option value="">[ Seleccione ]</option>');
			hidrateCiudades();
			setMonedaByPais();
		});

		//Cuando cambie el pais hidratar select centro
		$selectCiudad.on('change', function(event) {
			$selectCentro.empty();
			$selectCentro.append('<option value="">[ Seleccione ]</option>');
			hidrateCentros();
		});

		function setMonedaByPais() {
			var url = '/moneda/'+$selectPais.val();
			$.post(url, function(value) {
				$("#form_moneda").val($.parseJSON(value));
			}, "html");
		}

		//Agregar datos al select Ciudad
		function hidrateCiudades() {
			var url = '/pais/'+$selectPais.val();
			$.post(url, function(data) {
				var ciudades =  $.parseJSON(data);
				$.each(ciudades,function(key, value)
				{
					$selectCiudad.append('<option value=' + key + '>' + value + '</option>');
				});

			}, "html");
		}

		//Agregar datos al select Ciudad
		function hidrateCentros() {
			var url = '/centro/'+$selectCiudad.val();
			$.post(url, function(data) {
				var ciudades =  $.parseJSON(data);
				$.each(ciudades,function(key, value)
				{
					$selectCentro.append('<option value=' + key + '>' + value + '</option>');
				});

			}, "html");
		}
	onLoad();
});