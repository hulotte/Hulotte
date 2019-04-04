<?php

namespace Hulotte\Twig;

use Twig\{
    Extension\AbstractExtension,
    TwigFunction
};
use Hulotte\Session\MessageFlash;

/**
 * Class MessageFlashExtension
 *
 * @package Hulotte\Twig
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class MessageFlashExtension extends AbstractExtension
{
    /**
     * @var MessageFlash
     */
    private $messageFlash;

    /**
     * MessageFlash constructor
     * @param MessageFlash $messageFlash
     */
    public function __construct(MessageFlash $messageFlash)
    {
        $this->messageFlash = $messageFlash;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('flash', [$this, 'getFlash']),
        ];
    }

    /**
     * Get the message flash
     * @param string $type
     * @return null|string
     */
    public function getFlash($type): ?string
    {
        return $this->messageFlash->get($type);
    }
}
