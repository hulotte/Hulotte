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
 * @coversDefaultClass \Hulotte\Session\MessageFlash
 */
class MessageFlashTest extends TestCase
{
    /**
     * @var MessageFlash
     */
    private $messageFlash;

    public function setUp(): void
    {
        $session = new PhpSession();
        $this->messageFlash = new MessageFlash($session);
    }

    /**
     * @covers ::success
     */
    public function testSuccess(): void
    {
        $this->messageFlash->success('Success message');

        $this->assertEquals('Success message', $this->messageFlash->get('success'));
    }

    /**
     * @covers ::error
     */
    public function testError(): void
    {
        $this->messageFlash->error('Error message');

        $this->assertEquals('Error message', $this->messageFlash->get('error'));
    }

    /**
     * @covers ::success
     */
    public function testDeleteFlashAfterGettingIt(): void
    {
        $this->messageFlash->success('Bravo');

        $this->assertEquals('Bravo', $this->messageFlash->get('success'));
        $this->assertNull($this->messageFlash->get('flash'));
    }
}
