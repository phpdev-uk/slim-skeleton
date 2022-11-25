<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../dependencies.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->add(TwigMiddleware::createFromContainer($app));

require_once __DIR__ . '/../routes.php';

$app->run();
