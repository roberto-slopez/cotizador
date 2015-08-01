<?php
require_once __DIR__.'/../vendor/autoload.php';
//Symfony\Component\HttpFoundation\Request::enableHttpMethodParameterOverride();

$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$app = require __DIR__.'/../APP/app.php';
$app->run();