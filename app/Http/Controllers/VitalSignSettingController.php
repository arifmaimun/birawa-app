<?php

namespace App\Http\Controllers;

use App\Models\VitalSignSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VitalSignSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = VitalSignSetting::where('user_id', Auth::id())->get();
        return view('vital-sign-settings.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vital-sign-settings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'type' => 'required|in:number,text',
        ]);

        VitalSignSetting::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'unit' => $request->unit,
            'type' => $request->type,
            'is_active' => true,
        ]);

        return redirect()->route('vital-sign-settings.index')
            ->with('success', 'Custom vital sign field added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VitalSignSetting $vitalSignSetting)
    {
        if ($vitalSignSetting->user_id !== Auth::id()) {
            abort(403);
        }
        return view('vital-sign-settings.edit', compact('vitalSignSetting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VitalSignSetting $vitalSignSetting)
    {
        if ($vitalSignSetting->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'type' => 'required|in:number,text',
            'is_active' => 'boolean',
        ]);

        $vitalSignSetting->update([
            'name' => $request->name,
            'unit' => $request->unit,
            'type' => $request->type,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('vital-sign-settings.index')
            ->with('success', 'Custom vital sign field updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VitalSignSetting $vitalSignSetting)
    {
        if ($vitalSignSetting->user_id !== Auth::id()) {
            abort(403);
        }

        $vitalSignSetting->delete();

        return redirect()->route('vital-sign-settings.index')
            ->with('success', 'Custom vital sign field deleted successfully.');
    }
}
