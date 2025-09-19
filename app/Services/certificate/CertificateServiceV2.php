<?php

namespace App\Services\certificate;

use App\Components\enum\CertificateType;
use App\Components\enum\MinioBucket;
use App\Exceptions\CertificateServiceException;
use App\Exceptions\MinioException;
use App\Models\recognition\RecognitionCertificate;
use App\Services\MinioService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

class CertificateServiceV2
{
    protected MinioService $minioService;

    public function __construct(MinioService $minioService)
    {
        $this->minioService = $minioService;
    }

    /**
     * Generate a recognition certificate and return as PDF.
     *
     * @throws CertificateServiceException
     */
    public function generateRecognitionCertificate($certificate): string
    {
        try {
            $type = CertificateType::RECOGNITIONV2;
            $pdf = Pdf::loadView("template.$type->value", [
                'name' => $certificate['employeeName'],
                'citation' => $certificate['citation'],
                'title' => $certificate['title'],
                'description' => $certificate['description'],
                'issueDate' => $certificate['issue'],
                ]);

            $pdf->setPaper([0, 0, 1000, 1385]);
            return $pdf->output();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw new CertificateServiceException(
                "Failed to generate recognition certificate (v2).",
                $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }
    }

    /**
     * @throws CertificateServiceException
     */
    public function saveRecognitionCertificate($file, $certificate): array
    {
        try {
            DB::beginTransaction();

            $key = $this->minioService->fileNameConvert($certificate['employeeName'] . '.pdf', $certificate['id']);
            $result = $this->minioService->saveFile($key, $file, MinioBucket::CERTIFICATE);
            RecognitionCertificate::created($certificate);

            DB::commit();

            return $result;

        } catch (MinioException $e) {
            DB::rollBack();
            throw new CertificateServiceException(
                "Failed to save recognition certificate to MinIO.",
                $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw new CertificateServiceException(
                "Unexpected error while saving recognition certificate.",
                $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }
    }

    /**
     * @throws CertificateServiceException
     * @throws \Exception
     */
    public function searchCertificate($user, MinioBucket $type): array
    {
        try {
            $filename = $this->minioService->fileNameConvert($user['employeeName'] . ".pdf", $user['id']);
            log::info($filename);

            $metadata = RecognitionCertificate::where('recognition_id', $user['id'])->first();
            $url = $this->minioService->generateViewUrl($filename, $type);

            return [
                'url' => $url['url'],
                'expires' => $url['expires'],
                'metadata' => $metadata,
            ];
        } catch (MinioException $e) {
            log::info("CATCH BY MINIO EXCEPTION. THROW AS CertificateServiceException" . $e->getMessage());

            throw new CertificateServiceException(
                $e->getMessage(),
                $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }
    }
}
