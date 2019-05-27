<?php

namespace Tests\Hulotte\Middlewares;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\{
    MockObject\MockObject,
    TestCase
};
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
 * @coversDefaultClass \Hulotte\Middlewares\RouterMiddleware
 */
class RouterMiddlewareTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $handle;

    /**
     * @covers ::process
     */
    public function testNoRouteMatch(): void
    {
        $router = $this->createMock(Router::class);
        $router->method('match')->willReturn(null);
        $middleware = new RouterMiddleware($router);
        $request = new ServerRequest('GET', '/test');

        $this->getHandle()->expects($this->once())
            ->method('handle');

        $middleware->process($request, $this->getHandle());
    }

    /**
     * @covers ::process
     */
    public function testRouteMatch(): void
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

    /**
     * @return MockObject
     */
    private function getHandle(): MockObject
    {
        if (!$this->handle) {
            $this->handle = $this->getMockBuilder(RequestHandlerInterface::class)
                ->setMethods(['handle'])
                ->getMock();
        }

        return $this->handle;
    }
}
