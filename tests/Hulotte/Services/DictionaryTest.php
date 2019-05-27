<?php

namespace Tests\Hulotte;

use PHPUnit\Framework\TestCase;
use Hulotte\Services\Dictionary;
use Tests\Hulotte\Dictionary\TestModule;

/**
 * Class DictionaryTest
 *
 * @package Tests\Hulotte
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 * @coversDefaultClass \Hulotte\Services\Dictionary
 */
class DictionaryTest extends TestCase
{
    /**
     * @covers ::translate
     */
    public function testTranslateEn(): void
    {
        $sentence = $this->getDictionary('en')->translate('test:hello', TestModule::class);

        $this->assertEquals('Hello World.', $sentence);
    }

    /**
     * @param string $locale
     * @return Dictionary
     */
    private function getDictionary(string $locale): Dictionary
    {
        return new Dictionary($locale);
    }
}
