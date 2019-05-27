<?php

use Phinx\Migration\AbstractMigration;

class CreateAccountTable extends AbstractMigration
{
    public function change()
    {
        // User
        $this->table('user')
            ->addColumn('civility', 'string')
            ->addColumn('name', 'string')
            ->addColumn('firstName', 'string')
            ->addColumn('email', 'string')
            ->addColumn('password', 'string')
            ->addIndex(['email'], ['unique' => true])
            ->create();
        
        // Role
        $this->table('role')
            ->addColumn('label', 'string')
            ->create();
        
        $this->table('role')
            ->insert([
                'label' => 'admin'
            ])
            ->saveData();

        // User_role
        $this->table('user_role', ['id' => false, 'primary_key' => ['user_id', 'role_id']])
            ->addColumn('user_id', 'integer')
            ->addColumn('role_id', 'integer')
            ->addForeignKey('user_id', 'user', 'id')
            ->addForeignKey('role_id', 'role', 'id')
            ->create();

        // Permission
        $this->table('permission')
            ->addColumn('label', 'string')
            ->addColumn('module', 'string')
            ->addColumn('description', 'string')
            ->addIndex(['label'], ['unique' => true])
            ->create();

        $datas = [
            [
                'label' => 'accessPermissionManager',
                'module' => 'account',
                'description' => 'Manage permissions for roles'
            ]
        ];
        $this->table('permission')
            ->insert($datas)
            ->save();

        // Role_permission
        $this->table('role_permission', ['id' => false, 'primary_key' => ['role_id', 'permission_id']])
            ->addColumn('role_id', 'integer')
            ->addColumn('permission_id', 'integer')
            ->addForeignKey('role_id', 'role', 'id')
            ->addForeignKey('permission_id', 'permission', 'id')
            ->create();
    }
}
