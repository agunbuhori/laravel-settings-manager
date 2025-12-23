<?php

use Agunbuhori\SettingsManager\Controllers\SettingController;
use Agunbuhori\SettingsManager\Middlewares\SettingsManagerMiddleware;

Route::middleware(SettingsManagerMiddleware::class)->group(function () {
    Route::get('/settings', [SettingController::class, 'index']);
    Route::get('/settings/{key}', [SettingController::class, 'show']);
    Route::match(['put', 'patch', 'post'], '/settings/{key}', [SettingController::class, 'update']);
    Route::delete('/settings/{key}', [SettingController::class, 'destroy']);
    Route::delete('/settings-cache/clear', [SettingController::class, 'clearCache']);
});
