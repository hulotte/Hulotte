<?php

namespace Hulotte\Renderer;

use Twig\Environment;

/**
 * Class TwigRenderer
 *
 * @package Hulotte\Renderer
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class TwigRenderer implements RendererInterface
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * TwigRenderer constructor
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Add globals variables for all the views
     * @param string $key
     * @param mixed $value
     */
    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }

    /**
     * Add a path to load views
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->twig->getLoader()->addPath($path, $namespace);
    }

    /**
     * Twig parameter getter
     * @return Environment
     */
    public function getTwig(): Environment
    {
        return $this->twig;
    }

    /**
     * Render a view
     * The path can be precised by namespace add with addPath method
     * @param string $view
     * @param array $params
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render(string $view, array $params = []): string
    {
        return $this->twig->render($view . '.twig', $params);
    }
}
