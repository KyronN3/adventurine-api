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
use Illuminate\Support\Facades\Log;

class RecognitionReadServiceV2 implements IRecognitionReadService
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


    /**
     * @throws RecognitionServiceException
     */
    public function getRecognitions(): array
    {
        $recognitions = $this->fetchRecognitions();

        return $recognitions->map(fn($rec) => ResponseData::recognition2($rec))->toArray();
    }

    /**
     * @throws RecognitionServiceException
     */
    public function getRecognitionById($id): array
    {
        $recognitions = $this->fetchRecognitions(['id' => $id], RecognitionFunction::SEARCH_BY_ID);

        return $recognitions->map(fn($rec) => ResponseData::recognition2($rec))->toArray();
    }

    /**
     * @throws RecognitionServiceException
     */
    public function getRecognitionByDepartment($department): array
    {
        if ($department == null) {
            throw new RecognitionServiceException("Department is empty or null.");
        }

        $recognitions = $this->fetchRecognitions(['employee_department' => $department], RecognitionFunction::SEARCH_BY_DEPARTMENT);

        return $recognitions->map(fn($rec) => ResponseData::recognition2($rec))->toArray();
    }

    /**
     * @throws RecognitionServiceException
     */
    public function getRecognitionHistory(): array
    {
        $recognitions = $this->fetchRecognitions(['status' => ['approved', 'rejected']], RecognitionFunction::SEARCH_HISTORY);

        return $recognitions->map(fn($rec) => ResponseData::recognition2($rec))->toArray();
    }


    /**
     * @throws RecognitionServiceException
     */
    public function getRecognitionMediaById($id): array
    {
        $recognition = $this->fetchRecognitions(['id' => $id], RecognitionFunction::SEARCH_BY_ID);
        $media = $this->filesToUrl($recognition);

        return $recognition->map(fn($rec) => ResponseData::recognitionMedia(
            $rec->id,
            $media['expires'],
            $media['images'][$rec->id] ?? [],
            $media['files'][$rec->id] ?? []
        ))->toArray();
    }

    private function filesToUrl($recognitions): array
    {
        $images = [];
        $files = [];
        $globalExpires = null;

        Log::info("Attempting to generate media urls.");


        foreach ($recognitions as $re) {
            $images[$re->id] = [];
            $files[$re->id] = [];

            Log::info("Generating media urls for $re->id");

            foreach ($re->images as $image) {
                try {
                    $minio = $this->minioService->generateViewUrl($image->image_name, MinioBucket::RECOGNITION_IMAGE);
                    $images[$re->id][] = [
                        'originalName' => $image->original_name,
                        'fileName' => $image->image_name,
                        'url' => $minio['url'],
                        'expires' => $minio['expires']
                    ];
                    $globalExpires ??= $minio['expires']; // set once, reuse

                } catch (\Exception $e) {
                    // skip file silently
                    Log::warning("Failed to generate image url for $image->id" . $e->getMessage());

                }
            }
            foreach ($re->files as $file) {
                try {
                    $minio = $this->minioService->generateViewUrl($file->file_name, MinioBucket::RECOGNITION_FILE);
                    $files[$re->id][] = [
                        'originalName' => $file->original_name,
                        'fileName' => $file->file_name,
                        'url' => $minio['url'],
                        'expires' => $minio['expires']
                    ];
                    $globalExpires ??= $minio['expires']; // set once, reuse

                } catch (\Exception $e) {
                    // skip file silently
                    Log::warning("Failed to generate file url for $file->id" . $e->getMessage());
                }
            }
        }

        LogMessages::recognition(RecognitionFunction::SEARCH_MEDIA, LayerLevel::SERVICE, LogLevel::INFO);
        Log::info("Successfully generated media urls.");

        return [
            'images' => $images,
            'files' => $files,
            'expires' => $globalExpires
        ];
    }

}
