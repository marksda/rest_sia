<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;
use MyApp\Services\ServiceException;
use MyApp\Services\AbstractService;
use MyApp\Controllers\HttpExceptions\Http422Exception;
use MyApp\Controllers\HttpExceptions\Http500Exception;

class DetailJurnalController extends Controller
{

    /**
	 * Adding detailJurnal
	 */
    public function addAction()
    {
        $data = $this->request->getJsonRawBody();
        
        try {
            $this->detailJurnalService->createDetailJurnal($data);
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
     * Updating existing detailJurnal
     *
     * @param string $detailJurnalIdLama
     */
    // public function updateAction($idJurnalLama, $idPerusahaanLama, $idAkunLama)
    // {
    //     $data = $this->request->getJsonRawBody();
        
    //     try {
    //         $this->detailJurnalService->updateDetailJurnal($idJurnalLama, $idPerusahaanLama, $idAkunLama, $data);
    //     } catch (ServiceException $e) {
    //         switch ($e->getCode()) {
    //             case AbstractService::ERROR_ALREADY_EXISTS:
    //             case AbstractService::ERROR_UNABLE_CREATE_ITEM:
    //                 throw new Http422Exception($e->getMessage(), $e->getCode(), $e);
    //             default:
    //                 throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
    //         }
    //     }
    // }

    /**
     * Delete an existing detailJurnal
     *
     * @param string $detailJurnalId
     */
    // public function deleteAction($idJurnalLama, $idPerusahaanLama, $idAkunLama)
    // {
    //     try {
    //         $this->detailJurnalService->deleteDetailJurnal($idJurnalLama, $idPerusahaanLama, $idAkunLama);
    //     } catch (ServiceException $e) {
    //         switch ($e->getCode()) {
    //             case AbstractService::ERROR_UNABLE_DELETE_ITEM:
    //             case AbstractService::ERROR_ITEM_NOT_FOUND:
    //                 throw new Http422Exception($e->getMessage(), $e->getCode(), $e);
    //             case AbstractService::ERROR_FOREIGN_KEY_VIOLATION:
    //                 throw new Http422Exception($e->getMessage(), $e->getCode(), $e);    
    //             default:
    //                 throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
    //         }
    //     }
    // }

    /**
     * Returns detailJurnal list
     *
     * @return array
     */
    public function listAction()
    {
        try {
            $detailJurnalList = $this->detailJurnalService->getDetailJurnalList();
        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
        }

        return $detailJurnalList;
    }

}