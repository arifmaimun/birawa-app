<?php

namespace App\Http\Controllers;

use App\Models\StorageLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StorageLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return redirect()->route('inventory.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('inventory.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:warehouse,bag',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:0',
        ]);

        $location = StorageLocation::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'capacity' => $request->capacity,
            'is_default' => false,
        ]);

        return redirect()->route('inventory.index', ['location_id' => $location->id])
            ->with('success', 'Storage location created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StorageLocation $storageLocation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StorageLocation $storageLocation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StorageLocation $storageLocation)
    {
        if ($storageLocation->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:warehouse,bag',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:0',
        ]);

        $storageLocation->update($request->only(['name', 'type', 'description', 'capacity']));

        return redirect()->back()->with('success', 'Storage location updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StorageLocation $storageLocation)
    {
        if ($storageLocation->user_id !== Auth::id()) {
            abort(403);
        }

        if ($storageLocation->is_default) {
            return redirect()->back()->with('error', 'Cannot delete the default storage location.');
        }

        // Check if has items with stock
        $hasStock = $storageLocation->inventories()->where('stock_qty', '>', 0)->exists();
        if ($hasStock) {
            return redirect()->back()->with('error', 'Cannot delete location with active stock. Please transfer or remove items first.');
        }

        // Delete empty inventories associated with this location
        $storageLocation->inventories()->delete();
        $storageLocation->delete();

        return redirect()->route('inventory.index')->with('success', 'Storage location deleted successfully.');
    }
}
