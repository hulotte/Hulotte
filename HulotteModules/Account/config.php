<?php

use function DI\add;
use function DI\autowire;
use function DI\get;
use Hulotte\Auth\AuthInterface;
use HulotteModules\Auth;

return [
    // Class definitions
    AuthInterface::class => autowire(Auth::class),

    // Permissions

    // Twig extensions

    // URLs

    // Variables
];
