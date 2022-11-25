<?php

declare(strict_types=1);

use Dotenv\Dotenv;

error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$connection = [
    'driver' => 'pdo_mysql',
    'user' => $_ENV['APP_DATABASE_USER'],
    'password' => $_ENV['APP_DATABASE_PASSWORD'],
    'dbname' => $_ENV['APP_DATABASE_NAME'],
    'host' => $_ENV['APP_DATABASE_HOST'],
];

return $connection;
