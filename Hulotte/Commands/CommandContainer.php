<?php

namespace Hulotte\Commands;

use Psr\Container\ContainerInterface;

/**
 * Trait CommandContainer
 * Methods to manage container on commands
 *
 * @package Hulotte\Commands
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
trait CommandContainer
{
    /**
     * @var ContainerInterface
     */
    public $container;

    /**
     * @param ContainerInterface $container
     * @return CommandContainer
     */
    public function setContainer(ContainerInterface $container): self
    {
        $this->container = $container;

        return $this;
    }
}
