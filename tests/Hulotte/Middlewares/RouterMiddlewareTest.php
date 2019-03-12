<?php

namespace Tests\Hulotte\Middlewares;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Hulotte\{
    Middlewares\RouterMiddleware,
    Router,
    Router\Route
};

/**
 * Class RouterMiddlewareTest
 *
 * @package Tests\Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class RouterMiddlewareTest extends TestCase
{
    private $handle;
    
    public function testNoRouteMatch()
    {
        $router = $this->createMock(Router::class);
        $router->method('match')->willReturn(null);
        $middleware = new RouterMiddleware($router);
        $request = new ServerRequest('GET', '/test');

        $this->getHandle()->expects($this->once())
            ->method('handle');

        $middleware->process($request, $this->getHandle());
    }

    public function testRouteMatch()
    {
        $route = $this->createMock(Route::class);
        $router = $this->createMock(Router::class);
        $router->method('match')->willReturn($route);
        $middleware = new RouterMiddleware($router);
        $request = new ServerRequest('GET', '/test');

        $this->getHandle()->expects($this->once())
            ->method('handle');

        $middleware->process($request, $this->getHandle());
    }

    private function getHandle()
    {
        if (!$this->handle) {
            $this->handle = $this->getMockBuilder(RequestHandlerInterface::class)
                ->setMethods(['handle'])
                ->getMock();
        }

        return $this->handle;
    }
}
