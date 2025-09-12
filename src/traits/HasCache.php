<?php

namespace Agunbuhori\SettingsManager\Traits;

use Illuminate\Support\Facades\Cache;

trait HasCache
{
    public function setCacheTags()
    {
        $this->cache = Cache::tags(
            collect(['settings-manager', $this->bag, $this->group])
                    ->filter(fn ($item) => $item !== null)
                    ->toArray()
        ); 
    }

    public function setCache(mixed $value, string $key = null): void
    {
        if (!config('settings-manager.enable_cache')) return;

        if ($value === null) {
            $this->cache->forget("settings:$key" ?? $this->cacheKey);
            return;
        }

        $this->cache->set($key ? "settings:$key" : $this->cacheKey, serialize($value), config('settings-manager.cache_expiration', 86400));
    }

    public function getCache(string $key = null): mixed
    {
        if (!config('settings-manager.enable_cache')) return null;

        return unserialize($this->cache->get($key ? "settings:$key" : $this->cacheKey));
    }

    public function clearCache(): void
    {
        if (!config('settings-manager.enable_cache')) return;

        $this->cache->flush();
    }
}