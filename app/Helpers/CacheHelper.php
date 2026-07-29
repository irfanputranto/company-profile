<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redis; // Tambahkan ini

class CacheHelper
{
    /**
     * @var string
     */
    protected $cacheDriver;

    public function __construct()
    {
        $this->cacheDriver = config('cache.default');

        if ($this->cacheDriver === 'redis' && !$this->isRedisAvailable()) {
            Log::warning('Redis tidak tersedia, fallback ke driver default: ' . config('cache.default'));
            $this->cacheDriver = config('cache.default');
        }
    }

    /**
     * Cek apakah Redis tersedia
     */
    protected function isRedisAvailable(): bool
    {
        try {
            // Cek koneksi Redis menggunakan Redis facade
            Redis::connection()->ping();
            return true;
        } catch (\Exception $e) {
            Log::warning('Redis tidak tersedia: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ambil data dari cache
     */
    public function get(string $key, $default = null)
    {
        return $this->cacheDriver === 'redis'
            ? Cache::store('redis')->get($key, $default)
            : Session::get($key, $default);
    }

    /**
     * Simpan data ke cache
     */
    public function set(string $key, $value, ?int $ttl = null)
    {
        if ($this->cacheDriver === 'redis') {
            if ($ttl) {
                Cache::store('redis')->put($key, $value, $ttl);
            } else {
                Cache::store('redis')->forever($key, $value);
            }
        } else {
            Session::put($key, $value);
        }
    }

    /**
     * Hapus data dari cache
     */
    public function forget(string $key)
    {
        if ($this->cacheDriver === 'redis') {
            Cache::store('redis')->forget($key);
        } else {
            Session::forget($key);
        }
    }

    /**
     * Cek apakah data ada di cache
     */
    public function has(string $key): bool
    {
        return $this->cacheDriver === 'redis'
            ? Cache::store('redis')->has($key)
            : Session::has($key);
    }

    /**
     * Dapatkan driver cache yang sedang digunakan
     */
    public function getDriver(): string
    {
        return $this->cacheDriver;
    }

    public function tags(array $tags)
    {
        if ($this->cacheDriver === 'redis') {
            return Cache::store('redis')->tags($tags);
        }

        return Cache::store($this->cacheDriver);
    }
}
