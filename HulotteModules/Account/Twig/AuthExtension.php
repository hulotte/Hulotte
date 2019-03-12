<?php

namespace HulotteModules\Account\Twig;

use HulotteModules\Account\Auth;

/**
 * Class AuthExtension
 *
 * @package HulotteModules\Account\Twig
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class AuthExtension extends \Twig_Extension
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * AuthExtension constructor.
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('auth_hasRole', [$this, 'hasRole']),
            new \Twig_SimpleFunction('auth_hasPermission', [$this, 'hasPermission']),
        ];
    }

    /**
     * Is current user has a permission
     * @param string|array $permissions
     * @return bool
     * @throws \HulotteModules\Account\Exceptions\NoAuthException
     */
    public function hasPermission($permissions): bool
    {
        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                if ($this->auth->hasPermission($permission) === true) {
                    return true;
                }
            }

            return false;
        } else {
            return $this->auth->hasPermission($permissions);
        }
    }

    /**
     * Is current user has a role
     * @param string $role
     * @return bool
     * @throws \HulotteModules\Account\Exceptions\NoAuthException
     */
    public function hasRole(string $role): bool
    {
        return $this->auth->hasRole($role);
    }
}
