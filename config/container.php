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
$container['adminUsers'] = [
    getenv('ADMIN_LOGIN') => getenv('ADMIN_PASSWORD')
];
$container['entityPath'] = [__DIR__ . "/../src/App/Entity"];
$container['viewsPath'] = __DIR__ . '/../src/App/Views';
$container['assetsPath'] = __DIR__ . '/../public/';
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
$container['templateRenderer'] = function ($c) {
    $asset = new League\Plates\Extension\Asset($c['assetsPath']);
    $template = new League\Plates\Engine($c['viewsPath']);
    $template->loadExtension($asset);
    return $template;
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

$container['userManager'] = new \App\UserManager();

return new \Pimple\Psr11\Container($container);
