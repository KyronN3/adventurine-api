<?php

namespace App\Services\certificate;

use App\Models\recognition\Recognition;
use App\Services\MinioService;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;

class CertificateServiceV2
{
    protected MinioService $minioService;

    public function __construct(MinioService $minioService)
    {
        $this->minioService = $minioService;
    }

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function generateRecognitionCertificate(Recognition $recognition)
    {
        $templatePath = storage_path('storage/app/templates/certificate.docx');;
        $template = new TemplateProcessor($templatePath);

        $template->setValue('title', "CERTIFICATE OF RECOGNITION");
        $template->setValue('name', $recognition['employee_name']);
        $template->setValue('event-type', $recognition['recognition_type']);

    }

    public function generateEventCertificate()
    {
    }

    private function body(Recognition $recognition)
    {


    }

    private function referring(string $type): string
    {
        $type = strtolower($type);

        return match ($type) {
            "academic achievement" => "for having demonstrated exemplary performance in",
            "certificate" => "for being recognized and commended for",
            "service milestone" => "for having faithfully rendered dedicated service for",
            default => "just ";
    }
    }


}
