<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;
use MyApp\Services\ServiceException;
use MyApp\Controllers\HttpExceptions\Http400Exception;
use MyApp\Controllers\HttpExceptions\Http500Exception;

class TokenController extends Controller
{
    public function newAction()
    {
        try {
            $data = $this->request->getJsonRawBody();
            $hasil = $this->tokenService->createToken($data);
            
            if(count($hasil) == 0) {
                throw new Http400Exception(_('Token can not be generated because authentication failure'), 'Token failed');
            }
            return $hasil;
        } catch (ServiceException $e) {
            throw new Http500Exception(_('Internal Server Error'), $e->getCode(), $e);
        }
        
    }
}