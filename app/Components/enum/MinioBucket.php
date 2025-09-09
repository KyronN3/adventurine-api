<?php

namespace App\Components\enum;

enum MinioBucket: string
{
    case RECOGNITION_IMAGE = 'recognition-image-uploads';
    case RECOGNITION_FILE  = 'recognition-file-uploads';
    case EVENT_IMAGE       = 'event-image-uploads';
    case EVENT_FILE        = 'event-file-uploads';
    case CERTIFICATE       = 'certificates';
}
