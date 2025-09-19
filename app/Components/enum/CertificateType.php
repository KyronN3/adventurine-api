<?php

namespace App\Components\enum;

enum CertificateType: string
{
    case RECOGNITION = "Recognition";
    case RECOGNITIONV2 = "RecognitionV2";
    case EVENT = "Event";
}
