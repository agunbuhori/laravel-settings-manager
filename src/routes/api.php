<?php

use Agunbuhori\SettingsManager\Controllers\SettingController;

Route::get('/settings', [SettingController::class, 'index']);
Route::get('/settings/{key}', [SettingController::class, 'show']);
Route::match(['put', 'patch', 'post'], '/settings/{key}', [SettingController::class, 'update']);