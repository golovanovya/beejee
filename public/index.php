<?php

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

$whoops = new \Whoops\Run();
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
$whoops->register();


$container = require_once __DIR__ . '/../config/container.php';

$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES,
);

$strategy = (new League\Route\Strategy\ApplicationStrategy())->setContainer($container);
$router = (new League\Route\Router())->setStrategy($strategy);
$router->get('/', App\Controller\JobList::class);
$router->get('/login', App\Controller\LoginForm::class)
    ->middleware(new App\Middleware\ExtractFlashErrors());
$router->post('/login', \App\Controller\Login::class)
    ->middleware(new \App\Middleware\Validate($container->get('rules')['login']))
    ->middleware(new \App\Middleware\HandleValidationErrors());
$router->get('/logout', function (
    \Psr\Http\Message\ServerRequestInterface $request
): \Psr\Http\Message\ResponseInterface {
    /* @var $session Aura\Session\Session */
    $session = $request->getAttribute('session');
    if ($session !== null) {
        $session->destroy();
    }
    return new \Laminas\Diactoros\Response\RedirectResponse('/');
});
$router->get('/create', App\Controller\JobCreateForm::class)
    ->middleware(new App\Middleware\ExtractFlashErrors());
$router->post('/create', \App\Controller\JobCreate::class)
    ->middleware(new \App\Middleware\Validate($container->get('rules')['job']))
    ->middleware(new \App\Middleware\HandleValidationErrors());
$router->get('/update/{id:number}', App\Controller\JobUpdateForm::class)
    ->middleware(new App\Middleware\Authorize('@'))
    ->middleware(new App\Middleware\ExtractFlashErrors());
$router->post('/update/{id:number}', \App\Controller\JobUpdate::class)
    ->middleware(new App\Middleware\Authorize('@'))
    ->middleware(new \App\Middleware\Validate($container->get('rules')['job']))
    ->middleware(new \App\Middleware\HandleValidationErrors());
$router->middleware(new Middlewares\AuraSession())
    ->middleware(new App\Middleware\SessionAuthenticate($container->get('userManager')));

try {
    $response = $router->dispatch($request);
} catch (\League\Route\Http\Exception\NotFoundException $e) {
    $response = new \Laminas\Diactoros\Response();
    $response->getBody()->write($container->get(League\Plates\Engine::class)->render('app/404', ['e' => $e]));
    $response->withStatus(404);
} catch (League\Route\Http\Exception\UnauthorizedException $e) {
    $response = new \Laminas\Diactoros\Response\RedirectResponse('/login');
} catch (\Exception $e) {
    if ((bool) getenv('DEBUG') == true) {
        throw $e;
    }
    $response = new \Laminas\Diactoros\Response();
    $response->getBody()->write($container->get(League\Plates\Engine::class)->render('app/error', ['e' => $e]));
    $response->withStatus(500);
}

(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
