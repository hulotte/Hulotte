<?php

namespace Tests\Hulotte\Twig;

use PHPUnit\Framework\TestCase;
use Hulotte\Twig\TextExtension;

/**
 * Class TextExtensionTest
 *
 * @package Tests\Hulotte\Twig
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \Hulotte\Twig\TextExtension
 */
class TextExtensionTest extends TestCase
{
    /**
     * @var TextExtension
     */
    private $textExtension;

    public function setUp(): void
    {
        $this->textExtension = new TextExtension();
    }

    /**
     * @covers ::extract
     */
    public function testExtractWithShortText(): void
    {
        $text = 'hello-world';

        $this->assertEquals($text, $this->textExtension->extract($text, 15));
    }

    /**
     * @covers ::extract
     */
    public function testExctractWithLongText(): void
    {
        $text = 'hello all the world';

        $this->assertEquals('hello...', $this->textExtension->extract($text, 7));
        $this->assertEquals('hello all...', $this->textExtension->extract($text, 12));
    }

    /**
     * @covers ::extract
     */
    public function testExctractWithNoSpace(): void
    {
        $text = 'helloalltheworld';

        $this->assertEquals('helloal...', $this->textExtension->extract($text, 7));
    }
}
