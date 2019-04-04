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
 * @coversDefaultClass \HulotteModules\Account\Twig\AuthExtension
 */
class AuthExtensionTest extends TestCase
{
    /**
     * @covers ::hasRole
     * @throws \Hulotte\Exceptions\NoAuthException
     */
    public function testHasRole(): void
    {
        $auth = $this->createMock(Auth::class);
        $auth->method('hasRole')
            ->willReturn(true);

        $authExtension = new AuthExtension($auth);

        $this->assertTrue($authExtension->hasRole('test'));
    }

    /**
     * @covers ::hasPermission
     * @throws \Hulotte\Exceptions\NoAuthException
     */
    public function testHasPermission(): void
    {
        $auth = $this->createMock(Auth::class);
        $auth->method('hasPermission')
            ->willReturn(true);

        $authExtension = new AuthExtension($auth);

        $this->assertTrue($authExtension->hasPermission('test'));
    }

    /**
     * @covers ::hasPermission
     * @throws \Hulotte\Exceptions\NoAuthException
     */
    public function testHasMultiplePermission(): void
    {
        $auth = $this->createMock(Auth::class);
        $auth->method('hasPermission')
            ->willReturn(true);

        $authExtension = new AuthExtension($auth);

        $this->assertTrue($authExtension->hasPermission(['test', 'test2']));
    }
}
