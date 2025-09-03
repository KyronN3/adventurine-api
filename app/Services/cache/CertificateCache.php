<?php

namespace App\Services\cache;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CertificateCache
{
    protected string $prefix = 'cert-';

    /**
     * @throws \Exception
     */
    public function getRecognitionCert(string $id, callable $callback)
    {
        $cacheKey = $this->prefix . "recognition:" . $id;

        if (Cache::has($cacheKey)) {
            Log::info("Cache hit for $cacheKey");
            return Cache::get($cacheKey);
        }

        // Run callback → must return ['url' => ..., 'expiresAt' => DateTime]
        $result = $callback();


        if (!isset($result['url'], $result['expires'])) {
            throw new \Exception("Callback must return ['url' => string, 'expires' => UNIX timestamp]");
        }

        if (!is_string($result['url']) || !is_int($result['expires'])) {
            throw new \Exception("'url' must be string and 'expires' must be UNIX timestamp (int).");
        }

        $expiry = Carbon::createFromTimestamp($result['expires']);

        Cache::put($cacheKey, $result['url'], $expiry);
        return $result['url'];
    }


    public function forgetRecognitionCert(string $id): void
    {
        $cacheKey = $this->prefix . "recognition:" . $id;
        Cache::forget($cacheKey);
    }


    public function getEventCert(string $id, callable $callback): string
    {
        $cacheKey = $this->prefix . "event:" . $id;

        return Cache::remember($cacheKey, now()->addMinutes($this->ttlMinutes), function () use ($callback) {
            return $callback();
        });
    }


    public function forgetEventCert(string $id): void
    {
        $cacheKey = $this->prefix . "event:" . $id;
        Cache::forget($cacheKey);
    }
}
