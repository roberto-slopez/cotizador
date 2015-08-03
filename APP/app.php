<?php

use Silex\Application;
$app = new Application();
$app['debug'] = true;
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\SwiftmailerServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), [
        'locale_fallback' => 'es',
        'translator.messages' => [],
    ]
);
// https://github.com/silexphp/Silex/wiki/Third-Party-ServiceProviders#database
$app->register(
    new \Arseniew\Silex\Provider\IdiormServiceProvider(),
    [
        'idiorm.db.options' => [
            'connection_string' => 'mysql:host=localhost;dbname=cotizacion',
            'username' => 'root',
            'password' => '1234',
        ]
    ]
);
// Twig config  $twig
$app->register(new Silex\Provider\TwigServiceProvider(), [
        'twig.path' => __DIR__.'/../views',
        'twig.templates' => ['form' => __DIR__.'/../views/form_div_layout.html.twig'],
    ]
);
$app['twig']->addExtension(new \Entea\Twig\Extension\AssetExtension($app));

$app->mount('/', new APP\Controller\CotizadorController());

return $app;