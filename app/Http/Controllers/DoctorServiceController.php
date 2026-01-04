<?php

namespace App\Http\Controllers;

use App\Models\DoctorServiceCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorServiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $services = DoctorServiceCatalog::where('user_id', Auth::id())
            ->when($search, function ($query) use ($search) {
                $query->where('service_name', 'like', "%{$search}%");
            })
            ->orderBy('service_name')
            ->paginate(10);

        return view('services.index', compact('services', 'search'));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'unit' => 'required|string',
            'description' => 'nullable|string',
        ]);

        DoctorServiceCatalog::create([
            'user_id' => Auth::id(),
            'service_name' => $request->service_name,
            'price' => $request->price,
            'duration_minutes' => $request->duration_minutes,
            'unit' => $request->unit,
            'description' => $request->description,
        ]);

        return redirect()->route('services.index')->with('success', 'Service added successfully.');
    }

    public function edit(DoctorServiceCatalog $service)
    {
        if ($service->user_id !== Auth::id()) {
            abort(403);
        }

        return view('services.edit', compact('service'));
    }

    public function update(Request $request, DoctorServiceCatalog $service)
    {
        if ($service->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'service_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'unit' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $service->update($request->all());

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(DoctorServiceCatalog $service)
    {
        if ($service->user_id !== Auth::id()) {
            abort(403);
        }

        $service->delete();

        return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
    }
}
