<?php

namespace App\Exceptions;

use Exception;

class CertificateServiceException extends Exception
{
    protected string $internalMessage;

    public function __construct(string $userMessage = "User Message: Problems in Certificate Service Layer",
                                string $internalMessage = "",
                                int $code = 0,
                                ?Exception $previous = null)
    {
        $this->internalMessage = $internalMessage;
        parent::__construct($userMessage, $code, $previous);
    }
}
