<?php

declare(strict_types = 1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Extra\Intl\IntlExtension;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if ($_ENV['APP_WHOOPS'] === 'on') {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

date_default_timezone_set($_ENV['APP_DEFAULT_TIMEZONE']);

$container = new Container();
AppFactory::setContainer($container);

$container->set('view', function() {
    $twig = Twig::create(
        __DIR__ . '/../templates',
        [
            'strict_variables' => true
        ]
    );
    $twig->addExtension(new IntlExtension());
    return $twig;
});

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->add(TwigMiddleware::createFromContainer($app));

$app->get('/', function(
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $args
): ResponseInterface {
    return $this->get('view')->render(
        $response,
        'index.twig.html',
        []
    );
});

$app->run();
