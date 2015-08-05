<?php
/**
 * Created by TS.
 * User: TS
 * Date: 01/08/2015
 * Time: 13:38
 */

namespace APP\Controller;

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

        return $factory;
    }

    /**
     * @param Application $app
     * @param Request $request
     * @return mixed
     */
    public function index(Application $app, Request $request)
    {
        $form = $this->getForm($app['form.factory'], $app['cotizacion.repository']->getDatosIniciales());

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
     * @param FormFactory $formFactory
     * @param array $registros
     * @return \Symfony\Component\Form\Form
     */
    private function getForm(FormFactory $formFactory, $registros = [])
    {
        $formBuilder = $formFactory->createBuilder('form', [])
            // Primera sección
            ->add('curso', 'choice', ['choices' => $registros['curso'], 'placeholder' => '[ Seleccione ]'])
            ->add('pais', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('ciudad', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('centro', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('semanas', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('lecciones_por_semana', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('jornada_de_lecciones', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            // Segunda sección
            ->add('alojamiento', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('semanas_de_alojamiento', 'choice', ['choices' => range(1,52), 'placeholder' => '[ Seleccione ]'])
            ->add('tipo_de_alojamiento', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('tipo_de_habitacion', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('alimentacion', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('traslado', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('seguro', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            // Tercera sección
            ->add('moneda', 'choice', ['choices' => $registros['moneda'], 'placeholder' => '[ Seleccione ]']);

        return $formBuilder->getForm();
    }
}