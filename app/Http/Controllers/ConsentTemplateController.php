<?php

namespace App\Http\Controllers;

use App\Models\ConsentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsentTemplateController extends Controller
{
    public function index()
    {
        $templates = ConsentTemplate::where('doctor_id', Auth::id())->latest()->get();

        return view('templates.consent.index', compact('templates'));
    }

    public function create()
    {
        return view('templates.consent.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body_content' => 'required|string',
        ]);

        ConsentTemplate::create([
            'doctor_id' => Auth::id(),
            'title' => $request->title,
            'body_content' => $request->body_content,
        ]);

        return redirect()->route('consent-templates.index')->with('success', 'Template created.');
    }

    public function edit(ConsentTemplate $consentTemplate)
    {
        if ($consentTemplate->doctor_id !== Auth::id()) {
            abort(403);
        }

        return view('templates.consent.edit', compact('consentTemplate'));
    }

    public function update(Request $request, ConsentTemplate $consentTemplate)
    {
        if ($consentTemplate->doctor_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'body_content' => 'required|string',
        ]);

        $consentTemplate->update($request->only('title', 'body_content'));

        return redirect()->route('consent-templates.index')->with('success', 'Template updated.');
    }

    public function destroy(ConsentTemplate $consentTemplate)
    {
        if ($consentTemplate->doctor_id !== Auth::id()) {
            abort(403);
        }
        $consentTemplate->delete();

        return back()->with('success', 'Template deleted.');
    }
}
