<?php
chdir(dirname(__DIR__));
require 'vendor/autoload.php';

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

    
$container = require_once __DIR__.'/../config/container.php';

$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$strategy = (new League\Route\Strategy\ApplicationStrategy)->setContainer($container);
$router = (new League\Route\Router)->setStrategy($strategy);
$router->map('GET', '/', [App\Controller\JobController::class, 'indexAction']);
$router->map('GET', '/login', [App\Controller\SiteController::class, 'loginAction'])
    ->middleware(new App\Middleware\Anon());
$router->map('POST', '/login', [App\Controller\SiteController::class, 'loginAction']);
$router->map('GET', '/logout', [App\Controller\SiteController::class, 'logoutAction']);
$router->map('GET', '/create', [App\Controller\JobController::class, 'taskFormAction']);
$router->map('POST', '/create', [App\Controller\JobController::class, 'createAction']);
$router->map('GET', '/update/{id:number}', [App\Controller\JobController::class, 'taskFormAction'])
    ->middleware(new App\Middleware\Auth());
$router->map('POST', '/update/{id:number}', [App\Controller\JobController::class, 'updateAction']);
$router->middleware(new Middlewares\AuraSession());

try {
    $response = $router->dispatch($request);
} catch (\League\Route\Http\Exception\NotFoundException $e) {
    $response = new \Laminas\Diactoros\Response();
    $response->getBody()->write($container->get(League\Plates\Engine::class)->render('app/404', ['e' => $e]));
    $response->withStatus(404);
} catch (\Exception $e) {
    if ((bool)getenv('DEBUG') == true) {
        throw $e;
    }
    $response = new \Laminas\Diactoros\Response();
    $response->getBody()->write($container->get(League\Plates\Engine::class)->render('app/error', ['e' => $e]));
    $response->withStatus(500);
}

(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);




