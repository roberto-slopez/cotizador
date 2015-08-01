<?php
/**
 * Created by TS.
 * User: TS
 * Date: 01/08/2015
 * Time: 13:38
 */

namespace APP\Controller;

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

        return $app['twig']->render('index.twig', []);
    }
}