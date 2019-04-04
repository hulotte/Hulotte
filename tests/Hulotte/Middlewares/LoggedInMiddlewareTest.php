<?php

namespace Tests\Hulotte\Middlewares;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\{
    Container\ContainerInterface,
    Http\Server\RequestHandlerInterface
};
use Hulotte\{
    Auth\AuthInterface,
    Auth\UserEntityInterface,
    Exceptions\NoAuthException,
    Middlewares\LoggedInMiddleware
};

/**
 * Class LoggedInMiddlewareTest
 *
 * @package Tests\Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \Hulotte\Middlewares\LoggedInMiddleware
 */
class LoggedInMiddlewareTest extends TestCase
{
    /**
     * @var ObjectProphecy
     */
    private $container;

    public function setUp(): void
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->container->has('Hulotte\Auth\AuthInterface')->willReturn(true);
    }

    /**
     * @covers ::process
     * @throws NoAuthException
     */
    public function testSuccess(): void
    {
        $auth = $this->createMock(AuthInterface::class);
        $auth->method('getUser')
            ->willReturn($this->createMock(UserEntityInterface::class));
        $this->container->get(AuthInterface::class)->willReturn($auth);
        $middleware = new LoggedInMiddleware($this->container->reveal());

        $request = new ServerRequest('GET', '/test');
        $handle = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handle->expects($this->once())
            ->method('handle');
        
        $middleware->process($request, $handle);
    }

    /**
     * @covers ::process
     * @expectedException NoAuthException
     * @throws NoAuthException
     */
    public function testException(): void
    {
        $auth = $this->createMock(AuthInterface::class);
        $auth->method('getUser')
            ->will($this->throwException(new NoAuthException()));
        $this->container->get(AuthInterface::class)->willReturn($auth);
        $middleware = new LoggedInMiddleware($this->container->reveal());

        $request = new ServerRequest('GET', '/test');
        $handle = $this->getMockBuilder(RequestHandlerInterface::class)
                ->setMethods(['handle'])
                ->getMock();

        $this->expectException(NoAuthException::class);
        $handle->expects($this->never())
            ->method('handle');

        $middleware->process($request, $handle);
    }
}
