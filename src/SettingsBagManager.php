<?php

namespace Agunbuhori\SettingsManager;

class SettingsBagManager
{
    private ?int $bag = null;
    private ?string $group = null;

    public function __construct(?int $bag = null, ?string $group = null)
    {
        $this->bag = $bag;
        $this->group = $group;
    }

    public function getBag()
    {
        return $this->bag;
    }

    public function setBag(?int $bag = null, ?string $group = null): void
    {
        $this->bag = $bag;
        $this->group = $group;
    }

    public function getGroup()
    {
        return $this->group;
    }
}