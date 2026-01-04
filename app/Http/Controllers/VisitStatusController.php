<?php

namespace App\Http\Controllers;

use App\Models\VisitStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VisitStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = VisitStatus::orderBy('order')->get();

        return view('visit-statuses.index', compact('statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('visit-statuses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:visit_statuses',
            'color' => 'required|string|max:20', // Hex code
            'description' => 'nullable|string',
            'order' => 'integer',
        ]);

        VisitStatus::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'color' => $request->color,
            'description' => $request->description,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('visit-statuses.index')
            ->with('success', 'Status created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VisitStatus $visitStatus)
    {
        return view('visit-statuses.edit', compact('visitStatus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VisitStatus $visitStatus)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:visit_statuses,name,'.$visitStatus->id,
            'color' => 'required|string|max:20',
            'description' => 'nullable|string',
            'order' => 'integer',
        ]);

        $visitStatus->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'color' => $request->color,
            'description' => $request->description,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('visit-statuses.index')
            ->with('success', 'Status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VisitStatus $visitStatus)
    {
        // Prevent deleting if used? Or use nullOnDelete (already set in migration)
        // Check if it's a default one maybe? For now allow all.
        $visitStatus->delete();

        return redirect()->route('visit-statuses.index')
            ->with('success', 'Status deleted successfully.');
    }
}
