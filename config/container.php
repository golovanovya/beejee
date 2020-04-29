<?php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$container = new \Pimple\Container();

// ===== PARAMS =========
$container['dbParams'] = [
    'driver' => 'pdo_mysql',
    'host' => getenv('DB_HOST'),
    'dbname' => getenv('DB_NAME'),
    'user' => getenv('DB_USER'),
    'password' => getenv('DB_PASSWORD'),
];
$container['users'] = [
    getenv('ADMIN_LOGIN') => [
        'username' => getenv('ADMIN_LOGIN') ?? 'admin',
        'password' => getenv('ADMIN_PASSWORD') ?? 'admin',
        'roles' => '',
    ]
];
$container['admin'] = getenv('ADMIN_LOGIN');
$container['entityPath'] = ['src/App/Entity'];
$container['engineParams'] = [
    'viewsPath' => 'src/App/Views',
    'assetsPath' => 'public/',
];
$container['mainLayout'] = 'layout/main';
$container['rules'] = require_once __DIR__ . '/validation-rules.php';
$container['debug'] = boolval(getenv('DEBUG'));

// ===== SERVICES =========
$container['annotationConfig'] = function ($c) {
    $isDevMode = true;
    $proxyDir = null;
    $cache = null;
    $useSimpleAnnotationReader = false;
    $paths = $c['entityPath'];
    return Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);
};
$container['em'] = function ($c) {
    return Doctrine\ORM\EntityManager::create($c['dbParams'], $c['annotationConfig']);
};
$container[\PDO::class] = function ($c) {
    $pdo = new \PDO(
        sprintf('mysql:host=%s;dbname=%s', $c['dbParams']['host'], $c['dbParams']['dbname']),
        $c['dbParams']['user'],
        $c['dbParams']['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]
    );
    return $pdo;
};
$container[League\Plates\Engine::class] = function ($c) {
    $asset = new League\Plates\Extension\Asset($c['engineParams']['assetsPath']);
    $template = new League\Plates\Engine($c['engineParams']['viewsPath']);
    $template->loadExtension($asset);
    return $template;
};
$container['templateRenderer'] = function ($c) {
    return new App\Template\TemplateRenderer($c[League\Plates\Engine::class], $c['mainLayout']);
};

// ===== APP =========
$container[App\Controller\LoginForm::class] = function ($c) {
    $templateRenderer = $c['templateRenderer'];
    return new App\Controller\LoginForm($templateRenderer);
};
$container[\App\Controller\Login::class] = function ($c) {
    $userManager = $c['userManager'];
    return new \App\Controller\Login($userManager);
};
$container[App\Controller\JobList::class] = function ($c) {
    $templateRenderer = $c['templateRenderer'];
    $jobRepository = $c['jobRepository'];
    return new App\Controller\JobList($templateRenderer, $jobRepository);
};
$container[App\Controller\JobCreateForm::class] = function ($c) {
    return new App\Controller\JobCreateForm($c['templateRenderer'], $c['jobRepository']);
};
$container[App\Controller\JobUpdateForm::class] = function ($c) {
    return new App\Controller\JobUpdateForm($c['templateRenderer'], $c['jobRepository']);
};
$container[\App\Controller\JobCreate::class] = function ($c) {
    return new App\Controller\JobCreate($c['templateRenderer'], $c['jobRepository']);
};
$container[\App\Controller\JobUpdate::class] = function ($c) {
    return new App\Controller\JobUpdate($c['templateRenderer'], $c['jobRepository']);
};
$container['jobRepository'] = function ($c) {
    return new App\Models\JobRepository($c[\PDO::class], $c['em']);
};

$container[App\Route\ApplicationStrategy::class] = function ($c) {
    return (new App\Route\ApplicationStrategy())
        ->setContainer(new \Pimple\Psr11\Container($c));
};

/**
 * Router configuration
 */
$container['router'] = function ($c) {
    $container = new \Pimple\Psr11\Container($c);
    /* @var $router League\Route\Router */
    $router = (new App\Route\Router())->setStrategy($container->get(App\Route\ApplicationStrategy::class));
    $router->addPatternMatcher('sort_chars', '[\-\+]{0,1}');
    $router->get('/', App\Controller\JobList::class);
    $router->get('/sort/{sort:word}{direction:sort_chars}', App\Controller\JobList::class);
    $router->get('/sort/{sort:word}{direction:sort_chars}/page/{page:number}', App\Controller\JobList::class);
    $router->get('/page/{page:number}', App\Controller\JobList::class);

    $router->get('/login', App\Controller\LoginForm::class)
        ->middleware(new App\Middleware\ExtractFlashErrors());
    $router->post('/login', \App\Controller\Login::class)
        ->middleware(new \App\Middleware\Validate($c['rules']['login']))
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
        ->middleware(new \App\Middleware\Validate($c['rules']['job']))
        ->middleware(new \App\Middleware\HandleValidationErrors());
    $router->get('/update/{id:number}', App\Controller\JobUpdateForm::class)
        ->middleware(new App\Middleware\Authorize('@'))
        ->middleware(new App\Middleware\ExtractFlashErrors());
    $router->post('/update/{id:number}', \App\Controller\JobUpdate::class)
        ->middleware(new App\Middleware\Authorize('@'))
        ->middleware(new \App\Middleware\Validate($c['rules']['job']))
        ->middleware(new \App\Middleware\HandleValidationErrors());
    $router->middleware(new Middlewares\AuraSession())
        ->middleware(new App\Middleware\SessionAuthenticate($c['userManager']))
        ->middleware(new App\Middleware\ValidateCsrf())
        ->middleware(new App\Middleware\ExtractFlashNotice())
        ->middleware(new \App\Middleware\GenerateCsrf());
    return $router;
};

$container['userManager'] = function ($c) {
    return new \App\UserMemoryManager($c['users'], $c['admin']);
};

return new \Pimple\Psr11\Container($container);
