<?php

namespace Hulotte\Twig;

use Hulotte\Services\ResourceManager;

/**
 * Class ResourceManagerExtension
 *
 * @package Hulotte\Twig
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class ResourceManagerExtension extends \Twig_Extension
{
    /**
     * @var ResourceManager
     */
    private $resourceManager;

    /**
     * ResourceManagerExtension constructor
     * @param ResourceManager $resourceManager
     */
    public function __construct(ResourceManager $resourceManager)
    {
        $this->resourceManager = $resourceManager;
    }

    /**
     * Implement new functions on twig
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('resources_add', [$this, 'add'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('resources_write', [$this, 'write'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Add a resource to the resource manager
     * @param string $type
     * @param string $path
     */
    public function add(string $type, string $path): void
    {
        $this->resourceManager->add($type, $path);
    }

    /**
     * Write the html tags for resources
     * @param string $type
     * @return null|string
     */
    public function write(string $type): ?string
    {
        return $this->resourceManager->writeResources($type);
    }
}
