<?php

namespace App\Http\Controllers\Manual\Settings;

use App\Http\Controllers\Controller;
use App\Models\MessageTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = MessageTemplate::query();

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $templates = $query->latest()->paginate(10);

        return view('manual.settings.message-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('manual.settings.message-templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string', // e.g., whatsapp, email
            'content_pattern' => 'required|string',
            'trigger_event' => 'nullable|string',
        ]);

        $validated['doctor_id'] = Auth::id() ?? 1; // Fallback or handle appropriately

        MessageTemplate::create($validated);

        return redirect()->route('manual.message-templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function edit(MessageTemplate $messageTemplate)
    {
        return view('manual.settings.message-templates.edit', compact('messageTemplate'));
    }

    public function update(Request $request, MessageTemplate $messageTemplate)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'content_pattern' => 'required|string',
            'trigger_event' => 'nullable|string',
        ]);

        $messageTemplate->update($validated);

        return redirect()->route('manual.message-templates.index')
            ->with('success', 'Template updated successfully.');
    }

    public function destroy(MessageTemplate $messageTemplate)
    {
        $messageTemplate->delete();

        return redirect()->route('manual.message-templates.index')
            ->with('success', 'Template deleted successfully.');
    }
}
