<?php

namespace App\Http\Controllers;

use App\Components\enum\CertificateType;
use App\Components\enum\MinioBucket;
use App\Components\ResponseFormat;
use App\Exceptions\CertificateServiceException;
use App\Exceptions\RecognitionServiceException;
use App\Http\Requests\CertificateRecognitionRequest;
use App\Services\cache\CertificateCache;
use App\Services\certificate\CertificateService;
use App\Services\recognition\RecognitionReadService;
use Illuminate\Support\Facades\Log;

class CertificateController extends Controller
{
    protected CertificateService $certificateService;
    protected CertificateCache $certificateCache;

    protected RecognitionReadService $recognitionReadService;

    public function __construct(CertificateService     $certificateService,
                                CertificateCache       $certificateCache,
                                RecognitionReadService $recognitionReadService)
    {
        $this->certificateService = $certificateService;
        $this->certificateCache = $certificateCache;
        $this->recognitionReadService = $recognitionReadService;
    }


    // Returns PDF
    public function generateRecognitionCertificate(CertificateRecognitionRequest $request)
    {
        $data = $request->validated();

        try {
            // Get PDF binary content (string)
            $pdfUrl = $this->certificateCache->getRecognitionCert($data['recognition_id'], function () use ($data) {
                $pdf = $this->certificateService->generateCertificate($data['name'], $data['description'], $data['date'], CertificateType::RECOGNITION);
                return $this->certificateService->saveCertificate($pdf, $data['name'], $data['recognition_id']);
            });

            $filename = "$data[name]-certificate.pdf";

            return response(file_get_contents($pdfUrl), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "inline; filename=\"$filename\"",
            ]);

        } catch (\Exception $e) {
            return response($e->getMessage(), 400);
        }
    }

    /**
     * @throws RecognitionServiceException
     */
    public function generateRecognitionCertificateById($id)
    {
        try {
            $data = $this->recognitionReadService->getRecognitionById($id)[0];

            Log::info($data);

            // Get PDF binary content (string)
            $pdfUrl = $this->certificateCache->getRecognitionCert($data['id'], function () use ($data) {
                try {
                    return $this->certificateService->searchCertificate($data, MinioBucket::CERTIFICATE);
                } catch (CertificateServiceException $e) {
                    Log::info($e->getMessage() . "Prepare to generate new certificate");
                    $pdf = $this->certificateService->generateCertificate($data['employeeName'], $data['achievementDescription'], $data['dateSubmitted'], CertificateType::RECOGNITION);
                    return $this->certificateService->saveCertificate($pdf, $data['employeeName'], $data['id']);
                }
            });

            $filename = "$data[employeeName]-certificate.pdf";

            /* double bracket for a proper return type as an array, example: data : [] */
            return ResponseFormat::success('Certificate generated successfully', [[
                'filename' => $filename,
                'url' => $pdfUrl
            ]]);

        } catch (\Exception $e) {
            return response($e->getMessage(), 400);
        }
    }


}
