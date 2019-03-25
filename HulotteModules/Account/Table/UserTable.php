<?php

namespace HulotteModules\Account\Table;

use Hulotte\{
    Database\StatementBuilder,
    Database\Table
};
use HulotteModules\Account\Entity\UserEntity;

/**
 * Class UserTable
 *
 * @package HulotteModules\Account\Table
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class UserTable extends Table
{
    /**
     * @var string
     */
    protected $entity = UserEntity::class;

    /**
     * @var string
     */
    protected $table = 'user';

    /**
     * Delete all user roles
     * @param int $userId
     * @return mixed
     */
    public function deleteUserRoles(int $userId)
    {
        $statement = (new StatementBuilder())
            ->delete('user_role')
            ->where('user_id = :user_id');

        return $this->query($statement, [':user_id' => $userId], true);
    }

    /**
     * Update an item on database
     * @param int $id
     * @param array $values
     * @return UserEntity
     */
    public function update(int $id, array $values): UserEntity
    {
        $roles = $values['roles'] ?? null;
        unset($values['roles']);

        parent::update($id, $values);

        if ($roles) {
            $this->deleteUserRoles($id);

            foreach ($roles as $role) {
                $this->insert([
                    'user_id' => $id,
                    'role_id' => $role
                ], 'user_role');
            }
        }

        return $this->find($id);
    }
}
