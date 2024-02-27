<?php

use Phalcon\Mvc\Micro\Collection as MicroCollection;
use MyApp\Controllers\UsersController;
use MyApp\Controllers\TokensController;
use MyApp\Controllers\BarangController;
use MyApp\Controllers\PropinsiController;
use MyApp\Controllers\KabupatenController;
use MyApp\Controllers\KecamatanController;

$path = $app->request->getURI(true);
$parts = explode("/", $path);
$collection = $parts[2];

switch ($collection) {    
    case 'users':
        $userCollection = new MicroCollection();

        $userCollection
            ->setHandler(UsersController::class, true)
            ->setPrefix('/api/users')
            ->get('/list', 'listAction')
            ->post('/add', 'addAction')
            ;

        $app->mount($userCollection);

        break;
    case 'tokens':
        $userCollection = new MicroCollection();

        $userCollection
            ->setHandler(TokensController::class, true)
            ->setPrefix('/api/tokens')
            ->post('/new', 'newAction')
            ->put('/refresh', 'refreshAction')
            ;

        $app->mount($userCollection);

        break;
    case 'barang':
        $barangCollection = new MicroCollection();

        $barangCollection
            ->setHandler(BarangController::class, true)
            ->setPrefix('/api/barang')
            ->get('/list', 'listAction')
            ->post('/add', 'addAction')
            ->put('/{barangIdLama:[a-zA-Z0-9\_\-]+}', 'updateAction')
            ->delete('/{barangId:[a-zA-Z0-9\_\-]+}', 'deleteAction');
            ;

        $app->mount($barangCollection);

        break;
    case 'propinsi':
        $propinsiCollection = new MicroCollection();

        $propinsiCollection
            ->setHandler(PropinsiController::class, true)
            ->setPrefix('/api/propinsi')
            ->get('/list', 'listAction')
            ->post('/add', 'addAction')
            ->put('/{idLama:[a-zA-Z0-9\_\-]+}', 'updateAction')
            ->delete('/{id:[a-zA-Z0-9\_\-]+}', 'deleteAction');
            ;

        $app->mount($propinsiCollection);

        break;
    case 'kabupaten':
        $kabupatenCollection = new MicroCollection();

        $kabupatenCollection
            ->setHandler(KabupatenController::class, true)
            ->setPrefix('/api/kabupaten')
            ->get('/list', 'listAction')
            ->post('/add', 'addAction')
            ->put('/{idLama:[a-zA-Z0-9\_\-]+}', 'updateAction')
            ->delete('/{id:[a-zA-Z0-9\_\-]+}', 'deleteAction');
            ;

        $app->mount($kabupatenCollection);

        break;
    case 'kecamatan':
        $kecamatanCollection = new MicroCollection();

        $kecamatanCollection
            ->setHandler(KecamatanController::class, true)
            ->setPrefix('/api/kecamatan')
            ->get('/list', 'listAction')
            ->post('/add', 'addAction')
            ->put('/{idLama:[a-zA-Z0-9\_\-]+}', 'updateAction')
            ->delete('/{id:[a-zA-Z0-9\_\-]+}', 'deleteAction');
            ;

        $app->mount($kecamatanCollection);

        break;
    default:
        // throw new \RuntimeException('HttpException without httpCode or httpMessage');        
        break;
}

$app->notFound(
    function () use ($app) {
        $message = 'XXXXXX';
        $app
            ->response
            ->setStatusCode(404, 'Not Found')
            ->sendHeaders()
            ->setContent($message)
            ->send()
        ;
    }
);