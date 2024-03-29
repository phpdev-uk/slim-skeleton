<?php

declare(strict_types=1);

use App\Controller\PageController;
use App\Middleware\AuthRequired;
use App\TwigExtension\CsrfExtension;
use DI\Container;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
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

if (isset($_ENV['APP_WHOOPS']) && $_ENV['APP_WHOOPS'] === 'on') {
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
    // Set sensible secure default session cookie options, even if php.ini does not
    session_set_cookie_params([
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();

    $container->set('csrf', static function () use ($app) {
        return new Guard($app->getResponseFactory());
    });

    $app->add('csrf');

    $container->get('view')->addExtension(new CsrfExtension($container->get('csrf')));
}

$container->set('database', static function () {
    $config = ORMSetup::createAnnotationMetadataConfiguration([]);

    $connection = DriverManager::getConnection(
        [
            'driver' => 'pdo_mysql',
            'user' => $_ENV['APP_DATABASE_USER'],
            'password' => $_ENV['APP_DATABASE_PASSWORD'],
            'dbname' => $_ENV['APP_DATABASE_NAME'],
            'host' => $_ENV['APP_DATABASE_HOST']
        ],
        $config
    );

    return new EntityManager($connection, $config);
});

// Register all middleware
$container->set(AuthRequired::class, static function () {
    return new AuthRequired();
});

// Register all controllers
$container->set(PageController::class, static function (ContainerInterface $container) {
    $view = $container->get('view');

    return new PageController($view);
});

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->add(TwigMiddleware::createFromContainer($app));

if (!(isset($_ENV['APP_WHOOPS']) && $_ENV['APP_WHOOPS'] === 'on')) {
    $errorMiddleware = new ErrorMiddleware(
        $app->getCallableResolver(),
        $app->getResponseFactory(),
        false,
        true,
        true
    );

    $app->add($errorMiddleware);
}
