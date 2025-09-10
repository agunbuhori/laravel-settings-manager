<?php

namespace Agunbuhori\SettingsManager;

class SettingsBagManager
{
    private ?int $bag = null;

    public function __construct(?int $bag = null)
    {
        $this->bag = $bag;
    }

    public function getBag()
    {
        return $this->bag;
    }

    public function setBag(int $bag)
    {
        $this->bag = $bag;
    }
}