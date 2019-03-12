<?php

namespace Tests\Hulotte\Middlewares;

use DI\ContainerBuilder;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hulotte\{
    Middlewares\DispatcherMiddleware,
    Router,
    Router\Route,
    Services\Dictionary
};

/**
 * Class DispatcherMiddlewareTest
 *
 * @package Tests\Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class DispatcherMiddlewareTest extends TestCase
{
    private $middleware;
    private $router;

    public function setUp()
    {
        $builder = new ContainerBuilder();
        $dictionary = $this->createMock(Dictionary::class);
        $this->middleware = new DispatcherMiddleware($builder->build(), $dictionary);
        $containerMock = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $this->router = new Router($containerMock);
    }

    public function testIfNoRouteMatch()
    {
        $this->router->get('/fake', function () {
            return 'Hello-world';
        }, 'test');
        $request = new ServerRequest('GET', '/test');

        $route = $this->router->match($request);
        $request = $request->withAttribute(Route::class, $route);

        $handle = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();
        $handle->expects($this->once())
            ->method('handle');

        $this->middleware->process($request, $handle);
    }

    public function testIfRouteMatch()
    {
        $this->router->get('/test', function () {
            return 'Hello-world';
        }, 'test');
        $request = new ServerRequest('GET', '/test');

        $route = $this->router->match($request);
        $request = $request->withAttribute(get_class($route), $route);

        $handle = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $response = $this->middleware->process($request, $handle);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
