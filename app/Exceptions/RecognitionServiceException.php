<?php

namespace App\Exceptions;

use Exception;

class RecognitionServiceException extends Exception
{
    public string $internalMessage;

    public function __construct(string     $userMessage = "User Message: Problems in Recognition Service Layer",
                                string     $internalMessage = "",
                                int        $code = 500,
                                ?Exception $previous = null)
    {
        $this->internalMessage = $internalMessage;
        parent::__construct($userMessage, $code, $previous);
    }
}
