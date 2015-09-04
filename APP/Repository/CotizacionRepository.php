<?php
/**
 * Created by TS.
 * User: ts
 * Date: 4/08/15
 * Time: 09:40 AM
 */

namespace APP\Repository;

use Arseniew\Silex\Service\IdiormService;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints\All;

class CotizacionRepository
{
    const TIPO_HABITACION_SENCILLA = 'habindividual';
    const TIPO_HABITACION_DOBLE = 'habdoble';
    const TIPO_HABITACION_TRIPLE = 'habtriple';

    /**
     * @var IdiormService
     */
    private $orm;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var array
     */
    private $tipoHabitacion = [
        self::TIPO_HABITACION_SENCILLA => 'Sencilla',
        self::TIPO_HABITACION_DOBLE => 'Doble',
        self::TIPO_HABITACION_TRIPLE => 'Triple',
    ];

    private $tipoAlmentacion = [
        1 => 'No Aplica',
        2 => 'Sin alimentación',
        3 => 'Desayuno',
        4 => 'Desayuno y cena',
        5 => 'Derecho Cocina',
    ];

    /**
     * @param IdiormService $orm
     * @param Session $session
     */
    function __construct(IdiormService $orm, Session $session)
    {
        $this->orm = $orm;
        $this->session = $session;
    }

    /**
     * Retorna un array para los datos iniciales.
     *
     * @return array
     */
    public function getDatosIniciales()
    {
        $cursos = $this->orm
            ->for_table('curso')
            ->group_by('nombre')
            ->findMany();

        $monedas = $this->orm
            ->for_table('monedas')
            ->findMany();

        $elementos = [];
        foreach ($cursos as $elemento) {
            $elementos['curso'][$elemento->idCurso] = $elemento->nombre;
        }

        foreach ($monedas as $elemento) {
            $elementos['moneda'][$elemento->sigla] = $elemento->moneda;
        }

        return $elementos;
    }

    /**
     * @param $id idCurso
     * @return mixed
     */
    public function getDatosPais($id)
    {
        $curso = $this->orm
            ->for_table('curso')
            ->select('nombre')
            ->where('idCurso', $id)
            ->findOne();

        $paises = $this->orm
            ->for_table('curso')
            ->distinct()
            ->select_many('nombrePais', 'idPais')
            ->where('nombre', $curso->nombre)
            ->order_by_asc('nombrePais')
            ->findMany();

        $elementos = [];
        foreach ($paises as $pais) {
            $elementos[$pais->idPais] = $pais->nombrePais;
        }

        return $elementos;
    }

    /**
     * @param $idCurso
     * @param $idPais
     * @return array
     */
    public function getDatosCiudad($idCurso, $idPais)
    {
        $curso = $this->orm
            ->for_table('curso')
            ->where('idCurso', $idCurso)
            ->findOne()
        ;

        $ciudades = $this->orm
            ->for_table('curso')
            ->distinct()
            ->select_many('ciudad.nombre', 'ciudad.idCiudad')
            ->inner_join('centroeducativo', ['curso.idCentroEducativo', '=', 'centroeducativo.idCentroEducativo'])
            ->inner_join('ciudad', ['centroeducativo.idCiudad', '=', 'ciudad.idCiudad'])
            ->where('curso.nombre', $curso->nombre)
            ->where('curso.idPais', $idPais)
            ->order_by_asc('ciudad.nombre')
            ->find_many()
        ;

        $elementos = [];

        foreach ($ciudades as $ciudad) {
            $elementos[$ciudad->idCiudad] = utf8_encode($ciudad->nombre);
        }

        return $elementos;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getMonedaByPaisId($id)
    {
        $pais = $this->orm
            ->for_table('pais')
            ->where('idPais', $id)
            ->findOne();

        return $pais->moneda;
    }

    /**
     * @param $idCurso
     * @param $idPais
     * @param $idCiudad
     * @return array
     */
    public function getDatosCentroEducativo($idCurso, $idPais, $idCiudad)
    {
        $curso = $this->orm
            ->for_table('curso')
            ->select('nombre')
            ->where('idCurso', $idCurso)
            ->findOne()
        ;

        $centros = $this->orm
            ->for_table('curso')
            ->distinct()
            ->select('idCentroEducativo')
            ->where('nombre', $curso->nombre)
            ->where('idPais', $idPais)
            ->find_many()
        ;

        $elementos = [];
        foreach ($centros as $centro) {
            $centroQ = $this->orm
                ->for_table('centroeducativo')
                ->select_many('nombre', 'idCentroEducativo')
                ->where('idCentroEducativo', $centro->idCentroEducativo)
                ->where('idCiudad', $idCiudad)
                ->findOne()
            ;

            if ($centroQ) {
                $elementos[$centroQ->idCentroEducativo] = $centroQ->nombre;
            }
        }

        return $elementos;
    }

    /**
     * @param $idCentro
     * @param $nombreCurso
     * @return array
     */
    public function getDatosSemanaCurso($idCentro, $nombreCurso)
    {
        $curso = $this->orm
            ->for_table('curso')
            ->where('idCurso', $nombreCurso)
            ->findOne()
        ;

        $datos = $this->orm
            ->for_table('curso')
            ->distinct()
            ->select_many('semanasCurso', 'idCurso')
            ->where('idCentroEducativo', $idCentro)
            ->where('nombre', $curso->nombre)
            ->group_by('semanasCurso')
            ->order_by_asc('semanasCurso')
            ->findMany();

        $elementos = [];
        foreach ($datos as $semanaCurso) {
            $elementos[$semanaCurso->idCurso] = $semanaCurso->semanasCurso;
        }

        return $elementos;
    }

    /**
     * @param $idCentro
     * @param $nombreCurso
     * @param $semanasCurso
     * @return array
     */
    public function getDatosLeccionSemana($idCentro, $nombreCurso, $semanasCurso)
    {
        if ($nombreCurso == $semanasCurso) {
            $curso = $this->orm
                ->for_table('curso')
                ->where('idCurso', $nombreCurso)
                ->findOne();
            $cursoNombre = $curso->nombre;
            $cursoSemanas = $curso->semanasCurso;
        } else {
            $curso = $this->orm
                ->for_table('curso')
                ->where('idCurso', $nombreCurso)
                ->findOne();
            $cursoNombre = $curso->nombre;

            $curso = $this->orm
                ->for_table('curso')
                ->where('idCurso', $semanasCurso)
                ->findOne();
            $cursoSemanas = $curso->semanasCurso;
        }

        $datos = $this->orm
            ->for_table('curso')
            ->select_many('leccionesSemana', 'idCurso')
            ->where('idCentroEducativo', $idCentro)
            ->where('nombre', $cursoNombre)
            ->where('semanasCurso', $cursoSemanas)
            ->order_by_asc('semanasCurso')
            ->group_by('leccionesSemana')
            ->findMany()
        ;

        $elementos = [];
        foreach ($datos as $leccionesSemana) {
            $elementos[$leccionesSemana->idCurso] = $leccionesSemana->leccionesSemana;
        }

        return $elementos;
    }

    /**
     * @param $idCentro
     * @param $nombreCurso
     * @param $semanasCurso
     * @param $leccionesSemana
     * @return array
     */
    public function getDatosJornadaLecciones($idCentro, $nombreCurso, $semanasCurso, $leccionesSemana)
    {
        $curso = $this->orm
            ->for_table('curso')
            ->where('idCurso', $nombreCurso)
            ->findOne()
        ;
        $cursoNombre = $curso->nombre;
        $curso = $this->orm
            ->for_table('curso')
            ->where('idCurso', $semanasCurso)
            ->findOne()
        ;
        $cursoSemanas = $curso->semanasCurso;
        $curso = $this->orm
            ->for_table('curso')
            ->where('idCurso', $leccionesSemana)
            ->findOne()
        ;
        $semanaLecciones = $curso->leccionesSemana;

        $datos = $this->orm
            ->for_table('curso')
            ->distinct()
            ->select('jornadaLecciones')
            ->where('idCentroEducativo', $idCentro)
            ->where('nombre', $cursoNombre)
            ->where('semanasCurso', $cursoSemanas)
            ->where('leccionesSemana', $semanaLecciones)
            ->order_by_asc('jornadaLecciones')
            ->findMany()
        ;

        $elementos = [];
        foreach ($datos as $jornadaLecciones) {
            $elementos[$jornadaLecciones->jornadaLecciones] = $jornadaLecciones->jornadaLecciones;
        }

        return $elementos;
    }

    /**
     * @return array
     */
    public function getDatosTipoAlojamiento()
    {
        $datos = $this->orm
            ->for_table('tipoalojamiento')
            ->findMany()
        ;

        $elementos = [];
        foreach ($datos as $dato) {
            $elementos[$dato->idTipoAlojamiento] = $dato->tipoHabitacion;
        }

        return $elementos;
    }

    /**
     * @return array
     */
    public function getDatosTipoHabitacion()
    {
        $elementos[self::TIPO_HABITACION_SENCILLA] = 'Sencilla';
        $elementos[self::TIPO_HABITACION_DOBLE] = 'Doble';
        $elementos[self::TIPO_HABITACION_TRIPLE] = 'Triple';

        return $elementos;
    }

    /**
     * @param string $tipoHabitacion contiene el nombre de la tabla referente al tipo de habitación
     * @param $tipoAlojamiento
     * @param $centro
     * @return array
     */
    public function getDatosTipoAlimentacion($tipoHabitacion, $tipoAlojamiento, $centro)
    {
        $datos = $this->orm
            ->for_table($tipoHabitacion)
            ->select_many('sinComida', 'desayuno', 'mediaPension', 'completa', 'derechoCocina')
            ->where('idCentroEducativo', $centro)
            ->where('tipoAlojamiento', $tipoAlojamiento)
            ->findOne()
        ;
        $elementos = [];

        if (!$datos) {
            $elementos [0] = 'No Aplica';
        } elseif (
            $datos->sinComida == 0 &&
            $datos->desayuno == 0 &&
            $datos->mediaPension == 0 &&
            $datos->completa == 0 &&
            $datos->derechoCocina == 0
        ) {
            $elementos [0] = 'No Aplica';
        } else {
            if ($datos->sinComida != 0) {
                $elementos[$datos->sinComida] = 'Sin alimentación';
            }
            if ($datos->desayuno != 0) {
                $elementos[$datos->desayuno] = 'Desayuno';
            }
            if ($datos->mediaPension != 0) {
                $elementos[$datos->mediaPension] = 'Desayuno y cena';
            }
            if ($datos->derechoCocina != 0) {
                $elementos[$datos->derechoCocina] = 'Derecho Cocina';
            }
        }

        return $elementos;
    }

    /**
     * @param $curso
     * @param $pais
     * @param $semanas
     * @param $lecciones
     * @param $jornadas
     * @param $monedaSigla
     * @param $cuidad
     * @param $centro
     * @param $alojamiento
     * @param $semanasAlojamiento
     * @param $tipoAlojamiento
     * @param $tipoHabitacion
     * @param $tipoAlimentacion
     * @param $traslado
     * @return array
     */
    public function getResultCalculo(
        $curso,
        $pais,
        $semanas,
        $lecciones,
        $jornadas,
        $monedaSigla,
        $cuidad,
        $centro,
        $alojamiento,
        $semanasAlojamiento,
        $tipoAlojamiento,
        $tipoHabitacion,
        $tipoAlimentacion,
        $traslado
    )
    {
        $traslado = $traslado == 'SI' ? true :false;
        $datoCurso= $this->orm
            ->for_table('curso')
            ->select('nombre')
            ->where('idCurso', $curso)
            ->findOne()
        ;

        $semanasCurso = $this->orm
            ->for_table('curso')
            ->select('semanasCurso')
            ->where('idCurso', $semanas)
            ->findOne()
        ;

        $leccionesSemana = $this->orm
            ->for_table('curso')
            ->select('leccionesSemana')
            ->where('idCurso', $lecciones)
            ->findOne()
        ;

        $dato = $this->orm
            ->for_table('curso')
            ->where('nombre', $datoCurso->nombre)
            ->where('idPais', $pais)
            ->where('semanasCurso', $semanasCurso->semanasCurso)
            ->where('leccionesSemana', $leccionesSemana->leccionesSemana)
            ->where('idCentroEducativo', $centro)
            ->where('jornadaLecciones', $jornadas)
            ->findOne()
        ;

        $moneda = $this->orm
            ->for_table('moneda')
            ->where('sigla', $monedaSigla)
            ->findOne()
        ;

        $estadia  = 0;
        $numeroSemanas = $semanasAlojamiento != null ? (int)$semanasAlojamiento + 1 : 0;
        // ver si es posible usar una constante
        if ($alojamiento == 'SI') {
            // se suma 1 por que al construir un array con range los indices inician en 0 TODO: mejorar
            $estadia = $numeroSemanas > 0 ? $tipoAlimentacion * $numeroSemanas :$tipoAlimentacion;
        }

        $elementos = [];
        $datos = [];

        $monedaLocal = $this->orm
            ->for_table('moneda')
            ->where('sigla', $dato->tipoMoneda)
            ->findOne()
        ;

        $valorCurso = $dato->valorCurso;
        $valorInscripcion = $dato->valorInscripcion;
        $valorMateriales = $dato->materiales;
        $valorTraslado = $traslado ? ($dato->traslado > 0 ? $dato->traslado: 0.00) : 0;
        $valorGastosEnvio = $dato->gastosEnvio;
        $valorDerechoVisa = $dato->derechosVisa;
        $valorEstadia = $estadia;
        $valorSeguroCurso = $dato ? $dato->seguro : 0;

        $elementos['CURSO'] = $valorCurso;
        $elementos['REGISTRO'] = $valorInscripcion;
        $elementos['MATERIALES'] = $valorMateriales;
        $elementos['TRASLADO'] = $valorTraslado;
        $elementos['FINANCIEROS'] = $valorGastosEnvio;
        $elementos['VISA'] = $valorDerechoVisa;
        $elementos['ESTADIA'] = $valorEstadia;
        // el valor de seguro va en asistencia.
        $elementos['ASISTENCIA'] = $valorSeguroCurso;

        $total = round(
            $elementos['CURSO'] +
            $elementos['REGISTRO'] +
            $elementos['MATERIALES'] +
            $elementos['TRASLADO'] +
            $elementos['FINANCIEROS'] +
            $elementos['VISA'] +
            $elementos['ESTADIA'] +
            $elementos['ASISTENCIA'],
            2
        );

        $sigla = $moneda ? $moneda->sigla : 'd';
        //$valorRespectoDolar = round($sigla == 'd' ? (int)1 : $moneda->valorRespectoDolar,0);
        //$totalConvertido = $moneda ? ($total * $valorRespectoDolar) : $total;

        $elementos['TOTAL'] = sprintf('%s %s', $this->getSimboloMoneda($sigla), number_format($total, 0));

        $pesoValorRespectoDolar  = 1;
        if ($monedaSigla != 'p') {
            $monedaQuery = $this->orm
                ->for_table('moneda')
                ->where('sigla', 'p')
                ->findOne()
            ;

            $pesoValorRespectoDolar = $monedaQuery->valorRespectoDolar;
        }

        $conversionAPesos = 1;
        if ($monedaLocal) {
            if ($monedaLocal->sigla != 'p') {
                $conversionAPesos = $monedaLocal->valorRespectoDolar;
            }
        }

        $pesoValorRespectoDolar = $pesoValorRespectoDolar ?: 1;
        $conversionAPesos = $conversionAPesos ?: 1;

        $totalEnPesos = round(($pesoValorRespectoDolar * $conversionAPesos) * $total, 2);

        $datos['BADGE'] = [
            'CURSO' => round(isset($elementos['CURSO']) ? $elementos['CURSO'] : 0, 2),
            'REGISTRO' => round(isset($elementos['REGISTRO']) ? $elementos['REGISTRO'] : 0, 2),
            'MATERIALES' => round(isset($elementos['MATERIALES']) ? $elementos['MATERIALES'] : 0, 2),
            'ESTADIA' => round(isset($elementos['ESTADIA']) ? $elementos['ESTADIA'] : 0, 2),
            'TRASLADO' => round(isset($elementos['TRASLADO']) ? $elementos['TRASLADO'] : 0, 2),
            'FINANCIEROS' => round(isset($elementos['FINANCIEROS']) ? $elementos['FINANCIEROS'] : 0, 2),
            'ASISTENCIA' => round(isset($elementos['ASISTENCIA']) ? $elementos['ASISTENCIA'] : 0, 2),
            'VISA' => round($elementos['VISA'], 2),
            'TOTAL' => round($total),
            'TOTAL_PESOS' => $totalEnPesos,
            'TOTAL_STRING' => sprintf('%s %s', $this->getSimboloMoneda($sigla), number_format(round($total), 0)),
            'TOTAL_PESOS_STRING' => sprintf('%s %s', 'COL$', number_format($totalEnPesos, 0)),
        ];

        $elementos['TOTAL_PESOS'] = sprintf('%s %s', 'COL$', number_format($totalEnPesos,0));

        $pais = $this->orm->for_table('pais')->select_many('nombre','idPais')->where('idPais', $pais)->findOne();
        $ciudad = $this->orm->for_table('ciudad')->select_many('nombre','idCiudad')->where('idCiudad', $cuidad)->findOne();
        $centro = $this->orm
            ->for_table('centroeducativo')
            ->select_many('nombre','idCentroEducativo')
            ->where('idCentroEducativo', $centro)
            ->findOne()
        ;

        $tipoAlojamientoResult = 0;
        if ($alojamiento) {
            $tipoAlojamientoQ  = $this->orm
                ->for_table('tipoalojamiento')
                ->select('tipoHabitacion')
                ->where('idTipoAlojamiento',$tipoAlojamiento)
                ->findOne()
            ;

            $tipoAlojamientoResult = $tipoAlojamientoQ->tipoHabitacion;
        }

        $datos['INFO'] = [
            'ID_PAIS' => $pais->idPais,
            'ID_CIUDAD' => $ciudad->idCiudad,
            'ID_CENTRO' => $centro->idCentroEducativo,
            'ID_ALOJAMIENTO' => $tipoAlojamiento ?: 0,
            'SIGLA_MONEDA'=> (bool) $monedaSigla? $monedaSigla: 'd',
            'TIPO_MONEDA' => $moneda ? $moneda->nombreMoneda : 'Dolar',
            'CURSO' => $datoCurso->nombre,
            'PAIS' => $pais->nombre,
            'CIUDAD' => $ciudad->nombre,
            'CENTRO' => $centro->nombre,
            'SEMANAS_CURSO' => $semanasCurso->semanasCurso,
            'LECCIONES_SEMANA' => $leccionesSemana->leccionesSemana,
            'JORNADA_LECCIONES' => $jornadas,
            'ALOJAMIENTO' => $alojamiento ? $alojamiento : 'NO',
            'SEMANA_ALOJAMIENTO' => $numeroSemanas  ? $numeroSemanas :0,
            'TIPO_ALOJAMIENTO' => $tipoAlojamientoResult,
            'TIPO_HABITACION' => isset($this->tipoHabitacion[$tipoHabitacion]) ? $this->tipoHabitacion[$tipoHabitacion] : 0,
            'TRASLADO' => $traslado ? $traslado : 'NO',
        ];

        $this->session->set('TSdatosCotizacion', null);
        $this->session->set('TSdatosCotizacion', $datos);

        return $elementos;
    }

    /**
     * @param $sigla
     * @return string
     */
    public function getSimboloMoneda($sigla)
    {
        switch ($sigla) {
            case 'l':
                //'li2bra';
                return '£';
                break;
            case 'p':
                //'Peso';
                return 'COL$';
                break;
            case 'e':
                //'Euro';
                return '€';
                break;
            case 'cad':
                //'Dolar Canadiense'
                return 'C$';
                break;
            case 'aud':
                //'Dolar Australiano'
                return 'A$';
                break;
            case 'nzd':
                //'Dolar Neozelandes'
                return 'NZ$';
                break;
            default:
                // Dolas Estado Unidense.
                return '$';
                break;
        }
    }

    /**
     * @return string
     */
    public function getFechaString()
    {
        $dias = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
        $meses = [
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre",
        ];

        return $dias[date('w')]." ".date('d')." de ".$meses[date('n') - 1]." del ".date('Y');
    }

    /**
     * @return null
     */
    public function saveCotizacion()
    {
        $datos = $this->session->get('TSdatosCotizacion');
        $cotizacion = $this->orm->for_table('cotizaciones')->create();
        $today = new \DateTime('today');
        $cotizacion->set(
            [
                'fecha' => $today->format('Y-m-d'),
                'curso' => $datos['INFO']['CURSO'],
                'pais' => $datos['INFO']['ID_PAIS'],
                'ciudad' => $datos['INFO']['ID_CIUDAD'],
                'centro' => $datos['INFO']['ID_CENTRO'],
                'semanascurso' => $datos['INFO']['SEMANAS_CURSO'],
                'leccionessemana' => $datos['INFO']['LECCIONES_SEMANA'],
                'jornadalecciones' => $datos['INFO']['JORNADA_LECCIONES'],
                'alojamiento' => $datos['INFO']['ALOJAMIENTO'],
                'semanas_alojamiento' => $datos['INFO']['SEMANA_ALOJAMIENTO'],
                'tipo_alojamiento' => $datos['INFO']['ID_ALOJAMIENTO'],
                'tipo_alimentacion' => $this->session->get('TSTipoAlimentacion') ?: 0,
                'traslado' => $datos['INFO']['TRASLADO'],
                'seguro' => 'NO',
                'pasaje' => 'NO',
                'tipo_moneda' => $datos['INFO']['SIGLA_MONEDA'],
                'valor_curso' => $datos['BADGE']['CURSO'] + $datos['BADGE']['MATERIALES'],
                'valor_inscripcion' => $datos['BADGE']['REGISTRO'],
                'valor_alojamiento' => $datos['BADGE']['ESTADIA'],
                'valor_traslado' => $datos['BADGE']['TRASLADO'],
                'valor_envio' => $datos['BADGE']['FINANCIEROS'],
                'valor_seguro' => $datos['BADGE']['ASISTENCIA'],
                'valor_visa' => $datos['BADGE']['VISA'],
                'valor_total' => $datos['BADGE']['TOTAL'],
                'valor_total_pesos' => $datos['BADGE']['TOTAL_PESOS'],
                'obervaciones' => '',
            ]
        );
        $cotizacion->save();

        return $cotizacion->id();
    }
}