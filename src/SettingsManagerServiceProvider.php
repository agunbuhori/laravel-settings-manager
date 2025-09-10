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

        $datetime = date('Y_m_d_His', time());

        $this->publishes([
            __DIR__ . '/config/settings-manager.php' => config_path('settings-manager.php'),
            __DIR__ . '/database/migrations/2025_09_08_040901_create_settings_table.php' => database_path("migrations/{$datetime}_create_settings_table.php"),
        ], 'settings-manager');
    }
}