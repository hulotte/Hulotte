<?php

namespace HulotteModules\Account\Entity;

use HulotteModules\Account\Table\{
    PermissionTable,
    RoleTable
};

/**
 * Class UserEntity
 *
 * @package HulotteModules\Account\Entity
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class UserEntity
{
    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $password;

    /**
     * @var PermissionTable
     */
    private $permissionTable;

    /**
     * @var RoleTable
     */
    private $roleTable;

    /**
     * UserEntity constructor
     * @param PermissionTable $permissionTable
     * @param RoleTable $roleTable
     */
    public function __construct(PermissionTable $permissionTable, RoleTable $roleTable)
    {
        $this->permissionTable = $permissionTable;
        $this->roleTable = $roleTable;
    }

    /**
     * Get ids of user roles
     * @return null|array
     */
    public function getRolesId(): ?array
    {
        if ($this->id) {
            return $this->roleTable->getUserRolesList($this->id, 'id');
        }
        
        return null;
    }

    /**
     * Define if a user have the right for a permission
     * @param string $permissionLabel
     * @return bool
     */
    public function hasPermission(string $permissionLabel): bool
    {
        return $this->permissionTable->hasPermission($this->id, $permissionLabel);
    }

    /**
     * Define if a user have a role
     * @param string $roleLabel
     * @return bool
     */
    public function hasRole(string $roleLabel): bool
    {
        return $this->roleTable->hasRole($this->id, $roleLabel);
    }

    /**
     * Id setter
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
