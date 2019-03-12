<?php

namespace Hulotte\Services;

/**
 * Class ResourceManager
 *
 * @package Hulotte\Services
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class ResourceManager
{
    /**
     * @var array
     */
    private $resources = [];

    /**
     * Add a resource
     * @param string $type
     * @param string $path
     */
    public function add(string $type, string $path): void
    {
        if (isset($this->resources[$type])) {
            if (!in_array($path, $this->resources[$type])) {
                $this->resources[$type][] = $path;
            }
        } else {
            $this->resources[$type] = [$path];
        }
    }

    /**
     * Resources getter
     * @return array
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Write all the resources on html format
     * @param string $type
     * @return null|string
     */
    public function writeResources(string $type): ?string
    {
        $methodName = 'write' . ucfirst($type);

        return $this->$methodName();
    }

    /**
     * Write the css resources on html format
     * @return null|string
     */
    private function writeCss(): ?string
    {
        if (isset($this->resources['css'])) {
            $html = '';

            foreach ($this->resources['css'] as $resource) {
                $html .= '<link rel="stylesheet" type="text/css" href="' . $resource . '">';
            }
    
            return $html;
        }

        return null;
    }

    /**
     * Write the javascript resources on html format
     * @return null|string
     */
    private function writeJs(): ?string
    {
        if (isset($this->resources['js'])) {
            $html = '';

            foreach ($this->resources['js'] as $resource) {
                $html .= '<script type="text/javascript" src="' . $resource . '"></script>';
            }
    
            return $html;
        }

        return null;
    }
}
