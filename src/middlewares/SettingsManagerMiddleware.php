<?php

namespace Agunbuhori\SettingsManager\Middlewares;

class SettingsManagerMiddleware
{
    public function handle($request, \Closure $next)
    {
        if (!config('settings-manager.enable_api')) {
            return response()->json(['error' => 'Settings API is disabled'], 403);
        }

        return $next($request);
    }
}