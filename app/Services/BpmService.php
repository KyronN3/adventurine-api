<?php

namespace App\Services;

use App\Exceptions\BpmServiceException;
use App\Models\Bpm;
use App\Models\Relations\CustomORM;
use App\Services\cache\BpmCache;
use Illuminate\Database\Eloquent\Collection;

class BpmService
{
    protected BpmCache $cache;

    public function __construct(BpmCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Get all BPM records with employee details from vwActive
     *
     * @return Collection
     * @throws BpmServiceException
     */
    public function getAllBpms(): Collection
    {
        try {
            \Illuminate\Support\Facades\Log::info('BpmService: Starting getAllBpms');
            $result = $this->cache->getAllBpms(function () {
                \Illuminate\Support\Facades\Log::info('BpmService: Executing BPM query');

                $orm = new CustomORM(new Bpm, 'vwActive');
                $query = $orm->leftJoinCustomColumn(['ldrBpm.*',
                    'vwActive.Name4 as employee_name',
                    'vwActive.Sex',
                    'vwActive.Office',
                    'vwActive.Designation',
                    'vwActive.Status'],
                    'control_no',
                    'ControlNo')->distinct();
                \Illuminate\Support\Facades\Log::info('BpmService: Query built, executing get()');
                $records = $query->get();
                \Illuminate\Support\Facades\Log::info('BpmService: Query executed, records count: ' . $records->count());
                return $records;
            });
            \Illuminate\Support\Facades\Log::info('BpmService: getAllBpms completed successfully');
            return $result;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('BpmService: Exception in getAllBpms: ' . $e->getMessage());
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
            $bpm = Bpm::create($data);
            // Clear cache when new record is added
            $this->cache->clearAllCaches();
            return $bpm;
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
            // Clear cache when new records are added
            $this->cache->clearAllCaches();
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
