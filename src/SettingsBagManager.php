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

    public function setBag(int $bag)
    {
        $this->bag = $bag;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setGroup(?string $group)
    {
        $this->group = $group;
    }
}