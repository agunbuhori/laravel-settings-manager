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
    public function setBag(int $bag): void;

    /**
     * Set a bag for the settings
     * 
     * @param int $bag
     * @return self
     */
    public function bag(int $bag): self;

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
     * Get a value for a key
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed;
}