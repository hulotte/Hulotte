<?php

use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
    public function run()
    {
        $data = [
            'civility' => 'M.',
            'name' => 'CLEMENT',
            'firstName' => 'Sébastien',
            'email' => 'russandol@msn.com',
            'password' => password_hash('password', PASSWORD_DEFAULT)
        ];

        $this->table('user')->insert($data)->save();
    }
}
