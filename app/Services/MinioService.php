<?php

namespace App\Services;

use App\Components\enum\MinioBucket;
use App\Exceptions\MinioException;
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Illuminate\Support\Facades\Storage;


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
     */
    public function generateViewUrl(string $fileName, MinioBucket $bucket): array
    {
        try {
            $client = $this->getS3Client();

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
                'ResponseContentDisposition' => 'inline',
                'ResponseContentType' => $contentType, // important for browser preview
            ]);

            $expires = new \DateTime($this->expires); // actual expiration datetime
            $request = $client->createPresignedRequest($cmd, $expires);

            return [
                'url' => (string)$request->getUri(),
                'expires' => $expires->format('Y-m-d H:i:s'),];
        } catch (\Exception $e) {
            throw new MinioException(
                'Failed to generate view file url.',
                $e->getMessage(),
                (int)$e->getCode(),
                $e);
        }
    }

    /**
     * @throws MinioException
     */
    public function deleteFile(string $fileName, MinioBucket $bucket): void
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
    public function deleteFileBatch(array $files, MinioBucket $bucket): void
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


    public function fileNameConvert(string $fileName, int|string $id): string
    {
        $safeFileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', pathinfo($fileName, PATHINFO_FILENAME));
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        return $extension
            ? "{$safeFileName}_{$id}.{$extension}"
            : "{$safeFileName}_{$id}";
    }
}
