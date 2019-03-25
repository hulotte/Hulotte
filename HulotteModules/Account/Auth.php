<?php

namespace HulotteModules\Account;

use Hulotte\{
    Auth\AuthInterface, 
    Auth\UserEntityInterface,
    Exceptions\NoAuthException, 
    Services\Dictionary,
    Session\SessionInterface
};
use HulotteModules\Account\{
    Entity\UserEntity,
    Table\UserTable
};

class Auth implements AuthInterface
{
    /**
     * @var Dictionary
     */
    private $dictionary;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var UserEntity
     */
    private $user;

    /**
     * @var UserTable
     */
    private $userTable;

    /**
     * Auth constructor
     * @param Dictionary $dictionary
     * @param SessionInterface $session
     * @param UserTable $userTable
     */
    public function __construct(
        Dictionary $dictionary,
        SessionInterface $session,
        UserTable $userTable
    ) {
        $this->dictionary = $dictionary;
        $this->session = $session;
        $this->userTable = $userTable;
    }

    /**
     * Check if auth user has permission
     * @param string|array $permissions
     * @return bool
     * @throws NoAuthException
     */
    public function hasPermission($permissions): bool
    {
        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                if ($this->getUser() && $this->getUser()->hasPermission($permission)) {
                    return true;
                }
            }

            throw new NoAuthException();
        }

        if ($this->getUser()) {
            return $this->getUser()->hasPermission($permissions);
        }

        throw new NoAuthException();
    }

    /**
     * Check if auth user has role
     * @param string $role
     * @return bool
     * @throws NoAuthException
     */
    public function hasRole(string $role): bool
    {
        if ($this->getUser()) {
            return $this->getUser()->hasRole($role);
        }
        
        throw new NoAuthException();
    }

    /**
     * Get the connected user
     * @return null|UserEntityInterface
     * @throws NoAuthException
     */
    public function getUser(): ?UserEntityInterface
    {
        if ($this->user !== null) {
            return $this->user;
        }
        
        $userHash = $this->session->get('auth.user');
        
        if ($userHash) {
            $userHashExplode = explode('---', $userHash);
            $userId = (int)$userHashExplode[0];

            $user = $this->userTable->find($userId);

            if ($user === false
                || !password_verify($user->email . $user->name, $userHashExplode[1])
            ) {
                $this->session->delete('auth.user');

                throw new NoAuthException();
            }

            $this->user = $user;

            return $this->user;
        }
        
        return null;
    }

    /**
     * Logged the user
     * @param string $email
     * @param string $password
     * @return null|UserEntity
     */
    public function login(string $email, string $password): ?UserEntity
    {
        $user = $this->userTable->findBy('email', $email);

        if ($user) {
            if (password_verify($password, $user->password)) {
                $this->setUserHashSession($user);
            
                return $user;
            }

            (new messageFlash($this->session))->error(
                $this->dictionary->translate('Auth:errorPassword', AccountModule::class)
            );
        } else {
            (new messageFlash($this->session))->error(
                $this->dictionary->translate('Auth:errorUserNotExists', AccountModule::class)
            );
        }
        
        return null;
    }

    /**
     * Delete user session
     */
    public function logout(): void
    {
        $this->session->delete('auth.user');
    }

    /**
     * Hash user information and save it to the session
     * @param UserEntity $user
     */
    public function setUserHashSession(UserEntity $user): void
    {
        $userHash = $user->id . '---' . password_hash($user->email . $user->name, PASSWORD_DEFAULT);
        $this->session->set('auth.user', $userHash);
    }
}
