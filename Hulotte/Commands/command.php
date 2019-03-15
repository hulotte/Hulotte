#!/usr/bin/env php
<?php

// Call autoload
require dirname(__DIR__) . '/autoload.php';

// Appel du component
use Symfony\Component\Console\Application;
use Hulotte\Commands\InitCommand;

// Déclaration du component
$application = new Application();

// Ajout des commandes
$application->add(new InitCommand());

// Lancement des commandes
$application->run();
