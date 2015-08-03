<?php
/**
 * Created by TS.
 * User: TS
 * Date: 01/08/2015
 * Time: 13:38
 */

namespace APP\Controller;

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
        $factory->get('/', 'APP\Controller\CotizadorController::index');
        $factory->post('/pais/{id}', 'APP\Controller\CotizadorController::getPais');
        $factory->post('/centro/{id}', 'APP\Controller\CotizadorController::getCentroEducativo');
        $factory->post('/moneda/{id}', 'APP\Controller\CotizadorController::getMoneda');

        return $factory;
    }

    /**
     * @param Application $app
     * @param $id
     * @return JsonResponse
     */
     public function getPais(Application $app, $id)
    {
        $ciudades = $app['idiorm.db']
            ->for_table('ciudad')
            ->where('idPais', $id)
            ->findMany()
        ;

        $elemento = [];
        foreach ($ciudades as $ciudad) {
            $elemento[$ciudad->idCiudad]=$ciudad->nombre;
        }

        return new JsonResponse($elemento);
    }

    /**
     * @param Application $app
     * @param $id
     * @return JsonResponse
     */
    public function getMoneda(Application $app, $id)
    {
        $pais = $app['idiorm.db']
            ->for_table('pais')
            ->where('idPais', $id)
            ->findOne()
        ;

        return new JsonResponse($pais->moneda);
    }

    /**
     * @param Application $app
     * @param $id
     * @return JsonResponse
     */
    public function getCentroEducativo(Application $app, $id)
    {
        $centros = $app['idiorm.db']
            ->for_table('centroeducativo')
            ->where('idCiudad', $id)
            ->findMany()
        ;

        $elemento = [];
        foreach ($centros as $centro) {
            $elemento[$centro->idCentroEducativo]=$centro->nombre;
        }

        return new JsonResponse($elemento);
    }

    /**
     * @param Application $app
     * @param Request $request
     * @return mixed
     */
    public function index(Application $app, Request $request)
    {
        $cursos = $app['idiorm.db']
            ->for_table('curso')
            ->findMany()
        ;

        $paises = $app['idiorm.db']
            ->for_table('pais')
            ->findMany()
        ;

        $monedas = $app['idiorm.db']
            ->for_table('moneda')
            ->findMany()
        ;

        $elementos = [];
        foreach ($cursos as $elemento) {
            $elementos['curso'][$elemento->idCurso] = $elemento->nombre;
        }

        foreach ($paises as $elemento) {
            $elementos['pais'][$elemento->idPais] = $elemento->nombre;
        }

        foreach ($monedas as $elemento) {
            $elementos['moneda'][$elemento->sigla] = $elemento->nombreMoneda;
        }

        $form = $this->getForm($app['form.factory'], $elementos);

        return $app['twig']->render('index.twig', ['form' => $form->createView()]);
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
            ->add('pais', 'choice', ['choices' => $registros['pais'], 'placeholder' => '[ Seleccione ]'])
            ->add('ciudad', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('centro', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('semanas', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('lecciones_por_semana', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('jornada_de_lecciones', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            // Segunda sección
            ->add('alojamiento', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('semanas_de_alojamiento', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
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