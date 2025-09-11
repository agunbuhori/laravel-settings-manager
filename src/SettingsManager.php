<?php

namespace Agunbuhori\SettingsManager;

use Agunbuhori\SettingsManager\Traits\HasCache;
use Agunbuhori\SettingsManager\Models\Setting;
use Agunbuhori\SettingsManager\SettingsBagManager;
use Agunbuhori\SettingsManager\Interfaces\SettingsManagerInterface;
use Illuminate\Cache\TaggedCache;

class SettingsManager implements SettingsManagerInterface
{
    use HasCache;
    private ?int $bag = null;
    private?string $group = null;
    private string $key = '';
    private string $arrayKey = '';
    private string $cacheKey = '';
    private array $cacheKeys = [];
    private TaggedCache $cache;

    public function __construct(private SettingsBagManager $bagManager)
    {
        $this->bag = $bagManager->getBag();
        $this->group = $bagManager->getGroup();
        
        $this->setCacheTags();
    }

    public function setBag(int $bag, ?string $group = null): void
    {
        $this->bagManager->setBag($bag, $group);

        $this->bag = $bag;
        $this->group = $group;

        $this->setCacheTags();
    }

    public function bag(int $bag, ?string $group = null): self
    {
        $this->bag = $bag;
        $this->group = $group;

        $this->setCacheTags();

        return $this;
    }

    public function general(): self
    {
        $this->bag = null;
        $this->group = null;

        $this->setCacheTags();

        return $this;
    }

    public function set(string $key, mixed $value): mixed
    {   
        $this->validateKey($key);

        if ($value === null) {
            Setting::where(['bag' => $this->bag, 'group' => $this->group, 'key' => $this->key])->delete();
            $this->setCache(null);
            return null;
        }

        $initialValue = $value;

        $setting = Setting::firstOrCreate(
            [
                'key' => $this->key,
                'bag' => $this->bag,
                'group' => $this->group,
            ],
            [
                'type' => $this->validatedType($value),
            ]
        );

        if (is_array($setting->value) || $this->arrayKey) {
            $data = $setting->value ?? [];
            $value = data_fill($data, $this->arrayKey, $value);
        }

        $setting->update(['value' => $value, 'type' => $this->validatedType($value)]);

        $this->setCache($initialValue);
    
        return $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->validateKey($key);

        if ($setting = $this->getCache()) {
            return $setting;
        }

        $setting = Setting::where(['bag' => $this->bag, 'group' => $this->group, 'key' => $this->key])->first();

        if (!$setting) return $default;

        $result = $setting->value;

        if ($this->arrayKey) {
            $result = data_get($result, $this->arrayKey, $default);
        }

        $this->setCache($result);

        return $result;
    }

    public function getMany(array $keys): array
    {
        $this->cacheKeys = collect($keys)->map(fn ($key) => $this->validateKey($key))->toArray();

        if ($settings = $this->getManyCache()) {
            return $settings;
        }

        $settings = Setting::whereIn('key', $keys)->where(['bag' => $this->bag, 'group' => $this->group])->get();

        $data = [];

        foreach ($settings as $setting) {
            $data[$setting->key] = $setting->value;
        }

        $this->setManyCache($data);

        return $data;
    }

    private function validateKey(string $key): string
    { 
        if (!preg_match('/^[a-zA-Z0-9\-\_.]+$/', $key)) {
            throw new \Exception('Key must contain only letters, numbers, ".", "-" and "_".'.' Given key: '.$key);
        }

        $this->key = $key;

        if (str_contains($key, '.')) {
            $this->key = explode('.', $this->key)[0];
            $this->arrayKey = str_replace("{$this->key}.", '', $key);
        }

        $this->cacheKey = "settings:{$key}";

        return $this->cacheKey;
    }

    private function validatedType(mixed $value): string
    {
        if ($this->arrayKey) return 'array';

        return str_replace('double', 'float', gettype($value));
    }
}
