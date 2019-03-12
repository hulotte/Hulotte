<?php

namespace Tests\Hulotte\Session;

use PHPUnit\Framework\TestCase;
use Hulotte\Session\PhpSession;

/**
 * Class PhpSessionTest
 *
 * @package Tests\Hulotte\Session
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class PhpSessionTest extends TestCase
{
    private $phpSession;

    public function setUp()
    {
        $this->phpSession = new PhpSession();
    }

    public function testSet()
    {
        $this->phpSession->set('test', 'Le Test');

        $this->assertEquals('Le Test', $_SESSION['test']);
    }

    public function testGet()
    {
        $test = $this->phpSession->get('test');

        $this->assertEquals('Le Test', $test);
    }

    public function testDelete()
    {
        $this->phpSession->delete('test');

        $this->assertArrayNotHasKey('test', $_SESSION);
    }
}
