<?php

namespace MyApp\Controllers\HttpExceptions;

use MyApp\Controllers\AbstractHttpException;

/**
 * Class Http422Exception
 *
 * Execption class for Unprocessable entity Error (422)
 *
 * @package App\Lib\Exceptions
 */
class Http422Exception extends AbstractHttpException
{
    protected $httpCode = 422;
    protected $httpMessage = 'Unprocessable entity';
}
