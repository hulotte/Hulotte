<?php

namespace Tests\Hulotte\Middlewares;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\{
    MockObject\MockObject,
    TestCase
};
use Psr\Http\Server\RequestHandlerInterface;
use Hulotte\{
    Exceptions\ForbiddenException,
    Exceptions\NoAuthException,
    Middlewares\ForbiddenMiddleware,
    Services\Dictionary,
    Session\PhpSession
};

/**
 * Class ForbiddenMiddlewareTest
 *
 * @package Tests\Hulotte\Middlewares
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \Hulotte\Middlewares\ForbiddenMiddleware
 */
class ForbiddenMiddlewareTest extends TestCase
{
    /**
     * @var string
     */
    private $dashboardPath = '/dashboard';

    /**
     * @var MockObject
     */
    private $dictionary;

    /**
     * @var MockObject
     */
    private $handle;

    /**
     * @var string
     */
    private $loginPath = '/login';

    /**
     * @var ForbiddenMiddleware
     */
    private $middleware;

    /**
     * @var ServerRequest
     */
    private $request;

    /**
     * @var PhpSession
     */
    private $session;

    public function setUp(): void
    {
        $this->dictionary = $this->createMock(Dictionary::class);
        $this->request = new ServerRequest('GET', '/test');
        $this->handle = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();
        $this->session = new PhpSession();
        $this->middleware = new ForbiddenMiddleware(
            $this->loginPath,
            $this->dashboardPath,
            $this->dictionary,
            $this->session
        );
    }

    /**
     * @covers ::process
     */
    public function testNoAuthException(): void
    {
        $this->handle->method('handle')
            ->willThrowException($this->createMock(NoAuthException::class));
        
        $response = $this->middleware->process($this->request, $this->handle);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('/login', $response->getHeader('Location')[0]);
        $this->assertEquals('/test', $this->session->get('account.auth.redirect'));
    }

    /**
     * @covers ::process
     */
    public function testForbiddenException(): void
    {
        $this->handle->method('handle')
            ->willThrowException($this->createMock(ForbiddenException::class));
    
        $response = $this->middleware->process($this->request, $this->handle);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('/dashboard', $response->getHeader('Location')[0]);
    }
}
