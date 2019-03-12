<?php

namespace Hulotte\Twig;

use Hulotte\Router;

/**
 * Class RouterExtension
 *
 * @package Hulotte\Twig
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class RouterExtension extends \Twig_Extension
{
    /**
     * @var Router
     */
    private $router;

    /**
     * RouterExtension constructor
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Implement new functions on twig
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('path', [$this, 'pathFor']),
        ];
    }

    /**
     * Redirect
     * @param string $path
     * @param array $params
     * @return string|null
     */
    public function pathFor(string $path, array $params = [])
    {
        return $this->router->generateUri($path, $params);
    }
}
