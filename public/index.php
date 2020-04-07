<?php

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

$whoops = new \Whoops\Run();
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
$whoops->register();

$container = require_once __DIR__ . '/../config/container.php';

$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals();

$strategy = (new App\Route\ApplicationStrategy())->setContainer($container);

/* @var $router League\Route\Router */
$router = (new App\Route\Router())->setStrategy($strategy);
$router->addPatternMatcher('sort_chars', '[\-\+]{0,1}');
$router->get('/', App\Controller\JobList::class);
$router->get('/sort/{sort:word}{direction:sort_chars}', App\Controller\JobList::class);
$router->get('/sort/{sort:word}{direction:sort_chars}/page/{page:number}', App\Controller\JobList::class);
$router->get('/page/{page:number}', App\Controller\JobList::class);

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
    ->middleware(new App\Middleware\SessionAuthenticate($container->get('userManager')))
    ->middleware(new App\Middleware\ValidateCsrf())
    ->middleware(new App\Middleware\ExtractFlashNotice())
    ->middleware(new \App\Middleware\GenerateCsrf());

$response = $router->dispatch($request);

(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
