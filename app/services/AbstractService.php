<?php

namespace MyApp\Services;

use \Phalcon\Di\Injectable;

abstract class AbstractService extends Injectable 
{

    const ERROR_INVALID_PARAMETERS = 10001;

    const ERROR_ALREADY_EXISTS = 10002;

    const ERROR_UNABLE_CREATE_ITEM = 11001;

    const ERROR_ITEM_NOT_FOUND = 11002;

    const ERROR_INCORRECT_ITEM = 11003;

    const ERROR_UNABLE_UPDATE_ITEM = 11004;

    const ERROR_UNABLE_DELETE_ITEM = 1105;

}