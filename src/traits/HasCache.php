<?php

namespace Agunbuhori\SettingsManager\Traits;

use Illuminate\Cache\TaggedCache;
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

    public function setCache(mixed $value): void
    {
        if (!config('settings-manager.enable_cache') || $value === null) return;

        $this->cache->set($this->cacheKey, $value, 86400);
    }

    public function getCache(): mixed
    {
        if (!config('settings-manager.enable_cache')) return null;

        return $this->cache->get($this->cacheKey);
    }

    public function clearCache(): void
    {
        if (!config('settings-manager.enable_cache')) return;

        $this->cache->flush();
    }
}