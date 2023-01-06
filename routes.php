<?php

declare(strict_types=1);

use App\Controllers\PageController;

/** @var \Slim\App $app */

$app->get('/', PageController::class . ':index')->setName('index');
$app->post('/', PageController::class . ':csrf')->setName('csrf');
