<?php

namespace Hulotte\Router;

use Psr\Container\ContainerInterface;
use Hulotte\Router;

/**
 * Class RouterFactory
 *
 * @package Hulotte\Router
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class RouterFactory
{
    /**
     * @param ContainerInterface $container
     * @return Router
     */
    public function __invoke(ContainerInterface $container): Router
    {
        $cache = null;

        if ($container->get('env') === 'prod') {
            $cache = 'tmp/routes';
        }

        return new Router($container, $cache);
    }
}
