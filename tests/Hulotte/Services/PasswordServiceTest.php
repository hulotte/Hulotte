<?php

namespace Tests\Hulotte\Services;

use \Exception;
use PHPUnit\Framework\TestCase;
use Hulotte\Services\PasswordService;

/**
 * Class PasswordServiceTest
 *
 * @package Tests\Hulotte\Services
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \Hulotte\Services\PasswordService
 */
class PasswordServiceTest extends TestCase
{
    /**
     * @var PasswordService
     */
    private $passwordService;

    public function setUp(): void
    {
        $this->passwordService = new PasswordService();
    }

    /**
     * @covers ::createPassword
     * @throws Exception
     */
    public function testCreatePassword(): void
    {
        $password = $this->passwordService->createPassword(8);

        $this->assertEquals(8, mb_strlen($password));
        $this->assertRegExp('/^[a-zA-Z0-9,?;.:\/!&#-_@]+$/', $password);
    }

    /**
     * @covers ::createPassword
     * @expectedException Exception
     * @throws Exception
     */
    public function testCreatePasswordWithFakeSymbol(): void
    {
        $this->expectException(Exception::class);
        $this->passwordService->createPassword(8, ['fake']);
    }

    /**
     * @covers ::createPassword
     * @throws Exception
     */
    public function testCreatePasswordWithOneSymbol(): void
    {
        $password = $this->passwordService->createPassword(8, ['uppercase']);

        $this->assertRegExp('/^[A-Z]+$/', $password);
    }

    /**
     * @covers ::createPassword
     * @throws Exception
     */
    public function testCreatePasswordWithTwoSymbol(): void
    {
        $password = $this->passwordService->createPassword(8, ['uppercase', 'number']);

        $this->assertRegExp('/^[A-Z0-9]+$/', $password);
    }
}
