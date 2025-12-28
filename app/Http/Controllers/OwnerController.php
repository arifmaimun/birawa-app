<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $owners = User::where('role', 'client')
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->withCount('pets')
            ->latest()
            ->paginate(10);

        return view('owners.index', compact('owners', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('owners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'nullable|email|max:255|unique:users,email',
            'address' => 'nullable|string',
        ]);

        User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email ?? $request->phone . '@birawa.vet', // Fallback email
            'address' => $request->address,
            'role' => 'client',
            'password' => Hash::make('password'), // Default password
        ]);

        return redirect()->route('owners.index')
            ->with('success', 'Owner (Client) created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $owner)
    {
        if ($owner->role !== 'client') {
             abort(404);
        }
        $owner->load('pets');
        return view('owners.show', compact('owner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $owner)
    {
        if ($owner->role !== 'client') {
             abort(404);
        }
        return view('owners.edit', compact('owner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $owner)
    {
        if ($owner->role !== 'client') {
             abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($owner->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($owner->id)],
            'address' => 'nullable|string',
        ]);

        $owner->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email ?? $owner->email,
            'address' => $request->address,
        ]);

        return redirect()->route('owners.index')
            ->with('success', 'Owner updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $owner)
    {
        if ($owner->role !== 'client') {
             abort(403, 'Cannot delete non-client users via this resource.');
        }

        $owner->delete();

        return redirect()->route('owners.index')
            ->with('success', 'Owner deleted successfully.');
    }
}
