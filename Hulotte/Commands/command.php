#!/usr/bin/env php
<?php

// Call autoload
$autoloader = explode('vendor', __DIR__)[0] . 'vendor/autoload.php';
require $autoloader;

// Appel du component
use Symfony\Component\Console\Application;
use Hulotte\Commands\InitCommand;

// Déclaration du component
$application = new Application();

// Ajout des commandes
$application->add(new InitCommand());

// Lancement des commandes
$application->run();
