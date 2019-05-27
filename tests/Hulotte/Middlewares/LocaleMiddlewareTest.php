<?php

namespace Tests\Hulotte\Middlewares;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\{
    MockObject\MockObject,
    TestCase
};
use Psr\{
    Container\ContainerInterface,
    Http\Server\RequestHandlerInterface
};
use Hulotte\{
    Middlewares\LocaleMiddleware,
    Session\PhpSession
};

/**
 * Class LocaleMiddlewareTest
 *
 * @package Tests\Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \Hulotte\Middlewares\LocaleMiddleware
 */
class LocaleMiddlewareTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $handle;

    /**
     * @var LocaleMiddleware
     */
    private $middleware;

    /**
     * @var PhpSession
     */
    private $session;

    public function setUp(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
                ->setMethods(['get', 'has', 'set'])
                ->getMock();
        $container->method('get')->willReturn(['en', 'fr']);
        $this->session = new PhpSession();
        $this->middleware = new LocaleMiddleware($container, $this->session);
    }

    /**
     * @covers ::process
     */
    public function testWithoutLocale(): void
    {
        $request = new ServerRequest('GET', '/test');
        $this->getHandle()->expects($this->once())
            ->method('handle');
        
        $this->middleware->process($request, $this->getHandle());
    }

    /**
     * @covers ::process
     */
    public function testWithLocale(): void
    {
        $request = new ServerRequest('GET', '/test?lang=fr');
        $response = $this->middleware->process($request, $this->getHandle());

        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('/test', $response->getHeader('Location')[0]);
        $this->assertEquals('fr', $this->session->get('locale'));
    }

    /**
     * @covers ::process
     */
    public function testWithNothing(): void
    {
        $request = new ServerRequest('GET', '');
        $this->getHandle()->expects($this->once())
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
