<?php

namespace Hulotte;

use Hulotte\Exceptions\NoAuthException;

/**
 * Interface AuthInterface
 *
 * @package Hulotte
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
interface AuthInterface
{
    /**
     * Check if auth user has permission
     * @param string|array $permissions
     * @return bool
     * @throws NoAuthException
     */
    public function hasPermission($permissions): bool;

    /**
     * Check if auth user has role
     * @param string $role
     * @return bool
     * @throws NoAuthException
     */
    public function hasRole(string $role): bool;
}
