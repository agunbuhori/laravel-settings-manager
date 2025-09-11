<?php

namespace Agunbuhori\SettingsManager\Controllers;

use Agunbuhori\SettingsManager\Models\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(Request $request, private Setting $setting)
    {
        if ($request->has('bag') || $request->has('group')) {
            $setting->where('bag', $request->bag)->where('group', $request->group);
        }
    }

    /**
     * Display a listing of the resource.
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function index(Request $request)
    {        
        $request->validate([
            'per_page' => 'integer|max:100',
            'keys'     => 'nullable|string',
        ]);
        
        $settings = $this->setting->when($request->has('keys'), function ($query) use ($request) {
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
    public function show(Request $request, string $key)
    {
        $setting = settings()->get($key, null);

        return response()->json([
            'value' => $setting,
        ]);
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

        return response()->json(['message' => 'Setting updated successfully', 'data' => settings()->get($key)]);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     * */
    public function destroy(string $key)
    {
        settings()->set($key, null);

        return response()->json(['message' => 'Setting deleted successfully', 'data' => null]);
    }
}
