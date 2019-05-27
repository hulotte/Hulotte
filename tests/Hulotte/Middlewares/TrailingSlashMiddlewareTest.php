<?php

namespace Tests\Hulotte\Middlewares;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\{
    MockObject\MockObject,
    TestCase
};
use Psr\Http\Server\RequestHandlerInterface;
use Hulotte\Middlewares\TrailingSlashMiddleware;

/**
 * Class TrailingSlashMiddlewareTest
 *
 * @package Tests\Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \Hulotte\Middlewares\TrailingSlashMiddleware
 */
class TrailingSlashMiddlewareTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $handle;

    /**
     * @var TrailingSlashMiddleware
     */
    private $middleware;

    public function setUp(): void
    {
        $this->middleware = new TrailingSlashMiddleware();
    }

    /**
     * @covers ::process
     */
    public function testWhenUriIsRoot(): void
    {
        $request = new ServerRequest('GET', '/');

        $this->getHandle()->expects($this->once())
            ->method('handle');

        $this->middleware->process($request, $this->getHandle());
    }

    /**
     * @covers ::process
     */
    public function testWithTrailingSlash(): void
    {
        $request = new ServerRequest('GET', '/test/');
        $response = $this->middleware->process($request, $this->getHandle());

        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('/test', $response->getHeader('Location')[0]);
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
