<?php

namespace App\Http\Controllers;

use App\Components\enum\LayerLevel;
use App\Components\enum\LogLevel;
use App\Components\enum\MinioBucket;
use App\Components\enum\MinioFunction;
use App\Components\LogMessages;
use App\Components\ResponseFormat;
use App\Exceptions\MinioException;
use App\Http\Requests\DeleteBatchFileRequest;
use App\Services\MinioService;
use Illuminate\Http\JsonResponse;


class MinioController extends Controller
{
    protected MinioService $minioService;

    public function __construct(MinioService $minioService)
    {
        $this->minioService = $minioService;
    }

    /**
     * @throws \Exception
     */
    public function fetchByFileName($type, $fileName): JsonResponse
    {
        try {
            $fileType = $this->getFileType($type);
            $file = $this->minioService->generateViewUrl($fileName, $fileType);

            return ResponseFormat::success('File generated: ' . $fileName, $file);
        } catch (MinioException $e) {
            return ResponseFormat::error('Error retrieving file: ' . $e->getMessage());
        } catch (\Exception $e) {
            LogMessages::minio(MinioFunction::FETCH_BY_FILENAME, LogLevel::ERROR, LayerLevel::CONTROLLER, $e);
            return ResponseFormat::error('Error retrieving file: ', 500);
        }
    }

    public function fetchFileNameV2(string $route, string $type, string $filename): JsonResponse
    {
        try {
            $bucket = match ($route) {
                'recognition' => match ($type) {
                    'image' => MinioBucket::RECOGNITION_IMAGE,
                    'file' => MinioBucket::RECOGNITION_FILE,
                    default => throw new \InvalidArgumentException('Invalid type')
                },
                'event' => match ($type) {
                    'image' => MinioBucket::EVENT_IMAGE,
                    'file' => MinioBucket::EVENT_FILE,
                    default => throw new \InvalidArgumentException('Invalid type')
                },
                default => throw new \InvalidArgumentException('Invalid route')
            };

            $urlData = $this->minioService->generateViewUrl($filename, $bucket);

            return response()->json([
                'message' => 'File URL fetched successfully',
                'data' => $urlData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch file URL',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @throws \Exception
     */
    public function deleteByFileName($fileName, $type): JsonResponse
    {
        try {
            $fileType = $this->getFileType($type);
            $this->minioService->deleteFile($fileName, $fileType);

            return ResponseFormat::success('File deleted: ' . $fileName . ' successfully');
        } catch (MinioException $e) {
            return ResponseFormat::error($e->getMessage());
        } catch (\Exception $e) {
            LogMessages::minio(MinioFunction::DELETE_BY_FILENAME, LogLevel::ERROR, LayerLevel::CONTROLLER, $e);
            return ResponseFormat::error('Error deleting file: ' . $fileName, 500);
        }
    }

    public function deleteBatch(DeleteBatchFileRequest $files): JsonResponse
    {
        $fileType = $files->validated();
        try {
            $fileType = $this->getFileType($fileType['type']);
            $this->minioService->deleteFileBatch($fileType['files'], $fileType);

            return ResponseFormat::success('Batch file deleted successfully');
        } catch (MinioException $e) {
            return ResponseFormat::error($e->getMessage());
        } catch (\Exception $e) {
            LogMessages::minio(MinioFunction::DELETE_BATCH, LogLevel::ERROR, LayerLevel::CONTROLLER, $e);
            return ResponseFormat::error('Error deleting batch of files.', 500);
        }
    }


    /**
     * @throws MinioException
     */
    private function getFileType($type): MinioBucket
    {
        return match ($type) {
            'recognition-image' => MinioBucket::RECOGNITION_IMAGE,
            'recognition-file' => MinioBucket::RECOGNITION_FILE,
            'event-image' => MinioBucket::EVENT_IMAGE,
            'event-file' => MinioBucket::EVENT_FILE,
            'certificate' => MinioBucket::CERTIFICATE,
            default => throw new MinioException('Invalid file type'),
        };
    }

}
