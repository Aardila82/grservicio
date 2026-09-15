<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    private function getSettings(Request $request): Setting
    {
        $settings = Setting::first();
        if (!$settings) {
            $settings = Setting::create(['company_name' => $request->user()->name]);
        }
        return $settings;
    }

    public function show(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->getSettings($request)]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_name'     => 'sometimes|string|max:150',
            'company_phone'    => 'sometimes|nullable|string|max:30',
            'company_email'    => 'sometimes|nullable|email|max:150',
            'company_address'  => 'sometimes|nullable|string',
            'currency'         => 'sometimes|string|max:10',
            'whatsapp_message' => 'sometimes|nullable|string',
            'invoice_prefix'   => 'sometimes|string|max:10',
        ]);

        $settings = $this->getSettings($request);
        $settings->update($validated);

        return response()->json([
            'message' => 'Configuración actualizada.',
            'data'    => $settings,
        ]);
    }

    public function uploadLogo(Request $request): JsonResponse
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        $settings = $this->getSettings($request);

        // Delete old logo
        if ($settings->logo_path && Storage::disk('local')->exists($settings->logo_path)) {
            Storage::disk('local')->delete($settings->logo_path);
        }

        $path = $request->file('logo')->store('logos', 'local');
        $settings->update(['logo_path' => $path]);

        return response()->json([
            'message'   => 'Logo actualizado.',
            'logo_path' => $path,
        ]);
    }
}
