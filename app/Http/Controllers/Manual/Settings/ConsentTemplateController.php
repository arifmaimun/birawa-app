<?php

namespace App\Http\Controllers\Manual\Settings;

use App\Http\Controllers\Controller;
use App\Models\ConsentTemplate;
use Illuminate\Http\Request;

class ConsentTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = ConsentTemplate::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $templates = $query->latest()->paginate(10);

        return view('manual.settings.consent-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('manual.settings.consent-templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'body' => 'required|string',
            'is_active' => 'boolean',
        ]);

        ConsentTemplate::create($validated);

        return redirect()->route('manual.consent-templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function edit(ConsentTemplate $consentTemplate)
    {
        return view('manual.settings.consent-templates.edit', compact('consentTemplate'));
    }

    public function update(Request $request, ConsentTemplate $consentTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'body' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $consentTemplate->update($validated);

        return redirect()->route('manual.consent-templates.index')
            ->with('success', 'Template updated successfully.');
    }

    public function destroy(ConsentTemplate $consentTemplate)
    {
        $consentTemplate->delete();

        return redirect()->route('manual.consent-templates.index')
            ->with('success', 'Template deleted successfully.');
    }
}
