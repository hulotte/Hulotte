<?php

namespace Hulotte\Twig;

/**
 * Class TextExtension
 *
 * @package Hulotte\Twig
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class TextExtension extends \Twig_Extension
{
    /**
     * Implement new filters on twig
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('extract', [$this, 'extract']),
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
