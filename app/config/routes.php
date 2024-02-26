<?php

use Phalcon\Mvc\Micro\Collection as MicroCollection;
use MyApp\Controllers\UsersController;
use MyApp\Controllers\TokensController;
use MyApp\Controllers\BarangController;

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