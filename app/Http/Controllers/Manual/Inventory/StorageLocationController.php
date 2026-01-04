<?php

namespace App\Http\Controllers\Manual\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StorageLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StorageLocationController extends Controller
{
    public function index(Request $request)
    {
        $query = StorageLocation::where('user_id', Auth::id());

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $locations = $query->latest()->paginate(10);

        return view('manual.inventory.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('manual.inventory.locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        // Handle default toggle
        if ($request->is_default) {
            StorageLocation::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        StorageLocation::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'type' => $request->type ?? 'General',
            'description' => $request->description,
            'is_default' => $request->is_default ?? false,
        ]);

        return redirect()->route('manual.storage-locations.index')
            ->with('success', 'Storage location created successfully.');
    }

    public function edit(StorageLocation $storageLocation)
    {
        if ($storageLocation->user_id !== Auth::id()) {
            abort(403);
        }

        return view('manual.inventory.locations.edit', compact('storageLocation'));
    }

    public function update(Request $request, StorageLocation $storageLocation)
    {
        if ($storageLocation->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        if ($request->is_default) {
            StorageLocation::where('user_id', Auth::id())
                ->where('id', '!=', $storageLocation->id)
                ->update(['is_default' => false]);
        }

        $storageLocation->update([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'is_default' => $request->is_default ?? false,
        ]);

        return redirect()->route('manual.storage-locations.index')
            ->with('success', 'Storage location updated successfully.');
    }

    public function destroy(StorageLocation $storageLocation)
    {
        if ($storageLocation->user_id !== Auth::id()) {
            abort(403);
        }

        if ($storageLocation->inventories()->count() > 0) {
            return back()->with('error', 'Cannot delete location with inventory items.');
        }

        $storageLocation->delete();

        return redirect()->route('manual.storage-locations.index')
            ->with('success', 'Storage location removed successfully.');
    }
}
