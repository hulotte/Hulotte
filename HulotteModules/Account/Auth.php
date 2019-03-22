<?php

namespace HulotteModules\Account;

use Hulotte\{
    AuthInterface, 
    Exceptions\NoAuthException
};

class Auth implements AuthInterface
{
    /**
     * Check if auth user has permission
     * @param string|array $permissions
     * @return bool
     * @throws NoAuthException
     */
    public function hasPermission($permissions): bool
    {

    }

    /**
     * Check if auth user has role
     * @param string $role
     * @return bool
     * @throws NoAuthException
     */
    public function hasRole(string $role): bool
    {

    }
}
