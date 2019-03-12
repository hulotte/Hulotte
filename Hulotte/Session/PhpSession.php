<?php

namespace Hulotte\Session;

/**
 * Class PhpSession
 *
 * @package Hulotte\Session
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class PhpSession implements SessionInterface, \ArrayAccess
{
    /**
     * Delete a key on session
     * @param string $key
     */
    public function delete(string $key): void
    {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }

    /**
     * Get an information on session
     * @param string $key
     * @param null $default
     * @return null|array
     */
    public function get(string $key, $default = null)
    {
        $this->ensureStarted();

        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }

        return $default;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        $this->ensureStarted();

        return array_key_exists($offset, $_SESSION);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        $this->delete($offset);
    }

    /**
     * Add an information on session
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value): void
    {
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }

    /**
     * Check if session is started
     */
    private function ensureStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
