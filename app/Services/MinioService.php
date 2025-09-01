<?php

namespace App\Services;

use App\Components\enum\MinioBucket;
use Aws\S3\S3Client;

class MinioService
{
    private function getS3Client(): S3Client
    {
        return new S3Client([
            'version' => 'latest',
            'region' => env('MINIO_REGION'),
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => env('MINIO_KEY'),
                'secret' => env('MINIO_SECRET'),
            ],
        ]);
    }

    public function generateUploadUrl(string $fileName, MinioBucket $bucket): string
    {
        $client = $this->getS3Client();
        $cmd = $client->getCommand('PutObject', [
            'Bucket' => $bucket->value,
            'Key' => $fileName,
        ]);
        $request = $client->createPresignedRequest($cmd, '+5 minutes');
        return (string)$request->getUri();
    }

    public function generateViewUrl(string $fileName, MinioBucket $bucket): array
    {
        $client = $this->getS3Client();

        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $contentType = match($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        };

        $cmd = $client->getCommand('GetObject', [
            'Bucket' => $bucket->value,
            'Key' => $fileName,
            'ResponseContentDisposition' => 'inline',
            'ResponseContentType' => $contentType, // important for browser preview
        ]);

        $expires = new \DateTime("+12 hours"); // actual expiration datetime
        $request = $client->createPresignedRequest($cmd, $expires);

        return [
            'url' => (string)$request->getUri(),
            'expires' => $expires->format('Y-m-d H:i:s'),];
    }

    public function fileNameConvert(string $fileName, int|string $id): string
    {
        $safeFileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', pathinfo($fileName, PATHINFO_FILENAME));
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        return $extension
            ? "{$safeFileName}_{$id}.{$extension}"
            : "{$safeFileName}_{$id}";
    }


}
