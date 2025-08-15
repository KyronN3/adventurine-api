<?php

namespace App\Services;

use App\Components\enum\LayerLevel;
use App\Components\enum\LogLevel;
use App\Components\enum\RecognitionFunction;
use App\Components\LogMessages;
use App\Exceptions\RecognitionServiceException;
use App\Models\Recognition;

class RecognitionService
{
    /**
     * @throws RecognitionServiceException
     */
    // Creates new recognition
    // Overrides status and date
    // Save to DB using Model ORM
    // Console logs after action
    // Returns recognition id if success, throw otherwise
    public function createNewRecognition(array $data): string
    {
        // Override status and date submitted
        $data['status'] = $data['status'] ?? 'pending';
        $data['date_submitted'] = $data['date_submitted'] ?? now();

        try {
            $recognition = Recognition::create($data);

            LogMessages::recognition(
                RecognitionFunction::CREATION,
                LayerLevel::SERVICE,
                LogLevel::INFO,
                $recognition);

            return (string)$recognition->id;
        } catch (\Exception $e) {
            LogMessages::recognition(
                RecognitionFunction::CREATION,
                LayerLevel::SERVICE,
                LogLevel::ERROR,
                $e);

            throw new RecognitionServiceException(
                "Error creating new recognition.",
                $e->getMessage(),
                $e->getCode(),
                $e);
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
    public function rejectPendingRecognition($id): void
    {
        $recognition = Recognition::find($id);

        if (!$recognition)
            throw new RecognitionServiceException("Recognition not found.");

        if ($recognition->isApproved() || $recognition->isRejected())
            throw new RecognitionServiceException("Recognition is already {$recognition->status} and cannot be rejected.");

        try {
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
