<?php

use Agunbuhori\SettingsManager\Interfaces\SettingsManagerInterface;

if (!function_exists('settings')) {
    function settings()
    {
        return app(SettingsManagerInterface::class);
    }
}