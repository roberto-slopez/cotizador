<?php
/**
 * Created by TS.
 * User: TS
 * Date: 01/08/2015
 * Time: 13:38
 */

namespace APP\Controller;

use APP\Form\Type\CotizacionType;
use APP\Repository\CotizacionRepository;
use Arseniew\Silex\Service\IdiormService;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Application;
use Silex\ControllerProviderInterface;

/**
 * Class CotizadorController
 * @package APP\Controller
 */
class CotizadorController implements ControllerProviderInterface
{
    /**
     * Connect function is used by Silex to mount the controller to the application.
     *
     * Please list all routes inside here.
     *
     * @param Application $app Silex Application Object.
     *
     * @return Response Silex Response Object.
     */
    public function connect(Application $app)
    {
        /**
         * @var \Silex\ControllerCollection $factory
         */
        $factory = $app['controllers_factory'];

        // Primer segmento
        $factory->get('/', 'APP\Controller\CotizadorController::index');
        $factory->post('/pais/{id}', 'APP\Controller\CotizadorController::getPais');
        $factory->post('/ciudad/{id}', 'APP\Controller\CotizadorController::getCiudad');
        $factory->post('/centro/{id}', 'APP\Controller\CotizadorController::getCentroEducativo');
        $factory->post('/moneda/{id}', 'APP\Controller\CotizadorController::getMoneda');
        $factory->post('/semana/{idCentro}/{nombreCurso}', 'APP\Controller\CotizadorController::getSemanasCurso');
        $factory->post(
            '/semanalecciones/{idCentro}/{nombreCurso}/{semanasCurso}',
            'APP\Controller\CotizadorController::getLeccionesSemana'
        );
        $factory->post(
            '/jornadalecciones/{idCentro}/{nombreCurso}/{semanasCurso}/{leccionesSemana}',
            'APP\Controller\CotizadorController::getJornadaLecciones'
        );
        $factory->post('/tipoAlojamiento', 'APP\Controller\CotizadorController::getTipoAlojamiento');
        $factory->post('/tipoHabitacion', 'APP\Controller\CotizadorController::getTipoHabitacion');
        $factory->post('/tipoAlimentacion/{tipoHabitacion}/{tipoAlojamiento}/{centro}', 'APP\Controller\CotizadorController::getTipoAlimentacion');
        $factory->post(
            '/cotizacion/{curso}/{pais}/{semanas}/{lecciones}/{jornadas}/{moneda}/{cuidad}/{centro}/{alojamiento}/{semanasAlojamiento}/{tipoAlojamiento}/{tipoHabitacion}/{tipoAlimentacion}/{traslado}',
            'APP\Controller\CotizadorController::getCotizacion'
        );

        return $factory;
    }

    /**
     * @param Application $app
     * @param Request $request
     * @return mixed
     */
    public function index(Application $app, Request $request)
    {
        $form = $app['form.factory']
            ->create(new CotizacionType($app['cotizacion.repository']->getDatosIniciales()), [])
        ;

        return $app['twig']->render('index.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Application $app
     * @param $id
     * @return JsonResponse
     */
     public function getPais(Application $app, $id)
    {
        return new JsonResponse($app['cotizacion.repository']->getDatosPais($id));
    }

    /**
     * @param Application $app
     * @param $id
     * @return JsonResponse
     */
     public function getCiudad(Application $app, $id)
    {
        return new JsonResponse($app['cotizacion.repository']->getDatosCiudad($id));
    }

    /**
     * @param Application $app
     * @param $id
     * @return JsonResponse
     */
    public function getMoneda(Application $app, $id)
    {
        return new JsonResponse($app['cotizacion.repository']->getMonedaByPaisId($id));
    }

    /**
     * @param Application $app
     * @param $id
     * @return JsonResponse
     */
    public function getCentroEducativo(Application $app, $id)
    {
        return new JsonResponse($app['cotizacion.repository']->getDatosCentroEducativo($id));
    }

    /**
     * @param Application $app
     * @param $idCentro
     * @param $nombreCurso
     * @return JsonResponse
     */
    public function getSemanasCurso(Application $app, $idCentro, $nombreCurso)
    {
        return new JsonResponse($app['cotizacion.repository']->getDatosSemanaCurso($idCentro, $nombreCurso));
    }

    /**
     * @param Application $app
     * @param $idCentro
     * @param $nombreCurso
     * @param $semanasCurso
     * @return JsonResponse
     */
    public function getLeccionesSemana(Application $app, $idCentro, $nombreCurso, $semanasCurso)
    {
        return new JsonResponse(
            $app['cotizacion.repository']->getDatosLeccionSemana($idCentro, $nombreCurso, $semanasCurso)
        );
    }

    /**
     * @param Application $app
     * @param $idCentro
     * @param $nombreCurso
     * @param $semanasCurso
     * @param $leccionesSemana
     * @return JsonResponse
     */
    public function getJornadaLecciones(Application $app, $idCentro, $nombreCurso, $semanasCurso, $leccionesSemana)
    {
        return new JsonResponse(
            $app['cotizacion.repository']->getDatosJornadaLecciones(
                $idCentro,
                $nombreCurso,
                $semanasCurso,
                $leccionesSemana
            )
        );
    }

    /**
     * @param Application $app
     * @return JsonResponse
     */
    public function getTipoAlojamiento(Application $app)
    {
        return new JsonResponse($app['cotizacion.repository']->getDatosTipoAlojamiento());
    }

    /**
     * @param Application $app
     * @return JsonResponse
     */
    public function getTipoHabitacion(Application $app)
    {
        return new JsonResponse($app['cotizacion.repository']->getDatosTipoHabitacion());
    }

    /**
     * @param Application $app
     * @param $tipoHabitacion
     * @param $tipoAlojamiento
     * @param $centro
     * @return JsonResponse
     */
    public function getTipoAlimentacion(Application $app, $tipoHabitacion, $tipoAlojamiento, $centro)
    {
        return new JsonResponse($app['cotizacion.repository']->getDatosTipoAlimentacion($tipoHabitacion, $tipoAlojamiento, $centro));
    }

    /**
     * Calcula datos y guarda datos en session para su uso posterior en imprimir.
     *
     * @param Application $app
     * @param $curso
     * @param $pais
     * @param $semanas
     * @param $lecciones
     * @param $jornadas
     * @param $moneda
     * @param $cuidad
     * @param $centro
     * @param $alojamiento
     * @param $semanasAlojamiento
     * @param $tipoAlojamiento
     * @param $tipoHabitacion
     * @param $tipoAlimentacion
     * @param $traslado
     * @return JsonResponse
     */
    public function getCotizacion(
        Application $app,
        $curso,
        $pais,
        $semanas,
        $lecciones,
        $jornadas,
        $moneda,
        $cuidad,
        $centro,
        $alojamiento,
        $semanasAlojamiento,
        $tipoAlojamiento,
        $tipoHabitacion,
        $tipoAlimentacion,
        $traslado
    ) {
        return new JsonResponse(
            $app['cotizacion.repository']->getResultCalculo(
                $curso,
                $pais,
                $semanas,
                $lecciones,
                $jornadas,
                $moneda,
                $cuidad,
                $centro,
                $alojamiento,
                $semanasAlojamiento,
                $tipoAlojamiento,
                $tipoHabitacion,
                $tipoAlimentacion,
                $traslado
            )
        );
    }
}