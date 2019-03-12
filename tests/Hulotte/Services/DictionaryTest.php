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
 */
class DictionaryTest extends TestCase
{
    public function testTranslateEn()
    {
        $sentence = $this->getDictionary('en')->translate('test:hello', TestModule::class);

        $this->assertEquals('Hello World.', $sentence);
    }

    private function getDictionary($locale)
    {
        return new Dictionary($locale);
    }
}
