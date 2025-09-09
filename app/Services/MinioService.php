<?php

namespace App\Services;

use App\Components\enum\MinioBucket;
use App\Exceptions\MinioException;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Illuminate\Support\Facades\Log;


class MinioService
{
    protected string $expires = '+12 hours';

    private function getS3Client(): S3Client
    {
        return new S3Client([
            'version' => 'latest',
            'region' => env('MINIO_REGION'),
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'credentials' => new Credentials(env('MINIO_KEY'), env('MINIO_SECRET')),
        ]);
    }

    /**
     * @throws MinioException
     */
    public function generateUploadUrl(string $fileName, MinioBucket $bucket): string
    {
        try {
            $client = $this->getS3Client();
            $cmd = $client->getCommand('PutObject', [
                'Bucket' => $bucket->value,
                'Key' => $fileName,
            ]);
            $request = $client->createPresignedRequest($cmd, '+5 minutes');
            return (string)$request->getUri();
        } catch (\Exception $e) {
            throw new MinioException(
                'Failed to generate upload file url.',
                $e->getMessage(),
                (int)$e->getCode(),
                $e);
        }
    }

    /**
     * @throws MinioException
     */
    public function saveFile(string $fileName, $fileContents, MinioBucket $bucket): array
    {
        try {
            $this->getS3Client()->putObject([
                'Bucket' => $bucket->value,
                'Key' => $fileName,
                'Body' => $fileContents,
                'ContentType' => 'application/pdf',
            ]);

            // Generate presigned URL immediately after save
            $cmd = $this->getS3Client()->getCommand('GetObject', [
                'Bucket' => $bucket->value,
                'Key' => $fileName,
            ]);
            $expiresInMinutes = 60; // 1 hour
            $expiresAt = time() + ($expiresInMinutes * 60);

            $request = $this->getS3Client()->createPresignedRequest($cmd, $expiresAt);

            return [
                'url' => (string)$request->getUri(),
                'expires' => $expiresAt
            ];

        } catch (\Exception $e) {
            throw new MinioException(
                'Failed to save file.',
                $e->getMessage(),
                (int)$e->getCode(),
                $e);
        }
    }


    /**
     * @throws MinioException
     * @throws \Exception
     */
    public function generateViewUrl(string $fileName, MinioBucket $bucket): array
    {
        $client = $this->getS3Client();

        try {
            $client->getObject([
                'Bucket' => $bucket->value,
                'Key' => $fileName,
            ]);
        } catch (S3Exception $e) {
            throw new MinioException(
                "File not found in bucket [{$bucket->value}] with key [{$fileName}]",
                $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }

        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $contentType = match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        };

        $cmd = $client->getCommand('GetObject', [
            'Bucket' => $bucket->value,
            'Key' => $fileName,
            'ResponseContentDisposition' => "inline; filename=\"{$fileName}\"",
            'ResponseContentType' => $contentType, // important for browser preview
        ]);


        $expiresInMinutes = 60; // 1 hour
        $expiresAt = time() + ($expiresInMinutes * 60);
        $request = $client->createPresignedRequest($cmd, $expiresAt);

        log::info((string)$request->getUri());

        return [
            'url' => (string)$request->getUri(),
            'expires' => $expiresAt
        ];
    }


    /**
     * @throws MinioException
     */
    public
    function deleteFile(string $fileName, MinioBucket $bucket): void
    {
        try {
            $client = $this->getS3Client();

            $client->deleteObject([
                'Bucket' => $bucket->value,
                'Key' => $fileName,
            ]);
        } catch (\Exception $e) {
            throw new MinioException(
                'Failed to delete file.',
                $e->getMessage(),
                (int)$e->getCode(),
                $e);
        }
    }

    /**
     * @throws MinioException
     */
    public
    function deleteFileBatch(array $files, MinioBucket $bucket): void
    {
        try {
            $client = $this->getS3Client();

            $objects = array_map(fn($file) => ['Key' => $file], $files);

            $client->deleteObjects([
                'Bucket' => $bucket->value,
                'Delete' => [
                    'Objects' => $objects,
                    'Quiet' => true, // suppress verbose output
                ],
            ]);
        } catch (\Exception $e) {
            throw new MinioException(
                'Failed to delete file.',
                $e->getMessage(),
                (int)$e->getCode(),
                $e);
        }
    }


    public
    function fileNameConvert(string $fileName, int|string $id): string
    {
        $safeFileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', pathinfo($fileName, PATHINFO_FILENAME));
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        return $extension
            ? "{$safeFileName}_{$id}.{$extension}"
            : "{$safeFileName}_{$id}";
    }
}
