<?php

namespace Agunbuhori\SettingsManager;

use Agunbuhori\SettingsManager\Traits\HasCache;
use Illuminate\Support\Arr;
use Agunbuhori\SettingsManager\Models\Setting;
use Agunbuhori\SettingsManager\SettingsBagManager;
use Agunbuhori\SettingsManager\Interfaces\SettingsManagerInterface;
use Illuminate\Cache\TaggedCache;

class SettingsManager implements SettingsManagerInterface
{
    use HasCache;

    private ?int $bag = null;
    private ?string $group = null;
    private string $key = '';
    private string $arrayKey = '';
    private string $cacheKey = '';
    private TaggedCache $cache;

    public function __construct(private SettingsBagManager $bagManager)
    {
        $this->bag = $bagManager->getBag();
        $this->group = $bagManager->getGroup();

        if ($this->group && !$this->bag) {
            throw new \Exception('Group must be provided when a bag is set.');
        }

        $this->setCacheTags();
    }

    public function setBag(int $bag, ?string $group = null): void
    {
        $this->bagManager->setBag($bag, $group);

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

        $setting = Setting::firstOrCreate(
            [
                'key' => $this->key,
                'bag' => $this->bag,
                'group' => $this->group,
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

        $setting = Setting::where('key', $this->key)->first();

        if (!$setting) return $default;

        $result = $setting->value;

        if ($this->arrayKey) {
            $result = Arr::get($result, $this->arrayKey, $default);
        }

        $this->setCache($result);

        return $result;
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
}
