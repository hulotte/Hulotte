<?php

namespace Tests\Hulotte\Middlewares;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
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
 */
class NotFoundMiddlewareTest extends TestCase
{
    private $handle;
    private $middleware;

    public function setUp()
    {
        $dictionary = $this->createMock(Dictionary::class);
        $this->middleware = new NotFoundMiddleware($dictionary);
    }

    public function testRedirect()
    {
        $request = new ServerRequest('GET', '/test');
        $response = $this->middleware->process($request, $this->getHandle());

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testNoNext()
    {
        $request = new ServerRequest('GET', '/test');
        $this->getHandle()->expects($this->never())
            ->method('handle');

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
