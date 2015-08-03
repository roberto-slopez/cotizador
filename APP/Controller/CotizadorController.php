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
        return $factory;
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

        $elementos = [];
        foreach ($cursos as $elemento) {
            $elementos['curso'][] = $elemento->nombre;
        }

        foreach ($paises as $elemento) {
            $elementos['pais'][] = $elemento->nombre;
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
            ->add('curso', 'choice', ['choices' => $registros['curso'], 'placeholder' => '[ Seleccione ]'])
            ->add('pais', 'choice', ['choices' => $registros['pais'], 'placeholder' => '[ Seleccione ]'])
            ->add('ciudad', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('centro', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('semanas', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('lecciones_por_semana', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('jornada_de_lecciones', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            //
            ->add('alojamiento', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('semanas_de_alojamiento', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('tipo_de_alojamiento', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('tipo_de_habitacion', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('alimentacion', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('traslado', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            ->add('seguro', 'choice', ['choices' => [], 'placeholder' => '[ Seleccione ]'])
            //
            ->add(
                'moneda',
                'choice',
                ['choices' => ['COP' => 'COP', 'USD' => 'USD', 'EURO' => 'EURO'], 'placeholder' => '[ Seleccione ]']
            );

        return $formBuilder->getForm();
    }
}