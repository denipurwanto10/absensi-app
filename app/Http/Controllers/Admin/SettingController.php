<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $setting = Setting::current();

        return view('admin.settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:150'],
            'geofence_enabled' => ['nullable', 'boolean'],
            'office_lat' => ['nullable', 'required_if:geofence_enabled,1', 'numeric', 'between:-90,90'],
            'office_lng' => ['nullable', 'required_if:geofence_enabled,1', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:20', 'max:5000'],
            'batas_telat' => ['required', 'date_format:H:i'],
        ]);

        $validated['geofence_enabled'] = $request->boolean('geofence_enabled');

        Setting::current()->update($validated);

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
