<?php

namespace Tests\Hulotte\Middlewares;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\{
    MockObject\MockObject,
    TestCase
};
use Psr\Http\Server\RequestHandlerInterface;
use Hulotte\{
    Middlewares\NotFoundMiddleware,
    Services\Dictionary
};

/**
 * Class NotFoundMiddlewareTest
 *
 * @package Tests\Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \Hulotte\Middlewares\NotFoundMiddleware
 */
class NotFoundMiddlewareTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $handle;

    /**
     * @var NotFoundMiddleware
     */
    private $middleware;

    public function setUp(): void
    {
        $dictionary = $this->createMock(Dictionary::class);
        $this->middleware = new NotFoundMiddleware($dictionary);
    }

    /**
     * @covers ::process
     */
    public function testRedirect(): void
    {
        $request = new ServerRequest('GET', '/test');
        $response = $this->middleware->process($request, $this->getHandle());

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @covers ::process
     */
    public function testNoNext(): void
    {
        $request = new ServerRequest('GET', '/test');
        $this->getHandle()->expects($this->never())
            ->method('handle');

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
