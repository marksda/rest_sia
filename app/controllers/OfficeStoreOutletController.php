<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;
use MyApp\Services\ServiceException;
use MyApp\Services\AbstractService;
use MyApp\Controllers\HttpExceptions\Http422Exception;
use MyApp\Controllers\HttpExceptions\Http500Exception;

class OfficeStoreOutletController extends Controller
{

    /**
	 * Adding office
	 */
    public function addAction()
    {
        $data = $this->request->getJsonRawBody();
        
        try {
            $this->officeStoreOutletService->createOffice($data);
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
     * Updating existing office
     *
     * @param string $officeIdLama
     */
    public function updateAction($officeIdLama)
    {
        $data = $this->request->getJsonRawBody();
        
        try {
            $this->officeStoreOutletService->updateOffice($officeIdLama, $data);
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
     * Delete an existing office
     *
     * @param string $officeId
     */
    public function deleteAction($officeId)
    {
        try {
            $this->officeStoreOutletService->deleteOffice($officeId);
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
     * Returns office list
     *
     * @return array
     */
    public function listAction()
    {
        try {
            $officeList = $this->officeStoreOutletService->getOfficeList();
        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
        }

        return $officeList;
    }

}