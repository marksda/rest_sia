<?php

namespace MyApp\Controllers;

use MyApp\Services\ServiceException;

abstract class AbstractHttpException extends \RuntimeException
{
    
    const KEY_CODE = 'error';
    const KEY_DETAILS = 'details';
    const KEY_MESSAGE = 'error_description';

    protected $httpCode = null;

    protected $httpMessage = null;

    protected $appError = [];

    public function __construct($appErrorMessage = null, $appErrorCode = null, \Exception $previous = null)
    {
        if (is_null($this->httpCode) || is_null($this->httpMessage)) {
            throw new \RuntimeException('HttpException without httpCode or httpMessage');
        }

        if ($previous instanceof ServiceException) {
            if (is_null($appErrorCode)) {
                $appErrorCode = $previous->getCode();
            }

            if (is_null($appErrorMessage)) {
                $appErrorMessage = $previous->getMessage();
            }
        }

        $this->appError = [
          self::KEY_CODE => $appErrorCode,
          self::KEY_MESSAGE => $appErrorMessage
        ];

        parent::__construct($this->httpMessage, $this->httpCode, $previous);
    }

    public function getAppError()
    {
        return $this->appError;
    }

    public function addErrorDetails(array $fields)
    {
        if (array_key_exists(self::KEY_DETAILS, $this->appError)) {
            $fields = array_merge($this->appError[self::KEY_DETAILS], $fields);
        }
        $this->appError[self::KEY_DETAILS] = $fields;

        return $this;
    }
}
