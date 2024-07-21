<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;
use MyApp\Services\ServiceException;
use MyApp\Services\AbstractService;
use MyApp\Controllers\HttpExceptions\Http422Exception;
use MyApp\Controllers\HttpExceptions\Http500Exception;

class KelompokAkunController extends Controller
{

    /**
	 * Adding kelompokAkun
	 */
    public function addAction()
    {
        $data = $this->request->getJsonRawBody();
        
        try {
            $this->kelompokAkunService->createKelompokAkun($data);
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
     * Updating existing kelompokAkun
     *
     * @param string $kelompokAkunIdLama
     */
    public function updateAction($idLama)
    {
        $data = $this->request->getJsonRawBody();
        
        try {
            $this->kelompokAkunService->updateKelompokAkun($idLama, $data);
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
     * Delete an existing kelompokAkun
     *
     * @param string $kelompokAkunId
     */
    public function deleteAction($idLama)
    {
        try {
            $this->kelompokAkunService->deleteKelompokAkun($idLama);
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
     * Returns kelompokAkun list
     *
     * @return array
     */
    public function listAction()
    {
        try {
            $kelompokAkunList = $this->kelompokAkunService->getKelompokAkunList($filter);
        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
        }

        return $kelompokAkunList;
    }

}