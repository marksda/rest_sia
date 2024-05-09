<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;
use MyApp\Services\ServiceException;
use MyApp\Services\AbstractService;
use MyApp\Controllers\HttpExceptions\Http422Exception;
use MyApp\Controllers\HttpExceptions\Http500Exception;

class BukuBesarController extends Controller
{

    /**
	 * Adding buku besar
	 */
    public function addAction()
    {
        $data = $this->request->getJsonRawBody();
        
        try {
            $this->bukuBesarService->createBukuBesar($data);
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
     * Updating existing buku besar 
     *
     * @param string $jurnalIdLama
     * @param string $perusahaanIdLama
     * @param string $akunIdLama
     * 
     */
    public function updateAction($jurnalIdLama, $perusahaanIdLama, $akunIdLama)
    {
        $data = $this->request->getJsonRawBody();
        
        try {
            $this->bukuBesarService->updateBukuBesar($jurnalIdLama, $perusahaanIdLama, $akunIdLama, $data);
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
     * Delete an existing buku besar
     *
     * @param string $idLama
	 * @param string $idPerusahaanLama
	 * @param string $idJenisBukuBesarLama
     */
    public function deleteAction($jurnalIdLama, $perusahaanIdLama, $akunIdLama)
    {
        try {
            $this->bukuBesarService->deleteBukuBesar($jurnalIdLama, $perusahaanIdLama, $akunIdLama);
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
     * Returns buku besar list
     *
     * @return array
     */
    public function listAction()
    {
        try {
            $bukuBesarList = $this->bukuBesarService->getBukuBesarList();
        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
        }

        return $bukuBesarList;
    }
}