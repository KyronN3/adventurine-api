<?php

namespace App\Exceptions;

use Exception;

class NominateParticipantException extends Exception
{
    public string $internalMessage;

    public function __construct(string     $userMessage = "User Message: Problems in Nominate Participant Service Layer",
                                string     $internalMessage = "",
                                int        $code = 500,
                                ?Exception $previous = null)
    {
        $this->internalMessage = $internalMessage;
        parent::__construct($userMessage, $code, $previous);
    }
}
