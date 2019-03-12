<?php

namespace Tests\Hulotte\Twig;

use PHPUnit\Framework\TestCase;
use Hulotte\Twig\TextExtension;

/**
 * Class TextExtensionTest
 *
 * @package Tests\Hulotte\Twig
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class TextExtensionTest extends TestCase
{
    /**
     * @var TextExtension
     */
    private $textExtension;

    public function setUp()
    {
        $this->textExtension = new TextExtension();
    }

    public function testExtractWithShortText()
    {
        $text = 'hello-world';

        $this->assertEquals($text, $this->textExtension->extract($text, 15));
    }

    public function testExctractWithLongText()
    {
        $text = 'hello all the world';

        $this->assertEquals('hello...', $this->textExtension->extract($text, 7));
        $this->assertEquals('hello all...', $this->textExtension->extract($text, 12));
    }
    
    public function testExctractWithNoSpace()
    {
        $text = 'helloalltheworld';

        $this->assertEquals('helloal...', $this->textExtension->extract($text, 7));
    }
}
