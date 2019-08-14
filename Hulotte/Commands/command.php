#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;
use Hulotte\App;

if (file_exists('/public/index.php')) {
    require_once('/public/index.php');

    $container = $app->getContainer();
} else {
    // Define autoload path
    if (strpos(__DIR__, 'vendor')) {
        $autoloader = explode('vendor', __DIR__)[0] . 'vendor/autoload.php';
    } else {
        $autoloader = dirname(dirname(__DIR__)) . '/vendor/autoload.php';
    }

    require $autoloader;

    $container = (new App())->getContainer('dev');
}

$application = new Application();

foreach ($container->get('commands') as $command) {
    $command = new $command;

    if (method_exists($command, 'setContainer')) {
        $command->setContainer($container);
    }

    $application->add($command);
}

$application->run();
