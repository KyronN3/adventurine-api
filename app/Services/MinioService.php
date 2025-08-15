<?php

namespace App\Services;

use App\Components\enum\MinioBucket;
use Illuminate\Support\Facades\Storage;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;


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


    public function fileNameConvert(string $fileName, int|string $id): string
    {
        $safeFileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', pathinfo($fileName, PATHINFO_FILENAME));
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        return $extension
            ? "{$safeFileName}_{$id}.{$extension}"
            : "{$safeFileName}_{$id}";
    }


}
