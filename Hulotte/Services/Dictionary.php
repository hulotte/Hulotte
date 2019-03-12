<?php

namespace Hulotte\Services;

use Yosymfony\Toml\Toml;

/**
 * Class Dictionary
 *
 * @package Hulotte\Services
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class Dictionary
{
    /**
     * @var string
     */
    private $locale;

    /**
     * Dictionary constructor
     * @param string $locale
     */
    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    /**
     * Parses the sentence given and returns the corresponding translated sentence
     * @param string $sentence
     * @param null|string $module
     * @return string
     */
    public function translate(string $sentence, ?string $module = null): string
    {
        $dictionary = Toml::ParseFile(dirname(__DIR__) . '/dictionary/dictionary_' . $this->locale . '.toml');

        if ($module) {
            $dictionary = Toml::ParseFile($module::DICTIONARY . $this->locale . '.toml');
        }

        $details = explode(':', $sentence);
        $category = $details[0];
        $key = $details[1];

        return $dictionary[$category][$key];
    }
}
