<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;
use MyApp\Services\ServiceException;
use MyApp\Services\AbstractService;
use MyApp\Controllers\HttpExceptions\Http422Exception;
use MyApp\Controllers\HttpExceptions\Http500Exception;

class MetodePendekatanAkutansiController extends Controller
{
    /**
	 * Adding MetodePendekatanAkutansi
	 */
    public function addAction()
    {
        $data = $this->request->getJsonRawBody();
                
        try {            
            $this->metodePendekatanAkutansi->createMetodePendekatanAkutansi($data);
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
     * Delete an existing MetodePendekatanAkutansi
     *
     * @param string $idMetodePendekatanAkutansi
     * @param string $idPerusahaan
     */
    public function deleteAction($idMetodePendekatanAkutansi)
    {
        try {
            $this->metodePendekatanAkutansi->deleteMetodePendekatanAkutansi($idMetodePendekatanAkutansi);
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
     * Updating existing MetodePendekatanAkutansi
     *
     * @param string $idLama
     */
    public function updateAction($idLama)
    {
        $dataBaru = $this->request->getJsonRawBody();
        
        try {
            $this->metodePendekatanAkutansi->updateMetodePendekatanAkutansi($idLama, $dataBaru);
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
     * Returns MetodePendekatanAkutansi
     *
     * @param string $periode
     * @param string $idPerusahaan
     * @return array
     */
    public function listAction()
    {
        try {
            $metodePendekatanAkutansiList = $this->metodePendekatanAkutansi->getMetodePendekatanAkutansiList();

        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
        }

        return $metodePendekatanAkutansiList;
    }

}