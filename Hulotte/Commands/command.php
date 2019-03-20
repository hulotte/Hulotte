#!/usr/bin/env php
<?php

// Call autoload
if (strpos(__DIR__, 'vendor')) {
    $autoloader = explode('vendor', __DIR__)[0] . 'vendor/autoload.php';
} else {
    $autoloader = dirname(dirname(__DIR__)) . '/vendor/autoload.php';
}

require $autoloader;

// Appel du component
use Symfony\Component\Console\Application;
use Hulotte\Commands\{
    InitCommand,
    ModuleCommand
};

// Déclaration du component
$application = new Application();

// Ajout des commandes
$application->add(new InitCommand());
$application->add(new ModuleCommand());

// Lancement des commandes
$application->run();
