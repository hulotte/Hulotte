<?php

namespace Tests\Hulotte\Middlewares;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\{
    MockObject\MockObject,
    TestCase
};
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
 * @coversDefaultClass \Hulotte\Middlewares\CsrfMiddleware
 */
class CsrfMiddlewareTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $handle;

    /**
     * @var CsrfMiddleware
     */
    private $middleware;

    /**
     * @var array
     */
    private $session;

    public function setUp(): void
    {
        $this->session = [];
        $dictionary = $this->createMock(Dictionary::class);
        $this->middleware = new CsrfMiddleware($this->session, $dictionary);
    }

    /**
     * @covers ::process
     * @throws \Exception
     */
    public function testGetRequestPass(): void
    {
        $this->getHandle()->expects($this->once())
            ->method('handle');
        
        $request = new ServerRequest('GET', '/test');
        $this->middleware->process($request, $this->getHandle());
    }

    /**
     * @covers ::process
     * @expectedException CsrfException
     * @throws \Exception
     */
    public function testBlockPostRequestWithoutCsrf(): void
    {
        $this->getHandle()->expects($this->never())
            ->method('handle');

        $request = new ServerRequest('POST', '/test');
        $this->expectException(CsrfException::class);
        $this->middleware->process($request, $this->getHandle());
    }

    /**
     * @covers ::generateToken
     * @throws \Exception
     */
    public function testLetPostWithTockenPass(): void
    {
        $this->getHandle()->expects($this->once())
            ->method('handle');

        $request = new ServerRequest('POST', '/test');
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $this->getHandle());
    }

    /**
     * @covers ::process
     * @expectedException CsrfException
     * @throws \Exception
     */
    public function testBlockPostRequestWithInvalidCsrf(): void
    {
        $this->getHandle()->expects($this->never())
            ->method('handle');

        $request = new ServerRequest('POST', '/test');
        $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => 'wrongToken']);
        $this->expectException(CsrfException::class);
        $this->middleware->process($request, $this->getHandle());
    }

    /**
     * @covers ::process
     * @expectedException CsrfException
     * @throws \Exception
     */
    public function testLetPostWithTockenPassOnce(): void
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

    /**
     * @covers ::generateToken
     * @throws \Exception
     */
    public function testLimitTheTokenNumber(): void
    {
        $token = '';
        
        for ($i = 0; $i < 100; $i++) {
            $token = $this->middleware->generateToken();
        }

        $this->assertCount(50, $this->getSession()['csrf']);
        $this->assertEquals($token, $this->getSession()['csrf'][49]);
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

    /**
     * @return array
     */
    private function getSession(): array
    {
        if (empty($this->session)) {
            $this->session = $this->middleware->getSession();
        }

        return $this->session;
    }
}
