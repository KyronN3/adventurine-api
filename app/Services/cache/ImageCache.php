<?php

namespace App\Services\cache;

use Illuminate\Support\Facades\Cache;

class ImageCache
{
    protected string $prefix = 'image:';
    protected int $ttlMinutes;

    public function __construct(int $ttlMinutes = 10)
    {
        $this->ttlMinutes = $ttlMinutes;
    }

    public function getPresignUrl(string $key, callable $callback ): string
    {
        $cacheKey = $this->prefix . $key;

        return Cache::remember($cacheKey, now()->addMinutes($this->ttlMinutes),function () use ($callback) {
            return $callback();
        });
    }

    public function forgetPresignedUrl(string $key): void
    {
        $cacheKey = $this->prefix . $key;
        Cache::forget($cacheKey);
    }
}
