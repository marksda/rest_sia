<?php

use Phalcon\Config\Config;

return new Config(
    [
        'database' => [
            'adapter' => 'Postgresql',
            'host' => '192.168.1.111',
            'port' => 5432,
            'username' => 'postgres',
            'password' => 'i5a8190882',
            'dbname' => 'siadb',
            'persistent' => true
        ],
    ]
);
