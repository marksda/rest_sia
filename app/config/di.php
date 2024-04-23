<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Encryption\Security;
use Phalcon\Db\Adapter\Pdo\Postgresql;
use MyApp\Services\UserService;
use MyApp\Services\TokensService;
use MyApp\Services\BarangService;
use MyApp\Services\PropinsiService;
use MyApp\Services\KabupatenService;
use MyApp\Services\KecamatanService;
use MyApp\Services\DesaService;
use MyApp\Services\PerusahaanService;
use MyApp\Services\OfficeStoreOutletService;
use MyApp\Services\JenisJurnalService;
use MyApp\Services\HakAksesService;
use MyApp\Services\ModulService;


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
    'userService',
    UserService::class
);

$di->setShared(
    'tokensService',
    TokensService::class
);

$di->setShared(
    'barangService',
    BarangService::class
);

$di->setShared(
    'propinsiService',
    PropinsiService::class
);

$di->setShared(
    'kabupatenService',
    KabupatenService::class
);

$di->setShared(
    'kecamatanService',
    KecamatanService::class
);

$di->setShared(
    'desaService',
    DesaService::class
);

$di->setShared(
    'perusahaanService',
    PerusahaanService::class
);

$di->setShared(
    'officeStoreOutletService',
    OfficeStoreOutletService::class
);

$di->setShared(
    'jenisJurnalService',
    JenisJurnalService::class
);

$di->setShared(
    'hakAksesService',
    HakAksesService::class
);

$di->setShared(
    'modulService',
    ModulService::class
);

return $di;