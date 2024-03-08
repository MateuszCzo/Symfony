<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ServiceException extends HttpException
{
    private ServiceExceptionData $exceptionData;

    public function __construct(ServiceExceptionData $exceptionData) {
        $this->exceptionData = $exceptionData;
        $statusCode = $exceptionData->getStatusCode();
        $message = $exceptionData->getType();
        parent::__construct($statusCode, $message);
    }

    public function getExceptionData(): ServiceExceptionData
    {
        return $this->exceptionData;
    }
}