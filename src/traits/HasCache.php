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
        if (!config('settings-manager.enable_cache')) return;

        if ($value === null) {
            $this->cache->forget($this->cacheKey);
            return;
        }

        $this->cache->set($this->cacheKey, serialize($value), config('settings-manager.cache_expiration', 86400));
    }

    public function getCache(): mixed
    {
        if (!config('settings-manager.enable_cache')) return null;

        return unserialize($this->cache->get($this->cacheKey));
    }

    public function getManyCache(): array
    {
        if (!config('settings-manager.enable_cache')) return [];

        return collect($this->cache->many($this->cacheKeys))
                ->filter(fn ($value) => $value !== null)
                ->mapWithKeys(fn ($value, $key) => [str_replace('settings:', '', $key) => unserialize($value)])
                ->toArray();
    }

    public function setManyCache(array $values): void
    {
        if (!config('settings-manager.enable_cache')) return;

        $this->cache->putMany(collect($values)
            ->mapWithKeys(fn ($value, $key) => ["settings:{$key}" => serialize($value)])
            ->toArray(), config('settings-manager.cache_expiration', 86400)
        );
    }

    public function clearCache(): void
    {
        if (!config('settings-manager.enable_cache')) return;

        $this->cache->flush();
    }
}