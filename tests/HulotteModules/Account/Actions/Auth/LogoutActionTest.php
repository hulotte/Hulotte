<?php

namespace Tests\HulotteModules\Account\Actions\Auth;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Hulotte\{
    Renderer\RendererInterface,
    Services\Dictionary,
    Session\MessageFlash
};
use HulotteModules\Account\{
    Actions\Auth\LogoutAction,
    Auth
};

/**
 * Class LogoutActionTest
 *
 * @package Tests\HulotteModules\Account\Actions\Auth
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \HulotteModules\Account\Actions\Auth\LogoutAction
 */
class LogoutActionTest extends TestCase
{
    public function testLogout()
    {
        $auth = $this->createMock(Auth::class);
        $messageFlash = $this->createMock(MessageFlash::class);
        $renderer = $this->createMock(RendererInterface::class);
        $dictionary = $this->createMock(Dictionary::class);

        $logoutAction = new LogoutAction($auth, $dictionary, $messageFlash, $renderer);
        $request = new ServerRequest('GET', '/logout');

        $auth->expects($this->once())->method('logout');

        call_user_func_array($logoutAction, [$request]);
    }
}
