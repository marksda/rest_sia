<?php

use Phalcon\Mvc\Micro\Collection as MicroCollection;
use MyApp\Controllers\UserController;
use MyApp\Controllers\TokenController;
use MyApp\Controllers\BarangController;
use MyApp\Controllers\PropinsiController;
use MyApp\Controllers\KabupatenController;
use MyApp\Controllers\KecamatanController;
use MyApp\Controllers\DesaController;
use MyApp\Controllers\PerusahaanController;
use MyApp\Controllers\OfficeStoreOutletController;
use MyApp\Controllers\JenisJurnalController;
use MyApp\Controllers\HakAksesController;
use MyApp\Controllers\ModulController;
use MyApp\Controllers\JurnalController;
use MyApp\Controllers\AkunController;
use MyApp\Controllers\KelompokAkunController;
use MyApp\Controllers\DetailJurnalController;
use MyApp\Controllers\BukuBesarController;
use MyApp\Controllers\NeracaSaldoController;
use MyApp\Controllers\NeracaLajurController;
use MyApp\Controllers\MetodePendekatanAkutansiController;


// $methodenya = $app->request->getMethod();
if (strtoupper($app->request->getMethod()) != 'OPTIONS') {
    $path = $app->request->getURI(true);
    $parts = explode("/", $path);
    $collection = $parts[2];

    switch ($collection) {        
        case 'user':
            $userCollection = new MicroCollection();

            $userCollection
                ->setHandler(UserController::class, true)
                ->setPrefix('/api/user')
                ->get('/list', 'listAction')
                ->post('/add', 'addAction')
                ;

            $app->mount($userCollection);

            break;
        case 'token':
            $userCollection = new MicroCollection();

            $userCollection
                ->setHandler(TokenController::class, true)
                ->setPrefix('/api/token')
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
                ->delete('/{barangId:[a-zA-Z0-9\_\-]+}', 'deleteAction')
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
                ->delete('/{id:[a-zA-Z0-9\_\-]+}', 'deleteAction')
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
                ->delete('/{id:[a-zA-Z0-9\_\-]+}', 'deleteAction')
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
                ->delete('/{id:[a-zA-Z0-9\_\-]+}', 'deleteAction')
                ;

            $app->mount($kecamatanCollection);

            break;
        case 'desa':
            $desaCollection = new MicroCollection();

            $desaCollection
                ->setHandler(DesaController::class, true)
                ->setPrefix('/api/desa')
                ->get('/list', 'listAction')
                ->post('/add', 'addAction')
                ->put('/{idLama:[a-zA-Z0-9\_\-]+}', 'updateAction')
                ->delete('/{id:[a-zA-Z0-9\_\-]+}', 'deleteAction')
                ;

            $app->mount($desaCollection);

            break;
        case 'perusahaan':
            $perusahaanCollection = new MicroCollection();

            $perusahaanCollection
                ->setHandler(PerusahaanController::class, true)
                ->setPrefix('/api/perusahaan')
                ->get('/list', 'listAction')
                ->post('/add', 'addAction')
                ->put('/{perusahaanIdLama:[a-zA-Z0-9\_\-]+}', 'updateAction')
                ->delete('/{perusahaanId:[a-zA-Z0-9\_\-]+}', 'deleteAction')
                ;

            $app->mount($perusahaanCollection);

            break;
        case 'office':
                $officeCollection = new MicroCollection();
        
                $officeCollection
                    ->setHandler(OfficeStoreOutletController::class, true)
                    ->setPrefix('/api/office')
                    ->get('/list', 'listAction')
                    ->post('/add', 'addAction')
                    ->put('/{officeIdLama:[a-zA-Z0-9\_\-]+}', 'updateAction')
                    ->delete('/{officeId:[a-zA-Z0-9\_\-]+}', 'deleteAction')
                    ;
        
                $app->mount($officeCollection);
        
                break;
        case 'jenis_jurnal':
            $jenisJurnalCollection = new MicroCollection();

            $jenisJurnalCollection
                ->setHandler(JenisJurnalController::class, true)
                ->setPrefix('/api/jenis_jurnal')
                ->get('/list', 'listAction')
                ->post('/add', 'addAction')
                ->put('/{jenisJurnalIdLama:[a-zA-Z0-9\_\-]+}', 'updateAction')
                ->delete('/{jenisJurnalId:[a-zA-Z0-9\_\-]+}', 'deleteAction')
                ;

            $app->mount($jenisJurnalCollection);

            break;   
        case 'hak_akses':
            $hakAksesCollection = new MicroCollection();

            $hakAksesCollection
                ->setHandler(HakAksesController::class, true)
                ->setPrefix('/api/hak_akses')
                ->get('/list', 'listAction')
                ->post('/add', 'addAction')
                ->put('/{jenisJurnalIdLama:[a-zA-Z0-9\_\-]+}', 'updateAction')
                ->delete('/{jenisJurnalId:[a-zA-Z0-9\_\-]+}', 'deleteAction')
                ;

            $app->mount($hakAksesCollection);

            break;     
        case 'modul':
            $modulCollection = new MicroCollection();

            $modulCollection
                ->setHandler(ModulController::class, true)
                ->setPrefix('/api/modul')
                ->get('/list', 'listAction')
                ->post('/add', 'addAction')
                ->put('/{jenisJurnalIdLama:[a-zA-Z0-9\_\-]+}', 'updateAction')
                ->delete('/{jenisJurnalId:[a-zA-Z0-9\_\-]+}', 'deleteAction')
                ;

            $app->mount($modulCollection);

            break;   
        case 'jurnal':
            $jurnalCollection = new MicroCollection();

            $jurnalCollection
                ->setHandler(JurnalController::class, true)
                ->setPrefix('/api/jurnal')
                ->get('/list', 'listAction')
                ->post('/add', 'addAction')
                // ->put('/{idLama:[a-zA-Z0-9\_\-]+}/{idPerusahaanLama:[a-zA-Z0-9\_\-]+}/{idJenisJurnalLama:[a-zA-Z0-9\_\-]+}', 'updateAction')
                // ->delete('/{idLama:[a-zA-Z0-9\_\-]+}/{idPerusahaanLama:[a-zA-Z0-9\_\-]+}/{idJenisJurnalLama:[a-zA-Z0-9\_\-]+}', 'deleteAction')
                ;

            $app->mount($jurnalCollection);

            break;   
        case 'akun':
            $akunCollection = new MicroCollection();

            $akunCollection
                ->setHandler(AkunController::class, true)
                ->setPrefix('/api/akun')
                ->get('/list', 'listAction')
                ->post('/add', 'addAction')
                ->put('/{idLama:[a-zA-Z0-9\_\-]+}/{idPerusahaanLama:[a-zA-Z0-9\_\-]+}', 'updateAction')
                ->delete('/{idLama:[a-zA-Z0-9\_\-]+}/{idPerusahaanLama:[a-zA-Z0-9\_\-]+}', 'deleteAction')
                ;

            $app->mount($akunCollection);

            break; 
        case 'kelompok_akun':
            $kelompokAkunCollection = new MicroCollection();

            $kelompokAkunCollection
                ->setHandler(KelompokAkunController::class, true)
                ->setPrefix('/api/kelompok_akun')
                ->get('/list', 'listAction')
                ->post('/add', 'addAction')
                ->put('/{idLama:[a-zA-Z0-9\_\-]+}', 'updateAction')
                ->delete('/{idLama:[a-zA-Z0-9\_\-]+}', 'deleteAction')
                ;

            $app->mount($kelompokAkunCollection);

            break;     
        case 'detail_jurnal':
            $akunCollection = new MicroCollection();

            $akunCollection
                ->setHandler(AkunController::class, true)
                ->setPrefix('/api/detail_jurnal')
                ->get('/list', 'listAction')
                ->post('/add', 'addAction')
                // ->put('/{idJurnalLama:[a-zA-Z0-9\_\-]+}/{idPerusahaanLama:[a-zA-Z0-9\_\-]+}/{idAkunLama:[a-zA-Z0-9\_\-]+}', 'updateAction')
                // ->delete('/{idJurnalLama:[a-zA-Z0-9\_\-]+}/{idPerusahaanLama:[a-zA-Z0-9\_\-]+}/{idAkunLama:[a-zA-Z0-9\_\-]+}', 'deleteAction')
                ;

            $app->mount($akunCollection);

            break;
        case 'buku_besar':
            $bukuBesarCollection = new MicroCollection();

            $bukuBesarCollection
                ->setHandler(BukuBesarController::class, true)
                ->setPrefix('/api/buku_besar')
                ->get('/list', 'listAction')
                ->post('/add', 'addAction')
                // ->put('/{jurnalIdLama:[a-zA-Z0-9\_\-]+}/{$perusahaanIdLama:[a-zA-Z0-9\_\-]+}/{$akunIdLama:[a-zA-Z0-9\_\-]+}', 'updateAction')
                // ->delete('/{jurnalIdLama:[a-zA-Z0-9\_\-]+}/{$perusahaanIdLama:[a-zA-Z0-9\_\-]+}/{$akunIdLama:[a-zA-Z0-9\_\-]+}', 'deleteAction')
                ;

            $app->mount($akunCollection);

            break;  
        case 'neraca_saldo':
            $neracaSaldoCollection = new MicroCollection();

            $neracaSaldoCollection
                ->setHandler(NeracaSaldoController::class, true)
                ->setPrefix('/api/neraca_saldo')
                ->get('/list/{$idperusahaan:[a-zA-Z0-9\_\-]+}/{$priodeAkuntansi:[a-zA-Z0-9\_\-]+}', 'listAction')
                ->post('/add', 'addAction')
                ->delete('/{id:[a-zA-Z0-9\_\-]+}/{$idperusahaan:[a-zA-Z0-9\_\-]+}', 'deleteAction')
                ;

            $app->mount($akunCollection);

            break;      
        case 'neraca_lajur':
            $neracaLajurCollection = new MicroCollection();

            $neracaLajurCollection
                ->setHandler(NeracaLajurController::class, true)
                ->setPrefix('/api/neraca_lajur')
                ->get('/list/{$periode:[a-zA-Z0-9\_\-]+}/{$idPerusahaan:[a-zA-Z0-9\_\-]+}', 'listAction')
                ->post('/add', 'addAction')
                ->delete('/{idNeracaLajur:[a-zA-Z0-9\_\-]+}/{$idperusahaan:[a-zA-Z0-9\_\-]+}', 'deleteAction')
                ;

            $app->mount($akunCollection);

            break;        
        case 'metode_pendekatan_akutansi':
            $metodePendekatanAkutansiCollection = new MicroCollection();

            $metodePendekatanAkutansiCollection
                ->setHandler(MetodePendekatanAkutansiController::class, true)
                ->setPrefix('/api/metode_pendekatan_akutansi')
                ->get('/list', 'listAction')
                ->post('/add', 'addAction')
                ->put('/{idLama:[a-zA-Z0-9\_\-]+}', 'updateAction')
                ->delete('/{id:[a-zA-Z0-9\_\-]+}', 'deleteAction')
                ;

            $app->mount($metodePendekatanAkutansiCollection);

            break;      
        default:
            // throw new \RuntimeException('HttpException without httpCode or httpMessage');        
            break;
    }
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