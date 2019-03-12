<?php

namespace Tests\Hulotte\Middlewares;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Hulotte\Middlewares\TrailingSlashMiddleware;

/**
 * Class TrailingSlashMiddlewareTest
 *
 * @package Tests\Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class TrailingSlashMiddlewareTest extends TestCase
{
    private $handle;
    private $middleware;

    public function setUp()
    {
        $this->middleware = new TrailingSlashMiddleware();
    }

    public function testWhenUriIsRoot()
    {
        $request = new ServerRequest('GET', '/');

        $this->getHandle()->expects($this->once())
            ->method('handle');

        $this->middleware->process($request, $this->getHandle());
    }

    public function testWithTrailingSlash()
    {
        $request = new ServerRequest('GET', '/test/');
        $response = $this->middleware->process($request, $this->getHandle());

        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('/test', $response->getHeader('Location')[0]);
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
