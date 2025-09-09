<?php

namespace App\Exceptions;

use Exception;

class MinioException extends Exception
{
    public string $internalMessage;

    public function __construct(string     $userMessage = "User Message: Problems in Minio Layer",
                                string     $internalMessage = "",
                                int        $code = 0,
                                \Throwable $previous = null)
    {
        parent::__construct($userMessage, $code, $previous);
        $this->internalMessage = $internalMessage;
    }
}
