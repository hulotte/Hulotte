<?php

namespace Hulotte\Twig;

use Twig\{
    Extension\AbstractExtension,
    TwigFunction
};
use Hulotte\Router;

/**
 * Class RouterExtension
 *
 * @package Hulotte\Twig
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class RouterExtension extends AbstractExtension
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
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('path', [$this, 'pathFor']),
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
