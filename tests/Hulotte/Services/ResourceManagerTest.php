<?php

namespace Tests\Hulotte\Services;

use PHPUnit\Framework\TestCase;
use Hulotte\Services\ResourceManager;

/**
 * Class ResourceManagerTest
 *
 * @package Tests\Hulotte\Services
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class ResourceManagerTest extends TestCase
{
    private $resourceManager;

    public function setUp()
    {
        $this->resourceManager = new ResourceManager();
    }

    public function testAdd()
    {
        
        $this->resourceManager->add('css', 'path/to/resource.css');

        $this->assertCount(1, $this->resourceManager->getResources()['css']);
    }

    public function testMultipleAdd()
    {
        $this->resourceManager->add('css', 'path/to/resource.css');
        $this->resourceManager->add('css', 'path/to/secondResource.css');

        $this->assertCount(2, $this->resourceManager->getResources()['css']);
    }

    public function testMultipleAddWithSameResource()
    {
        $this->resourceManager->add('css', 'path/to/resource.css');
        $this->resourceManager->add('css', 'path/to/resource.css');

        $this->assertCount(1, $this->resourceManager->getResources()['css']);
    }

    public function testWriteCssResources()
    {
        $this->resourceManager->add('css', 'path/to/resource.css');
        $this->resourceManager->add('css', 'path/to/secondResource.css');

        $result = $this->resourceManager->writeResources('css');

        $expected = '<link rel="stylesheet" type="text/css" href="path/to/resource.css">'
            . '<link rel="stylesheet" type="text/css" href="path/to/secondResource.css">';

        $this->assertEquals($expected, $result);
    }

    public function testWriteWithoutCssResources()
    {
        $result = $this->resourceManager->writeResources('css');

        $this->assertNull($result);
    }

    public function testWriteJsResources()
    {
        $this->resourceManager->add('js', 'path/to/resource.js');
        $this->resourceManager->add('js', 'path/to/secondResource.js');

        $result = $this->resourceManager->writeResources('js');

        $expected = '<script type="text/javascript" src="path/to/resource.js"></script>'
            . '<script type="text/javascript" src="path/to/secondResource.js"></script>';

        $this->assertEquals($expected, $result);
    }
}
