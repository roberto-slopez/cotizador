$(function () {

    //Select (los nombres siempre son form_(el select) ver CotizadorController:105 para los nombres de los selects)
    var $selectCurso = $('#form_curso');
    var $selectPais = $('#form_pais');
    var $selectCiudad = $('#form_ciudad');
    var $selectCentro = $('#form_centro');
    var $selectSemanas = $('#form_semanas');
    var $selectLeccionesSemana = $('#form_lecciones_por_semana');
    var $selectJornadaLecciones = $('#form_jornada_de_lecciones');
    var $selectAlojamiento = $('#form_alojamiento');
    var $selectSemanaAlojamiento = $('#form_semanas_de_alojamiento');
    var $selectTipoAlojamiento = $('#form_tipo_de_alojamiento');
    var $selectTipoHabitacion = $('#form_tipo_de_habitacion');
    var $selectAlimentacion = $('#form_alimentacion');
    var $selectTraslado = $('#form_traslado');
    var $selectSeguro = $('#form_seguro');
    var $selectMoneda = $('#form_moneda')
    var $buttonCalcular = $('#boton_calcular');

    function onLoad() {
        $('#list_valores').hide();
        $('#boton_imprimir').hide();
        $('#paso_2').hide();
        $('#paso_3').hide();
    }

    $selectCurso.on('change', function (event) {
        if ($selectCurso.val() != '[ Seleccione ]') {
            $selectPais.attr('disabled', false);
            $('#list_valores').show('slow');
            $('#boton_imprimir').show('slow');
            $('#paso_2').show('slow');
            $('#paso_3').show('slow');
        } else if($selectCurso.val() == '[ Seleccione ]'){
            $selectPais.attr('disabled', true);
            $('#list_valores').hide();
            $('#boton_imprimir').hide();
            $('#paso_2').hide();
            $('#paso_3').hide();
        }

        $selectPais.empty();
        $selectPais.append('<option value="">[ Seleccione ]</option>');
        hidratePaises();
    });

    //Cuando cambie el pais hidratar select cidudad
    $selectPais.on('change', function (event) {
        $selectCiudad.empty();
        $selectCiudad.append('<option value="">[ Seleccione ]</option>');
        hidrateCiudades();
        setMonedaByPais();
    });

    //Cuando cambie el pais hidratar select centro
    $selectCiudad.on('change', function (event) {
        $selectCentro.empty();
        $selectCentro.append('<option value="">[ Seleccione ]</option>');
        hidrateCentros();
    });

    $selectCentro.on('change', function (event) {
        $selectSemanas.empty();
        $selectSemanas.append('<option value="">[ Seleccione ]</option>');
        hidrateSemanas();
    });

    $selectSemanas.on('change', function (event) {
        $selectLeccionesSemana.empty();
        $selectLeccionesSemana.append('<option value="">[ Seleccione ]</option>');
        hidrateLeccionesSemana();
    });

    $selectLeccionesSemana.on('change', function (event) {
        $selectJornadaLecciones.empty();
        $selectJornadaLecciones.append('<option value="">[ Seleccione ]</option>');
        hidrateJornada();
    });

    $selectAlojamiento.on('change', function (event) {
        if ($selectAlojamiento.val() == 'SI') {
            $selectSemanaAlojamiento.prop('disabled', false);
            $selectTipoAlojamiento.prop('disabled', false);
            $selectTipoHabitacion.prop('disabled', false);
            $selectAlimentacion.prop('disabled', false);

            $selectTipoHabitacion.empty();
            $selectTipoHabitacion.append('<option value="">[ Seleccione ]</option>');

            $selectTipoAlojamiento.empty();
            $selectTipoAlojamiento.append('<option value="">[ Seleccione ]</option>');
            hidrateSelectsForAlojamiento();
        } else if ($selectAlojamiento.val() == 'NO')
        {
            $selectSemanaAlojamiento.prop('disabled', true);
            $selectTipoAlojamiento.prop('disabled', true);
            $selectTipoHabitacion.prop('disabled', true);
            $selectAlimentacion.prop('disabled', true);
        }
        getValuesBadges();
    });

    $selectTipoHabitacion.on('change', function (event) {
        $selectAlimentacion.empty();
        $selectAlimentacion.append('<option value="">[ Seleccione ]</option>');
        hidrateAlimentacion();
    });

    $selectJornadaLecciones.on('change', function (event)
    {
        getValuesBadges();
    });

    $selectMoneda.on('change', function (event)
    {
        getValuesBadges();
    });

    $selectAlimentacion.on('change', function (event)
    {
        getValuesBadges();
        saveTipoAlimentacion();
    });

    $selectTraslado.on('change', function (event)
    {
        getValuesBadges();
    });

    function saveTipoAlimentacion() {
        var textSelected = $selectAlimentacion.find(":selected").text();
        var url = '/saveTipoAlimentacion';// + encodeURIComponent(textSelected);
        $.post(url, {tipoAlimentacion: textSelected});
    }

    function setMonedaByPais() {
        var url = '/moneda/' + $selectPais.val();
        $.post(url, function (value) {
            $("#form_moneda").val($.parseJSON(value));
        }, "html");
    }

    //Agregar datos al select Pais
    function hidratePaises() {
        var url = '/pais/' + $selectCurso.val();
        $.post(url, function (data) {
            var ciudades = $.parseJSON(data);
            $.each(ciudades, function (key, value) {
                $selectPais.append('<option value=' + key + '>' + value + '</option>');
            });

        }, "html");
    }

    //Agregar datos al select Ciudad
    function hidrateCiudades() {
        var url = '/ciudad/' + $selectCurso.val()+'/'+$selectPais.val();
        $.post(url, function (data) {
            var ciudades = $.parseJSON(data);
            $.each(ciudades, function (key, value) {
                $selectCiudad.append('<option value=' + key + '>' + value + '</option>');
            });

        }, "html");
    }

    //Agregar datos al select Centro
    function hidrateCentros() {
        var url = '/centro/' + $selectCiudad.val();
        $.post(url, function (data) {
            var ciudades = $.parseJSON(data);
            $.each(ciudades, function (key, value) {
                $selectCentro.append('<option value=' + key + '>' + value + '</option>');
            });

        }, "html");
    }

    //Agregar datos al select Semanas
    function hidrateSemanas() {
        var url = '/semana/' + $selectCentro.val() + '/' + $selectCurso.val();
        $.post(url, function (data) {
            var semanas = $.parseJSON(data);
            $.each(semanas, function (key, value) {
                $selectSemanas.append('<option value=' + key + '>' + value + '</option>');
            });

        }, "html");
    }

    //Agregar datos al select Centro
    function hidrateLeccionesSemana() {
        var url = '/semanalecciones/' + $selectCentro.val() + '/' + $selectCurso.val() + '/' + $selectSemanas.val();
        $.post(url, function (data) {
            var ciudades = $.parseJSON(data);
            $.each(ciudades, function (key, value) {
                $selectLeccionesSemana.append('<option value=' + key + '>' + value + '</option>');
            });

        }, "html");
    }

    //Agregar datos al select Centro
    function hidrateJornada() {
        var url = '/jornadalecciones/' + $selectCentro.val() + '/' + $selectCurso.val() + '/' + $selectSemanas.val() + '/' + $selectLeccionesSemana.val();
        $.post(url, function (data) {
            var ciudades = $.parseJSON(data);
            $.each(ciudades, function (key, value) {
                $selectJornadaLecciones.append('<option value=' + key + '>' + value + '</option>');
            });

        }, "html");
    }

    function hidrateSelectsForAlojamiento() {
        var urlAlojamiento = '/tipoAlojamiento';
        var urlHabitacion = '/tipoHabitacion';

        $.post(urlAlojamiento, function (data) {
            var datos = $.parseJSON(data);
            $.each(datos, function (key, value) {
                $selectTipoAlojamiento.append('<option value=' + key + '>' + value + '</option>');
            });

        }, "html");

        $.post(urlHabitacion, function (data) {
            var datos = $.parseJSON(data);
            $.each(datos, function (key, value) {
                $selectTipoHabitacion.append('<option value=' + key + '>' + value + '</option>');
            });

        }, "html");
    }

    function hidrateAlimentacion(){
        var urlAlimentacion = '/tipoAlimentacion/'+$selectTipoHabitacion.val()+'/'+$selectTipoAlojamiento.val()+'/'+$selectCentro.val();
        $.post(urlAlimentacion, function (data) {
            var datos = $.parseJSON(data);
            $.each(datos, function (key, value) {
                $selectAlimentacion.append('<option value=' + key + '>' + value + '</option>');
            });

        }, "html");
    }

    function getValuesBadges() {
        if ($selectAlojamiento.val() == 'SI'){
            var alojamiento = $selectAlojamiento.val();
            var semanasAlojamiento = $selectSemanaAlojamiento.val();
            var tipoAlojamiento = $selectTipoAlojamiento.val();
            var tipoHabitacion = $selectTipoHabitacion.val();
            var tipoAlimentacion = $selectAlimentacion.val();
        } else {
            var alojamiento = 0;
            var semanasAlojamiento = 0;
            var tipoAlojamiento = 0;
            var tipoHabitacion = 0;
            var tipoAlimentacion = 0;
        }

        if ($selectTraslado.val() == 'SI') {
            var traslado = $selectTraslado.val();
        } else {
            var traslado = 0;
        }

        var url =
            '/cotizacion/' +
            $selectCurso.val() + '/' +
            $selectPais.val() + '/' +
            $selectSemanas.val() + '/' +
            $selectLeccionesSemana.val() + '/' +
            $selectJornadaLecciones.val() + '/' +
            $selectMoneda.val() + '/' +
            $selectCiudad.val() + '/' +
            $selectCentro.val() + '/' +
            alojamiento + '/' +
            semanasAlojamiento + '/' +
            tipoAlojamiento + '/' +
            tipoHabitacion + '/' +
            tipoAlimentacion + '/' +
            traslado
        ;

        $.post(url, function (data) {
            var datos = $.parseJSON(data);
            $('#CURSO').html(datos['CURSO']);
            $('#REGISTRO').html(datos['REGISTRO']);
            $('#MATERIALES').html(datos['MATERIALES']);
            $('#ESTADIA').html(datos['ESTADIA']);
            $('#TRASLADO').html(datos['TRASLADO']);
            $('#FINANCIEROS').html(datos['FINANCIEROS']);
            $('#VISA').html(datos['VISA']);
            $('#TOTAL').html(datos['TOTAL']);
            $('#ASISTENCIA').html(datos['ASISTENCIA']);
            $('#PESOS').html(datos['TOTAL_PESOS']);
        }, "html");
    }

    onLoad();
});