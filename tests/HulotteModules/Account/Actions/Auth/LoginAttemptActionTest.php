<?php

namespace Tests\HulotteModules\Account\Actions\Auth;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
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
 */
class LoginAttemptActionTest extends TestCase
{
    public function testLoginWithError()
    {
        $auth = $this->createMock(Auth::class);
        $dictionary = $this->createMock(Dictionary::class);
        $renderer = $this->createMock(RendererInterface::class);
        $renderer->method('render');
        $router = $this->createMock(Router::class);
        $session = $this->createMock(SessionInterface::class);

        $loginAttemptAction = new LoginAttemptAction($auth, $dictionary, $renderer, $router, $session);
        $request = (new ServerRequest('POST', '/login'))
            ->withParsedBody([
                'email' => 'test@test',
                'password' => 'test'
            ]);

        $renderer->expects($this->once())->method('render');
        
        $response = call_user_func_array($loginAttemptAction, [$request]);
    }

    public function testLoginWithNoUser()
    {
        $auth = $this->createMock(Auth::class);
        $auth->method('login')->willReturn(null);
        $dictionary = $this->createMock(Dictionary::class);
        $renderer = $this->createMock(RendererInterface::class);
        $renderer->method('render');
        $router = $this->createMock(Router::class);
        $session = $this->createMock(SessionInterface::class);

        $loginAttemptAction = new LoginAttemptAction($auth, $dictionary, $renderer, $router, $session);
        $request = (new ServerRequest('POST', '/login'))
            ->withParsedBody([
                'email' => 'test@test.com',
                'password' => 'test'
            ]);
        
        $response = call_user_func_array($loginAttemptAction, [$request]);

        $this->assertEquals(301, $response->getStatusCode());
    }

    public function testLogin()
    {
        $auth = $this->createMock(Auth::class);
        $auth->method('login')
            ->willReturn($this->createMock(UserEntity::class));
        $dictionary = $this->createMock(Dictionary::class);
        $renderer = $this->createMock(RendererInterface::class);
        $renderer->method('render');
        $router = $this->createMock(Router::class);
        $session = $this->createMock(SessionInterface::class);
        $session->method('get')->willReturn('path');

        $loginAttemptAction = new LoginAttemptAction($auth, $dictionary, $renderer, $router, $session);
        $request = (new ServerRequest('POST', '/login'))
            ->withParsedBody([
                'email' => 'test@test.com',
                'password' => 'test'
            ]);
        
        $response = call_user_func_array($loginAttemptAction, [$request]);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
