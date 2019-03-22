<?php

namespace Tests\Hulotte\Middlewares;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\{
    Container\ContainerInterface,
    Http\Server\MiddlewareInterface,
    Http\Server\RequestHandlerInterface
};
use Hulotte\{
    AuthInterface,
    Exceptions\ForbiddenException,
    Middlewares\RestrictedRouteMiddleware
};

class RestrictedRouteMiddlewareTest extends TestCase
{
    private $authInterface;
    private $container;
    private $handle;
    private $middleware;
    private $middlewareResponse;

    public function setUp()
    {
        $this->authInterface = $this->createMock(AuthInterface::class);
        $this->container = $this->createMock(ContainerInterface::class);
        
        $this->middleware = new RestrictedRouteMiddleware(
            $this->container,
            'middleware',
            ['/restricted', 'permission' => '/with-permission', 'perm1/perm2' => '/multiple-permission']
        );
    }

    public function testNotRestriction()
    {
        $request = new ServerRequest('GET', '/test');
        $this->getHandle()->expects($this->once())->method('handle');

        $this->middleware->process($request, $this->getHandle());
    }

    public function testWithRestriction()
    {
        $request = new ServerRequest('GET', '/restricted');

        $this->container->method('get')->with('middleware')
            ->will($this->returnValue($this->getMiddlewareResponse()));
        
        $this->getHandle()->expects($this->never())->method('handle');
        $this->getMiddlewareResponse()->expects($this->once())->method('process');

        $this->middleware->process($request, $this->getHandle());
    }

    public function testWithRestrictionAndPermission()
    {
        $request = new ServerRequest('GET', '/with-permission');

        $this->getHandle()->expects($this->once())->method('handle');
        $this->authInterface->method('hasPermission')->willReturn(true);
        $this->container->expects($this->once())->method('get')
            ->with(AuthInterface::class)
            ->willReturn($this->authInterface);

        $this->middleware->process($request, $this->getHandle());
    }

    public function testWithRestrictionAndPermissionFail()
    {
        $request = new ServerRequest('GET', '/with-permission');

        $this->getHandle()->expects($this->never())->method('handle');
        $this->authInterface->method('hasPermission')->willReturn(false);
        $this->container->expects($this->once())->method('get')
            ->with(AuthInterface::class)
            ->willReturn($this->authInterface);

        $this->expectException(ForbiddenException::class);

        $this->middleware->process($request, $this->getHandle());
    }

    public function testWithRestrictionAndMultiplePermission()
    {
        $request = new ServerRequest('GET', '/multiple-permission');

        $this->getHandle()->expects($this->once())->method('handle');
        $this->authInterface->method('hasPermission')->willReturn(true);
        $this->container->expects($this->once())->method('get')
            ->with(AuthInterface::class)
            ->willReturn($this->authInterface);

        $this->middleware->process($request, $this->getHandle());
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

    private function getMiddlewareResponse()
    {
        if (!$this->middlewareResponse) {
            $this->middlewareResponse = $this->getMockBuilder(MiddlewareInterface::class)
                ->setMethods(['process'])
                ->getMock();
        }

        return $this->middlewareResponse;
    }
}
