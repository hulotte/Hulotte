<?php

namespace Tests\Hulotte\Session;

use PHPUnit\Framework\TestCase;
use Hulotte\Session\PhpSession;

/**
 * Class PhpSessionTest
 *
 * @package Tests\Hulotte\Session
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \Hulotte\Session\PhpSession
 */
class PhpSessionTest extends TestCase
{
    /**
     * @var PhpSession
     */
    private $phpSession;

    public function setUp(): void
    {
        $this->phpSession = new PhpSession();
    }

    /**
     * @covers ::set
     */
    public function testSet(): void
    {
        $this->phpSession->set('test', 'Le Test');

        $this->assertEquals('Le Test', $_SESSION['test']);
    }

    /**
     * @covers ::get
     */
    public function testGet(): void
    {
        $test = $this->phpSession->get('test');

        $this->assertEquals('Le Test', $test);
    }

    /**
     * @covers ::delete
     */
    public function testDelete(): void
    {
        $this->phpSession->delete('test');

        $this->assertArrayNotHasKey('test', $_SESSION);
    }
}
