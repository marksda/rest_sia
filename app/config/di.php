<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Encryption\Security;
use Phalcon\Db\Adapter\Pdo\Postgresql;
use MyApp\Services\UsersService;
use MyApp\Services\TokensService;
use MyApp\Services\BarangService;


$di = new FactoryDefault();

$di->set(
    'db',
    function () use ($config)  {
        return new Postgresql(
            [
                'host'     => $config->database->host,
                'username' => $config->database->username,
                'password' => $config->database->password,
                'dbname'   => $config->database->dbname,
            ]
        );
    }
);

$di->setShared(
    'security',
    function () {
        $security = new Security();
        $security->setWorkFactor(12);
        return $security;
    }
);

$di->setShared(
    'usersService',
    UsersService::class
);

$di->setShared(
    'tokensService',
    TokensService::class
);

$di->setShared(
    'barangService',
    BarangService::class
);

return $di;