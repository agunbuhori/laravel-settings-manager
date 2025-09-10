<?php

namespace Agunbuhori\SettingsManager\Controllers;

use Agunbuhori\SettingsManager\Models\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{

    /**
     * Display a listing of the resource.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $request->validate([
            'per_page' => 'integer|max:100',
            'keys'     => 'nullable|string',
        ]);
        
        $settings = Setting::when($request->has('keys'), function ($query) use ($request) {
                                $query->whereIn('key', explode(',', $request->keys));
                            })
                            ->cursorPaginate($request->get('per_page', 10));

        return response()->json($settings);
    }

    /**
     * Display the specified resource.
     * 
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $key)
    {
        $setting = Setting::where('key', $key)->firstOrFail();

        return response()->json($setting);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $key)
    {
        $request->validate([
            'value' => 'required|max:3000',
        ]);

        settings()->set($key, $request->value);

        return response()->json(['message' => 'Setting updated successfully']);
    }
}
