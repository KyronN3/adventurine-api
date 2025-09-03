<?php

namespace App\Services;

use App\Components\enum\CertificateType;
use App\Components\enum\MinioBucket;
use App\Exceptions\CertificateServiceException;
use App\Exceptions\MinioException;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CertificateService
{
    protected MinioService $minioService;

    public function __construct(MinioService $minioService)
    {
        $this->minioService = $minioService;
    }

    /**
     * @throws CertificateServiceException
     */
    public function generateCertificate(string $name, string $description, $date, CertificateType $type)
    {
        try {
            $issuedDate = Carbon::createFromFormat('Y-m-d', $date)->format('F d, Y');

            $pdf = Pdf::loadView("template.$type->value", [
                'awardeeName' => $name,
                'achievement' => $description,
                'issuedDate' => $issuedDate,
            ]);

            $pdf->setPaper([0, 0, 1500, 1062]);
            return $pdf->output();
        } catch (\Exception $e) {
            throw new CertificateServiceException(
                "Failed to generate certificate. Internal error.",
                $e->getMessage(),
                (int)$e->getCode(),
                $e);
        }
    }


    /**
     * @throws CertificateServiceException
     * Saves certificate PDF to MinIO
     * Returns a presign url for view
     */
    public function saveCertificate($file, string $fileName, int|string $id): array
    {
        try {
            $key = $this->minioService->fileNameConvert($fileName, $id);
            return $this->minioService->saveFile($key, $file, MinioBucket::CERTIFICATE);

        } catch (MinioException $e) {
            throw new CertificateServiceException(
                $e->getMessage(),
                'Failed to save certificate in storage file.',
                (int)$e->getCode(),
                $e);
        } catch (\Exception $e) {
            throw new CertificateServiceException(
                "Failed to save certificate. Internal error.",
                $e->getMessage(),
                (int)$e->getCode(),
                $e);
        }
    }
}
