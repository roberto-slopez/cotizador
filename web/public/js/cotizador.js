/*$( window ).load(function() {
 });*/
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
            $('#list_valores').slideToggle('slow');
            $('#boton_imprimir').slideToggle('slow');
            $('#paso_2').slideToggle('slow');
            $('#paso_3').slideToggle('slow');
        } else {
            $selectPais.attr('disabled', true);
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
            hidrateSelectsForAlojamiento()
        }
    });

    $selectTipoHabitacion.on('change', function (event) {
        if ($selectAlojamiento.val() == 'SI') {
            hidrateAlimentacion()
        }
    });

    $buttonCalcular.click(function () {
        getValuesBadges();
    });

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
        var url = '/ciudad/' + $selectCurso.val();
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
            $selectAlojamiento.val() + '/' +
            $selectSemanaAlojamiento.val() + '/' +
            $selectTipoAlojamiento.val() + '/' +
            $selectTipoHabitacion.val() + '/' +
            $selectAlimentacion.val() + '/' +
            $selectTraslado.val()
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
        }, "html");
    }

    onLoad();
});