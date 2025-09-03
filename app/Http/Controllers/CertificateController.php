<?php

namespace App\Http\Controllers;

use App\Components\enum\CertificateType;
use App\Http\Requests\CertificateRecognitionRequest;
use App\Services\cache\CertificateCache;
use App\Services\CertificateService;

class CertificateController extends Controller
{
    protected CertificateService $certificateService;
    protected CertificateCache $certificateCache;

    public function __construct(CertificateService $certificateService, CertificateCache $certificateCache)
    {
        $this->certificateService = $certificateService;
        $this->certificateCache = $certificateCache;
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
}
