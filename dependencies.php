<?php

declare(strict_types=1);

use App\TwigExtension\CsrfExtension;
use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Csrf\Guard;
use Slim\Middleware\ErrorMiddleware;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

$dotenv = Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

if (isset($_ENV['APP_WHOOPS']) && $_ENV['APP_WHOOPS'] === 'on') {
    $whoops = new \Whoops\Run();
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
    $whoops->register();
}

date_default_timezone_set($_ENV['APP_DEFAULT_TIMEZONE']);

$definitions = require_once __DIR__ . '/definitions.php';

$builder = new ContainerBuilder();
$builder->addDefinitions($definitions);
$container = $builder->build();
$app = Bridge::create($container);

// CSRF protection is optional as it requires cookies and this may present
// privacy and data protection issues
if (isset($_ENV['APP_CSRF_PROTECTION']) && $_ENV['APP_CSRF_PROTECTION'] === 'on') {
    // Set sensible secure default session cookie options, even if php.ini does not
    session_set_cookie_params([
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);

    session_start();

    $container->set('csrf', static function () use ($app) {
        return new Guard($app->getResponseFactory());
    });

    $app->add('csrf');

    $container->get(Twig::class)->addExtension(new CsrfExtension($container->get('csrf')));
}

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->add(TwigMiddleware::createFromContainer($app, Twig::class));

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
