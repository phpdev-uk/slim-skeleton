<?php

declare(strict_types=1);

use App\Environment;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Slim\Views\Twig;
use Twig\Extra\Intl\IntlExtension;

$definitions = [];

$definitions[Twig::class] = function () {
    $twig = Twig::create(
        __DIR__ . '/templates',
        [
            'strict_variables' => true
        ]
    );

    $flash = [];

    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        $_SESSION['flash'] = [];
    }

    $twig->addExtension(new IntlExtension());
    $twig->getEnvironment()->addGlobal('flash', $flash);

    return $twig;
};

$definitions[Environment::class] = function () {
    return new Environment($_ENV);
};

$definitions[EntityManagerInterface::class] = function () {
    $config = ORMSetup::createAttributeMetadataConfiguration([]);

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
};

return $definitions;
