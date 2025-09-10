<?php

namespace Agunbuhori\SettingsManager;

use Agunbuhori\SettingsManager\Interfaces\SettingsManagerInterface;
use Illuminate\Support\ServiceProvider;

class SettingsManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(SettingsBagManager::class, fn () => new SettingsBagManager(null));
        $this->app->bind(SettingsManagerInterface::class, SettingsManager::class);
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
    }
}