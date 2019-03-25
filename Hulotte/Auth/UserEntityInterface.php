<?php

namespace Hulotte\Auth;

/**
 * Interface UserEntityInterface
 *
 * @package Hulotte\Auth
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
interface UserEntityInterface
{
    /**
     * Define if a user have the right for a permission
     * @param string $permissionLabel
     * @return bool
     */
    public function hasPermission(string $permissionLabel): bool;

    /**
     * Define if a user have a role
     * @param string $roleLabel
     * @return bool
     */
    public function hasRole(string $roleLabel): bool;
}
