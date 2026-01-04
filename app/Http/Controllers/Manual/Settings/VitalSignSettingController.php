<?php

namespace App\Http\Controllers\Manual\Settings;

use App\Http\Controllers\Controller;
use App\Models\VitalSignSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VitalSignSettingController extends Controller
{
    public function index(Request $request)
    {
        $query = VitalSignSetting::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('unit', 'like', "%{$search}%");
        }

        $settings = $query->latest()->paginate(10);

        return view('manual.settings.vital-sign-settings.index', compact('settings'));
    }

    public function create()
    {
        return view('manual.settings.vital-sign-settings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'type' => 'required|in:number,text',
            'is_active' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id() ?? 1;
        $validated['is_active'] = $request->has('is_active');

        VitalSignSetting::create($validated);

        return redirect()->route('manual.vital-sign-settings.index')
            ->with('success', 'Vital sign setting created successfully.');
    }

    public function edit(VitalSignSetting $vitalSignSetting)
    {
        return view('manual.settings.vital-sign-settings.edit', compact('vitalSignSetting'));
    }

    public function update(Request $request, VitalSignSetting $vitalSignSetting)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'type' => 'required|in:number,text',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $vitalSignSetting->update($validated);

        return redirect()->route('manual.vital-sign-settings.index')
            ->with('success', 'Vital sign setting updated successfully.');
    }

    public function destroy(VitalSignSetting $vitalSignSetting)
    {
        $vitalSignSetting->delete();

        return redirect()->route('manual.vital-sign-settings.index')
            ->with('success', 'Vital sign setting deleted successfully.');
    }
}
