<?php

namespace Agunbuhori\SettingsManager\Interfaces;

interface SettingsManagerInterface
{
    /**
     * Set a bag for the settings
     * 
     * @param int $bag
     * @return self
     */
    public function setBag(int $bag, ?string $group): void;

    /**
     * Set a bag for the settings
     * 
     * @param int $bag
     * @return self
     */
    public function bag(int $bag, ?string $group): self;

    /**
     * Get the general bag
     * 
     * @return self
     */
    public function general(): self;

    /**
     * Set a value for a key
     * 
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function set(string $key, mixed $value): mixed;
    
    /**
     * Set many values for a key
     * 
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function setMany(array $values): mixed;

    /**
     * Get a value for a key
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Get some settings
     * 
     * @param array $keys
     * @return array<string, mixed>
     */
    public function getMany(array $keys): array;

    /**
     * Clear cache
     * 
     * @return void
     */
    public function clearCache(): void;

    /**
     * Flush cache by bag
     * 
     * @param int|null $bag
     * @param string|null $group
     * @return void
     */
    public function flush(?int $bag = null, ?string $group = null): void;
}