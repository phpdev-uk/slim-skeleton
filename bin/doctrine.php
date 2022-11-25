<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../dependencies.php';

ConsoleRunner::run(
    new SingleManagerProvider($container->get('database'))
);
