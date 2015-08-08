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
            ->findOne();

        $ciudades = $this->orm
            ->for_table('curso')
            ->distinct()
            ->select_many('ciudad.nombre', 'ciudad.idCiudad')
            ->inner_join('centroeducativo', ['curso.idCentroEducativo', '=', 'centroeducativo.idCentroEducativo'])
            ->inner_join('ciudad', ['centroeducativo.idCiudad', '=', 'ciudad.idCiudad'])
            ->where('curso.nombre', $curso->nombre)
            ->where('curso.idPais', $idPais)
            ->order_by_asc('ciudad.nombre')
            ->find_many();

        $elementos = [];
        foreach ($ciudades as $ciudad) {
            $elementos[$ciudad->idCiudad] = $ciudad->nombre;
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
     * @param $id
     * @return array
     */
    public function getDatosCentroEducativo($id)
    {
        // TODO: implementar.
        //$sql = "SELECT DISTINCT idCentroEducativo  FROM curso  WHERE  nombre ='$curso' AND idPais=$paisCentro ";
        //$sql2 = "SELECT  nombre , idCentroEducativo  FROM centroeducativo  WHERE  idCentroEducativo=$row[0] AND idCiudad = $ciudadCentro";

        $centros = $this->orm
            ->for_table('centroeducativo')
            ->where('idCiudad', $id)
            ->findMany();

        $elementos = [];
        foreach ($centros as $centro) {
            $elementos[$centro->idCentroEducativo] = $centro->nombre;
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
            ->order_by_asc('semanasCurso')
            ->findMany();

        $elementos = [];
        foreach ($datos as $semanaCurso) {
            $elementos[$semanaCurso->idCurso] = $semanaCurso->semanasCurso;
        }

        return $elementos;
    }

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
        //$sql = "SELECT DISTINCT jornadaLecciones FROM curso
        // WHERE idCentroEducativo = $centro AND
        // nombre = '$curso' and semanasCurso=$semanas_curso AND
        // leccionesSemana= $leccionesSemana
        // ORDER BY jornadaLecciones ASC";

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
            ->where('jornadaLecciones', $jornadas)
            ->findOne()
        ;

        $moneda = $this->orm
            ->for_table('moneda')
            ->where('sigla', $monedaSigla)
            ->findOne()
        ;

        $estadia  = 0;
        // ver si es posible usar una constante
        if ($alojamiento == 'SI') {
            // se suma 1 por que al construir un array con range los indices inician en 0 TODO: mejorar
            $numeroSemanas = $semanasAlojamiento != null ? (int)$semanasAlojamiento + 1 : 0;
            $estadia = $numeroSemanas > 0 ? $tipoAlimentacion * $numeroSemanas :$tipoAlimentacion;
        }

        $elementos = [];
        $elementos['CURSO'] = $dato->valorCurso;
        $elementos['REGISTRO'] = $dato->valorInscripcion;
        $elementos['MATERIALES'] = $dato->materiales;
        $elementos['TRASLADO'] = $traslado ? $dato->traslado : 0;
        $elementos['FINANCIEROS'] = $dato->gastosEnvio;
        $elementos['VISA'] = $dato->derechosVisa;
        $elementos['ESTADIA'] = $estadia;
        $elementos['ASISTENCIA'] = 0;

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

        $totalConvertido = $moneda ? $total * $moneda->valorRespectoDolar :$total;
        $sigla = $moneda ? $moneda->sigla : 'dolar';

        $elementos['TOTAL'] =sprintf('%s %s',$this->getSimboloMoneda($sigla),$totalConvertido);

        // TODO: no mejorar
        $peso = 0;
        if ($moneda != 'p') {
            $monedaQuery = $this->orm
                ->for_table('moneda')
                ->where('sigla', 'p')
                ->findOne()
            ;

            $peso = $monedaQuery->valorRespectoDolar;
        }


        $datos['BADGE'] = [
            'CURSO' => round($elementos['CURSO'] * $moneda->valorRespectoDolar, 2),
            'REGISTRO' => round($elementos['REGISTRO'] * $moneda->valorRespectoDolar, 2),
            'MATERIALES' => round($elementos['MATERIALES'] * $moneda->valorRespectoDolar, 2),
            'ESTADIA' => round($elementos['ESTADIA'] * $moneda->valorRespectoDolar, 2),
            'TRASLADO' => round($elementos['TRASLADO'] * $moneda->valorRespectoDolar, 2),
            'FINANCIEROS' => round($elementos['FINANCIEROS'] * $moneda->valorRespectoDolar, 2),
            'ASISTENCIA' => round($elementos['ASISTENCIA'] * $moneda->valorRespectoDolar, 2),
            'VISA' => round($elementos['VISA'] * $moneda->valorRespectoDolar, 2),
            'TOTAL' => round($total * $moneda->valorRespectoDolar, 2),
            'TOTAL_PESOS' => round($total * $peso, 2),
        ];

        $pais = $this->orm->for_table('pais')->select('nombre')->where('idPais', $pais)->findOne();
        $ciudad = $this->orm->for_table('ciudad')->select('nombre')->where('idCiudad', $cuidad)->findOne();
        $centro = $this->orm
            ->for_table('centroeducativo')
            ->select('nombre')
            ->where('idCentroEducativo', $centro)
            ->findOne()
        ;

        $tipoAlojamientoResult = '--';
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
            'TIPO_MONEDA' => $moneda ? $moneda->nombreMoneda : 'Dolar',
            'CURSO' => $datoCurso->nombre,
            'PAIS' => $pais->nombre,
            'CIUDAD' => $ciudad->nombre,
            'CENTRO' => $centro->nombre,
            'SEMANAS_CURSO' => $semanasCurso->semanasCurso,
            'LECCIONES_SEMANA' => $leccionesSemana->leccionesSemana,
            'JORNADA_LECCIONES' => $jornadas,
            'ALOJAMIENTO' => $alojamiento ? $alojamiento : '--',
            'SEMANA_ALOJAMIENTO' => $semanasAlojamiento ? $semanasAlojamiento:'--',
            'TIPO_ALOJAMIENTO' => $tipoAlojamientoResult,
            'TIPO_HABITACION' => isset($this->tipoHabitacion[$tipoHabitacion]) ? $this->tipoHabitacion[$tipoHabitacion] : '--',
            'TIPO_TRASLADO' => (bool) $traslado ? $traslado : '--',
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
}