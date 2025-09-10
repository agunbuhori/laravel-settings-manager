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

        $this->mergeConfigFrom(__DIR__ . '/config/settings-manager.php','settings-manager');
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');

        $this->publishes([
            __DIR__ . '/config/settings-manager.php' => config_path('settings-manager.php'),
        ], 'settings-manager');
    }
}