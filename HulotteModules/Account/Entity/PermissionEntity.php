<?php

namespace HulotteModules\Account\Entity;

use HulotteModules\Account\Table\RoleTable;

/**
 * Class PermissionEntity
 *
 * @package HulotteModules\Account\Entity
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class PermissionEntity
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $module;

    /**
     * @var array
     */
    private $rolePermission;

    /**
     * @var RoleTable
     */
    private $roleTable;

    /**
     * PermissionEntity constructor
     * @param RoleTable $roleTable
     */
    public function __construct(RoleTable $roleTable)
    {
        $this->roleTable = $roleTable;
    }

    /**
     * Return the roles associate to this permission
     * @return array
     */
    public function getRolePermission(): array
    {
        if (!$this->rolePermission) {
            $this->rolePermission = $this->roleTable->getRolePermission($this->id);
        }

        return $this->rolePermission;
    }

    /**
     * Unset roles associate to permission on all roles array
     * @param array $roles
     * @return null|array
     */
    public function unsetRoles(array $roles): ?array
    {
        foreach ($this->getRolePermission() as $rolePermission) {
            unset($roles[$rolePermission->id]);
        }

        if (empty($roles)) {
            return null;
        }
        
        return $roles;
    }
}
