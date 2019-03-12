<?php

namespace Hulotte\Renderer;

/**
 * Interface RendererInterface
 *
 * @package Hulotte\Renderer
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
interface RendererInterface
{
    /**
     * Add globals variables for all the views
     * @param string $key
     * @param mixed $value
     */
    public function addGlobal(string $key, $value): void;

    /**
     * Add a path to load views
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void;

    /**
     * Render a view
     * The path can be precised by namespace add with addPath method
     * @example $this->render('@app/index');
     * @example $this->render('index');
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string;
}
