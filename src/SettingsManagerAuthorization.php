<?php

namespace Agunbuhori\SettingsManager;

class SettingsManagerAuthorization
{
    public static bool $isAuthorized = false;

    public static function authorize(callable $callback)
    {
        if (is_callable($callback) && is_bool($callback())) {
            self::$isAuthorized = $callback();
        }
    }
}