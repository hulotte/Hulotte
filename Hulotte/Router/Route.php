<?php

namespace Hulotte\Router;

/**
 * Class Route
 *
 * @package Hulotte\Router
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class Route
{
    /**
     * @var string|callable
     */
    private $callback;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $parameters;

    /**
     * Route constructor
     * @param string $name
     * @param string|callable $callback
     * @param array $parameters
     */
    public function __construct(string $name, $callback, array $parameters)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->parameters = $parameters;
    }

    /**
     * return string|callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * return string[]
     */
    public function getParams(): array
    {
        return $this->parameters;
    }
}
