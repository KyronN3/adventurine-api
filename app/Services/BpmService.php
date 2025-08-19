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
}