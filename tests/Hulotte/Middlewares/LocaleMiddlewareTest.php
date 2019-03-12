<?php

namespace Tests\Hulotte\Middlewares;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
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
 */
class LocaleMiddlewareTest extends TestCase
{
    private $handle;
    private $middleware;
    private $session;

    public function setUp()
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
                ->setMethods(['get', 'has', 'set'])
                ->getMock();
        $container->method('get')->willReturn(['en', 'fr']);
        $this->session = new PhpSession();
        $this->middleware = new LocaleMiddleware($container, $this->session);
    }

    public function testWithoutLocale()
    {
        $request = new ServerRequest('GET', '/test');
        $this->getHandle()->expects($this->once())
            ->method('handle');
        
        $this->middleware->process($request, $this->getHandle());
    }

    public function testWithLocale()
    {
        $request = new ServerRequest('GET', '/test?lang=fr');
        $response = $this->middleware->process($request, $this->getHandle());

        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('/test', $response->getHeader('Location')[0]);
        $this->assertEquals('fr', $this->session->get('locale'));
    }

    public function testWithNothing()
    {
        $request = new ServerRequest('GET', '');
        $this->getHandle()->expects($this->once())
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
