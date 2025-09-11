<?php

namespace Agunbuhori\SettingsManager\Middlewares;

use Agunbuhori\SettingsManager\SettingsManagerAuthorization;

class SettingsManagerMiddleware
{
    public function handle($request, \Closure $next)
    {
        if (!config('settings-manager.enable_api')) {
            return response()->json(['error' => 'Settings API is disabled'], 401);
        }

        if (!app(SettingsManagerAuthorization::class)::$isAuthorized) {
            return response()->json(['error' => 'You are unauthorized to manage settings'], 403);
        }

        return $next($request);
    }
}