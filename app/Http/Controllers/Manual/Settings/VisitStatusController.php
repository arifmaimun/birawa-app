<?php

namespace App\Http\Controllers\Manual\Settings;

use App\Http\Controllers\Controller;
use App\Models\VisitStatus;
use Illuminate\Http\Request;

class VisitStatusController extends Controller
{
    public function index(Request $request)
    {
        $query = VisitStatus::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $statuses = $query->latest()->paginate(10);

        return view('manual.settings.visit-statuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('manual.settings.visit-statuses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:visit_statuses,name',
            'color' => 'nullable|string', // e.g., success, warning, danger
            'description' => 'nullable|string',
        ]);

        VisitStatus::create($validated);

        return redirect()->route('manual.visit-statuses.index')
            ->with('success', 'Status created successfully.');
    }

    public function edit(VisitStatus $visitStatus)
    {
        return view('manual.settings.visit-statuses.edit', compact('visitStatus'));
    }

    public function update(Request $request, VisitStatus $visitStatus)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:visit_statuses,name,'.$visitStatus->id,
            'color' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $visitStatus->update($validated);

        return redirect()->route('manual.visit-statuses.index')
            ->with('success', 'Status updated successfully.');
    }

    public function destroy(VisitStatus $visitStatus)
    {
        $visitStatus->delete();

        return redirect()->route('manual.visit-statuses.index')
            ->with('success', 'Status deleted successfully.');
    }
}
