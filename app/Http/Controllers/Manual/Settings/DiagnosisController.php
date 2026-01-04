<?php

namespace App\Http\Controllers\Manual\Settings;

use App\Http\Controllers\Controller;
use App\Models\Diagnosis;
use Illuminate\Http\Request;

class DiagnosisController extends Controller
{
    public function index(Request $request)
    {
        $query = Diagnosis::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        $diagnoses = $query->latest()->paginate(10);

        return view('manual.settings.diagnoses.index', compact('diagnoses'));
    }

    public function create()
    {
        return view('manual.settings.diagnoses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:diagnoses,code',
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        Diagnosis::create($validated);

        return redirect()->route('manual.diagnoses.index')
            ->with('success', 'Diagnosis created successfully.');
    }

    public function edit(Diagnosis $diagnosis)
    {
        return view('manual.settings.diagnoses.edit', compact('diagnosis'));
    }

    public function update(Request $request, Diagnosis $diagnosis)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:diagnoses,code,' . $diagnosis->id,
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $diagnosis->update($validated);

        return redirect()->route('manual.diagnoses.index')
            ->with('success', 'Diagnosis updated successfully.');
    }

    public function destroy(Diagnosis $diagnosis)
    {
        $diagnosis->delete();

        return redirect()->route('manual.diagnoses.index')
            ->with('success', 'Diagnosis deleted successfully.');
    }
}
