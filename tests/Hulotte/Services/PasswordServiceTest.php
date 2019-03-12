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
 */
class PasswordServiceTest extends TestCase
{
    private $passwordService;

    public function setUp()
    {
        $this->passwordService = new PasswordService();
    }

    public function testCreatePassword()
    {
        $password = $this->passwordService->createPassword(8);

        $this->assertEquals(8, mb_strlen($password));
        $this->assertRegExp('/^[a-zA-Z0-9,?;.:\/!&#-_@]+$/', $password);
    }

    public function testCreatePasswordWithFakeSymbol()
    {
        $this->expectException(Exception::class);
        $this->passwordService->createPassword(8, ['fake']);
    }
    
    public function testCreatePasswordWithOneSymbol()
    {
        $password = $this->passwordService->createPassword(8, ['uppercase']);

        $this->assertRegExp('/^[A-Z]+$/', $password);
    }

    public function testCreatePasswordWithTwoSymbol()
    {
        $password = $this->passwordService->createPassword(8, ['uppercase', 'number']);

        $this->assertRegExp('/^[A-Z0-9]+$/', $password);
    }
}
