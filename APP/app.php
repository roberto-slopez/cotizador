<?php

use Silex\Application;
$app = new Application();
$app['debug'] = true;
// debug tool enable for dev.
$app['php-console.settings'] = array(
    'sourcesBasePath' => dirname(__DIR__),
    'serverEncoding' => null,
    'headersLimit' => null,
    'password' => null,
    'enableSslOnlyMode' => false,
    'ipMasks' => array(),
    'isEvalEnabled' => false,
    'dumperLevelLimit' => 5,
    'dumperItemsCountLimit' => 100,
    'dumperItemSizeLimit' => 5000,
    'dumperDumpSizeLimit' => 500000,
    'dumperDetectCallbacks' => true,
    'detectDumpTraceAndSource' => false,
);

$app->register(new PhpConsole\Silex\ServiceProvider($app,
    new \PhpConsole\Storage\File(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'php-console.data') // any writable path
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
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
            'id_column_overrides' => [
                'cotizaciones' => 'cod'
            ],
            'driver_options', [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET CHARACTER SET utf8',
            ]
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

$app['cotizacion.repository'] = $app->share(function() use ($app) {
    return new \APP\Repository\CotizacionRepository($app['idiorm.db'], $app['session']);
});

$app->mount('/', new APP\Controller\CotizadorController());

return $app;