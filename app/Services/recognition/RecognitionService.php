<?php

namespace App\Services\recognition;

use App\Components\enum\LayerLevel;
use App\Components\enum\LogLevel;
use App\Components\enum\MinioBucket;
use App\Components\enum\RecognitionFunction;
use App\Components\LogMessages;
use App\Exceptions\RecognitionServiceException;
use App\Models\recognition\Recognition;
use App\Services\MinioService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class RecognitionService
{
    protected MinioService $minioService;

    public function __construct(MinioService $minioService)
    {
        $this->minioService = $minioService;
    }

    /**
     * @throws RecognitionServiceException
     */
//     Creates new recognition. Use Transaction for Atomicity
//     Foreach loops for images and files to generate key and save to db
//     Generate all with presign upload url
//     Console logs after action
//     Returns with id and presign urls if success, throw error if fail
    public function createNewRecognition(array $data): array
    {
        // Override status and date submitted
        $data['status'] = $data['status'] ?? 'pending';
        $data['date_submitted'] = $data['date_submitted'] ?? now();

        DB::beginTransaction();

        try {
            $recognition = Recognition::create($data);

            $imageKeys = [];
            $fileKeys = [];

            if (!empty($data['images'])) {
                foreach ($data['images'] as $imageName) {
                    $key = $this->minioService->fileNameConvert($imageName, $recognition->id);
                    $recognition->images()->create(['original_name' => $imageName, 'image_name' => $key]);
                    $imageKeys[] = $key;
                }
            }

            if (!empty($data['files'])) {
                foreach ($data['files'] as $fileName) {
                    $key = $this->minioService->fileNameConvert($fileName, $recognition->id);
                    $recognition->files()->create(['original_name' => $fileName, 'file_name' => $key]);
                    $fileKeys[] = $key;
                }
            }

            // generate presign urls
            $imageUrls = [];
            $fileUrls = [];

            foreach ($imageKeys as $key) {
                $imageUrls[] = $this->minioService->generateUploadUrl($key, MinioBucket::RECOGNITION_IMAGE);
            }

            foreach ($fileKeys as $key) {
                $fileUrls[] = $this->minioService->generateUploadUrl($key, MinioBucket::RECOGNITION_FILE);
            }

            // Commit DB transactions
            DB::commit();

            LogMessages::recognition(
                RecognitionFunction::CREATION,
                LayerLevel::SERVICE,
                LogLevel::INFO,
                $recognition);

            return [
                'recognitionId' => $recognition->id,
                'images' => $imageUrls,
                'files' => $fileUrls,
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            LogMessages::recognition(
                RecognitionFunction::CREATION,
                LayerLevel::SERVICE,
                LogLevel::ERROR,
                $e);

            throw new RecognitionServiceException(
                "Error creating new recognition.",
                $e->getMessage());
        }
    }




    /**
     * @throws RecognitionServiceException
     */
    // Delete pending recognition
    // Check recognition if currently pending
    // Console logs after action
    // Delete it, return error otherwise not
    public function deletePendingRecognition($id): void
    {
        $recognition = Recognition::find($id);

        if (!$recognition)
            throw new RecognitionServiceException("Recognition not found.");

        if (!$recognition->isPending())
            throw new RecognitionServiceException("Recognition is already {$recognition->status} and cannot be deleted.");

        try {
            $recognition->delete();

            LogMessages::recognition(
                RecognitionFunction::DELETE_PENDING,
                LayerLevel::SERVICE,
                LogLevel::INFO,
                $recognition);
        } catch (\Exception $e) {
            LogMessages::recognition(
                RecognitionFunction::DELETE_PENDING,
                LayerLevel::SERVICE,
                LogLevel::ERROR,
                $e);

            throw new RecognitionServiceException(
                "Error deleting pending recognition.",
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @throws RecognitionServiceException
     */
    // Approve pending recognition
    // Check recognition if currently pending
    // Console logs after action
    // Update to approve, return error otherwise not
    public function approvePendingRecognition($id): void
    {
        $recognition = Recognition::find($id);

        if ($recognition === null)
            throw new RecognitionServiceException("Recognition not found.");

        if (!$recognition->isPending())
            throw new RecognitionServiceException("Recognition is already {$recognition->status} and cannot be approved.");

        try {
            $recognition->toApproved();
            $recognition->save();

            LogMessages::recognition(
                RecognitionFunction::APPROVES,
                LayerLevel::SERVICE,
                LogLevel::INFO,
                $recognition);
        } catch (\Exception $e) {
            LogMessages::recognition(
                RecognitionFunction::APPROVES,
                LayerLevel::SERVICE,
                LogLevel::ERROR,
                $e);

            throw new RecognitionServiceException(
                "Error approving recognition.",
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @throws RecognitionServiceException
     */
    // Approve pending recognition
    // Check recognition if currently pending
    // Console logs after action
    // Update to approve, return error otherwise not
    public function rejectPendingRecognition($id, $hrComment = ''): void
    {
        $recognition = Recognition::find($id);

        if (!$recognition)
            throw new RecognitionServiceException("Recognition not found.");

        if ($recognition->isApproved() || $recognition->isRejected())
            throw new RecognitionServiceException("Recognition is already {$recognition->status} and cannot be rejected.");

        try {
            $recognition->hr_comment = $hrComment;
            $recognition->toRejected();
            $recognition->save();

            LogMessages::recognition(
                RecognitionFunction::REJECTS,
                LayerLevel::SERVICE,
                LogLevel::INFO,
                $recognition);
        } catch (\Exception $e) {
            LogMessages::recognition(
                RecognitionFunction::REJECTS,
                LayerLevel::SERVICE,
                LogLevel::ERROR,
                $e);

            throw new RecognitionServiceException(
                "Error rejecting recognition.",
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}
