<?php

namespace Tests\HulotteModules\Account\Middlewares;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use HulotteModules\Account\{
    Auth,
    Entity\UserEntity,
    Exceptions\NoAuthException,
    Middlewares\LoggedInMiddleware
};

/**
 * Class LoggedInMiddlewareTest
 *
 * @package Tests\HulotteModules\Account\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class LoggedInMiddlewareTest extends TestCase
{
    public function testSuccess()
    {
        $auth = $this->createMock(Auth::class);
        $auth->method('getUser')
            ->willReturn($this->createMock(UserEntity::class));
        $middleware = new LoggedInMiddleware($auth);

        $request = new ServerRequest('GET', '/test');
        $handle = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handle->expects($this->once())
            ->method('handle');
        
        $middleware->process($request, $handle);
    }

    public function testException()
    {
        $auth = $this->createMock(Auth::class);
        $auth->method('getUser')
            ->will($this->throwException(new NoAuthException()));
        $middleware = new LoggedInMiddleware($auth);

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
