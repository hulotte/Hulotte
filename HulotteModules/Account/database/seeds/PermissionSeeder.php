<?php

use Phinx\Seed\AbstractSeed;

class PermissionSeeder extends AbstractSeed
{
    public function run()
    {
        $datas = [
            ['role_id' => 1, 'permission_id' => 1]
        ];
        $this->table('role_permission')
            ->insert($datas)
            ->save();
    }
}
