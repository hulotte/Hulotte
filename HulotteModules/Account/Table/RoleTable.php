<?php

namespace HulotteModules\Account\Table;

use Hulotte\{
    Database\StatementBuilder,
    Database\Table
};
use HulotteModules\Account\Entity\RoleEntity;

/**
 * Class RoleTable
 *
 * @package HulotteModules\Account\Table
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class RoleTable extends Table
{
    /**
     * @var string
     */
    protected $entity = RoleEntity::class;

    /**
     * @var string
     */
    protected $table = 'role';

    /**
     * Add a new relation between role and permission
     * @param int $roleId
     * @param int $permissionId
     * @return mixed
     */
    public function createRolePermission(int $roleId, int $permissionId)
    {
        return $this->insert(['role_id' => $roleId, 'permission_id' => $permissionId], 'role_permission');
    }

    /**
     * Delete relation between role and permission
     * @param int $roleId
     * @param int $permissionId
     * @return mixed
     */
    public function deleteRolePermission(int $roleId, int $permissionId)
    {
        $statement = (new StatementBuilder())
            ->delete('role_permission')
            ->where(['role_id = :role_id', 'permission_id = :permission_id']);

        return $this->query($statement, [':role_id' => $roleId, ':permission_id' => $permissionId], true);
    }

    /**
     * Return the roles associate to this permission
     * @param int $permissionId
     * @return array
     */
    public function getRolePermission(int $permissionId): array
    {
        return $this->query($this->rolePermissionStatement($permissionId));
    }

    /**
     * Get all roles of a user
     * @param int $userId
     * @return array
     */
    public function getUserRoles(int $userId): array
    {
        return $this->query($this->userRolesStatement($userId));
    }

    /**
     * Get all user roles id on array
     * @param int $userId
     * @param string $field
     * @return null|array
     */
    public function getUserRolesList(int $userId, string $field): ?array
    {
        $statement = $this->userRolesStatement($userId)
            ->select($field);

        return $this->allList('id', null, $statement);
    }

    /**
     * Return if user has defined role
     * @param int $userId
     * @param string $roleLabel
     * @return bool
     */
    public function hasRole(int $userId, string $roleLabel): bool
    {
        $statement = $this->userRolesStatement($userId)
            ->where($this->table . '.label = "' . $roleLabel . '"');
        
        return $this->query($statement, [], true) ? true : false;
    }

    /**
     * Verify if a relation between a role and a permission exists
     * @param int $roleId
     * @param int $permissionId
     * @return bool
     */
    public function isRolePermissionExists($roleId, $permissionId): bool
    {
        $statement = $this->rolePermissionStatement($permissionId)
            ->where('role_id = ' . $roleId);

        return $this->query($statement, [], true) ? true : false;
    }

    /**
     * Define the statement to get all roles associate to a permission
     * @param int $permissionId
     * @return StatementBuilder
     */
    private function rolePermissionStatement(int $permissionId): StatementBuilder
    {
        return $this->allStatement()
            ->join('role_permission as rp', $this->table . '.id = rp.role_id')
            ->where('permission_id = ' . $permissionId);
    }

    /**
     * Define the statement to get all user's role
     * @param int $userId
     * @return StatementBuilder
     */
    private function userRolesStatement(int $userId): StatementBuilder
    {
        return $this->allStatement()
            ->join('user_role as ur', $this->table . '.id = ur.role_id')
            ->where('user_id = ' . $userId);
    }
}
