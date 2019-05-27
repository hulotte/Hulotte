<?php

namespace Tests\HulotteModules\Account\Actions\Auth;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Hulotte\{
    Renderer\RendererInterface,
    Router
};
use HulotteModules\Account\{
    Actions\Auth\LoginAction,
    Auth,
    Entity\UserEntity
};

/**
 * Class LoginActionTest
 *
 * @package Tests\HulotteModules\Account\Actions\Auth
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \HulotteModules\Account\Actions\Auth\LoginAction
 */
class LoginActionTest extends TestCase
{
    /**
     * @covers ::redirect
     */
    public function testRedirectIfConnected(): void
    {
        $auth = $this->createMock(Auth::class);
        $auth->method('getUser')
            ->willReturn($this->createMock(UserEntity::class));
        $renderer = $this->createMock(RendererInterface::class);
        $router = $this->createMock(Router::class);

        $loginAction = new LoginAction($auth, $renderer, $router);
        $request = new ServerRequest('GET', '/login');

        $response = call_user_func_array($loginAction, [$request]);

        $this->assertEquals(301, $response->getStatusCode());
    }
}
