<?php

namespace Tests\HulotteModules\Account\Actions\Auth;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\{
    MockObject\MockObject,
    TestCase
};
use Hulotte\{
    Renderer\RendererInterface,
    Router,
    Services\Dictionary,
    Session\SessionInterface
};
use HulotteModules\Account\{
    Actions\Auth\LoginAttemptAction,
    Auth,
    Entity\UserEntity
};

/**
 * Class LoginAttemptActionTest
 *
 * @package Tests\HulotteModules\Account\Actions\Auth
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \HulotteModules\Account\Actions\Auth\LoginAttemptAction
 */
class LoginAttemptActionTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $auth;

    /**
     * @var MockObject
     */
    private $dictionary;

    /**
     * @var MockObject
     */
    private $renderer;

    /**
     * @var MockObject
     */
    private $router;

    /**
     * @var MockObject
     */
    private $session;

    public function setup(): void
    {
        $this->auth = $this->createMock(Auth::class);
        $this->dictionary = $this->createMock(Dictionary::class);
        $this->renderer = $this->createMock(RendererInterface::class);
        $this->renderer->method('render');
        $this->router = $this->createMock(Router::class);
        $this->session = $this->createMock(SessionInterface::class);
    }

    public function testLoginWithError(): void
    {
        $loginAttemptAction = new LoginAttemptAction(
            $this->auth,
            $this->dictionary,
            $this->renderer,
            $this->router,
            $this->session
        );
        $request = (new ServerRequest('POST', '/login'))
            ->withParsedBody([
                'email' => 'test@test',
                'password' => 'test'
            ]);

        $this->renderer->expects($this->once())->method('render');
        
        $response = call_user_func_array($loginAttemptAction, [$request]);
    }

    public function testLoginWithNoUser(): void
    {
        $this->auth->method('login')->willReturn(null);

        $loginAttemptAction = new LoginAttemptAction(
            $this->auth,
            $this->dictionary,
            $this->renderer,
            $this->router,
            $this->session
        );
        $request = (new ServerRequest('POST', '/login'))
            ->withParsedBody([
                'email' => 'test@test.com',
                'password' => 'test'
            ]);
        
        $response = call_user_func_array($loginAttemptAction, [$request]);

        $this->assertEquals(301, $response->getStatusCode());
    }

    public function testLogin(): void
    {
        $this->auth->method('login')
            ->willReturn($this->createMock(UserEntity::class));
        $this->session->method('get')->willReturn('path');

        $loginAttemptAction = new LoginAttemptAction(
            $this->auth,
            $this->dictionary,
            $this->renderer,
            $this->router,
            $this->session
        );
        $request = (new ServerRequest('POST', '/login'))
            ->withParsedBody([
                'email' => 'test@test.com',
                'password' => 'test'
            ]);
        
        $response = call_user_func_array($loginAttemptAction, [$request]);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
