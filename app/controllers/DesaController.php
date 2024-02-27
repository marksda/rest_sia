<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;
use MyApp\Services\ServiceException;
use MyApp\Services\AbstractService;
use MyApp\Controllers\HttpExceptions\Http422Exception;
use MyApp\Controllers\HttpExceptions\Http500Exception;

class DesaController extends Controller
{

    /**
	 * Creating a new Desa
	 *
	 */
    public function addAction()
    {
        $data = $this->request->getJsonRawBody();
        
        try {
            $this->desaService->createDesa($data);
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
     * Updating existing Desa
     *
     * @param string $idLama
     */
    public function updateAction($idLama)
    {
        $dataBaru = $this->request->getJsonRawBody();
        
        try {
            $this->desaService->updateDesa($idLama, $dataBaru);
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
     * Delete an existing Desa
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        try {
            $this->desaService->deleteDesa($id);
        } catch (ServiceException $e) {
            switch ($e->getCode()) {
                case AbstractService::ERROR_UNABLE_DELETE_ITEM:
                case AbstractService::ERROR_ITEM_NOT_FOUND:
                    throw new Http422Exception($e->getMessage(), $e->getCode(), $e);
                default:
                    throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
            }
        }
    }

    /**
     * Returns Desa list
     *
     * @return array
     */
    public function listAction()
    {
        try {
            $desaList = $this->desaService->getDesaList();
        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
        }

        return $desaList;
    }

}