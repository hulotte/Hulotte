<?php

namespace HulotteModules\Account\Twig;

use Twig\{
    Extension\AbstractExtension,
    TwigFunction
};
use HulotteModules\Account\Auth;

/**
 * Class AuthExtension
 *
 * @package HulotteModules\Account\Twig
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class AuthExtension extends AbstractExtension
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
            new TwigFunction('auth_hasRole', [$this, 'hasRole']),
            new TwigFunction('auth_hasPermission', [$this, 'hasPermission']),
        ];
    }

    /**
     * Is current user has a permission
     * @param mixed $permissions
     * @return bool
     * @throws \Hulotte\Exceptions\NoAuthException
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
     * @throws \Hulotte\Exceptions\NoAuthException
     */
    public function hasRole(string $role): bool
    {
        return $this->auth->hasRole($role);
    }
}
