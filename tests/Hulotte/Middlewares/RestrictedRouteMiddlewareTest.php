<?php

namespace Tests\Hulotte\Middlewares;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\{
    MockObject\MockObject,
    TestCase
};
use Psr\{
    Container\ContainerInterface,
    Http\Server\MiddlewareInterface,
    Http\Server\RequestHandlerInterface
};
use Hulotte\{
    Auth\AuthInterface,
    Exceptions\ForbiddenException,
    Middlewares\RestrictedRouteMiddleware
};

/**
 * Class RestrictedRouteMiddlewareTest
 *
 * @package Tests\Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \Hulotte\Middlewares\RestrictedRouteMiddleware
 */
class RestrictedRouteMiddlewareTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $auth;

    /**
     * @var MockObject
     */
    private $container;

    /**
     * @var MockObject
     */
    private $handle;

    /**
     * @var RestrictedRouteMiddleware
     */
    private $middleware;

    /**
     * @var MockObject
     */
    private $middlewareResponse;

    public function setUp(): void
    {
        $this->auth = $this->createMock(AuthInterface::class);
        $this->container = $this->createMock(ContainerInterface::class);
        
        $this->middleware = new RestrictedRouteMiddleware(
            $this->container,
            'middleware',
            ['/restricted', 'permission' => '/with-permission', 'perm1/perm2' => '/multiple-permission']
        );
    }

    /**
     * @covers ::process
     * @throws ForbiddenException
     * @throws \Hulotte\Exceptions\NoAuthException
     */
    public function testNotRestriction(): void
    {
        $request = new ServerRequest('GET', '/test');
        $this->getHandle()->expects($this->once())->method('handle');

        $this->middleware->process($request, $this->getHandle());
    }

    /**
     * @covers ::process
     * @throws ForbiddenException
     * @throws \Hulotte\Exceptions\NoAuthException
     */
    public function testWithRestriction(): void
    {
        $request = new ServerRequest('GET', '/restricted');

        $this->container->method('get')->with('middleware')
            ->will($this->returnValue($this->getMiddlewareResponse()));
        
        $this->getHandle()->expects($this->never())->method('handle');
        $this->getMiddlewareResponse()->expects($this->once())->method('process');

        $this->middleware->process($request, $this->getHandle());
    }

    /**
     * @covers ::process
     * @throws ForbiddenException
     * @throws \Hulotte\Exceptions\NoAuthException
     */
    public function testWithRestrictionAndPermission(): void
    {
        $request = new ServerRequest('GET', '/with-permission');

        $this->getHandle()->expects($this->once())->method('handle');
        $this->auth->method('hasPermission')->willReturn(true);
        $this->container->expects($this->once())->method('get')
            ->with(AuthInterface::class)
            ->willReturn($this->auth);

        $this->middleware->process($request, $this->getHandle());
    }

    /**
     * @covers ::process
     * @throws ForbiddenException
     * @throws \Hulotte\Exceptions\NoAuthException
     */
    public function testWithRestrictionAndPermissionFail(): void
    {
        $request = new ServerRequest('GET', '/with-permission');

        $this->getHandle()->expects($this->never())->method('handle');
        $this->auth->method('hasPermission')->willReturn(false);
        $this->container->expects($this->once())->method('get')
            ->with(AuthInterface::class)
            ->willReturn($this->auth);

        $this->expectException(ForbiddenException::class);

        $this->middleware->process($request, $this->getHandle());
    }

    /**
     * @covers ::process
     * @throws ForbiddenException
     * @throws \Hulotte\Exceptions\NoAuthException
     */
    public function testWithRestrictionAndMultiplePermission(): void
    {
        $request = new ServerRequest('GET', '/multiple-permission');

        $this->getHandle()->expects($this->once())->method('handle');
        $this->auth->method('hasPermission')->willReturn(true);
        $this->container->expects($this->once())->method('get')
            ->with(AuthInterface::class)
            ->willReturn($this->auth);

        $this->middleware->process($request, $this->getHandle());
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

    /**
     * @return MockObject
     */
    private function getMiddlewareResponse(): MockObject
    {
        if (!$this->middlewareResponse) {
            $this->middlewareResponse = $this->getMockBuilder(MiddlewareInterface::class)
                ->setMethods(['process'])
                ->getMock();
        }

        return $this->middlewareResponse;
    }
}
