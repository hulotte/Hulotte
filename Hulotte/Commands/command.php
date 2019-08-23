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
    $container = (new App())->getContainer('dev');
}

// Create command application
$application = new Application();

foreach ($container->get('commands') as $command) {
    $command = new $command;
    $application->add($command);
}

$application->run();
