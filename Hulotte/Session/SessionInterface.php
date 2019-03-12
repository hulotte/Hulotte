<?php

namespace Hulotte\Session;

/**
 * Interface SessionInterface
 *
 * @package Hulotte\Session
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
interface SessionInterface
{
    /**
     * Delete a key on session
     * @param string $key
     */
    public function delete(string $key): void;

    /**
     * Get an information on session
     * @param string $key
     * @param mixed $default
     */
    public function get(string $key, $default = null);

    /**
     * Add an information on session
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value): void;
}
