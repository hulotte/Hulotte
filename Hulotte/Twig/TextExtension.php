<?php

namespace Hulotte\Twig;

use Twig\{
    Extension\AbstractExtension,
    TwigFunction
};

/**
 * Class TextExtension
 *
 * @package Hulotte\Twig
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class TextExtension extends AbstractExtension
{
    /**
     * Implement new filters on twig
     * @return TwigFunction[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFunction('extract', [$this, 'extract']),
        ];
    }

    /**
     * Return an extract of a text
     * @param string $content
     * @param int $maxLength
     * @return string
     */
    public function extract(string $content, int $maxLength = 100): string
    {
        if (mb_strlen($content) > $maxLength) {
            $extract = mb_substr($content, 0, $maxLength);
            $lastSpace = mb_strrpos($extract, ' ');
            
            if ($lastSpace) {
                return mb_substr($extract, 0, $lastSpace) . '...';
            }
           
            return $extract . '...';
        }

        return $content;
    }
}
