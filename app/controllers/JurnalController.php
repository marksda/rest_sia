<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;
use MyApp\Services\ServiceException;
use MyApp\Services\AbstractService;
use MyApp\Controllers\HttpExceptions\Http422Exception;
use MyApp\Controllers\HttpExceptions\Http500Exception;

class JurnalController extends Controller
{

    /**
	 * Adding jurnal
	 */
    public function addAction()
    {
        $data = $this->request->getJsonRawBody();
        
        try {
            $this->jurnalService->createJurnal($data);
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
     * Updating existing jurnal 
     *
     * @param string $idLama
	 * @param string $idPerusahaanLama
	 * @param string $idJenisJurnalLama
     * 
     */
    public function updateAction($idLama, $idPerusahaanLama, $idJenisJurnalLama)
    {
        $data = $this->request->getJsonRawBody();
        
        try {
            $this->jurnalService->updateJurnal($idLama, $idPerusahaanLama, $idJenisJurnalLama, $data);
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
     * Delete an existing jurnal
     *
     * @param string $idLama
	 * @param string $idPerusahaanLama
	 * @param string $idJenisJurnalLama
     */
    public function deleteAction($idLama, $idPerusahaanLama, $idJenisJurnalLama)
    {
        try {
            $this->jurnalService->deleteJurnal($idLama, $idPerusahaanLama, $idJenisJurnalLama);
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
     * Returns jurnal list
     *
     * @return array
     */
    public function listAction()
    {
        try {
            $jurnalList = $this->jurnalService->getJurnalList();
        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
        }

        return $jurnalList;
    }
}