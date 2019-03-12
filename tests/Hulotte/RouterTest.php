<?php

namespace Tests\Hulotte;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Hulotte\Router;

/**
 * Class RouterTest
 *
 * @package Tests\Hulotte
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class RouterTest extends TestCase
{
    private $router;

    public function setUp()
    {
        $containerMock = $this->createMock(ContainerInterface::class);
        $map = [
            ['crud.paths.suffix.create', '/create'],
            ['crud.paths.suffix.read', '/read'],
            ['crud.paths.suffix.update', '/update'],
            ['crud.paths.suffix.delete', '/delete'],
        ];
        $containerMock->method('get')
            ->will($this->returnValueMap($map));

        $this->router = new Router($containerMock);
    }

    public function testGetMethod()
    {
        $request = new ServerRequest('GET', '/index');
        $this->router->get('/index', function () {
            return 'Hello-world';
        }, 'index');
        $route = $this->router->match($request);

        $this->assertEquals('index', $route->getName());
        $this->assertEquals('Hello-world', call_user_func($route->getCallback(), [$request]));
    }

    public function testGetMethodIfUrlDoesNotExists()
    {
        $request = new ServerRequest('GET', '/index');
        $this->router->get('/notExist', function () {
            return 'Hello-world';
        }, 'index');
        $route = $this->router->match($request);

        $this->assertEquals(null, $route);
    }

    public function testGetMethodWithParameters()
    {
        $request = new ServerRequest('GET', '/index/my-slug-3');
        $this->router->get('/index', function () {
            return 'Hello-world';
        }, 'index');
        $this->router->get('/index/{slug: [a-z0-9\-]+}-{id:\d+}', function () {
            return 'Bye-world';
        }, 'index.slug');
        $route = $this->router->match($request);

        $this->assertEquals('index.slug', $route->getName());
        $this->assertEquals('Bye-world', call_user_func_array($route->getCallback(), [$request]));
        $this->assertEquals(['slug' => 'my-slug', 'id' => '3'], $route->getParams());
    }

    public function testGenerateUri()
    {
        $this->router->get('/index', function () {
            return 'Hello-world';
        }, 'index');
        $this->router->get('/index/{slug:[a-z0-9\-]+}-{id:\d+}', function () {
            return 'Bye-world';
        }, 'index.slug');
        $uri = $this->router->generateUri('index.slug', ['slug' => 'my-slug', 'id' => 3]);

        $this->assertEquals('/index/my-slug-3', $uri);
    }

    public function testGenerateUriWithParams()
    {
        $this->router->get('/index', function () {
            return 'Hello-world';
        }, 'index');
        $this->router->get('/index/{slug:[a-z0-9\-]+}-{id:\d+}', function () {
            return 'Bye-world';
        }, 'index.slug');
        $uri = $this->router->generateUri(
            'index.slug',
            ['slug' => 'my-slug', 'id' => 3],
            ['page' => 2]
        );

        $this->assertEquals('/index/my-slug-3?page=2', $uri);
    }

    public function testCrud()
    {
        $requestCreate = new ServerRequest('GET', '/test/create');
        $requestRead = new ServerRequest('GET', '/test/read');
        $requestUpdate = new ServerRequest('GET', '/test/update/1');
        $requestDelete = new ServerRequest('DELETE', '/test/delete/1');

        $this->router->crud('/test', function () {
            return 'Hello-world';
        }, 'test');

        $routeCreate = $this->router->match($requestCreate);
        $uriCreate = $this->router->generateUri('test.create');
        $routeRead = $this->router->match($requestRead);
        $uriRead = $this->router->generateUri('test.read');
        $routeUpdate = $this->router->match($requestUpdate);
        $uriUpdate = $this->router->generateUri('test.update', ['id' => 1]);
        $routeDelete = $this->router->match($requestDelete);
        $uriDelete = $this->router->generateUri('test.delete', ['id' => 1]);

        $this->assertEquals('test.create', $routeCreate->getName());
        $this->assertEquals('/test/create', $uriCreate);
        $this->assertEquals('test.read', $routeRead->getName());
        $this->assertEquals('/test/read', $uriRead);
        $this->assertEquals('test.update', $routeUpdate->getName());
        $this->assertEquals('/test/update/1', $uriUpdate);
        $this->assertEquals('test.delete', $routeDelete->getName());
        $this->assertEquals('/test/delete/1', $uriDelete);
    }
}
