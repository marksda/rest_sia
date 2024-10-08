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
        $filterNeracaSaldo = new \stdClass;
        $filterNeracaSaldo->perusahaan = $data->perusahaan;
        $filterNeracaSaldo->tanggal = $data->tanggal;
        
        $filterJurnal = new \stdClass;
        $filterJurnal->perusahaan = $data->perusahaan;
        $filterJurnal->tanggal = $data->tanggal;
        $filterJurnal->jenisJurnal = (object) array("id" => '06', "nama" => 'JURNAL PENYESUAIAN');
                
        try {
            $dataNeracaSaldo = $this->neracaSaldoService->getNeracaSaldoList($filterNeracaSaldo);
            $dataJurnalPenyesuaian = $this->jurnalService->getJurnalList($filterJurnal);
            
            $this->neracaLajurService->createNeracaLajur($data->perusahaan, $data->tanggal, $dataNeracaSaldo, $dataJurnalPenyesuaian);
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

    /**
     * Delete an existing neracaSaldo
     *
     * @param string $idNeracaLajur
     * @param string $idPerusahaan
     */
    public function deleteAction($idNeracaLajur, $idPerusahaan)
    {
        try {
            $perusahaan = new stdClass;
            $perusahaan->id = $idPerusahaan;
            $this->neracaLajurService->deleteNeracaLajur($idNeracaLajur, $perusahaan);
        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case AbstractService::ERROR_UNABLE_DELETE_ITEM:
                case AbstractService::ERROR_ITEM_NOT_FOUND:
                    throw new Http422Exception($e->getMessage(), $e->getCode(), $e);
                case AbstractService::ERROR_FOREIGN_KEY_VIOLATION:
                    throw new Http422Exception($e->getMessage(), $e->getCode(), $e);    
                default:
                    throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
            }
        }
    }

    /**
     * Returns neraca lajur
     *
     * @param string $periode
     * @param string $idPerusahaan
     * @return array
     */
    public function listAction($periode, $idPerusahaan)
    {
        try {
            $perusahaan = new stdClass;
            $perusahaan->id = $idPerusahaan;
            $neracaSaldoList = $this->neracaSaldoService->getNeracaSaldoList($periode, $perusahaan);
        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
        }

        return $neracaSaldoList;
    }

}