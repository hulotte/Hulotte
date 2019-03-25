<?php

return <<< 'EOD'
<?php
require 'public/index.php';

$migrations = [];
$seeds = [];

foreach($app->getModules() as $module){
    if($module::MIGRATIONS){
        $migrations[] = $module::MIGRATIONS;
    }

    if($module::SEEDS){
        $seeds[] = $module::SEEDS;
    }
}

return [
    'paths' => [
        'migrations' => $migrations,
        'seeds' => $seeds,
    ],
    'environments' => [
        'default_database' => 'development', 
        'development' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'BASE_NAME',
            'user' => 'root',
            'pass' => '',
            'charset' => 'utf8'
        ],
		'production' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'BASE_NAME',
            'user' => 'root',
            'pass' => '',
            'charset' => 'utf8'
        ],
    ],
];

EOD;
