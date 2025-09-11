<?php

namespace App\Services\recognition;

use App\Components\enum\LayerLevel;
use App\Components\enum\LogLevel;
use App\Components\enum\MinioBucket;
use App\Components\enum\RecognitionFunction;
use App\Components\LogMessages;
use App\Exceptions\RecognitionServiceException;
use App\Models\Recognition;
use App\Services\MinioService;
use App\Services\ResponseData;
use Illuminate\Support\Collection;


class RecognitionReadService
{
    protected MinioService $minioService;

    public function __construct(MinioService $minioService)
    {
        $this->minioService = $minioService;
    }

    /**
     * @throws RecognitionServiceException
     */
    private function fetchRecognitions(array $filters = [], RecognitionFunction $function = RecognitionFunction::SEARCH_ALL): Collection
    {
        try {
            LogMessages::recognition($function, LayerLevel::SERVICE, LogLevel::INFO);

            $query = Recognition::with(['images', 'files']);

            foreach ($filters as $key => $value) {
                if (is_array($value)) {
                    $query->whereIn($key, $value);
                } else {
                    $query->where($key, $value);
                }
            }
            return $query->get();

        } catch (\Exception $e) {
            throw new RecognitionServiceException(
                "Error fetching recognitions.",
                $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }
    }

    private function filesToUrl($recognitions): array
    {
        $images = [];
        $files = [];

        foreach ($recognitions as $re) {
            $images[$re->id] = [];
            $files[$re->id] = [];

            foreach ($re->images as $image) {
                try {
                    $minio = $this->minioService->generateViewUrl($image->image_name, MinioBucket::RECOGNITION_IMAGE);
                    $images[$re->id][] = [
                        'id' => $image->id,
                        'name' => $image->image_name,
                        'url' => $minio['url'],
                        'expires' => $minio['expires']
                    ];
                } catch (\Exception $e) {
                    // skip file silently
                }
            }
            foreach ($re->files as $file) {
                try {
                    $minio = $this->minioService->generateViewUrl($file->file_name, MinioBucket::RECOGNITION_FILE);
                    $files[$re->id][] = [
                        'id' => $file->id,
                        'name' => $file->file_name,
                        'url' => $minio['url'],
                        'expires' => $minio['expires']
                    ];
                } catch (\Exception $e) {
                    // skip file silently
                }
            }
        }

        return [
            'images' => $images,
            'files' => $files,
        ];
    }

    /**
     * @throws RecognitionServiceException
     */
    public function getRecognitions(): array
    {
        $recognitions = $this->fetchRecognitions();
        $preloadedUrls = $this->filesToUrl($recognitions);

        return $recognitions->map(fn($rec) => ResponseData::recognition(
            $rec,
            $preloadedUrls['images'][$rec->id] ?? [],
            $preloadedUrls['files'][$rec->id] ?? []
        ))->toArray();
    }

    /**
     * @throws RecognitionServiceException
     */
    public function getRecognitionById($id): array
    {
        $recognitions = $this->fetchRecognitions(['id' => $id], RecognitionFunction::SEARCH_BY_ID);
        $preloadedUrls = $this->filesToUrl($recognitions);

        return $recognitions->map(fn($rec) => ResponseData::recognition(
            $rec,
            $preloadedUrls['images'][$rec->id] ?? [],
            $preloadedUrls['files'][$rec->id] ?? []
        ))->toArray();
    }

    /**
     * @throws RecognitionServiceException
     */
    public function getRecognitionByDepartment($department): array
    {
        if($department == null) {
            throw new RecognitionServiceException("Department is empty or null.");
        }

        $recognitions = $this->fetchRecognitions(['employee_department' => $department], RecognitionFunction::SEARCH_BY_DEPARTMENT);
        $preloadedUrls = $this->filesToUrl($recognitions);

        return $recognitions->map(fn($rec) => ResponseData::recognition(
            $rec,
            $preloadedUrls['images'][$rec->id] ?? [],
            $preloadedUrls['files'][$rec->id] ?? []
        ))->toArray();
    }

    /**
     * @throws RecognitionServiceException
     */
    public function getRecognitionHistory(): array
    {
        $recognitions = $this->fetchRecognitions(['status' => ['approved', 'rejected']], RecognitionFunction::SEARCH_HISTORY);
        $preloadedUrls = $this->filesToUrl($recognitions);

        return $recognitions->map(fn($rec) => ResponseData::recognition(
            $rec,
            $preloadedUrls['images'][$rec->id] ?? [],
            $preloadedUrls['files'][$rec->id] ?? []
        ))->toArray();
    }
}
