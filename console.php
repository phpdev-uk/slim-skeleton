<?php

declare(strict_types=1);

use Symfony\Component\Console\Application;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/dependencies.php';

$application = new Application();

$application->run();
