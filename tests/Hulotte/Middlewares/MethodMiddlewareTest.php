<?php

namespace Tests\Hulotte\Middlewares;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Hulotte\Middlewares\MethodMiddleware;

/**
 * Class MethodMiddlewareTest
 *
 * @package Tests\Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class MethodMiddlewareTest extends TestCase
{
    private $handle;
    private $middleware;

    public function setUp()
    {
        $this->middleware = new MethodMiddleware();
    }

    public function testDeleteMethod()
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

    public function testPutMethod()
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
