<?php

namespace App\Components\enum;

enum LogLevel: string
{
    case INFO = 'info';
    case ERROR = 'error';
    case WARNING = 'warning';
}


