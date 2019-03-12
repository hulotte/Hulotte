<?php

namespace Hulotte\Session;

/**
 * Class MessageFlash
 *
 * @package Hulotte\Session
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class MessageFlash
{
    /**
     * @var null|array
     */
    private $messages = null;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $sessionKey = 'flash';

    /**
     * MessageFlash constructor
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Define error message
     * @param string $message
     */
    public function error(string $message): void
    {
        $flash = $this->session->get($this->sessionKey, []);
        $flash['error'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }

    /**
     * Get the flash message
     * @param string $type
     * @return null|string
     */
    public function get(string $type): ?string
    {
        if (is_null($this->messages)) {
            $this->messages = $this->session->get($this->sessionKey, []);
            $this->session->delete($this->sessionKey);
        }

        if (array_key_exists($type, $this->messages)) {
            return $this->messages[$type];
        }

        return null;
    }

    /**
     * Define success message
     * @param string $message
     */
    public function success(string $message): void
    {
        $flash = $this->session->get($this->sessionKey, []);
        $flash['success'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }
}
