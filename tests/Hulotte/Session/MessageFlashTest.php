<?php

namespace Tests\Hulotte\Session;

use PHPUnit\Framework\TestCase;
use Hulotte\Session\{
    MessageFlash,
    PhpSession
};

/**
 * Class MessageFlashTest
 *
 * @package Tests\Hulotte\Session
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class MessageFlashTest extends TestCase
{
    private $messageFlash;

    public function setUp()
    {
        $session = new PhpSession();
        $this->messageFlash = new MessageFlash($session);
    }

    public function testSuccess()
    {
        $this->messageFlash->success('Success message');

        $this->assertEquals('Success message', $this->messageFlash->get('success'));
    }

    public function testError()
    {
        $this->messageFlash->error('Error message');

        $this->assertEquals('Error message', $this->messageFlash->get('error'));
    }

    public function testDeleteFlashAfterGettingIt()
    {
        $this->messageFlash->success('Bravo');

        $this->assertEquals('Bravo', $this->messageFlash->get('success'));
        $this->assertNull($this->messageFlash->get('flash'));
    }
}
