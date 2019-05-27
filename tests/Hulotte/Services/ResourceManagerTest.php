<?php

namespace Tests\Hulotte\Services;

use PHPUnit\Framework\TestCase;
use Hulotte\Services\ResourceManager;

/**
 * Class ResourceManagerTest
 *
 * @package Tests\Hulotte\Services
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \Hulotte\Services\ResourceManager
 */
class ResourceManagerTest extends TestCase
{
    /**
     * @var ResourceManager
     */
    private $resourceManager;

    public function setUp(): void
    {
        $this->resourceManager = new ResourceManager();
    }

    /**
     * @covers ::add
     */
    public function testAdd(): void
    {
        
        $this->resourceManager->add('css', 'path/to/resource.css');

        $this->assertCount(1, $this->resourceManager->getResources()['css']);
    }

    /**
     * @covers ::add
     */
    public function testMultipleAdd(): void
    {
        $this->resourceManager->add('css', 'path/to/resource.css');
        $this->resourceManager->add('css', 'path/to/secondResource.css');

        $this->assertCount(2, $this->resourceManager->getResources()['css']);
    }

    /**
     * @covers ::add
     */
    public function testMultipleAddWithSameResource(): void
    {
        $this->resourceManager->add('css', 'path/to/resource.css');
        $this->resourceManager->add('css', 'path/to/resource.css');

        $this->assertCount(1, $this->resourceManager->getResources()['css']);
    }

    /**
     * @covers ::writeResources
     */
    public function testWriteCssResources(): void
    {
        $this->resourceManager->add('css', 'path/to/resource.css');
        $this->resourceManager->add('css', 'path/to/secondResource.css');

        $result = $this->resourceManager->writeResources('css');

        $expected = '<link rel="stylesheet" type="text/css" href="path/to/resource.css">'
            . '<link rel="stylesheet" type="text/css" href="path/to/secondResource.css">';

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers ::writeResources
     */
    public function testWriteWithoutCssResources(): void
    {
        $result = $this->resourceManager->writeResources('css');

        $this->assertNull($result);
    }

    /**
     * @covers ::writeResources
     */
    public function testWriteJsResources(): void
    {
        $this->resourceManager->add('js', 'path/to/resource.js');
        $this->resourceManager->add('js', 'path/to/secondResource.js');

        $result = $this->resourceManager->writeResources('js');

        $expected = '<script type="text/javascript" src="path/to/resource.js"></script>'
            . '<script type="text/javascript" src="path/to/secondResource.js"></script>';

        $this->assertEquals($expected, $result);
    }
}
