<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;
use MyApp\Services\ServiceException;
use MyApp\Services\AbstractService;
use MyApp\Controllers\HttpExceptions\Http422Exception;
use MyApp\Controllers\HttpExceptions\Http500Exception;

class NeracaLajurController extends Controller
{
    /**
	 * Adding neracaLajur
	 */
    public function addAction()
    {
        $data = $this->request->getJsonRawBody();
        $filterNeracaSaldo = new stdClass;
        $filterNeracaSaldo->perusahaan = $data->perusahaan;
        $filterNeracaSaldo->tanggal = $data->tanggal;
        
        $filterJurnal = new stdClass;
        $filterJurnal->perusahaan = $data->perusahaan;
        $filterJurnal->tanggal = $data->tanggal;
        $filterJurnal->jenisJurnal = (object) array("id" => '06', "nama" => 'JURNAL PENYESUAIAN');
                
        try {
            $listNeracaSaldo = $this->neracaSaldoService->getNeracaSaldoList($filterNeracaSaldo);
            $dataNeracaSaldo = count($listNeracaSaldo) > 1 ? (object) $listNeracaSaldo[0]:null;
            $listJurnalPenyesuaian = $this->jurnalService->getJurnalList($filterJurnal);
            $dataJurnalPenyesuaian = count($listJurnalPenyesuaian) > 1 ? (object) $listJurnalPenyesuaian[0]:null;
            
            $this->neracaLajurService->createNeracaLajur($dataNeracaSaldo, $dataJurnalPenyesuaian);
        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case AbstractService::ERROR_ALREADY_EXISTS:
                case AbstractService::ERROR_UNABLE_CREATE_ITEM:
                    throw new Http422Exception($e->getMessage(), $e->getCode(), $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
            }
        }
    }

}