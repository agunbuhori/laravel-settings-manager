<?php

namespace Agunbuhori\SettingsManager;

use DB;
use Illuminate\Support\Arr;
use Agunbuhori\SettingsManager\Models\Setting;
use Agunbuhori\SettingsManager\SettingsBagManager;
use Agunbuhori\SettingsManager\Interfaces\SettingsManagerInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Cache\TaggedCache;

class SettingsManager implements SettingsManagerInterface
{
    private ?int $bag = null;
    private string $key = '';
    private string $arrayKey = '';
    private string $cacheKey = '';
    private TaggedCache $cache;

    private static const CACHE_KEY = 'settings-manager';

    public function __construct(private SettingsBagManager $bagManager)
    {
        $this->bag = $bagManager->getBag();

        $this->cache = $this->bag 
            ? Cache::tags([self::CACHE_KEY, $this->bag]) 
            : Cache::tags(self::CACHE_KEY);
    }

    public function setBag(int $bag): void
    {
        $this->bagManager->setBag($bag);
    }

    public function bag(int $bag): self
    {
        $this->bag = $bag;
        $this->cache = Cache::tags([self::CACHE_KEY, $bag]);

        return $this;
    }

    public function general(): self
    {
        $this->bag = null;
        $this->cache = Cache::tags(self::CACHE_KEY);

        return $this;
    }

    public function set(string $key, mixed $value): mixed
    {   
        $this->validateKey($key);

        $setting = Setting::firstOrCreate(
            [
                'key' => $this->key,
                'bag' => $this->bag
            ],
            [
                'type' => str_replace('double', 'float', gettype($value)),
                'value' => "[]"
            ]
        );

        if ($setting->type == 'array') {
            $data = $setting->value ?? [];
            $newSetting = Arr::set($data, $this->arrayKey, $value);
            $setting->update(['value' => $newSetting]);
        } else {
            $setting->update(['value' => $value]);
        }

        $this->setCache($value);
    
        return $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->validateKey($key);

        if ($setting = $this->getCache()) {
            return $setting;
        }

        $setting = Setting::where('key', $this->key)->where('bag', $this->bag)->first();

        if (!$setting) return $default;

        $result = $setting->value;

        if ($this->arrayKey) {
            $result = Arr::get($result, $this->arrayKey, $default);
        }

        $this->setCache($result);

        return $result;
    }

    private function parseSetting(array $setting): mixed
    {
        return null;
    }

    private function validateKey(string $key): void
    {
        if (!preg_match('/^[a-zA-Z0-9\-\_.]+$/', $key)) {
            throw new \Exception('Key must contain only letters, numbers, ".", "-" and "_"');
        }

        $this->key = $key;

        if (str_contains($key, '.')) {
            $this->key = explode('.', $this->key)[0];
            $this->arrayKey = str_replace("{$this->key}.", '', $key);
        }

        $this->cacheKey = "settings:{$this->bag}:{$key}";
    }

    private function setCache(mixed $value): void
    {
        if (!config('settings-manager.enable_cache') || $value === null) return;

        $this->cache->set($this->cacheKey, $value, 86400);
    }

    private function getCache(): mixed
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
