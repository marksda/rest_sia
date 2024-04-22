<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;
use MyApp\Services\ServiceException;
use MyApp\Services\AbstractService;
use MyApp\Controllers\HttpExceptions\Http422Exception;
use MyApp\Controllers\HttpExceptions\Http500Exception;

class HakAksesController extends Controller
{

    /**
	 * Adding hak akses
	 */
    public function addAction()
    {
        $data = $this->request->getJsonRawBody();
        
        try {
            $this->hakAksesService->createHakAkses($data);
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
     * Updating existing hak akses 
     *
     * @param string $hakAksesIdLama
     */
    public function updateAction($hakAksesIdLama)
    {
        $data = $this->request->getJsonRawBody();
        
        try {
            $this->hakAksesService->updateHakAkses($hakAksesIdLama, $data);
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
     * Delete an existing hak akses
     *
     * @param string $hakAksesId
     */
    public function deleteAction($hakAksesId)
    {
        try {
            $this->hakAksesService->deleteHakAkses($hakAksesId);
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
     * Returns hak askses list
     *
     * @return array
     */
    public function listAction()
    {
        try {
            $hakAksesList = $this->hakAksesService->getHakAksesList();
        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
        }

        return $hakAksesList;
    }

}