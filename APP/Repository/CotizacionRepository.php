<?php
/**
 * Created by TS.
 * User: ts
 * Date: 4/08/15
 * Time: 09:40 AM
 */

namespace APP\Repository;

use Arseniew\Silex\Service\IdiormService;
use Symfony\Component\Validator\Constraints\All;

class CotizacionRepository
{
    const TIPO_HABITACION_SENCILLA = 'habindividual';
    const TIPO_HABITACION_DOBLE = 'habdoble';
    const TIPO_HABITACION_TRIPLE = 'habtriple';

    //const TIPO_HABITACION_
    /**
     * @var IdiormService
     */
    private $orm;

    function __construct(IdiormService $orm)
    {
        $this->orm = $orm;
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
     * @param $id
     * @return array
     */
    public function getDatosCiudad($id)
    {
        $curso = $this->orm
            ->for_table('curso')
            ->where('idCurso', $id)
            ->findOne();

        $ciudades = $this->orm
            ->for_table('curso')
            ->distinct()
            ->select_many('ciudad.nombre', 'ciudad.idCiudad')
            ->inner_join('centroeducativo', ['curso.idCentroEducativo', '=', 'centroeducativo.idCentroEducativo'])
            ->inner_join('ciudad', ['centroeducativo.idCiudad', '=', 'ciudad.idCiudad'])
            ->where('curso.nombre', $curso->nombre)
            ->where('curso.idPais', $curso->idPais)
            ->order_by_asc('ciudad.nombre')
            ->findMany();

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
        //$sql = "SELECT DISTINCT jornadaLecciones FROM curso WHERE idCentroEducativo = $centro AND nombre = '$curso' and semanasCurso=$semanas_curso and leccionesSemana= $leccionesSemana ORDER BY jornadaLecciones ASC";
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
     * @param $tipoHabitacion contiene el nombre de la tabla referente al tipo de habitación
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

        if (!$datos) {
            $elementos [0]='No Aplica';
        } elseif ($datos->sinComida == 0 && $datos->desayuno == 0 && $datos->mediaPension == 0 && $datos->completa == 0 && $datos->derechoCocina == 0){
            $elementos [0]='No Aplica';
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
     * @param $jornadas (ampm or am or pm)
     * @param $moneda
     * @param $cuidad
     * @param $centro
     * @param bool|false $alojamiento
     * @param $semanasAlojamiento
     * @param $tipoAlojamiento
     * @param $tipoHabitacion
     * @param $tipoAlimentacion
     * @param bool|false $traslado
     * @return array
     */
    public function getResultCalculo(
        $curso,
        $pais,
        $semanas,
        $lecciones,
        $jornadas,
        $moneda,
        $cuidad,
        $centro,
        $alojamiento = false,
        $semanasAlojamiento,
        $tipoAlojamiento,
        $tipoHabitacion,
        $tipoAlimentacion,
        $traslado = false
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
            ->where('sigla', $moneda)
            ->findOne()
        ;

        $elementos = [];
        $elementos['CURSO'] = $dato->valorCurso;
        $elementos['REGISTRO'] = $dato->valorInscripcion;
        $elementos['MATERIALES'] = $dato->materiales;
        $elementos['TRASLADO'] = $dato->traslado;
        $elementos['FINANCIEROS'] = $dato->gastosEnvio;
        $elementos['VISA'] = $dato->derechosVisa;
        $elementos['ESTADIA'] = $tipoAlimentacion;
        $elementos['ASISTENCIA'] = 0;
        //TOTAL(badge) = CURSO + REGISTRO + MATERIALES + ESTADIA + TRASLADO + FINANCIEROS + VISA (tipoMoneda)
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
}