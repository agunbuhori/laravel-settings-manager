<?php

use Agunbuhori\SettingsManager\Controllers\SettingController;
use Agunbuhori\SettingsManager\Middlewares\SettingsManagerMiddleware;

Route::middleware(SettingsManagerMiddleware::class)->group(function () {
    Route::get('/settings', [SettingController::class, 'index']);
    Route::get('/settings/{key}', [SettingController::class, 'show']);
    Route::match(['put', 'patch', 'post'], '/settings/{key}', [SettingController::class, 'update']);
});
