<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;
use MyApp\Services\ServiceException;
use MyApp\Services\AbstractService;
use MyApp\Controllers\HttpExceptions\Http422Exception;
use MyApp\Controllers\HttpExceptions\Http500Exception;

class NeracaSaldoController extends Controller
{

    /**
	 * Adding neracaSaldo
	 */
    public function addAction()
    {
        $data = $this->request->getJsonRawBody();
        
        try {
            $this->neracaSaldoService->createNeracaSaldo($data);
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
     * Updating existing neracaSaldo
     *
     * @param string $neracaSaldoIdLama
     */
    // public function updateAction($idLama, $idPerusahaanLama)
    // {
    //     $data = $this->request->getJsonRawBody();
        
    //     try {
    //         $this->neracaSaldoService->updateNeracaSaldo($idPerusahaanLama, $data);
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
     * Delete an existing neracaSaldo
     *
     * @param string $id
     * @param string $idPerusahaan
     */
    public function deleteAction($id, $idPerusahaan)
    {
        try {
            $this->neracaSaldoService->deleteNeracaSaldo($id, $idPerusahaan);
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
     * Returns neracaSaldo list
     *
     * @param string $idPerusahaan
     * @param string $priodeAkuntansi
     * @return array
     */
    public function listAction($idPerusahaan, $priodeAkuntansi)
    {
        try {
            $neracaSaldoList = $this->neracaSaldoService->getNeracaSaldoList($idPerusahaan, $priodeAkuntansi);
        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
        }

        return $neracaSaldoList;
    }

}