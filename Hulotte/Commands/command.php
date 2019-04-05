#!/usr/bin/env php
<?php

// Define autoload path
if (strpos(__DIR__, 'vendor')) {
    $autoloader = explode('vendor', __DIR__)[0] . 'vendor/autoload.php';
} else {
    $autoloader = dirname(dirname(__DIR__)) . '/vendor/autoload.php';
}

require $autoloader;

use Symfony\Component\Console\Application;
use Hulotte\App;

$container = (new App())->getContainer('dev');
$application = new Application();

foreach ($container->get('commands') as $command) {
    $application->add(new $command);
}

$application->run();
