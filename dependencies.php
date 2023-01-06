<?php

declare(strict_types=1);

use App\Controllers\PageController;
use App\TwigExtension\CsrfExtension;
use DI\Container;
use Doctrine\ORM\ORMSetup;
use Dotenv\Dotenv;
use Psr\Container\ContainerInterface;
use Slim\Csrf\Guard;
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Extra\Intl\IntlExtension;

$dotenv = Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

define('WHOOPS_ENABLED', isset($_ENV['APP_WHOOPS']) && $_ENV['APP_WHOOPS'] === 'on');

if (WHOOPS_ENABLED) {
    $whoops = new \Whoops\Run();
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
    $whoops->register();
}

date_default_timezone_set($_ENV['APP_DEFAULT_TIMEZONE']);

$container = new Container();
AppFactory::setContainer($container);

$app = AppFactory::create();

// Register all dependencies
$container->set('view', static function () {
    $twig = Twig::create(
        __DIR__ . '/templates',
        [
            'strict_variables' => true
        ]
    );

    $twig->addExtension(new IntlExtension());

    return $twig;
});

// CSRF protection is optional as it requires cookies and this may present
// privacy and data protection issues
if (isset($_ENV['APP_CSRF_PROTECTION']) && $_ENV['APP_CSRF_PROTECTION'] === 'on') {
    session_start();

    $container->set('csrf', static function () use ($app) {
        return new Guard($app->getResponseFactory());
    });

    $app->add('csrf');

    $container->get('view')->addExtension(new CsrfExtension($container->get('csrf')));
}

$container->set('database', static function () {
    $connection = [
        'driver' => 'pdo_mysql',
        'user' => $_ENV['APP_DATABASE_USER'],
        'password' => $_ENV['APP_DATABASE_PASSWORD'],
        'dbname' => $_ENV['APP_DATABASE_NAME'],
        'host' => $_ENV['APP_DATABASE_HOST']
    ];

    $config = ORMSetup::createAnnotationMetadataConfiguration([]);

    return Doctrine\ORM\EntityManager::create($connection, $config);
});

// Register all controllers
$container->set(PageController::class, static function (ContainerInterface $container) {
    $view = $container->get('view');

    return new PageController($view);
});

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->add(TwigMiddleware::createFromContainer($app));

if (!WHOOPS_ENABLED) {
    $errorMiddleware = new ErrorMiddleware(
        $app->getCallableResolver(),
        $app->getResponseFactory(),
        false,
        true,
        true
    );

    $app->add($errorMiddleware);
}
