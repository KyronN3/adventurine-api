<?php

namespace App\Services\cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BpmCache
{
    protected string $prefix = 'bpm-';
    protected int $ttlMinutes = 60; // Cache for 1 hour

    /**
     * Get all BPM records from cache or database
     *
     * @param callable $callback Function to fetch data from database
     * @return mixed
     */
    public function getAllBpms(callable $callback)
    {
        $cacheKey = $this->prefix . 'all-records';
        Log::info("BpmCache: Checking cache for key: $cacheKey");

        return Cache::remember($cacheKey, now()->addMinutes($this->ttlMinutes), function () use ($callback, $cacheKey) {
            Log::info("BpmCache: Cache miss for $cacheKey - fetching from database");
            $result = $callback();
            Log::info("BpmCache: Data fetched from database, caching result");
            return $result;
        });
    }

    /**
     * Get BPM records by office and date from cache or database
     *
     * @param string $office
     * @param string $date
     * @param callable $callback Function to fetch data from database
     * @return mixed
     */
    public function getBpmByOfficeAndDate(string $office, string $date, callable $callback)
    {
        $cacheKey = $this->prefix . "office:{$office}:date:{$date}";

        return Cache::remember($cacheKey, now()->addMinutes($this->ttlMinutes), function () use ($callback, $cacheKey) {
            Log::info("Cache miss for $cacheKey - fetching from database");
            return $callback();
        });
    }

    /**
     * Clear all BPM caches
     */
    public function clearAllCaches(): void
    {
        $cacheKeys = [
            $this->prefix . 'all-records',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
            Log::info("Cleared cache: $key");
        }

        // Also clear office/date specific caches (we'll use a pattern)
        $this->clearOfficeDateCaches();
    }

    /**
     * Clear office and date specific caches
     */
    public function clearOfficeDateCaches(): void
    {
        // Since we can't easily get all cache keys with a pattern in Laravel,
        // we'll clear them when they're accessed next time by setting a short TTL
        // For now, we'll rely on the TTL expiration i.e. delete based on timer
        Log::info("Office/date caches will expire naturally or be refreshed on next access");
    }

    /**
     * Clear specific office/date cache
     *
     * @param string $office
     * @param string $date
     */
    public function clearOfficeDateCache(string $office, string $date): void
    {
        $cacheKey = $this->prefix . "office:{$office}:date:{$date}";
        Cache::forget($cacheKey);
        Log::info("Cleared cache: $cacheKey");
    }
}