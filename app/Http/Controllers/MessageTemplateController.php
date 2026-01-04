<?php

namespace App\Http\Controllers;

use App\Models\MessageTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = MessageTemplate::where('doctor_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('message-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('message-templates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content_pattern' => 'required|string',
            'type' => 'required|string|in:whatsapp,email,sms',
            'trigger_event' => 'nullable|string|in:on_departure,on_arrival',
        ]);

        // If trigger_event is set, ensure uniqueness for this doctor?
        // Or just overwrite/allow multiple. Let's warn if one already exists?
        // For simplicity, if they set a trigger_event, we just save it.
        // The usage logic will pick the first one or we can add validation rule.

        if ($request->trigger_event) {
            // Optional: Check if a template for this event already exists and warn or error?
            // For now, let's allow it, but maybe UI should show which one is active.
        }

        MessageTemplate::create([
            'doctor_id' => Auth::id(),
            'title' => $request->title,
            'content_pattern' => $request->content_pattern,
            'type' => $request->type,
            'trigger_event' => $request->trigger_event,
        ]);

        return redirect()->route('message-templates.index')
            ->with('success', 'Template pesan berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MessageTemplate $messageTemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MessageTemplate $messageTemplate)
    {
        if ($messageTemplate->doctor_id !== Auth::id()) {
            abort(403);
        }

        return view('message-templates.edit', compact('messageTemplate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MessageTemplate $messageTemplate)
    {
        if ($messageTemplate->doctor_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content_pattern' => 'required|string',
            'type' => 'required|string|in:whatsapp,email,sms',
            'trigger_event' => 'nullable|string|in:on_departure,on_arrival',
        ]);

        $messageTemplate->update([
            'title' => $request->title,
            'content_pattern' => $request->content_pattern,
            'type' => $request->type,
            'trigger_event' => $request->trigger_event,
        ]);

        return redirect()->route('message-templates.index')
            ->with('success', 'Template pesan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MessageTemplate $messageTemplate)
    {
        if ($messageTemplate->doctor_id !== Auth::id()) {
            abort(403);
        }

        $messageTemplate->delete();

        return redirect()->route('message-templates.index')
            ->with('success', 'Template pesan berhasil dihapus.');
    }
}
