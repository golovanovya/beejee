<?php

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

$whoops = new \Whoops\Run();
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
$whoops->register();

$container = require_once 'config' . DIRECTORY_SEPARATOR . 'container.php';

$router = $container->get('router');

$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals();
$response = $router->dispatch($request);

(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
