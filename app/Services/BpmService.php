<?php

namespace App\Services;

use App\Models\Bpm;
use App\Exceptions\BpmServiceException;
use Illuminate\Database\Eloquent\Collection;

class BpmService
{
    /**
     * Get all BPM records
     *
     * @return Collection
     * @throws BpmServiceException
     */
    public function getAllBpms(): Collection
    {
        try {
            return Bpm::all();
        } catch (\Exception $e) {
            throw new BpmServiceException(
                "Failed to retrieve BPM records",
                "Database error: " . $e->getMessage()
            );
        }
    }

    /**
     * Create a new BPM record
     *
     * @param array $data
     * @return Bpm
     * @throws BpmServiceException
     */
    public function createNewBpm(array $data): Bpm
    {
        try {
            return Bpm::create($data);
        } catch (\Exception $e) {
            throw new BpmServiceException(
                "Failed to create new BPM record",
                "Database error: " . $e->getMessage()
            );
        }
    }

    /**
     * batch create bpm records
     *
     * @param array $data
     * @return array
     * @throws BpmServiceException
     */
    public function createMultipleBpms(array $data): array
    {
        try {
            $createdRecords = [];
            foreach ($data as $bpmData) {
                $createdRecords[] = Bpm::create($bpmData);
            }
            return $createdRecords;
        } catch (\Exception $e) {
            throw new BpmServiceException(
                "Failed to create multiple BPM records",
                "Database error: " . $e->getMessage()
            );
        }
    }

    /**
     * Update an existing BPM record
     *
     * @param BPM $bpm
     * @param array $data
     * @return BPM
     * @throws BpmServiceException
     */
    public function updateBpm(BPM $bpm, array $data): BPM
    {
        try {
            $bpm->update($data);
            return $bpm;
        } catch (\Exception $e) {
            throw new BpmServiceException(
                "Failed to update BPM record",
                "Database error: " . $e->getMessage()
            );
        }
    }
}