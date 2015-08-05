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
            ->findMany();

        $monedas = $this->orm
            ->for_table('moneda')
            ->findMany();

        $elementos = [];
        foreach ($cursos as $elemento) {
            $elementos['curso'][$elemento->idCurso] = $elemento->nombre;
        }

        foreach ($monedas as $elemento) {
            $elementos['moneda'][$elemento->sigla] = $elemento->nombreMoneda;
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
            ->where('idCurso', $semanasCurso)
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
            $elementos[$jornadaLecciones->idCurso] = $jornadaLecciones->jornadaLecciones;
        }

        return $elementos;
    }
}