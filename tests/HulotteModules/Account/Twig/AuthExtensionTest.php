<?php

namespace Tests\HulotteModules\Account\Twig;

use PHPUnit\Framework\TestCase;
use HulotteModules\Account\{
    Auth,
    Twig\AuthExtension
};

/**
 * Class AuthExtensionTest
 *
 * @package Tests\HulotteModules\Account\Twig
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class AuthExtensionTest extends TestCase
{
    public function testHasRole()
    {
        $auth = $this->createMock(Auth::class);
        $auth->method('hasRole')
            ->willReturn(true);

        $authExtension = new AuthExtension($auth);

        $this->assertTrue($authExtension->hasRole('test'));
    }

    public function testHasPermission()
    {
        $auth = $this->createMock(Auth::class);
        $auth->method('hasPermission')
            ->willReturn(true);

        $authExtension = new AuthExtension($auth);

        $this->assertTrue($authExtension->hasPermission('test'));
    }

    public function testHasMultiplePermission()
    {
        $auth = $this->createMock(Auth::class);
        $auth->method('hasPermission')
            ->willReturn(true);

        $authExtension = new AuthExtension($auth);

        $this->assertTrue($authExtension->hasPermission(['test', 'test2']));
    }
}
