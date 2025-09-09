<?php

namespace App\Components\enum;

enum LayerLevel: string
{
    case CONTROLLER = 'Controller';
    case SERVICE = 'Service';
    case MODEL = 'Model';
    case REPOSITORY = 'Repository';
}
