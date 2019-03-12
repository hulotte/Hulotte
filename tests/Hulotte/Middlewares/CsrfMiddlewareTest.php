<?php

namespace Tests\Hulotte\Middlewares;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Hulotte\{
    Exceptions\CsrfException,
    Middlewares\CsrfMiddleware,
    Services\Dictionary
};

/**
 * Class CsrfMiddlewareTest
 *
 * @package Tests\Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class CsrfMiddlewareTest extends TestCase
{
    private $handle;
    private $middleware;
    private $session;
    
    public function setUp()
    {
        $this->session = [];
        $dictionary = $this->createMock(Dictionary::class);
        $this->middleware = new CsrfMiddleware($this->session, $dictionary);
    }

    public function testGetRequestPass()
    {
        $this->getHandle()->expects($this->once())
            ->method('handle');
        
        $request = new ServerRequest('GET', '/test');
        $this->middleware->process($request, $this->getHandle());
    }

    public function testBlockPostRequestWithoutCsrf()
    {
        $this->getHandle()->expects($this->never())
            ->method('handle');

        $request = new ServerRequest('POST', '/test');
        $this->expectException(CsrfException::class);
        $this->middleware->process($request, $this->getHandle());
    }

    public function testLetPostWithTockenPass()
    {
        $this->getHandle()->expects($this->once())
            ->method('handle');

        $request = new ServerRequest('POST', '/test');
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $this->getHandle());
    }

    public function testBlockPostRequestWithInvalidCsrf()
    {
        $this->getHandle()->expects($this->never())
            ->method('handle');

        $request = new ServerRequest('POST', '/test');
        $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => 'wrongToken']);
        $this->expectException(CsrfException::class);
        $this->middleware->process($request, $this->getHandle());
    }

    public function testLetPostWithTockenPassOnce()
    {
        $this->getHandle()->expects($this->once())
            ->method('handle');

        $request = new ServerRequest('POST', '/test');
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $this->getHandle());
        $this->expectException(CsrfException::class);
        $this->middleware->process($request, $this->getHandle());
    }

    public function testLimitTheTokenNumber()
    {
        $token = '';
        
        for ($i = 0; $i < 100; $i++) {
            $token = $this->middleware->generateToken();
        }

        $this->assertCount(50, $this->getSession()['csrf']);
        $this->assertEquals($token, $this->getSession()['csrf'][49]);
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

    private function getSession()
    {
        if (empty($this->session)) {
            $this->session = $this->middleware->getSession();
        }

        return $this->session;
    }
}
