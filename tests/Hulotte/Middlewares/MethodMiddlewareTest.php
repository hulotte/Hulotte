<?php

namespace Tests\Hulotte\Middlewares;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\{
    MockObject\MockObject,
    TestCase
};
use Psr\Http\Server\RequestHandlerInterface;
use Hulotte\Middlewares\MethodMiddleware;

/**
 * Class MethodMiddlewareTest
 *
 * @package Tests\Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @covers \Hulotte\Middlewares\MethodMiddleware
 */
class MethodMiddlewareTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $handle;

    /**
     * @var MethodMiddleware
     */
    private $middleware;

    public function setUp(): void
    {
        $this->middleware = new MethodMiddleware();
    }

    /**
     * @covers ::process
     */
    public function testDeleteMethod(): void
    {
        $request = (new ServerRequest('POST', '/test'))
            ->withParsedBody(['_method' => 'DELETE']);

        $this->getHandle()->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($req) {
                return $req->getMethod() === 'DELETE';
            }));

        $this->middleware->process($request, $this->getHandle());
    }

    /**
     * @covers ::process
     */
    public function testPutMethod(): void
    {
        $request = (new ServerRequest('POST', '/test'))
            ->withParsedBody(['_method' => 'PUT']);

        $this->getHandle()->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($req) {
                return $req->getMethod() === 'PUT';
            }));

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
}
