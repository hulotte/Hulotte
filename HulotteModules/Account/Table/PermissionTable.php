<?php

namespace HulotteModules\Account\Table;

use Hulotte\{
    Database\StatementBuilder,
    Database\Table
};
use HulotteModules\Account\Entity\PermissionEntity;

/**
 * Class PermissionTable
 *
 * @package HulotteModules\Account\Table
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class PermissionTable extends Table
{
    /**
     * @var string
     */
    protected $entity = PermissionEntity::class;

    /**
     * @var string
     */
    protected $table = 'permission';

    /**
     * Return if user has defined permission
     * @param int $userId
     * @param string $permissionLabel
     * @return bool
     */
    public function hasPermission(int $userId, string $permissionLabel): bool
    {
        $statement = $this->userRolePermissionStatement($userId)
            ->where($this->table . '.label = "' . $permissionLabel . '"');

        return $this->query($statement, [], true) ? true : false;
    }

    /**
     * Define the statement to get all user's permissions
     * @param int $userId
     * @return StatementBuilder
     */
    private function userRolePermissionStatement(int $userId): StatementBuilder
    {
        return $this->allStatement()
            ->select([$this->table . '.id', $this->table . '.label'])
            ->join('role_permission as rp', $this->table . '.id = rp.permission_id')
            ->join('role as r', 'rp.role_id = r.id')
            ->join('user_role as ur', 'r.id = ur.role_id')
            ->where('ur.user_id = ' . $userId);
    }
}
