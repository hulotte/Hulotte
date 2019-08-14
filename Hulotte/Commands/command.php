#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;
use Hulotte\{
    App,
    Database\Database
};

// Define root folder
if (strpos(__DIR__, 'vendor')) {
    $rootFolder = explode('vendor', __DIR__)[0];
} else {
    $rootFolder = dirname(dirname(__DIR__)) . '/';
}

// Call autoloader and instanciate container
if (file_exists($rootFolder . 'public/index.php')) {
    require_once($rootFolder . 'public/index.php');
    $container = $app->getContainer();
} else {
    require_once $rootFolder . 'vendor/autoload.php';
    $container = (new App())->getContainer();
}

// Instanciate database
$pdo = new PDO('mysql:host=' . $container->get('database.host') . ';charset=utf8',
    $container->get('database.username'),
    $container->get('database.password')
);

$database = new Database($pdo);

// Create command application
$application = new Application();

foreach ($container->get('commands') as $command) {
    $command = new $command;

    if (method_exists($command, 'setDatabase')) {
        $command->setDatabase($database);
    }

    if (method_exists($command, 'setContainer')) {
        $command->setContainer($container);
    }

    $application->add($command);
}

$application->run();
